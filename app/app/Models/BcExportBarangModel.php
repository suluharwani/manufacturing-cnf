<?php namespace App\Models;

use CodeIgniter\Model;

class BcExportBarangModel extends Model
{
    protected $table = 'bc_e_barang';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'nomor_aju', 'seri_barang', 'hs', 'uraian', 'kode_satuan',
        'jumlah_satuan', 'netto', 'fob', 'kode_negara_tujuan'
    ];
    
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    
    // Get goods by nomor_aju
    public function getByNomorAju($nomorAju)
    {
        $result = $this->where('nomor_aju', $nomorAju)
                   ->orderBy('seri_barang', 'ASC')
                   ->findAll();
        return $result ? $result : [];
    }
    
    // Format FOB value with thousand separators
    public function formatFob($value, $currencyCode = 'USD')
    {
        if (!is_numeric($value) || $value == 0) {
            return '-';
        }
        
        $currencySymbols = [
            'IDR' => 'Rp',
            'USD' => '$',
            'EUR' => '€',
            'JPY' => '¥'
        ];
        
        $symbol = $currencySymbols[$currencyCode] ?? '$';
        $formattedValue = number_format($value, 2, ',', '.');
        
        return $symbol . ' ' . $formattedValue;
    }
    
    // Get total FOB for a shipment
    public function getTotalFob($nomorAju)
    {
        $result = $this->selectSum('fob')
                   ->where('nomor_aju', $nomorAju)
                   ->get()
                   ->getRow();
        
        return $result ? $result->fob : 0;
    }
    
    // Clean and validate data before insert
    public function cleanBarangData($data)
    {
        // Ensure numeric fields are properly formatted
        $numericFields = ['jumlah_satuan', 'netto', 'fob'];
        
        foreach ($numericFields as $field) {
            if (isset($data[$field])) {
                $data[$field] = $this->cleanNumericValue($data[$field]);
            }
        }
        
        return $data;
    }
    
    protected function cleanNumericValue($value)
    {
        if (empty($value) || $value === '-') {
            return 0;
        }
        
        if (is_numeric($value)) {
            return (float) $value;
        }
        
        // Remove non-numeric characters except dots and commas
        $cleaned = preg_replace('/[^\d,.-]/', '', $value);
        $cleaned = str_replace(',', '.', $cleaned);
        
        return (float) $cleaned;
    }
}