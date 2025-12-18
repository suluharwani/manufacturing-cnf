<?php namespace App\Models;

use CodeIgniter\Model;

class StMovementModel extends Model
{
    protected $table      = 'st_movement';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $allowedFields = ['id','product_id', 'quantity', 'movement_type', 'reference_id', 'finishing_id',
                              'reference_type', 'from_location', 'to_location', 'notes','code', 'created_by','status', 'user_id'];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    
    public function logMovement($data)
    {
        return $this->insert($data);
    }
// public function getStockByProduct(int $product_id): array
// {
//  $builder = $this->db->table('locations l');
//     $builder->select("l.id AS location_id, l.code AS location_code, l.name AS location_name");
    
//     // Calculate current stock with finishing filter
//     $finishingCondition = $finishing_id ? "AND finishing_id = $finishing_id" : "AND finishing_id IS NULL";
    
//     $builder->select("(
//         COALESCE(
//             (SELECT SUM(quantity) 
//              FROM st_initial 
//              WHERE product_id = $product_id 
//              AND location_id = l.id
//              $finishingCondition),
//         0
//         ) +
//         COALESCE(
//             (SELECT SUM(quantity) 
//              FROM st_movement 
//              WHERE product_id = $product_id 
//              AND (
//                  (movement_type = 'in' AND to_location = l.id) OR
//                  (movement_type = 'transfer' AND to_location = l.id)
//              $finishingCondition),
//         0
//         ) -
//         COALESCE(
//             (SELECT SUM(quantity) 
//              FROM st_movement 
//              WHERE product_id = $product_id 
//              AND (
//                  (movement_type = 'out' AND from_location = l.id) OR
//                  (movement_type = 'transfer' AND from_location = l.id)
//              $finishingCondition),
//         0
//         )
//     ) AS current_stock");

//     // Add booked quantity calculation
//     $builder->select("(
//         COALESCE(
//             (SELECT SUM(quantity) 
//              FROM st_movement 
//              WHERE product_id = $product_id 
//              AND movement_type = 'booked'
//              AND status = 'booked'
//              AND from_location = l.id
//              AND to_location = l.id),
//         0
//         )
//     ) AS booked_stock");

//     $builder->where('l.deleted_at IS NULL');
//     $builder->where('l.is_active', 1);
//     $builder->orderBy('l.name');

//     return $builder->get()->getResultArray();
// }

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
    return $this->select('st_movement.*, 
                        from_loc.name as from_location_name, 
                        to_loc.name as to_location_name, 
                        users.nama_depan as username,
                        finishing.name as finishing_name,
                        finishing.id as finishing_id,
                        proforma_invoice.invoice_number as pi_number,
                        COALESCE(finishing.name, "Standard") as variant_name')
               ->join('locations as from_loc', 'from_loc.id = st_movement.from_location', 'left')
               ->join('locations as to_loc', 'to_loc.id = st_movement.to_location', 'left')
               ->join('users', 'users.id = st_movement.user_id', 'left')
               ->join('finishing', 'finishing.id = st_movement.finishing_id', 'left')
               ->join('proforma_invoice', 'proforma_invoice.id = st_movement.reference_id AND st_movement.reference_type = "proforma_invoice"', 'left')
               ->where('product_id', $productId)
               ->orderBy('created_at', 'DESC')
               ->limit($limit)
               ->findAll();
}
public function bookStock($productId, $quantity, $piId, $locationId, $finishing_id, $notes = '')
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
            $bookedId = $this->createBookingRecord($productId, $deduct, $piId, $locationId, $notes, $finishing_id);
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
    // 1. Get initial stock
    $initialStock = $this->db->table('st_initial')
        ->select('id, quantity, location_id')
        ->where('product_id', $productId)
        ->where('location_id', $locationId)
        ->where('quantity >', 0)
        ->get()
        ->getResultArray();

