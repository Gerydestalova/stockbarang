<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require 'function.php'; // koneksi ke DB

// Proses login
if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $cekdatabase = mysqli_query($conn, "SELECT * FROM user WHERE email='$email'");

    if (mysqli_num_rows($cekdatabase) > 0) {
        $data = mysqli_fetch_array($cekdatabase);

        if ($password == $data['password']) {
            $_SESSION['log'] = true;
            $_SESSION['role'] = $data['role'];
            $_SESSION['iduser'] = $data['iduser'];

            if ($data['role'] == 'admin') {
    $_SESSION['admin_logged_in'] = true;
    $_SESSION['admin_id'] = $data['iduser'];
    header("Location: dashboard.php");
    exit;
} elseif ($data['role'] == 'owner') {
    $_SESSION['owner_logged_in'] = true;
    $_SESSION['owner_id'] = $data['iduser'];
    header("Location: owner_dashboard.php");
                exit;
            } else {
                $error = "Role tidak dikenali.";
            }
        } else {
            $error = "Password salah.";
        }
    } else {
        $error = "Email tidak terdaftar.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login - Matahari Motor Group</title>
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
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
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

        .login-card {
            background: var(--bg-light);
            border-radius: 1rem;
            box-shadow: 0 8px 24px var(--shadow-color);
            padding: 2.5rem;
            width: 100%;
            max-width: 420px;
            animation: fadeIn 1s ease-out;
        }

        body.dark-mode .login-card {
            background: var(--bg-dark);
            color: var(--text-dark);
        }

        .login-title {
            font-weight: 700;
            margin-bottom: 1.8rem;
            color: var(--primary-color);
            text-align: center;
        }

        .form-control {
            border-radius: 0.5rem;
        }

        .btn-login {
            background-color: var(--primary-color);
            border: none;
            width: 100%;
            border-radius: 0.5rem;
            font-weight: 600;
        }

        .btn-login:hover {
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

<div class="login-card">
    <h3 class="login-title">🔐 Login ke Sistem</h3>

    <?php if (isset($error)): ?>
        <div class="alert alert-danger text-center"><?= $error ?></div>
    <?php endif; ?>

    <form method="post">
        <div class="mb-3">
            <label for="email" class="form-label">📧 Email</label>
            <input type="email" name="email" class="form-control" placeholder="Masukkan email" required>
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">🔑 Password</label>
            <input type="password" name="password" class="form-control" placeholder="Masukkan password" required>
        </div>
        <button type="submit" name="login" class="btn btn-login text-white mt-3">Login</button>
    </form>
    <div class="footer-text">
        Belum punya akun? <a href="register.php" class="text-decoration-none">Daftar</a>
    </div>
</div>

<script>
    // Apply dark mode if previously enabled
    if (localStorage.getItem('darkMode') === 'enabled') {
        document.body.classList.add('dark-mode');
    }
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
