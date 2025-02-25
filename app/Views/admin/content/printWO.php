
<?php
// var_dump($invoice);
// die();
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
function formatDateJam($input) {
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
function getWeekNumber($date) {
    // Mengubah tanggal menjadi format DateTime
    $dateTime = new DateTime($date);
    
    // Mengembalikan minggu ke berapa dalam tahun tersebut
    return $dateTime->format("W");
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
            width: 21cm;
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
        <td width="40%" align="right" style="border: none; !important;">
            <h2>WORK ORDER</h2>
        </td>
    </tr>
</table>

        <!-- Informasi Header -->
       
        <table >
        <tr>
                <td width="40%">PI NUMBER: <?=$invoice['invoice_number']?></td>
                <td><table>
                    <tr>
                        <td>RELEASE DATE </td>
                        <td><?=formatDate($invoice['release_date'])?></td>
                    
                    </tr>
                    <tr>
                        <td>MANUFACTURE FINISHES </td>
                        <td><?=formatDate($invoice['manufacture_finishes'])?></td>
                    
                    </tr>
                    <tr style="background-color:yellow">
                        <td>LOADING DATE </td>
                        <td><?=formatDate($invoice['loading_date'])?></td>
                    
                    </tr>
                    <tr>
                        <td>WEEK </td>
                        <td><?=getWeekNumber($invoice['loading_date'])?></td>
                    
                    </tr>
                    
                </table></td>
            </tr>
            <tr>
                <td width="40%">WO NUMBER: <?=$invoice['kode']?></td>
                <td>WO DATE: <?=formatDateJam($invoice['created_at'])?></td>
                
            </tr>
            <tr>
                <td>CUSTOMER: <?=$invoice['customer_name']?></td>
                <td>CUSTOMER PO: <?=$invoice['cus_po']?></td>
            </tr>
            
        </table>

        <!-- Tabel Produk -->
        <table>
            <thead>
                <tr>
                    <th>Code</th>
                    <th>Product Name</th>
                    <th>Picture</th>
                    <th>Description</th>
                    <th>Finishing Picture</th>
                    <th>Qty</th>
                    <th>Size (cm)</th>
                    <th>Unit Volume</th>
                    <th>CBM Packaging</th>
                    <th>Total CBM Packaging</th>
                </tr>
            </thead>
            <tbody>
                <!-- Baris produk (diulang untuk setiap item) -->
                 <?php 
                 $tot_qty = 0;
                 $tot_price = 0;
                 $tot_cbm = 0;
                 $tot_volume = 0;
                 foreach ($details as $item) {
                    $tot_qty += $item['quantity'];
                    $unit_volume = (convertcm($item['length'])*convertcm($item['width'])*convertcm($item['height']))/1000000;
                    $tot_volume += $unit_volume;
                    $tot_cbm += $item['cbm']*$item['quantity'];
                    ?>
                <tr>

                    <td><?=$item['kode']?></td>
                    <td><?=$item['nama']?></td>
                    <td><img src="<?=base_url('assets/upload/thumb/').$item['picture']?>" height="40"></img></td>
                    <td><?=$item['finishing_name']?></td>
                    <td><img src="<?=base_url('uploads/finishing/').$item['f_picture']?>" height="40"></img></td>
                    <td><?=formatCurrency($item['quantity'])?></td>
                    <td><?=convertcm($item['length'])."x".convertcm($item['width'])."x".convertcm($item['height'])?></td>
                    <td><?=$unit_volume?></td>
                    <td><?=$item['cbm']?></td>
                    <td><?=$item['cbm']*$item['quantity']?></td>

                </tr>
                <?php } ?>
                <!-- Tambahkan baris lain sesuai kebutuhan -->
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="5">TOTAL</td>
                    <td><?=$tot_qty?></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td><?=$tot_cbm?></td>

                </tr>

            </tfoot>
        </table>

        <!-- Bank Details -->


        <!-- Confirm Order -->
        <table class="signature-section">
            <tr><th colspan="3">CONFIRM ORDER</th></tr>
            <tr>
                <td width="30%">Created By:<br><br><br><br></td>
                <td width="35%">Checked:<br><br><br><br></td>
                <td width="35%">Approved By:<br><br><br><br></td>
            </tr>
        </table>

        <!-- Catatan Kaki -->

    </div>
</body>
</html>