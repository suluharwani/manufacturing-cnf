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
      "url" :base_url+"/pembelian/listdataPembelian" , // json datasource 
      "type": "post",  // method  , by default get
      // "async": false,
      "dataType": 'json',
      "data":{},
    },
    
    columns: [
    {},
    {mRender: function (data, type, row) {
        return formatDateTime(row[4])
    }},
   
    {mRender: function (data, type, row) {
        return row[7]
    }},
    {mRender: function (data, type, row) {
        return row[3]
    }},
    {mRender: function (data, type, row) {
      if (row[8] == 0) {
        status = `<span class="badge bg-warning text-dark">Draft</span>`
      }else{
        status = `<span class="badge bg-success text-dark">Diposting</span>`
      }
        return status
    }},
    {mRender: function (data, type, row) {
     return `<a href="${base_url}pembelian/form/${row[1]}" target="_blank" class="btn btn-success btn-sm showPurchaseOrder">Edit</a>
             <a href="javascript:void(0);" class="btn btn-danger btn-sm deleteInvoice" invoice = "${row[3]}" id="${row[1]}" >Delete</a>
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

function formatNumber(number) {
  return number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
}
function formatDateTime(datetime) {
  const date = new Date(datetime); // Mengubah datetime menjadi objek Date
  const year = date.getFullYear();
  const month = String(date.getMonth() + 1).padStart(2, '0'); // Menambahkan leading zero untuk bulan < 10
  const day = String(date.getDate()).padStart(2, '0'); // Menambahkan leading zero untuk tanggal < 10
  return `${day}-${month}-${year}`;
}

$('.tambahPembelian').click(function() {
    // Reset form di modal
    $('#addPembelianForm')[0].reset();

    // Ambil opsi material dan masukkan ke dropdown
    getSupplierOption().then(function(options) {
      $('#supplier').html(options); // Masukkan opsi ke dalam elemen select
      $('#tambahPembelianModal').modal('show'); // Tampilkan modal
    }).catch(function(error) {
      alert(error); // Tampilkan error jika gagal mengambil data
    });
  });

  // Menangani form submit di modal
  $('#addPembelianForm').submit(function(event) {
    event.preventDefault();

    // Ambil data dari form
    var supplier = $('#supplier').val();
    var invoice = $('#invoice').val();
    var tanggal_nota = $('#tanggal_nota').val();
    var pajak = $('#pajak').val();


    // Kirim data melalui AJAX ke server
    $.ajax({
      type: "POST",
      url: base_url + "pembelian/addInvoice", // URL untuk menambahkan material (ganti dengan URL yang sesuai)
      data: {
        supplier: supplier,
        invoice: invoice,
        tanggal_nota: tanggal_nota,
        pajak: pajak
      },
      success: function(response) {
        // Tampilkan pesan sukses jika berhasil menambahkan material
        if(response.status === 'success') {
          Swal.fire({
            title: "success!",
            text: "invoice Added!",
            icon: "success"
          });
          $('#tambahPembelianModal').modal('hide'); 
          $('#tabel_serverside').DataTable().ajax.reload();
        } else {
          alert('Error adding invoice');
        }
      },
      error: function() {
        alert('Error connecting to server');
      }
    });
  });

  function getSupplierOption() {
    return new Promise((resolve, reject) => {

      $.ajax({
        type: 'GET',
        url: base_url + 'pembelian/getSupplierList', // Endpoint untuk mendapatkan produk
        success: function(response) {
          // Buat opsi produk dari data yang diterima
          var options = '<option value="">Pilih Supplier</option>';
                response.forEach(function(supplier) {
                    options += `<option value="${supplier.id}">${supplier.supplier_name}</option>`;
                });

          // Resolving the promise dengan materialOptions setelah sukses
          resolve(options);
        },
        error: function(xhr) {
          // Menolak promise jika terjadi kesalahan
          reject('Terjadi kesalahan saat mengambil daftar produk');
        }
      });
    });
  } 

          $('#tabel_serverside').on('click','.deleteInvoice',function(){
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
                            url: base_url + '/pembelian/deleteinvoice', 
                            type: 'post',
                            data: { id: id },
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