<?php namespace App\Controllers;

use App\Models\BcExportModel;
use App\Models\BcExportEntitasModel;
use App\Models\BcExportDokumenModel;
use App\Models\BcExportBarangModel;
use App\Models\BcExportPengangkutModel;
use App\Models\BcExportKontainerModel;
use App\Models\BcExportKemasanModel;
use App\Models\BcExportTarifModel;
use App\Models\BcExportPungutanModel;
use PhpOffice\PhpSpreadsheet\IOFactory;

class BcExport extends BaseController
{
    protected $bcExportModel;
    protected $bcExportEntitasModel;
    protected $bcExportDokumenModel;
    protected $bcExportBarangModel;
    protected $bcExportPengangkutModel;
    protected $bcExportKontainerModel;
    protected $bcExportKemasanModel;
    protected $bcExportTarifModel;
    protected $bcExportPungutanModel;

    public function __construct()
    {
        $this->bcExportModel = new BcExportModel();
        $this->bcExportEntitasModel = new BcExportEntitasModel();
        $this->bcExportDokumenModel = new BcExportDokumenModel();
        $this->bcExportBarangModel = new BcExportBarangModel();
        $this->bcExportPengangkutModel = new BcExportPengangkutModel();
        $this->bcExportKontainerModel = new BcExportKontainerModel();
        $this->bcExportKemasanModel = new BcExportKemasanModel();
        $this->bcExportTarifModel = new BcExportTarifModel();
        $this->bcExportPungutanModel = new BcExportPungutanModel();
        helper('form');
    }

    public function index()
    {
        $data = [
            'title' => 'BC Export Data',
            'bc_data' => $this->bcExportModel->orderBy('created_at', 'DESC')->findAll()
        ];
        $dataview['content'] = view('admin/content/bc_export/index', $data);
        return view('admin/index', $dataview);
    }

    public function detail($id)
    {
        $header = $this->bcExportModel->find($id);
        
        if (!$header) {
            return redirect()->to('/bc-export')->with('error', 'Data tidak ditemukan');
        }

        $nomorAju = $header['nomor_aju'];

        $data = [
            'title' => 'Detail BC Export - ' . $header['nomor_aju'],
            'header' => $header,
            'entitas' => $this->bcExportEntitasModel->getByNomorAju($nomorAju) ?? [],
            'dokumen' => $this->bcExportDokumenModel->getByNomorAju($nomorAju) ?? [],
            'barang' => $this->bcExportBarangModel->getByNomorAju($nomorAju) ?? [],
            'pengangkut' => $this->bcExportPengangkutModel->getByNomorAju($nomorAju) ?? [],
            'kontainer' => $this->bcExportKontainerModel->getByNomorAju($nomorAju) ?? [],
            'kemasan' => $this->bcExportKemasanModel->getByNomorAju($nomorAju) ?? [],
            'tarif' => $this->bcExportTarifModel->getByNomorAju($nomorAju) ?? [],
            'pungutan' => $this->bcExportPungutanModel->getByNomorAju($nomorAju) ?? []
        ];

        // Debug: Cek tipe data
        // var_dump(gettype($data['pengangkut'])); die();

        $dataview['content'] = view('admin/content/bc_export/detail', $data);
        return view('admin/index', $dataview);
    }

    public function importForm()
    {
        $data = [
            'title' => 'Import BC Export'
        ];
        $dataview['content'] = view('admin/content/bc_export/import_form', $data);
        return view('admin/index', $dataview);
    }

