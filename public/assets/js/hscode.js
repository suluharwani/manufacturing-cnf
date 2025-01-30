var loc = window.location;
var base_url = loc.protocol + "//" + loc.hostname + (loc.port? ":"+loc.port : "") + "/";
function checkHsCode(hsCode) {
    $.ajax({
        url: base_url + 'hscode/check_hs_code',
        method: 'GET',
        data: { term: hsCode },
        success: function(response) {
            const data = JSON.parse(response);
            if (data.status === 'success') {
                Swal.fire({
                    icon: 'info',
                    title: 'HS Code Information',
                    html: `<strong>Code:</strong> ${data.result.code}<br>
                           <strong>Description:</strong> ${data.result.description}<br>
                           <strong>Digits:</strong> ${data.result.digits}`
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: data.message
                });
            }
        },
        error: function() {
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'An error occurred while checking the HS Code.'
            });
        }
    });
  }
