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
            <a href="javascript:void(0);" class="btn btn-secondary btn-sm varGaji" id="${row[1]}" pin="${row[4]}">Variabel Gaji</a>
            <a href="javascript:void(0);" class="btn btn-warning btn-sm showDet" id="${row[1]}" pin="${row[4]}">Detail</a>
            <a href="javascript:void(0);" class="btn btn-primary btn-sm showPresensi" id="${row[1]}" pin="${row[4]}">Presensi</a>
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


function getDayName(input) {
    let date;
    
    if (typeof input === 'string') {
        date = new Date(input);
    } else if (input instanceof Date) {
        date = input;
    } else {
        throw new TypeError('Argument must be a Date object or a valid date string');
    }
    
    const options = { weekday: 'long' };
    return date.toLocaleDateString('id-ID', options);
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
function refreshPopupContent(pin, id, startDate,endDate) {
$('#popupContent').empty();
        $.ajax({
      url: base_url + 'user/getPresensi',
      type: 'POST',
      data: { pin: pin, id: id, startDate: startDate, endDate: endDate },
      dataType: 'json',
      success: function(response) {
       // Log the response for debugging

        const data = response.data; // Access the data array directly

        if (!Array.isArray(data)) {
          alert("Unexpected response format");
          return;
        }

        // Group records by date
        const groupedData = {};
        $.each(data, function(index, item) {
          const dateKey = item.scan_date.split(' ')[0]; // Get the date part only
          if (!groupedData[dateKey]) {
            groupedData[dateKey] = [];
          }
          groupedData[dateKey].push(item);
        });
 // console.log(groupedData); 
        // Process and display response data in a popup
let html = '<div class="table-responsive" style="max-height: 400px; overflow-y: auto;"><table class="table table-striped table-bordered"><thead><tr><th>Scan Date</th><th>Jam Masuk</th><th>Jam Keluar</th><th>Total Jam Kerja</th></tr></thead><tbody>';
let totalDurationMinutes = 0;
let workDay = 0;
$.each(groupedData, function(date, entries) {
  let inTime, outTime;
  let time1,time2;
  // Get the first entry as Jam Masuk and the last as Jam Keluar
  time1 = new Date(entries[0].scan_date);
  time2 = new Date(entries[entries.length - 1].scan_date);
  if (time1<time2) {
    inTime = time1;
    outTime = time2
  }else{
    inTime = time2;
    outTime = time1
  }

  if (inTime && outTime) {
    const duration = (outTime - inTime) / (1000 * 60); // Duration in minutes
    totalDurationMinutes += duration;

    const formattedInTime = inTime.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
    const formattedOutTime = outTime.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
    const totalHours = Math.floor(duration / 60);
    const totalMinutes = duration % 60;
    workDay++;
    html += `<tr>
               <td>${date} - ${getDayName(date)}</td>
               <td>${formattedInTime}</td>
               <td>${formattedOutTime}</td>
               <td>${totalHours} jam ${parseInt(totalMinutes)} menit</td>
               <td><a href="javascript:void(0);" class="btn btn-success btn-sm addAttendance" id = "${id}" startDate = "${startDate}" endDate = "${endDate}" pin = "${entries[0].pin}" date = "${date}">Tambah Data</a></td>
             </tr>`;
  }
});

html += '</tbody></table></div>';
html += `<div>Hari kerja: ${workDay} hari, Total Durasi Kerja: ${Math.floor(totalDurationMinutes / 60)} jam ${parseInt(totalDurationMinutes) % 60} menit</div>`;

        $('#popupContent').html(html);
        $('#popupModal').modal('show');

        $('#dateSelectionModal').modal('hide');
      },
      error: function() {
        alert("Error fetching data.");
      }
    });
}
