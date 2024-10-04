<?php

namespace App\Models;

use CodeIgniter\Model;

class MdlCurrency extends Model
{
    protected $table            = 'currency';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['id', 'kode', 'nama', 'rate','oldrate','update','olddate','updated_at','deleted_at','created_at'];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [];
    protected array $castHandlers = [];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    public function saveRates(array $rates)
{
    // Currency name mapping
    $currencyNames = [
        "EUR" => "Euro",
        "USD" => "US Dollar",
        "JPY" => "Japanese Yen",
        "BGN" => "Bulgarian Lev",
        "CZK" => "Czech Republic Koruna",
        "DKK" => "Danish Krone",
        "GBP" => "British Pound Sterling",
        "HUF" => "Hungarian Forint",
        "PLN" => "Polish Zloty",
        "RON" => "Romanian Leu",
        "SEK" => "Swedish Krona",
        "CHF" => "Swiss Franc",
        "ISK" => "Icelandic Króna",
        "NOK" => "Norwegian Krone",
        "HRK" => "Croatian Kuna",
        "RUB" => "Russian Ruble",
        "TRY" => "Turkish Lira",
        "AUD" => "Australian Dollar",
        "BRL" => "Brazilian Real",
        "CAD" => "Canadian Dollar",
        "CNY" => "Chinese Yuan",
        "HKD" => "Hong Kong Dollar",
        "IDR" => "Indonesian Rupiah",
        "ILS" => "Israeli New Sheqel",
        "INR" => "Indian Rupee",
        "KRW" => "South Korean Won",
        "MXN" => "Mexican Peso",
        "MYR" => "Malaysian Ringgit",
        "NZD" => "New Zealand Dollar",
        "PHP" => "Philippine Peso",
        "SGD" => "Singapore Dollar",
        "THB" => "Thai Baht",
        "ZAR" => "South African Rand"
    ];

    foreach ($rates as $currency => $rate) {
        // Check if the currency already exists
        $existing = $this->where('kode', $currency)->first();

        if ($existing) {
            // Update existing rate
            $this->update($existing['id'], [
                'nama' => $currencyNames[$currency] ?? 'Unknown', 
                'oldrate' => $existing['rate'],
                'olddate' => $existing['update'],
                'rate' => $rate,
                'update' => date('Y-m-d H:i:s') 
            ]);
        } else {
            $this->insert([
                'nama' => $currencyNames[$currency] ?? 'Unknown', 
                'kode' => $currency,
                'rate' => $rate,
                'update' => date('Y-m-d H:i:s')
            ]);
        }
    }
}

}
