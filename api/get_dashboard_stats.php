<?php
require '../config/db.php';
header('Content-Type: application/json');

try {
    // 1. Total Aset
    $stmt_total_aset = $pdo->query("SELECT COUNT(*) FROM aset");
    $total_aset = $stmt_total_aset->fetchColumn();

    // 2. Status Aset (untuk diagram)
    $stmt_status_aset = $pdo->query("SELECT status, COUNT(*) as jumlah FROM aset GROUP BY status");
    $status_aset_data = $stmt_status_aset->fetchAll(PDO::FETCH_KEY_PAIR);
    $aset_tersedia = $status_aset_data['Tersedia'] ?? 0;
    $aset_dipinjam = $status_aset_data['Dipinjam'] ?? 0;

    // 3. Total Jenis Persediaan
    $stmt_total_persediaan = $pdo->query("SELECT COUNT(*) FROM persediaan");
    $total_persediaan = $stmt_total_persediaan->fetchColumn();

    // 4. Peminjaman BMN yang Perlu Diproses
    $stmt_peminjaman_pending = $pdo->query("SELECT COUNT(*) FROM peminjaman WHERE status_peminjaman = 'Diajukan'");
    $peminjaman_pending = $stmt_peminjaman_pending->fetchColumn();

    // 5. Permintaan Persediaan yang Perlu Diproses
    $stmt_permintaan_pending = $pdo->query("SELECT COUNT(*) FROM permintaan_persediaan WHERE status_permintaan = 'Diajukan'");
    $permintaan_pending = $stmt_permintaan_pending->fetchColumn();

    // 6. Ambil 5 Riwayat Peminjaman Terakhir
    $stmt_riwayat_peminjaman = $pdo->query("
        SELECT p.nama_peminjam, p.status_peminjaman, GROUP_CONCAT(a.nama_bmn SEPARATOR ', ') AS daftar_aset
        FROM peminjaman p
        LEFT JOIN detail_peminjaman dp ON p.id = dp.peminjaman_id
        LEFT JOIN aset a ON dp.aset_id = a.id
        GROUP BY p.id
        ORDER BY p.tanggal_pengajuan DESC, p.id DESC
        LIMIT 5
    ");
    $riwayat_peminjaman = $stmt_riwayat_peminjaman->fetchAll(PDO::FETCH_ASSOC);

    // 7. Ambil 5 Riwayat Permintaan Terakhir
    $stmt_riwayat_permintaan = $pdo->query("
        SELECT pp.nama_pemohon, pp.status_permintaan, GROUP_CONCAT(CONCAT(dp.jumlah_diminta, ' ', ps.satuan, ' ', ps.nama_persediaan) SEPARATOR '; ') AS daftar_item
        FROM permintaan_persediaan pp
        LEFT JOIN detail_permintaan dp ON pp.id = dp.permintaan_id
        LEFT JOIN persediaan ps ON dp.persediaan_id = ps.id
        GROUP BY pp.id
        ORDER BY pp.tanggal_permintaan DESC, pp.id DESC
        LIMIT 5
    ");
    $riwayat_permintaan = $stmt_riwayat_permintaan->fetchAll(PDO::FETCH_ASSOC);


    // Gabungkan semua data ke dalam satu array
    $stats = [
        'total_aset' => (int)$total_aset,
        'aset_tersedia' => (int)$aset_tersedia,
        'aset_dipinjam' => (int)$aset_dipinjam,
        'total_persediaan' => (int)$total_persediaan,
        'peminjaman_pending' => (int)$peminjaman_pending,
        'permintaan_pending' => (int)$permintaan_pending,
        'riwayat_peminjaman' => $riwayat_peminjaman,
        'riwayat_permintaan' => $riwayat_permintaan
    ];

    echo json_encode(['success' => true, 'data' => $stats]);
} catch (PDOException $e) {
    // Kirim pesan error yang jelas jika query gagal
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Query Database Gagal: ' . $e->getMessage()]);
}
