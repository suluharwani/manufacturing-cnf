var loc = window.location;
var base_url = loc.protocol + "//" + loc.hostname + (loc.port ? ":" + loc.port : "") + "/";

$(document).ready(function() {
    initializeCustomerTable();
});

function initializeCustomerTable() {
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
            "url": base_url + "customer/listdataCustomerJoin",
            "type": "POST",
            "data": {}
        },
        columns: [

            { mRender: function (data, type, row) { return row[0]; } }, // No
            { mRender: function (data, type, row) { return row[3]; } }, // Code
            { mRender: function (data, type, row) { return row[4]; } }, // Nama Sup
            { mRender: function (data, type, row) {  if(row[1]==1){status = 'active'}else{status = 'inactive'} return status } }, 
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

// Tambah customer



// Simpan customer
// Fungsi untuk membuka modal dan menambahkan customer baru
$('.addCustomer').on('click', function () {
    // Bersihkan form modal
    $('#customerForm')[0].reset();
    $('#customerForm').find('[name="id"]').val(""); // Mengatur input ID sebagai kosong
    $('#ajaxImgUpload').attr('src', 'https://via.placeholder.com/300'); // Set image preview menjadi default

    // Mengisi opsi currency
    loadCurrencies();
    loadCountry();
    // Ganti label modal menjadi "Add"
    $('#customerModal').modal('show');
    $('.modal-title').text('Add Customer');
});

$(document).on('click', '.logoCustomer', function (){

    let customerId = $(this).data('id');
    let customerName = $(this).data('name');
    let logo_url = $(this).data('logo');
    $("#ajaxImgUpload").attr("src",  `${base_url}assets/upload/1000/${logo_url}`);
    $('#id_cus_edit_image').val(customerId);
    $('#modalEditImage').modal('show');

    $('.modal-title').text(`Edit Image ${customerName}`);
});
// Edit Customer
$(document).on('click', '.editCustomer', function () {
    let customerId = $(this).data('id');
    
    // Ambil data Customer berdasarkan ID
    $.ajax({
        type: 'GET',
        url: base_url + "customer/get/" + customerId,
        success: function (response) {
            let data = response[0];

            // Mengisi semua data yang relevan ke dalam form
            $('#customerForm [name="id"]').val(data.cus_id); // Isi ID ke dalam input hidden
            $('#customerForm [name="code"]').val(data.code || '');
            $('#customerForm [name="customer_name"]').val(data.customer_name || '');
            $('#customerForm [name="contact_name"]').val(data.contact_name || '');
            $('#customerForm [name="contact_email"]').val(data.contact_email || '');
            $('#customerForm [name="contact_phone"]').val(data.contact_phone || '');
            $('#customerForm [name="address"]').val(data.address || '');
            $('#customerForm [name="city"]').val(data.city || '');
            $('#customerForm [name="state"]').val(data.state || '');
            $('#customerForm [name="postal_code"]').val(data.postal_code || '');
            $('#customerForm [name="id_country"]').val(data.id_country || '');
            $('#customerForm [name="tax_number"]').val(data.tax_number || '');
            $('#customerForm [name="website_url"]').val(data.website_url || '');
            $('#customerForm [name="status"]').val(data.status || 'active');

            // Load currency options and set the selected one
            loadCurrencies(data.id_currency);
            loadCountry(data.id_country)
            // Set image preview jika logo ada
            if (data.logo_url) {
                $('#ajaxImgUpload').attr('src', base_url + 'uploads/customer_logos/' + data.logo_url);
            } else {
                $('#ajaxImgUpload').attr('src', 'https://via.placeholder.com/300');
            }

            // Tampilkan modal dengan data yang sudah diisi
            $('#customerModal').modal('show');
            $('.modal-title').text('Edit Customer');
        },
        error: function (xhr) {
            let error = JSON.parse(xhr.responseText);
            Swal.fire('Error', error.message, 'error');
        }
    });
});

// Save (Add or Update) Customer
$('.saveCustomer').on('click', function () {
    let formData = new FormData($('#customerForm')[0]);
    let customerId = $('#customerForm [name="id"]').val(); // Mengambil nilai dari input hidden untuk menentukan apakah ini add atau edit

    if (customerId) {
        // Update Customer
        $.ajax({
            type: 'POST',
            url: base_url + "customer/update/" + customerId,
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
                if (response.status) {
                    Swal.fire('Berhasil!', 'Data Customer berhasil diperbarui', 'success');
                    $('#tabel_serverside').DataTable().ajax.reload();
                    $('#customerModal').modal('hide');
                } else {
                    displayErrorMessages(response.message);
                }
            },
            error: function (xhr) {
                let error = JSON.parse(xhr.responseText);
                Swal.fire('Error', error.message, 'error');
            }
        });
    } else {
        // Add Customer
        $.ajax({
            type: 'POST',
            url: base_url + "customer/create",
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
                if (response.status) {
                    Swal.fire('Berhasil!', 'Data Customer berhasil ditambahkan', 'success');
                    $('#tabel_serverside').DataTable().ajax.reload();
                    $('#customerModal').modal('hide');
                } else {
                   displayErrorMessages(response.message);
                }
            },
            error: function (xhr) {
                let error = JSON.parse(xhr.responseText);
                Swal.fire('Error', error.message, 'error');
            }
        });
    }
});
function displayErrorMessages(messages) {
    let errorMessage = '';

    // Iterasi melalui pesan error dari response.message
    $.each(messages, function (key, value) {
        errorMessage += `${value} <br>`;
    });

    Swal.fire({
        icon: 'error',
        title: 'Validation Error',
        html: errorMessage, // Menampilkan semua pesan error
    });
}

