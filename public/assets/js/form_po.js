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
            materialOptions += `<option value="${material.id}" 
                                      data-price="${material.price}">
                                  ${material.name} - ${material.nama_satuan}(${material.kode_satuan}) - ${material.curr_code} ${material.price}
                               </option>`;
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
    $('#addForm')[0].reset();

    // Ambil opsi material dan masukkan ke dropdown
    getMaterialOption().then(function(options) {
      $('#id_material').html(options); // Masukkan opsi ke dalam elemen select
      $('#addMaterialModal').modal('show'); // Tampilkan modal
    }).catch(function(error) {
      alert(error); // Tampilkan error jika gagal mengambil data
    });
  });
$('#id_material').on('change', function() {
  // Dapatkan harga dari atribut data-price dari opsi yang dipilih
  const selectedPrice = $(this).find(':selected').data('price');

  // Isi input unit_price dengan harga yang diambil
  if (selectedPrice) {
    $('#unit_price').val(selectedPrice);
  } else {
    $('#unit_price').val(''); // Kosongkan input jika tidak ada harga
  }
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
      "url" :base_url+"/purchase/listdataPo/"+getLastSegment() , // json datasource 
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
        return row[7]
    }},
    {mRender: function (data, type, row) {
        return`
         <button class="btn btn-warning btn-sm editBtn" id = "${row[7]}">Edit</button>
         <button class="btn btn-danger btn-sm deleteBtn" id = "${row[7]}">Delete</button>
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
    var id_material = $('#id_material').val();
    var quantity = $('#quantity').val();
    var unit_price = $('#unit_price').val();
    var id_currency = $('#id_currency').val();
    var remarks = $('#remarks').val();
    id_po = getLastSegment()

    // Kirim data melalui AJAX ke server
    $.ajax({
      type: "POST",
      url: base_url + "purchase/addPOList", // URL untuk menambahkan material (ganti dengan URL yang sesuai)
      data: {
        id_material: id_material,
        quantity: quantity,
        price: unit_price,
        id_currency: id_currency,
        id_po : id_po,
        remarks : remarks
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
  $('.updatePO').click(function() {
    let id = getLastSegment();
    let date = $('#po_date').val();
    let arrival_target = $('#po_arrival_target').val();
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
      text: "Data akan diupdate",
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      confirmButtonText: 'Ya'
    }).then((result) => {
      if (result.isConfirmed) {
        $.ajax({
          type: 'post',
          url: base_url + '/purchase/update/' + id,
          async: false,
          data: {date : date , arrival_target : arrival_target},
          success: function(data) {
            //reload table
            location.reload(true);
            Swal.fire(
              'Diupdate!',
              'Data telah diupdate',
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

$(document).on('click', '.editBtn', function(e) {
    e.preventDefault();
    
    var productId = $(this).attr('id');
    
    $.ajax({
        url: base_url + 'proformainvoice/getProduct/' + productId,
        type: 'GET',
        success: function(response) {
            if (response.status === 'success') {
                var product = response.data;
                
                Swal.fire({
                    title: 'Edit Product',
                    html: `
        <form id="form_edit_product">
            
            <div class="form-group">
                <label for="hs_code">HS Code</label>
                <input type="text" class="form-control" value="${product.hs_code}" disabled />
            </div>
            <div class="form-group">
                <label for="quantity">Quantity</label>
                <input type="number" class="form-control" id="quantity" value="${product.quantity}" placeholder="Quantity" />
            </div>
            <div class="form-group">
                <label for="unit_price">Unit Price</label>
                <input type="number" class="form-control" id="unit_price" value="${product.unit_price}" placeholder="Unit Price" />
            </div>
              <div class="form-group">
                <label for="remarks">Remark</label>
                <input type="text" class="form-control" id="remarks" value="${product.remarks}" placeholder="Item Description" />
            </div>
        </form>
    `,
                    showCancelButton: true,
                    confirmButtonText: 'Update',
                    cancelButtonText: 'Cancel',
                    preConfirm: function() {
                        return {
                            id_product: $('#id_product').val(),
                            remarks: $('#remarks').val(),
                            hs_code: $('#hs_code').val(),
                            quantity: $('#quantity').val(),
                            unit_price: $('#unit_price').val(),
                            total_price: $('#total_price').val()
                        };
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        var updatedData = result.value;
                        
                        $.ajax({
                            url: base_url + 'proformainvoice/updateProduct/' + product.id,
                            type: 'POST',
                            data: updatedData,
                            success: function(response) {
                                if (response.status === 'success') {
                                    Swal.fire('Updated!', 'Product details have been updated.', 'success');
                                    // Reload your table or update UI here
                                } else {
                                    Swal.fire('Error', 'Failed to update product.', 'error');
                                }
                            },
                            error: function() {
                                Swal.fire('Error', 'Failed to update product.', 'error');
                            }
                        });
                    }
                });
            } else {
                Swal.fire('Error', 'Product not found.', 'error');
            }
        },
        error: function() {
            Swal.fire('Error', 'Failed to fetch product data.', 'error');
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
