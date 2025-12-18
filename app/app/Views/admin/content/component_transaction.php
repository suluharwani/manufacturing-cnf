<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Component Transaction</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            color: #333;
            line-height: 1.6;
            padding: 20px;
            min-height: 100vh;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background-color: white;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        
        header {
            background: linear-gradient(90deg, #4b6cb7 0%, #182848 100%);
            color: white;
            padding: 20px;
            text-align: center;
        }
        
        h1 {
            font-size: 2.2rem;
            margin-bottom: 10px;
        }
        
        .content {
            padding: 20px;
        }
        
        .transaction-info {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        
        .form-control:focus {
            border-color: #4b6cb7;
            box-shadow: 0 0 0 0.2rem rgba(75, 108, 183, 0.25);
        }
        
        .btn-primary {
            background: linear-gradient(90deg, #4b6cb7 0%, #182848 100%);
            border: none;
        }
        
        .btn-primary:hover {
            opacity: 0.9;
            transform: translateY(-2px);
        }
        
        .items-table {
            width: 100%;
            margin-top: 20px;
        }
        
        .items-table th {
            background-color: #4b6cb7;
            color: white;
        }
        
        .quantity-input {
            width: 80px;
            text-align: center;
        }
        
        .action-buttons {
            margin-top: 20px;
            display: flex;
            gap: 10px;
            justify-content: flex-end;
        }
        
        .alert {
            margin-top: 20px;
        }
        
        .type-badge {
            font-size: 0.85em;
            padding: 0.35em 0.65em;
        }
        
        @media (max-width: 768px) {
            .action-buttons {
                flex-direction: column;
            }
            
            .btn {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h1><i class="fas fa-exchange-alt"></i> Component Transaction</h1>
            <p>Manage component stock transactions (IN/OUT)</p>
        </header>
        
        <div class="content">
            <div class="transaction-info row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="document_number">Document Number *</label>
                        <input type="text" class="form-control" id="document_number" name="document_number" required>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="responsible_person">Responsible Person *</label>
                        <input type="text" class="form-control" id="responsible_person" name="responsible_person" required>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="transaction_type">Transaction Type *</label>
                        <select class="form-control" id="transaction_type" name="transaction_type" required>
                            <option value="in">Stock IN (Addition)</option>
                            <option value="out">Stock OUT (Reduction)</option>
                        </select>
                    </div>
                </div>
            </div>
            
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title"><i class="fas fa-plus-circle"></i> Add Component</h5>
                </div>
                <div class="card-body">
                    <div class="input-group">
                        <input type="text" class="form-control" id="component_code" placeholder="Enter component code and press Enter">
                        <button class="btn btn-primary" type="button" id="add-component-btn">
                            <i class="fas fa-plus"></i> Add
                        </button>
                    </div>
                    <small class="form-text text-muted">Enter component code and press Enter or click Add button</small>
                </div>
            </div>
            
            <div class="alert alert-info mt-3" id="no-items-alert">
                <i class="fas fa-info-circle"></i> No components added yet. Start by entering a component code above.
            </div>
            
            <div class="table-responsive mt-3 d-none" id="items-table-container">
                <table class="table table-bordered table-hover items-table">
                    <thead>
                        <tr>
                            <th>Code</th>
                            <th>Name</th>
                            <th>Current Stock</th>
                            <th>Quantity</th>
                            <th>Unit</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody id="items-list">
                        <!-- Items will be added here dynamically -->
                    </tbody>
                </table>
            </div>
            
            <div class="action-buttons">
                <button class="btn btn-success" id="post-transaction-btn" disabled>
                    <i class="fas fa-check"></i> Post Transaction
                </button>
                <button class="btn btn-secondary" id="reset-form-btn">
                    <i class="fas fa-redo"></i> Reset
                </button>
            </div>
            
            <div class="alert alert-success mt-3 d-none" id="success-alert"></div>
            <div class="alert alert-danger mt-3 d-none" id="error-alert"></div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    $(document).ready(function() {
        let transactionItems = [];
        
        // Add component when Enter is pressed in the code input
        $('#component_code').keypress(function(e) {
            if (e.which === 13) {
                e.preventDefault();
                addComponent();
            }
        });
        
        // Add component when button is clicked
        $('#add-component-btn').click(function() {
            addComponent();
        });
        
        // Post transaction
        $('#post-transaction-btn').click(function() {
            postTransaction();
        });
        
        // Reset form
        $('#reset-form-btn').click(function() {
            resetForm();
        });
        
        function addComponent() {
            const code = $('#component_code').val().trim();
            
            if (!code) {
                showError('Please enter a component code');
                return;
            }
            
            // Check if component already exists in the list
            if (transactionItems.some(item => item.code === code)) {
                showError('This component is already in the list');
                return;
            }
            
            // Fetch component details from server
            $.ajax({
                url: '<?= base_url('component/getByCode/') ?>' + code,
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.status) {
                        const component = response.data;
                        
                        // Add to transaction items
                        transactionItems.push({
                            id: component.id,
                            code: component.kode,
                            name: component.nama,
                            current_stock: component.quantity || 0,
                            quantity: 1,
                            unit: component.satuan || 'pcs'
                        });
                        
                        // Update UI
                        renderItemsList();
                        $('#component_code').val('').focus();
                        hideNoItemsAlert();
                        updatePostButtonState();
                    } else {
                        showError('Component not found: ' + code);
                    }
                },
                error: function() {
                    showError('Error fetching component details');
                }
            });
        }
        
        function renderItemsList() {
            const $itemsList = $('#items-list');
            $itemsList.empty();
            
            transactionItems.forEach((item, index) => {
                const row = `
                    <tr>
                        <td>${item.code}</td>
                        <td>${item.name}</td>
                        <td>${item.current_stock} ${item.unit}</td>
                        <td>
                            <input type="number" min="1" value="${item.quantity}" 
                                   class="form-control quantity-input" 
                                   data-index="${index}"
                                   onchange="updateQuantity(${index}, this.value)">
                        </td>
                        <td>${item.unit}</td>
                        <td>
                            <button class="btn btn-sm btn-danger" onclick="removeItem(${index})">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                `;
                $itemsList.append(row);
            });
        }
        
        function updateQuantity(index, newQuantity) {
            if (newQuantity > 0) {
                transactionItems[index].quantity = parseInt(newQuantity);
            }
        }
        
        function removeItem(index) {
            transactionItems.splice(index, 1);
            renderItemsList();
            
            if (transactionItems.length === 0) {
                showNoItemsAlert();
            }
            
            updatePostButtonState();
        }
        
        function postTransaction() {
            // Validate form
            const documentNumber = $('#document_number').val().trim();
            const responsiblePerson = $('#responsible_person').val().trim();
            const transactionType = $('#transaction_type').val();
            
            if (!documentNumber) {
                showError('Please enter a document number');
                return;
            }
            
            if (!responsiblePerson) {
                showError('Please enter a responsible person');
                return;
            }
            
            if (transactionItems.length === 0) {
                showError('Please add at least one component');
                return;
            }
            
            // Prepare transaction data
            const transactionData = {
                document_number: documentNumber,
                responsible_person: responsiblePerson,
                type: transactionType,
                items: transactionItems
            };
            
            // Send to server
            $.ajax({
                url: '<?= base_url('component/saveTransaction') ?>',
                type: 'POST',
                data: JSON.stringify(transactionData),
                contentType: 'application/json',
                dataType: 'json',
                success: function(response) {
                    if (response.status) {
                        showSuccess('Transaction posted successfully!');
                        resetForm();
                    } else {
                        showError(response.message || 'Error posting transaction');
                    }
                },
                error: function(xhr, status, error) {
                    showError('Error: ' + (xhr.responseJSON?.message || error));
                }
            });
        }
        
        function resetForm() {
            transactionItems = [];
            $('#document_number').val('');
            $('#responsible_person').val('');
            $('#transaction_type').val('in');
            $('#component_code').val('').focus();
            
            renderItemsList();
            showNoItemsAlert();
            updatePostButtonState();
            
            $('#success-alert').addClass('d-none');
            $('#error-alert').addClass('d-none');
        }
        
        function showNoItemsAlert() {
            $('#no-items-alert').removeClass('d-none');
            $('#items-table-container').addClass('d-none');
        }
        
        function hideNoItemsAlert() {
            $('#no-items-alert').addClass('d-none');
            $('#items-table-container').removeClass('d-none');
        }
        
        function updatePostButtonState() {
            $('#post-transaction-btn').prop('disabled', transactionItems.length === 0);
        }
        
        function showSuccess(message) {
            $('#success-alert').removeClass('d-none').html(`<i class="fas fa-check-circle"></i> ${message}`);
            $('#error-alert').addClass('d-none');
        }
        
        function showError(message) {
            $('#error-alert').removeClass('d-none').html(`<i class="fas fa-exclamation-circle"></i> ${message}`);
            $('#success-alert').addClass('d-none');
        }
        
        // Make functions available globally for inline event handlers
        window.updateQuantity = updateQuantity;
        window.removeItem = removeItem;
    });
    </script>
</body>
</html>