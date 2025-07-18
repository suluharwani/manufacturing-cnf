<style>
    /* Menambahkan border pada seluruh tabel */
    table {
        border-collapse: collapse;
        width: 100%;
    }

    /* Menambahkan border pada header tabel */
    th, td {
        border: 1px solid #000;
        padding: 8px;
        text-align: center;
    }

    /* Menambahkan border di antara setiap baris */
    tr:nth-child(even) {
        background-color: #f2f2f2;
    }

    .card-report {
        transition: all 0.3s ease;
    }

    .card-report:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.1);
    }

    .btn-generate-all {
        background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
        border: none;
        color: white;
        font-weight: bold;
        letter-spacing: 1px;
    }

    .btn-generate-all:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.2);
    }

    .report-container {
        min-height: 300px;
    }

    .loading-spinner {
        display: none;
    }
</style>

<div class="container-fluid pt-4 px-4">
    <div class="row g-4">
        <!-- Tombol untuk 7 Jenis Pelaporan -->
        <div class="col-12">
            <div class="bg-light rounded p-4">
                <h5 class="mb-4">Laporan Monitoring dan Evaluasi PER-5/BC/2023</h5>
                
                <!-- Tombol Generate Semua -->
                <div class="row mb-4">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-body text-center">
                                <h5 class="card-title">GENERATE SEMUA LAPORAN</h5>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Dari Tanggal</label>
                                        <input type="date" class="form-control" id="startDateAll">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Sampai Tanggal</label>
                                        <input type="date" class="form-control" id="endDateAll">
                                    </div>
                                </div>
                                <button class="btn btn-generate-all btn-lg w-100" onclick="generateAllReports()">
                                    <span id="loadingAll" class="spinner-border spinner-border-sm d-none"></span>
                                    GENERATE LAPORAN
                                </button>
                                
                                <small class="form-text text-muted d-block mt-2">
                                    Sistem akan mengecek dan hanya menggenerate laporan yang belum ada datanya
                                </small>
                                
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <!-- Laporan 1 -->
                    <div class="col-md-4 mb-3">
                        <div class="card h-100 card-report">
                            <div class="card-body">
                                <h5 class="card-title">LAPORAN PEMASUKAN BAHAN BAKU</h5>
                                <div class="mb-3">
                                    <label class="form-label">Dari Tanggal</label>
                                    <input type="date" class="form-control" id="startDate1">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Sampai Tanggal</label>
                                    <input type="date" class="form-control" id="endDate1">
                                </div>
                                <button class="btn btn-primary w-100" onclick="generateReport(1)">
                                    <span id="loading1" class="spinner-border spinner-border-sm d-none"></span>
                                    Tampilkan Laporan
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Laporan 2 -->
                    <div class="col-md-4 mb-3">
                        <div class="card h-100 card-report">
                            <div class="card-body">
                                <h5 class="card-title">LAPORAN PEMAKAIAN BAHAN BAKU</h5>
                                <div class="mb-3">
                                    <label class="form-label">Dari Tanggal</label>
                                    <input type="date" class="form-control" id="startDate2">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Sampai Tanggal</label>
                                    <input type="date" class="form-control" id="endDate2">
                                </div>
                                <button class="btn btn-success w-100" onclick="generateReport(2)">
                                    <span id="loading2" class="spinner-border spinner-border-sm d-none"></span>
                                    Tampilkan Laporan
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Laporan 3 -->
                    <div class="col-md-4 mb-3">
                        <div class="card h-100 card-report">
                            <div class="card-body">
                                <h5 class="card-title">LAPORAN PEMASUKAN HASIL PRODUKSI</h5>
                                <div class="mb-3">
                                    <label class="form-label">Dari Tanggal</label>
                                    <input type="date" class="form-control" id="startDate3">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Sampai Tanggal</label>
                                    <input type="date" class="form-control" id="endDate3">
                                </div>
                                <button class="btn btn-info w-100" onclick="generateReport(3)">
                                    <span id="loading3" class="spinner-border spinner-border-sm d-none"></span>
                                    Tampilkan Laporan
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Laporan 4 -->
                    <div class="col-md-4 mb-3">
                        <div class="card h-100 card-report">
                            <div class="card-body">
                                <h5 class="card-title">LAPORAN PENGELUARAN HASIL PRODUKSI</h5>
                                <div class="mb-3">
                                    <label class="form-label">Dari Tanggal</label>
                                    <input type="date" class="form-control" id="startDate4">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Sampai Tanggal</label>
                                    <input type="date" class="form-control" id="endDate4">
                                </div>
                                <button class="btn btn-warning w-100" onclick="generateReport(4)">
                                    <span id="loading4" class="spinner-border spinner-border-sm d-none"></span>
                                    Tampilkan Laporan
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Laporan 5 -->
                    <div class="col-md-4 mb-3">
                        <div class="card h-100 card-report">
                            <div class="card-body">
                                <h5 class="card-title">LAPORAN MUTASI BAHAN BAKU</h5>
                                <div class="mb-3">
                                    <label class="form-label">Dari Tanggal</label>
                                    <input type="date" class="form-control" id="startDate5">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Sampai Tanggal</label>
                                    <input type="date" class="form-control" id="endDate5">
                                </div>
                                <button class="btn btn-danger w-100" onclick="generateReport(5)">
                                    <span id="loading5" class="spinner-border spinner-border-sm d-none"></span>
                                    Tampilkan Laporan
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Laporan 6 -->
                    <div class="col-md-4 mb-3">
                        <div class="card h-100 card-report">
                            <div class="card-body">
                                <h5 class="card-title">LAPORAN MUTASI HASIL PRODUKSI</h5>
                                <div class="mb-3">
                                    <label class="form-label">Dari Tanggal</label>
                                    <input type="date" class="form-control" id="startDate6">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Sampai Tanggal</label>
                                    <input type="date" class="form-control" id="endDate6">
                                </div>
                                <button class="btn btn-secondary w-100" onclick="generateReport(6)">
                                    <span id="loading6" class="spinner-border spinner-border-sm d-none"></span>
                                    Tampilkan Laporan
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Laporan 7 -->
                    <div class="col-md-4 mb-3">
                        <div class="card h-100 card-report">
                            <div class="card-body">
                                <h5 class="card-title">LAPORAN WASTE/SCRAP</h5>
                                <div class="mb-3">
                                    <label class="form-label">Dari Tanggal</label>
                                    <input type="date" class="form-control" id="startDate7">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Sampai Tanggal</label>
                                    <input type="date" class="form-control" id="endDate7">
                                </div>
                                <button class="btn btn-dark w-100" onclick="generateReport(7)">
                                    <span id="loading7" class="spinner-border spinner-border-sm d-none"></span>
                                    Tampilkan Laporan
                                </button>
                            </div>
                        </div>
                    </div>
                    <button class="btn btn-generate-all btn-lg w-100" onclick="deleteAllReports()">
                                    <span id="loadingAll" class="spinner-border spinner-border-sm d-none"></span>
                                    HAPUS SEMUA LAPORAN
                                </button>
                </div>
            </div>
        </div>
        
        <!-- Area untuk menampilkan laporan -->
        <div class="col-12">
            <div id="reportContent" class="bg-light rounded p-4 report-container" style="display:none;">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 id="reportTitle"></h5>
                    <div id="dateRangeInfo" class="text-muted"></div>
                </div>
                <div id="reportData" class="table-responsive"></div>
                <div class="d-flex justify-content-end mt-3">
                    <button id="exportPdf" class="btn btn-outline-primary" style="display:none;">
                        <i class="fa fa-file-pdf me-2"></i>Export PDF
                    </button>
                    <button id="exportExcel" class="btn btn-outline-success ms-2" style="display:none;">
                        <i class="fa fa-file-excel me-2"></i>Export Excel
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Library untuk PDF dan Excel -->
<script src="https://unpkg.com/jspdf@latest/dist/jspdf.umd.min.js"></script>
<script src="https://html2canvas.hertzen.com/dist/html2canvas.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.8.4/jspdf.plugin.autotable.min.js"></script>
<script src="https://cdn.sheetjs.com/xlsx-0.19.3/package/dist/xlsx.full.min.js"></script>

