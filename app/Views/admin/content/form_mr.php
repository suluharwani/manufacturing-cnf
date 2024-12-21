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
                <h2>Material Requisition Form</h2>
                <?php

                ?>
                <form>
                    <div class="row mb-3">
                        <label for="invoice" class="col-md-3 col-form-label">Kode</label>
                        <div class="col-md-9">
                            <input type="text" class="form-control" value="<?= $mr['kode'] ?>" id="invoice"
                                placeholder="Invoice" disabled>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label for="invoice" class="col-md-3 col-form-label">PI</label>
                        <div class="col-md-9">
                            <input type="text" class="form-control" value="<?= $mr['pi'] ?>" id="invoice"
                                placeholder="Invoice" disabled>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label for="date" class="col-md-3 col-form-label">Date</label>
                        <div class="col-md-9">
                        <input type="date" class="form-control" value="<?= date('Y-m-d', strtotime($mr['created_at'])) ?>" id="date" placeholder="Date" disabled>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label for="remark" class="col-md-3 col-form-label">Remark</label>
                        <div class="col-md-9">
                            <textarea class="form-control" rows="4" id="remark"
                                disabled><?= $mr['remarks'] ?></textarea>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label for="status" class="col-md-3 col-form-label">Status</label>
                        <div class="col-md-9">
                            <select class="form-control" id="status">
                                <option value="0" <?= $mr['status'] == 0 ? 'selected' : '' ?>>Pending</option>
                                <option value="1" <?= $mr['status'] == 1 ? 'selected' : '' ?>>Approved</option>
                                <option value="2" <?= $mr['status'] == 2 ? 'selected' : '' ?>>Rejected</option>
                                <option value="3" <?= $mr['status'] == 3 ? 'selected' : '' ?>>Completed</option>
                                <option value="4" <?= $mr['status'] == 4 ? 'selected' : '' ?>>Canceled</option>
                            </select>
                        </div>

                </form>
  

            </div>
        </div>
    </div>
</div>


<div class="container-fluid pt-4 px-4">
                <div class="bg-light text-center rounded p-4">
                    <div class="d-flex align-items-center justify-content-between mb-4">
                        <h6 class="mb-0">Master Data</h6>
                    </div>
                      <div class="container mt-5">
        <div id="statusMessage" class="mt-3"></div>
    </div>
                    <div class="table-responsive" style="max-height: 300px;">
                        <table id="tabel_serverside" class="table table-bordered display text-left" cellspacing="0" width="100%">
              <thead>
                <tr  class="text-center">
                  <th style=" text-align: center;">#</th>
                  <th style=" text-align: center;">Code</th>
                  <th style=" text-align: center;">Name</th>
                  <th style=" text-align: center;">Type</th>
                  <th style=" text-align: center;">Import/Export</th>
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
                  <th style=" text-align: center;">Satuan</th>
                  <th style=" text-align: center;">Action</th>
                </tr>
            </tfoot>
          </table>
                    </div>
                </div>
            </div>

<!-- Recent Sales Start -->
<div class="container-fluid pt-4 px-4">
    <div class="bg-light text-center rounded p-4">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <h6 class="mb-0">Material Request List</h6>
            <div>
            <button id="printRekapGaji" class="btn btn-primary">Posting</button>
<a id="exportExcelLink" href="#" class="btn btn-success">
    <i class="fa fa-file-excel"></i> Print
</a>
            </div>
            
        </div>
        <div class="table-responsive">
                   <table class="table table-bordered">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Code</th>
                    <th>Name</th>
                    <th>PI</th>
                    <th>Department</th>
                    <th>Quantity</th>
                    <th>Price/Unit</th>
                    <th>Total</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody id="data-body">
                <!-- Data akan dimuat di sini -->
            </tbody>
        </table>
</div>
</div>

</div>
<!-- Recent Sales End -->


<!-- Widgets Start -->

<!-- Widgets End -->
<div id="employeeModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="employeeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="employeeModalLabel">Employee List</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- Tabel akan diisi secara dinamis -->
                <div id="employeeTableContainer"></div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="dateSelectionModal" tabindex="-1" role="dialog" aria-labelledby="dateSelectionModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="dateSelectionModalLabel">Pilih Tanggal Presensi</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="form-group">
          <label for="startDate">Tanggal Awal:</label>
          <input type="date" id="startDate" class="form-control" required>
        </div>
        <div class="form-group">
          <label for="endDate">Tanggal Akhir:</label>
          <input type="date" id="endDate" class="form-control" required>
        </div>
      </div>
      <div class="modal-footer">
        <button id="fetchPresensi" class="btn btn-primary">Tampilkan Presensi</button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
      </div>
    </div>
  </div>
