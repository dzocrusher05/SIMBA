<?php
// Selalu set header ke JSON di baris paling atas
header('Content-Type: application/json');

// Memanggil file koneksi database PDO
// Pastikan path ini sudah benar sesuai struktur folder Anda
require_once '../config/db.php';

// Cek apakah variabel $pdo dari db.php berhasil dibuat
if (!isset($pdo)) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Koneksi database (PDO) tidak ditemukan. Periksa file config/db.php.'
    ]);
    exit();
}

try {
    // Query yang benar menggunakan nama kolom `nama_bmn` dan `kode_bmn`
    $sql = "SELECT id, nama_bmn, kode_bmn, nup, merek FROM aset WHERE status = 'Tersedia' ORDER BY nama_bmn ASC";

    // Menyiapkan dan mengeksekusi statement menggunakan PDO
    $stmt = $pdo->query($sql);

    // Mengambil semua data sebagai associative array
    $aset_list = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($aset_list) {
        // Jika data ditemukan, kirim respons sukses
        echo json_encode([
            'status' => 'success',
            'data' => $aset_list
        ]);
    } else {
        // Jika tidak ada aset yang berstatus 'Tersedia'
        echo json_encode([
            'status' => 'error',
            'message' => 'Tidak ada aset yang tersedia untuk dipinjam saat ini.'
        ]);
    }
} catch (\PDOException $e) {
    // Jika terjadi error saat menjalankan query
    http_response_code(500); // Server error
    echo json_encode([
        'status' => 'error',
        'message' => 'Query ke database gagal: ' . $e->getMessage()
    ]);
}
