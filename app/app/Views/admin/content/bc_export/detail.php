<div class="container-fluid pt-4 px-4">
    <div class="bg-light rounded p-4">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <h4 class="mb-0">Detail BC Export - <?= $header['nomor_aju'] ?? 'N/A' ?></h4>
            <a href="<?= base_url('bc-export') ?>" class="btn btn-secondary">
                <i class="fa fa-arrow-left me-2"></i>Kembali
            </a>
        </div>

        <?php if (session()->has('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fa fa-exclamation-circle me-2"></i>
                <?= session('error') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <!-- HEADER INFORMATION -->
        <div class="card mb-4">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0"><i class="fa fa-info-circle me-2"></i>Informasi Header</h5>
                <span class="badge bg-light text-dark"><?= $header['kode_dokumen'] ?? 'N/A' ?></span>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-sm table-borderless">
                            <tr>
                                <th width="40%">Nomor AJU</th>
                                <td>: <?= $header['nomor_aju'] ?? '-' ?></td>
                            </tr>
                            <tr>
                                <th width="40%">Tanggal Daftar</th>
                                <td>: <?= $header['tanggal_daftar'] ?? '-' ?></td>
                            </tr>
                            <tr>
                                <th width="40%">Kode Daftar</th>
                                <td>: <?= $header['nomor_daftar'] ?? '-' ?></td>
                            </tr>
                            <tr>
                                <th>Kode Dokumen</th>
                                <td>: <?= $header['kode_dokumen'] ?? '-' ?></td>
                            </tr>
                            <tr>
                                <th>Kode Kantor</th>
                                <td>: <?= $header['kode_kantor'] ?? '-' ?></td>
                            </tr>
                            <tr>
                                <th>Jenis Ekspor</th>
                                <td>: <?= $header['kode_jenis_ekspor'] ?? '-' ?></td>
                            </tr>
                            <tr>
                                <th>Jenis Prosedur</th>
                                <td>: <?= $header['kode_jenis_prosedur'] ?? '-' ?></td>
                            </tr>
                            <tr>
                                <th>Nomor BC11</th>
                                <td>: <?= $header['nomor_bc11'] ?? '-' ?></td>
                            </tr>
                            <tr>
                                <th>Tanggal BC11</th>
                                <td>: <?= $header['tanggal_bc11'] ?? '-' ?></td>
                            </tr>
                            <tr>
                                <th>Tanggal Ekspor</th>
                                <td>: <?= $header['tanggal_ekspor'] ?? '-' ?></td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-sm table-borderless">
                            <tr>
                                <th width="40%">Pelabuhan Muat</th>
                                <td>: <?= $header['kode_pelabuhan_muat'] ?? '-' ?></td>
                            </tr>
                            <tr>
                                <th>Pelabuhan Tujuan</th>
                                <td>: <?= $header['kode_pelabuhan_tujuan'] ?? '-' ?></td>
                            </tr>
                            <tr>
                                <th>Valuta</th>
                                <td>: <?= $header['kode_valuta'] ?? '-' ?></td>
                            </tr>
                            <tr>
                                <th>Incoterm</th>
                                <td>: <?= $header['kode_incoterm'] ?? '-' ?></td>
                            </tr>
                            <tr>
                                <th>Nilai Barang</th>
                                <td>: <?= $header['nilai_barang'] ?? '-' ?></td>
                            </tr>
                            <tr>
                                <th>Nilai Incoterm</th>
                                <td>: <?= $header['nilai_incoterm'] ?? '-' ?></td>
                            </tr>
                            <tr>
                                <th>FOB</th>
                                <td>: <?= $header['fob'] ?? '-' ?></td>
                            </tr>
                            <tr>
                                <th>Bruto</th>
                                <td>: <?= $header['bruto'] ?? '-' ?></td>
                            </tr>
                            <tr>
                                <th>Netto</th>
                                <td>: <?= $header['netto'] ?? '-' ?></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- ENTITAS INFORMATION -->
        <?php if (!empty($entitas)): ?>
        <div class="card mb-4">
            <div class="card-header bg-success text-white">
                <h5 class="card-title mb-0"><i class="fa fa-building me-2"></i>Data Entitas (<?= count($entitas) ?>)</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-sm">
                        <thead class="table-light">
                            <tr>
                                <th>Seri</th>
                                <th>Kode Entitas</th>
                                <th>Nama Entitas</th>
                                <th>Nomor Identitas</th>
                                <th>Alamat</th>
                                <th>Negara</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($entitas as $item): ?>
                            <tr>
                                <td><?= $item['seri'] ?></td>
                                <td><?= $item['kode_entitas'] ?></td>
                                <td><?= $item['nama_entitas'] ?></td>
                                <td><?= $item['nomor_identitas'] ?></td>
                                <td title="<?= $item['alamat_entitas'] ?>">
                                    <?= strlen($item['alamat_entitas']) > 50 ? substr($item['alamat_entitas'], 0, 50) . '...' : $item['alamat_entitas'] ?>
                                </td>
                                <td><?= $item['kode_negara'] ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- DOKUMEN INFORMATION -->
        <?php if (!empty($dokumen)): ?>
        <div class="card mb-4">
            <div class="card-header bg-info text-white">
                <h5 class="card-title mb-0"><i class="fa fa-file me-2"></i>Data Dokumen (<?= count($dokumen) ?>)</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-sm">
                        <thead class="table-light">
                            <tr>
                                <th>Seri</th>
                                <th>Kode Dokumen</th>
                                <th>Nomor Dokumen</th>
                                <th>Tanggal Dokumen</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($dokumen as $item): ?>
                            <tr>
                                <td><?= $item['seri'] ?></td>
                                <td><?= $item['kode_dokumen'] ?></td>
                                <td><?= $item['nomor_dokumen'] ?></td>
                                <td><?= $item['tanggal_dokumen'] ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- BARANG INFORMATION -->
        <?php if (!empty($barang)): ?>
        <div class="card mb-4">
            <div class="card-header bg-warning text-dark">
                <h5 class="card-title mb-0"><i class="fa fa-box me-2"></i>Data Barang (<?= count($barang) ?>)</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-sm">
                        <thead class="table-light">
                            <tr>
                                <th>Seri Barang</th>
                                <th>HS</th>
                                <th>Uraian</th>
                                <th>Kode Satuan</th>
                                <th>Jumlah Satuan</th>
                                <th>Netto</th>
                                <th>FOB</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($barang as $item): ?>
                            <tr>
                                <td><?= $item['seri_barang'] ?></td>
                                <td><?= $item['hs'] ?></td>
                                <td title="<?= $item['uraian'] ?>"><?= strlen($item['uraian']) > 50 ? substr($item['uraian'], 0, 50) . '...' : $item['uraian'] ?></td>
                                <td><?= $item['kode_satuan'] ?></td>
                                <td><?= $item['jumlah_satuan'] ?></td>
                                <td><?= $item['netto'] ?></td>
                                <td><?= $item['fob'] ?? '-' ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- PENGANGKUT INFORMATION -->
        <?php if (!empty($pengangkut)): ?>
        <div class="card mb-4">
            <div class="card-header bg-secondary text-white">
                <h5 class="card-title mb-0"><i class="fa fa-ship me-2"></i>Data Pengangkut (<?= count($pengangkut) ?>)</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-sm">
                        <thead class="table-light">
                            <tr>
                                <th>Seri</th>
                                <th>Kode Cara Angkut</th>
                                <th>Nama Pengangkut</th>
                                <th>Nomor Pengangkut</th>
                                <th>Kode Bendera</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($pengangkut as $item): ?>
                            <tr>
                                <td><?= $item['seri'] ?></td>
                                <td><?= $item['kode_cara_angkut'] ?></td>
                                <td><?= $item['nama_pengangkut'] ?></td>
                                <td><?= $item['nomor_pengangkut'] ?></td>
                                <td><?= $item['kode_bendera'] ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- KONTAINER INFORMATION -->
        <?php if (!empty($kontainer)): ?>
        <div class="card mb-4">
            <div class="card-header bg-grey text-white">
                <h5 class="card-title mb-0"><i class="fa fa-container-storage me-2"></i>Data Kontainer (<?= count($kontainer) ?>)</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-sm">
                        <thead class="table-light">
                            <tr>
                                <th>Seri</th>
                                <th>Nomor Kontainer</th>
                                <th>Kode Ukuran</th>
                                <th>Kode Jenis</th>
                                <th>Kode Tipe</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($kontainer as $item): ?>
                            <tr>
                                <td><?= $item['seri'] ?></td>
                                <td><?= $item['nomor_kontainer'] ?></td>
                                <td><?= $item['kode_ukuran_kontainer'] ?></td>
                                <td><?= $item['kode_jenis_kontainer'] ?></td>
                                <td><?= $item['kode_tipe_kontainer'] ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- KEMASAN INFORMATION -->
        <?php if (!empty($kemasan)): ?>
        <div class="card mb-4">
            <div class="card-header bg-danger text-white">
                <h5 class="card-title mb-0"><i class="fa fa-cube me-2"></i>Data Kemasan (<?= count($kemasan) ?>)</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-sm">
                        <thead class="table-light">
                            <tr>
                                <th>Seri</th>
                                <th>Kode Kemasan</th>
                                <th>Jumlah Kemasan</th>
                                <th>Merek</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($kemasan as $item): ?>
                            <tr>
                                <td><?= $item['seri'] ?></td>
                                <td><?= $item['kode_kemasan'] ?></td>
                                <td><?= $item['jumlah_kemasan'] ?></td>
                                <td><?= $item['merek'] ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- TIMESTAMP INFORMATION -->
        <div class="card">
            <div class="card-header bg-light text-dark">
                <h5 class="card-title mb-0"><i class="fa fa-clock me-2"></i>Informasi Sistem</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-sm table-borderless">
                            <tr>
                                <th width="40%">Dibuat Pada</th>
                                <td>: <?= $header['created_at'] ? date('d/m/Y H:i:s', strtotime($header['created_at'])) : '-' ?></td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-sm table-borderless">
                            <tr>
                                <th width="40%">Diupdate Pada</th>
                                <td>: <?= $header['updated_at'] ? date('d/m/Y H:i:s', strtotime($header['updated_at'])) : '-' ?></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="d-flex justify-content-between">
                    <a href="<?= base_url('bc-export') ?>" class="btn btn-secondary">
                        <i class="fa fa-arrow-left me-2"></i>Kembali ke Daftar
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.card {
    box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
    border: 1px solid #e3e6f0;
}
.card-header {
    border-bottom: 1px solid #e3e6f0;
    font-weight: 600;
}
.table th {
    font-weight: 600;
    background-color: #f8f9fc;
}
.badge {
    font-size: 0.75em;
}
</style>

<script>
$(document).ready(function() {
    // Tambahkan tooltip untuk uraian barang yang dipotong
    $('[title]').tooltip({
        placement: 'top',
        trigger: 'hover'
    });
});
</script>