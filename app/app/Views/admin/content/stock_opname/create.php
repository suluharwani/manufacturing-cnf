<?= $this->extend('admin/content/layout/template'); ?>

<?= $this->section('content'); ?>
<div class="container">
    <div class="row">
        <div class="col">
            <h1 class="mt-2">Create Stock Opname</h1>

            <form action="/stock-opname/save" method="post">
                <?= csrf_field(); ?>
                <div class="form-group">
                    <label for="code">Opname Code</label>
                    <input type="text" class="form-control <?= ($validation->hasError('code')) ? 'is-invalid' : ''; ?>" id="code" name="code" value="<?= old('code'); ?>">
                    <div class="invalid-feedback">
                        <?= $validation->getError('code'); ?>
                    </div>
                </div>
                <div class="form-group">
                    <label for="remarks">Remarks</label>
                    <textarea class="form-control <?= ($validation->hasError('remarks')) ? 'is-invalid' : ''; ?>" id="remarks" name="remarks"><?= old('remarks'); ?></textarea>
                    <div class="invalid-feedback">
                        <?= $validation->getError('remarks'); ?>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">Create</button>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection(); ?>