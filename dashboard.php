<?php
require 'function.php';
require 'cek.php';

// Barang Masuk per Barang
$barangMasukNama = [];
$barangMasukQty = [];
$masuk = mysqli_query($conn, "
    SELECT s.namabarang, SUM(m.qty) as total
    FROM masuk m
    JOIN stock s ON m.idbarang = s.idbarang
    GROUP BY s.namabarang
");
while ($row = mysqli_fetch_assoc($masuk)) {
    $barangMasukNama[] = $row['namabarang'];
    $barangMasukQty[] = (int)$row['total'];
}

// Barang Keluar per Barang
$barangKeluarNama = [];
$barangKeluarQty = [];
$keluar = mysqli_query($conn, "
    SELECT s.namabarang, SUM(k.qty) as total
    FROM keluar k
    JOIN stock s ON k.idbarang = s.idbarang
    GROUP BY s.namabarang
");
while ($row = mysqli_fetch_assoc($keluar)) {
    $barangKeluarNama[] = $row['namabarang'];
    $barangKeluarQty[] = (int)$row['total'];
}

// Stok Barang
$stokBarangNama = [];
$stokBarangQty = [];
$stok = mysqli_query($conn, "SELECT namabarang, stock FROM stock");
while ($row = mysqli_fetch_assoc($stok)) {
    $stokBarangNama[] = $row['namabarang'];
    $stokBarangQty[] = (int)$row['stock'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <title>Dashboard Admin - Matahari Motor Group</title>
    <link href="css/styles.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free/css/all.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body { background-color: #f8f9fa; color: #212529; }
        .card { border-radius: 15px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
        .card-header { font-weight: 600; }
        .btn { border-radius: 8px; transition: all 0.3s ease; }
        .btn:hover { transform: scale(1.05); }
        .dark-mode {
            background-color: #121212;
            color: #ffffff;
        }
        .dark-mode .card {
            background-color: #1e1e2f;
            color: #ffffff;
            box-shadow: 0 4px 15px rgba(255, 255, 255, 0.1);
        }
        .dark-mode .navbar, .dark-mode .sb-sidenav {
            background-color: #1f1f1f;
        }
        .dark-mode .progress-bar {
            background-color: #0d6efd !important;
        }
        .dark-mode .card-footer a {
            color: #ffffff;
        }
        #layoutSidenav {
            display: flex;
            height: 100vh;
            transition: all 0.3s ease;
        }
        #layoutSidenav_nav {
            width: 250px;
            transition: margin-left 0.3s ease;
        }
        #layoutSidenav_content {
            flex-grow: 1;
            transition: margin-left 0.3s ease;
        }
        body.sb-sidenav-toggled #layoutSidenav_nav {
            margin-left: -250px;
        }
        body.sb-sidenav-toggled #layoutSidenav_content {
            margin-left: 0;
        }
        #loadingOverlay {
            position: fixed;
            top: 0; left: 0;
            width: 100%; height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 9999;
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.4s ease, visibility 0.4s ease;
        }
        #loadingOverlay.active {
            opacity: 1;
            visibility: visible;
        }
    </style>
</head>
<body class="sb-nav-fixed">
<nav class="sb-topnav navbar navbar-expand navbar-dark bg-success">
    <a class="navbar-brand" href="dashboard.php">Matahari Motor Group</a>
    <button class="btn btn-link btn-sm order-1 order-lg-0" id="sidebarToggle"><i class="fas fa-bars"></i></button>
    <button onclick="toggleDarkMode()" class="btn btn-sm btn-light ml-auto">🌙 / ☀</button>
</nav>

<!-- Loader Overlay -->
<div id="loadingOverlay">
    <div class="spinner-border text-light" role="status" style="width: 4rem; height: 4rem;">
        <span class="sr-only">Loading...</span>
    </div>
</div>

