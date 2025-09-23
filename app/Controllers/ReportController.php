<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use Dompdf\Dompdf;
use Dompdf\Options;
class ReportController extends BaseController
{
    public function __construct()
    {
        $this->db = \Config\Database::connect();
        $this->session = session();
        helper(['form', 'url']);
        $this->form_validation = \Config\Services::validation();

    }
public function tracking($productId, $finishingId)
{
    // Load models
    $productModel = new \App\Models\MdlProduct();
    $proformaInvoiceModel = new \App\Models\ProformaInvoice();
    
    // Get product and finishing data
    $productData = $productModel
        ->select('product.*, finishing.name AS finishing_name, finishing.id AS finishing_id')
        ->join('finishing', 'product.id = finishing.id_product', 'left')
        ->where('product.id', $productId)
        ->where('finishing.id', $finishingId)
        ->first();

    // Get Proforma Invoice history (tanpa filter tanggal di awal)
    $piHistory = $proformaInvoiceModel
        ->select('
            proforma_invoice.id as invoice_id,
            proforma_invoice.invoice_number,
            proforma_invoice.invoice_date,
            proforma_invoice.etd,
            proforma_invoice.eta,
            proforma_invoice.loading_date,
            proforma_invoice.status,
            proforma_invoice_details.quantity,
            proforma_invoice_details.unit,
            proforma_invoice_details.unit_price,
            customer.customer_name
        ')
        ->join('proforma_invoice_details', 'proforma_invoice.id = proforma_invoice_details.invoice_id')
        ->join('customer', 'proforma_invoice.customer_id = customer.id')
        ->where('proforma_invoice_details.id_product', $productId)
        ->where('proforma_invoice_details.finishing_id', $finishingId)
        ->orderBy('proforma_invoice.invoice_date', 'DESC')
        ->findAll();
        
    $data['group'] = 'tracking';
    $data['title'] = 'Tracking Product - ' . ($productData['nama'].'|'.$productData['finishing_name']?? 'Unknown');
    $data['productId'] = $productId;
    $data['finishingId'] = $finishingId;
    $data['productData'] = $productData;
    $data['piHistory'] = $piHistory;
    
    $data['content'] = view('admin/content/report/trackProduct', $data);
    return view('admin/index', $data);
}

public function printBom($productId, $finishingId)
{
    // Load models
    $productModel = new \App\Models\MdlProduct();

    // Get product and finishing data
    $productData = $productModel
        ->select('product.*, finishing.name AS finishing_name, finishing.id AS finishing_id')
        ->join('finishing', 'product.id = finishing.id_product', 'left')
        ->where('product.id', $productId)
        ->where('finishing.id', $finishingId)
        ->first();

    // Get BOM data from billofmaterialfinishing
    $bomData = $productModel
        ->select('
            m.id AS material_id, 
            m.name AS material_name, 
            m.kode AS material_code, 
            FORMAT(SUM(DISTINCT COALESCE(bom.penggunaan, 0)), 3) AS penggunaan, 
            satuan.nama as satuan, 
            type.nama as type, 
            finishing.name AS finishing_name,
            materials_detail.kite as kite
        ')
        ->from('product p')
        ->join('billofmaterialfinishing bom', 'p.id = bom.id_product', 'left')
        ->join('materials m', 'bom.id_material = m.id')
        ->join('materials_detail', 'materials_detail.material_id = bom.id_material', 'left')
        ->join('satuan', 'satuan.id = materials_detail.satuan_id', 'left')
        ->join('type', 'type.id = materials_detail.type_id', 'left')
        ->join('finishing', 'bom.id_modul = finishing.id', 'left')
        ->where('p.id', $productId)
        ->where('finishing.id', $finishingId)
        ->groupBy('m.id, m.name, m.kode, satuan.nama, type.nama, finishing.name, materials_detail.kite')
        ->orderBy('finishing.id, p.nama')
        ->findAll();

    // Get BOM data from billofmaterial (backup)
    $bomDataBackup = $productModel
        ->select('
            m.id AS material_id, 
            m.name AS material_name, 
            m.kode AS material_code, 
            FORMAT(SUM(COALESCE(bom.penggunaan, 0)), 3) AS penggunaan,
            satuan.nama as satuan, 
            type.nama as type,
            modul.name AS modul_name,
            materials_detail.kite as kite
        ')
        ->from('product p', true)
        ->join('billofmaterial bom', 'p.id = bom.id_product', 'left')
        ->join('materials m', 'bom.id_material = m.id')
        ->join('materials_detail', 'materials_detail.material_id = bom.id_material', 'left')
        ->join('satuan', 'satuan.id = materials_detail.satuan_id', 'left')
        ->join('type', 'type.id = materials_detail.type_id', 'left')
        ->join('modul', 'bom.id_modul = modul.id', 'left')
        ->where('p.id', $productId)
        ->groupBy('m.id, m.name, m.kode, satuan.nama, type.nama, modul.name, materials_detail.kite')
        ->orderBy('modul.id, p.id')
        ->findAll();

    $data = [
        'productData' => $productData,
        'bomData' => !empty($bomData) ? $bomData : $bomDataBackup,
        'productId' => $productId,
        'finishingId' => $finishingId
    ];

    // Load view
    $html = view('admin/content/printBOM', $data);

    // Setup Dompdf
    $options = new Options();
    $options->set('isHtml5ParserEnabled', true);
    $options->set('isPhpEnabled', true);
    $options->set('defaultFont', 'Helvetica');
    
    $dompdf = new Dompdf($options);
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();

    // Output PDF
    $dompdf->stream("BOM_{$productId}_{$finishingId}.pdf", ["Attachment" => false]);
}

public function getPiHistoryByDate()
{
    $productId = $this->request->getGet('productId');
    $finishingId = $this->request->getGet('finishingId');
    $startDate = $this->request->getGet('startDate');
    $endDate = $this->request->getGet('endDate');

    $proformaInvoiceModel = new \App\Models\ProformaInvoice();

    $query = $proformaInvoiceModel
        ->select('
            proforma_invoice.id as invoice_id,
            proforma_invoice.invoice_number,
            proforma_invoice.invoice_date,
            proforma_invoice.etd,
            proforma_invoice.eta,
            proforma_invoice.loading_date,
            proforma_invoice.status,
            proforma_invoice_details.quantity,
            proforma_invoice_details.unit,
            proforma_invoice_details.unit_price,
            customer.customer_name
        ')
        ->join('proforma_invoice_details', 'proforma_invoice.id = proforma_invoice_details.invoice_id')
        ->join('customer', 'proforma_invoice.customer_id = customer.id')
        ->where('proforma_invoice_details.id_product', $productId)
        ->where('proforma_invoice_details.finishing_id', $finishingId);

    if (!empty($startDate)) {
        $query->where('proforma_invoice.invoice_date >=', $startDate);
    }

    if (!empty($endDate)) {
        $query->where('proforma_invoice.invoice_date <=', $endDate);
    }

    $query->orderBy('proforma_invoice.invoice_date', 'DESC');

    $piHistory = $query->findAll();

    return $this->response->setJSON([
        'status' => 'success',
        'data' => $piHistory
    ]);
}
    public function material()
    {

        $data['group'] = 'Material';
        $data['title'] = 'Material Report';

        $data['content'] = view('admin/content/report/material');
        return view('admin/index', $data);
    }
    public function finished_good()
    {
        $data['group'] = 'Finished Good';
        $data['title'] = 'Finished Good Report';

        $data['content'] = view('admin/content/report/finished_good');
        return view('admin/index', $data);

    }
    public function purchase()
    {
        $data['group'] = 'Purchase';
        $data['title'] = 'Purchase Report';

        $data['content'] = view('admin/content/report/purchase');
        return view('admin/index', $data);

    }
    public function customer_order()
    {
        $data['group'] = 'Customer Order';
        $data['title'] = 'Customer Order Report';

        $data['content'] = view('admin/content/report/customer_order');
        return view('admin/index', $data);

    }
    public function activity()
    {
        $data['group'] = 'Activity';
        $data['title'] = 'Activity Report';

        $data['content'] = view('admin/content/report/activity');
        return view('admin/index', $data);

    }
    public function materialStockCard()
    {
        $data['opening_balance'] = $this->openingBalance($_POST);
        $data['balance_before'] = $this->balanceBefore($_POST);
        $data['destruction'] = $this->materialDestruction($_POST);
        $data['pembelian'] = $this->materialPurchase($_POST);
        $data['return'] = $this->materialReturn($_POST);
        $data['stock_opname'] = $this->materialStockOpname($_POST);
        $data['material_requisition'] = $this->materialRequisition($_POST);
        $data['merge'] = $this->sortByDate(array_merge($data['opening_balance'], $data['pembelian'], $data['material_requisition'], $data['return'], $data['destruction'], $data['stock_opname']));
        return json_encode($data);
    }
    function sortByDate($data)
    {
        usort($data, function ($a, $b) {
            return strtotime($a['created_at']) - strtotime($b['created_at']);
        });
        return $data;
    }
    public function openingBalance($params)
    {
        $mdl = new \App\Models\MdlStock();

        $materialId = $params['material_id'];
        $startDate = $params['start_date']; // Assuming the date is sent as 'start_date'
        $endDate = $params['end_date'];

        $query = $mdl->select('"Opening Balance" as source,
        "IN" as desc,
        "default stock" as activity,
        ROUND(stock.stock_awal,2) as jumlah, 
        stock.created_at as created_at, 
        materials.name as materials_name, 
        materials.kode as materials_code,
        satuan.kode as satuan,
        materials_detail.hscode
        ')
            ->join('materials', 'materials.id = stock.id_material')
            ->join('materials_detail', 'materials_detail.material_id = materials.id', 'left')
            ->join('satuan', 'materials_detail.satuan_id = satuan.id', 'left')
            ->where('id_material', $materialId);

        if (!empty($startDate) && !empty($endDate)) {
            $query->where('stock.created_at >=', $startDate)
                ->where('stock.created_at <=', $endDate);
        }
        $data = $query->findAll();


        // Return the data as JSON or load a view as needed
        return $data;

    }
    public function materialReturn($params)
    {
        $mdl = new \App\Models\MdlMaterialReturnList();

        // Get the material ID and date range from the POST request
        $materialId = $params['material_id'];
        $startDate = $params['start_date']; // Assuming the date is sent as 'start_date'
        $endDate = $params['end_date']; // Assuming the date is sent as 'end_date'

        // Initialize the query
        $query = $mdl->select(' 
        "Material Return" as source,
        "IN" as desc, 
        CONCAT("Reference code:(", material_return.code , ") - ", material_return.remarks) as activity,
        ROUND(material_return_list.jumlah, 2) as jumlah, 
        material_return_list.created_at as created_at, 
        materials.name as materials_name, 
        satuan.kode as satuan,
        materials_detail.hscode,
        materials.kode as materials_code, ') // Select fields from both tables
            ->join('materials', 'materials.id = material_return_list.id_material') // Join with materials table
            ->join('materials_detail', 'materials_detail.material_id = materials.id', 'left')
            ->join('satuan', 'materials_detail.satuan_id = satuan.id', 'left')
            ->join('material_return', 'material_return.id = material_return_list.id_material_return') // Join with materials table
            ->where('material_return_list.id_material', $materialId)
            ->where('material_return.status', 1);

        // Add date range conditions if provided
        if (!empty($startDate) && !empty($endDate)) {
            $query->where('material_return_list.created_at >=', $startDate)
                ->where('material_return_list.created_at <=', $endDate);
        }

        // Fetch the purchase details
        $data = $query->findAll();


        // Return the data as JSON or load a view as needed
        return $data;
    }
    public function materialRequisition($params)
    {
        $mdl = new \App\Models\MdlMaterialRequisitionProgress();

        // Get the material ID and date range from the POST request
        $materialId = $params['material_id'];
        $startDate = $params['start_date']; // Assuming the date is sent as 'start_date'
        $endDate = $params['end_date']; // Assuming the date is sent as 'end_date'

        // Initialize the query
        $query = $mdl->select(' 
        "Material Requisition" as source,
        "OUT" as desc, 
        CONCAT("Reference code:(", material_requisition.code , ") - ", material_requisition.remarks) as activity,
        -(ROUND(material_requisition_progress.jumlah,2)) as jumlah, 
        material_requisition_progress.created_at as created_at, 
        materials.name as materials_name, 
        satuan.kode as satuan,
        materials_detail.hscode,
        materials.kode as materials_code, ') // Select fields from both tables
            ->join('materials', 'materials.id = material_requisition_progress.id_material') // Join with materials table
            ->join('materials_detail', 'materials_detail.material_id = materials.id', 'left')
            ->join('satuan', 'materials_detail.satuan_id = satuan.id', 'left')
            ->join('material_requisition_list', 'material_requisition_list.id = material_requisition_progress.id_material_requisition_list') // Join with materials table
            ->join('material_requisition', 'material_requisition.id = material_requisition_list.id_material_requisition') // Join with materials table
            ->where('material_requisition.status', 1)
            ->where('material_requisition_progress.id_material', $materialId);

        // Add date range conditions if provided
        if (!empty($startDate) && !empty($endDate)) {
            $query->where('material_requisition_progress.created_at >=', $startDate)
                ->where('material_requisition_progress.created_at <=', $endDate);
        }

        // Fetch the purchase details
        $data = $query->findAll();


        // Return the data as JSON or load a view as needed
        return $data;
    }
    public function materialPurchase($params)
    {
        $mdl = new \App\Models\MdlPembelianDetail();

        // Get the material ID and date range from the POST request
        $materialId = $params['material_id'];
        $startDate = $params['start_date']; // Assuming the date is sent as 'start_date'
        $endDate = $params['end_date']; // Assuming the date is sent as 'end_date'

        // Initialize the query
        $query = $mdl->select(' 
        "Purchasing" as source,
        "IN" as desc, 
        CONCAT("Reference code:(", pembelian.invoice , ") - ", pembelian.remarks) as activity,
        ROUND(pembelian_detail.jumlah, 2) as jumlah, 
        pembelian_detail.created_at as created_at, 
        materials.name as materials_name, 
        satuan.kode as satuan,
        materials_detail.hscode,
        materials.kode as materials_code, ') // Select fields from both tables
            ->join('materials', 'materials.id = pembelian_detail.id_material') // Join with materials table
            ->join('materials_detail', 'materials_detail.material_id = materials.id', 'left')
            ->join('satuan', 'materials_detail.satuan_id = satuan.id', 'left')
            ->join('pembelian', 'pembelian.id = pembelian_detail.id_pembelian') // Join with materials table
            ->where('pembelian.posting', 1)
            ->where('pembelian_detail.id_material', $materialId);

        // Add date range conditions if provided
        if (!empty($startDate) && !empty($endDate)) {
            $query->where('pembelian_detail.created_at >=', $startDate)
                ->where('pembelian_detail.created_at <=', $endDate);
        }

        // Fetch the purchase details
        $data = $query->findAll();


        // Return the data as JSON or load a view as needed
        return $data;
    }
    public function materialDestruction($params)
    {
        $mdl = new \App\Models\MdlMaterialDestructionList();

        // Get the material ID and date range from the POST request
        $materialId = $params['material_id'];
        $startDate = $params['start_date']; // Assuming the date is sent as 'start_date'
        $endDate = $params['end_date']; // Assuming the date is sent as 'end_date'

        // Initialize the query
        $query = $mdl->select(' 
        "Material Destruction" as source,
        "OUT" as desc, 
        CONCAT("Reference code:(", material_destruction.code , ") - ", material_destruction.remarks) as activity,
        -(ROUND(material_destruction_list.jumlah,2)) as jumlah, 
        material_destruction_list.created_at as created_at, 
        materials.name as materials_name, 
        satuan.kode as satuan,
        materials_detail.hscode,
        materials.kode as materials_code, ') // Select fields from both tables
            ->join('materials', 'materials.id = material_destruction_list.id_material') // Join with materials table
            ->join('material_destruction', 'material_destruction.id = material_destruction_list.id_material_destruction') // Join with materials table
            ->join('materials_detail', 'materials_detail.material_id = materials.id', 'left')
            ->join('satuan', 'materials_detail.satuan_id = satuan.id', 'left')
            ->where('material_destruction.status', 1)
            ->where('material_destruction_list.id_material', $materialId);

        // Add date range conditions if provided
        if (!empty($startDate) && !empty($endDate)) {
            $query->where('material_destruction_list.created_at >=', $startDate)
                ->where('material_destruction_list.created_at <=', $endDate);
        }

        // Fetch the purchase details
        $data = $query->findAll();


        // Return the data as JSON or load a view as needed
        return $data;
    }
    public function materialStockOpname($params)
    {
        $mdl = new \App\Models\MdlStockOpnameList();

        // Get the material ID and date range from the POST request
        $materialId = $params['material_id'];
        $startDate = $params['start_date']; // Assuming the date is sent as 'start_date'
        $endDate = $params['end_date']; // Assuming the date is sent as 'end_date'

        // Initialize the query
        $query = $mdl->select(' 
        "Material Stock Opname" as source,
        "SO" as desc,
        CONCAT("Reference code:(", stock_opname.code , ") - ", stock_opname.remarks) as activity,
        ROUND((stock_opname_list.jumlah_akhir - stock_opname_list.jumlah_awal),2) as jumlah, 
        stock_opname_list.created_at as created_at, 
        materials.name as materials_name, 
         satuan.kode as satuan,
         materials_detail.hscode,
        materials.kode as materials_code, ') // Select fields from both tables
            ->join('materials', 'materials.id = stock_opname_list.id_material')
            ->join('materials_detail', 'materials_detail.material_id = materials.id', 'left')
            ->join('satuan', 'materials_detail.satuan_id = satuan.id', 'left')
            ->join('stock_opname', 'stock_opname.id = stock_opname_list.id_stock_opname')
            ->where('stock_opname.status', 1)
            ->where('stock_opname_list.id_material', value: $materialId);

        // Add date range conditions if provided
        if (!empty($startDate) && !empty($endDate)) {
            $query->where('stock_opname_list.created_at >=', $startDate)
                ->where('stock_opname_list.created_at <=', $endDate);
        }

        // Fetch the purchase details
        $data = $query->findAll();


        // Return the data as JSON or load a view as needed
        return $data;
    }
    public function balanceBefore($params)
    {
        // $startDate = $params['start_date'];
        $materialId = $params['material_id'];
        $endDate = $params['start_date'];

        // Set startDate to 1970-01-01 00:00:00
        $startDate = '1970-01-01 00:00:00';

        // Convert endDate to DateTime object, subtract one day, and keep the time
        $endDateObj = new \DateTime($endDate);
        $endDateObj->modify('-1 second');
        $endDate = $endDateObj->format('Y-m-d H:i:s');
        // var_dump($endDate);
        // Prepare data for passing to other methods
        $data['material_id'] = $materialId;
        $data['start_date'] = $startDate;
        $data['end_date'] = $endDate;

        // Get material destruction, purchase, return, stock opname, and requisition
        $stock_awal = $this->openingBalance($data);
        $destruction = $this->materialDestruction($data);
        $pembelian = $this->materialPurchase($data);
        $return = $this->materialReturn($data);
        $stock_opname = $this->materialStockOpname($data);
        $material_requisition = $this->materialRequisition($data);
        // var_dump($material_requisition);
        // Calculate the total balance by summing 'jumlah' from each result
        $totalBalance = 0;

        // Sum the 'jumlah' values from each result, ensure we handle empty or non-array values gracefully
        $totalBalance += $this->sumJumlah($stock_awal);
        $totalBalance += $this->sumJumlah($destruction);
        $totalBalance += $this->sumJumlah($pembelian);
        $totalBalance += $this->sumJumlah($return);
        $totalBalance += $this->sumJumlah($stock_opname);
        $totalBalance += $this->sumJumlah($material_requisition);

        return $totalBalance;
    }

    /**
     * Helper function to sum the 'jumlah' field from the query results.
     */
    private function sumJumlah($data)
    {
        $sum = 0;

        // Check if $data is an array and has 'jumlah' field in each entry
        if (is_array($data) && !empty($data)) {
            foreach ($data as $item) {
                if (isset($item['jumlah'])) {
                    $sum += $item['jumlah'];  // Add the value of 'jumlah'
                }
            }
        }

        return $sum;
    }
    function getHeader()
    {
        $userInfo = $_SESSION['auth'];
        // Get the ID from the AJAX request
        $id = $this->request->getGet('id');

        // Load the model to fetch material data
        $materialModel = new \App\Models\MdlMaterial();

        // Fetch material data based on the ID

        $materialData = $materialModel->select('materials.kode as kode, materials.name as name,satuan.kode as satuan_kode, satuan.nama as satuan_nama ')
            ->join('materials_detail', 'materials_detail.material_id = materials.id', 'left')
            ->join('satuan', 'materials_detail.satuan_id = satuan.id', 'left')
            ->where('materials.id', $id)->find();


        // If material data exists, return it as JSON
        $data = $materialData[0];
        if ($data) {
            return $this->response->setJSON([
                'status' => 'success',
                'data' => [
                    'code' => $data['kode'],
                    'name' => $data['name'],
                    'admin' => $userInfo['nama_depan'] . " " . $userInfo['nama_belakang'],
                    'satuan' => $data['satuan_nama'] . " (" . $data['satuan_kode'] . ")"
                ]
            ]);
        } else {
            // Return an error if the material data is not found
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Material not found'
            ]);
        }
    }
    function getHeaderScrap()
    {
        $userInfo = $_SESSION['auth'];
        // Get the ID from the AJAX request
        $id = $this->request->getGet('id');

        // Load the model to fetch material data
        $Model = new \App\Models\MdlScrapDoc();

        // Fetch material data based on the ID

        $materialData = $Model->select('* ')
            ->where('scrap_doc.id_wo', $id)->find();


        // If material data exists, return it as JSON
        $data = $materialData[0];
        if ($data) {
            return $this->response->setJSON([
                'status' => 'success',
                'data' => [
                    'code' => $data['code'],
                ]
            ]);
        } else {
            // Return an error if the material data is not found
            return $this->response->setJSON([
                'status' => 'error',
                'message' => ' not found'
            ]);
        }
    }
    public function materialScrap()
    {

        $idWO = $_POST['woId'];
        $endDate = $_POST['end_date'];
        $startDate = $_POST['start_date'];

        // $MdlDoc = new \App\Models\MdlScrapDoc();
        $mdl = new \App\Models\MdlScrap();
        $query = $mdl->select('scrap.*,materials_detail.hscode, scrap_doc.code sc, work_order.kode as wo, proforma_invoice.invoice_number as pi, materials.kode as material_code, materials.name as material_name, satuan.kode as satuan_kode, satuan.nama as satuan_nama') // Select fields from both tables
            ->join('scrap_doc', 'scrap_doc.id = scrap.scrap_doc_id')
            ->join('work_order', 'work_order.id = scrap_doc.id_wo')
            ->join('proforma_invoice', 'proforma_invoice.id = work_order.invoice_id')
            ->join('materials', 'materials.id = scrap.material_id')
            ->join('materials_detail', 'materials_detail.material_id = materials.id', 'left')
            ->join('satuan', 'materials_detail.satuan_id = satuan.id', 'left')
            ->where('scrap_doc.status', 1)
            ->where('scrap_doc.id_wo', $idWO);

        // Add date range conditions if provided
        if (!empty($startDate) && !empty($endDate)) {
            $query->where('scrap_doc.created_at >=', $startDate)
                ->where('scrap_doc.created_at <=', $endDate);
        }

        // Fetch the purchase details
        $data = $query->findAll();

        // Return the data as JSON or load a view as needed
        return json_encode($data);
    }
    public function productionReport()
    {
        $data['prod'] = $this->getProductionReport($_POST);
        $data['wh'] = $this->getWHreport($_POST);
        return json_encode($data);
    }
    public function productionReportPIByProduct()
    {
        $data['prod'] = $this->getProductionReportPIByProduct($_POST);
        $data['wh'] = $this->getWHreportPIByProduct(params: $_POST);
        return json_encode($data);
    }
    function getWHreport($params)
    {
        $idWO = $params['woId'];
        $endDate = $params['end_date'];
        $startDate = $params['start_date'];
        $mdl = new \App\Models\MdlProductionProgress();

        $queryWh = $mdl->select('production_progress.created_at,work_order.kode as wo,
    product.kode as product_code,
    product.nama as product_name, 
    product.hs_code as hs_code, 
    warehouses.name as production_area_name,
    production_progress.quantity as quantity 
') // Select fields from both tables
            ->join('work_order', 'work_order.id = production_progress.wo_id')
            ->join('product', 'product.id = production_progress.product_id')
            ->join('warehouses', 'warehouses.id = production_progress.warehouse_id');
        if (!empty($startDate) && !empty($endDate)) {
            $queryWh->where('production_progress.created_at >=', $startDate)
                ->where('production_progress.created_at <=', $endDate)
                ->where('quantity !=', 0);
        }
        if (!empty($idWO)) {
            $queryWh->where('production_progress.wo_id =', $idWO);
        }
        return $queryWh->findAll();
    }
    function getProductionReportPIByProduct($params)
    {
        $prodId = $params['prodId'];
        $piId = $params['piId'];
        $mdl = new \App\Models\MdlProductionProgress();

        $queryProd = $mdl->select('production_progress.created_at, work_order.kode as wo,
                                         product.kode as product_code,
                                         product.nama as product_name, 
                                         product.hs_code as hs_code, 
                                         production_area.name as production_area_name,
                                         production_progress.quantity as quantity
                                    ') // Select fields from both tables
            ->join('work_order', 'work_order.id = production_progress.wo_id')
            ->join('proforma_invoice', 'proforma_invoice.id = work_order.invoice_id')
            ->join('product', 'product.id = production_progress.product_id')
            ->join('production_area', 'production_area.id = production_progress.production_id');
        $queryProd->where('production_progress.product_id =', $prodId);

        if (!empty($prodId)) {
            $queryProd->where('proforma_invoice.id =', $piId)->where('quantity !=', 0);
            ;
        }




        return $queryProd->findAll();

    }

    function getWHreportPIByProduct($params)
    {
        $prodId = $params['prodId'];
        $piId = $params['piId'];
        $mdl = new \App\Models\MdlProductionProgress();

        $queryWh = $mdl->select('production_progress.created_at,work_order.kode as wo,
    product.kode as product_code,
    product.nama as product_name, 
    product.hs_code as hs_code, 
    warehouses.name as production_area_name,
    production_progress.quantity as quantity 
') // Select fields from both tables
            ->join('work_order', 'work_order.id = production_progress.wo_id')
            ->join('proforma_invoice', 'proforma_invoice.id = work_order.invoice_id')
            ->join('product', 'product.id = production_progress.product_id')
            ->join('warehouses', 'warehouses.id = production_progress.warehouse_id');
        $queryWh->where('production_progress.product_id =', $prodId);

        if (!empty($prodId)) {
            $queryWh->where('work_order.invoice_id =', $piId)->where('quantity !=', 0);
            ;

        }
        return $queryWh->findAll();
    }
    function getProductionReport($params)
    {
        $idWO = $params['woId'];
        $endDate = $params['end_date'];
        $startDate = $params['start_date'];
        $mdl = new \App\Models\MdlProductionProgress();

        $queryProd = $mdl->select('production_progress.created_at, work_order.kode as wo,
                                         product.kode as product_code,
                                         product.nama as product_name, 
                                         product.hs_code as hs_code, 
                                         production_area.name as production_area_name,
                                         production_progress.quantity as quantity
                                    ') // Select fields from both tables
            ->join('work_order', 'work_order.id = production_progress.wo_id')
            ->join('product', 'product.id = production_progress.product_id')
            ->join('production_area', 'production_area.id = production_progress.production_id');
        if (!empty($startDate) && !empty($endDate)) {
            $queryProd->where('production_progress.created_at >=', $startDate)
                ->where('production_progress.created_at <=', $endDate)
                ->where('quantity !=', 0);
        }
        if (!empty($idWO)) {
            $queryProd->where('production_progress.wo_id =', $idWO);
        }




        return $queryProd->findAll();

    }

    public function stockMovementReport()
    {
        $endDate = $_POST['end_date'];
        $startDate = $_POST['start_date'];
        $mdl = new \App\Models\MdlStockMove();
        $query = $mdl->select(' 
        stock_movements.created_at,
                work_order.kode AS wo_code, 
                stock_movements.id,  
                stock_movements.wo_id,  
                stock_movements.product_id,  
                stock_movements.stock_change,  
                stock_movements.created_at,  
                stock_movements.updated_at,  
                stock_movements.stock_change,
                product.kode,  
                product.hs_code,  
                product.nama,  
                production_area_asal.name AS production_area_asal_name,  
                production_area_tujuan.name AS production_area_tujuan_name,  
                warehouses_asal.name AS warehouse_asal_name,  
                warehouses_tujuan.name AS warehouse_tujuan_name  
            ')
            ->join('work_order', 'work_order.id = stock_movements.wo_id', 'left')
            ->join('product', 'product.id = stock_movements.product_id', 'left')
            ->join('production_area AS production_area_asal', 'production_area_asal.id = stock_movements.prod_id_asal', 'left')
            ->join('production_area AS production_area_tujuan', 'production_area_tujuan.id = stock_movements.prod_id_tujuan', 'left')
            ->join('warehouses AS warehouses_asal', 'warehouses_asal.id = stock_movements.wh_id_asal', 'left')
            ->join('warehouses AS warehouses_tujuan', 'warehouses_tujuan.id = stock_movements.wh_id_tujuan', 'left');

        if (!empty($startDate) && !empty($endDate)) {
            $query->where('stock_movements.created_at >=', $startDate)
                ->where('stock_movements.created_at <=', $endDate);
        }
        return json_encode($query->findAll());
    }
    public function searchProduct()
    {
        $model = new \App\Models\ProformaInvoice();

        // Ambil parameter dari request
        $id_product = $this->request->getGet('id_product');
        $start_date = $this->request->getGet('start_date');
        $end_date = $this->request->getGet('end_date');
        $status = $this->request->getGet('status'); // null atau 1
        $loading_date_filled = $this->request->getGet('loading_date_filled'); // true atau false

        // Panggil fungsi searchInvoices di model
        $results = $model->searchInvoices($id_product, $start_date, $end_date, $status, $loading_date_filled);

        // Tampilkan hasil
        return json_encode($results);
        // return view('invoice_search_results', ['results' => $results]);
    }
    public function finishedGoodReport()
    {
        $startDate = $this->request->getGet('startDate');
        $endDate = $this->request->getGet('endDate');
        $productId = $this->request->getGet('productId');
        $role = $this->request->getGet('role');
        // $startDate = $this->request->getGet('startDate');
        // $endDate = $this->request->getGet('endDate');
        // $productId = $this->request->getGet('productId');
        // $role = $this->request->getGet('role');
        $mdl = new \App\Models\MdlProductionProgress();
        $query = $mdl->select('production_progress.*,
        proforma_invoice.status_delivery as status_delivery,
        proforma_invoice.peb as peb,
        proforma_invoice.tgl_peb as tgl_peb,
        customer.customer_name as customer_name,
        customer.state as state,
        currency.kode as currency_code,
        currency.nama as currency_name,
         product.kode as product_code,
         product.nama as product_name,
         product.hs_code as hs_code, 
         production_area.name as production_area_name, 
         warehouses.name as warehouse_name, work_order.kode as wo_code, proforma_invoice.invoice_number as pi_number')
            ->join('product', 'product.id = production_progress.product_id', 'left')
            ->join('production_area', 'production_area.id = production_progress.production_id', 'left')
            ->join('warehouses', 'warehouses.id = production_progress.warehouse_id','left')
            ->join('work_order', 'work_order.id = production_progress.wo_id', 'left')
            ->join('proforma_invoice', 'proforma_invoice.id = work_order.invoice_id', 'left')
            ->join('product_details', 'product.id = product_details.id_product', 'left')
            ->join('customer', 'customer.id = proforma_invoice.customer_id', 'left')
            ->join('currency', 'currency.id = customer.id_currency', 'left');
            $query->where('production_progress.quantity !=', 0);
            
            if (isset($productId)) {
                $query->where('production_progress.product_id', $productId);
            }
        if (isset($endDate)) {
            $query->where('production_progress.created_at <', $endDate);
        }
        if (isset($startDate)) {
            $query->where('production_progress.created_at >', $startDate);
        }
        

        if ($role == 'production') {
            $query->where('production_progress.warehouse_id', 0);
        } else if ($role == 'warehouse') {

            $query->where('production_progress.production_id', 0);
        }else if ($role == 'delivery'){
            $query->where('proforma_invoice.status_delivery !=', 0);
            
        }
        
        $data['role'] = $role;
        $data['startDate'] = $startDate;
        $data['endDate'] = $endDate;
        $data['product'] = $query->get()->getResultArray();
        // $data['role'] = json_encode( $this->db->getLastQuery()->getQuery());

        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isPhpEnabled', true);
        $options->set('defaultFont', 'Helvetica');
        $options->set('isRemoteEnabled', true); 
        $dompdf = new Dompdf($options);
        
        
        
            // Data untuk tampilan
            $data['title'] = 'Proforma Invoice';
            $html = view('admin/content/printProduct', $data);
        
            // Load HTML ke Dompdf
            $dompdf->loadHtml($html);
        
            // Atur ukuran kertas A4 dan orientasi landscape
            $dompdf->setPaper('A4', 'landscape');
        
            // Render PDF
            $dompdf->render();
        
            // Output PDF ke browser tanpa mengunduh otomatis
            $dompdf->stream("product_{$productId}_{$role}.pdf", ["Attachment" => false]);
        // return json_encode( $this->db->getLastQuery()->getQuery());
    }
    public function beacukai(){
        $data['group'] = 'Beacukai';
        $data['title'] = 'Beacukai Report';

        $data['content'] = view('admin/content/report/beacukai');
        return view('admin/index', $data);
    }
}