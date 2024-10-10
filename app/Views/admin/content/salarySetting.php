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
     <div class="col-sm-12 col-xl-6">
            <div class="bg-light text-center rounded p-4">
                <div class="d-flex align-items-center justify-content-between mb-4">
                    <h6 class="mb-0">Tunjangan</h6>
                    <button class ="btn btn-primary addAllowance">Tambah</button>
                </div>
                <!-- satuan barang -->
                <div class="table-responsive" style="max-height: 300px;">
                   <table class="table table-striped table-bordered" id="allowanceTable">
                    <thead class="table-dark">
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Kode</th>
                            <th scope="col">Nama</th>
                            <th scope="col">Status</th>
                            <th scope="col">Delete</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
            <!-- end satuan barang -->
        </div>
    </div>
         <div class="col-sm-12 col-xl-6">
            <div class="bg-light text-center rounded p-4">
                <div class="d-flex align-items-center justify-content-between mb-4">
                    <h6 class="mb-0">Potongan</h6>
                    <button class ="btn btn-primary addDeduction">Tambah</button>
                </div>
                <!-- satuan barang -->
                <div class="table-responsive" style="max-height: 300px;">
                   <table class="table table-striped table-bordered" id="deductionTable">
                    <thead class="table-dark">
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Kode</th>
                            <th scope="col">Nama</th>
                            <th scope="col">Status</th>
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





<!-- Recent Sales End -->


<!-- Widgets Start -->

<!-- Widgets End -->

<script type="text/javascript" src="<?=base_url('assets')?>/js/salarysetting.js"></script>
<script type="text/javascript" src="<?=base_url('assets')?>/datatables/datatables.min.js"></script>
