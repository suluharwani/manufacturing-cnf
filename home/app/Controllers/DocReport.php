<?php
namespace App\Controllers;

use App\Controllers\BaseController;

class DocReport extends BaseController
{
    protected $models = [];

    public function __construct()
    {
        $this->models = [
            'purchase_order' => new \App\Models\MdlPurchaseOrder(),
            'pembelian' => new \App\Models\MdlPembelian(), // Changed from good_received_note to pembelian
            'proforma_invoice' => new \App\Models\ProformaInvoice(),
            'work_order' => new \App\Models\MdlWorkOrder(),
            'purchase_request' => new \App\Models\MdlMaterialRequest() // Assuming this is the correct model
        ];
    }

    // ==================== HALAMAN UTAMA PRINT DOCUMENT ====================
    public function index()
    {
        $data = [
            'title' => 'Print Document System',
            'breadcrumb' => ['Print Document']
        ];
        return view('print_document/index', $data);
    }

    // ==================== PURCHASE ORDER ====================
    public function purchaseOrder()
    {
        $data = [
            'title' => 'Print Purchase Order',
            'breadcrumb' => ['Print Document', 'Purchase Order']
        ];
        return view('print_document/purchase_order', $data);
    }

    public function getPurchaseOrder()
    {
        $startDate = $this->request->getGet('start_date');
        $endDate = $this->request->getGet('end_date');
        $status = $this->request->getGet('status');
        $supplier = $this->request->getGet('supplier');

        $builder = $this->models['purchase_order'];
        
        // Filter data
        if ($startDate && $endDate) {
            $builder->where('date >=', $startDate)
                   ->where('date <=', $endDate);
        }

        if ($status && $status != 'all') {
            $builder->where('status', $status);
        }

        if ($supplier) {
            $builder->where('supplier_id', $supplier);
        }

        $data = $builder->select('purchase_order.*, supplier.supplier_name')
                       ->join('supplier', 'supplier.id = purchase_order.supplier_id')
                       ->orderBy('purchase_order.date', 'DESC')
                       ->orderBy('purchase_order.code', 'ASC')
                       ->findAll();

        return $this->response->setJSON([
            'success' => true,
            'data' => $data,
            'total' => count($data)
        ]);
    }

    public function printPurchaseOrder($id)
    {
        $purchaseOrder = $this->models['purchase_order']
                            ->select('purchase_order.*, supplier.supplier_name, supplier.address, supplier.contact_name, supplier.contact_phone')
                            ->join('supplier', 'supplier.id = purchase_order.supplier_id')
                            ->find($id);

        if (!$purchaseOrder) {
            return redirect()->back()->with('error', 'Purchase Order tidak ditemukan');
        }

        $purchaseOrderItems = $this->models['purchase_order']->getItems($id);

        $data = [
            'purchase_order' => $purchaseOrder,
            'items' => $purchaseOrderItems,
            'title' => 'Purchase Order - ' . $purchaseOrder['code']
        ];

        return view('print_document/print/purchase_order', $data);
    }

    // ==================== GOOD RECEIVED NOTE (PEMBELIAN) ====================
    public function goodReceivedNote()
    {
        $data = [
            'title' => 'Print Good Received Note',
            'breadcrumb' => ['Print Document', 'Good Received Note']
        ];
        return view('print_document/good_received_note', $data);
    }

    public function getGoodReceivedNote()
    {
        $startDate = $this->request->getGet('start_date');
        $endDate = $this->request->getGet('end_date');
        $status = $this->request->getGet('status');
        $supplier = $this->request->getGet('supplier');

        $builder = $this->models['pembelian'];
        
        if ($startDate && $endDate) {
            $builder->where('tanggal_nota >=', $startDate)
                   ->where('tanggal_nota <=', $endDate);
        }

        if ($status && $status != 'all') {
            // Sesuaikan dengan field status di tabel pembelian
            $builder->where('status_pembayaran', $status);
        }

        if ($supplier) {
            $builder->where('id_supplier', $supplier);
        }

        $data = $builder->select('pembelian.*, supplier.supplier_name')
                       ->join('supplier', 'supplier.id = pembelian.id_supplier')
                       ->orderBy('pembelian.tanggal_nota', 'DESC')
                       ->orderBy('pembelian.invoice', 'ASC')
                       ->findAll();

        return $this->response->setJSON([
            'success' => true,
            'data' => $data,
            'total' => count($data)
        ]);
    }

