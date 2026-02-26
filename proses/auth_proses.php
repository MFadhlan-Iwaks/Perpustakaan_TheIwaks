<?php
session_start();
require_once '../config/koneksi.php';

if (isset($_POST['register_submit'])) {
    $nama_lengkap = mysqli_real_escape_string($koneksi, $_POST['nama_lengkap']);
    $username = mysqli_real_escape_string($koneksi, $_POST['username']);
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $role = 'user';

    $cek_user = mysqli_query($koneksi, "SELECT * FROM users WHERE username = '$username'");
    if (mysqli_num_rows($cek_user) > 0) {
        $_SESSION['error_register'] = "Username sudah digunakan, silakan cari yang lain!";
        header("Location: ../register.php");
        exit();
    }

    $query_insert = "INSERT INTO users (username, password, nama_lengkap, role) 
                     VALUES ('$username', '$password', '$nama_lengkap', '$role')";

    if (mysqli_query($koneksi, $query_insert)) {
        $_SESSION['error_login'] = "Akun berhasil dibuat! Silakan Sign In.";
        header("Location: ../index.php");
        exit();
    } else {
        $_SESSION['error_register'] = "Terjadi kesalahan pada server.";
        header("Location: ../register.php");
        exit();
    }
}

if (isset($_POST['login_submit'])) {
    $username = mysqli_real_escape_string($koneksi, $_POST['username']);
    $password = $_POST['password'];
    $query = mysqli_query($koneksi, "SELECT * FROM users WHERE username = '$username'");

    if (mysqli_num_rows($query) === 1) {
        $data_user = mysqli_fetch_assoc($query);

        if (password_verify($password, $data_user['password'])) {
            $_SESSION['id_user'] = $data_user['id_user'];
            $_SESSION['username'] = $data_user['username'];
            $_SESSION['role'] = $data_user['role'];

            if ($data_user['role'] == 'petugas') {
                header("Location: ../petugas_dashboard.php");
            } else {
                header("Location: ../user_dashboard.php");
            }
            exit();
        } else {
            $_SESSION['error_login'] = "Password salah!";
            header("Location: ../index.php");
            exit();
        }
    } else {
        $_SESSION['error_login'] = "Username tidak terdaftar!";
        header("Location: ../index.php");
        exit();
    }
}
?>