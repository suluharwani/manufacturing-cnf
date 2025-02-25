<?php

namespace App\Models;

use CodeIgniter\Model;

class MdlWorkOrder extends Model
{
    protected $table            = 'work_order';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['id','invoice_id','kode','target_date','start','end','updated_at','deleted_at','created_at','status','release_date','manufacture_finishes','loading_date'];

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

    public function getPrintData($id)    {
        // Fetch the proforma invoice
        $invoice = $this->where('work_order.id', $id)
                ->join('proforma_invoice', 'proforma_invoice.id = work_order.invoice_id')
                ->join('customer', 'customer.id = proforma_invoice.customer_id')        
        ->get()->getResultArray()[0];

        // Fetch the invoice details
        $details = $this->db->table('work_order_detail')
            ->select('work_order_detail.*, product.*, finishing.name as finishing_name, finishing.picture as f_picture')
            ->where('wo_id', $id)
            ->join('product', 'product.id = work_order_detail.product_id')
            ->join('finishing', 'finishing.id = work_order_detail.finishing_id')
            ->get()
            ->getResultArray();

        // Fetch product details for each item
        // foreach ($details as &$detail) {
        //     $product = $this->db->table('product')
        //         ->select('product.*, finishing.picture as f_picture, finishing.name as finishing')
        //         ->where('product.id', $detail['product_id'])
        //         ->join('finishing', 'finishing.id = '.$detail['finishing_id'].'')
        //         ->get()
        //         ->getRowArray();
        //     $detail['product'] = $product;
        // }

        return [
            'invoice' => $invoice,
            'details' => $details,
            // 'product' => $detail['product'],
        ];
    }
}
