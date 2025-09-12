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
        .company-info { margin-bottom: 30px; text-align: center; }
        .company-name { font-size: 24px; font-weight: bold; }
        .report-title { font-size: 18px; margin: 10px 0; }
    </style>
</head>
<body>
    <div class="company-info">
        <div class="company-name">YOUR COMPANY NAME</div>
        <div class="report-title">COMPONENT TRANSACTION REPORT</div>
    </div>
    
    <div class="header">
        <h3>Document: <?= $transaction['document_number'] ?></h3>
    </div>
    
    <div class="details">
        <table>
            <tr>
                <td>Transaction Date</td>
                <td><?= date('d/m/Y H:i', strtotime($transaction['created_at'])) ?></td>
            </tr>
            <tr>
                <td>Component Code</td>
                <td><?= $transaction['component_code'] ?></td>
            </tr>
            <tr>
                <td>Component Name</td>
                <td><?= $transaction['component_name'] ?></td>
            </tr>
            <tr>
                <td>Transaction Type</td>
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