<div class="container-fluid pt-4 px-4">
    <div class="row g-4">
        <div class="col-12">
            <div class="bg-light rounded h-100 p-4">
                <h1 class="mb-4"><?= $title ?></h1>
                
                <?php if (session('errors')): ?>
                    <div class="alert alert-danger">
                        <?php foreach (session('errors') as $error): ?>
                            <p class="mb-0"><?= $error ?></p>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <form action="/location/update/<?= $location['id'] ?>" method="post">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="code" class="form-label">Location Code</label>
                            <input type="text" class="form-control" id="code" name="code" 
                                   value="<?= old('code', $location['code']) ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label for="name" class="form-label">Location Name</label>
                            <input type="text" class="form-control" id="name" name="name" 
                                   value="<?= old('name', $location['name']) ?>" required>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="type" class="form-label">Location Type</label>
                            <select class="form-select" id="type" name="type" required>
                                <?php foreach ($locationTypes as $value => $label): ?>
                                    <option value="<?= $value ?>" <?= $location['type'] === $value ? 'selected' : '' ?>>
                                        <?= $label ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="parent_id" class="form-label">Parent Location (Optional)</label>
                            <select class="form-select" id="parent_id" name="parent_id">
                                <option value="">-- No Parent --</option>
                                <?php foreach ($parentLocations as $parent): ?>
                                    <option value="<?= $parent['id'] ?>" <?= $location['parent_id'] == $parent['id'] ? 'selected' : '' ?>>
                                        <?= esc($parent['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3"><?= old('description', $location['description']) ?></textarea>
                    </div>
                    
                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="is_active" name="is_active" value="1" 
                            <?= $location['is_active'] ? 'checked' : '' ?>>
                        <label class="form-check-label" for="is_active">Active Location</label>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">Update Location</button>
                    <a href="/location" class="btn btn-secondary">Cancel</a>
                </form>
            </div>
        </div>
    </div>
</div>