<?php namespace App\Models;

use CodeIgniter\Model;

class StMovementModel extends Model
{
    protected $table      = 'st_movement';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $allowedFields = ['id','product_id', 'quantity', 'movement_type', 'reference_id', 
                              'reference_type', 'from_location', 'to_location', 'notes', 'created_by','status', 'user_id'];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    
    public function logMovement($data)
    {
        return $this->insert($data);
    }
public function getStockByProduct(int $product_id): array
{
    $builder = $this->db->table('locations l');
    $builder->select("l.id AS location_id, l.code AS location_code, l.name AS location_name");
    
    // Calculate current stock
    $builder->select("(
        COALESCE(
            (SELECT SUM(quantity) 
             FROM st_initial 
             WHERE product_id = $product_id AND location_id = l.id),
        0
        ) +
        COALESCE(
            (SELECT SUM(quantity) 
             FROM st_movement 
             WHERE product_id = $product_id 
             AND (
                 (movement_type = 'in' AND to_location = l.id) OR
                 (movement_type = 'transfer' AND to_location = l.id)
             )),
        0
        ) -
        COALESCE(
            (SELECT SUM(quantity) 
             FROM st_movement 
             WHERE product_id = $product_id 
             AND (
                 (movement_type = 'out' AND from_location = l.id) OR
                 (movement_type = 'transfer' AND from_location = l.id)
             )),
        0
        )
    ) AS current_stock");

    // Add booked quantity calculation
    $builder->select("(
        COALESCE(
            (SELECT SUM(quantity) 
             FROM st_movement 
             WHERE product_id = $product_id 
             AND movement_type = 'booked'
             AND status = 'booked'
             AND from_location = l.id
             AND to_location = l.id),
        0
        )
    ) AS booked_stock");

    $builder->where('l.deleted_at IS NULL');
    $builder->where('l.is_active', 1);
    $builder->orderBy('l.name');

    return $builder->get()->getResultArray();
}

