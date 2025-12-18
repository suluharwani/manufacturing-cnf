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
                <h2>Material Destruction Form</h2>
                <?php

                ?>
                <form>
                    <div class="row mb-3">
                        <label for="invoice" class="col-md-3 col-form-label">Kode</label>
                        <div class="col-md-9">
                            <input type="text" class="form-control" value="<?= $material_destruction['code'] ?>" id="kode"
                                placeholder="kode" disabled>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label for="invoice" class="col-md-3 col-form-label">Department</label>
                        <div class="col-md-9">
                            <input type="text" class="form-control" value="<?= $material_destruction['dept'] ?>" id="invoice"
                                placeholder="Invoice" disabled>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label for="date" class="col-md-3 col-form-label">Date</label>
                        <div class="col-md-9">
                        <input type="date" class="form-control" value="<?= date('Y-m-d', strtotime($material_destruction['created_at'])) ?>" id="date" placeholder="Date" disabled>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label for="remark" class="col-md-3 col-form-label">Remark</label>
                        <div class="col-md-9">
                            <textarea class="form-control" rows="4" id="remark"
                                disabled><?= $material_destruction['remarks'] ?></textarea>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label for="status" class="col-md-3 col-form-label">Status</label>
                        <div class="col-md-9">
                            <select class="form-control" id="status">
                                <option value="0" <?= $material_destruction['status'] == 0 ? 'selected' : '' ?>>Draft</option>
                                <option value="1" <?= $material_destruction['status'] == 1 ? 'selected' : '' ?>>Posted</option>
                               
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
            <?php if ($material_destruction['status'] == 0) {?>
            <button class="btn btn-primary posting">Posting</button>
            <?php } else { ?>
            <button class="btn btn-danger batalposting" >Batal Posting</button>
            <?php }?>
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
                    <th>Quantity</th>
                    <th>Remarks</th>
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


<script type="text/javascript" src="<?= base_url('assets') ?>/js/materialdestructiondet.js"></script>
<script type="text/javascript" src="<?= base_url('assets') ?>/datatables/datatables.min.js"></script>  
<!-- <script>
    // Base URL tanpa ID
    const baseUrl = "<?= base_url('MasterPenggajianDetailController/exportToExcel'); ?>";
    
    // Mendapatkan segmen URI dari URL saat ini
    const currentUrl = window.location.href;
    const urlSegments = currentUrl.split('/');
    const id = urlSegments[4]; // Mengambil segmen ke-5 (index 4), sesuaikan sesuai struktur URL Anda

    // Mengatur href tautan dengan ID dinamis
    const exportLink = document.getElementById("exportExcelLink");
    exportLink.href = `${baseUrl}/${id}`;
</script> -->