<?php
require 'function.php';

$nama_barang = [];
$stok_barang = [];

// Ambil data stok barang
$query = mysqli_query($conn, "SELECT namabarang, stock FROM stock");

while ($row = mysqli_fetch_assoc($query)) {
    $nama_barang[] = $row['namabarang'];
    $stok_barang[] = (int)$row['stock'];
}

echo json_encode([
    'labels' => $nama_barang,
    'data' => $stok_barang
]);
?>
