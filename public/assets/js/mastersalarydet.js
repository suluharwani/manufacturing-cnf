
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
                    no = 1
                    // Menampilkan data ke dalam tabel
                    response.forEach(function(item) {
                        var row = `<tr>
                            <td>${no++}</td>
                            <td>${item.karyawan_id}</td>
                            <td>${item.pegawai_nama}</td>
                            <td>${item.pegawai_pin}</td>
                            <td>${item.kode_penggajian}</td>
                            <td>${formatDate(item.tanggal_awal_penggajian)}</td>
                            <td>${formatDate(item.tanggal_akhir_penggajian)}</td>
                           
                            <td><button class="btn btn-success btn-sm showPresensi" idKaryawan = "${item.karyawan_id}" pinKaryawan="${item.pegawai_pin}" tglAwal = "${formatDate(item.tanggal_awal_penggajian)}" tglAkhir= "${formatDate(item.tanggal_akhir_penggajian)}" data-id="${item.karyawan_id}" nama="${item.pegawai_nama}">Attendance</button>
                                <a href="javascript:void(0);" class="btn btn-primary btn-sm varTunjangan"  name = "${item.pegawai_nama}" id="${item.karyawan_id}" pin="${item.pegawai_pin}">Tunjangan</a>
                                <a href="javascript:void(0);" class="btn btn-warning btn-sm varPotongan" name = "${item.pegawai_nama}" id="${item.karyawan_id}" pin="${item.pegawai_pin}">Potongan</a>
                                <button class="btn btn-default btn-sm print-slip" data-id="${item.karyawan_id}">Print Slip</button>
                                <button class="btn btn-default btn-sm print-presensi" namaKaryawan = "${item.pegawai_nama}"  idKaryawan = "${item.karyawan_id}" pinKaryawan="${item.pegawai_pin}" tglAwal = "${formatDate(item.tanggal_awal_penggajian)}" tglAkhir= "${formatDate(item.tanggal_akhir_penggajian)}" data-id="${item.karyawan_id}" nama="${item.pegawai_nama}">Print Attendance</button>
                                <button class="btn btn-danger btn-sm delete-from-payroll" data-id="${item.karyawan_id}">Delete</button> </td>
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
  $('#data-body').on('click', '.showPresensi', function() {
    let pin = $(this).attr('pinKaryawan');
    let id = $(this).attr('idKaryawan');
    let startDate = $(this).attr('tglAwal');
    let endDate = $(this).attr('tglAkhir');


    // $('#dateSelectionModal').modal('show');

    // $('#fetchPresensi').off('click').on('click', function() {
    //   let startDate = $('#startDate').val();
    //   let endDate = $('#endDate').val();

    //   if (!startDate || !endDate) {
    //     alert("Silakan pilih tanggal awal dan akhir.");
    //     return;
    //   }

      refreshPopupContent(pin, id, startDate,endDate)

  });



  function refreshPopupContent(pin, id, startDate, endDate) {
    fetchEmployeeNameByPin(pin)
  $('#popupContent').empty();

    // AJAX request to fetch attendance data
  $.ajax({
    url: base_url + 'user/getPresensi',
    type: 'POST',
    data: { pin: pin, id: id, startDate: startDate, endDate: endDate },
    dataType: 'json',
    success: function(response) {
      const data = response.data;

            // Check if response data is an array
      if (!Array.isArray(data)) {
        alert("Unexpected response format");
        return;
      }

            // Fetch work day settings using AJAX
      $.ajax({
        url: base_url + 'user/getDataWorkDay',
        type: 'POST',
        dataType: 'json',
        success: function(workDays) {
          const groupedData = groupAttendanceData(data);

          let html = generateAttendanceTable(groupedData, workDays, id, startDate, endDate);

          $('#popupContent').html(html);
          $('#popupModal').modal('show');
          $('#dateSelectionModal').modal('hide');
        },
        error: function() {
          alert("Error fetching work day settings.");
        }
      });
    },
    error: function() {
      alert("Error fetching data.");
    }
  });
}

function groupAttendanceData(data) {
  const groupedData = {};
  $.each(data, function(index, item) {
        const dateKey = item.scan_date.split(' ')[0]; // Get the date part only
        if (!groupedData[dateKey]) {
          groupedData[dateKey] = [];
        }
        groupedData[dateKey].push(item);
      });
  return groupedData;
}


function generateAttendanceTable(groupedData, workDays, id, startDate, endDate) {
    let html = `
    <div class="table-responsive" style="max-height: 350px; overflow-y: auto;">
    <table class="table table-striped table-bordered">
    <thead>
    <tr>
    <th>Scan Date</th>
    <th>Jam Masuk</th>
    <th>Jam Keluar</th>
    <th>Total Jam Kerja</th>
    <th>Jam Normal</th>
    <th>Jam Lembur 1</th>
    <th>Jam Lembur 2</th>
    <th>Jam Lembur 3</th>
    <th>Revisi Jam</th>
    </tr>
    </thead>
    <tbody>`;

    let totalDurationMinutes = 0;
    let workDayCount = 0;
    let totalNormalWorkMinutes = 0;
    let totalOvertime1Minutes = 0;
    let totalOvertime2Minutes = 0;
    let totalOvertime3Minutes = 0;

    // Process each date's entries
    $.each(groupedData, function (date, entries) {
        const { inTime: originalInTime, outTime: originalOutTime } = getInOutTimes(entries);
        let inTime = new Date(originalInTime);
        let outTime = new Date(originalOutTime);
        const dayOfWeek = getDayName(date);
        const workDaySetting = workDays.find(day => day.day === dayOfWeek);

        if (inTime && outTime) {
            const duration = (outTime - inTime) / (1000 * 60); // Duration in minutes
            totalDurationMinutes += duration;

            // Format times and durations
            const formattedInTime = formatTime(inTime);
            const formattedOutTime = formatTime(outTime);
            const { totalHours, totalMinutes } = formatDuration(duration);
            workDayCount++;

            // Calculate normal and overtime minutes
            let { normalWorkMinutes, overtimeMinutes1, overtimeMinutes2, overtimeMinutes3 } = calculateWorkMinutes(inTime, outTime, workDaySetting, date);
            totalNormalWorkMinutes += normalWorkMinutes;

            // Set overtimeMinutes1 to 0 if it's less than 75 minutes
            if (overtimeMinutes1 < 75) {
                overtimeMinutes1 = 0;
            }
            totalOvertime1Minutes += overtimeMinutes1;

            // Bulatkan lembur level 2 dan 3 ke bawah per 15 menit
            const roundedOvertime2 = Math.floor(overtimeMinutes2 / 15) * 15;
            const roundedOvertime3 = Math.floor(overtimeMinutes3 / 15) * 15;
            totalOvertime2Minutes += roundedOvertime2;
            totalOvertime3Minutes += roundedOvertime3;

            // Display original and rounded overtime for level 2 and 3
            const overtime2Display = `${Math.floor(overtimeMinutes2 / 60)} jam ${Math.floor(overtimeMinutes2) % 60} menit (<span style="color: green;">Diakui: ${Math.floor(roundedOvertime2 / 60)} jam ${roundedOvertime2 % 60} menit</span>)`;
            const overtime3Display = `${Math.floor(overtimeMinutes3 / 60)} jam ${Math.floor(overtimeMinutes3) % 60} menit (<span style="color: green;">Diakui: ${Math.floor(roundedOvertime3 / 60)} jam ${roundedOvertime3 % 60} menit</span>)`;

            // Apply "Tidak Valid" label if overtime1 is less than 75 minutes and greater than 0
            const overtime1Display = (overtimeMinutes1 === 0 && overtimeMinutes1 < 75)
                ? `<span style="color: red;">(${Math.floor(overtimeMinutes1 / 60)} jam ${Math.floor(overtimeMinutes1 % 60)})Tidak Valid</span>`
                : `${Math.floor(overtimeMinutes1 / 60)} jam ${Math.floor(overtimeMinutes1 % 60)} menit`;

            // Apply red background if totalHours == 0
            const rowClass = totalHours <= 0 ? 'class="bg-warning"' : ''; // Assign class if totalHours is 0

            html += `
            <tr ${rowClass}>
            <td>${date} - ${dayOfWeek}</td>
            <td>${formattedInTime}</td>
            <td>${formattedOutTime}</td>
            <td>${totalHours} jam ${totalMinutes} menit</td>
            <td>${Math.floor(normalWorkMinutes / 60)} jam ${Math.floor(normalWorkMinutes % 60)} menit</td>
            <td>${overtime1Display}</td>
            <td>${overtime2Display}</td>
            <td>${overtime3Display}</td>
            <td>
            <a href="javascript:void(0);" class="btn btn-success btn-sm addAttendance" 
            id="${id}" startDate="${startDate}" endDate="${endDate}" pin="${entries[0].pin}" date="${date}">
            Tambah Data
            </a>
            </td>
            </tr>`;
        }
    });

    // Format total normal work minutes for display
    const totalNormalHours = Math.floor(totalNormalWorkMinutes / 60);
    const totalNormalMinutes = Math.floor(totalNormalWorkMinutes % 60);

    html += `</tbody></table></div>`;
    html += `
    <table class="table table-bordered">
    <thead>
    <tr>
    <th>Keterangan</th>
    <th>Jumlah</th>
    </tr>
    </thead>
    <tbody>
    <tr>
    <td>Periode</td>
    <td>${formatDateWithDayIndonesian(startDate)} - ${formatDateWithDayIndonesian(endDate)}</td>
    </tr>
    <tr>
    <td>Hari Kerja</td>
    <td>${workDayCount} hari</td>
    </tr>
    <tr>
    <td>Total Durasi Kerja</td>
    <td>${Math.floor(totalDurationMinutes / 60)} jam ${Math.floor(totalDurationMinutes % 60)} menit</td>
    </tr>
    <tr>
    <td>Total Kerja (Setelah Istirahat)</td>
    <td>${totalNormalHours} jam ${totalNormalMinutes} menit</td>
    </tr>
    <tr>
    <td>Total Lembur 1</td>
    <td>${Math.floor(totalOvertime1Minutes / 60)} jam ${Math.floor(totalOvertime1Minutes % 60)} menit</td>
    </tr>
    <tr>
    <td>Total Lembur 2</td>
    <td>${Math.floor(totalOvertime2Minutes / 60)} jam ${Math.floor(totalOvertime2Minutes % 60)} menit</td>
    </tr>
    <tr>
    <td>Total Lembur 3</td>
    <td>${Math.floor(totalOvertime3Minutes / 60)} jam ${Math.floor(totalOvertime3Minutes % 60)} menit</td>
    </tr>
    </tbody>
    </table>`;

    return html;
}


function getInOutTimes(entries) {
    let inTime = new Date(entries[0].scan_date); // original inTime
    let outTime = new Date(entries[entries.length - 1].scan_date); // original outTime

    // Check if outTime is smaller than inTime, if so, swap them
    if (outTime < inTime) {
        [inTime, outTime] = [outTime, inTime]; // swap inTime and outTime
    }

    return { inTime, outTime };
}
function getDayName(dateString) {
        const date = new Date(dateString);
        const options = { weekday: 'long' };
  return date.toLocaleDateString('id-ID', options); // Output in Indonesian
}
  function formatTime(date) {
    return date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
  }
  function formatDuration(duration) {
    return {
      totalHours: Math.floor(duration / 60),
      totalMinutes: Math.floor(duration % 60)
    };
  }
function calculateWorkMinutes(inTime, outTime, workDaySetting, date) {
    let normalWorkMinutes = 0;
    let overtimeMinutes1 = 0;
    let overtimeMinutes2 = 0;
    let overtimeMinutes3 = 0;

    if (workDaySetting) {
        const workStart = new Date(`${date} ${workDaySetting.work_start}`);
        const workEnd = new Date(`${date} ${workDaySetting.work_end}`);
        const overtimeStart1 = new Date(`${date} ${workDaySetting.overtime_start_1}`);
        const overtimeEnd1 = new Date(`${date} ${workDaySetting.overtime_end_1}`);
        const overtimeStart2 = new Date(`${date} ${workDaySetting.overtime_start_2}`);
        const overtimeEnd2 = new Date(`${date} ${workDaySetting.overtime_end_2}`);
        const overtimeStart3 = new Date(`${date} ${workDaySetting.overtime_start_3}`);
        const overtimeEnd3 = new Date(`${date} ${workDaySetting.overtime_end_3}`);
        const workBreakStart = new Date(`${date} ${workDaySetting.work_break}`);
        const workBreakEnd = new Date(`${date} ${workDaySetting.work_break_end}`);

        // Adjust inTime if needed
        if (inTime < workStart) {
            inTime = workStart; // Set inTime to workStart if it's earlier
        }

        // Calculate normal work minutes without break time
        if (outTime < workBreakStart) {
            // Jika waktu keluar lebih awal dari waktu mulai istirahat, hitung dari inTime hingga outTime
            normalWorkMinutes = (outTime - inTime) / (1000 * 60);
        } else if (outTime >= workBreakStart && outTime <= workBreakEnd) {
            // Jika waktu keluar di tengah waktu istirahat, hitung dari inTime hingga workBreakStart
            normalWorkMinutes = (workBreakStart - inTime) / (1000 * 60);
        } else if (outTime > workEnd) {
            // Jika waktu keluar lebih lambat dari workEnd, hitung dari inTime hingga workEnd
            normalWorkMinutes = (workEnd - inTime) / (1000 * 60);
        } else {
            // Jika waktu keluar berada dalam waktu kerja normal
            normalWorkMinutes = (outTime - inTime) / (1000 * 60);
        }

        // Subtract break time if outTime is later than the break start
        if (inTime < workBreakEnd && outTime > workBreakEnd) {
            const breakStartTime = inTime < workBreakStart ? workBreakStart : inTime; // Use the later time
            const breakDuration = (workBreakEnd - breakStartTime) / (1000 * 60); // Duration of break
            normalWorkMinutes -= breakDuration; // Subtract break duration from normal work minutes
        }

        // Calculate overtime levels
        if (outTime > overtimeStart1) {
            overtimeMinutes1 = (Math.min(outTime, overtimeEnd1) - overtimeStart1) / (1000 * 60); // Convert to minutes
        }
        if (outTime > overtimeStart2) {
            overtimeMinutes2 = (Math.min(outTime, overtimeEnd2) - overtimeStart2) / (1000 * 60); // Convert to minutes
        }
        if (outTime > overtimeStart3) {
            overtimeMinutes3 = (Math.min(outTime, overtimeEnd3) - overtimeStart3) / (1000 * 60); // Convert to minutes
        }

        // Prevent negative values
        normalWorkMinutes = Math.max(normalWorkMinutes, 0);
        overtimeMinutes1 = Math.max(overtimeMinutes1, 0);
        overtimeMinutes2 = Math.max(overtimeMinutes2, 0);
        overtimeMinutes3 = Math.max(overtimeMinutes3, 0);
    }

    return { normalWorkMinutes, overtimeMinutes1, overtimeMinutes2, overtimeMinutes3 };
}

function formatDateWithDayIndonesian(dateString) {
  const dayName = getDayName(dateString);
  const formattedDate = formatDateIndonesian(dateString);

  return `${dayName}, ${formattedDate}`;
}
function formatDateIndonesian(dateString) {
  const date = new Date(dateString);
    const day = ('0' + date.getDate()).slice(-2); // Two-digit day
    const month = ('0' + (date.getMonth() + 1)).slice(-2); // Two-digit month
    const year = date.getFullYear();
    
    return `${day}/${month}/${year}`;
  }

  $('#popupContent').on('click','.addAttendance',function(){
  let id = $(this).attr('id');
  let pin = $(this).attr('pin');
  let date = $(this).attr('date');
  let startDate = $(this).attr('startDate');
  let endDate = $(this).attr('endDate');

  Swal.fire({
    title: `Edit `,
    html: `<form id="form_edit_data">
    <div class="form-group">
    <label for="time">Jam Kerja Masuk/Pulang</label>
    <input type="time" class="form-control" id="time" aria-describedby="time"  >
    </div>
    </form>
    `,
    confirmButtonText: 'Confirm',
    focusConfirm: false,
    preConfirm: () => {
      const time = Swal.getPopup().querySelector('#time').value
      if (!time) {
        Swal.showValidationMessage('Silakan lengkapi data')
      }
      
      return {time:time}
    }
  }).then((result) => {
    params = {pin:pin,date:date,time:result.value.time}

    $.ajax({
      type : "POST",
      url  : base_url+'/user/updateAttendance',
      async : false,
      // dataType : "JSON",
      data : {params},
      success: function(data){

        refreshPopupContent(pin, id,startDate, endDate); 
        Swal.fire({
          position: 'center',
          icon: 'success',
          title: `Jam kerja berhasil ditambahkan.`,
          showConfirmButton: false,
          timer: 2500
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

  })
}); 
    function fetchEmployeeNameByPin(pin) {
    $.ajax({
      url: base_url + 'user/getEmployeeNameByPin', 
      type: 'POST',
      data: { pin: pin },
      dataType: 'json',
      success: function(response) {
        if (response.success) {
          $('#employeeName').text(response.name); 
        } else {
          console.error('Error: ', response.message);
        }
      },
      error: function(xhr, status, error) {
        console.error('AJAX Error: ', error);
      }
    });
  }
  $(document).on('click', '.delete-from-payroll', function () {
    let employeeId = $(this).data('id');

    // Tampilkan dialog konfirmasi
    Swal.fire({
        title: 'Apakah Anda yakin?',
        text: "Data ini akan dihapus secara permanen!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, hapus!'
    }).then((result) => {
        if (result.isConfirmed) {
            // Kirim request AJAX untuk delete
            $.ajax({
                type: "POST",
                url: base_url + '/MasterPenggajianDetailController/deleteEmployeeFromPayroll',
                data: { employeeId: employeeId, masterId: masterId },
                success: function(response) {
                    loadPayrollData()
                    Swal.fire({
                        icon: 'success',
                        title: 'Dihapus!',
                        text: 'Data berhasil dihapus.',
                        showConfirmButton: false,
                        timer: 1500
                    });
                },
                error: function(xhr) {
                    let d = JSON.parse(xhr.responseText);
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: `${d.message}`,
                        footer: '<a href="">Why do I have this issue?</a>'
                    });
                }
            });
        }
    });
});

  // Fungsi untuk mengenerate HTML slip gaji
// Fungsi untuk mengenerate HTML slip gaji
function generateSalarySlipHTML(employeeData) {
    let pegawai = employeeData.pegawai;
    let deductionList = '<tr><td><strong>Nama</strong></td><td><strong>Amount (Rp)</strong></td></tr>';
    
    $.each(employeeData.deduction, function(index, deduction) {
        deductionList += `<tr><td>${deduction.Nama}</td><td>${numberFormat(deduction.amount)}</td></tr>`;
    });

    let allowanceList = '<tr><td><strong>Nama</strong></td><td><strong>Amount (Rp)</strong></td></tr>';
    
    $.each(employeeData.allowance, function(index, allowance) {
        allowanceList += `<tr><td>${allowance.Nama}</td><td>${numberFormat(allowance.amount)}</td></tr>`;
    });

    const slipHTML = `
        <div class="salary-slip">
            <h1>Slip Gaji Karyawan</h1>
            <hr>
            <h3>Operator</h3>
            <p><strong>Nama Karyawan:</strong> ${pegawai.pemilik_rekening}</p>
            <p><strong>Nama Alias:</strong> ${employeeData.pegawai_nama}</p>
            <p><strong>ID Karyawan:</strong> ${employeeData.karyawan_id}</p>
            <p><strong>Rekening:</strong> ${pegawai.bank} - ${pegawai.bank_account}</p>
            <p><strong>Periode Gaji:</strong> ${formatDate(employeeData.tanggal_awal_penggajian)} - ${formatDate(employeeData.tanggal_akhir_penggajian)}</p>
            
            <h3>Rincian Gaji</h3>
            <table class="main-table">
                <tr><td><strong>Nama</strong></td><td><strong>Amount (Rp)</strong></td></tr>
                <tr><td>Gaji Harian Senin-Jumat</td><td>${employeeData.salary_slip_details.basic_salary}</td></tr>
                <tr><td>Gaji Lembur 16.45-18.00</td><td>${employeeData.salary_slip_details.overtime1_salary}</td></tr>
                <tr><td>Gaji Lembur 18.30-20.00</td><td>${employeeData.salary_slip_details.overtime2_salary}</td></tr>
                <tr><td>Gaji Lembur >20.30</td><td>${employeeData.salary_slip_details.overtime3_salary}</td></tr>
                <tr><td>Gaji Sabtu</td><td>${employeeData.salary_slip_details.saturday_salary}</td></tr>
                <tr><td>Gaji Minggu</td><td>${employeeData.salary_slip_details.sunday_salary}</td></tr>
                <tr><td>Gaji Kotor</td><td>${employeeData.salary_slip_details.gross_salary}</td></tr>
                <tr><td>Tunjangan</td><td>${employeeData.salary_slip_details.allowances}</td></tr>
                <tr><td>Potongan</td><td>${employeeData.salary_slip_details.deductions}</td></tr>
                <tr><td><strong>Gaji Bersih</strong></td><td><strong>${employeeData.salary_slip_details.net_salary}</strong></td></tr>
            </table>
            <hr>
            <div class="side-by-side">
                <div class="table-container">
                    <h3>Rincian Tunjangan</h3>
                    <table>${allowanceList}</table>
                </div>
                <div class="table-container">
                    <h3>Rincian Potongan</h3>
                    <table>${deductionList}</table>
                </div>
            </div>
            <p>Slip gaji ini dihasilkan pada ${formatDate(new Date())}</p>
            <hr>
            <div style="display: flex; justify-content: space-between; margin-top: 50px;">
                <div style="text-align: center; width: 40%;">
                    <p>Tanggal:</p>
                    <p>Penerima,</p>
                    <br><br><br>
                    <p>${pegawai.pemilik_rekening}</p>
                </div>
                <div style="text-align: center; width: 40%;">
                    <p>General Manager,</p>
                    <img src="${base_url}assets/img/ttd_cnf.png" alt="Tanda Tangan General Manager" style="width: 150px; height: auto;" />
                    <p>ARY SETIAJI</p>
                </div>
            </div>
        </div>
    `;

    const newWindow = window.open();
    newWindow.document.write(`
        <html>
            <head>
                <title>Slip Gaji</title>
                <style>
                    body { font-family: Arial, sans-serif; margin: 0; padding: 0; }
                    .salary-slip { width: 210mm; max-width: 210mm; margin: auto; padding: 20px; box-sizing: border-box; }
                    h1, h3 { text-align: center; }
                    table { width: 100%; margin-top: 20px; border-collapse: collapse; }
                    table, th, td { border: 1px solid black; padding: 8px; text-align: left; }
                    p { margin: 5px 0; }
                    .side-by-side { display: flex; justify-content: space-between; margin-top: 20px; }
                    .table-container { width: 48%; }
                    .main-table { font-size: 12px; width: 90%; margin: auto; }
                    @media print {
                        body { margin: 0; width: 210mm; height: 297mm; }
                        .salary-slip { page-break-inside: avoid; }
                    }
                </style>
            </head>
            <body onload="window.print();window.close();">
                ${slipHTML}
            </body>
        </html>
    `);
}

function numberFormat(number, decimals = 2, decPoint = ',', thousandsSep = '.') {
    // Cek apakah input adalah angka
    if (isNaN(number)) return '0';

    // Ubah angka menjadi fixed point berdasarkan jumlah desimal
    const fixedNumber = Number(number).toFixed(decimals);

    // Pisahkan angka sebelum dan setelah desimal
    const [integerPart, decimalPart] = fixedNumber.split('.');

    // Format angka sebelum desimal dengan pemisah ribuan
    const formattedInteger = integerPart.replace(/\B(?=(\d{3})+(?!\d))/g, thousandsSep);

    // Gabungkan kembali angka yang telah diformat
    return formattedInteger + (decimals ? decPoint + decimalPart : '');
}

// Event listener untuk tombol Print
$(document).on('click', '.print-slip', function () {
    const employeeId = $(this).data('id');
    $.ajax({
        url: base_url + '/MasterPenggajianDetailController/getEmployeeSalarySlip/' + employeeId + '/' + masterId,
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response) {
                generateSalarySlipHTML(response);
            } else {
                alert('Data karyawan tidak ditemukan.');
            }
        },
        error: function(xhr, status, error) {
            console.error('Error: ' + status + ' - ' + error);
            alert('Gagal memuat data slip gaji.');
        }
    });
});


  $(document).on('click', '.varTunjangan', function() {
    var pin = $(this).attr('pin');
    var id = $(this).attr('id');
    var name = $(this).attr('name');
          $('#AllowaceEmployeeName').val(name); 
          $('#AllowaceEmployeeId').val(id); 
          $('#AllowaceEmployeePin').val(pin); 

    fetchAllowanceOptions();
    fetchAllowancesUser(id);
    $('#tunjanganModal').modal('show');


  });
  $(document).on('click', '.varPotongan', function() {
    var pin = $(this).attr('pin');
    var id = $(this).attr('id');
    var name = $(this).attr('name');
          $('#DeductionEmployeeName').val(name); 
          $('#DeductionEmployeeId').val(id); 
          $('#DeductionEmployeePin').val(pin); 

    fetchDeductionOptions();
    fetchDeductionsUser(id);
    $('#potonganModal').modal('show');


  });
  function fetchAllowancesUser(id){
     $('#allowanceTable tbody').empty();
    $.ajax({
      url: base_url+`user/getEmployeeAllowances/${id}`,
      method: 'GET',
      dataType: 'json',
      success: function(data) {
        let tbody = '';
        if (data.length > 0) {
          data.forEach(function(allowance, index) {
            tbody += `
            <tr>
            <td>${index + 1}</td>
            <td>${allowance.allowance_name}</td>
            <td>${formatRupiah(allowance.amount)}</td>
            <td>
            <button class="btn btn-danger deleteAllowanceBtn" data-id="${allowance.id}">Delete</button>
            </td>
            </tr>
            `;
          });
        } else {
          tbody = `<tr><td colspan="4" class="text-center">No Allowances Found</td></tr>`;
        }
        $('#allowanceTable tbody').html(tbody);
      },
      error: function() {
        alert('Failed to fetch allowances.');
      }
    });
  }
    // Fetch allowances and populate the table
  function fetchAllowanceOptions() {
    $.ajax({
            url: base_url+'user/getAllowanceOptions', // Controller URL
            type: 'GET',
            dataType: 'json',
            success: function(response) {
              if (response.status === 'success' && response.data.length > 0) {
                populateAllowanceDropdown(response.data);
              } else {
                alert("No allowances found.");
              }
            },
            error: function() {
              alert("Failed to fetch allowances.");
            }
          });
  }

  function populateAllowanceDropdown(allowances) {
    let options = '<option value="">Select Allowance</option>';
    $.each(allowances, function(index, allowance) {
      options += `<option value="${allowance.id}" 
      data-allowance="${allowance.Kode}">
      ${allowance.Nama} - ${allowance.Status}
      </option>`;
    });
    $('#allowanceSelect').html(options);
  }

  $('#saveTunjanganBtn').on('click', function() {
    let employeeId = $('#AllowaceEmployeeId').val();
    let allowanceId = $('#allowanceSelect').val();
    let amount = $('#jumlahTunjangan').val();

    if (employeeId === '' || allowanceId === ''|| amount === '') {
      alert("Please fill out all fields.");
      return;
    }

    $.ajax({
      url: base_url+'user/addAllowance', // Controller URL to add allowance
      type: 'POST',
      data: {
        employeeId: employeeId,
        allowanceId: allowanceId,
        amount: amount
      },
      dataType: 'json',
      success: function(response) {
        if (response.status === 'success') {
                      fetchAllowancesUser(employeeId)

Swal.fire({
            position: 'center',
            icon: 'success',
            title: response.message,
            showConfirmButton: false,
            timer: 2500
          })
        } else {
          Swal.fire({
            position: 'center',
            icon: 'success',
            title: response.message,
            showConfirmButton: false,
            timer: 2500
          })
        }
      },
      error: function(xhr, status, error) {
        alert("Failed to add allowance.");
      }
    });
  });

function formatRupiah(amount) {
    return new Intl.NumberFormat('id-ID', { 
        style: 'currency', 
        currency: 'IDR', 
        minimumFractionDigits: 0,
        maximumFractionDigits: 0
    }).format(amount);
}

  $('#allowanceTable tbody').on('click', '.deleteAllowanceBtn', function() {
        const id = $(this).data('id');
        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: `Tunjangan ini akan dihapus!`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, hapus!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    type: "POST",
                    url: base_url + '/user/deleteAllowanceList/' + id,
                    success: function() {
                      fetchAllowancesUser(id)
                        Swal.fire('Sukses', 'Tunjangan berhasil dihapus.', 'success');
                    },
                    error: function() {
                        Swal.fire('Error', 'Gagal menghapus tunjangan.', 'error');
                    }
                });
            }
        });
    });


//deduction

  function fetchDeductionsUser(id){
     $('#potonganTable tbody').empty();
    $.ajax({
      url: base_url+`user/getEmployeeDeductions/${id}`,
      method: 'GET',
      dataType: 'json',
      success: function(data) {
        let tbody = '';
        if (data.length > 0) {
          data.forEach(function(deduction, index) {
            tbody += `
            <tr>
            <td>${index + 1}</td>
            <td>${deduction.deduction_name}</td>
            <td>${formatRupiah(deduction.amount)}</td>
            <td>
            <button class="btn btn-danger deleteDeductionBtn" data-id="${deduction.id}">Delete</button>
            </td>
            </tr>
            `;
          });
        } else {
          tbody = `<tr><td colspan="4" class="text-center">No Deduction Found</td></tr>`;
        }
        $('#potonganTable tbody').html(tbody);
      },
      error: function() {
        alert('Failed to fetch deduction.');
      }
    });
  }
    // Fetch allowances and populate the table
  function fetchDeductionOptions() {
    $.ajax({
            url: base_url+'user/getDeductionOptions', // Controller URL
            type: 'GET',
            dataType: 'json',
            success: function(response) {
              if (response.status === 'success' && response.data.length > 0) {
                populateDeductionDropdown(response.data);
              } else {
                alert("No allowances found.");
              }
            },
            error: function() {
              alert("Failed to fetch allowances.");
            }
          });
  }

  function populateDeductionDropdown(deduction) {
    let options = '<option value="">Select Deduction</option>';
    $.each(deduction, function(index, deduction) {
      options += `<option value="${deduction.id}" 
      data-deduction="${deduction.Kode}">
      ${deduction.Nama} - ${deduction.Status}
      </option>`;
    });
    $('#deductionSelect').html(options);
  }

  $('#addPotonganBtn').on('click', function() {
    let employeeId = $('#DeductionEmployeeId').val();
    let deductionId = $('#deductionSelect').val();
    let amount = $('#jumlahPotongan').val();

    if (employeeId === '' || deductionId === ''|| amount === '') {
      alert("Please fill out all fields.");
      return;
    }

    $.ajax({
      url: base_url+'user/addDeductionList', // Controller URL to add allowance
      type: 'POST',
      data: {
        employeeId: employeeId,
        deductionId: deductionId,
        amount: amount
      },
      dataType: 'json',
      success: function(response) {
        if (response.status === 'success') {
                      fetchDeductionsUser(employeeId)
          
Swal.fire({
            position: 'center',
            icon: 'success',
            title: response.message,
            showConfirmButton: false,
            timer: 2500
          })
        } else {
          Swal.fire({
            position: 'center',
            icon: 'success',
            title: response.message,
            showConfirmButton: false,
            timer: 2500
          })
        }
      },
      error: function(xhr, status, error) {
        alert("Failed to add allowance.");
      }
    });
  });

function formatRupiah(amount) {
    return new Intl.NumberFormat('id-ID', { 
        style: 'currency', 
        currency: 'IDR', 
        minimumFractionDigits: 0,
        maximumFractionDigits: 0
    }).format(amount);
}

  $('#potonganTable tbody').on('click', '.deleteDeductionBtn', function() {
        const id = $(this).data('id');
        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: `Tunjangan ini akan dihapus!`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, hapus!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    type: "POST",
                    url: base_url + '/user/deleteDeductionList/' + id,
                    success: function() {
                      fetchDeductionsUser(id)
                        Swal.fire('Sukses', 'Tunjangan berhasil dihapus.', 'success');
                    },
                    error: function() {
                        Swal.fire('Error', 'Gagal menghapus tunjangan.', 'error');
                    }
                });
            }
        });
    });

$(document).on('click', '#printRekapGaji', function () {
    // Panggil fungsi untuk mendapatkan dan mencetak data rekap gaji
    printRekapGajiHTML(masterId);
});
function printRekapGajiHTML(masterId) {
    $.ajax({
        url: base_url + '/MasterPenggajianDetailController/dataEmployeeMaster/' + masterId,
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            const rekapHTML = generateRekapGajiHTML(response);
            const newWindow = window.open();
            newWindow.document.write(`
                <html>
                    <head>
                        <title>Rekap Gaji</title>
                        <style>
                            body { font-family: Arial, sans-serif; padding: 20px; }
                            .rekap-slip { width: 100%; margin: auto; }
                            h1, h3 { text-align: center; }
                            table { width: 100%; margin-top: 20px; border-collapse: collapse; }
                            table, th, td { border: 1px solid black; padding: 10px; text-align: left; }
                            .page-break { page-break-before: always; }
                        </style>
                    </head>
                    <body onload="window.print();window.close();">
                        ${rekapHTML}
                    </body>
                </html>
            `);
        },
        error: function(xhr, status, error) {
            console.error('Error: ' + status + ' - ' + error);
            alert('Gagal memuat data rekap gaji.');
        }
    });
}

function generateRekapGajiHTML(employeeDataList) {
    let rekapHTML = `
        <div class="rekap-slip">
            <h1>Rekap Gaji Karyawan</h1>
            <p>Periode: ${formatDate(new Date())}</p>
            <hr>`;

    let totalGajiKeseluruhan = 0; // Inisialisasi total gaji keseluruhan

    employeeDataList.forEach((employeeData, index) => {
        totalGajiKeseluruhan += parseFloat(employeeData.total_salary); // Tambahkan total salary setiap karyawan ke total keseluruhan

        rekapHTML += `
            <div class="salary-slip ${index > 0 ? 'page-break' : ''}">
                <h3>${employeeData.pegawai_nama}</h3>
                <p><strong>ID Karyawan:</strong> ${employeeData.karyawan_id}</p>
                <p><strong>Periode Gaji:</strong> ${formatDate(employeeData.tanggal_awal_penggajian)} - ${formatDate(employeeData.tanggal_akhir_penggajian)}</p>
                <h4>Rincian Gaji</h4>
                <table>
                    <tr><td>Gaji Pokok</td><td>${formatRupiah(employeeData.total_salary)}</td></tr>
                    <tr><td>Total Jam Kerja</td><td>${employeeData.total_work_Hours}</td></tr>
                    <tr><td>Lembur 1</td><td>${employeeData.total_overtime1_Hours}</td></tr>
                    <tr><td>Lembur 2</td><td>${employeeData.total_overtime2_Hours}</td></tr>
                    <tr><td>Lembur 3</td><td>${employeeData.total_overtime3_Hours}</td></tr>
                    <tr><td>Kerja Hari Minggu</td><td>${employeeData.sunday_work_Hours}</td></tr>
                </table>
                <hr>
            </div>`;
    });

    // Tambahkan total keseluruhan gaji di akhir laporan
    rekapHTML += `
        <div class="total-gaji">
            <h3>Total Keseluruhan Gaji</h3>
            <p><strong>Total Gaji Semua Karyawan:</strong> ${formatRupiah(totalGajiKeseluruhan)}</p>
        </div>
    `;

    rekapHTML += `</div>`;
    return rekapHTML;
}

// Event listener untuk tombol Print Presensi
$(document).on('click', '.print-presensi', function () {
    const pin = $(this).attr('pinKaryawan');
    const nama = $(this).attr('namaKaryawan');
    const id = $(this).attr('idKaryawan');
    const startDate = $(this).attr('tglAwal');
    const endDate = $(this).attr('tglAkhir');

    // Panggil fungsi untuk mendapatkan data presensi dan cetak
    printAttendanceReport(nama,pin, id, startDate, endDate);
});

function printAttendanceReport(nama,pin, id, startDate, endDate) {
    $.ajax({
        url: base_url + 'user/getPresensi',
        type: 'POST',
        data: { pin: pin, id: id, startDate: startDate, endDate: endDate },
        dataType: 'json',
        success: function(response) {
            const data = response.data;

            if (!Array.isArray(data)) {
                alert("Unexpected response format");
                return;
            }

            $.ajax({
                url: base_url + 'user/getDataWorkDay',
                type: 'POST',
                dataType: 'json',
                success: function(workDays) {
                    const groupedData = groupAttendanceData(data);
                    const attendanceHTML = generatePrintableAttendanceTable(groupedData, workDays, startDate, endDate, nama, pin);

                    const newWindow = window.open();
                    newWindow.document.write(`
                        <html>
                            <head>
                                <title>Rekap Presensi</title>
                                <style>
                                    body { font-family: Arial, sans-serif; padding: 20px; }
                                    .rekap-presensi { width: 100%; margin: auto; }
                                    h1, h3 { text-align: center; }
                                    table { width: 100%; margin-top: 20px; border-collapse: collapse; }
                                    table, th, td { border: 1px solid black; padding: 10px; text-align: left; }
                                </style>
                            </head>
                            <body onload="window.print();window.close();">
                                ${attendanceHTML}
                            </body>
                        </html>
                    `);
                },
                error: function() {
                    alert("Error fetching work day settings.");
                }
            });
        },
        error: function() {
            alert("Error fetching attendance data.");
        }
    });
}

// Fungsi untuk mengenerate HTML presensi untuk dicetak
function generatePrintableAttendanceTable(groupedData, workDays, startDate, endDate, nama, pin) {
    let html = `
    <div class="rekap-presensi">
    <h1>Rekap Presensi Karyawan</h1>
    <p>Periode: ${formatDateWithDayIndonesian(startDate)} - ${formatDateWithDayIndonesian(endDate)}</p>
    <p>Karyawan: ${pin} - ${nama.toUpperCase()}</p>
    <hr>
    <table class="table table-striped table-bordered">
    <thead>
        <tr>
            <th>Scan Date</th>
            <th>Jam Masuk</th>
            <th>Jam Keluar</th>
            <th>Total Jam Kerja</th>
            <th>Jam Normal</th>
            <th>Jam Lembur 1</th>
            <th>Jam Lembur 2</th>
            <th>Jam Lembur 3</th>
        </tr>
    </thead>
    <tbody>`;

    let totalDurationMinutes = 0;
    let totalNormalWorkMinutes = 0;
    let totalOvertime1Minutes = 0;
    let totalOvertime2Minutes = 0;
    let totalOvertime3Minutes = 0;

    $.each(groupedData, function (date, entries) {
        const { inTime: originalInTime, outTime: originalOutTime } = getInOutTimes(entries);
        let inTime = new Date(originalInTime);
        let outTime = new Date(originalOutTime);
        const dayOfWeek = getDayName(date);
        const workDaySetting = workDays.find(day => day.day === dayOfWeek);

        if (inTime && outTime) {
            const duration = (outTime - inTime) / (1000 * 60); // Duration in minutes
            totalDurationMinutes += duration;

            // Format times and durations
            const formattedInTime = formatTime(inTime);
            const formattedOutTime = formatTime(outTime);
            const { totalHours, totalMinutes } = formatDuration(duration);

            // Calculate normal and overtime minutes
            let { normalWorkMinutes, overtimeMinutes1, overtimeMinutes2, overtimeMinutes3 } = calculateWorkMinutes(inTime, outTime, workDaySetting, date);
            totalNormalWorkMinutes += normalWorkMinutes;

            // Set overtimeMinutes1 to 0 if it's less than 75 minutes
            if (overtimeMinutes1 < 75) {
                overtimeMinutes1 = 0;
            }
            totalOvertime1Minutes += overtimeMinutes1;

            // Bulatkan lembur level 2 dan 3 ke bawah per 15 menit
            const roundedOvertime2 = Math.floor(overtimeMinutes2 / 15) * 15;
            const roundedOvertime3 = Math.floor(overtimeMinutes3 / 15) * 15;
            totalOvertime2Minutes += roundedOvertime2;
            totalOvertime3Minutes += roundedOvertime3;

            html += `
            <tr>
                <td>${date} - ${dayOfWeek}</td>
                <td>${formattedInTime}</td>
                <td>${formattedOutTime}</td>
                <td>${totalHours} jam ${totalMinutes} menit</td>
                <td>${Math.floor(normalWorkMinutes / 60)} jam ${Math.floor(normalWorkMinutes % 60)} menit</td>
                <td>${Math.floor(overtimeMinutes1 / 60)} jam ${Math.floor(overtimeMinutes1 % 60)} menit</td>
                <td>${Math.floor(roundedOvertime2 / 60)} jam ${roundedOvertime2 % 60} menit</td>
                <td>${Math.floor(roundedOvertime3 / 60)} jam ${roundedOvertime3 % 60} menit</td>
            </tr>`;
        }
    });

    // Format total normal work minutes for display
    const totalNormalHours = Math.floor(totalNormalWorkMinutes / 60);
    const totalNormalMinutes = Math.floor(totalNormalWorkMinutes % 60);

    html += `</tbody></table></div>`;
    html += `
    <table class="table table-bordered">
    <thead>
    <tr>
        <th>Keterangan</th>
        <th>Jumlah</th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td>Total Durasi Kerja</td>
        <td>${Math.floor(totalDurationMinutes / 60)} jam ${Math.floor(totalDurationMinutes % 60)} menit</td>
    </tr>
    <tr>
        <td>Total Kerja (Setelah Istirahat)</td>
        <td>${totalNormalHours} jam ${totalNormalMinutes} menit</td>
    </tr>
    <tr>
        <td>Total Lembur 1</td>
        <td>${Math.floor(totalOvertime1Minutes / 60)} jam ${Math.floor(totalOvertime1Minutes % 60)} menit</td>
    </tr>
    <tr>
        <td>Total Lembur 2</td>
        <td>${Math.floor(totalOvertime2Minutes / 60)} jam ${Math.floor(totalOvertime2Minutes % 60)} menit</td>
    </tr>
    <tr>
        <td>Total Lembur 3</td>
        <td>${Math.floor(totalOvertime3Minutes / 60)} jam ${Math.floor(totalOvertime3Minutes % 60)} menit</td>
    </tr>
    </tbody>
    </table>`;

    return html;
}




});

// Event handler untuk delete

