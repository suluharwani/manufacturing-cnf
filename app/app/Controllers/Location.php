<?php namespace App\Controllers;

use App\Models\StLocation;

class Location extends BaseController
{
    protected $locationModel;

    public function __construct()
    {
        $this->locationModel = new StLocation();
        helper('form');
    }

    public function index()
    {
        $data = [
            'title' => 'Location Management',
            'locations' => $this->locationModel->withDeleted()->findAll()
        ];
        $data['content'] = view('admin/content/location',$data);
        return view('admin/index', $data);
        // return view('location/index', $data);
    }

    public function create()
    {
        $data = [
            'title' => 'Add New Location',
            'validation' => \Config\Services::validation(),
            'locationTypes' => $this->locationModel->getLocationTypes(),
            'parentLocations' => $this->locationModel->getParentLocations()
        ];

        $data['content'] = view('admin/content/location_create',$data);
        return view('admin/index', $data);
    }

    public function store()
    {
        if (!$this->validate($this->locationModel->getValidationRules())) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->locationModel->save([
            'code' => strtoupper($this->request->getVar('code')),
            'name' => $this->request->getVar('name'),
            'type' => $this->request->getVar('type'),
            'description' => $this->request->getVar('description'),
            'parent_id' => $this->request->getVar('parent_id'),
            'is_active' => $this->request->getVar('is_active') ? 1 : 0
        ]);

        return redirect()->to('/location')->with('message', 'Location added successfully');
    }

    public function edit($id)
    {
        $data = [
            'title' => 'Edit Location',
            'location' => $this->locationModel->withDeleted()->find($id),
            'validation' => \Config\Services::validation(),
            'locationTypes' => $this->locationModel->getLocationTypes(),
            'parentLocations' => $this->locationModel->getParentLocations($id)
        ];

        $data['content'] = view('admin/content/location_edit',$data);
        return view('admin/index', $data);
    }

    public function update($id)
    {
        $location = $this->locationModel->withDeleted()->find($id);

        $rules = $this->locationModel->getValidationRules();
        
        // If code hasn't changed, remove unique validation
        if ($this->request->getVar('code') === $location['code']) {
            $rules['code'] = 'required|max_length[20]';
        }

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->locationModel->save([
            'id' => $id,
            'code' => strtoupper($this->request->getVar('code')),
            'name' => $this->request->getVar('name'),
            'type' => $this->request->getVar('type'),
            'description' => $this->request->getVar('description'),
            'parent_id' => $this->request->getVar('parent_id'),
            'is_active' => $this->request->getVar('is_active') ? 1 : 0
        ]);

        return redirect()->to('/location')->with('message', 'Location updated successfully');
    }

    public function delete($id)
    {
        $this->locationModel->delete($id);
        return redirect()->to('/location')->with('message', 'Location deleted successfully');
    }

    public function restore($id)
    {
        $this->locationModel->withDeleted()->update($id, ['deleted_at' => null]);
        return redirect()->to('/location')->with('message', 'Location restored successfully');
    }

    public function toggleStatus($id)
    {
        $location = $this->locationModel->withDeleted()->find($id);
        $newStatus = $location['is_active'] ? 0 : 1;
        
        $this->locationModel->update($id, ['is_active' => $newStatus]);
        
        return redirect()->to('/location')->with('message', 'Location status updated');
    }
    public function viewStock($locationId)
{
    $location = $this->locationModel->find($locationId);
    
    if (!$location) {
        return redirect()->to('/location')->with('error', 'Location not found');
    }

    $data = [
        'title' => 'Stock in ' . $location['name'],
        'location' => $location,
        'stocks' => $this->locationModel->getStockInLocation($locationId),
        'locationTypes' => $this->locationModel->getLocationTypes()
    ];

    $data['content'] = view('admin/content/location_view_stock',$data);
        return view('admin/index', $data);
}
}