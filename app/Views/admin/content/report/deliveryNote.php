<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Surat Jalan <?= $invoice['invoice_number'] ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .details {
            margin-bottom: 20px;
        }
        .details table {
            width: 100%;
            border-collapse: collapse;
        }
        .details th, .details td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
        }
        .total {
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>SURAT JALAN</h1>
        <p>Alamat Pengiriman: <?= nl2br($invoice['customer_address']) ?></p>
        <p>Delivery Order No: <?= $invoice['invoice_number'] ?></p>
        <p>Tanggal: <?= $invoice['invoice_date'] ?></p>
        <p>Driver: [Driver Name]</p>
        <p>Nopol: [Nopol]</p>
    </div>

    <div class="details">
        <h2>Detail Pengiriman</h2>
        <table>
            <thead>
                <tr>
                    <th>PHOTO</th>
                    <th>CODE</th>
                    <th>ITEM</th>
                    <th>Detail of Finishing & Fabric</th>
                    <th>Qty Item PCS</th>
                    <th>Packaging</th>
                    <th>CBM for each item</th>
                    <th>Total CBM</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $totalCBM = 0;
                
                foreach ($details as $detail): 
                    $cbm = $detail['product']['cbm'] ?: 0; // Assuming cbm is in product details
                    $totalCBM += $cbm * $detail['quantity'];
                ?>
                    <tr>
                        <td><img src="<?= base_url('uploads/' . $detail['product']['picture']) ?>" alt="Product Image" width="50"></td>
                        <td><?= $detail['product']['kode'] ?></td>
                        <td><?= $detail['product']['nama'] ?></td>
                        <td><?= $detail['item_description'] ?></td>
                        <td><?= $detail['quantity'] ?></td>
                        <td><?= $detail['unit'] ?></td>
                        <td><?= number_format($cbm, 2) ?></td>
                        <td><?= number_format($cbm * $detail['quantity'], 2) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <p class="total">Total CBM: <?= number_format($totalCBM, 2) ?></p>
    </div>

    <div>
        <p>Driver: [Driver Name]</p>
        <p>Penerima: [Receiver Name]</p>
        <p>Security: [Security Name]</p>
    </div>
</body>
</html>
