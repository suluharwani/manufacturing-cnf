
var loc = window.location;
var base_url = loc.protocol + "//" + loc.hostname + (loc.port ? ":" + loc.port : "") + "/";


// Mendapatkan path dari URL
const path = window.location.pathname;

// Memisahkan path menjadi segmen-segmen berdasarkan '/'
const segments = path.split('/');

// Mengambil segmen ke-3 (indeks ke-2 karena indeks dimulai dari 0)
const masterId = segments[2]; // Ubah indeks sesuai kebutuhan



$(document).ready(function () {
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
      "url" :base_url+"material/listdataMaterialJoin" , // json datasource 
      "type": "post",  // method  , by default get
      // "async": false,
      "dataType": 'json',
      "data":{},
    },
    
    columns: [
      {},
      {mRender: function (data, type, row) {
    //   return  row[1]+" "+row[2]+"</br>"+"<a href=mailto:"+row[3]+">"+row[3]+"</a>";
        return row[3]
      }},
      {mRender: function (data, type, row) {
        return row[2]
      }},
      {mRender: function (data, type, row) {
       return row[5]
     }},
     {mRender: function (data, type, row) {
       return row[4]; 
     }},
    {mRender: function (data, type, row) {
      return `(${row[6]}) ${row[7]}`; 
    }},

    {mRender: function (data, type, row) {
      return `<a href="javascript:void(0);" class="btn btn-success btn-sm addMR" id="${row[1]}" nama="${row[2]}">ADD</a>`; 
    }
  }
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
});


