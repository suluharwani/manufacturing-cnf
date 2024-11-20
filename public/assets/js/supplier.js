var loc = window.location;
var base_url = loc.protocol + "//" + loc.hostname + (loc.port ? ":" + loc.port : "") + "/";

initializeSupplierTable();

function initializeSupplierTable() {
    var dataTable = $('#tabel_serverside').DataTable({
        "processing": true,
        "oLanguage": {
            "sLengthMenu": "Tampilkan _MENU_ data per halaman",
            "sSearch": "Pencarian: ",
            "sZeroRecords": "Maaf, tidak ada data yang ditemukan",
            "sInfo": "Menampilkan _START_ s/d _END_ dari _TOTAL_ data",
            "sInfoEmpty": "Menampilkan 0 s/d 0 dari 0 data",
            "sInfoFiltered": "(di filter dari _MAX_ total data)",
            "oPaginate": {
                "sFirst": "<<",
                "sLast": ">>",
                "sPrevious": "<",
                "sNext": ">"
            }
        },
        "dom": 'Bfrtip',
        "buttons": ['csv'],
        "order": [],
        "ordering": true,
        "info": true,
        "serverSide": true,
        "stateSave": true,
        "scrollX": true,
        "ajax": {
            "url": base_url + "supplier/listdataSupplierJoin",
            "type": "POST",
            "data": {}
        },
        columns: [

            { mRender: function (data, type, row) { return row[0]; } }, // No
            { mRender: function (data, type, row) { return row[3]; } }, // Code
            { mRender: function (data, type, row) { return row[4]; } }, // Nama Sup
            { mRender: function (data, type, row) { return row[1]; } }, // 
            { mRender: function (data, type, row) { return row[10]; } }, // 

        ],
        "columnDefs": [{
            "targets": [0],
            "orderable": false
        }],
        error: function () {
            $(".tabel_serverside-error").html("");
            $("#tabel_serverside").append('<tbody class="tabel_serverside-error"><tr><th colspan="3">Data Tidak Ditemukan di Server</th></tr></tbody>');
            $("#tabel_serverside_processing").css("display", "none");
        }
    });
}

function showImagePopup(src, alt) {
    Swal.fire({
        title: alt,
        imageUrl: src,
        imageAlt: alt,
        showCloseButton: true,
        imageWidth: '100%',
        imageHeight: 'auto',
        background: '#fff',
        confirmButtonText: 'Close'
    });
}

// Tambah Supplier



// Simpan Supplier
// Fungsi untuk membuka modal dan menambahkan supplier baru
$('.addSupplier').on('click', function () {
    // Bersihkan form modal
    $('#supplierForm')[0].reset();
    $('#supplierForm').find('[name="id"]').val(""); // Mengatur input ID sebagai kosong
    $('#ajaxImgUpload').attr('src', 'https://via.placeholder.com/300'); // Set image preview menjadi default

    // Mengisi opsi currency
    loadCurrencies();

    // Ganti label modal menjadi "Add"
    $('#supplierModal').modal('show');
    $('.modal-title').text('Add Supplier');
});

