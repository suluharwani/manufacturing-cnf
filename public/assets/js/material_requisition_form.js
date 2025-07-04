var loc = window.location;
var base_url = loc.protocol + "//" + loc.hostname + (loc.port ? ":" + loc.port : "") + "/";

let statusDoc = $('#status').val();
function getLastSegment() {
  var pathname = window.location.pathname; // Mendapatkan path dari URL
  var segments = pathname.split('/').filter(function(segment) { return segment.length > 0; });
  return segments[segments.length - 1]; // Mengambil segment terakhir
}
masterId = getLastSegment();
$(document).ready(function () {
  initializeSupplierTable();

  tableWO();
  tabel_requisition();

});
  function tabel_requisition(){
    $.ajax({
      url: base_url+'/requisition/dataRequestList/'+getLastSegment(),
      type: 'GET',
      dataType: 'json',
      success: function(response) {
          // Mengosongkan tabel sebelum memuat data baru
          var tableBody = $('#tabel_requisition');
          tableBody.empty();
          no = 1
          
          // Menampilkan data ke dalam tabel
          response.forEach(function(item) {
            if(statusDoc == 'Posted'){
              button = `Document Posted`
            }else{
              button = `
                        <button class="btn btn-danger btn-sm delete" data-id="${item.id}">Delete</button>`
            }
              var row = `<tr>
                  <td>${no++}</td>
                  <td>${item.material_code}</td>
                  <td>${item.material_name}</td>
                  <td>${item.jumlah}</td>
                  <td>${button}</td>
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
function tableWO() {
  $.ajax({
    url: base_url + "requisition/getwoav/" + getLastSegment(),
    type: "GET",
    dataType: "json",
    success: function(response) {
      var tableBody = $("#materialTable tbody");
      tableBody.empty(); // Clear existing rows
      let no = 1;

      $.each(response, function(index, item) {
        // Calculate max_request based on total_stock (now included in the response)
        const totalStock = item.total_stock || 0;
        availableStock = item.remaining_quantity - item.terpenuhi - item.total_requisition_unposting ;
        availableStock = availableStock < 0 ? 0 : availableStock;
        const max_request = Math.ceil(Math.min(totalStock, availableStock));
        
        let button;
        if (statusDoc === 'Posted') {
          button = 'Document Posted';
        } else {
          button = (max_request <= 0) 
            ? 'Not available' 
            : `<button class="btn btn-primary request" max="${max_request}" material_id="${item.material_id}" name="${item.material_name}">Request</button>`;
        }

        var row = `<tr>
                      <td>${no++}</td>
                      <td>${item.material_name}</td>
                      <td>${item.satuan} (${item.c_satuan})</td>
                      <td>${(item.quantity * item.penggunaan).toFixed(2)}</td>
                      <td>${item.terpenuhi}</td>
                      <td>${item.remaining_quantity}</td>
                      <td>${item.total_requisition}</td>
                      <td>${item.total_requisition_unposting}</td>
                      <td>${totalStock}</td>
                      <td>${max_request}</td>
                      <td>${button}</td>
                      <td></td>
                  </tr>`;
        tableBody.append(row);
      });
    },
    error: function(xhr, status, error) {
      console.error("AJAX Error: ", error);
    }
  });
}

  


// Fungsi untuk menampilkan popup dengan input menggunakan SweetAlert2
$(document).on('click', '.request', function(e) {
  maxRequest = $(this).attr('max');
  idMaterial = $(this).attr('material_id');
  nama =   $(this).attr('name');
  Swal.fire({
    title: 'Add Request <br/>' + nama,
    text: `The maximum request you can make is ${maxRequest}. Please enter the quantity:`,
    input: 'number',
    inputAttributes: {
      min: 0.01,
      max: maxRequest,
    },
    inputValidator: (value) => {
      if (!value) {
        return 'You need to enter a value!';
      }
      if (value < 0.01 || value > parseFloat(maxRequest)) {
        console.log(true);
        return `The value must be between 0.01 and ${maxRequest}`;
      }else{
        console.log(false);
      }
    },
    showCancelButton: true,
    confirmButtonText: 'Submit',
    cancelButtonText: 'Cancel',
  }).then((result) => {
    if (result.isConfirmed) {
      const quantityRequested = result.value;

      // Panggilan AJAX untuk mengirimkan data ke server
      $.ajax({
        url: base_url + 'requisition/submitRequest', // Ganti dengan URL endpoint yang sesuai
        type: 'POST',
        data: {
          id_material_requisition: getLastSegment(),
          id_material: idMaterial, // Ganti dengan ID material yang sesuai
          jumlah: quantityRequested,
        },
        success: function (response) {
          // tableWO();
          //  $('#tabel_serverside').DataTable().ajax.reload();
          tableWO()
          tabel_requisition();
          Swal.fire({
            title: 'Success',
            text: `You have successfully requested ${quantityRequested} items.`,
            icon: 'success',
          });
          // Tambahkan logika tambahan jika diperlukan, seperti memperbarui tabel atau UI lainnya
        },
        error: function (xhr, status, error) {
          Swal.fire({
            title: 'Error',
            text: 'There was an error processing your request. Please try again later.',
            icon: 'error',
          });
          console.error("AJAX Error: ", status, error);
        }
      });
    }
  });
})

$(document).on('click', '.delete', function(e) {
  e.preventDefault();

  var id = $(this).data('id'); // Mendapatkan ID material dari data attribute

  // Menampilkan konfirmasi dengan SweetAlert
  Swal.fire({
    title: 'Are you sure?',
    text: "You won't be able to revert this!",
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#d33',
    cancelButtonColor: '#3085d6',
    confirmButtonText: 'Yes, delete it!',
  }).then((result) => {
    if (result.isConfirmed) {
      // Jika konfirmasi diterima, lakukan penghapusan
      $.ajax({
        type: "POST",
        url: base_url + "requisition/deleteList/" + id, // Endpoint untuk menghapus material
        data: {},
        success: function(response) {
    
            Swal.fire({
              icon: 'success',
              title: 'Deleted!',
              text: 'Material has been deleted.',
            });
            tableWO();
            tabel_requisition();
     
          
        },
        error: function() {
          Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Error deleting material!',
          });
        }
      });
    }
  });
});
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
              url: base_url + '/requisition/posting/' + id,
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
              url: base_url + '/requisition/posting/' + id,
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

$(document).on('click', '.edit-request', function(e) {
  maxRequest = $(this).attr('max');
  id = $(this).attr('id');
  nama = $(this).attr('name');
  currentQuantity = $(this).attr('current_quantity');

  Swal.fire({
    title: 'Edit Request <br/>' + nama,
    text: `The maximum request you can make is ${maxRequest}. Please enter the new quantity:`,
    input: 'number',
    inputValue: currentQuantity,
    inputAttributes: {
      min: 0.01,
      max: maxRequest,
    },
    inputValidator: (value) => {
      if (!value) {
        return 'You need to enter a value!';
      }
      if (value < 0.01 || value > parseFloat(maxRequest)) {
        return `The value must be between 0.01 and ${maxRequest}`;
      }
    },
    showCancelButton: true,
    confirmButtonText: 'Update',
    cancelButtonText: 'Cancel',
  }).then((result) => {
    if (result.isConfirmed) {
      const newQuantity = result.value;

      // Panggilan AJAX untuk memperbarui data di server
      $.ajax({
        url: base_url + 'requisition/updateRequest', // Ganti dengan URL endpoint yang sesuai untuk update
        type: 'POST',
        data: {
          id_material_requisition: getLastSegment(),
          id: id,
          jumlah: newQuantity,
        },
        success: function (response) {
          tableWO();
          tabel_requisition();
          Swal.fire({
            title: 'Success',
            text: `You have successfully updated the request to ${newQuantity} items.`,
            icon: 'success',
          });
        },
        error: function (xhr, status, error) {
          Swal.fire({
            title: 'Error',
            text: 'There was an error processing your request. Please try again later.',
            icon: 'error',
          });
          console.error("AJAX Error: ", status, error);
        }
      });
    }
  });
});

function formatNumber(number, decimals = 0) {
    // Memastikan angka adalah tipe Number
    if (isNaN(number)) {
        return 'Invalid number';
    }

    // Menentukan format desimal
    let options = { 
        minimumFractionDigits: decimals, 
        maximumFractionDigits: decimals 
    };

    // Menggunakan toLocaleString untuk format angka dengan titik ribuan
    return number.toLocaleString('id-ID', options);
}
function initializeSupplierTable() {
  console.log('Initializing DataTable for server-side processing');
   $('#tabel_serverside').DataTable({
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
            "url": base_url + "stock/stockdata",
            "type": "POST",
            "data": {}
        },
        columns: [

            { mRender: function (data, type, row) { return row[0]; } }, // No
            { mRender: function (data, type, row) { return row[3]; } }, // Code
            { mRender: function (data, type, row) { return row[2]; } }, // Nama Sup
          
            { mRender: function (data, type, row) { return `${row[14] === null ? 0 : row[14] } ${row[8]}`; } }, // 
            { mRender: function (data, type, row) { return `<button class="btn btn-primary request" max="${row[14]}" material_id="${row[9]}" name="${row[2]}">Request</button>`; } }, // 

        ],
        "columnDefs": [{
            "targets": [0],
            "orderable": false
        }],
        error: function () {
            $(".tabel_serverside-error").html("");
            $("#tabel_serverside").append('<tbody class="tabel_serverside-error"><tr><th colspan="3">Data Tidak Ditemukan di Server</th></tr></tbody>');
            $("#tabel_serverside_processing").css("display", "none");
        }
    });
}
