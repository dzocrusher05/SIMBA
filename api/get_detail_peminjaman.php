<?php
require '../config/db.php';
header('Content-Type: application/json');

if (!isset($_GET['id'])) {
    echo json_encode(['success' => false, 'message' => 'ID Peminjaman tidak disediakan.']);
    exit;
}

$peminjaman_id = $_GET['id'];

try {
    // Ambil data peminjaman utama
    $stmt_main = $pdo->prepare("SELECT * FROM peminjaman WHERE id = ?");
    $stmt_main->execute([$peminjaman_id]);
    $peminjaman = $stmt_main->fetch(PDO::FETCH_ASSOC);

    if (!$peminjaman) {
        echo json_encode(['success' => false, 'message' => 'Data peminjaman tidak ditemukan.']);
        exit;
    }

    // Ambil detail aset yang dipinjam
    $stmt_items = $pdo->prepare("
        SELECT a.nama_bmn, a.no_bmn, a.id as aset_id
        FROM detail_peminjaman dp
        JOIN aset a ON dp.aset_id = a.id
        WHERE dp.peminjaman_id = ?
    ");
    $stmt_items->execute([$peminjaman_id]);
    $items = $stmt_items->fetchAll(PDO::FETCH_ASSOC);

    $peminjaman['items'] = $items;
    echo json_encode(['success' => true, 'data' => $peminjaman]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Gagal mengambil detail data.']);
}
