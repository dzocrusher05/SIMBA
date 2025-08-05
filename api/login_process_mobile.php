    <?php
    // Memulai session
    session_start();

    // Memanggil file koneksi database
    require '../config/db.php';

    // Mengatur header untuk respons JSON
    header('Content-Type: application/json');
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type, Authorization');

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';

        if (empty($username) || empty($password)) {
            echo json_encode(['success' => false, 'message' => 'Username dan password harus diisi.']);
            exit;
        }

        $sql = "SELECT id, password FROM users WHERE username = :username";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            // Login berhasil, kirim respons JSON
            echo json_encode(['success' => true, 'message' => 'Login berhasil.']);
        } else {
            // Login gagal, kirim pesan error JSON
            echo json_encode(['success' => false, 'message' => 'Username atau password salah.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Metode request tidak valid.']);
    }
    ?>