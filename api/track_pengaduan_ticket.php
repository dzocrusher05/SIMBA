<?php
session_start();
require '../config/db.php';
header('Content-Type: application/json');

$ticket_number = $_GET['ticket_number'] ?? null;
if (!$ticket_number) {
    echo json_encode(['success' => false, 'message' => 'Nomor tiket tidak valid.']);
    exit;
}

try {
    $sql = "SELECT t.ticket_code, pk.status_laporan, pk.deskripsi_pekerjaan, pk.prakiraan_selesai, a.nama_bmn, a.no_bmn, pk.deskripsi
            FROM pengaduan_kerusakan pk
            JOIN tickets t ON pk.ticket_id = t.id
            JOIN aset a ON pk.aset_id = a.id
            WHERE t.ticket_code = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$ticket_number]);
    $data = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($data) {
        echo json_encode(['success' => true, 'data' => $data]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Nomor tiket tidak ditemukan.']);
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Gagal melacak tiket.']);
}
