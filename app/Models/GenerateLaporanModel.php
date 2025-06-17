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
    $exists = $this->db->table('laporan_pemasukan_bahan_baku')
        ->where('tanggal_dokumen >=', $startDate)
        ->where('tanggal_dokumen <=', $endDate)
        ->countAllResults();
        
    if ($exists > 0) {
        return [
            'status' => 'skipped',
            'message' => 'Data laporan pemasukan bahan baku untuk periode ini sudah ada',
            'count' => 0
        ];
    }
    
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
            'Pembelian' as jenis_bukti_penerimaan,
            p.invoice as no_dokumen,
            p.tanggal_nota as tanggal_dokumen,
            NULL as no_bc20,
            NULL as no_bc24,
            NULL as no_bc25,
            NULL as no_bc28,
            m.kode as kode_barang,
            NULL as seri_barang,
            m.name as nama_barang,
            s.id_country as negara_asal,
            sat.nama as satuan,
            pd.jumlah as jumlah,
            c.kode as mata_uang,
            (pd.harga * pd.jumlah) as nilai,
            'Gudang Utama' as penerima,
            'Gudang Utama' as gudang,
            NULL as subkontrak
        ");
        $builder->join('pembelian p', 'p.id = pd.id_pembelian');
        $builder->join('materials m', 'm.id = pd.id_material');
        $builder->join('materials_detail md', 'md.material_id = m.id', 'left');
        $builder->join('satuan sat', 'sat.id = md.satuan_id', 'left');
        $builder->join('currency c', 'c.id = p.id_currency', 'left');
        $builder->join('supplier s', 's.id = p.id_supplier', 'left');
        $builder->where('p.tanggal_nota >=', $startDate);
        $builder->where('p.tanggal_nota <=', $endDate);
        $builder->where('p.deleted_at', null);
        
        $results = $builder->get()->getResultArray();
        
        if (!empty($results)) {
            $this->db->table('laporan_pemasukan_bahan_baku')->insertBatch($results);
            return count($results);
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
            'Ya' as digunakan,
            'Tidak' as disubkontrakkan,
            NULL as penerima_subkontrak
        ");
        $builder->join('material_requisition mr', 'mr.id = mrl.id_material_requisition');
        $builder->join('materials m', 'm.id = mrl.id_material');
        $builder->join('materials_detail md', 'md.material_id = m.id', 'left');
        $builder->join('satuan sat', 'sat.id = md.satuan_id', 'left');
        $builder->where('mr.created_at >=', $startDate);
        $builder->where('mr.created_at <=', $endDate);
        $builder->where('mr.deleted_at', null);
        
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
        $builder = $this->db->table('proforma_invoice_details pid');
        $builder->select("
            pi.peb as no_peb,
            pi.tgl_peb as tanggal_peb,
            CONCAT('DO-', pi.invoice_number) as no_bukti_pengeluaran,
            pi.loading_date as tanggal_bukti,
            c.customer_name as pembeli_penerima,
            c.id_country as negara_tujuan,
            p.kode as kode_barang,
            p.nama as nama_barang,
            pid.unit as satuan,
            pid.quantity as jumlah,
            cur.kode as mata_uang,
            pid.total_price as nilai_barang
        ");
        $builder->join('proforma_invoice pi', 'pi.id = pid.invoice_id');
        $builder->join('product p', 'p.id = pid.id_product');
        $builder->join('customer c', 'c.id = pi.customer_id');
        $builder->join('currency cur', 'cur.id = pi.id_currency');
        $builder->where('pi.tgl_peb >=', $startDate);
        $builder->where('pi.tgl_peb <=', $endDate);
        $builder->where('pi.deleted_at', null);
        $builder->where('pi.peb IS NOT NULL');
        
        $results = $builder->get()->getResultArray();
        
        if (!empty($results)) {
            $this->db->table('laporan_pengeluaran_hasil_produksi')->insertBatch($results);
            return count($results);
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
            ->where('deleted_at', null)
            ->get()
            ->getResultArray();
        
        $records = [];
        $startDate = date('Y-m-01', strtotime($periode));
        $endDate = date('Y-m-t', strtotime($periode));
        
        foreach ($materials as $material) {
            // Hitung saldo awal (dari bulan sebelumnya)
            $saldoAwal = $this->getSaldoAwalBahanBaku($material['id'], $startDate);
            
            // Hitung total pemasukan
            $pemasukan = $this->getTotalPemasukanBahanBaku($material['id'], $startDate, $endDate);
            
            // Hitung total pengeluaran
            $pengeluaran = $this->getTotalPengeluaranBahanBaku($material['id'], $startDate, $endDate);
            
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
        
        // Ambil semua produk
        $products = $this->db->table('proforma_invoice_details')
        ->select('proforma_invoice_details.id_product, p.kode, p.nama')
            ->join('product p', 'p.id = proforma_invoice_details.id_product', 'left')
            ->where('proforma_invoice_details.deleted_at', null)
            ->get()
            ->getResultArray();
        
        $records = [];
        $startDate = date('Y-m-01', strtotime($periode));
        $endDate = date('Y-m-t', strtotime($periode));
        
        foreach ($products as $product) {

            // Hitung saldo awal (dari bulan sebelumnya)
            $saldoAwal = $this->getSaldoAwalHasilProduksi($product['id_product'], $startDate);
            
            // Hitung total pemasukan
            $pemasukan = $this->getTotalPemasukanHasilProduksi($product['id_product'], $startDate, $endDate);
            
            // Hitung total pengeluaran
            $pengeluaran = $this->getTotalPengeluaranHasilProduksi($product['id_product'], $startDate, $endDate)['total'];
            $tanggal = $this->getTotalPengeluaranHasilProduksi($product['id_product'], $startDate, $endDate)['tanggal'];
            
            // Hitung saldo akhir
            $saldoAkhir = $saldoAwal + $pemasukan - $pengeluaran;
            
            $records[] = [
                'kode_barang' => $product['kode'],
                'nama_barang' => $product['nama'],
                'satuan' => 'PCS',
                'saldo_awal' => $saldoAwal,
                'pemasukan' => $pemasukan,
                'pengeluaran' => $pengeluaran,
                'saldo_akhir' => $saldoAkhir,
                'gudang' => 'Gudang Produk',
                'periode' => $periode,
                'tanggal' => $tanggal
            ];
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
    private function getSaldoAwalBahanBaku($materialId, $startDate)
    {
        $previousMonth = date('Y-m', strtotime('-1 month', strtotime($startDate)));
        
        $saldo = $this->db->table('laporan_mutasi_bahan_baku')
            ->select('saldo_akhir')
            ->where('kode_barang', function($builder) use ($materialId) {
                $builder->select('kode')
                    ->from('materials')
                    ->where('id', $materialId);
            })
            ->where('periode', $previousMonth)
            ->get()
            ->getRowArray();
            
        return $saldo ? $saldo['saldo_akhir'] : 0;
    }

    // Fungsi pembantu untuk menghitung total pemasukan bahan baku
    private function getTotalPemasukanBahanBaku($materialId, $startDate, $endDate)
    {
        $result = $this->db->table('pembelian_detail pd')
            ->select('COALESCE(SUM(pd.jumlah), 0) as total')
            ->join('pembelian p', 'p.id = pd.id_pembelian')
            ->where('pd.id_material', $materialId)
            ->where('p.tanggal_nota >=', $startDate)
            ->where('p.tanggal_nota <=', $endDate)
            ->where('p.deleted_at', null)
            ->get()
            ->getRowArray();
            
        return $result['total'] ?? 0;
    }

    // Fungsi pembantu untuk menghitung total pengeluaran bahan baku
    private function getTotalPengeluaranBahanBaku($materialId, $startDate, $endDate)
    {
        $result = $this->db->table('material_requisition_list mrl')
            ->select('COALESCE(SUM(mrl.jumlah), 0) as total')
            ->join('material_requisition mr', 'mr.id = mrl.id_material_requisition')
            ->where('mrl.id_material', $materialId)
            ->where('mr.created_at >=', $startDate)
            ->where('mr.created_at <=', $endDate)
            ->where('mr.deleted_at', null)
            ->get()
            ->getRowArray();
            
        return $result['total'] ?? 0;
    }

    // Fungsi pembantu untuk menghitung saldo awal hasil produksi
    private function getSaldoAwalHasilProduksi($productId, $startDate)
    {
        $previousMonth = date('Y-m', strtotime('-1 month', strtotime($startDate)));
        
        $saldo = $this->db->table('laporan_mutasi_hasil_produksi')
            ->select('saldo_akhir')
            ->where('kode_barang', function($builder) use ($productId) {
                $builder->select('kode')
                    ->from('product')
                    ->where('id', $productId);
            })
            ->where('periode', $previousMonth)
            ->get()
            ->getRowArray();
            
        return $saldo ? $saldo['saldo_akhir'] : 0;
    }

    // Fungsi pembantu untuk menghitung total pemasukan hasil produksi
    private function getTotalPemasukanHasilProduksi($productId, $startDate, $endDate)
    {
        $result = $this->db->table('production_progress pp')
            ->select('COALESCE(SUM(pp.quantity), 0) as total')
            ->where('pp.product_id', $productId)
            ->where('pp.created_at >=', $startDate)
            ->where('pp.created_at <=', $endDate)
            ->where('pp.deleted_at', null)
            ->get()
            ->getRowArray();
            
        return $result['total'] ?? 0;
    }

    // Fungsi pembantu untuk menghitung total pengeluaran hasil produksi
    private function getTotalPengeluaranHasilProduksi($productId, $startDate, $endDate)
    {
        $startDate_ = date('Y-m-d H:i:s', strtotime($startDate));
        $endDate_ = date('Y-m-d H:i:s', strtotime($endDate));
        $result = $this->db->table('proforma_invoice_details pid')
            ->select('pid.quantity as total,pid.created_at as tanggal')
            // ->join('proforma_invoice pi', 'pi.id = pid.invoice_id')
            ->where('pid.id_product', $productId)
            ->where('pid.created_at >=', $startDate_)
            ->where('pid.created_at <=', $endDate_)
            // ->where('pi.deleted_at', null)
            // ->where('pi.peb IS NOT NULL')
            ->get()
            ->getRowArray();
        return $result;
    }
}