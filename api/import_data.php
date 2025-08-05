<?php
set_time_limit(300); // Batas waktu eksekusi 5 menit
require '../config/db.php';
require '../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['excel_file'])) {
    $file_type = $_POST['file_type'] ?? '';

    if (!in_array($file_type, ['persediaan', 'aset'])) {
        echo json_encode(['success' => false, 'message' => 'Tipe file tidak valid.']);
        exit;
    }

    $filePath = $_FILES['excel_file']['tmp_name'];

    try {
        $spreadsheet = IOFactory::load($filePath);
        $worksheet = $spreadsheet->getActiveSheet();
        $rows = $worksheet->toArray();

        // Hapus baris header
        array_shift($rows);

        $pdo->beginTransaction();

        if ($file_type === 'persediaan') {
            $stmt = $pdo->prepare("INSERT INTO persediaan (nama_persediaan, stok, satuan) VALUES (?, ?, ?)");
            foreach ($rows as $row) {
                if (!empty($row[0])) {
                    $stmt->execute([$row[0], $row[1], $row[2]]);
                }
            }
            echo json_encode(['success' => true, 'message' => 'Data persediaan berhasil diimpor!']);
        } elseif ($file_type === 'aset') {
            $stmt = $pdo->prepare("INSERT INTO aset (no_bmn, nama_bmn, status) VALUES (?, ?, ?)");
            foreach ($rows as $row) {
                if (!empty($row[0])) {
                    $stmt->execute([$row[0], $row[1], 'Tersedia']);
                }
            }
            echo json_encode(['success' => true, 'message' => 'Data aset berhasil diimpor!']);
        }

        $pdo->commit();
    } catch (Exception $e) {
        $pdo->rollBack();
        echo json_encode(['success' => false, 'message' => 'Gagal mengimpor data: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Metode atau file tidak valid.']);
}
