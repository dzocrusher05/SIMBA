<?php
require '../config/db.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_POST['user_id'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    if (empty($user_id) || empty($new_password) || empty($confirm_password)) {
        echo json_encode(['success' => false, 'message' => 'Semua kolom wajib diisi.']);
        exit;
    }

    if ($new_password !== $confirm_password) {
        echo json_encode(['success' => false, 'message' => 'Konfirmasi kata sandi tidak cocok.']);
        exit;
    }

    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

    try {
        $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
        $stmt->execute([$hashed_password, $user_id]);
        echo json_encode(['success' => true, 'message' => 'Kata sandi berhasil diperbarui.']);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Gagal memperbarui kata sandi.']);
    }
}
?>s