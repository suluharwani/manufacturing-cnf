<?php namespace App\Controllers;

use App\Models\LaporanModel;

class Laporan extends BaseController
{
    protected $laporanModel;

    public function __construct()
    {
        $this->laporanModel = new LaporanModel();
    }

    public function getLaporan()
    {
        // Validasi input
        if (!$this->validate([
            'report_type' => 'required|numeric',
            'start_date'  => 'required|valid_date',
            'end_date'    => 'required|valid_date'
        ])) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Invalid input parameters'
            ])->setStatusCode(400);
        }

        $reportType = $this->request->getPost('report_type');
        $startDate = $this->request->getPost('start_date');
        $endDate = $this->request->getPost('end_date');
        
        try {
            switch ($reportType) {
                case 1:
                    $data = $this->getPemasukanBahanBaku($startDate, $endDate);
                    break;
                case 2:
                    $data = $this->getPemakaianBahanBaku($startDate, $endDate);
                    break;
                case 3:
                    $data = $this->getPemasukanHasilProduksi($startDate, $endDate);
                    break;
                case 4:
                    $data = $this->getPengeluaranHasilProduksi($startDate, $endDate);
                    break;
                case 5:
                    $data = $this->getMutasiBahanBaku($startDate, $endDate);
                    break;
                case 6:
                    $data = $this->getMutasiHasilProduksi($startDate, $endDate);
                    break;
                case 7:
                    $data = $this->getWasteScrap($startDate, $endDate);
                    break;
                case 8:
                    $data = [];
                    break;
                default:
                    return $this->response->setJSON([
                        'status' => 'error',
                        'message' => 'Invalid report type'
                    ])->setStatusCode(400);
            }

            return $this->response->setJSON([
                'status' => 'success',
                'data' => $data
            ]);

        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Failed to generate report: ' . $e->getMessage()
            ])->setStatusCode(500);
        }
    }

    // 1. Laporan Pemasukan Bahan Baku
