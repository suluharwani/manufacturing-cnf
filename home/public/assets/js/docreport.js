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
                        <button class="btn btn-print" onclick="printSingleDocument('purchase/printPo/', ${item.id})" title="Cetak">
                            <i class="fas fa-print"></i>
                        </button>
                        <button type="button" class="btn btn-outline-secondary dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown">
                            <span class="visually-hidden">Toggle Dropdown</span>
                        </button>
                        
                    </div>
                </td>
            `;

        case 'grn':
            return `
    <td>${index + 1}</td>
    <td><strong>${item.invoice || '-'}</strong></td>
    <td><strong><a href="${base_url}/bc-import/detail/${item.nomor_aju}">${item.document || '-'}</a></strong></td>
    <td>${formatDate(item.tanggal_nota)}</td>
    <td>${item.supplier_name || '-'}</td>
    <td>${item.posting == 1 ? '<span class="badge bg-success">Posted</span>' : '<span class="badge bg-danger">Draft</span>'}</td>
    <td>
        <div class="btn-group btn-group-sm">
            <button class="btn btn-print" onclick="printSingleDocument('pembelian/printGRN/', ${item.id})" title="Cetak">
                <i class="fas fa-print"></i>
            </button>
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
                        <button class="btn btn-print" onclick="printSingleDocument('proformainvoice/printPi/', ${item.id})" title="Cetak">
                            <i class="fas fa-print"></i>
                        </button>
                
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
                        <button class="btn btn-print" onclick="printSingleDocument('printWO', ${item.id})" title="Cetak">
                            <i class="fas fa-print"></i>
                        </button>
                        
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
                        <button class="btn btn-print" onclick="printSingleDocument('materialrequest/printPR', ${item.id})" title="Cetak">
                            <i class="fas fa-print"></i>
                        </button>
                     
                    </div>
                </td>
            `;

        case 'recent':
            return `
                <td>${item.document_type || '-'}</td>
                <td><strong>${item.document_number || '-'}</strong></td>
                <td>${formatDate(item.document_date)}</td>
                <td><span class="badge ${getStatusBadge(item.status)}">${getStatusText(item.status)}</span></td>
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
    const url = `${base_url}/${documentType}/${id}`;
    window.open(url, '_blank');
}

// Fungsi untuk mencetak dokumen recent
function printRecentDocument(documentType, id) {
    const mapping = {
        'Purchase Order': 'purchase/printPo',
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

function exportToExcel(documentType) {
    const configType = config.documentTypes[documentType];
    if (!configType) return;

    // Tampilkan loading
    const tableBody = document.getElementById(configType.tableId);
    const originalContent = tableBody.innerHTML;
    
    showLoading(documentType);

    // Build query parameters (sama seperti filter)
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

    // Request data untuk export
    fetch(`${config.baseUrl}/${configType.endpoint}?${params.toString()}`)
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                generateExcel(documentType, data.data);
            } else {
                throw new Error(data.message || 'Failed to load data for export');
            }
        })
        .catch(error => {
            console.error(`Error exporting ${documentType}:`, error);
            showToast('error', `Gagal mengekspor data: ${error.message}`);
        })
        .finally(() => {
            // Kembalikan konten asli tabel
            tableBody.innerHTML = originalContent;
        });
}

// Fungsi untuk generate file Excel
function generateExcel(documentType, data) {
    // Import library SheetJS (pastikan sudah include di project)
    if (typeof XLSX === 'undefined') {
        showToast('error', 'Library Excel tidak tersedia. Pastikan SheetJS sudah diinclude.');
        return;
    }

    const workbook = XLSX.utils.book_new();
    const worksheetData = prepareExcelData(documentType, data);
    
    const worksheet = XLSX.utils.aoa_to_sheet(worksheetData);
    XLSX.utils.book_append_sheet(workbook, worksheet, getSheetName(documentType));
    
    // Generate filename dengan timestamp
    const timestamp = new Date().toISOString().slice(0, 19).replace(/:/g, '-');
    const filename = `${getDocumentTypeName(documentType)}_${timestamp}.xlsx`;
    
    // Export file
    XLSX.writeFile(workbook, filename);
    showToast('success', `File Excel berhasil diunduh: ${filename}`);
}

// Fungsi untuk menyiapkan data Excel berdasarkan jenis dokumen
function prepareExcelData(documentType, data) {
    const headers = getExcelHeaders(documentType);
    const worksheetData = [headers];
    
    data.forEach((item, index) => {
        const row = generateExcelRow(documentType, item, index);
        worksheetData.push(row);
    });
    
    return worksheetData;
}

function getExcelHeaders(documentType) {
    switch(documentType) {
        case 'purchaseOrder':
            return ['No', 'Nomor PO', 'Tanggal', 'Supplier', 'Status'];
        case 'grn':
            return ['No', 'Nomor GRN', 'Document Import', 'Tanggal', 'Supplier', 'Status'];
        case 'proforma':
            return ['No', 'Nomor Proforma', 'Tanggal', 'Klien', 'Jumlah'];
        case 'workOrder':
            return ['No', 'Nomor WO', 'Tanggal', 'Deskripsi', 'Status'];
        case 'purchaseRequest':
            return ['No', 'Nomor PR', 'Tanggal', 'Departemen', 'Status'];
        default:
            return ['No', 'Dokumen', 'Tanggal', 'Status'];
    }
}

function generateExcelRow(documentType, item, index) {
    switch(documentType) {
        case 'purchaseOrder':
            return [
                index + 1,
                item.code || '-',
                formatDate(item.date),
                item.supplier_name || '-',
                getStatusText(item.status)
            ];
            
        case 'grn':
            return [
                index + 1,
                item.invoice || '-',
                item.document || '-',
                formatDate(item.tanggal_nota),
                item.supplier_name || '-',
                item.posting == 1 ? 'Posted' : 'Draft'
            ];
            
        case 'proforma':
            return [
                index + 1,
                item.invoice_number || '-',
                formatDate(item.invoice_date),
                item.customer_name || '-',
                item.grand_total ? formatCurrencyValue(item.grand_total) : '0'
            ];
            
        case 'workOrder':
            return [
                index + 1,
                item.kode || '-',
                formatDate(item.created_at),
                item.description || item.customer_name || '-',
                getStatusText(item.status)
            ];
            
        case 'purchaseRequest':
            return [
                index + 1,
                item.kode || '-',
                formatDate(item.created_at),
                item.department_name || '-',
                getStatusText(item.status)
            ];
            
        default:
            return [index + 1, 'Unknown', '-', '-'];
    }
}

// Fungsi utility tambahan
function getDocumentTypeName(documentType) {
    const names = {
        'purchaseOrder': 'Purchase_Order',
        'grn': 'Good_Received_Note',
        'proforma': 'Proforma_Invoice',
        'workOrder': 'Work_Order',
        'purchaseRequest': 'Purchase_Request'
    };
    return names[documentType] || 'Document';
}

function getSheetName(documentType) {
    const names = {
        'purchaseOrder': 'Purchase Order',
        'grn': 'Good Received Note',
        'proforma': 'Proforma Invoice',
        'workOrder': 'Work Order',
        'purchaseRequest': 'Purchase Request'
    };
    return names[documentType] || 'Sheet1';
}

function formatCurrencyValue(amount) {
    if (!amount) return 0;
    return typeof amount === 'string' ? parseFloat(amount.replace(/[^\d.-]/g, '')) : amount;
}

// Export function untuk global usage
window.exportToExcel = exportToExcel;