$('#tabel_serverside').on('click', '.addMR', function () {
      // Menampilkan SweetAlert dan menambahkan dynamic select option untuk customer
      let id = $(this).attr('id')
      Swal.fire({
          title: `Tambah Material Request untuk ${$(this).attr('nama')}`,
          html: `
          <form id="form_add_data">

              <div class="form-group">
                  <label for="department">Department</label>
                  <select id="department" class="form-control">
                      <option value="">Department</option>
                  </select>
              </div>
              <div class="form-group">
                <label for="proforma_invoice">Proforma Invoice</label>
                <select id="proforma_invoice" class="form-control">
                    <option value="">Proforma Invoice</option>
                </select>
            </div>
              <div class="form-group">
                  <label for="supplier">supplier</label>
                  <select id="supplier" class="form-control">
                      <option value="">Supplier</option>
                  </select>
              </div>
              <div class="form-group">
                  <label for="quantity">Quantity</label>
                  <input type="text" class="form-control" id="quantity" aria-describedby="quantityHelp" placeholder="quantity">
              </div>
              <div class="form-group">
                  <label for="price">Price</label>
                  <input type="text" class="form-control" id="price" aria-describedby="priceHelp" placeholder="price">
              </div>
              <div class="form-group">
                  <label for="kode">Remarks</label>
                  <input type="text" class="form-control" id="remarks" aria-describedby="remarksHelp" placeholder="remarks">
              </div>
          </form>`,
          confirmButtonText: 'Confirm',
          focusConfirm: false,
          preConfirm: () => {
              const department = Swal.getPopup().querySelector('#department').value;
              const supplier = Swal.getPopup().querySelector('#supplier').value;
              const remarks = Swal.getPopup().querySelector('#remarks').value;
              const quantity = Swal.getPopup().querySelector('#quantity').value;
              const price = Swal.getPopup().querySelector('#price').value;
              const proforma_invoice = Swal.getPopup().querySelector('#proforma_invoice').value;
              if ( !remarks || !quantity || !price) {
                  Swal.showValidationMessage('Silakan lengkapi data');
              }
              return {proforma_invoice:proforma_invoice,
                department:department,
                 supplier: supplier,
                 remarks:remarks ,
                 quantity:quantity,
                 price:price};
          }
      }).then((result) => {
        $.ajax({
            type: "POST",
            url: base_url + '/materialrequest/addMR',
            // Removed async: false
            data: {
                id_pi: result.value.proforma_invoice,
                id_mr: masterId,
                id_material: id,
                id_dept: result.value.department,
                id_sup: result.value.supplier,
                quantity: result.value.quantity,
                price: result.value.price,
                remarks: result.value.remarks
            },
            success: function (data) {
                console.log("Data loaded successfully");
                loadData(); // Ensure this does not cause a loop
                Swal.fire({
                    position: 'center',
                    icon: 'success',
                    title: `Material request successfully added.`,
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
    });
      // Menambahkan event listener pada tombol generate kode

  
      // Menambahkan opsi customer ke select dropdown
      getSupplierOption().then(options => {
          $('#supplier').html(options); // Isi select dengan opsi customer
      }).catch(error => {
          Swal.fire('Error', error, 'error');
      });
      getPIOption().then(PIoptions => {
        $('#proforma_invoice').html(PIoptions); // Isi select dengan opsi customer
    }).catch(error => {
        Swal.fire('Error', error, 'error');
    });
    getDepartOption().then(Departoptions => {
      $('#department').html(Departoptions); // Isi select dengan opsi customer
  }).catch(error => {
      Swal.fire('Error', error, 'error');
  });
  });
    loadData()
     function loadData() {
            $.ajax({
                url: base_url+'/materialrequest/datamr/'+masterId,
                type: 'post',
                dataType: 'json',
                success: function(response) {
                    // Mengosongkan tabel sebelum memuat data baru
                    var tableBody = $('#data-body');
                    tableBody.empty();
                    no = 1
                    // Menampilkan data ke dalam tabel
                    response.forEach(function(item) {
                        var row = `<tr>
                            <td>${no++}</td>
                            <td>${item.code}</td>
                            <td>${item.material}</td>
                            <td>${item.pi}</td>
                            <td>${item.supplier}</td>
                            <td>${item.dep}</td>
                            <td>${item.quantity}</td>
                            <td>${item.price}</td>
                            <td>${parseFloat(item.price*item.quantity)}</td>
                           
                            <td>
                            <button class="btn btn-success btn-sm updateList"  data-id="${item.id}">Edit</button>
                            <button class="btn btn-success btn-sm deleteList"  data-id="${item.id}">Delete</button>
                             
                            </td>   
                        </tr>`;
                        tableBody.append(row);
                    });
                },
                error: function(xhr, status, error) {
                    // Menampilkan pesan kesalahan jika terjadi error
                    console.error('Error: ' + status + ' - ' + error);
                    alert('Gagal memuat data penggajian.');
                }
            });
        }
        $('#data-body').on('click', '.deleteList', function () {
          const id = $(this).data('id');
          Swal.fire({
              title: 'Apakah Anda yakin?',
              text: `Data akan dihapus!`,
              icon: 'warning',
              showCancelButton: true,
              confirmButtonColor: '#3085d6',
              cancelButtonColor: '#d33',
              confirmButtonText: 'Ya, hapus!'
          }).then((result) => {
              if (result.isConfirmed) {
                  $.ajax({
                      type: "POST",
                      url: base_url + '/materialrequest/deleteList/' + id,
                      success: function() {
                          loadData();
                          Swal.fire('Sukses', 'Tunjangan berhasil dihapus.', 'success');
                      },
                      error: function() {
                          Swal.fire('Error', 'Gagal menghapus tunjangan.', 'error');
                      }
                  });
              }
          });
        })
        // Memuat data saat halaman selesai dimuat

function formatRupiah(amount) {
    // Check if amount is already a string
    if (typeof amount === 'string') {
        return amount;
    }

    // Otherwise, format it as currency
    return new Intl.NumberFormat('id-ID', { 
        style: 'currency', 
        currency: 'IDR', 
        minimumFractionDigits: 0,
        maximumFractionDigits: 0
    }).format(amount);
}
function formatDate(datetime) {
    // Membuat objek tanggal dari input datetime
    const date = new Date(datetime);

    // Mengambil tahun, bulan, dan hari
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, '0'); // Tambahkan 1 ke bulan karena indeks dimulai dari 0
    const day = String(date.getDate()).padStart(2, '0');

    // Menggabungkan menjadi format 'YYYY-MM-DD'
    return `${year}-${month}-${day}`;
}
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
  function getDepartOption() {
    return new Promise((resolve, reject) => {
  
      $.ajax({
        type: 'POST',
        url: base_url + '/department/department_list', // Endpoint untuk mendapatkan PI
        success: function(response) {
          // Buat opsi produk dari data yang diterima
          var PIoptions = '<option value="">Department</option>';
                response.forEach(function(pi) {
                    PIoptions += `<option value="${pi.id}">${pi.name}</option>`;
                });
  
          // Resolving the promise dengan materialPIOptions setelah sukses
          resolve(PIoptions);
        },
        error: function(xhr) {
          // Menolak promise jika terjadi kesalahan
          reject('Terjadi kesalahan saat mengambil daftar produk');
        }
      });
    });
  }
  function getPIOption() {
    return new Promise((resolve, reject) => {
  
      $.ajax({
        type: 'POST',
        url: base_url + '/proformainvoice/get_list_json', // Endpoint untuk mendapatkan PI
        success: function(response) {
          // Buat opsi produk dari data yang diterima
          var PIoptions = '<option value="">Pilih Proforma Invoice</option>';
                response.forEach(function(pi) {
                    PIoptions += `<option value="${pi.id}">${pi.invoice_number}</option>`;
                });
  
          // Resolving the promise dengan materialPIOptions setelah sukses
          resolve(PIoptions);
        },
        error: function(xhr) {
          // Menolak promise jika terjadi kesalahan
          reject('Terjadi kesalahan saat mengambil daftar produk');
        }
      });
    });
  }