    /**
     * Get current stock by product ID for specific location
     * 
     * @param int $product_id
     * @param int $location_id
     * @return array|null
     */
    public function getStockByProductAndLocation(int $product_id, int $location_id): ?array
    {
        $builder = $this->db->table('locations l');
        $builder->select("l.id AS location_id, l.code AS location_code, l.name AS location_name");
        
        // Calculate current stock
        $builder->select("(
            COALESCE(
                (SELECT SUM(quantity) 
                 FROM st_initial 
                 WHERE product_id = $product_id AND location_id = l.id),
            0
            ) +
            COALESCE(
                (SELECT SUM(quantity) 
                 FROM st_movement 
                 WHERE product_id = $product_id 
                 AND (
                     (movement_type = 'in' AND to_location = l.id) OR
                     (movement_type = 'booked' AND from_location = to_location AND to_location = l.id) OR
                     (movement_type = 'transfer' AND to_location = l.id)
                 )),
            0
            ) -
            COALESCE(
                (SELECT SUM(quantity) 
                 FROM st_movement 
                 WHERE product_id = $product_id 
                 AND (
                     (movement_type = 'out' AND from_location = l.id) OR
                     (movement_type = 'transfer' AND from_location = l.id)
                 )),
            0
            )
        ) AS current_stock");

        $builder->where('l.id', $location_id);
        $builder->where('l.deleted_at IS NULL');
        $builder->where('l.is_active', 1);

        $result = $builder->get()->getRowArray();
        return $result ?: null;
    }
    public function getProductHistory($productId, $limit = 100)
    {
        return $this->select('st_movement.*, from_loc.name as from_location_name, to_loc.name as to_location_name, users.nama_depan as user_name')
                   ->join('locations as from_loc', 'from_loc.id = st_movement.from_location', 'left')
                   ->join('locations as to_loc', 'to_loc.id = st_movement.to_location', 'left')
                   ->join('users', 'users.id = st_movement.created_by', 'left')
                   ->where('product_id', $productId)
                   ->orderBy('created_at', 'DESC')
                   ->limit($limit)
                   ->findAll();
    }
public function bookStock($productId, $quantity, $piId, $locationId, $notes = '')
{
    $this->db->transStart();

    try {
        // 1. Get all available stock records at this location
        $availableStocks = $this->getAvailableStockRecords($productId, $locationId);
        
        // 2. Calculate total available quantity
        $totalAvailable = array_sum(array_column($availableStocks, 'quantity'));
        
        // 3. Validate sufficient stock
        if ($totalAvailable < $quantity) {
            throw new \RuntimeException("Insufficient stock at location. Available: $totalAvailable, Requested: $quantity");
        }

        // 4. Process booking
        $remaining = $quantity;
        $bookedIds = [];
        
        foreach ($availableStocks as $stock) {
            if ($remaining <= 0) break;
            
            $deduct = min($stock['quantity'], $remaining);
            $remaining -= $deduct;
            
            // Create booking record
            $bookedId = $this->createBookingRecord($productId, $deduct, $piId, $locationId, $notes);
            $bookedIds[] = $bookedId;
            
            // Update source record
            $this->updateSourceStock($stock, $deduct);
        }

        $this->db->transComplete();
        return $bookedIds;

    } catch (\Exception $e) {
        $this->db->transRollback();
        log_message('error', 'Booking failed: ' . $e->getMessage());
        throw new \RuntimeException('Booking operation failed: ' . $e->getMessage());
    }
}

/**
 * Get all available stock records for a product at a location
 */
protected function getAvailableStockRecords($productId, $locationId)
{
    // Get initial stock
    $initial = $this->db->table('st_initial')
        ->where('product_id', $productId)
        ->where('location_id', $locationId)
        ->where('quantity >', 0)
        ->get()
        ->getResultArray();

    // Get available movement records
    $movements = $this->where([
            'product_id' => $productId,
            'to_location' => $locationId,
            'status' => 'available',
            'quantity >' => 0
        ])
        ->findAll();

    return array_merge($initial, $movements);
}

/**
 * Create a booking movement record
 */
protected function createBookingRecord($productId, $quantity, $piId, $locationId, $notes)
{
    return $this->insert([
        'product_id' => $productId,
        'quantity' => $quantity,
        'movement_type' => 'booked',
        'status' => 'booked',
        'from_location' => $locationId,
        'to_location' => $locationId,
        'reference_id' => $piId,
        'reference_type' => 'proforma_invoice',
        'notes' => $notes,
        'user_id' => session('auth.id'),
        'created_at' => date('Y-m-d H:i:s'),
        'updated_at' => date('Y-m-d H:i:s')
    ]);
}

/**
 * Update source stock after booking
 */
protected function updateSourceStock($stock, $deduct)
{
    if (isset($stock['location_id'])) {
        // Initial stock record
        $this->db->table('st_initial')
            ->where('id', $stock['id'])
            ->set('quantity', "quantity - $deduct", false)
            ->update();
    } else {
        // Movement record
        $this->update($stock['id'], [
            'quantity' => $stock['quantity'] - $deduct
        ]);
    }
}

    // Release booked stock back to available
    public function releaseBookedStock($bookingIds)
    {
        foreach ($bookingIds as $id) {
            $booking = $this->find($id);
            if ($booking && $booking['status'] == 'booked') {
                // Find the original available stock
                $original = $this->where([
                    'product_id' => $booking['product_id'],
                    'label_code' => $booking['label_code'],
                    'status' => 'available'
                ])->first();

                if ($original) {
                    // Increase available quantity
                    $this->update($original['id'], [
                        'quantity' => $original['quantity'] + $booking['quantity']
                    ]);
                } else {
                    // Create new available record if original doesn't exist
                    $this->insert([
                        'product_id' => $booking['product_id'],
                        'label_code' => $booking['label_code'],
                        'quantity' => $booking['quantity'],
                        'from_location' => $booking['from_location'],
                        'to_location' => $booking['to_location'],
                        'status' => 'available',
                        'movement_type' => 'release',
                        'notes' => 'Released from booking #' . $id,
                        'user_id' => $_SESSION['auth']['id']
                    ]);
                }
                // Mark booking as released
                $this->update($id, ['status' => 'released']);
            }
        }
    }

