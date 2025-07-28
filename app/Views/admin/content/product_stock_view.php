<div class="container-fluid pt-4 px-4">
    <div class="row g-4">
        <div class="col-12">
            <div class="bg-light rounded h-100 p-4">
                <h1 class="mb-4"><?= esc($title) ?></h1>

                <!-- Card Statistik Stock -->
                <div class="row mb-4">
                    <!-- Card Initial Stock -->
                    <div class="col-md-3">
                        <div class="card bg-light">
                            <div class="card-body text-center">
                                <h6>Initial Stock</h6>
                                <h3><?= number_format($initial_stock) ?></h3>
                                <?php if (!empty($finishings)): ?>
                                    <?php foreach ($finishings as $finishing): ?>
                                        <div class="mt-2">
                                            <h6><?= number_format($finishing_stocks[$finishing['id']]['initial'] ?? 0) ?></h6>
                                            <small class="text-muted"><?= esc($finishing['name']) ?></small>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Card Available Stock -->
                    <div class="col-md-3">
                        <div class="card bg-success text-white">
                            <div class="card-body text-center">
                                <h6>Available</h6>
                                <h3><?= number_format($available) ?></h3>
                                <?php if (!empty($finishings)): ?>
                                    <?php foreach ($finishings as $finishing): ?>
                                        <div class="mt-2">
                                            <h6><?= number_format($finishing_stocks[$finishing['id']]['available'] ?? 0) ?></h6>
                                            <small><?= esc($finishing['name']) ?></small>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Card Booked Stock -->
                    <div class="col-md-3">
                        <div class="card bg-warning">
                            <div class="card-body text-center">
                                <h6>Booked</h6>
                                <h3><?= number_format($booked) ?></h3>
                                <?php if (!empty($finishings)): ?>
                                    <?php foreach ($finishings as $finishing): ?>
                                        <div class="mt-2">
                                            <h6><?= number_format($finishing_stocks[$finishing['id']]['booked'] ?? 0) ?></h6>
                                            <small><?= esc($finishing['name']) ?></small>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Card Total Stock -->
                    <div class="col-md-3">
                        <div class="card bg-primary text-white">
                            <div class="card-body text-center">
                                <h6>Total</h6>
                                <h3><?= number_format($total) ?></h3>
                                <?php if (!empty($finishings)): ?>
                                    <?php foreach ($finishings as $finishing): ?>
                                        <div class="mt-2">
                                            <?php 
                                                $finishing_available = $finishing_stocks[$finishing['id']]['available'] ?? 0;
                                                $finishing_booked = $finishing_stocks[$finishing['id']]['booked'] ?? 0;
                                            ?>
                                            <h6><?= number_format($finishing_available + $finishing_booked) ?></h6>
                                            <small><?= esc($finishing['name']) ?></small>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form Management Stock -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5>Stock Management</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <!-- Form Set Initial Stock -->
                            <div class="col-md-6">
                                <h6>Set Initial Stock</h6>
                                <form action="/productstock/set-initial/<?= $product['id'] ?>" method="post">
                                    <?= csrf_field() ?>
                                    <div class="mb-3">
                                        <label class="form-label">Quantity</label>
                                        <input type="number" step="0.01" class="form-control" name="quantity" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Finishing</label>
                                        <select class="form-select" name="finishing_id">
                                            <option value="">- Standard -</option>
                                            <?php foreach ($finishings as $finishing): ?>
                                                <option value="<?= $finishing['id'] ?>"><?= esc($finishing['name']) ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Location</label>
                                        <select class="form-select" name="location_id">
                                            <option value="">- Select Location -</option>
                                            <?php foreach ($locations as $loc): ?>
                                                <option value="<?= $loc['id'] ?>"><?= esc($loc['name']) ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <button type="submit" class="btn btn-primary">Set Initial Stock</button>
                                </form>
                            </div>
                            
                            <!-- Form Adjust Stock -->
                            <div class="col-md-6">
                                <h6>Adjust Stock</h6>
                                <form action="/productstock/adjust/<?= $product['id'] ?>" method="post">
                                    <?= csrf_field() ?>
                                    <div class="mb-3">
                                        <label class="form-label">Adjustment Type</label>
                                        <select class="form-select" name="adjustment_type" required>
                                            <option value="in">Stock In (+)</option>
                                            <option value="out">Stock Out (-)</option>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Finishing</label>
                                        <select class="form-select" name="finishing_id">
                                            <option value="">- Standard -</option>
                                            <?php foreach ($finishings as $finishing): ?>
                                                <option value="<?= $finishing['id'] ?>"><?= esc($finishing['name']) ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Document Code</label>
                                        <input type="text" class="form-control" name="code">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Quantity</label>
                                        <input type="number" step="0.01" class="form-control" name="quantity" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Location</label>
                                        <select class="form-select" name="location_id">
                                            <option value="">- Select Location -</option>
                                            <?php foreach ($locations as $loc): ?>
                                                <option value="<?= $loc['id'] ?>"><?= esc($loc['name']) ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Notes</label>
                                        <textarea class="form-control" name="notes"></textarea>
                                    </div>
                                    <button type="submit" class="btn btn-warning">Adjust Stock</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="card border-primary h-100">
                            <div class="card-header bg-primary text-white">
                                <h5 class="mb-0"><i class="fas fa-calendar-check me-2"></i>Book Stock</h5>
                            </div>
                            <div class="card-body text-center">
                                <i class="fas fa-clipboard-list fa-3x text-primary mb-3"></i>
                                <p>Reserve stock for customer orders</p>
                                <a href="/productstock/book/<?= $product['id'] ?>" class="btn btn-primary">
                                    <i class="fas fa-book me-1"></i> Book Stock
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card border-success h-100">
                            <div class="card-header bg-success text-white">
                                <h5 class="mb-0"><i class="fas fa-exchange-alt me-2"></i>Transfer Stock</h5>
                            </div>
                            <div class="card-body text-center">
                                <i class="fas fa-truck-moving fa-3x text-success mb-3"></i>
                                <p>Move stock between locations</p>
                                <a href="/productstock/transfer/<?= $product['id'] ?>" class="btn btn-success">
                                    <i class="fas fa-arrows-alt-h me-1"></i> Transfer Stock
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

