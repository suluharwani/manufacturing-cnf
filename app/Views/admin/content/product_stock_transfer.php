<div class="container-fluid pt-4 px-4">
    <div class="row g-4">
        <div class="col-12">
            <div class="bg-light rounded h-100 p-4">
                <div class="card-body">
                    <table class="table table-striped" border="1" cellpadding="8" cellspacing="0">
                        <thead>
                            <tr>
                                <th>Location</th>
                                <th>Finishing</th>
                                <th>Current</th>
                                <th>Booked</th>
                                <th>Available</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($stockData as $item): ?>
                                <tr>
                                    <td><?= esc($item['location_name']) ?></td>
                                    <td><?= $item['finishing_id'] ? esc($item['finishing_name']) : 'Standard' ?></td>
                                    <td><?= number_format($item['current_stock']) ?></td>
                                    <td><?= number_format($item['booked_stock']) ?></td>
                                    <td class="<?= ($item['current_stock'] - $item['booked_stock']) <= 0 ? 'text-danger' : '' ?>">
                                        <?= number_format($item['current_stock'] - $item['booked_stock']) ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                
                <h1 class="mb-4"><?= $title ?></h1>
                
                <?php if (session('error')): ?>
                    <div class="alert alert-danger"><?= session('error') ?></div>
                <?php endif; ?>
                
                <?php if (session('errors')): ?>
                    <div class="alert alert-danger">
                        <?php foreach (session('errors') as $error): ?>
                            <p class="mb-0"><?= $error ?></p>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <form action="/productstock/process-transfer/<?= $product['id'] ?>" method="post">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Product</label>
                            <input type="text" class="form-control" value="<?= esc($product['nama']) ?>" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Finishing</label>
                            <select class="form-select" name="finishing_id" id="finishing_select">
                                <option value="">- Standard -</option>
                                <?php foreach ($finishings as $finishing): ?>
                                    <option value="<?= $finishing['id'] ?>"><?= esc($finishing['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">From Location</label>
                            <select class="form-select" name="from_location_id" id="from_location" required>
                                <option value="">Select Source Location</option>
                                <?php foreach ($locations as $loc): ?>
                                    <option value="<?= $loc['id'] ?>"><?= esc($loc['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">To Location</label>
                            <select class="form-select" name="to_location_id" required>
                                <option value="">Select Destination Location</option>
                                <?php foreach ($locations as $loc): ?>
                                    <option value="<?= $loc['id'] ?>"><?= esc($loc['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Available Quantity</label>
                            <input type="text" class="form-control" id="available_quantity" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Quantity to Transfer</label>
                            <input type="number" class="form-control" name="quantity" min="0.01" step="0.01" required>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Notes</label>
                        <textarea class="form-control" name="notes" rows="3"></textarea>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">Transfer Stock</button>
                    <a href="/product/view/<?= $product['id'] ?>" class="btn btn-secondary">Cancel</a>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#finishing_select, #from_location').change(function() {
        var productId = <?= $product['id'] ?>;
        var finishingId = $('#finishing_select').val();
        var locationId = $('#from_location').val();
        
        if (locationId) {
            $.get('/productstock/get-available-stock', {
                product_id: productId,
                finishing_id: finishingId,
                location_id: locationId
            }, function(data) {
                $('#available_quantity').val(data.available);
            });
        }
    });
});
</script>