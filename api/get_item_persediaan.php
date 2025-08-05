<?php
require '../config/db.php';
header('Content-Type: application/json');

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $stmt = $pdo->prepare("SELECT * FROM persediaan WHERE id = ?");
    $stmt->execute([$id]);
    $item = $stmt->fetch(PDO::FETCH_ASSOC);
    echo json_encode($item ? ['success' => true, 'data' => $item] : ['success' => false, 'message' => 'Data tidak ditemukan.']);
}
