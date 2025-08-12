<?php
header('Content-Type: application/json');
require '../config/db.php';

// Pengaturan untuk paginasi dan pencarian
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$search = isset($_GET['search']) ? $_GET['search'] : '';
$limit = 10; // Jumlah item per halaman
$offset = ($page - 1) * $limit;

try {
    // Query utama untuk mengambil data peminjaman dengan paginasi dan pencarian
    // Menggunakan DISTINCT untuk memastikan setiap peminjaman hanya muncul sekali
    $whereClause = '';
    if (!empty($search)) {
        // Pencarian bisa berdasarkan nama peminjam, nomor surat, atau nama aset
        $whereClause = "WHERE p.nama_peminjam LIKE :search OR p.nomor_surat LIKE :search OR a.nama_bmn LIKE :search";
    }

    $sql = "SELECT DISTINCT p.id, p.nomor_surat, p.nama_peminjam, p.lokasi_peminjaman, p.tanggal_pengajuan, p.tanggal_pinjam, p.tanggal_kembali, p.status_peminjaman 
            FROM peminjaman p
            LEFT JOIN detail_peminjaman dp ON p.id = dp.peminjaman_id
            LEFT JOIN aset a ON dp.aset_id = a.id
            $whereClause
            ORDER BY p.tanggal_pengajuan DESC, p.id DESC
            LIMIT :limit OFFSET :offset";

    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    if (!empty($search)) {
        $stmt->bindValue(':search', '%' . $search . '%');
    }
    $stmt->execute();
    $peminjaman_list = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Siapkan statement untuk mengambil detail aset untuk setiap peminjaman
    $stmt_detail = $pdo->prepare("
        SELECT a.nama_bmn, a.kode_bmn, a.merek, a.nup
        FROM detail_peminjaman dp
        JOIN aset a ON dp.aset_id = a.id
        WHERE dp.peminjaman_id = :peminjaman_id
    ");

    // Lakukan loop untuk setiap peminjaman dan tambahkan detail asetnya
    foreach ($peminjaman_list as $key => $peminjaman) {
        $stmt_detail->execute([':peminjaman_id' => $peminjaman['id']]);
        $aset_details = $stmt_detail->fetchAll(PDO::FETCH_ASSOC);
        // Tambahkan array baru 'detail_aset' yang berisi semua info aset
        $peminjaman_list[$key]['detail_aset'] = $aset_details;
    }

    // Query untuk menghitung total data (untuk paginasi)
    $sql_total = "SELECT COUNT(DISTINCT p.id) 
                  FROM peminjaman p
                  LEFT JOIN detail_peminjaman dp ON p.id = dp.peminjaman_id
                  LEFT JOIN aset a ON dp.aset_id = a.id
                  $whereClause";
    $stmt_total = $pdo->prepare($sql_total);
    if (!empty($search)) {
        $stmt_total->bindValue(':search', '%' . $search . '%');
    }
    $stmt_total->execute();
    $total_results = $stmt_total->fetchColumn();
    $total_pages = ceil($total_results / $limit);

    // Kirim respons JSON yang lengkap
    echo json_encode([
        'success' => true,
        'data' => $peminjaman_list,
        'pagination' => [
            'current_page' => $page,
            'total_pages' => (int)$total_pages,
            'total_results' => (int)$total_results
        ]
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Gagal mengambil data peminjaman: ' . $e->getMessage()
    ]);
}
