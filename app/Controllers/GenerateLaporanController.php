<?php
namespace App\Controllers;

use App\Models\GenerateLaporanModel;

class GenerateLaporanController extends BaseController
{
    protected $generateLaporanModel;

    public function __construct()
    {
        $this->generateLaporanModel = new GenerateLaporanModel();
    }


public function generateAll()
{
    $rules = [
        'periode' => 'required|valid_date'
    ];
    
    if (!$this->validate($rules)) {
        return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
    }
    
    $periode = $this->request->getPost('periode');
    
    $results = $this->generateLaporanModel->generateAllLaporan($periode);
    
    // Format hasil untuk ditampilkan
    $messages = [];
    $totalGenerated = 0;
    
    foreach ($results as $key => $result) {
        $messages[] = [
            'type' => $result['status'] === 'generated' ? 'success' : 'info',
            'message' => ucwords(str_replace('_', ' ', $key)) . ': ' . $result['message'] . 
                        ($result['count'] > 0 ? ' (' . $result['count'] . ' records)' : '')
        ];
        
        if ($result['status'] === 'generated') {
            $totalGenerated += $result['count'];
        }
    }
    
    $summary = "Generate semua laporan selesai. Total data baru: $totalGenerated records";
    
    return redirect()->to('/report/bea_cukai')
        ->with('messages', $messages)
        ->with('summary', $summary);
}
    public function generate()
    {
        $rules = [
            'jenis_laporan' => 'required',
            'start_date' => 'required|valid_date',
            'end_date' => 'required|valid_date'
        ];
        
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        
        $jenisLaporan = $this->request->getPost('jenis_laporan');
        $startDate = $this->request->getPost('start_date');
        $endDate = $this->request->getPost('end_date');
        
        $count = 0;
        $message = '';
        
        switch ($jenisLaporan) {
            case 'pemasukan_bahan_baku':
                $count = $this->generateLaporanModel->generatePemasukanBahanBaku($startDate, $endDate);
                $message = "Berhasil generate $count data laporan pemasukan bahan baku";
                break;
                
            case 'pemakaian_bahan_baku':
                $count = $this->generateLaporanModel->generatePemakaianBahanBaku($startDate, $endDate);
                $message = "Berhasil generate $count data laporan pemakaian bahan baku";
                break;
                
            case 'pemasukan_hasil_produksi':
                $count = $this->generateLaporanModel->generatePemasukanHasilProduksi($startDate, $endDate);
                $message = "Berhasil generate $count data laporan pemasukan hasil produksi";
                break;
                
            case 'pengeluaran_hasil_produksi':
                $count = $this->generateLaporanModel->generatePengeluaranHasilProduksi($startDate, $endDate);
                $message = "Berhasil generate $count data laporan pengeluaran hasil produksi";
                break;
                
            case 'mutasi_bahan_baku':
                $count = $this->generateLaporanModel->generateMutasiBahanBaku($startDate);
                $message = "Berhasil generate $count data laporan mutasi bahan baku untuk periode $startDate";
                break;
                
            case 'mutasi_hasil_produksi':
                $count = $this->generateLaporanModel->generateMutasiHasilProduksi($startDate);
                $message = "Berhasil generate $count data laporan mutasi hasil produksi untuk periode $startDate";
                break;
                
            case 'waste_scrap':
                $count = $this->generateLaporanModel->generateWasteScrap($startDate, $endDate);
                $message = "Berhasil generate $count data laporan waste/scrap";
                break;
                
            default:
                return redirect()->back()->with('error', 'Jenis laporan tidak valid');
        }
        
        return redirect()->to('/report/bea_cukai')->with('success', $message);
    }
}