<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
require 'includes/head.php';
?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.23/jspdf.plugin.autotable.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>

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

<div id="approve-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden">
    <div class="bg-white p-6 rounded-lg shadow-xl w-full max-w-lg">
        <h2 class="text-2xl font-bold mb-2">Proses Persetujuan Peminjaman</h2>
        <form id="approve-form">
            <input type="hidden" id="approve_peminjaman_id">
            <input type="hidden" id="tanda_tangan_admin" name="tanda_tangan_admin">

            <p class="text-sm mb-4 text-gray-600">Aset yang dipinjam akan ditampilkan di bawah ini.</p>
            <div id="approve-items-list" class="space-y-3 max-h-48 overflow-y-auto mb-4 border p-3 rounded-md bg-gray-50">
            </div>

            <div class="mt-4">
                <h3 class="font-semibold text-sm mb-1">Tanda Tangan Admin:</h3>
                <div class="border rounded-lg bg-white">
                    <canvas id="admin-signature-pad" class="w-full h-32"></canvas>
                </div>
                <button type="button" id="clear-admin-signature" class="text-sm text-blue-600 hover:underline mt-1">Bersihkan</button>
            </div>
            <div class="flex justify-end gap-4 mt-6">
                <button type="button" class="close-approve-modal px-4 py-2 bg-gray-200 rounded-lg">Batal</button>
                <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg">Konfirmasi & Setujui</button>
            </div>
        </form>
    </div>
</div>

<div id="toast-notification" class="fixed bottom-5 right-5 p-4 rounded-lg shadow-lg text-white hidden">
    <p id="toast-message"></p>
</div>

<script src="assets/js/peminjaman.js"></script>
<?php require 'includes/footer.php'; ?>