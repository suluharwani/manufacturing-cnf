<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail BC Import - <?= $header['nomor_aju'] ?? 'N/A' ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .card {
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
            border: 1px solid #e3e6f0;
            margin-bottom: 1.5rem;
        }
        .card-header {
            border-bottom: 1px solid #e3e6f0;
            font-weight: 600;
            padding: 0.75rem 1.25rem;
        }
        .table th {
            font-weight: 600;
            background-color: #f8f9fc;
        }
        .badge {
            font-size: 0.75em;
        }
        .table-responsive {
            border-radius: 0.35rem;
        }
        .info-label {
            font-weight: 600;
            color: #6c757d;
        }
        .info-value {
            font-weight: 500;
        }
        .section-title {
            border-left: 4px solid #0d6efd;
            padding-left: 10px;
            margin-bottom: 1rem;
        }
        .truncate-text {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 200px;
        }
        @media (max-width: 768px) {
            .table-responsive {
                font-size: 0.875rem;
            }
            .card-header h5 {
                font-size: 1rem;
            }
        }
    </style>
</head>
<body>
    <div class="container-fluid pt-4 px-4">
        <div class="bg-light rounded p-4">
            <!-- Header dengan judul dan tombol kembali -->
            <div class="d-flex align-items-center justify-content-between mb-4">
                <h4 class="mb-0">Detail BC Import - <?= $header['nomor_aju'] ?? 'N/A' ?></h4>
                <a href="javascript:void(0);" onclick="history.back()" class="btn btn-outline-secondary">
    <i class="fas fa-arrow-left me-2"></i>Kembali
