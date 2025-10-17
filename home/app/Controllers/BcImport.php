<?php namespace App\Controllers;

use App\Models\BcHeaderModel;
use App\Models\BcEntitasModel;
use App\Models\BcDokumenModel;
use App\Models\BcBarangModel;
use PhpOffice\PhpSpreadsheet\IOFactory;
use CodeIgniter\Database\ConnectionInterface;
class BcImport extends BaseController
{ 
    protected $db;
    protected $bcHeaderModel;
    protected $bcEntitasModel;
    protected $bcDokumenModel;
    protected $bcBarangModel;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
        helper('form');
        $this->bcHeaderModel = new BcHeaderModel();
        $this->bcEntitasModel = new BcEntitasModel();
        $this->bcDokumenModel = new BcDokumenModel();
        $this->bcBarangModel = new BcBarangModel();
    }

    public function index()
    {
        $data = [
            'title' => 'BC Data Import',
            'bc_data' => $this->bcHeaderModel->orderBy('created_at', 'DESC')->findAll(),
        ];
        $dataview['content'] = view('admin/content/Bcimport', $data);
        return view( 'admin/index', $dataview);
    }

public function importForm()
{
    try {
        return view('bc_import/import_form');
    } catch (\CodeIgniter\Exceptions\PageNotFoundException $e) {
        // View file doesn't exist
        log_message('error', 'View file not found: bc_import/import_form.php');
        throw new \RuntimeException('The import form could not be loaded');
    }
}

    public function processImport()
    {
        $validation = $this->validate([
            'excel_file' => [
                'rules' => 'uploaded[excel_file]|ext_in[excel_file,xlsx,xls]',
                'errors' => [
                    'uploaded' => 'Please select a file to upload',
                    'ext_in' => 'Only Excel files are allowed'
                ]
            ]
        ]);

        if (!$validation) {
            return redirect()->to('/bc-import')->withInput()->with('errors', $this->validator->getErrors());
        }

        $file = $this->request->getFile('excel_file');

        try {
            $spreadsheet = IOFactory::load($file->getTempName());
            
            // Start transaction
            $this->db->transStart();
            
            // Process each sheet
            $this->processHeaderSheet($spreadsheet->getSheetByName('HEADER'));
            $this->processEntitasSheet($spreadsheet->getSheetByName('ENTITAS'));
            $this->processDokumenSheet($spreadsheet->getSheetByName('DOKUMEN'));
            $this->processBarangSheet($spreadsheet->getSheetByName('BARANG'));
            
            // Complete transaction
            $this->db->transComplete();
            
            if ($this->db->transStatus() === false) {
                throw new \RuntimeException('Database transaction failed');
            }

            return redirect()->to('/bc-import')->with('success', 'Data imported successfully');
        } catch (\Exception $e) {
            return redirect()->to('/bc-import/form')->with('error', 'Error importing data: ' . $e->getMessage());
        }
    }

    protected function processHeaderSheet($sheet)
    {
        $data = [
            'nomor_aju' => $sheet->getCell('A2')->getValue(),
            'kode_dokumen' => $sheet->getCell('B2')->getValue(),
            'kode_kantor' => $sheet->getCell('C2')->getValue(),
            'kode_kantor_bongkar' => $sheet->getCell('D2')->getValue(),
            'kode_kantor_periksa' => $sheet->getCell('E2')->getValue(),
            'kode_kantor_tujuan' => $sheet->getCell('F2')->getValue(),
            'kode_jenis_impor' => $sheet->getCell('G2')->getValue(),
            'kode_jenis_tpb' => $sheet->getCell('J2')->getValue(),
            'kode_jenis_prosedur' => $sheet->getCell('L2')->getValue(),
            'kode_cara_bayar' => $sheet->getCell('P2')->getValue(),
            'kode_pelabuhan_muat' => $sheet->getCell('S2')->getValue(),
            'kode_pelabuhan_tujuan' => $sheet->getCell('W2')->getValue(),
            'kode_tps' => $sheet->getCell('X2')->getValue(),
            'nomor_bc11' => $sheet->getCell('AB2')->getValue(),
            'tanggal_bc11' => $this->convertExcelDate($sheet->getCell('AC2')->getValue()),
            'nomor_pos' => $sheet->getCell('AD2')->getValue(),
            'nomor_sub_pos' => $sheet->getCell('AE2')->getValue(),
            'tanggal_tiba' => $this->convertExcelDate($sheet->getCell('AL2')->getValue()),
            'nilai_barang' => $sheet->getCell('AJ2')->getValue(),
            'nilai_incoterm' => $sheet->getCell('AK2')->getValue(),
            'asuransi' => $sheet->getCell('AN2')->getValue(),
            'freight' => $sheet->getCell('AO2')->getValue(),
            'fob' => $sheet->getCell('AP2')->getValue(),
            'cif' => $sheet->getCell('AV2')->getValue(),
            'ndpbm' => $sheet->getCell('AX2')->getValue(),
            'bruto' => $sheet->getCell('BH2')->getValue(),
            'netto' => $sheet->getCell('BI2')->getValue(),
            'kode_valuta' => $sheet->getCell('CI2')->getValue(),
            'kode_incoterm' => $sheet->getCell('CJ2')->getValue(),
            'kota_pernyataan' => $sheet->getCell('CM2')->getValue(),
            'tanggal_pernyataan' => $this->convertExcelDate($sheet->getCell('CN2')->getValue()),
            'nama_pernyataan' => $sheet->getCell('CO2')->getValue(),
            'jabatan_pernyataan' => $sheet->getCell('CP2')->getValue(),
            'nomor_daftar' => $sheet->getCell('CQ2')->getValue(),
            'tanggal_daftar' => $this->convertExcelDate($sheet->getCell('CR2')->getValue())
        ];

        $this->bcHeaderModel->insert($data);
    }

    protected function processEntitasSheet($sheet)
    {
        $highestRow = $sheet->getHighestRow();

        for ($row = 2; $row <= $highestRow; $row++) {
            $data = [
                'nomor_aju' => $sheet->getCell('A' . $row)->getValue(),
                'seri' => $sheet->getCell('B' . $row)->getValue(),
                'kode_entitas' => $sheet->getCell('C' . $row)->getValue(),
                'kode_jenis_identitas' => $sheet->getCell('D' . $row)->getValue(),
                'nomor_identitas' => $sheet->getCell('E' . $row)->getValue(),
                'nama_entitas' => $sheet->getCell('F' . $row)->getValue(),
                'alamat_entitas' => $sheet->getCell('G' . $row)->getValue(),
                'nib_entitas' => $sheet->getCell('H' . $row)->getValue(),
                'kode_jenis_api' => $sheet->getCell('I' . $row)->getValue(),
                'kode_status' => $sheet->getCell('J' . $row)->getValue(),
                'kode_negara' => $sheet->getCell('L' . $row)->getValue()
            ];

            $this->bcEntitasModel->insert($data);
        }
    }

    protected function processDokumenSheet($sheet)
    {
        $highestRow = $sheet->getHighestRow();

        for ($row = 2; $row <= $highestRow; $row++) {
            $data = [
                'nomor_aju' => $sheet->getCell('A' . $row)->getValue(),
                'seri' => $sheet->getCell('B' . $row)->getValue(),
                'kode_dokumen' => $sheet->getCell('C' . $row)->getValue(),
                'nomor_dokumen' => $sheet->getCell('D' . $row)->getValue(),
                'tanggal_dokumen' => $this->convertExcelDate($sheet->getCell('E' . $row)->getValue()),
                'kode_fasilitas' => $sheet->getCell('F' . $row)->getValue(),
                'kode_ijin' => $sheet->getCell('G' . $row)->getValue()
            ];

            $this->bcDokumenModel->insert($data);
        }
    }

    protected function processBarangSheet($sheet)
    {
        $highestRow = $sheet->getHighestRow();

        for ($row = 2; $row <= $highestRow; $row++) {
            $data = [
                'nomor_aju' => $sheet->getCell('A' . $row)->getValue(),
                'seri_barang' => $sheet->getCell('B' . $row)->getValue(),
                'hs' => $sheet->getCell('C' . $row)->getValue(),
                'kode_barang' => $sheet->getCell('D' . $row)->getValue(),
                'uraian' => $sheet->getCell('E' . $row)->getValue(),
                'merek' => $sheet->getCell('F' . $row)->getValue(),
                'tipe' => $sheet->getCell('G' . $row)->getValue(),
                'ukuran' => $sheet->getCell('H' . $row)->getValue(),
                'spesifikasi_lain' => $sheet->getCell('I' . $row)->getValue(),
                'kode_satuan' => $sheet->getCell('J' . $row)->getValue(),
                'jumlah_satuan' => $sheet->getCell('K' . $row)->getValue(),
                'kode_kemasan' => $sheet->getCell('L' . $row)->getValue(),
                'jumlah_kemasan' => $sheet->getCell('M' . $row)->getValue(),
                'netto' => $sheet->getCell('R' . $row)->getValue(),
                'bruto' => $sheet->getCell('S' . $row)->getValue(),
                'cif' => $sheet->getCell('W' . $row)->getValue(),
                'cif_rupiah' => $sheet->getCell('X' . $row)->getValue(),
                'ndpbm' => $sheet->getCell('Y' . $row)->getValue(),
                'fob' => $sheet->getCell('Z' . $row)->getValue(),
                'asuransi' => $sheet->getCell('AA' . $row)->getValue(),
                'freight' => $sheet->getCell('AB' . $row)->getValue(),
                'kode_negara_asal' => $sheet->getCell('AY' . $row)->getValue(),
                'kode_jenis_nilai' => $sheet->getCell('AZ' . $row)->getValue(),
                'kode_kondisi_barang' => $sheet->getCell('BA' . $row)->getValue()
            ];

            $this->bcBarangModel->insert($data);
        }
    }

    protected function convertExcelDate($excelDate)
    {
        if (is_numeric($excelDate)) {
            $unixDate = ($excelDate - 25569) * 86400;
            return gmdate("Y-m-d", $unixDate);
        }
        return $excelDate;
    }
}