    // Transfer stock between locations
public function transferStock($productId, $fromLocationId, $toLocationId, $quantity, $notes = '')
{
    // Validate transfer quantity
    if ($quantity <= 0) {
        throw new \RuntimeException('Transfer quantity must be positive');
    }

    // Validate different locations
    if ($fromLocationId == $toLocationId) {
        throw new \RuntimeException('Source and destination locations cannot be the same');
    }

    $this->db->transStart();

    try {


        // 2. Record incoming transfer (positive quantity)
        $this->insert([
            'product_id'    => $productId,
            'quantity'      => $quantity,
            'movement_type' => 'transfer',
            'from_location' => $fromLocationId,
            'to_location'   => $toLocationId,
            'notes'         => $notes,
            'user_id'       => session('auth.id'),
            'created_at'    => date('Y-m-d H:i:s'),
            'updated_at'    => date('Y-m-d H:i:s')
        ]);

        $this->db->transComplete();

        return true;
    } catch (\Exception $e) {
        $this->db->transRollback();
        log_message('error', 'Stock transfer failed: ' . $e->getMessage());
        throw new \RuntimeException('Stock transfer operation failed');
    }
}
    public function getAvailableStock($productId)
{
    $initial = $this->db->table('st_initial')
        ->selectSum('quantity')
        ->where('product_id', $productId)
        ->get()
        ->getRow()->quantity;

    // Get total stock in (initial stock + stock in adjustments)
    $stockIn = $this->db->table('st_movement')
        ->selectSum('quantity')
        ->where('product_id', $productId)
        ->where('movement_type', 'in')
        ->get()
        ->getRow()->quantity;

    // Get total stock out (stock out adjustments)
    $stockOut = $this->db->table('st_movement')
        ->selectSum('quantity')
        ->where('product_id', $productId)
        ->where('movement_type', 'out')
        ->get()
        ->getRow()->quantity;

    // Get total booked stock
    $bookedStock = $this->db->table('st_movement')
        ->selectSum('quantity')
        ->where('product_id', $productId)
        ->where('status', 'booked')
        ->get()
        ->getRow()->quantity;

    // Calculate available stock
    $availableStock =($initial ?? 0)+ ($stockIn ?? 0) - ($stockOut ?? 0) - ($bookedStock ?? 0);

    return max(0, $availableStock); // Ensure we don't return negative values
}
public function getAvailableStockAtLocation(int $productId, int $locationId): float
{
    // Get initial stock at this location
    $initialStock = $this->db->table('st_initial')
        ->selectSum('quantity')
        ->where('product_id', $productId)
        ->where('location_id', $locationId)
        ->get()
        ->getRow()->quantity ?? 0;

    // Get total incoming stock (in + transfer to this location)
    $incoming = $this->db->table('st_movement')
        ->selectSum('quantity')
        ->where('product_id', $productId)
        ->where('to_location', $locationId)
        ->groupStart()
            ->where('movement_type', 'in')
            ->orWhere('movement_type', 'transfer')
        ->groupEnd()
        ->get()
        ->getRow()->quantity ?? 0;

    // Get total outgoing stock (out + transfer from this location)
    $outgoing = $this->db->table('st_movement')
        ->selectSum('quantity')
        ->where('product_id', $productId)
        ->where('from_location', $locationId)
        ->groupStart()
            ->where('movement_type', 'out')
            ->orWhere('movement_type', 'transfer')
        ->groupEnd()
        ->get()
        ->getRow()->quantity ?? 0;

    // Get booked stock at this location (status = booked and location matches)
    $booked = $this->db->table('st_movement')
        ->selectSum('quantity')
        ->where('product_id', $productId)
        ->where('status', 'booked')
        ->where('from_location', $locationId)
        ->where('to_location', $locationId)
        ->get()
        ->getRow()->quantity ?? 0;

    // Calculate available stock
    $availableStock = $initialStock + $incoming + $outgoing - $booked;
    
    // Ensure we don't return negative values
    return max(0, $availableStock);
}
public function getMovementHistory($productId, $limit = 100)
{
    return $this->select('st_movement.*, 
                        from_loc.name as from_location_name, 
                        to_loc.name as to_location_name, 
                        users.username')
               ->join('locations as from_loc', 'from_loc.id = st_movement.from_location', 'left')
               ->join('locations as to_loc', 'to_loc.id = st_movement.to_location', 'left')
               ->join('users', 'users.id = st_movement.user_id', 'left')
               ->where('product_id', $productId)
               ->orderBy('created_at', 'DESC')
               ->limit($limit)
               ->findAll();
}
}