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
     return `<a href="${base_url}wo/${row[1]}" target="_blank" class="btn btn-success btn-sm showPurchaseOrder" id="'+row[1]+'" >Detail</a>`; 
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

Warehouse()
function Warehouse(){
  $.ajax({
    type : "POST",
    url  : base_url+"production/warehouseList",
    async : false,
    success: function(data){
     tableWarehouse(data);

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
function tableWarehouse(data){
  d = JSON.parse(data);
  console.log(d)
  let no = 1;
  let table = ''
  $.each(d, function(k, v){
    table+=     `<tr>`;
    table+=   `<td>${no++}</td>`;
    table+=   `<td>${d[k].name}</td>`;
    table+=   `<td>${d[k].location}</td>`;
    table+=   `<td><a href="javascript:void(0);" class="btn btn-success btn-sm  view"  id="${d[k].id}" nama = "${d[k].name}">View</a> 
               <a href="javascript:void(0);" class="btn btn-default btn-sm  add"  id="${d[k].id}" nama = "${d[k].name}">Add Stock</a>
               <a href="javascript:void(0);" class="btn btn-warning btn-sm  move"  id="${d[k].id}" nama = "${d[k].name}">Stock Move</a>
               </td>`;
    table+=   `</tr>`

  })
  $('#isiWarehouse').html(table)
}
Production()
function Production(){
  $.ajax({
    type : "POST",
    url  : base_url+"production/productionList",
    async : false,
    success: function(data){
     tableProduction(data);

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
function tableProduction(data){
  d = JSON.parse(data);
  console.log(d)
  let no = 1;
  let table = ''
  $.each(d, function(k, v){
    table+=     `<tr>`;
    table+=   `<td>${no++}</td>`;
    table+=   `<td>${d[k].name}</td>`;
    table+=   `<td>${d[k].location}</td>`;
    table+=   `<td><a href="javascript:void(0);" class="btn btn-success btn-sm  view"  id="${d[k].id}" nama = "${d[k].name}">View</a> 
               <a href="javascript:void(0);" class="btn btn-primary btn-sm  add"  id="${d[k].id}" nama = "${d[k].name}">Add WO</a>
               <a href="javascript:void(0);" class="btn btn-warning btn-sm  wo_list"  id="${d[k].id}" nama = "${d[k].name}">WO Product</a>
               <a href="javascript:void(0);" class="btn btn-secondary btn-sm  production"  id="${d[k].id}" nama = "${d[k].name}">Production</a>
               </td>`;
    table+=   `</tr>`

  })
  $('#isiProduction').html(table)
}
$('#isiProduction').on('click', '.add', function () {
    let warehouse_id = $(this).attr('id');
    let nama = $(this).attr('nama');

    $.when(
        $.ajax({
            url: base_url + 'production/getWOList',
            method: 'POST',
            dataType: 'json' // Expecting JSON response
        })
    ).done(function(woResponse) {
        // Debugging: Log the responses to check their structure
        console.log(woResponse); // Debugging

        // Pastikan data adalah array yang benar
        if (Array.isArray(woResponse) && woResponse.length > 0) {
            let WoOptions = woResponse.map(wo => `<option value="${wo.id}">${wo.kode}</option>`).join('');

            Swal.fire({
                title: 'Tambah WO',
                html: `
                    <form id="form_add_data">
                      <div class="form-group">
                            <label for="wo_id">Proforma Invoice</label>
                            <select class="form-control" id="wo_id">
                                ${WoOptions}
                            </select>
                        </div>
                    </form>
                `,
                confirmButtonText: 'Confirm',
                focusConfirm: false,
                preConfirm: () => {
                    const wo_id = Swal.getPopup().querySelector('#wo_id').value;

                    if (!wo_id) {
                        Swal.showValidationMessage('Silakan lengkapi data');
                    }
                    return { wo_id: wo_id };
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        type: "POST",
                        url: base_url + 'production/addWo',
                        async: false,
                        data: {
                            wo_id: result.value.wo_id,production_id:warehouse_id,nama:nama
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

$('#isiProduction').on('click', '.wo_list', function () {
    var id = $(this).attr('id');
    fetchDataProd(id);
    $('#ProdModal').modal('show');


  });


  // Fetch and display data
function fetchDataProd(id) {
    $.ajax({
        url: `${base_url}production/getProductByWO/${id}`, // Ensure this URL is correct and working
        type: 'GET',
        success: function(response) {
            data = JSON.parse(response)
            let tableRowsProd = '';

            // Since the response is a direct array, loop through it
            if (data.length > 0) {
                // Loop through the data using a regular for loop
                for (let i = 0; i < data.length; i++) {
                    let item = data[i];
                    tableRowsProd += `
                        <tr data-id="${item.id}">
                            <td>${i + 1}</td>
                            <td>${item.invoice_number}</td>
                            <td>${item.wo}</td>
                            <td>${item.nama}</td>
                            <td>${item.quantity}</td>
                            <td>
                                <button class="btn btn-secondary btn-sm produksiStock" wo = "${item.wo}" invoice_number = "${item.invoice_number}" nama = "${item.nama}" quantity = "${item.quantity}" production_id ="${item.production_id}" pi_id ="${item.pi_id}"  wo_id ="${item.wo_id}" id_product="${item.id_product}">Produksi</button>
                            </td>
                        </tr>
                    `;
                }
            }  else {
                // If no data is found, display a 'no data' message
                tableRowsProd = '<tr><td colspan="4" class="text-center">No data available</td></tr>';
            }
            console.log(tableRowsProd)
            // Populate the table body with the generated rows
            $('#tableProd').html(tableRowsProd);
        },
        error: function(xhr, status, error) {
            console.error('Error:', error);  // Log error details for debugging
            Swal.fire({
                icon: 'error',
                title: 'Failed to load data',
                text: 'There was an error loading the data. Please try again.',
            });
        }
    });
}

$('#tableProd').on('click', '.produksiStock', function () {
    $('#ProdModal').modal('hide');
   wo =  $(this).attr('wo') 
   invoice_number =  $(this).attr('invoice_number') 
   nama =  $(this).attr('nama')
   quantity = $(this).attr('quantity') 
   production_id =$(this).attr('production_id')
   pi_id =$(this).attr('pi_id')  
   wo_id =$(this).attr('wo_id') 
   id_product=$(this).attr('id_product')
    Swal.fire({
        title: `Proses Ke Produksi`,
        html: `
        <form id="form_add_data">
            <div class="form-group">
                <label for="kode">PI</label>
                <input type="text" class="form-control"  aria-describedby="kodeHelp" value="${invoice_number}" disabled>
            </div>
            <div class="form-group">
                <label for="kode">WO</label>
                <input type="text" class="form-control" aria-describedby="kodeHelp"  value="${wo}" disabled>
            </div>
            <div class="form-group">
                <label for="kode">Product</label>
                <input type="text" class="form-control" aria-describedby="kodeHelp"  value="${nama}" disabled>
            </div>
            <div class="form-group">
                <label for="customer">Quantity</label>
                <input type="text" id="quantity" class="form-control" aria-describedby="kodeHelp">
                <div> max : ${quantity}</div>
            </div>
        </form>`,
        confirmButtonText: 'Confirm',
        focusConfirm: false,
        preConfirm: () => {
            const quantity = Swal.getPopup().querySelector('#quantity').value;
            if (!quantity) {
                Swal.showValidationMessage('Silakan lengkapi data');
            }
            return { quantity: quantity};
        }
    }).then((result) => {
        $.ajax({
            type: "POST",
            url: base_url + '/production/addProgress',
            async: false,
            data: {wo_id:wo_id, production_id:production_id,product_id:id_product, quantity: result.value.quantity, },
            success: function (data) {
                fetchDataProd(production_id);
                $('#ProdModal').modal('show');
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
})

$('#isiProduction').on('click', '.view', function () {
    var id = $(this).attr('id');
    fetchData(id);
    $('#woModal').modal('show');


  });


  // Fetch and display data
function fetchData(id) {
    $.ajax({
        url: `${base_url}production/getWOProduction/${id}`, // Ensure this URL is correct and working
        type: 'GET',
        success: function(response) {
            data = JSON.parse(response)
            let tableRows = '';

            // Since the response is a direct array, loop through it
            if (data.length > 0) {
                // Loop through the data using a regular for loop
                for (let i = 0; i < data.length; i++) {
                    let item = data[i];
                    tableRows += `
                        <tr data-id="${item.id}">
                            <td>${i + 1}</td>
                            <td>${item.pi}</td>
                            <td>${item.kode}</td>
                            <td>
                                <button class="btn btn-danger btn-sm deleteBtn" data-id="${item.id}">Delete</button>
                                <button class="btn btn-danger btn-sm view" data-id="${item.id}">VIEW</button>
                            </td>
                        </tr>
                    `;
                }
            }  else {
                // If no data is found, display a 'no data' message
                tableRows = '<tr><td colspan="4" class="text-center">No data available</td></tr>';
            }
            console.log(tableRows)
            // Populate the table body with the generated rows
            $('#tableBody').html(tableRows);
        },
        error: function(xhr, status, error) {
            console.error('Error:', error);  // Log error details for debugging
            Swal.fire({
                icon: 'error',
                title: 'Failed to load data',
                text: 'There was an error loading the data. Please try again.',
            });
        }
    });
}

$('#isiProduction').on('click', '.production', function () {
    var id = $(this).attr('id');
    fetchDataProdArea(id);
    $('#productionModal').modal('show');


  });


  // Fetch and display data
function fetchDataProdArea(id) {
    $.ajax({
        url: `${base_url}production/getProductionProduct/${id}`, // Ensure this URL is correct and working
        type: 'GET',
        success: function(response) {
            data = JSON.parse(response)
            let tableRows = '';

            // Since the response is a direct array, loop through it
            if (data.length > 0) {
                // Loop through the data using a regular for loop
                for (let i = 0; i < data.length; i++) {
                    let item = data[i];
                    tableRows += `
                        <tr data-id="${item.id}">
                            <td>${i + 1}</td>
                            <td>${item.kode}</td>
                            <td>${item.nama}</td>
                            <td>${item.quantity}</td>
                            <td>
                                <button class="btn btn-danger btn-sm moveBtn" data-id="${item.id}">Move</button>
                            </td>
                        </tr>
                    `;
                }
            }  else {
                // If no data is found, display a 'no data' message
                tableRows = '<tr><td colspan="4" class="text-center">No data available</td></tr>';
            }
            console.log(tableRows)
            // Populate the table body with the generated rows
            $('#tableProdProgress').html(tableRows);
        },
        error: function(xhr, status, error) {
            console.error('Error:', error);  // Log error details for debugging
            Swal.fire({
                icon: 'error',
                title: 'Failed to load data',
                text: 'There was an error loading the data. Please try again.',
            });
        }
    });
}
  // Initialize data load on page load

  // Add new data
  // $('#addDataBtn').click(function() {
  //   // Get form values (for demonstration purposes, hardcoded data)
  //   const product_code = prompt('Enter Product Code');
  //   const product_name = prompt('Enter Product Name');
  //   const quantity = prompt('Enter Quantity');

  //   // AJAX request to add data
  //   $.ajax({
  //     url: 'your-api-endpoint-to-add-data', // Replace with your API endpoint for adding data
  //     type: 'POST',
  //     data: {
  //       product_code: product_code,
  //       product_name: product_name,
  //       quantity: quantity
  //     },
  //     success: function(response) {
  //       Swal.fire('Success', 'Product added successfully!', 'success');
  //       fetchData(); // Refresh the table
  //     },
  //     error: function(xhr) {
  //       Swal.fire('Error', 'Failed to add product.', 'error');
  //     }
  //   });
  // });

  // // Edit data
  // $(document).on('click', '.editBtn', function() {
  //   const row = $(this).closest('tr');
  //   const productId = row.data('id');
  //   const productCode = row.find('td').eq(1).text();
  //   const productName = row.find('td').eq(2).text();
  //   const quantity = row.find('td').eq(3).text();

  //   // Prompt user for new values
  //   const newProductCode = prompt('Edit Product Code', productCode);
  //   const newProductName = prompt('Edit Product Name', productName);
  //   const newQuantity = prompt('Edit Quantity', quantity);

  //   // AJAX request to update data
  //   $.ajax({
  //     url: 'your-api-endpoint-to-update-data/' + productId, // Replace with your API endpoint for updating
  //     type: 'PUT',
  //     data: {
  //       product_code: newProductCode,
  //       product_name: newProductName,
  //       quantity: newQuantity
  //     },
  //     success: function(response) {
  //       Swal.fire('Success', 'Product updated successfully!', 'success');
  //       fetchData(); // Refresh the table
  //     },
  //     error: function(xhr) {
  //       Swal.fire('Error', 'Failed to update product.', 'error');
  //     }
  //   });
  // });

  // // Delete data
  // $(document).on('click', '.deleteBtn', function() {
  //   const row = $(this).closest('tr');
  //   const productId = row.data('id');

  //   // Confirm deletion
  //   Swal.fire({
  //     title: 'Are you sure?',
  //     text: 'This will delete the product permanently.',
  //     icon: 'warning',
  //     showCancelButton: true,
  //     confirmButtonText: 'Yes, delete it!',
  //     cancelButtonText: 'No, keep it'
  //   }).then((result) => {
  //     if (result.isConfirmed) {
  //       // AJAX request to delete data
  //       $.ajax({
  //         url: 'your-api-endpoint-to-delete-data/' + productId, // Replace with your API endpoint for deleting
  //         type: 'DELETE',
  //         success: function(response) {
  //           Swal.fire('Deleted!', 'The product has been deleted.', 'success');
  //           fetchData(); // Refresh the table
  //         },
  //         error: function(xhr) {
  //           Swal.fire('Error', 'Failed to delete product.', 'error');
  //         }
  //       });
  //     }
  //   });
  // });
