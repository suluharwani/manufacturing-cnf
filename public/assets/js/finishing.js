var loc = window.location;
var base_url = loc.protocol + "//" + loc.hostname + (loc.port ? ":" + loc.port : "") + "/";
function getLastSegment() {
    var pathname = window.location.pathname; // Mendapatkan path dari URL
    var segments = pathname.split('/').filter(function(segment) { return segment.length > 0; });
    return segments[segments.length - 1]; // Mengambil segment terakhir
  }
$(document).ready(function () {
const table = $('#finishingTable').DataTable({
    ajax: {
        url: base_url + 'finishing/getAll/'+getLastSegment(),
        type: 'POST', // Menggunakan POST untuk DataTables
    },
    columns: [
        {  data: null,
            render: function (data, type, row, meta) {
                return meta.row + 1;
            }, },
        { data: 'name' }, 
        {
            data: 'description',
            render: function (data, type, row) {
                if (data.length > 20) {
                    const shortText = data.substring(0, 20); // Potong deskripsi
                    return `
                        <span class="short-description">${shortText}...</span>
                        <button class="btn btn-link read-more" data-description="${data}">Read More</button>
                    `;
                } else {
                    return `<span class="short-description">${data}</span>`;
                }
            },
        },
        {
            data: 'picture',
            render: (data) =>
                `<img src="${base_url}uploads/finishing/${data}" alt="Picture" height="50">`,
        },
        {
            data: null,
            render: (data) =>
                `
                    <button class="btn btn-warning print" data-id="${data.id}" data-id_product="${getLastSegment()}">Print Bill of material</button>
                    <button class="btn btn-warning printCost" data-id="${data.id}" data-id_product="${getLastSegment()}">Print Cost</button>
                    <button class="btn btn-warning bomFinishing" data-id="${data.id}">Bill of material</button>
                    <button class="btn btn-warning edit" data-id="${data.id}">Edit Data</button>
                    <button class="btn btn-info edit-picture" data-id="${data.id}">Edit Picture</button>
                    <button class="btn btn-danger delete" data-id="${data.id}">Delete</button>
                `,
        },
    ],
});

$(document).on('click', '.read-more', function () {
    const fullDescription = $(this).data('description'); // Ambil deskripsi lengkap dari tombol
    Swal.fire({
        title: 'Full Description',
        text: fullDescription,
        icon: 'info',
        confirmButtonText: 'Close',
    });
});

    // Handle form submission via AJAX
    $('#addFinishing').on('click', function () {
        $('#addFinishingModal').modal('show');
        $('#addFinishingForm')[0].reset(); // Reset form
        $('#addFinishingModalLabel').text('Add Finishing Item');
    });

    // Submit Add Form
    $('#addFinishingForm').on('submit', function (e) {
        e.preventDefault();

        const formData = new FormData(this);

        $.ajax({
            url: base_url + 'finishing/create/'+getLastSegment(),
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
                if (response.status) {
                    Swal.fire('Success', response.message, 'success');
                    $('#addFinishingModal').modal('hide');
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

    // Open Edit Modal
    $(document).on('click', '.edit', function () {
        const id = $(this).data('id');

        $.ajax({
            url: base_url + 'finishing/get',
            type: 'POST',
            data: { id: id },
            success: function (response) {
                if (response.status) {
                    const data = response.data;
                    $('#editFinishingModal').modal('show');
                    $('#editFinishingModalLabel').text('Edit Finishing Item');
                    $('#idFinishing').val(data.id); // Populate hidden ID field
                    $('#nameFinishing').val(data.name);
                    $('#descriptionFinishing').val(data.description);
                } else {
                    Swal.fire('Success', response.message, 'success');
                }
            },
            error: function (xhr) {
                console.error(xhr.responseText);
                alert('Failed to fetch item details.');
            },
        });
    });

    // Submit Edit Form
    $('#editFinishingFormBtn').on('click', function (e) {
        e.preventDefault();
        id = $('#idFinishing').val(); // Populate hidden ID field
        name = $('#nameFinishing').val();
        description = $('#descriptionFinishing').val();
        

        $.ajax({
            url: base_url + 'finishing/updateData',
            type: 'POST',
            data: {id:id,name:name,description:description},
            success: function (response) {
                if (response.status) {
                    Swal.fire('Success', response.message, 'success');
                    $('#editFinishingModal').modal('hide');
                    table.ajax.reload();
                } else {
                    alert(response.errors ? JSON.stringify(response.errors) : response.message);
                }
            },
            error: function (xhr) {
                console.error(xhr.responseText);
                alert('An error occurred while updating the item.');
            },
        });
    });

    // Open Edit Picture Modal
    $(document).on('click', '.edit-picture', function () {
        const id = $(this).data('id');
        $('#editPictureModal').modal('show');
        $('#editPictureModal #id').val(id);
    });

    // Submit Edit Picture Form
    $('#editPictureForm').on('submit', function (e) {
        e.preventDefault();

        const formData = new FormData(this);

        $.ajax({
            url: base_url + 'finishing/updatePicture',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
                if (response.status) {
                    Swal.fire('Success', response.message, 'success');
                    $('#editPictureModal').modal('hide');
                    table.ajax.reload();
                } else {
                    alert(response.errors ? JSON.stringify(response.errors) : response.message);
                }
            },
            error: function (xhr) {
                console.error(xhr.responseText);
                alert('An error occurred while updating the picture.');
            },
        });
    });


$(document).on('click', '.delete', function () {
    const id = $(this).data('id');

    // SweetAlert Confirmation
    Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!',
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: base_url + `finishing/delete/${id}`,
                method: 'POST', // Menggunakan POST untuk kompatibilitas umum
                data: { id: id },
                success: (response) => {
                    if (response.status) {
                        Swal.fire('Deleted!', response.message, 'success');
                        table.ajax.reload();
                    } else {
                        Swal.fire('Error', response.message, 'error');
                    }
                },
                error: (xhr) => {
                    console.error(xhr.responseText);
                    Swal.fire('Error', 'An error occurred while deleting the item.', 'error');
                },
            });
        }
    });
});

});



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
  $(document).on('click', '.remove-material', function() {
    $(this).closest('tr').remove(); // Menghapus baris material
  });
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
  
  
//finishing
$(document).on('click', '.print', function () {
  const idFinishing = $(this).data('id'); // Mengambil ID order dari atribut id
  const idProduct = getLastSegment();
  window.location.href = `${base_url}product/printBom/${idProduct}/${idFinishing}`;
})
$(document).on('click', '.printCost', function () {
  const idFinishing = $(this).data('id'); // Mengambil ID order dari atribut id
  const idProduct = getLastSegment();
  window.location.href = `${base_url}product/printCost/${idProduct}/${idFinishing}`;
})
$(document).on('click', '.bomFinishing', function () {
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
          materialOptions += `<option value="${material.id}">${material.name} - ${material.nama_satuan}(${material.kode_satuan}) || kode: ${material.kode} </option>`;
        });
        orderMaterialHtml = getOrderMaterial(materialOptions,idProduct,idModul);
      
  
        // Tampilkan modal dengan form produk
        Swal.fire({
          title: 'Bill of Material Finishing',
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
              url: base_url + 'product/saveBomFinishing', // Endpoint untuk menyimpan produk dalam order
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
      url: base_url + "product/getBomFinishing",
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
  