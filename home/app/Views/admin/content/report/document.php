    <style>
        body {
            background-color: #f8f9fa;
            padding-top: 20px;
            padding-bottom: 40px;
        }
        .header {
            background: linear-gradient(135deg, #6c5ce7, #a29bfe);
            color: white;
            padding: 30px 0;
            border-radius: 10px;
            margin-bottom: 30px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .card {
            border: none;
            border-radius: 10px;
            transition: transform 0.3s, box-shadow 0.3s;
            margin-bottom: 20px;
            height: 100%;
        }
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }
        .card-body {
            padding: 25px;
        }
        .card-icon {
            font-size: 2.5rem;
            margin-bottom: 15px;
            color: #6c5ce7;
        }
        .btn-print {
            background-color: #6c5ce7;
            border: none;
            color: white;
            padding: 8px 20px;
            border-radius: 5px;
            transition: background-color 0.3s;
        }
        .btn-print:hover {
            background-color: #5b4bc4;
        }
        .footer {
            margin-top: 40px;
            text-align: center;
            color: #6c757d;
            font-size: 0.9rem;
        }
        .modal-content {
            border-radius: 10px;
            border: none;
        }
        .modal-header {
            background: linear-gradient(135deg, #6c5ce7, #a29bfe);
            color: white;
            border-radius: 10px 10px 0 0;
        }
        .date-input {
            cursor: pointer;
            background-color: white;
        }
        .form-label {
            font-weight: 500;
        }
        .date-range-container {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
        }
        .date-range-title {
            font-weight: 600;
            margin-bottom: 15px;
            color: #6c5ce7;
        }
        .table-responsive {
            max-height: 400px;
            overflow-y: auto;
        }
        .document-table th {
            position: sticky;
            top: 0;
            background-color: #6c5ce7;
            color: white;
        }
        /* Tambahkan di CSS yang sudah ada */
.spinner-border-sm {
    width: 1rem;
    height: 1rem;
}

.toast {
    z-index: 9999;
}

.btn-group-sm .dropdown-toggle-split {
    padding-left: 0.5rem;
    padding-right: 0.5rem;
}

/* Responsive table */
.table-responsive {
    border-radius: 8px;
}

.document-table th {
    position: sticky;
    top: 0;
    background-color: #6c5ce7;
    color: white;
    z-index: 10;
}

/* Loading animation */
@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

.table tbody tr {
    animation: fadeIn 0.3s ease-in;
}
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header text-center">
            <h1><i class="fas fa-print me-2"></i>Print Document System</h1>
            <p class="lead">Pilih jenis dokumen yang ingin dicetak</p>
        </div>

        <!-- Menu Cards -->
        <div class="row">
            <!-- Purchase Order -->
            <div class="col-md-4 col-sm-6 mb-4">
                <div class="card">
                    <div class="card-body text-center">
                        <div class="card-icon">
                            <i class="fas fa-file-invoice-dollar"></i>
                        </div>
                        <h5 class="card-title">Purchase Order</h5>
                        <p class="card-text">Dokumen untuk memesan barang atau jasa dari supplier</p>
                        <button class="btn btn-print" data-bs-toggle="modal" data-bs-target="#purchaseOrderModal">
                            <i class="fas fa-print me-1"></i> Cetak Dokumen
                        </button>
                    </div>
                </div>
            </div>

            <!-- Good Received Note -->
            <div class="col-md-4 col-sm-6 mb-4">
                <div class="card">
                    <div class="card-body text-center">
                        <div class="card-icon">
                            <i class="fas fa-clipboard-check"></i>
                        </div>
                        <h5 class="card-title">Good Received Note</h5>
                        <p class="card-text">Dokumen penerimaan barang yang telah dikirim oleh supplier</p>
                        <button class="btn btn-print" data-bs-toggle="modal" data-bs-target="#grnModal">
                            <i class="fas fa-print me-1"></i> Cetak Dokumen
                        </button>
                    </div>
                </div>
            </div>

            <!-- Proforma Invoice -->
            <div class="col-md-4 col-sm-6 mb-4">
                <div class="card">
                    <div class="card-body text-center">
                        <div class="card-icon">
                            <i class="fas fa-file-invoice"></i>
                        </div>
                        <h5 class="card-title">Proforma Invoice</h5>
                        <p class="card-text">Dokumen faktur sementara sebelum pengiriman barang</p>
                        <button class="btn btn-print" data-bs-toggle="modal" data-bs-target="#proformaModal">
                            <i class="fas fa-print me-1"></i> Cetak Dokumen
                        </button>
                    </div>
                </div>
            </div>

            <!-- Work Order -->
            <div class="col-md-4 col-sm-6 mb-4">
                <div class="card">
                    <div class="card-body text-center">
                        <div class="card-icon">
                            <i class="fas fa-tasks"></i>
                        </div>
                        <h5 class="card-title">Work Order</h5>
                        <p class="card-text">Dokumen perintah kerja untuk melaksanakan suatu pekerjaan</p>
                        <button class="btn btn-print" data-bs-toggle="modal" data-bs-target="#workOrderModal">
                            <i class="fas fa-print me-1"></i> Cetak Dokumen
                        </button>
                    </div>
                </div>
            </div>

            <!-- Purchase Request -->
            <div class="col-md-4 col-sm-6 mb-4">
                <div class="card">
                    <div class="card-body text-center">
                        <div class="card-icon">
                            <i class="fas fa-shopping-cart"></i>
                        </div>
                        <h5 class="card-title">Purchase Request</h5>
                        <p class="card-text">Dokumen permintaan pembelian barang atau jasa</p>
                        <button class="btn btn-print" data-bs-toggle="modal" data-bs-target="#purchaseRequestModal">
                            <i class="fas fa-print me-1"></i> Cetak Dokumen
                        </button>
                    </div>
                </div>
            </div>

        </div>

        <!-- Footer -->
        <div class="footer">
            <p>&copy; 2023 Print Document System. All rights reserved.</p>
        </div>
    </div>

    <!-- Modal Purchase Order -->
    <div class="modal fade" id="purchaseOrderModal" tabindex="-1" aria-labelledby="purchaseOrderModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="purchaseOrderModalLabel"><i class="fas fa-file-invoice-dollar me-2"></i>Purchase Order</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Filter Tanggal -->
                    <div class="date-range-container">
                        <h6 class="date-range-title"><i class="fas fa-calendar-alt me-2"></i>Filter Berdasarkan Tanggal</h6>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="poStartDate" class="form-label">Tanggal Awal</label>
                                <input type="date" class="form-control" id="poStartDate" placeholder="Pilih tanggal awal">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="poEndDate" class="form-label">Tanggal Akhir</label>
                                <input type="date" class="form-control" id="poEndDate" placeholder="Pilih tanggal akhir">
                            </div>
                        </div>
                        <div class="d-grid">
                            <button class="btn btn-print" onclick="filterDocuments('purchaseOrder')">
                                <i class="fas fa-filter me-1"></i> Filter Dokumen
                            </button>
                        </div>
                    </div>

                    <!-- Tabel Dokumen -->
                    <div class="table-responsive">
                        <table class="table table-striped table-hover document-table">
                            <thead>
                                <tr>
                                    <th>No.</th>
                                    <th>Nomor PO</th>
                                    <th>Tanggal</th>
                                    <th>Supplier</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="poTableBody">
                                <!-- Data akan diisi oleh JavaScript -->
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Good Received Note -->
    <div class="modal fade" id="grnModal" tabindex="-1" aria-labelledby="grnModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="grnModalLabel"><i class="fas fa-clipboard-check me-2"></i>Good Received Note</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Filter Tanggal -->
                    <div class="date-range-container">
                        <h6 class="date-range-title"><i class="fas fa-calendar-alt me-2"></i>Filter Berdasarkan Tanggal</h6>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="grnStartDate" class="form-label">Tanggal Awal</label>
                                <input type="date" class="form-control" id="grnStartDate" placeholder="Pilih tanggal awal">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="grnEndDate" class="form-label">Tanggal Akhir</label>
                                <input type="date" class="form-control" id="grnEndDate" placeholder="Pilih tanggal akhir">
                            </div>
                        </div>
                        <div class="d-grid">
                            <button class="btn btn-print" onclick="filterDocuments('grn')">
                                <i class="fas fa-filter me-1"></i> Filter Dokumen
                            </button>
                        </div>
                    </div>

                    <!-- Tabel Dokumen -->
                    <div class="table-responsive">
                        <table class="table table-striped table-hover document-table">
                            <thead>
                                <tr>
                                    <th>No.</th>
                                    <th>Nomor GRN</th>
                                    <th>Document Import</th>
                                    <th>Tanggal</th>
                                    <th>Supplier</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="grnTableBody">
                                <!-- Data akan diisi oleh JavaScript -->
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Proforma Invoice -->
    <div class="modal fade" id="proformaModal" tabindex="-1" aria-labelledby="proformaModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="proformaModalLabel"><i class="fas fa-file-invoice me-2"></i>Proforma Invoice</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Filter Tanggal -->
                    <div class="date-range-container">
                        <h6 class="date-range-title"><i class="fas fa-calendar-alt me-2"></i>Filter Berdasarkan Tanggal</h6>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="proformaStartDate" class="form-label">Tanggal Awal</label>
                                <input type="date" class="form-control" id="proformaStartDate" placeholder="Pilih tanggal awal">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="proformaEndDate" class="form-label">Tanggal Akhir</label>
                                <input type="date" class="form-control" id="proformaEndDate" placeholder="Pilih tanggal akhir">
                            </div>
                        </div>
                        <div class="d-grid">
                            <button class="btn btn-print" onclick="filterDocuments('proforma')">
                                <i class="fas fa-filter me-1"></i> Filter Dokumen
                            </button>
                        </div>
                    </div>

                    <!-- Tabel Dokumen -->
                    <div class="table-responsive">
                        <table class="table table-striped table-hover document-table">
                            <thead>
                                <tr>
                                    <th>No.</th>
                                    <th>Nomor Proforma</th>
                                    <th>Tanggal</th>
                                    <th>Klien</th>
                                    <th>Jumlah</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="proformaTableBody">
                                <!-- Data akan diisi oleh JavaScript -->
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Work Order -->
    <div class="modal fade" id="workOrderModal" tabindex="-1" aria-labelledby="workOrderModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="workOrderModalLabel"><i class="fas fa-tasks me-2"></i>Work Order</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Filter Tanggal -->
                    <div class="date-range-container">
                        <h6 class="date-range-title"><i class="fas fa-calendar-alt me-2"></i>Filter Berdasarkan Tanggal</h6>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="woStartDate" class="form-label">Tanggal Awal</label>
                                <input type="date" class="form-control" id="woStartDate" placeholder="Pilih tanggal awal">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="woEndDate" class="form-label">Tanggal Akhir</label>
                                <input type="date" class="form-control" id="woEndDate" placeholder="Pilih tanggal akhir">
                            </div>
                        </div>
                        <div class="d-grid">
                            <button class="btn btn-print" onclick="filterDocuments('workOrder')">
                                <i class="fas fa-filter me-1"></i> Filter Dokumen
                            </button>
                        </div>
                    </div>

                    <!-- Tabel Dokumen -->
                    <div class="table-responsive">
                        <table class="table table-striped table-hover document-table">
                            <thead>
                                <tr>
                                    <th>No.</th>
                                    <th>Nomor WO</th>
                                    <th>Tanggal</th>
                                    <th>Deskripsi</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="woTableBody">
                                <!-- Data akan diisi oleh JavaScript -->
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Purchase Request -->
    <div class="modal fade" id="purchaseRequestModal" tabindex="-1" aria-labelledby="purchaseRequestModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="purchaseRequestModalLabel"><i class="fas fa-shopping-cart me-2"></i>Purchase Request</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Filter Tanggal -->
                    <div class="date-range-container">
                        <h6 class="date-range-title"><i class="fas fa-calendar-alt me-2"></i>Filter Berdasarkan Tanggal</h6>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="prStartDate" class="form-label">Tanggal Awal</label>
                                <input type="date" class="form-control" id="prStartDate" placeholder="Pilih tanggal awal">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="prEndDate" class="form-label">Tanggal Akhir</label>
                                <input type="date" class="form-control" id="prEndDate" placeholder="Pilih tanggal akhir">
                            </div>
                        </div>
                        <div class="d-grid">
                            <button class="btn btn-print" onclick="filterDocuments('purchaseRequest')">
                                <i class="fas fa-filter me-1"></i> Filter Dokumen
                            </button>
                        </div>
                    </div>

                    <!-- Tabel Dokumen -->
                    <div class="table-responsive">
                        <table class="table table-striped table-hover document-table">
                            <thead>
                                <tr>
                                    <th>No.</th>
                                    <th>Nomor PR</th>
                                    <th>Tanggal</th>
                                    <th>Departemen</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="prTableBody">
                                <!-- Data akan diisi oleh JavaScript -->
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>




    
    <script>
        // Data contoh untuk setiap jenis dokumen

        // Inisialisasi datepicker untuk semua input tanggal
        document.addEventListener('DOMContentLoaded', function() {
            // Konfigurasi Flatpickr
            const dateConfig = {
                dateFormat: "d-m-Y",
                locale: "id",
                allowInput: true
            };
            
            // Inisialisasi untuk semua input dengan kelas
            flatpickr(".date-input", dateConfig);

            // Isi data awal untuk setiap tabel
            populateTable('purchaseOrder', documentData.purchaseOrder);
            populateTable('grn', documentData.grn);
            populateTable('proforma', documentData.proforma);
            populateTable('workOrder', documentData.workOrder);
            populateTable('purchaseRequest', documentData.purchaseRequest);
        });

        // Fungsi untuk mengisi tabel dengan data
        function populateTable(type, data) {
            const tableBody = document.getElementById(`${type}TableBody`);
            tableBody.innerHTML = '';

            data.forEach((item, index) => {
                const row = document.createElement('tr');
                
                // Format baris berdasarkan jenis dokumen
                switch(type) {
                    case 'purchaseOrder':
                        row.innerHTML = `
                            <td>${index + 1}</td>
                            <td>${item.number}</td>
                            <td>${formatDate(item.date)}</td>
                            <td>${item.supplier}</td>
                            <td><span class="badge ${getStatusBadge(item.status)}">${item.status}</span></td>
                            <td><button class="btn btn-sm btn-print" onclick="printDocument('${type}', ${item.id})"><i class="fas fa-print"></i></button></td>
                        `;
                        break;
                    case 'grn':
                        row.innerHTML = `
                            <td>${index + 1}</td>
                            <td>${item.number}</td>
                            <td>${formatDate(item.date)}</td>
                            <td>${item.supplier}</td>
                            <td><span class="badge ${getStatusBadge(item.status)}">${item.status}</span></td>
                            <td><button class="btn btn-sm btn-print" onclick="printDocument('${type}', ${item.id})"><i class="fas fa-print"></i></button></td>
                        `;
                        break;
                    case 'proforma':
                        row.innerHTML = `
                            <td>${index + 1}</td>
                            <td>${item.number}</td>
                            <td>${formatDate(item.date)}</td>
                            <td>${item.client}</td>
                            <td>${item.amount}</td>
                            <td><button class="btn btn-sm btn-print" onclick="printDocument('${type}', ${item.id})"><i class="fas fa-print"></i></button></td>
                        `;
                        break;
                    case 'workOrder':
                        row.innerHTML = `
                            <td>${index + 1}</td>
                            <td>${item.number}</td>
                            <td>${formatDate(item.date)}</td>
                            <td>${item.description}</td>
                            <td><span class="badge ${getStatusBadge(item.status)}">${item.status}</span></td>
                            <td><button class="btn btn-sm btn-print" onclick="printDocument('${type}', ${item.id})"><i class="fas fa-print"></i></button></td>
                        `;
                        break;
                    case 'purchaseRequest':
                        row.innerHTML = `
                            <td>${index + 1}</td>
                            <td>${item.number}</td>
                            <td>${formatDate(item.date)}</td>
                            <td>${item.department}</td>
                            <td><span class="badge ${getStatusBadge(item.status)}">${item.status}</span></td>
                            <td><button class="btn btn-sm btn-print" onclick="printDocument('${type}', ${item.id})"><i class="fas fa-print"></i></button></td>
                        `;
                        break;
                }
                
                tableBody.appendChild(row);
            });
        }

        // Fungsi untuk memfilter dokumen berdasarkan tanggal
        function filterDocuments(type) {
            const startDateInput = document.getElementById(`${type}StartDate`);
            const endDateInput = document.getElementById(`${type}EndDate`);
            
            const startDate = startDateInput.value ? parseDate(startDateInput.value) : null;
            const endDate = endDateInput.value ? parseDate(endDateInput.value) : null;
            
            let filteredData = documentData[type];
            
            if (startDate && endDate) {
                filteredData = documentData[type].filter(item => {
                    const itemDate = new Date(item.date);
                    return itemDate >= startDate && itemDate <= endDate;
                });
            }
            
            populateTable(type, filteredData);
        }

        // Fungsi untuk mengonversi string tanggal ke objek Date
        function parseDate(dateString) {
            const parts = dateString.split('-');
            return new Date(parts[2], parts[1] - 1, parts[0]);
        }

        // Fungsi untuk memformat tanggal
        function formatDate(dateString) {
            const date = new Date(dateString);
            return date.toLocaleDateString('id-ID', {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric'
            });
        }

        // Fungsi untuk mendapatkan kelas badge berdasarkan status
        function getStatusBadge(status) {
            switch(status) {
                case 'Selesai':
                case 'Disetujui':
                    return 'bg-success';
                case 'Proses':
                case 'Menunggu':
                    return 'bg-warning';
                case 'Ditolak':
                    return 'bg-danger';
                default:
                    return 'bg-secondary';
            }
        }

        // Fungsi untuk mencetak dokumen
        function printDocument(documentType, documentId) {
            // Dalam implementasi nyata, ini akan membuka jendela cetak atau mengarahkan ke halaman cetak
            alert(`Mencetak ${documentType} dengan ID: ${documentId}`);
            // Contoh: window.print() atau window.open('print-page.html?id=' + documentId, '_blank');
        }
    </script>
    <script src="<?= base_url('assets') ?>/js/docreport.js"></script>