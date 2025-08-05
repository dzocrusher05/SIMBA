<?php
// Memulai session
session_start();

// Memanggil file koneksi database
require '../config/db.php';

// Memastikan request adalah POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Mencari user di database
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    // Memverifikasi user dan password
    // password_verify() membandingkan password input dengan hash di database
    if ($user && password_verify($password, $user['password'])) {
        // Jika berhasil, simpan data user ke session
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['peran'];

        // Arahkan ke halaman utama (dashboard)
        header("Location: ../index.php");
        exit();
    } else {
        // Jika gagal, kembali ke halaman login dengan pesan error
        header("Location: ../login.php?error=1");
        exit();
    }
} else {
    // Jika bukan POST, tendang kembali ke login
    header("Location: ../login.php");
    exit();
}
