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
  <h2>Form PO</h2>
  <form>
    <div class="row mb-3">
      <label for="invoice" class="col-md-3 col-form-label">Invoice</label>
      <div class="col-md-9">
        <input type="text" class="form-control" value="<?=$po[0]['code']?>" id="invoice" placeholder="Invoice" disabled> 
      </div>
    </div>
<!--     <div class="row mb-3">
      <label for="invoice" class="col-md-3 col-form-label">PO</label>
      <div class="col-md-9">
        <input type="text" class="form-control" value="" id="invoice" placeholder="Invoice" disabled>
      </div>
    </div> -->

        <div class="row mb-3">
      <label for="email" class="col-md-3 col-form-label">Supplier</label>
      <div class="col-md-9">
                <input type="text" class="form-control" value="<?=$po[0]['supplier_name']?>"  disabled>

      </div>
    </div>
    <div class="row mb-3">
      <label for="country_name" class="col-md-3 col-form-label">Country</label>
      <div class="col-md-9">
       <input type="text" class="form-control" id="country_name" value="<?=$po[0]['country_name']?>" disabled>
      </div>
    </div>
    <div class="row mb-3">
      <label for="supplier_address" class="col-md-3 col-form-label">Address</label>
      <div class="col-md-9">
       <textarea class="form-control"  rows="4" id="supplier_address" disabled><?=$po[0]['supplier_address']?></textarea>
      </div>
    </div>
    <div class="row mb-3">
      <label  class="col-md-3 col-form-label">Currency</label>
      <div class="col-md-9">
        <input type="text" class="form-control"  value="<?=$po[0]['curr_name']?>" disabled>
      </div>
    </div>
    <div class="row mb-3">
      <label  class="col-md-3 col-form-label">Date</label>
      <div class="col-md-9">
        <input type="date" class="form-control"  value="<?=$po[0]['date']?>" >
      </div>
    </div>
    <div class="row mb-3">
      <label  class="col-md-3 col-form-label">Arrival Target</label>
      <div class="col-md-9">
        <input type="date" class="form-control"  value="<?=$po[0]['arrival_target']?>" >
      </div>
    </div>
<!--  -->

    <!-- Tombol Kirim -->
<!--     <button type="button" class="btn btn-primary saveSupplier">Update Supplier</button>
    <button type="button" class="btn btn-warning saveSupplier">Import PO</button> -->

  </form>
                        <button class= "btn btn-secondary updatePI">Update</button>
                        <button class= "btn btn-primary statusPI">Status</button>
                        <button class= "btn btn-secondary alokasiPI">Alokasi Material</button>
                        <button class= "btn btn-warning purchaseRequestPI">Purchase Request</button>
                        <button class= "btn btn-success postingPI">Posting</button>

</div>
                    </div>
                           </div>
            </div>
            <div class="container-fluid pt-4 px-4">
             <div class="bg-light text-center rounded p-4">
                    <div class="d-flex align-items-center justify-content-between mb-4">
                        <h6 class="mb-0">Material</h6>
                        <div>
                        <button class= "btn btn-primary importMaterial">Import</button>
                        <button class= "btn btn-primary addMaterial">Add</button>
                        </div>
                        
                    </div>
                    <div class="table-responsive">

                    <table id="tabel_serverside" class="table table-bordered display text-left" cellspacing="0" width="100%">
              <thead>
                <tr  class="text-center">
                  <th style=" text-align: center;">#</th>
                  <th style=" text-align: center;">Kode</th>
                  <th style=" text-align: center;">Nama</th>
                  <th style=" text-align: center;">Qty</th>
                  <th style=" text-align: center;">Price</th>
                  <th style=" text-align: center;">Remarks</th>
                  <th style=" text-align: center;">Action</th>
                </tr>
              </thead>
              <tfoot>
                <tr  class="text-center">
                  <th style=" text-align: center;">#</th>
                  <th style=" text-align: center;">Kode</th>
                  <th style=" text-align: center;">Nama</th>
                  <th style=" text-align: center;">Qty</th>
                  <th style=" text-align: center;">Price</th>
                  <th style=" text-align: center;">Remarks</th>
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
            <label for="id_material" class="form-label">Material</label>
           <select class="form-control" id="id_material" required>
              <option value="">Select Material</option>
            </select>
          </div>
          <div class="mb-3">
            <label for="quantity" class="form-label">Quantity</label>
            <input type="number" class="form-control" id="quantity" required>
          </div>
          <div class="mb-3">
            <label for="unit_price" class="form-label">Harga</label> <br>
            <span>Supplier Currency: <?=$po[0]['curr_code'].'-'.$po[0]['curr_name']?></span>
            <input type="number" step="any" class="form-control" id="unit_price"> 
            <input type="text" class="form-control" id="id_currency" value="<?=$po[0]['curr_id']?>" hidden>
            <div class="mb-3">
            <label for="remarks" class="form-label">remarks</label>
            <input type="text" class="form-control" id="remarks" >
          </div>
          </div>
         

          <button type="submit" class="btn btn-primary">Save</button>
        </form>
      </div>
    </div>
  </div>
</div>

<script type="text/javascript" src="<?= base_url('assets') ?>/js/form_po.js"></script>
<script type="text/javascript" src="<?= base_url('assets') ?>/datatables/datatables.min.js"></script>