</a>
            </div>

            <!-- Alert untuk error -->
            <?php if (session()->has('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    <?= session('error') ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <!-- HEADER INFORMATION -->
            <div class="card">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0"><i class="fas fa-info-circle me-2"></i>Informasi Header</h5>
                    <span class="badge bg-light text-dark"><?= $header['kode_dokumen'] ?? 'N/A' ?></span>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-sm table-borderless">
                                <tr>
                                    <td class="info-label" width="40%">Nomor Daftar</td>
                                    <td class="info-value"><?= $header['nomor_daftar'] ?? '-' ?></td>
                                </tr>
                                <tr>
                                    <td class="info-label">Kode Dokumen</td>
                                    <td class="info-value"><?= $header['kode_dokumen'] ?? '-' ?></td>
                                </tr>
                                <tr>
                                    <td class="info-label">Kode Kantor</td>
                                    <td class="info-value"><?= $header['kode_kantor'] ?? '-' ?></td>
                                </tr>
                                <tr>
                                    <td class="info-label">Jenis Prosedur</td>
                                    <td class="info-value"><?= $header['kode_jenis_prosedur'] ?? '-' ?></td>
                                </tr>
                                <tr>
                                    <td class="info-label">Jenis Impor</td>
                                    <td class="info-value"><?= $header['kode_jenis_impor'] ?? '-' ?></td>
                                </tr>
                                <tr>
                                    <td class="info-label">Nomor BC11</td>
                                    <td class="info-value"><?= $header['nomor_bc11'] ?? '-' ?></td>
                                </tr>
                                <tr>
                                    <td class="info-label">Tanggal BC11</td>
                                    <td class="info-value"><?= $header['tanggal_bc11'] ?? '-' ?></td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-sm table-borderless">
                                <tr>
                                    <td class="info-label" width="40%">Pelabuhan Muat</td>
                                    <td class="info-value"><?= $header['kode_pelabuhan_muat'] ?? '-' ?></td>
                                </tr>
                                <tr>
                                    <td class="info-label">Pelabuhan Tujuan</td>
                                    <td class="info-value"><?= $header['kode_pelabuhan_tujuan'] ?? '-' ?></td>
                                </tr>
                                <tr>
                                    <td class="info-label">Valuta</td>
                                    <td class="info-value"><?= $header['kode_valuta'] ?? '-' ?></td>
                                </tr>
                                <tr>
                                    <td class="info-label">Incoterm</td>
                                    <td class="info-value"><?= $header['kode_incoterm'] ?? '-' ?></td>
                                </tr>
                                <tr>
                                    <td class="info-label">Nilai Barang</td>
                                    <td class="info-value"><?= isset($header['nilai_barang']) ?  number_format($header['nilai_barang'], 2) : '-' ?></td>
                                </tr>
                                <tr>
                                    <td class="info-label">CIF</td>
                                    <td class="info-value"><?= isset($header['cif']) ?  number_format($header['cif'], 2) : '-' ?></td>
                                </tr>
                                <tr>
                                    <td class="info-label">FOB</td>
                                    <td class="info-value"><?= isset($header['fob']) ?  number_format($header['fob'], 2) : '-' ?></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ENTITAS INFORMATION -->
            <?php if (!empty($entitas)): ?>
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h5 class="card-title mb-0"><i class="fas fa-building me-2"></i>Data Entitas</h5>
                    <span class="badge bg-light text-dark"><?= count($entitas) ?> entitas</span>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover table-sm">
                            <thead class="table-light">
                                <tr>
                                    <th>Seri</th>
                                    <th>Kode Entitas</th>
                                    <th>Nama Entitas</th>
                                    <th>Nomor Identitas</th>
                                    <th>NIB</th>
                                    <th>Jenis API</th>
                                    <th>Status</th>
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
                                    <td><?= $item['nib_entitas'] ?></td>
                                    <td><?= $item['kode_jenis_api'] ?></td>
                                    <td><?= $item['kode_status'] ?></td>
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
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h5 class="card-title mb-0"><i class="fas fa-file me-2"></i>Data Dokumen</h5>
                    <span class="badge bg-light text-dark"><?= count($dokumen) ?> dokumen</span>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover table-sm">
                            <thead class="table-light">
                                <tr>
                                    <th>Seri</th>
                                    <th>Kode Dokumen</th>
                                    <th>Nomor Dokumen</th>
                                    <th>Tanggal Dokumen</th>
                                    <th>Kode Fasilitas</th>
                                    <th>Kode Ijin</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($dokumen as $item): ?>
                                <tr>
                                    <td><?= $item['seri'] ?></td>
                                    <td><?= $item['kode_dokumen'] ?></td>
                                    <td><?= $item['nomor_dokumen'] ?></td>
                                    <td><?= $item['tanggal_dokumen'] ?></td>
                                    <td><?= $item['kode_fasilitas'] ?></td>
                                    <td><?= $item['kode_ijin'] ?></td>
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
            <div class="card">
                <div class="card-header bg-warning text-dark">
                    <h5 class="card-title mb-0"><i class="fas fa-box me-2"></i>Data Barang</h5>
                    <span class="badge bg-light text-dark"><?= count($barang) ?> barang</span>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover table-sm">
                            <thead class="table-light">
                                <tr>
                                    <th>Seri Barang</th>
                                    <th>HS</th>
                                    <th>Uraian</th>
                                    <th>Merek</th>
                                    <th>Kode Satuan</th>
                                    <th>Jumlah Satuan</th>
                                    <th>Kode Kemasan</th>
                                    <th>Jumlah Kemasan</th>
                                    <th>Netto</th>
                                    <th>Bruto</th>
                                    <th>FOB</th>
                                    <th>Negara Asal</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($barang as $item): ?>
                                <tr>
                                    <td><?= $item['seri_barang'] ?></td>
                                    <td><?= $item['hs'] ?></td>
                                    <td class="truncate-text" title="<?= $item['uraian'] ?>"><?= $item['uraian'] ?></td>
                                    <td><?= $item['merek'] ?></td>
                                    <td><?= $item['kode_satuan'] ?></td>
                                    <td><?= $item['jumlah_satuan'] ?></td>
                                    <td><?= $item['kode_kemasan'] ?></td>
                                    <td><?= $item['jumlah_kemasan'] ?></td>
                                    <td><?= $item['netto'] ?></td>
                                    <td><?= $item['bruto'] ?></td>
                                    <td><?= isset($item['fob']) ?  number_format($item['fob'], 2) : '-' ?></td>
                                    <td><?= $item['kode_negara_asal'] ?></td>
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
            <div class="card">
                <div class="card-header bg-secondary text-white">
                    <h5 class="card-title mb-0"><i class="fas fa-ship me-2"></i>Data Pengangkut</h5>
                    <span class="badge bg-light text-dark"><?= count($pengangkut) ?> pengangkut</span>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover table-sm">
                            <thead class="table-light">
                                <tr>
                                    <th>Seri</th>
                                    <th>Kode Cara Angkut</th>
                                    <th>Nama Pengangkut</th>
                                    <th>Nomor Pengangkut</th>
                                    <th>Kode Bendera</th>
                                    <th>Call Sign</th>
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
                                    <td><?= $item['call_sign'] ?></td>
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
            <div class="card">
                <div class="card-header bg-dark text-white">
                    <h5 class="card-title mb-0"><i class="fas fa-container-storage me-2"></i>Data Kontainer</h5>
                    <span class="badge bg-light text-dark"><?= count($kontainer) ?> kontainer</span>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover table-sm">
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

            <!-- PUNGUTAN INFORMATION -->
            <?php if (!empty($pungutan)): ?>
            <div class="card">
                <div class="card-header bg-danger text-white">
                    <h5 class="card-title mb-0"><i class="fas fa-money-bill me-2"></i>Data Pungutan</h5>
                    <span class="badge bg-light text-dark"><?= count($pungutan) ?> pungutan</span>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover table-sm">
                            <thead class="table-light">
                                <tr>
                                    <th>Kode Fasilitas Tarif</th>
                                    <th>Kode Jenis Pungutan</th>
                                    <th>Nilai Pungutan</th>
                                    <th>NPWP Billing</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($pungutan as $item): ?>
                                <tr>
                                    <td><?= $item['kode_fasilitas_tarif'] ?></td>
                                    <td><?= $item['kode_jenis_pungutan'] ?></td>
                                    <td><?= isset($item['nilai_pungutan']) ?  number_format($item['nilai_pungutan'], 2) : '-' ?></td>
                                    <td><?= $item['npwp_billing'] ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- BARANG TARIF INFORMATION -->
            <?php if (!empty($barang_tarif)): ?>
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0"><i class="fas fa-percentage me-2"></i>Data Barang Tarif</h5>
                    <span class="badge bg-light text-dark"><?= count($barang_tarif) ?> barang tarif</span>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover table-sm">
                            <thead class="table-light">
                                <tr>
                                    <th>Seri Barang</th>
                                    <th>Kode Pungutan</th>
                                    <th>Kode Tarif</th>
                                    <th>Tarif</th>
                                    <th>Kode Fasilitas</th>
                                    <th>Tarif Fasilitas</th>
                                    <th>Nilai Bayar</th>
                                    <th>Nilai Fasilitas</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($barang_tarif as $item): ?>
                                <tr>
                                    <td><?= $item['seri_barang'] ?></td>
                                    <td><?= $item['kode_pungutan'] ?></td>
                                    <td><?= $item['kode_tarif'] ?></td>
                                    <td><?= $item['tarif'] ?></td>
                                    <td><?= $item['kode_fasilitas'] ?></td>
                                    <td><?= $item['tarif_fasilitas'] ?></td>
                                    <td><?= isset($item['nilai_bayar']) ?  number_format($item['nilai_bayar'], 2) : '-' ?></td>
                                    <td><?= isset($item['nilai_fasilitas']) ?  number_format($item['nilai_fasilitas'], 2) : '-' ?></td>
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
                    <h5 class="card-title mb-0"><i class="fas fa-clock me-2"></i>Informasi Sistem</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-sm table-borderless">
                                <tr>
                                    <td class="info-label" width="40%">Dibuat Pada</td>
                                    <td class="info-value"><?= $header['created_at'] ? date('d/m/Y H:i:s', strtotime($header['created_at'])) : '-' ?></td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-sm table-borderless">
                                <tr>
                                    <td class="info-label" width="40%">Diupdate Pada</td>
                                    <td class="info-value"><?= $header['updated_at'] ? date('d/m/Y H:i:s', strtotime($header['updated_at'])) : '-' ?></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="row mt-4">
                <div class="col-12">
                    <div class="d-flex justify-content-end">
                        <a href="javascript:void(0);" onclick="history.back()" class="btn btn-outline-secondary">
    <i class="fas fa-arrow-left me-2"></i>Kembali
</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    $(document).ready(function() {
        // Tambahkan tooltip untuk uraian barang yang dipotong
        $('[title]').tooltip({
            placement: 'top',
            trigger: 'hover'
        });
    });
    </script>
</body>
</html>