<script>
// Set tanggal default (1 bulan terakhir)
document.addEventListener('DOMContentLoaded', function() {
    const today = new Date();
    const lastMonth = new Date();
    lastMonth.setMonth(lastMonth.getMonth() - 1);
    
    const formatDate = (date) => date.toISOString().split('T')[0];
    
    // Set untuk semua input tanggal
    for (let i = 1; i <= 7; i++) {
        $(`#startDate${i}`).val(formatDate(lastMonth));
        $(`#endDate${i}`).val(formatDate(today));
    }
    
    // Set untuk generate all
    $('#startDateAll').val(formatDate(lastMonth));
    $('#endDateAll').val(formatDate(today));
});

// Fungsi untuk generate semua laporan
function generateAllReports() {
    const startDate = $('#startDateAll').val();
    const endDate = $('#endDateAll').val();
    
    // Validasi tanggal
    if (!startDate || !endDate) {
        Swal.fire('Error', 'Harap isi kedua tanggal!', 'error');
        return;
    }
    
    if (new Date(startDate) > new Date(endDate)) {
        Swal.fire('Error', 'Tanggal mulai tidak boleh lebih besar dari tanggal akhir!', 'error');
        return;
    }

    // Tampilkan loading
    $('#loadingAll').removeClass('d-none');
    $('.btn-generate-all').prop('disabled', true);
    
    // AJAX Request
    $.ajax({
        type: "POST",
        url: '<?= base_url('laporan/generate-all') ?>',
        data: {
            periode: startDate,
            end:endDate
        },
        success: function(response) {
            // Sembunyikan loading
            $('#loadingAll').addClass('d-none');
            $('.btn-generate-all').prop('disabled', false);
            
            if (response.messages) {
                let messageHtml = '<div class="alert alert-info"><h5>' + response.summary + '</h5><ul>';
                
                response.messages.forEach(msg => {
                    messageHtml += `<li class="${msg.type === 'success' ? 'text-success' : 'text-info'}">${msg.message}</li>`;
                });
                
                messageHtml += '</ul></div>';
                
                // Tampilkan di reportContent
                $('#reportContent').show();
                $('#reportTitle').text('HASIL GENERATE SEMUA LAPORAN');
                $('#dateRangeInfo').html(`<i class="fa fa-calendar me-2"></i>Periode: ${startDate} s/d ${endDate}`);
                $('#reportData').html(messageHtml);
                
                // Sembunyikan tombol export
                $('#exportPdf, #exportExcel').hide();
            }
        },
        error: function(xhr) {
            $('#loadingAll').addClass('d-none');
            $('.btn-generate-all').prop('disabled', false);
            
            let errorMsg = 'Terjadi kesalahan saat memproses permintaan';
            try {
                const errorResponse = JSON.parse(xhr.responseText);
                errorMsg = errorResponse.message || errorMsg;
            } catch (e) {
                console.error('Error parsing response:', e);
            }
            
            Swal.fire('Error', errorMsg, 'error');
        }
    });
}

