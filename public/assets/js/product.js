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
      "url" :base_url+"product/listdataProdukJoin" , // json datasource 
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
     return `<img src="${base_url + 'assets/upload/thumb/' + row[4]}" alt="${row[2]}" style="height:45px; cursor: pointer;" onclick="showImagePopup('${base_url + 'assets/upload/image/' + row[4]}', '${row[2]}')">`;
    }},
    {mRender: function (data, type, row) {
     return row[6]
    }},
    {mRender: function (data, type, row) {
       return `<a href="javascript:void(0);" class="btn btn-success btn-sm createBom" id="${row[1]}" >BoM</a>
               <a href="${base_url}breakdownBoM/${row[1]}" target="_blank" class="btn btn-success btn-sm breakdownBom" id="${row[1]}" >Breakdown BoM</a>
               <a href="javascript:void(0);" class="btn btn-success btn-sm createBom" id="${row[1]}" >Labour Cost</a>
               <a href="javascript:void(0);" class="btn btn-warning btn-sm createDesign" id="${row[1]}" >Design</a>
               <a href="javascript:void(0);" class="btn btn-primary btn-sm createFile" id="${row[1]}" >File</a>
                `; 
     }},
    {mRender: function (data, type, row) {
     return `<a href="javascript:void(0);" class="btn btn-success btn-sm trackProduct" id="'+row[1]+'" >Track</a>`; 
    }},

    {mRender: function (data, type, row) {
     return `<a href="javascript:void(0);" class="btn btn-warning btn-sm deleteProduct" id="'+row[1]+'" >Edit</a>
             <a href="javascript:void(0);" class="btn btn-danger btn-sm deleteProduct" id="'+row[1]+'" >Delete</a>`; 
    }},
   
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

function showImagePopup(src, alt) {
    Swal.fire({
        title: alt,
        imageUrl: src,
        imageAlt: alt,
        showCloseButton: true,
        imageWidth: '100%', // Sesuaikan lebar gambar sesuai kebutuhan
        imageHeight: 'auto',
        background: '#fff', // Warna background modal
        confirmButtonText: 'Close'
    });
}
 function selectCat(id_cat=null){
      $.ajax({
        type : "POST",
        url  : base_url+"product/cat_list",
        async : true,
        dataType : 'json',
        success: function(data){
          let selOpts = '';
          $.each(data, function(k, v)
          {
            var id = data[k].id;
            var nama = data[k].nama;

            selOpts += "<option value='"+id+"'>"+nama+"</option>";

        });
          $('.select_cat').html(selOpts);
          if (id_cat!=null) {
              $('.select_cat option[value='+id_cat+']').attr('selected','selected');
          }
      }
  });
  }
$('.addFinishedGoods').on('click',function(){ 
  selectCat()
  $('.modalTambahData').modal('show')
})
$('#file').change(function(){
  $('.uploadBtn').html('Upload');
  $('.uploadBtn').prop('disabled', false);
  $('.uploadBtn').addClass("btn-danger");
  $('.uploadBtn').removeClass("btn-success");
  $('#picture').val('');
  const file = this.files[0];
  if (file){
    let reader = new FileReader();
    reader.onload = function(event){
      $('#ajaxImgUpload').attr('src', event.target.result).width(300);
    }
    reader.readAsDataURL(file);
  }
});
$('.reset').on('click',function(){
  $('#form').trigger("reset");
  $('.uploadBtn').html('Upload');
  $('.uploadBtn').prop('disabled', false);
  $('.uploadBtn').addClass("btn-danger");
  $('.uploadBtn').removeClass("btn-success");
  $('#ajaxImgUpload').attr('src', 'https://via.placeholder.com/1080');
})


