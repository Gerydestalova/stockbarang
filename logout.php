<?php
session_start();

// Cek role pengguna
if (isset($_SESSION['admin_login'])) {
    unset($_SESSION['admin_login']);
} elseif (isset($_SESSION['owner_login'])) {
    unset($_SESSION['owner_login']);
}

// Jika tidak ada sesi lain yang aktif, hancurkan sesi total
if (empty($_SESSION)) {
    session_destroy();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="refresh" content="3;url=login.php"> <!-- Redirect otomatis -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logout - Matahari Motor Group</title>
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
            margin: 0;
            padding: 0;
            height: 100vh;
            background: linear-gradient(135deg, #1d2b64, #f8cdda);
            display: flex;
            justify-content: center;
            align-items: center;
            font-family: 'Segoe UI', sans-serif;
            transition: all 0.3s ease;
        }

        body.dark-mode {
            background: linear-gradient(135deg, #34495e, #2c3e50);
            color: var(--text-dark);
        }

        .logout-box {
            background-color: var(--bg-light);
            padding: 2rem 3rem;
            border-radius: 1rem;
            box-shadow: 0 8px 24px var(--shadow-color);
            text-align: center;
            animation: fadeIn 1s ease-out;
        }

        body.dark-mode .logout-box {
            background-color: var(--bg-dark);
            color: var(--text-dark);
        }

        .logout-box h1 {
            margin-bottom: 1rem;
            font-size: 2rem;
            color: var(--primary-color);
            font-weight: 700;
        }

        .logout-box p {
            font-size: 1rem;
            margin-bottom: 1.5rem;
            color: #555;
        }

        body.dark-mode .logout-box p {
            color: var(--text-dark);
        }

        .logout-box a {
            display: inline-block;
            text-decoration: none;
            background-color: var(--primary-color);
            color: #fff;
            padding: 0.5rem 1.2rem;
            border-radius: 0.5rem;
            font-weight: 600;
            transition: background-color 0.3s ease, transform 0.2s ease;
        }

        .logout-box a:hover {
            background-color: var(--secondary-color);
            transform: scale(1.05);
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>

<div class="logout-box">
    <h1>Logout Berhasil</h1>
    <p>Anda telah keluar dari sistem.</p>
    <p>Anda akan diarahkan ke halaman login...</p>
    <a href="login.php">Kembali ke Login</a>
</div>

<script>
    // Enable dark mode if previously active
    if (localStorage.getItem('darkMode') === 'enabled') {
        document.body.classList.add('dark-mode');
    }
</script>
</body>
</html>