// Hapus Customer
$(document).on('click', '.deleteCustomer', function () {
    let customerId = $(this).data('id');
    Swal.fire({
        title: 'Apakah Anda yakin?',
        text: "Data customer akan dihapus!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                type: 'post',
                url: base_url + "customer/delete/" + customerId,
                success: function () {
                    Swal.fire('Berhasil!', 'Customer berhasil dihapus', 'success');
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

function loadCountry(selectedCountryId = null) {
    $.ajax({
        type: "GET",
        url: base_url + "dashboard/getCountryData",
        async: true,
        dataType: 'json',
        success: function (data) {
            let selOpts = '<option value="">-- Select Country --</option>'; // Placeholder awal

            // Iterasi untuk membuat opsi dropdown dan tentukan yang terpilih
            $.each(data, function (k, v) {
                var id = v.id;
                var nama = v.nama;
                var kode = v.kode;

                // Menambahkan opsi ke dropdown dengan kondisi terpilih
                if (selectedCountryId && selectedCountryId == id) {
                    selOpts += `<option value="${id}" selected>${kode} - ${nama}</option>`;
                } else {
                    selOpts += `<option value="${id}">${kode} - ${nama}</option>`;
                }
            });

            // Mengisi opsi ke dalam elemen select dengan id "Country"
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
function loadCountry(selectedCountryId = null) {
    $.ajax({
        type: "GET",
        url: base_url + "dashboard/getCountryData",
        async: true,
        dataType: 'json',
        success: function (data) {
            let selOpts = '<option value="">-- Select Country --</option>'; // Placeholder awal

            // Iterasi untuk membuat opsi dropdown dan tentukan yang terpilih
            $.each(data, function (k, v) {
                var id = v.id_country;
                var nama = v.country_name;
                var kode = v.code2;

                // Menambahkan opsi ke dropdown dengan kondisi terpilih
                if (selectedCountryId && selectedCountryId == id) {
                    selOpts += `<option value="${id}" selected>${kode} - ${nama}</option>`;
                } else {
                    selOpts += `<option value="${id}">${kode} - ${nama}</option>`;
                }
            });

            // Mengisi opsi ke dalam elemen select dengan id "Country"
            $('#country').html(selOpts);
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
$(document).on('click', '.viewCustomer', function () {
    let customerId = $(this).data('id');
    $.ajax({
        type: 'GET',
        url: base_url + 'customer/get/' + customerId,
        success: function (response) {
            let data = response[0];
            $('.modal-title').text(`View Data ${data.customer_name}`);
            
            // Mengisi data ke modal
            $('#viewCustomerModal #customer_name').text(data.customer_name);
            $('#viewCustomerModal #contact_name').text(data.contact_name);
            $('#viewCustomerModal #contact_email').text(data.contact_email);
            $('#viewCustomerModal #contact_phone').text(data.contact_phone);
            $('#viewCustomerModal #address').text(data.address);
            $('#viewCustomerModal #city').text(data.city);
            $('#viewCustomerModal #state').text(data.state);
            $('#viewCustomerModal #postal_code').text(data.postal_code);
            $('#viewCustomerModal #country').text(data.country_name);
            $('#viewCustomerModal #currency_name').text(data.kode+'-'+data.nama);
            $('#viewCustomerModal #logo').html(`<img src=" ${base_url}assets/upload/1000/${data.logo_url}  " style='height: 50px;'>`);

            // Tampilkan modal
            $('#viewCustomerModal').modal('show');
        },
        error: function (xhr) {
            let error = JSON.parse(xhr.responseText);
            Swal.fire('Error', error.message, 'error');
        }
    });
});

$('#file').change(function(){
  $('.uploadBtn').html('Upload');
  $('.uploadBtn').prop('disabled', false);
  $('.uploadBtn').addClass("btn-danger");
  $('.uploadBtn').removeClass("btn-success");
  $('#picture').val('');
  const file = this.files[0];
  if (file){
    let reader = new FileReader();
    reader.onload = function(event){
      $('#ajaxImgUpload').attr('src', event.target.result).width(300);
    }
    reader.readAsDataURL(file);
  }
});
$('.reset').on('click',function(){
  $('#form').trigger("reset");
  $('.uploadBtn').html('Upload');
  $('.uploadBtn').prop('disabled', false);
  $('.uploadBtn').addClass("btn-danger");
  $('.uploadBtn').removeClass("btn-success");
  $('#ajaxImgUpload').attr('src', 'https://via.placeholder.com/300');
})


$('.uploadBtn').on('click',function(){
  let id = $('#id_cus_edit_image').val()

  upload(id)
  $('#tabel_serverside').DataTable().ajax.reload();

})
function upload(id){
  input = $('#file').prop('files')[0];

  param = ''
  data = new FormData();
          // data['file'] = input;
  data.append('id', id);
  data.append('file', input);
  data.append('param', param);
  data.append('data', '');
  $('.uploadBtn').html('Uploading ...');
  $('.uploadBtn').attr('disabled');
  if (!input) {
    alert("Choose File");
    $('.uploadBtn').html('Upload');
    $('.uploadBtn').prop('disabled', false);
  } else {
    $.ajax({
     type : "POST",
     enctype: 'multipart/form-data',
     url  : base_url+"customer/upload",
     async : false,
     processData: false,
     contentType: false,
     data:data,
     success: function (res) {
      if (res.success == true) {
        $('.uploadBtn').html('Uploaded!');
        $('.uploadBtn').prop('disabled', true);
        $('.uploadBtn').removeClass("btn-danger");
        $('.uploadBtn').addClass("btn-success");
        $('#picture').val(res.picture);

                                // $('#ajaxImgUpload').attr('src', 'https://via.placeholder.com/300');
        $('#alertMsg').addClass("text-success");
        $('#alertMsg').html(res.msg);
        $('#alertMessage').show();
      } else if (res.success == false) {
        $('#alertMsg').addClass("text-danger");
        $('#alertMsg').html(res.msg);
        $('#picture').val('');

        $('#alertMessage').show();
        $('.uploadBtn').html('Upload Failed!');
        $('.uploadBtn').prop('disabled', false);
        $('.uploadBtn').addClass("btn-danger");
        $('.uploadBtn').removeClass("btn-success");

      }
      setTimeout(function () {
        $('#alertMsg').html('');
        $('#alertMessage').hide();
      }, 4000);

                            // document.getElementById("form").reset();
    }
  });
  }
}
