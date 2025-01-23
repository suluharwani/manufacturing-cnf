var loc = window.location;
var base_url = loc.protocol + "//" + loc.hostname + (loc.port ? ":" + loc.port : "") + "/";

$(document).ready(function () {
const table = $('#finishingTable').DataTable({
    ajax: {
        url: base_url + 'finishing/getAll',
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
            url: base_url + 'finishing/create',
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
