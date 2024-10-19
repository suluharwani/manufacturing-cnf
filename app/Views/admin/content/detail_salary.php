<link rel="stylesheet" type="text/css" href="<?= base_url('assets') ?>/datatables/datatables.min.css" />
<style>
    /* Tambahan minimal CSS untuk fixed header */
    thead th {
        position: sticky;
        top: 0;
        background-color: #343a40;
        /* Warna background yang sama dengan header */
        z-index: 100;
    }
</style>








            <div class="container-fluid pt-4 px-4">
                <div class="bg-light text-center rounded p-4">
                    <div class="d-flex align-items-center justify-content-between mb-4">
                        <h6 class="mb-0">Master Data</h6>
                    </div>
                      <div class="container mt-5">
        <div id="statusMessage" class="mt-3"></div>
    </div>
                    <div class="table-responsive" style="max-height: 300px;">
                        <table id="tabel_serverside" class="table table-bordered display text-left" cellspacing="0" width="100%">
              <thead>
                <tr  class="text-center">
                  <th style=" text-align: center;">#</th>
                  <th style=" text-align: left;">NIP</th>
                  <th style=" text-align: left;">Nama</th>
                  <th style=" text-align: left;">Pin</th>
                  <th style=" text-align: center;">Action</th>
                </tr>
              </thead>
              <tfoot>
                <tr class="text-center">
                  <th style=" text-align: center;">#</th>
                  <th style=" text-align: left;">NIP</th>
                  <th style=" text-align: left;">Nama</th>
                  <th style=" text-align: left;">Pin</th>
                  <th style=" text-align: center;">Action</th>
                </tr>
            </tfoot>
          </table>
                    </div>
                </div>
            </div>

<!-- Recent Sales Start -->
<div class="container-fluid pt-4 px-4">
    <div class="bg-light text-center rounded p-4">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <h6 class="mb-0">DetailMaster Payroll</h6>
            <button class= "btn btn-primary create">Employee List</button>
        </div>
        <div class="table-responsive">
                   <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Karyawan ID</th>
                    <th>Nama Pegawai</th>
                    <th>PIN Pegawai</th>
                    <th>Kode Penggajian</th>
                    <th>Tanggal Awal Penggajian</th>
                    <th>Tanggal Akhir Penggajian</th>
                    <th>Total Gaji</th>
                    <th>Total Jam Kerja</th>
                    <th>Lembur 1 (Jam)</th>
                    <th>Lembur 2 (Jam)</th>
                    <th>Lembur 3 (Jam)</th>
                    <th>Jam Kerja Hari Minggu</th>
                </tr>
            </thead>
            <tbody id="data-body">
                <!-- Data akan dimuat di sini -->
            </tbody>
        </table>
</div>
</div>

</div>
<!-- Recent Sales End -->


<!-- Widgets Start -->

<!-- Widgets End -->
<div id="employeeModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="employeeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="employeeModalLabel">Employee List</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- Tabel akan diisi secara dinamis -->
                <div id="employeeTableContainer"></div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript" src="<?= base_url('assets') ?>/js/mastersalarydet.js"></script>
<script type="text/javascript" src="<?= base_url('assets') ?>/datatables/datatables.min.js"></script>