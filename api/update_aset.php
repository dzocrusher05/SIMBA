<?php
require '../config/db.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['edit_aset_id'];
    $kode_bmn = $_POST['edit_kode_bmn'];
    $nup = $_POST['edit_nup'] ?? null;
    $nama_bmn = $_POST['edit_nama_bmn'];
    $merek = $_POST['edit_merek'] ?? null;
    $status = $_POST['edit_status'];

    if (empty($id) || empty($kode_bmn) || empty($nup) || empty($nama_bmn) || empty($merek) || empty($status)) {
        echo json_encode(['success' => false, 'message' => 'Semua kolom harus diisi.']);
        exit;
    }

    try {
        $stmt = $pdo->prepare("UPDATE aset SET kode_bmn = ?, nup = ?, nama_bmn = ?, merek = ?, status = ? WHERE id = ?");
        $stmt->execute([$kode_bmn, $nup, $nama_bmn, $merek, $status, $id]);
        echo json_encode(['success' => true]);
    } catch (PDOException $e) {
        if ($e->getCode() == 23000) {
            echo json_encode(['success' => false, 'message' => 'Kode BMN atau NUP sudah digunakan oleh aset lain.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Gagal memperbarui data.']);
        }
    }
}
