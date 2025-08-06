<?php
require '../config/db.php';
header('Content-Type: application/json');

try {
    $stmt = $pdo->query("SELECT id, nama_bmn, kode_bmn, nup, merek FROM aset WHERE status = 'Tersedia' ORDER BY nama_bmn ASC");
    $asets = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['success' => true, 'data' => $asets]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Gagal mengambil data aset yang tersedia dari database: ' . $e->getMessage()]);
}
