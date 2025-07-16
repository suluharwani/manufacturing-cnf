var loc = window.location;
var base_url = loc.protocol + "//" + loc.hostname + (loc.port? ":"+loc.port : "") + "/";


$(document).ready(function() {
  var dataTable = $('#tabel_serverside').DataTable( {
    "processing" : true,
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
    "buttons": [
    'csv'
    ],
    "order": [],
    "ordering": true,
    "info": true,
    "serverSide": true,
    "stateSave" : true,
    "scrollX": true,
    "ajax":{
      "url" :base_url+"proformainvoice/listdata" , // json datasource 
      "type": "post",  // method  , by default get
      // "async": false,
      "dataType": 'json',
      "data":{},
    },
    
    columns: [
    {},
    {mRender: function (data, type, row) {
        return row[2]
    }},
    {mRender: function (data, type, row) {
        return row[3]
    }},
    {mRender: function (data, type, row) {
        return row[4]
    }},
    {mRender: function (data, type, row) {
     return `
     <a href="${base_url}proformainvoice/piDoc/${row[1]}"  class="btn btn-warning btn-sm showDocumentShipment" id="${row[1]}" >Document&Shipment</a>
     <a href="${base_url}proformainvoice/pi/${row[1]}"  class="btn btn-success btn-sm showPurchaseOrder" id="${row[1]}" >Detail</a>
     <a href="javascript:void(0);" class="btn btn-danger btn-sm deletePi" id="${row[1]}" invoice = "${row[2]}" >Delete</a>
     `; 
   
    }}
  ],
  "columnDefs": [{
    "targets": [0],
    "orderable": false
  }],

  error: function(){  // error handling
    $(".tabel_serverside-error").html("");
    $("#tabel_serverside").append('<tbody class="tabel_serverside-error"><tr><th colspan="3">Data Tidak Ditemukan di Server</th></tr></tbody>');
    $("#tabel_serverside_processing").css("display","none");

  }

});
})
function getCurrentDate() {
    // Create a new Date object for the current date
    const currentDate = new Date();

    // Extract the year, month, and day
    const year = currentDate.getFullYear();
    const month = String(currentDate.getMonth() + 1).padStart(2, '0'); // Months are zero-based
    const day = String(currentDate.getDate()).padStart(2, '0');

    // Format the date as YYYY-MM-DD
    const formattedDate = `${year}-${month}-${day}`;

    // Return the formatted date
    return formattedDate;
}

$('.tambahProformaInvoice').on('click', function () {
    const currentDate = getCurrentDate();
    
    // Menampilkan SweetAlert dan menambahkan dynamic select option untuk customer
    Swal.fire({
        title: `Tambah Proforma Invoice`,
        html: `
        <form id="form_add_data">
            
            <div class="form-group">
                <label for="customer">Customer</label>
                <select id="customer" class="form-control">
                    <option value="">Pilih Customer</option>
                </select>
            </div>
            <div class="form-group">
                <label for="kode">Kode</label>
                <input type="text" class="form-control" id="kode" aria-describedby="kodeHelp" placeholder="Kode" readonly>
            </div>
        </form>`,
        confirmButtonText: 'Confirm',
        focusConfirm: false,
        preConfirm: () => {
            const kode = Swal.getPopup().querySelector('#kode').value;
            const customerId = Swal.getPopup().querySelector('#customer').value;
            if (!kode || !customerId) {
                Swal.showValidationMessage('Silakan lengkapi data');
            }
            return { kode: kode, customerId: customerId };
        }
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                type: "POST",
                url: base_url + '/proformainvoice/add',
                async: false,
                data: { 
                    invoice_number: result.value.kode, 
                    customer_id: result.value.customerId,
                    invoice_date: currentDate
                },
                success: function (data) {
                    $('#tabel_serverside').DataTable().ajax.reload();
                    Swal.fire({
                        position: 'center',
                        icon: 'success',
                        title: `Proforma Invoice berhasil ditambahkan.`,
                        showConfirmButton: false,
                        timer: 1500
                    });
                },
                error: function (xhr) {
                    let d = JSON.parse(xhr.responseText);
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: `${d.message}`,
                        footer: '<a href="">Why do I have this issue?</a>'
                    });
                }
            });
        }
    });

    // Generate code when customer is selected
    $('#customer').on('change', function() {
        const customerId = $(this).val();
        if (customerId) {
            generateCode(customerId);
        } else {
            $('#kode').val('');
        }
    });

    // Menambahkan opsi customer ke select dropdown
    getCustomerOption().then(options => {
        $('#customer').html(options); // Isi select dengan opsi customer
    }).catch(error => {
        Swal.fire('Error', error, 'error');
    });
});

function generateCode(customerId) {
    $.ajax({
        url: base_url + 'generate/generateCode/' + customerId,
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            $('#kode').val(response.code);
        },
        error: function(xhr) {
            Swal.fire('Error', 'Gagal generate kode', 'error');
        }
    });
}

function getCustomerOption() {
    return new Promise((resolve, reject) => {
        $.ajax({
            type: 'POST',
            url: base_url + 'proformainvoice/getCustomerList', // Endpoint untuk mendapatkan produk
            success: function (response) {
                // Buat opsi produk dari data yang diterima
                var options = '<option value="">Pilih Customer</option>';
                response.forEach(function (customer) {
                    options += `<option value="${customer.id}">${customer.customer_name}</option>`;
                });
                // Resolving the promise dengan materialOptions setelah sukses
                resolve(options);
            },
            error: function (xhr) {
                // Menolak promise jika terjadi kesalahan
                reject('Terjadi kesalahan saat mengambil daftar');
            }
        });
    });
}

$('#tabel_serverside').on('click','.deletePi',function(){
    const id = $(this).attr('id');
    const invoice = $(this).attr('invoice');
    Swal.fire({
        title: 'Anda yakin ingin menghapus invoice: '+invoice+'?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Hapus',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: base_url + '/proformainvoice/deleteinvoice/'+id, 
                type: 'post',
                success: function(response) {
                    Swal.fire('Dihapus!', response.message, 'success');
                    $('#tabel_serverside').DataTable().ajax.reload();

                },
                error: function(xhr) {
                    let d = JSON.parse(xhr.responseText);
                    Swal.fire('Oops...', d.message, 'error');
                }
            });
        }
    });
});