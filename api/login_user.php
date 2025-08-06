<?php
header('Content-Type: application/json');

// Cek apakah request menggunakan metode POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid request method.'
    ]);
    exit;
}

// Koneksi database
$host = 'sql101.infinityfree.com';
$dbname = 'simba_db';
$user = 'if0_39634329';
$pass = 'Se7encyber';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $pass);
} catch (PDOException $e) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Database connection failed: ' . $e->getMessage()
    ]);
    exit;
}

// Ambil input dari POST
$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';

// Validasi input
if (empty($username) || empty($password)) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Username dan password harus diisi.'
    ]);
    exit;
}

// Cari user di database
$stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username");
$stmt->execute(['username' => $username]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($user && password_verify($password, $user['password'])) {
    // Login sukses
    echo json_encode([
        'status' => 'success',
        'message' => 'Login berhasil.',
        'user' => [
            'id' => $user['id'],
            'username' => $user['username'],
            'nama_lengkap' => $user['nama_lengkap'],
            'peran' => $user['peran'],
            'notifikasi_peran' => $user['notifikasi_peran'],
            'email' => $user['email'],
            'no_telepon' => $user['no_telepon']
        ]
    ]);
} else {
    // Login gagal
    echo json_encode([
        'status' => 'error',
        'message' => 'Username atau password salah.'
    ]);
}
