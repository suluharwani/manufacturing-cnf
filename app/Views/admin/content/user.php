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
      <h6 class="mb-0">Tabel User</h6>
      <button class="btn btn-primary tambah_user">Tambah</button>
    </div>
    <div class="table-responsive">
      <table id="tabel_serverside" class="table table-bordered display text-center" cellspacing="0" width="100%">
        <thead>
          <tr class="text-center">
            <th style="width: 5%; text-align: center;">NO</th>
            <th style="width: 20%;">NAMA/EMAIL</th>
            <th style="width: 15%;">LEVEL USER</th>
            <th style="width: 15%;">STATUS</th>
            <th style="width: 40%; text-align: center;">ACTION</th>
          </tr>
        </thead>
        <tfoot>
          <tr class="text-center">
            <th style="width: 5%; text-align: center;">NO</th>
            <th style="width: 20%;">NAMA/EMAIL</th>
            <th style="width: 15%;">LEVEL USER</th>
            <th style="width: 15%;">STATUS</th>
            <th style="width: 40%; text-align: center;">ACTION</th>
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

<script type="text/javascript" src="<?= base_url('assets') ?>/datatables/datatables.min.js"></script>

<script type="text/javascript" src="<?= base_url('assets') ?>/js/user.js"></script>
