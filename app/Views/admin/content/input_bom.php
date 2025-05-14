<link rel="stylesheet" type="text/css" href="<?= base_url('assets') ?>/datatables/datatables.min.css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

<style>
    /* Tambahan minimal CSS untuk fixed header */
    thead th {
        position: sticky;
        top: 0;
        background-color: #343a40;
        /* Warna background yang sama dengan header */
        z-index: 100;
    }

      body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
            position: sticky;
            top: 0;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        tr:hover {
            background-color: #f1f1f1;
        }
        .loading {
            text-align: center;
            padding: 20px;
        }
        .error {
            color: red;
            padding: 20px;
        }
        .btn-delete {
            background-color: #f44336;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 4px;
            cursor: pointer;
        }
        .btn-delete:hover {
            background-color: #d32f2f;
        }
        .action-cell {
            width: 100px;
            text-align: center;
        }
    </style>






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
            <h6 class="mb-0">BILL OF MATERIAL</h6>
            <div>
          
            </div>
            
        </div>
        <div class="table-responsive">
                   <h1>Bill of Materials (BOM) Data</h1>
    <div id="table-container">
        <div class="loading">Memuat data...</div>
    </div>
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
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script type="text/javascript" src="<?= base_url('assets') ?>/js/inputbom.js"></script>
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