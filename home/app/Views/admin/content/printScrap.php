
<?php
// var_dump($material);
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
    <title>Scrap</title>
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
            <h2>SCRAP</h2>
        </td>
    </tr>
</table>

        <!-- Informasi Header -->
       
        <table >
        <tr>
                <td width="40%">Department: <?=$doc['dept_name']?></td>
                <td>SCRAP NUMBER: <?=$doc['code']?></td>
                
            </tr>
        <tr>
                <td width="40%">PI NUMBER: <?=$doc['pi']?></td>
                <td width="40%"> Created By: <?= session()->get('auth')['name'] ?></td>
            </tr>
            
            <tr>
                <td width="40%">WO NUMBER: <?=$doc['wo_code']?></td>
                <td>DATE: <?=formatDateJam($doc['created_at'])?></td>
            </tr>
            
        </table>

        <!-- Tabel Produk -->
        <table>
            <thead>
                <tr>
                    <th>Code</th>
                    <th>Product Name</th>
                    <th>Quantity</th>
                    <th>UoM</th>
                    <th>Reason</th>
                </tr>
            </thead>
            <tbody>
                <!-- Baris produk (diulang untuk setiap item) -->
                 <?php 
              
                 foreach ($material as $item) {

                    ?>
                <tr>

                    <td><?=$item['kode']?></td>
                    <td><?=$item['name']?></td>
                    <td><?=$item['quantity']?></td>
                    <td><?=$item['satuan']?></td>
                    <td><?=$item['reason']?></td>


                </tr>
                <?php } ?>
                <!-- Tambahkan baris lain sesuai kebutuhan -->
            </tbody>
            <tfoot>
                <>
                <th colspan="5" >NOTE: <?=$doc['remarks']?> </th>
                </tr>

            </tfoot>
        </table>

        <!-- Bank Details -->


        <!-- Confirm Order -->
        <table class="signature-section">
            <tr><th colspan="3">CONFIRM DOCUMENT</th></tr>
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