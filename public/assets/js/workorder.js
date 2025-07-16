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
      "url" :base_url+"wo/listdata" , // json datasource 
      "type": "post",  // method  , by default get
      // "async": false,
      "dataType": 'json',
      "data":{},
    },
    
    columns: [
    {},
    {mRender: function (data, type, row) {
       return row[3]
     }},
     {mRender: function (data, type, row) {
       return row[4]
     }},
     {mRender: function (data, type, row) {
       return row[5]
     }},
    {mRender: function (data, type, row) {
        return row[6]
    }},

    {mRender: function (data, type, row) {
     return `<a href="${base_url}wo/${row[1]}"  class="btn btn-success btn-sm showPurchaseOrder" id="'+row[1]+'" >Detail</a>`; 
    }}
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
});

  $('.tambahInvoice').on('click', function() {
    $.when(
        $.ajax({
            url: base_url + '/proformainvoice/get_list',
            method: 'POST',
            dataType: 'json'
        })
    ).done(function(piResponse) {
        const piData = piResponse;
        
        if (Array.isArray(piData)) {
            let piOptions = piData.map(pi => `<option value="${pi.id}">${pi.invoice_number}</option>`).join('');

            Swal.fire({
                title: 'Tambah WO',
                html: `
                    <form id="form_add_data">
                        <div class="form-group">
                            <label for="invoice_id">Proforma Invoice</label>
                            <select class="form-control" id="invoice_id">
                            <option value="" selected disabled>Pilih Proforma Invoice</option>
                                ${piOptions}
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="kode">Kode</label>
                            <input type="text" class="form-control" id="kode" placeholder="Kode" readonly>
                        </div>
                        <div class="form-group">
                            <label for="start">Start</label>
                            <input type="date" class="form-control" id="start">
                        </div>
                        <div class="form-group">
                            <label for="end">End</label>
                            <input type="date" class="form-control" id="end">
                        </div>
                    </form>
                `,
                confirmButtonText: 'Confirm',
                focusConfirm: false,
                preConfirm: () => {
                    const invoice_id = Swal.getPopup().querySelector('#invoice_id').value;
                    const kode = Swal.getPopup().querySelector('#kode').value;
                    const start = Swal.getPopup().querySelector('#start').value;
                    const end = Swal.getPopup().querySelector('#end').value;

                    if (!invoice_id || !kode || !start || !end) {
                        Swal.showValidationMessage('Silakan lengkapi data');
                    }
                    return { invoice_id: invoice_id, kode: kode, start: start, end: end };
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        type: "POST",
                        url: base_url + 'wo/add',
                        async: false,
                        data: {
                            invoice_id: result.value.invoice_id,
                            kode: result.value.kode,
                            start: result.value.start,
                            end: result.value.end
                        },
                        success: function(data) {
                            Swal.fire({
                                position: 'center',
                                icon: 'success',
                                title: 'WO berhasil ditambahkan.',
                                showConfirmButton: false,
                                timer: 1500
                            });
                            $('#tabel_serverside').DataTable().ajax.reload();
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

            // Auto-generate WO code when PI is selected
            $('#invoice_id').on('change', function() {
                const invoiceId = $(this).val();
                if (invoiceId) {
                    generateWoCode(invoiceId);
                } else {
                    $('#kode').val('');
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

function generateWoCode(invoiceId) {
    $.ajax({
        url: base_url + '/generate/generateWoCode/' + invoiceId,
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.error) {
                Swal.fire('Error', response.error, 'error');
            } else {
                $('#kode').val(response.code);
            }
        },
        error: function(xhr) {
            Swal.fire('Error', 'Gagal generate kode WO', 'error');
        }
    });
}