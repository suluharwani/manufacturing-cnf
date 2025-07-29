
<style>
    .select2-container--bootstrap-5 {
        width: 100% !important;
    }
    .table-responsive {
        overflow-x: auto;
    }
    .badge {
        font-size: 85%;
    }
    .is-invalid {
        border-color: #dc3545 !important;
    }
    .invalid-feedback {
        display: block;
        color: #dc3545;
        font-size: 0.875em;
    }
    .action-buttons .btn {
        padding: 0.25rem 0.5rem;
        font-size: 0.875rem;
        margin: 0 2px;
    }
    /* Modal improvements */
    .modal-content {
        border-radius: 0.5rem;
    }
</style>

<div class="container-fluid pt-4 px-4">
    <div class="bg-light text-center rounded p-4">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <h6 class="mb-0">Component Management</h6>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#componentModal">
                <i class="fas fa-plus me-2"></i>Add Component
            </button>
        </div>
        
        <!-- Success/Error Messages -->
        <?php if(session()->getFlashdata('success')): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <?= session()->getFlashdata('success') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        
        <?php if(session()->getFlashdata('errors')): ?>
            <div class="alert alert-danger alert-dismissible fade show">
                <ul class="mb-0">
                    <?php foreach(session()->getFlashdata('errors') as $error): ?>
                        <li><?= $error ?></li>
                    <?php endforeach; ?>
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        <th>Code</th>
                        <th>Name</th>
                        <th>Product</th>
                        <th>Stock</th>
                        <th>Unit</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($components as $index => $component): ?>
                    <tr>
                        <td><?= $index + 1 ?></td>
                        <td><?= esc($component['kode']) ?></td>
                        <td><?= esc($component['nama']) ?></td>
                        <td><?= esc($component['product_name'] ?? '-') ?></td>
                        <td class="text-center">
                            <span class="badge <?= ($component['quantity'] < $component['minimum_stock']) ? 'bg-danger' : 'bg-success' ?>">
                                <?= $component['quantity'] ?? 0 ?>
                            </span>
                        </td>
                        <td class="text-center"><?= esc($component['satuan']) ?></td>
                        <td class="text-center">
                            <span class="badge <?= $component['aktif'] == 1 ? 'bg-success' : 'bg-danger' ?>">
                                <?= $component['aktif'] == 1 ? 'Active' : 'Inactive' ?>
                            </span>
                        </td>
                        <td class="text-center action-buttons">
                             <button class="btn btn-sm btn-warning stock-btn" data-id="<?=$component['id']?>" title="Manage Stock"> <i class="fas fa-book"></i></button>
                            <button class="btn btn-sm btn-info" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#componentModal"
                                    onclick="editComponent(<?= $component['id'] ?>)">
                                <i class="fas fa-edit"></i>
                            </button>
                            <form action="<?= site_url('component/delete/'.$component['id']) ?>" method="post" class="d-inline">
                                <?= csrf_field() ?>
                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Component Modal -->
