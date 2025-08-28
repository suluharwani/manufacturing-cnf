<?php namespace App\Models;

use CodeIgniter\Model;

class SupFinanceModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'sup_finance_account';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = ['supplier_id', 'balance', 'credit_limit', 'terms', 'status'];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [
        'supplier_id' => 'required|is_unique[sup_finance_account.supplier_id]',
        'balance'     => 'permit_empty|decimal',
        'credit_limit'=> 'permit_empty|decimal',
        'terms'       => 'permit_empty|string|max_length[100]',
        'status'      => 'permit_empty|in_list[0,1]'
    ];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    // Transaction Model
    protected $transactionTable = 'sup_finance_transaction';
    protected $documentTable = 'sup_finance_document';
    protected $logTable = 'sup_finance_log';

    /**
     * Get supplier accounts with supplier info
     */
    public function getAccounts($search = null, $start = 0, $length = 10)
    {
        $builder = $this->db->table($this->table . ' a')
            ->select('a.*, s.supplier_name, s.contact_name, s.contact_phone')
            ->join('supplier s', 's.id = a.supplier_id', 'left')
            ->where('a.deleted_at', null);

        if ($search) {
            $builder->groupStart()
                ->like('s.supplier_name', $search)
                ->orLike('s.contact_name', $search)
                ->orLike('s.contact_phone', $search)
                ->groupEnd();
        }

        $builder->limit($length, $start);
        return $builder->get()->getResultArray();
    }

    public function getOrCreateAccount($supplierId)
{
    $account = $this->where('supplier_id', $supplierId)->first();
    
    if (!$account) {
        $this->insert([
            'supplier_id' => $supplierId,
            'balance' => 0,
            'status' => 1
        ]);
        return $this->where('supplier_id', $supplierId)->first();
    }
    
    return $account;
}
    /**
     * Count filtered accounts
     */
    public function countFiltered($search = null)
    {
        $builder = $this->db->table($this->table . ' a')
            ->join('supplier s', 's.id = a.supplier_id', 'left')
            ->where('a.deleted_at', null);

        if ($search) {
            $builder->groupStart()
                ->like('s.supplier_name', $search)
                ->orLike('s.contact_name', $search)
                ->orLike('s.contact_phone', $search)
                ->groupEnd();
        }

        return $builder->countAllResults();
    }

    /**
     * Get transactions for an account
     */
    public function getTransactions($accountId, $start = 0, $length = 10)
    {
        return $this->db->table($this->transactionTable)
            ->where('account_id', $accountId)
            ->where('deleted_at', null)
            ->orderBy('transaction_date', 'DESC')
            ->limit($length, $start)
            ->get()
            ->getResultArray();
    }

    /**
     * Get transaction details with documents
     */
    public function getTransactionWithDocuments($transactionId)
    {
        $transaction = $this->db->table($this->transactionTable)
            ->where('id', $transactionId)
            ->get()
            ->getRowArray();

        if ($transaction) {
            $transaction['documents'] = $this->db->table($this->documentTable)
                ->where('transaction_id', $transactionId)
                ->get()
                ->getResultArray();
        }

        return $transaction;
    }

    /**
     * Add a new transaction
     */
