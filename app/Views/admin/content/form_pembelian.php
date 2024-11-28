<link rel="stylesheet" type="text/css" href="<?= base_url('assets') ?>/datatables/datatables.min.css" />
<style>
    /* Tambahan minimal CSS untuk fixed header */
    thead th {
        position: sticky;
        top: 0;
        background-color: #343a40;
        /* Warna background yang sama dengan header */
        z-index: 100;
    }

    body {
      font-family: Arial, sans-serif;
      margin: 20px;
    }
    .container {
      max-width: 400px;
      margin: 0 auto;
      padding: 20px;
      border: 1px solid #ddd;
      border-radius: 8px;
      background-color: #f9f9f9;
    }
    .form-group {
      margin-bottom: 15px;
    }
    .form-group label {
      display: block;
      margin-bottom: 5px;
    }
    .form-group input, .form-group select {
      width: 100%;
      padding: 8px;
      border: 1px solid #ccc;
      border-radius: 4px;
    }
    .form-group button {
      padding: 10px 20px;
      background-color: #4CAF50;
      color: white;
      border: none;
      border-radius: 4px;
      cursor: pointer;
    }
    .form-group button:hover {
      background-color: #45a049;
    }
    .total-price {
      margin-top: 20px;
      font-weight: bold;
    }
  </style>





<!-- Recent Sales Start -->
<div class="container-fluid pt-4 px-4">
                <div class="bg-light text-center rounded p-4">
                    <div class="d-flex align-items-center justify-content-between mb-4">
                        <h6 class="mb-0">Form Pembelian</h6>
                        <button class= "btn btn-primary">Tambah</button>
                    </div>
<div class="container">
    <h2>Form Pembelian Barang</h2>
    <form id="purchaseForm">
      <div class="form-group">
        <label for="item">Pilih Barang</label>
        <select id="item" name="item" required>
          <option value="">-- Pilih Barang --</option>
          <option value="item1" data-price="50000">Barang 1 (Rp 50.000)</option>
          <option value="item2" data-price="75000">Barang 2 (Rp 75.000)</option>
          <option value="item3" data-price="100000">Barang 3 (Rp 100.000)</option>
        </select>
      </div>

      <div class="form-group">
        <label for="quantity">Jumlah</label>
        <input type="number" id="quantity" name="quantity" value="1" min="1" required>
      </div>

      <div class="form-group">
        <button type="submit">Hitung Total</button>
      </div>
    </form>

    <div class="total-price" id="totalPrice">
      Total Harga: Rp 0
    </div>
  </div>
                </div>
            </div>
<!-- Recent Sales End -->


<!-- Widgets Start -->

<!-- Widgets End -->

<script type="text/javascript" src="<?= base_url('assets') ?>/js/form_pembelian.js"></script>
<script type="text/javascript" src="<?= base_url('assets') ?>/datatables/datatables.min.js"></script>