<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
require 'includes/head.php';
?>

<div class="flex">
    <?php require 'includes/sidebar.php'; ?>

    <main class="main-content flex-1 p-8">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-slate-800">Dashboard SIMBA</h1>
            <p id="last-updated" class="text-sm text-gray-400"></p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">

            <a href="data_aset.php" class="block bg-white p-6 rounded-lg shadow-sm transition-all duration-300 hover:shadow-lg hover:-translate-y-1">
                <div class="flex items-center gap-x-4">
                    <div class="bg-blue-100 p-3 rounded-full">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Total Aset</p>
                        <p id="total-aset" class="text-2xl font-bold text-gray-800">0</p>
                    </div>
                </div>
            </a>

            <a href="data_persediaan.php" class="block bg-white p-6 rounded-lg shadow-sm transition-all duration-300 hover:shadow-lg hover:-translate-y-1">
                <div class="flex items-center gap-x-4">
                    <div class="bg-green-100 p-3 rounded-full">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Jenis Persediaan</p>
                        <p id="total-persediaan" class="text-2xl font-bold text-gray-800">0</p>
                    </div>
                </div>
            </a>

            <a href="peminjaman_bmn.php" class="block bg-white p-6 rounded-lg shadow-sm transition-all duration-300 hover:shadow-lg hover:-translate-y-1">
                <div class="flex items-center gap-x-4">
                    <div class="bg-yellow-100 p-3 rounded-full">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Peminjaman Pending</p>
                        <p id="peminjaman-pending" class="text-2xl font-bold text-gray-800">0</p>
                    </div>
                </div>
            </a>

            <a href="permintaan_persediaan.php" class="block bg-white p-6 rounded-lg shadow-sm transition-all duration-300 hover:shadow-lg hover:-translate-y-1">
                <div class="flex items-center gap-x-4">
                    <div class="bg-orange-100 p-3 rounded-full">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-orange-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Permintaan Pending</p>
                        <p id="permintaan-pending" class="text-2xl font-bold text-gray-800">0</p>
                    </div>
                </div>
            </a>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="bg-white p-6 rounded-lg shadow-sm lg:col-span-1">
                <h3 class="font-semibold mb-4">Status Ketersediaan Aset</h3>
                <div class="max-w-xs mx-auto"><canvas id="asetStatusChart"></canvas></div>
            </div>

            <div class="lg:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="bg-white p-6 rounded-lg shadow-sm">
                    <h3 class="font-semibold mb-4">Riwayat Peminjaman Terbaru</h3>
                    <div id="riwayat-peminjaman-list" class="space-y-4"></div>
                </div>
                <div class="bg-white p-6 rounded-lg shadow-sm">
                    <h3 class="font-semibold mb-4">Riwayat Permintaan Terbaru</h3>
                    <div id="riwayat-permintaan-list" class="space-y-4"></div>
                </div>
            </div>
        </div>
    </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="assets/js/dashboard.js"></script>
<?php require 'includes/footer.php'; ?>