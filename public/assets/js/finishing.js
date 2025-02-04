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
        { data: 'id' },
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
                    $('#editFinishingForm #id').val(data.id); // Populate hidden ID field
                    $('#editFinishingForm #name').val(data.name);
                    $('#editFinishingForm #description').val(data.description);
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
    $('#editFinishingForm').on('submit', function (e) {
        e.preventDefault();

        const formData = $(this).serialize();

        $.ajax({
            url: base_url + 'finishing/updateData',
            type: 'POST',
            data: formData,
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
  