<?php
require '../config/db.php';
require '../includes/fonnte_helper.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $peminjaman_id = $_POST['id'];

    // Ambil data peminjaman yang diperlukan untuk notifikasi
    $stmt = $pdo->prepare("SELECT nomor_telepon_peminjam, nama_peminjam FROM peminjaman WHERE id = ?");
    $stmt->execute([$peminjaman_id]);
    $peminjam = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$peminjam) {
        echo json_encode(['success' => false, 'message' => 'Peminjaman tidak ditemukan.']);
        exit;
    }

    $pdo->beginTransaction();
    try {
        // 1. Update status peminjaman
        $stmt1 = $pdo->prepare("UPDATE peminjaman SET status_peminjaman = 'Disetujui', tanggal_pinjam = CURDATE() WHERE id = ?");
        $stmt1->execute([$peminjaman_id]);

        // 2. Update status SEMUA aset terkait menjadi 'Dipinjam'
        $sql_update_aset = "UPDATE aset SET status = 'Dipinjam' WHERE id IN (SELECT aset_id FROM detail_peminjaman WHERE peminjaman_id = ?)";
        $stmt2 = $pdo->prepare($sql_update_aset);
        $stmt2->execute([$peminjaman_id]);

        $pdo->commit();

        // Kirim Notifikasi Detail ke Pengguna dengan Ikon
        try {
            // Ambil daftar aset yang disetujui
            $aset_stmt = $pdo->prepare("SELECT a.nama_bmn FROM aset a JOIN detail_peminjaman dp ON a.id = dp.aset_id WHERE dp.peminjaman_id = ?");
            $aset_stmt->execute([$peminjaman_id]);
            $asets = $aset_stmt->fetchAll(PDO::FETCH_ASSOC);

            $daftarAset = "";
            foreach ($asets as $aset) {
                $daftarAset .= "    - " . $aset['nama_bmn'] . "\n";
            }

            if ($peminjam['nomor_telepon_peminjam']) {
                $messageToUser = "✅ *Persetujuan Peminjaman Aset - SIMBA* ✅\n\n" .
                    "👋 Hai *{$peminjam['nama_peminjam']}*,\n\n" .
                    "Pengajuan peminjaman Anda telah *DISETUJUI*.\n\n" .
                    "📦 *Aset yang Disetujui:*\n" .
                    "{$daftarAset}\n" .
                    "🤝 Silakan berkoordinasi dengan pengelola aset untuk pengambilan barang.\n\n" .
                    "Terima kasih.";
                sendWhatsApp($peminjam['nomor_telepon_peminjam'], $messageToUser);
            }
        } catch (Exception $e) { /* Abaikan jika notifikasi gagal */
        }

        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        $pdo->rollBack();
        echo json_encode(['success' => false, 'message' => 'Gagal menyetujui peminjaman: ' . $e->getMessage()]);
    }
}
