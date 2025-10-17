var loc = window.location;
var base_url = loc.protocol + "//" + loc.hostname + (loc.port? ":"+loc.port : "") + "/";


$('.restoreData').on('click',function(){
tableRestoreData()
$('.modal_restore').modal('show')


})
$('#isiGudang').on('click','.editData',function(){
  let id = $(this).attr('id');
  let nama = $(this).attr('nama');
  let location = $(this).attr('location');

  Swal.fire({
    title: `Edit Warehouse `,
    html: `<form id="form_edit_data">
      <div class="form-group">
      <label for="kode">WH Location</label>
      <input type="text" class="form-control" id="location" aria-describedby="location" placeholder="location" value= "${location}">

      </div>
      <div class="form-group">
      <label for="namaWarehouse">WH Name</label>
      <input type="text" class="form-control" id="name" aria-describedby="name" placeholder="name" value= "${nama}">

      </div>
      </form>
    `,
    confirmButtonText: 'Confirm',
    focusConfirm: false,
    preConfirm: () => {
      const location = Swal.getPopup().querySelector('#location').value
      const name = Swal.getPopup().querySelector('#name').value
      if (!name||!location) {
        Swal.showValidationMessage('Silakan lengkapi data')
      }
      
      return {name:name, location:location  }
    }
  }).then((result) => {
    params = {name:result.value.name,id:id,location:result.value.location}
    $.ajax({
      type : "POST",
      url  : base_url+'/warehouseController/update',
      async : false,
      // dataType : "JSON",
      data : {params},
      success: function(data){
        dataGudang()
        Swal.fire({
          position: 'center',
          icon: 'success',
          title: `Halaman ${nama} berhasil diubah menjadi ${result.value.name}.`,
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
function tableRestoreData(){
$('#tabelRestore').trigger("reset");
let isi = '' 
$.ajax({
    type : "POST",
    url  : base_url+"warehouseController/deletedData",
    async : false,
    dataType : 'json',
    data:{},
    success: function(data){
      let no = 1;
      isi+='<thead>'+
      '<tr>'+
      '<th scope="col" align="center" width="5%">#</th>'+
      '<th scope="col" align="center">Location</th>'+
      '<th scope="col" align="center">Name</th>'+
      '<th scope="col" align="center">Action</th>'+
      '</tr>'+
      '</thead>'+
      '<tbody>';
      $.each(data, function(k, v)
      {
        console.log(data[k].page)
        isi +=  '<tr>'+
        '<td scope="row" align="center">'+ no++ +'</td>'+
        '<td align="left">'+data[k].location+'</td>'+
        '<td align="left">'+data[k].name+'</td>'+
        '<td align="left"><a href="javascript:void(0);" class="btn btn-info btn-sm restoreData"  id="'+data[k].id+'" nama = "'+data[k].name+'" location = "'+data[k].location+'" >Restore</a> <a href="javascript:void(0);" class="btn btn-danger btn-sm purgeData"  id="'+data[k].id+'" nama = "'+data[k].name+'"  >Purge</a></td>'+
        '</tr>';

      });
      isi+='</tbody>'

    }
  })
$('#tabelRestore').html(isi)
}
$('#tabelRestore').on('click','.purgeData',function(){
  id = $(this).attr('id');
  nama = $(this).attr('nama');
  Swal.fire({
    title: 'Apakah anda yakin?',
    text: ""+nama+" akan dihapus permanen!",
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#3085d6',
    cancelButtonColor: '#d33',
    confirmButtonText: 'Ya, hapus!'
  }).then((result) => {
    if (result.isConfirmed) {
      param = {id:id, name:nama}
      data = ''
      $.ajax({
        type : "POST",
        url  : base_url+"warehouseController/purgeData",
        async : false,
        data:{param:param},
        success: function(data){
         tableRestoreData()
          Swal.fire(
            'Deleted!',
            ''+nama+' telah dihapus.',
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
$('#tabelRestore').on('click','.restoreData',function(){
  id = $(this).attr('id');
  nama = $(this).attr('nama');
  param = {id:id, name:nama}

  Swal.fire({
    title: 'Apakah anda yakin?',
    text: "Halaman "+nama+" akan dikembalikan!",
    icon: 'info',
    showCancelButton: true,
    confirmButtonColor: '#3085d6',
    cancelButtonColor: '#d33',
    confirmButtonText: 'Ya, restore halaman!'
  }).then((result) => {
    if (result.isConfirmed) {
      $.ajax({
        type  : 'post',
        url   : base_url+'/warehouseController/restoreData',
        async : false,
        // dataType : 'json',
        data:{param:param},
        success : function(data){
          //reload table
          tableRestoreData()
          dataGudang()
          Swal.fire(
            'Restored!',
            'Halaman '+nama+' telah dikembalikan.',
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

$('.tambahWarehouse').on('click',function(){

    Swal.fire({
      title: `Tambah Warehouse `,
      // html: `<input type="text" id="password" class="swal2-input" placeholder="Password baru">`,
      html:`<form id="form_add_data">
      <div class="form-group">
      <label for="location">WH Location</label>
      <input type="text" class="form-control" id="location" aria-describedby="locationHelp" placeholder="location">
      </div>
      <div class="form-group">
      <label for="namaWarehouse">WH Name</label>
      <input type="text" class="form-control" id="namaWarehouse" placeholder="Name">
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
        url  : base_url+'/warehouseController/create',
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
      url  : base_url+"warehouseController/gudang_list",
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
                table+=   `<td><a href="javascript:void(0);" class="btn btn-warning btn-sm editData" location = "${d[k].location}"  id="${d[k].id}" nama = "${d[k].name}">Edit</a> <a href="javascript:void(0);" class="btn btn-danger btn-sm delete"  id="${d[k].id}" nama = "${d[k].name}" >Delete</a>`;
            table+=   `</tr>`
 
          })
   $('#isiGudang').html(table)
  }
$('#isiGudang').on('click','.delete',function(){
  id = $(this).attr('id');
  nama = $(this).attr('nama');
  Swal.fire({
    title: 'Apakah anda yakin?',
    text: ""+nama+" akan dihapus!",
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#3085d6',
    cancelButtonColor: '#d33',
    confirmButtonText: 'Ya, hapus!'
  }).then((result) => {
    if (result.isConfirmed) {
      param = {id:id, name:nama}
      data = ''
      $.ajax({
        type : "POST",
        url  : base_url+"warehouseController/delete",
        async : false,
        data:{param:param},
        success: function(data){
          dataGudang()
          Swal.fire(
            'Deleted!',
            ''+nama+' telah dihapus.',
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