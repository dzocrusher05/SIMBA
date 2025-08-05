<?php
require '../config/db.php';
header('Content-Type: application/json');

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $stmt = $pdo->prepare("SELECT * FROM aset WHERE id = ?");
    $stmt->execute([$id]);
    $aset = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($aset) {
        echo json_encode(['success' => true, 'data' => $aset]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Aset tidak ditemukan.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'ID tidak disediakan.']);
}
