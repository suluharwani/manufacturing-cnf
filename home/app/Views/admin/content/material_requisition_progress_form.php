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
                <h2>Form Material Requisition</h2>
                <form>
                    <div class="row mb-3">
                        <label for="invoice" class="col-md-3 col-form-label">Proforma Invoice</label>
                        <div class="col-md-9">
                            <input type="text" class="form-control" value="<?= $mreq['pi'] ?>" id="invoice"
                                placeholder="Invoice" disabled>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label for="kode" class="col-md-3 col-form-label">WO</label>
                        <div class="col-md-9">
                            <input type="text" class="form-control" value="<?= $mreq['wo'] ?>" id="kode" disabled>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label for="email" class="col-md-3 col-form-label">Admin</label>
                        <div class="col-md-9">
                            <input type="text" class="form-control"
                                value="<?= $mreq['nama_depan'] . " " . $mreq['nama_belakang'] ?>" id="customer"
                                placeholder="customer" disabled>

                        </div>
                    </div>
                    <div class="row mb-3">
                        <label for="lastName" class="col-md-3 col-form-label">Department</label>
                        <div class="col-md-9">
                            <input type="text" class="form-control" id="department" value="<?= $mreq['dep'] ?>" disabled>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label for="lastName" class="col-md-3 col-form-label">Materail Requisition</label>
                        <div class="col-md-9">
                            <input type="text" class="form-control" value="<?= $mreq['code'] ?>" id="mrn" disabled>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label for="lastName" class="col-md-3 col-form-label">Status</label>
                        <div class="col-md-9">
                            <?php if ($mreq['status'] == 1) {
                                $status = "Posted";
                            } else {
                                $status = "Unposted";
                            } ?>
                            <input type="text" class="form-control" id="status" value="<?= $status ?>" disabled>
                        </div>
                    </div>


                </form>
            </div>
        </div>
    </div>
</div>
<div class="container-fluid pt-4 px-4">
    <div class="bg-light text-center rounded p-4">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <h6 class="mb-0">WO Material List</h6>
        </div>
        <div class="table-responsive">

            <div class="table-responsive">
                <table id="materialTable" class="table table-striped">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Material Name</th>
                            <th>Measurement</th>
                            <th>Total Usage</th>
                            <th>Fulfilled Request</th>
                            <th>Unfulfilled Request</th>
                            <th>Total Requisition</th>
                            <th>Total Requisition Unposting</th>
                            <th>Stock</th>
                            <th>Max Request</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Rows will be dynamically appended here -->
                    </tbody>
                </table>

            </div>
        </div>
    </div>
</div>


<div class="container-fluid pt-4 px-4">
    <div class="bg-light text-center rounded p-4">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <h6 class="mb-0">Product WO</h6>
            <?php if ($mreq['completion'] == 1) { ?>
                <button class="btn btn-secondary alokasiPI" onclick="window.location.href='<?= base_url('requisition/print') ?>/<?= $mreq['id'] ?>'">Print</button>
            <?php } else { ?>
                <button class="btn btn-primary posting">Completion</button>

            <?php } ?>
        </div>
        <div class="table-responsive">

            <table class="table table-bordered display text-left" cellspacing="0" width="100%">
                <thead>
                    <tr class="text-center">
                        <th style=" text-align: center;">#</th>
                        <th style=" text-align: center;">Kode</th>
                        <th style=" text-align: center;">Nama</th>
                        <th style=" text-align: center;">Qty</th>
                        <th style=" text-align: center;">Action</th>
                    </tr>
                </thead>
                <tbody id="tabel_requisition"></tbody>
                <tfoot>
                    <tr class="text-center">
                        <th style=" text-align: center;">#</th>
                        <th style=" text-align: center;">Kode</th>
                        <th style=" text-align: center;">Nama</th>
                        <th style=" text-align: center;">Qty</th>
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
<div class="modal fade" id="addMaterialModal" tabindex="-1" aria-labelledby="addMaterialModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addMaterialModalLabel">Add Material</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="addForm">
                    <div class="mb-3">
                        <label for="id_product" class="form-label">Product</label>
                        <select class="form-control" id="id_product" required>
                            <option value="">Select Product</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="quantity" class="form-label">Quantity</label>
                        <input type="number" class="form-control" id="quantity" required>
                    </div>



                    <button type="submit" class="btn btn-primary">Save</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript" src="<?= base_url('assets') ?>/js/material_requisition_progress_form.js"></script>
<script type="text/javascript" src="<?= base_url('assets') ?>/datatables/datatables.min.js"></script>