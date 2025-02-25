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
        <h2>Form Scrap</h2>
        <form>
          <div class="row mb-3">
            <label for="invoice" class="col-md-3 col-form-label">Invoice</label>
            <div class="col-md-9">
              <input type="text" class="form-control" value="<?= $scrap_doc[0]['pi'] ?>" id="invoice"
                placeholder="Invoice" disabled>
            </div>
          </div>
          <div class="row mb-3">
            <label for="kode" class="col-md-3 col-form-label">WO</label>
            <div class="col-md-9">
              <input type="text" class="form-control" value="<?= $scrap_doc[0]['wo_code'] ?>" disabled>
            </div>
          </div>

          <div class="row mb-3">
            <label for="lastName" class="col-md-3 col-form-label">Scrap</label>
            <div class="col-md-9">
              <input type="text" class="form-control" value="<?= $scrap_doc[0]['code'] ?>" disabled>
              <input type="text" class="form-control" value="<?= $scrap_doc[0]['status'] ?>" id="status" disabled hidden>

            </div>
            
          </div>
          <div class="row mb-3">
            <label for="lastName" class="col-md-3 col-form-label">Department</label>
            <div class="col-md-9">
            <input type="text" class="form-control" value="<?= $scrap_doc[0]['dept_name'] ?>" disabled>
            </div>
          </div>
          <!--  -->

          <!-- Tombol Kirim -->
          <!--     <button type="button" class="btn btn-primary saveSupplier">Update Supplier</button>
    <button type="button" class="btn btn-warning saveSupplier">Import PO</button> -->

        </form>
      </div>
    </div>
  </div>
</div>
<div class="container-fluid pt-4 px-4">
  <div class="bg-light text-center rounded p-4">
    <div class="d-flex align-items-center justify-content-between mb-4">
      <h6 class="mb-0">WO Material</h6>
    </div>
    <div class="table-responsive">
                <table id="materialTable" class="table table-striped">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Material Code</th>
                            <th>Material Name</th>
                            <th>Measurement</th>
                            <th>Total Usage</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Rows will be dynamically appended here -->
                    </tbody>
                </table>

            </div>
  </div>
</div>


<div class="container-fluid pt-4 px-4">
  <div class="bg-light text-center rounded p-4">
    <div class="d-flex align-items-center justify-content-between mb-4">
      <h6 class="mb-0">Material Scrap</h6>
      <button class="btn btn-secondary " onclick="window.location.href='<?= base_url('scrap/printScrap') ?>/<?= $scrap_doc[0]['id'] ?>'">
    Print PO
</button>
      <?php
    if ($scrap_doc[0]['status'] == 0||$scrap_doc[0]['status'] == "0") {?>
    <button type="button" class="btn btn-success posting">Posting</button>
    <?php
    }else{?>
    <button type="button" class="btn btn-danger batalPosting">Batal Posting</button>
    <?php
    }
    ?>
    </div>
    <div class="table-responsive">

      <table class="table table-bordered display text-left" cellspacing="0" width="100%">
        <thead>
          <tr class="text-center">
            <th style=" text-align: center;">#</th>
            <th style=" text-align: center;">Kode</th>
            <th style=" text-align: center;">Nama</th>
            <th style=" text-align: center;">Satuan</th>
            <th style=" text-align: center;">Qty</th>
            <th style=" text-align: center;">Action</th>
          </tr>
        </thead>
        <tbody id="data-body"></tbody>
        <tfoot>
          <tr class="text-center">
            <th style=" text-align: center;">#</th>
            <th style=" text-align: center;">Kode</th>
            <th style=" text-align: center;">Nama</th>
            <th style=" text-align: center;">Satuan</th>
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



          <button type="submit" class="btn btn-primary">Save</button>
        </form>
      </div>
    </div>
  </div>
</div>

<script type="text/javascript" src="<?= base_url('assets') ?>/js/scrapdet.js"></script>
<script type="text/javascript" src="<?= base_url('assets') ?>/datatables/datatables.min.js"></script>