$('.uploadBtn').on('click',function(){

  upload()

})
function upload(){
  input = $('#file').prop('files')[0];

  param = ''
  data = new FormData();
          // data['file'] = input;
  data.append('file', input);
  data.append('param', param);
  data.append('data', '');
  $('.uploadBtn').html('Uploading ...');
  $('.uploadBtn').attr('disabled');
  if (!input) {
    alert("Choose File");
    $('.uploadBtn').html('Upload');
    $('.uploadBtn').prop('disabled', false);
  } else {
    $.ajax({
     type : "POST",
     enctype: 'multipart/form-data',
     url  : base_url+"product/upload",
     async : false,
     processData: false,
     contentType: false,
     data:data,
     success: function (res) {
      if (res.success == true) {
        $('.uploadBtn').html('Uploaded!');
        $('.uploadBtn').prop('disabled', true);
        $('.uploadBtn').removeClass("btn-danger");
        $('.uploadBtn').addClass("btn-success");
        $('#picture').val(res.picture);

                                // $('#ajaxImgUpload').attr('src', 'https://via.placeholder.com/300');
        $('#alertMsg').addClass("text-success");
        $('#alertMsg').html(res.msg);
        $('#alertMessage').show();
      } else if (res.success == false) {
        $('#alertMsg').addClass("text-danger");
        $('#alertMsg').html(res.msg);
        $('#picture').val('');

        $('#alertMessage').show();
        $('.uploadBtn').html('Upload Failed!');
        $('.uploadBtn').prop('disabled', false);
        $('.uploadBtn').addClass("btn-danger");
        $('.uploadBtn').removeClass("btn-success");

      }
      setTimeout(function () {
        $('#alertMsg').html('');
        $('#alertMessage').hide();
      }, 4000);

                            // document.getElementById("form").reset();
    }
  });
  }
}

$('.save').on('click',function(){
  if ($('#picture').val()=='') {
    Swal.fire({
      title: 'Gambar belum diupload!',
      showDenyButton: true,
      showCancelButton: true,
      confirmButtonText: 'Upload gambar lalu simpan',
      denyButtonText: `Tanpa Gambar`,
    }).then((result) => {
  /* Read more about isConfirmed, isDenied below */
      if (result.isConfirmed) {
        upload()
      } else if (result.isDenied) {
        simpan()
      }
    })
  }else{
    simpan()

  }
})


