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
      "url" :base_url+"purchase/purchaselist" , // json datasource 
      "type": "post",  // method  , by default get
      // "async": false,
      "dataType": 'json',
      "data":{},
    },
    
    columns: [
      {},
      {mRender: function (data, type, row) {
    //   return  row[1]+" "+row[2]+"</br>"+"<a href=mailto:"+row[3]+">"+row[3]+"</a>";
        return row[3]
      }},
      {mRender: function (data, type, row) {
        return row[2]
      }},
      {mRender: function (data, type, row) {
       return row[5]
     }},
     {mRender: function (data, type, row) {
       return row[4]; 
     }},

     {mRender: function (data, type, row) {
       return `<a href="javascript:void(0);" class="btn btn-success btn-sm showSalesOrder" id="'+row[1]+'" >Detail</a>`; 
     }},
     {mRender: function (data, type, row) {
      return `<a href="javascript:void(0);" class="btn btn-success btn-sm showTersedia" id="'+row[1]+'" >Detail</a>`; 
    }},
    {mRender: function (data, type, row) {
      return `<a href="javascript:void(0);" class="btn btn-success btn-sm showTersediaSalesOrder" id="'+row[1]+'" >Detail</a>`; 
    }},
    {mRender: function (data, type, row) {
      return `(${row[6]}) ${row[7]}`; 
    }},

    {mRender: function (data, type, row) {
      return `<a href="javascript:void(0);" class="btn btn-success btn-sm editMaterial" id="${row[1]}" nama="${row[2]}">Edit</a> <a href="javascript:void(0);" class="btn btn-danger btn-sm delete" id="${row[1]}" nama="${row[2]}" >Delete</a>`; 
    }
  }
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

$('.addPurchaseOrder').on('click',function(){

    Swal.fire({
      title: `Tambah Invoice Masuk `,
      // html: `<input type="text" id="password" class="swal2-input" placeholder="Password baru">`,
      html:`<form id="form_add_data">
      <div class="form-group">
      <label for="kode">Nomor Invoice</label>
      <input type="text" class="form-control" id="kode" aria-describedby="kodeHelp" placeholder="Kode">
      </div>
      <div class="form-group">
      <label for="namaBarang">Mata uang</label>
      <input type="text" class="form-control" id="namaBarang" placeholder="Nama Tipe">
      </div>
      </form>`,
      confirmButtonText: 'Confirm',
      focusConfirm: false,
      preConfirm: () => {
        const kode = Swal.getPopup().querySelector('#kode').value
        const nama = Swal.getPopup().querySelector('#namaBarang').value
        if (!kode || !nama) {
          Swal.showValidationMessage('Silakan lengkapi data')
        }
        return {kode:kode, nama: nama }
      }
    }).then((result) => {
      $.ajax({
        type : "POST",
        url  : base_url+'/material/tambah_tipe',
        async : false,
        // dataType : "JSON",
        data : {kode:result.value.kode,nama:result.value.nama},
        success: function(data){
          dataTypeBarang()
          Swal.fire({
            position: 'center',
            icon: 'success',
            title: `Jenis barang berhasil ditambahkan.`,
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
  
    })
  })