</div>
<div class="modal fade" id="popupModal" tabindex="-1" role="dialog" aria-labelledby="popupModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="popupModalLabel">Detail Presensi : <span id="employeeName"></span></h5>

        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body" id="popupContent">
        <!-- Content will be dynamically populated -->
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
      </div>
    </div>
  </div>
</div>
<!-- Modal for managing tunjangan (allowance) -->
<div class="modal fade" id="tunjanganModal" tabindex="-1" aria-labelledby="tunjanganModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="tunjanganModalLabel">Manage Tunjangan (Allowance)</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="tunjanganForm">
                    <!-- Form for adding new tunjangan -->
                    <div class="mb-3">
                        <label for="namaTunjangan" class="form-label">Nama</label>
                        <input type="text" class="form-control" id="AllowaceEmployeeName" disabled>
                    </div>
                    <div class="mb-3">
                        <label for="namaTunjangan" class="form-label">ID/PIN</label>
                        <input type="number" class="form-control" id="AllowaceEmployeeId" disabled>
                        <input type="number" class="form-control" id="AllowaceEmployeePin" disabled>
                    </div>
                    <div class="mb-3">
                        <label for="namaTunjangan" class="form-label">Nama Tunjangan</label>
                        <select class="form-select" id="allowanceSelect" required>
                    </div>
                    <div class="mb-3">
                        <label for="jumlahTunjangan" class="form-label">Jumlah Tunjangan</label>
                        <input type="number" class="form-control" id="jumlahTunjangan" placeholder="Rp. " required>
                    </div>
                    <button type="button" class="btn btn-primary" id="saveTunjanganBtn">Add Tunjangan</button>
                </form>

                <hr>

                <!-- Table to display and manage tunjangan -->
                <div class="table-responsive">
                    <table class="table table-bordered" id="allowanceTable">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Nama Tunjangan</th>
                                <th>Jumlah</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Data will be dynamically populated here -->
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>


<!-- Modal for managing potongan (deduction) -->
<div class="modal fade" id="potonganModal" tabindex="-1" aria-labelledby="potonganModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="potonganModalLabel">Manage Potongan (Deductions)</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="potonganForm">
                    <!-- Form for adding new potongan -->
                        <div class="mb-3">
                        <label for="namaTunjangan" class="form-label">Nama</label>
                        <input type="text" class="form-control" id="DeductionEmployeeName" disabled>
                    </div>
                    <div class="mb-3">
                        <label for="namaTunjangan" class="form-label">ID/PIN</label>
                        <input type="number" class="form-control" id="DeductionEmployeeId" disabled>
                        <input type="number" class="form-control" id="DeductionEmployeePin" disabled>
                    </div>
                    <div class="mb-3">
                        <label for="namaPotongan" class="form-label">Nama Potongan</label>
                        <select class="form-select" id="deductionSelect" required>
                    </div>
                    <div class="mb-3">
                        <label for="jumlahPotongan" class="form-label">Jumlah Potongan</label>
                        <input type="number" class="form-control" id="jumlahPotongan" placeholder="Enter Deduction Amount" required>
                    </div>
                    <button type="button" class="btn btn-primary" id="addPotonganBtn">Add Potongan</button>
                </form>

                <hr>

                <!-- Table to display and manage potongan -->
                <div class="table-responsive">
                    <table class="table table-bordered" id="potonganTable">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Nama Potongan</th>
                                <th>Jumlah</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Data will be dynamically populated here by AJAX -->
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript" src="<?= base_url('assets') ?>/js/materialrequestdet.js"></script>
<script type="text/javascript" src="<?= base_url('assets') ?>/datatables/datatables.min.js"></script>  
<script>
    // Base URL tanpa ID
    const baseUrl = "<?= base_url('MasterPenggajianDetailController/exportToExcel'); ?>";
    
    // Mendapatkan segmen URI dari URL saat ini
    const currentUrl = window.location.href;
    const urlSegments = currentUrl.split('/');
    const id = urlSegments[4]; // Mengambil segmen ke-5 (index 4), sesuaikan sesuai struktur URL Anda

    // Mengatur href tautan dengan ID dinamis
    const exportLink = document.getElementById("exportExcelLink");
    exportLink.href = `${baseUrl}/${id}`;
</script>