<?php
require '../config/db.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $peran = $_POST['peran'];
    $notifikasi_peran = $_POST['notifikasi_peran'];

    if (empty($username) || empty($password) || empty($peran)) {
        echo json_encode(['success' => false, 'message' => 'Semua kolom harus diisi.']);
        exit;
    }

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    try {
        $stmt = $pdo->prepare("INSERT INTO users (username, password, peran, notifikasi_peran) VALUES (?, ?, ?, ?)");
        $stmt->execute([$username, $hashed_password, $peran, $notifikasi_peran]);
        echo json_encode(['success' => true, 'message' => 'Pengguna berhasil ditambahkan!']);
    } catch (PDOException $e) {
        if ($e->getCode() == 23000) {
            echo json_encode(['success' => false, 'message' => 'Username sudah ada.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Gagal menambahkan pengguna.']);
        }
    }
}
