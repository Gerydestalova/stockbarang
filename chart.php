<?php
require 'function.php'; // koneksi ke database
require 'cek.php'; // session login

// Query jumlah data
$jumlah_masuk = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM masuk"));
$jumlah_keluar = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM keluar"));
$jumlah_stock = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM stock"));
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <title>Charts - SB Admin</title>
    <link href="css/styles.css" rel="stylesheet" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/js/all.min.js" crossorigin="anonymous"></script>
</head>
<body class="sb-nav-fixed">

<!-- Navbar -->
<nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">
    <a class="navbar-brand" href="index.php">Matahari Motor Group</a>
    <button class="btn btn-link btn-sm order-1 order-lg-0" id="sidebarToggle"><i class="fas fa-bars"></i></button>
</nav>

<div id="layoutSidenav">
    <!-- Sidebar -->
    <div id="layoutSidenav_nav">
        <nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
            <div class="sb-sidenav-menu">
                <div class="nav">
                    <a class="nav-link" href="index.php"><div class="sb-nav-link-icon"><i class="fas fa-box"></i></div>Stock Barang</a>
                    <a class="nav-link" href="masuk.php"><div class="sb-nav-link-icon"><i class="fas fa-download"></i></div>Barang Masuk</a>
                    <a class="nav-link" href="keluar.php"><div class="sb-nav-link-icon"><i class="fas fa-upload"></i></div>Barang Keluar</a>
                    <a class="nav-link active" href="charts.php"><div class="sb-nav-link-icon"><i class="fas fa-chart-bar"></i></div>Grafik</a>
                    <a class="nav-link" href="#" data-toggle="modal" data-target="#logoutModal">Logout</a>
                </div>
            </div>
        </nav>
    </div>

    <!-- Content -->
    <div id="layoutSidenav_content">
        <main>
            <div class="container-fluid">
                <h1 class="mt-4">Grafik Barang</h1>
                <ol class="breadcrumb mb-4">
                    <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                    <li class="breadcrumb-item active">Charts</li>
                </ol>

                <div class="card mb-4">
                    <div class="card-header">
                        <i class="fas fa-chart-bar mr-1"></i>Bar Chart: Barang Masuk, Keluar & Stok
                    </div>
                    <div class="card-body">
                        <canvas id="myBarChart" width="100%" height="40"></canvas>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<!-- Logout Modal -->
<div class="modal fade" id="logoutModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Yakin Logout?</h5>
                <button class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body">Pilih Logout jika kamu ingin keluar dari sesi ini.</div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <a class="btn btn-danger" href="logout.php">Logout</a>
            </div>
        </div>
    </div>
</div>

<!-- Script -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@2.8.0"></script>

<script>
// Ambil data dari PHP
var jumlahMasuk = <?= $jumlah_masuk ?>;
var jumlahKeluar = <?= $jumlah_keluar ?>;
var jumlahStock = <?= $jumlah_stock ?>;

var ctx = document.getElementById("myBarChart");
var myBarChart = new Chart(ctx, {
    type: 'bar',
    data: {
        labels: ["Barang Masuk", "Barang Keluar", "Stok"],
        datasets: [{
            label: "Jumlah",
            backgroundColor: ["#4e73df", "#e74a3b", "#1cc88a"],
            hoverBackgroundColor: ["#2e59d9", "#be2617", "#17a673"],
            borderColor: "#4e73df",
            data: [jumlahMasuk, jumlahKeluar, jumlahStock]
        }]
    },
    options: {
        scales: {
            xAxes: [{
                gridLines: { display: false },
                ticks: { maxTicksLimit: 6 }
            }],
            yAxes: [{
                ticks: {
                    beginAtZero: true,
                    min: 0
                },
                gridLines: { color: "rgb(234, 236, 244)" }
            }]
        },
        legend: { display: false }
    }
});
</script>
</body>
</html>
