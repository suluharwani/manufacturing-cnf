// Data dan konfigurasi
var loc = window.location;
var base_url = loc.protocol + "//" + loc.hostname + (loc.port? ":"+loc.port : "") + "/";

const config = {
    baseUrl: base_url+'/print', // Sesuaikan dengan base URL aplikasi Anda
    documentTypes: {
        purchaseOrder: {
            endpoint: 'purchase-order/data',
            tableId: 'poTableBody',
            startDateId: 'poStartDate',
            endDateId: 'poEndDate',
            statusId: 'poStatus',
            supplierId: 'poSupplier'
        },
        grn: {
            endpoint: 'good-received-note/data',
            tableId: 'grnTableBody',
            startDateId: 'grnStartDate',
            endDateId: 'grnEndDate',
            statusId: 'grnStatus',
            supplierId: 'grnSupplier'
        },
        proforma: {
            endpoint: 'proforma-invoice/data',
            tableId: 'proformaTableBody',
            startDateId: 'proformaStartDate',
            endDateId: 'proformaEndDate',
            statusId: 'proformaStatus',
            customerId: 'proformaCustomer'
        },
        workOrder: {
            endpoint: 'work-order/data',
            tableId: 'woTableBody',
            startDateId: 'woStartDate',
            endDateId: 'woEndDate',
            statusId: 'woStatus',
            invoiceId: 'woInvoice'
        },
        purchaseRequest: {
            endpoint: 'purchase-request/data',
            tableId: 'prTableBody',
            startDateId: 'prStartDate',
            endDateId: 'prEndDate',
            statusId: 'prStatus',
            departmentId: 'prDepartment'
        },
        recent: {
            endpoint: 'recent-documents/data',
            tableId: 'recentTableBody'
        }
    }
};

// Inisialisasi saat dokumen siap
document.addEventListener('DOMContentLoaded', function() {
    initializeApplication();
});

// Fungsi inisialisasi aplikasi
function initializeApplication() {
    // Load data awal untuk semua modal
    loadInitialData();
    
    // Setup event listeners untuk modal
    setupModalEvents();
    
    // Load filter options
    loadFilterOptions();
}

// Setup event listeners untuk modal
function setupModalEvents() {
    // Ketika modal dibuka, load data
    const modals = ['purchaseOrderModal', 'grnModal', 'proformaModal', 'workOrderModal', 'purchaseRequestModal', 'recentModal'];
    
    modals.forEach(modalId => {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.addEventListener('shown.bs.modal', function() {
                const documentType = getDocumentTypeFromModal(modalId);
                if (documentType) {
                    loadDocuments(documentType);
                }
            });
        }
    });
}

// Mendapatkan tipe dokumen dari ID modal
function getDocumentTypeFromModal(modalId) {
    const mapping = {
        'purchaseOrderModal': 'purchaseOrder',
        'grnModal': 'grn',
        'proformaModal': 'proforma',
        'workOrderModal': 'workOrder',
        'purchaseRequestModal': 'purchaseRequest',
        'recentModal': 'recent'
    };
    return mapping[modalId] || null;
}

// Load data awal
function loadInitialData() {
    // Preload data untuk modal yang sering digunakan
    ['purchaseOrder', 'grn'].forEach(type => {
        loadDocuments(type);
    });
}

