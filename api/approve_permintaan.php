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
        // --- Logika Generate Nomor SBBK ---
        $tahun_sekarang = date('Y');
        $bulan_sekarang = date('m');
        $stmt_pengaturan = $pdo->query("SELECT nama_pengaturan, nilai_pengaturan FROM pengaturan WHERE nama_pengaturan LIKE '%_sbbk'")->fetchAll(PDO::FETCH_KEY_PAIR);
        $tahun_db = $stmt_pengaturan['tahun_terakhir_sbbk'];
        $bulan_db = $stmt_pengaturan['bulan_terakhir_sbbk'];
        $nomor_berikutnya = 1;
        if ($tahun_sekarang == $tahun_db && $bulan_sekarang == $bulan_db) {
            $nomor_berikutnya = (int)$stmt_pengaturan['nomor_terakhir_sbbk'] + 1;
        }
        $nomor_sbbk = "SBBK/" . $bulan_sekarang . "/" . $tahun_sekarang . "/" . $nomor_berikutnya;

        // 1. Ubah status, ttd admin, dan nomor sbbk
        $stmt_update = $pdo->prepare("UPDATE permintaan_persediaan SET status_permintaan = 'Disetujui', tanda_tangan_admin = ?, nomor_sbbk = ? WHERE id = ?");
        $stmt_update->execute([$tanda_tangan_admin, $nomor_sbbk, $permintaan_id]);

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

        // 3. Update counter nomor sbbk
        $stmt_update_counter = $pdo->prepare("UPDATE pengaturan SET nilai_pengaturan = ? WHERE nama_pengaturan = ?");
        $stmt_update_counter->execute([$nomor_berikutnya, 'nomor_terakhir_sbbk']);
        $stmt_update_counter->execute([$bulan_sekarang, 'bulan_terakhir_sbbk']);
        $stmt_update_counter->execute([$tahun_sekarang, 'tahun_terakhir_sbbk']);

        $pdo->commit();
        // ===================================================================
        // ### BAGIAN BARU: KIRIM NOTIFIKASI KE PEMOHON ###
        // ===================================================================
        try {
            // A. Ambil data pemohon
            $stmt_pemohon = $pdo->prepare("SELECT nama_pemohon, nomor_telepon_pemohon FROM permintaan_persediaan WHERE id = ?");
            $stmt_pemohon->execute([$permintaan_id]);
            $pemohon = $stmt_pemohon->fetch(PDO::FETCH_ASSOC);

            if ($pemohon && !empty($pemohon['nomor_telepon_pemohon'])) {
                // B. Ambil detail item yang disetujui
                $item_details_for_notif = [];
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

                // C. Susun dan kirim pesan
                $messageToUser = "✅ *Permintaan Disetujui - SIMBA* ✅\n\n" .
                    "👋 Hai *{$pemohon['nama_pemohon']}*,\n\n" .
                    "Permintaan persediaan Anda telah *DISETUJUI*.\n\n" .
                    "📦 *Item yang Disetujui:*\n" .
                    "{$daftarItems}\n" .
                    "🤝 Silakan berkoordinasi dengan pengelola untuk pengambilan barang.\n\n" .
                    "Terima kasih.";

                sendWhatsApp($pemohon['nomor_telepon_pemohon'], $messageToUser);
            }
        } catch (Exception $e) {
            // Abaikan jika notifikasi gagal agar tidak membatalkan proses utama
        }
        // ===================================================================
        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        $pdo->rollBack();
        echo json_encode(['success' => false, 'message' => 'Gagal menyetujui permintaan: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Metode request tidak valid']);
}