function simpan(){
  form = $('.form').serializeArray()
  let data = {}
  $.each(form, function(i, field){
    data[field.name] =  field.value;

  });
  if (typeof document.getElementsByName("text")[0] !== 'undefined') {
  data['text'] = document.getElementsByName("text")[0].value
}
  param=''
  $.ajax({
    type  : 'post',
    url  : base_url+"product/create",
    async : false,
        // dataType : 'json',
    data:{data:data, param:param},
    success : function(res){
          //reload table
      
      $('.uploadBtn').html('Upload');
      $('.uploadBtn').prop('disabled', false);
      $('.modalTambahData').modal('hide');  
      $('#ajaxImgUpload').removeAttr('src');
      $('.uploadBtn').addClass("btn-danger");
      $('.uploadBtn').removeClass("btn-success");

      $('#form').trigger("reset");
      $('#tabelProduct').DataTable().ajax.reload();
      Swal.fire(
        'Berhasil!',
        ''+data['nama']+' telah ditambahkan.',
        'success'
        )
    },
    error: function(xhr){
      $('.uploadBtn').html('Upload');
      $('.uploadBtn').prop('disabled', false);
      $('.uploadBtn').addClass("btn-danger");
      $('.uploadBtn').removeClass("btn-success");
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

$('.addCat').on('click',function(){

  Swal.fire({
    title: `Tambah Kategori `,
      // html: `<input type="text" id="password" class="swal2-input" placeholder="Password baru">`,
    html:`<form id="form_add_data">
    <div class="form-group">
    <label for="namaBarang">Nama Jenis/Tipe</label>
    <input type="text" class="form-control" id="nama" placeholder="Nama Kategori">
    </div>
    </form>`,
    confirmButtonText: 'Confirm',
    focusConfirm: false,
    preConfirm: () => {
      const nama = Swal.getPopup().querySelector('#nama').value
      if ( !nama) {
        Swal.showValidationMessage('Silakan lengkapi data')
      }
      return {nama: nama }
    }
  }).then((result) => {
    $.ajax({
      type : "POST",
      url  : base_url+'/product/addcat',
      async : false,
        // dataType : "JSON",
      data : {kode:result.value.kode,nama:result.value.nama},
      success: function(data){
        dataCat()
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
dataCat()
function dataCat(){
  $.ajax({
    type : "POST",
    url  : base_url+"product/cat_list",
    async : false,
    success: function(data){
     tableCat(data);

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
function tableCat(data){
  d = JSON.parse(data);
  let no = 1;
  let table = ''
  $.each(d, function(k, v){
    table+=     `<tr>`;
    table+=   `<td>${no++}</td>`;
    table+=   `<td>${d[k].nama}</td>`;
    table+=   `<td><a href="javascript:void(0);" class="btn btn-warning btn-sm edit"  id="${d[k].id}" nama = "${d[k].nama}" >Edit</a> <a href="javascript:void(0);" class="btn btn-danger btn-sm delete"  id="${d[k].id}" nama = "${d[k].nama}" >Delete</a>`;
    table+=   `</tr>`

  })
  $('#productCat').html(table)
}
// Event listener untuk menambah baris produk baru

// Event listener untuk menghapus baris produk
$(document).on('click', '.remove-product', function () {
  $(this).closest('tr').remove();
});
function getOrderMaterial(materialOptions, idProduct) {
  let orderMaterialHtml = '';

  $.ajax({
    type: "POST",
    url: base_url + "product/getBom",
    async: false,
    data: { idProduct: idProduct },
    success: function(data) {
      const parsedData = JSON.parse(data);
      if (parsedData.length > 0) {

        orderMaterialHtml += `
          <form id="form_order_list">
            <table class="table table-bordered" id="order_material_table">
              <thead>
                <tr>
                  <th>Nama Material</th>
                  <th>Ukuran</th>
                  <th>Aksi</th>
                </tr>
              </thead>
              <tbody>
        `;

        // Menampilkan data yang ada di response JSON
        parsedData.forEach((item) => {
          let materialOptionsHtml = materialOptions.split('</option>'); // Memecah opsi material
          materialOptionsHtml = materialOptionsHtml.map(option => {
            if (option.includes('value="' + item.id_material + '"')) {
              return option.replace('<option', '<option selected="selected"');
            }
            return option;
          });

          orderMaterialHtml += `
            <tr>
              <td>
                <select class="form-control" name="id_material[]">
                  ${materialOptionsHtml.join('</option>')} <!-- Gabungkan kembali pilihan -->
                </select>
              </td>
              <td>
                <input type="number" class="form-control material-penggunaan" name="penggunaan[]" value="${item.penggunaan}" placeholder="Masukkan ukuran">
              </td>
              <td>
                <button type="button" class="btn btn-danger btn-sm remove-material">Hapus</button>
              </td>
            </tr>
          `;
        });

        orderMaterialHtml += `
              </tbody>
            </table>
            <button type="button" class="btn btn-primary btn-sm" id="addProduct">Tambah Material</button>
          </form>
        `;
      } else {
        orderMaterialHtml += `
          <form id="form_order_list">
            <table class="table table-bordered" id="order_material_table">
              <thead>
                <tr>
                  <th>Nama Material</th>
                  <th>Ukuran</th>
                  <th>Aksi</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>
                    <select class="form-control" name="id_material[]">
                      ${materialOptions}
                    </select>
                  </td>
                  <td>
                    <input type="number" class="form-control material-penggunaan" name="penggunaan[]" placeholder="Masukkan ukuran">
                  </td>
                  <td>
                    <button type="button" class="btn btn-danger btn-sm remove-material">Hapus</button>
                  </td>
                </tr>
              </tbody>
            </table>
            <button type="button" class="btn btn-primary btn-sm" id="addProduct">Tambah Material</button>
          </form>
        `;
      }
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

  return orderMaterialHtml;
}

// Event listener untuk tombol "Tambah Material"
$(document).on('click', '#addProduct', async function() {
  try {
    // Menunggu materialOptions yang diambil dari getMaterialOption
    let materialOptions = await getMaterialOption();
    console.log(materialOptions);

    let newRow = `
      <tr>
        <td>
          <select class="form-control material-select" name="id_material[]">
            ${materialOptions}
          </select>
        </td>
        <td>
          <input type="number" class="form-control material-penggunaan" name="penggunaan[]" placeholder="Masukkan ukuran">
        </td>
        <td>
          <button type="button" class="btn btn-danger btn-sm remove-material">Hapus</button>
        </td>
      </tr>
    `;

    // Menambahkan baris baru ke tabel
    $('#order_material_table tbody').append(newRow);
  } catch (error) {
    // Menangani error jika ada
    Swal.fire({
      icon: 'error',
      title: 'Oops...',
      text: error,
      footer: '<a href="">Why do I have this issue?</a>'
    });
  }
});


// Event listener untuk tombol "Hapus" di setiap baris
$(document).on('click', '.remove-material', function() {
  $(this).closest('tr').remove(); // Menghapus baris material
});



function getMaterialOption() {
  return new Promise((resolve, reject) => {
    let materialOptions = '';

    $.ajax({
      type: 'GET',
      url: base_url + 'product/getMaterial', // Endpoint untuk mendapatkan produk
      success: function (response) {
        // Buat opsi produk dari data yang diterima
        response.material.forEach(material => {
          materialOptions += `<option value="${material.id}">${material.name} - ${material.nama_satuan}(${material.kode_satuan})</option>`;
        });

        // Resolving the promise with the materialOptions after success
        resolve(materialOptions);
      },
      error: function (xhr) {
        // Reject the promise in case of error
        reject('Terjadi kesalahan saat mengambil daftar produk');
      }
    });
  });
}

$(document).on('click', '.createBom', function () {
  const idProduct = $(this).attr('id'); // Mengambil ID order dari atribut id

  // Buat AJAX request untuk mengambil daftar produk
  $.ajax({
    type: 'GET',
    url: base_url + 'product/getMaterial', // Endpoint untuk mendapatkan produk
    success: function (response) {
      let materialOptions = '';

      // Buat opsi produk dari data yang diterima
      response.material.forEach(material => {
        materialOptions += `<option value="${material.id}">${material.name} - ${material.nama_satuan}(${material.kode_satuan})</option>`;
      });
      orderMaterialHtml = getOrderMaterial(materialOptions,idProduct);
    

      // Tampilkan modal dengan form produk
      Swal.fire({
        title: 'Bill of Material',
        html: orderMaterialHtml,
        width: '800px',
        showCancelButton: true,
        confirmButtonText: 'Simpan',
        cancelButtonText: 'Batal',
        preConfirm: () => {
          // Ambil data dari form
          const formData = $('#form_order_list').serializeArray();
          // Konversi form data menjadi format array of objects yang lebih mudah dibaca
          const processedData = convertFormDataToObject(formData);
          return processedData;
        }
      }).then((result) => {
        if (result.isConfirmed) {
          const data = result.value;

          // Kirim data produk ke server untuk disimpan
          $.ajax({
            type: 'POST',
            url: base_url + 'product/saveBom', // Endpoint untuk menyimpan produk dalam order
            data: {
              idProduct: idProduct,
              data: data
            },
            success: function (response) {
              Swal.fire({
                icon: 'success',
                title: 'Produk berhasil ditambahkan ke order',
                showConfirmButton: false,
                timer: 1500
              });
            },
            error: function (xhr) {
              Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Terjadi kesalahan saat menyimpan produk',
                footer: '<a href="">Why do I have this issue?</a>'
              });
            }
          });
        }
      });
    },
    error: function (xhr) {
      Swal.fire({
        icon: 'error',
        title: 'Oops...',
        text: 'Terjadi kesalahan saat mengambil daftar produk',
        footer: '<a href="">Why do I have this issue?</a>'
      });
    }
  });
});

// Fungsi untuk konversi serializeArray menjadi format yang lebih mudah
function convertFormDataToObject(formData) {
  const dataObj = {};
  
  formData.forEach(item => {
    const name = item.name.replace('[]', ''); // Hilangkan tanda []
    if (!dataObj[name]) {
      dataObj[name] = [];
    }
    dataObj[name].push(item.value);
  });

  return dataObj;
}



$(document).on('click', '.viewOrderDetail', function () {
    const orderId = $(this).data('id'); // Mengambil ID order dari tombol
    
    // Membuat URL tujuan
    const url = base_url+'/admin/order/detail/' + orderId;
    
    // Redirect ke URL di tab baru
    window.open(url, '_blank');
});
