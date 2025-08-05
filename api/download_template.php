<?php
require '../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$type = $_GET['type'] ?? null;

if (!in_array($type, ['persediaan', 'aset'])) {
    die('Tipe template tidak valid.');
}

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

if ($type === 'persediaan') {
    $sheet->setCellValue('A1', 'Nama_Persediaan');
    $sheet->setCellValue('B1', 'Stok');
    $sheet->setCellValue('C1', 'Satuan');
    $filename = 'template_persediaan.xlsx';
} elseif ($type === 'aset') {
    $sheet->setCellValue('A1', 'No_BMN');
    $sheet->setCellValue('B1', 'Nama_BMN');
    $filename = 'template_aset.xlsx';
}

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="' . $filename . '"');
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
