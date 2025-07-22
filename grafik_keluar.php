<?php
require 'function.php';

$data = [];
$labels = [];

$query = mysqli_query($conn, "
    SELECT s.namabarang, SUM(k.qty) as total 
    FROM keluar k
    JOIN stock s ON k.idbarang = s.idbarang 
    GROUP BY s.namabarang
");

if (!$query) {
    echo json_encode(['error' => mysqli_error($conn)]);
    exit;
}

while ($row = mysqli_fetch_assoc($query)) {
    $labels[] = $row['namabarang'];
    $data[] = (int)$row['total'];
}

echo json_encode([
    'labels' => $labels,
    'data' => $data
]);
?>