<div id="layoutSidenav">
    <div id="layoutSidenav_nav">
        <nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
            <div class="sb-sidenav-menu">
                <div class="nav">
                    <a class="nav-link active" href="dashboard.php"><div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>Dashboard</a>
                    <a class="nav-link" href="index.php"><div class="sb-nav-link-icon"><i class="fas fa-box"></i></div>Stock Barang</a>
                    <a class="nav-link" href="masuk.php"><div class="sb-nav-link-icon"><i class="fas fa-download"></i></div>Restock Barang</a>
                    <a class="nav-link" href="keluar.php"><div class="sb-nav-link-icon"><i class="fas fa-upload"></i></div>Barang Keluar</a>
                    <a class="nav-link" href="supplier.php"><div class="sb-nav-link-icon"><i class="fas fa-truck"></i></div>Supplier</a>
                    <a class="nav-link" href="laporan.php"><div class="sb-nav-link-icon"><i class="fas fa-file-alt"></i></div>Laporan</a>
                    <a class="nav-link" href="logout.php"><div class="sb-nav-link-icon"><i class="fas fa-sign-out-alt"></i></div>Logout</a>
                </div>
            </div>
        </nav>
    </div>

    <div id="layoutSidenav_content">
        <main class="container-fluid px-4 mt-4">
            <h1 class="mb-4">Dashboard Admin</h1>

            <!-- Summary Cards -->
            <div class="row">
                <?php
                $cards = [
                    ['Stock Barang', 'fa-box', 'success', 'index.php', 'SELECT * FROM stock'],
                    ['Restock Barang', 'fa-download', 'warning', 'masuk.php', 'SELECT * FROM masuk'],
                    ['Barang Keluar', 'fa-upload', 'primary', 'keluar.php', 'SELECT * FROM keluar'],
                    ['Total Supplier', 'fa-truck', 'danger', 'supplier.php', 'SELECT * FROM supplier']
                ];
                foreach ($cards as $c) {
                    [$label, $icon, $color, $link, $query] = $c;
                    $count = mysqli_num_rows(mysqli_query($conn, $query));
                    echo "
                    <div class='col-md-3'>
                        <div class='card text-white bg-$color mb-4 shadow'>
                            <div class='card-body'>
                                <div class='d-flex justify-content-between'>
                                    <span>$label</span>
                                    <i class='fas $icon fa-2x'></i>
                                </div>
                                <h3 class='mt-2'>$count</h3>
                                <div class='progress mt-2'>
                                    <div class='progress-bar bg-light' style='width:70%'></div>
                                </div>
                            </div>
                            <div class='card-footer text-white'>
                                <a href='$link' class='text-white'>View Detail <i class='fas fa-arrow-right'></i></a>
                            </div>
                        </div>
                    </div>";
                }
                ?>
            </div>

            <!-- Charts -->
            <div class="row">
                <div class="col-md-4">
                    <div class="card mb-4 shadow">
                        <div class="card-header">📈 Restock Barang Masuk</div>
                        <div class="card-body"><canvas id="chartMasuk"></canvas></div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card mb-4 shadow">
                        <div class="card-header">📊 Barang Keluar</div>
                        <div class="card-body"><canvas id="chartKeluar"></canvas></div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card mb-4 shadow">
                        <div class="card-header">📦 Stok Barang</div>
                        <div class="card-body"><canvas id="chartStock"></canvas></div>
                    </div>
                </div>
            </div>
             <!-- Shortcut ke Laporan -->
            <div class="card mt-4 mb-5 shadow-lg border-0">
                <div class="card-header d-flex justify-content-between align-items-center 
                    bg-gradient-primary">
                    <h5 class="mb-0"><i class="fas fa-file-alt"></i> Laporan Gabungan Barang</h5>
                    <span class="badge bg-warning text-dark"></span>
                </div>
                <div class="card-body">
                    <p class="fs-6 mb-3">
                        📊 Lihat dan export laporan barang masuk & keluar secara lengkap dalam satu periode.
                    </p>
                    <a href="laporan.php" class="btn btn-success btn-lg rounded-pill">
                        <i class="fas fa-eye"></i> Lihat Laporan Per Periode
                    </a>
                </div>
            </div>

        </main>
         <footer class="footer mt-auto py-3 bg-gradient" style="background: linear-gradient(90deg, #20c997, #17a2b8); color: #fff;">
            <div class="container-fluid">
                <div class="d-flex flex-column flex-md-row align-items-center justify-content-between small">
                    <div class="mb-2 mb-md-0">
                        <strong>&copy; 2025 SUKSES MANTAP</strong> — All rights reserved
                    </div>
                    <div>
                        <a href="#" class="text-white text-decoration-none mx-2" style="opacity:0.9;">Privacy Policy</a>
                        <span class="text-white mx-1">|</span>
                        <a href="#" class="text-white text-decoration-none mx-2" style="opacity:0.9;">Terms & Conditions</a>
                    </div>
                </div>
            </div>
        </footer>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="js/scripts.js"></script>
