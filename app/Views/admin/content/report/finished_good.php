<script src="https://unpkg.com/jspdf@latest/dist/jspdf.umd.min.js"></script>
<script src="https://html2canvas.hertzen.com/dist/html2canvas.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.8.4/jspdf.plugin.autotable.min.js"></script>

<style>/* Menambahkan border pada seluruh tabel */
table {
    border-collapse: collapse; /* Untuk menggabungkan border agar lebih rapi */
    width: 100%;
}

/* Menambahkan border pada header tabel */
th, td {
    border: 1px solid #000; /* Border hitam dengan ketebalan 1px */
    padding: 8px; /* Memberikan jarak di dalam cell */
    text-align: center; /* Menyelaraskan teks ke tengah */
}

/* Menambahkan border di antara setiap baris */
tr:nth-child(even) {
    background-color: #f2f2f2; /* Memberikan warna latar belakang yang lebih terang pada baris genap */

}
#btnReportPd {  
            display: none; /* Awalnya sembunyikan konten */  
            margin-top: 10px;  
            padding: 10px;  
            border: 1px solid #ccc;  
        }  
        #btnReportMv {  
            display: none; /* Awalnya sembunyikan konten */  
            margin-top: 10px;  
            padding: 10px;  
            border: 1px solid #ccc;  
        }  
</style>
<div class="container-fluid pt-4 px-4">
    <div class="row g-4">
        <div class="col-sm-12 col-xl-12">
            <div class="bg-light rounded h-100 p-4">
                <!-- Add this button for List Laporan -->
                <button class="btn btn-primary" id="listLaporanBtnPd">Production</button>
                <button class="btn btn-primary" id="listLaporanBtnMv">Movement</button>
                <button class="btn btn-primary" id="listLaporanBtnP">Product</button>
                
                <div class="modal fade" id="laporanProductModal" tabindex="-1" aria-labelledby="laporanModalLabel" aria-hidden="true">
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
                                    <div class="form-group">
                                        <label for="productSelect">Pilih Product</label>
                                        <input type="text" class="form-control" id="productSelect" hidden>
                                    </div>
                                    <div class="dropdown">
                                        <input type="text" class="form-control" id="searchInput"
                                            placeholder="Search Material" aria-label="Search Material">
                                        <div class="dropdown-menu" id="dropdownMenu" aria-labelledby="searchInput">
                                            <div id="productOptions">
                                                <!-- Options will be populated here -->
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="endDate">Select Product Status</label>
                                        <select class="form-select" id="selectlist">
                                            <option value="all" disabled>Select Product Status</option>
                                            <option value="production">Production</option>
                                            <option value="warehouse">Finish Production</option>
                                        </select>

                                    </div>
              
                                </form>

                <hr>

                <!-- Table to display and manage potongan -->

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="generateProductReport" data-bs-dismiss="modal">View</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

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
                                    <div class="form-group">
                                        <label for="endDate">Select WO</label>
                                        <div class="dropdown">
                                            <input type="text" id="searchWoInput" class="form-control"
                                                placeholder="Search Work Orders" aria-haspopup="true"
                                                aria-expanded="false">
                                            <div id="woDropdownMenu" class="dropdown-menu"
                                                aria-labelledby="searchWoInput">
                                                <div id="WoOptions"></div>
                                            </div>
                                            <input type="hidden" id="woSelect" />
                                        </div>

                                    </div>
                                </form>

                <hr>

                <!-- Table to display and manage potongan -->

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="generateReportBtnProd" data-bs-dismiss="modal">View</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="laporanMovementModal" tabindex="-1" aria-labelledby="laporanModalLabel" aria-hidden="true">
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
                                        <input type="date" class="form-control" id="startDateMV" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="endDate">Tanggal Akhir</label>
                                        <input type="date" class="form-control" id="endDateMV" required>
                                    </div>
     
                                    
                                    <!-- <div class="form-group">
                                        <label for="endDate">Select WO</label>
                                        <div class="dropdown">
                                            <input type="text" id="searchWoInputMV" class="form-control"
                                                placeholder="Search Work Orders" aria-haspopup="true"
                                                aria-expanded="false">
                                            <div id="woDropdownMenuMV" class="dropdown-menu"
                                                aria-labelledby="searchWoInputMV">
                                                <div id="WoOptionsMV"></div>
                                            </div>
                                            <input type="hidden" id="woSelectMV" />
                                        </div>

                                    </div> -->
                                </form>

                <hr>

                <!-- Table to display and manage potongan -->

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="generateReportBtnMV" data-bs-dismiss="modal">View</button>
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
                    <div id="btnReportPd">  
                <button class="btn btn-success" id="printBtn">Print Production</button>
                </div>
                <div id="btnReportMv">  
                <button class="btn btn-success" id="printBtnMovement">Print Movement</button>
                </div>

                    <div id="resultTableContainer"  class="table-responsive" >
                        <!-- Results will be displayed here -->
                    </div>
                    
                </div>
            </div>
        </div>



    </div>
</div>
<!-- Button End -->
<script type="text/javascript" src="<?= base_url('assets') ?>/js/report_prod.js"></script>





