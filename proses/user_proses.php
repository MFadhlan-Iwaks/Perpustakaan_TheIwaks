<?php
session_start();
require_once '../config/koneksi.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'petugas') {
    header("Location: ../index.php");
    exit();
}

if (isset($_POST['tambah_user'])) {
    $nama_lengkap = mysqli_real_escape_string($koneksi, $_POST['nama_lengkap']);
    $username = mysqli_real_escape_string($koneksi, $_POST['username']);
    $password = mysqli_real_escape_string($koneksi, $_POST['password']);
    $role = mysqli_real_escape_string($koneksi, $_POST['role']);

    $cek_username = mysqli_query($koneksi, "SELECT username FROM users WHERE username = '$username'");

    if (mysqli_num_rows($cek_username) > 0) {
        $_SESSION['pesan'] = "Gagal! Username '$username' sudah terdaftar. Silakan gunakan username lain.";
    } else {
        $query_insert = mysqli_query($koneksi, "INSERT INTO users (nama_lengkap, username, password, role) VALUES ('$nama_lengkap', '$username', '$password', '$role')");

        if ($query_insert) {
            $_SESSION['pesan'] = "Pengguna '$nama_lengkap' berhasil didaftarkan!";
        } else {
            $_SESSION['pesan'] = "Terjadi kesalahan sistem, gagal menyimpan pengguna.";
        }
    }

    header("Location: ../kelola_user.php");
    exit();
}

if (isset($_GET['hapus'])) {
    $id_user = $_GET['hapus'];

    if ($id_user == $_SESSION['id_user']) {
        $_SESSION['pesan'] = "Aksi ditolak! Anda tidak dapat menghapus akun Anda sendiri.";
        header("Location: ../kelola_user.php");
        exit();
    }

    $query = mysqli_query($koneksi, "DELETE FROM users WHERE id_user = '$id_user'");

    if ($query) {
        $_SESSION['pesan'] = "Pengguna berhasil dihapus!";
    } else {
        $_SESSION['pesan'] = "Gagal menghapus pengguna.";
    }

    header("Location: ../kelola_user.php");
    exit();
}
?>