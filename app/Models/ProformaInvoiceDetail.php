<?php

namespace App\Models;

use CodeIgniter\Model;

class ProformaInvoiceDetail extends Model
{
    protected $table            = 'proforma_invoice_details';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [ 'id',
                                    'invoice_id',
                                    'id_product',
                                    'id_currency',
                                    'item_description',
                                    'hs_code',
                                    'quantity',
                                    'unit',
                                    'unit_price',
                                    'total_price' ,
                                    'remarks',
                                    'updated_at',
                                    'deleted_at' ];

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


 public function getProductById($id)
    {
        $builder = $this->db->table($this->table);

        // Define the joins
        $builder->select('proforma_invoice_details.*, 
                          product.hs_code as hs_code, 
                          product.kode as product_code, 
                          product.nama as product_name, 
                          product.id_product_cat as product_category_id, 
                          product.picture as product_picture, 
                          product.text as product_text,
                          currency.kode as currency_code, 
                          currency.nama as currency_name,
                          currency.rate as currency_rate');
        
        $builder->join('product', 'product.id = proforma_invoice_details.id_product', 'left');
        $builder->join('currency', 'currency.id = proforma_invoice_details.id_currency', 'left');

        // Apply the where condition
        $builder->where('proforma_invoice_details.id', $id);
  
        // Execute the query and return the result
        return $builder->get()->getRowArray();
    }

    public function updateProduct($id, $data)
    {
        $builder = $this->db->table($this->table);

        // Apply the where condition
        $builder->where('id', $id);

        // Update the record with new data
        return $builder->update($data);
    }

    public function deleteProduct($id)
    {
        $builder = $this->db->table($this->table);

        // Apply the where condition
        $builder->where('id', $id);

        // Delete the record
        return $builder->delete();
    }
}
