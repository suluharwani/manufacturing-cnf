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
            <h6 class="mb-0">Persediaan Material</h6>
            <div>
                <!-- Import Button with Modal Trigger -->
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#importModal">
                    <i class="fa fa-upload me-2"></i>Import
                </button>
                
                <!-- Export Button -->
                <a href="<?= base_url('stock/exportExcel') ?>" class="btn btn-primary">
                    <i class="fa fa-download me-2"></i>Export
                </a>
            </div>
        </div>
        
        <!-- Import Modal -->
        <div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="importModalLabel">Import Data Persediaan</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form method="post" action="<?= base_url('stock/importExcel') ?>" enctype="multipart/form-data">
                        <div class="modal-body">
                            <?php if (session()->has('message')): ?>
                                <div class="alert alert-info">
                                    <?= session('message') ?>
                                </div>
                            <?php endif; ?>
                            
                            <?php if (session()->has('import_errors')): ?>
                                <div class="alert alert-danger">
                                    <h5>Errors:</h5>
                                    <ul>
                                        <?php foreach (session('import_errors') as $error): ?>
                                            <li><?= $error ?></li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            <?php endif; ?>
                            
                            <div class="mb-3">
                                <label for="excelFile" class="form-label">Pilih File Excel</label>
                                <input class="form-control" type="file" id="excelFile" name="excel_file" required accept=".xlsx, .xls">
                                <div class="form-text">Format file harus .xlsx atau .xls</div>
                            </div>
                            
                            <div class="mt-3">
                                <a href="<?= base_url('stock/exportExcel') ?>" class="btn btn-sm btn-outline-primary">
                                    <i class="fa fa-download me-1"></i>Download Template
                                </a>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                            <button type="submit" class="btn btn-primary">Import Data</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="table-responsive">
            <table id="tabel_serverside" class="table table-bordered display text-left" cellspacing="0" width="100%">
                <thead>
                    <tr class="text-center">
                        <th style="text-align: center;">#</th>
                        <th style="text-align: center;">Code</th>
                        <th style="text-align: center;">Name</th>
                        <th style="text-align: center;">Opening Balance</th>
                        <th style="text-align: center;">Total Incoming</th>
                        <th style="text-align: center;">Total Outgoing</th>
                        <th style="text-align: center;">Stock Opname</th>
                        <th style="text-align: center;">Balance</th>
                        <th style="text-align: center;">Action</th>
                    </tr>
                </thead>
                <tfoot>
                    <tr class="text-center">
                        <th style="text-align: center;">#</th>
                        <th style="text-align: center;">Code</th>
                        <th style="text-align: center;">Name</th>
                        <th style="text-align: center;">Opening Balance</th>
                        <th style="text-align: center;">Total Incoming</th>
                        <th style="text-align: center;">Total Outgoing</th>
                        <th style="text-align: center;">Stock Opname</th>
                        <th style="text-align: center;">Balance</th>
                        <th style="text-align: center;">Action</th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
<!-- Recent Sales End -->


<!-- Widgets Start -->

<!-- Widgets End -->

<script type="text/javascript" src="<?= base_url('assets') ?>/js/stock_logistic.js"></script>
<script type="text/javascript" src="<?= base_url('assets') ?>/datatables/datatables.min.js"></script>