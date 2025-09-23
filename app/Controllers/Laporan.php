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
    
    $builder->where('created_at >=', $startDateFormatted);
    $builder->where('created_at <=', $endDateFormatted);
    
    $results = $builder->get()->getResultArray();
    
    // Modifikasi hasil untuk mengubah kode_barang menjadi link dan gabungkan nama_barang dengan nama_finishing
    foreach ($results as &$row) {
        $kodeBarang = $row['kode_barang'];
        $idProduct = $row['id_product'];
        $idFinishing = $row['id_finishing'];
        
        // Buat link untuk kode_barang dengan format tracking/kode_barang/id_finishing
        $row['kode_barang'] = '<a href="tracking/' . $idProduct . '/' . $idFinishing . '">' . $kodeBarang . '</a>';
        
        // Gabungkan nama_barang dengan nama_finishing dipisahkan spasi
        $row['nama_barang'] = $row['nama_barang'] . " | " . $row['nama_finishing'];
    }
    
    return $results;
}

    // 5. Laporan Mutasi Bahan Baku
    protected function getMutasiBahanBaku($startDate, $endDate)
    {
        $builder = $this->laporanModel->builder('laporan_mutasi_bahan_baku');
        
        $builder->select("
            kode_barang,
            nama_barang,
            satuan,
            saldo_awal,
            pemasukan,
            pengeluaran,
            saldo_akhir,
            gudang
        ");
        
        $builder->where('periode >=', $startDate);
        $builder->where('periode <=', $endDate);
        $builder->where('saldo_akhir !=', 0);
        
        return $builder->get()->getResultArray();
    }

    // 6. Laporan Mutasi Hasil Produksi
protected function getMutasiHasilProduksi($startDate, $endDate)
{
    $builder = $this->laporanModel->builder('laporan_mutasi_hasil_produksi');
    
    $builder->select("
        kode_barang,
        nama_barang,
        satuan,
        saldo_awal,
        pemasukan,
        pengeluaran,
        saldo_akhir,
        gudang
    ");
    
    // Format start date to include time (00:00:00)
    $startDate = date('Y-m-d 00:00:00', strtotime($startDate));
    
    // Format end date to include time (23:59:59)
    $endDate = date('Y-m-d 23:59:59', strtotime($endDate));
    
    $builder->where('tanggal >=', $startDate);
    $builder->where('tanggal <=', $endDate);
    
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
        
        $builder->where('tanggal >=', $startDate);
        $builder->where('tanggal <=', $endDate);
        
        return $builder->get()->getResultArray();
    }
}