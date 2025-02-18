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
            font-size: 10pt;
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
        <h2 class="header">PROFORMA INVOICE</h2>

        <!-- Informasi Header -->
        <table>
            <tr>
                <td width="70%">PI NUMBER: <?=$pi['invoice_number']?></td>
                <td>PI DATE: <?=$pi['invoice_date']?></td>
            </tr>
            <tr>
                <td>CUSTOMER: <?=$pi['customer_name']?></td>
                <td>CONTAINER #170</td>
            </tr>
        </table>

        <!-- Informasi Customer -->
        <table width="100%">
    <tr>
        <td width="50%">
            <strong>Bill To:</strong><br>
            <?=$pi['address']?><br>
            Contact: <?=$pi['contact_name']?><br>
            Phone: <?=$pi['contact_phone']?><br>
            Email: <?=$pi['contact_email']?>
        </td>
        <td width="50%">
            <strong>Ship To:</strong><br>
            PORT OF LOADING: SEMARANG, INDONESIA<br>
            PORT OF DISCHARGE: <?=$pi['city']?>, <?=$pi['state']?><br><br>
            VESSEL NAME: -<br>
            LOADING DATE: -
        </td>
    </tr>
</table>


        <!-- Tabel Produk -->
        <table>
            <thead>
                <tr>
                    <th>Code</th>
                    <th>Product Name</th>
                    <th>Description</th>
                    <th>Qty</th>
                    <th>Size (cm)</th>
                    <th>CBM</th>
                    <th>Price/Unit</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                <!-- Baris produk (diulang untuk setiap item) -->
                <tr>
                    <td>HEL10</td>
                    <td>DESK</td>
                    <td>FINISHING FRENCH STAIN</td>
                    <td>12</td>
                    <td>140x78x70</td>
                    <td>0.743</td>
                    <td>557</td>
                    <td>6,684</td>
                </tr>
                <!-- Tambahkan baris lain sesuai kebutuhan -->
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="3">TOTAL</td>
                    <td>211</td>
                    <td></td>
                    <td>12.345</td>
                    <td></td>
                    <td>45,678</td>
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
                <td width="50%">Send Date:<br><br></td>
                <td>Stamp & Sign:<br><br></td>
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