<?php
session_start();

if (isset($_SESSION['role'])) {
    if ($_SESSION['role'] == 'petugas') {
        header("Location: petugas_dashboard.php");
        exit();
    } else {
        header("Location: user_dashboard.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In - Sistem Perpustakaan</title>
    <link rel="stylesheet" href="assets/css/auth.css">
</head>

<body>
    <div class="auth-container">
        <div class="auth-card">
            <h2>Sign In Perpustakaan</h2>

            <div id="alert-box" style="display: none;"></div>

            <form id="login-form">
                <div class="input-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" required autocomplete="off">
                </div>

                <div class="input-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                </div>

                <button type="submit" class="btn-primary">Sign In</button>
            </form>

            <p class="auth-footer">
                Belum punya akun? <a href="register.php">Sign Up di sini</a>
            </p>
        </div>
    </div>

    <script>
        document.getElementById('login-form').addEventListener('submit', async function (e) {
            e.preventDefault(); // Mencegah reload halaman
            
            const data = Object.fromEntries(new FormData(this).entries());
            const alertBox = document.getElementById('alert-box');

            try {
                const response = await fetch('api/auth.php?action=login', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(data)
                });
                
                const result = await response.json();

                if (result.status === 'success') {
                    // Redirect sesuai role
                    if(result.data.role === 'petugas') {
                        window.location.href = 'petugas_dashboard.php';
                    } else {
                        window.location.href = 'user_dashboard.php';
                    }
                } else {
                    // Tampilkan error dengan gaya CSS lamamu
                    alertBox.style.display = 'block';
                    alertBox.className = 'error-message';
                    alertBox.innerText = result.message;
                }
            } catch (error) {
                alertBox.style.display = 'block';
                alertBox.className = 'error-message';
                alertBox.innerText = 'Gagal terhubung ke server.';
            }
        });
    </script>
</body>

</html>