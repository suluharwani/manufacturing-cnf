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
    public function getCustomerList()
{
    // Gunakan query builder untuk mengambil data customer
    $customers = $this->custFinanceModel->db->table('customer')
        ->select('id, customer_name')
        ->where('deleted_at', null)
        ->orderBy('customer_name', 'ASC')
        ->get()
        ->getResultArray();

    return $this->response->setJSON([
        'status' => 'success',
        'data' => $customers
    ]);
}

public function downloadReport()
{
    // Validasi input
    $startDate = $this->request->getGet('start_date');
    $endDate = $this->request->getGet('end_date');
    $format = $this->request->getGet('format');
    $customerId = $this->request->getGet('customer_filter');
    
    // Set default date jika tidak ada
    if (empty($startDate)) {
        $startDate = date('Y-m-01'); // Awal bulan ini
    }
    if (empty($endDate)) {
        $endDate = date('Y-m-d'); // Hari ini
    }
    
    // Validasi format
    if (!in_array($format, ['excel', 'pdf'])) {
        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'Format laporan tidak valid'
        ]);
    }
    
    // Ambil data rekap melalui model
    $reportData = $this->custFinanceModel->getReportData($startDate, $endDate, $customerId);
    
    if ($format === 'excel') {
        return $this->generateExcelReport($reportData, $startDate, $endDate);
    } else {
        return $this->generatePdfReport($reportData, $startDate, $endDate);
    }
}

private function generateExcelReport($data, $startDate, $endDate)
{
    $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    
    // Set judul
    $sheet->setCellValue('A1', 'REKAP CUSTOMER FINANCE');
    $sheet->setCellValue('A2', 'Periode: ' . date('d/m/Y', strtotime($startDate)) . ' - ' . date('d/m/Y', strtotime($endDate)));
    
    // Header tabel
    $sheet->setCellValue('A4', 'No');
    $sheet->setCellValue('B4', 'Customer');
    $sheet->setCellValue('C4', 'Contact Person');
    $sheet->setCellValue('D4', 'Telepon');
    $sheet->setCellValue('E4', 'Saldo Awal');
    $sheet->setCellValue('F4', 'Total Penjualan');
    $sheet->setCellValue('G4', 'Total Pembayaran');
    $sheet->setCellValue('H4', 'Adjustment');
    $sheet->setCellValue('I4', 'Saldo Akhir');
    $sheet->setCellValue('J4', 'Credit Limit');
    $sheet->setCellValue('K4', 'Status');
    
    // Isi data
    $row = 5;
    $no = 1;
    
    foreach ($data as $item) {
        $sheet->setCellValue('A' . $row, $no++);
        $sheet->setCellValue('B' . $row, $item['customer_name']);
        $sheet->setCellValue('C' . $row, $item['contact_name']);
        $sheet->setCellValue('D' . $row, $item['contact_phone']);
        $sheet->setCellValue('E' . $row, $item['initial_balance']);
        $sheet->setCellValue('F' . $row, $item['total_sale']);
        $sheet->setCellValue('G' . $row, $item['total_payment']);
        $sheet->setCellValue('H' . $row, $item['total_adjustment']);
        $sheet->setCellValue('I' . $row, $item['final_balance']);
        $sheet->setCellValue('J' . $row, $item['credit_limit']);
        $sheet->setCellValue('K' . $row, $item['status'] == 1 ? 'Aktif' : 'Nonaktif');
        
        $row++;
    }
    
    // Format angka
    $sheet->getStyle('E5:I' . ($row-1))->getNumberFormat()->setFormatCode('#,##0.00');
    
    // Auto size columns
    foreach(range('A','K') as $columnID) {
        $sheet->getColumnDimension($columnID)->setAutoSize(true);
    }
    
    // Style header
    $headerStyle = [
        'font' => ['bold' => true],
        'fill' => [
            'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
            'startColor' => ['argb' => 'FFE0E0E0']
        ]
    ];
    $sheet->getStyle('A4:K4')->applyFromArray($headerStyle);
    
    // Set judul style
    $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
    $sheet->getStyle('A2')->getFont()->setItalic(true);
    
    // Writer
    $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
    
    // Set header untuk download
    $filename = 'rekap_customer_finance_' . date('Ymd_His') . '.xlsx';
    
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="' . $filename . '"');
    header('Cache-Control: max-age=0');
    
    $writer->save('php://output');
    exit;
}

private function generatePdfReport($data, $startDate, $endDate)
{
    // Load library Dompdf
    $dompdf = new \Dompdf\Dompdf();
    
    $html = '
    <!DOCTYPE html>
    <html>
    <head>
        <title>Rekap Customer Finance</title>
        <style>
            body { font-family: Arial, sans-serif; }
            h1 { text-align: center; font-size: 18px; }
            h2 { text-align: center; font-size: 14px; font-weight: normal; }
            table { width: 100%; border-collapse: collapse; margin-top: 20px; }
            th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
            th { background-color: #f2f2f2; font-weight: bold; }
            .text-right { text-align: right; }
            .footer { margin-top: 30px; text-align: right; font-size: 12px; }
        </style>
    </head>
    <body>
        <h1>REKAP CUSTOMER FINANCE</h1>
        <h2>Periode: ' . date('d/m/Y', strtotime($startDate)) . ' - ' . date('d/m/Y', strtotime($endDate)) . '</h2>
        
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Customer</th>
                    <th>Contact Person</th>
                    <th>Telepon</th>
                    <th class="text-right">Saldo Awal</th>
                    <th class="text-right">Total Penjualan</th>
                    <th class="text-right">Total Pembayaran</th>
                    <th class="text-right">Adjustment</th>
                    <th class="text-right">Saldo Akhir</th>
                    <th class="text-right">Credit Limit</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>';
    
    $no = 1;
    foreach ($data as $item) {
        $html .= '
                <tr>
                    <td>' . $no++ . '</td>
                    <td>' . $item['customer_name'] . '</td>
                    <td>' . $item['contact_name'] . '</td>
                    <td>' . $item['contact_phone'] . '</td>
                    <td class="text-right">' . number_format($item['initial_balance'], 2) . '</td>
                    <td class="text-right">' . number_format($item['total_sale'], 2) . '</td>
                    <td class="text-right">' . number_format($item['total_payment'], 2) . '</td>
                    <td class="text-right">' . number_format($item['total_adjustment'], 2) . '</td>
                    <td class="text-right">' . number_format($item['final_balance'], 2) . '</td>
                    <td class="text-right">' . number_format($item['credit_limit'], 2) . '</td>
                    <td>' . ($item['status'] == 1 ? 'Aktif' : 'Nonaktif') . '</td>
                </tr>';
    }
    
    $html .= '
            </tbody>
        </table>
        
        <div class="footer">
            Dicetak pada: ' . date('d/m/Y H:i:s') . '
        </div>
    </body>
    </html>';
    
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'landscape');
    $dompdf->render();
    
    $filename = 'rekap_customer_finance_' . date('Ymd_His') . '.pdf';
    
    $dompdf->stream($filename, array("Attachment" => true));
    exit;
}
}