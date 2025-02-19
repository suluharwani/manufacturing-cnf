<link rel="stylesheet" type="text/css" href="<?= base_url('assets') ?>/datatables/datatables.min.css" />
<style>
    /* Tambahan minimal CSS untuk fixed header */
    thead th {
        position: sticky;
        top: 0;
        background-color: #343a40;
        /* Warna background yang sama dengan header */
        z-index: 100;
    }
</style>



<!-- Recent Sales Start -->
<div class="container-fluid pt-4 px-4">
    <div class="bg-light text-center rounded p-4">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div class="container mt-5">
                <h2>Form Bill of Material</h2>
                <form>
                    <div class="row mb-3">
                        <label for="code" class="col-md-3 col-form-label">CODE</label>
                        <div class="col-md-9">
                            <input type="text" class="form-control" value="" id="code" placeholder="code" disabled>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label for="hscode" class="col-md-3 col-form-label">HSCODE</label>
                        <div class="col-md-9">
                            <input type="text" class="form-control" value="" id="hscode" placeholder="hscode" disabled>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label for="product_name" class="col-md-3 col-form-label">NAME</label>
                        <div class="col-md-9">
                            <input type="text" class="form-control" value="" id="product_name" placeholder="" disabled>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label for="category" class="col-md-3 col-form-label">CATEGORY</label>
                        <div class="col-md-9">
                            <input type="text" class="form-control" value="" id="category" placeholder="category"
                                disabled>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label for="desc" class="col-md-3 col-form-label">DESCRIPTION</label>
                        <div class="col-md-9">
                            <span id="desc"></span>

                        </div>
                    </div>
                    <div class="row mb-3">
                        <label for="category" class="col-md-3 col-form-label">DIMENSION</label>
                        <div class="col-md-9">
                        <label for="category" class="col-md-3 col-form-label">Product Length (mm)</label>
                            <input type="number" class="form-control" id="length" placeholder="length (mm)">
                            <label for="category" class="col-md-3 col-form-label">Product Width (mm)</label>
                            <input type="number" class="form-control" id="width" placeholder="width (mm)">
                            <label for="category" class="col-md-3 col-form-label">Product Height (mm)</label>
                            <input type="number" class="form-control" id="height" placeholder="height (mm)">
                            <label for="category" class="col-md-3 col-form-label">Product Packaging (m³)</label>
                            <input type="number" class="form-control" id="cbm" placeholder="cbm (m³)">
                        </div>
                    </div>
                <button class="btn btn-primary updateDimension">Update Dimension</button>

                    <!--  -->

                    <!-- Tombol Kirim -->
                    <!--     <button type="button" class="btn btn-primary saveSupplier">Update Supplier</button>
    <button type="button" class="btn btn-warning saveSupplier">Import PO</button> -->

                </form>
                <!-- <button class="btn btn-secondary finising">Finishing</button>
                <button class="btn btn-primary modul">Modul</button>
                <button class="btn btn-warning dimension">Dimension</button>
                <button class="btn btn-info design">Design</button>
                <button class="btn btn-success updateBoM">Update BoM</button> -->

            </div>
        </div>
    </div>
</div>
<div class="container-fluid pt-4 px-4">
    <div class="bg-light text-center rounded p-4">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <h6 class="mb-0">Finishing Items</h6>
            <button class="btn btn-primary" data-bs-toggle="modal" id="addFinishing">Add</button>
        </div>

        <div class="table-responsive">
            <table id="finishingTable" class="table table-bordered display text-left" cellspacing="0" width="100%">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Description</th>
                        <th>Picture</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>
<div class="container-fluid pt-4 px-4">
    <div class="bg-light text-center rounded p-4">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <h6 class="mb-0">Modul</h6>
            <button class="btn btn-primary addModul">Add</button>
        </div>
        <div class="table-responsive">

            <table id="tabel_serverside" class="table table-bordered display text-left" cellspacing="0" width="100%">
            <thead>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Description</th>
                        <th>Picture</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>
<!-- Recent Sales End -->


<!-- Widgets Start -->

