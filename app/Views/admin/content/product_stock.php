<div class="container-fluid pt-4 px-4">
    <div class="row g-4">
        <div class="col-12">
            <div class="bg-light rounded h-100 p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1><?= esc($title) ?></h1>
                    <div>
                        <a href="location" class="btn btn-warning me-2"><i class="fas fa-file-pdf me-2"></i>Location Data</a>
                        <a href="<?= base_url() ?>productstock/exportExcel" class="btn btn-success">
                            <i class="fas fa-file-excel me-2"></i> Export Excel
                        </a>
                    </div>
                </div>

                <!-- Stock Summary Cards -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card bg-light">
                            <div class="card-body text-center">
                                <h6>Total Variants</h6>
                                <h3><?= count($products) ?></h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-primary text-white">
                            <div class="card-body text-center">
                                <h6>Total Stock</h6>
                                <h3><?= number_format(array_sum(array_column($products, 'total'))) ?></h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-success text-white">
                            <div class="card-body text-center">
                                <h6>Available</h6>
                                <h3><?= number_format(array_sum(array_column($products, 'available'))) ?></h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-warning">
                            <div class="card-body text-center">
                                <h6>Booked</h6>
                                <h3><?= number_format(array_sum(array_column($products, 'booked'))) ?></h3>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Products Table -->
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="productsTable" class="table table-striped table-hover" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Product</th>
                                        <th>Variant</th>
                                        <th class="text-end">Available</th>
                                        <th class="text-end">Booked</th>
                                        <th class="text-end">Total</th>
                                        <th class="text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $no = 1; ?>
                                    <?php foreach ($products as $product): ?>
                                    <tr>
                                        <td><?= $no++ ?></td>
                                        <td>
                                            <div class="fw-bold"><?= esc($product['code']) ?></div>
                                            <div><?= esc($product['name']) ?></div>
                                        </td>
                                        <td>
                                            <?php if (!empty($product['finishing_name']) && $product['finishing_name'] != 'Standard'): ?>
                                                <span class="badge bg-secondary"><?= esc($product['finishing_name']) ?></span>
                                            <?php else: ?>
                                                <span class="badge bg-light text-dark">Standard</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-end <?= $product['available'] == 0 ? 'text-danger fw-bold' : '' ?>">
                                            <?= number_format($product['available']) ?>
                                        </td>
                                        <td class="text-end"><?= number_format($product['booked']) ?></td>
                                        <td class="text-end"><?= number_format($product['total']) ?></td>
                                        <td class="text-center">
                                            <a href="/productstock/view/<?= $product['id'] ?>" class="btn btn-sm btn-primary">
                                                <i class="fas fa-eye"></i> View
                                            </a>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="3" style="text-align:right">Total:</th>
                                        <th class="text-end"></th>
                                        <th class="text-end"></th>
                                        <th class="text-end"></th>
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

