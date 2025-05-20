
<?php
function formatCurrency($number, $decimals = 0, $decimalSeparator = '.', $thousandSeparator = ',') {
    // Validasi input harus numeric
    if(!is_numeric($number)) {
        throw new InvalidArgumentException('Input must be a numeric value');
    }
    
    // Format angka sesuai parameter
    return number_format(
        (float)$number,
        $decimals,
        $decimalSeparator,
        $thousandSeparator
    );

}
function formatDate($dateString) {
    // Ubah string menjadi objek DateTime
    $date = DateTime::createFromFormat('Y-m-d', $dateString);
    
    // Periksa apakah tanggal valid
    if (!$date) {
        return "-";
    }

    // Format ke dalam bahasa Inggris: "Month Day, Year"
    return $date->format('F j, Y');
}
function hargaDisc($price, $discount) {
    // Validasi input harus numerik

    // Jika discount 0, kembalikan harga asli
    if ($discount == 0) {
        return $price;
    }

    // Hitung harga setelah diskon
    $discountedPrice = $price - ($price * ($discount / 100));

    return $discountedPrice;
}
function convertcm($mm) {
    if (empty($mm) && $mm !== 0 && $mm !== '0') {
        return "-";
    }
    return $mm / 10;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Proforma Invoice</title>
    <style>
        @page {
            size: A4 landscape; 
            margin: 1cm;
        }
        body {
            font-family: Arial, sans-serif;
            font-size: 9pt;
            margin: 0;
        }
        .a4-container {
            width: 27cm;
            min-height: 29.7cm;
            margin: 0 auto;
            padding: 0cm;
            box-sizing: border-box;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 0px 0;
            page-break-inside: avoid;
        }
        th, td {
            border: 1px solid black;
            padding: 5px;
            vertical-align: top;
            
        }
        .header {
            text-align: center;
            margin-bottom: 15px;
        }
        .two-column {
        display: flex;
        justify-content: space-between; /* Spasi antara dua kolom */
        gap: 20px; /* Jarak antar kolom */
    }
    .two-column div {
        width: 48%; /* Lebar masing-masing kolom */
    }
        .signature-section td {
            height: 50px;
        }
        
    </style>
</head>
<body>
    <div class="a4-container">

        <table width="100%" >
    <tr>
        <td width="15%" style="border: none; !important;">
            <img src="<?=base_url('assets/')?>cnf.png" alt="Company Logo" height="70">
        </td>
        <td width="15%" style="border: none; !important;">
        Desa Suwawal<br>RT 02 RW 01 <br> Mlonggo, Jepara<br>Jawa Tengah 59452 <br> Indonesia

        </td>
        <td width="70%" align="left" style="border: none; !important;">
            <h2 align = "right">PROFORMA INVOICE</h2>
        </td>
    </tr>
</table>

        <!-- Informasi Header -->
        <table >
            <tr>
                <td width="70%">PI NUMBER: <?=$pi['invoice_number']?></td>
                <td>PI DATE: <?=formatDate($pi['invoice_date'])?></td>
            </tr>
            <tr>
                <td>CUSTOMER: <?=$pi['customer_name']?></td>
                <td>PO: <?=$pi['cus_po']?></td>
            </tr>
        </table>

        <!-- Informasi Customer -->
        <table width="100%">
    <tr>
        <td width="25%">
            <strong>Customer Detail:</strong><br>
            <?=$pi['address']?><br>
            Contact: <?=$pi['contact_name']?><br>
            Phone: <?=$pi['contact_phone']?><br>
            Email: <?=$pi['contact_email']?><br>
            <?=$pi['customer_address']?><br>
        </td>
        <td width="75%">
        <table border="1" cellspacing="0" cellpadding="5" width="100%">
    <tr>
        <td colspan="2"><strong>Ship To:</strong></td>
        <td colspan="2"><strong>Shipment Details:</strong></td>
    </tr>
    <tr>
        <td>PORT OF LOADING</td>
        <td><?=$pi['port_loading']?></td>
        <td>VESSEL NAME</td>
        <td><?=$pi['vessel']?></td>
    </tr>
    <tr>
        <td>PORT OF DISCHARGE</td>
        <td><?=$pi['port_discharge']?></td>
        <td>ETD</td>
        <td><?=formatDate($pi['etd'])?></td>
    </tr>
    <tr>
        <td>END OF PRODUCTION</td>
        <td><?=formatDate($pi['end_prod'])?></td>
        <td>ETA</td>
        <td><?=formatDate($pi['eta'])?></td>
    </tr>
    <tr>
        <td>LOADING DATE</td>
        <td><?=formatDate($pi['loading_date'])?></td>
        <td></td>
        <td></td>
    </tr>
</table>

</td>
    </tr>
</table>


        <!-- Tabel Produk -->
<!-- Tabel Produk -->
<table>
    <thead>
        <tr>
            <th>Code</th>
            <th>Product Name</th>
            <th>Picture</th>
            <th>Description</th>
            <th>Finishing</th>
            <th>Finishing Picture</th>
            <th>HS Code</th>
            <th>Qty</th>
            <th>Size (cm)</th>
            <th>CBM</th>
            <th>Total CBM</th> <!-- New column -->
            <th>Price/Unit</th>
            <th>Disc %</th>
            <th>Final Price</th>
            <th style="width: 50px">Total Price</th>
        </tr>
    </thead>
    <tbody>
        <?php 
        $tot_qty = 0;
        $tot_price = 0;
        $tot_cbm = 0;
        $tot_qty_cbm = 0; // New total variable
        foreach ($piDet as $item) {
            $finalPrice = hargaDisc($item['unit_price'], $item['disc']);
            $qty_cbm = $item['quantity'] * $item['p_cbm']; // Calculate Qty x CBM

            $tot_qty += $item['quantity'];
            $tot_price += $item['quantity'] * $finalPrice;
            $tot_cbm += $item['p_cbm'];
            $tot_qty_cbm += $qty_cbm; // Add to total
        ?>
        <tr>
            <td><?=$item['p_code']?></td>
            <td><?=$item['p_name']?></td>
            <td><img src="<?=base_url('assets/upload/thumb/').$item['p_picture']?>" height="40"></img></td>
            <td><?=$item['remarks']?></td>
            <td><?=$item['f_name']?></td>
            <td><img src="<?=base_url('uploads/finishing/').$item['f_picture']?>" height="40"></img></td>
            <td><?=$item['p_hs_code']?></td>
            <td><?=formatCurrency($item['quantity'])?></td>
            <td><?=convertcm($item['p_length'])."x".convertcm($item['p_width'])."x".convertcm($item['p_height'])?></td>
            <td><?=number_format($item['p_cbm'], 2)?></td> <!-- Changed to 2 decimal places -->
            <td><?=number_format($qty_cbm, 2)?></td> <!-- Changed to 2 decimal places -->
            <td style="white-space: nowrap;"><?=$item['currency_code']." ".formatCurrency($item['unit_price'])?></td>
            <td><?=$item['disc']?></td>
            <td style="white-space: nowrap;"><?=$item['currency_code']." ".formatCurrency($finalPrice)?></td>
            <td style="white-space: nowrap;"><?=$item['currency_code']." ".formatCurrency($finalPrice*$item['quantity'])?></td>
        </tr>
        <?php } ?>
    </tbody>
    <tfoot>
        <tr>
            <td colspan="7">TOTAL</td>
            <td><?=$tot_qty?></td>
            <td></td>
            <td><?=number_format($tot_cbm, 2)?></td> <!-- Changed to 2 decimal places -->
            <td><?=number_format($tot_qty_cbm, 2)?></td> <!-- Changed to 2 decimal places -->
            <td colspan="3"></td>
            <td style="white-space: nowrap;"><?=$item['currency_code']." ".formatCurrency($tot_price)?></td>
        </tr>
        <tr>
            <td colspan="13" style="border: none; !important;"></td>
            <td>CHARGE</td>
            <td style="white-space: nowrap;"><?=$item['currency_code']." ".formatCurrency($pi['charge'])?></td>
        </tr>
        <tr>
            <td colspan="13" style="border: none; !important;"></td>
            <td>DEPOSIT</td>
            <td style="white-space: nowrap;"><?=$item['currency_code']." ".formatCurrency($pi['deposit'])?></td>
        </tr>
        <tr>
            <td colspan="13" style="border: none; !important;"></td>
            <td>GRAND TOTAL</td>
            <td><?=$item['currency_code']." ".formatCurrency($tot_price+$pi['charge']-$pi['deposit'])?></td>
        </tr>
    </tfoot>
</table>

        <!-- Bank Details -->
        <table>
            <tr><th colspan="2">BANK DETAILS</th></tr>
            <tr>
                <td width="30%">BENEFICIARY'S NAME</td>
                <td>PT. CHAKRA NAGA FURNITURE</td>
            </tr>
            <tr>
                <td>BENEFICIARY'S ADDRESS</td>
                <td>JL.SUNAN MANTINGAN NO.19-21 DEMAAN JEPARA 59419</td>
            </tr>
            <tr>
                <td>BANK NAME</td>
                <td>BANK RAKYAT INDONESIA (PERSERO) JEPARA BRANCH</td>
            </tr>
            <tr>
                <td>BANK ADDRESS </td>
                <td>JL. PEMUDA NO.101 JEPARA <br> CENTRAL JAVA</td>
            </tr>
            <tr>
                <td>COUNTRY</td>
                <td>INDONESIA</td>
            </tr>
            <tr>
                <td>ACCOUNT NUMBER</td>
                <td>0022.02.000013.30.0</td>
            </tr>
            <tr>
                <td>SWIFT Code</td>
                <td>BRINIDJAXXX</td>
            </tr>
        </table>

        <!-- Confirm Order -->
        <table class="signature-section">
            <tr><th colspan="2">CONFIRM ORDER</th></tr>
            <tr>
                <td width="50%">Factory Stamp & Sign:<br><br><br><br></td>
                <td width="50%">Customer Stamp & Sign:<br><br><br><br></td>
            </tr>
        </table>

        <!-- Catatan Kaki -->
        <div style="margin-top: 10px;">
            <p><strong>ATTENTION:</strong></p>
            <ol>
                <li>Our patina will change with the time, depending of the light exposure or stay inside the packaging</li>
                <li>All colouring and patina are made by hands. Color confirmation with swatch required for re-orders</li>
                <li>We cannot guarantee exact color matching for re-orders</li>
            </ol>
        </div>
    </div>
</body>
</html>