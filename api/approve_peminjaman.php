<?php
require '../config/db.php';
require '../includes/fonnte_helper.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $peminjaman_id = $_POST['id'];
    $tanda_tangan_admin = $_POST['tanda_tangan_admin'] ?? null;

    if (empty($tanda_tangan_admin)) {
        echo json_encode(['success' => false, 'message' => 'Tanda tangan admin wajib diisi.']);
        exit;
    }

    $pdo->beginTransaction();
    try {
        $stmt_check = $pdo->prepare("SELECT status_peminjaman, nomor_surat FROM peminjaman WHERE id = ? FOR UPDATE");
        $stmt_check->execute([$peminjaman_id]);
        $peminjaman_data = $stmt_check->fetch(PDO::FETCH_ASSOC);

        if ($peminjaman_data['status_peminjaman'] === 'Diajukan') {
            $stmt = $pdo->prepare("SELECT nomor_telepon_peminjam, nama_peminjam, nomor_surat FROM peminjaman WHERE id = ?");
            $stmt->execute([$peminjaman_id]);
            $peminjam = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$peminjam) {
                echo json_encode(['success' => false, 'message' => 'Peminjaman tidak ditemukan.']);
                exit;
            }

            // 1. Update status peminjaman dan tanda tangan admin
            $stmt1 = $pdo->prepare("UPDATE peminjaman SET status_peminjaman = 'Disetujui', tanggal_pinjam = CURDATE(), tanda_tangan_admin = ? WHERE id = ?");
            $stmt1->execute([$tanda_tangan_admin, $peminjaman_id]);

            // 2. Update status SEMUA aset terkait menjadi 'Dipinjam'
            $sql_update_aset = "UPDATE aset SET status = 'Dipinjam' WHERE id IN (SELECT aset_id FROM detail_peminjaman WHERE peminjaman_id = ?)";
            $stmt2 = $pdo->prepare($sql_update_aset);
            $stmt2->execute([$peminjaman_id]);

            // Catat riwayat aset yang dipinjam
            $stmt_aset_ids = $pdo->prepare("SELECT aset_id FROM detail_peminjaman WHERE peminjaman_id = ?");
            $stmt_aset_ids->execute([$peminjaman_id]);
            $aset_ids = $stmt_aset_ids->fetchAll(PDO::FETCH_COLUMN);
            $stmt_riwayat_aset = $pdo->prepare("INSERT INTO riwayat_aset (aset_id, tanggal, jenis_transaksi, keterangan, nomor_dokumen) VALUES (?, CURDATE(), 'peminjaman', ?, ?)");
            foreach ($aset_ids as $aset_id) {
                $stmt_riwayat_aset->execute([$aset_id, 'Dipinjam oleh ' . $peminjam['nama_peminjam'], $peminjaman_data['nomor_surat']]);
            }

            $pdo->commit();

            try {
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
                        "Pengajuan peminjaman Anda telah *DISETUJUI*.\n" .
                        "Nomor Surat Peminjaman (SPA): *{$peminjaman_data['nomor_surat']}*\n\n" .
                        "📦 *Aset yang Disetujui:*\n" .
                        "{$daftarAset}\n" .
                        "🤝 Silakan berkoordinasi dengan pengelola aset untuk pengambilan barang.\n\n" .
                        "Terima kasih.";
                    sendWhatsApp($peminjam['nomor_telepon_peminjam'], $messageToUser);
                }
            } catch (Exception $e) { /* Abaikan jika notifikasi gagal */
            }
            echo json_encode(['success' => true]);
        } else {
            $pdo->rollBack();
            echo json_encode(['success' => false, 'message' => 'Peminjaman sudah diproses sebelumnya.']);
        }
    } catch (Exception $e) {
        $pdo->rollBack();
        echo json_encode(['success' => false, 'message' => 'Gagal menyetujui peminjaman: ' . $e->getMessage()]);
    }
}