<!-- DataTables & Export Scripts -->
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css"/>
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/2.3.6/css/buttons.bootstrap5.min.css"/>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"/>

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
    var table = $('#productsTable').DataTable({
        dom: '<"top"<"row"<"col-md-6"B><"col-md-6"f>>>rt<"bottom"<"row"<"col-md-6"l><"col-md-6"p>>><"clear">',
        buttons: [
            {
                extend: 'excelHtml5',
                text: '<i class="fas fa-file-excel me-2"></i> Excel',
                className: 'btn btn-success btn-sm',
                exportOptions: {
                    columns: ':visible',
                    format: {
                        body: function(data, row, column, node) {
                            if (column === 2) { // Variant column
                                return $(node).text().trim();
                            }
                            if ([3,4,5].includes(column)) { // Numeric columns
                                return data.replace(/,/g, '');
                            }
                            return data;
                        }
                    }
                }
            },
            {
                extend: 'pdfHtml5',
                text: '<i class="fas fa-file-pdf me-2"></i> PDF',
                className: 'btn btn-danger btn-sm',
                exportOptions: {
                    columns: ':visible',
                    format: {
                        body: function(data, row, column, node) {
                            if (column === 2) {
                                return $(node).text().trim();
                            }
                            if ([3,4,5].includes(column)) {
                                return data.replace(/,/g, '');
                            }
                            return data;
                        }
                    }
                }
            },
            {
                extend: 'print',
                text: '<i class="fas fa-print me-2"></i> Print',
                className: 'btn btn-info btn-sm',
                exportOptions: {
                    columns: ':visible',
                    format: {
                        body: function(data, row, column, node) {
                            if (column === 2) {
                                return $(node).text().trim();
                            }
                            if ([3,4,5].includes(column)) {
                                return data.replace(/,/g, '');
                            }
                            return data;
                        }
                    }
                }
            },
            {
                extend: 'colvis',
                text: '<i class="fas fa-columns me-2"></i> Columns',
                className: 'btn btn-secondary btn-sm'
            }
        ],
        columnDefs: [
            { 
                orderable: false, 
                targets: [6] // Action column
            },
            {
                className: "text-center",
                targets: [6] // Center align Action column
            },
            {
                className: "text-end",
                targets: [3,4,5] // Right align numeric columns
            },
            {
                targets: [2], // Variant column
                render: function(data, type, row) {
                    if (type === 'export') {
                        return data.replace(/<[^>]*>/g, ''); // Strip HTML for export
                    }
                    return data;
                }
            }
        ],
        order: [[0, 'asc']],
        footerCallback: function(row, data, start, end, display) {
            var api = this.api();
            
            var availableTotal = api.column(3, {page: 'current'}).data().reduce(function(a, b) {
                return a + parseInt(b.replace(/,/g, ''));
            }, 0);
            
            var bookedTotal = api.column(4, {page: 'current'}).data().reduce(function(a, b) {
                return a + parseInt(b.replace(/,/g, ''));
            }, 0);
            
            var totalTotal = api.column(5, {page: 'current'}).data().reduce(function(a, b) {
                return a + parseInt(b.replace(/,/g, ''));
            }, 0);
            
            $(api.column(3).footer()).html('<strong>' + availableTotal.toLocaleString() + '</strong>');
            $(api.column(4).footer()).html('<strong>' + bookedTotal.toLocaleString() + '</strong>');
            $(api.column(5).footer()).html('<strong>' + totalTotal.toLocaleString() + '</strong>');
        },
        language: {
            search: "_INPUT_",
            searchPlaceholder: "Search products...",
            lengthMenu: "Show _MENU_ entries",
            paginate: {
                previous: '<i class="fas fa-chevron-left"></i>',
                next: '<i class="fas fa-chevron-right"></i>'
            }
        },
        responsive: true,
        initComplete: function() {
            $('.dt-buttons').addClass('btn-group');
            $('.dt-button').removeClass('dt-button');
        }
    });
});
</script>

<style>
    .dataTables_wrapper .dt-buttons {
        margin-bottom: 15px;
        display: flex;
        gap: 5px;
        flex-wrap: wrap;
    }
    .dataTables_wrapper .dt-button {
        border-radius: 4px;
        padding: 5px 10px;
        font-size: 14px;
        margin-right: 0;
        border: 1px solid transparent;
    }
    .dataTables_filter input {
        border-radius: 4px;
        border: 1px solid #ddd;
        padding: 5px 10px;
        margin-left: 10px;
    }
    .dataTables_length select {
        border-radius: 4px;
        border: 1px solid #ddd;
        padding: 5px;
    }
    table.dataTable thead th {
        border-bottom: 2px solid #dee2e6;
        background-color: #f8f9fa;
        vertical-align: middle;
    }
    table.dataTable.no-footer {
        border-bottom: 1px solid #dee2e6;
    }
    .text-danger {
        color: #dc3545 !important;
    }
    .table td {
        vertical-align: middle;
        padding: 12px;
    }
    .table-hover tbody tr:hover {
        background-color: rgba(0, 0, 0, 0.03);
    }
    .btn-sm {
        padding: 0.25rem 0.5rem;
        font-size: 0.875rem;
    }
    tfoot th {
        font-weight: bold;
        background-color: #f8f9fa;
    }
    .card {
        border: none;
        box-shadow: 0 0.15rem 1.75rem 0 rgba(33, 40, 50, 0.15);
    }
    .badge {
        font-size: 0.85em;
        padding: 0.35em 0.65em;
    }
    #productsTable tbody td:nth-child(2) {
        min-width: 200px;
    }
    #productsTable tbody td:nth-child(3) {
        min-width: 120px;
    }
    .dataTables_wrapper .row {
        margin-bottom: 15px;
    }
    .fw-bold {
        font-weight: 600;
    }
    .text-end {
        text-align: right !important;
    }
</style>