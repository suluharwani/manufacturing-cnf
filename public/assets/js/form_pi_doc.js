var loc = window.location;
var base_url = loc.protocol + "//" + loc.hostname + (loc.port? ":"+loc.port : "") + "/";

function getLastSegment() {
  var pathname = window.location.pathname; // Mendapatkan path dari URL
  var segments = pathname.split('/').filter(function(segment) { return segment.length > 0; });
  return segments[segments.length - 1]; // Mengambil segment terakhir
}
$(document).ready(function() {
    function getProductOption() {
    return new Promise((resolve, reject) => {
      let productOptions  = '';

      $.ajax({
        type: 'GET',
        url: base_url + 'product/getProduct', // Endpoint untuk mendapatkan produk
        success: function(response) {
          // Buat opsi produk dari data yang diterima
          response.product.forEach(product => {
            productOptions += `<option value="${product.id}">${product.kode} - ${product.nama})</option>`;
          });

          // Resolving the promise dengan productOptions setelah sukses
          resolve(productOptions);
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
    $('#addForm')[0].reset();

    // Ambil opsi material dan masukkan ke dropdown
    getProductOption().then(function(options) {
      $('#id_product').html(options); // Masukkan opsi ke dalam elemen select
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

    $('#tabel_serverside_file').DataTable( {
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
        "url" :base_url+"/proformainvoice/file/"+getLastSegment() , // json datasource 
        "type": "post",  // method  , by default get
        // "async": false,
        "dataType": 'json',
        "data":{},
      },
  
      columns: [
      {},
      {mRender: function (data, type, row) {
          return row[5]
      }},
      {mRender: function (data, type, row) {
          return row[1]
      }},
      {mRender: function (data, type, row) {
          return row[2]
      }},
      {mRender: function (data, type, row) {
          return `<a href="${base_url}${row[3]}">View File</a>`
      }},
      {mRender: function (data, type, row) {
          return`
           <button class="btn btn-warning btn-sm editBtnDoc" id = "${row[4]}">Edit</button>
           <button class="btn btn-danger btn-sm deleteBtnDoc" id = "${row[4]}">Delete</button>
          `
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
      "url" :base_url+"/proformainvoice/listdataPi/"+getLastSegment() , // json datasource 
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
        return row[2]
    }},
    {mRender: function (data, type, row) {
        return row[4]
    }},
    {mRender: function (data, type, row) {
        return row[5]
    }},
    {mRender: function (data, type, row) {
      if(row[9] == 1){
        return 'Posted'
      }else if(row[9] == 2){
        return 'Delivered'
      }else{
        return 'Production'
      }
  }},
    {mRender: function (data, type, row) {
      if(row[9] == 1){
        return 'Posted'
      }else if(row[9] == 2){
        return 'Delivered'
      }else{
        return ` <button class="btn btn-warning btn-sm viewProd" totalOrder = "${row[4]}" idProd = "${row[1]}">View Production</button>`
      }

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
  $('#addForm').submit(function(event) {
    event.preventDefault();

    // Ambil data dari form
    var id_product = $('#id_product').val();
    var quantity = $('#quantity').val();
    var unit_price = $('#unit_price').val();
    var id_currency = $('#id_currency').val();
    invoice_id = getLastSegment()

    // Kirim data melalui AJAX ke server
    $.ajax({
      type: "POST",
      url: base_url + "proformainvoice/addProduct", // URL untuk menambahkan material (ganti dengan URL yang sesuai)
      data: {
        id_product: id_product,
        quantity: quantity,
        unit_price: unit_price,
        id_currency: id_currency,
        invoice_id : invoice_id
      },
      success: function(response) {
        // Tampilkan pesan sukses jika berhasil menambahkan material
          Swal.fire({
  title: "Good job!",
  text: "Material added successfully!",
  icon: "success"
});
          $('#tabel_serverside').DataTable().ajax.reload();
          $('#addMaterialModal').modal('hide'); // Tutup modal setelah berhasil
       
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


$(document).on('click', '.deleteBtn', function(e) {
    e.preventDefault();
    
    var productId = $(this).attr('id');
    
    Swal.fire({
        title: 'Are you sure?',
        text: "This will permanently delete the product.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: base_url + 'proformainvoice/deleteProduct/' + productId,
                type: 'POST',
                success: function(response) {
                    if (response.status === 'success') {
                        Swal.fire('Deleted!', 'Product has been deleted.', 'success');
                        // Reload your table or update UI here
                    } else {
                        Swal.fire('Error', 'Failed to delete product.', 'error');
                    }
                },
                error: function() {
                    Swal.fire('Error', 'Failed to delete product.', 'error');
                }
            });
        }
    });
});

$('#uploadBtn').click(function() {
  Swal.fire({
      title: 'Upload File',
      html: `
          <input type="text" id="document_name" class="swal2-input" placeholder="Document Name">
          <input type="text" id="document_code" class="swal2-input" placeholder="Document Code">
          <input type="file" id="fileInput" class="swal2-input">
          Only PDF, JPG, JPEG, and PNG files are allowed.
      `,
      showCancelButton: true,
      confirmButtonText: 'Upload',
      preConfirm: () => {
          const fileInput = document.getElementById('fileInput');
          const file = fileInput.files[0];
          const idPi = getLastSegment();
          const documentName = document.getElementById('document_name').value;
          const documentCode = document.getElementById('document_code').value;

          if (!file) {
              Swal.showValidationMessage('Please select a file.');
              return false;
          }
          if (!documentName) {
              Swal.showValidationMessage('Please enter a document name.');
              return false;
          }
          if (!documentCode) {
              Swal.showValidationMessage('Please enter a document code.');
              return false;
          }

          const formData = new FormData();
          formData.append('file', file);
          formData.append('id_pi', idPi);
          formData.append('document_name', documentName);
          formData.append('document_code', documentCode);

          return fetch(`${base_url}proformainvoice/upload`
          , {
              method: 'POST',
              body: formData
          })
          .then(response => response.json())
          .then(data => {
              if (data.status === 'success') {
                  Swal.fire({
                      icon: 'success',
                      title: 'Success',
                      text: data.message
                  });
                  $('#tabel_serverside_file').DataTable().ajax.reload();
              } else {
                  Swal.fire({
                      icon: 'error',
                      title: 'Error',
                      text: data.message
                  });
              }
          })
          .catch(error => {
              Swal.fire({
                  icon: 'error',
                  title: 'Error',
                  text: 'An error occurred while uploading the file.'
              });
          });
      }
  });
});



// Delete button functionality
$(document).on('click', '.deleteBtnDoc', function() {
  const documentId = $(this).attr('id');
  Swal.fire({
      title: 'Are you sure?',
      text: "You won't be able to revert this!",
      icon: 'warning',
      showCancelButton: true,
      confirmButtonText: 'Yes, delete it!',
      cancelButtonText: 'No, cancel!',
  }).then((result) => {
      if (result.isConfirmed) {
          $.ajax({
              url: base_url+`proformainvoice/delete/` + documentId,
              method: 'DELETE',
              success: function(data) {
                  if (data.status === 'success') {
                    $('#tabel_serverside_file').DataTable().ajax.reload();
                      Swal.fire(
                          'Deleted!',
                          data.message,
                          'success'
                      );
                      // Optionally, refresh the document table here
                  } else {
                      Swal.fire(
                          'Error!',
                          data.message,
                          'error'
                      );
                  }
              },
              error: function() {
                  Swal.fire(
                      'Error!',
                      'An error occurred while deleting the document.',
                      'error'
                  );
              }
          });
      }
  });
});
$(document).on('click', '.editBtnDoc', function() {
  const documentId = $(this).attr('id'); // Get the document ID from the button's ID
  // Fetch the current document details using AJAX
  $.ajax({
      url: `${base_url}proformainvoice/getDocumentDetails`, // Adjust the URL as needed
      method: 'GET',
      data: { id: documentId },
      dataType: 'json',
      success: function(data) {
          if (data.status === 'success') {
              // Show SweetAlert2 popup with current document details
              Swal.fire({
                  title: 'Edit Document',
                  html: `
                      <input type="text" id="document_name" class="swal2-input" placeholder="Document Name" value="${data.document.document}">
                      <input type="text" id="document_code" class="swal2-input" placeholder="Document Code" value="${data.document.code}">
                  `,
                  showCancelButton: true,
                  confirmButtonText: 'Update',
                  preConfirm: () => {
                      const documentName = document.getElementById('document_name').value;
                      const documentCode = document.getElementById('document_code').value;

                      if (!documentName || !documentCode) {
                          Swal.showValidationMessage('Please enter both document name and code.');
                          return false;
                      }

                      // Send the updated data to the server
                      return $.ajax({
                          url: `${base_url}proformainvoice/updateDocument`, // Adjust the URL as needed
                          method: 'POST',
                          data: {
                              id: documentId,
                              document_name: documentName,
                              document_code: documentCode
                          }
                      });
                  }
              }).then((result) => {
                  if (result.isConfirmed) {
                      if (result.value.status === 'success') {
                          Swal.fire('Updated!', result.value.message, 'success');
                    $('#tabel_serverside_file').DataTable().ajax.reload();
                          
                          // Optionally, refresh the table or data here
                      } else {
                          Swal.fire('Error!', result.value.message, 'error');
                      }
                  }
              });
          } else {
              Swal.fire('Error!', 'Failed to fetch document details.', 'error');
          }
      },
      error: function() {
          Swal.fire('Error!', 'An error occurred while fetching document details.', 'error');
      }
  });
});
$('#generateReportBtnProd').on('click', function () {

  
});
$(document).on('click', '.viewProd', function(e) {
  e.preventDefault();
  const totalOrder = $(this).attr('totalOrder');
  const prodId = $(this).attr('idProd');
  const piId = getLastSegment();
      // Tambahkan waktu ke endDate


// Jika startDate adalah datetime-local, maka format juga

  $.ajax({
      url: base_url+'report/productionReportPIByProduct',  
      method: 'POST',
      data: {
        prodId: prodId,
        piId: piId,
      },
      success: function (result) {
          data = JSON.parse(result);
          
          let no = 1;
          let no_ = 1;
          let qt_wh = 0;
          let qt_prod = 0;
          var data = JSON.parse(result);
          var tableBody = $('#resultTableBody');
          tableBody.empty(); // Clear existing rows
          let row = `<thead>
          
  <tr>
      <th>No</th>
      <th>DATE</th>
      <th>WORK ORDER</th>
      <th>PRODUCTION AREA</th>
      <th>CODE</th>
      <th>HSCODE</th>
      <th>NAME</th>
      <th>QUANTITY</th>
  </tr>
</thead>`; // Initialize the row variable
          // Loop through the pembelian array and create table rows
          // hs_code
          // product_code
          // product_name
          // production_area_name
          // quantity
          // wo
                  data['prod'].forEach(function (item) {
              row += `
              ${qt_prod += parseInt(item.quantity)}
                  <tr>
                      <td>${no++}</td>
                      <td>${formatDateIndo(item.created_at)}</td>
                      <td>${item.wo} </td> 
                      <td>${item.production_area_name} </td> 
                      <td>${item.product_code}</td>
                       <td><p style="color:blue;" onclick="checkHsCode('${item.hs_code}')">${item.hs_code}</p></td>
                      <td>${item.product_name}</td>
                      <td>${item.quantity}</td>
                  </tr>
              `;

          });
          row += `  <tr>
                      <td colspan = "8" >Finished Good</td>
                  </tr>  `
          data['wh'].forEach(function (wh) {

              row += `
                  ${qt_wh += parseInt(wh.quantity)}

                  <tr>
                      <td>${no_++}</td>
                      <td>${formatDateIndo(wh.created_at)}</td>
                      <td>${wh.wo} </td> 
                      <td>${wh.production_area_name} </td> 
                      <td>${wh.product_code}</td>
                      <td><p style="color:blue;" onclick="checkHsCode('${wh.hs_code}')">${wh.hs_code}</p></td>
                      <td>${wh.product_name}</td>
                      <td>${wh.quantity}</td>
                  </tr>
              `;

          });

          tableBody = row; // Append the row to the table body
          
          $('#totalOrder').html(parseInt(totalOrder)); // Update the table container
          $('#unProgress').html(parseInt(totalOrder)-(parseInt(qt_prod)+parseInt(qt_wh))); // Update the table container
          $('#qtprod').html(qt_prod); // Update the table container
          $('#qtwh').html(qt_wh); // Update the table container
          $('#totalProd').html(parseInt(qt_prod)+parseInt(qt_wh)); // Update the table container
          $('#resultTableContainer').html(tableBody); // Update the table container
          $('#resultTableContainer').html(tableBody); // Update the table container
          $('#prodView').modal('show');

      }
  });
});
 

function formatDateIndo(dateString) {
  // Create a new Date object from the input date string
  const date = new Date(dateString);

  // Get the day, month, and year
  const day = String(date.getDate()).padStart(2, '0'); // Pad with leading zero if needed
  const month = String(date.getMonth() + 1).padStart(2, '0'); // Months are zero-based
  const year = date.getFullYear();

  // Return the formatted date in DD/MM/YYYY
  return `${day}/${month}/${year}`;
}
function formatAngka(angka) {
  // Memisahkan bagian desimal dan ribuan
  let [bagianRibuan, bagianDesimal] = angka.toString().split(".");
  
  // Menambahkan titik sebagai pemisah ribuan
  bagianRibuan = bagianRibuan.replace(/\B(?=(\d{3})+(?!\d))/g, ".");
  
  // Mengatur bagian desimal, maksimal 2 angka
  if (bagianDesimal) {
      bagianDesimal = bagianDesimal.substring(0, 2);
  } else {
      bagianDesimal = "00"; // Jika tidak ada bagian desimal
  }
  
  return `${bagianRibuan},${bagianDesimal}`;
}
$(document).on('click', '.finish', function(e) {
  e.preventDefault();
  var id = getLastSegment();

  // Menampilkan konfirmasi dengan SweetAlert
  Swal.fire({
    title: 'Are you sure?',
    text: "Please ensure that all production tasks are completed before clicking the 'Finish' button!",
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#3fe86c',
    cancelButtonColor: '#3085d6',
    confirmButtonText: 'Yes, finish it!',
  }).then((result) => {
    if (result.isConfirmed) {
      // Jika konfirmasi diterima, lakukan penghapusan
      $.ajax({
        type: "POST",
        url: base_url + "proformainvoice/finish/" + id, // Endpoint untuk menghapus material
        data: {},
        success: function(response) {
          if (response.status === 'success') {
            Swal.fire({
              icon: 'success',
              title: 'Finished!',
              text: 'Proforma invoice has been completed.',
            });
            location.reload(true);// Reload halaman untuk memperbarui data
          } else {
            Swal.fire({
              icon: 'error',
              title: 'Error',
              text: 'Cannot process request!',
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
$(document).on('click', '.batalFinish', function(e) {
  e.preventDefault();
  var id = getLastSegment();

  // Menampilkan konfirmasi dengan SweetAlert
  Swal.fire({
    title: 'Are you sure?',
    text: "Please ensure that all production tasks are completed before clicking the 'Finish' button!",
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#3fe86c',
    cancelButtonColor: '#3085d6',
    confirmButtonText: 'Yes, finish it!',
  }).then((result) => {
    if (result.isConfirmed) {
      // Jika konfirmasi diterima, lakukan penghapusan
      $.ajax({
        type: "POST",
        url: base_url + "proformainvoice/batalFinish/" + id, // Endpoint untuk menghapus material
        data: {},
        success: function(response) {
          if (response.status === 'success') {
            Swal.fire({
              icon: 'success',
              title: 'Finished!',
              text: 'Proforma invoice has been canceled.',
            });
            location.reload(true);// Reload halaman untuk memperbarui data
          } else {
            Swal.fire({
              icon: 'error',
              title: 'Error',
              text: 'Cannot process request!',
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

$('.printButton').on('click', function() {
  var invoiceId = getLastSegment(); // Replace with the actual invoice ID
  window.open(base_url + "proformainvoice/print/" + invoiceId, '_blank');
});
$('.printDeliveryButton').on('click', function() {
  var invoiceId = getLastSegment(); // Replace with the actual invoice ID
  window.open(base_url + "proformainvoice/delivery_note/" + invoiceId, '_blank');
});