// Fungsi untuk generate laporan individual
function generateReport(reportType) {
    const startDate = $(`#startDate${reportType}`).val();
    const endDate = $(`#endDate${reportType}`).val();
    
    // Validasi tanggal
    if (!startDate || !endDate) {
        Swal.fire('Error', 'Harap isi kedua tanggal!', 'error');
        return;
    }
    
    if (new Date(startDate) > new Date(endDate)) {
        Swal.fire('Error', 'Tanggal mulai tidak boleh lebih besar dari tanggal akhir!', 'error');
        return;
    }

    // Tampilkan loading
    $(`#loading${reportType}`).removeClass('d-none');
    $(`button[onclick="generateReport(${reportType})"]`).prop('disabled', true);
    
    // Sembunyikan tombol export
    $('#exportPdf, #exportExcel').hide();
    
    // Tampilkan area laporan
    $('#reportContent').show();
    
    // Set judul laporan
    const reportTitles = {
        1: 'LAPORAN PEMASUKAN BAHAN BAKU',
        2: 'LAPORAN PEMAKAIAN BAHAN BAKU',
        3: 'LAPORAN PEMASUKAN HASIL PRODUKSI',
        4: 'LAPORAN PENGELUARAN HASIL PRODUKSI',
        5: 'LAPORAN MUTASI BAHAN BAKU',
        6: 'LAPORAN MUTASI HASIL PRODUKSI',
        7: 'LAPORAN WASTE/SCRAP'
    };
    
    $('#reportTitle').text(reportTitles[reportType]);
    
    // Format tanggal untuk ditampilkan
    const options = { day: '2-digit', month: 'long', year: 'numeric' };
    const startDateFormatted = new Date(startDate).toLocaleDateString('id-ID', options);
    const endDateFormatted = new Date(endDate).toLocaleDateString('id-ID', options);
    $('#dateRangeInfo').html(`<i class="fa fa-calendar me-2"></i>Periode: ${startDateFormatted} s/d ${endDateFormatted}`);
    
    // Tampilkan loading di tabel
    $('#reportData').html(`<div class="text-center py-5">
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
        <p class="mt-2">Memuat data laporan...</p>
    </div>`);
    
    // AJAX Request
    $.ajax({
        type: "POST",
        url: '<?= base_url('api/laporan') ?>',
        data: {
            report_type: reportType,
            start_date: startDate,
            end_date: endDate
        },
        success: function(response) {
            // Sembunyikan loading
            $(`#loading${reportType}`).addClass('d-none');
            $(`button[onclick="generateReport(${reportType})"]`).prop('disabled', false);
            
            if (response.status === 'success') {
                // Generate tabel
                generateTable(reportType, response.data);
                
                // Tampilkan tombol export
                $('#exportPdf, #exportExcel').show();
                
                // Set event untuk export
                $('#exportPdf').off('click').on('click', function() {
                    exportToPdf(reportType, startDate, endDate, response.columns, response.data);
                });
                
                $('#exportExcel').off('click').on('click', function() {
                    exportToExcel(reportType, startDate, endDate, response.columns, response.data);
                });
            } else {
                $('#reportData').html(`<div class="alert alert-danger">${response.message || 'Gagal memuat data laporan'}</div>`);
            }
        },
        error: function(xhr) {
            $(`#loading${reportType}`).addClass('d-none');
            $(`button[onclick="generateReport(${reportType})"]`).prop('disabled', false);
            
            let errorMsg = 'Terjadi kesalahan saat memuat data';
            try {
                const errorResponse = JSON.parse(xhr.responseText);
                errorMsg = errorResponse.message || errorMsg;
            } catch (e) {
                console.error('Error parsing response:', e);
            }
            
            $('#reportData').html(`<div class="alert alert-danger">${errorMsg}</div>`);
        }
    });
}

