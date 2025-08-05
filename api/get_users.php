<?php
require '../config/db.php';
header('Content-Type: application/json');

try {
    $stmt = $pdo->query("SELECT id, username, peran, notifikasi_peran FROM users ORDER BY id ASC");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode(['success' => true, 'data' => $users]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Gagal mengambil data pengguna.']);
}
