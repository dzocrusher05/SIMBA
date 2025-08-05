<?php
require '../config/db.php';
require '../includes/fonnte_helper.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id'])) {
    $peminjaman_id = $_POST['id'];

    // Cek status peminjaman saat ini sebelum melakukan update
    $stmt_check = $pdo->prepare("SELECT status_peminjaman FROM peminjaman WHERE id = ?");
    $stmt_check->execute([$peminjaman_id]);
    $current_status = $stmt_check->fetchColumn();

    if ($current_status === 'Diajukan') {
        $stmt = $pdo->prepare("SELECT nomor_telepon_peminjam, nama_peminjam FROM peminjaman WHERE id = ?");
        $stmt->execute([$peminjaman_id]);
        $peminjam = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$peminjam) {
            echo json_encode(['success' => false, 'message' => 'Peminjaman tidak ditemukan.']);
            exit;
        }

        try {
            $stmt = $pdo->prepare("UPDATE peminjaman SET status_peminjaman = 'Ditolak' WHERE id = ?");
            $stmt->execute([$peminjaman_id]);

            // --- KIRIM NOTIFIKASI PENOLAKAN KE PENGGUNA ---
            try {
                if ($peminjam['nomor_telepon_peminjam']) {
                    $messageToUser = "❌ *Notifikasi SIMBA* ❌\n\nHai *{$peminjam['nama_peminjam']}*,\n\nMohon maaf, pengajuan peminjaman aset Anda dengan terpaksa harus kami *TOLAK* saat ini. Untuk informasi lebih lanjut, silakan hubungi pengelola aset.\n\nTerima kasih.";
                    sendWhatsApp($peminjam['nomor_telepon_peminjam'], $messageToUser);
                }
            } catch (Exception $e) { /* Abaikan jika notifikasi gagal */
            }
            // ---------------------------------------------

            echo json_encode(['success' => true]);
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'message' => 'Gagal menolak peminjaman.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Peminjaman sudah diproses sebelumnya.']);
    }
}