public function addTransaction($data)
{
    $this->db->transStart();

    try {
        // Insert transaction
        $this->db->table($this->transactionTable)->insert($data);
        $transactionId = $this->db->insertID();

        // Calculate balance change
        $balanceChange = 0;
        if ($data['type'] == 'purchase') {
            $balanceChange = -$data['amount']; // Increase debt (balance becomes more negative)
        }elseif ($data['type'] == 'adjustment') {
            $balanceChange = $data['amount']; // Reduce debt (balance becomes less negative)
        }elseif ($data['type'] == 'payment') {
            $balanceChange = $data['amount']; // Reduce debt (balance becomes less negative)
        }

        // Update account balance
        if ($balanceChange != 0) {
            $updateResult = $this->db->table($this->table)
                ->set('balance', 'balance + ' . $balanceChange, false)
                ->where('id', $data['account_id'])
                ->update();

            if (!$updateResult) {
                throw new \RuntimeException('Failed to update account balance');
            }
        }

        // Log the transaction
        $this->addLog($transactionId, 'create', 'Transaction created', null, json_encode($data));

        $this->db->transComplete();

        if ($this->db->transStatus() === false) {
            throw new \RuntimeException('Transaction failed: ' . implode(', ', $this->db->error()));
        }

        return $transactionId;
    } catch (\Exception $e) {
        $this->db->transRollback();
        log_message('error', 'Transaction error: ' . $e->getMessage());
        return false;
    }
}

    /**
     * Add document to transaction
     */
    public function addDocument($transactionId, $documentData)
    {
        $documentData['transaction_id'] = $transactionId;
        $this->db->table($this->documentTable)->insert($documentData);
        $documentId = $this->db->insertID();

        $this->addLog($transactionId, 'document_upload', 'Document uploaded: ' . $documentData['file_name']);

        return $documentId;
    }

    /**
     * Add log entry
     */
    public function addLog($transactionId, $action, $description = null, $oldValue = null, $newValue = null)
    {
        $logData = [
            'transaction_id' => $transactionId,
            'action' => $action,
            'description' => $description,
            'old_value' => $oldValue,
            'new_value' => $newValue,
            'user_id' => session()->get('user_id') // Assuming you have user_id in session
        ];

        $this->db->table($this->logTable)->insert($logData);
    }
 
    /**
     * Get purchase history for supplier
     */
    public function getPurchaseHistory($supplierId)
    {
        return $this->db->table('pembelian p')
            ->select('p.*, SUM(pd.jumlah * pd.harga) as total')
            ->join('pembelian_detail pd', 'pd.id_pembelian = p.id', 'left')
            ->where('p.id_supplier', $supplierId)
            ->where('p.deleted_at', null)
            ->groupBy('p.id')
            ->orderBy('p.tanggal_nota', 'DESC')
            ->get()
            ->getResultArray();
    }

    /**
     * Get PO history for supplier
     */
    public function getPOHistory($supplierId)
    {
        return $this->db->table('purchase_order po')
            ->select('po.*, SUM(pol.quantity * pol.price) as total')
            ->join('purchase_order_list pol', 'pol.id_po = po.id', 'left')
            ->where('po.supplier_id', $supplierId)
            ->where('po.deleted_at', null)
            ->groupBy('po.id')
            ->orderBy('po.date', 'DESC')
            ->get()
            ->getResultArray();
    }
    public function deleteTransaction($transactionId, $userId)
{
    $this->db->transStart();

    // 1. Dapatkan data transaksi yang akan dihapus
    $transaction = $this->db->table($this->transactionTable)
        ->where('id', $transactionId)
        ->where('deleted_at', null)
        ->get()
        ->getRowArray();

    if (!$transaction) {
        throw new \RuntimeException('Transaction not found or already deleted');
    }

    // 2. Hitung reversal amount berdasarkan type
    $reversalAmount = 0;
    switch ($transaction['type']) {
        case 'purchase':
            $reversalAmount = $transaction['amount']; // Mengurangi hutang (balance naik)
            break;
        case 'payment':
            $reversalAmount = -$transaction['amount']; // Menambah hutang (balance turun)
            break;
        case 'adjustment':
            $reversalAmount = -$transaction['amount']; // Membalikkan efek adjustment
            break;
    }

    // 3. Update balance account
    if ($reversalAmount != 0) {
        $this->db->table($this->table)
            ->set('balance', 'balance + ' . $reversalAmount, false)
            ->where('id', $transaction['account_id'])
            ->update();
    }

    // 4. Soft delete transaction
    $this->db->table($this->transactionTable)
        ->where('id', $transactionId)
        ->update([
            'deleted_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);

    // 5. Catat di log
    $this->addLog($transactionId, 'delete', 'Transaction deleted', json_encode($transaction), null, $userId);

    $this->db->transComplete();

    return $this->db->transStatus();
}
public function getReportData($startDate, $endDate, $supplierId = null)
{
    $builder = $this->db->table('supplier s')
        ->select('s.id as supplier_id, s.supplier_name, s.contact_name, s.contact_phone,
                 a.balance as final_balance, a.credit_limit, a.status,
                 (SELECT COALESCE(SUM(amount), 0) FROM sup_finance_transaction 
                  WHERE account_id = a.id AND type = "purchase" 
                  AND transaction_date < "' . $startDate . '" AND deleted_at IS NULL) as initial_purchase,
                 (SELECT COALESCE(SUM(amount), 0) FROM sup_finance_transaction 
                  WHERE account_id = a.id AND type = "payment" 
                  AND transaction_date < "' . $startDate . '" AND deleted_at IS NULL) as initial_payment,
                 (SELECT COALESCE(SUM(amount), 0) FROM sup_finance_transaction 
                  WHERE account_id = a.id AND type = "adjustment" 
                  AND transaction_date < "' . $startDate . '" AND deleted_at IS NULL) as initial_adjustment,
                 (SELECT COALESCE(SUM(amount), 0) FROM sup_finance_transaction 
                  WHERE account_id = a.id AND type = "purchase" 
                  AND transaction_date BETWEEN "' . $startDate . '" AND "' . $endDate . '" AND deleted_at IS NULL) as total_purchase,
                 (SELECT COALESCE(SUM(amount), 0) FROM sup_finance_transaction 
                  WHERE account_id = a.id AND type = "payment" 
                  AND transaction_date BETWEEN "' . $startDate . '" AND "' . $endDate . '" AND deleted_at IS NULL) as total_payment,
                 (SELECT COALESCE(SUM(amount), 0) FROM sup_finance_transaction 
                  WHERE account_id = a.id AND type = "adjustment" 
                  AND transaction_date BETWEEN "' . $startDate . '" AND "' . $endDate . '" AND deleted_at IS NULL) as total_adjustment')
        ->join('sup_finance_account a', 'a.supplier_id = s.id', 'left')
        ->where('s.deleted_at', null);
    
    if ($supplierId && $supplierId !== 'all') {
        $builder->where('s.id', $supplierId);
    }
    
    $results = $builder->get()->getResultArray();
    
    // Hitung saldo awal dan format data
    foreach ($results as &$row) {
        $row['initial_balance'] = ($row['initial_purchase'] - $row['initial_payment'] + $row['initial_adjustment']);
        unset($row['initial_purchase'], $row['initial_payment'], $row['initial_adjustment']);
    }
    
    return $results;
}
}