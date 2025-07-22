<?php
session_start();
require 'function.php';

if (isset($_POST['register'])) {
    $nama = $_POST['nama'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $role = $_POST['role'];

$cek = mysqli_query($conn, "SELECT * FROM user WHERE email='$email'");


if (!$cek) {
    die("Query Error: " . mysqli_error($conn)); // bantu debug
}

if (mysqli_num_rows($cek) > 0) {

        $error = "Email sudah terdaftar!";
    } else {
        $insert = mysqli_query($conn, "INSERT INTO user (nama, email, password, role) VALUES ('$nama', '$email', '$password', '$role')");
        if ($insert) {
            echo "<script>alert('Pendaftaran berhasil! Silakan login.'); window.location='login.php';</script>";
            exit;
        } else {
            $error = "Pendaftaran gagal.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register - Matahari Motor Group</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #1abc9c;
            --secondary-color: #16a085;
            --bg-light: #ffffff;
            --bg-dark: #1e1e1e;
            --text-light: #2c3e50;
            --text-dark: #ecf0f1;
            --shadow-color: rgba(0, 0, 0, 0.2);
        }

        body {
            background: linear-gradient(135deg, #1d2b64, #f8cdda);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            font-family: 'Segoe UI', sans-serif;
            transition: all 0.3s ease;
        }

        body.dark-mode {
            background: linear-gradient(135deg, #34495e, #2c3e50);
        }

        .register-card {
            background: var(--bg-light);
            border-radius: 1rem;
            box-shadow: 0 8px 24px var(--shadow-color);
            padding: 2.5rem;
            width: 100%;
            max-width: 500px;
            animation: fadeIn 1s ease-out;
        }

        body.dark-mode .register-card {
            background: var(--bg-dark);
            color: var(--text-dark);
        }

        .register-title {
            font-weight: 700;
            margin-bottom: 1.8rem;
            color: var(--primary-color);
            text-align: center;
        }

        .form-control {
            border-radius: 0.5rem;
        }

        .btn-register {
            background-color: var(--primary-color);
            border: none;
            width: 100%;
            border-radius: 0.5rem;
            font-weight: 600;
        }

        .btn-register:hover {
            background-color: var(--secondary-color);
            transform: scale(1.02);
        }

        .footer-text {
            font-size: 0.9rem;
            margin-top: 1rem;
            text-align: center;
            color: #555;
        }

        body.dark-mode .footer-text {
            color: var(--text-dark);
        }

        .alert {
            font-size: 0.95rem;
            border-radius: 0.5rem;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>

<div class="register-card">
    <h3 class="register-title">📝 Buat Akun Baru</h3>

    <?php if (isset($error)): ?>
        <div class="alert alert-danger text-center"><?= $error ?></div>
    <?php endif; ?>

    <form method="post">
        <div class="mb-3">
            <label class="form-label">👤 Nama Lengkap</label>
            <input type="text" name="nama" class="form-control" placeholder="Masukkan nama lengkap" required>
        </div>
        <div class="mb-3">
            <label class="form-label">📧 Email</label>
            <input type="email" name="email" class="form-control" placeholder="Masukkan email aktif" required>
        </div>
        <div class="mb-3">
            <label class="form-label">🔑 Password</label>
            <input type="password" name="password" class="form-control" placeholder="Masukkan password" required>
        </div>
        <div class="mb-3">
            <label class="form-label">📋 Daftar Sebagai</label>
            <select name="role" class="form-control" required>
                <option value="manager">Manager</option>
                <option value="admin">Admin</option>
            </select>
        </div>
        <button type="submit" name="register" class="btn btn-register text-white mt-3">Daftar</button>
    </form>
    <div class="footer-text">
        Sudah punya akun? <a href="login.php" class="text-decoration-none">Login di sini</a>
    </div>
</div>

<script>
    // Enable dark mode if user has it active
    if (localStorage.getItem('darkMode') === 'enabled') {
        document.body.classList.add('dark-mode');
    }
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
