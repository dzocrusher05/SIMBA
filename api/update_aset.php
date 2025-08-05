<?php
require '../config/db.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['edit_aset_id'];
    $no_bmn = $_POST['edit_no_bmn'];
    $nama_bmn = $_POST['edit_nama_bmn'];
    $status = $_POST['edit_status'];

    if (empty($id) || empty($no_bmn) || empty($nama_bmn) || empty($status)) {
        echo json_encode(['success' => false, 'message' => 'Semua kolom harus diisi.']);
        exit;
    }

    try {
        $stmt = $pdo->prepare("UPDATE aset SET no_bmn = ?, nama_bmn = ?, status = ? WHERE id = ?");
        $stmt->execute([$no_bmn, $nama_bmn, $status, $id]);
        echo json_encode(['success' => true]);
    } catch (PDOException $e) {
        if ($e->getCode() == 23000) {
            echo json_encode(['success' => false, 'message' => 'No. BMN sudah digunakan oleh aset lain.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Gagal memperbarui data.']);
        }
    }
}
