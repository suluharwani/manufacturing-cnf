var loc = window.location;
var base_url = loc.protocol + "//" + loc.hostname + (loc.port? ":"+loc.port : "") + "/";
window.jsPDF = window.jspdf.jsPDF
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
            const selectlist = $('#selectlist').val();
                // Tambahkan waktu ke endDate
    let startDateTime = startDate + ' 00:00:00';
    let endDateTime = endDate + ' 23:59:59';

    // Jika startDate adalah datetime-local, maka format juga

            const materialId = $('#selectlist').val();
            $.ajax({
                url: base_url+'report/materialStockCard',  
                method: 'POST',
                data: {
                    start_date: startDateTime,
                    end_date: endDateTime,
                    material_id: materialId
                },
                success: function (result) {
                    data = JSON.parse(result);
                    let balanceBefore = data.balance_before
                    let no = 1;
                    var data = JSON.parse(result);
                    var tableBody = $('#resultTableBody');
                    tableBody.empty(); // Clear existing rows
                    let row = `<thead>
            <tr>
                <th>No</th>
                <th>DATE</th>
                <th>CODE</th>
                <th>NAME</th>
                <th>DESC</th>
                <th>SOURCE</th>
                <th>ACTIVITY</th>
                <th>QUANTITY</th>
                <th>BALANCE</th>
            </tr>
        </thead>`; // Initialize the row variable
                    // Loop through the pembelian array and create table rows
           

                    // data.pembelian.forEach(function (item) {

                        row += `
                            <tr>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td>Default</td>
                                <td>STOCK</td>
                                <td>From stock before ${startDate}</td>
            
                                <td>${balanceBefore}</td>
                                <td>${balanceBefore}</td>
                            </tr>
                        `;
                    // console.log(result);
                    // });
                    let total = parseFloat(balanceBefore);
                                        // <option value="all">Semua Material</option>
                                        // <option value="materialDestruction">Material Destruction</option>
                                        // <option value="materialRequisition">Material Requisition</option>
                                        // <option value="materialReceiptNote">Material Receipt Note</option>
                                        // <option value="materialReturn">Material Return</option>
                                        // <option value="opname">Stock Opname</option>
                                let dataview = ''
                                console.log(selectlist)
                            if (selectlist === 'all' || selectlist === '') {
                                dataview = data.merge
                            } else if(selectlist == 'materialDestruction' ) {
                                dataview = data.destruction
                            }else if(selectlist == 'materialRequisition'){
                                dataview = data.material_requisition
                            }else if (selectlist == 'materialReceiptNote'){
                                dataview = data.pembelian
                            }
                            else if(selectlist == 'materialReturn'){
                                dataview = data.return
                            }
                            else if(selectlist == 'opname'){
                                dataview = data.stock_opname
                            }

                            console.log(dataview)
                            
                            dataview.forEach(function (item) {
                            total+=parseFloat(item.jumlah ? item.jumlah : 0);
                        row += `
                            <tr>
                                <td>${no++}</td>
                                <td>${formatDateIndo(item.created_at)}</td>
                                <td>${item.materials_code}</td>
                                <td>${item.materials_name}</td>
                                <td>${item.desc }</td>
                                <td>${item.source }</td>
                                <td>${item.activity }</td>
                                <td>${item.jumlah }</td>
                                <td>${total}</td>
                            </tr>
                        `;

                    });

                    tableBody = row; // Append the row to the table body
                
                    $('#resultTableContainer').html(tableBody); // Update the table container
                    $('#laporanModal').modal('hide');

                }
            });
        });

        // Print to Excel functionality
        // $('#printExcelBtn').on('click', function () {
        //     const table = document.getElementById('resultTableContainer');
        //     const wb = XLSX.utils.table_to_book(table, { sheet: "Sheet1" });
        //     XLSX.writeFile(wb, 'Laporan_Material.xlsx');
        // });
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
        $('#printBtn').on('click', function () {
            const startDate = $('#startDate').val();
            const endDate = $('#endDate').val();
            const id = $('#materialSelect').val(); // Get the selected material ID
        
            // Make an AJAX request to fetch the header data
            $.ajax({
                url: base_url+'report/getHeader',  // Your API endpoint for fetching the material header
                type: 'GET',
                data: { id: id }, // Send the selected ID to the server
                success: function(response) {
                    // Assuming the server responds with an object like { code, name, admin }
                    const dataHeader = response.data;
        
                    const { jsPDF } = window.jspdf;
                    const pdf = new jsPDF('l', 'mm', 'a4'); // Landscape orientation, mm units, A4 size
        
                    // Add title
                    pdf.setFontSize(18);
                    pdf.text('MATERIAL ACTIVITY REPORT', 10, 10);
        
                    // Add a table for Item Codes with specific values
                    pdf.setFontSize(12);
                    const itemCodeData = [
                        ['Item Code', `${dataHeader.code}`], // First row of item codes
                        ['Name', `${dataHeader.name}`], // Second row of item codes
                        ['Periode', `${startDate} - ${endDate}`], // Period for the report
                        ['Admin', `${dataHeader.admin}`],  // Admin responsible for the material
                        ['Satuan', `${dataHeader.satuan}`]  // Admin responsible for the material
                    ];
        
                    // Add header for Item Codes without borders
                    pdf.autoTable({
                        body: itemCodeData, // Body rows with item code values
                        startY: 20, // Start Y position for the table
                        theme: 'grid', // Use grid theme
                        margin: { horizontal: 10 }, // Margin from the edges
                        styles: {
                            overflow: 'linebreak', // Allow line breaks
                            cellWidth: 'auto', // Auto width for cells
                            fontSize: 10, // Font size for the item codes
                            lineWidth: 0, // Disable borders
                            fillColor: null // Disable fill color
                        },
                        columnStyles: {
                            0: { cellWidth: 80 }, // Set width for each column
                            1: { cellWidth: 80 },
                  
                        }
                    });
        
                    // Define columns and data for material activity
                    const columns = [
                        { header: 'NO', dataKey: 'no' },
                        { header: 'DATE', dataKey: 'date' },
                        { header: 'CODE', dataKey: 'code' },
                        { header: 'NAME', dataKey: 'name' },
                        { header: 'DESC', dataKey: 'desc' },
                        { header: 'SOURCE', dataKey: 'source' },
                        { header: 'ACTIVITY', dataKey: 'activity' },
                        { header: 'QUANTITY', dataKey: 'quantity' },
                        { header: 'BALANCE', dataKey: 'balance' }
                    ];
        
                    // Prepare data for the material activity table
                    const data = [];
                    $('#resultTableContainer tr').each(function(index) {
                        // Skip the first row (header)
                        if (index === 0) return;
        
                        const row = $(this).find('td');
                        const rowData = {
                            no: row.eq(0).text() || 'N/A',
                            date: row.eq(1).text() || 'N/A',
                            code: row.eq(2).text() || 'N/A',
                            name: row.eq(3).text() || 'N/A',
                            desc: row.eq(4).text() || 'N/A',
                            source: row.eq(5).text() || 'N/A',
                            activity: row.eq(6).text() || 'N/A',
                            quantity: row.eq(7).text() || 'N/A',
                            balance: row.eq(8).text() || 'N/A'
                        };
                        data.push(rowData);
                    });
        
                    // Create material activity table
                    pdf.autoTable({
                        head: [columns.map(col => col.header)], // Use headers for the first row
                        body: data.map(item => columns.map(col => item[col.dataKey])), // Use data for the table body
                        startY: 60, // Start Y position for the material activity table (below the Item Code table)
                        theme: 'grid', // Optional: choose a theme
                        margin: { horizontal: 10 }, // Margin from the edges
                        styles: {
                            overflow: 'linebreak', // Allow line breaks
                            cellWidth: 'auto', // Auto width for cells
                            fontSize: 10 // Font size for the material activity table
                        },
                        columnStyles: {
                            // Optional: set specific styles for columns
                            0: { cellWidth: 10 }, // Example: set width for the first column
                            1: { cellWidth: 30 }, // Example: set width for the second column
                            // Add more styles as needed
                        }
                    });
        
                    // Save the PDF
                    pdf.save('Laporan_Material.pdf');
                },
                error: function(error) {
                    console.error('Error fetching header data:', error);
                }
            });
        });
        
        
        
 



    });

    function formatDateIndo(dateString) {
        // Create a new Date object from the input date string
        const date = new Date(dateString);
    
        // Get the day, month, and year
        const day = String(date.getDate()).padStart(2, '0'); // Pad with leading zero if needed
        const month = String(date.getMonth() + 1).padStart(2, '0'); // Months are zero-based
        const year = date.getFullYear();
    
        // Return the formatted date in DD/MM/YYYY
        return `${day}/${month}/${year}`;
    }
    function formatAngka(angka) {
        // Memisahkan bagian desimal dan ribuan
        let [bagianRibuan, bagianDesimal] = angka.toString().split(".");
        
        // Menambahkan titik sebagai pemisah ribuan
        bagianRibuan = bagianRibuan.replace(/\B(?=(\d{3})+(?!\d))/g, ".");
        
        // Mengatur bagian desimal, maksimal 2 angka
        if (bagianDesimal) {
            bagianDesimal = bagianDesimal.substring(0, 2);
        } else {
            bagianDesimal = "00"; // Jika tidak ada bagian desimal
        }
        
        return `${bagianRibuan},${bagianDesimal}`;
    }