<?php

/**
 * Konfigurasi koneksi database menggunakan PDO (PHP Data Objects)
 */

// --- SESUAIKAN BAGIAN INI ---
$host   = 'localhost';
$dbname = 'simba_db'; // Nama database
$user   = 'root';
$pass   = '';         // Default XAMPP kosong
$charset = 'utf8mb4';
// ----------------------------

// Data Source Name (DSN)
$dsn = "mysql:host=$host;dbname=$dbname;charset=$charset";

// Opsi untuk koneksi PDO
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Mengaktifkan mode error exception
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // Mengatur mode fetch default ke associative array
    PDO::ATTR_EMULATE_PREPARES   => false,                  // Menonaktifkan emulasi prepared statements
];

// Membuat instance PDO baru
try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    // Jika koneksi gagal, kirimkan output JSON agar bisa ditangani di frontend
    header('Content-Type: application/json');
    echo json_encode([
        'status' => 'error',
        'message' => 'Koneksi ke database gagal: ' . $e->getMessage()
    ]);
    exit(); // Hentikan script
}