<!-- Widgets End -->
<!-- Button to trigger modal -->

<!-- Modal -->
<div class="modal fade" id="finisingModal" tabindex="-1" aria-labelledby="finisingModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="finisingModalLabel">Add Material</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="addForm">

            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="editModulModal" tabindex="-1" aria-labelledby="editModulModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form id="editFinising" method="post">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModulModalLabel">Edit Modul Item</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="idModul" name="id">
                    <div class="mb-3">
                        <label for="nameModul" class="form-label">Name</label>
                        <input type="text" class="form-control" id="nameModul" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="descriptionModul" class="form-label">Description</label>
                        <textarea class="form-control" id="descriptionModul" name="description" rows="3" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" id= "editModulFormBtn" class="btn btn-primary">Save</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="editFinishingModal" tabindex="-1" aria-labelledby="editFinishingModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form id="editFinising" method="post">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editFinishingModalLabel">Edit Finishing Item</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="idFinishing" name="id">
                    <div class="mb-3">
                        <label for="nameFinishing" class="form-label">Name</label>
                        <input type="text" class="form-control" id="nameFinishing" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="descriptionFinishing" class="form-label">Description</label>
                        <textarea class="form-control" id="descriptionFinishing" name="description" rows="3" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" id= "editFinishingFormBtn" class="btn btn-primary">Save</button>
                </div>
            </div>
        </form>
    </div>
</div>


<!-- Modal for Editing Picture -->
<div class="modal fade" id="editPictureModal" tabindex="-1" aria-labelledby="editPictureModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form id="editPictureForm" enctype="multipart/form-data">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editPictureModalLabel">Edit Picture</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="id" name="id">
                    <div class="mb-3">
                        <label for="picture" class="form-label">Picture</label>
                        <input type="file" class="form-control" id="picture" name="picture" accept="image/*" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Update Picture</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="editPictureModulModal" tabindex="-1" aria-labelledby="editPictureModulModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form id="editPictureModulForm" enctype="multipart/form-data">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editPictureModulModalLabel">Edit Picture</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="id" name="id">
                    <div class="mb-3">
                        <label for="picture" class="form-label">Picture</label>
                        <input type="file" class="form-control" id="picture" name="picture" accept="image/*" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Update Picture</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="addModulModal" tabindex="-1" aria-labelledby="addModulModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form id="addModulForm" enctype="multipart/form-data" method="post">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addModulModalLabel">Add Modul Item</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="name" class="form-label">Name</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="desc" class="form-label">Description</label>
                        <textarea class="form-control" id="desc" name="desc" rows="3" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="picture" class="form-label">Picture</label>
                        <input type="file" class="form-control" id="picture" name="picture" accept="image/*" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </div>
        </form>
    </div>
</div>
<div class="modal fade" id="editModulModal" tabindex="-1" aria-labelledby="editModulModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form id="editModulForm" method="post">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModulModalLabel">Edit Modul Item</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="id" name="id">
                    <div class="mb-3">
                        <label for="name" class="form-label">Name</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </div>
        </form>
    </div>
</div>


<!-- Modal for Editing Picture -->
<div class="modal fade" id="editPictureModal" tabindex="-1" aria-labelledby="editPictureModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form id="editPictureForm" enctype="multipart/form-data">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editPictureModalLabel">Edit Picture</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="id" name="id">
                    <div class="mb-3">
                        <label for="picture" class="form-label">Picture</label>
                        <input type="file" class="form-control" id="picture" name="picture" accept="image/*" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Update Picture</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="addFinishingModal" tabindex="-1" aria-labelledby="addFinishingModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form id="addFinishingForm" enctype="multipart/form-data" method="post">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addFinishingModalLabel">Add Finishing Item</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="name" class="form-label">Name</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="picture" class="form-label">Picture</label>
                        <input type="file" class="form-control" id="picture" name="picture" accept="image/*" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script type="text/javascript" src="<?= base_url('assets') ?>/js/breakdown_material.js"></script>
<script type="text/javascript" src="<?= base_url('assets') ?>/js/finishing.js"></script>
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
