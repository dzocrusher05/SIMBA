<?php
require '../config/db.php';
header('Content-Type: application/json');
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $pengaturan = $_POST['pengaturan'];
    $pdo->beginTransaction();
    try {
        $stmt = $pdo->prepare("UPDATE pengaturan SET nilai_pengaturan = ? WHERE nama_pengaturan = ?");
        foreach ($pengaturan as $nama => $nilai) {
            $stmt->execute([$nilai, $nama]);
        }
        $pdo->commit();
        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        $pdo->rollBack();
        echo json_encode(['success' => false, 'message' => 'Gagal menyimpan.']);
    }
}
