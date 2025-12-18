<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="viewport" content="width=device-width, initial-scale=1.0">
    <title>Input Stok Barang</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2>Form Input Stok Barang</h2>
        <form action="#" method="POST">
            <div class="form-group">
                <label for="supplier">Supplier</label>
                <input type="text" id="supplier" name="supplier" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="tanggal">Tanggal</label>
                <input type="date" id="tanggal" name="tanggal" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="kode_po">Kode PO</label>
                <input type="text" id="kode_po" name="kode_po" class="form-control" required>
            </div>

            <h4>Daftar Barang</h4>
            <button type="button" id="addMaterial" class="btn btn-primary mb-3">Add Material</button>
            <table class="table table-bordered" id="stockTable">
                <thead>
                    <tr>
                        <th>Kode Barang</th>
                        <th>Nama Barang</th>
                        <th>Jumlah</th>
                        <th>Harga Satuan</th>
                        <th>Total Harga</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>

            <div class="form-group mt-3">
                <button type="submit" class="btn btn-success">Simpan</button>
            </div>
        </form>
    </div>

    <!-- Modal untuk Pencarian Material -->
    <div class="modal fade" id="materialModal" tabindex="-1" aria-labelledby="materialModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="materialModalLabel">Pilih Material</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="text" id="searchMaterial" class="form-control mb-3" placeholder="Cari material...">
                    <button type="button" id="searchButton" class="btn btn-primary mb-3">Cari</button>
                    <div id="materialList" class="list-group">
                        <!-- Daftar material akan dimunculkan di sini -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function () {
            // Membuka modal untuk memilih material
            $("#addMaterial").click(function () {
                $("#materialModal").modal('show');
            });

            // Pencarian material dengan AJAX ketika tombol "Cari" diklik
            $("#searchButton").click(function () {
                var query = $("#searchMaterial").val();
                if (query.length >= 2) {
                    $.ajax({
                        url: '/product/searchMaterial', // Ganti dengan endpoint pencarian material Anda
                        method: 'GET',
                        data: { query: query },
                        dataType: 'json',
                        success: function (data) {
                            $("#materialList").html('');
                            if (data.length > 0) {
                                data.forEach(function (item) {
                                    $("#materialList").append(`<a href="#" class="list-group-item list-group-item-action selectMaterial" data-kode="${item.kode}" data-nama="${item.name}">${item.kode} - ${item.name}</a>`);
                                });
                            } else {
                                $("#materialList").append(`<div class="list-group-item">Material tidak ditemukan</div>`);
                            }
                        }
                    });
                } else {
                    $("#materialList").html('<div class="list-group-item">Masukkan minimal 2 karakter untuk mencari material</div>');
                }
            });

            // Menambahkan material yang dipilih ke tabel
            $(document).on('click', '.selectMaterial', function (e) {
                e.preventDefault();
                var kode = $(this).data('kode');
                var nama = $(this).data('nama');

                // Cek apakah material sudah ada di tabel
                var isExist = false;
                $("#stockTable tbody tr").each(function () {
                    var existingKode = $(this).find('input[name="kode_barang[]"]').val();
                    if (existingKode === kode) {
                        isExist = true;
                        return false; // Berhenti looping jika ditemukan
                    }
                });

                // Jika material sudah ada, tampilkan peringatan
                if (isExist) {
                    alert("Material sudah ada dalam tabel!");
                } else {
                    // Tambahkan material baru ke tabel jika belum ada
                    var newRow = `<tr>
                        <td><input type="text" name="kode_barang[]" class="form-control" value="${kode}" readonly></td>
                        <td><input type="text" name="nama_barang[]" class="form-control" value="${nama}" readonly></td>
                        <td><input type="number" name="jumlah[]" class="form-control jumlah" required></td>
                        <td><input type="number" name="harga_satuan[]" class="form-control harga_satuan" required></td>
                        <td><input type="number" name="total_harga[]" class="form-control total_harga" readonly></td>
                        <td><button type="button" class="btn btn-danger btn-sm removeRow">Hapus</button></td>
                    </tr>`;
                    $("#stockTable tbody").append(newRow);
                    $("#materialModal").modal('hide');
                }
            });

            // Menghitung total harga per baris
            $(document).on('input', '.jumlah, .harga_satuan', function () {
                var row = $(this).closest('tr');
                var jumlah = row.find('.jumlah').val();
                var hargaSatuan = row.find('.harga_satuan').val();
                var totalHarga = jumlah * hargaSatuan;
                row.find('.total_harga').val(totalHarga);
            });

            // Menghapus baris
            $(document).on('click', '.removeRow', function () {
                $(this).closest('tr').remove();
            });
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