    public function processImport()
    {
        $validation = $this->validate([
            'excel_file' => [
                'rules' => 'uploaded[excel_file]|ext_in[excel_file,xlsx,xls]',
                'errors' => [
                    'uploaded' => 'Silakan pilih file untuk diupload',
                    'ext_in' => 'Hanya file Excel yang diperbolehkan'
                ]
            ]
        ]);

        if (!$validation) {
            return redirect()->to('/bc-export/import')->withInput()->with('errors', $this->validator->getErrors());
        }

        $file = $this->request->getFile('excel_file');

        try {
            // Get database connection
            $db = \Config\Database::connect();
            $db->transStart();
            
            $spreadsheet = IOFactory::load($file->getTempName());
            
            // Process semua sheet
            $this->processHeaderSheet($spreadsheet->getSheetByName('HEADER'));
            $this->processEntitasSheet($spreadsheet->getSheetByName('ENTITAS'));
            $this->processDokumenSheet($spreadsheet->getSheetByName('DOKUMEN'));
            $this->processBarangSheet($spreadsheet->getSheetByName('BARANG'));
            $this->processPengangkutSheet($spreadsheet->getSheetByName('PENGANGKUT'));
            $this->processKontainerSheet($spreadsheet->getSheetByName('KONTAINER'));
            $this->processKemasanSheet($spreadsheet->getSheetByName('KEMASAN'));
            $this->processTarifSheet($spreadsheet->getSheetByName('BARANGTARIF'));
            $this->processPungutanSheet($spreadsheet->getSheetByName('PUNGUTAN'));
            
            $db->transComplete();
            
            if ($db->transStatus() === false) {
                throw new \RuntimeException('Database transaction failed');
            }

            return redirect()->to('/bc-export')->with('success', 'Data BC Export berhasil diimport');
        } catch (\Exception $e) {
            return redirect()->to('/bc-export/import')->with('error', 'Error importing data: ' . $e->getMessage());
        }
    }

    protected function processHeaderSheet($sheet)
    {
        if (!$sheet) return;

        $nomorAju = $sheet->getCell('A2')->getValue();
        
        // Cek apakah data sudah ada
        $existing = $this->bcExportModel->where('nomor_aju', $nomorAju)->first();
        if ($existing) {
            throw new \Exception("Data dengan nomor AJU {$nomorAju} sudah ada");
        }

        $data = [
            'nomor_aju' => $nomorAju,
            'kode_dokumen' => $sheet->getCell('B2')->getValue(),
            'kode_kantor' => $sheet->getCell('C2')->getValue(),
            'kode_jenis_ekspor' => $sheet->getCell('I2')->getValue(),
            'kode_jenis_tpb' => $sheet->getCell('J2')->getValue(),
            'kode_jenis_prosedur' => $sheet->getCell('L2')->getValue(),
            'kode_pelabuhan_muat' => $sheet->getCell('AO2')->getValue(),
            'kode_pelabuhan_tujuan' => $sheet->getCell('AN2')->getValue(),
            'nomor_bc11' => $sheet->getCell('AJ2')->getValue(),
            'tanggal_bc11' => $this->convertExcelDate($sheet->getCell('AK2')->getValue()),
            'tanggal_daftar' => $this->convertExcelDate($sheet->getCell('CQ2')->getValue()),
            'tanggal_ekspor' => $this->convertExcelDate($sheet->getCell('AV2')->getValue()),
            'nilai_barang' => $sheet->getCell('BL2')->getValue() ?: 0,
            'nilai_incoterm' => $sheet->getCell('BM2')->getValue() ?: 0,
            'nomor_daftar' => $sheet->getCell('CP2')->getValue(),
            'fob' => $sheet->getCell('BQ2')->getValue() ?: 0,
            'kode_valuta' => $sheet->getCell('CI2')->getValue(),
            'kode_incoterm' => $sheet->getCell('CJ2')->getValue(),
            'bruto' => $sheet->getCell('CB2')->getValue() ?: 0,
            'netto' => $sheet->getCell('CC2')->getValue() ?: 0,
            'kota_pernyataan' => $sheet->getCell('CM2')->getValue(),
            'tanggal_pernyataan' => $this->convertExcelDate($sheet->getCell('CN2')->getValue()),
            'nama_pernyataan' => $sheet->getCell('CO2')->getValue()
        ];

        $this->bcExportModel->insert($data);
    }

    protected function processEntitasSheet($sheet)
    {
        if (!$sheet) return;

        $highestRow = $sheet->getHighestRow();

        for ($row = 2; $row <= $highestRow; $row++) {
            $nomorAju = $sheet->getCell('A' . $row)->getValue();
            if (empty($nomorAju)) continue;

            $data = [
                'nomor_aju' => $nomorAju,
                'seri' => $sheet->getCell('B' . $row)->getValue(),
                'kode_entitas' => $sheet->getCell('C' . $row)->getValue(),
                'kode_jenis_identitas' => $sheet->getCell('D' . $row)->getValue(),
                'nomor_identitas' => $sheet->getCell('E' . $row)->getValue(),
                'nama_entitas' => $sheet->getCell('F' . $row)->getValue(),
                'alamat_entitas' => $sheet->getCell('G' . $row)->getValue(),
                'kode_negara' => $sheet->getCell('M' . $row)->getValue()
            ];

            $this->bcExportEntitasModel->insert($data);
        }
    }

