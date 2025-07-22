<?php
require 'function.php';
require 'cek.php';

// Ambil filter tanggal
$dari = $_GET['dari'] ?? '';
$sampai = $_GET['sampai'] ?? '';
$type = $_GET['type'] ?? '';

// Pastikan tanggal ada
if ($dari && $sampai) {
    $dari .= " 00:00:00";
    $sampai .= " 23:59:59";
}

// Fungsi tampilkan isi laporan
function renderLaporanContent($dari, $sampai)
{
    global $conn;
    ob_start();
?>
    <style>
        body { font-family: Arial, sans-serif; }
        h2, p { text-align: center; }
        table {
            border-collapse: collapse;
            width: 100%;
            margin-top: 10px;
        }
        table, th, td {
            border: 1px solid #333;
        }
        th {
            background-color: #28a745;
            color: #fff;
            padding: 8px;
        }
        td {
            padding: 6px;
        }
        .table-section {
            margin-top: 20px;
        }
        .no-data {
            text-align: center;
            color: #dc3545;
            font-weight: bold;
        }
    </style>

    <h2>LAPORAN BARANG<br>MATAHARI MOTOR GROUP</h2>
    <p>Periode: <?= date('Y-m-d', strtotime($dari)) ?> s/d <?= date('Y-m-d', strtotime($sampai)) ?></p>
    <hr>

    <!-- Ringkasan Stok -->
<div class="card mb-4">
    <div class="card-header"><i class="fas fa-box"></i> Ringkasan Stok per Barang</div>
    <div class="card-body table-responsive">
        <table class="table table-bordered table-striped" id="dataTableSummary">
            <thead class="bg-success text-white">
                <tr>
                    <th>Nama Barang</th>
                    <th>Stok Awal</th>
                    <th>Masuk</th>
                    <th>Keluar</th>
                    <th>Stok Akhir</th>
                    <th>Jumlah Saat Ini</th>
                </tr>
            </thead>
            <tbody>
            <?php
            $queryBarang = mysqli_query($conn, "SELECT * FROM stock");
            while ($barang = mysqli_fetch_array($queryBarang)) {
                $idbarang = $barang['idbarang'];
                $namabarang = $barang['namabarang'];
                $jumlah_saat_ini = $barang['jumlah'];

                // Hitung stok awal
                $stok_masuk_sebelum = mysqli_fetch_array(mysqli_query($conn, 
                    "SELECT IFNULL(SUM(qty),0) as total FROM masuk WHERE idbarang='$idbarang' AND tanggal < '$dari 00:00:00'"))['total'];
                $stok_keluar_sebelum = mysqli_fetch_array(mysqli_query($conn, 
                    "SELECT IFNULL(SUM(qty),0) as total FROM keluar WHERE idbarang='$idbarang' AND tanggal < '$dari 00:00:00'"))['total'];
                $stok_awal = $stok_masuk_sebelum - $stok_keluar_sebelum;

                // Hitung selama periode
                $masuk_periode = mysqli_fetch_array(mysqli_query($conn, 
                    "SELECT IFNULL(SUM(qty),0) as total FROM masuk WHERE idbarang='$idbarang' AND tanggal BETWEEN '$dari 00:00:00' AND '$sampai 23:59:59'"))['total'];
                $keluar_periode = mysqli_fetch_array(mysqli_query($conn, 
                    "SELECT IFNULL(SUM(qty),0) as total FROM keluar WHERE idbarang='$idbarang' AND tanggal BETWEEN '$dari 00:00:00' AND '$sampai 23:59:59'"))['total'];
                $stok_akhir = $stok_awal + $masuk_periode - $keluar_periode;

                echo "<tr>
                    <td>$namabarang</td>
                    <td>" . number_format($stok_awal) . "</td>
                    <td>" . number_format($masuk_periode) . "</td>
                    <td>" . number_format($keluar_periode) . "</td>
                    <td>" . number_format($stok_akhir) . "</td>
                    <td><b>" . number_format($jumlah_saat_ini) . "</b></td>
                </tr>";
            }
            ?>

            </tbody>
        </table>
    </div>

  <!-- Data Barang Masuk -->
<div class="table-section">
    <h4>Data Restock Barang</h4>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Barang</th>
                <th>Jumlah</th>
                <th>Tanggal</th>
                <th>Penerima</th>
                <th>Supplier</th>
                <th>Jenis Transaksi</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $no = 1;
            $masuk = mysqli_query($conn, "SELECT m.*, s.namabarang, s.jenisbarang, s.supplier
                FROM masuk m
                JOIN stock s ON m.idbarang = s.idbarang
                WHERE m.tanggal BETWEEN '$dari' AND '$sampai'
                ORDER BY m.tanggal DESC");

           if ($masuk && mysqli_num_rows($masuk) > 0) {
    while ($m = mysqli_fetch_array($masuk)) {
        echo "<tr>
                <td>{$no}</td>
                <td>{$m['namabarang']}</td>
                <td>{$m['qty']}</td>
                <td>{$m['tanggal']}</td>
                <td>{$m['penerima']}</td>
                <td>{$m['namasupplier']}</td>
                <td>{$m['jnstransaksi']}</td>
            </tr>";
        $no++;
    }
} else {
    echo "<tr><td colspan='7' class='no-data'>Tidak ada data barang masuk pada periode ini.</td></tr>";
}

            ?>
        </tbody>
    </table>
</div>

<!-- Data Barang Keluar -->
<div class="table-section">
    <h4>Data Barang Keluar</h4>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Barang</th>
                <th>Jumlah</th>
                <th>Tanggal</th>
                <th>Penerima</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $no = 1;
            $keluar = mysqli_query($conn, "SELECT k.*, s.namabarang, s.jenisbarang
                FROM keluar k
                JOIN stock s ON k.idbarang = s.idbarang
                WHERE k.tanggal BETWEEN '$dari' AND '$sampai'
                ORDER BY k.tanggal DESC");

           if ($keluar && mysqli_num_rows($keluar) > 0) {
    while ($k = mysqli_fetch_array($keluar)) {
        echo "<tr>
                <td>{$no}</td>
                <td>{$k['namabarang']}</td>
                <td>{$k['qty']}</td>
                <td>{$k['tanggal']}</td>
                <td>{$k['penerima']}</td>
            </tr>";
        $no++;
    }
} else {
    echo "<tr><td colspan='5' class='no-data'>Tidak ada data barang keluar pada periode ini.</td></tr>";
}

            ?>
        </tbody>
    </table>
</div>

<?php
    return ob_get_clean();
}

// Export PDF/Excel
if ($type == 'pdf' || $type == 'excel') {
    $content = renderLaporanContent($dari, $sampai);

    if ($type == 'pdf') {
        require_once __DIR__ . '/vendor/autoload.php';
        $mpdf = new \Mpdf\Mpdf();
        $mpdf->WriteHTML($content);
        $mpdf->Output("laporan-" . date('Ymd-His') . ".pdf", 'D');
    } else {
        header("Content-Type: application/vnd.ms-excel");
        header("Content-Disposition: attachment; filename=laporan-" . date('Ymd-His') . ".xls");
        echo $content;
    }
    exit;
}
?>


    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="utf-8" />
        <title>Laporan - Matahari Motor Group</title>
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <link href="css/styles.css" rel="stylesheet" />
        <link href="https://cdn.datatables.net/1.10.20/css/dataTables.bootstrap4.min.css" rel="stylesheet" />
        <link href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free/css/all.css" rel="stylesheet">
        <link href="https://cdn.datatables.net/buttons/1.6.5/css/buttons.dataTables.min.css" rel="stylesheet" />
        <style>
    body { background-color: #f8f9fa; color: #212529; }
        .card { border-radius: 15px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
        .card-header { font-weight: 600; }
        .table th, .table td { vertical-align: middle; }
        .btn { border-radius: 8px; transition: all 0.3s ease; }
        .btn:hover { transform: scale(1.05); }
        .modal-content { border-radius: 20px; }
        .modal-header { background: linear-gradient(135deg, #20c997, #17a2b8); color: white; border-top-left-radius: 20px; border-top-right-radius: 20px; }
        .modal-footer { background-color: #f1f3f5; border-bottom-left-radius: 20px; border-bottom-right-radius: 20px; }
        .dark-mode {
            background-color: #121212;
            color: #ffffff;
        }
        .dark-mode .card, .dark-mode .modal-content {
            background-color: #1e1e2f;
            color: #ffffff;
            box-shadow: 0 4px 15px rgba(255, 255, 255, 0.1);
        }
        .dark-mode .modal-header {
            background: linear-gradient(135deg, #6f42c1, #20c997);
            color: #ffffff;
        }
        .dark-mode .modal-footer {
            background-color: #2c2f33;
        }
        .dark-mode .btn-primary {
            background-color: #0d6efd;
            border-color: #0d6efd;
        }
        .dark-mode .btn-success {
            background-color: #198754;
            border-color: #198754;
        }
        .dark-mode .btn-danger {
            background-color: #dc3545;
            border-color: #dc3545;
        }
        .dark-mode .table { color: #ffffff; }
        .dark-mode .navbar, .dark-mode .sb-sidenav {
            background-color: #1f1f1f;
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
    <!-- Top Navbar -->
    <nav class="sb-topnav navbar navbar-expand navbar-dark bg-success">
        <a class="navbar-brand" href="index.php">Matahari Motor Group</a>
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
        <!-- Sidebar -->
        <div id="layoutSidenav_nav">
            <nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
                <div class="sb-sidenav-menu">
                    <div class="nav">
                        <a class="nav-link" href="dashboard.php"><div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>Dashboard</a>
                        <a class="nav-link" href="index.php"><div class="sb-nav-link-icon"><i class="fas fa-box"></i></div>Stock Barang</a>
                        <a class="nav-link" href="masuk.php"><div class="sb-nav-link-icon"><i class="fas fa-download"></i></div>Restock Barang</a>
                        <a class="nav-link" href="keluar.php"><div class="sb-nav-link-icon"><i class="fas fa-upload"></i></div>Barang Keluar</a>
                        <a class="nav-link" href="supplier.php"><div class="sb-nav-link-icon"><i class="fas fa-truck"></i></div>Supplier</a>
                        <a class="nav-link active" href="laporan.php"><div class="sb-nav-link-icon"><i class="fas fa-file-alt"></i></div>Laporan</a>
                        <a class="nav-link" href="logout.php"><div class="sb-nav-link-icon"><i class="fas fa-sign-out-alt"></i></div>Logout</a>
                    </div>
                </div>
            </nav>
        </div>

        <!-- Content -->
        <div id="layoutSidenav_content">
            <main class="container-fluid px-4 mt-4">
                <h1 class="mb-4">Laporan Barang</h1>
                <div class="card mb-4">
                    <div class="card-header"><i class="fas fa-filter"></i> Filter Laporan</div>
                    <div class="card-body">
                        <form method="GET" class="form-inline">
                            <label class="mr-2">Dari Tanggal:</label>
                            <input type="date" name="dari" class="form-control mr-3" required value="<?= isset($_GET['dari']) ? $_GET['dari'] : '' ?>">
                            <label class="mr-2">Sampai Tanggal:</label>
                            <input type="date" name="sampai" class="form-control mr-3" required value="<?= isset($_GET['sampai']) ? $_GET['sampai'] : '' ?>">
                            <button type="submit" class="btn btn-primary">Tampilkan</button>
                        </form>
                        <?php if (isset($_GET['dari']) && isset($_GET['sampai'])): ?>
        <div class="mb-3">
            <div class="btn-group" role="group" aria-label="Export Buttons">
                <button id="exportExcel" class="btn btn-success"><i class="fas fa-file-excel"></i> Export Excel</button>
                <button id="exportPDF" class="btn btn-danger"><i class="fas fa-file-pdf"></i> Export PDF</button>
                <button id="exportPrint" class="btn btn-secondary"><i class="fas fa-print"></i> Print</button>
            </div>
        </div>
    <?php endif; ?>

                    </div>
                </div>

                <?php if (isset($_GET['dari']) && isset($_GET['sampai'])):
                    $dari = $_GET['dari'];
                    $sampai = $_GET['sampai'];
                ?>

<!-- Ringkasan Stok -->
<div class="card mb-4">
    <div class="card-header"><i class="fas fa-box"></i> Ringkasan Stok per Barang</div>
    <div class="card-body table-responsive">
        <table class="table table-bordered table-striped" id="dataTableSummary">
            <thead class="bg-success text-white">
                <tr>
                    <th>Nama Barang</th>
                    <th>Stok Awal</th>
                    <th>Masuk</th>
                    <th>Keluar</th>
                    <th>Stok Akhir</th>
                    <th>Jumlah Saat Ini</th>
                </tr>
            </thead>
            <tbody>
            <?php
 $queryBarang = mysqli_query($conn, "SELECT * FROM stock");
while ($barang = mysqli_fetch_array($queryBarang)) {
    $idbarang = $barang['idbarang'];
    $namabarang = $barang['namabarang'];
    $jumlah_saat_ini = $barang['jumlah'] ?? 0;

    // Hitung stok awal
    $stok_masuk_sebelum = mysqli_fetch_array(mysqli_query($conn, 
        "SELECT IFNULL(SUM(qty),0) as total FROM masuk WHERE idbarang='$idbarang' AND tanggal < '$dari 00:00:00'"))['total'];
    $stok_keluar_sebelum = mysqli_fetch_array(mysqli_query($conn, 
        "SELECT IFNULL(SUM(qty),0) as total FROM keluar WHERE idbarang='$idbarang' AND tanggal < '$dari 00:00:00'"))['total'];
    $stok_awal = $stok_masuk_sebelum - $stok_keluar_sebelum;

    // Hitung selama periode
    $masuk_periode = mysqli_fetch_array(mysqli_query($conn, 
        "SELECT IFNULL(SUM(qty),0) as total FROM masuk WHERE idbarang='$idbarang' AND tanggal BETWEEN '$dari 00:00:00' AND '$sampai 23:59:59'"))['total'];
    $keluar_periode = mysqli_fetch_array(mysqli_query($conn, 
        "SELECT IFNULL(SUM(qty),0) as total FROM keluar WHERE idbarang='$idbarang' AND tanggal BETWEEN '$dari 00:00:00' AND '$sampai 23:59:59'"))['total'];

    $stok_akhir = $stok_awal + $masuk_periode - $keluar_periode;

    echo "<tr>
        <td>$namabarang</td>
        <td>" . number_format($stok_awal) . "</td>
        <td>" . number_format($masuk_periode) . "</td>
        <td>" . number_format($keluar_periode) . "</td>
        <td>" . number_format($stok_akhir) . "</td>
        <td><b>" . number_format($jumlah_saat_ini) . "</b></td>
    </tr>";
}

            ?>
            </tbody>
        </table>
    </div>
</div>


                <!-- Barang Masuk -->
                <div class="card mb-4">
                    <div class="card-header"><i class="fas fa-download"></i> Restock Barang</div>
                    <div class="card-body table-responsive">
                        <table class="table table-bordered" id="dataTableMasuk">
                            <thead class="bg-primary text-white">
                                <tr>
                                    <th>No</th>
                                    <th>Nama Barang</th>
                                    <th>Jumlah</th>
                                    <th>Tanggal</th>
                                    <th>Penerima</th>
                                    <th>Supplier</th>
                                    <th>Jenis Transaksi</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php
                            $getMasuk = mysqli_query($conn, "SELECT m.*, s.namabarang FROM masuk m JOIN stock s ON m.idbarang = s.idbarang WHERE m.tanggal BETWEEN '$dari 00:00:00' AND '$sampai 23:59:59'");
                            $i = 1;
                            while ($data = mysqli_fetch_array($getMasuk)) {
                                echo "<tr>
                                    <td>{$i}</td>
                                    <td>{$data['namabarang']}</td>
                                    <td>{$data['qty']}</td>
                                    <td>{$data['tanggal']}</td>
                                    <td>{$data['penerima']}</td>
                                    <td>{$data['namasupplier']}</td>
                                    <td>{$data['jnstransaksi']}</td>
                                </tr>";
                                $i++;
                            }
                            ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Barang Keluar -->
                <div class="card mb-4">
                    <div class="card-header"><i class="fas fa-upload"></i> Barang Keluar</div>
                    <div class="card-body table-responsive">
                        <table class="table table-bordered" id="dataTableKeluar">
                            <thead class="bg-danger text-white">
                                <tr>
                                    <th>No</th>
                                    <th>Nama Barang</th>
                                    <th>Jumlah</th>
                                    <th>Tanggal</th>
                                    <th>Penerima</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php
                            $ambil = mysqli_query($conn, "SELECT k.*, s.namabarang FROM keluar k JOIN stock s ON k.idbarang = s.idbarang WHERE k.tanggal BETWEEN '$dari 00:00:00' AND '$sampai 23:59:59'");
                            $i = 1;
                            while ($data = mysqli_fetch_array($ambil)) {
                                echo "<tr>
                                    <td>{$i}</td>
                                    <td>{$data['namabarang']}</td>
                                    <td>{$data['qty']}</td>
                                    <td>{$data['tanggal']}</td>
                                    <td>{$data['penerima']}</td>
                                </tr>";
                                $i++;
                            }
                            ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <?php endif; ?>
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

    <!-- Script -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/scripts.js"></script>
    <script src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.6.5/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.6.5/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.6.5/js/buttons.print.min.js"></script>
    
    <script>
        document.getElementById('exportPDF')?.addEventListener('click', function () {
    const dari = '<?= $_GET['dari'] ?? '' ?>';
    const sampai = '<?= $_GET['sampai'] ?? '' ?>';
    window.location.href = `laporan.php?type=pdf&dari=${dari}&sampai=${sampai}`;
});
document.getElementById('exportExcel')?.addEventListener('click', function () {
    const dari = '<?= $_GET['dari'] ?? '' ?>';
    const sampai = '<?= $_GET['sampai'] ?? '' ?>';
    window.location.href = `laporan.php?type=excel&dari=${dari}&sampai=${sampai}`;
});
document.getElementById('exportPrint')?.addEventListener('click', function () {
    window.print();
});

        $(document).ready(function () {
        $('#dataTable').DataTable();
    });

    // Dark mode persist
    if (localStorage.getItem('darkMode') === 'enabled') {
        document.body.classList.add('dark-mode');
    }

    function toggleDarkMode() {
        document.body.classList.toggle('dark-mode');
        if (document.body.classList.contains('dark-mode')) {
            localStorage.setItem('darkMode', 'enabled');
        } else {
            localStorage.setItem('darkMode', 'disabled');
        }
    }

    // Sidebar toggle
    document.getElementById('sidebarToggle').addEventListener('click', function () {
        document.body.classList.toggle('sb-sidenav-toggled');
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
