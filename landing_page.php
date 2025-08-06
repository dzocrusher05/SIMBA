<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Selamat Datang di SIMBA</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="assets/css/landing_page.css">
</head>

<body class="bg-gray-100 min-h-screen flex items-center justify-center">

    <div class="container mx-auto p-4 md:p-8 max-w-4xl">
        <div class="text-center mb-10">
            <h1 class="text-4xl md:text-5xl font-bold text-slate-800 mb-2">SIMBA</h1>
            <p class="text-lg text-slate-500">Sistem Informasi Manajemen Barang Aset</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">

            <a href="ajukan_permintaan.php" class="card-action">
                <div class="bg-teal-100 p-4 rounded-full mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-teal-600 mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 13h.01M10 5a2 2 0 012-2h2a2 2 0 012 2v12a2 2 0 01-2 2H6a2 2 0 01-2-2V5a2 2 0 012-2z" />
                    </svg>
                </div>
                <h3 class="font-bold text-xl mb-2 text-slate-800">Permintaan Persediaan</h3>
                <p class="text-sm text-slate-600">Ajukan permintaan barang habis pakai.</p>
            </a>

            <a href="ajukan_peminjaman.php" class="card-action">
                <div class="bg-blue-100 p-4 rounded-full mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-blue-600 mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 7v4a1 1 0 001 1h4a1 1 0 001-1V7m0 0a2 2 0 100-4 2 2 0 000 4zM8 7a2 2 0 100-4 2 2 0 000 4zm0 10H5a2 2 0 00-2 2v2a2 2 0 002 2h4a2 2 0 002-2v-2a2 2 0 00-2-2h-3zm2 2a2 2 0 100-4 2 2 0 000 4zM16 17H9a2 2 0 01-2-2v-2a2 2 0 012-2h7a2 2 0 012 2v2a2 2 0 01-2 2z" />
                    </svg>
                </div>
                <h3 class="font-bold text-xl mb-2 text-slate-800">Peminjaman BMN</h3>
                <p class="text-sm text-slate-600">Ajukan peminjaman barang aset.</p>
            </a>

            <div class="card-action disabled">
                <div class="bg-red-100 p-4 rounded-full mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-red-600 mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
                <h3 class="font-bold text-xl mb-2 text-slate-800">Aduan Kerusakan</h3>
                <p class="text-sm text-slate-600">Segera hadir.</p>
            </div>

            <div class="card-action disabled">
                <div class="bg-gray-100 p-4 rounded-full mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-gray-600 mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                </div>
                <h3 class="font-bold text-xl mb-2 text-slate-800">Usulan Pengadaan</h3>
                <p class="text-sm text-slate-600">Segera hadir.</p>
            </div>

        </div>

        <div class="mt-10 text-center">
            <p class="text-sm text-slate-600 mb-2">Sudah punya akun? <a href="login.php" class="font-bold text-blue-600 hover:underline">Masuk</a></p>
        </div>
    </div>

</body>

</html>