<?php
require '../config/db.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $no_bmn = $_POST['no_bmn'];
    $nama_bmn = $_POST['nama_bmn'];

    if (empty($no_bmn) || empty($nama_bmn)) {
        echo json_encode(['success' => false, 'message' => 'Semua kolom harus diisi.']);
        exit;
    }

    try {
        $stmt = $pdo->prepare("INSERT INTO aset (no_bmn, nama_bmn, status) VALUES (?, ?, 'Tersedia')");
        $stmt->execute([$no_bmn, $nama_bmn]);
        echo json_encode(['success' => true]);
    } catch (PDOException $e) {
        // Cek jika error adalah duplicate entry
        if ($e->getCode() == 23000) {
            echo json_encode(['success' => false, 'message' => 'No. BMN sudah ada.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Gagal menyimpan data ke database.']);
        }
    }
}
