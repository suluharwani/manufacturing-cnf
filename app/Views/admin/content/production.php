<link rel="stylesheet" type="text/css" href="<?=base_url('assets')?>/datatables/datatables.min.css"/>
<style>
        /* Tambahan minimal CSS untuk fixed header */
        thead th {
            position: sticky;
            top: 0;
            background-color: #343a40; /* Warna background yang sama dengan header */
            z-index: 100;
        }
    </style>




            <!-- Sales Chart Start -->
            <div class="container-fluid pt-4 px-4">
                <div class="row g-4">
                    <div class="col-sm-12 col-xl-6">
                        <div class="bg-light text-center rounded p-4">
                            <div class="d-flex align-items-center justify-content-between mb-4">
                                <h6 class="mb-0">Warehouse</h6>
                            </div>
                            <!-- jenis barang -->
                            <div class="table-responsive" style="max-height: 300px;">
            <table class="table table-striped table-bordered">
                <thead class="table-dark">
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Nama</th>
                        <th scope="col">Location</th>
                        <th scope="col">Action</th>
                    </tr>
                </thead>
                <tbody id="isiWarehouse">
                 </tbody>
            </table>
        </div>
                             <!-- end jenis barang -->
                        </div>
                    </div>
                    <div class="col-sm-12 col-xl-6">
                        <div class="bg-light text-center rounded p-4">
                            <div class="d-flex align-items-center justify-content-between mb-4">
                                <h6 class="mb-0">Production</h6>
                            </div>
                                                       <!-- satuan barang -->
                                                       <div class="table-responsive" style="max-height: 300px;">
            <table class="table table-striped table-bordered">
                <thead class="table-dark">
                     <tr>
                        <th scope="col">#</th>
                        <th scope="col">Nama</th>
                        <th scope="col">Location</th>
                        <th scope="col">Action</th>
                    </tr>
                </thead>
                <tbody id= "isiProduction"></tbody>
            </table>
        </div>
                             <!-- end satuan barang -->
                        </div>
                    </div>
                </div>
            </div>
            <!-- Sales Chart End -->


            <!-- Recent Sales Start -->
            <div class="container-fluid pt-4 px-4">
                <div class="bg-light text-center rounded p-4">
                    <div class="d-flex align-items-center justify-content-between mb-4">
                        <h6 class="mb-0">Working Order</h6>
                    </div>
                    <div class="table-responsive">
                    <table id="tabel_serverside" class="table table-bordered display text-left" cellspacing="0" width="100%">
              <thead>
                <tr  class="text-center">
                  <th style=" text-align: center;">#</th>
                  <th style=" text-align: center;">Proforma Invoice</th>
                  <th style=" text-align: center;">WO</th>
                  <th style=" text-align: center;">Start</th>
                  <th style=" text-align: center;">End</th>
                  <th style=" text-align: center;">Action</th>
                </tr>
              </thead>
              <tfoot>
               <tr  class="text-center">
                  <th style=" text-align: center;">#</th>
                  <th style=" text-align: center;">Proforma Invoice</th>
                  <th style=" text-align: center;">WO</th>
                  <th style=" text-align: center;">Start</th>
                  <th style=" text-align: center;">End</th>
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
     <div class="modal fade" id="woModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Data Table</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <!-- Table inside modal -->
        <table class="table table-striped">
          <thead>
            <tr>
              <th scope="col">NO</th>
              <th scope="col">PROFORMA INVOICE</th>
              <th scope="col">WORK ORDER</th>
              <th scope="col">ACTION</th>
            </tr>
          </thead>
          <tbody id="tableBody">
          
          </tbody>
        </table>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<!-- Large Modal -->
<div class="modal fade" id="ProdModal" tabindex="-1" aria-labelledby="ProdModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg"> <!-- Use the modal-lg class for a large modal -->
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="ProdModalLabel">Data Table</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <!-- Table inside modal -->
        <table class="table table-striped">
          <thead>
            <tr>
              <th scope="col">NO</th>
              <th scope="col">PROFORMA INVOICE</th>
              <th scope="col">WORK ORDER</th>
              <th scope="col">PRODUCT</th>
              <th scope="col">QUANTITY</th>
              <th scope="col">ACTION</th>
            </tr>
          </thead>
          <tbody id="tableProd">
            <!-- Dynamic data will be inserted here -->
          </tbody>
        </table>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="progressModal" data-bs-focus="false" tabindex="-1" aria-labelledby="ProdModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg"> <!-- Use the modal-lg class for a large modal -->
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="ProdModalLabel">Data Table</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <!-- Table inside modal -->
        <table class="table table-striped">
          <thead>
            <tr>
              <th scope="col">NO</th>
              <th scope="col">PROFORMA INVOICE</th>
              <th scope="col">WORK ORDER</th>
              <th scope="col">PRODUCT</th>
              <th scope="col">QUANTITY</th>
              <th scope="col">ACTION</th>
            </tr>
          </thead>
          <tbody id="tableProd">
            <!-- Dynamic data will be inserted here -->
          </tbody>
        </table>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
<div class="modal fade" id="productionModal" data-bs-focus="false" tabindex="-1" aria-labelledby="ProdModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg"> <!-- Use the modal-lg class for a large modal -->
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="ProdModalLabel">Data Table</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <!-- Table inside modal -->
        <table class="table table-striped">
          <thead>
            <tr>
              <th scope="col">NO</th>
              <th scope="col">KODE</th>
              <th scope="col">NAMA</th>
              <th scope="col">QUANTITY</th>
              <th scope="col">ACTION</th>
            </tr>
          </thead>
          <tbody id="tableProdProgress">
            <!-- Dynamic data will be inserted here -->
          </tbody>
        </table>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

            <script type="text/javascript" src="<?=base_url('assets')?>/js/production.js"></script>
            <script type="text/javascript" src="<?=base_url('assets')?>/datatables/datatables.min.js"></script> 
