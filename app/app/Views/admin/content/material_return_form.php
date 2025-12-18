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
                       <div class="container mt-5">
  <h2>Form Material Return</h2>
  <form>
    <?php 
    ?>
    <div class="row mb-3">
      <label for="kode" class="col-md-3 col-form-label">Code</label>
      <div class="col-md-9">
        <input type="text" class="form-control" value="<?=$material_return[0]['code']?>" id="kode" disabled placeholder="kode">
      </div>
    </div>
    <div class="row mb-3">
      <label for="wo" class="col-md-3 col-form-label">WO</label>
      <div class="col-md-9">
        <input type="text" class="form-control" value="<?=$material_return[0]['wo']?>" id="wo" disabled placeholder="wo">
      </div>
    </div>
    <div class="row mb-3">
      <label for="wo" class="col-md-3 col-form-label">DEPARTMENT</label>
      <div class="col-md-9">
        <input type="text" class="form-control" value="<?=$material_return[0]['dept']?> " id="department" disabled placeholder="department">
      </div>
    </div>
    <div class="row mb-3">
      <label for="wo" class="col-md-3 col-form-label">ADMIN</label>
      <div class="col-md-9">
        <input type="text" class="form-control" value="<?=$material_return[0]['nama_depan']?> <?=$material_return[0]['nama_belakang']?>" id="admin" disabled placeholder="admin">
      </div>
    </div>
  

    <!-- Tombol Kirim -->
    <button type="button" class="btn btn-primary saveSupplier">Update Supplier</button>
    <button type="button" class="btn btn-warning saveSupplier">Import PO</button>
    <?php
    if ($material_return[0]['status'] == 0||$material_return[0]['status'] == "0") {?>
    <button type="button" class="btn btn-success postingPembelian">Posting Pembelian</button>
    <?php
    }else{?>
    <button type="button" class="btn btn-danger batalPostingPembelian">Batal Posting Pembelian</button>
    <?php
    }
    ?>
  </form>
</div>
                    </div>
                           </div>
            </div>
            <div class="container-fluid pt-4 px-4">
             <div class="bg-light text-center rounded p-4">
                    <div class="d-flex align-items-center justify-content-between mb-4">
                        <h6 class="mb-0">Pembelian</h6>
                        <button class= "btn btn-primary addMaterial">Add</button>
                    </div>
                    <div class="table-responsive">

                    <table id="tabel_serverside" class="table table-bordered display text-left" cellspacing="0" width="100%">
              <thead>
                <tr  class="text-center">
                  <th style=" text-align: center;">#</th>
                  <th style=" text-align: center;">Kode</th>
                  <th style=" text-align: center;">Nama</th>
                  <th style=" text-align: center;">Qty</th>
                  <th style=" text-align: center;">Action</th>
                </tr>
              </thead>
              <tfoot>
                <tr  class="text-center">
                  <th style=" text-align: center;">#</th>
                  <th style=" text-align: center;">Kode</th>
                  <th style=" text-align: center;">Nama</th>
                  <th style=" text-align: center;">Qty</th>
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

<!-- Modal -->
<div class="modal fade" id="addMaterialModal" tabindex="-1" aria-labelledby="addMaterialModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="addMaterialModalLabel">Add Material</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="addMaterialForm">
          <div class="mb-3">
            <label for="materialCode" class="form-label">Material</label>
           <select class="form-control" id="materialCode" required>
              <option value="">Select Material</option>
              <!-- Material options will be added here -->
            </select>
          </div>
          <div class="mb-3">
            <label for="materialQty" class="form-label">Quantity</label>
            <input type="number" class="form-control" id="materialQty" required>
          </div>
         
          <button type="submit" class="btn btn-primary">Save</button>
        </form>
      </div>
    </div>
  </div>
</div>

<script type="text/javascript" src="<?= base_url('assets') ?>/js/material_return_list.js"></script>
<script type="text/javascript" src="<?= base_url('assets') ?>/datatables/datatables.min.js"></script>
