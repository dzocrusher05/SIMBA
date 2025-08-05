<?php
// Pengaturan untuk koneksi database
$host = 'sql101.infinityfree.com';
$dbname = 'if0_39634329_simba_db';
$user = 'if0_39634329';
$pass = 'Se7encyber'; // Biasanya kosong jika menggunakan XAMPP default

// Membuat koneksi menggunakan PDO untuk keamanan
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $pass);
    // Mengatur mode error PDO ke exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // Menampilkan pesan error jika koneksi gagal
    die("Koneksi ke database gagal: " . $e->getMessage());
}