    public function printGoodReceivedNote($id)
    {
        $pembelian = $this->models['pembelian']
                   ->select('pembelian.*, supplier.supplier_name, supplier.address, supplier.contact_name, supplier.contact_phone, currency.kode as currency_code')
                   ->join('supplier', 'supplier.id = pembelian.id_supplier')
                   ->join('currency', 'currency.id = pembelian.id_currency', 'left')
                   ->find($id);

        if (!$pembelian) {
            return redirect()->back()->with('error', 'Good Received Note tidak ditemukan');
        }

        // Ambil items dari tabel pembelian_detail
        $pembelianItems = $this->models['pembelian']->getItems($id);

        $data = [
            'pembelian' => $pembelian,
            'items' => $pembelianItems,
            'title' => 'Good Received Note - ' . $pembelian['invoice']
        ];

        return view('print_document/print/good_received_note', $data);
    }

    // ==================== PROFORMA INVOICE ====================
    public function proformaInvoice()
    {
        $data = [
            'title' => 'Print Proforma Invoice',
            'breadcrumb' => ['Print Document', 'Proforma Invoice']
        ];
        return view('print_document/proforma_invoice', $data);
    }

    public function getProformaInvoice()
    {
        $startDate = $this->request->getGet('start_date');
        $endDate = $this->request->getGet('end_date');
        $customer = $this->request->getGet('customer');
        $status = $this->request->getGet('status');

        $builder = $this->models['proforma_invoice'];
        
        if ($startDate && $endDate) {
            $builder->where('invoice_date >=', $startDate)
                   ->where('invoice_date <=', $endDate);
        }

        if ($customer) {
            $builder->where('customer_id', $customer);
        }

        if ($status && $status != 'all') {
            $builder->where('status', $status);
        }

        $data = $builder->select('proforma_invoice.*, customer.customer_name, currency.kode as currency_code')
                       ->join('customer', 'customer.id = proforma_invoice.customer_id', 'left')
                       ->join('currency', 'currency.id = proforma_invoice.id_currency', 'left')
                       ->orderBy('proforma_invoice.invoice_date', 'DESC')
                       ->orderBy('proforma_invoice.invoice_number', 'ASC')
                       ->findAll();

        return $this->response->setJSON([
            'success' => true,
            'data' => $data,
            'total' => count($data)
        ]);
    }

    public function printProformaInvoice($id)
    {
        $proforma = $this->models['proforma_invoice']
                        ->select('proforma_invoice.*, customer.customer_name, customer.address as customer_address, customer.contact_name, customer.contact_phone, currency.kode as currency_code, currency.nama as currency_name')
                        ->join('customer', 'customer.id = proforma_invoice.customer_id')
                        ->join('currency', 'currency.id = proforma_invoice.id_currency')
                        ->find($id);

        if (!$proforma) {
            return redirect()->back()->with('error', 'Proforma Invoice tidak ditemukan');
        }

        $proformaItems = $this->models['proforma_invoice']->getItems($id);

        $data = [
            'proforma' => $proforma,
            'items' => $proformaItems,
            'title' => 'Proforma Invoice - ' . $proforma['invoice_number']
        ];

        return view('print_document/print/proforma_invoice', $data);
    }

    // ==================== WORK ORDER ====================
    public function workOrder()
    {
        $data = [
            'title' => 'Print Work Order',
            'breadcrumb' => ['Print Document', 'Work Order']
        ];
        return view('print_document/work_order', $data);
    }

