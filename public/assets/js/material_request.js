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


  $('.tambahMaterialRequest').on('click', function () {
    // Menampilkan SweetAlert dan menambahkan dynamic select option untuk customer
    Swal.fire({
        title: `Tambah Material Request Form`,
        html: `
        <form id="form_add_data">
            <div class="form-group">
                <label for="kode">Kode</label>
                <input type="text" class="form-control" id="kode" aria-describedby="kodeHelp" placeholder="Kode">
                <button type="button" id="generateCode" class="btn btn-primary mt-2">Generate Kode</button>
            </div>
            <div class="form-group">
                <label for="proforma_invoice">Proforma Invoice</label>
                <select id="proforma_invoice" class="form-control">
                    <option value="">Proforma Invoice</option>
                </select>
            </div>
            <div class="form-group">
                <label for="department">Department</label>
                <select id="department" class="form-control">
                    <option value="">Pilih Department</option>
                </select>
            </div>
            <div class="form-group">
                <label for="kode">Remarks</label>
                <input type="text" class="form-control" id="remarks" aria-describedby="remarksHelp" placeholder="remarks">
            </div>
        </form>`,
        confirmButtonText: 'Confirm',
        focusConfirm: false,
        preConfirm: () => {
            const kode = Swal.getPopup().querySelector('#kode').value;
            const proforma_invoice = Swal.getPopup().querySelector('#proforma_invoice').value;
            const department = Swal.getPopup().querySelector('#department').value;
            const remarks = Swal.getPopup().querySelector('#remarks').value;
            if (!kode || !proforma_invoice || !department || !remarks) {
                Swal.showValidationMessage('Silakan lengkapi data');
            }
            return {department:department, kode: kode, proforma_invoice: proforma_invoice , remarks:remarks };
        }
    }).then((result) => {
        $.ajax({
            type: "POST",
            url: base_url + '/materialrequest/add',
            async: false,
            // 'kode','dept_id', 'id_pi', 'status', 'remarks',
            data: { kode: result.value.kode, dept_id: result.value.department,id_pi:result.value.proforma_invoice,status:0, remarks:result.value.remarks },
            success: function (data) {
                 $('#tabel_serverside').DataTable().ajax.reload();
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
    $('#generateCode').on('click', function () {
        const generatedCode = generateCode();
        $('#kode').val(generatedCode);  // Set value ke input kode
    });

    // Menambahkan opsi customer ke select dropdown

    getPIOption().then(PIoptions => {
      console.log(PIoptions);
      $('#proforma_invoice').html(PIoptions); // Isi select dengan opsi customer
  }).catch(error => {
      Swal.fire('Error', error, 'error');
  });
  getDepartOption().then(Departoptions => {
    console.log(Departoptions);
    $('#department').html(Departoptions); // Isi select dengan opsi customer
}).catch(error => {
    Swal.fire('Error', error, 'error');
});
});

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
  const code = `PR${year}${month}${day}${randomNumStr}`;
  
  return code;
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