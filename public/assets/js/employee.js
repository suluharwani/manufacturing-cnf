var loc = window.location;
var base_url = loc.protocol + "//" + loc.hostname + (loc.port ? ":" + loc.port : "") + "/";

$(document).ready(function() {
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
          <a href="javascript:void(0);" class="btn btn-secondary btn-sm varGaji" id="${row[1]}" pin="${row[4]}">Harian</a>
          <a href="javascript:void(0);" class="btn btn-secondary btn-sm varGaji" id="${row[1]}" pin="${row[4]}">Tunjangan</a>
          <a href="javascript:void(0);" class="btn btn-secondary btn-sm varGaji" id="${row[1]}" pin="${row[4]}">Potongan</a>
          <a href="javascript:void(0);" class="btn btn-warning btn-sm showDet" id="${row[1]}" pin="${row[4]}">Detail</a>
          <a href="javascript:void(0);" class="btn btn-primary btn-sm showPresensi" id="${row[1]}" pin="${row[4]}" nama="${row[2]}">Presensi</a>
          <a href="javascript:void(0);" class="btn btn-success btn-sm showGaji" id="${row[1]}" pin="${row[4]}">Gaji</a>`;
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
    // Handle the click event for varGaji button
$(document).on('click', '.varGaji', function() {
  var pin = $(this).attr('pin');
  var id = $(this).attr('id');

  // Fetch salary setting data using AJAX 
  $.ajax({
    url: base_url + '/user/getSalarySetting', // Define the controller URL
    type: 'POST',
    data: { pin: pin, id: id },
    dataType: 'json',
    success: function(response) {
      const existingPatterns = response.existingPatterns.map(Number); // Convert existing pattern IDs to numbers
      const allPatterns = response.allPatterns; // All available patterns

      let html = '';
      $.each(allPatterns, function(index, pattern) {
        const patternId = Number(pattern.id_salary_pattern); // Convert pattern ID to number for comparison

        // Check if this pattern exists for the current employee
        const isSelected = existingPatterns[0]
   
        if (isSelected) {
          // If the pattern is already assigned, show an "Update" button
          html += `<tr data-id="${pattern.id_salary_pattern}">
            <td>${pattern.id_salary_pattern}</td>
            <td>${pattern.nama}</td>
            <td><button class="btn btn-warning updateItemBtn" data-kode="${pattern.id_salary_pattern}" data-nama="${pattern.nama}">Update</button></td>
            </tr>`;
        } else {
          // Otherwise, show a "Pilih" button
          html += `<tr data-id="${pattern.id_salary_pattern}">
            <td>${pattern.id_salary_pattern}</td>
            <td>${pattern.nama}</td>
            <td><button class="btn btn-success selectItemBtn" data-kode="${pattern.id_salary_pattern}" data-nama="${pattern.nama}">Pilih</button></td>
            </tr>`;
        }
      });

      $('#availableItemsTable tbody').html(html);

      // Show the modal
      $('#salarySettingModal').modal('show');
    },
    error: function() {
      alert("Error fetching salary settings.");
    }
  });
});


// Event listener for selecting a new salary item
$(document).on('click', '.selectItemBtn', function() {
  var kode = $(this).data('kode');
  var nama = $(this).data('nama');

  // Send an AJAX request to insert the new salary data
  $.ajax({
    url: base_url + '/user/addSalarySetting', // Define the URL for adding a new item
    type: 'POST',
    data: { kode: kode, nama: nama },
    success: function() {
      alert('Salary item added successfully.');
      $('#salarySettingModal').modal('hide');
    },
    error: function() {
      alert('Failed to add salary item.');
    }
  });
});

// Event listener for updating an existing salary item
$(document).on('click', '.updateItemBtn', function() {
  var kode = $(this).data('kode');
  var nama = $(this).data('nama');

  // Send an AJAX request to update the salary data
  $.ajax({
    url: base_url + '/user/updateSalarySetting', // Define the URL for updating the item
    type: 'POST',
    data: { kode: kode, nama: nama },
    success: function() {
      alert('Salary item updated successfully.');
      $('#salarySettingModal').modal('hide');
    },
    error: function() {
      alert('Failed to update salary item.');
    }
  });
});


// Move selected item to the selected table (right)
  $(document).on('click', '.selectItemBtn', function() {
    var row = $(this).closest('tr').clone();
    var kode = row.data('id');
    row.find('td:last-child').html(`
      <input type="number" class="form-control nominalInput" placeholder="Masukkan Nominal" data-id="${kode}">
      <button class="btn btn-danger removeItemBtn">Hapus</button>
      `);
    $('#selectedItemsTable tbody').append(row);
    $(this).closest('tr').remove();
  });

// Remove item from the selected table (move back to available)
  $(document).on('click', '.removeItemBtn', function() {
    var row = $(this).closest('tr').clone();
    row.find('td:last-child').html('<button class="btn btn-success selectItemBtn">Pilih</button>');
    $('#availableItemsTable tbody').append(row);
    $(this).closest('tr').remove();
  });

// Save selected salary settings
  $('#saveSalarySettings').click(function() {
    let selectedItems = [];
    $('#selectedItemsTable tbody tr').each(function() {
      let kode = $(this).data('id');
      let nominal = $(this).find('.nominalInput').val();
      selectedItems.push({
        kode: kode,
        nominal: nominal
      });
    });

    // Send the selected items to the server
    $.ajax({
        url: base_url + '/user/saveSalarySettings', // Save URL
        type: 'POST',
        data: { items: selectedItems },
        dataType: 'json',
        success: function(response) {
          if (response.status === 'success') {
            alert("Data berhasil disimpan.");
            $('#salarySettingModal').modal('hide');
          } else {
            alert("Terjadi kesalahan saat menyimpan data.");
          }
        },
        error: function() {
          alert("Error saving salary settings.");
        }
      });
  });



  $(document).on('click', '.showPresensi', function() {
    var pin = $(this).attr('pin');
    var id = $(this).attr('id');

    $('#dateSelectionModal').modal('show');

    $('#fetchPresensi').off('click').on('click', function() {
      var startDate = $('#startDate').val();
      var endDate = $('#endDate').val();

      if (!startDate || !endDate) {
        alert("Silakan pilih tanggal awal dan akhir.");
        return;
      }

      refreshPopupContent(pin, id, startDate,endDate)
    });
  });

});


// function getDayName(input) {
//   let date;

//   if (typeof input === 'string') {
//     date = new Date(input);
//   } else if (input instanceof Date) {
//     date = input;
//   } else {
//     throw new TypeError('Argument must be a Date object or a valid date string');
//   }

//   const options = { weekday: 'long' };
//   return date.toLocaleDateString('id-ID', options);
// }
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
function refreshPopupContent(pin, id, startDate, endDate) {
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

// Function to group attendance data by date
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

// Function to get in and out times from attendance entries
function getInOutTimes(entries) {
    const inTime = new Date(entries[0].scan_date); // original inTime
    const outTime = new Date(entries[entries.length - 1].scan_date); // original outTime
    return { inTime, outTime };
}

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

// Function to group attendance data by date
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

// Function to get in and out times from attendance entries
function getInOutTimes(entries) {
    const inTime = new Date(entries[0].scan_date); // original inTime
    const outTime = new Date(entries[entries.length - 1].scan_date); // original outTime
    return { inTime, outTime };
}

// Function to generate HTML table for attendance data
// Function to generate HTML table for attendance data
// Function to generate HTML table for attendance data
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
        let inTime = new Date(originalInTime); // Create a new variable for adjustment
        let outTime = new Date(originalOutTime); // Create a new variable for adjustment
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
            const { normalWorkMinutes, overtimeMinutes1, overtimeMinutes2, overtimeMinutes3 } = calculateWorkMinutes(inTime, outTime, workDaySetting, date);
            totalNormalWorkMinutes += normalWorkMinutes; // Add to total normal work minutes
            totalOvertime1Minutes += overtimeMinutes1;
            totalOvertime2Minutes += overtimeMinutes2;
            totalOvertime3Minutes += overtimeMinutes3;

            // Apply red background if totalHours == 0
            const rowClass = totalHours <= 0 ? 'class="bg-danger"' : ''; // Assign class if totalHours is 0

            html += `
                <tr ${rowClass}>
                    <td>${date} - ${dayOfWeek}</td>
                    <td>${formattedInTime}</td>
                    <td>${formattedOutTime}</td>
                    <td>${totalHours} jam ${totalMinutes} menit</td>
                    <td>${Math.floor(normalWorkMinutes / 60)} jam ${Math.floor(normalWorkMinutes % 60)} menit</td>
                    <td>${Math.floor(overtimeMinutes1 / 60)} jam ${Math.floor(overtimeMinutes1 % 60)} menit</td>
                    <td>${Math.floor(overtimeMinutes2 / 60)} jam ${Math.floor(overtimeMinutes2 % 60)} menit</td>
                    <td>${Math.floor(overtimeMinutes3 / 60)} jam ${Math.floor(overtimeMinutes3 % 60)} menit</td>
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

// Function to format time to HH:MM
function formatTime(date) {
    return date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
}

// Function to format duration in hours and minutes
function formatDuration(duration) {
    return {
        totalHours: Math.floor(duration / 60),
        totalMinutes: Math.floor(duration % 60)
    };
}

// Function to calculate normal work and overtime minutes
// Function to calculate normal work and multiple overtime minutes (1, 2, and 3)
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

        // Calculate normal work minutes
        if (outTime > workEnd) {
            normalWorkMinutes = (workEnd - inTime) / (1000 * 60); // Calculate from inTime to workEnd
        } else {
            normalWorkMinutes = (outTime - inTime) / (1000 * 60); // Calculate from inTime to outTime
        }

        // Subtract break time from start of break until end of break
        if (inTime < workBreakEnd && outTime > workBreakStart) {
            const breakStartTime = inTime < workBreakStart ? workBreakStart : inTime; // Use the later time
            const breakDuration = (workBreakEnd - breakStartTime) / (1000 * 60); // Duration of break
            normalWorkMinutes -= breakDuration; // Subtract break duration from normal work minutes
        }

        // Calculate overtime levels
        if (outTime > overtimeStart1) {
            overtimeMinutes1 = (outTime < overtimeEnd1 ? outTime : overtimeEnd1) - overtimeStart1;
            overtimeMinutes1 = overtimeMinutes1 / (1000 * 60); // Convert to minutes
        }
        if (outTime > overtimeStart2) {
            overtimeMinutes2 = (outTime < overtimeEnd2 ? outTime : overtimeEnd2) - overtimeStart2;
            overtimeMinutes2 = overtimeMinutes2 / (1000 * 60); // Convert to minutes
        }
        if (outTime > overtimeStart3) {
            overtimeMinutes3 = (outTime < overtimeEnd3 ? outTime : overtimeEnd3) - overtimeStart3;
            overtimeMinutes3 = overtimeMinutes3 / (1000 * 60); // Convert to minutes
        }

        // Prevent negative values
        normalWorkMinutes = Math.max(normalWorkMinutes, 0);
        overtimeMinutes1 = Math.max(overtimeMinutes1, 0);
        overtimeMinutes2 = Math.max(overtimeMinutes2, 0);
        overtimeMinutes3 = Math.max(overtimeMinutes3, 0);
    }

    return { normalWorkMinutes, overtimeMinutes1, overtimeMinutes2, overtimeMinutes3 };
}