    public function getWorkOrder()
    {
        $startDate = $this->request->getGet('start_date');
        $endDate = $this->request->getGet('end_date');
        $status = $this->request->getGet('status');
        $invoice = $this->request->getGet('invoice');

        $builder = $this->models['work_order'];
        
        if ($startDate && $endDate) {
            $builder->where('created_at >=', $startDate)
                   ->where('created_at <=', $endDate);
        }

        if ($status && $status != 'all') {
            $builder->where('status', $status);
        }

        if ($invoice) {
            $builder->where('invoice_id', $invoice);
        }

        $data = $builder->select('work_order.*, proforma_invoice.invoice_number, customer.customer_name')
                       ->join('proforma_invoice', 'proforma_invoice.id = work_order.invoice_id')
                       ->join('customer', 'customer.id = proforma_invoice.customer_id')
                       ->orderBy('work_order.created_at', 'DESC')
                       ->orderBy('work_order.kode', 'ASC')
                       ->findAll();

        return $this->response->setJSON([
            'success' => true,
            'data' => $data,
            'total' => count($data)
        ]);
    }

    public function printWorkOrder($id)
    {
        $workOrder = $this->models['work_order']
                         ->select('work_order.*, proforma_invoice.invoice_number, customer.customer_name, customer.address as customer_address')
                         ->join('proforma_invoice', 'proforma_invoice.id = work_order.invoice_id')
                         ->join('customer', 'customer.id = proforma_invoice.customer_id')
                         ->find($id);

        if (!$workOrder) {
            return redirect()->back()->with('error', 'Work Order tidak ditemukan');
        }

        $workOrderItems = $this->models['work_order']->getItems($id);

        $data = [
            'work_order' => $workOrder,
            'items' => $workOrderItems,
            'title' => 'Work Order - ' . $workOrder['kode']
        ];

        return view('print_document/print/work_order', $data);
    }

    // ==================== PURCHASE REQUEST ====================
    public function purchaseRequest()
    {
        $data = [
            'title' => 'Print Purchase Request',
            'breadcrumb' => ['Print Document', 'Purchase Request']
        ];
        return view('print_document/purchase_request', $data);
    }

    public function getPurchaseRequest()
    {
        $startDate = $this->request->getGet('start_date');
        $endDate = $this->request->getGet('end_date');
        $status = $this->request->getGet('status');
        $department = $this->request->getGet('department');

        $builder = $this->models['purchase_request'];
        
        if ($startDate && $endDate) {
            $builder->where('created_at >=', $startDate)
                   ->where('created_at <=', $endDate);
        }

        if ($status && $status != 'all') {
            $builder->where('status', $status);
        }

        if ($department) {
            $builder->where('dept_id', $department);
        }

        $data = $builder->select('material_request.*, department.name as department_name')
                       ->join('department', 'department.id = material_request.dept_id', 'left')
                       ->orderBy('material_request.created_at', 'DESC')
                       ->orderBy('material_request.kode', 'ASC')
                       ->findAll();

        return $this->response->setJSON([
            'success' => true,
            'data' => $data,
            'total' => count($data)
        ]);
    }

    public function printPurchaseRequest($id)
    {
        $purchaseRequest = $this->models['purchase_request']
                              ->select('material_request.*, department.name as department_name')
                              ->join('department', 'department.id = material_request.dept_id', 'left')
                              ->find($id);

        if (!$purchaseRequest) {
            return redirect()->back()->with('error', 'Purchase Request tidak ditemukan');
        }

        $purchaseRequestItems = $this->models['purchase_request']->getItems($id);

        $data = [
            'purchase_request' => $purchaseRequest,
            'items' => $purchaseRequestItems,
            'title' => 'Purchase Request - ' . $purchaseRequest['kode']
        ];

        return view('print_document/print/purchase_request', $data);
    }

