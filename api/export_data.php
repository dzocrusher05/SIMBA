<?php
require '../config/db.php';
require '../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$type = $_GET['type'] ?? null;

if (!in_array($type, ['persediaan', 'aset'])) {
    die('Tipe data tidak valid.');
}

try {
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    if ($type === 'persediaan') {
        $stmt = $pdo->query("SELECT nama_persediaan, stok, satuan FROM persediaan");
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $header = ['Nama Persediaan', 'Stok', 'Satuan'];
        $sheet->fromArray($header, NULL, 'A1');
        $sheet->fromArray($data, NULL, 'A2');

        $filename = 'data_persediaan.xlsx';
    } elseif ($type === 'aset') {
        $stmt = $pdo->query("SELECT kode_bmn, nup, nama_bmn, merek, status FROM aset");
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $header = ['Kode BMN', 'NUP', 'Nama BMN', 'Merek', 'Status'];
        $sheet->fromArray($header, NULL, 'A1');
        $sheet->fromArray($data, NULL, 'A2');

        $filename = 'data_aset.xlsx';
    }

    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="' . $filename . '"');
    header('Cache-Control: max-age=0');

    $writer = new Xlsx($spreadsheet);
    $writer->save('php://output');
    exit;
} catch (Exception $e) {
    die('Gagal mengekspor data: ' . $e->getMessage());
}
