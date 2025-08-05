<?php
require '../config/db.php';
require '../includes/fonnte_helper.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Metode request tidak valid.']);
    exit;
}

$nama_peminjam = $_POST['nama_peminjam'] ?? '';
$nomor_telepon_peminjam = $_POST['nomor_telepon_peminjam'] ?? '';
$alasan_peminjaman = $_POST['alasan_peminjaman'] ?? '';
$lokasi_peminjaman = $_POST['lokasi_peminjaman'] ?? '';
$lokasi_kustom = $_POST['lokasi_kustom'] ?? '';
$tanggal_peminjaman_str = $_POST['tanggal_peminjaman'] ?? '';
$aset_ids_json = $_POST['aset_ids'] ?? '[]';
$tanda_tangan_peminjam = $_POST['tanda_tangan_peminjam'] ?? null;
$aset_ids = json_decode($aset_ids_json, true);

if (empty($nama_peminjam) || empty($nomor_telepon_peminjam) || empty($alasan_peminjaman) || empty($tanggal_peminjaman_str) || empty($aset_ids) || empty($tanda_tangan_peminjam)) {
    echo json_encode(['success' => false, 'message' => 'Semua data wajib diisi.']);
    exit;
}

$lokasi_final = ($lokasi_peminjaman === 'lainnya') ? $lokasi_kustom : $lokasi_peminjaman;
if (empty($lokasi_final)) {
    echo json_encode(['success' => false, 'message' => 'Lokasi tujuan harus diisi.']);
    exit;
}

$dates = explode(' to ', $tanggal_peminjaman_str);
$tanggal_pinjam_mysql = date('Y-m-d', strtotime($dates[0]));
$tanggal_kembali_mysql = count($dates) == 2 ? date('Y-m-d', strtotime($dates[1])) : $tanggal_pinjam_mysql;

$pdo->beginTransaction();
try {
    // --- Logika Generate Nomor SPA Otomatis ---
    $bulan_sekarang = date('m');
    $stmt_nomor = $pdo->query("SELECT peminjaman FROM nomor FOR UPDATE");
    $nomor_terakhir = (int)($stmt_nomor->fetchColumn() ?? 0);
    $nomor_berikutnya = $nomor_terakhir + 1;
    $nomor_spa = "SPA/672845/" . $bulan_sekarang . "/" . $nomor_berikutnya;

    $stmt1 = $pdo->prepare("INSERT INTO peminjaman (nama_peminjam, nomor_telepon_peminjam, alasan_peminjaman, lokasi_peminjaman, tanggal_pengajuan, tanggal_pinjam, tanggal_kembali, status_peminjaman, tanda_tangan_peminjam, nomor_surat) VALUES (?, ?, ?, ?, CURDATE(), ?, ?, 'Diajukan', ?, ?)");
    $stmt1->execute([$nama_peminjam, $nomor_telepon_peminjam, $alasan_peminjaman, $lokasi_final, $tanggal_pinjam_mysql, $tanggal_kembali_mysql, $tanda_tangan_peminjam, $nomor_spa]);
    $peminjaman_id = $pdo->lastInsertId();

    $stmt2 = $pdo->prepare("INSERT INTO detail_peminjaman (peminjaman_id, aset_id) VALUES (?, ?)");
    foreach ($aset_ids as $aset_id) {
        $stmt2->execute([$peminjaman_id, $aset_id]);
    }

    // Update counter nomor di tabel baru
    $stmt_update_nomor = $pdo->prepare("UPDATE nomor SET peminjaman = ?");
    $stmt_update_nomor->execute([$nomor_berikutnya]);

    $pdo->commit();

    try {
        $placeholders = implode(',', array_fill(0, count($aset_ids), '?'));
        $aset_stmt = $pdo->prepare("SELECT nama_bmn FROM aset WHERE id IN ($placeholders)");
        $aset_stmt->execute($aset_ids);
        $asets = $aset_stmt->fetchAll(PDO::FETCH_ASSOC);
        $daftarAset = "";
        foreach ($asets as $aset) {
            $daftarAset .= "    - " . $aset['nama_bmn'] . "\n";
        }
        $admin_stmt = $pdo->query("SELECT nomor_telepon FROM users WHERE peran = 'admin' LIMIT 1");
        $admin_phone = $admin_stmt->fetchColumn();
        if ($admin_phone) {
            $messageToAdmin = "🔔 *Notifikasi SIMBA* 🔔\n\n" .
                "Pengajuan peminjaman baru telah masuk:\n\n" .
                "👤 *Nama:* {$nama_peminjam}\n" .
                "📞 *Telepon:* {$nomor_telepon_peminjam}\n" .
                "🗓️ *Periode:* {$tanggal_peminjaman_str}\n" .
                "📍 *Lokasi:* {$lokasi_final}\n" .
                "📝 *Alasan:* {$alasan_peminjaman}\n\n" .
                "📦 *Aset yang Diajukan:*\n" .
                "{$daftarAset}\n" .
                "Mohon untuk segera ditindaklanjuti melalui panel admin. Terima kasih.";
            sendWhatsApp($admin_phone, $messageToAdmin);
        }
    } catch (Exception $e) { /* Abaikan jika notifikasi gagal */
    }

    echo json_encode(['success' => true, 'message' => 'Pengajuan peminjaman berhasil dikirim.']);
} catch (Exception $e) {
    $pdo->rollBack();
    echo json_encode(['success' => false, 'message' => 'Terjadi kesalahan pada server: ' . $e->getMessage()]);
}