    // ==================== DOKUMEN TERBARU ====================
    public function recentDocuments()
    {
        $data = [
            'title' => 'Dokumen Terbaru',
            'breadcrumb' => ['Print Document', 'Dokumen Terbaru']
        ];
        return view('print_document/recent_documents', $data);
    }

    public function getRecentDocuments()
    {
        $limit = $this->request->getGet('limit') ?? 10;

        // Ambil dokumen terbaru dari berbagai tabel
        $recentDocuments = [];

        // Purchase Order Terbaru
        $purchaseOrders = $this->models['purchase_order']
                             ->select("'Purchase Order' as document_type, code as document_number, date as document_date, status, id")
                             ->orderBy('date', 'DESC')
                             ->limit($limit)
                             ->findAll();

        // Good Received Note Terbaru (dari tabel pembelian)
        $grns = $this->models['pembelian']
                   ->select("'Good Received Note' as document_type, invoice as document_number, tanggal_nota as document_date, status_pembayaran as status, id")
                   ->orderBy('tanggal_nota', 'DESC')
                   ->limit($limit)
                   ->findAll();

        // Proforma Invoice Terbaru
        $proformas = $this->models['proforma_invoice']
                        ->select("'Proforma Invoice' as document_type, invoice_number as document_number, invoice_date as document_date, status, id")
                        ->orderBy('invoice_date', 'DESC')
                        ->limit($limit)
                        ->findAll();

        // Work Order Terbaru
        $workOrders = $this->models['work_order']
                         ->select("'Work Order' as document_type, kode as document_number, created_at as document_date, status, id")
                         ->orderBy('created_at', 'DESC')
                         ->limit($limit)
                         ->findAll();

        // Gabungkan semua dokumen
        $recentDocuments = array_merge($purchaseOrders, $grns, $proformas, $workOrders);

        // Urutkan berdasarkan tanggal terbaru
        usort($recentDocuments, function($a, $b) {
            return strtotime($b['document_date']) - strtotime($a['document_date']);
        });

        // Ambil hanya $limit dokumen terbaru
        $recentDocuments = array_slice($recentDocuments, 0, $limit);

        return $this->response->setJSON([
            'success' => true,
            'data' => $recentDocuments,
            'total' => count($recentDocuments)
        ]);
    }

    // ==================== PRINT MULTIPLE DOCUMENTS ====================
    public function printMultipleDocuments()
    {
        $documentType = $this->request->getGet('document_type');
        $documentIds = $this->request->getGet('document_ids');

        if (!$documentType || !$documentIds) {
            return redirect()->back()->with('error', 'Pilih dokumen yang akan dicetak');
        }

        $documentIds = explode(',', $documentIds);

        switch ($documentType) {
            case 'purchase_order':
                return $this->printMultiplePurchaseOrders($documentIds);
            case 'good_received_note':
                return $this->printMultipleGRNs($documentIds);
            case 'proforma_invoice':
                return $this->printMultipleProformas($documentIds);
            case 'work_order':
                return $this->printMultipleWorkOrders($documentIds);
            case 'purchase_request':
                return $this->printMultiplePurchaseRequests($documentIds);
            default:
                return redirect()->back()->with('error', 'Jenis dokumen tidak valid');
        }
    }

    private function printMultiplePurchaseOrders($ids)
    {
        $documents = [];
        foreach ($ids as $id) {
            $po = $this->models['purchase_order']
                      ->select('purchase_order.*, supplier.supplier_name')
                      ->join('supplier', 'supplier.id = purchase_order.supplier_id')
                      ->find($id);
            if ($po) {
                $po['items'] = $this->models['purchase_order']->getItems($id);
                $documents[] = $po;
            }
        }

        $data = [
            'documents' => $documents,
            'title' => 'Multiple Purchase Orders'
        ];

        return view('print_document/print/multiple_purchase_orders', $data);
    }

