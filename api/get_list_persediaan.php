<?php
require '../config/db.php';
$stmt = $pdo->query("SELECT id, nama_persediaan, stok, satuan FROM persediaan WHERE stok > 0 ORDER BY nama_persediaan ASC");
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);

header('Content-Type: application/json');
echo json_encode($items);
