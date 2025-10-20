<?php
namespace App\Controllers;

use App\Controllers\BaseController;

class DocReport extends BaseController
{
    protected $models = [];

    public function __construct()
    {
        $this->models = [
            'mutasi_bahan_baku' => new \App\Models\LaporanMutasiBahanBakuModel(),
            'mutasi_hasil_produksi' => new \App\Models\LaporanMutasiHasilProduksiModel(),
            'pemakaian_bahan_baku' => new \App\Models\LaporanPemakaianBahanBakuModel(),
            'pemasukan_bahan_baku' => new \App\Models\LaporanPemasukanBahanBakuModel(),
            'pengeluaran_hasil_produksi' => new \App\Models\LaporanPengeluaranHasilProduksiModel(),
            'waste_scrap' => new \App\Models\LaporanWasteScrapModel()
        ];
    }

    // ==================== HALAMAN UTAMA LAPORAN ====================
    public function index()
    {
        $data = [
            'title' => 'Sistem Laporan',
            'breadcrumb' => ['Laporan'],
            'laporan_list' => $this->getLaporanList()
        ];
        return view('laporan/index', $data);
    }

    // ==================== LAPORAN MUTASI BAHAN BAKU ====================
    public function mutasiBahanBaku()
    {
        $data = [
            'title' => 'Laporan Mutasi Bahan Baku',
            'breadcrumb' => ['Laporan', 'Mutasi Bahan Baku'],
            'gudang_list' => $this->getGudangList()
        ];
        return view('laporan/mutasi_bahan_baku', $data);
    }

    public function getMutasiBahanBaku()
    {
        $startDate = $this->request->getGet('start_date');
        $endDate = $this->request->getGet('end_date');
        $gudang = $this->request->getGet('gudang');
        $kodeBarang = $this->request->getGet('kode_barang');

        $builder = $this->models['mutasi_bahan_baku'];
        
        // Filter data
        if ($startDate && $endDate) {
            $builder->where('periode >=', $startDate)
                   ->where('periode <=', $endDate);
        }

        if ($gudang) {
            $builder->where('gudang', $gudang);
        }

        if ($kodeBarang) {
            $builder->where('kode_barang', $kodeBarang);
        }

        $data = $builder->orderBy('periode', 'DESC')
                       ->orderBy('kode_barang', 'ASC')
                       ->findAll();

        return $this->response->setJSON([
            'success' => true,
            'data' => $data,
            'total' => count($data)
        ]);
    }

    // ==================== LAPORAN MUTASI HASIL PRODUKSI ====================
    public function mutasiHasilProduksi()
    {
        $data = [
            'title' => 'Laporan Mutasi Hasil Produksi',
            'breadcrumb' => ['Laporan', 'Mutasi Hasil Produksi'],
            'gudang_list' => $this->getGudangList()
        ];
        return view('laporan/mutasi_hasil_produksi', $data);
    }

    public function getMutasiHasilProduksi()
    {
        $startDate = $this->request->getGet('start_date');
        $endDate = $this->request->getGet('end_date');
        $gudang = $this->request->getGet('gudang');
        $kodeBarang = $this->request->getGet('kode_barang');

        $builder = $this->models['mutasi_hasil_produksi'];
        
        if ($startDate && $endDate) {
            $builder->where('periode >=', $startDate)
                   ->where('periode <=', $endDate);
        }

        if ($gudang) {
            $builder->where('gudang', $gudang);
        }

        if ($kodeBarang) {
            $builder->where('kode_barang', $kodeBarang);
        }

        $data = $builder->orderBy('periode', 'DESC')
                       ->orderBy('kode_barang', 'ASC')
                       ->findAll();

        return $this->response->setJSON([
            'success' => true,
            'data' => $data,
            'total' => count($data)
        ]);
    }

    // ==================== LAPORAN PEMAKAIAN BAHAN BAKU ====================
    public function pemakaianBahanBaku()
    {
        $data = [
            'title' => 'Laporan Pemakaian Bahan Baku',
            'breadcrumb' => ['Laporan', 'Pemakaian Bahan Baku']
        ];
        return view('laporan/pemakaian_bahan_baku', $data);
    }

