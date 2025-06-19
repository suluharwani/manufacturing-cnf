<div class="container-fluid pt-4 px-4">
    <div class="row g-4">
        <div class="col-12">
            <div class="card-body">
                        <table class="table table-striped" border="1" cellpadding="8" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Location ID</th>
                                    <th>Location Code</th>
                                    <th>Location Name</th>
                                    <th>Current Stock</th>
                                    <th>Booked Quantity</th>
                                    <th>Available Quantity</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($stockData as $item): ?>
                                    <tr>
                                        <td><?= $item['location_id'] ?></td>
                                        <td><?= $item['location_code'] ?></td>
                                        <td><?= $item['location_name'] ?></td>
                                        <td><?= $item['current_stock'] ?></td>
                                        <td><?= $item['booked_stock'] ?></td>
                                        <td><?= $item['current_stock'] - $item['booked_stock'] ?></td>
                                    </tr>
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
                        <div class="col-md-12">
                            <label class="form-label">Product</label>
                            <input type="text" class="form-control" value="<?= esc($product['nama']) ?>" readonly>
                        </div>
        
                    </div>
                    <div class="mb-3">
                                        <label class="form-label">Location</label>
                                        <select class="form-select" name="location_id">
                                            <option value="">- Select Location -</option>
                                            <?php foreach ($locations as $loc): ?>
                                            <option value="<?= $loc['id'] ?>"><?= $loc['name'] ?></option>
                                            <?php endforeach; ?>
                                        </select>
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

