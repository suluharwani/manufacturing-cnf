<?php namespace App\Models;

use CodeIgniter\Model;

class CustFinanceModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'cust_finance_account';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = ['customer_id', 'balance', 'credit_limit', 'terms', 'status'];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [
        'customer_id' => 'required|is_unique[cust_finance_account.customer_id]',
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
    protected $transactionTable = 'cust_finance_transaction';
    protected $documentTable = 'cust_finance_document';
    protected $logTable = 'cust_finance_log';

    /**
     * Get customer accounts with customer info
     */
    public function getAccounts($search = null, $start = 0, $length = 10)
    {
        $builder = $this->db->table($this->table . ' a')
            ->select('a.*, c.customer_name, c.contact_name, c.contact_phone')
            ->join('customer c', 'c.id = a.customer_id', 'left')
            ->where('a.deleted_at', null);

        if ($search) {
            $builder->groupStart()
                ->like('c.customer_name', $search)
                ->orLike('c.contact_name', $search)
                ->orLike('c.contact_phone', $search)
                ->groupEnd();
        }

        $builder->limit($length, $start);
        return $builder->get()->getResultArray();
    }

    public function getOrCreateAccount($customerId)
    {
        $account = $this->where('customer_id', $customerId)->first();
        
        if (!$account) {
            $this->insert([
                'customer_id' => $customerId,
                'balance' => 0,
                'status' => 1
            ]);
            return $this->where('customer_id', $customerId)->first();
        }
        
        return $account;
    }

    /**
     * Count filtered accounts
     */
    public function countFiltered($search = null)
    {
        $builder = $this->db->table($this->table . ' a')
            ->join('customer c', 'c.id = a.customer_id', 'left')
            ->where('a.deleted_at', null);

        if ($search) {
            $builder->groupStart()
                ->like('c.customer_name', $search)
                ->orLike('c.contact_name', $search)
                ->orLike('c.contact_phone', $search)
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
            if ($data['type'] == 'sale') {
                $balanceChange = $data['amount']; // Increase credit (balance becomes more positive)
            } elseif ($data['type'] == 'adjustment') {
                $balanceChange = $data['amount']; // Can be positive or negative
            } elseif ($data['type'] == 'payment') {
                $balanceChange = -$data['amount']; // Reduce credit (balance becomes less positive)
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
            'user_id' => session()->get('user_id')
        ];

        $this->db->table($this->logTable)->insert($logData);
    }
 
    /**
     * Get sales history for customer
     */
    public function getSalesHistory($customerId)
    {
        return $this->db->table('proforma_invoice p')
            ->select('p.*, SUM(pd.quantity * pd.unit_price) as total')
            ->join('proforma_invoice_details pd', 'pd.invoice_id = p.id', 'left')
            ->where('p.customer_id', $customerId)
            ->where('p.deleted_at', null)
            ->groupBy('p.id')
            ->orderBy('p.created_at', 'DESC')
            ->get()
            ->getResultArray();
    }

    /**
     * Get SO history for customer
     */
    public function getSOHistory($customerId)
    {
        // return $this->db->table('sales_order so')
        //     ->select('so.*, SUM(sol.quantity * sol.price) as total')
        //     ->join('sales_order_list sol', 'sol.id_so = so.id', 'left')
        //     ->where('so.customer_id', $customerId)
        //     ->where('so.deleted_at', null)
        //     ->groupBy('so.id')
        //     ->orderBy('so.date', 'DESC')
        //     ->get()
        //     ->getResultArray();
    }

    public function deleteTransaction($transactionId, $userId)
    {
        $this->db->transStart();

        // 1. Get transaction data to be deleted
        $transaction = $this->db->table($this->transactionTable)
            ->where('id', $transactionId)
            ->where('deleted_at', null)
            ->get()
            ->getRowArray();

        if (!$transaction) {
            throw new \RuntimeException('Transaction not found or already deleted');
        }

        // 2. Calculate reversal amount based on type
        $reversalAmount = 0;
        switch ($transaction['type']) {
            case 'sale':
                $reversalAmount = -$transaction['amount']; // Reduce credit (balance decreases)
                break;
            case 'payment':
                $reversalAmount = $transaction['amount']; // Increase credit (balance increases)
                break;
            case 'adjustment':
                $reversalAmount = -$transaction['amount']; // Reverse the adjustment effect
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

        // 5. Record in log
        $this->addLog($transactionId, 'delete', 'Transaction deleted', json_encode($transaction), null, $userId);

        $this->db->transComplete();

        return $this->db->transStatus();
    }
}