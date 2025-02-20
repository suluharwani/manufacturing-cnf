
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
function formatDate($input) {
    // Jika input adalah angka (timestamp)
    if (is_numeric(value: $input)) {
        $date = new DateTime();
        $date->setTimestamp($input);
    } else {
        // Jika input adalah string dalam format datetime
        $date = DateTime::createFromFormat('Y-m-d H:i:s', $input);
        if (!$date) {
            return "-";
        }
    }

    // Format ke dalam bahasa Inggris: "Month Day, Year H:i:s"
    return $date->format('F j, Y H:i:s');
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

<?php
$item = json_decode($prDet, true);
// var_dump($item[0]);
// die();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Proforma Invoice</title>
    <style>
        @page {
            size: A4 portrait; 
            margin: 1cm;
        }
        body {
            font-family: Arial, sans-serif;
            font-size: 9pt;
            margin: 0;
        }
        .a4-container {
            width: 18cm;
            min-height: 29.7cm;
            margin: 0 auto;
            padding: 1cm;
            box-sizing: border-box;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 5px 0;
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
            <h2>PURCHASE REQUEST</h2>
        </td>
    </tr>
</table>

        <!-- Informasi Header -->
        <table >
            <tr>
                <td width="50%">PI NUMBER: <?=$pr['pi']?></td>
                <td>PR DATE: <?=formatDate($pr['created_at'])?></td>
            </tr>
            <tr>
                <td>PR: <?=$pr['kode']?></td>
                <td>DEPARTMENT: <?=$pr['department']?></td>
            </tr>
        </table>

        <!-- Informasi Customer -->
        


        <!-- Tabel Produk -->
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Code</th>
                    <th>HS Code</th>
                    <th>Material</th>
                    <th>Kite</th>
                    <th>Department</th>
                    <th>Quantity</th>
                    <th>UOM</th>
                    <th>Remark</th>
                </tr>
            </thead>
            <tbody>
                <!-- Baris produk (diulang untuk setiap item) -->
                 <?php 
                 $no = 1;
                 $tot_qty = 0;
                 $tot_price = 0;
                 $tot_cbm = 0;
                 foreach ($item as $data) {
                    $tot_qty += $data['quantity'];

                    ?>
                <tr>

                    <td><?=$no++?></td>
                    <td><?=$data['code']?></td>
                    <td><?=$data['hs_code']?></td>
                    <td><?=$data['material']?></td>
                    <td><?=$data['kite']?></td>
                    <td><?=$data['dep']?></td>
                    <td><?=$data['quantity']?></td>
                    <td><?=$data['satuan']?></td>
                    <td><?=$data['remarks']?></td>

                </tr>
                <?php } ?>
                <!-- Tambahkan baris lain sesuai kebutuhan -->
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="9">Note: <?=$pr['remarks']?></td>
                    


                </tr>


            </tfoot>
        </table>

        <!-- Bank Details -->
        <!-- Confirm Order -->
        <table class="signature-section">
            <tr><th colspan="4">CONFIRM PURCHASE REQUEST</th></tr>
            <tr>
                <td width="25%">Pemohon:<br><br><br><br>___________</td>
                <td width="25%">SPV:<br><br><br><br>___________</td>
                <td width="25%">Disetujui:<br><br><br><br>___________</td>
                <td width="25%">Dikeluarkan:<br><br><br><br>___________</td>
            </tr>
        </table>

        <!-- Catatan Kaki -->
        <div style="margin-top: 10px;">
            <!-- <p><strong>ATTENTION:</strong></p>
            <ol>
                <li>Our patina will change with the time, depending of the light exposure or stay inside the packaging</li>
                <li>All colouring and patina are made by hands. Color confirmation with swatch required for re-orders</li>
                <li>We cannot guarantee exact color matching for re-orders</li>
            </ol> -->
        </div>
    </div>
</body>
</html>