// Fungsi untuk generate tabel
function generateTable(reportType, data) {
    const columns = {
        1: ['No','Tgl Rekam', 'Jenis Dokumen','Pabean Nomor','Tanggal','Kode HS','Nomor Seri Barang','Bukti Penerimaan Nomor','Tanggal','Kode BB', 'Nama Barang','Satuan','Jumlah','Mata Uang','Nilai Barang','Gudang','Penerima Subkontrak', 'Negara Asal BB'],
        2: ['No', 'No. Bukti', 'Tanggal', 'Kode Barang', 'Nama Barang', 'Satuan', 'Jumlah Digunakan', 'Jumlah Subkontrak', 'Penerima Subkontrak'],
        3: ['No', 'No. Pengeluaran Barang', 'Tanggal', 'Kode Barang', 'Nama Barang', 'Satuan', 'Jumlah Dari Produksi', 'Jumlah Dari Subkontrak', 'Gudang'],
        4: ['No', 'No. PEB', 'Tanggal', 'Penerima', 'Negara', 'Kode Barang', 'Nama Barang', 'Satuan', 'Jumlah', 'Nilai'],
        5: ['No', 'Kode Barang', 'Nama Barang', 'Satuan', 'Saldo Awal', 'Masuk', 'Keluar', 'Saldo Akhir', 'Gudang'],
        6: ['No', 'Kode Barang', 'Nama Barang', 'Satuan', 'Saldo Awal', 'Masuk', 'Keluar', 'Saldo Akhir', 'Gudang'],
        7: ['No', 'No. BC 2.4', 'Tanggal', 'Kode Barang', 'Nama Barang', 'Satuan', 'Jumlah', 'Nilai']
    };

    let tableHTML = `<div class="table-responsive"><table class="table table-bordered table-striped"><thead><tr>`;
    
    // Header kolom
    columns[reportType].forEach(col => {
        tableHTML += `<th>${col}</th>`;
    });
    
    tableHTML += `</tr></thead><tbody>`;
    
    // Isi data
    if (data && data.length > 0) {
        data.forEach((row, index) => {
            tableHTML += `<tr><td>${index + 1}</td>`;
            Object.values(row).forEach(val => {
                tableHTML += `<td>${val || '-'}</td>`;
            });
            tableHTML += `</tr>`;
        });
    } else {
        tableHTML += `<tr><td colspan="${columns[reportType].length}" class="text-center">Tidak ada data</td></tr>`;
    }
    
    tableHTML += `</tbody></table></div>`;
    $('#reportData').html(tableHTML);
}

