
<?php
// var_dump($product[0]);
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


function hargaDisc($pOice, $discount) {
    // Validasi input harus numerik

    // Jika discount 0, kembalikan harga asli
    if ($discount == 0) {
        return $pOice;
    }

    // Hitung harga setelah diskon
    $discountedPrice = $pOice - ($pOice * ($discount / 100));

    return $discountedPrice;
}
function convertcm($mm) {
    if (empty($mm) && $mm !== 0 && $mm !== '0') {
        return "-";
    }
    return $mm / 10;
}
function hargapajak($harga, $pajak) {
    // Jika pajak null atau 0, kembalikan harga asli
    if (is_null($pajak) || $pajak == 0) {
        return $harga;
    }
    
    // Hitung harga dengan pajak
    return $harga + ($harga * ($pajak / 100));
}
?>

<?php
// var_dump($po);
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
            margin: 0cm;
        }
        body {
            font-family: Arial, sans-serif;
            font-size: 8pt;
            margin: 0;
        }
        .a4-container {
            width: 20cm;
            min-height: 29.7cm;
            margin: 0 auto;
            padding: 0cm;
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
        <td width="50%" style="border: none; !important;">
        PT CHAKRA NAGA FURNITURE<br>
Desa Suwawal RT 002 RW 001, Mlonggo, Jepara 59452 Central Java - Indonesia<br>
Phone : +62 291 4292 915 | email: adm.ppic@chakranaga.com<br>
http://www.chakranaga.com<br>

        </td>
        <td width="70%" align="right" style="border: none; !important;">
            <h2>Finished Good</h2>
        </td>
    </tr>
</table>

        <!-- Informasi Header -->
        <table >
            <tr>
                <!-- <td width="50%">Location :<?=$role?> </td> -->
                <td><?=formatDateJam($startDate)?> - <?=formatDateJam($endDate)?></td>
            </tr>


        </table>

        <!-- Informasi Customer -->
        


        <!-- Tabel Produk -->
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Created</th>
                    <th>Customer</th>
                    <th>PEB</th>
                    <th>PEB Date</th>
                    <th>Status</th>
                    <th>Code</th>
                    <th>Name</th>
                    <th>HS Code</th>
                    <th>Production</th>
                    <th>Warehouse</th>
                    <th>PI</th>
                    <th>WO</th>
                    <th>Qty</th>
                </tr>
            </thead>
            <tbody>
                <!-- Baris produk (diulang untuk setiap item) -->
                 <?php 
                 $no = 1;
                 $total = 0;
                 foreach ($product as $data) {
                    $total += $data['quantity'];
                    if ($data['status_delivery'] == 0) {
                        $status = 'On Progress';
                    }else if ($data['status_delivery'] == 1) {
                        $status = 'Waiting Delivery';
                    }else if ($data['status_delivery'] == 2) {
                        $status = 'Delivered';
                    }
                    ?>
                <tr>
                <!-- customer.customer_name as customer_name,
        customer.state as state,
        currency.kode as currency_code,
        currency.nama as currency_name, -->
                    <td><?=$no++?></td>
                    <td><?=formatDateJam($data['created_at'])?></td>
                    <td><?=$data['customer_name']?> - <?=$data['state']?> -  <?=$data['currency_name']?>(<?=$data['currency_code']?>)</td>
                    <td><?=$data['peb']?></td>
                    <td><?=formatDate($data['tgl_peb'])?></td>
                    <td><?=$status?></td>
                    <td><?=$data['product_code']?></td>
                    <td><?=$data['product_name']?></td>
                    <td><?=$data['hs_code']?></td>
                    <td><?=$data['production_area_name']?></td>
                    <td><?=$data['warehouse_name']?></td>
                    <td><?=$data['pi_number']?></td>
                    <td><?=$data['wo_code']?></td>
                    <td><?=$data['quantity']?></td>
                    

                </tr>
                <?php } ?>
                <!-- Tambahkan baris lain sesuai kebutuhan -->
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="13" align="center">Total</td>
                    <td><?=$total?></td>

                    


                </tr>


            </tfoot>
        </table>

        <!-- Bank Details -->
        <!-- Confirm Order -->


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