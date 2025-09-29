
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Product Tracking</h3>
        <div class="card-tools">
            <a href="<?= base_url('product/printBom/' . $productId . '/' . $finishingId) ?>" 
               class="btn btn-primary" target="_blank">
                <i class="fas fa-print"></i> Print BOM
            </a>
            <a href="<?= base_url('productstock/view/' . $productId) ?>" 
               class="btn btn-info">
                <i class="fas fa-boxes"></i> View Stock
            </a>
        </div>
    </div>
    <div class="card-body">
        <!-- Tabel 1: Product Information -->
        <div class="row mb-4">
            <div class="col-md-12">
                <h4>Product Information</h4>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Product Code</th>
                            <th>Product Name</th>
                            <th>HS Code</th>
                            <th>Finishing</th>
                            <th>Dimensions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><?= $productData['kode'] ?? 'N/A' ?></td>
                            <td><?= $productData['nama'] ?? 'N/A' ?></td>
                            <td><?= $productData['hs_code'] ?? 'N/A' ?></td>
                            <td><?= $productData['finishing_name'] ?? 'N/A' ?></td>
                            <td>
                                <?= $productData['length'] ?? '0' ?> x 
                                <?= $productData['width'] ?? '0' ?> x 
                                <?= $productData['height'] ?? '0' ?> 
                                (CBM: <?= $productData['cbm'] ?? '0' ?>)
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <h4>Proforma Invoice History</h4>
                
                <!-- Filter Form -->
                <div class="row mb-3">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Start Date</label>
                            <input type="date" id="startDate" class="form-control">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>End Date</label>
                            <input type="date" id="endDate" class="form-control">
                        </div>
                    </div>
                    <div class="col-md-6" style="padding-top: 32px;">
                        <button type="button" id="filterBtn" class="btn btn-primary">
                            <i class="fas fa-filter"></i> Filter
                        </button>
                        <button type="button" id="resetBtn" class="btn btn-secondary">
                            <i class="fas fa-redo"></i> Reset
                        </button>
                    </div>
                </div>

                <table class="table table-bordered table-striped" id="piTable">
                    <thead>
                        <tr>
                            <th>Invoice Number</th>
                            <th>Invoice Date</th>
                            <th>Customer</th>
                            <th>Quantity</th>
                            <th>Unit Price</th>
                            <th>ETD</th>
                            <th>ETA</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody id="piTableBody">
                        <?php if (!empty($piHistory)): ?>
                            <?php foreach ($piHistory as $pi): ?>
                                <tr>
                                    <td>
                                        <a href="<?= base_url('proformainvoice/piDoc/' . $pi['invoice_id']) ?>" 
                                           target="_blank" class="text-primary">
                                            <?= $pi['invoice_number'] ?>
                                        </a>
                                    </td>
                                    <td><?= $pi['invoice_date'] ?></td>
                                    <td><?= $pi['customer_name'] ?></td>
                                    <td><?= $pi['quantity'] ?> <?= $pi['unit'] ?></td>
                                    <td><?= number_format($pi['unit_price'], 2) ?></td>
                                    <td><?= $pi['etd'] ?? 'N/A' ?></td>
                                    <td><?= $pi['eta'] ?? 'N/A' ?></td>
                                    <td>
                                        <?php 
                                        $statusBadge = match($pi['status']) {
                                            1 => 'success',
                                            2 => 'warning',
                                            default => 'secondary'
                                        };
                                        $statusText = match($pi['status']) {
                                            1 => 'Completed',
                                            2 => 'Pending',
                                            default => 'Draft'
                                        };
                                        ?>
                                        <span class="badge badge-<?= $statusBadge ?>"><?= $statusText ?></span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" class="text-center">No Proforma Invoice history found</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Tabel 3: Material Information -->
        <div class="row mt-4">
            <div class="col-md-12">
                <h4>Material</h4>
                <table class="table table-bordered table-striped" id="materialTable">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Material Code</th>
                            <th>Material Name</th>
                            <th>Module</th>
                            <th>Kite</th>
                            <th>Unit</th>
                            <th>Usage</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="materialTableBody">
                        <!-- Data akan diisi via AJAX -->
                        <tr>
                            <td colspan="8" class="text-center">Loading materials...</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Load material data on page load
    loadMaterialData();

    // Filter functionality for PI history
    $('#filterBtn').click(function() {
        const startDate = $('#startDate').val();
        const endDate = $('#endDate').val();
        const productId = '<?= $productId ?>';
        const finishingId = '<?= $finishingId ?>';

        // Show loading
        $('#piTableBody').html('<tr><td colspan="8" class="text-center">Loading...</td></tr>');

        $.get('<?= base_url('report/getPiHistoryByDate') ?>', {
            productId: productId,
            finishingId: finishingId,
            startDate: startDate,
            endDate: endDate
        }, function(response) {
            if (response.status === 'success') {
                updatePiTable(response.data);
            } else {
                $('#piTableBody').html('<tr><td colspan="8" class="text-center">Error loading data</td></tr>');
            }
        }).fail(function() {
            $('#piTableBody').html('<tr><td colspan="8" class="text-center">Error loading data</td></tr>');
        });
    });

    // Reset filter
    $('#resetBtn').click(function() {
        $('#startDate').val('');
        $('#endDate').val('');
        $('#filterBtn').click();
    });

    // Function to load material data
    function loadMaterialData() {
    const productId = '<?= $productId ?>';
    const finishingId = '<?= $finishingId ?>';

    $.get('<?= base_url('report/material/') ?>' + productId + '/' + finishingId, 
    function(response) {
        console.log('Raw Material response:', response); // Debug log raw response
        
        try {
            // Parse response menjadi JSON object
            const jsonResponse = JSON.parse(response);
            console.log('Parsed Material response:', jsonResponse); // Debug log parsed response
            
            if (jsonResponse.status === 'success') {
                updateMaterialTable(jsonResponse.data);
            } else {
                $('#materialTableBody').html('<tr><td colspan="8" class="text-center">Error: ' + (jsonResponse.message || 'Unknown error') + '</td></tr>');
            }
        } catch (e) {
            console.error('Error parsing JSON:', e);
            $('#materialTableBody').html('<tr><td colspan="8" class="text-center">Error parsing response data</td></tr>');
        }
    }).fail(function(xhr, status, error) {
        console.error('Error loading material data:', error);
        $('#materialTableBody').html('<tr><td colspan="8" class="text-center">Error loading material data: ' + error + '</td></tr>');
    });
}

    function updateMaterialTable(data) {
        const tbody = $('#materialTableBody');
        tbody.empty();

        if (!data.items || data.items.length === 0) {
            tbody.append('<tr><td colspan="8" class="text-center">No material data found</td></tr>');
            return;
        }

        // Add material rows
        data.items.forEach(function(material, index) {
            const row = `
<tr>
    <td>${index + 1}</td>
    <td>${material.kode || 'N/A'}</td>
    <td>${material.name || 'N/A'}</td>
    <td>Module ${material.id_modul || 'N/A'}</td>
    <td>
        <span class="badge" style="background-color: ${material.kite === 'KITE' ? '#dc3545' : '#343a40'}; color: white;">
            ${material.kite || 'NON KITE'}
        </span>
    </td>
    <td>${material.satuan_nama || 'N/A'}</td>
    <td>${material.penggunaan || '0'}</td>
    <td>
        <button type="button" class="btn btn-sm btn-info view-material-history" 
                data-material-id="${material.id_material}" 
                data-material-name="${material.name || 'Material'}">
            <i class="fas fa-history"></i> View History
        </button>
    </td>
</tr>
            `;
            tbody.append(row);
        });

        // Add click event for view history buttons
        $('.view-material-history').click(function() {
            const materialId = $(this).data('material-id');
            const materialName = $(this).data('material-name');
            viewMaterialHistory(materialId, materialName);
        });
    }

    function viewMaterialHistory(materialId, materialName) {
        // Show loading modal
        const modal = $(`
            <div class="modal fade" id="materialHistoryModal" tabindex="-1">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Material History - ${materialName}</h5>
                             <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="text-center">
                                <div class="spinner-border" role="status">
                                    <span class="sr-only">Loading...</span>
                                </div>
                                <p>Loading material history...</p>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>
        `);
        
        $('body').append(modal);
        $('#materialHistoryModal').modal('show');

        // Load material history via AJAX
        $.get('<?= base_url('materialhistory/') ?>' + materialId, 
        function(response) {
            $('#materialHistoryModal .modal-body').html(`
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> 
                    Material history page for <strong>${materialName}</strong> (ID: ${materialId}) is under development.
                </div>
                <div class="text-center">
                    <p>This feature will display the complete history of material usage, stock movements, and related transactions.</p>
                    <a href="<?= base_url('materialhistory/') ?>${materialId}" class="btn btn-primary" target="_blank">
                        <i class="fas fa-external-link-alt"></i> Go to Material History Page
                    </a>
                </div>
            `);
        }).fail(function(xhr, status, error) {
            console.error('Error loading material history:', error);
            $('#materialHistoryModal .modal-body').html(`
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle"></i> 
                    Failed to load material history data. Error: ${error}
                </div>
                <div class="text-center">
                    <a href="<?= base_url('materialhistory/') ?>${materialId}" class="btn btn-primary" target="_blank">
                        <i class="fas fa-external-link-alt"></i> Go to Material History Page
                    </a>
                </div>
            `);
        });

        // Clean up modal on close
        $('#materialHistoryModal').on('hidden.bs.modal', function() {
            $(this).remove();
        });
    }

    function updatePiTable(data) {
        const tbody = $('#piTableBody');
        tbody.empty();

        if (data.length === 0) {
            tbody.append('<tr><td colspan="8" class="text-center">No data found for the selected date range</td></tr>');
            return;
        }

        data.forEach(function(pi) {
            const statusBadge = getStatusBadge(pi.status);
            const row = `
                <tr>
                    <td>
                        <a href="<?= base_url('proformainvoice/piDoc/') ?>${pi.invoice_id}" 
                           target="_blank" class="text-primary">
                            ${pi.invoice_number}
                        </a>
                    </td>
                    <td>${pi.invoice_date}</td>
                    <td>${pi.customer_name}</td>
                    <td>${pi.quantity} ${pi.unit}</td>
                    <td>${parseFloat(pi.unit_price).toFixed(2)}</td>
                    <td>${pi.etd || 'N/A'}</td>
                    <td>${pi.eta || 'N/A'}</td>
                    <td><span class="badge badge-${statusBadge.class}">${statusBadge.text}</span></td>
                </tr>
            `;
            tbody.append(row);
        });
    }

    function getStatusBadge(status) {
        switch(status) {
            case 1: return { class: 'success', text: 'Completed' };
            case 2: return { class: 'warning', text: 'Pending' };
            default: return { class: 'secondary', text: 'Draft' };
        }
    }

    // Auto-set end date to today and start date to 30 days ago
    const today = new Date().toISOString().split('T')[0];
    const thirtyDaysAgo = new Date();
    thirtyDaysAgo.setDate(thirtyDaysAgo.getDate() - 30);
    const thirtyDaysAgoFormatted = thirtyDaysAgo.toISOString().split('T')[0];
    
    $('#endDate').val(today);
    $('#startDate').val(thirtyDaysAgoFormatted);
});
</script>