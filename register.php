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
    <title>Sign Up - Sistem Perpustakaan</title>
    <link rel="stylesheet" href="assets/css/auth.css">
</head>
<body>
    <div class="auth-container">
        <div class="auth-card">
            <h2>Sign Up User Baru</h2>
            
            <?php
            if (isset($_SESSION['error_register'])) {
                echo "<p class='error-message'>" . $_SESSION['error_register'] . "</p>";
                unset($_SESSION['error_register']);
            }
            ?>

            <form id="registerForm">
                <div class="input-group">
                    <label for="nama_lengkap">Nama Lengkap</label>
                    <input type="text" id="nama_lengkap" name="nama_lengkap" required autocomplete="off">
                </div>

                <div class="input-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" required autocomplete="off">
                </div>
                
                <div class="input-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                </div>
                
                <div id="api-message" style="margin-bottom: 15px; font-size: 14px; text-align: center;"></div>

                <button type="submit" class="btn-primary">Sign Up</button>
            </form>

            <script>
                document.getElementById('registerForm').addEventListener('submit', async function(e) {
                    e.preventDefault();
                    
                    const nama_lengkap = document.getElementById('nama_lengkap').value;
                    const username = document.getElementById('username').value;
                    const password = document.getElementById('password').value;
                    const messageDiv = document.getElementById('api-message');
                    
                    messageDiv.textContent = 'Memproses...';
                    messageDiv.style.color = '#475569';

                    try {
                        const response = await fetch('api/auth.php?action=register', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify({ nama_lengkap, username, password })
                        });

                        const result = await response.json();

                        if (result.status === 'success') {
                            messageDiv.textContent = 'Pendaftaran Berhasil! Silakan Sign In.';
                            messageDiv.style.color = '#166534';
                            
                            setTimeout(() => {
                                window.location.href = 'index.php';
                            }, 1500);
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
                Sudah punya akun? <a href="index.php">Sign In di sini</a>
            </p>
        </div>
    </div>
</body>
</html>