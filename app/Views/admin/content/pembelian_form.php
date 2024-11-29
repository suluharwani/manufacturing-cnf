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

<!-- Tambahkan di dalam <head> -->

<!-- Tambahkan sebelum tag penutupan </body> -->







<!-- Recent Sales Start -->
<div class="container-fluid pt-4 px-4">
                <div class="bg-light text-center rounded p-4">
                    <div class="d-flex align-items-center justify-content-between mb-4">
                       <div class="container mt-5">
  <h2>Form Pembelian</h2>
  <form>
    <div class="row mb-3">
      <label for="invoice" class="col-md-3 col-form-label">Invoice</label>
      <div class="col-md-9">
        <input type="text" class="form-control" value="<?=$pembelian[0]['invoice']?>" id="invoice" placeholder="Invoice">
      </div>
    </div>
        <div class="row mb-3">
      <label for="email" class="col-md-3 col-form-label">Supplier</label>
      <div class="col-md-9">
        <select id="supplier" class="form-control">
                        <option value="">-- Select Supplier --</option>
                        <!-- Data supplier akan di-load melalui AJAX -->
                    </select>
      </div>
    </div>
    <div class="row mb-3">
      <label for="lastName" class="col-md-3 col-form-label">Country</label>
      <div class="col-md-9">
       <input type="text" class="form-control" id="country" disabled>
      </div>
    </div>
    <div class="row mb-3">
      <label for="lastName" class="col-md-3 col-form-label">Currency</label>
      <div class="col-md-9">
        <input type="text" class="form-control" id="currency" disabled>
      </div>
    </div>
    <div class="row mb-3">
      <label for="pajak" class="col-md-3 col-form-label">Pajak</label>
      <div class="col-md-9">
        <input type="tel" class="form-control" id="pajak" placeholder="...%"> 
      </div>
    </div>

    <!-- Tombol Kirim -->
    <button type="submit" class="btn btn-primary saveSupplier">Save</button>
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
                  <th style=" text-align: center;">Harga Dasar</th>
                  <th style=" text-align: center;">Disc 1</th>
                  <th style=" text-align: center;">Disc 2</th>
                  <th style=" text-align: center;">Disc 3</th>
                  <th style=" text-align: center;">Potongan</th>
                  <th style=" text-align: center;">Pajak</th>
                  <th style=" text-align: center;">Harga Akhir/Satuan</th>
                  <th style=" text-align: center;">Kurs Rp/Satuan</th>
                  <th style=" text-align: center;">Harga Akhir</th>
                  <th style=" text-align: center;">Kurs Rp</th>
                  <th style=" text-align: center;">Action</th>
                </tr>
              </thead>
              <tfoot>
                <tr  class="text-center">
                  <th style=" text-align: center;">#</th>
                  <th style=" text-align: center;">Kode</th>
                  <th style=" text-align: center;">Nama</th>
                  <th style=" text-align: center;">Qty</th>
                  <th style=" text-align: center;">Harga Dasar</th>
                  <th style=" text-align: center;">Disc 1</th>
                  <th style=" text-align: center;">Disc 2</th>
                  <th style=" text-align: center;">Disc 3</th>
                  <th style=" text-align: center;">Potongan</th>
                  <th style=" text-align: center;">Pajak</th>
                  <th style=" text-align: center;">Harga Akhir/Satuan</th>
                  <th style=" text-align: center;">Kurs Rp/Satuan</th>
                  <th style=" text-align: center;">Harga Akhir</th>
                  <th style=" text-align: center;">Kurs Rp</th>
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
<button class="btn btn-primary" id="openAddMaterialModal">Add</button>

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
          <div class="mb-3">
            <label for="materialPrice" class="form-label">Price</label>
            <input type="number" class="form-control" id="materialPrice" required>
          </div>
          <div class="mb-3">
            <label for="materialDiscount" class="form-label">Discount</label>
            <input type="number" class="form-control" id="materialDiscount">
          </div>
          <div class="mb-3">
            <label for="materialTax" class="form-label">Tax</label>
            <input type="number" class="form-control" id="materialTax">
          </div>
          <button type="submit" class="btn btn-primary">Save</button>
        </form>
      </div>
    </div>
  </div>
</div>

<script type="text/javascript" src="<?= base_url('assets') ?>/js/form_pembelian.js"></script>
<script type="text/javascript" src="<?= base_url('assets') ?>/datatables/datatables.min.js"></script>
