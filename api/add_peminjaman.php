<?php
session_start();
require '../config/db.php';
require '../includes/fonnte_helper.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Metode request tidak valid.']);
    exit;
}

// Ambil data dari form
$nama_peminjam = $_POST['nama_peminjam'] ?? '';
$nomor_telepon_peminjam = $_POST['nomor_telepon_peminjam'] ?? '';
$tanda_tangan = $_POST['tanda_tangan_peminjam'] ?? '';
$aset_ids = $_POST['aset_ids'] ?? [];
$lokasi = $_POST['lokasi_peminjaman'] ?? '';
$alasan = $_POST['alasan_peminjaman'] ?? '';
$tanggal_mulai = $_POST['tanggal_mulai'] ?? '';
$tanggal_selesai = $_POST['tanggal_selesai'] ?? '';

// Validasi data
if (empty($nama_peminjam) || empty($nomor_telepon_peminjam) || empty($tanda_tangan) || empty($aset_ids)) {
    echo json_encode(['success' => false, 'message' => 'Semua data wajib diisi, pastikan minimal satu aset dipilih.']);
    exit;
}

$pdo->beginTransaction();
try {
    // --- Logika Generate Nomor Surat Peminjaman (SPM) Otomatis ---
    $bulan_sekarang = date('m');
    $stmt_nomor = $pdo->query("SELECT peminjaman FROM nomor FOR UPDATE");
    $nomor_terakhir = (int)($stmt_nomor->fetchColumn() ?? 0);
    $nomor_berikutnya = $nomor_terakhir + 1;
    // Format Nomor: SPM / Kode Instansi / Bulan / Nomor Urut
    $nomor_surat_peminjaman = "SPM/672845/" . $bulan_sekarang . "/" . $nomor_berikutnya;

    // 1. Simpan data peminjaman utama dengan nomor surat yang baru
    $stmt1 = $pdo->prepare(
        "INSERT INTO peminjaman (nama_peminjam, nomor_telepon_peminjam, lokasi_peminjaman, alasan_peminjaman, tanggal_pengajuan, tanggal_pinjam, tanggal_kembali, status_peminjaman, tanda_tangan_peminjam, nomor_surat) 
         VALUES (?, ?, ?, ?, CURDATE(), ?, ?, 'Diajukan', ?, ?)"
    );
    $stmt1->execute([$nama_peminjam, $nomor_telepon_peminjam, $lokasi, $alasan, $tanggal_mulai, $tanggal_selesai, $tanda_tangan, $nomor_surat_peminjaman]);
    $peminjaman_id = $pdo->lastInsertId();

    // 2. Simpan detail aset yang dipinjam ke tabel `detail_peminjaman`
    $stmt2 = $pdo->prepare("INSERT INTO detail_peminjaman (peminjaman_id, aset_id) VALUES (?, ?)");
    $stmt_update_aset = $pdo->prepare("UPDATE aset SET status = 'Dipinjam' WHERE id = ?");

    foreach ($aset_ids as $aset_id) {
        $stmt2->execute([$peminjaman_id, $aset_id]);
        $stmt_update_aset->execute([$aset_id]);
    }

    // 3. Update counter nomor di tabel `nomor`
    $stmt_update_nomor = $pdo->prepare("UPDATE nomor SET peminjaman = ?");
    $stmt_update_nomor->execute([$nomor_berikutnya]);

    $pdo->commit();

    // 4. Kirim notifikasi ke Admin
    try {
        $admin_phone = '0812XXXXXXXX'; // GANTI DENGAN NOMOR WA ADMIN

        $inQuery = implode(',', array_fill(0, count($aset_ids), '?'));
        $item_stmt = $pdo->prepare("SELECT nama_bmn FROM aset WHERE id IN ($inQuery)");
        $item_stmt->execute($aset_ids);
        $detail_items = $item_stmt->fetchAll(PDO::FETCH_ASSOC);

        $daftarItems = "";
        foreach ($detail_items as $detail_item) {
            $daftarItems .= "- " . $detail_item['nama_bmn'] . "\n";
        }

        $messageToAdmin = "🔔 *Notifikasi Peminjaman Aset Baru* 🔔\n\n" .
            "Pengajuan baru telah masuk:\n" .
            "Nomor Surat: *{$nomor_surat_peminjaman}*\n\n" .
            "👤 *Nama:* {$nama_peminjam}\n" .
            "📞 *Telepon:* {$nomor_telepon_peminjam}\n\n" .
            "📦 *Aset yang Dipinjam:*\n" .
            "{$daftarItems}\n" .
            "Mohon untuk segera ditindaklanjuti melalui panel admin. Terima kasih.";

        sendWhatsApp($admin_phone, $messageToAdmin);
    } catch (Exception $e) { /* Abaikan jika notifikasi gagal */
    }

    echo json_encode(['success' => true, 'message' => 'Permintaan peminjaman berhasil dikirim. Nomor Surat Anda: ' . $nomor_surat_peminjaman]);
} catch (Exception $e) {
    $pdo->rollBack();
    echo json_encode(['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()]);
}
