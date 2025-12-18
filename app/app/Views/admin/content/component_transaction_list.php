<div class="card">
    <div class="card-header">
        <h3 class="card-title">Component Transactions</h3>
        <div class="card-tools">
            <a href="<?= base_url('component/transaction') ?>" class="btn btn-primary">
                <i class="fas fa-plus"></i> New Transaction
            </a>
        </div>
    </div>
    <div class="card-body">
        <table id="transactionTable" class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Date</th>
                    <th>Component Code</th>
                    <th>Component Name</th>
                    <th>Type</th>
                    <th>Quantity</th>
                    <th>Document No.</th>
                    <th>Responsible</th>
                    <th>Reference</th>
                    <th>Notes</th>
                    <th>Created By</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>
</div>

<script>
$(document).ready(function() {
    var table = $('#transactionTable').DataTable({
        "processing": true,
        "serverSide": true,
        "ajax": {
            "url": "<?= base_url('component/getTransactionData') ?>",
            "type": "POST"
        },
        "columns": [
            {"data": "id"},
            {"data": "created_at"},
            {"data": "component_code"},
            {"data": "component_name"},
            {"data": "type"},
            {"data": "quantity"},
            {"data": "document_number"},
            {"data": "responsible_person"},
            {"data": "reference"},
            {"data": "notes"},
            {"data": "created_by_name"},
            {
                "data": null,
                "render": function(data, type, row) {
                    return '<a href="<?= base_url('component/printTransaction') ?>/' + row.id + '" class="btn btn-sm btn-info" target="_blank"><i class="fas fa-print"></i> Print</a>';
                }
            }
        ],
        "order": [[0, 'desc']]
    });
});
</script>