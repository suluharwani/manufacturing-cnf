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
      "url" :base_url+"material/listdataMaterialJoin" , // json datasource 
      "type": "post",  // method  , by default get
      // "async": false,
      "dataType": 'json',
      "data":{},
    },
    
    columns: [
      {},
      {mRender: function (data, type, row) {
    //   return  row[1]+" "+row[2]+"</br>"+"<a href=mailto:"+row[3]+">"+row[3]+"</a>";
        return `${row[3]} <br> <p style="color:blue;" onclick="checkHsCode('${row[8]}')">${row[8]}</p> `
      }},
      {mRender: function (data, type, row) {
        return row[2]
      }},
      {mRender: function (data, type, row) {
       return row[5]
     }},
     {mRender: function (data, type, row) {
       return `${row[4]}`; 
     }},
    {mRender: function (data, type, row) {
      return `(${row[6]}) ${row[7]}`; 
    }},

    {mRender: function (data, type, row) {
      return ` <a href="javascript:void(0);" class="btn btn-success btn-sm add" id="${row[1]}" nama="${row[2]}" code= "${row[3]}">Add</a>`; 
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
loadBOMData()
     function loadBOMData() {
            const endpoint = base_url+'databom/24/33';
            const $tableContainer = $('#table-container');
            
            $.ajax({
                url: endpoint,
                type: 'GET',
                dataType: 'json',
                beforeSend: function() {
                    $tableContainer.html('<div class="loading">Memuat data...</div>');
                },
                success: function(data) {
                    if (data && data.length > 0) {
                        renderTable(data);
                    } else {
                        $tableContainer.html('<div class="error">Tidak ada data yang ditemukan</div>');
                    }
                },
                error: function(xhr, status, error) {
                    $tableContainer.html(`<div class="error">Gagal memuat data: ${error}</div>`);
                }
            });
        }

        function renderTable(data) {
            const $table = $('<table>');
            const $thead = $('<thead>');
            const $tbody = $('<tbody>');
            
            // Buat header tabel
            const $headerRow = $('<tr>');
            $headerRow.append(
                $('<th>').text('No'),
                $('<th>').text('Nama Material'),
                $('<th>').text('Kode'),
                $('<th>').text('Penggunaan'),
                $('<th>').text('Satuan'),
                $('<th>').text('Kategori'),
                $('<th>').text('Action').addClass('action-cell')
            );
            $thead.append($headerRow);
            
            // Isi data ke dalam tabel
            $.each(data, function(index, item) {
                const $row = $('<tr>').attr('data-id', item.id || '');
                $row.append(
                    $('<td>').text(index + 1),
                    $('<td>').text(item.name || '-'),
                    $('<td>').text(item.kode || '-'),
                    $('<td>').text(item.penggunaan || '0'),
                    $('<td>').text(item.nama || '-'),
                    $('<td>').text(item.kite || '-'),
                    $('<td>').addClass('action-cell').append(
                        $('<button>')
                            .addClass('btn-delete')
                            .text('Hapus')
                            .click(function() {
                                deleteItem(item.id, $row);
                            })
                    )
                );
                $tbody.append($row);
            });
            
            $table.append($thead, $tbody);
            $('#table-container').html($table);
            
            // Tambahkan pesan jumlah data
            $('#table-container').append(
                $('<div>').css({'margin-top': '10px', 'font-style': 'italic'})
                          .text(`Total ${data.length} material ditemukan`)
            );
        }

        function deleteItem(id, $row) {
            if (!confirm('Apakah Anda yakin ingin menghapus data ini?')) {
                return;
            }

            // Endpoint untuk hapus data (sesuaikan dengan endpoint Anda)
            const deleteEndpoint = `databom/delete/${id}`;
            
            $.ajax({
                url: deleteEndpoint,
                type: 'DELETE',
                dataType: 'json',
                beforeSend: function() {
                    $row.css('opacity', '0.5');
                },
                success: function(response) {
                    if (response.success) {
                        $row.fadeOut(400, function() {
                            $(this).remove();
                            showAlert('Data berhasil dihapus', 'success');
                        });
                    } else {
                        $row.css('opacity', '1');
                        showAlert('Gagal menghapus data: ' + (response.message || ''), 'error');
                    }
                },
                error: function(xhr, status, error) {
                    $row.css('opacity', '1');
                    showAlert('Gagal menghapus data: ' + error, 'error');
                }
            });
        }

        function showAlert(message, type) {
            const $alert = $('<div>')
                .text(message)
                .css({
                    'padding': '10px',
                    'margin': '10px 0',
                    'border-radius': '4px',
                    'color': 'white',
                    'background-color': type === 'success' ? '#4CAF50' : '#f44336'
                });
            
            $('#table-container').prepend($alert);
            
            // Hilangkan alert setelah 5 detik
            setTimeout(function() {
                $alert.fadeOut(400, function() {
                    $(this).remove();
                });
            }, 5000);
        }

        $('#tabel_serverside').on('click', '.add', function() {
    const id = $(this).attr('id');
    const nama = $(this).attr('nama');
    const code = $(this).attr('code');
    
    showAddPopup(id, nama, code);
  });


// Function to show SweetAlert popup for adding material
function showAddPopup(id, nama, code) {
  Swal.fire({
    title: `Tambah Material`,
    html: `
      <div style="text-align: left;">
        <p><strong>ID:</strong> ${id}</p>
        <p><strong>Nama:</strong> ${nama}</p>
        <p><strong>Kode:</strong> ${code}</p>
        <hr>
        <div class="form-group">
          <label for="swal-input2">Jumlah:</label>
          <input type="number" id="swal-input2" class="swal2-input" placeholder="Masukkan jumlah" min="1">
        </div>
      </div>
    `,
    showCancelButton: true,
    confirmButtonText: 'Simpan',
    cancelButtonText: 'Batal',
    focusConfirm: false,
    preConfirm: () => {
      return {
        jumlah: document.getElementById('swal-input2').value,
      }
    },
    didOpen: () => {
      // Focus on first input when popup opens
      document.getElementById('swal-input1').focus();
    }
  }).then((result) => {
    if (result.isConfirmed) {
      const data = result.value;
      
      // Validate inputs
      if (!data.jumlah) {
        Swal.fire('Error', 'Ukuran dan Jumlah harus diisi!', 'error');
        return;
      }
      
      // Process the data (you can replace this with your AJAX call)
      Swal.fire({
        title: 'Konfirmasi',
        html: `Anda akan menambahkan:<br>
               <strong>Material:</strong> ${material}<br>
               <strong>Jumlah:</strong> ${data.jumlah}<br>
     `,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Ya, Simpan',
        cancelButtonText: 'Batal'
      }).then((confirmResult) => {
        if (confirmResult.isConfirmed) {
          // AJAX call to save data
          $.ajax({
            url: base_url + 'material/addMaterial', // Replace with your endpoint
            type: 'POST',
            dataType: 'json',
            data: {
              id: id,
              nama: nama,
              material: material,
              ukuran: data.ukuran,
              jumlah: data.jumlah,
              keterangan: data.keterangan
            },
            success: function(response) {
              if (response.success) {
                Swal.fire('Sukses', response.message || 'Data berhasil ditambahkan', 'success');
                // Optional: Refresh DataTable
                $('#tabel_serverside').DataTable().ajax.reload();
              } else {
                Swal.fire('Error', response.message || 'Gagal menambahkan data', 'error');
              }
            },
            error: function(xhr, status, error) {
              Swal.fire('Error', 'Terjadi kesalahan: ' + error, 'error');
            }
          });
        }
      });
    }
  });
}
