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
                        <h6 class="mb-0">Material Destruction</h6>
                        <button class= "btn btn-primary tambah">Tambah</button>
                    </div>
                    <div class="table-responsive">
                    <table id="tabel_serverside" class="table table-bordered display text-left" cellspacing="0" width="100%">
              <thead>
                <tr  class="text-center">
                  <th style=" text-align: center;">#</th>
                  <th style=" text-align: center;">Tanggal</th>
                  <th style=" text-align: center;">Department</th>
                  <th style=" text-align: center;">Invoice</th>
                  <th style=" text-align: center;">Status</th>
                  <th style=" text-align: center;">Action</th>
                </tr>
              </thead>
              <tfoot>
                <tr  class="text-center">
                  <th style=" text-align: center;">#</th>
                  <th style=" text-align: center;">Tanggal</th>
                  <th style=" text-align: center;">Department</th>
                  <th style=" text-align: center;">Invoice</th>
                  <th style=" text-align: center;">Status</th>
                  <th style=" text-align: center;">Action</th>
                </tr>
              </tr>
            </tfoot>
          </table>
                    </div>
                </div>
            </div>
<!-- Recent Sales End -->

<div class="modal fade" id="tambah" tabindex="-1" aria-labelledby="addMaterialModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content"> 
      <div class="modal-header">
        <h5 class="modal-title" id="tambahLabel">Material Destruction</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="addForm">
          <div class="mb-3">
          <div class="mb-3">
            <label for="materialQty" class="form-label">Document Code</label>
            <input type="text" class="form-control" id="code" required>
          </div>
            <label for="department" class="form-label">Department</label>
           <select class="form-control" id="department" required>
              <option value="">Select Department</option>
              <!-- Material options will be added here -->
            </select>
          </div>
          <div class="mb-3">
            <label for="pajak" class="form-label">Remarks</label>
            <textarea class="form-control" id="remarks"></textarea>
          </div>
          <div class="mb-3">
            NOTE: Bila material ada di produksi, harap dikembalikan dulu ke logistik untuk dihapus dari inventory.
          </div>
          <button type="submit" class="btn btn-primary">Save</button>
        </form>
      </div>
    </div>
  </div>
</div>
<!-- Widgets Start -->

<!-- Widgets End -->

<script type="text/javascript" src="<?= base_url('assets') ?>/js/material_destruction.js"></script>
<script type="text/javascript" src="<?= base_url('assets') ?>/datatables/datatables.min.js"></script>