    public function getPemakaianBahanBaku()
    {
        $startDate = $this->request->getGet('start_date');
        $endDate = $this->request->getGet('end_date');
        $kodeBarang = $this->request->getGet('kode_barang');
        $digunakan = $this->request->getGet('digunakan');

        $builder = $this->models['pemakaian_bahan_baku'];
        
        if ($startDate && $endDate) {
            $builder->where('tanggal >=', $startDate)
                   ->where('tanggal <=', $endDate);
        }

        if ($kodeBarang) {
            $builder->where('kode_barang', $kodeBarang);
        }

        if ($digunakan) {
            $builder->like('digunakan', $digunakan);
        }

        $data = $builder->orderBy('tanggal', 'DESC')
                       ->orderBy('no_bukti_pengeluaran', 'ASC')
                       ->findAll();

        return $this->response->setJSON([
            'success' => true,
            'data' => $data,
            'total' => count($data)
        ]);
    }

    public function getSummaryPemakaianBahanBaku()
    {
        $startDate = $this->request->getGet('start_date');
        $endDate = $this->request->getGet('end_date');

        $builder = $this->models['pemakaian_bahan_baku'];
        
        if ($startDate && $endDate) {
            $builder->where('tanggal >=', $startDate)
                   ->where('tanggal <=', $endDate);
        }

        $data = $builder->select('kode_barang, nama_barang, satuan, SUM(jumlah) as total_pemakaian')
                       ->groupBy('kode_barang, nama_barang, satuan')
                       ->orderBy('total_pemakaian', 'DESC')
                       ->findAll();

        return $this->response->setJSON([
            'success' => true,
            'data' => $data
        ]);
    }

    // ==================== LAPORAN PEMASUKAN BAHAN BAKU ====================
    public function pemasukanBahanBaku()
    {
        $data = [
            'title' => 'Laporan Pemasukan Bahan Baku',
            'breadcrumb' => ['Laporan', 'Pemasukan Bahan Baku'],
            'jenis_dokumen_list' => $this->getJenisDokumenList()
        ];
        return view('laporan/pemasukan_bahan_baku', $data);
    }

    public function getPemasukanBahanBaku()
    {
        $startDate = $this->request->getGet('start_date');
        $endDate = $this->request->getGet('end_date');
        $jenisDokumen = $this->request->getGet('jenis_dokumen');
        $kodeBarang = $this->request->getGet('kode_barang');
        $gudang = $this->request->getGet('gudang');

        $builder = $this->models['pemasukan_bahan_baku'];
        
        if ($startDate && $endDate) {
            $builder->where('tgl_rekam >=', $startDate)
                   ->where('tgl_rekam <=', $endDate);
        }

        if ($jenisDokumen) {
            $builder->where('jenis_dokumen', $jenisDokumen);
        }

        if ($kodeBarang) {
            $builder->where('kode_bb', $kodeBarang);
        }

        if ($gudang) {
            $builder->where('gudang', $gudang);
        }

        $data = $builder->orderBy('tgl_rekam', 'DESC')
                       ->orderBy('no', 'ASC')
                       ->findAll();

        return $this->response->setJSON([
            'success' => true,
            'data' => $data,
            'total' => count($data)
        ]);
    }

    public function getSummaryImport()
    {
        $startDate = $this->request->getGet('start_date');
        $endDate = $this->request->getGet('end_date');

        $builder = $this->models['pemasukan_bahan_baku'];
        
        if ($startDate && $endDate) {
            $builder->where('tgl_rekam >=', $startDate)
                   ->where('tgl_rekam <=', $endDate);
        }

        $data = $builder->select('negara_asal_bb, COUNT(*) as total_transaksi, SUM(jumlah) as total_quantity, SUM(nilai_barang) as total_nilai')
                       ->groupBy('negara_asal_bb')
                       ->orderBy('total_nilai', 'DESC')
                       ->findAll();

        return $this->response->setJSON([
            'success' => true,
            'data' => $data
        ]);
    }

    // ==================== LAPORAN PENGELUARAN HASIL PRODUKSI ====================
    public function pengeluaranHasilProduksi()
    {
        $data = [
            'title' => 'Laporan Pengeluaran Hasil Produksi',
            'breadcrumb' => ['Laporan', 'Pengeluaran Hasil Produksi'],
            'negara_list' => $this->getNegaraList()
        ];
        return view('laporan/pengeluaran_hasil_produksi', $data);
    }

