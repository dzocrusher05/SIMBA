<?php
require '../config/db.php';
header('Content-Type: application/json');

if (!isset($_GET['id'])) {
    echo json_encode(['success' => false, 'message' => 'ID Permintaan tidak disediakan.']);
    exit;
}

$permintaan_id = $_GET['id'];

try {
    // Ambil semua data termasuk nomor surat dan tanda tangan
    $stmt_main = $pdo->prepare("SELECT *, nomor_spb, nomor_sbbk FROM permintaan_persediaan WHERE id = ?");
    $stmt_main->execute([$permintaan_id]);
    $permintaan = $stmt_main->fetch(PDO::FETCH_ASSOC);

    if (!$permintaan) {
        echo json_encode(['success' => false, 'message' => 'Data permintaan tidak ditemukan.']);
        exit;
    }

    // Ambil detail item yang diminta
    $stmt_items = $pdo->prepare("
        SELECT dp.jumlah_diminta, p.satuan, p.nama_persediaan, p.id as persediaan_id
        FROM detail_permintaan dp
        JOIN persediaan p ON dp.persediaan_id = p.id
        WHERE dp.permintaan_id = ?
    ");
    $stmt_items->execute([$permintaan_id]);
    $items = $stmt_items->fetchAll(PDO::FETCH_ASSOC);

    $permintaan['items'] = $items;
    echo json_encode(['success' => true, 'data' => $permintaan]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Gagal mengambil detail data.']);
}
