<?php
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
$items_json = $_POST['items'] ?? '{}';
$items = json_decode($items_json, true);

if (empty($nama_pemohon) || empty($nomor_telepon_pemohon) || empty($items)) {
    echo json_encode(['success' => false, 'message' => 'Semua data wajib diisi.']);
    exit;
}

$pdo->beginTransaction();
try {
    // --- Logika Generate Nomor SPB ---
    $tahun_sekarang = date('Y');
    $bulan_sekarang = date('m');

    $stmt_pengaturan = $pdo->query("SELECT nama_pengaturan, nilai_pengaturan FROM pengaturan WHERE nama_pengaturan LIKE '%_spb'")->fetchAll(PDO::FETCH_KEY_PAIR);
    $tahun_db = $stmt_pengaturan['tahun_terakhir_spb'];
    $bulan_db = $stmt_pengaturan['bulan_terakhir_spb'];

    $nomor_berikutnya = 1;
    if ($tahun_sekarang == $tahun_db && $bulan_sekarang == $bulan_db) {
        $nomor_berikutnya = (int)$stmt_pengaturan['nomor_terakhir_spb'] + 1;
    }
    $nomor_spb = "SPB/" . $bulan_sekarang . "/" . $tahun_sekarang . "/" . $nomor_berikutnya;

    // 1. Simpan data permintaan utama DENGAN NOMOR SPB
    $stmt1 = $pdo->prepare("INSERT INTO permintaan_persediaan (nama_pemohon, nomor_telepon_pemohon, tanggal_permintaan, status_permintaan, tanda_tangan_pemohon, nomor_spb) VALUES (?, ?, CURDATE(), 'Diajukan', ?, ?)");
    $stmt1->execute([$nama_pemohon, $nomor_telepon_pemohon, $tanda_tangan, $nomor_spb]);
    $permintaan_id = $pdo->lastInsertId();

    // 2. Simpan detail item
    $stmt2 = $pdo->prepare("INSERT INTO detail_permintaan (permintaan_id, persediaan_id, jumlah_diminta) VALUES (?, ?, ?)");
    foreach ($items as $persediaan_id => $jumlah) {
        $stmt2->execute([$permintaan_id, $persediaan_id, $jumlah]);
    }

    // 3. Update counter nomor di tabel pengaturan
    $stmt_update_counter = $pdo->prepare("UPDATE pengaturan SET nilai_pengaturan = ? WHERE nama_pengaturan = ?");
    $stmt_update_counter->execute([$nomor_berikutnya, 'nomor_terakhir_spb']);
    $stmt_update_counter->execute([$bulan_sekarang, 'bulan_terakhir_spb']);
    $stmt_update_counter->execute([$tahun_sekarang, 'tahun_terakhir_spb']);

    $pdo->commit();

    // 3. Kirim notifikasi ke Admin
    try {
        $admin_stmt = $pdo->query("SELECT nomor_telepon FROM users WHERE peran = 'admin' LIMIT 1");
        $admin_phone = $admin_stmt->fetchColumn();
        if ($admin_phone) {
            $daftarItems = implode("\n", $item_details_for_notif);
            $messageToAdmin = "🔔 *Notifikasi Permintaan Persediaan* 🔔\n\n" .
                "Permintaan baru telah masuk:\n\n" .
                "👤 *Nama:* {$nama_pemohon}\n" .
                "📞 *Telepon:* {$nomor_telepon_pemohon}\n\n" .
                "📦 *Item yang Diminta:*\n" .
                "{$daftarItems}\n\n" .
                "Mohon untuk segera ditindaklanjuti melalui panel admin. Terima kasih.";
            sendWhatsApp($admin_phone, $messageToAdmin);
        }
    } catch (Exception $e) { /* Abaikan jika notifikasi gagal */
    }
    echo json_encode(['success' => true, 'message' => 'Permintaan berhasil dikirim.']);
} catch (Exception $e) {
    $pdo->rollBack();
    echo json_encode(['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()]);
}
