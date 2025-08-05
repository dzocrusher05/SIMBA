<?php
require '../config/db.php';
header('Content-Type: application/json');
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id'])) {
    $peminjaman_id = $_POST['id'];
    $pdo->beginTransaction();
    try {
        $stmt1 = $pdo->prepare("UPDATE peminjaman SET status_peminjaman = 'Dikembalikan', tanggal_kembali = CURDATE() WHERE id = ?");
        $stmt1->execute([$peminjaman_id]);
        $sql_update_aset = "UPDATE aset SET status = 'Tersedia' WHERE id IN (SELECT aset_id FROM detail_peminjaman WHERE peminjaman_id = ?)";
        $stmt2 = $pdo->prepare($sql_update_aset);
        $stmt2->execute([$peminjaman_id]);
        $pdo->commit();
        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        $pdo->rollBack();
        echo json_encode(['success' => false, 'message' => 'Gagal memproses pengembalian.']);
    }
}
