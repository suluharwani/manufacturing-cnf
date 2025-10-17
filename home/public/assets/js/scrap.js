var loc = window.location;
var base_url =
  loc.protocol + "//" + loc.hostname + (loc.port ? ":" + loc.port : "") + "/";
function formatIndonesianDateTime(datetimeString) {
  const date = new Date(datetimeString);
  
  // Jika tanggal tidak valid
  if (isNaN(date.getTime())) {
    return 'Tanggal tidak valid';
  }

  const days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
  const months = [
    'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
    'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
  ];

  const dayName = days[date.getDay()];
  const day = date.getDate();
  const month = months[date.getMonth()];
  const year = date.getFullYear();
  


  return `${dayName}, ${day} ${month} ${year}`;
}
$(document).ready(function () {
  var dataTable = $("#tabel_serverside").DataTable({
    processing: true,
    oLanguage: {
      sLengthMenu: "Tampilkan _MENU_ data per halaman",
      sSearch: "Pencarian: ",
      sZeroRecords: "Maaf, tidak ada data yang ditemukan",
      sInfo: "Menampilkan _START_ s/d _END_ dari _TOTAL_ data",
      sInfoEmpty: "Menampilkan 0 s/d 0 dari 0 data",
      sInfoFiltered: "(di filter dari _MAX_ total data)",
      oPaginate: {
        sFirst: "<<",
        sLast: ">>",
        sPrevious: "<",
        sNext: ">",
      },
    },
    dom: "Bfrtip",
    buttons: ["csv"],
    order: [],
    ordering: true,
    info: true,
    serverSide: true,
    stateSave: true,
    scrollX: true,
    ajax: {
      url: base_url + "scrap/listdataScrap", // json datasource
      type: "post", // method  , by default get
      // "async": false,
      dataType: "json",
      data: {},
    },

    // $row[] = $lists->id;
    // $row[] = $lists->invoice_number;
    // $row[] = $lists->wo_code;
    // $row[] = $lists->code;

    columns: [
      {},
      {
        mRender: function (data, type, row) {
          return formatIndonesianDateTime(row[7]);
        },
      },
      {
        mRender: function (data, type, row) {
          return row[2];
        },
      },
      {
        mRender: function (data, type, row) {
          return row[3];
        },
      },
      {
        mRender: function (data, type, row) {
          return row[5];
        },
      },
      {
        mRender: function (data, type, row) {
          return row[4];
        },
      },
      {
        mRender: function (data, type, row) {
          if(row[6] == 0){
            return `Unposted`;

          }else{
            return `Posted`
          }
      },
      },
      {
        mRender: function (data, type, row) {
          if(row[6] == 0){
            return `<a href="${base_url}scrap/form/${row[1]}"  class="btn btn-success btn-sm detail" id="'+row[1]+'" >Detail</a>
                  <a href="javascript:void(0);"  class="btn btn-danger btn-sm delete" id="${row[1]}" >Delete</a>`;

          }else{
            return `<a href="${base_url}scrap/form/${row[1]}"  class="btn btn-success btn-sm detail" id="'+row[1]+'" >Detail</a>`
          }
      },
      },
    ],
    columnDefs: [
      {
        targets: [0],
        orderable: false,
      },
    ],

    error: function () {
      // error handling
      $(".tabel_serverside-error").html("");
      $("#tabel_serverside").append(
        '<tbody class="tabel_serverside-error"><tr><th colspan="3">Data Tidak Ditemukan di Server</th></tr></tbody>'
      );
      $("#tabel_serverside_processing").css("display", "none");
    },
  });
});

$('.add').on('click', function () {
    // Generate code first before showing the popup
    $.ajax({
        url: base_url + '/scrap/generateCode',
        type: 'GET',
        async: false, // Make synchronous to ensure we have the code before showing popup
        success: function(response) {
            // After getting the code, show the popup
            showAddPopup(response.code);
        },
        error: function(xhr) {
            Swal.fire('Error', 'Failed to generate code', 'error');
        }
    });
});

