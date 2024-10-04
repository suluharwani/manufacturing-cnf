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
                                <h6 class="mb-0">Jenis Barang</h6>
                                <button class ="btn btn-primary tambahJenisBarang">Tambah</button>
                            </div>
                            <!-- jenis barang -->
                            <div class="table-responsive" style="max-height: 300px;">
            <table class="table table-striped table-bordered">
                <thead class="table-dark">
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Kode</th>
                        <th scope="col">Nama</th>
                        <th scope="col">Action</th>
                    </tr>
                </thead>
                <tbody id="isiType">
                 </tbody>
            </table>
        </div>
                             <!-- end jenis barang -->
                        </div>
                    </div>
                    <div class="col-sm-12 col-xl-6">
                        <div class="bg-light text-center rounded p-4">
                            <div class="d-flex align-items-center justify-content-between mb-4">
                                <h6 class="mb-0">Satuan Barang</h6>
                                <button class ="btn btn-primary tambahSatuanBarang">Tambah</button>
                            </div>
                                                       <!-- satuan barang -->
                                                       <div class="table-responsive" style="max-height: 300px;">
            <table class="table table-striped table-bordered">
                <thead class="table-dark">
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Kode</th>
                        <th scope="col">Nama</th>
                        <th scope="col">Action</th>
                    </tr>
                </thead>
                <tbody id= "isiSatuan"></tbody>
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
                        <h6 class="mb-0">Persediaan Material</h6>
                        <button class= "btn btn-primary tambahMaterial">Tambah</button>
                    </div>
                    <div class="table-responsive">
                    <table id="tabel_serverside" class="table table-bordered display text-left" cellspacing="0" width="100%">
              <thead>
                <tr  class="text-center">
                  <th style=" text-align: center;">#</th>
                  <th style=" text-align: center;">Code</th>
                  <th style=" text-align: center;">Name</th>
                  <th style=" text-align: center;">Type</th>
                  <th style=" text-align: center;">Import/Export</th>
                  <th style=" text-align: center;">Picture</th>
                  <th style=" text-align: center;">Tersedia</th>
                  <th style=" text-align: center;">Tersedia Sales Order</th>
                  <th style=" text-align: center;">Satuan</th>
                  <th style=" text-align: center;">Action</th>
                </tr>
              </thead>
              <tfoot>
                <tr class="text-center">
                <th style=" text-align: center;">#</th>
                  <th style=" text-align: center;">Code</th>
                  <th style=" text-align: center;">Name</th>
                  <th style=" text-align: center;">Type</th>
                  <th style=" text-align: center;">Import/Export</th>
                  <th style=" text-align: center;">Picture</th>
                  <th style=" text-align: center;">Tersedia</th>
                  <th style=" text-align: center;">Tersedia Sales Order</th>
                  <th style=" text-align: center;">Satuan</th>
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
     
            <script type="text/javascript" src="<?=base_url('assets')?>/js/material.js"></script>
            <script type="text/javascript" src="<?=base_url('assets')?>/datatables/datatables.min.js"></script>
