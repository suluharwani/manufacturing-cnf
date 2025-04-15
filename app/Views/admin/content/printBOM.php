<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Invoice PDF</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 8px;
            text-align: left;
        }
        .group-header {
            background-color: #f2f2f2;
            font-weight: bold;
        }
    </style>
</head>
<body>


    <!-- Query 1: Product -->
    <table>
        <thead>
            <tr>
                <th>Kode</th>
                <th>HS Code</th>
                <th>Produk</th>
                <th>Finishing</th>
                <th>Tanggal Cetak</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($query1 as $row): ?>
                <tr>
                    <td><?= $row['kode'] ?></td>
                    <td><?= $row['hs_code'] ?></td>
                    <td><?= $row['nama'] ?></td>
                    <td><?= $row['finishing'] ?></td>
                    <td><?= date("F j, Y") ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>

    </table>

    <!-- Query 2: Material dan Penggunaan (Bill of Material Finishing) -->
    <h2>Material dan Penggunaan (Bill of Material Finishing)</h2>
    <?php
    $groupedFinishing = [];
    foreach ($query2 as $row) {
        $finishing_id = $row['finishing_id'];
        if (!isset($groupedFinishing[$finishing_id])) {
            $groupedFinishing[$finishing_id] = [
                'finishing_name' => $row['finishing_name'],
                'materials' => [],
            ];
        }
        $groupedFinishing[$finishing_id]['materials'][] = $row;
    }
    ?>
    <?php foreach ($groupedFinishing as $finishing): ?>
        <h3>Finishing: <?= $finishing['finishing_name'] ?></h3>
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Material</th>
                    <th>Kode</th>
                    <th>KITE</th>
                    <th>Penggunaan</th>

                </tr>
            </thead>
            <tbody>
                <?php
                $noFinishing = 1;
                foreach ($finishing['materials'] as $row):
                    $penggunaan = rtrim(rtrim(number_format(floatval($row['penggunaan']), 4), '0'), '.');
                    $total_penggunaan = rtrim(rtrim(number_format(floatval($row['total_penggunaan']), 4), '0'), '.');
                ?>
                    <tr>
                        <td><?= $noFinishing++ ?></td>
                        <td><?= $row['material_name'] ?></td>
                        <td><?= $row['material_code'] ?></td>
                        <td><?= $row['kite'] ?></td>
                        <td><?= $penggunaan . ' ' . $row['satuan'] ?></td>

                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endforeach; ?>

    <!-- Query 3: Material dan Penggunaan (Bill of Material) -->
    <h2>Material dan Penggunaan (Bill of Material)</h2>
    <?php
    $groupedModul = [];
    foreach ($query3 as $row) {
        $modul_id = $row['modul_id'];
        if (!isset($groupedModul[$modul_id])) {
            $groupedModul[$modul_id] = [
                'modul_name' => $row['modul_name'],
                'modul_code' => $row['modul_code'],
                'materials' => [],
            ];
        }
        $groupedModul[$modul_id]['materials'][] = $row;
    }
    ?>
    <?php foreach ($groupedModul as $modul): ?>
        <h3>Modul: <?= $modul['modul_name'] ?> </h3>
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Material</th>
                    <th>Kode</th>
                    <th>KITE</th>
                    <th>Penggunaan</th>
    
                </tr>
            </thead>
            <tbody>
                <?php
                $noModul = 1;
                foreach ($modul['materials'] as $row):
                    $penggunaan = rtrim(rtrim(number_format(floatval($row['penggunaan']), 4), '0'), '.');
                    $total_penggunaan = rtrim(rtrim(number_format(floatval($row['total_penggunaan']), 4), '0'), '.');
                ?>
                    <tr>
                        <td><?= $noModul++ ?></td>
                        <td><?= $row['material_name'] ?></td>
                        <td><?= $row['material_code'] ?></td>
                        <td><?= $row['kite'] ?></td>
                        <td><?= $penggunaan . ' ' . $row['satuan'] ?></td>

                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endforeach; ?>
</body>
</html>