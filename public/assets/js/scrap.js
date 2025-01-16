var loc = window.location;
var base_url =
  loc.protocol + "//" + loc.hostname + (loc.port ? ":" + loc.port : "") + "/";

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
            return `<a href="${base_url}scrap/form/${row[1]}" target="_blank" class="btn btn-success btn-sm detail" id="'+row[1]+'" >Detail</a>
                  <a href="javascript:void(0);" target="_blank" class="btn btn-danger btn-sm delete" id="'+row[1]+'" >Delete</a>`;

          }else{
            return `<a href="${base_url}scrap/form/${row[1]}" target="_blank" class="btn btn-success btn-sm detail" id="'+row[1]+'" >Detail</a>`
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
  // Menampilkan SweetAlert dan menambahkan dynamic select option untuk customer
  Swal.fire({
      title: `Add Scrap Document`,
      html: `
      <form id="form_add_data">
          <div class="form-group">
              <label for="kode">Kode</label>
              <input type="text" class="form-control" id="kode" aria-describedby="kodeHelp" placeholder="Kode">
              <button type="button" id="generateCode" class="btn btn-primary mt-2">Generate Kode</button>
          </div>
         
          <div class="form-group">
              <label for="work_order">work_order</label>
              <select id="work_order" class="form-control">
                  <option value="">Work Order</option>
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
          const work_order = Swal.getPopup().querySelector('#work_order').value;
          const department = Swal.getPopup().querySelector('#department').value;
          const remarks = Swal.getPopup().querySelector('#remarks').value;
          if (!kode || !proforma_invoice || !work_order || !department || !remarks) {
              Swal.showValidationMessage('Silakan lengkapi data');
          }
          return {department:department, kode: kode, proforma_invoice: proforma_invoice, work_order:work_order , remarks:remarks };
      }
  }).then((result) => {
      $.ajax({
          type: "POST",
          url: base_url + '/materialrequest/add',
          async: false,
          // 'kode','dept_id', 'id_pi', 'status', 'remarks',
          data: { kode: result.value.kode, dept_id: result.value.department,id_pi:result.value.proforma_invoice, work_order:result.value.work_order, status:0, remarks:result.value.remarks },
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
  getWOOption().then(options => {
      $('#work_order').html(options); // Isi select dengan opsi customer
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