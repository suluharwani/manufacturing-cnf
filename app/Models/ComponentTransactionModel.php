<?php

// app/Models/ComponentTransactionModel.php
namespace App\Models;

use CodeIgniter\Model;

class ComponentTransactionModel extends Model
{
    protected $table = 'component_transactions';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'id',
        'component_id', 
        'type', 
        'quantity', 
        'reference', 
        'notes', 
        'created_by',
        'created_at'
    ];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = ''; // Empty string if you don't need updated_at
}