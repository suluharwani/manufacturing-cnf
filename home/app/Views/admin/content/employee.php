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
                        <h6 class="mb-0">Employee</h6>
                        <!-- <button class= "btn btn-primary">Tambah</button> -->
                    </div>
                    <div class="table-responsive">
                    <table id="tabel_serverside" class="table table-bordered display text-left" cellspacing="0" width="100%">
              <thead>
                <tr  class="text-center">
                  <th style=" text-align: center;">#</th>
                  <th style=" text-align: left;">NIP</th>
                  <th style=" text-align: left;">Nama</th>
                  <th style=" text-align: left;">Pin</th>
                  <th style=" text-align: center;">Action</th>
                </tr>
              </thead>
              <tfoot>
                <tr class="text-center">
                  <th style=" text-align: center;">#</th>
                  <th style=" text-align: left;">NIP</th>
                  <th style=" text-align: left;">Nama</th>
                  <th style=" text-align: left;">Pin</th>
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
<!-- <div>
  <button id="selectDateBtn" class="btn btn-info">Pilih Tanggal Presensi</button>
</div> -->

<!-- Date Selection Modal -->
<!-- Date Selection Modal -->
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

<!-- Popup Modal for displaying attendance details -->
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

<!-- Modal for displaying Salary Setting -->
<!-- Salary Category Modal -->
<!-- Salary Category Modal -->
<div class="modal fade" id="salaryCategoryModal" tabindex="-1" aria-labelledby="salaryCategoryModalLabel" aria-hidden>
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="salaryCategoryModalLabel">Pilih Kategori Gaji</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="salaryCategoryForm">
          <div class="mb-3">
            <input type="text" class="form-control" name="id" id="id" hidden>
            <input type="text" class="form-control" name="pin" id="pin" hidden>
            <label for="salaryCategorySelect" class="form-label">Kategori Gaji</label>
            <select class="form-select" id="salaryCategorySelect" required>
                         <!-- Options will be populated dynamically by AJAX -->
            </select>
          </div>
          <div class="mb-3">
            <label for="gajiPokok" class="form-label">Gaji Pokok</label>
            <input type="text" class="form-control" id="gajiPokok" readonly>
          </div>
          <div class="mb-3">
            <label for="gajiPerJam" class="form-label">Gaji Per Jam</label>
            <input type="text" class="form-control" id="gajiPerJam" readonly>
          </div>
          <div class="mb-3">
            <label for="gajiPerJamMinggu" class="form-label">Gaji Per Jam Hari Minggu</label>
            <input type="text" class="form-control" id="gajiPerJamMinggu" readonly>
          </div>
        </form>
        <div  class="table-responsive">
        <button type="button" class="btn btn-primary" id="saveCategoryBtn">Simpan</button>
        <div class="mb-3">
             <table class="table table-bordered" id="availableItemsTable">

        </div>
        <div class="mb-3">
        <thead>
            <tr>
                <th>Nama</th>

                <th>Kode</th>
                <th>perjam</th>
            </tr>
        </thead>
        <tbody>
            <!-- Data will be dynamically populated here by AJAX -->
        </tbody>
    </table>
  </div>
</div>

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


<!-- Modal for managing potongan (data) -->
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
<div class="modal fade" id="dataModal" tabindex="-1" aria-labelledby="potonganModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="potonganModalLabel">Manage Data Karyawan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="potonganForm">
                    <!-- Basic Employee Info -->
                    <div class="mb-3">
                        <label for="dataEmployeeName" class="form-label">Nama</label>
                        <input type="text" class="form-control" id="d_dataEmployeeName" disabled>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="dataEmployeeId" class="form-label">ID Pegawai</label>
                            <input type="number" class="form-control" id="d_dataEmployeeId" disabled>
                        </div>
                        <div class="col-md-6">
                            <label for="dataEmployeePin" class="form-label">PIN</label>
                            <input type="text" class="form-control" id="d_dataEmployeePin" disabled>
                        </div>
                    </div>

                    <!-- Bank Information -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="bank" class="form-label">Bank</label>
                            <input type="text" class="form-control" id="bank">
                        </div>
                        <div class="col-md-6">
                            <label for="bank_account" class="form-label">Nomor Rekening</label>
                            <input type="text" class="form-control" id="bank_account">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="pemilik_rekening" class="form-label">Pemilik Rekening</label>
                        <input type="text" class="form-control" id="pemilik_rekening">
                    </div>

                    <!-- Employment Dates -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="masuk_kerja" class="form-label">Tanggal Masuk Kerja</label>
                            <input type="date" class="form-control" id="masuk_kerja">
                        </div>
                        <div class="col-md-6">
                            <label for="keluar_kerja" class="form-label">Tanggal Keluar Kerja</label>
                            <input type="date" class="form-control" id="keluar_kerja">
                        </div>
                    </div>

                    <!-- Personal Information -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="nik" class="form-label">NIK</label>
                            <input type="text" class="form-control" id="nik">
                        </div>
                        <div class="col-md-6">
                            <label for="tgl_lahir" class="form-label">Tanggal Lahir</label>
                            <input type="date" class="form-control" id="tgl_lahir">
                        </div>
                    </div>

                    <!-- Insurance Information -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="no_bpjs" class="form-label">Nomor BPJS</label>
                            <input type="text" class="form-control" id="no_bpjs">
                        </div>
                        <div class="col-md-6">
                            <label for="no_bpjstk" class="form-label">Nomor BPJS TK</label>
                            <input type="text" class="form-control" id="no_bpjstk">
                        </div>
                    </div>

                    <!-- Additional Information -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="jumlah_tanggungan" class="form-label">Jumlah Tanggungan</label>
                            <input type="number" class="form-control" id="jumlah_tanggungan">
                        </div>
                        <div class="col-md-6">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-control" id="status">
                                <option value="1">Aktif</option>
                                <option value="0">Tidak Aktif</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="alamat" class="form-label">Alamat</label>
                        <textarea class="form-control" id="alamat" rows="2"></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="posisi" class="form-label">Posisi</label>
                        <input type="text" class="form-control" id="posisi">
                    </div>

                     <div class="mb-3">
            <label class="form-label">Foto</label>
            <input type="file" class="form-control" id="foto">
            <div id="photoPreview" class="mt-2"></div>
          </div>
                </form>

                <hr>

                <!-- Table to display and manage potongan -->

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="saveEData">Save Changes</button>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript" src="<?= base_url('assets') ?>/js/employee.js"></script>
<script type="text/javascript" src="<?= base_url('assets') ?>/datatables/datatables.min.js"></script>