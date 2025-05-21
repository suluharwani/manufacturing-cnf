<link rel="stylesheet" type="text/css" href="<?= base_url('assets') ?>/datatables/datatables.min.css" />
<style>
body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }

        .table-container {
            width: 100%; /* Memastikan div memenuhi lebar kontainer */
            overflow-x: auto; /* Menambahkan scroll horizontal jika diperlukan */
            border: 1px solid #ccc; /* Border untuk div */
            border-radius: 5px; /* Sudut melengkung */
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1); /* Bayangan */
        }

        table {
            width: 100%; /* Tabel memenuhi lebar div */
            border-collapse: collapse; /* Menghilangkan jarak antara border sel */
        }

        th, td {
            padding: 12px; /* Ruang dalam sel */
            text-align: left; /* Rata kiri */
            border-bottom: 1px solid #ddd; /* Garis bawah sel */
        }

        th {
            background-color: #f2f2f2; /* Warna latar belakang header */
        }

        tr:hover {
            background-color: #f1f1f1; /* Warna latar belakang saat hover */
        }
</style>

<div class="container-fluid pt-4 px-4">
  <div class="bg-light text-center rounded p-4">
    <div class="d-flex align-items-center justify-content-between mb-4">
      <h6 class="mb-0">Document</h6>
      <button class="btn btn-primary" id="uploadBtn">Add</button>
    </div>
    <div class="table-responsive">

      <table id="tabelDesign" class="table table-bordered display text-left" cellspacing="0" width="100%">
        <thead>

          <tr class="text-center">
            <th style=" text-align: center;">#</th>
            <th style=" text-align: center;">Date</th>
            <th style=" text-align: center;">Name</th>
            <th style=" text-align: center;">Desc</th>
            <th style=" text-align: center;">Action</th>
          </tr>
        </thead>
        <tfoot>
        <tr class="text-center">
            <th style=" text-align: center;">#</th>
            <th style=" text-align: center;">Date</th>
            <th style=" text-align: center;">Name</th>
            <th style=" text-align: center;">Desc</th>
            <th style=" text-align: center;">Action</th>
          </tr>
        </tfoot>
      </table>
    </div>
  </div>
</div>
<!-- Recent Sales End -->


<!-- Widgets Start -->

<!-- Widgets End -->
<!-- Button to trigger modal -->


                        <!-- Results will be displayed here -->
<script type="text/javascript" src="<?= base_url('assets') ?>/js/product_design.js"></script>
<script type="text/javascript" src="<?= base_url('assets') ?>/datatables/datatables.min.js"></script>