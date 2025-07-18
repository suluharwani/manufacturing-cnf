
<div class="container-fluid pt-4 px-4">
    <div class="bg-light rounded p-4">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <h4 class="mb-0">BC Import Data</h4>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#importModal">
                <i class="fa fa-upload me-2"></i>Import Data
            </button>
        </div>

        <?php if (session()->has('success')): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fa fa-check-circle me-2"></i>
                <?= session('success') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?php if (session()->has('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fa fa-exclamation-circle me-2"></i>
                <?= session('error') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>No. Aju</th>
                        <th>No. BC11</th>
                        <th>Tanggal BC11</th>
                        <th>Kantor</th>
                        <th>Jenis Impor</th>
                        <th>Nilai Barang</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($bc_data as $item): ?>
                    <tr>
                        <td><?= $item['nomor_aju'] ?></td>
                        <td><?= $item['nomor_bc11'] ?></td>
                        <td><?= $item['tanggal_bc11'] ?></td>
                        <td><?= $item['kode_kantor'] ?></td>
                        <td><?= $item['kode_jenis_impor'] ?></td>
                        <td class="text-end"><?= number_format($item['nilai_barang'], 2) ?></td>
                        <td class="text-center">
                            <a href="<?= base_url('bc-import/detail/' . $item['id']) ?>" class="btn btn-sm btn-info">
                                <i class="fa fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Import Modal -->
<div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="importModalLabel">Import BC Data</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <?= form_open_multipart('bc-import/process', ['id' => 'importForm']) ?>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="excel_file" class="form-label">Pilih File Excel</label>
                    <input class="form-control" type="file" id="excel_file" name="excel_file" accept=".xlsx,.xls" required>
                    <div class="form-text">Format file harus .xlsx atau .xls</div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                <button type="submit" class="btn btn-primary">
                    <span id="importButtonText">Import</span>
                    <span id="importSpinner" class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                </button>
            </div>
            <?= form_close() ?>
        </div>
    </div>
</div>


<script>
    $(document).ready(function() {
        // Handle form submission
        $('#importForm').submit(function(e) {
            e.preventDefault();
            
            // Show loading state
            $('#importButtonText').text('Memproses...');
            $('#importSpinner').removeClass('d-none');
            
            // Submit form via AJAX
            $.ajax({
                url: $(this).attr('action'),
                type: 'POST',
                data: new FormData(this),
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.redirect) {
                        window.location.href = response.redirect;
                    }
                },
                error: function(xhr) {
                    alert('Terjadi kesalahan: ' + xhr.responseText);
                    $('#importButtonText').text('Import');
                    $('#importSpinner').addClass('d-none');
                }
            });
        });
        
        // Reset form when modal is closed
        $('#importModal').on('hidden.bs.modal', function() {
            $('#importForm')[0].reset();
            $('#importButtonText').text('Import');
            $('#importSpinner').addClass('d-none');
        });
    });
</script>