<div class="modal fade" id="componentModal" tabindex="-1" aria-labelledby="componentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="componentModalLabel">Add Component</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="post" action="<?= site_url('component/save') ?>" id="componentForm">
                <input type="hidden" name="id" id="component_id">
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="kode" class="form-label">Code *</label>
                            <input type="text" class="form-control <?= session()->getFlashdata('errors.kode') ? 'is-invalid' : '' ?>" 
                                   id="kode" name="kode" value="<?= old('kode') ?>" required>
                            <?php if(session()->getFlashdata('errors.kode')): ?>
                                <div class="invalid-feedback"><?= session()->getFlashdata('errors.kode') ?></div>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-6">
                            <label for="nama" class="form-label">Name *</label>
                            <input type="text" class="form-control <?= session()->getFlashdata('errors.nama') ? 'is-invalid' : '' ?>" 
                                   id="nama" name="nama" value="<?= old('nama') ?>" required>
                            <?php if(session()->getFlashdata('errors.nama')): ?>
                                <div class="invalid-feedback"><?= session()->getFlashdata('errors.nama') ?></div>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-6">
                            <label for="product_id" class="form-label">Product</label>
                            <select class="form-select select2" id="product_id" name="product_id">
                                <option value="">-- Select Product --</option>
                                <?php foreach($products as $product): ?>
                                    <option value="<?= $product['id'] ?>" <?= old('product_id') == $product['id'] ? 'selected' : '' ?>>
                                        <?= esc($product['kode']) ?> - <?= esc($product['nama']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="satuan" class="form-label">Unit *</label>
                            <select class="form-select <?= session()->getFlashdata('errors.satuan') ? 'is-invalid' : '' ?>" 
                                    id="satuan" name="satuan" required>
                                <option value="">-- Select Unit --</option>
                                <option value="pcs" <?= old('satuan') == 'pcs' ? 'selected' : '' ?>>Pieces</option>
                                <option value="kg" <?= old('satuan') == 'kg' ? 'selected' : '' ?>>Kilogram</option>
                                <option value="m" <?= old('satuan') == 'm' ? 'selected' : '' ?>>Meter</option>
                                <option value="l" <?= old('satuan') == 'l' ? 'selected' : '' ?>>Liter</option>
                                <option value="set" <?= old('satuan') == 'set' ? 'selected' : '' ?>>Set</option>
                            </select>
                            <?php if(session()->getFlashdata('errors.satuan')): ?>
                                <div class="invalid-feedback"><?= session()->getFlashdata('errors.satuan') ?></div>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-6">
                            <label for="minimum_stock" class="form-label">Minimum Stock</label>
                            <input type="number" class="form-control" id="minimum_stock" name="minimum_stock" 
                                   step="0.01" min="0" value="<?= old('minimum_stock') ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Status</label>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="aktif" name="aktif" value="1" 
                                    <?= old('aktif', 1) == 1 ? 'checked' : '' ?>>
                                <label class="form-check-label" for="aktif">Active</label>
                            </div>
                        </div>
                        <div class="col-12">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="3"><?= old('description') ?></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- Add this modal for stock management -->
<div class="modal fade" id="stockModal" tabindex="-1" aria-labelledby="stockModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="stockModalLabel">Stock Management</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="stockForm">
                <div class="modal-body">
                    <input type="hidden" name="component_id" id="stock_component_id">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Current Stock</label>
                            <input type="text" class="form-control" id="current_stock" readonly>
                        </div>
                        <div class="col-md-6">
                            <label for="minimum_stock" class="form-label">Minimum Stock</label>
                            <input type="number" class="form-control" id="minimum_stock" name="minimum_stock" step="0.01" min="0">
                        </div>
                        <div class="col-md-6">
                            <label for="transaction_type" class="form-label">Transaction Type *</label>
                            <select class="form-select" id="transaction_type" name="type" required>
                                <option value="in">Stock In</option>
                                <option value="out">Stock Out</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="quantity" class="form-label">Quantity *</label>
                            <input type="number" class="form-control" id="quantity" name="quantity" step="0.01" min="0.01" required>
                        </div>
                        <div class="col-md-6">
                            <label for="reference" class="form-label">Reference</label>
                            <input type="text" class="form-control" id="reference" name="reference">
                        </div>
                        <div class="col-12">
                            <label for="notes" class="form-label">Notes</label>
                            <textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
                        </div>
                    </div>
                    <hr>
                    <h5>Transaction History</h5>
                    <div class="table-responsive">
                        <table class="table table-bordered" id="transactionHistory">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Type</th>
                                    <th>Quantity</th>
                                    <th>Reference</th>
                                    <th>Notes</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Transaction history will be loaded here -->
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" id="saveStockButton">
                        <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                        Save Transaction
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- JavaScript Libraries -->
<script src="<?= base_url('assets') ?>/js/jquery-3.6.0.min.js"></script>
<script src="<?= base_url('assets') ?>/js/bootstrap.bundle.min.js"></script>
<script src="<?= base_url('assets') ?>/js/select2.min.js"></script>

<script>
$(document).ready(function() {
    // Initialize Select2 with Bootstrap 5 theme
    $('.select2').select2({
        theme: 'bootstrap-5',
        placeholder: 'Select an option',
        allowClear: true
    });

    // Clear form when modal is hidden for adding new
    $('#componentModal').on('hidden.bs.modal', function() {
        if($('#component_id').val() === '') {
            $('#componentForm')[0].reset();
            $('#product_id').val(null).trigger('change');
        }
    });
});

// Function to load component data for editing
function editComponent(id) {
    fetch(`<?= site_url('component/get/') ?>${id}`)
        .then(response => response.json())
        .then(data => {
            if(data.status) {
                const component = data.data;
                $('#componentModalLabel').text('Edit Component');
                $('#component_id').val(component.id);
                $('#kode').val(component.kode);
                $('#nama').val(component.nama);
                $('#product_id').val(component.product_id).trigger('change');
                $('#satuan').val(component.satuan);
                $('#minimum_stock').val(component.stock?.minimum_stock || '');
                $('#description').val(component.description);
                $('#aktif').prop('checked', component.aktif == 1);
            } else {
                alert(data.message || 'Failed to load component data');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error loading component data');
        });
}
// Add this to your existing JavaScript
$(document).on('click', '.stock-btn', function() {
    var id = $(this).data('id');
    showStockModal(id);
});

function showStockModal(id) {
    var modal = $('#stockModal');
    modal.find('#stock_component_id').val(id);
    
    // Load current stock and minimum stock
    $.get("<?= site_url('component/getStock/') ?>" + id, function(response) {
        if (response.status) {
            $('#current_stock').val(response.data.quantity);
            $('#minimum_stock').val(response.data.minimum_stock);
            
            // Load transaction history
            loadTransactionHistory(id);
        } else {
            swal.fire('danger', response.message);
        }
    }, 'json');
    
    modal.modal('show');
}

function loadTransactionHistory(componentId) {
    $.get("<?= site_url('component/getTransactions/') ?>" + componentId, function(response) {
        var html = '';
        if (response.status && response.data.length > 0) {
            response.data.forEach(function(transaction) {
                html += `
                    <tr>
                        <td>${transaction.created_at}</td>
                        <td><span class="badge ${transaction.type === 'in' ? 'bg-success' : 'bg-danger'}">${transaction.type === 'in' ? 'IN' : 'OUT'}</span></td>
                        <td>${transaction.quantity}</td>
                        <td>${transaction.reference || '-'}</td>
                        <td>${transaction.notes || '-'}</td>
                    </tr>
                `;
            });
        } else {
            html = '<tr><td colspan="5" class="text-center">No transactions found</td></tr>';
        }
        $('#transactionHistory tbody').html(html);
    }, 'json');
}

// Handle stock form submission
$('#stockForm').submit(function(e) {
    e.preventDefault();
    
    var formData = $(this).serialize();
    var saveButton = $('#saveStockButton');
    
    saveButton.prop('disabled', true);
    saveButton.find('.spinner-border').removeClass('d-none');
    
    $.post("<?= site_url('component/saveTransaction') ?>", formData, function(response) {
        if (response.status) {
            swal.fire('success', response.message);
            // Refresh stock info and history
            var componentId = $('#stock_component_id').val();
            loadTransactionHistory(componentId);
            $.get("<?= site_url('component/getStock/') ?>" + componentId, function(stockResponse) {
                if (stockResponse.status) {
                    $('#current_stock').val(stockResponse.data.quantity);
                }
            }, 'json');
            
            // Also refresh the main components table
            loadComponents();
        } else {
            swal.fire('danger', response.message);
        }
    }, 'json').always(function() {
        saveButton.prop('disabled', false);
        saveButton.find('.spinner-border').addClass('d-none');
    });
});
</script>