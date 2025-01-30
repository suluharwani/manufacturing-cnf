<?php

namespace App\Controllers;
use AllowDynamicProperties;
use App\Controllers\WarehouseController;
use Bcrypt\Bcrypt;

class Home extends BaseController
{
    protected $bcrypt;
    protected $userValidation;
    protected $bcrypt_version;
    protected $session;
    protected $db;
    protected $uri;
    protected $form_validation;
    public function __construct()
    {
        $this->request = \Config\Services::request();
        $this->db = \Config\Database::connect();
        $this->session = session();
        $this->bcrypt = new Bcrypt();
        $this->bcrypt_version = '2a';
        $this->uri = service('uri');
        helper('form');
        $this->form_validation = \Config\Services::validation();
        $this->userValidation = new \App\Controllers\LoginValidation();


    }
    public function index(): string
    {
        // $this->access('operator');
        $d = new WarehouseController();
        $data['group'] = 'Admin';
        $data['title'] = 'Dashboard';
        $data['content'] = view('admin/content/dashboard');
        return view( 'admin/index', $data);
    }
    function col($table)
    {
        $query = $this->db->query("SELECT * FROM $table");

    foreach ($query->getFieldNames() as $field) {
      echo '"'.$field.'",';
    }
    }
    function forbidden(){
        return view('403');
    }
    function access($page)
    {
        $check = new \App\Controllers\CheckAccess();
        $check->access($_SESSION['auth']['id'], $page);
    }
    public function material()
    {
        $data['group'] = 'Material';
        $data['title'] = 'All Material';

        $data['content'] = view('admin/content/material');
        return view('admin/index', $data);
    }
    public function warehouse()
    {
        $data['group'] = 'Warehouse';
        $data['title'] = 'All Warehouse';

        $data['content'] = view('admin/content/warehouse');
        return view('admin/index', $data);
    }
    public function stock()
    {
        $data['group'] = 'Warehouse';
        $data['title'] = 'Warehouse Stock';
        $data['content'] = view('admin/content/warehouse_stock');
        return view('admin/index', $data);
    }
    public function production()
    {
        $data['group'] = 'Production';
        $data['title'] = 'All Production';
        $data['content'] = view('admin/content/production');
        return view('admin/index', $data);

    }
    public function design()
    {
        $data['group'] = 'Production';
        $data['title'] = 'Design';
        $data['content'] = view('admin/content/design');
        return view('admin/index', $data);

    }
    public function scrap()
    {
        $data['group'] = 'Production';
        $data['title'] = 'Scrap';
        $data['content'] = view('admin/content/scrap');
        return view('admin/index', $data);

    }
    public function product()
    {
        $data['group'] = 'Material';
        $data['title'] = 'Finished Goods';
        $data['content'] = view('admin/content/product');
        return view('admin/index', $data);

    }
    public function track_material()
    {
        $data['group'] = 'Material';
        $data['title'] = 'Track Material';
        $data['content'] = view('admin/content/track_material');
        return view('admin/index', $data);
    }
    public function scrap_management()
    {
        $data['group'] = 'Material';
        $data['title'] = 'Scrap Management';
        $data['content'] = view('admin/content/scrap_management');
        return view('admin/index', $data);
    }
    public function warehouse_report()
    {
        $data['group'] = 'Warehouse';
        $data['title'] = 'Report';
        $data['content'] = view('admin/content/warehouse_report');
        return view('admin/index', $data);
    }
    public function chagelog()
    {
        $data = [
            'group' => 'Warehouse',
            'title' => 'Warehouse Report',
            'content' => view('admin/content/chagelog'),
        ];

        return view('admin/index', $data);
    }
    public function purchase_order()
    {
        $data['group'] = 'Purchase';
        $data['title'] = 'All Purchase Order';
        $data['content'] = view('admin/content/purchase_order');
        return view('admin/index', $data);
    }
    public function work_order()
    {

        $data['group'] = 'Customer Order';
        $data['title'] = 'All Work Order';
        $data['content'] = view('admin/content/work_order');
        return view('admin/index', $data);
    }
    public function track_work_order()
    {
        $data['group'] = 'Customer Order';
        $data['title'] = 'Track Work Order';
        $data['content'] = view('admin/content/track_work_order');
        return view('admin/index', $data);
    }
    public function review_scrap_report()
    {
        $data['group'] = 'Scrap';
        $data['title'] = 'Review Scrap Report';
        $data['content'] = view('admin/content/review_scrap_report');
        return view('admin/index', $data);
    }
    public function user()
    {
        $data['group'] = 'User';
        $data['title'] = 'User';
        $data['content'] = view('admin/content/user');
        return view('admin/index', $data);
    }
    public function role()
    {
        $data['group'] = 'User';
        $data['title'] = 'Role';
        $data['content'] = view('admin/content/role');
        return view('admin/index', $data);
    }
    public function activitylog()
    {
        $data['group'] = 'User';
        $data['title'] = 'Activity log';
        $data['content'] = view('admin/content/activity_log');
        return view('admin/index', $data);
    }
    public function track_purchase_delivery()
    {
        $data['group'] = 'Purchase';
        $data['title'] = 'Track Purchase Delivery';
        $data['content'] = view('admin/content/track_purchase_delivery');
        return view('admin/index', $data);
    }
        public function employee()
    {
        $data['group'] = 'Employee';
        $data['title'] = 'Employee';
        $data['content'] = view('admin/content/employee');
        return view('admin/index', $data);
    }
        public function salarySetting()
    {
        $data['group'] = 'Salary Setting';
        $data['title'] = 'Salary Setting';
        $data['content'] = view('admin/content/salarySetting');
        return view('admin/index', $data);
    }
     public function masterSalary()
    {
        $data['group'] = 'Master Salary';
        $data['title'] = 'Master Salary';
        $data['content'] = view('admin/content/master_salary');
        return view('admin/index', $data);
    }
       public function detailSalary()
    {
        $data['group'] = 'Detail Salary';
        $data['title'] = 'Detail Salary';
        $data['content'] = view('admin/content/detail_salary');
        return view('admin/index', $data);
    }
        public function pembelian()
    {
        $data['group'] = 'Goods Received Note';
        $data['title'] = 'Goods Received Note';
        $data['content'] = view('admin/content/daftarPembelian');
        return view('admin/index', $data);
    }
          public function supplier()
    {
        $data['group'] = 'Master Data';
        $data['title'] = 'Supplier';
        $data['content'] = view('admin/content/supplier');
        return view('admin/index', $data);
    }
         public function customer()
    {
        $data['group'] = 'Master Data';
        $data['title'] = 'Customer';
        $data['content'] = view('admin/content/customer');
        return view('admin/index', $data);
    }
         public function proformainvoice()
    {
        $data['group'] = 'Customer Order';
        $data['title'] = 'Proforma Invoice';
        $data['content'] = view('admin/content/proformaInvoice');
        return view('admin/index', $data);
    }
    public function finishing(){
        $data['group'] = 'Master Data';
        $data['title'] = 'Finishing Product';
        $data['content'] = view('admin/content/finishing');
        return view('admin/index', $data);
    } 
    public function productionArea(){
        $data['group'] = 'Production Area';
        $data['title'] = 'Production Area';
        $data['content'] = view('admin/content/productionArea');
        return view('admin/index', $data);
    }
    public function materialRequest(){
        $data['group'] = 'Material Request';
        $data['title'] = 'Material Request';
        $data['content'] = view('admin/content/material_request');
        return view('admin/index', $data);
    }
    public function department(){
        $data['group'] = 'Department';
        $data['title'] = 'Department';
        $data['content'] = view('admin/content/department');
        return view('admin/index', $data);
    }
    public function material_requisition(){
        $data['group'] = 'Material Requisition';
        $data['title'] = 'Material Requisition';
        $data['content'] = view('admin/content/material_requisition');
        return view('admin/index', $data);
    }
    public function material_requisition_list(){
        $data['group'] = 'Material Requisition';
        $data['title'] = 'Material Requisition List';
        $data['content'] = view('admin/content/material_requisition_list');
        return view('admin/index', $data);
    }
    public function material_requisition_process(){
        $data['group'] = 'Material Requisition';
        $data['title'] = 'Material Requisition Process';
        $data['content'] = view('admin/content/material_requisition_process');
        return view('admin/index', $data);
    }
    public function material_return(){
        $data['group'] = 'Material Return';
        $data['title'] = 'Material Return';
        $data['content'] = view('admin/content/material_return');
        return view('admin/index', $data);
    }
    public function inventory_destruction(){
        $data['group'] = 'Inventory Destruction';
        $data['title'] = 'Inventory Destruction';
        $data['content'] = view('admin/content/material_destruction');
        return view('admin/index', $data);
    }
    public function material_requisition_progress(){
        $data['group'] = 'Material Requisition Progress';
        $data['title'] = 'Material Requisition Progress';
        $data['content'] = view('admin/content/material_requisition_progress');
        return view('admin/index', $data);
    }
    public function check_hs_code() {
        $term = $_GET['term'];

        if (empty($term)) {
            echo json_encode(['status' => 'error', 'message' => 'HS Code term is required.']);
            return;
        }

        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => "https://hs-code-harmonized-system.p.rapidapi.com/code?term=" . urlencode($term),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => [
                "x-rapidapi-host: hs-code-harmonized-system.p.rapidapi.com",
                "x-rapidapi-key: fd47581e71mshaf2d7feb7752fa7p1920a9jsn96228f76aa27"
            ],
        ]);

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            echo json_encode(['status' => 'error', 'message' => 'cURL Error #' . $err]);
        } else {
            echo $response;
        }
    }
}
