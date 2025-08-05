<?php
require '../config/db.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $persediaan_id = $_POST['input_masuk_persediaan_id'];
    $jumlah = $_POST['jumlah'];
    $keterangan = $_POST['keterangan'] ?? null;
    $nomor_dokumen = $_POST['nomor_dokumen'] ?? null;
    $jenis_transaksi = $_POST['jenis_transaksi'];

    if (empty($persediaan_id) || empty($jumlah) || empty($jenis_transaksi)) {
        echo json_encode(['success' => false, 'message' => 'Data tidak lengkap.']);
        exit;
    }

    $pdo->beginTransaction();
    try {
        // Tambahkan ke riwayat
        $stmt_riwayat = $pdo->prepare("INSERT INTO riwayat_persediaan (persediaan_id, tanggal, jenis_transaksi, jumlah, keterangan, nomor_dokumen) VALUES (?, CURDATE(), ?, ?, ?, ?)");
        $stmt_riwayat->execute([$persediaan_id, $jenis_transaksi, $jumlah, $keterangan, $nomor_dokumen]);

        // Perbarui stok persediaan
        $stmt_update_stok = $pdo->prepare("UPDATE persediaan SET stok = stok + ? WHERE id = ?");
        $stmt_update_stok->execute([$jumlah, $persediaan_id]);

        $pdo->commit();
        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        $pdo->rollBack();
        echo json_encode(['success' => false, 'message' => 'Gagal mencatat barang masuk.']);
    }
}
