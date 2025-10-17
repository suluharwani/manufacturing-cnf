var loc = window.location;
var base_url = loc.protocol + "//" + loc.hostname + (loc.port? ":"+loc.port : "") + "/";


select_level();
function select_level(){
  let selOpts = '<option value="all">All data</option>'+
  "<option value='1'>Administrator</option>"+
  "<option value='2'>Operator</option>";

  $('#select_level_user').html(selOpts);
}
$("#select_level_user").change(function(){
  $('#tabel_serverside').dataTable().fnDestroy();
  tabel();
});
tabel();
function tabel(){
  var level = $('#select_level_user').val();
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
      "url" :base_url+"user/listdata_user" , // json datasource 
      "type": "post",  // method  , by default get
      "data":{level:level},
    },
    columns: [
    {},
    {mRender: function (data, type, row) {
      return  row[1]+" "+row[2]+"</br>"+"<a href=mailto:"+row[3]+">"+row[3]+"</a>";
    }},

    {mRender: function (data, type, row) {
      if (row[5] == 1) {
        return  `Administrator`;

      }else if (row[5] == 2) {
        return  `Operator`;

      }else{
        return 'Belum Ada'
      }
    }},
    {mRender: function (data, type, row) {
      if (row[6] == 1) {
        return  `Aktif`;

      }else if (row[6] == 0) {
        return  `Tidak Aktif`;
      }else{
        return 'Belum Ada'
      }
    }},

    {mRender: function (data, type, row) {
      if (row[6] == 1) {
        if (row[5] == 5) {
          return   '<a href="javascript:void(0);" class="btn btn-info btn-sm resetPassword"  id="'+row[4]+'" >Reset Password</a> <a href="javascript:void(0);" class="btn btn-warning btn-sm nonaktifkanstatus"  id="'+row[4]+'" >Nonaktifkan</a> <a href="javascript:void(0);" class="btn btn-danger btn-sm btn_hapus_user" id="'+row[4]+'" nama = "'+row[1]+' '+row[2]+'">Hapus</a>';
        }else{
          return   '<a href="javascript:void(0);" class="btn btn-info btn-sm resetPassword"  id="'+row[4]+'" >Reset Password</a> <a href="javascript:void(0);" class="btn btn-warning btn-sm nonaktifkanstatus"  id="'+row[4]+'" >Nonaktifkan</a> <a href="javascript:void(0);" class="btn btn-info btn-sm ubah_level_user"  id="'+row[4]+'" nama = "'+row[1]+' '+row[2]+'">Ubah Level</a> <a href="javascript:void(0);" class="btn btn-danger btn-sm btn_hapus_user" id="'+row[4]+'" nama = "'+row[1]+' '+row[2]+'">Hapus</a>';
        }
      }else{
        if (row[5] == 5) {
          return   '<a href="javascript:void(0);" class="btn btn-info btn-sm resetPassword"  id="'+row[4]+'" >Reset Password</a> <a href="javascript:void(0);" class="btn btn-success btn-sm aktifkanstatus"  id="'+row[4]+'" >Aktifkan</a> <a href="javascript:void(0);" class="btn btn-danger btn-sm btn_hapus_user"  id="'+row[4]+'" nama = "'+row[1]+' '+row[2]+'">Hapus</a>';
        }else{
          return   '<a href="javascript:void(0);" class="btn btn-info btn-sm resetPassword"  id="'+row[4]+'" >Reset Password</a> <a href="javascript:void(0);" class="btn btn-success btn-sm aktifkanstatus"  id="'+row[4]+'" >Aktifkan</a> <a href="javascript:void(0);" class="btn btn-info btn-sm ubah_level_user"  id="'+row[4]+'" nama = "'+row[1]+' '+row[2]+'">Ubah Level</a> <a href="javascript:void(0);" class="btn btn-danger btn-sm btn_hapus_user"  id="'+row[4]+'" nama = "'+row[1]+' '+row[2]+'">Hapus</a>';

        }
      }
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
$('#tabel_serverside').on('click','.resetPassword',function(){
  let id = $(this).attr('id');

  Swal.fire({
    title: `Set Password `,
    html: `<input type="text" id="password" class="swal2-input" placeholder="Password baru">`,
    confirmButtonText: 'Confirm',
    focusConfirm: false,
    preConfirm: () => {
      const password = Swal.getPopup().querySelector('#password').value
      if (!password) {
        Swal.showValidationMessage('Silakan lengkapi data')
      }
      return {password: password }
    }
  }).then((result) => {
    $.ajax({
      type : "POST",
      url  : base_url+'/user/reset_password',
      async : false,
      // dataType : "JSON",
      data : {id:id,password:result.value.password},
      success: function(data){
        $('#tabel_serverside').DataTable().ajax.reload();
        Swal.fire({
          position: 'center',
          icon: 'success',
          title: `Password berhasil diubah menjadi ${result.value.password}.`,
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
});

$('#tabel_serverside').on('click','.aktifkanstatus',function(){
  let id = $(this).attr('id');
  let status = 1;
  $.ajax({
    type : "POST",
    url  : base_url+"/user/ubah_status_user",
    async : false,
    data:{id:id,status:status},
    success: function(data){
      $('#tabel_serverside').DataTable().ajax.reload();
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
$('#tabel_serverside').on('click','.nonaktifkanstatus',function(){
  let id = $(this).attr('id');
  let status = 0;
  $.ajax({
    type : "POST",
    url  : base_url+"/user/ubah_status_user",
    async : false,
    data:{id:id,status:status},
    success: function(data){
      $('#tabel_serverside').DataTable().ajax.reload();
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
function valueChecked(a){
  if (a == 1) {
    $('#valueChecked').html("Administrator");
  }else if (a == 2) {
    $('#valueChecked').html("Operator");
  }else{
    $('#valueChecked').html("");
  }

}
$('#tabel_serverside').on('click','.ubah_level_user',function(){
  let id = $(this).attr('id');
  let nama = $(this).attr('nama');

  Swal.fire({
    title: `Ubah level ${nama}`,
    // html: `<input type="text" id="password" class="swal2-input" placeholder="Password baru">`,
    html:`<div class="btn-group btn-group-toggle" data-toggle="buttons">
    <label class="btn btn-success active">
    <input type="radio" name="options" onclick="valueChecked(1)" value = "1" class = "level_user" autocomplete="off"> Administrator
    </label>
    &nbsp;
    <label class="btn btn-primary">
    <input type="radio" name="options" onclick="valueChecked(2)"  value = "2" class = "level_user" autocomplete="off"> Operator
    </label>
    </div>
    <div>
    <span id = "valueChecked">
    </div>`,
    confirmButtonText: 'Confirm',
    focusConfirm: false,
    preConfirm: () => {
      const level = Swal.getPopup().querySelector('input[name="options"]:checked').value

      return {level:level}
    }
  }).then((result) => {
    $.ajax({
      type : "POST",
      url  : base_url+'/user/ubah_level_user',
      async : false,
      // dataType : "JSON",
      data : {level:result.value.level,id:id},
      success: function(data){
        $('#tabel_serverside').DataTable().ajax.reload();
        Swal.fire({
          position: 'center',
          icon: 'success',
          title: `Level ${nama} berhasil diubah.`,
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
$('#tabel_serverside').on('click','.btn_hapus_user',function(){
  id = $(this).attr('id');
  nama = $(this).attr('nama');
  Swal.fire({
    title: 'Apakah anda yakin?',
    text: "User "+nama+" akan dihapus!",
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#3085d6',
    cancelButtonColor: '#d33',
    confirmButtonText: 'Ya, hapus user!'
  }).then((result) => {
    if (result.isConfirmed) {
      $.ajax({
        type  : 'post',
        url   : base_url+'/user/hapus_user',
        async : false,
        // dataType : 'json',
        data:{id:id, nama:nama},
        success : function(data){
          //reload table
          $('#tabel_serverside').DataTable().ajax.reload();
          Swal.fire(
            'Deleted!',
            'User '+nama+' telah dihapus.',
            'success'
            )
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
  })
})
$('.tambah_user').on('click',function(){

  Swal.fire({
    title: `Tambah admin `,
    // html: `<input type="text" id="password" class="swal2-input" placeholder="Password baru">`,
    html:`<form id="form_add_data">
    <div class="form-group">
    <label for="namaDepan">Nama Depan</label>
    <input type="text" class="form-control" id="namaDepan" aria-describedby="namaDepanHelp" placeholder="Nama Depan">
    </div>
    <div class="form-group">
    <label for="namaBelakang">Nama Belakang</label>
    <input type="text" class="form-control" id="namaBelakang" aria-describedby="namaBelakangHelp" placeholder="Nama Belakang">
    </div>
    <div class="form-group">
    <label for="email">Email address</label>
    <input type="email" class="form-control" id="email" aria-describedby="emailHelp" placeholder="Enter email">
    </div>
    <div class="form-group">
    <label for="password">Password</label>
    <input type="text" class="form-control" id="password" placeholder="Password">
    </div>
    </form>`,
    confirmButtonText: 'Confirm',
    focusConfirm: false,
    preConfirm: () => {
      const namaDepan = Swal.getPopup().querySelector('#namaDepan').value
      const namaBelakang = Swal.getPopup().querySelector('#namaBelakang').value
      const email = Swal.getPopup().querySelector('#email').value
      const password = Swal.getPopup().querySelector('#password').value
      if (!email || !password) {
        Swal.showValidationMessage('Silakan lengkapi data')
      }
      return {email:email, password: password , namaDepan:namaDepan, namaBelakang:namaBelakang}
    }
  }).then((result) => {
    $.ajax({
      type : "POST",
      url  : base_url+'/user/tambah_admin',
      async : false,
      // dataType : "JSON",
      data : {email:result.value.email,password:result.value.password, namaDepan:result.value.namaDepan, namaBelakang:result.value.namaBelakang},
      success: function(data){
        $('#tabel_serverside').DataTable().ajax.reload();
        Swal.fire({
          position: 'center',
          icon: 'success',
          title: `Admin berhasil ditambahkan.`,
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
