<?php
require '../config/db.php';
header('Content-Type: application/json');
$stmt = $pdo->query("SELECT nama_pengaturan, nilai_pengaturan FROM pengaturan");
$pengaturan = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
echo json_encode(['success' => true, 'data' => $pengaturan]);
