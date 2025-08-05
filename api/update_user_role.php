<?php
require '../config/db.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['user_id'];
    $peran = $_POST['peran'];
    $notifikasi_peran = $_POST['notifikasi_peran'];

    if (empty($id) || empty($peran) || empty($notifikasi_peran)) {
        echo json_encode(['success' => false, 'message' => 'Semua kolom harus diisi.']);
        exit;
    }

    try {
        $stmt = $pdo->prepare("UPDATE users SET peran = ?, notifikasi_peran = ? WHERE id = ?");
        $stmt->execute([$peran, $notifikasi_peran, $id]);
        echo json_encode(['success' => true]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Gagal memperbarui data.']);
    }
}