// Edit Supplier
$(document).on('click', '.editSupplier', function () {
    let supplierId = $(this).data('id');
    
    // Ambil data supplier berdasarkan ID
    $.ajax({
        type: 'GET',
        url: base_url + "supplier/get/" + supplierId,
        success: function (response) {
            let data = response[0];

            // Mengisi semua data yang relevan ke dalam form
            $('#supplierForm [name="id"]').val(data.id); // Isi ID ke dalam input hidden
            $('#supplierForm [name="kode"]').val(data.code || '');
            $('#supplierForm [name="supplier_name"]').val(data.supplier_name || '');
            $('#supplierForm [name="contact_name"]').val(data.contact_name || '');
            $('#supplierForm [name="contact_email"]').val(data.contact_email || '');
            $('#supplierForm [name="contact_phone"]').val(data.contact_phone || '');
            $('#supplierForm [name="address"]').val(data.address || '');
            $('#supplierForm [name="city"]').val(data.city || '');
            $('#supplierForm [name="state"]').val(data.state || '');
            $('#supplierForm [name="postal_code"]').val(data.postal_code || '');
            $('#supplierForm [name="country"]').val(data.country || '');
            $('#supplierForm [name="tax_number"]').val(data.tax_number || '');
            $('#supplierForm [name="website_url"]').val(data.website_url || '');
            $('#supplierForm [name="status"]').val(data.status || 'active');

            // Load currency options and set the selected one
            loadCurrencies(data.id_currency);

            // Set image preview jika logo ada
            if (data.logo_url) {
                $('#ajaxImgUpload').attr('src', base_url + 'uploads/supplier_logos/' + data.logo_url);
            } else {
                $('#ajaxImgUpload').attr('src', 'https://via.placeholder.com/300');
            }

            // Tampilkan modal dengan data yang sudah diisi
            $('#supplierModal').modal('show');
            $('.modal-title').text('Edit Supplier');
        },
        error: function (xhr) {
            let error = JSON.parse(xhr.responseText);
            Swal.fire('Error', error.message, 'error');
        }
    });
});

// Save (Add or Update) Supplier
$('.saveSupplier').on('click', function () {
    let formData = new FormData($('#supplierForm')[0]);
    let supplierId = $('#supplierForm [name="id"]').val(); // Mengambil nilai dari input hidden untuk menentukan apakah ini add atau edit

    if (supplierId) {
        // Update Supplier
        $.ajax({
            type: 'POST',
            url: base_url + "supplier/update/" + supplierId,
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
                Swal.fire('Berhasil!', 'Data Supplier berhasil diperbarui', 'success');
                $('#tabel_serverside').DataTable().ajax.reload();
                $('#supplierModal').modal('hide');
            },
            error: function (xhr) {
                let error = JSON.parse(xhr.responseText);
                Swal.fire('Error', error.message, 'error');
            }
        });
    } else {
        // Add Supplier
        $.ajax({
            type: 'POST',
            url: base_url + "supplier/create",
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
                Swal.fire('Berhasil!', 'Data Supplier berhasil ditambahkan', 'success');
                $('#tabel_serverside').DataTable().ajax.reload();
                $('#supplierModal').modal('hide');
            },
            error: function (xhr) {
                let error = JSON.parse(xhr.responseText);
                Swal.fire('Error', error.message, 'error');
            }
        });
    }
});

// Hapus Supplier
$(document).on('click', '.deleteSupplier', function () {
    let supplierId = $(this).data('id');
    Swal.fire({
        title: 'Apakah Anda yakin?',
        text: "Data supplier akan dihapus!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                type: 'DELETE',
                url: base_url + "supplier/delete/" + supplierId,
                success: function () {
                    Swal.fire('Berhasil!', 'Supplier berhasil dihapus', 'success');
                    $('#tabel_serverside').DataTable().ajax.reload();
                },
                error: function (xhr) {
                    let error = JSON.parse(xhr.responseText);
                    Swal.fire('Error', error.message, 'error');
                }
            });
        }
    });
});

function loadCurrencies(selectedCurrencyId = null) {
    $.ajax({
        type: "GET",
        url: base_url + "dashboard/getCurrencyData",
        async: true,
        dataType: 'json',
        success: function (data) {
            let selOpts = '<option value="">-- Select Currency --</option>'; // Placeholder awal

            // Iterasi untuk membuat opsi dropdown dan tentukan yang terpilih
            $.each(data, function (k, v) {
                var id = v.id;
                var nama = v.nama;
                var kode = v.kode;

                // Menambahkan opsi ke dropdown dengan kondisi terpilih
                if (selectedCurrencyId && selectedCurrencyId == id) {
                    selOpts += `<option value="${id}" selected>${kode} - ${nama}</option>`;
                } else {
                    selOpts += `<option value="${id}">${kode} - ${nama}</option>`;
                }
            });

            // Mengisi opsi ke dalam elemen select dengan id "currency"
            $('#currency').html(selOpts);
        },
        error: function (xhr, status, error) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Failed to load currencies: ' + error,
            });
        }
    });
}