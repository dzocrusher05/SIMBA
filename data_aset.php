<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Panggil head
require 'includes/head.php';
?>

<div class="flex">
    <?php require 'includes/sidebar.php'; ?>

    <main class="main-content flex-1 p-8">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-slate-800">Data Aset</h1>
            <button id="open-add-modal" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                </svg>
                Tambah Aset
            </button>
        </div>

        <div class="mb-4">
            <input type="text" id="search-aset" placeholder="Cari berdasarkan Nama atau No. BMN..." class="w-full max-w-sm p-2 border border-gray-300 rounded-lg">
        </div>

        <div class="bg-white p-6 rounded-lg shadow-sm">
            <div class="overflow-x-auto">
                <table class="min-w-full bg-white">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="text-left py-3 px-4 uppercase font-semibold text-sm text-slate-600">No</th>
                            <th class="text-left py-3 px-4 uppercase font-semibold text-sm text-slate-600 cursor-pointer hover:bg-gray-200 sortable" data-sort="no_bmn">No. BMN</th>
                            <th class="text-left py-3 px-4 uppercase font-semibold text-sm text-slate-600 cursor-pointer hover:bg-gray-200 sortable" data-sort="nama_bmn">Nama BMN</th>
                            <th class="text-left py-3 px-4 uppercase font-semibold text-sm text-slate-600 cursor-pointer hover:bg-gray-200 sortable" data-sort="status">Status</th>
                            <th class="text-center py-3 px-4 uppercase font-semibold text-sm text-slate-600">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="aset-table-body">
                        <tr>
                            <td colspan="5" class="text-center py-4">Memuat data...</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div id="pagination-container" class="mt-4 flex justify-end"></div>
        </div>
    </main>
</div>

<div id="add-aset-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden">
    <div class="bg-white p-6 rounded-lg shadow-xl w-full max-w-md">
        <h2 class="text-2xl font-bold mb-4">Tambah Aset Baru</h2>
        <form id="add-aset-form">
            <div class="mb-4">
                <label for="no_bmn" class="block text-sm font-medium text-gray-700">No. BMN</label>
                <input type="text" id="no_bmn" name="no_bmn" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div class="mb-4">
                <label for="nama_bmn" class="block text-sm font-medium text-gray-700">Nama BMN</label>
                <input type="text" id="nama_bmn" name="nama_bmn" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div class="flex justify-end gap-4">
                <button type="button" class="close-modal px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300">Batal</button>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Simpan</button>
            </div>
        </form>
    </div>
</div>

<div id="edit-aset-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden">
    <div class="bg-white p-6 rounded-lg shadow-xl w-full max-w-md">
        <h2 class="text-2xl font-bold mb-4">Edit Aset</h2>
        <form id="edit-aset-form">
            <input type="hidden" id="edit_aset_id" name="edit_aset_id">
            <div class="mb-4">
                <label for="edit_no_bmn" class="block text-sm font-medium text-gray-700">No. BMN</label>
                <input type="text" id="edit_no_bmn" name="edit_no_bmn" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm">
            </div>
            <div class="mb-4">
                <label for="edit_nama_bmn" class="block text-sm font-medium text-gray-700">Nama BMN</label>
                <input type="text" id="edit_nama_bmn" name="edit_nama_bmn" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm">
            </div>
            <div class="mb-4">
                <label for="edit_status" class="block text-sm font-medium text-gray-700">Status</label>
                <select id="edit_status" name="edit_status" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm">
                    <option value="Tersedia">Tersedia</option>
                    <option value="Dipinjam">Dipinjam</option>
                </select>
            </div>
            <div class="flex justify-end gap-4">
                <button type="button" class="close-modal px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300">Batal</button>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<div id="delete-confirm-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden">
    <div class="bg-white p-6 rounded-lg shadow-xl w-full max-w-sm">
        <h2 class="text-xl font-bold mb-4">Konfirmasi Hapus</h2>
        <p id="delete-confirm-message" class="mb-6 text-gray-700">Apakah Anda yakin ingin menghapus aset ini?</p>
        <div class="flex justify-end gap-4">
            <button id="cancel-delete-btn" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300">Batal</button>
            <button id="confirm-delete-btn" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">Ya, Hapus</button>
        </div>
    </div>
</div>

<div id="toast-notification" class="fixed bottom-5 right-5 p-4 rounded-lg shadow-lg text-white hidden">
    <p id="toast-message"></p>
</div>

<script src="assets/js/aset.js"></script>

<?php require 'includes/footer.php'; ?>