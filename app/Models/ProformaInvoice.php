<?php

namespace App\Models;

use CodeIgniter\Model;

class ProformaInvoice extends Model
{
    protected $table            = 'proforma_invoice';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = ['peb','tgl_peb','status_delivery','id','port_loading','port_discharge','end_prod','charge','deposit','invoice_number','invoice_date','customer_id','customer_address','id_currency','etd','eta','payment_terms','remarks','status','vessel','cus_po','loading_date','updated_at','deleted_at','created_at'];

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

    public function getInvoiceData($id)
    {
        // Fetch the proforma invoice
        $invoice = $this->find($id);

        // Fetch the invoice details
        $details = $this->db->table('proforma_invoice_details')
            ->where('invoice_id', $id)
            ->get()
            ->getResultArray();

        // Fetch product details for each item
        foreach ($details as &$detail) {
            $product = $this->db->table('product')
                ->where('id', $detail['id_product'])
                ->get()
                ->getRowArray();
            $detail['product'] = $product;
        }

        return [
            'invoice' => $invoice,
            'details' => $details,
        ];
    }
    public function getDeliveryNoteData($id)
    {
        // Fetch the proforma invoice
        $invoice = $this->find($id);

        // Fetch the invoice details
        $details = $this->db->table('proforma_invoice_details')
            ->where('invoice_id', $id)
            ->get()
            ->getResultArray();

        // Fetch product details for each item
        foreach ($details as &$detail) {
            $product = $this->db->table('product')
            ->select('product.*, product_details.*')
                 ->join('product_details', 'product.id = product_details.id_product', 'left')
                ->where('product.id', $detail['id_product'])
                ->get()
                ->getRowArray();
            $detail['product'] = $product;
        }

        return [
            'invoice' => $invoice,
            'details' => $details,
        ];
    }
    public function searchInvoices($id_product, $start_date, $end_date, $status, $loading_date_filled)
    {
        $builder = $this->db->table('proforma_invoice');
        $builder->select('proforma_invoice.*, proforma_invoice_details.*');
        $builder->join('proforma_invoice_details', 'proforma_invoice.id = proforma_invoice_details.invoice_id', 'left');

        // Filter berdasarkan id_product
        if (!empty($id_product)) {
            $builder->where('proforma_invoice_details.id_product', $id_product);
        }

        // Filter berdasarkan rentang tanggal created_at
        if (!empty($start_date) && !empty($end_date)) {
            $builder->where('proforma_invoice.created_at >=', $start_date);
            $builder->where('proforma_invoice.created_at <=', $end_date);
        }

        // Filter berdasarkan status (null atau 1)
        if ($status !== null) {
            $builder->where('proforma_invoice.status', $status);
        } else {
            $builder->where('proforma_invoice.status IS NULL');
        }

        // Filter berdasarkan loading_date (terisi atau null)
        if ($loading_date_filled) {
            $builder->where('proforma_invoice.loading_date IS NOT NULL');
        } else {
            $builder->where('proforma_invoice.loading_date IS NULL');
        }

        return $builder->get()->getResultArray();
    }
}

