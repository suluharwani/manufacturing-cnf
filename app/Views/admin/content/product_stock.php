<div class="container-fluid pt-4 px-4">
    <div class="row g-4">
        <div class="col-12">
            <div class="bg-light rounded h-100 p-4">
                <h1 class="mb-4"><?= $title ?></h1>
                
                <div class="card">
                    <div class="card-header">
                        <h5>Product List</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Product Code</th>
                                    <th>Product Name</th>
                                    <th>Available Stock</th>
                                    <th>Booked Stock</th>
                                    <th>Total Stock</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($products as $product): ?>
                                <?php
                                    $available = $this->stProductModel->getAvailableStock($product['id']);
                                    $booked = $this->stProductModel->getBookedStock($product['id']);
                                ?>
                                <tr>
                                    <td><?= $product['code'] ?></td>
                                    <td><?= $product['name'] ?></td>
                                    <td><?= $available ?></td>
                                    <td><?= $booked ?></td>
                                    <td><?= $available + $booked ?></td>
                                    <td>
                                        <a href="/stock/view/<?= $product['id'] ?>" class="btn btn-sm btn-primary">
                                            <i class="fas fa-eye"></i> View
                                        </a>
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

<script src="https://unpkg.com/jspdf@latest/dist/jspdf.umd.min.js"></script>
<script src="https://html2canvas.hertzen.com/dist/html2canvas.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.8.4/jspdf.plugin.autotable.min.js"></script>

<style>
    table {
        border-collapse: collapse;
        width: 100%;
    }

    th, td {
        border: 1px solid #000;
        padding: 8px;
        text-align: center;
    }

    tr:nth-child(even) {
        background-color: #f2f2f2;
    }

    #btnReportMt, #btnReportSc {
        display: none;
        margin-top: 10px;
        padding: 10px;
        border: 1px solid #ccc;
    }
</style>