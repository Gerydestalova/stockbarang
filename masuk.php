<?php
require 'function.php';
require 'cek.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Restock Barang - Matahari Motor Group</title>
    <link href="css/styles.css" rel="stylesheet" />
    <link href="https://cdn.datatables.net/1.10.20/css/dataTables.bootstrap4.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free/css/all.css" rel="stylesheet">
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
    <div id="layoutSidenav_nav">
        <nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
            <div class="sb-sidenav-menu">
                <div class="nav">
                    <a class="nav-link" href="dashboard.php"><div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>Dashboard</a>
                    <a class="nav-link" href="index.php"><div class="sb-nav-link-icon"><i class="fas fa-box"></i></div>Stock Barang</a>
                    <a class="nav-link active" href="masuk.php"><div class="sb-nav-link-icon"><i class="fas fa-download"></i></div>Restock Barang</a>
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
            <h1 class="mb-4">Restock Barang</h1>
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span><i class="fas fa-download"></i> Data Restock Barang</span>
                    <button type="button" class="btn btn-success" data-toggle="modal" data-target="#tambahRestock">+ Tambah Restock</button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                            <thead class="bg-success text-white">
                                <tr>
                                    <th>No</th>
                                    <th>Tanggal</th>
                                    <th>Nama Barang</th>
                                    <th>Jumlah</th>
                                    <th>Penerima</th>
                                    <th>Supplier</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php
                            $ambilsemuadata = mysqli_query($conn, "
                                SELECT m.*, s.namabarang 
                                FROM masuk m 
                                JOIN stock s ON m.idbarang = s.idbarang
                            ");
                            $i = 1;
                            while ($data = mysqli_fetch_array($ambilsemuadata)) {
                                $idb = $data['idbarang'];
                                $idm = $data['idmasuk'];
                                $tanggal = $data['tanggal'];
                                $namabarang = $data['namabarang'];
                                $qty = $data['qty'];
                                $penerima = $data['penerima'];
                                $supplier = $data['namasupplier'] ?? '-';
                            ?>
                            <tr>
                                <td><?= $i++; ?></td>
                                <td><?= $tanggal; ?></td>
                                <td><?= $namabarang; ?></td>
                                <td><?= $qty; ?></td>
                                <td><?= $penerima; ?></td>
                                <td><?= $supplier; ?></td>
                                <td>
                                    <button class="btn btn-warning btn-sm" data-toggle="modal" data-target="#edit<?= $idm; ?>"><i class="fas fa-edit"></i></button>
                                    <button class="btn btn-danger btn-sm" data-toggle="modal" data-target="#delete<?= $idm; ?>"><i class="fas fa-trash"></i></button>
                                </td>
                            </tr>

                            <!-- Edit Modal -->
                            <div class="modal fade" id="edit<?= $idm; ?>">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Edit Restock Barang</h5>
                                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                                        </div>
                                        <form method="post">
                                            <div class="modal-body">
                                                <input type="hidden" name="idb" value="<?= $idb; ?>">
                                                <input type="hidden" name="idm" value="<?= $idm; ?>">
                                                <label>Jumlah</label>
                                                <input type="number" name="qty" value="<?= $qty; ?>" class="form-control" required>
                                                <label>Penerima</label>
                                                <input type="text" name="penerima" value="<?= $penerima; ?>" class="form-control" required>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="submit" class="btn btn-primary" name="updatebarangmasuk">Update</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <!-- Delete Modal -->
                            <div class="modal fade" id="delete<?= $idm; ?>">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header bg-danger">
                                            <h5 class="modal-title">Hapus Restock Barang</h5>
                                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                                        </div>
                                        <form method="post">
                                            <input type="hidden" name="idm" value="<?= $idm; ?>">
                                            <input type="hidden" name="idb" value="<?= $idb; ?>">
                                            <div class="modal-body">
                                                Yakin ingin menghapus <strong><?= $namabarang; ?></strong>?
                                            </div>
                                            <div class="modal-footer">
                                                <button type="submit" class="btn btn-danger" name="hapusbarangmasuk">Hapus</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <?php } ?>
                            </tbody>
                        </table>
                    </div>
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

<!-- Tambah Restock Modal -->
<div class="modal fade" id="tambahRestock">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Restock Barang</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form method="post">
                <div class="modal-body">
                    <label>Pilih Barang</label>
                    <select name="barangnya" class="form-control" required>
                        <option value="" disabled selected>-- Pilih Barang --</option>
                        <?php
                        $getbarang = mysqli_query($conn, "SELECT * FROM stock");
                        while ($b = mysqli_fetch_array($getbarang)) {
                            echo '<option value="'.$b['idbarang'].'">'.$b['namabarang'].'</option>';
                        }
                        ?>
                    </select>
                    <label>Jumlah</label>
                    <input type="number" name="qty" class="form-control" required>
                    <label>Penerima</label>
                    <input type="text" name="penerima" class="form-control" required>
                    <label>Supplier</label>
                    <select name="namasupplier" class="form-control" required>
                        <option value="" disabled selected>-- Pilih Supplier --</option>
                        <?php
                        $getsupplier = mysqli_query($conn, "SELECT DISTINCT namasupplier FROM supplier");
                        while ($s = mysqli_fetch_array($getsupplier)) {
                            echo '<option value="'.$s['namasupplier'].'">'.$s['namasupplier'].'</option>';
                        }
                        ?>
                    </select>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success" name="barangmasuk">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="js/scripts.js"></script>
<script src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.10.20/js/dataTables.bootstrap4.min.js"></script>
<script>
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