// Fungsi untuk export ke PDF
function exportToPdf(reportType, startDate, endDate, column, data) {
    try {
        const { jsPDF } = window.jspdf;
        if (!jsPDF) throw new Error("jsPDF library not loaded");
        
        // Define columns for each report type
        const columns = {
            1: ['No','Tgl Rekam', 'Jenis Dokumen BC 2.0 BC 2.4 BC 2.5 BC 2.8','Pabean Nomor','Tanggal','Kode HS','Nomor Seri Barang','Bukti Penerimaan Nomor','Tanggal','Kode BB', 'Nama Barang','Satuan','Jumlah','Mata Uang','Nilai Barang','Gudang','Penerima Subkontrak', 'Negara Asal BB'],
            2: ['No', 'No. Bukti', 'Tanggal', 'Kode Barang', 'Nama Barang', 'Satuan', 'Jumlah', 'Digunakan', 'Subkontrak', 'Penerima'],
            3: ['No', 'No. Dokumen', 'Tanggal', 'Kode Barang', 'Nama Barang', 'Satuan', 'Jumlah', 'Produksi', 'Subkontrak', 'Gudang'],
            4: ['No', 'No. PEB', 'Tanggal', 'Penerima', 'Negara', 'Kode Barang', 'Nama Barang', 'Satuan', 'Jumlah', 'Nilai'],
            5: ['No', 'Kode Barang', 'Nama Barang', 'Satuan', 'Saldo Awal', 'Masuk', 'Keluar', 'Saldo Akhir', 'Gudang'],
            6: ['No', 'Kode Barang', 'Nama Barang', 'Satuan', 'Saldo Awal', 'Masuk', 'Keluar', 'Saldo Akhir', 'Gudang'],
            7: ['No', 'No. BC 2.4', 'Tanggal', 'Kode Barang', 'Nama Barang', 'Satuan', 'Jumlah', 'Nilai']
        };
        
        const reportTitles = {
            1: 'LAPORAN PEMASUKAN BAHAN BAKU',
            2: 'LAPORAN PEMAKAIAN BAHAN BAKU',
            3: 'LAPORAN PEMASUKAN HASIL PRODUKSI',
            4: 'LAPORAN PENGELUARAN HASIL PRODUKSI',
            5: 'LAPORAN MUTASI BAHAN BAKU',
            6: 'LAPORAN MUTASI HASIL PRODUKSI',
            7: 'LAPORAN WASTE/SCRAP'
        };
        
        const title = reportTitles[reportType];
        const reportColumns = columns[reportType];
        
        if (!reportColumns) throw new Error("Invalid report type");
        
        // Format dates for display
        const options = { day: '2-digit', month: 'long', year: 'numeric' };
        const startDateFormatted = new Date(startDate).toLocaleDateString('id-ID', options);
        const endDateFormatted = new Date(endDate).toLocaleDateString('id-ID', options);
        
        // Initialize PDF document
        const doc = new jsPDF('l', 'mm', 'a4');
        
        // Header
        doc.setFontSize(12);
        doc.text('PT.CHAKRA NAGA FURNITURE', 145, 10, { align: 'center' });
        doc.setFontSize(14);
        doc.setFont('helvetica', 'bold');
        doc.text(title, 145, 22, { align: 'center' });
        doc.setFontSize(10);
        doc.text(`Periode: ${startDateFormatted} s/d ${endDateFormatted}`, 145, 28, { align: 'center' });
        doc.setFontSize(8);
        doc.text('Berdasarkan PER-5/BC/2023 tentang Tata Laksana Monitoring dan Evaluasi', 145, 34, { align: 'center' });
        doc.setDrawColor(0);
        doc.setLineWidth(0.5);
        doc.line(10, 36, 280, 36);
        
        // Prepare table data
        const pdfData = [];
        
        // Add data rows with proper numbering
        if (data && data.length > 0) {
            data.forEach((row, index) => {
                const rowData = [index + 1]; // Add row number
                Object.values(row).forEach(val => rowData.push(val || '-'));
                pdfData.push(rowData);
            });
        } else {
            // Add empty data message if no data
            pdfData.push(['Tidak ada data']);
        }
        
        // Calculate column widths based on content
        const calculateColumnWidths = (columns, data) => {
            const pageWidth = doc.internal.pageSize.width - 20; // 10mm margin on each side
            const columnCount = columns.length;
            
            // For reports with many columns (like reportType 1), use auto width
            if (columnCount > 10) {
                return 'auto';
            }
            
            // For other reports, calculate proportional widths
            const baseWidth = pageWidth / columnCount;
            const columnWidths = {};
            
            columns.forEach((col, i) => {
                // Adjust width for certain columns
                if (col.includes('Nama Barang')) {
                    columnWidths[i] = baseWidth * 1.5;
                } else if (col.includes('Tanggal') || col.includes('Tgl')) {
                    columnWidths[i] = baseWidth * 0.8;
                } else if (col === 'No') {
                    columnWidths[i] = baseWidth * 0.5;
                } else {
                    columnWidths[i] = baseWidth;
                }
            });
            
            return columnWidths;
        };
        
        const columnStyles = calculateColumnWidths(reportColumns, pdfData);
        
        // Create table with improved styling
        doc.autoTable({
            startY: 45,
            head: [reportColumns],
            body: pdfData,
            margin: { left: 10, right: 10 },
            styles: { 
                fontSize: 8,
                cellPadding: 2,
                overflow: 'linebreak',
                valign: 'middle',
                lineColor: [0, 0, 0], // Black borders
                lineWidth: 0.1,
                textColor: [0, 0, 0], // Black text
                fillColor: false // No background
            },
            headStyles: { 
                fillColor: [13, 110, 253], 
                textColor: [255, 255, 255], 
                fontStyle: 'bold',
                fontSize: 8,
                valign: 'middle',
                lineWidth: 0.1,
                lineColor: [0, 0, 0]
            },
            bodyStyles: {
                lineWidth: 0.1,
                lineColor: [0, 0, 0]
            },
            alternateRowStyles: { 
                fillColor: [240, 240, 240] // Light gray for alternate rows
            },
            columnStyles: columnStyles === 'auto' ? undefined : columnStyles,
            tableWidth: columnStyles === 'auto' ? 'wrap' : 'auto',
            pageBreak: 'auto',
            showHead: 'everyPage',
            horizontalPageBreak: true,
            horizontalPageBreakRepeat: 0 // Repeat header row on horizontal page breaks
        });
        
        // Footer
        const pageCount = doc.internal.getNumberOfPages();
        for(let i = 1; i <= pageCount; i++) {
            doc.setPage(i);
            doc.setFontSize(8);
            doc.text(`Halaman ${i} dari ${pageCount}`, 145, doc.internal.pageSize.height - 10, { align: 'center' });
        }
        
        doc.save(`${title.replace(/ /g, '_')}_${startDate}_to_${endDate}.pdf`);
    } catch (error) {
        console.error("Error generating PDF:", error);
        Swal.fire('Error', `Gagal membuat PDF: ${error.message}`, 'error');
    }
}
// Fungsi untuk export ke Excel

