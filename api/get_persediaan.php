<?php
require '../config/db.php';

$limit = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page > 0) ? ($page - 1) * $limit : 0;

$search = isset($_GET['search']) ? $_GET['search'] : '';
$sort_by = isset($_GET['sort_by']) && in_array($_GET['sort_by'], ['nama_persediaan', 'stok']) ? $_GET['sort_by'] : 'id';
$sort_order = isset($_GET['sort_order']) && in_array(strtoupper($_GET['sort_order']), ['ASC', 'DESC']) ? $_GET['sort_order'] : 'DESC';

$sql = "SELECT * FROM persediaan";
$count_sql = "SELECT COUNT(*) FROM persediaan";

if (!empty($search)) {
    $sql .= " WHERE nama_persediaan LIKE :search";
    $count_sql .= " WHERE nama_persediaan LIKE :search";
}

$sql .= " ORDER BY $sort_by $sort_order LIMIT :limit OFFSET :offset";

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
$persediaan = $stmt->fetchAll(PDO::FETCH_ASSOC);

$count_stmt->execute();
$total_records = $count_stmt->fetchColumn();
$total_pages = ceil($total_records / $limit);

header('Content-Type: application/json');
echo json_encode([
    'data' => $persediaan,
    'pagination' => [
        'current_page' => $page,
        'total_pages' => $total_pages,
        'total_records' => $total_records
    ]
]);
