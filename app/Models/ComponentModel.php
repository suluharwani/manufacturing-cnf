<?php

namespace App\Models;

use CodeIgniter\Model;

class ComponentModel extends Model
{
    protected $table = 'component_components';
    protected $primaryKey = 'id';
    protected $allowedFields = ['product_id', 'parent_id', 'kode', 'nama', 'description', 'satuan', 'aktif'];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';
    protected $useSoftDeletes = true;

    public function getData(array $postData)
    {
        $builder = $this->db->table($this->table . ' c');
        $builder->select('c.*, p.nama as product_name, pc.nama as parent_name, 
                         GROUP_CONCAT(cc.nama SEPARATOR ", ") as categories,
                         s.quantity, s.minimum_stock');
        $builder->join('product p', 'p.id = c.product_id', 'left');
        $builder->join($this->table . ' pc', 'pc.id = c.parent_id', 'left');
        $builder->join('component_component_categories ccc', 'ccc.component_id = c.id', 'left');
        $builder->join('component_categories cc', 'cc.id = ccc.category_id', 'left');
        $builder->join('component_stocks s', 's.component_id = c.id', 'left');
        $builder->groupBy('c.id');

        // Search
        if (!empty($postData['search']['value'])) {
            $builder->groupStart()
                    ->like('c.kode', $postData['search']['value'])
                    ->orLike('c.nama', $postData['search']['value'])
                    ->orLike('p.nama', $postData['search']['value'])
                    ->orLike('pc.nama', $postData['search']['value'])
                    ->groupEnd();
        }

        // Order
        $columns = ['c.id', 'c.kode', 'c.nama', 'p.nama', 'pc.nama', 'categories', 's.quantity'];
        $builder->orderBy($columns[$postData['order'][0]['column']], $postData['order'][0]['dir']);

        // Pagination
        if (isset($postData['length']) && $postData['length'] != -1) {
            $builder->limit($postData['length'], $postData['start']);
        }

        $query = $builder->get();
        return $query->getResultArray();
    }

    public function countFiltered(array $postData)
    {
        $builder = $this->db->table($this->table . ' c');
        $builder->join('product p', 'p.id = c.product_id', 'left');
        $builder->join($this->table . ' pc', 'pc.id = c.parent_id', 'left');
        $builder->join('component_component_categories ccc', 'ccc.component_id = c.id', 'left');
        $builder->join('component_categories cc', 'cc.id = ccc.category_id', 'left');

        if (!empty($postData['search']['value'])) {
            $builder->groupStart()
                    ->like('c.kode', $postData['search']['value'])
                    ->orLike('c.nama', $postData['search']['value'])
                    ->orLike('p.nama', $postData['search']['value'])
                    ->orLike('pc.nama', $postData['search']['value'])
                    ->groupEnd();
        }

        return $builder->countAllResults();
    }

    public function countAll()
    {
        return $this->db->table($this->table)->countAllResults();
    }

    public function getCategories($componentId)
    {
        $builder = $this->db->table('component_component_categories');
        $builder->select('category_id');
        $builder->where('component_id', $componentId);
        $query = $builder->get();
        
        return array_column($query->getResultArray(), 'category_id');
    }

    public function syncCategories($componentId, array $categoryIds)
    {
        // Delete existing relationships
        $this->db->table('component_component_categories')
                 ->where('component_id', $componentId)
                 ->delete();

        // Insert new relationships
        if (!empty($categoryIds)) {
            $data = [];
            foreach ($categoryIds as $categoryId) {
                $data[] = [
                    'component_id' => $componentId,
                    'category_id' => $categoryId
                ];
            }
            
            $this->db->table('component_component_categories')
                     ->insertBatch($data);
        }
    }
}