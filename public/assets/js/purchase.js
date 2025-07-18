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
      "url" :base_url+"purchase/listdataPurchaseOrder" , // json datasource 
      "type": "post",  // method  , by default get
      // "async": false,
      "dataType": 'json',
      "data":{},
    },
    
    columns: [
    {},
    {mRender: function (data, type, row) {
        return row[1]
    }},
    {mRender: function (data, type, row) {
        return row[2]
    }},
    {mRender: function (data, type, row) {
        return row[5]
    }},
    {mRender: function (data, type, row) {
      if (row[3] == 0) {
        status = `<span class="badge bg-secondary">Draft</span>`
      }else if (row[3]==1) {
        status = `<span class="badge bg-primary">Diposting</span>`
      }else{
        status =  `<span class="badge bg-danger">Tidak ada</span>`
      }
      return status
    }},
    {mRender: function (data, type, row) {
     return `<a href="${base_url}purchase/po/${row[4]}"  class="btn btn-success btn-sm showPurchaseOrder" id="${row[4]}" >Detail</a>`; 
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
$('.addPurchaseOrder').on('click', function() {
    // Generate code first via AJAX
    $.ajax({
        url: base_url + '/purchase/generateCode',
        type: 'GET',
        async: false, // Synchronous to ensure we get the code first
        success: function(response) {
            showPurchaseOrderPopup(response.code);
        },
        error: function(xhr) {
            Swal.fire('Error', 'Gagal generate kode PO', 'error');
        }
    });
});

function showPurchaseOrderPopup(generatedCode) {
    const currentDate = new Date().toISOString().split('T')[0];
    
    Swal.fire({
        title: `Tambah Purchase Order`,
        html: `
        <form id="form_add_data">
            <div class="form-group">
                <label for="kode">Kode PO</label>
                <input type="text" class="form-control" id="kode" value="${generatedCode}" readonly>
            </div>
            <div class="form-group">
                <label for="supplier">Supplier</label>
                <select id="supplier" class="form-control" required>
                    <option value="">Pilih Supplier</option>
                </select>
            </div>
            <div class="form-group">
                <label for="date">Tanggal</label>
                <input type="date" class="form-control" id="date" value="${currentDate}" required>
            </div>
        </form>`,
        confirmButtonText: 'Simpan',
        focusConfirm: false,
        didOpen: () => {
            // Load supplier options
            getSupplierOption().then(options => {
                $('#supplier').html('<option value="">Pilih Supplier</option>' + options);
            }).catch(error => {
                Swal.fire('Error', error, 'error');
            });
        },
        preConfirm: () => {
            const kode = $('#kode').val();
            const supplier = $('#supplier').val();
            const date = $('#date').val();

            if (!kode || !supplier || !date) {
                Swal.showValidationMessage('Harap lengkapi semua field yang wajib diisi');
                return false;
            }

            return {
                code: kode,
                supplier_id: supplier,
                date: date,
                status: 0
            };
        }
    }).then((result) => {
        if (result.isConfirmed && result.value) {
            $.ajax({
                type: "POST",
                url: base_url + '/purchase/add_po',
                data: result.value,
                success: function(response) {
                    $('#tabel_serverside').DataTable().ajax.reload();
                    Swal.fire({
                        position: 'center',
                        icon: 'success',
                        title: 'Purchase Order berhasil ditambahkan',
                        showConfirmButton: false,
                        timer: 1500
                    });
                },
                error: function(xhr) {
                    const error = JSON.parse(xhr.responseText);
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: error.message || 'Terjadi kesalahan saat menyimpan PO'
                    });
                }
            });
        }
    });
}

function getSupplierOption() {
    return new Promise((resolve, reject) => {
        $.ajax({
            type: 'get',
            url: base_url + 'pembelian/getSupplierList', // Endpoint untuk mendapatkan produk
            success: function (response) {
                // Buat opsi produk dari data yang diterima
                var options = '<option value="">Pilih Supplier</option>';
                response.forEach(function (supplier) {
                    options += `<option value="${supplier.id}">${supplier.supplier_name}</option>`;
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
