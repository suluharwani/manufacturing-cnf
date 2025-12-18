var loc = window.location;
var base_url = loc.protocol + "//" + loc.hostname + (loc.port ? ":" + loc.port : "") + "/";
function decimalToRupiah(angka) {
    // Pastikan input adalah number
    if (typeof angka !== 'number') {
      angka = parseFloat(angka);
      if (isNaN(angka)) return 'Invalid number';
    }
  
    // Format ke Rupiah dan hilangkan .00 di belakang jika ada
    let rupiah = angka.toLocaleString('id-ID', {
      style: 'currency',
      currency: 'IDR',
      minimumFractionDigits: 0,
      maximumFractionDigits: 2
    });
  
    // Hilangkan .00 di belakang jika ada
    rupiah = rupiah.replace(/\.00$/, '');
  
    return rupiah;
  }
function getLastSegment() {
    var pathname = window.location.pathname; // Mendapatkan path dari URL
    var segments = pathname.split('/').filter(function(segment) { return segment.length > 0; });
    return segments[segments.length - 1]; // Mengambil segment terakhir
  }
productData()
function productData(){
    $.ajax({
      type : "get",
      url  : base_url+"product/getProductData/"+getLastSegment(),
      async : false,
      success: function(data){
        res = data.product;
       $('#code').val(res.kode);
       $('#hscode').val(res.hs_code);
       $('#category').val(res.category);
       $('#product_name').val(res.nama);
       $('#length').val(res.length);
       $('#width').val(res.width);
       $('#height').val(res.height);
       $('#cbm').val(res.cbm);
        
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

$(document).ready(function () {
  var dataTable = $('#tabelDesign').DataTable( {
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
      "url" :base_url + 'product/getFile/1/'+getLastSegment(),
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
       return row[3]; 
     }},
    {mRender: function (data, type, row) {
      return row[4];
    }},

    {mRender: function (data, type, row) {
      return `<a href="javascript:void(0);" class="btn btn-success btn-sm download" id="${row[1]}" file="${row[5]}">Download</a>
              <a href="javascript:void(0);" class="btn btn-danger btn-sm delete" id="${row[1]}" file="${row[5]}">Delete</a>`; 
    }
  }
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
  
$('#uploadBtn').click(function() {
  Swal.fire({
      title: 'Upload File',
      html: `
          <input type="text" id="document_name" class="swal2-input" placeholder="Document Name">
          <input type="text" id="document_desc" class="swal2-input" placeholder="Document Code">
          <input type="file" id="fileInput" class="swal2-input">
          Only PDF, JPG, JPEG, and PNG files are allowed.
      `,
      showCancelButton: true,
      confirmButtonText: 'Upload',
      preConfirm: () => {
          const fileInput = document.getElementById('fileInput');
          const file = fileInput.files[0];
          const idProduct = getLastSegment();
          const documentName = document.getElementById('document_name').value;
          const documentDesc = document.getElementById('document_desc').value;

          if (!file) {
              Swal.showValidationMessage('Please select a file.');
              return false;
          }
          if (!documentName) {
              Swal.showValidationMessage('Please enter a document name.');
              return false;
          }
          if (!documentDesc) {
              Swal.showValidationMessage('Please enter a document code.');
              return false;
          }

          const formData = new FormData();
          formData.append('file', file);
          formData.append('product_id', idProduct);
          formData.append('name', documentName);
          formData.append('desc', documentDesc);
          formData.append('category', 1);

          return fetch(`${base_url}product/uploadFile`
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
                  $('#tabelDesign').DataTable().ajax.reload();
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
$(document).on('click', '.delete', function() {
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
              url: base_url+`product/deleteFile/` + documentId,
              method: 'DELETE',
              success: function(data) {
                  if (data.status) {
                    $('#tabelDesign').DataTable().ajax.reload();
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
$(document).on('click', '.download', function() {
    const button = $(this);
    const url = base_url+"uploads/file/"+ $(this).attr('file');;
    const filename =  $(this).attr('file');
    console.log(url);
    // Tampilkan loading (opsional)
    button.html('<i class="fa fa-spinner fa-spin"></i> Mengunduh...');
    button.prop('disabled', true);
    
    $.ajax({
      url: url,
      method: 'GET',
      xhrFields: {
        responseType: 'blob' // Untuk menerima data biner
      },
      success: function(data) {
        // Buat URL objek dari blob
        const blobUrl = URL.createObjectURL(data);
        
        // Buat elemen <a> sementara untuk download
        const a = document.createElement('a');
        a.href = blobUrl;
        a.download = filename;
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        
        // Bebaskan memori
        setTimeout(() => {
          URL.revokeObjectURL(blobUrl);
          button.html('Download Berhasil!');
          setTimeout(() => {
            button.html(`Download`);
            button.prop('disabled', false);
          }, 2000);
        }, 100);
      },
      error: function(xhr, status, error) {
        console.error('Gagal mengunduh:', error);
        button.html('Gagal Mengunduh!');
        setTimeout(() => {
          button.html(`Download ${filename}`);
          button.prop('disabled', false);
        }, 2000);
      }
    });
  });