<?php
session_start();
require '../config/db.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Silakan login terlebih dahulu.']);
    exit;
}

$user_id = $_SESSION['user_id'];

try {
    $sql = "SELECT pk.id, a.nama_bmn, a.no_bmn, pk.deskripsi, pk.tanggal_lapor, pk.status_laporan
            FROM pengaduan_kerusakan pk
            JOIN aset a ON pk.aset_id = a.id
            WHERE pk.user_id = ?
            ORDER BY pk.tanggal_lapor DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$user_id]);
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['success' => true, 'data' => $data]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Gagal mengambil data pengaduan.']);
}
