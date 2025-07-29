<?php namespace App\Controllers;

use App\Models\CustFinanceModel;

class CustFinance extends BaseController
{
    protected $custFinanceModel;

    public function __construct()
    {
        $this->custFinanceModel = new CustFinanceModel();
        helper(['form', 'filesystem']);
    }

    public function index()
    {
        $data['group'] = 'Customer Finance';
        $data['title'] = 'Customer Accounts';

        $data['content'] = view('admin/content/cust_finance/accounts');
        return view('admin/index', $data);
    }

    public function getAccounts()
    {
        $search = $this->request->getPost('search[value]');
        $start = $this->request->getPost('start');
        $length = $this->request->getPost('length');
        $draw = $this->request->getPost('draw');

        // Use customer table as base
        $builder = $this->custFinanceModel->db->table('customer c')
            ->select('c.id as customer_id, c.customer_name, c.contact_name, c.contact_phone, 
                     IFNULL(a.balance, 0) as balance, IFNULL(a.credit_limit, 0) as credit_limit, 
                     IFNULL(a.status, 1) as status, a.id')
            ->join('cust_finance_account a', 'a.customer_id = c.id', 'left')
            ->where('c.deleted_at', null);

        if ($search) {
            $builder->groupStart()
                ->like('c.customer_name', $search)
                ->orLike('c.contact_name', $search)
                ->orLike('c.contact_phone', $search)
                ->groupEnd();
        }

        $totalRecords = $builder->countAllResults(false);
        
        $builder->limit($length, $start);
        $data = $builder->get()->getResultArray();

        // Format data for DataTables
        $output = [
            'draw' => $draw,
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $totalRecords,
            'data' => $data
        ];

        return $this->response->setJSON($output);
    }

    public function manage($customerId)
    {
        $data['group'] = 'Customer Finance';
        $data['title'] = 'Manage Customer Finance';

        // Ensure account exists
        $account = $this->custFinanceModel->getOrCreateAccount($customerId);
        
        $data['account'] = $account;
        $data['customer'] = $this->custFinanceModel->db->table('customer')
            ->where('id', $customerId)
            ->get()
            ->getRowArray();
            
        $data['transactions'] = $this->custFinanceModel->getTransactions($account['id']);
        $data['salesHistory'] = $this->custFinanceModel->getSalesHistory($customerId);

        $data['content'] = view('admin/content/cust_finance/manage', $data);
        return view('admin/index', $data);
    }

    public function addTransaction()
    {
        $validation = \Config\Services::validation();
        $validation->setRules([
            'account_id' => 'required|numeric',
            'type' => 'required|in_list[sale,payment,adjustment]',
            'amount' => 'required|decimal',
            'transaction_date' => 'required|valid_date',
            'description' => 'permit_empty|string'
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => $validation->getErrors()
            ]);
        }

        $data = [
            'account_id' => $this->request->getPost('account_id'),
            'type' => $this->request->getPost('type'),
            'amount' => $this->request->getPost('amount'),
            'transaction_date' => $this->request->getPost('transaction_date'),
            'description' => $this->request->getPost('description'),
            'status' => 'completed',
            'created_by' => session()->get('user_id')
        ];

        $transactionId = $this->custFinanceModel->addTransaction($data);

        if (!$transactionId) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Failed to add transaction. Please try again.'
            ]);
        }

        // Handle file upload if exists
        if ($files = $this->request->getFiles()) {
            foreach ($files['documents'] as $file) {
                if ($file->isValid() && !$file->hasMoved()) {
                    $newName = $file->getRandomName();
                    $file->move(WRITEPATH . 'uploads/cust_finance', $newName);

                    $documentData = [
                        'document_type' => $this->request->getPost('document_type') ?? 'receipt',
                        'file_name' => $file->getClientName(),
                        'file_path' => 'uploads/cust_finance/' . $newName,
                        'file_size' => $file->getSize(),
                        'uploaded_by' => session()->get('user_id')
                    ];

                    $this->custFinanceModel->addDocument($transactionId, $documentData);
                }
            }
        }

        // Get updated account balance
        $updatedAccount = $this->custFinanceModel->find($data['account_id']);

        return $this->response->setJSON([
            'status' => 'success',
            'message' => 'Transaction added successfully',
            'transaction_id' => $transactionId,
            'new_balance' => $updatedAccount['balance']
        ]
        );
    }

    public function getTransactionDetails($transactionId)
    {
        $transaction = $this->custFinanceModel->getTransactionWithDocuments($transactionId);
        
        if (!$transaction) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Transaction not found'
            ]);
        }

        return $this->response->setJSON([
            'status' => 'success',
            'data' => $transaction
        ]);
    }

    public function downloadDocument($documentId)
    {
        $document = $this->custFinanceModel->db->table('cust_finance_document')
            ->where('id', $documentId)
            ->get()
            ->getRowArray();

        if (!$document) {
            return redirect()->back()->with('error', 'Document not found');
        }

        return $this->response->download(WRITEPATH . $document['file_path'], null);
    }

    public function deleteTransaction($transactionId)
    {
        try {
            $userId = session()->get('user_id'); // ID of user performing the action

            $success = $this->custFinanceModel->deleteTransaction($transactionId, $userId);

            if ($success) {
                // Get updated balance
                $transaction = $this->custFinanceModel->db->table('cust_finance_transaction')
                    ->where('id', $transactionId)
                    ->get()
                    ->getRowArray();

                $account = $this->custFinanceModel->find($transaction['account_id']);

                return $this->response->setJSON([
                    'status' => 'success',
                    'message' => 'Transaction deleted successfully',
                    'new_balance' => $account['balance']
                ]);
            }

            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Failed to delete transaction'
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }
}