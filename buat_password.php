<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Generator Password Hash</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 flex items-center justify-center min-h-screen">
    <div class="bg-white p-8 rounded-lg shadow-md w-full max-w-md">
        <h1 class="text-2xl font-bold mb-4 text-center">Buat Password Hash</h1>
        <form method="POST">
            <div class="mb-4">
                <label for="password" class="block text-gray-700 text-sm font-bold mb-2">Masukkan Password:</label>
                <input type="text" id="password" name="password" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-teal-500" required>
            </div>
            <button type="submit" class="bg-teal-600 hover:bg-teal-700 text-white font-bold py-2 px-4 rounded w-full focus:outline-none focus:shadow-outline transition duration-300">
                Buat Hash
            </button>
        </form>

        <?php
        // Cek jika form telah disubmit
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST['password'])) {
            $password = $_POST['password'];
            // Membuat hash dari password menggunakan algoritma default yang aman
            $hash = password_hash($password, PASSWORD_DEFAULT);
        ?>
            <div class="mt-6 p-4 bg-green-100 border border-green-400 text-green-800 rounded">
                <p class="font-bold">Password Input:</p>
                <p class="mb-2"><?php echo htmlspecialchars($password); ?></p>
                <p class="font-bold">Hasil Hash (Kopi semua teks di bawah ini):</p>
                <textarea readonly class="w-full h-24 p-2 mt-1 bg-white border rounded resize-none focus:outline-none"><?php echo $hash; ?></textarea>
            </div>
        <?php
        }
        ?>
    </div>
</body>

</html>