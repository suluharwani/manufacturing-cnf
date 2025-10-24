<?php namespace App\Controllers;

use App\Models\BcHeaderModel;
use App\Models\BcEntitasModel;
use App\Models\BcDokumenModel;
use App\Models\BcBarangModel;
use App\Models\BcPengangkutModel;
use App\Models\BcKemasanModel;
use App\Models\BcKontainerModel;
use App\Models\BcBarangDokumenModel;
use App\Models\BcBarangEntitasModel;
use App\Models\BcBarangTarifModel;
use App\Models\BcPungutanModel;
use App\Models\BcBarangVdModel;
use App\Models\BcImportModel;
use PhpOffice\PhpSpreadsheet\IOFactory;
use CodeIgniter\Database\ConnectionInterface;

class BcImport extends BaseController
{ 
    protected $db;
    protected $bcHeaderModel;
    protected $bcEntitasModel;
    protected $bcDokumenModel;
    protected $bcBarangModel;
    protected $bcPengangkutModel;
    protected $bcKemasanModel;
    protected $bcKontainerModel;
    protected $bcBarangDokumenModel;
    protected $bcBarangEntitasModel;
    protected $bcBarangTarifModel;
    protected $bcPungutanModel;
    protected $bcBarangVdModel;
    protected $bcImportModel;

    // Counter untuk tracking data yang diimport
    protected $importCounters = [
        'header' => 0,
        'entitas' => 0,
        'dokumen' => 0,
        'pengangkut' => 0,
        'kemasan' => 0,
        'kontainer' => 0,
        'barang' => 0,
        'barang_tarif' => 0,
        'barang_dokumen' => 0,
        'barang_entitas' => 0,
        'barang_vd' => 0,
        'pungutan' => 0
    ];

    public function __construct()
    {
        $this->db = \Config\Database::connect();
        helper('form');
        $this->bcHeaderModel = new BcHeaderModel();
        $this->bcEntitasModel = new BcEntitasModel();
        $this->bcDokumenModel = new BcDokumenModel();
        $this->bcBarangModel = new BcBarangModel();
        $this->bcPengangkutModel = new BcPengangkutModel();
        $this->bcKemasanModel = new BcKemasanModel();
        $this->bcKontainerModel = new BcKontainerModel();
        $this->bcBarangDokumenModel = new BcBarangDokumenModel();
        $this->bcBarangEntitasModel = new BcBarangEntitasModel();
        $this->bcBarangTarifModel = new BcBarangTarifModel();
        $this->bcPungutanModel = new BcPungutanModel();
        $this->bcBarangVdModel = new BcBarangVdModel();
        $this->bcImportModel = new BcImportModel();
    }

    public function index()
    {
        $data = [
            'title' => 'BC Data Import',
            'bc_data' => $this->bcHeaderModel->orderBy('created_at', 'DESC')->findAll(),
        ];
        $dataview['content'] = view('admin/content/bcimport', $data);
        return view('admin/index', $dataview);
    }

    public function detail($nomorAju)
    {
        try {
            // Ambil data dari semua tabel terkait
            $data = [
                'header' => $this->bcHeaderModel->where('nomor_aju', $nomorAju)->first(),
                'entitas' => $this->bcEntitasModel->where('nomor_aju', $nomorAju)->findAll(),
                'dokumen' => $this->bcDokumenModel->where('nomor_aju', $nomorAju)->findAll(),
                'barang' => $this->bcBarangModel->where('nomor_aju', $nomorAju)->findAll(),
                'pengangkut' => $this->bcPengangkutModel->where('nomor_aju', $nomorAju)->findAll(),
                'kemasan' => $this->bcKemasanModel->where('nomor_aju', $nomorAju)->findAll(),
                'kontainer' => $this->bcKontainerModel->where('nomor_aju', $nomorAju)->findAll(),
                'barang_tarif' => $this->bcBarangTarifModel->where('nomor_aju', $nomorAju)->findAll(),
                'barang_dokumen' => $this->bcBarangDokumenModel->where('nomor_aju', $nomorAju)->findAll(),
                'barang_entitas' => $this->bcBarangEntitasModel->where('nomor_aju', $nomorAju)->findAll(),
                'barang_vd' => $this->bcBarangVdModel->where('nomor_aju', $nomorAju)->findAll(),
                'pungutan' => $this->bcPungutanModel->where('nomor_aju', $nomorAju)->findAll(),
            ];

            if (!$data['header']) {
                return redirect()->to('/bc-import')->with('error', 'Data tidak ditemukan');
            }

            $dataview['content'] = view('admin/content/bcimport_detail', $data);
            return view('admin/index', $dataview);
            
        } catch (\Exception $e) {
            log_message('error', 'Detail Error: ' . $e->getMessage());
            return redirect()->to('/bc-import')->with('error', 'Error loading detail: ' . $e->getMessage());
        }
    }

