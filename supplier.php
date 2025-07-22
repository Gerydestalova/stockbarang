<?php
require 'function.php';
require 'cek.php';

// Proses tambah supplier
if (isset($_POST['tambahsupplier'])) {
    $namasupplier = $_POST['namasupplier'];
    $alamat = $_POST['alamat'];
    $no_tlp = $_POST['no_tlp'];
    $emailsupplier = $_POST['emailsupplier'];

    $insert = mysqli_query($conn, "INSERT INTO supplier (namasupplier, alamat, no_tlp, emailsupplier)
        VALUES ('$namasupplier', '$alamat', '$no_tlp', '$emailsupplier')");

    if ($insert) {
        header("Location: supplier.php");
        exit;
    }
}

// Proses update supplier
if (isset($_POST['updatesupplier'])) {
    $id_supplier = $_POST['id_supplier'];
    $namasupplier = $_POST['namasupplier'];
    $alamat = $_POST['alamat'];
    $no_tlp = $_POST['no_tlp'];
    $emailsupplier = $_POST['emailsupplier'];

    $update = mysqli_query($conn, "UPDATE supplier SET namasupplier='$namasupplier', alamat='$alamat', no_tlp='$no_tlp', emailsupplier='$emailsupplier' WHERE idsupplier='$id_supplier'");
}

// Proses hapus supplier
if (isset($_POST['hapussupplier'])) {
    $id_supplier = $_POST['id_supplier'];
    $delete = mysqli_query($conn, "DELETE FROM supplier WHERE idsupplier='$id_supplier'");
    if ($delete) {
        header("Location: supplier.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <title>Data Supplier - Matahari Motor Group</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
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
<!-- Top Navbar -->
<nav class="sb-topnav navbar navbar-expand navbar-dark bg-success">
    <button class="btn btn-link btn-sm order-1 order-lg-0" id="sidebarToggle"><i class="fas fa-bars"></i></button>
    <a class="navbar-brand" href="index.php">Matahari Motor Group</a>
    <button onclick="toggleDarkMode()" class="btn btn-light btn-sm ml-auto">🌙 / ☀</button>
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
                    <a class="nav-link active" href="supplier.php"><div class="sb-nav-link-icon"><i class="fas fa-truck"></i></div>Supplier</a>
                    <a class="nav-link" href="laporan.php"><div class="sb-nav-link-icon"><i class="fas fa-file-alt"></i></div>Laporan</a>
                    <a class="nav-link" href="logout.php"><div class="sb-nav-link-icon"><i class="fas fa-sign-out-alt"></i></div>Logout</a>
                </div>
            </div>
        </nav>
    </div>

    <!-- Content -->
    <div id="layoutSidenav_content">
        <main class="container-fluid px-4 mt-4">
            <h1 class="mb-4">Data Supplier</h1>
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span><i class="fas fa-truck"></i> List Supplier</span>
                    <button class="btn btn-success" data-toggle="modal" data-target="#addSupplierModal">+ Tambah Supplier</button>
                </div>
                <div class="card-body">
                    <table class="table table-bordered" id="dataTable" width="100%">
                        <thead class="bg-success text-white">
                            <tr>
                                <th>No</th>
                                <th>Nama Supplier</th>
                                <th>Alamat</th>
                                <th>No Telepon</th>
                                <th>Email Supplier</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $i = 1;
                            $ambildatasupplier = mysqli_query($conn, "SELECT * FROM supplier");
                            while ($data = mysqli_fetch_array($ambildatasupplier)) {
                                $idsupplier = $data['idsupplier'];
                                $namasupplier = $data['namasupplier'];
                                $alamat = $data['alamat'];
                                $no_tlp = $data['no_tlp'];
                                $emailsupplier = $data['emailsupplier'];
                            ?>
                            <tr>
                                <td><?= $i++; ?></td>
                                <td><?= $namasupplier; ?></td>
                                <td><?= $alamat; ?></td>
                                <td><?= $no_tlp; ?></td>
                                <td><?= $emailsupplier; ?></td>
                                <td>
                                    <button class="btn btn-warning btn-sm" data-toggle="modal" data-target="#edit<?= $idsupplier; ?>"><i class="fas fa-edit"></i></button>
                                    <button class="btn btn-danger btn-sm" data-toggle="modal" data-target="#delete<?= $idsupplier; ?>"><i class="fas fa-trash"></i></button>
                                </td>
                            </tr>

                            <!-- Edit Modal -->
                            <div class="modal fade" id="edit<?= $idsupplier; ?>" tabindex="-1">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <form method="post">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Edit Supplier</h5>
                                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                            </div>
                                            <div class="modal-body">
                                                <input type="hidden" name="id_supplier" value="<?= $idsupplier; ?>">
                                                <label>Nama Supplier</label>
                                                <input type="text" name="namasupplier" value="<?= $namasupplier; ?>" class="form-control" required>
                                                <label>Alamat</label>
                                                <input type="text" name="alamat" value="<?= $alamat; ?>" class="form-control" required>
                                                <label>No Telepon</label>
                                                <input type="text" name="no_tlp" value="<?= $no_tlp; ?>" class="form-control" required>
                                                <label>Email Supplier</label>
                                                <input type="email" name="emailsupplier" value="<?= $emailsupplier; ?>" class="form-control" required>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="submit" class="btn btn-primary" name="updatesupplier">Simpan</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <!-- Delete Modal -->
                            <div class="modal fade" id="delete<?= $idsupplier; ?>" tabindex="-1">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <form method="post">
                                            <div class="modal-header bg-danger">
                                                <h5 class="modal-title">Hapus Supplier</h5>
                                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                            </div>
                                            <div class="modal-body">
                                                Yakin ingin menghapus <strong><?= $namasupplier; ?></strong>?
                                                <input type="hidden" name="id_supplier" value="<?= $idsupplier; ?>">
                                            </div>
                                            <div class="modal-footer">
                                                <button type="submit" class="btn btn-danger" name="hapussupplier">Hapus</button>
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

<!-- Modal Tambah Supplier -->
<div class="modal fade" id="addSupplierModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form method="post">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Supplier</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <label>Nama Supplier</label>
                    <input type="text" name="namasupplier" class="form-control" required>
                    <label>Alamat</label>
                    <input type="text" name="alamat" class="form-control" required>
                    <label>No Telepon</label>
                    <input type="text" name="no_tlp" class="form-control" required>
                    <label>Email Supplier</label>
                    <input type="email" name="emailsupplier" class="form-control" required>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success" name="tambahsupplier">Simpan</button>
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
