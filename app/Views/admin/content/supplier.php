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







<!-- Recent Sales Start -->
<div class="container-fluid pt-4 px-4">
                <div class="bg-light text-center rounded p-4">
                    <div class="d-flex align-items-center justify-content-between mb-4">
                        <h6 class="mb-0">Persediaan Material</h6>
                        <button class= "btn btn-primary">Tambah</button>
                    </div>
                    <div class="table-responsive">
                    <table id="tabel_serverside" class="table table-bordered display text-left" cellspacing="0" width="100%">
              <thead>
                <tr  class="text-center">
                  <th style=" text-align: center;">#</th>
                  <th style=" text-align: center;">Kode</th>
                  <th style=" text-align: center;">Nama</th>
                  <th style=" text-align: center;">Kelompok</th>
                  <th style=" text-align: center;">Purchase Order</th>
                  <th style=" text-align: center;">Sales Order</th>
                  <th style=" text-align: center;">Tersedia</th>
                  <th style=" text-align: center;">Tersedia Sales Order</th>
                  <th style=" text-align: center;">Satuan</th>
                  <th style=" text-align: center;">Aktif</th>
                </tr>
              </thead>
              <tfoot>
                <tr class="text-center">
                <th style=" text-align: center;">#</th>
                  <th style=" text-align: center;">Kode</th>
                  <th style=" text-align: center;">Nama</th>
                  <th style=" text-align: center;">Kelompok</th>
                  <th style=" text-align: center;">Purchase Order</th>
                  <th style=" text-align: center;">Sales Order</th>
                  <th style=" text-align: center;">Tersedia</th>
                  <th style=" text-align: center;">Tersedia Sales Order</th>
                  <th style=" text-align: center;">Satuan</th>
                  <th style=" text-align: center;">Aktif</th>
                </tr>
              </tr>
            </tfoot>
          </table>
                    </div>
                </div>
            </div>
<!-- Recent Sales End -->


<!-- Widgets Start -->

<!-- Widgets End -->

<script type="text/javascript" src="<?= base_url('assets') ?>/js/supplier.js"></script>
<script type="text/javascript" src="<?= base_url('assets') ?>/datatables/datatables.min.js"></script>