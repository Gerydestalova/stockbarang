<?php
require 'function.php';
require 'vendor/autoload.php';

use Mpdf\Mpdf;

if (isset($_GET['dari'], $_GET['sampai'], $_GET['type'])) {
    $dari = $_GET['dari'];
    $sampai = $_GET['sampai'];
    $type = $_GET['type'];

    // Hitung stok awal dan akhir
    $stok_awal = (int)mysqli_fetch_array(mysqli_query($conn, "SELECT SUM(qty) as total FROM masuk WHERE tanggal < '$dari 00:00:00'"))['total']
               - (int)mysqli_fetch_array(mysqli_query($conn, "SELECT SUM(qty) as total FROM keluar WHERE tanggal < '$dari 00:00:00'"))['total'];

    $stok_akhir = (int)mysqli_fetch_array(mysqli_query($conn, "SELECT SUM(qty) as total FROM masuk WHERE tanggal <= '$sampai 23:59:59'"))['total']
                - (int)mysqli_fetch_array(mysqli_query($conn, "SELECT SUM(qty) as total FROM keluar WHERE tanggal <= '$sampai 23:59:59'"))['total'];

    ob_start(); // Mulai tangkap HTML output
?>
<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { border-collapse: collapse; width: 100%; margin-bottom: 20px; }
        th, td { border: 1px solid black; padding: 5px; text-align: left; }
        h3, h4, p { margin: 0 0 10px 0; }
    </style>
</head>
<body>
    <h3 align="center">Laporan Barang Masuk & Keluar</h3>
    <p><strong>Periode:</strong> <?= $dari ?> s.d <?= $sampai ?></p>
    <p><strong>Stok Awal:</strong> <?= $stok_awal ?> &nbsp;&nbsp;&nbsp; <strong>Stok Akhir:</strong> <?= $stok_akhir ?></p>

    <h4>Barang Masuk</h4>
    <table>
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Nama Barang</th>
                <th>Qty</th>
                <th>Keterangan</th>
                <th>Supplier</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $getMasuk = mysqli_query($conn, "SELECT * FROM masuk WHERE tanggal BETWEEN '$dari 00:00:00' AND '$sampai 23:59:59'");
            while ($data = mysqli_fetch_array($getMasuk)) {
                echo "<tr>
                        <td>{$data['tanggal']}</td>
                        <td>{$data['namabarang']}</td>
                        <td>{$data['qty']}</td>
                        <td>{$data['keterangan']}</td>
                        <td>{$data['namasupplier']}</td>
                    </tr>";
            }
            ?>
        </tbody>
    </table>

    <h4>Barang Keluar</h4>
    <table>
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Nama Barang</th>
                <th>Qty</th>
                <th>Penerima</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $getKeluar = mysqli_query($conn, "SELECT * FROM keluar WHERE tanggal BETWEEN '$dari 00:00:00' AND '$sampai 23:59:59'");
            while ($data = mysqli_fetch_array($getKeluar)) {
                echo "<tr>
                        <td>{$data['tanggal']}</td>
                        <td>{$data['namabarang']}</td>
                        <td>{$data['qty']}</td>
                        <td>{$data['penerima']}</td>
                    </tr>";
            }
            ?>
        </tbody>
    </table>
</body>
</html>
<?php
    $html = ob_get_clean(); // Tangkap seluruh HTML ke variabel

    if ($type === 'pdf') {
        $mpdf = new Mpdf(['format' => 'A4']);
        $mpdf->WriteHTML($html);
        $mpdf->Output("Laporan_Gabungan_{$dari}_sd_{$sampai}.pdf", "D");
    } elseif ($type === 'excel') {
        header("Content-type: application/vnd-ms-excel");
        header("Content-Disposition: attachment; filename=Laporan_Gabungan_{$dari}_sd_{$sampai}.xls");
        echo $html;
    } else {
        echo "Tipe file tidak dikenali. Gunakan ?type=pdf atau ?type=excel.";
    }
} else {
    echo "Parameter tidak lengkap. Gunakan ?dari=YYYY-MM-DD&sampai=YYYY-MM-DD&type=pdf|excel";
}
?>
