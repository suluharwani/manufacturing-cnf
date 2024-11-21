<?php

namespace App\Models;

use CodeIgniter\Model;

class MdlSupplier extends Model
{
    protected $table            = 'supplier';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id',
        'code',
        'supplier_name',
        'contact_name',
        'contact_email',
        'contact_phone',
        'address',
        'city',
        'state',
        'postal_code',
        'id_country',
        'id_currency',
        'tax_number',
        'website_url',
        'logo_url',
        'status',
        'created_at',
        'deleted_at',
        'updated_at',
    ];

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

     protected $validationRules = [];

    protected $validationMessages = [
        'supplier_name' => [
            'required' => 'Supplier name is required.',
            'max_length' => 'Supplier name must not exceed 255 characters.',
        ],
        'contact_email' => [
            'valid_email' => 'Please provide a valid email address.',
        ],
        'logo_url' => [
            'valid_url' => 'Please provide a valid URL for the logo.',
        ],
    ];

    public function getAllSuppliers($status = null)
    {
        if ($status) {
            return $this->where('status', $status)->findAll();
        }
        return $this->findAll();
    }

    public function searchSuppliers($keyword)
    {
        return $this->like('supplier_name', $keyword)
                    ->orLike('city', $keyword)
                    ->findAll();
    }
}
