<style>
    .balance-card {
        border-radius: 10px;
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
    
    .balance-positive {
        color: green;
        font-weight: bold;
    }
    
    .balance-negative {
        color: red;
        font-weight: bold;
    }
    
    .nav-tabs .nav-link.active {
        font-weight: bold;
    }
    
    .transaction-type-payment {
        background-color: rgba(40, 167, 69, 0.1);
    }
    
    .transaction-type-sale {
        background-color: rgba(220, 53, 69, 0.1);
    }
    
    .transaction-type-adjustment {
        background-color: rgba(255, 193, 7, 0.1);
    }
</style>

<div class="container-fluid pt-4 px-4">
    <div class="row g-4">
        <div class="col-md-12">
            <div class="bg-light rounded p-4">
                <div class="d-flex align-items-center justify-content-between mb-4">
                    <h4 class="mb-0">Customer Finance: <?= $customer['customer_name'] ?></h4>
                    
                    <button class="btn btn-primary" onclick="openTransactionModal(<?= $account['id'] ?>)">Add Transaction</button>
                </div>
                
                <div class="row mb-4">
                    <div class="col-md-4">
                        <div class="balance-card p-3 bg-white">
                            <h6 class="text-muted">Current Balance</h6>
                            <h3 class="<?= $account['balance'] >= 0 ? 'balance-positive' : 'balance-negative' ?>">
                                <?= number_format($account['balance'], 2, ',', '.') ?>
                            </h3>
                            <small class="text-muted">Last updated: <?= date('d M Y H:i', strtotime($account['updated_at'] ?? 'now')) ?></small>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="balance-card p-3 bg-white">
                            <h6 class="text-muted">Credit Limit</h6>
                            <h3><?= number_format($account['credit_limit'] ?? 0, 2, ',', '.') ?></h3>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="balance-card p-3 bg-white">
                            <h6 class="text-muted">Status</h6>
                            <h3><?= $account['status'] == 1 ? '<span class="text-success">Active</span>' : '<span class="text-danger">Inactive</span>' ?></h3>
                        </div>
                    </div>
                </div>
                
                <ul class="nav nav-tabs mb-4" id="financeTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="transactions-tab" data-bs-toggle="tab" data-bs-target="#transactions" type="button" role="tab">Transactions</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="sales-tab" data-bs-toggle="tab" data-bs-target="#sales" type="button" role="tab">Sales History</button>
                    </li>
                </ul>
                
                <div class="tab-content" id="financeTabsContent">
                    <div class="tab-pane fade show active" id="transactions" role="tabpanel">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Type</th>
                                        <th>Amount</th>
                                        <th>Description</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($transactions as $txn): ?>
                                    <tr class="transaction-type-<?= $txn['type'] ?>">
                                        <td><?= $txn['transaction_date'] ?></td>
                                        <td><?= ucfirst($txn['type']) ?></td>
                                        <td><?= number_format($txn['amount'], 2, ',', '.') ?></td>
                                        <td><?= $txn['description'] ?? 'N/A' ?></td>
                                        <td><?= ucfirst($txn['status']) ?></td>
                                        <td>
                                            <button class="btn btn-sm btn-info" onclick="viewTransactionDetails(<?= $txn['id'] ?>)">Details</button>
                                            <button class="btn btn-sm btn-danger" onclick="confirmDelete(<?= $txn['id'] ?>)">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                    <div class="tab-pane fade" id="sales" role="tabpanel">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Proforma Invoice</th>
                                        <th>Date</th>
                                        <th>Deposit</th>
                                        <th>Total</th>
                                        <th>Remarks</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($salesHistory as $sale): ?>
                                    <tr>
                                        <td><?= $sale['invoice_number'] ?></td>
                                        <td><?= $sale['invoice_date'] ?></td>
                                        <td><?= number_format($sale['deposit'] ?? 0, 2, ',', '.') ?></td>
                                        <td><?= number_format($sale['total'] ?? 0, 2, ',', '.') ?></td>
                                        <td><?= $sale['remarks'] ?? 'N/A' ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    

                </div>
            </div>
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

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteConfirmationModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">Confirm Delete</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this transaction?</p>
                <p class="fw-bold">This action will update the account balance and cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Delete</button>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        // Initialize datepicker with today's date
        $('#transaction_date').val(new Date().toISOString().split('T')[0]);
        
        // Make sure modal is initialized
        var transactionModal = new bootstrap.Modal(document.getElementById('transactionModal'));
        var detailsModal = new bootstrap.Modal(document.getElementById('transactionDetailsModal'));
    });

    function openTransactionModal(accountId) {
        $('#account_id').val(accountId);
        $('#transactionForm')[0].reset(); // Reset form
        $('#transaction_date').val(new Date().toISOString().split('T')[0]); // Set today's date
        
        // Show modal using Bootstrap 5
        var modal = new bootstrap.Modal(document.getElementById('transactionModal'));
        modal.show();
    }

    function submitTransaction() {
        var formData = new FormData($('#transactionForm')[0]);
        
        // Show loading indicator
        $('.modal-footer .btn-primary').prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Processing...');
        
        $.ajax({
            url: '<?= base_url('custfinance/addtransaction') ?>',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.status === 'success') {
                    // Update balance display immediately
                    var newBalance = parseFloat(response.new_balance || 0);
                    var balanceElement = $('.balance-card:first h3');
                    
                    // Update class based on balance
                    balanceElement.removeClass('balance-positive balance-negative')
                                 .addClass(newBalance >= 0 ? 'balance-positive' : 'balance-negative')
                                 .text(newBalance.toLocaleString('id-ID', {
                                     style: 'currency',
                                     currency: 'IDR',
                                     minimumFractionDigits: 2,
                                     maximumFractionDigits: 2
                                 }));
                    
                    // Close modal
                    var modal = bootstrap.Modal.getInstance(document.getElementById('transactionModal'));
                    modal.hide();
                    
                    // Show success message
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: response.message,
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        // Refresh transactions list
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        html: typeof response.message === 'object' 
                            ? Object.values(response.message).join('<br>')
                            : response.message
                    });
                }
            },
            error: function(xhr) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'An error occurred: ' + xhr.responseText
                });
            },
            complete: function() {
                $('.modal-footer .btn-primary').prop('disabled', false).html('Save Transaction');
            }
        });
    }

    function viewTransactionDetails(transactionId) {
        // Show loading indicator
        $('#transactionDetailsContent').html('<div class="text-center my-4"><div class="spinner-border" role="status"><span class="visually-hidden">Loading...</span></div></div>');
        
        // Show modal first
        var modal = new bootstrap.Modal(document.getElementById('transactionDetailsModal'));
        modal.show();
        
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
                            <strong>Status:</strong> <span class="badge bg-${transaction.status === 'completed' ? 'success' : 'warning'}">${transaction.status.toUpperCase()}</span>
                        </div>
                    </div>
                    <div class="mb-3">
                        <strong>Description:</strong><br>
                        ${transaction.description || 'N/A'}
                    </div>
                `;
                
                if (transaction.documents && transaction.documents.length > 0) {
                    html += '<div class="mb-3"><strong>Documents:</strong><ul class="list-group">';
                    transaction.documents.forEach(doc => {
                        html += `
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <i class="bi bi-file-earmark-${doc.document_type === 'invoice' ? 'text' : 'pdf'} me-2"></i>
                                ${doc.file_name} (${doc.document_type})
                            </div>
                            <a href="<?= base_url('custfinance/downloaddocument') ?>/${doc.id}" class="btn btn-sm btn-outline-primary" target="_blank">
                                <i class="bi bi-download"></i> Download
                            </a>
                        </li>`;
                    });
                    html += '</ul></div>';
                }
                
                $('#transactionDetailsContent').html(html);
            } else {
                $('#transactionDetailsContent').html(`
                    <div class="alert alert-danger">
                        ${response.message || 'Failed to load transaction details'}
                    </div>
                `);
            }
        }).fail(function() {
            $('#transactionDetailsContent').html(`
                <div class="alert alert-danger">
                    Failed to load transaction details. Please try again.
                </div>
            `);
        });
    }

    let transactionToDelete = null;

    function confirmDelete(transactionId) {
        transactionToDelete = transactionId;
        const modal = new bootstrap.Modal(document.getElementById('deleteConfirmationModal'));
        modal.show();
    }

    // Event listener for confirm button
    document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
        if (!transactionToDelete) return;
        
        const modal = bootstrap.Modal.getInstance(document.getElementById('deleteConfirmationModal'));
        
        $.ajax({
            url: '<?= base_url('custfinance/deletetransaction') ?>/' + transactionToDelete,
            type: 'DELETE',
            dataType: 'json',
            beforeSend: function() {
                $('#confirmDeleteBtn').html('<span class="spinner-border spinner-border-sm" role="status"></span> Deleting...');
            },
            success: function(response) {
                if (response.status === 'success') {
                    modal.hide();
                    
                    // Update balance display
                    const newBalance = parseFloat(response.new_balance || 0);
                    const balanceElement = $('.balance-card:first h3');
                    
                    balanceElement.removeClass('balance-positive balance-negative')
                                 .addClass(newBalance >= 0 ? 'balance-positive' : 'balance-negative')
                                 .text(newBalance.toLocaleString('id-ID', {
                                     style: 'currency',
                                     currency: 'IDR'
                                 }));
                    
                    // Refresh transactions list
                    setTimeout(() => {
                        window.location.reload();
                    }, 1500);
                    
                    Swal.fire({
                        icon: 'success',
                        title: 'Deleted!',
                        text: response.message,
                        timer: 2000,
                        showConfirmButton: false
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
                    text: 'Failed to delete: ' + xhr.responseText
                });
            },
            complete: function() {
                $('#confirmDeleteBtn').html('Delete');
                transactionToDelete = null;
            }
        });
    });
</script>