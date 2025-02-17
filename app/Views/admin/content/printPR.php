<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Invoice PDF</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 8px;
            text-align: left;
        }
    </style>
</head>
<body>
    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Code</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($query0 as $row): ?>
                <tr>
                    <td><?= $row['invoice_date'] ?></td>
                    <td><?= $row['invoice_number'] ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <h2>Query 1: Product</h2>
    <table>
        <thead>
            <tr>
                <th>Kode</th>
                <th>Nama Produk</th>
                <th>Quantity</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($query1 as $row): ?>
                <tr>
                    <td><?= $row['kode'] ?></td>
                    <td><?= $row['nama'] ?></td>
                    <td><?= $row['quantity'] ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <h2>Query 2: Material dan Penggunaan (Bill of Material Finishing)</h2>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Material Name</th>
                <th>Material Code</th>
                <th>Penggunaan</th>
                <th>Quantity</th>
                <th>Product</th>
                <th>Total Penggunaan</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $nofinishing = 1;
            foreach ($query2 as $row):
                // Format nilai penggunaan dan total_penggunaan
                $penggunaan = rtrim(rtrim(number_format(floatval($row['penggunaan']), 4), '0'), '.');
                $total_penggunaan = rtrim(rtrim(number_format(floatval($row['total_penggunaan']), 4), '0'), '.');
            ?>
                <tr>
                    <td><?= $nofinishing++ ?></td>
                    <td><?= $row['material_name'] ?></td>
                    <td><?= $row['material_code'] ?></td>
                    <td><?= $penggunaan . ' ' . $row['satuan'] ?></td>
                    <td><?= $row['quantity'] ?></td>
                    <td><?= $row['product'] ?></td>
                    <td><?= $total_penggunaan . ' ' . $row['satuan'] ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <h2>Query 3: Material dan Penggunaan (Bill of Material)</h2>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Material Name</th>
                <th>Material Code</th>
                <th>Penggunaan</th>
                <th>Quantity</th>
                <th>Product</th>
                <th>Total Penggunaan</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $noModul = 1;
            foreach ($query3 as $row):
                // Format nilai penggunaan dan total_penggunaan
                $penggunaan = rtrim(rtrim(number_format(floatval($row['penggunaan']), 4), '0'), '.');
                $total_penggunaan = rtrim(rtrim(number_format(floatval($row['total_penggunaan']), 4), '0'), '.');
            ?>
                <tr>
                    <td><?= $noModul++ ?></td>
                    <td><?= $row['material_name'] ?></td>
                    <td><?= $row['material_code'] ?></td>
                    <td><?= $penggunaan . ' ' . $row['satuan'] ?></td>
                    <td><?= $row['quantity'] ?></td>
                    <td><?= $row['product'] ?></td>
                    <td><?= $total_penggunaan . ' ' . $row['satuan'] ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>