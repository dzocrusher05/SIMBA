<?php
require '../config/db.php';
header('Content-Type: application/json');

$id = $_GET['id'] ?? null;
if (!$id) {
    echo json_encode(['success' => false, 'message' => 'ID tidak valid.']);
    exit;
}

try {
    $sql = "SELECT pk.*, a.nama_bmn, a.no_bmn, u.username
            FROM pengaduan_kerusakan pk
            JOIN aset a ON pk.aset_id = a.id
            JOIN users u ON pk.user_id = u.id
            WHERE pk.id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id]);
    $data = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($data) {
        echo json_encode(['success' => true, 'data' => $data]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Pengaduan tidak ditemukan.']);
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Gagal mengambil detail pengaduan.']);
}
