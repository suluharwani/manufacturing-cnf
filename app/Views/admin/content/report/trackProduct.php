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
    </div>
</div>

<script>
$(document).ready(function() {
    // Filter functionality
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