// Load filter options
function loadFilterOptions() {
    const filterTypes = ['suppliers', 'customers', 'departments', 'status'];
    
    filterTypes.forEach(type => {
        fetch(`${config.baseUrl}/filter-options/${type}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    populateFilterOptions(type, data.data);
                }
            })
            .catch(error => {
                console.error(`Error loading ${type} options:`, error);
            });
    });
}

// Populate filter options
function populateFilterOptions(type, data) {
    const selectors = {
        'suppliers': ['poSupplier', 'grnSupplier'],
        'customers': ['proformaCustomer'],
        'departments': ['prDepartment'],
        'status': ['poStatus', 'grnStatus', 'proformaStatus', 'woStatus', 'prStatus']
    };

    if (selectors[type]) {
        selectors[type].forEach(selectorId => {
            const selectElement = document.getElementById(selectorId);
            if (selectElement) {
                // Clear existing options except the first one
                while (selectElement.options.length > 1) {
                    selectElement.remove(1);
                }
                
                // Add new options
                data.forEach(option => {
                    const optionElement = document.createElement('option');
                    optionElement.value = option.value || option.id;
                    optionElement.textContent = option.text || option.supplier_name || option.customer_name || option.name;
                    selectElement.appendChild(optionElement);
                });
            }
        });
    }
}

// Fungsi utama untuk memuat dokumen
function loadDocuments(documentType, filters = {}) {
    const configType = config.documentTypes[documentType];
    if (!configType) return;

    showLoading(documentType);

    // Build query parameters
    const params = new URLSearchParams();
    
    // Add date filters
    const startDate = document.getElementById(configType.startDateId)?.value;
    const endDate = document.getElementById(configType.endDateId)?.value;
    
    if (startDate) params.append('start_date', startDate);
    if (endDate) params.append('end_date', endDate);
    
    // Add status filter
    const status = document.getElementById(configType.statusId)?.value;
    if (status && status !== 'all') {
        params.append('status', status);
    }
    
    // Add other specific filters
    if (configType.supplierId) {
        const supplier = document.getElementById(configType.supplierId)?.value;
        if (supplier) params.append('supplier', supplier);
    }
    
    if (configType.customerId) {
        const customer = document.getElementById(configType.customerId)?.value;
        if (customer) params.append('customer', customer);
    }
    
    if (configType.departmentId) {
        const department = document.getElementById(configType.departmentId)?.value;
        if (department) params.append('department', department);
    }
    
    if (configType.invoiceId) {
        const invoice = document.getElementById(configType.invoiceId)?.value;
        if (invoice) params.append('invoice', invoice);
    }

    // Add custom filters
    Object.keys(filters).forEach(key => {
        if (filters[key]) {
            params.append(key, filters[key]);
        }
    });

    // Make API request
    fetch(`${config.baseUrl}/${configType.endpoint}?${params.toString()}`)
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                populateTable(documentType, data.data);
                updateDocumentCount(documentType, data.total);
            } else {
                throw new Error(data.message || 'Failed to load data');
            }
        })
        .catch(error => {
            console.error(`Error loading ${documentType}:`, error);
            showError(documentType, error.message);
        })
        .finally(() => {
            hideLoading(documentType);
        });
}

// Fungsi untuk menampilkan data di tabel
function populateTable(documentType, data) {
    const configType = config.documentTypes[documentType];
    const tableBody = document.getElementById(configType.tableId);
    
    if (!tableBody) return;

    tableBody.innerHTML = '';

    if (!data || data.length === 0) {
        tableBody.innerHTML = `
            <tr>
                <td colspan="6" class="text-center text-muted py-4">
                    <i class="fas fa-inbox fa-2x mb-2"></i><br>
                    Tidak ada data ditemukan
                </td>
            </tr>
        `;
        return;
    }

    data.forEach((item, index) => {
        const row = document.createElement('tr');
        row.innerHTML = generateTableRow(documentType, item, index);
        tableBody.appendChild(row);
    });
}

// Generate table row berdasarkan jenis dokumen
function generateTableRow(documentType, item, index) {
    const baseUrl = config.baseUrl;
    
    switch(documentType) {
        case 'purchaseOrder':
            return `
                <td>${index + 1}</td>
                <td>
                    <strong>${item.code || '-'}</strong>
                    ${item.note ? `<br><small class="text-muted">${item.note}</small>` : ''}
                </td>
                <td>${formatDate(item.date)}</td>
                <td>${item.supplier_name || '-'}</td>
                <td><span class="badge ${getStatusBadge(item.status)}">${getStatusText(item.status)}</span></td>
                <td>
                    <div class="btn-group btn-group-sm">
                        <button class="btn btn-print" onclick="printSingleDocument('purchase-order', ${item.id})" title="Cetak">
                            <i class="fas fa-print"></i>
                        </button>
                        <button type="button" class="btn btn-outline-secondary dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown">
                            <span class="visually-hidden">Toggle Dropdown</span>
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#" onclick="printSingleDocument('purchase-order', ${item.id})"><i class="fas fa-print me-2"></i>Cetak</a></li>
                            <li><a class="dropdown-item" href="#" onclick="addToPrintQueue('purchase_order', ${item.id}, '${item.code}')"><i class="fas fa-list me-2"></i>Tambah ke Antrian</a></li>
                        </ul>
                    </div>
                </td>
            `;

        case 'grn':
            return `
                <td>${index + 1}</td>
                <td><strong>${item.invoice || '-'}</strong></td>
                <td><strong>${item.document || '-'}</strong></td>
                <td>${formatDate(item.tanggal_nota)}</td>
                <td>${item.supplier_name || '-'}</td>
               <td>${item.posting == 1 ? '<span class="badge bg-success">Posted</span>' : '<span class="badge bg-danger">Draft</span>'}</td>
                <td>
                    <div class="btn-group btn-group-sm">
                        <button class="btn btn-print" onclick="printSingleDocument('good-received-note', ${item.id})" title="Cetak">
                            <i class="fas fa-print"></i>
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#" onclick="printSingleDocument('good-received-note', ${item.id})"><i class="fas fa-print me-2"></i>Cetak</a></li>
                            <li><a class="dropdown-item" href="#" onclick="addToPrintQueue('good_received_note', ${item.id}, '${item.code}')"><i class="fas fa-list me-2"></i>Tambah ke Antrian</a></li>
                        </ul>
                    </div>
                </td>
            `;

        case 'proforma':
            return `
                <td>${index + 1}</td>
                <td><strong>${item.invoice_number || '-'}</strong></td>
                <td>${formatDate(item.invoice_date)}</td>
                <td>${item.customer_name || '-'}</td>
                <td>${item.currency_code ? formatCurrency(item.grand_total, item.currency_code) : '-'}</td>
                <td>
                    <div class="btn-group btn-group-sm">
                        <button class="btn btn-print" onclick="printSingleDocument('proforma-invoice', ${item.id})" title="Cetak">
                            <i class="fas fa-print"></i>
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#" onclick="printSingleDocument('proforma-invoice', ${item.id})"><i class="fas fa-print me-2"></i>Cetak</a></li>
                            <li><a class="dropdown-item" href="#" onclick="addToPrintQueue('proforma_invoice', ${item.id}, '${item.invoice_number}')"><i class="fas fa-list me-2"></i>Tambah ke Antrian</a></li>
                        </ul>
                    </div>
                </td>
            `;

        case 'workOrder':
            return `
                <td>${index + 1}</td>
                <td><strong>${item.kode || '-'}</strong></td>
                <td>${formatDate(item.created_at)}</td>
                <td>${item.description || item.customer_name || '-'}</td>
                <td><span class="badge ${getStatusBadge(item.status)}">${getStatusText(item.status)}</span></td>
                <td>
                    <div class="btn-group btn-group-sm">
                        <button class="btn btn-print" onclick="printSingleDocument('work-order', ${item.id})" title="Cetak">
                            <i class="fas fa-print"></i>
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#" onclick="printSingleDocument('work-order', ${item.id})"><i class="fas fa-print me-2"></i>Cetak</a></li>
                            <li><a class="dropdown-item" href="#" onclick="addToPrintQueue('work_order', ${item.id}, '${item.kode}')"><i class="fas fa-list me-2"></i>Tambah ke Antrian</a></li>
                        </ul>
                    </div>
                </td>
            `;

        case 'purchaseRequest':
            return `
                <td>${index + 1}</td>
                <td><strong>${item.kode || '-'}</strong></td>
                <td>${formatDate(item.created_at)}</td>
                <td>${item.department_name || '-'}</td>
                <td><span class="badge ${getStatusBadge(item.status)}">${getStatusText(item.status)}</span></td>
                <td>
                    <div class="btn-group btn-group-sm">
                        <button class="btn btn-print" onclick="printSingleDocument('purchase-request', ${item.id})" title="Cetak">
                            <i class="fas fa-print"></i>
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#" onclick="printSingleDocument('purchase-request', ${item.id})"><i class="fas fa-print me-2"></i>Cetak</a></li>
                            <li><a class="dropdown-item" href="#" onclick="addToPrintQueue('purchase_request', ${item.id}, '${item.kode}')"><i class="fas fa-list me-2"></i>Tambah ke Antrian</a></li>
                        </ul>
                    </div>
                </td>
            `;

        case 'recent':
            return `
                <td>${item.document_type || '-'}</td>
                <td><strong>${item.document_number || '-'}</strong></td>
                <td>${formatDate(item.document_date)}</td>
                <td><span class="badge ${getStatusBadge(item.status)}">${getStatusText(item.status)}</span></td>
                <td>
                    <button class="btn btn-sm btn-print" onclick="printRecentDocument('${item.document_type}', ${item.id})" title="Cetak">
                        <i class="fas fa-print"></i>
                    </button>
                </td>
            `;

        default:
            return `<td colspan="6">Unknown document type</td>`;
    }
}

// Fungsi untuk memfilter dokumen
function filterDocuments(documentType) {
    loadDocuments(documentType);
}

// Fungsi untuk reset filter
function resetFilter(documentType) {
    const configType = config.documentTypes[documentType];
    
    // Reset date inputs
    if (configType.startDateId) {
        document.getElementById(configType.startDateId).value = '';
    }
    if (configType.endDateId) {
        document.getElementById(configType.endDateId).value = '';
    }
    
    // Reset select inputs
    ['status', 'supplier', 'customer', 'department', 'invoice'].forEach(field => {
        const fieldId = configType[`${field}Id`];
        if (fieldId) {
            const element = document.getElementById(fieldId);
            if (element) element.value = '';
        }
    });
    
    // Reload data
    loadDocuments(documentType);
}

// Fungsi untuk mencetak dokumen tunggal
function printSingleDocument(documentType, id) {
    const url = `${config.baseUrl}/${documentType}/print/${id}`;
    window.open(url, '_blank');
}

// Fungsi untuk mencetak dokumen recent
function printRecentDocument(documentType, id) {
    const mapping = {
        'Purchase Order': 'purchase-order',
        'Good Received Note': 'good-received-note',
        'Proforma Invoice': 'proforma-invoice',
        'Work Order': 'work-order'
    };
    
    const urlType = mapping[documentType];
    if (urlType) {
        printSingleDocument(urlType, id);
    }
}

// Fungsi untuk menambah ke antrian cetak
function addToPrintQueue(documentType, id, documentNumber) {
    // Implementasi antrian cetak
    console.log(`Added to queue: ${documentType} - ${documentNumber} (ID: ${id})`);
    // Bisa ditambahkan notifikasi atau update UI
    showToast('success', `Dokumen ${documentNumber} ditambahkan ke antrian cetak`);
}

// Utility functions
function formatDate(dateString) {
    if (!dateString) return '-';
    const date = new Date(dateString);
    return date.toLocaleDateString('id-ID', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric'
    });
}

function formatCurrency(amount, currencyCode = 'IDR') {
    if (!amount) return '-';
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: currencyCode
    }).format(amount);
}

function getStatusBadge(status) {
    const statusMap = {
        'pending': 'bg-warning',
        'approved': 'bg-success',
        'completed': 'bg-success',
        'cancelled': 'bg-danger',
        'draft': 'bg-secondary',
        'rejected': 'bg-danger'
    };
    return statusMap[status] || 'bg-secondary';
}

function getStatusText(status) {
    const statusMap = {
        'pending': 'Pending',
        'approved': 'Disetujui',
        'completed': 'Selesai',
        'cancelled': 'Dibatalkan',
        'draft': 'Draft',
        'rejected': 'Ditolak'
    };
    return statusMap[status] || status;
}

function showLoading(documentType) {
    const configType = config.documentTypes[documentType];
    const tableBody = document.getElementById(configType.tableId);
    if (tableBody) {
        tableBody.innerHTML = `
            <tr>
                <td colspan="6" class="text-center py-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2 text-muted">Memuat data...</p>
                </td>
            </tr>
        `;
    }
}

function hideLoading(documentType) {
    // Loading akan otomatis hilang ketika data dimuat
}

function showError(documentType, message) {
    const configType = config.documentTypes[documentType];
    const tableBody = document.getElementById(configType.tableId);
    if (tableBody) {
        tableBody.innerHTML = `
            <tr>
                <td colspan="6" class="text-center text-danger py-4">
                    <i class="fas fa-exclamation-triangle fa-2x mb-2"></i><br>
                    ${message || 'Terjadi kesalahan saat memuat data'}
                    <br>
                    <button class="btn btn-sm btn-outline-primary mt-2" onclick="loadDocuments('${documentType}')">
                        <i class="fas fa-redo me-1"></i> Coba Lagi
                    </button>
                </td>
            </tr>
        `;
    }
}

function updateDocumentCount(documentType, count) {
    // Bisa diimplementasikan untuk menampilkan jumlah dokumen di suatu tempat
    console.log(`${documentType}: ${count} documents loaded`);
}

function showToast(type, message) {
    // Implementasi toast notification
    const toast = document.createElement('div');
    toast.className = `toast align-items-center text-bg-${type} border-0 position-fixed top-0 end-0 m-3`;
    toast.innerHTML = `
        <div class="d-flex">
            <div class="toast-body">${message}</div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    `;
    document.body.appendChild(toast);
    
    const bsToast = new bootstrap.Toast(toast);
    bsToast.show();
    
    toast.addEventListener('hidden.bs.toast', () => {
        document.body.removeChild(toast);
    });
}

// Export functions untuk penggunaan global
window.loadDocuments = loadDocuments;
window.filterDocuments = filterDocuments;
window.resetFilter = resetFilter;
window.printSingleDocument = printSingleDocument;
window.printRecentDocument = printRecentDocument;
window.addToPrintQueue = addToPrintQueue;