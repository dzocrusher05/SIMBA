<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
require 'includes/head.php';
?>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

<div class="flex">
    <?php require 'includes/sidebar.php'; ?>
    <main class="main-content flex-1 p-8">
        <h1 class="text-3xl font-bold text-slate-800 mb-6">Laporan</h1>

        <div class="bg-white p-6 rounded-lg shadow-sm mb-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                <div>
                    <label for="jenis_laporan" class="block text-sm font-medium text-gray-700">Jenis Laporan</label>
                    <select id="jenis_laporan" class="mt-1 block w-full p-2 border border-gray-300 rounded-md">
                        <option value="peminjaman">Peminjaman BMN</option>
                        <option value="permintaan">Permintaan Persediaan</option>
                    </select>
                </div>
                <div>
                    <label for="tanggal_mulai" class="block text-sm font-medium text-gray-700">Tanggal Mulai</label>
                    <input type="text" id="tanggal_mulai" placeholder="Pilih tanggal..." class="mt-1 block w-full p-2 border border-gray-300 rounded-md">
                </div>
                <div>
                    <label for="tanggal_selesai" class="block text-sm font-medium text-gray-700">Tanggal Selesai</label>
                    <input type="text" id="tanggal_selesai" placeholder="Pilih tanggal..." class="mt-1 block w-full p-2 border border-gray-300 rounded-md">
                </div>
                <div class="flex gap-x-2">
                    <button id="tampilkan-btn" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg">Tampilkan</button>

                    <button id="pdf-btn" class="w-full bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded-lg flex items-center justify-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd" />
                        </svg>
                        PDF
                    </button>
                </div>
            </div>
        </div>

        <div id="hasil-laporan-container" class="bg-white p-6 rounded-lg shadow-sm">
            <h2 id="judul-laporan" class="text-xl font-bold mb-4 text-center"></h2>
            <p id="periode-laporan" class="text-sm text-center text-gray-500 mb-4"></p>
            <div id="tabel-laporan-wrapper" class="overflow-x-auto">
                <p class="text-center text-gray-500">Silakan pilih jenis laporan dan rentang tanggal, lalu klik "Tampilkan".</p>
            </div>
            <button id="export-btn" class="hidden mt-4 bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-lg">Export ke Excel</button>
        </div>
    </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/xlsx/dist/xlsx.full.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.23/jspdf.plugin.autotable.min.js"></script>

<script src="assets/js/laporan.js"></script>
<?php require 'includes/footer.php'; ?>