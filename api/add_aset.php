<?php
require '../config/db.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $kode_bmn = $_POST['kode_bmn'];
    $nup = $_POST['nup'] ?? null;
    $nama_bmn = $_POST['nama_bmn'];
    $merek = $_POST['merek'] ?? null;

    if (empty($kode_bmn) || empty($nup) || empty($nama_bmn) || empty($merek)) {
        echo json_encode(['success' => false, 'message' => 'Semua kolom harus diisi.']);
        exit;
    }

    try {
        $stmt = $pdo->prepare("INSERT INTO aset (kode_bmn, nup, nama_bmn, merek, status) VALUES (?, ?, ?, ?, 'Tersedia')");
        $stmt->execute([$kode_bmn, $nup, $nama_bmn, $merek]);
        echo json_encode(['success' => true]);
    } catch (PDOException $e) {
        // Cek jika error adalah duplicate entry
        if ($e->getCode() == 23000) {
            echo json_encode(['success' => false, 'message' => 'Kode BMN atau NUP sudah digunakan oleh aset lain.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Gagal menyimpan data ke database.']);
        }
    }
}
