<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Invoice <?= $invoice['invoice_number'] ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        .invoice-header {
            text-align: center;
            margin-bottom: 20px;
        }
        .invoice-details {
            margin-bottom: 20px;
        }
        .invoice-table {
            width: 100%;
            border-collapse: collapse;
        }
        .invoice-table th, .invoice-table td {
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
    <div class="invoice-header">
        <h1>Invoice</h1>
        <p>Invoice Number: <?= $invoice['invoice_number'] ?></p>
        <p>Invoice Date: <?= $invoice['invoice_date'] ?></p>
        <p>Customer Address: <?= nl2br($invoice['customer_address']) ?></p>
    </div>

    <div class="invoice-details">
        <h2>Invoice Details</h2>
        <table class="invoice-table">
            <thead>
                <tr>
                    <th>Item Description</th>
                    <th>HS Code</th>
                    <th>Quantity</th>
                    <th>Unit Price</th>
                    <th>Total Price</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $total = 0; 
                foreach ($details as $detail): ?>
                    <tr>
                        <td><?= $detail['item_description'] ?: $detail['product']['nama'] ?></td>
                        <td><?= $detail['hs_code'] ?: $detail['product']['hs_code'] ?></td>
                        <td><?= $detail['quantity'] ?></td>
                        <td><?= number_format($detail['unit_price'], 2) ?></td>
                        <td><?= number_format($detail['quantity']*$detail['unit_price'], 2) ?></td>
                    </tr>
                <?php 
                $total += $detail['quantity']*$detail['unit_price'];
            endforeach; ?>
            </tbody>
        </table>
    </div>

    <div class="total">
        <p>Total Amount: <?= number_format($total, 2) ?></p>
    </div>
</body>
</html>
