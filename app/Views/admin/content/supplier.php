<link rel="stylesheet" type="text/css" href="<?= base_url('assets') ?>/datatables/datatables.min.css" />
<link rel="stylesheet" type="text/css" href="<?=base_url('assets')?>/summernote/summernote-image-list.min.css"/>
<link rel="stylesheet" type="text/css" href="<?=base_url('assets')?>/summernote/summernote-lite.min.css"/>
<style type="text/css">

</style>

<!-- Supplier Management Start -->
<div class="container-fluid pt-4 px-4">
    <!-- Supplier Table -->
    <div class="bg-light text-center rounded p-4">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <h6 class="mb-0">Suppliers</h6>
            <button class="btn btn-primary addSupplier">Add</button>
        </div>

        <div class="table-responsive">
            <table id="tabel_serverside" class="table table-bordered display text-left" cellspacing="0" width="100%">
                <thead>
                    <tr class="text-center">
                        <th style="text-align: center;">#</th>
                        <th style="text-align: center;">Code</th>
                        <th style="text-align: center;">Name</th>
                        <th style="text-align: center;">Status</th>
                        <th style="text-align: center;">Actions</th>
                    </tr>
                </thead>
                <tfoot>
                    <tr class="text-center">
                        <th style="text-align: center;">#</th>
                        <th style="text-align: center;">Code</th>
                        <th style="text-align: center;">Name</th>
                        <th style="text-align: center;">Status</th>
                        <th style="text-align: center;">Actions</th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
<!-- Supplier Management End -->
<div class="modal fade supplierModal" id="supplierModal" data-bs-focus="false" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Supplier Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="supplierForm" class="form" enctype="multipart/form-data">
                    <!-- Supplier Code -->
                    <div class="mb-3">
                        <label for="kode" class="form-label">Supplier Code</label>
                        <input type="text" name="id" class="form-control" id="id" hidden/>
                        <input type="text" name="code" class="form-control" id="code" />
                    </div>
                    
                    <!-- Supplier Name -->
                    <div class="mb-3">
                        <label for="supplier_name" class="form-label">Supplier Name</label>
                        <input type="text" name="supplier_name" class="form-control" id="supplier_name" />
                    </div>
                    
                    <!-- Contact Name -->
                    <div class="mb-3">
                        <label for="contact_name" class="form-label">Contact Name</label>
                        <input type="text" name="contact_name" class="form-control" id="contact_name" />
                    </div>
                    
                    <!-- Contact Email -->
                    <div class="mb-3">
                        <label for="contact_email" class="form-label">Contact Email</label>
                        <input type="email" name="contact_email" class="form-control" id="contact_email" />
                    </div>
                    
                    <!-- Contact Phone -->
                    <div class="mb-3">
                        <label for="contact_phone" class="form-label">Contact Phone</label>
                        <input type="text" name="contact_phone" class="form-control" id="contact_phone" />
                    </div>
              
                    
                    <!-- Address -->
                    <div class="mb-3">
                        <label for="address" class="form-label">Address</label>
                        <input type="text" name="address" class="form-control" id="address" />
                    </div>
                    
                    <!-- City -->
                    <div class="mb-3">
                        <label for="city" class="form-label">City</label>
                        <input type="text" name="city" class="form-control" id="city" />
                    </div>
                    
                    <!-- State -->
                    <div class="mb-3">
                        <label for="state" class="form-label">State</label>
                        <input type="text" name="state" class="form-control" id="state" />
                    </div>
                    
                    <!-- Postal Code -->
                    <div class="mb-3">
                        <label for="postal_code" class="form-label">Postal Code</label>
                        <input type="text" name="postal_code" class="form-control" id="postal_code" />
                    </div>
                    
                    <!-- Country -->
                    <div class="mb-3">
                        <label for="country" class="form-label">Country</label>
                        <select class="form-control country" name="id_country" aria-label="Default select example" id="country"></select>
                    </div>
                    
                    <!-- Currency -->
                    <div class="mb-3">
                        <label for="currency" class="form-label">Currency</label>
                        <select class="form-control currency" name="id_currency" aria-label="Default select example" id="currency"></select>
                    </div>
                    
                    <!-- Tax Number -->
                    <div class="mb-3">
                        <label for="tax_number" class="form-label">Tax Number</label>
                        <input type="text" name="tax_number" class="form-control" id="tax_number" />
                    </div>
                    
                    <!-- Website URL -->
                    <div class="mb-3">
                        <label for="website_url" class="form-label">Website URL</label>
                        <input type="text" name="website_url" class="form-control" id="website_url" />
                    </div>

                    <!-- Status -->
                    <div class="mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-control" name="status" id="status">
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger reset">Reset</button>
                <button type="button" class="btn btn-primary saveSupplier">Save</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="viewSupplierModal" tabindex="-1" aria-labelledby="viewSupplierModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewSupplierModalLabel">Supplier Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Supplier Name:</strong> <span id="supplier_name"></span></p>
                        <p><strong>Contact Name:</strong> <span id="contact_name"></span></p>
                        <p><strong>Contact Email:</strong> <span id="contact_email"></span></p>
                        <p><strong>Contact Phone:</strong> <span id="contact_phone"></span></p>
                        <p><strong>Logo:</strong> <span id="logo"></span></p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Address:</strong> <span id="address"></span></p>
                        <p><strong>City:</strong> <span id="city"></span></p>
                        <p><strong>State:</strong> <span id="state"></span></p>
                        <p><strong>Postal Code:</strong> <span id="postal_code"></span></p>
                        <p><strong>Country:</strong> <span id="country"></span></p>
                        <p><strong>Currency:</strong> <span id="currency_name"></span></p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade modalEditImage" id="modalEditImage" data-bs-focus="false"  tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel"></span></h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>
    <div class="modal-body">
        <form id="form"  class="form" enctype="multipart/form-data">
            <div class="mb-3">
                 <img class="mb-3" id="ajaxImgUpload" alt="Preview Image" width="300px" />
                <input type="text" name="id_sup_edit_image" id="id_sup_edit_image" hidden>
                <input type="file" name="file" multiple="true" id="file" 
                class="form-control form-control-lg"  accept="image/*">
            </div>
            <div class="d-grid">
             <button type="button" class="btn btn-danger uploadBtn">Upload</button>
         </div>
     </div>
 </form>

