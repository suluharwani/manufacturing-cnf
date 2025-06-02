<?= $this->extend('admin/content/layout/template'); ?>

<?= $this->section('content'); ?>
<div class="container">
    <div class="row">
        <div class="col">
            <h1 class="mt-2">Stock Opname: <?= $opname['code']; ?></h1>
            
            <!-- Alert Messages -->
            <?php if (session()->getFlashdata('import_errors')) : ?>
                <div class="alert alert-danger mt-3">
                    <h5>Import Errors:</h5>
                    <ul class="mb-0">
                        <?php foreach (session()->getFlashdata('import_errors') as $error) : ?>
                            <li><?= $error ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('import_stats')) : ?>
                <div class="alert alert-info mt-3">
                    Import stats: 
                    <?= session()->getFlashdata('import_stats')['success'] ?> successful,
                    <?= session()->getFlashdata('import_stats')['errors'] ?> errors
                </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('message')) : ?>
                <div class="alert alert-success" role="alert">
                    <?= session()->getFlashdata('message'); ?>
                </div>
            <?php endif; ?>
            
            <?php if (session()->getFlashdata('error')) : ?>
                <div class="alert alert-danger" role="alert">
                    <?= session()->getFlashdata('error'); ?>
                </div>
            <?php endif; ?>

            <!-- Opname Information Card -->
            <div class="card mb-3">
                <div class="card-body">
                    <h5 class="card-title">Opname Information</h5>
                    <p class="card-text"><strong>Date:</strong> <?= date('d M Y H:i', strtotime($opname['created_at'])); ?></p>
                    <p class="card-text"><strong>Status:</strong> 
                        <span class="badge <?= $opname['status'] == 0 ? 'bg-warning' : 'bg-success' ?>">
                            <?= $opname['status'] == 0 ? 'Draft' : 'Completed'; ?>
                        </span>
                    </p>
                    <p class="card-text"><strong>Remarks:</strong> <?= $opname['remarks']; ?></p>
                    
                    <div class="d-flex flex-wrap gap-2">
                        <?php if ($opname['status'] == 0) : ?>
                            <a href="/stock-opname/complete/<?= $opname['id']; ?>" class="btn btn-success">
                                <i class="fas fa-check-circle me-1"></i> Complete Opname
                            </a>
                        <?php endif; ?>
                        
                        <a href="/stock-opname/export/<?= $opname['id']; ?>" class="btn btn-secondary">
                            <i class="fas fa-file-excel me-1"></i> Export Report
                        </a>
                        <a href="/stock-opname/export-template/<?= $opname['id']; ?>" class="btn btn-primary">
                            <i class="fas fa-file-export me-1"></i> Export Template
                        </a>
                    </div>
                    
                    <?php if ($opname['status'] == 0 && !empty($items)) : ?>
                        <div class="mt-3">
                            <form action="/stock-opname/delete-all-items/<?= $opname['id'] ?>" method="post" 
                                onsubmit="return confirm('Are you sure you want to delete ALL items? This action cannot be undone!')">
                                <?= csrf_field() ?>
                                <input type="hidden" name="_method" value="DELETE">
                                <button type="submit" class="btn btn-danger">
                                    <i class="fas fa-trash-alt me-1"></i> Delete All Items
                                </button>
                                <small class="text-muted ms-2">This will remove all <?= count($items) ?> items</small>
                            </form>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Import Data Card -->
            <div class="card mb-3">
                <div class="card-body">
                    <h5 class="card-title">Import Data</h5>
                    <form action="/stock-opname/import/<?= $opname['id']; ?>" method="post" enctype="multipart/form-data">
                        <div class="row g-3 align-items-center">
                            <div class="col-md-6">
                                <input type="file" class="form-control" name="excel_file" required>
                            </div>
                            <div class="col-md-6">
                                <button type="submit" class="btn btn-primary me-2">
                                    <i class="fas fa-upload me-1"></i> Import
                                </button>
                                <a href="/stock-opname/download-template" class="btn btn-outline-primary">
                                    <i class="fas fa-download me-1"></i> Download Template
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Opname Items Card -->
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="card-title mb-0">Opname Items</h5>
                        <span class="badge bg-primary"><?= count($items) ?> items</span>
                    </div>
                    
                    <?php if ($opname['status'] == 0) : ?>
                    <form action="/stock-opname/add-item" method="post" class="mb-4">
                        <?= csrf_field(); ?>
                        <input type="hidden" name="opname_id" value="<?= $opname['id']; ?>">
                        <div class="row g-3 align-items-end">
                            <div class="col-md-5">
                                <label for="material_id" class="form-label">Material</label>
                                <select class="form-select select2-material" id="material_id" name="material_id" required>
                                    <option value="">Select Material</option>
                                    <?php foreach ($materials as $material) : ?>
                                        <option value="<?= $material['id']; ?>" 
                                                data-kode="<?= $material['kode']; ?>"
                                                data-name="<?= $material['name']; ?>">
                                            <?= $material['kode']; ?> - <?= $material['name']; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="jumlah_akhir" class="form-label">Counted Quantity</label>
                                <input type="number" step="0.01" class="form-control" id="jumlah_akhir" name="jumlah_akhir" placeholder="0.00" required>
                            </div>
                            <div class="col-md-4">
                                <button type="submit" class="btn btn-primary w-100 h-100">
                                    <i class="fas fa-plus me-1"></i> Add Item
                                </button>
                            </div>
                        </div>
                    </form>
                    <?php endif; ?>

                    <div class="table-responsive">
                        <table id="opnameItemsTable" class="table table-striped table-hover" style="width:100%">
                            <thead class="table-light">
                                <tr>
                                    <th>No</th>
                                    <th>Material Code</th>
                                    <th>Material Name</th>
                                    <th>System Qty</th>
                                    <th>Counted Qty</th>
                                    <th>Difference</th>
                                    <?php if ($opname['status'] == 0) : ?>
                                        <th>Actions</th>
                                    <?php endif; ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($items as $i => $item) : ?>
                                    <?php
                                    $difference = $item['jumlah_akhir'] - $item['jumlah_awal'];
                                    $differenceClass = $difference > 0 ? 'text-success' : ($difference < 0 ? 'text-danger' : 'text-muted');
                                    ?>
                                    <tr>
                                        <td><?= $i + 1 ?></td>
                                        <td><?= $item['material_code'] ?></td>
                                        <td><?= $item['material_name'] ?></td>
                                        <td><?= number_format($item['jumlah_awal'], 2) ?></td>
                                        <td><?= number_format($item['jumlah_akhir'], 2) ?></td>
                                        <td class="<?= $differenceClass ?>">
                                            <?= $difference > 0 ? '+' : '' ?><?= number_format($difference, 2) ?>
                                        </td>
                                        <?php if ($opname['status'] == 0) : ?>
                                            <td>
                                                <form action="/stock-opname/delete-item/<?= $item['id'] ?>" method="post" class="d-inline">
                                                    <?= csrf_field() ?>
                                                    <input type="hidden" name="_method" value="DELETE">
                                                    <button type="submit" class="btn btn-sm btn-danger" 
                                                        onclick="return confirm('Are you sure to delete this item?')"
                                                        title="Delete Item">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        <?php endif; ?>
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

