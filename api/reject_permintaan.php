<?php
require '../config/db.php';
require '../includes/fonnte_helper.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id'])) {
    $permintaan_id = $_POST['id'];
    try {
        // Cek status permintaan sebelum melakukan update
        $stmt_check = $pdo->prepare("SELECT status_permintaan FROM permintaan_persediaan WHERE id = ?");
        $stmt_check->execute([$permintaan_id]);
        $current_status = $stmt_check->fetchColumn();

        if ($current_status === 'Diajukan') {
            // Lanjutkan jika status masih 'Diajukan'
            // Cukup update status, tidak ada perubahan stok
            $stmt = $pdo->prepare("UPDATE permintaan_persediaan SET status_permintaan = 'Ditolak' WHERE id = ?");
            $stmt->execute([$permintaan_id]);

            // Kirim notifikasi penolakan ke pemohon
            $stmt_pemohon = $pdo->prepare("SELECT nomor_telepon_pemohon, nama_pemohon FROM permintaan_persediaan WHERE id = ?");
            $stmt_pemohon->execute([$permintaan_id]);
            $pemohon = $stmt_pemohon->fetch(PDO::FETCH_ASSOC);

            if ($pemohon && $pemohon['nomor_telepon_pemohon']) {
                $messageToUser = "❌ *Notifikasi SIMBA* ❌\n\nHai *{$pemohon['nama_pemohon']}*,\n\nMohon maaf, permintaan persediaan Anda dengan terpaksa harus kami *TOLAK* saat ini. Untuk informasi lebih lanjut, silakan hubungi pengelola aset.\n\nTerima kasih.";
                sendWhatsApp($pemohon['nomor_telepon_pemohon'], $messageToUser);
            }
        }
        echo json_encode(['success' => true]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Gagal menolak permintaan.']);
    }
}
