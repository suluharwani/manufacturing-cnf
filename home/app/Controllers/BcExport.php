<?php namespace App\Controllers;

use App\Models\BcExportModel;
use App\Models\BcExportEntitasModel;
use App\Models\BcExportDokumenModel;
use App\Models\BcExportBarangModel;
use PhpOffice\PhpSpreadsheet\IOFactory;

class BcExport extends BaseController
{
    protected $bcExportModel;
    protected $bcExportEntitasModel;
    protected $bcExportDokumenModel;
    protected $bcExportBarangModel;

    public function __construct()
    {
        $this->bcExportModel = new BcExportModel();
        $this->bcExportEntitasModel = new BcExportEntitasModel();
        $this->bcExportDokumenModel = new BcExportDokumenModel();
        $this->bcExportBarangModel = new BcExportBarangModel();
        helper('form');
    }

    public function index()
    {
        $data = [
            'title' => 'BC Export Data',
            'bc_data' => $this->bcExportModel->orderBy('created_at', 'DESC')->findAll()
        ];
        $dataview['content'] = view('admin/content/bc_export/index', $data);
        return view( 'admin/index', $dataview);
    }


    public function importForm()
    {
        return view('bc_export/import_form');
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
            return redirect()->to('/bc-export/form')->withInput()->with('errors', $this->validator->getErrors());
        }

        $file = $this->request->getFile('excel_file');

        try {
            // Get database connection
            $db = \Config\Database::connect();
            $db->transStart();
            
            $spreadsheet = IOFactory::load($file->getTempName());
            
            // Process HEADER sheet
            $this->processHeaderSheet($spreadsheet->getSheetByName('HEADER'));
            
            // Process ENTITAS sheet
            $this->processEntitasSheet($spreadsheet->getSheetByName('ENTITAS'));
            
            // Process DOKUMEN sheet
            $this->processDokumenSheet($spreadsheet->getSheetByName('DOKUMEN'));
            
            // Process BARANG sheet
            $this->processBarangSheet($spreadsheet->getSheetByName('BARANG'));
            
            $db->transComplete();
            
            if ($db->transStatus() === false) {
                throw new \RuntimeException('Database transaction failed');
            }

            return redirect()->to('/bc-export')->with('success', 'Export data imported successfully');
        } catch (\Exception $e) {
            return redirect()->to('/bc-export/form')->with('error', 'Error importing data: ' . $e->getMessage());
        }
    }

    protected function processHeaderSheet($sheet)
    {
        $data = [
            'nomor_aju' => $sheet->getCell('A2')->getValue(),
            'kode_dokumen' => $sheet->getCell('B2')->getValue(),
            'kode_kantor' => $sheet->getCell('C2')->getValue(),
            'kode_jenis_ekspor' => $sheet->getCell('H2')->getValue(),
            'kode_jenis_tpb' => $sheet->getCell('I2')->getValue(),
            'kode_jenis_prosedur' => $sheet->getCell('K2')->getValue(),
            'kode_pelabuhan_muat' => $sheet->getCell('T2')->getValue(),
            'kode_pelabuhan_tujuan' => $sheet->getCell('W2')->getValue(),
            'nomor_bc11' => $sheet->getCell('AB2')->getValue(),
            'tanggal_bc11' => $this->convertExcelDate($sheet->getCell('AC2')->getValue()),
            'tanggal_ekspor' => $this->convertExcelDate($sheet->getCell('AH2')->getValue()),
            'nilai_barang' => $sheet->getCell('AJ2')->getValue(),
            'nilai_incoterm' => $sheet->getCell('AK2')->getValue(),
            'fob' => $sheet->getCell('AP2')->getValue(),
            'kode_valuta' => $sheet->getCell('CI2')->getValue(),
            'kode_incoterm' => $sheet->getCell('CJ2')->getValue(),
            'bruto' => $sheet->getCell('BH2')->getValue(),
            'netto' => $sheet->getCell('BI2')->getValue(),
            'kota_pernyataan' => $sheet->getCell('CM2')->getValue(),
            'tanggal_pernyataan' => $this->convertExcelDate($sheet->getCell('CN2')->getValue()),
            'nama_pernyataan' => $sheet->getCell('CO2')->getValue()
        ];

        $this->bcExportModel->insert($data);
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
                'kode_negara' => $sheet->getCell('L' . $row)->getValue()
            ];

            $this->bcExportEntitasModel->insert($data);
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
                'tanggal_dokumen' => $this->convertExcelDate($sheet->getCell('E' . $row)->getValue())
            ];

            $this->bcExportDokumenModel->insert($data);
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
                'uraian' => $sheet->getCell('E' . $row)->getValue(),
                'kode_satuan' => $sheet->getCell('J' . $row)->getValue(),
                'jumlah_satuan' => $sheet->getCell('K' . $row)->getValue(),
                'netto' => $sheet->getCell('R' . $row)->getValue(),
                'fob' => $sheet->getCell('Z' . $row)->getValue(),
                'kode_negara_tujuan' => $sheet->getCell('AY' . $row)->getValue()
            ];

            $this->bcExportBarangModel->insert($data);
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