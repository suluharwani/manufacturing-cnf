<link rel="stylesheet" type="text/css" href="<?= base_url('assets') ?>/datatables/datatables.min.css" />
<link rel="stylesheet" type="text/css" href="<?=base_url('assets')?>/summernote/summernote-image-list.min.css"/>
<link rel="stylesheet" type="text/css" href="<?=base_url('assets')?>/summernote/summernote-lite.min.css"/>
<style type="text/css">

</style>





<!-- Recent Sales Start -->
<div class="container-fluid pt-4 px-4">
  <div class="col-sm-12 col-xl-12">
                        <div class="bg-light text-center rounded p-4">
                            <div class="d-flex align-items-center justify-content-between mb-4">
                                <h6 class="mb-0">Category</h6>
                                <button class ="btn btn-primary addCat">Add</button>
                            </div>
                                                       <!-- satuan barang -->
                                                       <div class="table-responsive" style="max-height: 300px;">
            <table class="table table-striped table-bordered">
                <thead class="table-dark">
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Name</th>
                        <th scope="col">Action</th>
                    </tr>
                </thead>
                <tbody id= "productCat"></tbody>
            </table>
        </div>
                             <!-- end satuan barang -->
                        </div>
                    </div>
                <div class="bg-light text-center rounded p-4">
                    <div class="d-flex align-items-center justify-content-between mb-4">
                        <h6 class="mb-0">Finished Goods</h6>
                        <button class= "btn btn-primary addFinishedGoods">Add</button>
                    </div>

                    <div class="table-responsive">
                    <table id="tabel_serverside" class="table table-bordered display text-left" cellspacing="0" width="100%">
              <thead>
                <tr  class="text-center">
                  <th style=" text-align: center;">#</th>
                  <th style=" text-align: center;">Code</th>
                  <th style=" text-align: center;">Name</th>
                  <th style=" text-align: center;">Picture</th>
                  <th style=" text-align: center;">Category</th>
                  <th style=" text-align: center;">Variable</th>
                  <th style=" text-align: center;">Aktif</th>
                </tr>
              </thead>
              <tfoot>
                <tr class="text-center">
                  <th style=" text-align: center;">#</th>
                  <th style=" text-align: center;">Code</th>
                  <th style=" text-align: center;">Name</th>
                  <th style=" text-align: center;">Picture</th>
                  <th style=" text-align: center;">Category</th>
                  <th style=" text-align: center;">Variable</th>
                  <th style=" text-align: center;">Aktif</th>
                </tr>
              </tr>
            </tfoot>
          </table>
                    </div>
                </div>
            </div>
<!-- Recent Sales End -->
 
<div class="modal fade modalTambahData" id="modalTambahData" data-bs-focus="false"  tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Finished Good: <span class = "nama_halaman"></span></h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>
    <div class="modal-body">
        <form id="form"  class="form" enctype="multipart/form-data">
          <div class="mb-3">
                <label for="kode" class="form-label">Kode</label>
                <input type="text" name="kode" class="form-control" id="kode" >
            </div>
            <div class="mb-3">
                <label for="kode" class="form-label">HS Code(Harmonized Commodity Description and Coding System)</label>
                <input type="text" name="hs_code" class="form-control" id="hs_code" >
            </div>
            <div class="mb-3">
                <label for="nama" class="form-label">Nama Produk</label>
                <input type="text" name="nama" class="form-control" id="nama" >
            </div>
            <div class="mb-3">
                <label for="contact" class="form-label">Category</label>
                <select class="form-control  select_cat" name="id_product_cat"  aria-label="Default select example" id="id_cat"></select>

            </div>
            <div class="mb-3">
                <div class="d-grid text-center">
                    <div class="container">
                        <div class="row">
                            <div class="col">
                                <img class="mb-3" id="ajaxImgUpload" alt="Preview Image" src="https://via.placeholder.com/300" />
                            </div>
                            <div class="col">
                                <span id = "alertMsg"></span>
                                <input type="text" name="picture" id="picture" hidden>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="mb-3">
                    <input type="file" name="file" multiple="true" id="file" 
                    class="form-control form-control-lg"  accept="image/*">
                </div>
                <div class="d-grid">
                   <button type="button" class="btn btn-danger uploadBtn">Upload</button>
               </div>
           </div>
           <div class="mb-3">
                <label for="contact" class="form-label">Deskripsi Produk</label>
                <textarea class="summernote editVal" name="text" inputVal = "text" id="text" cols="30" rows="10"></textarea>
            </div>
    </form>

</div>
<div class="modal-footer">

    <button type="button" class="btn btn-danger reset" >Reset</button>
    <button type="button" class="btn btn-primary save" >Simpan</button>
    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
</div>
</div>
</div>
</div>
<!-- modal -->
<!-- Add this modal after your existing modals -->
<div class="modal fade" id="editPictureModal" tabindex="-1" aria-labelledby="editPictureModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="editPictureModalLabel">Edit Product Picture</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="editPictureForm" enctype="multipart/form-data">
          <input type="hidden" name="id" id="editPictureId">
          <div class="mb-3">
            <label for="editFile" class="form-label">New Picture</label>
            <input type="file" class="form-control" id="editFile" name="file" accept="image/*">
          </div>
          <div class="mb-3 text-center">
            <img id="editPreview" src="" alt="Preview" style="max-width: 100%; max-height: 200px;">
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary" id="saveEditPicture">Save Changes</button>
      </div>
    </div>
  </div>
</div>
<!-- Widgets Start -->
 
<!-- Widgets End -->
 
<script type="text/javascript" src="<?= base_url('assets') ?>/js/product.js"></script>
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