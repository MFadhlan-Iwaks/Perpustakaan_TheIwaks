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

            <form action="proses/auth_proses.php" method="POST">
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
                
                <button type="submit" name="register_submit" class="btn-primary">Sign Up</button>
            </form>

            <p class="auth-footer">
                Sudah punya akun? <a href="index.php">Sign In di sini</a>
            </p>
        </div>
    </div>
</body>
</html>