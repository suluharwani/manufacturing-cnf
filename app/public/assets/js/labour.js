var loc = window.location;
var base_url = loc.protocol + "//" + loc.hostname + (loc.port ? ":" + loc.port : "") + "/";
function decimalToRupiah(angka) {
    // Pastikan input adalah number
    if (typeof angka !== 'number') {
      angka = parseFloat(angka);
      if (isNaN(angka)) return 'Invalid number';
    }
  
    // Format ke Rupiah dan hilangkan .00 di belakang jika ada
    let rupiah = angka.toLocaleString('id-ID', {
      style: 'currency',
      currency: 'IDR',
      minimumFractionDigits: 0,
      maximumFractionDigits: 2
    });
  
    // Hilangkan .00 di belakang jika ada
    rupiah = rupiah.replace(/\.00$/, '');
  
    return rupiah;
  }
function getLastSegment() {
    var pathname = window.location.pathname; // Mendapatkan path dari URL
    var segments = pathname.split('/').filter(function(segment) { return segment.length > 0; });
    return segments[segments.length - 1]; // Mengambil segment terakhir
  }
$(document).ready(function () {
    let no = 1;
const table = $('#finishingTable').DataTable({
    ajax: {
        url: base_url + 'product/getLabour/'+getLastSegment(),
        type: 'POST', // Menggunakan POST untuk DataTables
    },
    columns: [
        { data: null,
            render: (data) =>
                `
                    ${no++}
                `, },
        { data: 'process' }, 
        { data: 'div' }, 
        { data: 'worker' }, 
        { data: 'total_worker' }, 
        { data: 'time_hours' }, 
        { data: 'wage_per_hours' ,
            render: (data) =>
                `
                   ${decimalToRupiah(data)}
                `,}, 
        { data: 'cost' ,
            render: (data) =>
                `
                   ${decimalToRupiah(data)}
                `,}, 
        { data: 'total_cost_idr' ,
            render: (data) =>
                `
                   ${decimalToRupiah(data)}
                `,}, 

        {
            data: null,
            render: (data) =>
                `
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
            url: base_url + 'product/labourCreate/'+getLastSegment(),
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
                url: base_url + `product/deleteLabour/${id}`,
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
       $('#length').val(res.length);
       $('#width').val(res.width);
       $('#height').val(res.height);
       $('#cbm').val(res.cbm);
        
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