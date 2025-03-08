<link rel="stylesheet" type="text/css" href="<?= base_url('assets') ?>/datatables/datatables.min.css" />
<style>
body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }

        .table-container {
            width: 100%; /* Memastikan div memenuhi lebar kontainer */
            overflow-x: auto; /* Menambahkan scroll horizontal jika diperlukan */
            border: 1px solid #ccc; /* Border untuk div */
            border-radius: 5px; /* Sudut melengkung */
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1); /* Bayangan */
        }

        table {
            width: 100%; /* Tabel memenuhi lebar div */
            border-collapse: collapse; /* Menghilangkan jarak antara border sel */
        }

        th, td {
            padding: 12px; /* Ruang dalam sel */
            text-align: left; /* Rata kiri */
            border-bottom: 1px solid #ddd; /* Garis bawah sel */
        }

        th {
            background-color: #f2f2f2; /* Warna latar belakang header */
        }

        tr:hover {
            background-color: #f1f1f1; /* Warna latar belakang saat hover */
        }
</style>



<!-- Recent Sales Start -->
<div class="container-fluid pt-4 px-4">
  <div class="bg-light text-center rounded p-4">
    <div class="d-flex align-items-center justify-content-between mb-4">
      <div class="container mt-5">
        <h2>Form PI</h2>
        <form>
          <div class="row mb-3">
            <label for="invoice" class="col-md-3 col-form-label">Invoice</label>
            <div class="col-md-9">
              <input type="text" class="form-control" value="<?= $pi[0]['invoice_number'] ?>" id="invoice"
                placeholder="Invoice" disabled>
            </div>
          </div>
          <!--     <div class="row mb-3">
      <label for="invoice" class="col-md-3 col-form-label">PO</label>
      <div class="col-md-9">
        <input type="text" class="form-control" value="" id="invoice" placeholder="Invoice" disabled>
      </div>
    </div> -->
          <div class="row mb-3">
            <label for="email" class="col-md-3 col-form-label">Customer</label>
            <div class="col-md-9">
              <input type="text" class="form-control" value="<?= $pi[0]['customer_name'] ?>" id="customer"
                placeholder="customer" disabled>

            </div>
          </div>
          <div class="row mb-3">
            <label for="country_name" class="col-md-3 col-form-label">Country</label>
            <div class="col-md-9">
              <input type="text" class="form-control" id="country_name" value="<?= $pi[0]['country_name'] ?>" disabled>
            </div>
          </div>
          <div class="row mb-3">
            <label for="customer_address" class="col-md-3 col-form-label">Address</label>
            <div class="col-md-9">
              <textarea class="form-control" rows="4" id="customer_address"><?= $pi[0]['customer_address'] ?></textarea>
            </div>
          </div>
          <div class="row mb-3">
            <label class="col-md-3 col-form-label">Currency</label>
            <div class="col-md-9">
              <input type="text" class="form-control" value="<?= $pi[0]['curr_name'] ?>" disabled>
            </div>
          </div>
          <div class="row mb-3">
            <label class="col-md-3 col-form-label">ETD</label>
            <div class="col-md-9">
              <input type="date" class="form-control" value="<?= $pi[0]['etd'] ?>">
            </div>
          </div>
          <div class="row mb-3">
            <label class="col-md-3 col-form-label">ETA</label>
            <div class="col-md-9">
              <input type="date" class="form-control" value="<?= $pi[0]['eta'] ?>">
            </div>
          </div>
          <div class="row mb-3">
            <label class="col-md-3 col-form-label">Status</label>
            <div class="col-md-9">
              <select name="status_delivery" class="form-control" id="status_delivery">
                <option class="form-control" value="0" <?php if ($pi[0]['status_delivery'] == 0) echo 'selected' ?>>On Production Progress</option>
                <option class="form-control" value="1" <?php if ($pi[0]['status_delivery'] == 1) echo 'selected' ?>>Waiting Delivery</option>
                <option class="form-control" value="2" <?php if ($pi[0]['status_delivery'] == 2) echo 'selected' ?>>Delivered</option>
              </select>
              
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
      <h6 class="mb-0">Product</h6>
      <?php if ($pi[0]['status'] == 1){?>
        <button class="btn btn-warning printButton"> Print Invoice</button>
        <button class="btn btn-warning printDeliveryButton"> Print Delivery Note</button>
        <button class="btn btn-warning batalFinish"> Cancel Finish Production</button>
      <?php } else{?>
        <button class="btn btn-primary finish">Finish Production</button>
      <?php } ?>
    </div>
    <div class="table-responsive">

      <table id="tabel_serverside" class="table table-bordered display text-left" cellspacing="0" width="100%">
        <thead>
          <tr class="text-center">
            <th style=" text-align: center;">#</th>
            <th style=" text-align: center;">Kode</th>
            <th style=" text-align: center;">Nama</th>
            <th style=" text-align: center;">Qty</th>
            <th style=" text-align: center;">Price</th>
            <th style=" text-align: center;">Status</th>
            <th style=" text-align: center;">Action</th>
          </tr>
        </thead>
        <tfoot>
          <tr class="text-center">
            <th style=" text-align: center;">#</th>
            <th style=" text-align: center;">Kode</th>
            <th style=" text-align: center;">Nama</th>
            <th style=" text-align: center;">Qty</th>
            <th style=" text-align: center;">Price</th>
            <th style=" text-align: center;">Status</th>
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
      <h6 class="mb-0">Document</h6>
      <button class="btn btn-primary" id="uploadBtn">Add</button>
    </div>
    <div class="table-responsive">

      <table id="tabel_serverside_file" class="table table-bordered display text-left" cellspacing="0" width="100%">
        <thead>

          <tr class="text-center">
            <th style=" text-align: center;">#</th>
            <th style=" text-align: center;">Date</th>
            <th style=" text-align: center;">Code</th>
            <th style=" text-align: center;">Document</th>
            <th style=" text-align: center;">File</th>
            <th style=" text-align: center;">Action</th>
          </tr>
        </thead>
        <tfoot>
        <tr class="text-center">
            <th style=" text-align: center;">#</th>
            <th style=" text-align: center;">Date</th>
            <th style=" text-align: center;">Code</th>
            <th style=" text-align: center;">Document</th>
            <th style=" text-align: center;">File</th>
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
  <div class="modal-dialog ">
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
            <input type="number" class="form-control" id="unit_price" required> <span> Currency:
              <?= $pi[0]['curr_code'] . '-' . $pi[0]['curr_name'] ?></span>
            <input type="text" class="form-control" id="id_currency" value="<?= $pi[0]['curr_id'] ?>" hidden>

          </div>


          <button type="submit" class="btn btn-primary">Save</button>
        </form>
      </div>
    </div>
  </div>