<?= $this->endSection(); ?>

<?= $this->section('scripts'); ?>
<!-- DataTables CSS -->
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.bootstrap5.min.css">

<!-- DataTables JS -->
<script type="text/javascript" src="<?=base_url('assets')?>/datatables/DataTables-1.13.1/js/jquery.dataTables.min.js"></script>

<!-- Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<style>
    /* Custom Select2 styling */
    .select2-container--bootstrap-5 .select2-selection {
        min-height: 38px;
        padding: 5px 0.375rem;
        font-size: 1rem;
        border: 1px solid #ced4da;
    }
    
    .select2-container--bootstrap-5 .select2-selection--single .select2-selection__rendered {
        padding-left: 0;
    }
    
    .select2-container--bootstrap-5 .select2-dropdown {
        border: 1px solid #ced4da;
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    }
    
    .select2-results__option {
        padding: 0.5rem 1rem;
    }
    
    .select2-results__option--highlighted {
        background-color: #0d6efd;
    }
    
    .material-option {
        display: flex;
        flex-direction: column;
    }
    
    .material-code {
        font-weight: bold;
    }
    
    .material-name {
        font-size: 0.875rem;
        color: #6c757d;
    }
</style>

<script>
$(document).ready(function() {
    // Initialize DataTable
    $('#opnameItemsTable').DataTable({
        dom: '<"top"Bf>rt<"bottom"lip><"clear">',
        buttons: [
            {
                extend: 'excel',
                text: '<i class="fas fa-file-excel me-1"></i> Export Excel',
                className: 'btn btn-success btn-sm',
                title: 'StockOpname_<?= $opname["code"] ?>',
                exportOptions: {
                    columns: ':visible'
                }
            },
            {
                extend: 'print',
                text: '<i class="fas fa-print me-1"></i> Print',
                className: 'btn btn-secondary btn-sm',
                title: 'Stock Opname - <?= $opname["code"] ?>',
                exportOptions: {
                    columns: ':visible'
                },
                customize: function (win) {
                    $(win.document.body).find('table')
                        .addClass('compact')
                        .css('font-size', 'inherit');
                }
            },
            {
                extend: 'colvis',
                text: '<i class="fas fa-columns me-1"></i> Columns',
                className: 'btn btn-info btn-sm'
            }
        ],
        columnDefs: [
            { orderable: false, targets: <?= $opname['status'] == 0 ? '[6]' : '[5]' ?> }
        ],
        language: {
            search: "_INPUT_",
            searchPlaceholder: "Search...",
            lengthMenu: "Show _MENU_ items per page",
            zeroRecords: "No matching items found",
            info: "Showing _START_ to _END_ of _TOTAL_ items",
            infoEmpty: "No items available",
            infoFiltered: "(filtered from _MAX_ total items)",
            paginate: {
                first: "First",
                last: "Last",
                next: "Next",
                previous: "Previous"
            }
        },
        responsive: true,
        stateSave: true,
        order: [[0, 'asc']]
    });

    // Initialize Select2 for material dropdown
    $('.select2-material').select2({
        theme: 'bootstrap-5',
        width: '100%',
        placeholder: "Search for a material...",
        allowClear: true,
        templateResult: formatMaterial,
        templateSelection: formatMaterialSelection
    });

    // Format how materials are displayed in dropdown
    function formatMaterial(material) {
        if (!material.id) return material.text;
        
        var $container = $(
            '<div class="material-option">' +
                '<span class="material-code">' + $(material.element).data('kode') + '</span>' +
                '<span class="material-name">' + $(material.element).data('name') + '</span>' +
            '</div>'
        );
        
        return $container;
    }

    // Format how selected material is displayed
    function formatMaterialSelection(material) {
        if (!material.id) return material.text;
        return $(material.element).data('kode') + ' - ' + $(material.element).data('name');
    }

    // Focus search field when dropdown is opened
    $(document).on('select2:open', () => {
        document.querySelector('.select2-search__field').focus();
    });
});
</script>
<?= $this->endSection(); ?>