var loc = window.location;
var base_url = loc.protocol + "//" + loc.hostname + (loc.port? ":"+loc.port : "") + "/";

    $(document).ready(function () {
        // Show the modal when the button is clicked
        $('#listLaporanBtnKS').on('click', function () {
            $('#laporanModal').modal('show');
        });

        // Populate material select options (you can fetch this from your server)
        function loadMaterials() {
            $.ajax({
                url: base_url + 'product/getMaterial', // Update with your API endpoint
                method: 'GET',
                success: function (data) {
                    // Clear existing options
                    $('#materialOptions').empty();
    
                    // Populate the dropdown with new options
                    data.material.forEach(function (material) {
                        $('#materialOptions').append(`
                            <a class="dropdown-item" href="#" data-id="${material.id}">${material.name}</a>
                        `);
                    });
                },
                error: function (xhr, status, error) {
                    console.error("Error loading materials: ", error);
                }
            });
        }
    
        // Call the function to load materials
        loadMaterials();
    
        // Search functionality
        $('#searchInput').on('input', function() {
            var searchValue = $(this).val().toLowerCase();
            $('#materialOptions .dropdown-item').filter(function() {
                $(this).toggle($(this).text().toLowerCase().indexOf(searchValue) > -1);
            });
        });
    
        // Handle selection
        $(document).on('click', '.dropdown-item', function() {
            var selectedMaterial = $(this).text();
            var selectedId = $(this).data('id');
            $('#searchInput').val(selectedMaterial); // Set the input value
            $('#dropdownMenu').removeClass('show'); // Hide the dropdown
            // You can store the selected ID in a hidden input or use it as needed
            // console.log("Selected Material ID: ", selectedId);
            $('#materialSelect').val(selectedId); // Set the selected material ID

        });
    
        // Show dropdown on focus
        $('#searchInput').on('focus', function() {
            $('#dropdownMenu').addClass('show');
        });
    
        // Hide dropdown when clicking outside
        $(document).on('click', function(e) {
            if (!$(e.target).closest('.dropdown').length) {
                $('#dropdownMenu').removeClass('show');
            }
        });

        // Generate report on button click
        $('#generateReportBtnMaterial').on('click', function () {
            const startDate = $('#startDate').val();
            const endDate = $('#endDate').val();
            const materialId = $('#materialSelect').val();
            $.ajax({
                url: base_url+'report/materialStockCard', 
                method: 'POST',
                data: {
                    start_date: startDate,
                    end_date: endDate,
                    material_id: materialId
                },
                success: function (result) {
                    var data = JSON.parse(result);
                    var tableBody = $('#resultTableBody');
                    tableBody.empty(); // Clear existing rows
                    let row = `<thead>
            <tr>
                <th>ID</th>
                <th>ID Pembelian</th>
                <th>ID Material</th>
                <th>ID Currency</th>
                <th>Jumlah</th>
                <th>Harga</th>
                <th>Status Pembayaran</th>
                <th>Diskon 1</th>
                <th>Diskon 2</th>
                <th>Diskon 3</th>
                <th>Pajak</th>
                <th>Potongan</th>
                <th>Created At</th>
                <th>Updated At</th>
            </tr>
        </thead>`; // Initialize the row variable
                    // Loop through the pembelian array and create table rows
                    data.pembelian.forEach(function (item) {

                        row += `
                            <tr>
                                <td>${item.id}</td>
                                <td>${item.id_pembelian}</td>
                                <td>${item.id_material}</td>
                                <td>${item.id_currency}</td>
                                <td>${item.jumlah !== null ? item.jumlah : 'N/A'}</td>
                                <td>${item.harga !== null ? item.harga : 'N/A'}</td>
                                <td>${item.status_pembayaran !== null ? item.status_pembayaran : 'N/A'}</td>
                                <td>${item.diskon1}</td>
                                <td>${item.diskon2}</td>
                                <td>${item.diskon3}</td>
                                <td>${item.pajak}</td>
                                <td>${item.potongan}</td>
                                <td>${item.created_at}</td>
                                <td>${item.updated_at}</td>
                            </tr>
                        `;

                    });
                    console.log(row);

                    tableBody = row; // Append the row to the table body
                
                    $('#resultTableContainer').html(tableBody); // Update the table container
                    $('#laporanModal').modal('hide');

                }
            });
        });

        // Print to Excel functionality
        $('#printExcelBtn').on('click', function () {
            const table = document.getElementById('resultTableContainer');
            const wb = XLSX.utils.table_to_book(table, { sheet: "Sheet1" });
            XLSX.writeFile(wb, 'Laporan_Material.xlsx');
        });
    });

    $(document).ready(function () {
        // Show the modal when the button is clicked
        $('#listLaporanBtnKS').on('click', function () {
            $('#laporanModal').modal('show');
        });

        // Populate material select options (you can fetch this from your server)
        function loadMaterials() {
            $.ajax({
                url: 'path/to/your/materials/api', // Update with your API endpoint
                method: 'GET',
                success: function (data) {
                    data.forEach(function (material) {
                        $('#materialSelect').append(new Option(material.name, material.id));
                    });
                }
            });
        }

        loadMaterials();

        // Generate report on button click
        $('#generateReportBtn').on('click', function () {
            const startDate = $('#startDate').val();
            const endDate = $('#endDate').val();
            const materialId = $('#materialSelect').val();

            $.ajax({
                url: 'path/to/your/report/api', // Update with your report API endpoint
                method: 'POST',
                data: {
                    start_date: startDate,
                    end_date: endDate,
                    material_id: materialId
                },
                success: function (result) {
                    $('#resultTableContainer').html(result); // Assuming result is HTML
                    $('#laporanModal').modal('hide');
                }
            });
        });

        // Print to Excel functionality
        $('#printExcelBtn').on('click', function () {
            const table = document.getElementById('resultTableContainer');
            const wb = XLSX.utils.table_to_book(table, { sheet: "Sheet1" });
            XLSX.writeFile(wb, 'Laporan_Material.xlsx');
        });
    });
