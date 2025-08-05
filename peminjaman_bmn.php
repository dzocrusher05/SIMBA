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
            <h1 class="text-3xl font-bold text-slate-800">Manajemen Peminjaman BMN</h1>
        </div>

        <div class="mb-4">
            <input type="text" id="search-input" placeholder="Cari nama peminjam atau aset..." class="w-full max-w-sm p-2 border border-gray-300 rounded-lg">
        </div>

        <div class="bg-white p-6 rounded-lg shadow-sm">
            <div class="overflow-x-auto">
                <table class="min-w-full bg-white">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="py-3 px-4 text-left uppercase font-semibold text-sm text-slate-600">Daftar Aset</th>
                            <th class="py-3 px-4 text-left uppercase font-semibold text-sm text-slate-600">Detail Peminjaman</th>
                            <th class="py-3 px-4 text-left uppercase font-semibold text-sm text-slate-600">Tgl Pinjam & Durasi</th>
                            <th class="py-3 px-4 text-left uppercase font-semibold text-sm text-slate-600">Status</th>
                            <th class="py-3 px-4 text-center uppercase font-semibold text-sm text-slate-600">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="table-body"></tbody>
                </table>
            </div>
            <div id="pagination-container" class="mt-4 flex justify-end"></div>
        </div>
    </main>
</div>

<div id="action-confirm-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden">
    <div class="bg-white p-6 rounded-lg shadow-xl w-full max-w-sm">
        <h2 id="modal-title" class="text-xl font-bold mb-4">Konfirmasi Aksi</h2>
        <p id="modal-message" class="mb-6 text-gray-700">Apakah Anda yakin?</p>
        <div class="flex justify-end gap-4">
            <button id="cancel-action-btn" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300">Batal</button>
            <button id="confirm-action-btn" class="px-4 py-2 text-white rounded-lg">Ya, Lanjutkan</button>
        </div>
    </div>
</div>
<div id="toast-notification" class="fixed bottom-5 right-5 p-4 rounded-lg shadow-lg text-white hidden">
    <p id="toast-message"></p>
</div>

<script src="assets/js/peminjaman.js"></script>
<?php require 'includes/footer.php'; ?>