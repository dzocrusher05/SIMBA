<?php
require '../config/db.php';
header('Content-Type: application/json');

$limit = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page > 0) ? ($page - 1) * $limit : 0;
$search = isset($_GET['search']) ? $_GET['search'] : '';

// Bagian ini sangat penting untuk diperiksa
$sql_base = "FROM peminjaman p JOIN detail_peminjaman dp ON p.id = dp.peminjaman_id JOIN aset a ON dp.aset_id = a.id";
$where_clause = "";
if (!empty($search)) {
    // Pastikan pencarian dilakukan di beberapa kolom yang relevan
    $where_clause = "WHERE p.nama_peminjam LIKE :search OR a.nama_bmn LIKE :search";
}

// PASTIKAN ADA "p.*" DI SINI UNTUK MENGAMBIL SEMUA KOLOM DARI TABEL PEMINJAMAN
$sql = "SELECT 
            p.*, 
            GROUP_CONCAT(a.nama_bmn SEPARATOR ', ') AS daftar_aset
        $sql_base
        $where_clause
        GROUP BY
            p.id
        ORDER BY 
            p.tanggal_pengajuan DESC
        LIMIT :limit OFFSET :offset";

$count_sql = "SELECT COUNT(DISTINCT p.id) 
              FROM peminjaman p 
              JOIN detail_peminjaman dp ON p.id = dp.peminjaman_id 
              JOIN aset a ON dp.aset_id = a.id 
              $where_clause";

$stmt = $pdo->prepare($sql);
$count_stmt = $pdo->prepare($count_sql);

if (!empty($search)) {
    $search_param = "%$search%";
    $stmt->bindParam(':search', $search_param);
    $count_stmt->bindParam(':search', $search_param);
}
$stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
$stmt->bindParam(':offset', $offset, PDO::PARAM_INT);

$stmt->execute();
$peminjaman = $stmt->fetchAll(PDO::FETCH_ASSOC);

$count_stmt->execute();
$total_records = $count_stmt->fetchColumn();
$total_pages = ceil($total_records / $limit);

echo json_encode([
    'data' => $peminjaman,
    'pagination' => ['current_page' => $page, 'total_pages' => $total_pages, 'total_records' => $total_records]
]);
