<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<script src="https://code.jquery.com/jquery-3.7.0.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<!-- Menambahkan html2canvas untuk konversi QR ke gambar -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<style>
    /* Perbaikan untuk dropdown Select2 di dalam modal */
    .select2-container--open {
        z-index: 9999 !important;
    }
    .select2-dropdown {
        z-index: 9999 !important;
    }
    .select2-container {
        z-index: 9999 !important;
    }
    
    /* Style lainnya */
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
    /* QR Code Preview Styling */
    #qrcode {
        display: flex;
        justify-content: center;
        margin: 15px 0;
    }
    /* Print-specific styles */
    @media print {
        body * {
            visibility: hidden;
        }
        .qrcode-sheet, .qrcode-sheet * {
            visibility: visible;
        }
        .qrcode-sheet {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
        }
        .qrcode-container {
            width: 100%;
            text-align: center;
        }
        .qrcode-label {
            font-size: 10px;
            margin-top: 2px;
            font-weight: bold;
        }
        .no-print {
            display: none !important;
        }
        /* Ensure QR codes are visible when printing */
        #qrcodePrintArea, #qrcodePrintArea * {
            display: block !important;
            visibility: visible !important;
        }
        
        /* A4 page setup */
        @page {
            size: A4;
            margin: 0;
        }
        body {
            margin: 0;
            padding: 0;
        }
    }
    /* Style for print template */
    .qrcode-print-item {
        width: 46mm; /* A8 width is 74mm but we need to fit 4 in a row with margins */
        height: 36mm; /* A8 height is 52mm but we need to fit 4 in a column with margins */
        display: inline-block;
        text-align: center;
        padding: 2mm;
        box-sizing: border-box;
        page-break-inside: avoid;
        border: 1px dotted #ccc;
        margin: 2mm;
    }
    .print-only {
        display: none;
    }
    .a4-page {
        width: 210mm;
        height: 297mm;
        padding: 5mm;
        box-sizing: border-box;
        page-break-after: always;
        position: relative;
    }
    .qr-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        grid-template-rows: repeat(4, 1fr);
        gap: 2mm;
        width: 100%;
        height: 100%;
    }
    .qr-item {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        border: 1px dotted #ccc;
        padding: 1mm;
    }
    /* Styling untuk QR Code A8 */
    .a8-qrcode {
        width: 74mm;
        height: 52mm;
        padding: 5mm;
        box-sizing: border-box;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        border: 1px solid #000;
        background: white;
        font-family: Arial, sans-serif;
    }
    .a8-qrcode canvas {
        width: 45mm !important;
        height: 45mm !important;
    }
    .a8-code {
        font-size: 12px;
        font-weight: bold;
        margin-top: 2mm;
        text-align: center;
    }
    .a8-name {
        font-size: 10px;
        text-align: center;
        margin-top: 1mm;
        max-width: 70mm;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .a8-product {
        font-size: 9px;
        text-align: center;
        margin-top: 1mm;
        max-width: 70mm;
        overflow: hidden;
        text-overflow: ellipsis;
        color: #555;
    }
</style>

<div class="container-fluid pt-4 px-4">
    <div class="bg-light text-center rounded p-4">
        <div class="d-flex align-items-center justify-content-between mb-4">
    <h6 class="mb-0">Component Management</h6>
    <div>
        <a href="/component/transactionList" class="btn btn-info me-2"> <i class="fas fa-file me-2"> Doc</i> </a>

        <button type="button" class="btn btn-info me-2" data-bs-toggle="modal" data-bs-target="#exportModal">
            <i class="fas fa-file-excel me-2"></i>Export Excel
        </button>
        <button type="button" class="btn btn-success me-2" data-bs-toggle="modal" data-bs-target="#printQRModal">
            <i class="fas fa-qrcode me-2"></i>Print QR Codes
        </button>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#componentModal">
            <i class="fas fa-plus me-2"></i>Add Component
        </button>
    </div>
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
            <table id="componentTable" class="table table-bordered table-hover" style="width:100%">
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
                            <button class="btn btn-sm btn-secondary qr-btn" 
                                    data-id="<?=$component['id']?>" 
                                    data-code="<?=esc($component['kode'])?>" 
                                    data-name="<?=esc($component['nama'])?>" 
                                    data-product="<?=esc($component['product_name'] ?? '')?>"
                                    title="Generate QR Code">
                                <i class="fas fa-qrcode"></i>
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

        <script>
            $(document).ready(function() {
                $('#componentTable').DataTable({
                    responsive: true,
                    language: {
                        search: "Cari:",
                        lengthMenu: "Tampilkan _MENU_ data per halaman",
                        zeroRecords: "Data tidak ditemukan",
                        info: "Menampilkan halaman _PAGE_ dari _PAGES_",
                        infoEmpty: "Tidak ada data yang tersedia",
                        infoFiltered: "(difilter dari _MAX_ total data)",
                        paginate: {
                            first: "Pertama",
                            last: "Terakhir",
                            next: "Selanjutnya",
                            previous: "Sebelumnya"
                        }
                    }
                });
            });
        </script>
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

<!-- Print QR Code Modal -->
<div class="modal fade" id="printQRModal" tabindex="-1" aria-labelledby="printQRModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="printQRModalLabel">Print QR Codes</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="componentSelect" class="form-label">Select Component</label>
                            <select class="form-select select2-print" id="componentSelect">
                                <option value="">-- Select Component --</option>
                                <?php foreach($components as $component): ?>
                                    <option value="<?= $component['id'] ?>" 
                                            data-code="<?= esc($component['kode']) ?>" 
                                            data-name="<?= esc($component['nama']) ?>"
                                            data-product="<?= esc($component['product_name'] ?? '') ?>">
                                        <?= esc($component['kode']) ?> - <?= esc($component['nama']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="qtyInput" class="form-label">Number of QR Codes to Print</label>
                            <input type="number" class="form-control" id="qtyInput" min="1" max="100" value="16">
                        </div>
                        <div class="mb-3">
                            <label for="qrSize" class="form-label">QR Code Size (px)</label>
                            <input type="number" class="form-control" id="qrSize" min="50" max="300" value="150">
                        </div>
                        <button type="button" class="btn btn-primary" id="generateQRBtn">
                            <i class="fas fa-qrcode me-2"></i>Generate QR Code
                        </button>
                        <button type="button" class="btn btn-success ms-2 no-print" id="printQRBtn" disabled>
                            <i class="fas fa-print me-2"></i>Print QR Codes
                        </button>
                        <!-- Tombol Download QR Code sebagai JPG dengan ukuran A8 -->
                        <button type="button" class="btn btn-info ms-2 no-print" id="downloadQRBtn" disabled>
                            <i class="fas fa-download me-2"></i>Download A8 JPG
                        </button>
                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header bg-light">
                                <h6 class="mb-0">QR Code Preview</h6>
                            </div>
                            <div class="card-body text-center">
                                <div id="qrcode"></div>
                                <div id="qrInfo" class="mt-2">
                                    <p class="mb-1 fw-bold" id="qrCodeText"></p>
                                    <p class="mb-0 text-muted" id="qrNameText"></p>
                                    <p class="mb-0 text-muted" id="qrProductText"></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- QR Code Print Area (Hidden) -->
<div id="qrcodePrintArea" class="d-none"></div>

<!-- Stock Modal -->
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
                            <label for="document_number" class="form-label">Document Number</label>
                            <input type="text" class="form-control" id="document_number" name="document_number" step="0.01" min="0">
                        </div><div class="col-md-6">
                            <label for="responsible_person" class="form-label">Document Author</label>
                            <input type="text" class="form-control" id="responsible_person" name="responsible_person" step="0.01" min="0">
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
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
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

<!-- Tambahkan di bagian atas file component.php, setelah styles -->
<div class="modal fade" id="exportModal" tabindex="-1" aria-labelledby="exportModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title" id="exportModalLabel">Export Stock Data</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="exportForm" action="<?= site_url('component/exportExcel') ?>" method="post">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="startDate" class="form-label">Start Date</label>
                        <input type="date" class="form-control" id="startDate" name="start_date" required>
                    </div>
                    <div class="mb-3">
                        <label for="endDate" class="form-label">End Date</label>
                        <input type="date" class="form-control" id="endDate" name="end_date" required>
                    </div>
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" id="includeTransactions" name="include_transactions" checked>
                        <label class="form-check-label" for="includeTransactions">
                            Include Transaction History
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Export Excel</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Tambahkan tombol export di bagian header -->


<!-- Perbaikan tabel riwayat transaksi di stockModal -->

<script>
$(document).ready(function() {
    // Initialize Select2 with Bootstrap 5 theme untuk modal component
    $('#componentModal .select2').select2({
        theme: 'bootstrap-5',
        placeholder: 'Select an option',
        allowClear: true,
        dropdownParent: $('#componentModal')
    });

    // Initialize Select2 dengan class yang berbeda untuk modal print
    $('.select2-print').select2({
        theme: 'bootstrap-5',
        placeholder: 'Select an option',
        allowClear: true,
        dropdownParent: $('#printQRModal')
    });

    // Clear form when modal is hidden for adding new
    $('#componentModal').on('hidden.bs.modal', function() {
        if($('#component_id').val() === '') {
            $('#componentForm')[0].reset();
            $('#product_id').val(null).trigger('change');
        }
    });

    // QR Code Generation
    $('#generateQRBtn').click(function() {
        const componentId = $('#componentSelect').val();
        const componentCode = $('#componentSelect option:selected').data('code');
        const componentName = $('#componentSelect option:selected').data('name');
        const componentProduct = $('#componentSelect option:selected').data('product');
        const qrSize = $('#qrSize').val();
        
        if (!componentId) {
            alert('Please select a component first');
            return;
        }
        
        // Generate QR code data (you can customize this)
        const qrData = `${componentCode}`;
        
        // Clear previous QR code
        $('#qrcode').empty();
        
        // Generate new QR code
        new QRCode(document.getElementById("qrcode"), {
            text: qrData,
            width: parseInt(qrSize),
            height: parseInt(qrSize),
            colorDark : "#000000",
            colorLight : "#ffffff",
            correctLevel : QRCode.CorrectLevel.H
        });
        
        // Update info text
        $('#qrCodeText').text(componentCode);
        $('#qrNameText').text(componentName);
        $('#qrProductText').text(componentProduct || 'No product assigned');
        
        // Enable print and download buttons
        $('#printQRBtn').prop('disabled', false);
        $('#downloadQRBtn').prop('disabled', false);
    });
    
    // Print QR Codes
    $('#printQRBtn').click(function() {
        const componentId = $('#componentSelect').val();
        const componentCode = $('#componentSelect option:selected').data('code');
        const componentName = $('#componentSelect option:selected').data('name');
        const componentProduct = $('#componentSelect option:selected').data('product');
        const qty = $('#qtyInput').val();
        
        if (!componentId) {
            alert('Please select a component first');
            return;
        }
        
        // Generate QR code data
        const qrData = `${componentCode}`;
        
        // Prepare print area
        const printArea = $('#qrcodePrintArea');
        printArea.empty().removeClass('d-none');
        
        // Calculate how many A4 pages we need (16 QR codes per page)
        const qrPerPage = 16;
        const totalPages = Math.ceil(qty / qrPerPage);
        
        // Generate A4 pages with QR codes
        for (let page = 0; page < totalPages; page++) {
            const a4Page = $('<div>').addClass('a4-page');
            const qrGrid = $('<div>').addClass('qr-grid');
            
            // Calculate how many QR codes to put on this page
            const qrOnThisPage = Math.min(qrPerPage, qty - (page * qrPerPage));
            
            // Generate QR codes for this page
            for (let i = 0; i < qrOnThisPage; i++) {
                const qrItem = $('<div>').addClass('qr-item');
                
                // Create a unique container for each QR code
                const qrContainerId = `qrcode-${page}-${i}`;
                const qrDiv = $('<div>').attr('id', qrContainerId);
                qrItem.append(qrDiv);
                
                const codeText = $('<div>').addClass('fw-bold mb-1').text(componentCode).css('font-size', '10px');
                qrItem.append(codeText);
                
                const nameText = $('<div>').text(componentName).css('font-size', '8px');
                qrItem.append(nameText);
                
                if (componentProduct) {
                    const productText = $('<div>').text(componentProduct).css('font-size', '7px').css('color', '#555');
                    qrItem.append(productText);
                }
                
                qrGrid.append(qrItem);
                
                // Generate QR code after the element is added to the DOM
                setTimeout(() => {
                    try {
                        new QRCode(document.getElementById(qrContainerId), {
                            text: qrData,
                            width: 80,
                            height: 80,
                            colorDark : "#000000",
                            colorLight : "#ffffff",
                            correctLevel : QRCode.CorrectLevel.H
                        });
                    } catch (error) {
                        console.error("Error generating QR code:", error);
                    }
                }, 10);
            }
            
            a4Page.append(qrGrid);
            printArea.append(a4Page);
        }
        
        // Tunggu sebentar untuk memastikan semua QR code tergenerate
        setTimeout(() => {
            // Store original document contents
            const originalContents = document.body.innerHTML;
            
            // Show only the print area for printing
            const printContents = printArea[0].innerHTML;
            document.body.innerHTML = `
                <!DOCTYPE html>
                <html>
                <head>
                    <title>Print QR Codes</title>
                    <meta charset="utf-8">
                    <style>
                        @page {
                            size: A4;
                            margin: 0;
                        }
                        body {
                            margin: 0;
                            padding: 0;
                            font-family: Arial, sans-serif;
                        }
                        .a4-page {
                            width: 210mm;
                            height: 297mm;
                            padding: 5mm;
                            box-sizing: border-box;
                            page-break-after: always;
                        }
                        .qr-grid {
                            display: grid;
                            grid-template-columns: repeat(4, 1fr);
                            grid-template-rows: repeat(4, 1fr);
                            gap: 2mm;
                            width: 100%;
                            height: 100%;
                        }
                        .qr-item {
                            display: flex;
                            flex-direction: column;
                            align-items: center;
                            justify-content: center;
                            border: 1px dotted #ccc;
                            padding: 1mm;
                        }
                        .qr-item canvas, .qr-item img {
                            max-width: 100%;
                            height: auto;
                        }
                    </style>
                </head>
                <body>${printContents}</body>
                </html>
            `;
            
            // Print the page
            window.print();
            
            // Restore original contents
            document.body.innerHTML = originalContents;
            
            // Re-initialize event listeners
            initializeEventListeners();
            
            // Hide the print area again
            printArea.addClass('d-none');
        }, 1000); // Tunggu 1 detik untuk memastikan semua QR code selesai digenerate
    });

    // Download QR Code as JPG dengan ukuran A8
    $('#downloadQRBtn').click(function() {
        const componentCode = $('#componentSelect option:selected').data('code');
        const componentName = $('#componentSelect option:selected').data('name');
        const componentProduct = $('#componentSelect option:selected').data('product');
        
        // Buat elemen untuk QR Code A8
        const a8Element = document.createElement('div');
        a8Element.className = 'a8-qrcode';
        
        // Tambahkan QR code ke elemen A8
        const qrDiv = document.createElement('div');
        qrDiv.id = 'a8-qrcode-container';
        a8Element.appendChild(qrDiv);
        
        // Tambahkan teks kode, nama, dan product
        const codeText = document.createElement('div');
        codeText.className = 'a8-code';
        codeText.textContent = componentCode;
        a8Element.appendChild(codeText);
        
        const nameText = document.createElement('div');
        nameText.className = 'a8-name';
        nameText.textContent = componentName;
        a8Element.appendChild(nameText);
        
        if (componentProduct) {
            const productText = document.createElement('div');
            productText.className = 'a8-product';
            productText.textContent = componentProduct;
            a8Element.appendChild(productText);
        }
        
        // Sembunyikan elemen sementara dari viewport
        a8Element.style.position = 'fixed';
        a8Element.style.left = '-1000px';
        a8Element.style.top = '-1000px';
        document.body.appendChild(a8Element);
        
        // Generate QR code di dalam elemen A8
        new QRCode(document.getElementById('a8-qrcode-container'), {
            text: componentCode,
            width: 100,
            height: 100,
            colorDark: "#000000",
            colorLight: "#ffffff",
            correctLevel: QRCode.CorrectLevel.H
        });
        
        // Tunggu sebentar untuk memastikan QR code tergenerate
        setTimeout(() => {
            // Gunakan html2canvas untuk mengonversi elemen A8 ke gambar
            html2canvas(a8Element, {
                scale: 3, // Skala tinggi untuk kualitas gambar yang baik
                width: 74 * 3.78, // Konversi mm ke px (1mm = 3.78px)
                height: 52 * 3.78, // Konversi mm ke px (1mm = 3.78px)
                useCORS: true
            }).then(canvas => {
                // Konversi canvas ke JPG data URL
                const dataURL = canvas.toDataURL('image/jpeg', 1.0);
                
                // Buat link sementara untuk mendownload
                const link = document.createElement('a');
                link.download = `QRCode_${componentCode}_A8.jpg`;
                link.href = dataURL;
                
                // Trigger download
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
                
                // Hapus elemen A8 dari DOM
                document.body.removeChild(a8Element);
            });
        }, 300);
    });
});

// Re-initialize event listeners after printing
function initializeEventListeners() {
    $('#printQRBtn').click(function() {
        const componentId = $('#componentSelect').val();
        const componentCode = $('#componentSelect option:selected').data('code');
        const componentName = $('#componentSelect option:selected').data('name');
        const componentProduct = $('#componentSelect option:selected').data('product');
        const qty = $('#qtyInput').val();
        
        if (!componentId) {
            alert('Please select a component first');
            return;
        }
        
        const qrData = `${componentCode}`;
        const printArea = $('#qrcodePrintArea');
        printArea.empty().removeClass('d-none');
        
        const qrPerPage = 16;
        const totalPages = Math.ceil(qty / qrPerPage);
        
        for (let page = 0; page < totalPages; page++) {
            const a4Page = $('<div>').addClass('a4-page');
            const qrGrid = $('<div>').addClass('qr-grid');
            
            const qrOnThisPage = Math.min(qrPerPage, qty - (page * qrPerPage));
            
            for (let i = 0; i < qrOnThisPage; i++) {
                const qrItem = $('<div>').addClass('qr-item');
                
                const qrContainerId = `qrcode-${page}-${i}`;
                const qrDiv = $('<div>').attr('id', qrContainerId);
                qrItem.append(qrDiv);
                
                const codeText = $('<div>').addClass('fw-bold mb-1').text(componentCode).css('font-size', '10px');
                qrItem.append(codeText);
                
                const nameText = $('<div>').text(componentName).css('font-size', '8px');
                qrItem.append(nameText);
                
                if (componentProduct) {
                    const productText = $('<div>').text(componentProduct).css('font-size', '7px').css('color', '#555');
                    qrItem.append(productText);
                }
                
                qrGrid.append(qrItem);
                
                // Generate QR code after the element is added to the DOM
                setTimeout(() => {
                    try {
                        new QRCode(document.getElementById(qrContainerId), {
                            text: qrData,
                            width: 80,
                            height: 80,
                            colorDark : "#000000",
                            colorLight : "#ffffff",
                            correctLevel : QRCode.CorrectLevel.H
                        });
                    } catch (error) {
                        console.error("Error generating QR code:", error);
                    }
                }, 100);
            }
            
            a4Page.append(qrGrid);
            printArea.append(a4Page);
        }
        
        // Tunggu sebentar untuk memastikan semua QR code tergenerate
        setTimeout(() => {
            const originalContents = document.body.innerHTML;
            const printContents = printArea[0].innerHTML;
            document.body.innerHTML = `
                <!DOCTYPE html>
                <html>
                <head>
                    <title>Print QR Codes</title>
                    <meta charset="utf-8">
                    <style>
                        @page {
                            size: A4;
                            margin: 0;
                        }
                        body {
                            margin: 0;
                            padding: 0;
                            font-family: Arial, sans-serif;
                        }
                        .a4-page {
                            width: 210mm;
                            height: 297mm;
                            padding: 5mm;
                            box-sizing: border-box;
                            page-break-after: always;
                        }
                        .qr-grid {
                            display: grid;
                            grid-template-columns: repeat(4, 1fr);
                            grid-template-rows: repeat(4, 1fr);
                            gap: 2mm;
                            width: 100%;
                            height: 100%;
                        }
                        .qr-item {
                            display: flex;
                            flex-direction: column;
                            align-items: center;
                            justify-content: center;
                            border: 1px dotted #ccc;
                            padding: 1mm;
                        }
                        .qr-item canvas, .qr-item img {
                            max-width: 100%;
                            height: auto;
                        }
                    </style>
                </head>
                <body>${printContents}</body>
                </html>
            `;
            
            window.print();
            document.body.innerHTML = originalContents;
            initializeEventListeners();
            printArea.addClass('d-none');
        }, 500);
    });
}

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

// Add this for individual QR code generation
$(document).on('click', '.qr-btn', function() {
    const id = $(this).data('id');
    const code = $(this).data('code');
    const name = $(this).data('name');
    const product = $(this).data('product');
    
    // Set the values in the modal
    $('#componentSelect').val(id).trigger('change');
    $('#qtyInput').val(16); // Default to one A4 page
    
    // Open the modal
    $('#printQRModal').modal('show');
    
    // Generate QR code immediately
    setTimeout(function() {
        $('#generateQRBtn').click();
    }, 500);
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
            alert(response.message);
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
                        <td class="text-center">
                            <button class="btn btn-sm btn-danger" onclick="deleteTransaction(${transaction.id}, ${componentId})" title="Delete Transaction">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                `;
            });
        } else {
            html = '<tr><td colspan="6" class="text-center">No transactions found</td></tr>';
        }
        $('#transactionHistory tbody').html(html);
    }, 'json');
}

// Handle stock form submission
$('#stockForm').submit(function(e) {
    e.preventDefault();
    
    var formData = $(this).serializeArray();
    var componentId = $('#stock_component_id').val();
    var quantity = $('input[name="quantity"]').val();
    
    // Membuat array items dengan struktur yang diinginkan
    var requestData = {
        items: [
            {
                id: componentId,
                quantity: quantity
            }
        ],
        document_number: $('input[name="document_number"]').val(),
        responsible_person: $('input[name="responsible_person"]').val(),
        type: $('select[name="type"]').val(),
        reference: $('input[name="reference"]').val(),
        notes: $('textarea[name="notes"]').val()
    };
    
    var saveButton = $('#saveStockButton');
    
    saveButton.prop('disabled', true);
    saveButton.find('.spinner-border').removeClass('d-none');
    
    // Kirim sebagai JSON
    $.ajax({
        url: "<?= site_url('component/saveTransaction') ?>",
        type: 'POST',
        contentType: 'application/json',
        data: JSON.stringify(requestData),
        dataType: 'json',
        success: function(response) {
            if (response.status) {
                alert(response.message);
                // Refresh stock info and history
                loadTransactionHistory(componentId);
                $.get("<?= site_url('component/getStock/') ?>" + componentId, function(stockResponse) {
                    if (stockResponse.status) {
                        $('#current_stock').val(stockResponse.data.quantity);
                    }
                }, 'json');
            } else {
                alert(response.message);
            }
        },
        error: function(xhr, status, error) {
            alert('Error: ' + error);
        },
        complete: function() {
            saveButton.prop('disabled', false);
            saveButton.find('.spinner-border').addClass('d-none');
        }
    });
});

// Tambahkan fungsi untuk menghapus transaksi
function deleteTransaction(transactionId, componentId) {
    if (confirm('Are you sure you want to delete this transaction? This will reverse the stock change.')) {
        // Show loading state jika ada
        $('.delete-btn[data-id="' + transactionId + '"]').prop('disabled', true).text('Deleting...');
        
        $.post("<?= site_url('component/deleteTransaction') ?>", {
            id: transactionId,
            component_id: componentId,
            _token: "<?= csrf_hash() ?>"
        }, function(response) {
            if (response.status) {
                alert(response.message);
                // Refresh stock info and history
                loadTransactionHistory(componentId);
                $.get("<?= site_url('component/getStock/') ?>" + componentId, function(stockResponse) {
                    if (stockResponse.status) {
                        $('#current_stock').val(stockResponse.data.quantity);
                    }
                    // Optional: Show success message dengan toast/notification
                    showNotification('Transaction deleted successfully', 'success');
                }, 'json');
            } else {
                alert(response.message);
                // Optional: Show error message
                showNotification(response.message, 'error');
            }
        }, 'json')
        .fail(function(xhr, status, error) {
            alert('Error: ' + error);
            showNotification('Failed to delete transaction', 'error');
        })
        .always(function() {
            // Reset button state
            $('.delete-btn[data-id="' + transactionId + '"]').prop('disabled', false).text('Delete');
        });
    }
}

// Optional: Function untuk show notification yang lebih user friendly
function showNotification(message, type = 'info') {
    // Jika menggunakan Bootstrap toast
    if (typeof bootstrap !== 'undefined') {
        const toastEl = document.getElementById('notificationToast');
        const toastBody = toastEl.querySelector('.toast-body');
        const toast = new bootstrap.Toast(toastEl);
        
        toastBody.textContent = message;
        toastEl.className = `toast ${type === 'success' ? 'bg-success text-white' : type === 'error' ? 'bg-danger text-white' : 'bg-info text-white'}`;
        toast.show();
    } else {
        // Fallback ke alert biasa
        console.log(`${type.toUpperCase()}: ${message}`);
    }
}

// Perbarui fungsi loadTransactionHistory untuk menambahkan tombol delete

$('#stockForm').on('submit', function(e) {
    // Hanya submit jika tombol yang ditekan adalah tombol submit form
    if (!$(e.originalEvent.submitter).hasClass('delete-transaction-btn')) {
        return true;
    }
    e.preventDefault();
    return false;
});
// Set tanggal default untuk form export
$(document).ready(function() {
    // Set tanggal awal ke awal bulan ini
    const today = new Date();
    const firstDay = new Date(today.getFullYear(), today.getMonth(), 1);
    const lastDay = new Date(today.getFullYear(), today.getMonth() + 1, 0);
    
    $('#startDate').val(firstDay.toISOString().split('T')[0]);
    $('#endDate').val(lastDay.toISOString().split('T')[0]);
});
</script>