</div>
<!-- Modal -->
<div class="modal fade" id="prodView" tabindex="-1" aria-labelledby="prodViewLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="prodViewLabel">Production</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="d-flex justify-content-between">
        <div class="card border-warning mb-3" style="max-width: 18rem;">
            <div class="card-header bg-warning text-white">Order</div>
            <div class="card-body text-warning text-center">
              
              <p class="card-text"><h1><span id="totalOrder">0</span></h1></p>
            </div>
          </div>
          <div class="card border-warning mb-3" style="max-width: 18rem;">
            <div class="card-header bg-warning text-white">Production</div>
            <div class="card-body text-warning text-center">
              
              <p class="card-text"><h1><span id="qtprod">0</span></h1></p>
            </div>
          </div>
          <div class="card border-success mb-3" style="max-width: 18rem;">
            <div class="card-header bg-success text-white">Warehouse</div>
            <div class="card-body text-success text-center">
              
              <p class="card-text"><h1><span id="qtwh">0</span></h1></p>
            </div>
          </div>
          <div class="card border-danger mb-3" style="max-width: 18rem;">
            <div class="card-header bg-danger text-white">Waiting Progress</div>
            <div class="card-body text-danger text-center">
              
              <p class="card-text"><h1><span id="unProgress">0</span></h1></p>
            </div>
          </div>
          <div class="card border-primary mb-3" style="max-width: 18rem;">
            <div class="card-header bg-primary text-white">Total Production</div>
            <div class="card-body text-primary text-center">
              
              <p class="card-text"><h1><span id="totalProd">0</span></h1></p>
            </div>
          </div>
        </div>
        <div id="resultTableContainer" class="table-responsive">
        </div>
      </div>
    </div>
  </div>
</div>


                        <!-- Results will be displayed here -->
<script type="text/javascript" src="<?= base_url('assets') ?>/js/form_pi_doc.js"></script>
<script type="text/javascript" src="<?= base_url('assets') ?>/datatables/datatables.min.js"></script>