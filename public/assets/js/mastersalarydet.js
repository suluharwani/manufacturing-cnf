
var loc = window.location;
var base_url = loc.protocol + "//" + loc.hostname + (loc.port ? ":" + loc.port : "") + "/";


// Mendapatkan path dari URL
const path = window.location.pathname;

// Memisahkan path menjadi segmen-segmen berdasarkan '/'
const segments = path.split('/');

// Mengambil segmen ke-3 (indeks ke-2 karena indeks dimulai dari 0)
const masterId = segments[2]; // Ubah indeks sesuai kebutuhan



$(document).ready(function () {


  var dataTable = $('#tabel_serverside').DataTable({
    "processing": true,
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
    "buttons": ['csv'],
    "order": [],
    "ordering": true,
    "info": true,
    "serverSide": true,
    "stateSave": true,
    "scrollX": true,
    "ajax": {
      "url": base_url + "employeeData",
      "type": "post",
      "dataType": 'json',
      "data": {},
    },
    columns: [
      {},
      {
        mRender: function(data, type, row) {
          return `${row[3]} `;
        }
      },
      {
        mRender: function(data, type, row) {
          return row[2];
        }
      },
      {
        mRender: function(data, type, row) {
          return row[4];
        }
      },
      {
        mRender: function(data, type, row) {
          return `
          <td><button class="btn btn-success btn-sm add-to-payroll" data-id="${row[1]}" data-name="${row[2]}">Add</button></td>
          `;
        }
      },
      ],
    "columnDefs": [{
      "targets": [0],
      "orderable": false
    }],
    error: function() {
      $(".tabel_serverside-error").html("");
      $("#tabel_serverside").append('<tbody class="tabel_serverside-error"><tr><th colspan="3">Data Tidak Ditemukan di Server</th></tr></tbody>');
      $("#tabel_serverside_processing").css("display", "none");
    }
  });

    // Menambahkan pegawai ke payroll
    $(document).on('click', '.add-to-payroll', function () {
        let employeeId = $(this).data('id');
        $.ajax({
        type : "POST",
        url  : base_url+'/MasterPenggajianDetailController/addEmployeeToPayroll',
        async : false,
        // dataType : "JSON",
        data : {employeeId:employeeId,masterId:masterId},
        success: function(data){
           loadPayrollData()
          Swal.fire({
            position: 'center',
            icon: 'success',
            title: `berhasil ditambahkan.`,
            showConfirmButton: false,
            timer: 1500
          })
        },
        error: function(xhr){
          let d = JSON.parse(xhr.responseText);
          Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: `${d.message}`,
            footer: '<a href="">Why do I have this issue?</a>'
          })
        }
      });

    });

     function loadPayrollData() {
            $.ajax({
                url: base_url+'/MasterPenggajianDetailController/dataEmployeeMaster/'+masterId,
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    // Mengosongkan tabel sebelum memuat data baru
                    var tableBody = $('#data-body');
                    tableBody.empty();

                    // Menampilkan data ke dalam tabel
                    response.forEach(function(item) {
                        var row = `<tr>
                            <td>${item.karyawan_id}</td>
                            <td>${item.pegawai_nama}</td>
                            <td>${item.pegawai_pin}</td>
                            <td>${item.kode_penggajian}</td>
                            <td>${formatDate(item.tanggal_awal_penggajian)}</td>
                            <td>${formatDate(item.tanggal_akhir_penggajian)}</td>
                            <td>${formatRupiah(item.total_salary)}</td>
                            <td>${item.total_work_Hours}</td>
                            <td>${item.total_overtime1_Hours}</td>
                            <td>${item.total_overtime2_Hours}</td>
                            <td>${item.total_overtime3_Hours}</td>
                            <td>${item.sunday_work_Hours}</td>
                            <td><button class="btn btn-success btn-sm add-to-payroll" data-id="${item.karyawan_id}">Attendance</button><button class="btn btn-success btn-sm add-to-payroll" data-id="${item.karyawan_id}">Print</button> <button class="btn btn-success btn-sm add-to-payroll" data-id="${item.karyawan_id}">Delete</button> </td>
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

        // Memuat data saat halaman selesai dimuat
        loadPayrollData();
function formatRupiah(amount) {
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

});
