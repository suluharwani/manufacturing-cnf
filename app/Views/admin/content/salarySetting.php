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
                    <div class="col-sm-12 col-xl-12">
                        <div class="bg-light text-center rounded p-4">
                            <div class="d-flex align-items-center justify-content-between mb-4">
                                <h6 class="mb-0">Hari Kerja</h6>
                                <button class ="btn btn-primary addDay">Tambah</button>
                            </div>
                            <!-- jenis barang -->
                            <div class="table-responsive" style="max-height: 400px;">
            <table class="table table-striped table-bordered" id="workScheduleTable">
                <thead class="table-dark">
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Hari</th>
                        <th scope="col">Mulai Kerja</th>
                        <th scope="col">Istirahat</th>
                        <th scope="col">Pulang</th>
                        <th scope="col">Istirahat Lembur</th>
                        <th scope="col">Mulai Lembur</th>
                        <th scope="col">Pulang Lembur</th>
                        <th scope="col">Hapus</th>

                    </tr>
                </thead>
                <tbody>
                 </tbody>
            </table>
        </div>
                             <!-- end jenis barang -->
                        </div>
                    </div>
                    <div class="col-sm-12 col-xl-12">
                        <div class="bg-light text-center rounded p-4">
                            <div class="d-flex align-items-center justify-content-between mb-4">
                                <h6 class="mb-0">Rincian Gaji</h6>
                                <button class ="btn btn-primary addSalaryCat">Tambah</button>
                            </div>
                                                       <!-- satuan barang -->
                                                       <div class="table-responsive" style="max-height: 300px;">
             <table class="table table-striped table-bordered" id="salaryCatTable">
                <thead class="table-dark">
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Kode</th>
                        <th scope="col">Nama</th>
                        <th scope="col">Kategori</th>
                        <th scope="col">Gaji Pokok</th>
                        <th scope="col">Gaji Per Jam</th>
                        <th scope="col">Gaji Per Jam Hari Minggu</th>
                        <th scope="col">Delete</th>
                    </tr>
                </thead>
                <tbody></tbody>
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
     
            <script type="text/javascript" src="<?=base_url('assets')?>/js/salarysetting.js"></script>
            <script type="text/javascript" src="<?=base_url('assets')?>/datatables/datatables.min.js"></script>
