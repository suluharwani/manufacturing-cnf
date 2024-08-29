<?php

namespace App\Controllers;

use App\Models\WarehouseModel;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
class WarehouseController extends BaseController
{
    protected $warehouseModel;

    public function __construct()
    {
        $this->warehouseModel = new WarehouseModel();
    }

    // Menampilkan semua gudang
    public function index()
    {
        $data['warehouses'] = $this->warehouseModel->findAll();
        return view('warehouse/index', $data);
    }

    // Menampilkan form untuk menambahkan gudang baru
    public function create()
    {
        return view('warehouse/create');
    }

    // Menyimpan gudang baru ke database
    public function store()
    {
        $data = [
            'name' => $this->request->getPost('name'),
            'location' => $this->request->getPost('location'),
            'capacity' => $this->request->getPost('capacity'),
        ];
        
        $this->warehouseModel->insert($data);
        return redirect()->to('/warehouse');
    }

    // Menampilkan detail gudang berdasarkan ID
    public function show($id)
    {
        $data['warehouse'] = $this->warehouseModel->find($id);
        return view('warehouse/show', $data);
    }

    // Menampilkan form untuk mengedit gudang
    public function edit($id)
    {
        $data['warehouse'] = $this->warehouseModel->find($id);
        return view('warehouse/edit', $data);
    }

    // Memperbarui informasi gudang di database
    public function update($id)
    {
        $data = [
            'name' => $this->request->getPost('name'),
            'location' => $this->request->getPost('location'),
            'capacity' => $this->request->getPost('capacity'),
        ];
        
        $this->warehouseModel->update($id, $data);
        return redirect()->to('/warehouse');
    }

    // Menghapus gudang dari database
    public function delete($id)
    {
        $this->warehouseModel->delete($id);
        return redirect()->to('/warehouse');
    }

    // Menambah stok barang ke gudang
    public function addStock($id)
    {
        $data['warehouse'] = $this->warehouseModel->find($id);
        return view('warehouse/add_stock', $data);
    }

    // Memperbarui stok barang di gudang
    public function updateStock($id)
    {
        $stock = $this->request->getPost('stock');
        
        $this->warehouseModel->updateStock($id, $stock);
        return redirect()->to('/warehouse');
    }

    // Menghapus stok barang dari gudang
    public function removeStock($id)
    {
        $stock = $this->request->getPost('stock');
        
        $this->warehouseModel->removeStock($id, $stock);
        return redirect()->to('/warehouse');
    }

    // Melacak pergerakan stok antar gudang
    public function trackStockMovement()
    {
        $data['stockMovements'] = $this->warehouseModel->getStockMovements();
        return view('warehouse/track_stock_movement', $data);
    }

    // Menampilkan laporan stok gudang
    public function report()
    {
        $data['warehouseReports'] = $this->warehouseModel->getWarehouseReport();
        return view('warehouse/report', $data);
    }
}
