
var loc = window.location;
var base_url = loc.protocol + "//" + loc.hostname + (loc.port ? ":" + loc.port : "") + "/";


// Mendapatkan path dari URL
const path = window.location.pathname;

// Memisahkan path menjadi segmen-segmen berdasarkan '/'
const segments = path.split('/');

// Mengambil segmen ke-3 (indeks ke-2 karena indeks dimulai dari 0)
const masterId = segments[3]; // Ubah indeks sesuai kebutuhan
let statusDoc = $('#status').val();



$(document).ready(function () {
  tableWO()
  async function tableWO() {
    try {
      const data = await $.ajax({
        url: base_url + "scrap/WoAvailablelistdata/" + masterId,
        type: "GET",
        dataType: "json",
      });

      var tableBody = $("#materialTable tbody");
      tableBody.empty(); // Clear existing rows
      let no = 1;
      for (const item of data) {
      max_request = item.total_usage

        if(statusDoc == 1){
          button = `Document Posted`
        }else{
           button = (max_request <= 0) 
          ? 'Not available' 
          : `<button class="btn btn-primary add" max = "${max_request}" material_id = "${item.material_id}" name= "${item.material_name}" satuan = "${item.satuan} (${item.c_satuan})">Add</button>`;
        }
        

        var row = `<tr>
                    <td>${no++}</td>
                    <td class="text-start">${item.material_code}</td>
                    <td class="text-start">${item.material_name}</td>
                    <td class="text-start">${item.satuan} (${item.c_satuan})</td>
                    <td class="text-start">${item.quantity}</td>
                    <td class="text-start">${button}</td>
                    <td></td>
                </tr>`;
        tableBody.append(row);
      }
    } catch (error) {
      console.error("AJAX Error: ", error);
    }
  }


});



$('#materialTable tbody').on('click', '.add', function () {
      // Menampilkan SweetAlert dan menambahkan dynamic select option untuk customer
      let id = masterId
      let material_id = $(this).attr('material_id')
      let name = $(this).attr('name')
      let satuan = $(this).attr('satuan')
      let max = $(this).attr('max')

      Swal.fire({
          title: `Add Scrap from material ${name}`,
          html: `
          <form id="form_add_data">
              <div class="form-group">
                <label for="material">Material</label>
                <input type="text" value="${name}" class="form-control" id="material" aria-describedby="materialHelp" placeholder="material" disabled>
                <input type="text" value="${material_id}" class="form-control" id="material_id" aria-describedby="materialHelp" disabled hidden="true">
            </div>
            <div class="form-group">
                  <label for="satuan">Satuan</label>
                  <input type="text" class="form-control" value="${satuan}" id="satuan" aria-describedby="satuanHelp" placeholder="satuan" disabled>
              </div>
              <div class="form-group">
                  <label for="quantity">Quantity</label>
                  <input type="number" class="form-control" id="quantity" aria-describedby="quantityHelp" placeholder="quantity">
              </div>
              <div class="form-group">
                  <label for="remarks">Remark</label>
                  <input type="text" class="form-control" id="remarks" aria-describedby="remarksHelp" placeholder="remarks">
              </div>
          </form>`,
          confirmButtonText: 'Confirm',
          focusConfirm: false,
          preConfirm: () => {
              const material_id = Swal.getPopup().querySelector('#material_id').value;
              const quantity = Swal.getPopup().querySelector('#quantity').value;
              const remarks = Swal.getPopup().querySelector('#remarks').value;
              if ( !material_id || !quantity ) {
                  Swal.showValidationMessage('Silakan lengkapi data');
              }
              return {
                 material_id:material_id,
                 remarks:remarks ,
                 quantity:quantity
                }
          }
      }).then((result) => {
        $.ajax({
            type: "POST",
            url: base_url + '/scrap/addScrap',
            // Removed async: false
            data: {
              scrap_doc_id: id,
                material_id: result.value.material_id,
                reason: result.value.remarks,
                quantity: result.value.quantity
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
                url: base_url+'/scrap/materialScrapList/'+masterId,
                type: 'get',
                dataType: 'json',
                success: function(response) {
                    // Mengosongkan tabel sebelum memuat data baru
                    var tableBody = $('#data-body');
                    tableBody.empty();
                    no = 1
                    
                     
                    // Menampilkan data ke dalam tabel
                    response.forEach(function(item) {
                      if(statusDoc == 1){
                        button = `Document Posted`
                      }else{
                        button = `<button class="btn btn-danger btn-sm deleteList"  data-id="${item.id}">Delete</button>`
                      }
                        var row = `<tr>
                            <td>${no++}</td>
                            <td>${item.kode}</td>
                            <td>${item.name}</td>
                            <td>${item.satuan}</td>
                            <td>${item.quantity}</td>
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
                      url: base_url + '/scrap/deleteList/' + id,
                      success: function() {
                          loadData();
                          Swal.fire('Sukses', 'Scrap berhasil dihapus.', 'success');
                      },
                      error: function() {
                          Swal.fire('Error', 'Gagal menghapus scrap.', 'error');
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
                url: base_url + '/scrap/posting/' + id,
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
  $('.batalPosting').on('click',function(){
    const id = masterId;
    const status = 0;
    Swal.fire({
        title: 'Apakah Anda yakin?',
        text: `Posting akan dibatalkan!`,
        icon: 'info',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Ya, Posting!'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                type: "POST",
                url: base_url + '/scrap/posting/' + id,
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