    // 2. Calculate net available movements (in - out - booked - transfer)
    $movementSummary = $this->db->table('st_movement')
        ->select([
            'id',
            'quantity',
            'movement_type',
            'from_location',
            'to_location',
            'status'
        ])
        ->where('product_id', $productId)
        ->groupStart()
            ->where('to_location', $locationId)
            ->orWhere('from_location', $locationId)
        ->groupEnd()
        ->groupStart()
            ->where('movement_type', 'in')
            ->orWhere('movement_type', 'out')
            ->orWhere('movement_type', 'booked')
            ->orWhere('movement_type', 'transfer')
        ->groupEnd()
        ->get()
        ->getResultArray();

    // 3. Process movements to calculate available quantities
    $availableRecords = [];

    // Add initial stock if exists
    foreach ($initialStock as $stock) {
        $availableRecords[] = [
            'type' => 'initial',
            'id' => $stock['id'],
            'quantity' => $stock['quantity'],
            'location_id' => $stock['location_id']
        ];
    }

    // Process movement records
    foreach ($movementSummary as $movement) {
        if (($movement['movement_type'] === 'in' || $movement['movement_type'] === 'transfer') 
            && $movement['to_location'] == $locationId) {
            // Add in/transfer movements (as positive quantity)
            $availableRecords[] = [
                'type' => 'movement',
                'subtype' => $movement['movement_type'],
                'id' => $movement['id'],
                'quantity' => $movement['quantity'],
                'location_id' => $movement['to_location']
            ];
        } elseif ($movement['movement_type'] === 'out' && $movement['from_location'] == $locationId) {
            // Subtract out movements
            $this->subtractFromAvailable($availableRecords, $movement['quantity']);
        } elseif ($movement['movement_type'] === 'booked' && 
                 $movement['from_location'] == $locationId && 
                 $movement['status'] == 'booked') {
            // Subtract booked quantities
            $this->subtractFromAvailable($availableRecords, $movement['quantity']);
        }
    }

    return $availableRecords;
}

/**
 * Helper method to subtract quantity from available records
 */

/**
 * Helper method to subtract quantity from available records
 */
protected function subtractFromAvailable(&$availableRecords, $quantityToSubtract)
{
    $remaining = $quantityToSubtract;
    
    foreach ($availableRecords as &$record) {
        if ($remaining <= 0) break;
        
        if ($record['quantity'] > 0) {
            $deduct = min($record['quantity'], $remaining);
            $record['quantity'] -= $deduct;
            $remaining -= $deduct;
        }
    }
}

/**
 * Create a booking movement record
 */
