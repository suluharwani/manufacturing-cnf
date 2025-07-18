var loc = window.location;
var base_url =
  loc.protocol + "//" + loc.hostname + (loc.port ? ":" + loc.port : "") + "/";

// Mendapatkan path dari URL
const path = window.location.pathname;

// Memisahkan path menjadi segmen-segmen berdasarkan '/'
const segments = path.split("/");

// Mengambil segmen ke-3 (indeks ke-2 karena indeks dimulai dari 0)
const masterId = segments[2]; // Ubah indeks sesuai kebutuhan

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
      url: base_url + "materialrequest/materialRequest",
      type: "post",
      dataType: "json",
      data: {},
    },
    columns: [
      {},
      {
        mRender: function (data, type, row) {
          return `${dateFormater(row[2])} `;
        },
      },
      {
        mRender: function (data, type, row) {
          return row[3];
        },
      },
      {
        mRender: function (data, type, row) {
          return row[4];
        },
      },
      {
        mRender: function (data, type, row) {
          return row[5];
        },
      },
      {
        mRender: function (data, type, row) {
            if(row[6] == 0){
                return "Pending"
            }else if(row[6] == 1){
                return "Approved"
            }else if(row[6] == 2){
                return "Rejected"
            }else if(row[6] == 3){
                return "Completed"
            }else if(row[6] == 4){
                return "Canceled"
            }else   {  
                return "Unknown"
            }

        },
      },
      {
        mRender: function (data, type, row) {
          return `
          <td><button class="btn btn-success btn-sm" onclick="window.open('/mr/${row[1]}', '_blank')" >Detail</button></td>
          `;
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
      $(".tabel_serverside-error").html("");
      $("#tabel_serverside").append(
        '<tbody class="tabel_serverside-error"><tr><th colspan="3">Data Tidak Ditemukan di Server</th></tr></tbody>'
      );
      $("#tabel_serverside_processing").css("display", "none");
    },
  });
});
function dateFormater(date) {
  let newDate = new Date(date);
  return newDate.toLocaleDateString("id-ID", {
    year: "numeric",
    month: "long",
    day: "numeric",
  });
}


$('.tambahMaterialRequest').on('click', function() {
    // Generate code first via AJAX
    $.ajax({
        url: base_url + '/materialrequest/generateCode',
        type: 'GET',
        async: false, // Synchronous to ensure we get the code first
        success: function(response) {
            showMaterialRequestPopup(response.code);
        },
        error: function(xhr) {
            Swal.fire('Error', 'Gagal generate kode', 'error');
        }
    });
});

function showMaterialRequestPopup(generatedCode) {
    Swal.fire({
        title: `Tambah Material Request`,
        html: `
        <form id="form_add_data">
            <div class="form-group">
                <label for="kode">Kode</label>
                <input type="text" class="form-control" id="kode" value="${generatedCode}" readonly>
            </div>
            <div class="form-group">
                <label for="proforma_invoice">Proforma Invoice</label>
                <select id="proforma_invoice" class="form-control" required>
                    <option value="">Pilih Proforma Invoice</option>
                </select>
            </div>
            <div class="form-group">
                <label for="department">Department</label>
                <select id="department" class="form-control" required>
                    <option value="">Pilih Department</option>
                </select>
            </div>
            <div class="form-group">
                <label for="remarks">Remarks</label>
                <input type="text" class="form-control" id="remarks" required placeholder="Masukkan remarks">
            </div>
        </form>`,
        confirmButtonText: 'Simpan',
        focusConfirm: false,
        didOpen: () => {
            // Load dropdown options
            getPIOption().then(options => {
                $('#proforma_invoice').html('<option value="">Pilih Proforma Invoice</option>' + options);
            }).catch(error => {
                Swal.fire('Error', error, 'error');
            });

            getDepartOption().then(options => {
                $('#department').html('<option value="">Pilih Department</option>' + options);
            }).catch(error => {
                Swal.fire('Error', error, 'error');
            });
        },
        preConfirm: () => {
            const kode = $('#kode').val();
            const proforma_invoice = $('#proforma_invoice').val();
            const department = $('#department').val();
            const remarks = $('#remarks').val();

            if (!kode) {
                Swal.showValidationMessage('Harap lengkapi semua field yang wajib diisi');
                return false;
            }

            return {
                kode: kode,
                dept_id: department,
                id_pi: proforma_invoice,
                remarks: remarks
            };
        }
    }).then((result) => {
        if (result.isConfirmed && result.value) {
            $.ajax({
                type: "POST",
                url: base_url + '/materialrequest/add',
                data: {
                    kode: result.value.kode,
                    dept_id: result.value.dept_id,
                    id_pi: result.value.id_pi,
                    status: 0,
                    remarks: result.value.remarks
                },
                success: function(response) {
                    $('#tabel_serverside').DataTable().ajax.reload();
                    Swal.fire({
                        position: 'center',
                        icon: 'success',
                        title: 'Material Request berhasil ditambahkan',
                        showConfirmButton: false,
                        timer: 1500
                    });
                },
                error: function(xhr) {
                    const error = JSON.parse(xhr.responseText);
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: error.message || 'Terjadi kesalahan saat menyimpan data'
                    });
                }
            });
        }
    });
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

function getWOOption() {
  return new Promise((resolve, reject) => {

    $.ajax({
      type: 'POST',
      url: base_url + '/department/department_list', // Endpoint untuk mendapatkan PI
      success: function(response) {
        // Buat opsi produk dari data yang diterima
        var PIoptions = '<option value="">Work Order</option>';
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