    protected function processDokumenSheet($sheet)
    {
        if (!$sheet) return;

        $highestRow = $sheet->getHighestRow();

        for ($row = 2; $row <= $highestRow; $row++) {
            $nomorAju = $sheet->getCell('A' . $row)->getValue();
            if (empty($nomorAju)) continue;

            $data = [
                'nomor_aju' => $nomorAju,
                'seri' => $sheet->getCell('B' . $row)->getValue(),
                'kode_dokumen' => $sheet->getCell('C' . $row)->getValue(),
                'nomor_dokumen' => $sheet->getCell('D' . $row)->getValue(),
                'tanggal_dokumen' => $this->convertExcelDate($sheet->getCell('E' . $row)->getValue())
            ];

            $this->bcExportDokumenModel->insert($data);
        }
    }

protected function processBarangSheet($sheet)
{
    if (!$sheet) return;

    $highestRow = $sheet->getHighestRow();

    for ($row = 2; $row <= $highestRow; $row++) {
        $nomorAju = $sheet->getCell('A' . $row)->getValue();
        if (empty($nomorAju)) continue;

        // Debug: Tampilkan data untuk row tertentu
        if ($row <= 5) { // Hanya debug untuk 5 baris pertama
            $debugData = [
                'row' => $row,
                'nomor_aju' => $nomorAju,
                'seri_barang' => $sheet->getCell('B' . $row)->getValue(),
                'hs' => $sheet->getCell('C' . $row)->getValue(),
                'uraian' => $sheet->getCell('E' . $row)->getValue(),
                'netto' => $sheet->getCell('R' . $row)->getValue(),
                'fob' => $sheet->getCell('Z' . $row)->getValue(),
                'negara_tujuan' => $sheet->getCell('AY' . $row)->getValue()
            ];
            // log_message('debug', 'Barang Data: ' . print_r($debugData, true));
        }

        $data = [
            'nomor_aju' => $nomorAju,
            'seri_barang' => $sheet->getCell('B' . $row)->getValue(),
            'hs' => $sheet->getCell('C' . $row)->getValue(),
            'uraian' => $sheet->getCell('E' . $row)->getValue(),
            'kode_satuan' => $sheet->getCell('J' . $row)->getValue(),
            'jumlah_satuan' => $this->cleanNumber($sheet->getCell('K' . $row)->getValue()),
            'netto' => $this->cleanNumber($sheet->getCell('T' . $row)->getValue()),
            'fob' => $this->cleanNumber($sheet->getCell('AC' . $row)->getValue()),
            'kode_negara_asal' => $sheet->getCell('AY' . $row)->getValue()
        ];

        // Validasi data sebelum insert
        if (!empty($data['seri_barang']) && !empty($data['uraian'])) {
            $this->bcExportBarangModel->insert($data);
        }
    }
}

// Tambahkan method helper untuk membersihkan angka
protected function cleanNumber($value)
{
    if (empty($value) || $value === '-') {
        return 0;
    }
    
    // Jika sudah numeric, langsung return
    if (is_numeric($value)) {
        return (float) $value;
    }
    
    // Hapus karakter non-numeric kecuali titik dan koma
    $cleaned = preg_replace('/[^\d,.-]/', '', $value);
    
    // Ganti koma dengan titik untuk format decimal
    $cleaned = str_replace(',', '.', $cleaned);
    
    // Hapus multiple dots
    if (substr_count($cleaned, '.') > 1) {
        $cleaned = str_replace('.', '', $cleaned);
    }
    
    return (float) $cleaned;
}

    protected function processPengangkutSheet($sheet)
    {
        if (!$sheet) return;

        $highestRow = $sheet->getHighestRow();

        for ($row = 2; $row <= $highestRow; $row++) {
            $nomorAju = $sheet->getCell('A' . $row)->getValue();
            if (empty($nomorAju)) continue;

            $data = [
                'nomor_aju' => $nomorAju,
                'seri' => $sheet->getCell('B' . $row)->getValue(),
                'kode_cara_angkut' => $sheet->getCell('C' . $row)->getValue(),
                'nama_pengangkut' => $sheet->getCell('D' . $row)->getValue(),
                'nomor_pengangkut' => $sheet->getCell('E' . $row)->getValue(),
                'kode_bendera' => $sheet->getCell('F' . $row)->getValue()
            ];

            $this->bcExportPengangkutModel->insert($data);
        }
    }

