<link rel="stylesheet" type="text/css" href="<?= base_url('assets') ?>/datatables/datatables.min.css" />
<style>
    /* Minimal CSS for fixed header */
    thead th {
        position: sticky;
        top: 0;
        background-color: #343a40;
        z-index: 100;
    }
    
    .balance-positive {
        color: green;
        font-weight: bold;
    }
    
    .balance-negative {
        color: red;
        font-weight: bold;
    }
</style>

<div class="container-fluid pt-4 px-4">
    <div class="bg-light text-center rounded p-4">
        <div class="d-flex align-items-center justify-content-between mb-4">
    <h6 class="mb-0">Customer Finance Accounts</h6>
    <div>
        <button class="btn btn-success me-2" onclick="openReportModal()">Download Rekap</button>
        <button class="btn btn-primary" onclick="refreshTable()">Refresh</button>
    </div>
</div>
        <div class="table-responsive">
            <table id="tabel_serverside" class="table table-bordered display text-left" cellspacing="0" width="100%">
                <thead>
                    <tr class="text-center">
                        <th style="text-align: center;">#</th>
                        <th style="text-align: center;">Customer</th>
                        <th style="text-align: center;">Contact</th>
                        <th style="text-align: center;">Phone</th>
                        <th style="text-align: center;">Balance</th>
                        <th style="text-align: center;">Credit Limit</th>
                        <th style="text-align: center;">Status</th>
                        <th style="text-align: center;">Actions</th>
                    </tr>
                </thead>
                <tfoot>
                    <tr class="text-center">
                        <th style="text-align: center;">#</th>
                        <th style="text-align: center;">Customer</th>
                        <th style="text-align: center;">Contact</th>
                        <th style="text-align: center;">Phone</th>
                        <th style="text-align: center;">Balance</th>
                        <th style="text-align: center;">Credit Limit</th>
                        <th style="text-align: center;">Status</th>
                        <th style="text-align: center;">Actions</th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

<!-- Transaction Modal -->
<div class="modal fade" id="transactionModal" tabindex="-1" aria-labelledby="transactionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="transactionModalLabel">Add Transaction</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="transactionForm">
                    <input type="hidden" name="account_id" id="account_id">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="type" class="form-label">Transaction Type</label>
                            <select class="form-select" name="type" id="type" required>
                                <option value="payment">Payment</option>
                                <option value="sale">Sale</option>
                                <option value="adjustment">Adjustment</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="amount" class="form-label">Amount</label>
                            <input type="number" class="form-control" name="amount" id="amount" step="0.01" min="0" required>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="transaction_date" class="form-label">Date</label>
                            <input type="date" class="form-control" name="transaction_date" id="transaction_date" required>
                        </div>
                        <div class="col-md-6">
                            <label for="document_type" class="form-label">Document Type</label>
                            <select class="form-select" name="document_type" id="document_type">
                                <option value="receipt">Receipt</option>
                                <option value="invoice">Invoice</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" name="description" id="description" rows="2"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="documents" class="form-label">Documents</label>
                        <input class="form-control" type="file" name="documents[]" id="documents" multiple>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="submitTransaction()">Save Transaction</button>
            </div>
        </div>
    </div>
</div>

