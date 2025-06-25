<?php
namespace App\Models;

use CodeIgniter\Model;

class GenerateLaporanModel extends Model
{
    protected $DBGroup = 'default';
    // Generate semua laporan sekaligus dengan pengecekan data yang sudah ada
    public function generateAllLaporan($periode)
    {
        // Format periode untuk laporan harian dan bulanan
        $startDate = date('Y-m-01', strtotime($periode));
        $endDate = date('Y-m-t', strtotime($periode));

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
        $results['mutasi_bahan_baku'] = $this->generateMutasiBahanBakuWithCheck($periode);

        // 6. Mutasi Hasil Produksi (periode bulanan)
        $results['mutasi_hasil_produksi'] = $this->generateMutasiHasilProduksiWithCheck($periode);

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

    public function generateMutasiBahanBakuWithCheck($periode)
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

        $count = $this->generateMutasiBahanBaku($periode);
        return [
            'status' => 'generated',
            'message' => 'Berhasil generate laporan mutasi bahan baku',
            'count' => $count
        ];
    }

    public function generateMutasiHasilProduksiWithCheck($periode)
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

        $count = $this->generateMutasiHasilProduksi($periode);
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
        CASE 
            WHEN p.document LIKE '%BC 2.0%' THEN 'BC 2.0'
            WHEN p.document LIKE '%BC 2.4%' THEN 'BC 2.4'
            WHEN p.document LIKE '%BC 2.5%' THEN 'BC 2.5'
            WHEN p.document LIKE '%BC 2.8%' THEN 'BC 2.8'
            ELSE 'Document Import'
        END AS jenis_dokumen,
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
        $builder = $this->db->table('production_progress pp');
        $builder->select("
            CONCAT('PROD-', wo.kode) as no_dokumen,
            pp.created_at as tanggal,
            p.kode as kode_barang,
            p.nama as nama_barang,
            'PCS' as satuan,
            pp.quantity as jumlah,
            pp.quantity as dari_produksi,
            0 as dari_subkontrak,
            w.name as gudang
        ");
        $builder->join('work_order wo', 'wo.id = pp.wo_id');
        $builder->join('product p', 'p.id = pp.product_id');
        $builder->join('warehouses w', 'w.id = pp.warehouse_id');
        $builder->where('pp.created_at >=', $startDate);
        $builder->where('pp.created_at <=', $endDate);
        $builder->where('pp.deleted_at', null);

        $results = $builder->get()->getResultArray();

        if (!empty($results)) {
            $this->db->table('laporan_pemasukan_hasil_produksi')->insertBatch($results);
            return count($results);
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
    public function generateMutasiBahanBaku($periode)
    {
        // Pertama, hapus data lama untuk periode yang sama
        $this->db->table('laporan_mutasi_bahan_baku')->where('periode', $periode)->delete();

        // Ambil semua material
        $materials = $this->db->table('materials')
            ->join('materials_detail md', 'md.material_id = materials.id', 'left')
            ->where('materials.deleted_at', null)
            ->where('md.kite', 'KITE')
            ->get()
            ->getResultArray();

        $records = [];
        $startDate = date('Y-m-01', strtotime($periode));
        $endDate = date('Y-m-t', strtotime($periode));

        foreach ($materials as $material) {
            // Hitung saldo awal (dari bulan sebelumnya)
            $saldoAwal = $this->getSaldoBahanBaku($material['id'], $startDate)['stock_awal'] ?? 0;

            // Hitung total pemasukan
            $pemasukan = $this->getSaldoBahanBaku($material['id'], $startDate)['stock_masuk'] ?? 0;

            // Hitung total pengeluaran
            $pengeluaran = $this->getSaldoBahanBaku($material['id'], $startDate)['stock_keluar'] ?? 0;

            // Hitung saldo akhir
            $saldoAkhir = $saldoAwal + $pemasukan - $pengeluaran;

            // Dapatkan satuan
            $satuan = $this->db->table('materials_detail md')
                ->select('sat.nama')
                ->join('satuan sat', 'sat.id = md.satuan_id', 'left')
                ->where('md.material_id', $material['id'])
                ->get()
                ->getRowArray();

            $records[] = [
                'kode_barang' => $material['kode'],
                'nama_barang' => $material['name'],
                'satuan' => $satuan['nama'] ?? 'PCS',
                'saldo_awal' => $saldoAwal,
                'pemasukan' => $pemasukan,
                'pengeluaran' => $pengeluaran,
                'saldo_akhir' => $saldoAkhir,
                'gudang' => 'Gudang Utama',
                'periode' => $periode
            ];
        }

        if (!empty($records)) {
            $this->db->table('laporan_mutasi_bahan_baku')->insertBatch($records);
            return count($records);
        }

        return 0;
    }

    // Model untuk laporan mutasi hasil produksi
    public function generateMutasiHasilProduksi($periode)
    {
        // Pertama, hapus data lama untuk periode yang sama
        $this->db->table('laporan_mutasi_hasil_produksi')->where('periode', $periode)->delete();

        // Ambil semua gudang yang aktif
        $warehouses = $this->db->table('locations')
            ->select('id, code, name')
            ->where('is_active', 1)
            ->where('type', 'warehouse')
            ->get()
            ->getResultArray();

        // Ambil semua produk yang memiliki data awal atau pergerakan stok
        $products = $this->db->table('st_initial')
            ->select('st_initial.product_id, p.kode, p.nama')
            ->join('product p', 'p.id = st_initial.product_id', 'left')
            ->groupBy('st_initial.product_id')
            ->get()
            ->getResultArray();

        // Jika tidak ada data awal, ambil dari produk yang memiliki pergerakan
        if (empty($products)) {
            $products = $this->db->table('st_movement')
                ->select('st_movement.product_id, p.kode, p.nama')
                ->join('product p', 'p.id = st_movement.product_id', 'left')
                ->groupBy('st_movement.product_id')
                ->get()
                ->getResultArray();
        }

        $records = [];
        $startDate = date('Y-m-01', strtotime($periode));
        $endDate = date('Y-m-t', strtotime($periode));

        foreach ($warehouses as $warehouse) {
            foreach ($products as $product) {
                // Hitung saldo awal dari tabel st_initial
                $saldoAwal = $this->getSaldoAwalHasilProduksi($product['product_id'], $warehouse['id']);

                // Hitung total pemasukan untuk gudang ini
                $pemasukanData = $this->getTotalPemasukanHasilProduksi($product['product_id'], $warehouse['id'], $startDate, $endDate);
                $pemasukan = $pemasukanData['total'];
                $tanggalMasuk = $pemasukanData['tanggal'];

                // Hitung total pengeluaran untuk gudang ini
                $pengeluaranData = $this->getTotalPengeluaranHasilProduksi($product['product_id'], $warehouse['id'], $startDate, $endDate);
                $pengeluaran = $pengeluaranData['total'];
                $tanggalKeluar = $pengeluaranData['tanggal'];

                // Hitung saldo akhir
                $saldoAkhir = $saldoAwal + $pemasukan - $pengeluaran;

                // Skip jika semua nilai 0 (tidak ada pergerakan)
                if ($saldoAwal == 0 && $pemasukan == 0 && $pengeluaran == 0) {
                    continue;
                }

                // Ambil tanggal terbaru antara masuk dan keluar
                $tanggal = max($tanggalMasuk, $tanggalKeluar);

                $records[] = [
                    'kode_barang' => $product['kode'],
                    'nama_barang' => $product['nama'],
                    'satuan' => 'PCS',
                    'saldo_awal' => $saldoAwal,
                    'pemasukan' => $pemasukan,
                    'pengeluaran' => $pengeluaran,
                    'saldo_akhir' => $saldoAkhir,
                    'gudang' => $warehouse['name'],
                    'gudang_kode' => $warehouse['code'],
                    'periode' => $periode,
                    'tanggal' => $tanggal
                ];
            }
        }

        if (!empty($records)) {
            $this->db->table('laporan_mutasi_hasil_produksi')->insertBatch($records);
            return count($records);
        }

        return 0;
    }

    // Model untuk laporan waste/scrap
    public function generateWasteScrap($startDate, $endDate)
    {
        $builder = $this->db->table('scrap s');
        $builder->select("
            sd.code as no_bc24,
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

        $results = $builder->get()->getResultArray();

        if (!empty($results)) {
            $this->db->table('laporan_waste_scrap')->insertBatch($results);
            return count($results);
        }

        return 0;
    }

    // Fungsi pembantu untuk menghitung saldo awal bahan baku
    private function getSaldoBahanBaku($materialId, $startDate)
    {

        $saldo = $this->db->table('stock')
            ->select('stock_awal, stock_masuk, stock_keluar')
            ->where('id_material', $materialId)
            ->get()
            ->getRowArray();

        return $saldo;
    }

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