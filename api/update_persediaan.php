<?php
require '../config/db.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['edit_id'];
    $nama = $_POST['edit_nama_persediaan'];
    $stok = $_POST['edit_stok'];
    $satuan = $_POST['edit_satuan'];

    if (empty($id) || empty($nama) || !isset($stok) || empty($satuan)) {
        echo json_encode(['success' => false, 'message' => 'Semua kolom harus diisi.']);
        exit;
    }

    try {
        $stmt = $pdo->prepare("UPDATE persediaan SET nama_persediaan = ?, stok = ?, satuan = ? WHERE id = ?");
        $stmt->execute([$nama, $stok, $satuan, $id]);
        echo json_encode(['success' => true]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Gagal memperbarui data.']);
    }
}
