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
                        <h6 class="mb-0">Data</h6> 
                        <button class= "btn btn-primary tambah">ADD</button>
                    </div>
                    <div class="table-responsive">
                    <table id="tabel_serverside" class="table table-bordered display text-left" cellspacing="0" width="100%">
              <thead>
                <tr  class="text-center">
                  <th style=" text-align: center;">#</th>
                  <th style=" text-align: center;">DATE</th>
                  <th style=" text-align: center;">CODE</th>
                  <th style=" text-align: center;">PI</th>
                  <th style=" text-align: center;">WO</th>
                  <th style=" text-align: center;">DEPARTMENT</th>
                  <th style=" text-align: center;">ADMIN</th>
                  <th style=" text-align: center;">REQUESTOR</th>
                  <th style=" text-align: center;">STATUS</th>
                  <th style=" text-align: center;">Action</th>
                </tr>
              </thead>
              <tfoot>
                <tr  class="text-center">
                <th style=" text-align: center;">#</th>
                  <th style=" text-align: center;">DATE</th>
                  <th style=" text-align: center;">CODE</th>
                  <th style=" text-align: center;">PI</th>
                  <th style=" text-align: center;">WO</th>
                  <th style=" text-align: center;">DEPARTMENT</th>
                  <th style=" text-align: center;">ADMIN</th>
                  <th style=" text-align: center;">REQUESTOR</th>
                  <th style=" text-align: center;">STATUS</th>
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
        <h5 class="modal-title" id="tambahLabel">Requisition</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="addReq">
        <div class="mb-3">
            <label for="mr" class="form-label">MR</label>
            <input type="text" class="form-control" id="mr" required>
          </div>
          <div class="mb-3">
            <label for="mr" class="form-label">Requestor</label>
            <input type="text" class="form-control" id="requestor" value="<?= session()->get('auth')['name'] ?>" required>
          </div>
          <div class="mb-3">
            <label for="department" class="form-label">DEPARTMENT</label>
           <select class="form-control" id="department" required>
              <option value="">Select Department</option>
              <!-- Material options will be added here -->
            </select>
          </div>
          <div class="mb-3">
            <label for="work_order" class="form-label">WORK ORDER</label>
           <select class="form-control" id="work_order">
              <option value="">Selct WO</option>
              <!-- Material options will be added here -->
            </select>
          </div>
          <div class="mb-3">
            <label for="mr" class="form-label">Remarks</label>
            <input type="text" class="form-control" id="remarks" required>
          </div>
          <button type="submit" class="btn btn-primary">Save</button>
        </form>
      </div>
    </div>
  </div>
</div>
<!-- Widgets Start -->

<!-- Widgets End -->

<script type="text/javascript" src="<?= base_url('assets') ?>/js/material_requisition.js"></script>
<script type="text/javascript" src="<?= base_url('assets') ?>/datatables/datatables.min.js"></script>