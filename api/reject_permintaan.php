<?php
require '../config/db.php';
require '../includes/fonnte_helper.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id'])) {
    $permintaan_id = $_POST['id'];
    try {
        // Cukup update status, tidak ada perubahan stok
        $stmt = $pdo->prepare("UPDATE permintaan_persediaan SET status_permintaan = 'Ditolak' WHERE id = ?");
        $stmt->execute([$permintaan_id]);

        // Kirim notifikasi penolakan ke pemohon (jika perlu)

        echo json_encode(['success' => true]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Gagal menolak permintaan.']);
    }
}
