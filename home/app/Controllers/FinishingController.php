<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\FinishingModel;

class FinishingController extends BaseController
{
    public function index()
    {
        return view('finishing/index');
    }

    public function getAll($id)
    {
        $model = new FinishingModel();
        $data = $model->where('id_product',$id)->findAll();

        return $this->response->setJSON(['data' => $data]);
    }

public function create($id)
{
    $validation = \Config\Services::validation();
    $rules = [
        'name' => 'required',
        'description' => 'required',
        'picture' => 'uploaded[picture]|is_image[picture]|max_size[picture,2048]',
    ];

    if (!$this->validate(rules: $rules)) {
        return $this->response->setJSON([
            'status' => false,
            'errors' => $validation->getErrors(),
        ]);
    }

    $file = $this->request->getFile('picture');
    $fileName = $file->getRandomName();
    $file->move('uploads/finishing', $fileName);

    $data = [
        'name' => $this->request->getPost('name'),
        'id_product' => $id,
        'description' => $this->request->getPost('description'),
        'picture' => $fileName,
    ];

    $model = new \App\Models\FinishingModel();
    $model->insert($data);

    return $this->response->setJSON([
        'status' => true,
        'message' => 'Item added successfully',
    ]);
}


public function updateData()
{
    $id = $this->request->getPost('id'); // Mengambil ID dari request POST
    $data = $this->request->getPost();

    if (!$id) {
        return $this->response->setJSON([
            'status' => false,
            'message' => 'Invalid request: ID is required.',
        ]);
    }

    $validation = \Config\Services::validation();
    $rules = [
        'name' => 'required',
        'description' => 'required',
    ];

    if (!$this->validate($rules)) {
        return $this->response->setJSON([
            'status' => false,
            'errors' => $validation->getErrors(),
        ]);
    }

    $model = new \App\Models\FinishingModel();
    $item = $model->find($id);

    if (!$item) {
        return $this->response->setJSON([
            'status' => false,
            'message' => 'Item not found.',
        ]);
    }

    $model->update($id, $data);

    return $this->response->setJSON([
        'status' => true,
        'message' => 'Item updated successfully.',
    ]);
}
public function updatePicture()
{
    $id = $this->request->getPost('id'); // Mengambil ID dari request POST

    if (!$id) {
        return $this->response->setJSON([
            'status' => false,
            'message' => 'Invalid request: ID is required.',
        ]);
    }

    $model = new \App\Models\FinishingModel();
    $item = $model->find($id);

    if (!$item) {
        return $this->response->setJSON([
            'status' => false,
            'message' => 'Item not found.',
        ]);
    }

    if (!$this->validate(['picture' => 'uploaded[picture]|is_image[picture]|max_size[picture,2048]'])) {
        return $this->response->setJSON([
            'status' => false,
            'errors' => $this->validator->getErrors(),
        ]);
    }

    $file = $this->request->getFile('picture');
    $fileName = $file->getRandomName();
    $file->move('uploads/finishing', $fileName);

    if ($item['picture']) {
        unlink('uploads/finishing/' . $item['picture']); // Menghapus gambar lama
    }

    $model->update($id, ['picture' => $fileName]);

    return $this->response->setJSON([
        'status' => true,
        'message' => 'Picture updated successfully.',
    ]);
}


public function delete($id)
{
    // Validate input
    if (!is_numeric($id) || $id <= 0) {
        return $this->response->setJSON([
            'status' => false,
            'message' => 'Invalid ID provided'
        ]);
    }

    $model = new FinishingModel();
    $bomf = new \App\Models\MdlBillOfMaterialFinishing();
    
    try {
        // Start transaction
        db_connect()->transBegin();

        $item = $model->find($id);
        if (!$item) {
            throw new \RuntimeException('Item not found');
        }

        // Delete associated file if exists
        if (!empty($item['picture'])) {
            $filePath = 'uploads/finishing/' . $item['picture'];
            if (file_exists($filePath)) {
                if (!unlink($filePath)) {
                    throw new \RuntimeException('Failed to delete associated file');
                }
            };
        }

        // Delete from bill of material first (child records)
        $bomf->where('id_modul', $id)->delete();
        
        // Then delete the main record
        if (!$model->delete($id)) {
            throw new \RuntimeException('Failed to delete main record');
        }

        // Commit transaction if all operations succeeded
        db_connect()->transCommit();

        return $this->response->setJSON([
            'status' => true,
            'message' => 'Item and associated data deleted successfully'
        ]);

    } catch (\Exception $e) {
        // Rollback transaction on error
        db_connect()->transRollback();

        return $this->response->setJSON([
            'status' => false,
            'message' => 'Delete failed: ' . $e->getMessage()
        ]);
    }
}
    public function get()
{
    $id = $this->request->getPost('id'); // Mengambil ID dari request POST

    if (!$id) {
        return $this->response->setJSON([
            'status' => false,
            'message' => 'Invalid request: ID is required.',
        ]);
    }

    $model = new \App\Models\FinishingModel();
    $item = $model->find($id); // Mencari data berdasarkan ID

    if ($item) {
        return $this->response->setJSON([
            'status' => true,
            'data' => $item,
        ]);
    }

    return $this->response->setJSON([
        'status' => false,
        'message' => 'Item not found.',
    ]);
}

}
