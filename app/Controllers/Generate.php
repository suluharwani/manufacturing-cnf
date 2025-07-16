<?php

namespace App\Controllers;

use App\Models\MdlWorkOrder;
use App\Models\ProformaInvoice;
class Generate extends BaseController
{
    protected $proformaInvoiceModel;
    protected $workOrderModel;
    public function __construct()
    {
        //   parent::__construct();
        $this->db = \Config\Database::connect();

        $this->proformaInvoiceModel = new ProformaInvoice();
        $this->workOrderModel = new MdlWorkOrder();

    }
// app/Controllers/ProformaInvoice.php
public function generateCode($customerId)
{
    // Get current date components
    $year = date('y');
    $month = date('m');
    $day = date('d');
    
    // Count ALL invoices for this customer (not just today)
    $count = $this->proformaInvoiceModel
        ->where('customer_id', $customerId)
        ->countAllResults();
    
    $sequence = $count + 1; // Start from 1
    
    // Format the code: PI{CustomerID}{Year}{Month}{Day}{TotalCount}
    $generatedCode = 'PI' . $customerId .'.'.$year . $month . $day .'-'. str_pad($sequence, 3, '0', STR_PAD_LEFT);
    
    return $this->response->setJSON([
        'code' => $generatedCode
    ]);
}
// app/Controllers/Wo.php
public function generateWoCode($invoiceId)
{
    // Get the PI number
    $pi = $this->proformaInvoiceModel->find($invoiceId);
    if (!$pi) {
        return $this->response->setJSON(['error' => 'Proforma Invoice not found']);
    }

    // Get current date
    $currentDate = date('Y-m-d');
    
    // Count how many WOs were created today
    $count = $this->workOrderModel
        ->where('DATE(created_at)', $currentDate)
        ->countAllResults();
    
    $sequence = $count + 1; // Start from 1
    
    // Format the code: WO-{PI_NUMBER}-{SEQUENCE}
    $generatedCode = 'WO-' . $pi['invoice_number'] . '-' . str_pad($sequence, 3, '0', STR_PAD_LEFT);
    
    return $this->response->setJSON([
        'code' => $generatedCode
    ]);
}
}