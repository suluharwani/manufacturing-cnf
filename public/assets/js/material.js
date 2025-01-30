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
      "url" :base_url+"material/listdataMaterialJoin" , // json datasource 
      "type": "post",  // method  , by default get
      // "async": false,
      "dataType": 'json',
      "data":{},
    },
    
    columns: [
      {},
      {mRender: function (data, type, row) {
    //   return  row[1]+" "+row[2]+"</br>"+"<a href=mailto:"+row[3]+">"+row[3]+"</a>";
        return `${row[3]} <br> <p style="color:blue;" onclick="checkHsCode('${row[8]}')">${row[8]}</p> `
      }},
      {mRender: function (data, type, row) {
        return row[2]
      }},
      {mRender: function (data, type, row) {
       return row[5]
     }},
     {mRender: function (data, type, row) {
       return `${row[4]}`; 
     }},
    {mRender: function (data, type, row) {
      return `(${row[6]}) ${row[7]}`; 
    }},

    {mRender: function (data, type, row) {
      return `<a href="javascript:void(0);" class="btn btn-success btn-sm editMaterial" id="${row[1]}" nama="${row[2]}" >Edit</a> <a href="javascript:void(0);" class="btn btn-danger btn-sm delete" id="${row[1]}" nama="${row[2]}" >Delete</a>`; 
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

$('.tambahJenisBarang').on('click',function(){

  Swal.fire({
    title: `Tambah Tipe/Jenis `,
      // html: `<input type="text" id="password" class="swal2-input" placeholder="Password baru">`,
    html:`<form id="form_add_data">
    <div class="form-group">
    <label for="kode">Kode</label>
    <input type="text" class="form-control" id="kode" aria-describedby="kodeHelp" placeholder="Kode">
    </div>
    <div class="form-group">
    <label for="namaBarang">Nama Jenis/Tipe</label>
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

$('.tambahSatuanBarang').on('click',function(){

  Swal.fire({
    title: `Tambah Satuan `,
      // html: `<input type="text" id="password" class="swal2-input" placeholder="Password baru">`,
    html:`<form id="form_add_data">
    <div class="form-group">
    <label for="kode">Kode</label>
    <input type="text" class="form-control" id="kode" aria-describedby="kodeHelp" placeholder="Kode">
    </div>
    <div class="form-group">
    <label for="namaSatuan">Nama Satuan</label>
    <input type="text" class="form-control" id="namaSatuan" placeholder="Nama Satuan">
    </div>
    </form>`,
    confirmButtonText: 'Confirm',
    focusConfirm: false,
    preConfirm: () => {
      const kode = Swal.getPopup().querySelector('#kode').value
      const nama = Swal.getPopup().querySelector('#namaSatuan').value
      if (!kode || !nama) {
        Swal.showValidationMessage('Silakan lengkapi data')
      }
      return {kode:kode, nama: nama }
    }
  }).then((result) => {
    $.ajax({
      type : "POST",
      url  : base_url+'/material/tambah_satuan',
      async : false,
        // dataType : "JSON",
      data : {kode:result.value.kode,nama:result.value.nama},
      success: function(data){
        dataSatuan()
        Swal.fire({
          position: 'center',
          icon: 'success',
          title: `Satuan ukuran barang berhasil ditambahkan.`,
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
dataTypeBarang()
function dataTypeBarang(){
  $.ajax({
    type : "POST",
    url  : base_url+"material/type_list",
    async : false,
    success: function(data){
     tableType(data);

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
function tableType(data){
  d = JSON.parse(data);
  console.log(d)
  let no = 1;
  let table = ''
  $.each(d, function(k, v){
    table+=     `<tr>`;
    table+=   `<td>${no++}</td>`;
    table+=   `<td>${d[k].kode}</td>`;
    table+=   `<td>${d[k].nama}</td>`;
    table+=   `<td><a href="javascript:void(0);" class="btn btn-warning btn-sm edit"  id="${d[k].id}" nama = "${d[k].nama}" code = "${d[k].kode}" >Edit</a> <a href="javascript:void(0);" class="btn btn-danger btn-sm delete"  id="${d[k].id}" nama = "${d[k].nama}" >Delete</a>`;
    table+=   `</tr>`

  })
  $('#isiType').html(table)
}
dataSatuan()
function dataSatuan(){
  $.ajax({
    type : "POST",
    url  : base_url+"material/satuan_list",
    async : false,
    success: function(data){
     tableSatuan(data);

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
function tableSatuan(data){
  d = JSON.parse(data);
  console.log(d)
  let no = 1;
  let table = ''
  $.each(d, function(k, v){
    table+=     `<tr>`;
    table+=   `<td>${no++}</td>`;
    table+=   `<td>${d[k].kode}</td>`;
    table+=   `<td>${d[k].nama}</td>`;
    table+=   `<td><a href="javascript:void(0);" class="btn btn-warning btn-sm edit"  id="${d[k].id}" nama = "${d[k].nama}" kode = "${d[k].kode}">Edit</a> <a href="javascript:void(0);" class="btn btn-danger btn-sm delete"  id="${d[k].id}" nama = "${d[k].nama}" >Delete</a>`;
    table+=   `</tr>`

  })
  $('#isiSatuan').html(table)
}

  //MATERIAL
$('.tambahMaterial').on('click', function() {
  $.when(
    $.ajax({
      url: base_url + '/material/type_list',
      method: 'POST',
            dataType: 'json' // Expecting JSON response
          }),
    $.ajax({
      url: base_url + '/material/satuan_list',
      method: 'POST',
            dataType: 'json' // Expecting JSON response
          })
    ).done(function(typesResponse, satuanUkuranResponse) {
        // Debugging: Log the responses to check their structure
             // Extract data
        const typesData = typesResponse[0]; // Array of types
        const satuanUkuranData = satuanUkuranResponse[0]; // Array of satuan ukuran

        if (Array.isArray(typesData) && Array.isArray(satuanUkuranData)) {
          let typeOptions = typesData.map(type => `<option value="${type.id}">${type.nama}</option>`).join('');
          let satuanUkuranOptions = satuanUkuranData.map(satuan => `<option value="${satuan.id}">${satuan.nama}</option>`).join('');

          Swal.fire({
            title: 'Tambah Material',
            html: `
            <form id="form_add_data">
            <div class="form-group">
            <label for="kode">Kode</label>
            <input type="text" class="form-control" id="kode" placeholder="Kode">
            </div>
            <div class="form-group">
            <label for="namaMaterial">Nama Material</label>
            <input type="text" class="form-control" id="namaMaterial" placeholder="Nama Material">
            </div>
            <div class="form-group">
            <label for="type">Type</label>
            <select class="form-control" id="type">
            ${typeOptions}
            </select>
            </div>
            <div class="form-group">
            <label for="satuanUkuran">Satuan Ukuran</label>
            <select class="form-control" id="satuanUkuran">
            ${satuanUkuranOptions}
            </select>
            </div>
            <div class="form-group">
            <label for="type">Import Export Status</label>
            <select class="form-control" id="kite">
            <option value="kite">KITE</option>
            <option value="non_kite">Non-KITE</option>
            </select>
            </div>
            </form>
            `,
            confirmButtonText: 'Confirm',
            focusConfirm: false,
            preConfirm: () => {
              const kode = Swal.getPopup().querySelector('#kode').value;
              const nama = Swal.getPopup().querySelector('#namaMaterial').value;
              const type = Swal.getPopup().querySelector('#type').value;
              const kite = Swal.getPopup().querySelector('#kite').value;
              const satuanUkuran = Swal.getPopup().querySelector('#satuanUkuran').value;

              if (!kode || !nama || !type || !satuanUkuran || !kite) {
                Swal.showValidationMessage('Silakan lengkapi data');
              }
              return {kite:kite, kode: kode, nama: nama, type: type, satuanUkuran: satuanUkuran };
            }
          }).then((result) => {
            if (result.isConfirmed) {
              $.ajax({
                type: "POST",
                url: base_url + '/material/tambah_material',
                async : false,
                data: {
                  kode: result.value.kode,
                  nama: result.value.nama,
                  type: result.value.type,
                  kite: result.value.kite,
                  satuanUkuran: result.value.satuanUkuran
                },
                success: function(data) {

                  Swal.fire({
                    position: 'center',
                    icon: 'success',
                    title: 'Material berhasil ditambahkan.',
                    showConfirmButton: false,
                    timer: 1500
                  });
                  $('#tabel_serverside').DataTable().ajax.reload();
                            // tabel()
                            // $('#tabel_serverside').dataTable( ).api().ajax.reload();
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
        } else {
          Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Data tidak dalam format yang diharapkan.',
            footer: '<a href="">Why do I have this issue?</a>'
          });
        }
      }).fail(function() {
        Swal.fire({
          icon: 'error',
          title: 'Error',
          text: 'Gagal memuat data.',
          footer: '<a href="">Why do I have this issue?</a>'
        });
      });
    });

    // Function to handle material editing
$(document).on('click', '.editMaterial', function() {
  const materialId = $(this).attr('id');

  $.when(
    $.ajax({
      url: base_url + '/material/type_list',
      method: 'POST',
      dataType: 'json'
    }),
    $.ajax({
      url: base_url + '/material/satuan_list',
      method: 'POST',
      dataType: 'json'
    }),
    $.ajax({
              url: base_url + '/material/get_material/' + materialId, // Endpoint to get material details
              method: 'GET',
              dataType: 'json'
            })
    ).done(function(typesResponse, satuanUkuranResponse, materialResponse) {
      const typesData = typesResponse[0]; 
      const satuanUkuranData = satuanUkuranResponse[0];
          const materialData = materialResponse[0]; // Material details

          if (Array.isArray(typesData) && Array.isArray(satuanUkuranData) && materialData) {
            let typeOptions = typesData.map(type => `<option value="${type.id}" ${type.id == materialData.type_id ? 'selected' : ''}>${type.nama}</option>`).join('');
            let satuanUkuranOptions = satuanUkuranData.map(satuan => `<option value="${satuan.id}" ${satuan.id == materialData.satuan_id ? 'selected' : ''}>${satuan.nama}</option>`).join('');

            Swal.fire({
              title: 'Edit Material',
              html: `
              <form id="form_edit_data">
              <div class="form-group">
              <label for="kode">Kode</label>
              <input type="text" class="form-control" id="kode" value="${materialData.kode}" placeholder="Kode">
              </div>
              <div class="form-group">
              <label for="kode">HSCODE</label>
              <input type="text" class="form-control" id="hscode" value="${materialData.hscode}" placeholder="Hscode">
              </div>
              <div class="form-group">
              <label for="namaMaterial">Nama Material</label>
              <input type="text" class="form-control" id="namaMaterial" value="${materialData.name}" placeholder="Nama Material">
              </div>
              <div class="form-group">
              <label for="type">Type</label>
              <select class="form-control" id="type">
              ${typeOptions}
              </select>
              </div>
              <div class="form-group">
              <label for="satuanUkuran">Satuan Ukuran</label>
              <select class="form-control" id="satuanUkuran">
              ${satuanUkuranOptions}
              </select>
              </div>
               <div class="form-group">
            <label for="type">Import Export Status</label>
            <select class="form-control" id="kite">
            <option value="kite" ${materialData.kite == 'kite' ? 'selected' : ''}>KITE</option>
            <option value="non_kite"${materialData.kite == 'non_kite' ? 'selected' : ''}>Non-KITE</option>
            </select>
            </div>
              </form>
              `,
              confirmButtonText: 'Update',
              focusConfirm: false,
              preConfirm: () => {
                const kode = Swal.getPopup().querySelector('#kode').value;
                const hscode = Swal.getPopup().querySelector('#hscode').value;
                const nama = Swal.getPopup().querySelector('#namaMaterial').value;
                const type = Swal.getPopup().querySelector('#type').value;
                const kite = Swal.getPopup().querySelector('#kite').value;
                const satuanUkuran = Swal.getPopup().querySelector('#satuanUkuran').value;

                if (!hscode||!kode || !nama || !type || !satuanUkuran|| !kite) {
                  Swal.showValidationMessage('Silakan lengkapi data');
                }
                return {kite:kite, id: materialId, kode: kode, nama: nama, type: type, satuanUkuran: satuanUkuran , hscode:hscode};
              }
            }).then((result) => {
              if (result.isConfirmed) {
                param = {
                  id: result.value.id,
                  kode: result.value.kode,
                  hscode: result.value.hscode,
                  nama: result.value.nama,
                  type: result.value.type,
                  kite: result.value.kite,
                  hscode: result.value.hscode,
                  satuanUkuran: result.value.satuanUkuran
                }
                $.ajax({
                  type: "POST",
                  url: base_url + '/material/materialUpdate',
                  data: {
                   param
                 },
                 success: function(data) {
                  $('#tabel_serverside').DataTable().ajax.reload();
                  Swal.fire({
                    position: 'center',
                    icon: 'success',
                    title: 'Material berhasil diperbarui.',
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
          } else {
            Swal.fire({
              icon: 'error',
              title: 'Error',
              text: 'Data tidak dalam format yang diharapkan.',
              footer: '<a href="">Why do I have this issue?</a>'
            });
          }
        }).fail(function() {
          Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Gagal memuat data.',
            footer: '<a href="">Why do I have this issue?</a>'
          });
        });
      });

  // Function to handle material deletion

$('#isiType').on('click','.delete',function(){
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
        url  : base_url+"material/typeDelete",
        async : false,
        data:{param:param},
        success: function(data){
          dataTypeBarang()
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
$('#isiSatuan').on('click','.delete',function(){
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
        url  : base_url+"material/satuanDelete",
        async : false,
        data:{param:param},
        success: function(data){
          dataSatuan()
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
$('#tabel_serverside').on('click','.delete',function(){
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
        url  : base_url+"material/delete",
        async : false,
        data:{param:param},
        success: function(data){
          $('#tabel_serverside').DataTable().ajax.reload();
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
$('#isiType').on('click','.edit',function(){
  let id = $(this).attr('id');
  let nama = $(this).attr('nama');
  let code = $(this).attr('code');

  Swal.fire({
    title: `Edit `,
    html: `<form id="form_edit_data">
    <div class="form-group">
    <label for="kode">Code</label>
    <input type="text" class="form-control" id="code" aria-describedby="code" placeholder="code" value= "${code}">

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
      const code = Swal.getPopup().querySelector('#code').value
      const name = Swal.getPopup().querySelector('#name').value
      if (!name||!code) {
        Swal.showValidationMessage('Silakan lengkapi data')
      }
      
      return {name:name, code:code  }
    }
  }).then((result) => {
    params = {name:result.value.name,id:id,code:result.value.code}
    $.ajax({
      type : "POST",
      url  : base_url+'/material/typeUpdate',
      async : false,
      // dataType : "JSON",
      data : {params},
      success: function(data){
        dataTypeBarang()
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
$('#isiSatuan').on('click','.edit',function(){
  let id = $(this).attr('id');
  let nama = $(this).attr('nama');
  let kode = $(this).attr('kode');

  Swal.fire({
    title: `Edit Warehouse `,
    html: `<form id="form_edit_data">
    <div class="form-group">
    <label for="kode">Code</label>
    <input type="text" class="form-control" id="code" aria-describedby="code" placeholder="code" value= "${kode}">

    </div>
    <div class="form-group">
    <label for="namaWarehouse">WH Name</label>
    <input type="text" class="form-control" id="name" aria-describedby="name" placeholder="name" value= "${nama}">

    </div>
    </form>
    `,
    confirmButtonText: 'Update',
    focusConfirm: false,
    preConfirm: () => {
      const code = Swal.getPopup().querySelector('#code').value
      const name = Swal.getPopup().querySelector('#name').value
      if (!name||!code) {
        Swal.showValidationMessage('Silakan lengkapi data')
      }
      
      return {name:name, code:code  }
    }
  }).then((result) => {
    params = {name:result.value.name,id:id,code:result.value.code}
    $.ajax({
      type : "POST",
      url  : base_url+'/material/satuanUpdate',
      async : false,
      // dataType : "JSON",
      data : {params},
      success: function(data){
        dataSatuan()
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