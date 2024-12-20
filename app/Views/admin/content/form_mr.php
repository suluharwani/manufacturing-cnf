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
            <h6 class="mb-0">Product</h6>
            <button class="btn btn-primary addMaterial">Add</button>
        </div>
        <div class="table-responsive">

            <table id="tabel_serverside" class="table table-bordered display text-left" cellspacing="0" width="100%">
                <thead>
                    <tr class="text-center">
                        <th style=" text-align: center;">#</th>
                        <th style=" text-align: center;">Kode</th>
                        <th style=" text-align: center;">Nama</th>
                        <th style=" text-align: center;">Qty</th>
                        <th style=" text-align: center;">Price</th>
                        <th style=" text-align: center;">Action</th>
                    </tr>
                </thead>
                <tfoot>
                    <tr class="text-center">
                        <th style=" text-align: center;">#</th>
                        <th style=" text-align: center;">Kode</th>
                        <th style=" text-align: center;">Nama</th>
                        <th style=" text-align: center;">Qty</th>
                        <th style=" text-align: center;">Price</th>
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
<!-- Button to trigger modal -->

<!-- Modal -->


<script type="text/javascript" src="<?= base_url('assets') ?>/js/form_mr.js"></script>
<script type="text/javascript" src="<?= base_url('assets') ?>/datatables/datatables.min.js"></script>