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

            <form action="proses/auth_proses.php" method="POST">
                <div class="input-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" required autocomplete="off">
                </div>

                <div class="input-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                </div>

                <button type="submit" name="login_submit" class="btn-primary">Sign In</button>
            </form>

            <p class="auth-footer">
                Belum punya akun? <a href="register.php">Sign Up di sini</a>
            </p>
        </div>
    </div>
</body>

</html>