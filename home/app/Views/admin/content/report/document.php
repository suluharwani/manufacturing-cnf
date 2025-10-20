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

            <!-- Recent Documents -->
            <div class="col-md-4 col-sm-6 mb-4">
                <div class="card">
                    <div class="card-body text-center">
                        <div class="card-icon">
                            <i class="fas fa-history"></i>
                        </div>
                        <h5 class="card-title">Dokumen Terbaru</h5>
                        <p class="card-text">Lihat dan cetak dokumen yang baru saja dibuat</p>
                        <button class="btn btn-print" data-bs-toggle="modal" data-bs-target="#recentModal">
                            <i class="fas fa-list me-1"></i> Lihat Dokumen
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

    <!-- Modal Recent Documents -->
    <div class="modal fade" id="recentModal" tabindex="-1" aria-labelledby="recentModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="recentModalLabel"><i class="fas fa-history me-2"></i>Dokumen Terbaru</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Jenis Dokumen</th>
                                    <th>Nomor</th>
                                    <th>Tanggal</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Purchase Order</td>
                                    <td>PO-2023-001</td>
                                    <td>15 Nov 2023</td>
                                    <td><span class="badge bg-success">Selesai</span></td>
                                    <td><button class="btn btn-sm btn-print"><i class="fas fa-print"></i></button></td>
                                </tr>
                                <tr>
                                    <td>Good Received Note</td>
                                    <td>GRN-2023-045</td>
                                    <td>14 Nov 2023</td>
                                    <td><span class="badge bg-success">Selesai</span></td>
                                    <td><button class="btn btn-sm btn-print"><i class="fas fa-print"></i></button></td>
                                </tr>
                                <tr>
                                    <td>Work Order</td>
                                    <td>WO-2023-078</td>
                                    <td>13 Nov 2023</td>
                                    <td><span class="badge bg-warning">Proses</span></td>
                                    <td><button class="btn btn-sm btn-print"><i class="fas fa-print"></i></button></td>
                                </tr>
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

    <!-- Bootstrap 5 JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Flatpickr JS -->
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://npmcdn.com/flatpickr/dist/l10n/id.js"></script>
    
    <script>
        // Data contoh untuk setiap jenis dokumen
        const documentData = {
            purchaseOrder: [
                { id: 1, number: 'PO-2023-001', date: '2023-11-15', supplier: 'PT Supplier Jaya', status: 'Selesai' },
                { id: 2, number: 'PO-2023-002', date: '2023-11-14', supplier: 'CV Barokah Abadi', status: 'Proses' },
                { id: 3, number: 'PO-2023-003', date: '2023-11-10', supplier: 'UD Makmur Sentosa', status: 'Selesai' },
                { id: 4, number: 'PO-2023-004', date: '2023-11-05', supplier: 'PT Global Sukses', status: 'Ditolak' },
                { id: 5, number: 'PO-2023-005', date: '2023-11-01', supplier: 'CV Mandiri Jaya', status: 'Selesai' }
            ],
            grn: [
                { id: 1, number: 'GRN-2023-045', date: '2023-11-14', supplier: 'PT Supplier Jaya', status: 'Selesai' },
                { id: 2, number: 'GRN-2023-046', date: '2023-11-12', supplier: 'CV Barokah Abadi', status: 'Selesai' },
                { id: 3, number: 'GRN-2023-047', date: '2023-11-08', supplier: 'UD Makmur Sentosa', status: 'Selesai' },
                { id: 4, number: 'GRN-2023-048', date: '2023-11-03', supplier: 'PT Global Sukses', status: 'Selesai' }
            ],
            proforma: [
                { id: 1, number: 'PRO-2023-101', date: '2023-11-13', client: 'PT Klien Utama', amount: 'Rp 5.250.000' },
                { id: 2, number: 'PRO-2023-102', date: '2023-11-11', client: 'CV Mitra Kerja', amount: 'Rp 3.750.000' },
                { id: 3, number: 'PRO-2023-103', date: '2023-11-07', client: 'UD Sumber Rejeki', amount: 'Rp 8.500.000' }
            ],
            workOrder: [
                { id: 1, number: 'WO-2023-078', date: '2023-11-13', description: 'Pemeliharaan Mesin Produksi', status: 'Proses' },
                { id: 2, number: 'WO-2023-079', date: '2023-11-09', description: 'Instalasi Sistem Baru', status: 'Selesai' },
                { id: 3, number: 'WO-2023-080', date: '2023-11-06', description: 'Perbaikan Jaringan Listrik', status: 'Selesai' }
            ],
            purchaseRequest: [
                { id: 1, number: 'PR-2023-201', date: '2023-11-12', department: 'Produksi', status: 'Disetujui' },
                { id: 2, number: 'PR-2023-202', date: '2023-11-08', department: 'IT', status: 'Disetujui' },
                { id: 3, number: 'PR-2023-203', date: '2023-11-04', department: 'HRD', status: 'Menunggu' },
                { id: 4, number: 'PR-2023-204', date: '2023-11-02', department: 'Marketing', status: 'Disetujui' }
            ]
        };

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