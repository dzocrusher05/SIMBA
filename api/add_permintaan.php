<?php
session_start();
require '../config/db.php';
require '../includes/fonnte_helper.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Metode request tidak valid.']);
    exit;
}

$nama_pemohon = $_POST['nama_pemohon'] ?? '';
$nomor_telepon_pemohon = $_POST['nomor_telepon_pemohon'] ?? '';
$tanda_tangan = $_POST['tanda_tangan_pemohon'] ?? null;
$items_json = $_POST['items'] ?? '';
$items = json_decode($items_json, true);

if (empty($nama_pemohon) || empty($nomor_telepon_pemohon) || empty($tanda_tangan) || empty($items)) {
    if (empty($nama_pemohon)) $error_message = 'Nama pemohon tidak diisi.';
    else if (empty($nomor_telepon_pemohon)) $error_message = 'Nomor telepon tidak diisi.';
    else if (empty($tanda_tangan)) $error_message = 'Tanda tangan tidak diisi.';
    else if (empty($items)) $error_message = 'Tidak ada item persediaan yang dipilih.';

    echo json_encode(['success' => false, 'message' => $error_message ?? 'Semua data wajib diisi.']);
    exit;
}

$pdo->beginTransaction();
try {
    // --- Logika Generate Nomor SPB Otomatis dengan format 672845/bulan/nomor ---
    $bulan_sekarang = date('m');
    $stmt_nomor = $pdo->query("SELECT spb FROM nomor FOR UPDATE");
    $nomor_terakhir = (int)($stmt_nomor->fetchColumn() ?? 0);
    $nomor_berikutnya = $nomor_terakhir + 1;

    $nomor_spb = "SPB/672845/" . $bulan_sekarang . "/" . $nomor_berikutnya;

    // 1. Simpan data permintaan utama DENGAN NOMOR SPB
    $stmt1 = $pdo->prepare("INSERT INTO permintaan_persediaan (nama_pemohon, nomor_telepon_pemohon, tanggal_permintaan, status_permintaan, tanda_tangan_pemohon, nomor_spb) VALUES (?, ?, CURDATE(), 'Diajukan', ?, ?)");
    $stmt1->execute([$nama_pemohon, $nomor_telepon_pemohon, $tanda_tangan, $nomor_spb]);
    $permintaan_id = $pdo->lastInsertId();

    // 2. Simpan detail item dan catat riwayat keluar
    $stmt2 = $pdo->prepare("INSERT INTO detail_permintaan (permintaan_id, persediaan_id, jumlah_diminta) VALUES (?, ?, ?)");
    $stmt_riwayat_keluar = $pdo->prepare("INSERT INTO riwayat_persediaan (persediaan_id, tanggal, jenis_transaksi, jumlah, keterangan, nomor_dokumen) VALUES (?, CURDATE(), 'keluar', ?, ?, ?)");

    foreach ($items as $persediaan_id => $jumlah) {
        $stmt2->execute([$permintaan_id, $persediaan_id, $jumlah]);
        $stmt_riwayat_keluar->execute([$persediaan_id, $jumlah, 'Permintaan barang oleh ' . $nama_pemohon, $nomor_spb]);
    }

    // 3. Update counter nomor di tabel baru
    $stmt_update_nomor = $pdo->prepare("UPDATE nomor SET spb = ?");
    $stmt_update_nomor->execute([$nomor_berikutnya]);

    $pdo->commit();

    // 4. Kirim notifikasi ke Admin Persediaan
    try {
        $admin_stmt = $pdo->query("SELECT nomor_telepon FROM users WHERE notifikasi_peran = 'admin_persediaan' LIMIT 1");
        $admin_phone = $admin_stmt->fetchColumn();
        if ($admin_phone) {
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
            $messageToAdmin = "🔔 *Notifikasi Permintaan Persediaan* 🔔\n\n" .
                "Permintaan baru telah masuk:\n" .
                "Nomor SPB: *{$nomor_spb}*\n\n" .
                "👤 *Nama:* {$nama_pemohon}\n" .
                "📞 *Telepon:* {$nomor_telepon_pemohon}\n\n" .
                "📦 *Item yang Diminta:*\n" .
                "{$daftarItems}\n\n" .
                "Mohon untuk segera ditindaklanjuti melalui panel admin. Terima kasih.";

            sendWhatsApp($admin_phone, $messageToAdmin, true);
        }
    } catch (Exception $e) { /* Abaikan jika notifikasi gagal */
    }
    echo json_encode(['success' => true, 'message' => 'Permintaan berhasil dikirim. Nomor SPB Anda: ' . $nomor_spb]);
} catch (Exception $e) {
    $pdo->rollBack();
    echo json_encode(['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()]);
}
