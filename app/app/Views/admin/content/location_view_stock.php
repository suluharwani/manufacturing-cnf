<div class="container-fluid pt-4 px-4">
    <div class="row g-4">
        <div class="col-12">
            <div class="bg-light rounded h-100 p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1><?= $title ?></h1>
                    <a href="/location" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Back to Locations
                    </a>
                </div>

                <div class="card mb-4">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <h5>Location Information</h5>
                                <table class="table table-bordered">
                                    <tr>
                                        <th>Code</th>
                                        <td><?= esc($location['code']) ?></td>
                                    </tr>
                                    <tr>
                                        <th>Name</th>
                                        <td><?= esc($location['name']) ?></td>
                                    </tr>
                                    <tr>
                                        <th>Type</th>
                                        <td><?= esc($locationTypes[$location['type']] ?? $location['type']) ?></td>
                                    </tr>
                                    <tr>
                                        <th>Status</th>
                                        <td>
                                            <span class="badge bg-<?= $location['is_active'] ? 'success' : 'danger' ?>">
                                                <?= $location['is_active'] ? 'Active' : 'Inactive' ?>
                                            </span>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-8">
                                <h5>Location Description</h5>
                                <div class="p-3 bg-white rounded">
                                    <?= $location['description'] ? esc($location['description']) : 'No description available' ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h5>Current Stock</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="locationStockTable" class="table table-striped table-hover" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>Product Code</th>
                                        <th>Product Name</th>
                                        <th>Quantity</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($stocks)): ?>
                                        <?php foreach ($stocks as $stock): ?>
                                        <tr>
                                            <td><?= esc($stock['product_code']) ?></td>
                                            <td><?= esc($stock['product_name']) ?></td>
                                            <td><?= esc($stock['total_quantity']) ?></td>
                                            <td>
                                                <a href="/productstock/view/<?= $stock['id'] ?>" class="btn btn-sm btn-primary">
                                                    <i class="fas fa-eye"></i> View Product
                                                </a>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="4" class="text-center">No stock available in this location</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- DataTables Scripts -->
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css"/>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>

<script>
$(document).ready(function() {
    $('#locationStockTable').DataTable({
        responsive: true,
        columnDefs: [
            { orderable: false, targets: [3] }
        ]
    });
});
</script>