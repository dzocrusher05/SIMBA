<?php
require '../config/db.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama = $_POST['nama_persediaan'];
    $stok = $_POST['stok'];
    $satuan = $_POST['satuan'];

    if (empty($nama) || !isset($stok) || empty($satuan)) {
        echo json_encode(['success' => false, 'message' => 'Semua kolom harus diisi.']);
        exit;
    }

    try {
        $stmt = $pdo->prepare("INSERT INTO persediaan (nama_persediaan, stok, satuan) VALUES (?, ?, ?)");
        $stmt->execute([$nama, $stok, $satuan]);
        echo json_encode(['success' => true]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Gagal menyimpan data.']);
    }
}
