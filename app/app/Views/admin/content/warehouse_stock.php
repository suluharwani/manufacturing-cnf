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



  <?php if (session()->getFlashdata('success')) : ?>
                        <script>
                            Swal.fire({
                                title: 'Sukses',
                                html: '<?= session()->getFlashdata('success'); ?>',
                                icon: 'success'
                            });
                        </script>
                    <?php endif; ?>

                    <?php if (session()->getFlashdata('error')) : ?>
                        <script>
                            Swal.fire({
                                title: 'Error',
                                text: '<?= session()->getFlashdata('error'); ?>',
                                icon: 'error'
                            });
                        </script>
                    <?php endif; ?>



<!-- Recent Sales Start -->
<div class="container-fluid pt-4 px-4">
                <div class="bg-light text-center rounded p-4">
                    <div class="d-flex align-items-center justify-content-between mb-4">
                        <h6 class="mb-0">Persediaan Material</h6>
                        <div>
                            
                          <button type="button" class="btn btn-success btn-sm" onclick="exportExcel()">
                            <i class="fas fa-file-excel"></i> Export Excel
                        </button>
                         <button type="button" class="btn btn-primary btn-sm ml-1" id="importButton">
                            <i class="fas fa-file-import"></i> Import Excel
                        </button>
                        </div>
                        
                    </div>
                    
                    <div class="table-responsive">
                    <table id="tabel_serverside" class="table table-bordered display text-left" cellspacing="0" width="100%">
              <thead>
                <tr  class="text-center">
                  <th style=" text-align: center;">#</th>
                  <th style=" text-align: center;">Code</th>
                  <th style=" text-align: center;">Name</th>
                  <th style=" text-align: center;">Opening Balance</th>
                  <th style=" text-align: center;">Total Incoming</th>
                  <th style=" text-align: center;">Total Outgoing</th>
                  <th style=" text-align: center;">Stock Opname</th>
                  <th style=" text-align: center;">Balance</th>
                  <th style=" text-align: center;">Price</th>
                  <th style=" text-align: center;">Price (Rupiah)</th>
                  <th style=" text-align: center;">Action</th>
                </tr>
              </thead>
              <tfoot>
                <tr class="text-center">
                <th style=" text-align: center;">#</th>
                  <th style=" text-align: center;">Code</th>
                  <th style=" text-align: center;">Name</th>
                  <th style=" text-align: center;">Opening Balance</th>
                  <th style=" text-align: center;">Total Incoming</th>
                  <th style=" text-align: center;">Total Outgoing</th>
                  <th style=" text-align: center;">Stock Opname</th>
                  <th style=" text-align: center;">Balance</th>
                  <th style=" text-align: center;">Price</th>
                  <th style=" text-align: center;">Price (Rupiah)</th>
                  <th style=" text-align: center;">Action</th>
              </tr>
            </tfoot>
          </table>
                    </div>
                </div>
            </div>
<!-- modal -->
<div class="modal fade" id="importModal" tabindex="-1" role="dialog" aria-labelledby="importModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="importModalLabel">Import Stock dari Excel</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="<?= base_url('/stock/initimport'); ?>" method="post" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="excel_file">File Excel</label>
                        <input type="file" class="form-control-file" id="excel_file" name="excel_file" accept=".xlsx,.xls" required>
                        <small class="form-text text-muted">
                            Format file harus Excel (.xlsx atau .xls). 
                            <a href="<?= base_url('/stock/initexport'); ?>" download>Download template</a><br>
                            <strong>Format Import:</strong><br>
                            - Sheet "Data Stock" untuk data stock<br>
                            - Kolom F harus berisi ID Currency (bisa dilihat di Sheet "List Currency")
                        </small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-primary">Import</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
   $(document).ready(function() {
        $('#stockTable').DataTable();
        
        // Handle tombol import
        $('#importButton').click(function() {
            $('#importModal').modal('show');
        });
    });

    function exportExcel() {
        Swal.fire({
            title: 'Export Data Stock',
            text: 'Anda akan mengekspor data stock ke file Excel?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Export!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = '<?= base_url('/stock/initexport'); ?>';
            }
        });
    }
</script>
<script type="text/javascript" src="<?= base_url('assets') ?>/js/stock.js"></script>
<script type="text/javascript" src="<?= base_url('assets') ?>/datatables/datatables.min.js"></script>