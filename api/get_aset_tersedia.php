<?php
require '../config/db.php';
$stmt = $pdo->query("SELECT id, nama_bmn, no_bmn FROM aset WHERE status = 'Tersedia' ORDER BY nama_bmn ASC");
$asets = $stmt->fetchAll(PDO::FETCH_ASSOC);

header('Content-Type: application/json');
echo json_encode($asets);