function showAddPopup(generatedCode) {
    Swal.fire({
        title: `Add Scrap Document`,
        html: `
        <form id="form_add_data">
            <div class="form-group">
                <label for="kode">Kode</label>
                <input type="text" class="form-control" id="kode" value="${generatedCode}" readonly>
            </div>
            <div class="form-group">
                <label for="department">Department</label>
                <select id="department" class="form-control" required>
                    <option value="">Select Department</option>
                </select>
            </div>
            <div class="form-group">
                <label for="work_order">Work Order</label>
                <select id="work_order" class="form-control" required>
                    <option value="">Select Work Order</option>
                </select>
            </div>
            <div class="form-group">
                <label for="document_bc_2_4">Document BC 2.4</label>
                <input type="text" class="form-control" id="document_bc_2_4" required placeholder="Enter BC 2.4 Document">
            </div>
            <div class="form-group">
                <label for="remarks">Remarks</label>
                <input type="text" class="form-control" id="remarks" required placeholder="Enter Remarks">
            </div>
        </form>`,
        confirmButtonText: 'Submit',
        focusConfirm: false,
        didOpen: () => {
            // Load departments using your existing function
            getDepartOption().then(Departoptions => {
                $('#department').html(Departoptions);
            }).catch(error => {
                Swal.fire('Error', error, 'error');
            });

            // Load work orders using your existing function
            getWOOption().then(options => {
                $('#work_order').html(options);
            }).catch(error => {
                Swal.fire('Error', error, 'error');
            });
        },
        preConfirm: () => {
            const kode = Swal.getPopup().querySelector('#kode').value;
            const work_order = Swal.getPopup().querySelector('#work_order').value;
            const department = Swal.getPopup().querySelector('#department').value;
            const document_bc_2_4 = Swal.getPopup().querySelector('#document_bc_2_4').value;
            const remarks = Swal.getPopup().querySelector('#remarks').value;
            
            if (!kode || !work_order || !department || !document_bc_2_4 || !remarks) {
                Swal.showValidationMessage('Please complete all fields');
                return false;
            }
            
            return { 
                code: kode,
                id_dept: department,
                id_wo: work_order,
                document_bc: document_bc_2_4,
                remarks: remarks 
            };
        }
    }).then((result) => {
        if (result.isConfirmed && result.value) {
            $.ajax({
                type: "POST",
                url: base_url + '/scrap/add',
                data: result.value,
                success: function (response) {
                    $('#tabel_serverside').DataTable().ajax.reload();
                    Swal.fire({
                        position: 'center',
                        icon: 'success',
                        title: 'Scrap document added successfully',
                        showConfirmButton: false,
                        timer: 1500
                    });
                },
                error: function (xhr) {
                    if (xhr.status === 500) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'No changes were made to the data'
                        });
                    } else {
                        let error = JSON.parse(xhr.responseText);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: error.message || 'An error occurred'
                        });
                    }
                }
            });
        }
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
function generateCode() {
  // Mendapatkan tanggal saat ini
  const today = new Date();
  
  // Format tanggal: yymmdd
  const year = today.getFullYear().toString().slice(-2); // Ambil 2 digit terakhir dari tahun
  const month = ('0' + (today.getMonth() + 1)).slice(-2); // Bulan dalam format 2 digit
  const day = ('0' + today.getDate()).slice(-2); // Hari dalam format 2 digit
  
  // Menghasilkan dua angka acak
  const randomNum = Math.floor(Math.random() * 100); // Angka acak antara 0 dan 99
  const randomNumStr = randomNum.toString().padStart(2, '0'); // Pastikan dua digit dengan menambahkan 0 di depan jika perlu
  
  // Gabungkan semuanya menjadi format PIXXXXXXXX
  const code = `SC${year}${month}${day}${randomNumStr}`;
  
  return code;
}
function getWOOption() {
  return new Promise((resolve, reject) => {

    $.ajax({
      type: 'POST',
      url: base_url + '/wo/woList', // Endpoint untuk mendapatkan PI
      success: function(response) {
        // Buat opsi produk dari data yang diterima
        var WOoptions = '<option value="">Work Order</option>';
              response.forEach(function(wo) {
                  WOoptions += `<option value="${wo.id}">${wo.kode}</option>`;
              });

        // Resolving the promise dengan materialWOOptions setelah sukses
        resolve(WOoptions);
      },
      error: function(xhr) {
        // Menolak promise jika terjadi kesalahan
        reject('Terjadi kesalahan saat mengambil daftar produk');
      }
    });
  });
}
// Tambahkan event handler untuk tombol delete
$(document).on('click', '.delete', function() {
    const id = $(this).attr('id');
    const row = $(this).closest('tr');
    
    Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: base_url + 'scrap/delete/' + id,
                type: 'DELETE',
                dataType: 'json',
                success: function(response) {
                    if(response.status === 'success') {
                        Swal.fire(
                            'Deleted!',
                            'Your scrap document has been deleted.',
                            'success'
                        );
                        // Reload datatable
                        $('#tabel_serverside').DataTable().ajax.reload();
                    } else {
                        Swal.fire(
                            'Error!',
                            response.message || 'Failed to delete scrap document',
                            'error'
                        );
                    }
                },
                error: function(xhr) {
                    let error = JSON.parse(xhr.responseText);
                    Swal.fire(
                        'Error!',
                        error.message || 'Something went wrong',
                        'error'
                    );
                }
            });
        }
    });
});