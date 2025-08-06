<?php
require '../config/db.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Metode request tidak valid.']);
    exit;
}

$id = $_POST['id'] ?? null;
$status_laporan = $_POST['status_laporan'] ?? 'diajukan';
$deskripsi_pekerjaan = $_POST['deskripsi_pekerjaan'] ?? null;
$prakiraan_selesai = $_POST['prakiraan_selesai'] ?? null;

if (empty($id)) {
    echo json_encode(['success' => false, 'message' => 'ID tidak valid.']);
    exit;
}

try {
    $stmt = $pdo->prepare("UPDATE pengaduan_kerusakan SET status_laporan = ?, deskripsi_pekerjaan = ?, prakiraan_selesai = ? WHERE id = ?");
    $stmt->execute([$status_laporan, $deskripsi_pekerjaan, $prakiraan_selesai, $id]);

    echo json_encode(['success' => true, 'message' => 'Status pengaduan berhasil diperbarui.']);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Gagal memperbarui status: ' . $e->getMessage()]);
}
