<?php
require '../config/db.php';
require '../includes/fonnte_helper.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Metode request tidak valid.']);
    exit;
}

// Ambil semua data dari POST
$nama_peminjam = $_POST['nama_peminjam'] ?? '';
$nomor_telepon_peminjam = $_POST['nomor_telepon_peminjam'] ?? '';
$alasan_peminjaman = $_POST['alasan_peminjaman'] ?? '';
$lokasi_peminjaman = $_POST['lokasi_peminjaman'] ?? '';
$lokasi_kustom = $_POST['lokasi_kustom'] ?? '';
$tanggal_peminjaman_str = $_POST['tanggal_peminjaman'] ?? '';
$aset_ids_json = $_POST['aset_ids'] ?? '[]';
$aset_ids = json_decode($aset_ids_json, true);

// Validasi data yang masuk
if (empty($nama_peminjam) || empty($nomor_telepon_peminjam) || empty($alasan_peminjaman) || empty($tanggal_peminjaman_str) || empty($aset_ids)) {
    echo json_encode(['success' => false, 'message' => 'Semua data wajib diisi.']);
    exit;
}

$lokasi_final = ($lokasi_peminjaman === 'lainnya') ? $lokasi_kustom : $lokasi_peminjaman;
if (empty($lokasi_final)) {
    echo json_encode(['success' => false, 'message' => 'Lokasi tujuan harus diisi.']);
    exit;
}

// Parsing Tanggal dari Flatpickr
$dates = explode(' to ', $tanggal_peminjaman_str);
$tanggal_pinjam_mysql = date('Y-m-d', strtotime($dates[0]));
$tanggal_kembali_mysql = count($dates) == 2 ? date('Y-m-d', strtotime($dates[1])) : $tanggal_pinjam_mysql;

// Transaksi Database
$pdo->beginTransaction();
try {
    $stmt1 = $pdo->prepare("INSERT INTO peminjaman (nama_peminjam, nomor_telepon_peminjam, alasan_peminjaman, lokasi_peminjaman, tanggal_pengajuan, tanggal_pinjam, tanggal_kembali, status_peminjaman) VALUES (?, ?, ?, ?, CURDATE(), ?, ?, 'Diajukan')");
    $stmt1->execute([$nama_peminjam, $nomor_telepon_peminjam, $alasan_peminjaman, $lokasi_final, $tanggal_pinjam_mysql, $tanggal_kembali_mysql]);

    $peminjaman_id = $pdo->lastInsertId();

    $stmt2 = $pdo->prepare("INSERT INTO detail_peminjaman (peminjaman_id, aset_id) VALUES (?, ?)");
    foreach ($aset_ids as $aset_id) {
        $stmt2->execute([$peminjaman_id, $aset_id]);
    }

    $pdo->commit();

    // Kirim Notifikasi Detail ke Admin dengan Ikon
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
