var loc = window.location;
var base_url = loc.protocol + "//" + loc.hostname + (loc.port? ":"+loc.port : "") + "/";

function getLastSegment() {
  var pathname = window.location.pathname; // Mendapatkan path dari URL
  var segments = pathname.split('/').filter(function(segment) { return segment.length > 0; });
  return segments[segments.length - 1]; // Mengambil segment terakhir
}
$(document).ready(function() {
    function getMaterialOption() {
    return new Promise((resolve, reject) => {
      let materialOptions = '';

      $.ajax({
        type: 'GET',
        url: base_url + 'product/getMaterial', // Endpoint untuk mendapatkan produk
        success: function(response) {
          // Buat opsi produk dari data yang diterima
          response.material.forEach(material => {
            materialOptions += `<option value="${material.id}">${material.name} - ${material.nama_satuan}(${material.kode_satuan})</option>`;
          });

          // Resolving the promise dengan materialOptions setelah sukses
          resolve(materialOptions);
        },
        error: function(xhr) {
          // Menolak promise jika terjadi kesalahan
          reject('Terjadi kesalahan saat mengambil daftar produk');
        }
      });
    });
  }
    // Ketika tombol "Add" diklik


 // Ketika tombol "Add" diklik, tampilkan modal dan ambil data material
  $('.addMaterial').click(function() {
    // Reset form di modal
    $('#addMaterialForm')[0].reset();

    // Ambil opsi material dan masukkan ke dropdown
    getMaterialOption().then(function(options) {
      $('#materialCode').html(options); // Masukkan opsi ke dalam elemen select
      $('#addMaterialModal').modal('show'); // Tampilkan modal
    }).catch(function(error) {
      alert(error); // Tampilkan error jika gagal mengambil data
    });
  });

  // Menangani form submit di modal
  $('#addMaterialForm').submit(function(event) {
    event.preventDefault();

    // Ambil data dari form
    var materialCode = $('#materialCode').val();
    var materialName = $('#materialName').val();
    var materialQty = $('#materialQty').val();
    var materialPrice = $('#materialPrice').val();
    var materialDiscount = $('#materialDiscount').val();
    var materialTax = $('#materialTax').val();

    // Kirim data melalui AJAX ke server
    $.ajax({
      type: "POST",
      url: base_url + "pembelian/addMaterial", // URL untuk menambahkan material (ganti dengan URL yang sesuai)
      data: {
        materialCode: materialCode,
        materialName: materialName,
        materialQty: materialQty,
        materialPrice: materialPrice,
        materialDiscount: materialDiscount,
        materialTax: materialTax
      },
      success: function(response) {
        // Tampilkan pesan sukses jika berhasil menambahkan material
        if(response.status === 'success') {
          alert('Material added successfully!');
          $('#addMaterialModal').modal('hide'); // Tutup modal setelah berhasil
          location.reload(); // Reload halaman untuk memperbarui tabel
        } else {
          alert('Error adding material');
        }
      },
      error: function() {
        alert('Error connecting to server');
      }
    });
  });

    loadSupplierList(); // Memanggil fungsi untuk mengisi list supplier ketika halaman dimuat
 var id_pembelian = getLastSegment();

    // Mengambil data supplier berdasarkan ID Pembelian
    $.ajax({
        type: "GET",
        async:false,
        url: base_url + 'pembelian/getSupplierDataByPurchase/' + id_pembelian,
        dataType: 'json',
        success: function(data) {
            // Jika data supplier ada, isi form dengan data supplier tersebut
            if (data) {
                // Misalnya, jika data berisi id_supplier dan nama_supplier
                $('#supplier').val(data.id); // Atur nilai dropdown ke ID Supplier yang terpilih
                $('#country').val(data.country_name); // Isi Country
                $('#currency').val(data.currency_name); // Isi Currency
            } else {
                // Jika tidak ada data, reset form
                $('#supplier').val('');
                $('#country').val('');
                $('#currency').val('');
            }
        },
        error: function() {
            console.error("Error fetching supplier data.");
        }
    });

    // Fungsi untuk mengupdate data saat supplier dipilih
    $('#supplier').on('change', function() {
        var supplierId = $(this).val();
        if (supplierId) {
            $.ajax({
                type: "GET",
                url: base_url + 'pembelian/getSupplierData/' + supplierId,
                dataType: 'json',
                success: function(supplier) {
                    console.log(supplier.country_name)
                    // Update Country dan Currency berdasarkan supplier yang dipilih
                    $('#country').val(supplier.country_name);
                    $('#currency').val(supplier.currency_name);
                }
            });
        } else {
            // Reset nilai Country dan Currency jika tidak ada supplier yang dipilih
            $('#country').val('');
            $('#currency').val('');
        }
    });

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
      "url" :base_url+"/pembelian/listdataPembelianDetail/"+getLastSegment() , // json datasource 
      "type": "post",  // method  , by default get
      // "async": false,
      "dataType": 'json',
      "data":{},
    },
            //     $row[] = $no; //0
            // $row[] = $lists->id_pembelian_detail; //1
            // $row[] = $lists->material_kode; //2
            // $row[] = $lists->material_name;//3
            // $row[] = $lists->harga;//4
            // $row[] = $lists->kode_currency;//5
            // $row[] = $lists->nama_currency;//6
            // $row[] = $lists->rate;//7
            // $row[] = $lists->id_material;//8
            // $row[] = $lists->jumlah;//9
            // $row[] = $lists->diskon1;//10
            // $row[] = $lists->diskon2;//11
            // $row[] = $lists->diskon3;//12
            // $row[] = $lists->pajak;//13
            // $row[] = $lists->potongan;//14


                  // <th style=" text-align: center;">#</th>
                  // <th style=" text-align: center;">Kode</th>
                  // <th style=" text-align: center;">Nama</th>
                  // <th style=" text-align: center;">Harga Dasar</th>
                  // <th style=" text-align: center;">Disc 1</th>
                  // <th style=" text-align: center;">Disc 2</th>
                  // <th style=" text-align: center;">Disc 3</th>
                  // <th style=" text-align: center;">Potongan</th>
                  // <th style=" text-align: center;">Pajak</th>
                  // <th style=" text-align: center;">Harga Akhir</th>
                  // <th style=" text-align: center;">Kurs Rupiah</th>
                  // <th style=" text-align: center;">Action</th>
    columns: [
    {},
    {mRender: function (data, type, row) {
        return row[2]
    }},
    {mRender: function (data, type, row) {
        return row[3]
    }},
    {mRender: function (data, type, row) {
        return row[9]
    }},
    {mRender: function (data, type, row) {
        return `${formatNumber(row[4])} ${row[5]}`
    }},
     {mRender: function (data, type, row) {
        return `${row[10]}%`
    }},
     {mRender: function (data, type, row) {
        return `${row[11]}%`
    }},
     {mRender: function (data, type, row) {
        return `${row[12]}%`
    }},
    {mRender: function (data, type, row) {
        return `${row[14]} ${row[5]}`
    }},
    {mRender: function (data, type, row) {
        return `${row[13]}%`
    }},
        {mRender: function (data, type, row) {
              // Menghitung harga akhir setelah diskon dan potongan
 // Mengambil nilai dari setiap kolom
      let hargaDasar = row[4];
      let diskon1 = row[10] || 0;  // Jika diskon1 kosong, anggap 0
      let diskon2 = row[11] || 0;  // Jika diskon2 kosong, anggap 0
      let diskon3 = row[12] || 0;  // Jika diskon3 kosong, anggap 0
      let potongan = row[14] || 0;  // Jika potongan kosong, anggap 0
      let pajak = row[13] || 0;     // Jika pajak kosong, anggap 0

      // Menghitung harga setelah diskon bertingkat
      let hargaSetelahDiskon = hargaDasar - (hargaDasar * (diskon1 / 100)) - (hargaDasar * (diskon2 / 100)) - (hargaDasar * (diskon3 / 100)) - potongan;

      // Menghitung pajak dari harga setelah diskon
      let hargaDenganPajak = hargaSetelahDiskon + (hargaSetelahDiskon * (pajak / 100));

      // Format hasil akhir dan kembalikan
      return `${formatNumber(hargaDenganPajak)} ${row[5]}`;
    }},
    {mRender: function (data, type, row) {
              // Menghitung harga akhir setelah diskon dan potongan
 // Mengambil nilai dari setiap kolom
      let hargaDasar = row[4];
      let diskon1 = row[10] || 0;  // Jika diskon1 kosong, anggap 0
      let diskon2 = row[11] || 0;  // Jika diskon2 kosong, anggap 0
      let diskon3 = row[12] || 0;  // Jika diskon3 kosong, anggap 0
      let potongan = row[14] || 0;  // Jika potongan kosong, anggap 0
      let pajak = row[13] || 0;     // Jika pajak kosong, anggap 0

      // Menghitung harga setelah diskon bertingkat
      let hargaSetelahDiskon = hargaDasar - (hargaDasar * (diskon1 / 100)) - (hargaDasar * (diskon2 / 100)) - (hargaDasar * (diskon3 / 100)) - potongan;

      // Menghitung pajak dari harga setelah diskon
      let hargaDenganPajak = hargaSetelahDiskon + (hargaSetelahDiskon * (pajak / 100));

      // Format hasil akhir dan kembalikan
      return `${formatRupiah(hargaDenganPajak/row[7])}`;
    }},
    {mRender: function (data, type, row) {
              // Menghitung harga akhir setelah diskon dan potongan
 // Mengambil nilai dari setiap kolom
      let hargaDasar = row[4];
      let diskon1 = row[10] || 0;  // Jika diskon1 kosong, anggap 0
      let diskon2 = row[11] || 0;  // Jika diskon2 kosong, anggap 0
      let diskon3 = row[12] || 0;  // Jika diskon3 kosong, anggap 0
      let potongan = row[14] || 0;  // Jika potongan kosong, anggap 0
      let pajak = row[13] || 0;     // Jika pajak kosong, anggap 0

      // Menghitung harga setelah diskon bertingkat
      let hargaSetelahDiskon = hargaDasar - (hargaDasar * (diskon1 / 100)) - (hargaDasar * (diskon2 / 100)) - (hargaDasar * (diskon3 / 100)) - potongan;

      // Menghitung pajak dari harga setelah diskon
      let hargaDenganPajak = hargaSetelahDiskon + (hargaSetelahDiskon * (pajak / 100));

      // Format hasil akhir dan kembalikan
      return `${formatNumber(hargaDenganPajak*row[9])} ${row[5]}`;
    }},
    {mRender: function (data, type, row) {
              // Menghitung harga akhir setelah diskon dan potongan
       // Mengambil nilai dari setiap kolom
      let hargaDasar = row[4];
      let diskon1 = row[10] || 0;  // Jika diskon1 kosong, anggap 0
      let diskon2 = row[11] || 0;  // Jika diskon2 kosong, anggap 0
      let diskon3 = row[12] || 0;  // Jika diskon3 kosong, anggap 0
      let potongan = row[14] || 0;  // Jika potongan kosong, anggap 0
      let pajak = row[13] || 0;     // Jika pajak kosong, anggap 0

      // Menghitung harga setelah diskon bertingkat
      let hargaSetelahDiskon = hargaDasar - (hargaDasar * (diskon1 / 100)) - (hargaDasar * (diskon2 / 100)) - (hargaDasar * (diskon3 / 100)) - potongan;

      // Menghitung pajak dari harga setelah diskon
      let hargaDenganPajak = hargaSetelahDiskon + (hargaSetelahDiskon * (pajak / 100));

      // Format hasil akhir dan kembalikan
      return formatRupiah((hargaDenganPajak*row[9])/row[7]);
    }},
    {mRender: function (data, type, row) {
     return `<a href="${base_url}pembelian/form/${row[1]}" target="_blank" class="btn btn-warning btn-sm showPurchaseOrder">Edit</a>
             <a href="${base_url}pembelian/form/${row[1]}" target="_blank" class="btn btn-danger btn-sm showPurchaseOrder">Delete</a>`; 
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
function formatNumber(number) {
  return number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
}
function formatDateTime(datetime) {
  const date = new Date(datetime); // Mengubah datetime menjadi objek Date
  const year = date.getFullYear();
  const month = String(date.getMonth() + 1).padStart(2, '0'); // Menambahkan leading zero untuk bulan < 10
  const day = String(date.getDate()).padStart(2, '0'); // Menambahkan leading zero untuk tanggal < 10
  return `${day}-${month}-${year}`;
}
function formatRupiah(angka) {
  // Memastikan angka adalah angka yang valid
  angka = parseFloat(angka);
  if (isNaN(angka)) {
    return "0";
  }

  // Format angka menjadi Rupiah dengan 2 angka di belakang koma
  var format = angka.toLocaleString('id-ID', { 
    style: 'currency', 
    currency: 'IDR', 
    minimumFractionDigits: 2, 
    maximumFractionDigits: 2 
  });

  return format;
}
function loadSupplierList() {
    $.ajax({
        type: "GET",
        async:false,
        url: base_url + 'pembelian/getSupplierList', // Pastikan endpoint ini benar
        dataType: 'json',
        success: function(data) {
            if (data && Array.isArray(data)) {
                var options = '<option value="">Pilih Supplier</option>';
                data.forEach(function(supplier) {
                    options += `<option value="${supplier.id}">${supplier.supplier_name}</option>`;
                });
                $('#supplier').html(options); // Mengisi dropdown dengan options
            } else {
                $('#supplier').html('<option value="">No Supplier Found</option>');
            }
        },
        error: function() {
            console.error("Error fetching supplier list.");
        }
    });
}
  $('.addMaterial').click(function() {
    // Reset form di modal
    $('#addMaterialForm')[0].reset();
  });

  // Menangani form submit di modal
  $('#addMaterialForm').submit(function(event) {
    event.preventDefault();

    // Ambil data dari form
    var materialCode = $('#materialCode').val();
    var materialName = $('#materialName').val();
    var materialQty = $('#materialQty').val();
    var materialPrice = $('#materialPrice').val();
    var materialDiscount = $('#materialDiscount').val();
    var materialTax = $('#materialTax').val();

    // Kirim data melalui AJAX ke server
    $.ajax({
      type: "POST",
      url: base_url + "pembelian/addMaterial", // URL untuk menambahkan material (ganti dengan URL yang sesuai)
      data: {
        materialCode: materialCode,
        materialName: materialName,
        materialQty: materialQty,
        materialPrice: materialPrice,
        materialDiscount: materialDiscount,
        materialTax: materialTax
      },
      success: function(response) {
        // Tampilkan pesan sukses jika berhasil menambahkan material
        if(response.status === 'success') {
          alert('Material added successfully!');
          $('#addMaterialModal').modal('hide'); // Tutup modal setelah berhasil
          location.reload(); // Reload halaman untuk memperbarui tabel
        } else {
          alert('Error adding material');
        }
      },
      error: function() {
        alert('Error connecting to server');
      }
    });
  });

    $('.saveSupplier').submit(function(event) {
    event.preventDefault();

    // Ambil data dari form
    var supplier = $('#supplier').val();
    var pajak = $('#pajak').val();
   

    // Kirim data melalui AJAX ke server
    $.ajax({
      type: "POST",
      url: base_url + "pembelian/updateSupplier/"+getLastSegment(), // URL untuk menambahkan material (ganti dengan URL yang sesuai)
      data: {
        supplier: supplier,
        pajak: pajak,

      },
      success: function(response) {
        // Tampilkan pesan sukses jika berhasil menambahkan material
        if(response.status === 'success') {
          alert('Material added successfully!');
          $('#addMaterialModal').modal('hide'); // Tutup modal setelah berhasil
          location.reload(); // Reload halaman untuk memperbarui tabel
        } else {
          alert('Error adding material');
        }
      },
      error: function() {
        alert('Error connecting to server');
      }
    });
  });