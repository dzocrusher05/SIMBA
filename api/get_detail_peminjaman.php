<?php
header('Content-Type: application/json');
require '../config/db.php'; // Menggunakan koneksi PDO

// Pastikan ID peminjaman dikirim melalui GET
if (!isset($_GET['id'])) {
    echo json_encode(['success' => false, 'message' => 'ID Peminjaman tidak ditemukan.']);
    exit;
}

$peminjaman_id = (int)$_GET['id'];

try {
    // 1. Ambil data utama dari tabel peminjaman
    $stmt_main = $pdo->prepare("SELECT * FROM peminjaman WHERE id = ?");
    $stmt_main->execute([$peminjaman_id]);
    $peminjaman_data = $stmt_main->fetch(PDO::FETCH_ASSOC);

    if (!$peminjaman_data) {
        echo json_encode(['success' => false, 'message' => 'Data peminjaman tidak ditemukan.']);
        exit;
    }

    // 2. Ambil semua aset yang terkait dengan peminjaman ini
    $stmt_items = $pdo->prepare("
        SELECT a.id, a.nama_bmn, a.kode_bmn as no_bmn
        FROM detail_peminjaman dp
        JOIN aset a ON dp.aset_id = a.id
        WHERE dp.peminjaman_id = ?
    ");
    $stmt_items->execute([$peminjaman_id]);
    $items = $stmt_items->fetchAll(PDO::FETCH_ASSOC);

    // Gabungkan data utama dengan daftar item/aset
    $peminjaman_data['items'] = $items;

    // Kirim respons sukses
    echo json_encode([
        'success' => true,
        'data' => $peminjaman_data
    ]);
} catch (PDOException $e) {
    // Kirim pesan error jika query gagal
    echo json_encode([
        'success' => false,
        'message' => 'Gagal mengambil detail dari database: ' . $e->getMessage()
    ]);
}
