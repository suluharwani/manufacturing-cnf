<?php

namespace App\Controllers;

use App\Models\MasterPenggajianModel;
use CodeIgniter\Controller;

class MasterPenggajianController extends Controller
{
    public function get_list()
    {
        $serverside_model = new \App\Models\Mdl_datatables_penggajian();
        $request = \Config\Services::request();
        $list_data = $serverside_model;
        $where = ['id !=' => 0, 'deleted_at' => NULL];
        $column_order = [null, 'master_penggajian.kode_penggajian', 'master_penggajian.tanggal_awal_penggajian', 'master_penggajian.group', 'master_penggajian.creator'];
        $column_search = ['master_penggajian.kode_penggajian', 'master_penggajian.group'];
        $order = ['master_penggajian.id' => 'desc'];
        $list = $list_data->get_datatables('master_penggajian', $column_order, $column_search, $order, $where);
        $data = [];
        $no = $request->getPost("start");
        foreach ($list as $lists) {
            $no++;
            $row = [];
            $row[] = $no;
            $row[] = $lists->kode_penggajian;
            $row[] = $lists->tanggal_awal_penggajian . ' - ' . $lists->tanggal_akhir_penggajian;
            $row[] = $lists->group;
            $row[] = $lists->creator;
            $row[] = '
    <a href="' . base_url('detail_salary/' . $lists->id) . '" class="btn btn-success btn-sm proses">Proses</a>
    <a href="javascript:void(0);" class="btn btn-secondary btn-sm edit" id="' . $lists->id . '">Edit</a>
    <a href="javascript:void(0);" class="btn btn-danger btn-sm delete" id="' . $lists->id . '">Delete</a>
';

            $data[] = $row;
        }
        $output = [
            "draw" => $request->getPost("draw"),
            "recordsTotal" => $list_data->count_all('master_penggajian', $where),
            "recordsFiltered" => $list_data->count_filtered('master_penggajian', $column_order, $column_search, $order, $where),
            "data" => $data,
        ];

        return $this->response->setJSON($output);
    }

    public function add()
    {
        $model = new MasterPenggajianModel();
        $data = [
            'kode_penggajian' => $this->request->getPost('kode_penggajian'),
            'tanggal_awal_penggajian' => $this->request->getPost('tanggal_awal_penggajian'),
            'tanggal_akhir_penggajian' =>date('Y-m-d 23:59:59', strtotime( $this->request->getPost('tanggal_akhir_penggajian'))),
            'group' => $this->request->getPost('group'),
            'creator' => $this->request->getPost('creator'),
            'keterangan' => $this->request->getPost('keterangan'),
        ];
        $model->insert($data);
        return $this->response->setJSON(['status' => 'success']);
    }

    public function get($id)
    {
        $model = new MasterPenggajianModel();
        $data = $model->find($id);
        return $this->response->setJSON($data);
    }

    public function update()
    {
        $model = new MasterPenggajianModel();
        $id = $this->request->getPost('id');
        $data = [
            'kode_penggajian' => $this->request->getPost('kode_penggajian'),
            'tanggal_awal_penggajian' => $this->request->getPost('tanggal_awal_penggajian'),
            'tanggal_akhir_penggajian' => date('Y-m-d 23:59:59', strtotime($this->request->getPost('tanggal_akhir_penggajian'))),
            'group' => $this->request->getPost('group'),
            'creator' => $this->request->getPost('creator'),
            'keterangan' => $this->request->getPost('keterangan'),
        ];
        $model->update($id, $data);
        return $this->response->setJSON(['status' => 'success']);
    }

    public function delete($id)
    {
        $model = new MasterPenggajianModel();
        $model->delete($id);
        return $this->response->setJSON(['status' => 'success']);
    }
}