</div>
<div class="modal-footer">
</div>
</div>
</div>

<script type="text/javascript" src="<?= base_url('assets') ?>/js/supplier.js"></script>
<script type="text/javascript" src="<?= base_url('assets') ?>/datatables/datatables.min.js"></script> 
<script type="text/javascript" src="<?=base_url()?>/assets/summernote/summernote-lite.min.js"></script>
<script type="text/javascript" src="<?=base_url()?>/assets/summernote/summernote-image-list.min.js"></script>
<script type="text/javascript">
   $(document).ready(function() {
    $('.summernote').summernote({
        callbacks: {
            onImageUpload: function(files) {
                for (let i = 0; i < files.length; i++) {
                    $.upload(files[i]);
                }
            },
            onMediaDelete: function(target) {
                $.delete(target[0].src);
            }
        },
        height: 400,
        toolbar: [
            ["style", ["bold", "italic", "underline", "clear"]],
            ["fontname", ["fontname"]],
            ["fontsize", ["fontsize"]],
            ["color", ["color"]],
            ["para", ["ul", "ol", "paragraph"]],
            ['table', ['table']],
            ["height", ["height"]],
            ["insert", ["link", "picture", "imageList", "video", "hr"]],
            ['view', ['fullscreen', 'codeview', 'help']],

            ],
        dialogsInBody: true,
        imageList: {
            endpoint: `${base_url}admin/static_page/listGambar`,
            fullUrlPrefix: `${base_url}assets/upload/tinymce/image/`,
            thumbUrlPrefix: `${base_url}assets/upload/tinymce/1000/`
        }
    });

    $.upload = function(file) {
        let out = new FormData();
        out.append('file', file, file.name);
        $.ajax({
            method: 'POST',
            url: `${base_url}admin/upload/tinymce`,
            contentType: false,
            cache: false,
            processData: false,
            data: out,
            success: function(img) {
                $('.summernote').summernote('insertImage', img);

            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.error(textStatus + " " + errorThrown);
            }
        });
    };
    $.delete = function(src) {
        $.ajax({
            method: 'POST',
            url: `${base_url}admin/static/deleteGambar`,
            cache: false,
            data: {
                src: src
            },
            success: function(response) {
                        // Swal.fire({
                        //  position: 'top-end',
                        //  icon: 'success',
                        //  title: response,
                        //  showConfirmButton: false,
                        //  timer: 1500
                        // })

            }

        });
    };
});
</script>