<script>
function getTextColor() {
    return document.body.classList.contains('dark-mode') ? '#ffffff' : '#000000';
}
function updateChartColors(chart) {
    const textColor = getTextColor();
    chart.options.scales.x.ticks.color = textColor;
    chart.options.scales.y.ticks.color = textColor;
    chart.options.plugins.legend.labels.color = textColor;
    chart.options.plugins.tooltip.titleColor = textColor;
    chart.options.plugins.tooltip.bodyColor = textColor;
    chart.update('none');
}
const chartMasuk = new Chart(document.getElementById('chartMasuk'), {
    type: 'bar',
    data: {
        labels: <?= json_encode($barangMasukNama); ?>,
        datasets: [{
            label: 'Barang Masuk',
            data: <?= json_encode($barangMasukQty); ?>,
            backgroundColor: 'rgba(40, 167, 69, 0.7)',
            borderColor: 'rgba(40, 167, 69, 1)',
            borderWidth: 1
        }]
    },
    options: {
        plugins: {
            legend: { labels: { color: getTextColor() } },
            tooltip: { titleColor: getTextColor(), bodyColor: getTextColor() }
        },
        scales: {
            x: { ticks: { color: getTextColor() } },
            y: { ticks: { color: getTextColor() } }
        }
    }
});
const chartKeluar = new Chart(document.getElementById('chartKeluar'), {
    type: 'bar',
    data: {
        labels: <?= json_encode($barangKeluarNama); ?>,
        datasets: [{
            label: 'Barang Keluar',
            data: <?= json_encode($barangKeluarQty); ?>,
            backgroundColor: 'rgba(255, 193, 7, 0.7)',
            borderColor: 'rgba(255, 193, 7, 1)',
            borderWidth: 1
        }]
    },
    options: {
        plugins: {
            legend: { labels: { color: getTextColor() } },
            tooltip: { titleColor: getTextColor(), bodyColor: getTextColor() }
        },
        scales: {
            x: { ticks: { color: getTextColor() } },
            y: { ticks: { color: getTextColor() } }
        }
    }
});
const chartStock = new Chart(document.getElementById('chartStock'), {
    type: 'bar',
    data: {
        labels: <?= json_encode($stokBarangNama); ?>,
        datasets: [{
            label: 'Stok Barang',
            data: <?= json_encode($stokBarangQty); ?>,
            backgroundColor: 'rgba(0, 123, 255, 0.7)',
            borderColor: 'rgba(0, 123, 255, 1)',
            borderWidth: 1
        }]
    },
    options: {
        plugins: {
            legend: { labels: { color: getTextColor() } },
            tooltip: { titleColor: getTextColor(), bodyColor: getTextColor() }
        },
        scales: {
            x: { ticks: { color: getTextColor() } },
            y: { ticks: { color: getTextColor() } }
        }
    }
});
// Dark mode toggle
function toggleDarkMode() {
    document.body.classList.toggle('dark-mode');
    if (document.body.classList.contains('dark-mode')) {
        localStorage.setItem('darkMode', 'enabled');
    } else {
        localStorage.setItem('darkMode', 'disabled');
    }
    updateChartColors(chartMasuk);
    updateChartColors(chartKeluar);
    updateChartColors(chartStock);
}
if (localStorage.getItem('darkMode') === 'enabled') {
    document.body.classList.add('dark-mode');
    updateChartColors(chartMasuk);
    updateChartColors(chartKeluar);
    updateChartColors(chartStock);
}
document.getElementById('sidebarToggle').addEventListener('click', function () {
    document.getElementById('layoutSidenav').classList.toggle('sb-sidenav-toggled');
});
// Saat klik menu tampilkan loader fade in
document.querySelectorAll('a.nav-link').forEach(link => {
    link.addEventListener('click', function(e) {
        if (this.href && !this.href.includes('#') && !this.href.includes('logout.php')) {
            e.preventDefault();
            const overlay = document.getElementById('loadingOverlay');
            overlay.classList.add('active');
            setTimeout(() => {
                window.location.href = this.href;
            }, 400); // delay biar animasi muncul
        }
    });
});

// Hilangkan loader saat halaman sudah load
window.addEventListener('load', function() {
    document.getElementById('loadingOverlay').classList.remove('active');
});
</script>
</body>
</html>
