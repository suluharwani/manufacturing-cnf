var loc = window.location;
var base_url = loc.protocol + "//" + loc.hostname + (loc.port? ":"+loc.port : "") + "/";


$(document).ready(function() {
  var dataTable = $('#tabel_serverside').DataTable( {
    "processing" : true,
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
    "buttons": [
    'csv'
    ],
    "order": [],
    "ordering": true,
    "info": true,
    "serverSide": true,
    "stateSave" : true,
    "scrollX": true,
    "ajax":{
      "url" :base_url+"/requisition/listdata" , // json datasource 
      "type": "post",  // method  , by default get
      // "async": false,
      "dataType": 'json',
      "data":{},
    },

    columns: [
    {},
    {mRender: function (data, type, row) {
        return formatDateTime(row[1])
    }},
   
    {mRender: function (data, type, row) {
        return row[2]
    }},
    {mRender: function (data, type, row) {
      return row[8]
  }},
    {mRender: function (data, type, row) {
        return row[3]
    }},
    {mRender: function (data, type, row) {
      return row[4]

    }},
    {mRender: function (data, type, row) {
      return row[5]

    }},
    {mRender: function (data, type, row) {
      if (row[6] == 0 || row[6] == null) {
        stat = `<span class="badge bg-warning text-dark">Draft</span>`
      }else{
        stat = `<span class="badge bg-success text-dark">Posted</span>`
      }
        return stat
    }},
    {mRender: function (data, type, row) {
     return `<a href="${base_url}requisition/form/${row[7]}" target="_blank" class="btn btn-success btn-sm ">Edit</a>
             <a href="javascript:void(0);" class="btn btn-danger btn-sm delete" code = "${row[1]}" id="${row[7]}" >Delete</a>
             `; 
    }}
  ],
  "columnDefs": [{
    "targets": [0],
    "orderable": false
  }],

  error: function(){  // error handling
    $(".tabel_serverside-error").html("");
    $("#tabel_serverside").append('<tbody class="tabel_serverside-error"><tr><th colspan="3">Data Tidak Ditemukan di Server</th></tr></tbody>');
    $("#tabel_serverside_processing").css("display","none");

  }

});
})
function formatNumber(number) {
  return number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
}
function formatDateTime(datetime) {
  const date = new Date(datetime); // Mengubah datetime menjadi objek Date
  const year = date.getFullYear();
  const month = String(date.getMonth() + 1).padStart(2, '0'); // Menambahkan leading zero untuk bulan < 10
  const day = String(date.getDate()).padStart(2, '0'); // Menambahkan leading zero untuk tanggal < 10
  return `${day}-${month}-${year}`;
}

$('.tambah').click(function() { 
    // Reset form di modal
    $('#addReq')[0].reset();

    // Ambil opsi material dan masukkan ke dropdown
    getDepartOption().then(getDepartOption => {
      console.log(getDepartOption);
      $('#department').html(getDepartOption);
      $('#tambah').modal('show');
  }).catch(error => {
      Swal.fire('Error', error, 'error');
  });
  getWOOption().then(getWOOption => {
    console.log(getWOOption);
    $('#work_order').html(getWOOption);
    $('#tambah').modal('show');
}).catch(error => {
    Swal.fire('Error', error, 'error');
});
  });

  // Menangani form submit di modal
  $('#addReq').submit(function(event) {
    event.preventDefault();

    // Ambil data dari form
    var code = $('#mr').val();
    var wo = $('#work_order').val();
    var dept = $('#department').val();


    // Kirim data melalui AJAX ke server
    $.ajax({
      type: "POST",
      url: base_url + "requisition/addDocument", // URL untuk menambahkan material (ganti dengan URL yang sesuai)
      data: {
        code: code,
        id_dept: dept,
        id_wo: wo
      },
      success: function(response) {
        // Tampilkan pesan sukses jika berhasil menambahkan material
          Swal.fire({
            title: "success!",
            text: "Document Added!",
            icon: "success"
          });
          $('#tambah').modal('hide'); 
          $('#tabel_serverside').DataTable().ajax.reload();

      }, 
      error: function() {
        alert('Error connecting to server');
      }
    });
  });

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
        url: base_url + 'production/getWOList', // Endpoint untuk mendapatkan PI
        success: function(response) {
          data = JSON.parse(response);
          // Buat opsi produk dari data yang diterima
          var PIoptions = '<option value="">Work Order</option>';
                data.forEach(function(pi) {
                    PIoptions += `<option value="${pi.id}">${pi.kode}</option>`;
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
          $('#tabel_serverside').on('click','.delete',function(){
                const id = $(this).attr('id');
                const code = $(this).attr('code');
                Swal.fire({
                    title: 'Anda yakin ingin menghapus dokumen: '+code+'?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Hapus',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: base_url + '/pemusnahan/delete', 
                            type: 'post',
                            data: { id: id },
                            success: function(response) {
                                Swal.fire('Dihapus!', response.message, 'success');
                                $('#tabel_serverside').DataTable().ajax.reload();

                            },
                            error: function(xhr) {
                                let d = JSON.parse(xhr.responseText);
                                Swal.fire('Oops...', d.message, 'error');
                            }
                        });
                    }
                });
            });