    public function getPengeluaranHasilProduksi()
    {
        $startDate = $this->request->getGet('start_date');
        $endDate = $this->request->getGet('end_date');
        $negaraTujuan = $this->request->getGet('negara_tujuan');
        $kodeBarang = $this->request->getGet('kode_barang');
        $pembeli = $this->request->getGet('pembeli');

        $builder = $this->models['pengeluaran_hasil_produksi'];
        
        if ($startDate && $endDate) {
            $builder->where('tanggal_peb >=', $startDate)
                   ->where('tanggal_peb <=', $endDate);
        }

        if ($negaraTujuan) {
            $builder->where('negara_tujuan', $negaraTujuan);
        }

        if ($kodeBarang) {
            $builder->where('kode_barang', $kodeBarang);
        }

        if ($pembeli) {
            $builder->like('pembeli_penerima', $pembeli);
        }

        $data = $builder->orderBy('tanggal_peb', 'DESC')
                       ->orderBy('no_peb', 'ASC')
                       ->findAll();

        return $this->response->setJSON([
            'success' => true,
            'data' => $data,
            'total' => count($data)
        ]);
    }

    public function getSummaryExport()
    {
        $startDate = $this->request->getGet('start_date');
        $endDate = $this->request->getGet('end_date');

        $builder = $this->models['pengeluaran_hasil_produksi'];
        
        if ($startDate && $endDate) {
            $builder->where('tanggal_peb >=', $startDate)
                   ->where('tanggal_peb <=', $endDate);
        }

        $data = $builder->select('negara_tujuan, COUNT(*) as total_peb, SUM(jumlah) as total_quantity, SUM(nilai_barang) as total_nilai')
                       ->groupBy('negara_tujuan')
                       ->orderBy('total_nilai', 'DESC')
                       ->findAll();

        return $this->response->setJSON([
            'success' => true,
            'data' => $data
        ]);
    }

    // ==================== LAPORAN WASTE & SCRAP ====================
    public function wasteScrap()
    {
        $data = [
            'title' => 'Laporan Waste & Scrap',
            'breadcrumb' => ['Laporan', 'Waste & Scrap']
        ];
        return view('laporan/waste_scrap', $data);
    }

    public function getWasteScrap()
    {
        $startDate = $this->request->getGet('start_date');
        $endDate = $this->request->getGet('end_date');
        $kodeBarang = $this->request->getGet('kode_barang');

        $builder = $this->models['waste_scrap'];
        
        if ($startDate && $endDate) {
            $builder->where('tanggal >=', $startDate)
                   ->where('tanggal <=', $endDate);
        }

        if ($kodeBarang) {
            $builder->where('kode_barang', $kodeBarang);
        }

        $data = $builder->orderBy('tanggal', 'DESC')
                       ->orderBy('kode_barang', 'ASC')
                       ->findAll();

        return $this->response->setJSON([
            'success' => true,
            'data' => $data,
            'total' => count($data)
        ]);
    }

    public function getSummaryWaste()
    {
        $startDate = $this->request->getGet('start_date');
        $endDate = $this->request->getGet('end_date');

        $builder = $this->models['waste_scrap'];
        
        if ($startDate && $endDate) {
            $builder->where('tanggal >=', $startDate)
                   ->where('tanggal <=', $endDate);
        }

        $data = $builder->select('kode_barang, nama_barang, satuan, SUM(jumlah) as total_waste, SUM(nilai) as total_nilai')
                       ->groupBy('kode_barang, nama_barang, satuan')
                       ->orderBy('total_waste', 'DESC')
                       ->findAll();

        return $this->response->setJSON([
            'success' => true,
            'data' => $data
        ]);
    }

    // ==================== FUNGSI EKSPOR DATA ====================
    public function exportExcel($jenisLaporan)
    {
        $startDate = $this->request->getGet('start_date');
        $endDate = $this->request->getGet('end_date');
        $additionalParams = $this->request->getGet();

        $data = $this->getDataForExport($jenisLaporan, $startDate, $endDate, $additionalParams);

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set judul berdasarkan jenis laporan
        $judulLaporan = $this->getJudulLaporan($jenisLaporan);
        $sheet->setCellValue('A1', $judulLaporan);
        $sheet->setCellValue('A2', 'Periode: ' . ($startDate ?? 'Semua') . ' - ' . ($endDate ?? 'Semua'));

        // Set header kolom berdasarkan jenis laporan
        $this->setExcelHeaders($sheet, $jenisLaporan);

        // Isi data
        $this->fillExcelData($sheet, $data, $jenisLaporan);

        // Auto size columns
        $this->autoSizeColumns($sheet, $jenisLaporan);

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $filename = 'laporan_' . $jenisLaporan . '_' . date('Ymd_His') . '.xlsx';
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        
        $writer->save('php://output');
        exit;
    }

