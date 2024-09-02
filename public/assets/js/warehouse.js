var loc = window.location;
var base_url = loc.protocol + "//" + loc.hostname + (loc.port? ":"+loc.port : "") + "/";


tabel();
function tabel(){
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
      "url" :base_url+"material/listdataMaterialJoin" , // json datasource 
      "type": "post",  // method  , by default get
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
     return `<a href="javascript:void(0);" class="btn btn-success btn-sm showPurchaseOrder" id="'+row[1]+'" >Detail</a>`; 
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
    return ``; 
    }},

    {mRender: function (data, type, row) {
    return `<a href="javascript:void(0);" class="btn btn-success btn-sm showPurchaseOrder" id="'+row[1]+'" >Detail</a>`; 
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
};

$('.tambahWarehouse').on('click',function(){

    Swal.fire({
      title: `Tambah Warehouse `,
      // html: `<input type="text" id="password" class="swal2-input" placeholder="Password baru">`,
      html:`<form id="form_add_data">
      <div class="form-group">
      <label for="kode">Warehouse</label>
      <input type="text" class="form-control" id="location" aria-describedby="locationHelp" placeholder="location">
      </div>
      <div class="form-group">
      <label for="namaWarehouse">Nama Warehouse</label>
      <input type="text" class="form-control" id="namaWarehouse" placeholder="Nama Tipe">
      </div>
      </form>`,
      confirmButtonText: 'Confirm',
      focusConfirm: false,
      preConfirm: () => {
        const location = Swal.getPopup().querySelector('#location').value
        const name = Swal.getPopup().querySelector('#namaWarehouse').value
        if (!location || !name) {
          Swal.showValidationMessage('Silakan lengkapi data')
        }
        return {location:location, name: name }
      }
    }).then((result) => {
      $.ajax({
        type : "POST",
        url  : base_url+'/WarehouseController/buat_gudang_baru',
        async : false,
        // dataType : "JSON",
        data : {location:result.value.location,name:result.value.name},
        success: function(data){
          dataGudang()
          console.log(data)
          Swal.fire({
            position: 'center',
            icon: 'success',
            title: `Warehouse berhasil ditambahkan.`,
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
  

  dataGudang()
  function dataGudang(){
    $.ajax({
      type : "POST",
      url  : base_url+"warehousecontroller/gudang_list",
      async : false,
      success: function(data){
       tableGudang(data);
    
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
  }
  function tableGudang(data){
    d = JSON.parse(data);
    console.log(d)
    let no = 1;
    let table = ''
    $.each(d, function(k, v){
            table+=     `<tr>`;
                table+=   `<td>${no++}</td>`;
                table+=   `<td>${d[k].location}</td>`;
                table+=   `<td>${d[k].name}</td>`;
                table+=   `<td><a href="javascript:void(0);" class="btn btn-warning btn-sm editType"  id="${d[k].id}" nama = "${d[k].nama}">Edit</a> <a href="javascript:void(0);" class="btn btn-danger btn-sm deleteType"  id="${d[k].id}" nama = "${d[k].nama}" >Delete</a>`;
            table+=   `</tr>`
 
          })
   $('#isiGudang').html(table)
  }
