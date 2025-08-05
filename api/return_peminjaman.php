<?php
require '../config/db.php';
header('Content-Type: application/json');
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id'])) {
    $peminjaman_id = $_POST['id'];
    $pdo->beginTransaction();
    try {
        $stmt_check = $pdo->prepare("SELECT status_peminjaman FROM peminjaman WHERE id = ? FOR UPDATE");
        $stmt_check->execute([$peminjaman_id]);
        $current_status = $stmt_check->fetchColumn();

        if ($current_status === 'Disetujui') {
            $stmt1 = $pdo->prepare("UPDATE peminjaman SET status_peminjaman = 'Dikembalikan', tanggal_kembali = CURDATE() WHERE id = ?");
            $stmt1->execute([$peminjaman_id]);
            $sql_update_aset = "UPDATE aset SET status = 'Tersedia' WHERE id IN (SELECT aset_id FROM detail_peminjaman WHERE peminjaman_id = ?)";
            $stmt2 = $pdo->prepare($sql_update_aset);
            $stmt2->execute([$peminjaman_id]);

            $stmt_aset_ids = $pdo->prepare("SELECT aset_id FROM detail_peminjaman WHERE peminjaman_id = ?");
            $stmt_aset_ids->execute([$peminjaman_id]);
            $aset_ids = $stmt_aset_ids->fetchAll(PDO::FETCH_COLUMN);

            $stmt_peminjaman_info = $pdo->prepare("SELECT nama_peminjam, nomor_surat FROM peminjaman WHERE id = ?");
            $stmt_peminjaman_info->execute([$peminjaman_id]);
            $peminjaman_info = $stmt_peminjaman_info->fetch(PDO::FETCH_ASSOC);

            $stmt_riwayat_aset = $pdo->prepare("INSERT INTO riwayat_aset (aset_id, tanggal, jenis_transaksi, keterangan, nomor_dokumen) VALUES (?, CURDATE(), 'pengembalian', ?, ?)");
            foreach ($aset_ids as $aset_id) {
                $stmt_riwayat_aset->execute([$aset_id, 'Dikembalikan oleh ' . $peminjaman_info['nama_peminjam'], $peminjaman_info['nomor_surat']]);
            }

            $pdo->commit();
            echo json_encode(['success' => true]);
        } else {
            $pdo->rollBack();
            echo json_encode(['success' => false, 'message' => 'Status peminjaman tidak valid untuk dikembalikan.']);
        }
    } catch (Exception $e) {
        $pdo->rollBack();
        echo json_encode(['success' => false, 'message' => 'Gagal memproses pengembalian.']);
    }
}
