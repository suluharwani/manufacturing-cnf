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
                $('#id_currency').val(data.id_currency); // Isi Currency
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
      if (row[15]==0) {
        return `<a href="javascript:void(0);" class="btn btn-warning btn-sm editMaterial" data-id = "${row[1]}">Edit</a>
             <a href="javascript:void(0);" class="btn btn-danger btn-sm deleteMaterial" data-id = "${row[1]}">Delete</a>`; 
      }else{
        return `Data sudah diposting`
      }
     
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
function formatNumber(number, decimals = 2, decimalSeparator = ".", thousandSeparator = ",") {
    // Memastikan angka dalam format yang benar
    const num = parseFloat(number);
    
    // Menghasilkan string dengan angka yang diformat
    let formattedNumber = num.toFixed(decimals);
    
    // Memisahkan angka menjadi bagian integer dan desimal
    let [integer, decimal] = formattedNumber.split(decimalSeparator);

    // Menambahkan pemisah ribuan pada bagian integer
    integer = integer.replace(/\B(?=(\d{3})+(?!\d))/g, thousandSeparator);

    // Menggabungkan bagian integer dan desimal kembali
    return decimal ? integer + decimalSeparator + decimal : integer;
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

  // Menangani form submit di modal
  $('#addMaterialForm').submit(function(event) {
    event.preventDefault();

    // Ambil data dari form
    var materialCode = $('#materialCode').val();
    var materialQty = $('#materialQty').val();
    var harga = $('#harga').val();
    var id_currency = $('#id_currency').val();
    var disc1 = $('#disc1').val();
    var disc2 = $('#disc2').val();
    var disc3 = $('#disc3').val();
    var potongan = $('#potongan').val();
    var pajak = $('#pajak_barang').val();
    id_pembelian = getLastSegment()

    // Kirim data melalui AJAX ke server
    $.ajax({
      type: "POST",
      url: base_url + "pembelian/addMaterial", // URL untuk menambahkan material (ganti dengan URL yang sesuai)
      data: {
        materialCode: materialCode,
        materialQty: materialQty,
        harga: harga,
        id_currency: id_currency,
        disc1: disc1,
        disc2: disc2,
        disc3: disc3,
        potongan: potongan,
        pajak: pajak,
        id_pembelian : id_pembelian
      },
      success: function(response) {
        // Tampilkan pesan sukses jika berhasil menambahkan material
        if(response.status === 'success') {
          Swal.fire({
  title: "Good job!",
  text: "Material added successfully!",
  icon: "success"
});
          $('#tabel_serverside').DataTable().ajax.reload();
          $('#addMaterialModal').modal('hide'); // Tutup modal setelah berhasil
        } else {
          alert('Error adding material');
        }
      },
      error: function() {
        alert('Error connecting to server');
      }
    });
  });
$('.postingPembelian').click(function() {
  let id = getLastSegment();

  // Memeriksa apakah ID valid
  if (!id) {
    Swal.fire({
      title: 'ID tidak ditemukan!',
      text: 'Tidak ada ID yang dapat diproses.',
      icon: 'error',
      confirmButtonColor: '#3085d6',
      confirmButtonText: 'Tutup'
    });
    return; // Hentikan eksekusi jika ID tidak valid
  }

  Swal.fire({
    title: 'Apakah anda yakin?',
    text: "Pembelian akan diposting",
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#3085d6',
    cancelButtonColor: '#d33',
    confirmButtonText: 'Ya, Posting pembelian!'
  }).then((result) => {
    if (result.isConfirmed) {
      $.ajax({
        type: 'post',
        url: base_url + '/pembelian/posting',
        async: false,
        data: { id: id },
        success: function(data) {
          //reload table
          location.reload(true);
          Swal.fire(
            'Diposting!',
            'Pembelian telah diposting',
            'success'
          );
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
});

$('.batalPostingPembelian').click(function() {
  id = getLastSegment()
  Swal.fire({
    title: 'Apakah anda yakin?',
    text: "Pembelian akan diposting",
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#3085d6',
    cancelButtonColor: '#d33',
    confirmButtonText: 'Ya, Posting pembelian!'
  }).then((result) => {
    if (result.isConfirmed) {
      $.ajax({
        type  : 'post',
        url   : base_url+'/pembelian/unposting',
        async : false,
        // dataType : 'json',
        data:{id:id},
        success : function(data){
          //reload table
          location.reload(true);
          Swal.fire(
            'Diposting!',
            'Pembelian telah diposting',
            'success'
            )
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
  })
});
$('.saveSupplier').click(function() {

  // Ambil data dari form
  var supplier = $('#supplier').val();
  var pajak = $('#pajak').val();
  var invoice = $('#invoice').val();  // Perbaikan: mengganti variabel pajak kedua dengan invoice

  $.ajax({
    type: "POST",
    url: base_url + "pembelian/updateSupplier/" + getLastSegment(), // URL untuk menambahkan material (ganti dengan URL yang sesuai)
    data: {
      supplier: supplier,
      pajak: pajak,
      invoice: invoice
    },
    success: function(response) {
      // Tampilkan pesan sukses jika berhasil menambahkan material
      if(response.status === 'success') {
        Swal.fire({
        title: "Good job!",
        text: "Updated!",
        icon: "success"
      });
        $('#addMaterialModal').modal('hide'); // Tutup modal setelah berhasil
         $('#tabel_serverside').DataTable().ajax.reload(); // Reload halaman untuk memperbarui tabel
      } else {
        alert('Error adding material');
      }
    },
    error: function() {
      alert('Error connecting to server');
    }
  });
});
$(document).on('click', '.deleteMaterial', function(e) {
  e.preventDefault();
  var materialId = $(this).data('id'); // Mendapatkan ID material dari data attribute

  // Menampilkan konfirmasi dengan SweetAlert
  Swal.fire({
    title: 'Are you sure?',
    text: "You won't be able to revert this!",
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#d33',
    cancelButtonColor: '#3085d6',
    confirmButtonText: 'Yes, delete it!',
  }).then((result) => {
    if (result.isConfirmed) {
      // Jika konfirmasi diterima, lakukan penghapusan
      $.ajax({
        type: "POST",
        url: base_url + "pembelian/delete/" + materialId, // Endpoint untuk menghapus material
        data: {},
        success: function(response) {
          if (response.status === 'success') {
            Swal.fire({
              icon: 'success',
              title: 'Deleted!',
              text: 'Material has been deleted.',
            });
             $('#tabel_serverside').DataTable().ajax.reload(); // Reload halaman untuk memperbarui data
          } else {
            Swal.fire({
              icon: 'error',
              title: 'Error',
              text: 'Error deleting material!',
            });
          }
        },
        error: function() {
          Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Error connecting to the server!',
          });
        }
      });
    }
  });
});
$(document).on('click', '.editMaterial', function(e) {
        e.preventDefault();

        // Ambil ID material dari atribut data-id
        var materialId = $(this).data('id');
        
        // Menggunakan AJAX untuk mendapatkan data material
        $.ajax({
            url: base_url+'pembelian/get/' + materialId,  // Endpoint untuk mengambil data material
            type: 'GET',
            success: function(response) {
                if (response.status === 'success') {
                    var material = response.data;
                    
                    // Tampilkan SweetAlert dengan form edit
                    Swal.fire({
                        title: 'Edit Material',
                        html: `
                            <input type="hidden" id="materialId" value="${material.id}" />
                            <div>
                                <label for="materialCode">Material Code</label>
                                <input type="text" id="materialCode" class="swal2-input" value="${material.material_code}" required />
                            </div>
                            <div>
                                <label for="materialQty">Quantity</label>
                                <input type="number" id="materialQty" class="swal2-input" value="${material.material_qty}" required />
                            </div>
                            <div>
                                <label for="harga">Harga</label>
                                <input type="number" id="harga" class="swal2-input" value="${material.harga}" required />
                            </div>
                            <div>
                                <label for="id_currency">Currency</label>
                                <input type="number" id="id_currency" class="swal2-input" value="${material.id_currency}" required />
                            </div>
                            <div>
                                <label for="disc1">Discount 1</label>
                                <input type="number" id="disc1" class="swal2-input" value="${material.disc1}" />
                            </div>
                            <div>
                                <label for="disc2">Discount 2</label>
                                <input type="number" id="disc2" class="swal2-input" value="${material.disc2}" />
                            </div>
                            <div>
                                <label for="disc3">Discount 3</label>
                                <input type="number" id="disc3" class="swal2-input" value="${material.disc3}" />
                            </div>
                            <div>
                                <label for="potongan">Potongan</label>
                                <input type="number" id="potongan" class="swal2-input" value="${material.potongan}" />
                            </div>
                            <div>
                                <label for="pajak">Tax</label>
                                <input type="number" id="pajak" class="swal2-input" value="${material.pajak}" />
                            </div>
                        `,
                        showCancelButton: true,
                        confirmButtonText: 'Update',
                        cancelButtonText: 'Cancel',
                        preConfirm: function() {
                            // Ambil nilai input dari form SweetAlert
                            var updatedData = {
                                materialCode: $('#materialCode').val(),
                                materialQty: $('#materialQty').val(),
                                harga: $('#harga').val(),
                                id_currency: $('#id_currency').val(),
                                disc1: $('#disc1').val(),
                                disc2: $('#disc2').val(),
                                disc3: $('#disc3').val(),
                                potongan: $('#potongan').val(),
                                pajak: $('#pajak').val()
                            };
                            return updatedData;
                        }
                    }).then((result) => {
                        // Jika tombol Update diklik
                        if (result.isConfirmed) {
                            // Kirim data yang diupdate ke server menggunakan AJAX
                            var materialId = $('#materialId').val();
                            var updatedData = result.value;
                            
                            $.ajax({
                                url: 'material/update/' + materialId,  // Endpoint untuk update material
                                type: 'POST',
                                data: updatedData,
                                success: function(response) {
                                    if (response.status === 'success') {
                                        Swal.fire({
                                            icon: 'success',
                                            title: 'Material Updated!',
                                            text: response.message
                                        }).then(() => {
                                            location.reload();  // Reload halaman untuk melihat perubahan
                                        });
                                    } else {
                                        Swal.fire({
                                            icon: 'error',
                                            title: 'Error',
                                            text: response.message
                                        });
                                    }
                                },
                                error: function() {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Error',
                                        text: 'Failed to update material.'
                                    });
                                }
                            });
                        }
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Material not found.'
                    });
                }
            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Failed to fetch material data.'
                });
            }
        });
    });

    $('.importPO').on('click',function(){
      let curr = $('#id_currency').val();
      Swal.fire({
        title: `Import From Purchase Order`,
        // html: `<input type="text" id="password" class="swal2-input" placeholder="Password baru">`,
        html:`<form id="form_add_data">
        <div class="form-group">
        <label for="kode">Kode</label>
        <input type="text" class="form-control" id="kode" aria-describedby="kodeHelp" placeholder="Kode">
        </div>
        </form>`,
        confirmButtonText: 'Confirm',
        focusConfirm: false,
        preConfirm: () => {
          const kode = Swal.getPopup().querySelector('#kode').value
          if (!kode) {
            Swal.showValidationMessage('Silakan lengkapi data')
          }
          return {kode:kode }
        }
      }).then((result) => {
        $.ajax({
          type : "POST",
          url  : base_url+'/pembelian/importpo',
          async : false,
          // dataType : "JSON",
          data : {kode:result.value.kode,id:getLastSegment(), curr:curr},
          success: function(data){
            if (data.status == true) {
              $('#tabel_serverside').DataTable().ajax.reload();
              Swal.fire({
                position: 'center',
                icon: 'success',
                title: `${data.message}`,
                showConfirmButton: false,
                timer: 1500
              })

          }else{
            Swal.fire({
              position: 'center',
              icon: 'error',
              title: `${data.message}`,
              showConfirmButton: false,
              timer: 1500
            })
          }
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
    
      })
    })
