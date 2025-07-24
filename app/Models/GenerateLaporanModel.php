<?php
namespace App\Models;

use CodeIgniter\Model;

class GenerateLaporanModel extends Model
{
    protected $DBGroup = 'default';
    // Generate semua laporan sekaligus dengan pengecekan data yang sudah ada
    public function generateAllLaporan($p, $end)
    {
        // Format periode untuk laporan harian dan bulanan
        $periode = $end;
        $startDate = $p;
        $endDate = $end;
    
        $results = [];

        // 1. Pemasukan Bahan Baku
        $results['pemasukan_bahan_baku'] = $this->generatePemasukanBahanBakuWithCheck($startDate, $endDate);

        // 2. Pemakaian Bahan Baku
        $results['pemakaian_bahan_baku'] = $this->generatePemakaianBahanBakuWithCheck($startDate, $endDate);

        // 3. Pemasukan Hasil Produksi
        $results['pemasukan_hasil_produksi'] = $this->generatePemasukanHasilProduksiWithCheck($startDate, $endDate);

        // 4. Pengeluaran Hasil Produksi
        $results['pengeluaran_hasil_produksi'] = $this->generatePengeluaranHasilProduksiWithCheck($startDate, $endDate);

        // 5. Mutasi Bahan Baku (periode bulanan)
        $results['mutasi_bahan_baku'] = $this->generateMutasiBahanBakuWithCheck($periode,$startDate, $endDate);

        // 6. Mutasi Hasil Produksi (periode bulanan)
        $results['mutasi_hasil_produksi'] = $this->generateMutasiHasilProduksiWithCheck($periode, $startDate, $endDate);

        // 7. Waste/Scrap
        $results['waste_scrap'] = $this->generateWasteScrapWithCheck($startDate, $endDate);

        return $results;
    }

    // Versi dengan pengecekan untuk setiap laporan
    public function generatePemasukanBahanBakuWithCheck($startDate, $endDate)
    {
        // Cek apakah data untuk periode ini sudah ada


        $count = $this->generatePemasukanBahanBaku($startDate, $endDate);
        return [
            'status' => 'generated',
            'message' => 'Berhasil generate laporan pemasukan bahan baku',
            'count' => $count
        ];
    }

    public function generatePemakaianBahanBakuWithCheck($startDate, $endDate)
    {
        $exists = $this->db->table('laporan_pemakaian_bahan_baku')
            ->where('tanggal >=', $startDate)
            ->where('tanggal <=', $endDate)
            ->countAllResults();

        if ($exists > 0) {
            return [
                'status' => 'skipped',
                'message' => 'Data laporan pemakaian bahan baku untuk periode ini sudah ada',
                'count' => 0
            ];
        }

        $count = $this->generatePemakaianBahanBaku($startDate, $endDate);
        return [
            'status' => 'generated',
            'message' => 'Berhasil generate laporan pemakaian bahan baku',
            'count' => $count
        ];
    }

    public function generatePemasukanHasilProduksiWithCheck($startDate, $endDate)
    {
        $exists = $this->db->table('laporan_pemasukan_hasil_produksi')
            ->where('tanggal >=', $startDate)
            ->where('tanggal <=', $endDate)
            ->countAllResults();

        if ($exists > 0) {
            return [
                'status' => 'skipped',
                'message' => 'Data laporan pemasukan hasil produksi untuk periode ini sudah ada',
                'count' => 0
            ];
        }

        $count = $this->generatePemasukanHasilProduksi($startDate, $endDate);
        return [
            'status' => 'generated',
            'message' => 'Berhasil generate laporan pemasukan hasil produksi',
            'count' => $count
        ];
    }

    public function generatePengeluaranHasilProduksiWithCheck($startDate, $endDate)
    {
        $exists = $this->db->table('laporan_pengeluaran_hasil_produksi')
            ->where('tanggal_peb >=', $startDate)
            ->where('tanggal_peb <=', $endDate)
            ->countAllResults();

        if ($exists > 0) {
            return [
                'status' => 'skipped',
                'message' => 'Data laporan pengeluaran hasil produksi untuk periode ini sudah ada',
                'count' => 0
            ];
        }

        $count = $this->generatePengeluaranHasilProduksi($startDate, $endDate);
        return [
            'status' => 'generated',
            'message' => 'Berhasil generate laporan pengeluaran hasil produksi',
            'count' => $count
        ];
    }

