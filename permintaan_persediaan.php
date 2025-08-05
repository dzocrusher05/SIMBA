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
        <input type="hidden" id="admin-name" value="<?php echo htmlspecialchars($_SESSION['username']); ?>">

        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-slate-800">Manajemen Permintaan Persediaan</h1>
            <button id="open-add-modal" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                </svg>
                Buat Permintaan
            </button>
        </div>

        <div class="mb-4">
            <input type="text" id="search-input" placeholder="Cari nama pemohon atau item..." class="w-full max-w-sm p-2 border border-gray-300 rounded-lg">
        </div>

        <div class="bg-white p-6 rounded-lg shadow-sm">
            <div class="overflow-x-auto">
                <table class="min-w-full bg-white">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="py-3 px-4 text-left uppercase font-semibold text-sm">Item yang Diminta</th>
                            <th class="py-3 px-4 text-left uppercase font-semibold text-sm">Pemohon</th>
                            <th class="py-3 px-4 text-left uppercase font-semibold text-sm">Tgl Permintaan</th>
                            <th class="py-3 px-4 text-left uppercase font-semibold text-sm">Status</th>
                            <th class="py-3 px-4 text-center uppercase font-semibold text-sm">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="table-body"></tbody>
                </table>
            </div>
            <div id="pagination-container" class="mt-4 flex justify-end"></div>
        </div>
    </main>
</div>

<div id="add-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden">
    <div class="bg-white p-6 rounded-lg shadow-xl w-full max-w-md">
        <h2 class="text-2xl font-bold mb-4">Formulir Permintaan Persediaan</h2>
        <form id="add-form">
            <div class="mb-4">
                <label for="persediaan_id" class="block text-sm font-medium text-gray-700">Pilih Item</label>
                <select id="persediaan_id" name="persediaan_id" required class="mt-1 block w-full p-2 border border-gray-300 rounded-md"></select>
            </div>
            <div class="mb-4">
                <label for="jumlah_diminta">Jumlah yang Diminta</label>
                <input type="number" id="jumlah_diminta" name="jumlah_diminta" min="1" required class="mt-1 block w-full p-2 border rounded-md">
            </div>
            <div class="mb-4">
                <label for="nama_pemohon">Nama Pemohon</label>
                <input type="text" id="nama_pemohon" name="nama_pemohon" required class="mt-1 block w-full p-2 border rounded-md">
            </div>
            <div class="mb-4">
                <label for="nomor_telepon_pemohon">No. Telepon (WA)</label>
                <input type="text" id="nomor_telepon_pemohon" name="nomor_telepon_pemohon" required class="mt-1 block w-full p-2 border rounded-md">
            </div>
            <div class="flex justify-end gap-4">
                <button type="button" class="close-modal px-4 py-2 bg-gray-200 rounded-lg">Batal</button>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg">Kirim Permintaan</button>
            </div>
        </form>
    </div>
</div>

<div id="action-confirm-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden">
    <div class="bg-white p-6 rounded-lg shadow-xl w-full max-w-sm">
        <h2 id="modal-title" class="text-xl font-bold mb-4">Konfirmasi Aksi</h2>
        <p id="modal-message" class="mb-6 text-gray-700">Apakah Anda yakin?</p>
        <div class="flex justify-end gap-4">
            <button id="cancel-action-btn" class="px-4 py-2 bg-gray-200 rounded-lg">Batal</button>
            <button id="confirm-action-btn" class="px-4 py-2 text-white rounded-lg">Ya, Lanjutkan</button>
        </div>
    </div>
</div>

<div id="approve-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden">
    <div class="bg-white p-6 rounded-lg shadow-xl w-full max-w-lg">
        <h2 class="text-2xl font-bold mb-2">Proses Persetujuan Permintaan</h2>
        <form id="approve-form">
            <input type="hidden" id="approve_permintaan_id">
            <input type="hidden" id="tanda_tangan_admin" name="tanda_tangan_admin">

            <div class="mb-4">
                <div class="flex justify-between items-center mb-1">
                    <label for="nomor_spb_display" class="block text-sm font-medium text-gray-700">Nomor SPB</label>
                </div>
                <input type="text" id="nomor_spb_display" disabled class="mt-1 block w-full p-2 border rounded-md bg-gray-100">
            </div>

            <p class="text-sm mb-4 text-gray-600">Edit jumlah yang disetujui jika perlu.</p>
            <div id="approve-items-list" class="space-y-3 max-h-48 overflow-y-auto mb-4 border p-3 rounded-md bg-gray-50">
            </div>
            <div class="mt-4">
                <h3 class="font-semibold text-sm mb-1">Tanda Tangan Pengelola Gudang:</h3>
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

<script src="assets/js/permintaan.js"></script>
<?php require 'includes/footer.php'; ?>