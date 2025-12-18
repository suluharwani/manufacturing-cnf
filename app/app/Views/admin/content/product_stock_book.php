<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stock Booking System</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        .card {
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
            margin-bottom: 1.5rem;
        }
        .table th {
            background-color: #f8f9fc;
            border-bottom: 1px solid #e3e6f0;
        }
        .alert {
            border-radius: 0.35rem;
            border: none;
        }
        .form-control, .form-select {
            border-radius: 0.35rem;
        }
        .btn {
            border-radius: 0.35rem;
            padding: 0.5rem 1rem;
        }
        .loading {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid rgba(255,255,255,.3);
            border-radius: 50%;
            border-top-color: #fff;
            animation: spin 1s ease-in-out infinite;
        }
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
    <div class="container-fluid pt-4 px-4">
        <div class="row g-4">
            <div class="col-12">
                <!-- Stock Summary Table -->
                
                
                <!-- Booking Form -->
                <div class="card">
                    <div class="card-body bg-light rounded p-4">
                        <h1 class="mb-4">Book Stock</h1>
                        
                        <!-- Error Messages -->
                        <?php if (session('error')): ?>
                            <div class="alert alert-danger"><?= session('error') ?></div>
                        <?php endif; ?>
                        
                        <?php if (session('errors')): ?>
                            <div class="alert alert-danger">
                                <?php foreach (session('errors') as $error): ?>
                                    <p class="mb-0"><?= $error ?></p>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                        
                        <!-- AJAX Error Messages -->
                        <div id="ajaxErrorAlert" class="alert alert-danger d-none">
                            <!-- Error messages will be displayed here -->
                        </div>
                        
                        <!-- AJAX Success Messages -->
                        <div id="ajaxSuccessAlert" class="alert alert-success d-none">
                            <!-- Success messages will be displayed here -->
                        </div>
                        
                        <form id="bookingForm">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Product</label>
                                    <input type="text" class="form-control" value="<?= esc($product['nama']) ?>" readonly>
                                    <input type="hidden" name="product_id" id="product_id" value="<?= $product['id'] ?>">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Finishing</label>
                                    <select class="form-select" name="finishing_id" id="finishing_select">
                                        <option value="">- Standard -</option>
                                        <?php foreach ($finishings as $finishing): ?>
                                            <option value="<?= $finishing['id'] ?>"><?= esc($finishing['name']) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Location</label>
                                    <select class="form-select" name="location_id" id="location_select" required>
                                        <option value="">- Select Location -</option>
                                        <?php foreach ($locations as $loc): ?>
                                            <option value="<?= $loc['id'] ?>"><?= $loc['name'] ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Available Quantity</label>
                                    <input type="text" class="form-control" id="available_quantity" readonly>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Quantity to Book</label>
                                <input type="number" class="form-control" name="quantity" id="quantity" min="1" required>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Proforma Invoice</label>
                                <select class="form-select" name="pi_id" id="pi_select" required>
                                    <option value="">Select Invoice</option>
                                    <?php foreach ($proformaInvoices as $pi): ?>
                                        <option value="<?= $pi['id'] ?>"><?= esc($pi['invoice_number']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Notes</label>
                                <textarea class="form-control" name="notes" rows="3"></textarea>
                            </div>
                            
                            <button type="submit" class="btn btn-primary" id="submitBtn">
                                <span id="submitText">Book Stock</span>
                                <span id="submitSpinner" class="d-none">
                                    <span class="loading"></span> Processing...
                                </span>
                            </button>
                            <a href="/productstock/view/<?= $product['id'] ?>" class="btn btn-secondary">Cancel</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        $(document).ready(function() {
            // Update available quantity ketika finishing atau location berubah
            $('#finishing_select, #location_select').change(function() {
                updateAvailableQuantity();
            });

            // Validasi quantity
            $('#quantity').on('input', function() {
                validateQuantity();
            });

            // Form submission dengan AJAX
            $('#bookingForm').submit(function(e) {
                e.preventDefault();
                submitBooking();
            });
        });

        // Fungsi untuk update available quantity
        function updateAvailableQuantity() {
            const productId = $('#product_id').val();
            const finishingId = $('#finishing_select').val();
            const locationId = $('#location_select').val();
            
            if (locationId) {
                // Tampilkan loading state
                $('#available_quantity').val('Loading...');
                
                $.get('/productstock/get-available-stock', {
                    product_id: productId,
                    finishing_id: finishingId,
                    location_id: locationId
                }, function(data) {
                    $('#available_quantity').val(data.available || 0);
                    validateQuantity();
                }).fail(function() {
                    $('#available_quantity').val('Error loading data');
                });
            } else {
                $('#available_quantity').val('');
            }
        }

        // Fungsi untuk validasi quantity
        function validateQuantity() {
            const quantity = parseInt($('#quantity').val()) || 0;
            const available = parseInt($('#available_quantity').val()) || 0;
            
            if (quantity > available) {
                $('#quantity').addClass('is-invalid');
                $('#submitBtn').prop('disabled', true);
            } else {
                $('#quantity').removeClass('is-invalid');
                $('#submitBtn').prop('disabled', false);
            }
        }

        // Fungsi untuk submit booking
   function submitBooking() {
    // Tampilkan loading state
    $('#submitText').addClass('d-none');
    $('#submitSpinner').removeClass('d-none');
    $('#submitBtn').prop('disabled', true);
    
    // Sembunyikan pesan sebelumnya
    $('#ajaxErrorAlert').addClass('d-none');
    $('#ajaxSuccessAlert').addClass('d-none');
    
    // Dapatkan data form
    const formData = {
        product_id: $('#product_id').val(),
        finishing_id: $('#finishing_select').val(),
        location_id: $('#location_select').val(),
        quantity: $('#quantity').val(),
        pi_id: $('#pi_select').val(),
        notes: $('textarea[name="notes"]').val()
    };
    
    // Validasi client-side
    if (!validateForm(formData)) {
        resetSubmitButton();
        return;
    }
    
    // Handle finishing_id yang kosong
    const finishingId = formData.finishing_id || '0';
    
    // Submit via AJAX
    $.ajax({
        url: `/productstock/process-booking/${formData.product_id}/${finishingId}`,
        type: 'POST',
        data: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        },
        success: function(response) {
            if (response.success) {
                // Tampilkan pesan sukses
                showSuccess(response.message || 'Stock successfully booked!');
                
                // Reset form
                $('#bookingForm')[0].reset();
                $('#available_quantity').val('');
                
                // Redirect atau refresh halaman setelah beberapa detik
                setTimeout(() => {
                    window.location.href = '/productstock/view/' + formData.product_id;
                }, 2000);
            } else {
                // Tampilkan pesan error
                showErrors(response.errors || ['An error occurred during booking']);
            }
        },
        error: function(xhr, status, error) {
            // Tampilkan pesan error
            let errorMessage = 'An error occurred while processing your request.';
            
            try {
                const response = JSON.parse(xhr.responseText);
                if (response.errors) {
                    showErrors(response.errors);
                    return;
                }
                if (response.message) {
                    errorMessage = response.message;
                }
            } catch (e) {
                // Jika response bukan JSON, gunakan default message
                if (xhr.status === 500) {
                    errorMessage = 'Internal server error. Please try again later.';
                } else if (xhr.status === 404) {
                    errorMessage = 'Request not found. Please check the URL.';
                }
            }
            
            showErrors([errorMessage]);
        },
        complete: function() {
            resetSubmitButton();
        }
    });
}