<!-- Transaction Details Modal -->
<div class="modal fade" id="transactionDetailsModal" tabindex="-1" aria-labelledby="transactionDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="transactionDetailsModalLabel">Transaction Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="transactionDetailsContent">
                Loading...
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="reportModal" tabindex="-1" aria-labelledby="reportModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="reportModalLabel">Download Rekap Customer Finance</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="reportForm">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="start_date" class="form-label">Tanggal Mulai</label>
                            <input type="date" class="form-control" name="start_date" id="start_date">
                        </div>
                        <div class="col-md-6">
                            <label for="end_date" class="form-label">Tanggal Akhir</label>
                            <input type="date" class="form-control" name="end_date" id="end_date">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="format" class="form-label">Format Laporan</label>
                        <select class="form-select" name="format" id="format" required>
                            <option value="excel">Excel (.xlsx)</option>
                            <option value="pdf">PDF (.pdf)</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="customer_filter" class="form-label">Filter Customer (Opsional)</label>
                        <select class="form-select" name="customer_filter" id="customer_filter">
                            <option value="all">Semua Customer</option>
                            <!-- Options akan di-load via AJAX -->
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" onclick="downloadReport()">Download</button>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript" src="<?= base_url('assets') ?>/datatables/datatables.min.js"></script>
<script>
    $(document).ready(function() {
        // Set current date as default
        $('#transaction_date').val(new Date().toISOString().split('T')[0]);
        
        // Initialize DataTable
        var table = $('#tabel_serverside').DataTable({
            "processing": true,
            "serverSide": true,
            "ajax": {
                "url": "<?= base_url('custfinance/getAccounts') ?>",
                "type": "POST"
            },
            "columns": [
                {"data": "customer_id", "className": "text-center"},
                {"data": "customer_name"},
                {"data": "contact_name"},
                {"data": "contact_phone", "className": "text-center"},
                {
                    "data": "balance", 
                    "className": "text-center",
                    "render": function(data, type, row) {
                        var balance = parseFloat(data || 0);
                        var balanceClass = balance >= 0 ? 'balance-positive' : 'balance-negative';
                        return '<span class="' + balanceClass + '">' + balance.toLocaleString('id-ID', {style: 'currency', currency: 'IDR'}) + '</span>';
                    }
                },
                {
                    "data": "credit_limit", 
                    "className": "text-center",
                    "render": function(data, type, row) {
                        return parseFloat(data || 0).toLocaleString('id-ID', {style: 'currency', currency: 'IDR'});
                    }
                },
                {
                    "data": "status", 
                    "className": "text-center",
                    "render": function(data, type, row) {
                        return data == 1 ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-danger">Inactive</span>';
                    }
                },
                {
                    "data": "customer_id",
                    "className": "text-center",
                    "render": function(data, type, row) {
                        return '<a href="<?= base_url('custfinance/manage') ?>/' + data + '" class="btn btn-sm btn-primary">Manage</a>';
                    }
                }
            ]
        });
    });

    function refreshTable() {
        $('#tabel_serverside').DataTable().ajax.reload();
    }

    function openTransactionModal(accountId) {
        $('#account_id').val(accountId);
        $('#transactionModal').modal('show');
    }

    function submitTransaction() {
        var formData = new FormData($('#transactionForm')[0]);
        
        $.ajax({
            url: '<?= base_url('custfinance/addtransaction') ?>',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.status === 'success') {
                    $('#transactionModal').modal('hide');
                    refreshTable();
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: response.message
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.message
                    });
                }
            },
            error: function(xhr) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'An error occurred while processing your request'
                });
            }
        });
    }

    function viewTransactionDetails(transactionId) {
        $.get('<?= base_url('custfinance/gettransactiondetails') ?>/' + transactionId, function(response) {
            if (response.status === 'success') {
                var transaction = response.data;
                var html = `
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>Type:</strong> ${transaction.type.toUpperCase()}
                        </div>
                        <div class="col-md-6">
                            <strong>Amount:</strong> ${parseFloat(transaction.amount).toLocaleString('id-ID', {style: 'currency', currency: 'IDR'})}
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>Date:</strong> ${transaction.transaction_date}
                        </div>
                        <div class="col-md-6">
                            <strong>Status:</strong> ${transaction.status.toUpperCase()}
                        </div>
                    </div>
                    <div class="mb-3">
                        <strong>Description:</strong><br>
                        ${transaction.description || 'N/A'}
                    </div>
                `;
                
                if (transaction.documents && transaction.documents.length > 0) {
                    html += '<div class="mb-3"><strong>Documents:</strong><ul>';
                    transaction.documents.forEach(doc => {
                        html += `<li>
                            <a href="<?= base_url('custfinance/downloaddocument') ?>/${doc.id}" target="_blank">${doc.file_name}</a>
                            (${doc.document_type})
                        </li>`;
                    });
                    html += '</ul></div>';
                }
                
                $('#transactionDetailsContent').html(html);
                $('#transactionDetailsModal').modal('show');
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: response.message
                });
            }
        });
    }
    function openReportModal() {
    // Set default date range (last month)
    const today = new Date();
    const lastMonth = new Date(today.getFullYear(), today.getMonth() - 1, today.getDate());
    
    $('#start_date').val(lastMonth.toISOString().split('T')[0]);
    $('#end_date').val(today.toISOString().split('T')[0]);
    
    // Load customer list via AJAX
    loadCustomerList();
    
    $('#reportModal').modal('show');
}

function loadCustomerList() {
    $.ajax({
        url: '<?= base_url('custfinance/getCustomerList') ?>',
        type: 'GET',
        success: function(response) {
            if (response.status === 'success') {
                var options = '<option value="all">Semua Customer</option>';
                response.data.forEach(function(customer) {
                    options += '<option value="' + customer.id + '">' + customer.customer_name + '</option>';
                });
                $('#customer_filter').html(options);
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Gagal memuat daftar customer'
                });
            }
        },
        error: function() {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Terjadi kesalahan saat memuat daftar customer'
            });
        }
    });
}

function downloadReport() {
    const formData = new FormData($('#reportForm')[0]);
    const params = new URLSearchParams(formData);
    
    // Redirect ke URL download
    window.location.href = `<?= base_url('custfinance/downloadReport') ?>?${params.toString()}`;
    
    // Tutup modal
    $('#reportModal').modal('hide');
}
</script>