<!-- Stock Location Table -->
<div class="card mb-4">
    <div class="card-header">
        <h5>Stock by Location</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>Location</th>
                        <th>Variant</th>
                        <th class="text-end">Current</th>
                        <th class="text-end">Booked</th>
                        <th class="text-end">Available</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    // Group stock data by location first
                    $locations = [];
                    foreach ($stock_data as $stock) {
                        $locId = $stock['location_id'];
                        if (!isset($locations[$locId])) {
                            $locations[$locId] = [
                                'name' => $stock['location_name'],
                                'variants' => [],
                                'total_current' => 0,
                                'total_booked' => 0,
                                'total_available' => 0
                            ];
                        }
                        
                        // Add variant to location
                        $variantKey = $stock['finishing_id'] ?? 'standard';
                        $locations[$locId]['variants'][$variantKey] = [
                            'name' => $stock['finishing_name'] ?? 'Standard',
                            'current' => $stock['current_stock'],
                            'booked' => $stock['booked_stock'],
                            'available' => $stock['available_stock']
                        ];
                        
                        // Update location totals
                        $locations[$locId]['total_current'] += $stock['current_stock'];
                        $locations[$locId]['total_booked'] += $stock['booked_stock'];
                        $locations[$locId]['total_available'] += $stock['available_stock'];
                    }

                    // Display the data
                    foreach ($locations as $locId => $location): 
                        // Display each variant for this location
                        foreach ($location['variants'] as $variantKey => $variant): ?>
                            <tr>
                                <td><?= esc($location['name']) ?></td>
                                <td>
                                    <?php if ($variantKey !== 'standard'): ?>
                                        <span class="badge bg-secondary"><?= esc($variant['name']) ?></span>
                                    <?php else: ?>
                                        <span class="badge bg-light text-dark">Standard</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end"><?= number_format($variant['current']) ?></td>
                                <td class="text-end"><?= number_format($variant['booked']) ?></td>
                                <td class="text-end <?= $variant['available'] == 0 ? 'text-danger fw-bold' : '' ?>">
                                    <?= number_format($variant['available']) ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        
                        <!-- Display location total -->
                        <tr class="bg-light">
                            <td colspan="2"><strong>Total for <?= esc($location['name']) ?></strong></td>
                            <td class="text-end"><strong><?= number_format($location['total_current']) ?></strong></td>
                            <td class="text-end"><strong><?= number_format($location['total_booked']) ?></strong></td>
                            <td class="text-end"><strong><?= number_format($location['total_available']) ?></strong></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
                <!-- Movement History -->
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Movement History</h5>
                        <div>
                            <a href="/productstock/export-movements/<?= $product['id'] ?>" class="btn btn-sm btn-success">
                                <i class="fas fa-file-excel me-1"></i> Export
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Document</th>
                                        <th>Variant</th>
                                        <th>Type</th>
                                        <th class="text-end">Qty</th>
                                        <th>From</th>
                                        <th>To</th>
                                        <th>Notes</th>
                                        <th>User</th>
                                        <th class="text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                  
                                    <?php foreach ($movement_history as $movement): ?>
                                        <tr>
                                            <td><?= date('d M Y H:i', strtotime($movement['created_at'])) ?></td>
                                            <td><?= esc($movement['code']) ?></td>
                                            <td>
                                                <?php if (!empty($movement['finishing_name']) && $movement['finishing_name'] != 'Standard'): ?>
                                                    <span class="badge bg-secondary"><?= esc($movement['finishing_name']) ?></span>
                                                <?php else: ?>
                                                    <span class="badge bg-light text-dark">Standard</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <span class="badge bg-<?= 
                                                    $movement['movement_type'] == 'in' ? 'success' :
                                                    ($movement['movement_type'] == 'out' ? 'danger' : 
                                                    ($movement['movement_type'] == 'booked' ? 'warning' : 'info'))
                                                ?>">
                                                    <?= ucfirst($movement['movement_type']) ?>
                                                    <?= ($movement['status'] ?? '') == 'completed' ? ' (Completed)' : '' ?>
                                                </span>
                                            </td>
                                            <td class="text-end"><?= number_format($movement['quantity']) ?></td>
                                            <td><?= esc($movement['from_location_name'] ?? '-') ?></td>
                                            <td><?= esc($movement['to_location_name'] ?? '-') ?></td>
                                            <td><?= esc($movement['notes']) ?></td>
                                            <td><?= esc($movement['username'] ?? 'System') ?></td>
                                            <td class="text-center">
                                                <?php if ($movement['movement_type'] == 'booked'): ?>
                                                    <button class="btn btn-sm btn-success complete-booking" 
                                                            data-id="<?= $movement['id'] ?>"
                                                            data-product="<?= $product['id'] ?>">
                                                        <i class="fas fa-check"></i> Complete
                                                    </button>
                                                <?php endif; ?>
                                                <button class="btn btn-sm btn-danger delete-movement" 
                                                        data-id="<?= $movement['id'] ?>"
                                                        data-product="<?= $product['id'] ?>">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </td>
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

