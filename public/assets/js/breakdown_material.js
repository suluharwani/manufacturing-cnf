var loc = window.location;
var base_url = loc.protocol + "//" + loc.hostname + (loc.port ? ":" + loc.port : "") + "/";
function getLastSegment() {
    var pathname = window.location.pathname; // Mendapatkan path dari URL
    var segments = pathname.split('/').filter(function(segment) { return segment.length > 0; });
    return segments[segments.length - 1]; // Mengambil segment terakhir
  }
$(document).ready(function() {
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
           $('#desc').html(res.text);
            
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
});
$('.finising').on('click',function(){

    $('#finisingModal').modal('show')
    
    
    })