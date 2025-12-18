
var loc = window.location;
var base_url = loc.protocol + "//" + loc.hostname + (loc.port ? ":" + loc.port : "") + "/";


// Mendapatkan path dari URL
const path = window.location.pathname;

// Memisahkan path menjadi segmen-segmen berdasarkan '/'
const segments = path.split('/');

// Mengambil segmen ke-3 (indeks ke-2 karena indeks dimulai dari 0)
const masterId = segments[3]; // Ubah indeks sesuai kebutuhan



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
      return `<a href="javascript:void(0);" class="btn btn-success btn-sm addMD" id="${row[1]}" nama="${row[2]}">ADD</a>`; 
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


$('#tabel_serverside').on('click', '.addMD', function () {
      // Menampilkan SweetAlert dan menambahkan dynamic select option untuk customer
      let id = $(this).attr('id')
      Swal.fire({
          title: `Tambah Material Request untuk ${$(this).attr('nama')}`,
          html: `
          <form id="form_add_data">
              <div class="form-group">
                  <label for="quantity">Quantity</label>
                  <input type="text" class="form-control" id="quantity" aria-describedby="quantityHelp" placeholder="quantity">
              </div>
              <div class="form-group">
                  <label for="kode">Remarks</label>
                  <input type="text" class="form-control" id="remarks" aria-describedby="remarksHelp" placeholder="remarks">
              </div>
          </form>`,
          confirmButtonText: 'Confirm',
          focusConfirm: false,
          preConfirm: () => {
              const remarks = Swal.getPopup().querySelector('#remarks').value;
              const quantity = Swal.getPopup().querySelector('#quantity').value;
              if ( !remarks || !quantity) {
                  Swal.showValidationMessage('Silakan lengkapi data');
              }
              return {
                 remarks:remarks ,
                 quantity:quantity,
              };
          }
      }).then((result) => {
        $.ajax({
            type: "POST",
            url: base_url + '/pemusnahan/addMD',
            // Removed async: false
            data: {
                id_material: id,
                quantity: result.value.quantity,
                remarks: result.value.remarks,
                id_material_destruction: masterId
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
     
  });
    loadData()
     function loadData() {
            $.ajax({
                url: base_url+'/pemusnahan/datamd/'+masterId,
                type: 'post',
                dataType: 'json',
                success: function(response) {
                    // Mengosongkan tabel sebelum memuat data baru
                    var tableBody = $('#data-body');
                    tableBody.empty();
                    no = 1
                    
                    // Menampilkan data ke dalam tabel
                    response.forEach(function(item) {
                      if(item.status == 1){
                        button =    `DATA SUDAH DIPOSTING`;
                      }else{
                        button = `<button class="btn btn-success btn-sm updateList"  data-id="${item.id}">Edit</button>
                        <button class="btn btn-success btn-sm deleteList"  data-id="${item.id}">Delete</button>`;
                      }
                        var row = `<tr>
                            <td>${no++}</td>
                            <td>${item.code}</td>
                            <td>${item.material}</td>
                            <td>${item.jumlah}</td>
                            <td>${item.remarks}</td>
                            <td>
                            ${button}
                             
                            </td>   
                        </tr>`;
                        tableBody.append(row);
                    });
                },
                error: function(xhr, status, error) {
                    // Menampilkan pesan kesalahan jika terjadi error
                    console.error('Error: ' + status + ' - ' + error);
                    alert('Gagal memuat data.');
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
                      url: base_url + '/pemusnahan/deleteList/' + id,
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
        $('.posting').on('click',function(){
          const id = masterId;
          const status = 1;
          Swal.fire({
              title: 'Apakah Anda yakin?',
              text: `Data akan diposting!`,
              icon: 'info',
              showCancelButton: true,
              confirmButtonColor: '#3085d6',
              cancelButtonColor: '#d33',
              confirmButtonText: 'Ya, Posting!'
          }).then((result) => {
              if (result.isConfirmed) {
                  $.ajax({
                      type: "POST",
                      url: base_url + '/pemusnahan/posting/' + id,
                      data: {status:status},
                      success: function() {
                        window.location.reload();
                          Swal.fire('Sukses', 'Dokumen berhasil diposting.', 'success');
                      },
                      error: function() {
                          Swal.fire('Error', 'Gagal.', 'error');
                      }
                  });
              }
          });
        })
        $('.batalposting').on('click',function(){
          const id = masterId;
          const status = 0;
          Swal.fire({
              title: 'Apakah Anda yakin?',
              text: `Data akan dibatalkan posting!`,
              icon: 'info',
              showCancelButton: true,
              confirmButtonColor: '#3085d6',
              cancelButtonColor: '#d33',
              confirmButtonText: 'Ya, Batalkan Posting!'
          }).then((result) => {
              if (result.isConfirmed) {
                  $.ajax({
                      type: "POST",
                      url: base_url + '/pemusnahan/posting/' + id,
                      data: {status:status},
                      success: function() {
                        window.location.reload();
                          
                          Swal.fire('Sukses', 'Dokumen berhasil diposting.', 'success');
                      },
                      error: function() {
                          Swal.fire('Error', 'Gagal.', 'error');
                      }
                  });
              }
          });
        })
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
