<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="<?= base_url('assets') ?>/datatables/datatables.min.css" />
    <style>
        /* Minimal CSS for fixed header */
        thead th {
            position: sticky;
            top: 0;
            background-color: #343a40; /* Same background color as header */
            z-index: 100;
        }
    </style>
    <title>DataTables Example</title>
</head>
<body>

<!-- Filter Form -->
<div class="container-fluid pt-4 px-4">
    <div class="bg-light text-center rounded p-4">
        <h6 class="mb-0">Filter Data</h6>
        <form id="filterForm" class="mb-4">
            <div class="row">
                <div class="col-md-4">
                    <input type="text" class="form-control" id="filterKode" placeholder="Filter Kode">
                </div>
                <div class="col-md-4">
                    <input type="text" class="form-control" id="filterNama" placeholder="Filter Nama">
                </div>
                <div class="col-md-4">
                    <button type="button" class="btn btn-primary" id="applyFilter">Apply Filter</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- DataTable -->
<div class="container-fluid pt-4 px-4">
    <div class="bg-light text-center rounded p-4">
        <h6 class="mb-0">Product PI tersedia</h6>
        <div class="table-responsive">
            <table class="table table-bordered display text-left" cellspacing="0" width="100%">
                <thead>
                    <tr class="text-center">
                        <th style="text-align: center;">#</th>
                        <th style="text-align: center;">Kode</th>
                        <th style="text-align: center;">Nama</th>
                        <th style="text-align: center;">Qty</th>
                        <th style="text-align: center;">Action</th>
                    </tr>
                </thead>
                <tbody id="tabel_pi">
                    <!-- Sample Data -->
                    <tr>
                        <td>1</td>
                        <td>K001</td>
                        <td>Product A</td>
                        <td>10</td>
                        <td><button class="btn btn-info">Edit</button></td>
                    </tr>
                    <tr>
                        <td>2</td>
                        <td>K002</td>
                        <td>Product B</td>
                        <td>20</td>
                        <td><button class="btn btn-info">Edit</button></td>
                    </tr>
                    <!-- Add more rows as needed -->
                </tbody>
                <tfoot>
                    <tr class="text-center">
                        <th style="text-align: center;">#</th>
                        <th style="text-align: center;">Kode</th>
                        <th style="text-align: center;">Nama</th>
                        <th style="text-align: center;">Qty</th>
                        <th style="text-align: center;">Action</th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

<script type="text/javascript" src="<?= base_url('assets') ?>/js/jquery.min.js"></script>
<script type="text/javascript" src="<?= base_url('assets') ?>/datatables/datatables.min.js"></script>
<script>
    $(document).ready(function() {
        // Initialize DataTable
        var table = $('.table').DataTable();

        // Apply filter
        $('#applyFilter').on('click', function() {
            var kode = $('#filterKode').val();
            var nama = $('#filterNama').val();

            // Clear previous search
            table.search('').draw();

            // Apply new search
            if (kode) {
                table.columns(1).search(kode).draw(); // Filter by Kode
            }
            if (nama) {
                table.columns(2).search(nama).draw(); // Filter by Nama
            }
        });
    });
</script>

</body>
</html>