<?php
require '../config/db.php';
header('Content-Type: application/json');

try {
    // Mengambil semua item persediaan dengan riwayatnya
    $stmt = $pdo->prepare("
        SELECT
            p.id,
            p.nama_persediaan,
            p.stok,
            p.satuan,
            rp.tanggal,
            rp.jenis_transaksi,
            rp.jumlah,
            rp.keterangan,
            rp.nomor_dokumen
        FROM persediaan p
        LEFT JOIN riwayat_persediaan rp ON p.id = rp.persediaan_id
        ORDER BY p.nama_persediaan ASC, rp.tanggal ASC
    ");
    $stmt->execute();
    $raw_data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Mengelompokkan data berdasarkan item persediaan
    $grouped_data = [];
    foreach ($raw_data as $row) {
        $item_id = $row['id'];
        if (!isset($grouped_data[$item_id])) {
            $grouped_data[$item_id] = [
                'id' => $row['id'],
                'nama_persediaan' => $row['nama_persediaan'],
                'stok' => $row['stok'],
                'satuan' => $row['satuan'],
                'riwayat' => [],
            ];
        }
        if ($row['tanggal']) { // Pastikan ada riwayat
            $grouped_data[$item_id]['riwayat'][] = [
                'tanggal' => $row['tanggal'],
                'jenis_transaksi' => $row['jenis_transaksi'],
                'jumlah' => $row['jumlah'],
                'keterangan' => $row['keterangan'],
                'nomor_dokumen' => $row['nomor_dokumen'],
            ];
        }
    }

    $final_data = array_values($grouped_data);

    echo json_encode(['success' => true, 'data' => $final_data]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Gagal mengambil data riwayat: ' . $e->getMessage()]);
}