<!-- Complete Booking Modal -->
<div class="modal fade" id="completeModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Complete Booking</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to mark this booking as completed?</p>
                <form id="completeForm" method="post">
                    <?= csrf_field() ?>
                    <input type="hidden" name="movement_id" id="movement_id">
                    <input type="hidden" name="product_id" id="product_id">
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="confirmComplete">Complete</button>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Complete booking button
    $('.complete-booking').click(function() {
        $('#movement_id').val($(this).data('id'));
        $('#product_id').val($(this).data('product'));
        $('#completeModal').modal('show');
    });

    // Confirm complete
    $('#confirmComplete').click(function() {
        $('#completeForm').attr('action', '/productstock/complete-booking').submit();
    });

    // Delete movement
    $('.delete-movement').click(function() {
        if (confirm('Are you sure you want to delete this record?')) {
            $('<form>', {
                'action': `/productstock/delete-movement/`+$(this).data('id'),
                'method': 'post',
                'html': `
                    <input type="hidden" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>">
                    <input type="hidden" name="product_id" value="${$(this).data('product')}">
                `
            }).appendTo('body').submit();
        }
    });
});
</script>

<style>
    .badge {
        font-size: 0.85em;
        padding: 0.35em 0.65em;
    }
    .text-end {
        text-align: right !important;
    }
    .text-center {
        text-align: center !important;
    }
    .fw-bold {
        font-weight: 600;
    }
    .table-hover tbody tr:hover {
        background-color: rgba(0, 0, 0, 0.03);
    }
    .card-header {
        background-color: #f8f9fa;
    }
</style>