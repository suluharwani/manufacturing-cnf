<?= $this->extend('admin/content/layout/template'); ?>

<?= $this->section('content'); ?>
<div class="container">
    <div class="row">
        <div class="col">
            <h1 class="mt-2">Stock Opname</h1>
            <a href="/stock-opname/create" class="btn btn-primary mb-3">Create New</a>

            <?php if (session()->getFlashdata('message')) : ?>
                <div class="alert alert-success" role="alert">
                    <?= session()->getFlashdata('message'); ?>
                </div>
            <?php endif; ?>

            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Code</th>
                        <th>Date</th>
                        <th>Remarks</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $i = 1; ?>
                    <?php foreach ($opnames as $opname) : ?>
                        <tr>
                            <td><?= $i++; ?></td>
                            <td><?= $opname['code']; ?></td>
                            <td><?= $opname['created_at']; ?></td>
                            <td><?= $opname['remarks']; ?></td>
                            <td><?= $opname['status'] == 0 ? 'Draft' : 'Completed'; ?></td>
                            <td>
                                <a href="/stock-opname/detail/<?= $opname['id']; ?>" class="btn btn-sm btn-info">Detail</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?= $this->endSection(); ?>