// Helper function to get the day name in Indonesian
function getDayName(dateString) {
  const date = new Date(dateString);
  const options = { weekday: 'long' };
  return date.toLocaleDateString('id-ID', options); // Output in Indonesian
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



function fetchSalaryCategories() {
  $.ajax({
            url: base_url + '/user/getSalaryCat',  // Controller URL to fetch salary categories
            type: 'POST',                          // POST request
            dataType: 'json',                      // Expecting JSON response
            success: function(response) {
              if (response.status === 'success' && response.data.length > 0) {
                populateSalaryDropdown(response.data);
              } else {
                alert("Kategori gaji tidak ditemukan.");
              }
            },
            error: function(xhr, status, error) {
              alert("Gagal mengambil data kategori gaji.");
            }
          });
}

    // Function to populate the dropdown with fetched salary categories
function populateSalaryDropdown(categories) {
  let categoryOptions = '<option value="">Pilih Kategori</option>';

  $.each(categories, function(index, category) {
    categoryOptions += `<option value="${category.id}" 
    data-gaji-pokok="${category.Gaji_Pokok}" 
    data-gaji-per-jam="${category.Gaji_Per_Jam}" 
    data-gaji-per-jam-minggu="${category.Gaji_Per_Jam_Hari_Minggu}">
    ${category.Nama}
    </option>`;
  });

        // Append options to the dropdown
  $('#salaryCategorySelect').html(categoryOptions);
}

    // Show the modal and fetch categories when button is clicked
$(document).on('click', '.varGaji', function() {
  var pin = $(this).attr('pin');
  var id = $(this).attr('id');

        // Fetch categories when the modal is about to be shown
  $('#id').val(id)
  $('#pin').val(pin)
  $('#salaryCategoryModal').modal('show');
  fetchSalaryCategories();
});

    // Update the salary fields when a category is selected
$('#salaryCategorySelect').on('change', function() {
  var selectedOption = $(this).find('option:selected');
  var gajiPokok = selectedOption.data('gaji-pokok');
  var gajiPerJam = selectedOption.data('gaji-per-jam');
  var gajiPerJamMinggu = selectedOption.data('gaji-per-jam-minggu');

        // Set the values of the salary fields
  $('#gajiPokok').val(gajiPokok);
  $('#gajiPerJam').val(gajiPerJam);
  $('#gajiPerJamMinggu').val(gajiPerJamMinggu);
});

    // Save the selected category when "Simpan" button is clicked
$('#saveCategoryBtn').on('click', function() {
  var selectedCategoryId = $('#salaryCategorySelect').val();

  if (!selectedCategoryId) {
    alert("Silakan pilih kategori gaji.");
    return;
  }

  var formData = {
            pin: $('#pin').val(),  
            id: $('#id').val(),    
            salaryCategoryId: selectedCategoryId,
          };

     
          $.ajax({
            url: base_url + '/user/saveSalaryCategory',  
            type: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
              if (response.status === 'success') {
                alert("Kategori gaji berhasil disimpan.");
                    $('#salaryCategoryModal').modal('hide');  
                  } else {
                    alert("Gagal menyimpan kategori gaji.");
                  }
                },
                error: function() {
                  alert("Terjadi kesalahan saat menyimpan kategori gaji.");
                }
              });
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