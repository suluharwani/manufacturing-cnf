<div class="container-fluid pt-4 px-4">
    <div class="row g-4">
        <div class="col-12">
            <div class="bg-light rounded h-100 p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1><?= $title ?></h1>
                    <a href="/location/create" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>Add Location
                    </a>
                    
                </div>

                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="locationsTable" class="table table-striped table-hover" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>Code</th>
                                        <th>Name</th>
                                        <th>Type</th>
                                        <th>Parent</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($locations as $loc): ?>
                                    <tr>
                                        <td><?= esc($loc['code']) ?></td>
                                        <td><?= esc($loc['name']) ?></td>
                                        <td><?= ucfirst(esc($loc['type'])) ?></td>
                                        <td>
                                            <?php if ($loc['parent_id']): 
                                                $parent = $this->locationModel->withDeleted()->find($loc['parent_id']);
                                                echo esc($parent['name'] ?? 'N/A');
                                            else: ?>
                                                -
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <span class="badge bg-<?= $loc['is_active'] ? 'success' : 'danger' ?>">
                                                <?= $loc['is_active'] ? 'Active' : 'Inactive' ?>
                                            </span>
                                            <?php if ($loc['deleted_at']): ?>
                                                <span class="badge bg-secondary">Deleted</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="/location/stock/<?= $loc['id'] ?>" class="btn btn-sm btn-info" title="View Stock">
                                                    <i class="fas fa-info"></i>
                                                </a>
                                                <a href="/location/edit/<?= $loc['id'] ?>" class="btn btn-sm btn-primary">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <?php if ($loc['deleted_at']): ?>
                                                    <a href="/location/restore/<?= $loc['id'] ?>" class="btn btn-sm btn-warning">
                                                        <i class="fas fa-trash-restore"></i>
                                                    </a>
                                                <?php else: ?>
                                                    <a href="/location/delete/<?= $loc['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">
                                                        <i class="fas fa-trash"></i>
                                                    </a>
                                                <?php endif; ?>
                                                <a href="/location/toggle-status/<?= $loc['id'] ?>" class="btn btn-sm btn-<?= $loc['is_active'] ? 'warning' : 'success' ?>">
                                                    <i class="fas fa-power-off"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- DataTables Scripts -->
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css"/>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>

<script>
$(document).ready(function() {
    $('#locationsTable').DataTable({
        responsive: true,
        columnDefs: [
            { orderable: false, targets: [5] }
        ]
    });
});
</script>