<div class="container-fluid pt-4 px-4">
    <div class="row g-4">
        <div class="col-12">
            <div class="bg-light rounded h-100 p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1><?= $title ?></h1>
                    <a href="location" class="btn btn-warning"><i class="fas fa-file-pdf me-2"></i>Location Data</></a>
                    <a href="<?=base_url()?>productstock/stockExport" class="btn btn-success">
    <i class="fas fa-file-excel"></i> Export Excel Stock
</a>
                    <button id="exportPdf" class="btn btn-danger"><i class="fas fa-file-pdf me-2"></i>Export PDF</button>
                </div>
                
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="productsTable" class="table table-striped table-hover" style="width:100%">
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
                                    <tr>
                                        <td><?= esc($product['code']) ?></td>
                                        <td><?= esc($product['name']) ?></td>
                                        <td><?= esc($product['available']) ?></td>
                                        <td><?= esc($product['booked']) ?></td>
                                        <td><?= esc($product['total']) ?></td>
                                        <td>
                                            <a href="/productstock/view/<?= $product['id'] ?>" class="btn btn-sm btn-primary">
                                                <i class="fas fa-eye"></i> View
                                            </a>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="2" style="text-align:right">Total:</th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- DataTables & PDF Export Scripts -->
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css"/>
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/2.3.6/css/buttons.bootstrap5.min.css"/>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.3.6/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.bootstrap5.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.print.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.colVis.min.js"></script>

<script>
$(document).ready(function() {
    $('#productsTable').DataTable({
        dom: 'Bfrtip',
        buttons: [
            {
                extend: 'excelHtml5',
                text: '<i class="fas fa-file-excel me-1"></i> Excel',
                className: 'btn btn-success',
                exportOptions: {
                    columns: ':visible'
                }
            },
            {
                extend: 'pdfHtml5',
                text: '<i class="fas fa-file-pdf me-1"></i> PDF',
                className: 'btn btn-danger',
                exportOptions: {
                    columns: ':visible'
                }
            },
            {
                extend: 'print',
                text: '<i class="fas fa-print me-1"></i> Print',
                className: 'btn btn-info',
                exportOptions: {
                    columns: ':visible'
                }
            },
            'colvis'
        ],
        columnDefs: [
            { orderable: false, targets: [5] } // Disable sorting for Action column
        ],
        footerCallback: function (row, data, start, end, display) {
            var api = this.api();
            
            // Calculate totals for available, booked, and total stock columns
            var availableTotal = api
                .column(2, { page: 'current' })
                .data()
                .reduce(function (a, b) {
                    return parseInt(a) + parseInt(b);
                }, 0);
                
            var bookedTotal = api
                .column(3, { page: 'current' })
                .data()
                .reduce(function (a, b) {
                    return parseInt(a) + parseInt(b);
                }, 0);
                
            var totalTotal = api
                .column(4, { page: 'current' })
                .data()
                .reduce(function (a, b) {
                    return parseInt(a) + parseInt(b);
                }, 0);
            
            // Update footer
            $(api.column(2).footer()).html(availableTotal);
            $(api.column(3).footer()).html(bookedTotal);
            $(api.column(4).footer()).html(totalTotal);
        }
    });
});

// Custom PDF Export Button
document.getElementById('exportPdf').addEventListener('click', function() {
    var table = $('#productsTable').DataTable();
    table.button('.buttons-pdf').trigger();
});
</script>

<style>
    .dataTables_wrapper .dt-buttons {
        margin-bottom: 20px;
    }
    .dt-button {
        margin-right: 5px;
    }
    .dataTables_filter input {
        border-radius: 4px;
        border: 1px solid #ddd;
        padding: 5px;
    }
    .dataTables_length select {
        border-radius: 4px;
        border: 1px solid #ddd;
        padding: 5px;
    }
    table.dataTable thead th {
        border-bottom: 2px solid #dee2e6;
    }
    table.dataTable.no-footer {
        border-bottom: 1px solid #dee2e6;
    }
    .badge {
        font-size: 90%;
    }
</style>