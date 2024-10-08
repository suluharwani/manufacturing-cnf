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
                        <!-- <button class= "btn btn-primary">Tambah</button> -->
                    </div>
                    <div class="table-responsive">
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
              </tr>
            </tfoot>
          </table>
                    </div>
                </div>
            </div>
<!-- Recent Sales End -->


<!-- Widgets Start -->

<!-- Widgets End -->
<!-- <div>
  <button id="selectDateBtn" class="btn btn-info">Pilih Tanggal Presensi</button>
</div> -->

<!-- Date Selection Modal -->
<!-- Date Selection Modal -->
<div class="modal fade" id="dateSelectionModal" tabindex="-1" role="dialog" aria-labelledby="dateSelectionModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="dateSelectionModalLabel">Pilih Tanggal Presensi</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="form-group">
          <label for="startDate">Tanggal Awal:</label>
          <input type="date" id="startDate" class="form-control" required>
        </div>
        <div class="form-group">
          <label for="endDate">Tanggal Akhir:</label>
          <input type="date" id="endDate" class="form-control" required>
        </div>
      </div>
      <div class="modal-footer">
        <button id="fetchPresensi" class="btn btn-primary">Tampilkan Presensi</button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
      </div>
    </div>
  </div>
</div>

<!-- Popup Modal for displaying attendance details -->
<div class="modal fade" id="popupModal" tabindex="-1" role="dialog" aria-labelledby="popupModalLabel" aria-hidden="true">
  <div class="modal-dialog  modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="popupModalLabel">Detail Presensi</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body" id="popupContent">
        <!-- Content will be dynamically populated -->
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
      </div>
    </div>
  </div>
</div>




<script type="text/javascript" src="<?= base_url('assets') ?>/js/employee.js"></script>
<script type="text/javascript" src="<?= base_url('assets') ?>/datatables/datatables.min.js"></script>