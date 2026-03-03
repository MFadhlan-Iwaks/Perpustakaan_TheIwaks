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

            <?php
            if (isset($_SESSION['error_login'])) {
                echo "<p class='error-message'>" . $_SESSION['error_login'] . "</p>";
                unset($_SESSION['error_login']);
            }
            ?>

            <form id="loginForm">
                <div class="input-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" required autocomplete="off">
                </div>

                <div class="input-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                </div>

                <div id="api-message" style="margin-bottom: 15px; font-size: 14px; text-align: center;"></div>

                <button type="submit" class="btn-primary">Sign In</button>
            </form>

            <script>
                document.getElementById('loginForm').addEventListener('submit', async function(e) {
                    e.preventDefault();
                    
                    const username = document.getElementById('username').value;
                    const password = document.getElementById('password').value;
                    const messageDiv = document.getElementById('api-message');
                    
                    messageDiv.textContent = 'Memproses...';
                    messageDiv.style.color = '#475569';

                    try {
                        const response = await fetch('api/auth.php?action=login', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify({ username, password })
                        });

                        const result = await response.json();

                        if (result.status === 'success') {
                            messageDiv.textContent = 'Login Berhasil! Mengalihkan...';
                            messageDiv.style.color = '#166534';
                            
                            setTimeout(() => {
                                if (result.data.role === 'petugas') {
                                    window.location.href = 'petugas_dashboard.php';
                                } else {
                                    window.location.href = 'user_dashboard.php';
                                }
                            }, 1000);
                        } else {
                            messageDiv.textContent = result.message;
                            messageDiv.style.color = '#991b1b';
                        }
                    } catch (error) {
                        messageDiv.textContent = 'Terjadi kesalahan pada server.';
                        messageDiv.style.color = '#991b1b';
                        console.error('Error:', error);
                    }
                });
            </script>

            <p class="auth-footer">
                Belum punya akun? <a href="register.php">Sign Up di sini</a>
            </p>
        </div>
    </div>
</body>

</html>