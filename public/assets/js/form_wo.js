var loc = window.location;
var base_url = loc.protocol + "//" + loc.hostname + (loc.port? ":"+loc.port : "") + "/";

function getLastSegment() {
  var pathname = window.location.pathname; // Mendapatkan path dari URL
  var segments = pathname.split('/').filter(function(segment) { return segment.length > 0; });
  return segments[segments.length - 1]; // Mengambil segment terakhir
}
$(document).ready(function() {
  woTable(getLastSegment())

function woTable(id_wo){
  $.ajax({
    type : "GET",
    url  : base_url+"getWo/"+id_wo,
    async : false,
    success: function(data){
      console.log('ok')
    wo(data)

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
    function wo(data){
  d = JSON.parse(data);
  console.log(d)
  let no = 1;
  let table = ''
  $.each(d, function(k, v){
    table+=     `<tr>`;
    table+=   `<td>${no++}</td>`;
    table+=   `<td>${d[k].kode}</td>`;
    table+=   `<td>${d[k].nama}</td>`;
    table+=   `<td>${d[k].quantity}</td>`;
    table+=   `<td><a href="javascript:void(0);" class="btn btn-danger btn-sm remove"  id="${d[k].id_det}" nama = "${d[k].nama}" code = "${d[k].kode}">Remove</a>`;
    table+=   `</tr>`

  })
  $('#tabel_wo').html(table)
}


piTable(getLastSegment())

function piTable(id_wo){
  $.ajax({
    type : "GET",
    url  : base_url+"WogetPi/"+id_wo,
    async : false,
    success: function(data){
      console.log('ok')
    Pi(data)

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
    function Pi(data){
  d = JSON.parse(data);
  console.log(d)
  let no = 1;
  let table = ''
  $.each(d, function(k, v){
    table+=     `<tr>`;
    table+=   `<td>${no++}</td>`;
    table+=   `<td>${d[k].kode}</td>`;
    table+=   `<td>${d[k].nama}</td>`;
    table+=   `<td>${d[k].qty_tersedia}</td>`;
    table+=   `<td><a href="javascript:void(0);" class="btn btn-warning btn-sm add" max = "${d[k].qty_tersedia}" id="${d[k].id_product}" nama = "${d[k].nama}" code = "${d[k].kode}">Add</a>`;
    table+=   `</tr>`

  })
  $('#tabel_pi').html(table)
}
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


 
   });
      $('#tabel_pi').on('click', '.add', function() {
        let productId = $(this).attr('id');
        let productNama = $(this).attr('nama');
        let productCode = $(this).attr('code');
        let max = $(this).attr('max');
        let wo_id = getLastSegment();
        // showAddProductPopup(productId, productNama, productCode);
 
       Swal.fire({
        title: `Tambah Proforma Invoice`,
        html: `
        <form id="form_add_data">
            <div class="form-group">
                <label for="quantity">Kode</label>
                <input type="text" class="form-control" id="quantity" aria-describedby="kodeHelp" placeholder="quantity max = ${max}">
            </div>
        </form>`,
        confirmButtonText: 'Confirm',
        focusConfirm: false,
        preConfirm: () => {
            const quantity = Swal.getPopup().querySelector('#quantity').value;
            if (!quantity) {
                Swal.showValidationMessage('Silakan lengkapi data');
            }
            return { quantity: quantity };
        }
    }).then((result) => {
        $.ajax({
            type: "POST",
            url: base_url + '/workOrder/addDetail',
            async: false,
            data: { quantity: result.value.quantity, product_id:productId, wo_id:wo_id },
            success: function (data) {
                 $('#tabel_serverside').DataTable().ajax.reload();
                Swal.fire({
                    position: 'center',
                    icon: 'success',
                    title: `berhasil ditambahkan.`,
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
});
    
    
   $('#tabel_wo').on('click', '.remove', function () {
    let id = $(this).attr('id');

    // Tampilkan dialog konfirmasi
    Swal.fire({
        title: 'Apakah Anda yakin?',
        text: "Data ini akan dihapus secara permanen!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, hapus!'
    }).then((result) => {
        if (result.isConfirmed) {
            // Kirim request AJAX untuk delete
            $.ajax({
                type: "POST",
                url: base_url + '/workOrder/delete/'+id,
                data: {},
                success: function(response) {
          
                    Swal.fire({
                        icon: 'success',
                        title: 'Dihapus!',
                        text: 'Data berhasil dihapus.',
                        showConfirmButton: false,
                        timer: 1500
                    });
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