    // ==================== PRINT MULTIPLE GOOD RECEIVED NOTES (PEMBELIAN) ====================
    private function printMultipleGRNs($ids)
    {
        $documents = [];
        foreach ($ids as $id) {
            $pembelian = $this->models['pembelian']
                       ->select('pembelian.*, supplier.supplier_name, currency.kode as currency_code')
                       ->join('supplier', 'supplier.id = pembelian.id_supplier')
                       ->join('currency', 'currency.id = pembelian.id_currency', 'left')
                       ->find($id);
            if ($pembelian) {
                $pembelian['items'] = $this->models['pembelian']->getItems($id);
                $documents[] = $pembelian;
            }
        }

        $data = [
            'documents' => $documents,
            'title' => 'Multiple Good Received Notes'
        ];

        return view('print_document/print/multiple_grns', $data);
    }

    // ==================== PRINT MULTIPLE PURCHASE REQUESTS ====================
    private function printMultiplePurchaseRequests($ids)
    {
        $documents = [];
        
        foreach ($ids as $id) {
            $purchaseRequest = $this->models['purchase_request']
                                  ->select('material_request.*, department.name as department_name, users.nama_depan, users.nama_belakang')
                                  ->join('department', 'department.id = material_request.dept_id', 'left')
                                  ->join('users', 'users.id = material_request.id_user', 'left')
                                  ->find($id);
            
            if ($purchaseRequest) {
                // Get items untuk purchase request ini
                $purchaseRequestItems = $this->models['purchase_request']->getItems($id);
                $purchaseRequest['items'] = $purchaseRequestItems;
                
                // Calculate totals
                $purchaseRequest['total_items'] = 0;
                $purchaseRequest['total_quantity'] = 0;
                $purchaseRequest['total_value'] = 0;
                
                foreach ($purchaseRequestItems as $item) {
                    $purchaseRequest['total_items']++;
                    $purchaseRequest['total_quantity'] += $item['quantity'];
                    if (isset($item['harga']) && $item['harga'] > 0) {
                        $purchaseRequest['total_value'] += ($item['quantity'] * $item['harga']);
                    }
                }
                
                // Format requester name
                $purchaseRequest['requester_name'] = trim($purchaseRequest['nama_depan'] . ' ' . $purchaseRequest['nama_belakang']);
                if (empty($purchaseRequest['requester_name'])) {
                    $purchaseRequest['requester_name'] = $purchaseRequest['requestor'] ?? '-';
                }
                
                // Format dates
                $purchaseRequest['created_formatted'] = $purchaseRequest['created_at'] ? date('d M Y', strtotime($purchaseRequest['created_at'])) : '-';
                
                $documents[] = $purchaseRequest;
            }
        }

        if (empty($documents)) {
            return redirect()->back()->with('error', 'Tidak ada Purchase Request yang ditemukan');
        }

        $data = [
            'documents' => $documents,
            'title' => 'Multiple Purchase Requests',
            'print_date' => date('d F Y'),
            'total_documents' => count($documents)
        ];

        return view('print_document/print/multiple_purchase_requests', $data);
    }

    private function printMultipleWorkOrders($ids)
    {
        $documents = [];
        
        foreach ($ids as $id) {
            $workOrder = $this->models['work_order']
                             ->select('work_order.*, proforma_invoice.invoice_number, customer.customer_name, customer.address as customer_address, customer.contact_name, customer.contact_phone, customer.tax_number as customer_tax')
                             ->join('proforma_invoice', 'proforma_invoice.id = work_order.invoice_id')
                             ->join('customer', 'customer.id = proforma_invoice.customer_id')
                             ->find($id);
            
            if ($workOrder) {
                // Get items untuk work order ini
                $workOrderItems = $this->models['work_order']->getItems($id);
                $workOrder['items'] = $workOrderItems;
                
                // Calculate totals
                $workOrder['total_quantity'] = 0;
                $workOrder['total_products'] = 0;
                foreach ($workOrderItems as $item) {
                    $workOrder['total_quantity'] += $item['quantity'];
                    $workOrder['total_products']++;
                }
                
                // Format dates
                $workOrder['start_formatted'] = $workOrder['start'] ? date('d M Y', strtotime($workOrder['start'])) : '-';
                $workOrder['end_formatted'] = $workOrder['end'] ? date('d M Y', strtotime($workOrder['end'])) : '-';
                $workOrder['release_date_formatted'] = $workOrder['release_date'] ? date('d M Y', strtotime($workOrder['release_date'])) : '-';
                $workOrder['manufacture_finishes_formatted'] = $workOrder['manufacture_finishes'] ? date('d M Y', strtotime($workOrder['manufacture_finishes'])) : '-';
                $workOrder['loading_date_formatted'] = $workOrder['loading_date'] ? date('d M Y', strtotime($workOrder['loading_date'])) : '-';
                
                $documents[] = $workOrder;
            }
        }

        if (empty($documents)) {
            return redirect()->back()->with('error', 'Tidak ada Work Order yang ditemukan');
        }

        $data = [
            'documents' => $documents,
            'title' => 'Multiple Work Orders',
            'print_date' => date('d F Y'),
            'total_documents' => count($documents)
        ];

        return view('print_document/print/multiple_work_orders', $data);
    }