// Fungsi validasi form
function validateForm(formData) {
    const errors = [];
    
    if (!formData.quantity || formData.quantity <= 0) {
        errors.push('Quantity must be greater than 0');
    }
    
    if (!formData.location_id) {
        errors.push('Please select a location');
    }
    
    if (!formData.pi_id) {
        errors.push('Please select a proforma invoice');
    }
    
    if (errors.length > 0) {
        showErrors(errors);
        return false;
    }
    
    return true;
}

// Fungsi tampilkan error
function showErrors(errors) {
    const errorList = $('#errorList');
    errorList.empty();
    
    errors.forEach(error => {
        errorList.append(`<li>${error}</li>`);
    });
    
    $('#ajaxErrorAlert').removeClass('d-none');
}

// Fungsi tampilkan sukses
function showSuccess(message) {
    $('#successMessage').text(message);
    $('#ajaxSuccessAlert').removeClass('d-none');
}

// Fungsi reset button
function resetSubmitButton() {
    $('#submitText').removeClass('d-none');
    $('#submitSpinner').addClass('d-none');
    $('#submitBtn').prop('disabled', false);
}

        // Fungsi untuk validasi form
        function validateForm(formData) {
            const errors = [];
            
            if (!formData.location_id) {
                errors.push('Location is required');
            }
            
            if (!formData.quantity || formData.quantity < 1) {
                errors.push('Valid quantity is required');
            }
            
            const available = parseInt($('#available_quantity').val()) || 0;
            if (parseInt(formData.quantity) > available) {
                errors.push('Requested quantity exceeds available stock');
            }
            
            if (!formData.pi_id) {
                errors.push('Proforma invoice is required');
            }
            
            if (errors.length > 0) {
                showErrors(errors);
                return false;
            }
            
            return true;
        }

        // Fungsi untuk menampilkan error
        function showErrors(errors) {
            let errorHtml = '';
            errors.forEach(error => {
                errorHtml += `<p class="mb-0">${error}</p>`;
            });
            
            $('#ajaxErrorAlert').html(errorHtml).removeClass('d-none');
            
            // Scroll ke pesan error
            $('html, body').animate({
                scrollTop: $('#ajaxErrorAlert').offset().top - 100
            }, 500);
        }

        // Fungsi untuk menampilkan pesan sukses
        function showSuccess(message) {
            $('#ajaxSuccessAlert').html(`<p class="mb-0">${message}</p>`).removeClass('d-none');
            
            // Scroll ke pesan sukses
            $('html, body').animate({
                scrollTop: $('#ajaxSuccessAlert').offset().top - 100
            }, 500);
        }

        // Fungsi untuk reset tombol submit
        function resetSubmitButton() {
            $('#submitText').removeClass('d-none');
            $('#submitSpinner').addClass('d-none');
            $('#submitBtn').prop('disabled', false);
        }
    </script>
</body>
</html>