<?php
require '../config/db.php';
header('Content-Type: application/json');

$jenis = $_GET['jenis_laporan'] ?? 'peminjaman';
$mulai = $_GET['tanggal_mulai'] ?? '';
$selesai = $_GET['tanggal_selesai'] ?? '';

if (empty($mulai) || empty($selesai)) {
    echo json_encode(['success' => false, 'message' => 'Rentang tanggal harus diisi.']);
    exit;
}

$sql = "";
switch ($jenis) {
    case 'peminjaman':
        $sql = "SELECT p.nama_peminjam, p.lokasi_peminjaman, p.tanggal_pengajuan, p.tanggal_pinjam, p.tanggal_kembali, p.status_peminjaman, 
                       GROUP_CONCAT(a.nama_bmn SEPARATOR ', ') as daftar_aset
                FROM peminjaman p
                LEFT JOIN detail_peminjaman dp ON p.id = dp.peminjaman_id
                LEFT JOIN aset a ON dp.aset_id = a.id
                WHERE p.tanggal_pengajuan BETWEEN ? AND ?
                GROUP BY p.id
                ORDER BY p.tanggal_pengajuan ASC";
        break;

    case 'permintaan':
        $sql = "SELECT pp.nama_pemohon, pp.tanggal_permintaan, pp.status_permintaan,
                       GROUP_CONCAT(CONCAT(dp.jumlah_diminta, ' ', ps.satuan, ' ', ps.nama_persediaan) SEPARATOR '; ') as daftar_item
                FROM permintaan_persediaan pp
                LEFT JOIN detail_permintaan dp ON pp.id = dp.permintaan_id
                LEFT JOIN persediaan ps ON dp.persediaan_id = ps.id
                WHERE pp.tanggal_permintaan BETWEEN ? AND ?
                GROUP BY pp.id
                ORDER BY pp.tanggal_permintaan ASC";
        break;

    default:
        echo json_encode(['success' => false, 'message' => 'Jenis laporan tidak valid.']);
        exit;
}

try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$mulai, $selesai]);
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode(['success' => true, 'data' => $data]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Query gagal: ' . $e->getMessage()]);
}