    private function printMultipleProformas($ids)
    {
        $documents = [];
        
        foreach ($ids as $id) {
            $proforma = $this->models['proforma_invoice']
                            ->select('proforma_invoice.*, customer.customer_name, customer.address as customer_address, customer.contact_name, customer.contact_phone, customer.tax_number as customer_tax, currency.kode as currency_code, currency.nama as currency_name')
                            ->join('customer', 'customer.id = proforma_invoice.customer_id')
                            ->join('currency', 'currency.id = proforma_invoice.id_currency')
                            ->find($id);
            
            if ($proforma) {
                // Get items untuk proforma ini
                $proformaItems = $this->models['proforma_invoice']->getItems($id);
                $proforma['items'] = $proformaItems;
                
                // Calculate totals
                $proforma['subtotal'] = 0;
                $proforma['total_quantity'] = 0;
                foreach ($proformaItems as $item) {
                    $proforma['subtotal'] += $item['total_price'];
                    $proforma['total_quantity'] += $item['quantity'];
                }
                
                $documents[] = $proforma;
            }
        }

        if (empty($documents)) {
            return redirect()->back()->with('error', 'Tidak ada Proforma Invoice yang ditemukan');
        }

        $data = [
            'documents' => $documents,
            'title' => 'Multiple Proforma Invoices',
            'print_date' => date('d F Y'),
            'total_documents' => count($documents)
        ];

        return view('print_document/print/multiple_proformas', $data);
    }

    // ==================== GET OPTIONS FOR FILTERS ====================
    public function getFilterOptions($type)
    {
        switch ($type) {
            case 'suppliers':
                $supplierModel = new \App\Models\MdlSupplier();
                $data = $supplierModel->select('id, supplier_name as text')
                                    ->where('status', 1)
                                    ->orderBy('supplier_name', 'ASC')
                                    ->findAll();
                break;

            case 'customers':
                $customerModel = new \App\Models\MdlCustomer();
                $data = $customerModel->select('id, customer_name as text')
                                    ->where('status', 1)
                                    ->orderBy('customer_name', 'ASC')
                                    ->findAll();
                break;

            case 'departments':
                $departmentModel = new \App\Models\MdlDepartment();
                $data = $departmentModel->select('id, name as text')
                                      ->orderBy('name', 'ASC')
                                      ->findAll();
                break;

            case 'status':
                $data = [
                    ['value' => 'all', 'text' => 'Semua Status'],
                    ['value' => 'pending', 'text' => 'Pending'],
                    ['value' => 'approved', 'text' => 'Disetujui'],
                    ['value' => 'completed', 'text' => 'Selesai'],
                    ['value' => 'cancelled', 'text' => 'Dibatalkan']
                ];
                break;

            default:
                $data = [];
                break;
        }

        return $this->response->setJSON([
            'success' => true,
            'data' => $data
        ]);
    }
}