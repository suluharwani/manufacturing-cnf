var loc = window.location;
var base_url = loc.protocol + "//" + loc.hostname + (loc.port ? ":" + loc.port : "") + "/";

$(document).ready(function() {
    initializeSupplierTable();
});
function formatNumber(number, decimals = 0) {
    // Memastikan angka adalah tipe Number
    if (isNaN(number)) {
        return 'Invalid number';
    }

    // Menentukan format desimal
    let options = { 
        minimumFractionDigits: decimals, 
        maximumFractionDigits: decimals 
    };

    // Menggunakan toLocaleString untuk format angka dengan titik ribuan
    return number.toLocaleString('id-ID', options);
}
function initializeSupplierTable() {
    var dataTable = $('#tabel_serverside').DataTable({
        "processing": true,
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
        "buttons": ['csv'],
        "order": [],
        "ordering": true,
        "info": true,
        "serverSide": true,
        "stateSave": true,
        "scrollX": true,
        "ajax": {
            "url": base_url + "stock/stockdata",
            "type": "POST",
            "data": {}
        },
        columns: [

            { mRender: function (data, type, row) { return row[0]; } }, // No
            { mRender: function (data, type, row) { return row[3]; } }, // Code
            { mRender: function (data, type, row) { return row[2]; } }, // Nama Sup
            { mRender: function (data, type, row) { return `${Math.abs(row[4])} ${row[8]}` ; } }, 
            { mRender: function (data, type, row) { return `${Math.abs(row[5])} ${row[8]}`; } }, // 
            { mRender: function (data, type, row) { return `${Math.abs(row[6])} ${row[8]}`; } }, // 
            { mRender: function (data, type, row) { return `${row[13] === null ? 0 : row[13] } ${row[8]}`; } }, // 
            { mRender: function (data, type, row) { return `${parseFloat(Math.abs(row[14]))} ${row[8]}` ; } }, // 
            { mRender: function (data, type, row) { return `<a href="javascript:void(0);" class="btn btn-warning btn-sm view" id="${row[1]}" ">View</a>
                                                            <a href="javascript:void(0);" class="btn btn-success btn-sm history" id="${row[1]}" ">History</a>`; } }, // 

        ],
        "columnDefs": [{
            "targets": [0],
            "orderable": false
        }],
        error: function () {
            $(".tabel_serverside-error").html("");
            $("#tabel_serverside").append('<tbody class="tabel_serverside-error"><tr><th colspan="3">Data Tidak Ditemukan di Server</th></tr></tbody>');
            $("#tabel_serverside_processing").css("display", "none");
        }
    });
}