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

<div class="flex">
    <?php require 'includes/sidebar.php'; ?>
    <main class="main-content flex-1 p-8">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-slate-800">Data Persediaan</h1>
            <div>
                <button id="open-add-modal" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg inline-flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                    </svg>
                    Tambah Item
                </button>
            </div>
        </div>
        <div class="mb-4">
            <input type="text" id="search-input" placeholder="Cari nama item..." class="w-full max-w-sm p-2 border border-gray-300 rounded-lg">
        </div>
        <div class="bg-white p-6 rounded-lg shadow-sm">
            <div class="overflow-x-auto">
                <table class="min-w-full bg-white">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="text-left py-3 px-4 uppercase font-semibold text-sm text-slate-600">No</th>
                            <th class="text-left py-3 px-4 uppercase font-semibold text-sm text-slate-600 cursor-pointer hover:bg-gray-200 sortable" data-sort="nama_persediaan">Nama Item</th>
                            <th class="text-left py-3 px-4 uppercase font-semibold text-sm text-slate-600 cursor-pointer hover:bg-gray-200 sortable" data-sort="stok">Stok</th>
                            <th class="text-left py-3 px-4 uppercase font-semibold text-sm text-slate-600">Satuan</th>
                            <th class="text-center py-3 px-4 uppercase font-semibold text-sm text-slate-600">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="table-body"></tbody>
                </table>
            </div>
            <div id="pagination-container" class="mt-4 flex justify-end"></div>
        </div>
    </main>
</div>

<div id="form-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden">
    <div class="bg-white p-6 rounded-lg shadow-xl w-full max-w-md">
        <h2 id="modal-title" class="text-2xl font-bold mb-4">Tambah Item Baru</h2>
        <form id="persediaan-form">
            <input type="hidden" id="edit_id" name="edit_id">
            <div class="mb-4">
                <label for="nama_persediaan" class="block text-sm font-medium text-gray-700">Nama Item</label>
                <input type="text" id="nama_persediaan" name="nama_persediaan" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm">
            </div>
            <div class="mb-4">
                <label for="stok" class="block text-sm font-medium text-gray-700">Stok</label>
                <input type="number" id="stok" name="stok" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm">
            </div>
            <div class="mb-4">
                <label for="satuan" class="block text-sm font-medium text-gray-700">Satuan (cth: Rim, Box, Buah)</label>
                <input type="text" id="satuan" name="satuan" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm">
            </div>
            <div class="flex justify-end gap-4">
                <button type="button" id="close-modal-btn" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300">Batal</button>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Simpan</button>
            </div>
        </form>
    </div>
</div>

<div id="delete-confirm-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden">
    <div class="bg-white p-6 rounded-lg shadow-xl w-full max-w-sm">
        <h2 class="text-xl font-bold mb-4">Konfirmasi Hapus</h2>
        <p class="mb-6 text-gray-700">Apakah Anda yakin ingin menghapus item ini?</p>
        <div class="flex justify-end gap-4">
            <button id="cancel-delete-btn" class="px-4 py-2 bg-gray-200 rounded-lg">Batal</button>
            <button id="confirm-delete-btn" class="px-4 py-2 bg-red-600 text-white rounded-lg">Ya, Hapus</button>
        </div>
    </div>
</div>

<div id="input-masuk-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden">
    <div class="bg-white p-6 rounded-lg shadow-xl w-full max-w-md">
        <h2 class="text-2xl font-bold mb-4">Input Barang Masuk</h2>
        <form id="input-masuk-form">
            <input type="hidden" id="input_masuk_persediaan_id" name="input_masuk_persediaan_id">
            <p id="input_masuk_nama_item" class="text-lg font-semibold mb-4"></p>
            <div class="mb-4">
                <label for="input_masuk_jumlah" class="block text-sm font-medium text-gray-700">Jumlah Masuk</label>
                <input type="number" id="input_masuk_jumlah" name="jumlah" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm">
            </div>
            <div class="mb-4">
                <label for="input_masuk_nomor_dokumen" class="block text-sm font-medium text-gray-700">Nomor Surat/Dokumen</label>
                <input type="text" id="input_masuk_nomor_dokumen" name="nomor_dokumen" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm">
            </div>
            <div class="mb-4">
                <label for="input_masuk_keterangan" class="block text-sm font-medium text-gray-700">Keterangan (Opsional)</label>
                <input type="text" id="input_masuk_keterangan" name="keterangan" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm">
            </div>
            <div class="flex justify-end gap-4">
                <button type="button" id="close-input-masuk-modal" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300">Batal</button>
                <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">Simpan</button>
            </div>
        </form>
    </div>
</div>

<div id="riwayat-stok-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden">
    <div class="bg-white p-6 rounded-lg shadow-xl w-full max-w-3xl max-h-[90vh] overflow-y-auto">
        <div class="flex justify-between items-center mb-4">
            <h2 id="riwayat-stok-title" class="text-2xl font-bold">Riwayat Stok</h2>
            <div class="flex items-center gap-2">
                <button id="print-riwayat-stok-btn" class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold py-2 px-4 rounded-lg">Cetak PDF</button>
                <button id="close-riwayat-stok-modal" class="text-gray-500 hover:text-gray-800 text-2xl">&times;</button>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="py-2 px-4 text-left text-sm font-semibold">Tanggal</th>
                        <th class="py-2 px-4 text-left text-sm font-semibold">Jenis Transaksi</th>
                        <th class="py-2 px-4 text-left text-sm font-semibold">Jumlah</th>
                        <th class="py-2 px-4 text-left text-sm font-semibold">Keterangan</th>
                        <th class="py-2 px-4 text-left text-sm font-semibold">Nomor Dokumen</th>
                    </tr>
                </thead>
                <tbody id="riwayat-stok-table-body"></tbody>
            </table>
        </div>
    </div>
</div>

<div id="toast-notification" class="fixed bottom-5 right-5 p-4 rounded-lg shadow-lg text-white hidden">
    <p id="toast-message"></p>
</div>

<script src="assets/js/persediaan.js"></script>
<?php require 'includes/footer.php'; ?>