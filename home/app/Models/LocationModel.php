<?php namespace App\Models;

use CodeIgniter\Model;

class LocationModel extends Model
{
    protected $table      = 'locations';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useSoftDeletes = true;
    protected $allowedFields = ['code', 'name', 'type', 'description', 'parent_id', 'is_active'];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';
    protected $validationRules = [
        'code' => 'required|max_length[20]|is_unique[locations.code,id,{id}]',
        'name' => 'required|max_length[100]',
        'type' => 'required|in_list[warehouse,production,display,quarantine,other]',
        'is_active' => 'required|in_list[0,1]'
    ];
    protected $validationMessages = [
        'code' => [
            'required' => 'Location code is required',
            'max_length' => 'Location code cannot exceed 20 characters',
            'is_unique' => 'Location code must be unique'
        ],
        'name' => [
            'required' => 'Location name is required',
            'max_length' => 'Location name cannot exceed 100 characters'
        ],
        'type' => [
            'required' => 'Location type is required',
            'in_list' => 'Invalid location type selected'
        ],
        'is_active' => [
            'required' => 'Active status is required',
            'in_list' => 'Invalid active status selected'
        ]
    ];
    
    // Hierarchical structure support
    protected $hierarchyTable = 'location_hierarchy';
    
    /**
     * Get all active locations
     */
    public function getActiveLocations()
    {
        return $this->where('is_active', 1)
                   ->orderBy('name', 'ASC')
                   ->findAll();
    }
    
    /**
     * Get location by type
     */
    public function getLocationsByType($type)
    {
        return $this->where('type', $type)
                   ->where('is_active', 1)
                   ->orderBy('name', 'ASC')
                   ->findAll();
    }
    
    /**
     * Get warehouse locations
     */
    public function getWarehouses()
    {
        return $this->getLocationsByType('warehouse');
    }
    
    /**
     * Get production locations
     */
    public function getProductionLocations()
    {
        return $this->getLocationsByType('production');
    }
    
    /**
     * Get location with hierarchy
     */
    public function getLocationWithHierarchy($id)
    {
        $location = $this->find($id);
        if (!$location) return null;
        
        $location['hierarchy'] = $this->getLocationHierarchy($id);
        
        return $location;
    }
    
    /**
     * Get location hierarchy
     */
    public function getLocationHierarchy($id)
    {
        $hierarchy = [];
        
        $currentId = $id;
        while ($currentId) {
            $location = $this->find($currentId);
            if (!$location) break;
            
            array_unshift($hierarchy, [
                'id' => $location['id'],
                'name' => $location['name'],
                'type' => $location['type']
            ]);
            
            $currentId = $location['parent_id'] ?? null;
        }
        
        return $hierarchy;
    }
    
    /**
     * Get child locations
     */
    public function getChildLocations($parentId)
    {
        return $this->where('parent_id', $parentId)
                   ->where('is_active', 1)
                   ->orderBy('name', 'ASC')
                   ->findAll();
    }
    
    /**
     * Get full location path
     */
    public function getLocationPath($id)
    {
        $hierarchy = $this->getLocationHierarchy($id);
        return implode(' > ', array_column($hierarchy, 'name'));
    }
    
    /**
     * Create location hierarchy relationship
     */
    public function createHierarchy($childId, $parentId)
    {
        $db = \Config\Database::connect();
        $builder = $db->table($this->hierarchyTable);
        
        // Get parent hierarchy
        $parentHierarchy = $builder->where('child_id', $parentId)
                                  ->get()
                                  ->getResultArray();
        
        // Insert direct relationship
        $builder->insert([
            'ancestor_id' => $parentId,
            'child_id' => $childId,
            'depth' => 0
        ]);
        
        // Insert indirect relationships
        foreach ($parentHierarchy as $row) {
            $builder->insert([
                'ancestor_id' => $row['ancestor_id'],
                'child_id' => $childId,
                'depth' => $row['depth'] + 1
            ]);
        }
        
        return true;
    }
    
    /**
     * Update location hierarchy when parent changes
     */
    public function updateHierarchy($locationId, $newParentId)
    {
        $db = \Config\Database::connect();
        $builder = $db->table($this->hierarchyTable);
        
        // First, remove all existing hierarchy for this location
        $builder->where('child_id', $locationId)->delete();
        
        // Then create new hierarchy
        if ($newParentId) {
            $this->createHierarchy($locationId, $newParentId);
        }
        
        // Update child locations
        $children = $this->getChildLocations($locationId);
        foreach ($children as $child) {
            $this->updateHierarchy($child['id'], $locationId);
        }
        
        return true;
    }
    
    /**
     * Get all descendants of a location
     */
    public function getDescendants($locationId)
    {
        $db = \Config\Database::connect();
        return $db->table($this->hierarchyTable.' as h')
                 ->select('l.*')
                 ->join('locations as l', 'l.id = h.child_id')
                 ->where('h.ancestor_id', $locationId)
                 ->where('h.depth >', 0)
                 ->get()
                 ->getResultArray();
    }
    
    /**
     * Get location options for dropdown
     */
    public function getLocationOptions($includePath = true)
    {
        $locations = $this->where('is_active', 1)
                         ->orderBy('name', 'ASC')
                         ->findAll();
        
        $options = [];
        foreach ($locations as $location) {
            if ($includePath) {
                $path = $this->getLocationPath($location['id']);
                $options[$location['id']] = $path;
            } else {
                $options[$location['id']] = $location['name'];
            }
        }
        
        return $options;
    }
    
    /**
     * Get location tree
     */
    public function getLocationTree($parentId = null)
    {
        $tree = [];
        
        $locations = $this->where('parent_id', $parentId)
                         ->where('is_active', 1)
                         ->orderBy('name', 'ASC')
                         ->findAll();
        
        foreach ($locations as $location) {
            $children = $this->getLocationTree($location['id']);
            
            $tree[] = [
                'id' => $location['id'],
                'name' => $location['name'],
                'type' => $location['type'],
                'children' => $children
            ];
        }
        
        return $tree;
    }
    
    /**
     * Deactivate location and its descendants
     */
    public function deactivateLocation($id)
    {
        $descendants = $this->getDescendants($id);
        $allIds = array_merge([$id], array_column($descendants, 'id'));
        
        return $this->whereIn('id', $allIds)
                   ->set(['is_active' => 0])
                   ->update();
    }

}