<?php
session_start();
require '../config/db.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in.']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_SESSION['user_id'];
    $username = $_POST['username'] ?? '';
    $nama_lengkap = $_POST['nama_lengkap'] ?? '';
    $email = $_POST['email'] ?? '';
    $no_telepon = $_POST['no_telepon'] ?? '';
    $alamat = $_POST['alamat'] ?? '';

    // Validasi input
    if (empty($username) || empty($nama_lengkap)) {
        echo json_encode(['success' => false, 'message' => 'Username dan Nama Lengkap wajib diisi.']);
        exit;
    }

    try {
        $stmt = $pdo->prepare("
            UPDATE users
            SET username = ?, nama_lengkap = ?, email = ?, no_telepon = ?, alamat = ?
            WHERE id = ?
        ");
        $stmt->execute([$username, $nama_lengkap, $email, $no_telepon, $alamat, $user_id]);

        // Perbarui sesi dengan username yang baru jika diubah
        $_SESSION['username'] = $username;

        echo json_encode(['success' => true, 'message' => 'Profil berhasil diperbarui.']);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Gagal memperbarui profil: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Metode request tidak valid.']);
}
