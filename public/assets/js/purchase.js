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
      "url" :base_url+"purchase/listdataPurchaseOrder" , // json datasource 
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
        return row[5]
    }},
    {mRender: function (data, type, row) {
      if (row[3] == 0) {
        status = `<span class="badge bg-secondary">Draft</span>`
      }else if (row[3]==1) {
        status = `<span class="badge bg-primary">Diposting</span>`
      }else{
        status =  `<span class="badge bg-danger">Tidak ada</span>`
      }
      return status
    }},
    {mRender: function (data, type, row) {
     return `<a href="${base_url}purchase/po/${row[4]}" target="_blank" class="btn btn-success btn-sm showPurchaseOrder" id="${row[4]}" >Detail</a>`; 
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
})
$('.addPurchaseOrder').on('click', function () {
    // Menampilkan SweetAlert dan menambahkan dynamic select option untuk customer
  const currentDate = new Date();
const formattedDate = currentDate.toISOString().split('T')[0];
    Swal.fire({
        title: `Tambah Purchase Order`,
        html: `
        <form id="form_add_data">
            <div class="form-group">
                <label for="kode">Kode</label>
                <input type="text" class="form-control" id="kode" aria-describedby="kodeHelp" placeholder="Kode">
                <button type="button" id="generateCode" class="btn btn-primary mt-2">Generate Kode</button>
            </div>
            <div class="form-group">
                <label for="supplier">Supplier</label>
                <select id="supplier" class="form-control">
                    <option value="">Pilih supplier</option>
                </select>
            </div>
        </form>`,
        confirmButtonText: 'Confirm',
        focusConfirm: false,
        preConfirm: () => {
            const kode = Swal.getPopup().querySelector('#kode').value;
            const supplier = Swal.getPopup().querySelector('#supplier').value;
            if (!kode || !supplier) {
                Swal.showValidationMessage('Silakan lengkapi data');
            }
            return { kode: kode, supplier: supplier};
        }
    }).then((result) => {
        $.ajax({
            type: "POST",
            url: base_url + '/purchase/add_po',
            async: false,
            data: { code: result.value.kode, supplier_id: result.value.supplier, date:formattedDate, status :0  },
            success: function (data) {
                 $('#tabel_serverside').DataTable().ajax.reload();
                Swal.fire({
                    position: 'center',
                    icon: 'success',
                    title: `Jenis barang berhasil ditambahkan.`,
                    showConfirmButton: false,
                    timer: 1500
                });
            },
            error: function (xhr) {
                let d = JSON.parse(xhr.responseText);
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: `${d.message}`,
                    footer: '<a href="">Why do I have this issue?</a>'
                });
            }
        });
    });

    // Menambahkan event listener pada tombol generate kode
    $('#generateCode').on('click', function () {
        const generatedCode = generateCode();
        $('#kode').val(generatedCode);  // Set value ke input kode
    });

    // Menambahkan opsi customer ke select dropdown
    // getCustomerOption().then(options => {
    //     $('#customer').html(options); // Isi select dengan opsi customer
    // }).catch(error => {
    //     Swal.fire('Error', error, 'error');
    // });
        getSupplierOption().then(options => {
        $('#supplier').html(options); // Isi select dengan opsi customer
    }).catch(error => {
        Swal.fire('Error', error, 'error');
    });
});

function generateCode() {
    // Mendapatkan tanggal saat ini
    const today = new Date();
    
    // Format tanggal: yymmdd
    const year = today.getFullYear().toString().slice(-2); // Ambil 2 digit terakhir dari tahun
    const month = ('0' + (today.getMonth() + 1)).slice(-2); // Bulan dalam format 2 digit
    const day = ('0' + today.getDate()).slice(-2); // Hari dalam format 2 digit
    
    // Menghasilkan dua angka acak
    const randomNum = Math.floor(Math.random() * 100); // Angka acak antara 0 dan 99
    const randomNumStr = randomNum.toString().padStart(2, '0'); // Pastikan dua digit dengan menambahkan 0 di depan jika perlu
    
    // Gabungkan semuanya menjadi format PIXXXXXXXX
    const code = `PO${year}${month}${day}${randomNumStr}`;
    
    return code;
}

function getSupplierOption() {
    return new Promise((resolve, reject) => {
        $.ajax({
            type: 'get',
            url: base_url + 'pembelian/getSupplierList', // Endpoint untuk mendapatkan produk
            success: function (response) {
                // Buat opsi produk dari data yang diterima
                var options = '<option value="">Pilih Supplier</option>';
                response.forEach(function (supplier) {
                    options += `<option value="${supplier.id}">${supplier.supplier_name}</option>`;
                });
                // Resolving the promise dengan materialOptions setelah sukses
                resolve(options);
            },
            error: function (xhr) {
                // Menolak promise jika terjadi kesalahan
                reject('Terjadi kesalahan saat mengambil daftar');
            }
        });
    });
}
