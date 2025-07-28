<div class="container-fluid pt-4 px-4">
    <div class="row g-4">
        <div class="col-12">
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
                        <?php 
                        $finishingModel = new \App\Models\FinishingModel();
                        $finishings = $finishingModel->where('id_product', $product['id'])->findAll();
                        
                        foreach ($stockData as $item): ?>
                            <tr>
                                <td><?= $item['location_name'] ?></td>
                                <td>Standard</td>
                                <td><?= $item['current_stock'] ?></td>
                                <td><?= $item['booked_stock'] ?></td>
                                <td><?= $item['current_stock'] - $item['booked_stock'] ?></td>
                            </tr>
                            <?php foreach ($finishings as $finishing): 
                                $finishingStock = $stockData ?>
                                <tr>
                                    <td><?= $item['location_name'] ?></td>
                                    <td><?= $finishing['name'] ?></td>
                                    <td><?= $finishingStock['current_stock'] ?? 0 ?></td>
                                    <td><?= $finishingStock['booked_stock'] ?? 0 ?></td>
                                    <td><?= ($finishingStock['current_stock'] ?? 0) - ($finishingStock['booked_stock'] ?? 0) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <div class="bg-light rounded h-100 p-4">
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
                            
                <form action="/productstock/process-booking/<?= $product['id'] ?>" method="post">
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
                            <label class="form-label">Location</label>
                            <select class="form-select" name="location_id" id="location_select" required>
                                <option value="">- Select Location -</option>
                                <?php foreach ($locations as $loc): ?>
                                    <option value="<?= $loc['id'] ?>"><?= $loc['name'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Available Quantity</label>
                            <input type="text" class="form-control" id="available_quantity" readonly>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Quantity to Book</label>
                        <input type="number" class="form-control" name="quantity" min="1" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Proforma Invoice</label>
                        <select class="form-select" name="pi_id" required>
                            <option value="">Select Invoice</option>
                            <?php foreach ($proformaInvoices as $pi): ?>
                                <option value="<?= $pi['id'] ?>"><?= esc($pi['invoice_number']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Notes</label>
                        <textarea class="form-control" name="notes" rows="3"></textarea>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">Book Stock</button>
                    <a href="/productstock/view/<?= $product['id'] ?>" class="btn btn-secondary">Cancel</a>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#finishing_select, #location_select').change(function() {
        var productId = <?= $product['id'] ?>;
        var finishingId = $('#finishing_select').val();
        var locationId = $('#location_select').val();
        
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