    public function generateMutasiBahanBakuWithCheck($periode,$startDate, $endDate)
    {
        $exists = $this->db->table('laporan_mutasi_bahan_baku')
            ->where('periode', $periode)
            ->countAllResults();

        if ($exists > 0) {
            return [
                'status' => 'skipped',
                'message' => 'Data laporan mutasi bahan baku untuk periode ini sudah ada',
                'count' => 0
            ];
        }

        $count = $this->generateMutasiBahanBaku($periode,$startDate, $endDate);
        return [
            'status' => 'generated',
            'message' => 'Berhasil generate laporan mutasi bahan baku',
            'count' => $count
        ];
    }

    public function generateMutasiHasilProduksiWithCheck($periode,$startDate, $endDate)
    {
        $exists = $this->db->table('laporan_mutasi_hasil_produksi')
            ->where('periode', $periode)
            ->countAllResults();

        if ($exists > 0) {
            return [
                'status' => 'skipped',
                'message' => 'Data laporan mutasi hasil produksi untuk periode ini sudah ada',
                'count' => 0
            ];
        }

        $count = $this->generateMutasiHasilProduksi($periode,$startDate, $endDate);
        return [
            'status' => 'generated',
            'message' => 'Berhasil generate laporan mutasi hasil produksi',
            'count' => $count
        ];
    }

