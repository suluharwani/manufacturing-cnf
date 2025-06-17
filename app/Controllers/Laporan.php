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
            jenis_bukti_penerimaan,
            no_dokumen,
            tanggal_dokumen,
            no_bc20,
            no_bc24,
            no_bc25,
            no_bc28,
            kode_barang,
            seri_barang,
            nama_barang,
            negara_asal,
            satuan,
            jumlah,
            mata_uang,
            nilai,
            penerima,
            gudang,
            subkontrak
        ");
        
        $builder->where('tanggal_dokumen >=', $startDate);
        $builder->where('tanggal_dokumen <=', $endDate);
        
        return $builder->get()->getResultArray();
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
            digunakan,
            disubkontrakkan,
            penerima_subkontrak
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
            dari_produksi,
            dari_subkontrak,
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
            satuan,
            jumlah,
            mata_uang,
            nilai_barang
        ");
        
        $builder->where('tanggal_peb >=', $startDate);
        $builder->where('tanggal_peb <=', $endDate);
        
        return $builder->get()->getResultArray();
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
        
        $builder->where('tanggal >=', $startDate);
        $builder->where('tanggal <=', $endDate);
        
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