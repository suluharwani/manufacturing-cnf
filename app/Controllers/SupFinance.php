<?php namespace App\Controllers;

use App\Models\SupFinanceModel;

class SupFinance extends BaseController
{
    protected $supFinanceModel;

    public function __construct()
    {
        $this->supFinanceModel = new SupFinanceModel();
        helper(['form', 'filesystem']);
    }

    public function index()
    {
        $data['group'] = 'Supplier Finance';
        $data['title'] = 'Supplier Accounts';

        $data['content'] = view('admin/content/sup_finance/accounts');
        return view('admin/index', $data);
    }

public function getAccounts()
{
    $search = $this->request->getPost('search[value]');
    $start = $this->request->getPost('start');
    $length = $this->request->getPost('length');
    $draw = $this->request->getPost('draw');

    // Gunakan tabel supplier sebagai base
    $builder = $this->supFinanceModel->db->table('supplier s')
        ->select('s.id as supplier_id, s.supplier_name, s.contact_name, s.contact_phone, 
                 IFNULL(a.balance, 0) as balance, IFNULL(a.credit_limit, 0) as credit_limit, 
                 IFNULL(a.status, 1) as status, a.id')
        ->join('sup_finance_account a', 'a.supplier_id = s.id', 'left')
        ->where('s.deleted_at', null);

    if ($search) {
        $builder->groupStart()
            ->like('s.supplier_name', $search)
            ->orLike('s.contact_name', $search)
            ->orLike('s.contact_phone', $search)
            ->groupEnd();
    }

    $totalRecords = $builder->countAllResults(false);
    
    $builder->limit($length, $start);
    $data = $builder->get()->getResultArray();

    // Format data untuk DataTables
    $output = [
        'draw' => $draw,
        'recordsTotal' => $totalRecords,
        'recordsFiltered' => $totalRecords,
        'data' => $data
    ];

    return $this->response->setJSON($output);
}
 public function manage($supplierId)
{
    $data['group'] = 'Supplier Finance';
    $data['title'] = 'Manage Supplier Finance';

    // Pastikan account ada
    $account = $this->supFinanceModel->getOrCreateAccount($supplierId);
    
    $data['account'] = $account;
    $data['supplier'] = $this->supFinanceModel->db->table('supplier')
        ->where('id', $supplierId)
        ->get()
        ->getRowArray();
        
    $data['transactions'] = $this->supFinanceModel->getTransactions($account['id']);
    $data['purchaseHistory'] = $this->supFinanceModel->getPurchaseHistory($supplierId);
    $data['poHistory'] = $this->supFinanceModel->getPOHistory($supplierId);

    $data['content'] = view('admin/content/sup_finance/manage', $data);
    return view('admin/index', $data);
}

public function addTransaction()
{
    $validation = \Config\Services::validation();
    $validation->setRules([
        'account_id' => 'required|numeric',
        'type' => 'required|in_list[purchase,payment,adjustment]',
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

    $transactionId = $this->supFinanceModel->addTransaction($data);

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
                $file->move(WRITEPATH . 'uploads/sup_finance', $newName);

                $documentData = [
                    'document_type' => $this->request->getPost('document_type') ?? 'receipt',
                    'file_name' => $file->getClientName(),
                    'file_path' => 'uploads/sup_finance/' . $newName,
                    'file_size' => $file->getSize(),
                    'uploaded_by' => session()->get('user_id')
                ];

                $this->supFinanceModel->addDocument($transactionId, $documentData);
            }
        }
    }

    // Get updated account balance
    $updatedAccount = $this->supFinanceModel->find($data['account_id']);

    return $this->response->setJSON([
        'status' => 'success',
        'message' => 'Transaction added successfully',
        'transaction_id' => $transactionId,
        'new_balance' => $updatedAccount['balance']
    ]);
}

    public function getTransactionDetails($transactionId)
    {
        $transaction = $this->supFinanceModel->getTransactionWithDocuments($transactionId);
        
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
        $document = $this->supFinanceModel->db->table('sup_finance_document')
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
        $userId = session()->get('user_id'); // ID user yang melakukan aksi

        $success = $this->supFinanceModel->deleteTransaction($transactionId, $userId);

        if ($success) {
            // Dapatkan updated balance
            $transaction = $this->supFinanceModel->db->table('sup_finance_transaction')
                ->where('id', $transactionId)
                ->get()
                ->getRowArray();

            $account = $this->supFinanceModel->find($transaction['account_id']);

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