
var loc = window.location;
var base_url = loc.protocol + "//" + loc.hostname + (loc.port? ":"+loc.port : "") + "/";

$('.updateCurrency').on('click',function(){

                // Tampilkan pesan loading
                $('#statusMessage').html('<div class="alert alert-info">Mengambil data...</div>');

                $.ajax({
                    url: base_url+'/dashboard/fetchAndSaveRates',
                    type: 'GET', 
                    dataType: 'json', 
                    success: function(response) {
                currencyData();
                if(response.success){
                    $('#statusMessage').html('<div class="alert alert-success">' + response.message + '</div>');
                } else {
                    $('#statusMessage').html('<div class="alert alert-danger">Error: ' + response.message + '</div>');
                }
              
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', xhr.responseText);
                $('#statusMessage').html('<div class="alert alert-danger">Error: ' + xhr.responseText + '</div>');
            }
                });
            });
currencyData()
function currencyData(){
  $.ajax({
    type : "get",
    url  : base_url+"dashboard/getCurrencyData",
    async : false,
    success: function(data){
     tableCurrency(data);

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
function tableCurrency(data){
  d = JSON.parse(data);
  console.log(d)
  let no = 1;
  let table = ''
  let arrow = ''

  $.each(d, function(k, v){
  if (d[k].oldrate - d[k].rate < 0) { //naik
  	arrow = '<i class="fa fa-arrow-up">';
  }else if(d[k].oldrate - d[k].rate > 0){//turun
  	arrow = '<i class="fa fa-arrow-down">';
  }else{ //equal
	arrow = '<i class="fa fa-arrows-alt-h">';
  }
    table+=     `<tr>`;
    table+=   `<td>${no++}</td>`;
    table+=   `<td>${d[k].kode}</td>`;
    table+=   `<td>${d[k].nama}</td>`;
    table+=   `<td>${d[k].olddate}</td>`;
    table+=   `<td>${d[k].oldrate} </br> ${formatRupiah(1/d[k].oldrate)}/${d[k].kode}</td>`;
    table+=   `<td>${d[k].update}</td>`;
    table+=   `<td>${d[k].rate}</br> ${formatRupiah(1/d[k].rate)}/${d[k].kode}</td>`;
    table+=   `<td>${arrow}<td>`;
    table+=   `</tr>`

  })
  $('#tableCurrency').html(table)
}
function formatRupiah(amount) {
    // Pastikan bahwa jumlah adalah angka atau dapat dikonversi menjadi angka
    if (isNaN(amount)) {
        return '0,00'; // Jika bukan angka, kembalikan default '0,00'
    }
    
    // Konversi nilai menjadi fixed-point dengan 2 angka desimal
    amount = parseFloat(amount).toFixed(2);

    // Pisahkan bagian desimal dan bagian integer
    let parts = amount.split('.');
    let integerPart = parts[0];
    let decimalPart = parts[1];

    // Tambahkan tanda pemisah ribuan
    integerPart = integerPart.replace(/\B(?=(\d{3})+(?!\d))/g, '.');

    // Gabungkan kembali bagian integer dan desimal
    return 'Rp ' + integerPart + ',' + decimalPart;
}