    public function printPdf($jenisLaporan)
    {
        $startDate = $this->request->getGet('start_date');
        $endDate = $this->request->getGet('end_date');
        $additionalParams = $this->request->getGet();

        $data = $this->getDataForExport($jenisLaporan, $startDate, $endDate, $additionalParams);

        $viewData = [
            'data' => $data,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'judulLaporan' => $this->getJudulLaporan($jenisLaporan),
            'additionalParams' => $additionalParams
        ];

        $dompdf = new \Dompdf\Dompdf();
        $html = view('laporan/pdf/' . $jenisLaporan, $viewData);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();
        $dompdf->stream("laporan_{$jenisLaporan}.pdf", array("Attachment" => false));
    }

    // ==================== FUNGSI HELPER ====================
    private function getLaporanList()
    {
        return [
            [
                'nama' => 'Mutasi Bahan Baku',
                'slug' => 'mutasi-bahan-baku',
                'icon' => 'fas fa-exchange-alt',
                'deskripsi' => 'Laporan pergerakan stok bahan baku'
            ],
            [
                'nama' => 'Mutasi Hasil Produksi',
                'slug' => 'mutasi-hasil-produksi',
                'icon' => 'fas fa-boxes',
                'deskripsi' => 'Laporan pergerakan stok hasil produksi'
            ],
            [
                'nama' => 'Pemakaian Bahan Baku',
                'slug' => 'pemakaian-bahan-baku',
                'icon' => 'fas fa-tools',
                'deskripsi' => 'Laporan penggunaan bahan baku untuk produksi'
            ],
            [
                'nama' => 'Pemasukan Bahan Baku',
                'slug' => 'pemasukan-bahan-baku',
                'icon' => 'fas fa-arrow-down',
                'deskripsi' => 'Laporan penerimaan bahan baku dari supplier'
            ],
            [
                'nama' => 'Pengeluaran Hasil Produksi',
                'slug' => 'pengeluaran-hasil-produksi',
                'icon' => 'fas fa-arrow-up',
                'deskripsi' => 'Laporan pengiriman hasil produksi ke customer'
            ],
            [
                'nama' => 'Waste & Scrap',
                'slug' => 'waste-scrap',
                'icon' => 'fas fa-trash',
                'deskripsi' => 'Laporan bahan sisa dan scrap produksi'
            ]
        ];
    }

    private function getGudangList()
    {
        // Ambil dari tabel warehouses atau locations
        $warehouseModel = new \App\Models\WarehouseModel();
        return $warehouseModel->findAll();
    }

    private function getJenisDokumenList()
    {
        return ['BC 2.0', 'BC 2.4', 'BC 2.5', 'BC 2.8'];
    }

    private function getNegaraList()
    {
        // Ambil dari tabel country_data
        $countryModel = new \App\Models\CountryModel();
        return $countryModel->findAll();
    }

    private function getDataForExport($jenisLaporan, $startDate, $endDate, $params)
    {
        $modelKey = str_replace('-', '_', $jenisLaporan);
        $builder = $this->models[$modelKey];

        // Terapkan filter berdasarkan parameter
        if ($startDate && $endDate) {
            $dateField = $this->getDateField($jenisLaporan);
            $builder->where($dateField . ' >=', $startDate)
                   ->where($dateField . ' <=', $endDate);
        }

        // Terapkan filter tambahan
        foreach ($params as $key => $value) {
            if ($value && !in_array($key, ['start_date', 'end_date'])) {
                $builder->where($key, $value);
            }
        }

        return $builder->findAll();
    }

    private function getDateField($jenisLaporan)
    {
        $dateFields = [
            'mutasi-bahan-baku' => 'periode',
            'mutasi-hasil-produksi' => 'periode',
            'pemakaian-bahan-baku' => 'tanggal',
            'pemasukan-bahan-baku' => 'tgl_rekam',
            'pengeluaran-hasil-produksi' => 'tanggal_peb',
            'waste-scrap' => 'tanggal'
        ];

        return $dateFields[$jenisLaporan] ?? 'created_at';
    }

    private function getJudulLaporan($jenisLaporan)
    {
        $judul = [
            'mutasi-bahan-baku' => 'LAPORAN MUTASI BAHAN BAKU',
            'mutasi-hasil-produksi' => 'LAPORAN MUTASI HASIL PRODUKSI',
            'pemakaian-bahan-baku' => 'LAPORAN PEMAKAIAN BAHAN BAKU',
            'pemasukan-bahan-baku' => 'LAPORAN PEMASUKAN BAHAN BAKU',
            'pengeluaran-hasil-produksi' => 'LAPORAN PENGELUARAN HASIL PRODUKSI',
            'waste-scrap' => 'LAPORAN WASTE & SCRAP'
        ];

        return $judul[$jenisLaporan] ?? 'LAPORAN';
    }

