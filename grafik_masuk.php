<?php
require 'function.php';

$data = [];
$labels = [];

$query = mysqli_query($conn, "
    SELECT s.namabarang, SUM(m.qty) as total 
    FROM masuk m
    JOIN stock s ON m.idbarang = s.idbarang 
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
