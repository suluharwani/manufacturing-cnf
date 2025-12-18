<!-- File: trackmaterial.php -->
<div class="container-fluid">
    <!-- Tombol Print Excel -->
    <div class="mb-3">
        <button type="button" class="btn btn-success" onclick="exportToExcel()">
            <i class="fas fa-file-excel"></i> Print to Excel
        </button>
    </div>

    <div id="resultTableContainer">
        <table class="table table-bordered table-striped" id="trackingTable">
            <thead>
                <tr>
                    <th>No</th>
                    <th>DATETIME</th>
                    <th>CODE</th>
                    <th>HSCODE</th>
                    <th>NAME</th>
                    <th>DESC</th>
                    <th>SOURCE</th>
                    <th>ACTIVITY</th>
                    <th>QUANTITY</th>
                    <th>BALANCE</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Initial balance row
                $balance = $balance_before;
                ?>
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td>Default</td>
                    <td>STOCK</td>
                    <td>From stock before <?= date('Y-m-d H:i:s', strtotime($params['start_date'])) ?></td>
                    <td>0</td>
                    <td class="balance"><?= $balance ?></td>
                </tr>

                <?php
                // Counter for row numbers
                $counter = 1;
                
                // Process all merged transactions
                foreach ($merge as $transaction) {
                    // Calculate running balance
                    $quantity = floatval($transaction['jumlah']);
                    $balance += $quantity;
                    
                    // Format datetime
                    $formattedDateTime = date('d/m/Y H:i:s', strtotime($transaction['created_at']));
                    
                    // Determine CSS class for quantity based on IN/OUT
                    $quantityClass = $transaction['desc'] === 'IN' ? 'in' : 'out';
                    
                    // Get material code (handle different array structures)
                    $materialCode = isset($transaction['materials_code']) ? $transaction['materials_code'] : '';
                    
                    // Get unit
                    $unit = isset($transaction['satuan']) ? $transaction['satuan'] : '';
                ?>
                <tr>
                    <td><?= $counter ?></td>
                    <td><?= $formattedDateTime ?></td>
                    <td><?= $materialCode ?></td>
                    <td class="hscode"><?= $transaction['hscode'] ?? '' ?></td>
                    <td><?= $transaction['materials_name'] ?></td>
                    <td class="<?= $quantityClass ?>"><?= $transaction['desc'] ?></td>
                    <td><?= $transaction['source'] ?></td>
                    <td><?= $transaction['activity'] ?></td>
                    <td class="<?= $quantityClass ?>"><?= abs($quantity) ?> (<?= $unit ?>)</td>
                    <td class="balance"><?= number_format($balance, 2) ?> (<?= $unit ?>)</td>
                </tr>
                <?php
                    $counter++;
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

<style>
.table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
    font-family: Arial, sans-serif;
}

.table th {
    background-color: #3498db;
    color: white;
    padding: 12px 15px;
    text-align: left;
    font-weight: bold;
    border: 1px solid #2980b9;
}

.table td {
    padding: 10px 15px;
    border: 1px solid #ddd;
}

.table tbody tr:nth-child(even) {
    background-color: #f8f9fa;
}

.table tbody tr:hover {
    background-color: #f1f8ff;
}

.in {
    color: #27ae60;
    font-weight: bold;
}

.out {
    color: #e74c3c;
    font-weight: bold;
}

.hscode {
    color: blue;
    font-weight: bold;
}

.balance {
    font-weight: bold;
    background-color: #fff3cd;
}

/* Styling for the first row (balance before) */
.table tbody tr:first-child {
    background-color: #e9ecef;
    font-style: italic;
}

/* Tombol style */
.btn-success {
    background-color: #28a745;
    border-color: #28a745;
    color: white;
    padding: 10px 20px;
    font-size: 14px;
    font-weight: bold;
}

.btn-success:hover {
    background-color: #218838;
    border-color: #1e7e34;
}

.fas {
    margin-right: 5px;
}
</style>

<script>
function exportToExcel() {
    // Create a temporary table for export (without styling classes)
    const table = document.getElementById('trackingTable');
    const tempTable = document.createElement('table');
    
    // Copy the table structure
    const thead = table.getElementsByTagName('thead')[0].cloneNode(true);
    const tbody = table.getElementsByTagName('tbody')[0].cloneNode(true);
    
    // Remove CSS classes from the cloned table
    removeClassesFromTable(thead);
    removeClassesFromTable(tbody);
    
    tempTable.appendChild(thead);
    tempTable.appendChild(tbody);
    
    // Create HTML content for Excel
    const htmlContent = `
        <html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel">
        <head>
            <meta charset="UTF-8">
            <!--[if gte mso 9]>
            <xml>
                <x:ExcelWorkbook>
                    <x:ExcelWorksheets>
                        <x:ExcelWorksheet>
                            <x:Name>Material Tracking</x:Name>
                            <x:WorksheetOptions>
                                <x:DisplayGridlines/>
                            </x:WorksheetOptions>
                        </x:ExcelWorksheet>
                    </x:ExcelWorksheets>
                </x:ExcelWorkbook>
            </xml>
            <![endif]-->
            <style>
                table { border-collapse: collapse; width: 100%; }
                th { background-color: #3498db; color: white; font-weight: bold; padding: 8px; border: 1px solid #000; }
                td { padding: 6px; border: 1px solid #000; }
                .in { color: #27ae60; font-weight: bold; }
                .out { color: #e74c3c; font-weight: bold; }
            </style>
        </head>
        <body>
            <h2>Material Tracking Report</h2>
            <p>Generated on: ${new Date().toLocaleString()}</p>
            ${tempTable.outerHTML}
        </body>
        </html>
    `;
    
    // Create blob and download
    const blob = new Blob([htmlContent], { type: 'application/vnd.ms-excel' });
    const url = URL.createObjectURL(blob);
    const link = document.createElement('a');
    link.href = url;
    
    // Create filename with timestamp
    const timestamp = new Date().toISOString().slice(0, 19).replace(/:/g, '-');
    link.download = `Material_Tracking_${timestamp}.xls`;
    
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    URL.revokeObjectURL(url);
}

function removeClassesFromTable(element) {
    const rows = element.getElementsByTagName('tr');
    for (let i = 0; i < rows.length; i++) {
        const cells = rows[i].getElementsByTagName('td');
        for (let j = 0; j < cells.length; j++) {
            cells[j].removeAttribute('class');
        }
        const thCells = rows[i].getElementsByTagName('th');
        for (let j = 0; j < thCells.length; j++) {
            thCells[j].removeAttribute('class');
        }
    }
}
</script>