function exportToExcel(reportType, startDate, endDate,column, data) {
    // Define columns for each report type
    const columns = {
        1: ['No','Tgl Rekam', 'Jenis Dokumen BC 2.0 BC 2.4 BC 2.5 BC 2.8','Pabean Nomor','Tanggal','Kode HS','Nomor Seri Barang','Bukti Penerimaan Nomor','Tanggal','Kode BB', 'Nama Barang','Satuan','Jumlah','Mata Uang','Nilai Barang','Gudang','Penerima Subkontrak', 'Negara Asal BB'],
        2: ['No', 'No. Bukti', 'Tanggal', 'Kode Barang', 'Nama Barang', 'Satuan', 'Jumlah', 'Digunakan', 'Subkontrak', 'Penerima'],
        3: ['No', 'No. Dokumen', 'Tanggal', 'Kode Barang', 'Nama Barang', 'Satuan', 'Jumlah', 'Produksi', 'Subkontrak', 'Gudang'],
        4: ['No', 'No. PEB', 'Tanggal', 'Penerima', 'Negara', 'Kode Barang', 'Nama Barang', 'Satuan', 'Jumlah', 'Nilai'],
        5: ['No', 'Kode Barang', 'Nama Barang', 'Satuan', 'Saldo Awal', 'Masuk', 'Keluar', 'Saldo Akhir', 'Gudang'],
        6: ['No', 'Kode Barang', 'Nama Barang', 'Satuan', 'Saldo Awal', 'Masuk', 'Keluar', 'Saldo Akhir', 'Gudang'],
        7: ['No', 'No. BC 2.4', 'Tanggal', 'Kode Barang', 'Nama Barang', 'Satuan', 'Jumlah', 'Nilai']
    };
    
    const reportTitles = {
        1: 'LAPORAN_PEMASUKAN_BAHAN_BAKU',
        2: 'LAPORAN_PEMAKAIAN_BAHAN_BAKU',
        3: 'LAPORAN_PEMASUKAN_HASIL_PRODUKSI',
        4: 'LAPORAN_PENGELUARAN_HASIL_PRODUKSI',
        5: 'LAPORAN_MUTASI_BAHAN_BAKU',
        6: 'LAPORAN_MUTASI_HASIL_PRODUKSI',
        7: 'LAPORAN_WASTE_SCRAP'
    };
    
    const title = reportTitles[reportType];
    const fileName = `${title}_${startDate}_to_${endDate}.xlsx`;
    const reportColumns = columns[reportType];
    
    // Siapkan data
    const excelData = [
        ['KEMENTERIAN KEUANGAN REPUBLIK INDONESIA'],
        ['DIREKTORAT JENDERAL BEA DAN CUKAI'],
        [title.replace(/_/g, ' ')],
        [`Periode: ${startDate} s/d ${endDate}`],
        ['Berdasarkan PER-5/BC/2023 tentang Tata Laksana Monitoring dan Evaluasi'],
        [''],
        reportColumns // Use the columns we defined
    ];
    
    // Add data rows
    if (data && data.length > 0) {
        data.forEach((row, index) => {
            const rowData = [index + 1];
            Object.values(row).forEach(val => rowData.push(val || '-'));
            excelData.push(rowData);
        });
    }
    
    // Buat worksheet
    const ws = XLSX.utils.aoa_to_sheet(excelData);
    
    // Set column widths
    const colWidths = reportColumns.map(() => ({ wch: 15 }));
    ws['!cols'] = colWidths;
    
    // Merge header cells
    ws['!merges'] = [
        { s: { r: 0, c: 0 }, e: { r: 0, c: reportColumns.length - 1 } },
        { s: { r: 1, c: 0 }, e: { r: 1, c: reportColumns.length - 1 } },
        { s: { r: 2, c: 0 }, e: { r: 2, c: reportColumns.length - 1 } },
        { s: { r: 3, c: 0 }, e: { r: 3, c: reportColumns.length - 1 } },
        { s: { r: 4, c: 0 }, e: { r: 4, c: reportColumns.length - 1 } }
    ];
    
    // Style header row
    for (let i = 0; i < reportColumns.length; i++) {
        const cellAddress = XLSX.utils.encode_cell({ r: 6, c: i });
        if (!ws[cellAddress]) continue;
        ws[cellAddress].s = {
            font: { bold: true },
            fill: { fgColor: { rgb: "FFD9D9D9" } },
            alignment: { horizontal: 'center' }
        };
    }
    
    // Buat workbook
    const wb = XLSX.utils.book_new();
    XLSX.utils.book_append_sheet(wb, ws, "Laporan");
    XLSX.writeFile(wb, fileName);
}
// Fungsi untuk menghapus semua laporan
function deleteAllReports() {
    Swal.fire({
        title: 'Konfirmasi Hapus Semua Laporan',
        text: "Anda yakin ingin menghapus semua laporan? Tindakan ini tidak dapat dibatalkan!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, Hapus Semua!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            // Tampilkan loading
            const loadingBtn = $('#loadingAll').removeClass('d-none');
            $('.btn-generate-all').prop('disabled', true);
            
            // AJAX Request untuk menghapus semua laporan
            $.ajax({
                type: "POST",
                url: '<?= base_url('laporan/delete-all') ?>', // Sesuaikan dengan endpoint Anda
                dataType: 'json',
                success: function(response) {
                    // Sembunyikan loading
                    loadingBtn.addClass('d-none');
                    $('.btn-generate-all').prop('disabled', false);
                    
                    if (response.status === 'success') {
                        Swal.fire(
                            'Berhasil!',
                            response.message || 'Semua laporan telah dihapus',
                            'success'
                        );
                        
                        // Kosongkan tampilan laporan jika ada
                        $('#reportContent').hide();
                        $('#reportData').empty();
                    } else {
                        Swal.fire(
                            'Error!',
                            response.message || 'Gagal menghapus laporan',
                            'error'
                        );
                    }
                },
                error: function(xhr) {
                    loadingBtn.addClass('d-none');
                    $('.btn-generate-all').prop('disabled', false);
                    
                    let errorMsg = 'Terjadi kesalahan saat menghapus laporan';
                    try {
                        const errorResponse = JSON.parse(xhr.responseText);
                        errorMsg = errorResponse.message || errorMsg;
                    } catch (e) {
                        console.error('Error parsing response:', e);
                    }
                    
                    Swal.fire('Error', errorMsg, 'error');
                }
            });
        }
    });
}
</script>