    public function generateWasteScrapWithCheck($startDate, $endDate)
    {
        $exists = $this->db->table('laporan_waste_scrap')
            ->where('tanggal >=', $startDate)
            ->where('tanggal <=', $endDate)
            ->countAllResults();

        if ($exists > 0) {
            return [
                'status' => 'skipped',
                'message' => 'Data laporan waste/scrap untuk periode ini sudah ada',
                'count' => 0
            ];
        }

        $count = $this->generateWasteScrap($startDate, $endDate);
        return [
            'status' => 'generated',
            'message' => 'Berhasil generate laporan waste/scrap',
            'count' => $count
        ];
    }
    // Model untuk laporan pemasukan bahan baku
public function generatePemasukanBahanBaku($startDate, $endDate)
{
    $builder = $this->db->table('pembelian_detail pd');
    $builder->select("
        ROW_NUMBER() OVER (ORDER BY p.tanggal_nota) AS no,
        CURRENT_DATE() AS tgl_rekam,
        p.jenis_doc AS jenis_dokumen,
        p.document AS pabean_nomor,
        p.tanggal_nota AS pabean_tanggal,
        md.hscode AS kode_hs,
        m.kode AS nomor_seri_barang,
        p.invoice AS bukti_penerimaan_nomor,
        p.tanggal_nota AS bukti_penerimaan_tanggal,
        m.kode AS kode_bb,
        m.name AS nama_barang,
        sat.nama AS satuan,
        pd.jumlah AS jumlah,
        c.kode AS mata_uang,
        (pd.harga * pd.jumlah) AS nilai_barang,
        'Gudang Utama' AS gudang,
        NULL AS penerima_subkontrak,
        country.country_name AS negara_asal_bb
    ");

    $builder->join('pembelian p', 'p.id = pd.id_pembelian');
    $builder->join('materials m', 'm.id = pd.id_material', 'left');
    $builder->join('materials_detail md', 'md.material_id = m.id', 'left');
    $builder->join('satuan sat', 'sat.id = md.satuan_id', 'left');
    $builder->join('supplier s', 's.id = p.id_supplier', 'left');
    $builder->join('currency c', 's.id_currency = c.id', 'left');
    $builder->join('country_data country', 's.id_country = country.id_country', 'left');

    $builder->where('p.deleted_at', null);
    $builder->where('p.posting', 1);
    $builder->orderBy('p.invoice');

    $results = $builder->get()->getResultArray();

    if (!empty($results)) {
        // Check for existing records based on unique fields
        $existingRecords = $this->db->table('laporan_pemasukan_bahan_baku')
            ->select('pabean_nomor, bukti_penerimaan_nomor, kode_bb')
            ->get()
            ->getResultArray();
            
        // Convert existing records to a hash for quick lookup
        $existingHashes = [];
        foreach ($existingRecords as $record) {
            $key = $record['pabean_nomor'] . '|' . $record['bukti_penerimaan_nomor'] . '|' . $record['kode_bb'];
            $existingHashes[$key] = true;
        }
        
        // Filter out records that already exist
        $newRecords = [];
        foreach ($results as $record) {
            $key = $record['pabean_nomor'] . '|' . $record['bukti_penerimaan_nomor'] . '|' . $record['kode_bb'];
            if (!isset($existingHashes[$key])) {
                $newRecords[] = $record;
            }
        }
        
        // Insert only new records
        if (!empty($newRecords)) {
            $this->db->table('laporan_pemasukan_bahan_baku')->insertBatch($newRecords);
            return count($newRecords);
        }
        
        return 0; // All records already existed
    }

    return 0;
}

    // Model untuk laporan pemakaian bahan baku
    public function generatePemakaianBahanBaku($startDate, $endDate)
    {
        
        $builder = $this->db->table('material_requisition_list mrl');
        $builder->select("
            CONCAT('MR-', mr.code) as no_bukti_pengeluaran,
            mr.created_at as tanggal,
            m.kode as kode_barang,
            m.name as nama_barang,
            sat.nama as satuan,
            mrl.jumlah as jumlah,
            mr.remarks as digunakan,
            'Tidak' as disubkontrakkan,
            mr.requestor as penerima_subkontrak
        ");
        $builder->join('material_requisition mr', 'mr.id = mrl.id_material_requisition');
        $builder->join('materials m', 'm.id = mrl.id_material');
        $builder->join('materials_detail md', 'md.material_id = m.id', 'left');
        $builder->join('satuan sat', 'sat.id = md.satuan_id', 'left');
        $builder->where('mr.created_at >=', $startDate);
        $builder->where('mr.created_at <=', $endDate);
        $builder->where('mr.deleted_at', null);
        $builder->where('md.kite', 'KITE');

        $results = $builder->get()->getResultArray();

        if (!empty($results)) {
            $this->db->table('laporan_pemakaian_bahan_baku')->insertBatch($results);
            return count($results);
        }

        return 0;
    }

    // Model untuk laporan pemasukan hasil produksi
    public function generatePemasukanHasilProduksi($startDate, $endDate)
    {
         $builder = $this->db->table('st_movement sm');
    
    $builder->select("
        sm.code as no_dokumen,
        sm.created_at as tanggal,
        p.kode as kode_barang,
        p.nama as nama_barang,
        'PCS' as satuan,
        sm.quantity as jumlah,
        'Produksi' as dari_produksi,
        'Tidak' as dari_subkontrak,
        'Gudang Utama' as gudang,
    ");
    
    $builder->join('product p', 'sm.product_id = p.id', 'inner');
    
    $builder->where('sm.movement_type', 'in');
    
    // Tambahkan filter tanggal jika diperlukan

    
    $results = $builder->get()->getResultArray();
    
    if (!empty($results)) {
        $insertedCount = 0;
        // $datacheck = [];
        foreach ($results as $row) {
            // Cek apakah data sudah ada
            $exists = $this->db->table('laporan_pemasukan_hasil_produksi')
                ->where('no_dokumen', $row['no_dokumen'])
                ->where('nama_barang', $row['nama_barang'])
                ->where('jumlah', $row['jumlah'])
                ->countAllResults();
                
            //  array_push($datacheck, $exists);
      
            if ($exists == 0) {
                $this->db->table('laporan_pemasukan_hasil_produksi')->insert($row);
                $insertedCount++;
            }
        }
        // var_dump($datacheck);
        //     die();
        return $insertedCount;
    }

    return 0;
    }

    // Model untuk laporan pengeluaran hasil produksi
public function generatePengeluaranHasilProduksi($startDate, $endDate)
{
    $builder = $this->db->table('st_movement sm');
    
    $builder->select("
        sm.id AS id_movement,
        pi.peb AS no_peb,
        pi.tgl_peb AS tanggal_peb,
        pi.invoice_number AS no_bukti_pengeluaran,
        pi.invoice_date AS tanggal_bukti,
        c.customer_name AS pembeli_penerima,
        pi.port_discharge AS negara_tujuan,
        p.kode AS kode_barang,
        p.nama AS nama_barang,
        sm.quantity AS jumlah,
        (SELECT nama FROM currency WHERE id = pi.id_currency) AS mata_uang,
        (SELECT unit_price FROM proforma_invoice_details 
         WHERE invoice_id = pi.id AND id_product = sm.product_id LIMIT 1) * sm.quantity AS nilai_barang,
        NOW() AS created_at
    ");
    
    $builder->join('product p', 'sm.product_id = p.id', 'inner');
    $builder->join('proforma_invoice pi', 'sm.reference_id = pi.id', 'inner');
    $builder->join('customer c', 'pi.customer_id = c.id', 'left');
    
    $builder->where('sm.movement_type', 'out');
    $builder->where('sm.reference_type', 'proforma_invoice');
    $builder->where('sm.status', 'completed');
    
    // Tambahkan filter tanggal jika diperlukan

    
    $results = $builder->get()->getResultArray();
    
    if (!empty($results)) {
        $insertedCount = 0;
        
        foreach ($results as $row) {
            // Cek apakah data sudah ada
            $exists = $this->db->table('laporan_pengeluaran_hasil_produksi')
                ->where('no_peb', $row['no_peb'])
                ->where('no_bukti_pengeluaran', $row['no_bukti_pengeluaran'])
                ->where('kode_barang', $row['kode_barang'])
                ->where('jumlah', $row['jumlah'])
                ->where('id_movement', $row['id_movement'])
                ->countAllResults();
            
            if ($exists == 0) {
                $this->db->table('laporan_pengeluaran_hasil_produksi')->insert($row);
                $insertedCount++;
            }
        }
        
        return $insertedCount;
    }

    return 0;
}

    // Model untuk laporan mutasi bahan baku
    public function generateMutasiBahanBaku($periode,$startDate, $endDate)
    {
        // Pertama, hapus data lama untuk periode yang sama
        $this->db->table('laporan_mutasi_bahan_baku')->where('periode', $periode)->delete();

        // Ambil semua material
        $materials = $this->db->table('materials')
            ->select('materials.id as material_id, materials.kode as kode, materials.name as name')
            ->join('materials_detail md', 'md.material_id = materials.id', 'left')
            ->where('md.kite', 'KITE')
            ->get()
            ->getResultArray();

        $records = [];
 

        foreach ($materials as $material) {
            // Hitung saldo awal (dari bulan sebelumnya)
            $saldo = $this->getSaldoBahanBaku($material['material_id']);
            // Hitung saldo akhir
            $saldoawal = $saldo['stock_awal'] ?? 0;
            $saldomasuk = $saldo['stock_masuk'] ?? 0;
            $saldokeluar = $saldo['stock_keluar'] ?? 0;
            
            $saldoAkhir = $saldoawal + $saldomasuk - $saldokeluar;
            

            $records[] = [
                'material_id' => $material['material_id'],
                'kode_barang' => $material['kode'],
                'nama_barang' => $material['name'],
                'satuan' => $satuan['nama'] ?? 'PCS',
                'saldo_awal' => $saldoawal,
                'pemasukan' => $saldomasuk,
                'pengeluaran' => $saldokeluar,
                'saldo_akhir' => $saldoAkhir,
                'gudang' => 'Logistik',
                'periode' => $periode
            ];
        }
        // var_dump($records);
        // die();
        if (!empty($records)) {
            $this->db->table('laporan_mutasi_bahan_baku')->insertBatch($records);
            return count($records);
        }

        return 0;
    }
    private function getSaldoBahanBaku($materialId)
    {

        $saldo = $this->db->table('stock')
            ->select('stock.stock_awal, stock.stock_masuk, stock.stock_keluar, sat.nama as satuan')
            ->join('materials_detail md', 'md.material_id = stock.id_material', 'left')
            ->join('satuan sat', 'sat.id = md.satuan_id', 'left')
            ->where('id_material', $materialId)
            ->limit(1)
            ->get()
            ->getRowArray();

        return $saldo;
    }
    // Model untuk laporan mutasi hasil produksi
public function generateMutasiHasilProduksi($periode, $startDate, $endDate)
{
    // Check if data already exists for this period
    $existingData = $this->db->table('laporan_mutasi_hasil_produksi')
        ->where('periode', $periode)
        ->countAllResults();
    
    if ($existingData > 0) {
        return 0; // Skip generation as data already exists
    }

    // Build the complete query with parameterized dates
    $query = "
    INSERT INTO laporan_mutasi_hasil_produksi (
        kode_barang, 
        nama_barang, 
        satuan, 
        saldo_awal, 
        pemasukan, 
        pengeluaran, 
        saldo_akhir, 
        gudang, 
        gudang_kode, 
        periode, 
        tanggal
    )
    SELECT 
        p.kode COLLATE utf8mb4_uca1400_ai_ci AS kode_barang,
        p.nama COLLATE utf8mb4_uca1400_ai_ci AS nama_barang,
        'pcs' AS satuan,
        COALESCE((
            SELECT si.quantity 
            FROM st_initial si 
            WHERE si.product_id = p.id 
            AND si.location_id = l.id
        ), 0) AS saldo_awal,

        COALESCE((
            SELECT SUM(sm.quantity) 
            FROM st_movement sm 
            WHERE sm.product_id = p.id 
            AND sm.to_location = l.id 
            AND (sm.movement_type = 'in' OR sm.movement_type = 'transfer')
            AND DATE(sm.created_at) BETWEEN ? AND ?
        ), 0) AS pemasukan,
        
        COALESCE((
            SELECT SUM(sm.quantity) 
            FROM st_movement sm 
            WHERE sm.product_id = p.id 
            AND sm.from_location = l.id 
            AND (sm.movement_type = 'out' OR sm.movement_type = 'transfer')
            AND DATE(sm.created_at) BETWEEN ? AND ?
        ), 0) AS pengeluaran,
        
        (COALESCE((
            SELECT si.quantity 
            FROM st_initial si 
            WHERE si.product_id = p.id 
            AND si.location_id = l.id
        ), 0) +
        COALESCE((
            SELECT SUM(sm.quantity) 
            FROM st_movement sm 
            WHERE sm.product_id = p.id 
            AND sm.to_location = l.id 
            AND (sm.movement_type = 'in' OR sm.movement_type = 'transfer')
            AND DATE(sm.created_at) BETWEEN ? AND ?
        ), 0) -
        COALESCE((
            SELECT SUM(sm.quantity) 
            FROM st_movement sm 
            WHERE sm.product_id = p.id 
            AND sm.from_location = l.id 
            AND (sm.movement_type = 'out' OR sm.movement_type = 'transfer')
            AND DATE(sm.created_at) BETWEEN ? AND ?
        ), 0)) AS saldo_akhir,
        l.name AS gudang,
        l.code COLLATE utf8mb4_uca1400_ai_ci AS gudang_kode,
        ? AS periode,
        NOW() AS tanggal
    FROM 
        product p
    CROSS JOIN 
        locations l
    LEFT JOIN 
        laporan_mutasi_hasil_produksi existing 
        ON existing.kode_barang = p.kode COLLATE utf8mb4_uca1400_ai_ci
        AND existing.gudang_kode = l.code COLLATE utf8mb4_uca1400_ai_ci
        AND existing.periode = ?
    WHERE 
        l.type = 'warehouse' 
        AND l.deleted_at IS NULL
        AND p.deleted_at IS NULL
        AND existing.id IS NULL -- Skip if already exists
        AND (
            EXISTS (
                SELECT 1 FROM st_initial si 
                WHERE si.product_id = p.id 
                AND si.location_id = l.id
            )
            OR EXISTS (
                SELECT 1 FROM st_movement sm 
                WHERE sm.product_id = p.id 
                AND (sm.from_location = l.id OR sm.to_location = l.id)
                AND DATE(sm.created_at) BETWEEN ? AND ?
            )
        )";

    // Execute the query with parameters
    $result = $this->db->query($query, [
        $startDate, $endDate,    // pemasukan date range
        $startDate, $endDate,    // pengeluaran date range
        $startDate, $endDate,    // saldo_akhir pemasukan
        $startDate, $endDate,    // saldo_akhir pengeluaran
        $periode,                // periode value
        $periode,               // existing check periode
        $startDate, $endDate     // EXISTS check date range
    ]);

    // Return number of affected rows
    return $this->db->affectedRows();
}

    // Model untuk laporan waste/scrap
    public function generateWasteScrap($startDate, $endDate)
    {
        $builder = $this->db->table('scrap s');
        $builder->select("
            sd.document_bc as no_bc24,
            s.created_at as tanggal,
            m.kode as kode_barang,
            m.name as nama_barang,
            sat.nama as satuan,
            s.quantity as jumlah,
            (s.quantity * st.price) as nilai
        ");
        $builder->join('scrap_doc sd', 'sd.id = s.scrap_doc_id');
        $builder->join('materials m', 'm.id = s.material_id');
        $builder->join('materials_detail md', 'md.material_id = m.id', 'left');
        $builder->join('satuan sat', 'sat.id = md.satuan_id', 'left');
        $builder->join('stock st', 'st.id_material = m.id', 'left');
        $builder->where('s.created_at >=', $startDate);
        $builder->where('s.created_at <=', $endDate);
        $builder->where('s.deleted_at', null);
        $builder->where('sd.status', 1);

        $results = $builder->get()->getResultArray();

        if (!empty($results)) {
            $this->db->table('laporan_waste_scrap')->insertBatch($results);
            return count($results);
        }

        return 0;
    }

    // Fungsi pembantu untuk menghitung saldo awal bahan baku


    // Fungsi pembantu untuk menghitung saldo awal hasil produksi
    private function getSaldoAwalHasilProduksi($productId, $locationId)
    {
        // Ambil saldo awal dari tabel st_initial
        $initialStock = $this->db->table('st_initial')
            ->select('quantity')
            ->where('product_id', $productId)
            ->where('location_id', $locationId)
            ->where('deleted_at', null)
            ->get()
            ->getRow();

        return $initialStock ? $initialStock->quantity : 0;
    }

    // private function getTotalPemasukanHasilProduksi($productId, $locationId, $startDate, $endDate)
// {

    //     // Debug: Check if we're getting the expected parameters
//     // You can log these values to verify
//     // log_message('debug', 'Params: productId='.$productId.', locationId='.$locationId.', startDate='.$startDate.', endDate='.$endDate);

    //     // Get all 'in' movements to this location during the period
//     $inQuery = $this->db->table('st_movement')
//         ->select('quantity, created_at, reference_type')
//         ->where('product_id', $productId)
//         ->where('movement_type', 'in')
//         ->where('to_location', $locationId)
//         ->where('created_at >=', $startDate)
//         ->where('created_at <=', $endDate)
//         ->get();

    //     $totalIn = 0;
//     foreach ($inQuery->getResult() as $row) {
//         $totalIn += $row->quantity;
//     }

    //     // Get all transfers in to this location during the period
//     $transferQuery = $this->db->table('st_movement')
//         ->select('quantity, created_at, reference_type')
//         ->where('product_id', $productId)
//         ->where('movement_type', 'transfer')
//         ->where('to_location', $locationId)
//         ->where('created_at >=', $startDate)
//         ->where('created_at <=', $endDate)
//         ->get();

    //     $transfersIn = 0;
//     foreach ($transferQuery->getResult() as $row) {
//         $transfersIn += $row->quantity;
//     }

    //     // Debug: Log the queries being executed
//     // log_message('debug', 'In movements query: '.$this->db->getLastQuery());
//     // log_message('debug', 'Transfer movements query: '.$this->db->getLastQuery());
//     // log_message('debug', 'Total in: '.$totalIn.', Transfers in: '.$transfersIn);

    //     return $totalIn + $transfersIn;
// }

    // private function getTotalPengeluaranHasilProduksi($productId, $locationId, $startDate, $endDate)
// {
//     // Get all 'out' movements from this location during the period
//     $outQuery = $this->db->table('st_movement')
//         ->select('SUM(quantity) as total, MAX(created_at) as tanggal')
//         ->where('product_id', $productId)
//         ->where('movement_type', 'out')
//         ->where('from_location', $locationId)
//         ->where('created_at >=', $startDate)
//         ->where('created_at <=', $endDate)
//         ->get()
//         ->getRow();

    //     $totalOut = $outQuery->total ?? 0;
//     $tanggal = $outQuery->tanggal ?? null;

    //     // Get all transfers out from this location during the period
//     $transfersOut = $this->db->table('st_movement')
//         ->selectSum('quantity')
//         ->where('product_id', $productId)
//         ->where('movement_type', 'transfer')
//         ->where('from_location', $locationId)
//         ->where('created_at >=', $startDate)
//         ->where('created_at <=', $endDate)
//         ->get()
//         ->getRow()
//         ->quantity ?? 0;

    //     return [
//         'total' => $totalOut + $transfersOut,
//         'tanggal' => $tanggal
//     ];
// }
    private function getTotalPemasukanHasilProduksi($productId, $locationId, $startDate, $endDate)
    {
        // Get all 'in' movements to this location during the period
        $inQuery = $this->db->table('st_movement')
            ->select('SUM(quantity) as total, MAX(created_at) as tanggal')
            ->where('product_id', $productId)
            ->where('movement_type', 'in')
            ->where('to_location', $locationId)
            ->where('created_at >=', $startDate)
            ->where('created_at <=', $endDate)
            ->get()
            ->getRow();

        $totalIn = $inQuery->total ?? 0;
        $tanggalIn = $inQuery->tanggal ?? null;

        // Get all transfers in to this location during the period
        $transferQuery = $this->db->table('st_movement')
            ->select('SUM(quantity) as total, MAX(created_at) as tanggal')
            ->where('product_id', $productId)
            ->where('movement_type', 'transfer')
            ->where('to_location', $locationId)
            ->where('created_at >=', $startDate)
            ->where('created_at <=', $endDate)
            ->get()
            ->getRow();

        $transfersIn = $transferQuery->total ?? 0;
        $tanggalTransferIn = $transferQuery->tanggal ?? null;

        // Combine results
        $total = $totalIn + $transfersIn;
        $tanggal = max($tanggalIn, $tanggalTransferIn);

        return [
            'total' => $total,
            'tanggal' => $tanggal
        ];
    }

    private function getTotalPengeluaranHasilProduksi($productId, $locationId, $startDate, $endDate)
    {
        // Get all 'out' movements from this location during the period
        $outQuery = $this->db->table('st_movement')
            ->select('SUM(quantity) as total, MAX(created_at) as tanggal')
            ->where('product_id', $productId)
            ->where('movement_type', 'out')
            ->where('from_location', $locationId)
            ->where('created_at >=', $startDate)
            ->where('created_at <=', $endDate)
            ->get()
            ->getRow();

        $totalOut = $outQuery->total ?? 0;
        $tanggalOut = $outQuery->tanggal ?? null;

        // Get all transfers out from this location during the period
        $transferQuery = $this->db->table('st_movement')
            ->select('SUM(quantity) as total, MAX(created_at) as tanggal')
            ->where('product_id', $productId)
            ->where('movement_type', 'transfer')
            ->where('from_location', $locationId)
            ->where('created_at >=', $startDate)
            ->where('created_at <=', $endDate)
            ->get()
            ->getRow();

        $transfersOut = $transferQuery->total ?? 0;
        $tanggalTransferOut = $transferQuery->tanggal ?? null;

        // Combine results
        $total = $totalOut + $transfersOut;
        $tanggal = max($tanggalOut, $tanggalTransferOut);

        return [
            'total' => $total,
            'tanggal' => $tanggal
        ];
    }
}