protected function getPemasukanBahanBaku($startDate, $endDate)
{
    $builder = $this->laporanModel->builder('laporan_pemasukan_bahan_baku');
    
    $builder->select("
         tgl_rekam, 
         jenis_dokumen, 
         pabean_nomor, 
         pabean_tanggal, 
         kode_hs, 
         nomor_seri_barang, 
         bukti_penerimaan_nomor, 
         bukti_penerimaan_tanggal, 
         kode_bb, 
         nama_barang, 
         satuan, 
         jumlah, 
         mata_uang, 
         nilai_barang, 
         gudang, 
         penerima_subkontrak, 
         negara_asal_bb
    ");
    $startDate = date('Y-m-d 00:00:00', strtotime($startDate));
    
    // Format end date to include time (23:59:59)
    $endDate = date('Y-m-d 23:59:59', strtotime($endDate));
    $builder->where('bukti_penerimaan_tanggal >=', $startDate);
    $builder->where('bukti_penerimaan_tanggal <=', $endDate);
    
    $results = $builder->get()->getResultArray();
    
    // Format the numbers
    foreach ($results as &$row) {
        $row['jumlah'] = $this->formatNumber($row['jumlah']);
        $row['nilai_barang'] = $this->formatNumber($row['nilai_barang']);
    }
    
    return $results;
}

protected function formatNumber($number)
{
    // Format with thousand separators and 2 decimal places
    $formatted = number_format((float)$number, 2, ',', '.');
    
    // Remove trailing zeros and decimal point if not needed
    return preg_replace('/,00$/', '', $formatted);
}

    // 2. Laporan Pemakaian Bahan Baku
    protected function getPemakaianBahanBaku($startDate, $endDate)
    {
        $builder = $this->laporanModel->builder('laporan_pemakaian_bahan_baku');
        
        $builder->select("
            no_bukti_pengeluaran,
            tanggal,
            kode_barang,
            nama_barang,
            satuan,
            jumlah,
            null,
            null,
            '-'
        ");
        $startDate = date('Y-m-d 00:00:00', strtotime($startDate));
    
    // Format end date to include time (23:59:59)
    $endDate = date('Y-m-d 23:59:59', strtotime($endDate));
        $builder->where('tanggal >=', $startDate);
        $builder->where('tanggal <=', $endDate);
        
        return $builder->get()->getResultArray();
    }

    // 3. Laporan Pemasukan Hasil Produksi
    protected function getPemasukanHasilProduksi($startDate, $endDate)
    {
        $builder = $this->laporanModel->builder('laporan_pemasukan_hasil_produksi');
        
        $builder->select("
            no_dokumen,
            tanggal,
            kode_barang,
            nama_barang,
            satuan,
            jumlah,
            '0' as dari_subkontrak,
            gudang
        ");
        $startDate = date('Y-m-d 00:00:00', strtotime($startDate));
    
    // Format end date to include time (23:59:59)
    $endDate = date('Y-m-d 23:59:59', strtotime($endDate));
        $builder->where('tanggal >=', $startDate);
        $builder->where('tanggal <=', $endDate);
        
        return $builder->get()->getResultArray();
    }

    // 4. Laporan Pengeluaran Hasil Produksi
protected function getPengeluaranHasilProduksi($startDate, $endDate)
{
    $builder = $this->laporanModel->builder('laporan_pengeluaran_hasil_produksi');
    
    $builder->select("
        no_peb,
        tanggal_peb,
        no_bukti_pengeluaran,
        tanggal_bukti,
        pembeli_penerima,
        negara_tujuan,
        kode_barang,
        nama_barang,
        nama_finishing,
        id_product,
        id_finishing,
        'PCS' as satuan,
        jumlah,
        mata_uang,
        FORMAT(nilai_barang, 2) AS nilai_uang
    ");
    
    // Format start date (pastikan sudah dalam format Y-m-d)
    $startDateFormatted = date('Y-m-d 00:00:00', strtotime($startDate));
    
    // Format end date dengan waktu 23:59:59
    $endDateFormatted = date('Y-m-d 23:59:59', strtotime($endDate));
    
    $builder->where('tanggal_bukti >=', $startDateFormatted);
    $builder->where('tanggal_bukti <=', $endDateFormatted);
    
    $results = $builder->get()->getResultArray();
    
    // Array untuk menyimpan hasil akhir
    $finalResults = [];
    
    // Modifikasi hasil untuk mengubah kode_barang menjadi link dan gabungkan nama_barang dengan nama_finishing
    foreach ($results as $row) {
        $kodeBarang = $row['kode_barang'];
        $idProduct = $row['id_product'];
        $idFinishing = $row['id_finishing'];
        
        // Buat link untuk kode_barang dengan format tracking/kode_barang/id_finishing
        $kodeBarangLink = '<a href="tracking/' . $idProduct . '/' . $idFinishing . '">' . $kodeBarang . '</a>';
        
        // Gabungkan nama_barang dengan nama_finishing dipisahkan spasi
        $namaBarangGabung = $row['nama_barang'] . " | " . $row['nama_finishing'];
        
        // Buat array hasil baru hanya dengan field yang diinginkan
        $finalRow = [
            'no_peb' => $row['no_peb'],
            'tanggal_peb' => $row['tanggal_peb'],
            'no_bukti_pengeluaran' => $row['no_bukti_pengeluaran'],
            'tanggal_bukti' => $row['tanggal_bukti'],
            'pembeli_penerima' => $row['pembeli_penerima'],
            'negara_tujuan' => $row['negara_tujuan'],
            'kode_barang' => $kodeBarangLink,
            'nama_barang' => $namaBarangGabung,
            'satuan' => 'PCS',
            'jumlah' => $row['jumlah'],
            'mata_uang' => $row['mata_uang'],
            'nilai_uang' => $row['nilai_uang']
        ];
        
        $finalResults[] = $finalRow;
    }
    
    return $finalResults;
}

    // 5. Laporan Mutasi Bahan Baku
protected function getMutasiBahanBaku($startDate, $endDate)
{
    $builder = $this->laporanModel->builder('laporan_mutasi_bahan_baku');
    
    $builder->select("
        material_id,
        kode_barang,
        nama_barang,
        satuan,
        SUM(CASE WHEN jenis = 'INIT' THEN jumlah ELSE 0 END) as saldo_awal,
        SUM(CASE WHEN jenis = 'IN' THEN jumlah ELSE 0 END) as pemasukan,
        SUM(CASE WHEN jenis = 'OUT' THEN jumlah ELSE 0 END) as pengeluaran,
        (SUM(CASE WHEN jenis = 'INIT' THEN jumlah ELSE 0 END) + 
         SUM(CASE WHEN jenis = 'IN' THEN jumlah ELSE 0 END) - 
         SUM(CASE WHEN jenis = 'OUT' THEN jumlah ELSE 0 END)) as saldo_akhir,
        gudang
    ");
    
    $builder->where('tanggal_transaksi >=', $startDate);
    // Tambahkan waktu hingga 23:59:59 untuk endDate
    $builder->where('tanggal_transaksi <=', $endDate . ' 23:59:59');
    $builder->groupBy('material_id, kode_barang, nama_barang, satuan, gudang');
    $builder->having('saldo_akhir !=', 0);
    
    $results = $builder->get()->getResultArray();
    
    // Format angka untuk menghilangkan 0 di belakang koma dan buat kode_barang menjadi link
    foreach ($results as &$row) {
        $row['saldo_awal'] = $this->formatAngka($row['saldo_awal']);
        $row['pemasukan'] = $this->formatAngka($row['pemasukan']);
        $row['pengeluaran'] = $this->formatAngka($row['pengeluaran']);
        $row['saldo_akhir'] = $this->formatAngka($row['saldo_akhir']);
        
        // Buat kode_barang menjadi link tracking
        $row['kode_barang'] = '<a href="trackmaterial/' . $row['material_id'] . '/' . $startDate . '/' . $endDate . '">' . $row['kode_barang'] . '</a>';
        
        // Hapus material_id dari hasil
        unset($row['material_id']);
    }
    
    return $results;
}
// Fungsi helper untuk format angka
private function formatAngka($value)
{
    if ($value === null || $value === '') {
        return $value;
    }
    
    // Konversi ke float untuk memastikan format yang benar
    $floatValue = floatval($value);
    
    // Jika angka bulat, tampilkan tanpa desimal
    if ($floatValue == intval($floatValue)) {
        return number_format($floatValue, 0, ',', '.');
    }
    
    // Jika ada desimal, tampilkan dengan maksimal 2 digit desimal
    // dan hilangkan 0 di belakang koma
    return rtrim(rtrim(number_format($floatValue, 2, ',', '.'), '0'), ',');
}

    // 6. Laporan Mutasi Hasil Produksi
protected function getMutasiHasilProduksi($startDate, $endDate)
{
    $builder = $this->laporanModel->builder('laporan_mutasi_hasil_produksi');
    
    $builder->select("
        kode_barang,
        CONCAT(nama_barang, ' - ', COALESCE(finishing_name, '-')) as nama_barang,
        satuan,
        SUM(saldo_awal) as saldo_awal,
        SUM(pemasukan) as pemasukan,
        SUM(pengeluaran) as pengeluaran,
        (SUM(saldo_awal) + SUM(pemasukan) - SUM(pengeluaran)) as saldo_akhir,
        gudang,
    ");
    
    // Format start date to include time (00:00:00)
    $startDate = date('Y-m-d 00:00:00', strtotime($startDate));
    
    // Format end date to include time (23:59:59)
    $endDate = date('Y-m-d 23:59:59', strtotime($endDate));
    
    $builder->where('tanggal >=', $startDate);
    $builder->where('tanggal <=', $endDate);
    
    $builder->groupBy('kode_barang');
    $builder->groupBy('finishing_id');
    $builder->groupBy('gudang');
    
    return $builder->get()->getResultArray();
}

    // 7. Laporan Waste/Scrap
    protected function getWasteScrap($startDate, $endDate)
    {
        $builder = $this->laporanModel->builder('laporan_waste_scrap');
        
        $builder->select("
            no_bc24,
            tanggal,
            kode_barang,
            nama_barang,
            satuan,
            jumlah,
            nilai
        ");
        $startDate = date('Y-m-d 00:00:00', strtotime($startDate));
    
    // Format end date to include time (23:59:59)
    $endDate = date('Y-m-d 23:59:59', strtotime($endDate));
        $builder->where('tanggal >=', $startDate);
        $builder->where('tanggal <=', $endDate);
        
        return $builder->get()->getResultArray();
    }
}