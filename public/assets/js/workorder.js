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
        //   return  row[1]+" "+row[2]+"</br>"+"<a href=mailto:"+row[3]+">"+row[3]+"</a>";
        return row[3]
        }},
        {mRender: function (data, type, row) {
            //   return  row[1]+" "+row[2]+"</br>"+"<a href=mailto:"+row[3]+">"+row[3]+"</a>";
            return row[3]
            }},
            {mRender: function (data, type, row) {
                //   return  row[1]+" "+row[2]+"</br>"+"<a href=mailto:"+row[3]+">"+row[3]+"</a>";
                return row[3]
                }},
    {mRender: function (data, type, row) {
        return row[2]
    }},

    {mRender: function (data, type, row) {
     return `<a href="javascript:void(0);" class="btn btn-success btn-sm showPurchaseOrder" id="'+row[1]+'" >Detail</a>`; 
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