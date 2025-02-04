var loc = window.location;
var base_url = loc.protocol + "//" + loc.hostname + (loc.port ? ":" + loc.port : "") + "/";
function getLastSegment() {
    var pathname = window.location.pathname; // Mendapatkan path dari URL
    var segments = pathname.split('/').filter(function(segment) { return segment.length > 0; });
    return segments[segments.length - 1]; // Mengambil segment terakhir
  }
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
          "url" :base_url+"product/modul/"+getLastSegment() , // json datasource 
          "type": "post",  // method  , by default get
          // "async": false,
          "dataType": 'json',
          "data":{},
      },

      columns: [
        {},

        {mRender: function (data, type, row) {
            
            return row[4]
        }},
        {mRender: function (data, type, row) {
           
            return row[5]
        }},
        {mRender: function (data, type, row) {
            return `<img src="${base_url}uploads/modul/${row[6]}" alt="Picture" height="50">`
        }},
    
        {mRender: function (data, type, row) {
            return `<button class="btn btn-warning bomModul" data-id="${row[1]}" data-id_product="${row[3]}">Bill of material</button>
                    <button class="btn btn-warning edit" data-id="${row[1]}">Edit Data</button>
                    <button class="btn btn-info edit-picture" data-id="${row[1]}">Edit Picture</button>
                    <button class="btn btn-danger delete" data-id="${row[1]}" data-id_product="${row[3]}">Delete</button>`
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
    productData()
    function productData(){
        $.ajax({
          type : "get",
          url  : base_url+"product/getProductData/"+getLastSegment(),
          async : false,
          success: function(data){
            res = data.product;
           $('#code').val(res.kode);
           $('#hscode').val(res.hs_code);
           $('#category').val(res.category);
           $('#product_name').val(res.nama);
           $('#desc').html(res.text);
            
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
});
$('.addModul').on('click', function () {
  console.log('addModul');
  $('#addModulModal').modal('show');
  $('#addModulForm')[0].reset(); // Reset form
  $('#addModulModalLabel').text('Add Modul Product');
});
$('#addModulForm').on('submit', function (e) {
  e.preventDefault();

  const formData = new FormData(this);

  $.ajax({
      url: base_url + 'modul/create/'+getLastSegment(),
      type: 'POST',
      data: formData,
      processData: false,
      contentType: false,
      success: function (response) {
          if (response.status) {
              Swal.fire('Success', response.message, 'success');
              $('#addModulModal').modal('hide');
              table.ajax.reload();
          } else {
              alert(response.errors ? JSON.stringify(response.errors) : response.message);
          }
      },
      error: function (xhr) {
          console.error(xhr.responseText);
          alert('An error occurred while adding the item.');
      },
  });
});
$(document).on('click', '.bomModul', function () {
  const idModul = $(this).data('id'); // Mengambil ID order dari atribut id
  const idProduct = getLastSegment();
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
      orderMaterialHtml = getOrderMaterial(materialOptions,idProduct,idModul);
    

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
              idModul: idModul,
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

$(document).on('click', '.remove-product', function () {
  $(this).closest('tr').remove();
});
function getOrderMaterial(materialOptions, idProduct, idModul) {
  let orderMaterialHtml = '';

  $.ajax({
    type: "POST",
    url: base_url + "product/getBom",
    async: false,
    data: { idProduct: idProduct ,idModul:idModul},
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
