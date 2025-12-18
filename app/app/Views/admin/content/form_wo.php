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
<?php
// var_dump($wo[0]);
// die();
?>


<!-- Recent Sales Start -->
<div class="container-fluid pt-4 px-4">
                <div class="bg-light text-center rounded p-4">
                    <div class="d-flex align-items-center justify-content-between mb-4">
                       <div class="container mt-5">
  <h2>Form WO</h2>
  <form>
    <div class="row mb-3">
      <label for="invoice" class="col-md-3 col-form-label">Invoice</label>
      <div class="col-md-9">
        <input type="text" class="form-control" value="<?=$wo[0]['invoice_number']?>" id="invoice" placeholder="Invoice" disabled> 
      </div>
    </div>
    <div class="row mb-3">
      <label for="kode" class="col-md-3 col-form-label">WO</label>
      <div class="col-md-9">
        <input type="text" class="form-control" value="<?=$wo[0]['kode']?>" id="kode"disabled>
      </div>
    </div>
        <div class="row mb-3">
      <label for="email" class="col-md-3 col-form-label">Customer</label>
      <div class="col-md-9">
                <input type="text" class="form-control" value="<?=$wo[0]['customer_name']?>" id="customer" placeholder="customer" disabled>

      </div>
    </div>
    <div class="row mb-3">
      <label for="lastName" class="col-md-3 col-form-label">Address</label>
      <div class="col-md-9">
       <input type="text" class="form-control" id="country" value="<?=$wo[0]['customer_address']?>" disabled>
      </div>
    </div>
    <div class="row mb-3">
      <label for="lastName" class="col-md-3 col-form-label">Currency</label>
      <div class="col-md-9">
        <input type="text" class="form-control" id="currency" value="<?=$wo[0]['curr_name']?>" disabled>
      </div>
    </div>
    <div class="row mb-3">
      <label for="lastName" class="col-md-3 col-form-label">RELEASE DATE</label>
      <div class="col-md-9">
        <input type="date" class="form-control" id="release_date" value="<?=$wo[0]['release_date']?>" >
      </div>
    </div>
    <div class="row mb-3">
      <label for="lastName" class="col-md-3 col-form-label">MANUFACTURE FINISHES</label>
      <div class="col-md-9">
        <input type="date" class="form-control" id="manufacture_finishes" value="<?=$wo[0]['manufacture_finishes']?>">
      </div>
    </div>
    <div class="row mb-3">
      <label for="lastName" class="col-md-3 col-form-label">LOADING DATE</label>
      <div class="col-md-9">
        <input type="date" class="form-control" id="loading_date" value="<?=$wo[0]['loading_date']?>">
      </div>
    </div>
<!--  -->

    <!-- Tombol Kirim -->
<!--     <button type="button" class="btn btn-primary saveSupplier">Update Supplier</button>
    <button type="button" class="btn btn-warning saveSupplier">Import PO</button> -->

  </form>
  <button type="button" class="btn btn-primary updateWO">Update</button>
</div>
                    </div>
                           </div>
            </div>
               <div class="container-fluid pt-4 px-4">
             <div class="bg-light text-center rounded p-4">
                    <div class="d-flex align-items-center justify-content-between mb-4">
                        <h6 class="mb-0">Product PI tersedia</h6>
                    </div>
                    <div class="table-responsive">

                    <table  class="table table-bordered display text-left" cellspacing="0" width="100%">
              <thead>
                <tr  class="text-center">
                  <th style=" text-align: center;">#</th>
                  <th style=" text-align: center;">Kode</th>
                  <th style=" text-align: center;">Nama</th>
                  <th style=" text-align: center;">Qty</th>
                  <th style=" text-align: center;">Action</th>
                </tr>
              </thead>
              <tbody id="tabel_pi"></tbody>
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


            <div class="container-fluid pt-4 px-4">
             <div class="bg-light text-center rounded p-4">
                    <div class="d-flex align-items-center justify-content-between mb-4">
                        <h6 class="mb-0">Product WO</h6>
                        <button class="btn btn-secondary alokasiPI" onclick="window.location.href='<?= base_url('printWO') ?>/<?= $wo[0]['id_wo'] ?>'">
    Print WO
</button>
                        <button class= "btn btn-primary addMaterial">Add</button>
                    </div>
                    <div class="table-responsive">

                    <table class="table table-bordered display text-left" cellspacing="0" width="100%">
              <thead>
                <tr  class="text-center">
                  <th style=" text-align: center;">#</th>
                  <th style=" text-align: center;">Kode</th>
                  <th style=" text-align: center;">Nama</th>
                  <th style=" text-align: center;">Qty</th>
                  <th style=" text-align: center;">Action</th>
                </tr>
              </thead>
               <tbody id="tabel_wo"></tbody>
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
        <form id="addForm">
          <div class="mb-3">
            <label for="id_product" class="form-label">Product</label>
           <select class="form-control" id="id_product" required>
              <option value="">Select Product</option>
            </select>
          </div>
          <div class="mb-3">
            <label for="quantity" class="form-label">Quantity</label>
            <input type="number" class="form-control" id="quantity" required>
          </div>
          <div class="mb-3">
            <label for="unit_price" class="form-label">Harga</label>
            <input type="number" class="form-control" id="unit_price" required> <span> Currency: <?=$wo[0]['curr_code'].'-'.$wo[0]['curr_name']?></span>
            <input type="text" class="form-control" id="id_currency" value="<?=$wo[0]['curr_id']?>" hidden>
            
          </div>
         

          <button type="submit" class="btn btn-primary">Save</button>
        </form>
      </div>
    </div>
  </div>
</div>

<script type="text/javascript" src="<?= base_url('assets') ?>/js/form_wo.js"></script>
<script type="text/javascript" src="<?= base_url('assets') ?>/datatables/datatables.min.js"></script>