    private function setExcelHeaders($sheet, $jenisLaporan)
    {
        $headers = $this->getExcelHeaders($jenisLaporan);
        $row = 4;
        $col = 'A';

        foreach ($headers as $header) {
            $sheet->setCellValue($col . $row, $header);
            $col++;
        }
    }

    private function getExcelHeaders($jenisLaporan)
    {
        $headers = [
            'mutasi-bahan-baku' => ['NO', 'KODE BARANG', 'NAMA BARANG', 'SATUAN', 'SALDO AWAL', 'PEMASUKAN', 'PENGELUARAN', 'SALDO AKHIR', 'GUDANG', 'PERIODE'],
            'mutasi-hasil-produksi' => ['NO', 'KODE BARANG', 'NAMA BARANG', 'SATUAN', 'SALDO AWAL', 'PEMASUKAN', 'PENGELUARAN', 'SALDO AKHIR', 'GUDANG', 'PERIODE'],
            'pemakaian-bahan-baku' => ['NO', 'NO BUKTI', 'TANGGAL', 'KODE BARANG', 'NAMA BARANG', 'SATUAN', 'JUMLAH', 'DIGUNAKAN UNTUK', 'SUBKONTRAK'],
            'pemasukan-bahan-baku' => ['NO', 'TGL REKAM', 'JENIS DOKUMEN', 'NOMOR PABEAN', 'KODE HS', 'KODE BB', 'NAMA BARANG', 'SATUAN', 'JUMLAH', 'NILAI BARANG', 'GUDANG', 'NEGARA ASAL'],
            'pengeluaran-hasil-produksi' => ['NO', 'NO PEB', 'TANGGAL PEB', 'PEMBELI', 'NEGARA TUJUAN', 'KODE BARANG', 'NAMA BARANG', 'SATUAN', 'JUMLAH', 'NILAI BARANG'],
            'waste-scrap' => ['NO', 'NO BC24', 'TANGGAL', 'KODE BARANG', 'NAMA BARANG', 'SATUAN', 'JUMLAH', 'NILAI']
        ];

        return $headers[$jenisLaporan] ?? ['NO', 'DATA'];
    }

    private function fillExcelData($sheet, $data, $jenisLaporan)
    {
        $row = 5;
        $no = 1;

        foreach ($data as $item) {
            $col = 'A';
            $sheet->setCellValue($col++ . $row, $no++);

            // Isi data berdasarkan jenis laporan
            $this->fillRowData($sheet, $col, $row, $item, $jenisLaporan);
            $row++;
        }
    }

    private function fillRowData($sheet, $col, $row, $item, $jenisLaporan)
    {
        switch ($jenisLaporan) {
            case 'mutasi-bahan-baku':
                $sheet->setCellValue($col++ . $row, $item['kode_barang']);
                $sheet->setCellValue($col++ . $row, $item['nama_barang']);
                $sheet->setCellValue($col++ . $row, $item['satuan']);
                $sheet->setCellValue($col++ . $row, $item['saldo_awal']);
                $sheet->setCellValue($col++ . $row, $item['pemasukan']);
                $sheet->setCellValue($col++ . $row, $item['pengeluaran']);
                $sheet->setCellValue($col++ . $row, $item['saldo_akhir']);
                $sheet->setCellValue($col++ . $row, $item['gudang']);
                $sheet->setCellValue($col++ . $row, $item['periode']);
                break;

            case 'pemakaian-bahan-baku':
                $sheet->setCellValue($col++ . $row, $item['no_bukti_pengeluaran']);
                $sheet->setCellValue($col++ . $row, $item['tanggal']);
                $sheet->setCellValue($col++ . $row, $item['kode_barang']);
                $sheet->setCellValue($col++ . $row, $item['nama_barang']);
                $sheet->setCellValue($col++ . $row, $item['satuan']);
                $sheet->setCellValue($col++ . $row, $item['jumlah']);
                $sheet->setCellValue($col++ . $row, $item['digunakan']);
                $sheet->setCellValue($col++ . $row, $item['disubkontrakkan']);
                break;

            // Tambahkan case untuk laporan lainnya...
            
            default:
                // Default fill for unknown report types
                foreach ($item as $value) {
                    $sheet->setCellValue($col++ . $row, $value);
                }
                break;
        }
    }

    private function autoSizeColumns($sheet, $jenisLaporan)
    {
        $columnCount = count($this->getExcelHeaders($jenisLaporan));
        for ($i = 'A'; $i < chr(ord('A') + $columnCount); $i++) {
            $sheet->getColumnDimension($i)->setAutoSize(true);
        }
    }
}