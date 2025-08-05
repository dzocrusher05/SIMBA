<?php
require '../config/db.php';
header('Content-Type: application/json');

if (!isset($_GET['id'])) {
    echo json_encode(['success' => false, 'message' => 'ID Persediaan tidak disediakan.']);
    exit;
}

$persediaan_id = $_GET['id'];

try {
    $stmt = $pdo->prepare("
        SELECT id, tanggal, jenis_transaksi, jumlah, keterangan, nomor_dokumen
        FROM riwayat_persediaan
        WHERE persediaan_id = ?
        ORDER BY tanggal DESC
    ");
    $stmt->execute([$persediaan_id]);
    $riwayat = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['success' => true, 'data' => $riwayat]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Gagal mengambil data riwayat.']);
}
