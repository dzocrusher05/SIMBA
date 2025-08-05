<?php
require '../config/db.php';
header('Content-Type: application/json');

try {
    $limit = 10;
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $offset = ($page > 0) ? ($page - 1) * $limit : 0;
    $search = isset($_GET['search']) ? $_GET['search'] : '';

    $sql_base = "FROM permintaan_persediaan pp LEFT JOIN detail_permintaan dp ON pp.id = dp.permintaan_id LEFT JOIN persediaan p ON dp.persediaan_id = p.id";
    $where_clause = "";
    if (!empty($search)) {
        $where_clause = "WHERE pp.nama_pemohon LIKE :search OR p.nama_persediaan LIKE :search";
    }

    $sql = "SELECT pp.id, pp.nama_pemohon, pp.tanggal_permintaan, pp.status_permintaan, 
                   GROUP_CONCAT(CONCAT(dp.jumlah_diminta, ' ', p.satuan, ' ', p.nama_persediaan) SEPARATOR '; ') AS daftar_item
            $sql_base 
            $where_clause 
            GROUP BY pp.id
            ORDER BY pp.tanggal_permintaan DESC 
            LIMIT :limit OFFSET :offset";

    $count_sql = "SELECT COUNT(DISTINCT pp.id) FROM permintaan_persediaan pp LEFT JOIN detail_permintaan dp ON pp.id = dp.permintaan_id LEFT JOIN persediaan p ON dp.persediaan_id = p.id $where_clause";

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
    $permintaan = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $count_stmt->execute();
    $total_records = $count_stmt->fetchColumn();
    $total_pages = ceil($total_records / $limit);

    // Pastikan output selalu dalam format yang diharapkan
    echo json_encode([
        'data' => $permintaan,
        'pagination' => ['current_page' => $page, 'total_pages' => $total_pages]
    ]);
} catch (PDOException $e) {
    // Jika ada error database, kirim pesan error dalam format JSON
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage(), 'data' => [], 'pagination' => []]);
}
