<div class="container-fluid pt-4 px-4">
    <div class="row g-4">
        <div class="col-12">
            <div class="bg-light rounded h-100 p-4">
                <h1 class="mb-4"><?= $title ?></h1>

                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card bg-light">
                            <div class="card-body text-center">
                                <h6>Initial Stock</h6>
                                <h3><?= $initial_stock ?></h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-success text-white">
                            <div class="card-body text-center">
                                <h6>Available</h6>
                                <h3><?= $available ?></h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-warning">
                            <div class="card-body text-center">
                                <h6>Booked</h6>
                                <h3><?= $booked ?></h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-primary text-white">
                            <div class="card-body text-center">
                                <h6>Total</h6>
                                <h3><?= $total ?></h3>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header">
                        <h5>Stock Management</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h6>Set Initial Stock</h6>
                                <form action="/productstock/set-initial/<?= $product['id'] ?>" method="post">
                                    <div class="mb-3">
                                        <label class="form-label">Quantity</label>
                                        <input type="number" step="0.01" class="form-control" name="quantity" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Location (Optional)</label>
                                        <select class="form-select" name="location_id">
                                            <option value="">- Select Location -</option>
                                            <?php foreach ($locations as $loc): ?>
                                                <option value="<?= $loc['id'] ?>"><?= $loc['name'] ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <button type="submit" class="btn btn-primary">Set Initial Stock</button>
                                </form>
                            </div>
                            <div class="col-md-6">
                                <h6>Adjust Stock</h6>
                                <form action="/productstock/adjust/<?= $product['id'] ?>" method="post">
                                    <div class="mb-3">
                                        <label class="form-label">Adjustment Type</label>
                                        <select class="form-select" name="adjustment_type" required>
                                            <option value="in">Stock In (+)</option>
                                            <option value="out">Stock Out (-)</option>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Quantity</label>
                                        <input type="number" step="0.01" class="form-control" name="quantity" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Location (Optional)</label>
                                        <select class="form-select" name="location_id">
                                            <option value="">- Select Location -</option>
                                            <?php foreach ($locations as $loc): ?>
                                                <option value="<?= $loc['id'] ?>"><?= $loc['name'] ?></option>
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
                <div class="row mb-4">
                    <!-- Card Book Stock -->
                    <div class="col-md-6">
                        <div class="card border-primary h-100">
                            <div class="card-header bg-primary text-white">
                                <h5 class="mb-0"><i class="fas fa-calendar-check me-2"></i>Book Stock</h5>
                            </div>
                            <div class="card-body text-center">
                                <i class="fas fa-clipboard-list fa-3x text-primary mb-3"></i>
                                <p>Reserve stock for customer orders or specific purposes</p>
                                <a href="/productstock/book/<?= $product['id'] ?>" class="btn btn-primary">
                                    <i class="fas fa-book me-1"></i> Book Stock
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Card Transfer Stock -->
                    <div class="col-md-6">
                        <div class="card border-success h-100">
                            <div class="card-header bg-success text-white">
                                <h5 class="mb-0"><i class="fas fa-exchange-alt me-2"></i>Transfer Stock</h5>
                            </div>
                            <div class="card-body text-center">
                                <i class="fas fa-truck-moving fa-3x text-success mb-3"></i>
                                <p>Move stock between locations without reducing total quantity</p>
                                <a href="/productstock/transfer/<?= $product['id'] ?>" class="btn btn-success">
                                    <i class="fas fa-arrows-alt-h me-1"></i> Transfer Stock
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card mb-4">
                    <div class="card-header">
                        <h5>Stock Location</h5>
                    </div>
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
                </div>

                <div class="card">
                    <div class="card-header">
                        <h5>Movement History</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Type</th>
                                    <th>Quantity</th>
                                    <th>From</th>
                                    <th>To</th>
                                    <th>Notes</th>
                                    <th>User</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($movement_history as $movement): ?>
                                    <tr>
                                        <td><?= date('d M Y H:i', strtotime($movement['created_at'])) ?></td>
                                        <td>
                                            <span class="badge bg-<?=
                                                $movement['movement_type'] == 'in' ? 'success' :
                                                ($movement['movement_type'] == 'out' ? 'danger' : 'info')
                                                ?>">
                                                <?= ucfirst($movement['movement_type']) ?>
                                            </span>
                                        </td>
                                        <td><?= $movement['quantity'] ?></td>
                                        <td><?= $movement['from_location_name'] ?? '-' ?></td>
                                        <td><?= $movement['to_location_name'] ?? '-' ?></td>
                                        <td><?= $movement['notes'] ?></td>
                                        <td><?= $movement['username'] ?? 'System' ?></td>
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