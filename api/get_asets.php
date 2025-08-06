<?php
require '../config/db.php';

// --- Pengaturan Paginasi ---
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page > 0) ? ($page - 1) * $limit : 0;

// --- Pengaturan Filter & Sort ---
$search = isset($_GET['search']) ? $_GET['search'] : '';
$sort_by = isset($_GET['sort_by']) && in_array($_GET['sort_by'], ['kode_bmn', 'nup', 'nama_bmn', 'merek', 'status']) ? $_GET['sort_by'] : 'id';
$sort_order = isset($_GET['sort_order']) && in_array(strtoupper($_GET['sort_order']), ['ASC', 'DESC']) ? $_GET['sort_order'] : 'DESC';

// --- Membangun Query ---
$sql = "SELECT id, kode_bmn, nup, nama_bmn, merek, status FROM aset";
$count_sql = "SELECT COUNT(*) FROM aset";

// Tambahkan filter jika ada
if (!empty($search)) {
    $sql .= " WHERE nama_bmn LIKE :search OR kode_bmn LIKE :search OR nup LIKE :search OR merek LIKE :search";
    $count_sql .= " WHERE nama_bmn LIKE :search OR kode_bmn LIKE :search OR nup LIKE :search OR merek LIKE :search";
}

// Tambahkan sorting
$sql .= " ORDER BY $sort_by $sort_order";

// Tambahkan limit untuk paginasi
$sql .= " LIMIT :limit OFFSET :offset";

$stmt = $pdo->prepare($sql);
$count_stmt = $pdo->prepare($count_sql);

// Bind parameter search jika ada
if (!empty($search)) {
    $search_param = "%$search%";
    $stmt->bindParam(':search', $search_param);
    $count_stmt->bindParam(':search', $search_param);
}

// Bind parameter paginasi
$stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
$stmt->bindParam(':offset', $offset, PDO::PARAM_INT);

$stmt->execute();
$asets = $stmt->fetchAll(PDO::FETCH_ASSOC);

$count_stmt->execute();
$total_records = $count_stmt->fetchColumn();
$total_pages = ceil($total_records / $limit);

// --- Mengirim Respons JSON ---
header('Content-Type: application/json');
echo json_encode([
    'data' => $asets,
    'pagination' => [
        'current_page' => $page,
        'total_pages' => $total_pages,
        'total_records' => $total_records
    ]
]);
