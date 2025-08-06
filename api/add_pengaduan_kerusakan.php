<?php
session_start();
require '../config/db.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Metode request tidak valid.']);
    exit;
}

$nama_pelapor = $_POST['nama_pelapor'] ?? null;
$nomor_telepon_pelapor = $_POST['nomor_telepon_pelapor'] ?? null;
$aset_id = $_POST['aset_id'] ?? null;
$deskripsi = $_POST['deskripsi'] ?? '';
$tanda_tangan_pelapor = $_POST['tanda_tangan_pelapor'] ?? null;

if (empty($nama_pelapor) || empty($nomor_telepon_pelapor) || empty($aset_id) || empty($deskripsi) || empty($tanda_tangan_pelapor)) {
    echo json_encode(['success' => false, 'message' => 'Semua kolom wajib diisi.']);
    exit;
}

$gambar_bukti = null;
if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] === UPLOAD_ERR_OK) {
    $uploadDir = '../uploads/pengaduan/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }
    $fileName = uniqid() . '_' . basename($_FILES['gambar']['name']);
    $filePath = $uploadDir . $fileName;
    if (move_uploaded_file($_FILES['gambar']['tmp_name'], $filePath)) {
        $gambar_bukti = 'uploads/pengaduan/' . $fileName;
    }
}

try {
    $pdo->beginTransaction();

    $stmt_nomor = $pdo->query("SELECT pengaduan FROM nomor FOR UPDATE");
    $nomor_terakhir = (int)($stmt_nomor->fetchColumn() ?? 0);
    $nomor_berikutnya = $nomor_terakhir + 1;
    $ticket_code = "TICKET-ADUAN-" . $nomor_berikutnya;

    $stmt = $pdo->prepare("INSERT INTO pengaduan_kerusakan (aset_id, user_id, nama_pelapor, nomor_telepon_pelapor, tanggal_lapor, deskripsi, gambar_bukti, tanda_tangan_pelapor, status_laporan) VALUES (?, ?, ?, ?, CURDATE(), ?, ?, ?, 'diajukan')");
    $user_id = $_SESSION['user_id'] ?? null;
    $stmt->execute([$aset_id, $user_id, $nama_pelapor, $nomor_telepon_pelapor, $deskripsi, $gambar_bukti, $tanda_tangan_pelapor]);
    $pengaduan_id = $pdo->lastInsertId();

    $stmt_ticket = $pdo->prepare("INSERT INTO tickets (user_id, request_id, request_type, ticket_code, status) VALUES (?, ?, 'pengaduan', ?, 'diajukan')");
    $stmt_ticket->execute([$user_id, $pengaduan_id, $ticket_code]);
    $ticket_id = $pdo->lastInsertId();

    $stmt_update_pengaduan = $pdo->prepare("UPDATE pengaduan_kerusakan SET ticket_id = ? WHERE id = ?");
    $stmt_update_pengaduan->execute([$ticket_id, $pengaduan_id]);

    $stmt_update_nomor = $pdo->prepare("UPDATE nomor SET pengaduan = ?");
    $stmt_update_nomor->execute([$nomor_berikutnya]);

    $pdo->commit();

    echo json_encode(['success' => true, 'message' => 'Pengaduan berhasil dikirim. Nomor tiket Anda: ' . $ticket_code]);
} catch (PDOException $e) {
    $pdo->rollBack();
    echo json_encode(['success' => false, 'message' => 'Gagal mengirim pengaduan: ' . $e->getMessage()]);
}
