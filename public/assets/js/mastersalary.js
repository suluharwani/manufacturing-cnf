var loc = window.location;
var base_url = loc.protocol + "//" + loc.hostname + (loc.port ? ":" + loc.port : "") + "/";


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
      "url" :base_url+"master_penggajian/get_list" , // json datasource 
      "type": "post",  // method  , by default get
      // "async": false,
      "dataType": 'json',
      "data":{},
  },
  
  columns: [
    {},
    {mRender: function (data, type, row) {

        return row[1]
    }},
    {mRender: function (data, type, row) {
        
        return row[2]
    }},
    {mRender: function (data, type, row) {
       
        return row[3]
    }},
    {mRender: function (data, type, row) {
        return row[4]
    }},

    {mRender: function (data, type, row) {
        return row[5]
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
})

$('.create').on('click', function () {
    Swal.fire({
        title: 'Add Payroll',
        html: `
        <form id="form_add_data">
        
        <div class="form-group">
        <label for="tanggal">Kode Penggajian</label>
        <input type="text" id="kode_penggajian" class="form-control" placeholder="Code">

        </div>
        <div class="form-group">
        <label for="waktu_mulai">Waktu Mulai</label>
        <input type="date" id="tanggal_awal_penggajian" class="form-control">
        </div>
        <div class="form-group">
        <label for="waktu_selesai">Waktu Selesai</label>
        <input type="date" id="tanggal_akhir_penggajian" class="form-control">
        </div>
        <div class="form-group">
        <label for="lokasi">Group</label>
        <input type="text" id="group" class="form-control" placeholder="Group">
        </div>
        <div class="form-group">
        <label for="lokasi">Pembuat</label>
        <input type="text" id="creator" class="form-control" placeholder="Creator">
        </div>
        <div class="form-group">
        <label for="lokasi">Deskripsi/Catatan</label>
        <textarea id="keterangan" class="form-control"></textarea>
        </div>
        </form>
        
        
        
        
        
        
        `,
        confirmButtonText: 'Save',
        preConfirm: () => {
            return {
                kode_penggajian: $('#kode_penggajian').val(),
                tanggal_awal_penggajian: $('#tanggal_awal_penggajian').val(),
                tanggal_akhir_penggajian: $('#tanggal_akhir_penggajian').val(),
                group: $('#group').val(),
                creator: $('#creator').val(),
                keterangan: $('#keterangan').val()
            };
        }
    }).then((result) => {
        if (result.isConfirmed) {
            $.post(base_url + 'master_penggajian/add', result.value, function () {
                Swal.fire('Success', 'Payroll added', 'success');
                $('#tabel_serverside').DataTable().ajax.reload();
            });
        }
    });
});

$(document).on('click', '.edit', function () {
    var id = $(this).attr('id');

        // Mendapatkan data payroll berdasarkan ID
    $.get(base_url + 'master_penggajian/get/' + id, function (data) {
        Swal.fire({
            title: 'Edit Payroll',
            html: `
            <form id="form_add_data">
            
            <div class="form-group">
            <input type="hidden" id="id" value="${data.id}">

            <label for="tanggal">Kode Penggajian</label>
            <input type="text" id="kode_penggajian" class="form-control" value="${data.kode_penggajian}" placeholder="Code">
            </div>
            <div class="form-group">
            <label for="waktu_mulai">Waktu Mulai</label>
            <input type="date" id="tanggal_awal_penggajian" class="form-control" value="${data.tanggal_awal_penggajian.split(' ')[0]}">
            </div>
            <div class="form-group">
            <label for="waktu_selesai">Waktu Selesai</label>
            <input type="date" id="tanggal_akhir_penggajian" class="form-control" value="${data.tanggal_akhir_penggajian.split(' ')[0]}">
            </div>
            <div class="form-group">
            <label for="lokasi">Group</label>
            <input type="text" id="group" class="form-control" value="${data.group}" placeholder="Group">
            </div>
            <div class="form-group">
            <label for="lokasi">Pembuat</label>
            <input type="text" id="creator" class="form-control" value="${data.creator}" placeholder="Creator">
            </div>
            <div class="form-group">
            <label for="lokasi">Deskripsi/Catatan</label>
            <textarea id="keterangan" class="form-control">${data.keterangan}</textarea>
            </div>
            </form>
            
            
            
            
            
            
            
            `,
            confirmButtonText: 'Update',
            preConfirm: () => {
                return {
                    id: $('#id').val(),
                    kode_penggajian: $('#kode_penggajian').val(),
                    tanggal_awal_penggajian: $('#tanggal_awal_penggajian').val(),
                    tanggal_akhir_penggajian: $('#tanggal_akhir_penggajian').val(),
                    group: $('#group').val(),
                    creator: $('#creator').val(),
                    keterangan: $('#keterangan').val()
                };
            }
        }).then((result) => {
            if (result.isConfirmed) {
                $.post(base_url + 'master_penggajian/update', result.value, function () {
                    Swal.fire('Success', 'Payroll updated', 'success');
                    $('#tabel_serverside').DataTable().ajax.reload();
                });
            }
        });
    }, 'json');
});

    // Delete Data
$(document).on('click', '.delete', function () {
    var id = $(this).attr('id');

    Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            $.post(base_url + 'master_penggajian/delete/' + id, function () {
                Swal.fire('Deleted!', 'Your record has been deleted.', 'success');
                $('#tabel_serverside').DataTable().ajax.reload();
            });
        }
    });
});
