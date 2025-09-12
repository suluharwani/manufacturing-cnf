<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Transaction Report - <?= $transaction['document_number'] ?></title>
    <style>
        body { font-family: Arial, sans-serif; }
        .header { text-align: center; margin-bottom: 20px; }
        .details { margin-bottom: 20px; }
        .details table { width: 100%; border-collapse: collapse; }
        .details td { padding: 5px; border: 1px solid #ddd; }
        .details td:first-child { font-weight: bold; width: 30%; }
        .footer { margin-top: 50px; text-align: right; }
    </style>
</head>
<body>
    <div class="header">
        <h2>COMPONENT TRANSACTION</h2>
        <h3>Document: <?= $transaction['document_number'] ?></h3>
    </div>
    
    <div class="details">
        <table>
            <tr>
                <td>Transaction Date</td>
                <td><?= date('d/m/Y H:i', strtotime($transaction['created_at'])) ?></td>
            </tr>
            <tr>
                <td>Component</td>
                <td><?= $transaction['component_code'] ?> - <?= $transaction['component_name'] ?></td>
            </tr>
            <tr>
                <td>Type</td>
                <td><?= strtoupper($transaction['type']) ?></td>
            </tr>
            <tr>
                <td>Quantity</td>
                <td><?= $transaction['quantity'] ?> <?= $transaction['satuan'] ?></td>
            </tr>
            <tr>
                <td>Responsible Person</td>
                <td><?= $transaction['responsible_person'] ?></td>
            </tr>
            <tr>
                <td>Reference</td>
                <td><?= $transaction['reference'] ?? '-' ?></td>
            </tr>
            <tr>
                <td>Notes</td>
                <td><?= $transaction['notes'] ?? '-' ?></td>
            </tr>
            <tr>
                <td>Created By</td>
                <td><?= $transaction['created_by_name'] ?? 'System' ?></td>
            </tr>
        </table>
    </div>
    
    <div class="footer">
        <p>Printed on: <?= date('d/m/Y H:i') ?></p>
    </div>
</body>
</html>