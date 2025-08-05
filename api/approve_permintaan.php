<?php
require '../config/db.php';
require '../includes/fonnte_helper.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $permintaan_id = $_POST['permintaan_id'] ?? null;
    $items_json = $_POST['items'] ?? '[]';
    $tanda_tangan_admin = $_POST['tanda_tangan_admin'] ?? null;
    $items = json_decode($items_json, true);

    if (empty($permintaan_id) || !is_array($items) || empty($items) || empty($tanda_tangan_admin)) {
        echo json_encode(['success' => false, 'message' => 'Data tidak lengkap. Pastikan semua item dan tanda tangan telah diisi.']);
        exit;
    }

    $pdo->beginTransaction();
    try {
        $stmt_check = $pdo->prepare("SELECT status_permintaan, nomor_spb FROM permintaan_persediaan WHERE id = ? FOR UPDATE");
        $stmt_check->execute([$permintaan_id]);
        $permintaan_data = $stmt_check->fetch(PDO::FETCH_ASSOC);

        if ($permintaan_data['status_permintaan'] === 'Diajukan') {
            // --- Logika Generate Nomor SBBK Otomatis ---
            $bulan_sekarang = date('m');
            $stmt_nomor = $pdo->query("SELECT sbbk FROM nomor FOR UPDATE");
            $nomor_terakhir = (int)($stmt_nomor->fetchColumn() ?? 0);
            $nomor_berikutnya = $nomor_terakhir + 1;

            $nomor_sbbk = "SBBK/672845/" . $bulan_sekarang . "/" . $nomor_berikutnya;

            // 1. Ubah status, ttd admin, dan nomor sbbk yang diinput manual.
            $stmt_update = $pdo->prepare("UPDATE permintaan_persediaan SET status_permintaan = 'Disetujui', tanda_tangan_admin = ?, nomor_sbbk = ? WHERE id = ?");
            $stmt_update->execute([$tanda_tangan_admin, $nomor_sbbk, $permintaan_id]);

            // Update counter nomor di tabel nomor
            $stmt_update_nomor = $pdo->prepare("UPDATE nomor SET sbbk = ?");
            $stmt_update_nomor->execute([$nomor_berikutnya]);

            // Hapus detail permintaan lama
            $stmt_delete_details = $pdo->prepare("DELETE FROM detail_permintaan WHERE permintaan_id = ?");
            $stmt_delete_details->execute([$permintaan_id]);

            // Siapkan statement untuk insert detail baru dan update stok
            $stmt_insert = $pdo->prepare("INSERT INTO detail_permintaan (permintaan_id, persediaan_id, jumlah_diminta) VALUES (?, ?, ?)");
            $stmt_stock = $pdo->prepare("UPDATE persediaan SET stok = stok - ? WHERE id = ?");
            foreach ($items as $item) {
                if ($item['jumlah'] > 0) {
                    $stmt_insert->execute([$permintaan_id, $item['id'], $item['jumlah']]);
                    $stmt_stock->execute([$item['jumlah'], $item['id']]);
                }
            }
            $pdo->commit();

            try {
                $stmt_pemohon = $pdo->prepare("SELECT nama_pemohon, nomor_telepon_pemohon FROM permintaan_persediaan WHERE id = ?");
                $stmt_pemohon->execute([$permintaan_id]);
                $pemohon = $stmt_pemohon->fetch(PDO::FETCH_ASSOC);

                if ($pemohon && !empty($pemohon['nomor_telepon_pemohon'])) {
                    $item_stmt = $pdo->prepare("
                        SELECT dp.jumlah_diminta, p.satuan, p.nama_persediaan 
                        FROM detail_permintaan dp 
                        JOIN persediaan p ON dp.persediaan_id = p.id 
                        WHERE dp.permintaan_id = ?
                    ");
                    $item_stmt->execute([$permintaan_id]);
                    $detail_items = $item_stmt->fetchAll(PDO::FETCH_ASSOC);

                    $daftarItems = "";
                    foreach ($detail_items as $detail_item) {
                        $daftarItems .= "- {$detail_item['jumlah_diminta']} {$detail_item['satuan']} {$detail_item['nama_persediaan']}\n";
                    }

                    $messageToUser = "✅ *Permintaan Disetujui - SIMBA* ✅\n\n" .
                        "👋 Hai *{$pemohon['nama_pemohon']}*,\n\n" .
                        "Permintaan persediaan Anda telah *DISETUJUI*.\n" .
                        "Nomor SBBK: *{$nomor_sbbk}*\n\n" .
                        "📦 *Item yang Disetujui:*\n" .
                        "{$daftarItems}\n" .
                        "🤝 Silakan berkoordinasi dengan pengelola untuk pengambilan barang.\n\n" .
                        "Terima kasih.";

                    sendWhatsApp($pemohon['nomor_telepon_pemohon'], $messageToUser);
                }
            } catch (Exception $e) { /* Abaikan jika notifikasi gagal */
            }
            echo json_encode(['success' => true]);
        } else {
            $pdo->rollBack();
            echo json_encode(['success' => false, 'message' => 'Permintaan sudah diproses sebelumnya.']);
        }
    } catch (Exception $e) {
        $pdo->rollBack();
        echo json_encode(['success' => false, 'message' => 'Gagal menyetujui permintaan: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Metode request tidak valid']);
}