    protected function processKontainerSheet($sheet)
    {
        if (!$sheet) return;

        $highestRow = $sheet->getHighestRow();

        for ($row = 2; $row <= $highestRow; $row++) {
            $nomorAju = $sheet->getCell('A' . $row)->getValue();
            if (empty($nomorAju)) continue;

            $data = [
                'nomor_aju' => $nomorAju,
                'seri' => $sheet->getCell('B' . $row)->getValue(),
                'nomor_kontainer' => $sheet->getCell('C' . $row)->getValue(),
                'kode_ukuran_kontainer' => $sheet->getCell('D' . $row)->getValue(),
                'kode_jenis_kontainer' => $sheet->getCell('E' . $row)->getValue(),
                'kode_tipe_kontainer' => $sheet->getCell('F' . $row)->getValue()
            ];

            $this->bcExportKontainerModel->insert($data);
        }
    }

    protected function processKemasanSheet($sheet)
    {
        if (!$sheet) return;

        $highestRow = $sheet->getHighestRow();

        for ($row = 2; $row <= $highestRow; $row++) {
            $nomorAju = $sheet->getCell('A' . $row)->getValue();
            if (empty($nomorAju)) continue;

            $data = [
                'nomor_aju' => $nomorAju,
                'seri' => $sheet->getCell('B' . $row)->getValue(),
                'kode_kemasan' => $sheet->getCell('C' . $row)->getValue(),
                'jumlah_kemasan' => $sheet->getCell('D' . $row)->getValue() ?: 0,
                'merek' => $sheet->getCell('E' . $row)->getValue()
            ];

            $this->bcExportKemasanModel->insert($data);
        }
    }

    protected function processTarifSheet($sheet)
    {
        if (!$sheet) return;

        $highestRow = $sheet->getHighestRow();

        for ($row = 2; $row <= $highestRow; $row++) {
            $nomorAju = $sheet->getCell('A' . $row)->getValue();
            if (empty($nomorAju)) continue;

            $data = [
                'nomor_aju' => $nomorAju,
                'seri_barang' => $sheet->getCell('B' . $row)->getValue(),
                'kode_pungutan' => $sheet->getCell('C' . $row)->getValue(),
                'kode_tarif' => $sheet->getCell('D' . $row)->getValue(),
                'tarif' => $sheet->getCell('E' . $row)->getValue() ?: 0,
                'kode_fasilitas' => $sheet->getCell('F' . $row)->getValue(),
                'tarif_fasilitas' => $sheet->getCell('G' . $row)->getValue() ?: 0,
                'nilai_bayar' => $sheet->getCell('H' . $row)->getValue() ?: 0,
                'nilai_fasilitas' => $sheet->getCell('I' . $row)->getValue() ?: 0,
                'kode_satuan' => $sheet->getCell('J' . $row)->getValue(),
                'jumlah_satuan' => $sheet->getCell('K' . $row)->getValue() ?: 0
            ];

            $this->bcExportTarifModel->insert($data);
        }
    }

    protected function processPungutanSheet($sheet)
    {
        if (!$sheet) return;

        $highestRow = $sheet->getHighestRow();

        for ($row = 2; $row <= $highestRow; $row++) {
            $nomorAju = $sheet->getCell('A' . $row)->getValue();
            if (empty($nomorAju)) continue;

            $data = [
                'nomor_aju' => $nomorAju,
                'kode_fasilitas_tarif' => $sheet->getCell('B' . $row)->getValue(),
                'kode_jenis_pungutan' => $sheet->getCell('C' . $row)->getValue(),
                'nilai_pungutan' => $sheet->getCell('D' . $row)->getValue() ?: 0,
                'npwp_billing' => $sheet->getCell('E' . $row)->getValue()
            ];

            $this->bcExportPungutanModel->insert($data);
        }
    }

    protected function convertExcelDate($excelDate)
    {
        if (empty($excelDate)) {
            return null;
        }

        if (is_numeric($excelDate)) {
            $unixDate = ($excelDate - 25569) * 86400;
            return gmdate("Y-m-d", $unixDate);
        }
        
        // Jika sudah dalam format string, coba parse
        if (is_string($excelDate)) {
            $parsedDate = date_create_from_format('Y-m-d', $excelDate);
            if ($parsedDate !== false) {
                return $parsedDate->format('Y-m-d');
            }
        }
        
        return null;
    }
}