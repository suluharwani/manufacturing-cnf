<!-- Button Start -->
<div class="container-fluid pt-4 px-4">
    <div class="row g-4">
        <div class="col-sm-12 col-xl-12">
            <div class="bg-light rounded h-100 p-4">
                <!-- Add this button for List Laporan -->
                <button class="btn btn-primary" id="listLaporanBtnKS">Kartu Stock</button>
                <button class="btn btn-primary" id="listLaporanBtnPB">Pembelian</button>
                <button class="btn btn-primary" id="listLaporanBtnPM">Pemakaian</button>
                <button class="btn btn-primary" id="listLaporanBtnRS">Rusak/Scrap</button>

      


                <div class="modal fade" id="laporanModal" tabindex="-1" aria-labelledby="laporanModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="laporanModalLabel">Filter</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
            <form id="laporanMaterialForm">
                                    <div class="form-group">
                                        <label for="startDate">Tanggal Mulai</label>
                                        <input type="date" class="form-control" id="startDate" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="endDate">Tanggal Akhir</label>
                                        <input type="date" class="form-control" id="endDate" required>
                                    </div>
                                    <label for="materialSelect">Pilih Material</label>
                                    <input type="text" class="form-control" id="materialSelect" hidden>

    <div class="dropdown">
        <input type="text" class="form-control" id="searchInput" placeholder="Search Material" aria-label="Search Material">
        <div class="dropdown-menu" id="dropdownMenu" aria-labelledby="searchInput">
            <div id="materialOptions">
                <!-- Options will be populated here -->
            </div>
        </div>
    </div>
                                </form>

                <hr>

                <!-- Table to display and manage potongan -->

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="generateReportBtnMaterial" data-bs-dismiss="modal">View</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>


            </div>
        </div>
        <div class="col-sm-12 col-xl-12">
            <div class="bg-light rounded h-100 p-4">
                <!-- Section to display results -->
                <div id="resultSection" class="mt-4">
                    <h6>Hasil Laporan</h6>
                    <div id="resultTableContainer">
                        <!-- Results will be displayed here -->
                    </div>
                    <button class="btn btn-success" id="printExcelBtn">Print to Excel</button>
                </div>
            </div>
        </div>



    </div>
</div>
<!-- Button End -->
<script type="text/javascript" src="<?= base_url('assets') ?>/js/report_material.js"></script>