    public function importForm()
    {
        try {
            return view('admin/content/bcimport');
        } catch (\CodeIgniter\Exceptions\PageNotFoundException $e) {
            log_message('error', 'View file not found: bc_import/import_form.php');
            throw new \RuntimeException('The import form could not be loaded');
        }
    }

    public function processImport()
    {
        // Set header JSON untuk AJAX response
        if ($this->request->isAJAX()) {
            header('Content-Type: application/json');
        }

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
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Validation error',
                    'errors' => $this->validator->getErrors()
                ]);
            }
            return redirect()->to('/bc-import')->withInput()->with('errors', $this->validator->getErrors());
        }

        $file = $this->request->getFile('excel_file');

        try {
            $spreadsheet = IOFactory::load($file->getTempName());
            
            // Reset counters
            $this->importCounters = array_fill_keys(array_keys($this->importCounters), 0);
            
            // Start transaction
            $this->db->transStart();
            
            // Process each sheet
            $this->processHeaderSheet($spreadsheet->getSheetByName('HEADER'));
            $this->processEntitasSheet($spreadsheet->getSheetByName('ENTITAS'));
            $this->processDokumenSheet($spreadsheet->getSheetByName('DOKUMEN'));
            $this->processPengangkutSheet($spreadsheet->getSheetByName('PENGANGKUT'));
            $this->processKemasanSheet($spreadsheet->getSheetByName('KEMASAN'));
            $this->processKontainerSheet($spreadsheet->getSheetByName('KONTAINER'));
            $this->processBarangSheet($spreadsheet->getSheetByName('BARANG'));
            $this->processBarangTarifSheet($spreadsheet->getSheetByName('BARANGTARIF'));
            $this->processBarangDokumenSheet($spreadsheet->getSheetByName('BARANGDOKUMEN'));
            $this->processBarangEntitasSheet($spreadsheet->getSheetByName('BARANGENTITAS'));
            $this->processBarangVdSheet($spreadsheet->getSheetByName('BARANGVD'));
            $this->processPungutanSheet($spreadsheet->getSheetByName('PUNGUTAN'));
            
            // Complete transaction
            $this->db->transComplete();
            
            if ($this->db->transStatus() === false) {
                throw new \RuntimeException('Database transaction failed');
            }

            // Prepare success message with import statistics
            $successMessage = 'Data BC Import berhasil diimport!<br>Statistik Import:<br>';
            foreach ($this->importCounters as $type => $count) {
                if ($count > 0) {
                    $successMessage .= "- " . ucfirst(str_replace('_', ' ', $type)) . ": {$count} records<br>";
                }
            }

            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'status' => 'success',
                    'message' => $successMessage,
                    'redirect' => base_url('/bc-import')
                ]);
            }

            return redirect()->to('/bc-import')->with('success', $successMessage);
        } catch (\Exception $e) {
            // Rollback transaction if error occurs
            $this->db->transRollback();
            log_message('error', 'Import Error: ' . $e->getMessage());
            log_message('error', 'Import Trace: ' . $e->getTraceAsString());
            
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Error importing data: ' . $e->getMessage()
                ]);
            }
            return redirect()->to('/bc-import/form')->with('error', 'Error importing data: ' . $e->getMessage());
        }
    }

    protected function processHeaderSheet($sheet)
    {
        if (!$sheet) return;

        $nomorAju = $sheet->getCell('A2')->getValue();
        if (empty($nomorAju)) return;

        $data = [
            'nomor_aju' => $nomorAju,
            'kode_dokumen' => $sheet->getCell('B2')->getValue(),
            'kode_kantor' => $sheet->getCell('C2')->getValue(),
            'kode_kantor_bongkar' => $sheet->getCell('D2')->getValue(),
            'kode_kantor_periksa' => $sheet->getCell('E2')->getValue(),
            'kode_kantor_tujuan' => $sheet->getCell('F2')->getValue(),
            'kode_jenis_impor' => $sheet->getCell('G2')->getValue(),
            'kode_jenis_tpb' => $sheet->getCell('J2')->getValue(),
            'kode_jenis_prosedur' => $sheet->getCell('L2')->getValue(),
            'kode_cara_bayar' => $sheet->getCell('Q2')->getValue(),
            'kode_pelabuhan_muat' => $sheet->getCell('AO2')->getValue(),
            'kode_pelabuhan_tujuan' => $sheet->getCell('AN2')->getValue(),
            'kode_tps' => $sheet->getCell('AT2')->getValue(),
            'nomor_bc11' => $sheet->getCell('AL2')->getValue(),
            'tanggal_bc11' => $this->convertExcelDate($sheet->getCell('AK2')->getValue()),
            'nomor_pos' => $sheet->getCell('AL2')->getValue(),
            'nomor_sub_pos' => $sheet->getCell('AM2')->getValue(),
            'tanggal_tiba' => $this->convertExcelDate($sheet->getCell('AY2')->getValue()),
            'nilai_barang' => $this->cleanNumber($sheet->getCell('BL2')->getValue()),
            'nilai_incoterm' => $this->cleanNumber($sheet->getCell('BM2')->getValue()),
            'asuransi' => $this->cleanNumber($sheet->getCell('BJ2')->getValue()),
            'freight' => $this->cleanNumber($sheet->getCell('BP2')->getValue()),
            'fob' => $this->cleanNumber($sheet->getCell('BQ2')->getValue()),
            'cif' => $this->cleanNumber($sheet->getCell('BU2')->getValue()),
            'ndpbm' => $this->cleanNumber($sheet->getCell('BW2')->getValue()),
            'bruto' => $this->cleanNumber($sheet->getCell('CB2')->getValue()),
            'netto' => $this->cleanNumber($sheet->getCell('CC2')->getValue()),
            'kode_valuta' => $sheet->getCell('CI2')->getValue(),
            'kode_incoterm' => $sheet->getCell('CJ2')->getValue(),
            'kota_pernyataan' => $sheet->getCell('CE2')->getValue(),
            'tanggal_pernyataan' => $this->convertExcelDate($sheet->getCell('CF2')->getValue()),
            'nama_pernyataan' => $sheet->getCell('CH2')->getValue(),
            'jabatan_pernyataan' => $sheet->getCell('CP2')->getValue(),
            'nomor_daftar' => $sheet->getCell('CP2')->getValue(),
            'tanggal_daftar' => $this->convertExcelDate($sheet->getCell('CQ2')->getValue()),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        // Validate required fields
        if (empty($data['nomor_aju'])) {
            throw new \RuntimeException("Nomor AJU is required");
        }

        // Cek apakah data sudah ada
        $existing = $this->bcHeaderModel->where('nomor_aju', $nomorAju)->first();
        if ($existing) {
            // Update existing data
            $result = $this->bcHeaderModel->update($existing['id'], $data);
            $action = 'updated';
        } else {
            // Insert new data
            $data['created_at'] = date('Y-m-d H:i:s');
            $result = $this->bcHeaderModel->insert($data);
            $action = 'inserted';
        }

        if (!$result) {
            throw new \RuntimeException("Failed to {$action} header data: " . implode(', ', $this->bcHeaderModel->errors()));
        }
        
        $this->importCounters['header']++;
    }

    protected function processEntitasSheet($sheet)
    {
        if (!$sheet) return;

        $highestRow = $sheet->getHighestRow();

        for ($row = 2; $row <= $highestRow; $row++) {
            $nomorAju = $sheet->getCell('A' . $row)->getValue();
            $seri = $sheet->getCell('B' . $row)->getValue();
            
            // Skip empty rows
            if (empty($nomorAju) || empty($seri)) {
                continue;
            }

            $data = [
                'nomor_aju' => $nomorAju,
                'seri' => $seri,
                'kode_entitas' => $sheet->getCell('C' . $row)->getValue(),
                'kode_jenis_identitas' => $sheet->getCell('D' . $row)->getValue(),
                'nomor_identitas' => $sheet->getCell('E' . $row)->getValue(),
                'nama_entitas' => $sheet->getCell('F' . $row)->getValue(),
                'alamat_entitas' => $sheet->getCell('G' . $row)->getValue(),
                'nib_entitas' => $sheet->getCell('H' . $row)->getValue(),
                'kode_jenis_api' => $sheet->getCell('I' . $row)->getValue(),
                'kode_status' => $sheet->getCell('J' . $row)->getValue(),
                'kode_negara' => $sheet->getCell('M' . $row)->getValue()
            ];

            // Cek apakah data sudah ada
            $existing = $this->bcEntitasModel
                ->where('nomor_aju', $nomorAju)
                ->where('seri', $seri)
                ->first();

            if ($existing) {
                $result = $this->bcEntitasModel->update($existing['id'], $data);
                $action = 'updated';
            } else {
                $result = $this->bcEntitasModel->insert($data);
                $action = 'inserted';
            }

            if (!$result) {
                throw new \RuntimeException("Failed to {$action} entitas data at row {$row}: " . implode(', ', $this->bcEntitasModel->errors()));
            }
            
            $this->importCounters['entitas']++;
        }
    }

    protected function processDokumenSheet($sheet)
    {
        if (!$sheet) return;

        $highestRow = $sheet->getHighestRow();

        for ($row = 2; $row <= $highestRow; $row++) {
            $nomorAju = $sheet->getCell('A' . $row)->getValue();
            $seri = $sheet->getCell('B' . $row)->getValue();
            
            // Skip empty rows
            if (empty($nomorAju) || empty($seri)) {
                continue;
            }

            $data = [
                'nomor_aju' => $nomorAju,
                'seri' => $seri,
                'kode_dokumen' => $sheet->getCell('C' . $row)->getValue(),
                'nomor_dokumen' => $sheet->getCell('D' . $row)->getValue(),
                'tanggal_dokumen' => $this->convertExcelDate($sheet->getCell('E' . $row)->getValue()),
                'kode_fasilitas' => $sheet->getCell('F' . $row)->getValue(),
                'kode_ijin' => $sheet->getCell('G' . $row)->getValue()
            ];

            // Cek apakah data sudah ada
            $existing = $this->bcDokumenModel
                ->where('nomor_aju', $nomorAju)
                ->where('seri', $seri)
                ->first();

            if ($existing) {
                $result = $this->bcDokumenModel->update($existing['id'], $data);
                $action = 'updated';
            } else {
                $result = $this->bcDokumenModel->insert($data);
                $action = 'inserted';
            }

            if (!$result) {
                throw new \RuntimeException("Failed to {$action} dokumen data at row {$row}: " . implode(', ', $this->bcDokumenModel->errors()));
            }
            
            $this->importCounters['dokumen']++;
        }
    }

    protected function processPengangkutSheet($sheet)
    {
        if (!$sheet) return;

        $highestRow = $sheet->getHighestRow();

        for ($row = 2; $row <= $highestRow; $row++) {
            $nomorAju = $sheet->getCell('A' . $row)->getValue();
            $seri = $sheet->getCell('B' . $row)->getValue();
            
            // Skip empty rows
            if (empty($nomorAju) || empty($seri)) {
                continue;
            }

            $data = [
                'nomor_aju' => $nomorAju,
                'seri' => $seri,
                'kode_cara_angkut' => $sheet->getCell('C' . $row)->getValue(),
                'nama_pengangkut' => $sheet->getCell('D' . $row)->getValue(),
                'nomor_pengangkut' => $sheet->getCell('E' . $row)->getValue(),
                'kode_bendera' => $sheet->getCell('F' . $row)->getValue(),
                'call_sign' => $sheet->getCell('G' . $row)->getValue()
            ];

            // Cek apakah data sudah ada
            $existing = $this->bcPengangkutModel
                ->where('nomor_aju', $nomorAju)
                ->where('seri', $seri)
                ->first();

            if ($existing) {
                $result = $this->bcPengangkutModel->update($existing['id'], $data);
                $action = 'updated';
            } else {
                $result = $this->bcPengangkutModel->insert($data);
                $action = 'inserted';
            }

            if (!$result) {
                throw new \RuntimeException("Failed to {$action} pengangkut data at row {$row}: " . implode(', ', $this->bcPengangkutModel->errors()));
            }
            
            $this->importCounters['pengangkut']++;
        }
    }

    protected function processKemasanSheet($sheet)
    {
        if (!$sheet) return;

        $highestRow = $sheet->getHighestRow();

        for ($row = 2; $row <= $highestRow; $row++) {
            $nomorAju = $sheet->getCell('A' . $row)->getValue();
            $seri = $sheet->getCell('B' . $row)->getValue();
            
            // Skip empty rows
            if (empty($nomorAju) || empty($seri)) {
                continue;
            }

            $data = [
                'nomor_aju' => $nomorAju,
                'seri' => $seri,
                'kode_kemasan' => $sheet->getCell('C' . $row)->getValue(),
                'jumlah_kemasan' => $this->cleanNumber($sheet->getCell('D' . $row)->getValue()),
                'merek' => $sheet->getCell('E' . $row)->getValue()
            ];

            // Cek apakah data sudah ada
            $existing = $this->bcKemasanModel
                ->where('nomor_aju', $nomorAju)
                ->where('seri', $seri)
                ->first();

            if ($existing) {
                $result = $this->bcKemasanModel->update($existing['id'], $data);
                $action = 'updated';
            } else {
                $result = $this->bcKemasanModel->insert($data);
                $action = 'inserted';
            }

            if (!$result) {
                throw new \RuntimeException("Failed to {$action} kemasan data at row {$row}: " . implode(', ', $this->bcKemasanModel->errors()));
            }
            
            $this->importCounters['kemasan']++;
        }
    }

    protected function processKontainerSheet($sheet)
    {
        if (!$sheet) return;

        $highestRow = $sheet->getHighestRow();

        for ($row = 2; $row <= $highestRow; $row++) {
            $nomorAju = $sheet->getCell('A' . $row)->getValue();
            $seri = $sheet->getCell('B' . $row)->getValue();
            $nomorKontainer = $sheet->getCell('C' . $row)->getValue();
            
            // Skip empty rows
            if (empty($nomorAju) || empty($seri)) {
                continue;
            }

            $data = [
                'nomor_aju' => $nomorAju,
                'seri' => $seri,
                'nomor_kontainer' => $nomorKontainer,
                'kode_ukuran_kontainer' => $sheet->getCell('D' . $row)->getValue(),
                'kode_jenis_kontainer' => $sheet->getCell('E' . $row)->getValue(),
                'kode_tipe_kontainer' => $sheet->getCell('F' . $row)->getValue()
            ];

            // Cek apakah data sudah ada
            $existing = $this->bcKontainerModel
                ->where('nomor_aju', $nomorAju)
                ->where('seri', $seri)
                ->first();

            if ($existing) {
                $result = $this->bcKontainerModel->update($existing['id'], $data);
                $action = 'updated';
            } else {
                $result = $this->bcKontainerModel->insert($data);
                $action = 'inserted';
            }

            if (!$result) {
                throw new \RuntimeException("Failed to {$action} kontainer data at row {$row}: " . implode(', ', $this->bcKontainerModel->errors()));
            }
            
            $this->importCounters['kontainer']++;
        }
    }

    protected function processBarangSheet($sheet)
    {
        if (!$sheet) return;

        $highestRow = $sheet->getHighestRow();

        for ($row = 2; $row <= $highestRow; $row++) {
            $nomorAju = $sheet->getCell('A' . $row)->getValue();
            $seriBarang = $sheet->getCell('B' . $row)->getValue();
            
            // Skip empty rows
            if (empty($nomorAju) || empty($seriBarang)) {
                continue;
            }

            $data = [
                'nomor_aju' => $nomorAju,
                'seri_barang' => $seriBarang,
                'hs' => $sheet->getCell('C' . $row)->getValue(),
                'kode_barang' => $sheet->getCell('D' . $row)->getValue(),
                'uraian' => $sheet->getCell('E' . $row)->getValue(),
                'merek' => $sheet->getCell('F' . $row)->getValue(),
                'tipe' => $sheet->getCell('G' . $row)->getValue(),
                'ukuran' => $sheet->getCell('H' . $row)->getValue(),
                'spesifikasi_lain' => $sheet->getCell('I' . $row)->getValue(),
                'kode_satuan' => $sheet->getCell('J' . $row)->getValue(),
                'jumlah_satuan' => $this->cleanNumber($sheet->getCell('K' . $row)->getValue()),
                'kode_kemasan' => $sheet->getCell('L' . $row)->getValue(),
                'jumlah_kemasan' => $this->cleanNumber($sheet->getCell('M' . $row)->getValue()),
                'netto' => $this->cleanNumber($sheet->getCell('R' . $row)->getValue()),
                'bruto' => $this->cleanNumber($sheet->getCell('S' . $row)->getValue()),
                'cif' => $this->cleanNumber($sheet->getCell('W' . $row)->getValue()),
                'cif_rupiah' => $this->cleanNumber($sheet->getCell('X' . $row)->getValue()),
                'ndpbm' => $this->cleanNumber($sheet->getCell('Y' . $row)->getValue()),
                'fob' => $this->cleanNumber($sheet->getCell('Z' . $row)->getValue()),
                'asuransi' => $this->cleanNumber($sheet->getCell('AA' . $row)->getValue()),
                'freight' => $this->cleanNumber($sheet->getCell('AB' . $row)->getValue()),
                'kode_negara_asal' => $sheet->getCell('AY' . $row)->getValue(),
                'kode_jenis_nilai' => $sheet->getCell('AZ' . $row)->getValue(),
                'kode_kondisi_barang' => $sheet->getCell('BA' . $row)->getValue()
            ];

            // Cek apakah data sudah ada
            $existing = $this->bcBarangModel
                ->where('nomor_aju', $nomorAju)
                ->where('seri_barang', $seriBarang)
                ->first();

            if ($existing) {
                $result = $this->bcBarangModel->update($existing['id'], $data);
                $action = 'updated';
            } else {
                $result = $this->bcBarangModel->insert($data);
                $action = 'inserted';
            }

            if (!$result) {
                throw new \RuntimeException("Failed to {$action} barang data at row {$row}: " . implode(', ', $this->bcBarangModel->errors()));
            }
            
            $this->importCounters['barang']++;
        }
    }

    protected function processBarangTarifSheet($sheet)
    {
        if (!$sheet) return;

        $highestRow = $sheet->getHighestRow();

        for ($row = 2; $row <= $highestRow; $row++) {
            $nomorAju = $sheet->getCell('A' . $row)->getValue();
            $seriBarang = $sheet->getCell('B' . $row)->getValue();
            $kodePungutan = $sheet->getCell('C' . $row)->getValue();
            
            // Skip empty rows
            if (empty($nomorAju) || empty($seriBarang) || empty($kodePungutan)) {
                continue;
            }

            $data = [
                'nomor_aju' => $nomorAju,
                'seri_barang' => $seriBarang,
                'kode_pungutan' => $kodePungutan,
                'kode_tarif' => $sheet->getCell('D' . $row)->getValue(),
                'tarif' => $this->cleanNumber($sheet->getCell('E' . $row)->getValue()),
                'kode_fasilitas' => $sheet->getCell('F' . $row)->getValue(),
                'tarif_fasilitas' => $this->cleanNumber($sheet->getCell('G' . $row)->getValue()),
                'nilai_bayar' => $this->cleanNumber($sheet->getCell('H' . $row)->getValue()),
                'nilai_fasilitas' => $this->cleanNumber($sheet->getCell('I' . $row)->getValue()),
                'kode_satuan' => $sheet->getCell('J' . $row)->getValue(),
                'jumlah_satuan' => $this->cleanNumber($sheet->getCell('K' . $row)->getValue())
            ];

            // Cek apakah data sudah ada
            $existing = $this->bcBarangTarifModel
                ->where('nomor_aju', $nomorAju)
                ->where('seri_barang', $seriBarang)
                ->where('kode_pungutan', $kodePungutan)
                ->first();

            if ($existing) {
                $result = $this->bcBarangTarifModel->update($existing['id'], $data);
                $action = 'updated';
            } else {
                $result = $this->bcBarangTarifModel->insert($data);
                $action = 'inserted';
            }

            if (!$result) {
                throw new \RuntimeException("Failed to {$action} barang tarif data at row {$row}: " . implode(', ', $this->bcBarangTarifModel->errors()));
            }
            
            $this->importCounters['barang_tarif']++;
        }
    }

    protected function processBarangDokumenSheet($sheet)
    {
        if (!$sheet) return;

        $highestRow = $sheet->getHighestRow();

        for ($row = 2; $row <= $highestRow; $row++) {
            $nomorAju = $sheet->getCell('A' . $row)->getValue();
            $seriBarang = $sheet->getCell('B' . $row)->getValue();
            $seriDokumen = $sheet->getCell('C' . $row)->getValue();
            
            // Skip empty rows
            if (empty($nomorAju) || empty($seriBarang) || empty($seriDokumen)) {
                continue;
            }

            $data = [
                'nomor_aju' => $nomorAju,
                'seri_barang' => $seriBarang,
                'seri_dokumen' => $seriDokumen,
                'seri_izin' => $sheet->getCell('D' . $row)->getValue()
            ];

            // Cek apakah data sudah ada
            $existing = $this->bcBarangDokumenModel
                ->where('nomor_aju', $nomorAju)
                ->where('seri_barang', $seriBarang)
                ->where('seri_dokumen', $seriDokumen)
                ->first();

            if ($existing) {
                $result = $this->bcBarangDokumenModel->update($existing['id'], $data);
                $action = 'updated';
            } else {
                $result = $this->bcBarangDokumenModel->insert($data);
                $action = 'inserted';
            }

            if (!$result) {
                throw new \RuntimeException("Failed to {$action} barang dokumen data at row {$row}: " . implode(', ', $this->bcBarangDokumenModel->errors()));
            }
            
            $this->importCounters['barang_dokumen']++;
        }
    }

    protected function processBarangEntitasSheet($sheet)
    {
        if (!$sheet) return;

        $highestRow = $sheet->getHighestRow();

        for ($row = 2; $row <= $highestRow; $row++) {
            $nomorAju = $sheet->getCell('A' . $row)->getValue();
            $seriBarang = $sheet->getCell('B' . $row)->getValue();
            $seriEntitas = $sheet->getCell('C' . $row)->getValue();
            
            // Skip empty rows
            if (empty($nomorAju) || empty($seriBarang) || empty($seriEntitas)) {
                continue;
            }

            $data = [
                'nomor_aju' => $nomorAju,
                'seri_barang' => $seriBarang,
                'seri_entitas' => $seriEntitas
            ];

            // Cek apakah data sudah ada
            $existing = $this->bcBarangEntitasModel
                ->where('nomor_aju', $nomorAju)
                ->where('seri_barang', $seriBarang)
                ->where('seri_entitas', $seriEntitas)
                ->first();

            if ($existing) {
                $result = $this->bcBarangEntitasModel->update($existing['id'], $data);
                $action = 'updated';
            } else {
                $result = $this->bcBarangEntitasModel->insert($data);
                $action = 'inserted';
            }

            if (!$result) {
                throw new \RuntimeException("Failed to {$action} barang entitas data at row {$row}: " . implode(', ', $this->bcBarangEntitasModel->errors()));
            }
            
            $this->importCounters['barang_entitas']++;
        }
    }

    protected function processBarangVdSheet($sheet)
    {
        if (!$sheet) return;

        $highestRow = $sheet->getHighestRow();

        for ($row = 2; $row <= $highestRow; $row++) {
            $nomorAju = $sheet->getCell('A' . $row)->getValue();
            $seriBarang = $sheet->getCell('B' . $row)->getValue();
            $kodeVd = $sheet->getCell('C' . $row)->getValue();
            
            // Skip empty rows
            if (empty($nomorAju) || empty($seriBarang) || empty($kodeVd)) {
                continue;
            }

            $data = [
                'nomor_aju' => $nomorAju,
                'seri_barang' => $seriBarang,
                'kode_vd' => $kodeVd,
                'nilai_barang' => $this->cleanNumber($sheet->getCell('D' . $row)->getValue()),
                'biaya_tambahan' => $this->cleanNumber($sheet->getCell('E' . $row)->getValue()),
                'biaya_pengurang' => $this->cleanNumber($sheet->getCell('F' . $row)->getValue()),
                'jatuh_tempo' => $this->convertExcelDate($sheet->getCell('G' . $row)->getValue())
            ];

            // Cek apakah data sudah ada
            $existing = $this->bcBarangVdModel
                ->where('nomor_aju', $nomorAju)
                ->where('seri_barang', $seriBarang)
                ->where('kode_vd', $kodeVd)
                ->first();

            if ($existing) {
                $result = $this->bcBarangVdModel->update($existing['id'], $data);
                $action = 'updated';
            } else {
                $result = $this->bcBarangVdModel->insert($data);
                $action = 'inserted';
            }

            if (!$result) {
                throw new \RuntimeException("Failed to {$action} barang VD data at row {$row}: " . implode(', ', $this->bcBarangVdModel->errors()));
            }
            
            $this->importCounters['barang_vd']++;
        }
    }

    protected function processPungutanSheet($sheet)
    {
        if (!$sheet) return;

        $highestRow = $sheet->getHighestRow();

        for ($row = 2; $row <= $highestRow; $row++) {
            $nomorAju = $sheet->getCell('A' . $row)->getValue();
            $kodeJenisPungutan = $sheet->getCell('C' . $row)->getValue();
            
            // Skip empty rows
            if (empty($nomorAju) || empty($kodeJenisPungutan)) {
                continue;
            }

            $data = [
                'nomor_aju' => $nomorAju,
                'kode_fasilitas_tarif' => $sheet->getCell('B' . $row)->getValue(),
                'kode_jenis_pungutan' => $kodeJenisPungutan,
                'nilai_pungutan' => $this->cleanNumber($sheet->getCell('D' . $row)->getValue()),
                'npwp_billing' => $sheet->getCell('E' . $row)->getValue()
            ];

            // Cek apakah data sudah ada
            $existing = $this->bcPungutanModel
                ->where('nomor_aju', $nomorAju)
                ->where('kode_jenis_pungutan', $kodeJenisPungutan)
                ->first();

            if ($existing) {
                $result = $this->bcPungutanModel->update($existing['id'], $data);
                $action = 'updated';
            } else {
                $result = $this->bcPungutanModel->insert($data);
                $action = 'inserted';
            }

            if (!$result) {
                throw new \RuntimeException("Failed to {$action} pungutan data at row {$row}: " . implode(', ', $this->bcPungutanModel->errors()));
            }
            
            $this->importCounters['pungutan']++;
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

    protected function convertExcelDate($excelDate)
    {
        if (is_numeric($excelDate)) {
            $unixDate = ($excelDate - 25569) * 86400;
            return gmdate("Y-m-d", $unixDate);
        }
        
        // Handle string dates
        if (is_string($excelDate) && !empty($excelDate)) {
            try {
                $date = \DateTime::createFromFormat('Y-m-d', $excelDate);
                if ($date) {
                    return $date->format('Y-m-d');
                }
                
                // Try other common formats
                $date = \DateTime::createFromFormat('d/m/Y', $excelDate);
                if ($date) {
                    return $date->format('Y-m-d');
                }
                
                $date = \DateTime::createFromFormat('m/d/Y', $excelDate);
                if ($date) {
                    return $date->format('Y-m-d');
                }
                
            } catch (\Exception $e) {
                return null;
            }
        }
        
        return null;
    }
}