protected function createBookingRecord($productId, $quantity, $piId, $locationId, $notes, $finishing_id)
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
        'finishing_id' => $finishing_id,
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
public function getAvailableStockAtLocation(int $productId, int $locationId, ?int $finishingId = null): float
{
    // Get initial stock at this location with finishing filter
    $initialStock = $this->db->table('st_initial')
        ->selectSum('quantity')
        ->where('product_id', $productId)
        ->where('location_id', $locationId)
        ->where($finishingId ? ['finishing_id' => $finishingId] : 'finishing_id IS NULL')
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
        ->where($finishingId ? ['finishing_id' => $finishingId] : 'finishing_id IS NULL')
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
        ->where($finishingId ? ['finishing_id' => $finishingId] : 'finishing_id IS NULL')
        ->get()
        ->getRow()->quantity ?? 0;

    // Get booked stock at this location (status = booked and location matches)
    $booked = $this->db->table('st_movement')
        ->selectSum('quantity')
        ->where('product_id', $productId)
        ->where('status', 'booked')
        ->where('from_location', $locationId)
        ->where('to_location', $locationId)
        ->where($finishingId ? ['finishing_id' => $finishingId] : 'finishing_id IS NULL')
        ->get()
        ->getRow()->quantity ?? 0;

    // Calculate available stock
    $availableStock = ($initialStock + $incoming) - $outgoing - $booked;
    
    // Ensure we don't return negative values
    return max(0, $availableStock);
}
public function getMovementHistory($productId, $limit = 100)
{
    return $this->select('st_movement.*, 
                        from_loc.name as from_location_name, 
                        to_loc.name as to_location_name, 
                        users.username,
                        finishing.name as finishing_name,
                        finishing.id as finishing_id')
               ->join('locations as from_loc', 'from_loc.id = st_movement.from_location', 'left')
               ->join('locations as to_loc', 'to_loc.id = st_movement.to_location', 'left')
               ->join('users', 'users.id = st_movement.user_id', 'left')
               ->join('finishing', 'finishing.id = st_movement.finishing_id', 'left')
               ->where('product_id', $productId)
               ->orderBy('created_at', 'DESC')
               ->limit($limit)
               ->findAll();
}
public function getStockByProduct(int $product_id, int $finishing_id = null): array
{
    // Initialize the query builder
    $builder = $this->db->table('locations l');
    
    // Select location fields
    $builder->select('l.id AS location_id, l.code AS location_code, l.name AS location_name');
    
    // Handle finishing information
    if ($finishing_id) {
        $builder->select("$finishing_id AS finishing_id")
               ->select("(SELECT name FROM finishing WHERE id = $finishing_id) AS finishing_name");
        $finishingCondition = ['finishing_id' => $finishing_id];
    } else {
        $builder->select("NULL AS finishing_id, 'Standard' AS finishing_name");
        $finishingCondition = ['finishing_id' => null];
    }
    
    // Calculate current stock
    $initialStockSubquery = $this->db->table('st_initial')
        ->select('COALESCE(SUM(quantity), 0)')
        ->where('product_id', $product_id)
        ->where('location_id = l.id', null, false)
        ->where($finishingCondition)
        ->getCompiledSelect();
    
    $incomingStockSubquery = $this->db->table('st_movement')
        ->select('COALESCE(SUM(quantity), 0)')
        ->where('product_id', $product_id)
        ->where('to_location = l.id', null, false)
        ->whereIn('movement_type', ['in', 'transfer'])
        ->where($finishingCondition)
        ->getCompiledSelect();
    
    $outgoingStockSubquery = $this->db->table('st_movement')
        ->select('COALESCE(SUM(quantity), 0)')
        ->where('product_id', $product_id)
        ->where('from_location = l.id', null, false)
        ->whereIn('movement_type', ['out', 'transfer'])
        ->where($finishingCondition)
        ->getCompiledSelect();
    
    $builder->select("(($initialStockSubquery) + ($incomingStockSubquery) - ($outgoingStockSubquery)) AS current_stock");
    
    // Calculate booked stock
    $bookedStockSubquery = $this->db->table('st_movement')
        ->select('COALESCE(SUM(quantity), 0)')
        ->where('product_id', $product_id)
        ->where('from_location = l.id', null, false)
        ->where('to_location = l.id', null, false)
        ->where('movement_type', 'booked')
        ->where('status', 'booked')
        ->where($finishingCondition)
        ->getCompiledSelect();
    
    $builder->select("($bookedStockSubquery) AS booked_stock");
    
    // Calculate available stock
    $builder->select("(($initialStockSubquery) + ($incomingStockSubquery) - ($outgoingStockSubquery) - ($bookedStockSubquery)) AS available_stock");
    
    // Apply filters
    $builder->where('l.deleted_at IS NULL')
           ->where('l.is_active', 1)
           ->orderBy('l.name');
    
    try {
        $result = $builder->get()->getResultArray();
        
        // Format the results
        return array_map(function($row) {
            return [
                'location_id' => (int)$row['location_id'],
                'location_code' => $row['location_code'],
                'location_name' => $row['location_name'],
                'finishing_id' => $row['finishing_id'],
                'finishing_name' => $row['finishing_name'],
                'current_stock' => (float)$row['current_stock'],
                'booked_stock' => (float)$row['booked_stock'],
                'available_stock' => (float)$row['available_stock']
            ];
        }, $result);
        
    } catch (\Exception $e) {
        log_message('error', 'Failed to get product stock: ' . $e->getMessage());
        return [];
    }
}

// Update getAvailableStockAtLocation
// public function getAvailableStockAtLocation(int $productId, int $locationId, int $finishingId = null): float
// {
//     $finishingCondition = $finishingId ? "AND finishing_id = $finishingId" : "AND finishing_id IS NULL";
    
//     // Get initial stock with finishing filter
//     $initialStock = $this->db->table('st_initial')
//         ->selectSum('quantity')
//         ->where('product_id', $productId)
//         ->where('location_id', $locationId)
//         ->where($finishingCondition)
//         ->get()
//         ->getRow()->quantity ?? 0;

//     // ... update other queries similarly with $finishingCondition ...
    
//     return max(0, $initialStock + $incoming - $outgoing - $booked);
// }
/**
 * Get total booked stock quantity for a product
 * 
 * @param int $product_id Product ID
 * @param int|null $finishing_id Finishing ID (optional)
 * @return float Total booked quantity
 */
public function getBookedStock(int $product_id, ?int $finishing_id = null): float
{
    $builder = $this->builder();
    
    // Select sum of quantities
    $builder->selectSum('quantity');
    
    // Where conditions
    $builder->where('product_id', $product_id);
    $builder->where('movement_type', 'booked');
    $builder->where('status', 'booked');
    
    // Handle finishing condition
    if ($finishing_id !== null) {
        $builder->where('finishing_id', $finishing_id);
    } else {
        $builder->where('finishing_id IS NULL');
    }
    
    // Execute query
    $result = $builder->get()->getRow();
    
    return (float)($result->quantity ?? 0);
}

public function getBookedStockByLocation(int $product_id, ?int $finishing_id = null): array
{
    $builder = $this->db->table('st_movement sm');
    $builder->select([
        'l.id AS location_id',
        'l.name AS location_name',
        'COALESCE(f.id, NULL) AS finishing_id',
        'COALESCE(f.name, "Standard") AS finishing_name',
        'SUM(sm.quantity) AS booked_quantity'
    ]);
    
    $builder->join('locations l', 'l.id = sm.from_location', 'left');
    $builder->join('finishing f', 'f.id = sm.finishing_id', 'left');
    
    $builder->where('sm.product_id', $product_id);
    $builder->where('sm.movement_type', 'booked');
    $builder->where('sm.status', 'booked');
    
    if ($finishing_id !== null) {
        $builder->where('sm.finishing_id', $finishing_id);
    }
    
    $builder->groupBy(['l.id', 'f.id']);
    $builder->orderBy('l.name');
    
    $result = $builder->get()->getResultArray();
    
    // Format results
    $formatted = [];
    foreach ($result as $row) {
        $formatted[] = [
            'location_id' => (int)$row['location_id'],
            'location_name' => $row['location_name'],
            'finishing_id' => $row['finishing_id'],
            'finishing_name' => $row['finishing_name'],
            'quantity' => (float)$row['booked_quantity']
        ];
    }
    
    return $formatted;
}
/**
 * Get booked stock quantity for a product at a specific location
 * 
 * @param int $productId Product ID
 * @param int $locationId Location ID
 * @param int|null $finishingId Finishing ID (optional)
 * @return float Booked quantity
 */
public function getBookedStockAtLocation(int $productId, int $locationId, ?int $finishingId = null): float
{
    $builder = $this->builder();
    
    $builder->selectSum('quantity');
    $builder->where([
        'product_id' => $productId,
        'from_location' => $locationId,
        'to_location' => $locationId, // Booked stock has same from/to location
        'movement_type' => 'booked',
        'status' => 'booked'
    ]);
    
    // Handle finishing condition
    if ($finishingId !== null) {
        $builder->where('finishing_id', $finishingId);
    } else {
        $builder->where('finishing_id IS NULL');
    }
    
    $result = $builder->get()->getRow();
    
    return (float)($result->quantity ?? 0);
}
}