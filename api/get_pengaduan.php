<?php
require '../config/db.php';
header('Content-Type: application/json');

$search = $_GET['search'] ?? '';

$sql = "SELECT pk.id, a.nama_bmn, a.no_bmn, pk.deskripsi, pk.tanggal_lapor, pk.status_laporan
        FROM pengaduan_kerusakan pk
        JOIN aset a ON pk.aset_id = a.id";

if (!empty($search)) {
    $sql .= " WHERE a.nama_bmn LIKE :search OR pk.deskripsi LIKE :search";
}

$sql .= " ORDER BY pk.tanggal_lapor DESC";

try {
    $stmt = $pdo->prepare($sql);
    if (!empty($search)) {
        $search_param = "%$search%";
        $stmt->bindParam(':search', $search_param);
    }
    $stmt->execute();
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode(['success' => true, 'data' => $data]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Gagal mengambil data pengaduan.']);
}
