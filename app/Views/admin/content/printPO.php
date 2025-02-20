
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
        <td width="50%" style="border: none; !important;">
        PT CHAKRA NAGA FURNITURE<br>
Desa Suwawal RT 002 RW 001, Mlonggo, Jepara 59452 Central Java - Indonesia<br>
Phone : +62 291 4292 915 | email: adm.ppic@chakranaga.com<br>
http://www.chakranaga.com<br>

        </td>
        <td width="70%" align="right" style="border: none; !important;">
            <h2>PURCHASE ORDER</h2>
        </td>
    </tr>
</table>

        <!-- Informasi Header -->
        <table >
            <tr>
                <td width="50%">P0 NUMBER: <?=$po['po']?></td>
                <td>PO DATE: <?=formatDate($po['date'])?></td>
            </tr>
            <tr>
                <td colspan="2">Invoice must reference this purchase order and 
                match PO line items in order to process payment</td>
            </tr>
            <tr>
                <td>
                <table >
        <tr>
            <th align="left">To</th>
            <td align="left"><?=$po['supplier_name']?></td>
        </tr>
        <tr>
            <th align="left">ADDRESS</th>
            <td align="left"><?=$po['address']?></td>
        </tr>
        <tr>
            <th align="left">PHONE</th>
            <td align="left"><?=$po['contact_phone']?></td>
        </tr>
        <tr>
            <th align="left">EMAIL</th>
            <td align="left"><?=$po['contact_email']?></td>
        </tr>
        <tr>
            <th align="left">CURRENCY</th>
            <td align="left"><?=$po['curr_name']?></td>
        </tr>
      </table>

                </td>
                <td>
                <table >
        <tr>
            <th align="left">FROM</th>
            <td align="left">PT CHAKRA NAGA FURNITURE</td>
        </tr>
        <tr>
            <th align="left">ADDRESS</th>
            <td align="left">Desa Suwawal 02/01, Kec. Mlonggo, Kab. Jepara 59452</td>
        </tr>
        <tr>
            <th align="left">TOP</th>
            <td align="left"></td>
        </tr>
        <tr>
            <th align="left">ARRIVAL TARGET</th>
            <td align="left"><?=formatDate($po['arrival_target'])?></td>
        </tr>

      </table>


                </td>
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
                    <!-- <th>Kite</th> -->
                    <th>Qty</th>
                    <th>UOM</th>
                    <th>Price</th>
                    <th>VAT %</th>
                    <th>Total</th>
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
                 $grand_total = 0;
                 foreach ($poDet as $data) {
                    $tot_qty += $data['quantity'];
                    $tot_price =hargapajak($data['price'], $data['vat'])*$data['quantity'];
                    $grand_total += $tot_price;
                    ?>
                <tr>

                    <td><?=$no++?></td>
                    <td><?=$data['kode']?></td>
                    <td><?=$data['hscode']?></td>
                    <td><?=$data['name']?></td>
                    <td><?=$data['quantity']?></td>
                    <td><?=$data['satuan']?></td>
                    <td><?=$data['price']." ".$data['curr_code']?></td>
                    <td><?=$data['vat']?></td>
                    <td><?=$tot_price." ".$data['curr_code']?></td>
                    
                    <td><?=$data['remarks']?></td>

                </tr>
                <?php } ?>
                <!-- Tambahkan baris lain sesuai kebutuhan -->
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="8">GRAND TOTAL</td>
                    <td><?=$grand_total." ".$data['curr_code']?></td>
                    <td></td>
                    


                </tr>


            </tfoot>
        </table>

        <!-- Bank Details -->
        <!-- Confirm Order -->
        <table class="signature-section">
            <tr><th colspan="2">CONFIRM PURCHASE REQUEST</th></tr>
            <tr>
                <td width="50%">Prepared By,<br><br><br><br>___________<br>Purchasing Dept</td>
                <td width="50%">Approved By,<br><br><br><br>___________<br>General Manager</td>
            </tr>
            <tr>
                <td width="50%">Acknowledge By,<br><br><br><br>___________<br>Accounting Manager</td>
                <td width="50%">Approved By,<br><br><br><br>___________<br>Director/Commissioner</td>
            </tr>
            <tr><th colspan="2">SPECIAL INSTRUCTION</th></tr>
            <tr><th colspan="2"><?=$po['remarks']?></th></tr>
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