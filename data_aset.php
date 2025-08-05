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
<script src="https://cdn.jsdelivr.net/npm/xlsx/dist/xlsx.full.min.js"></script>

<div class="flex">
    <?php require 'includes/sidebar.php'; ?>

    <main class="main-content flex-1 p-8">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-slate-800">Data Aset</h1>
            <div class="flex gap-2">
                <button id="open-add-modal" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                    </svg>
                    Tambah Aset
                </button>
                <button id="open-import-aset-modal-btn" class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded-lg inline-flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM6.293 6.707a1 1 0 010-1.414l3-3a1 1 0 011.414 0l3 3a1 1 0 01-1.414 1.414L11 5.414V13a1 1 0 11-2 0V5.414L7.707 6.707a1 1 0 01-1.414-1.414z" clip-rule="evenodd" />
                    </svg>
                    Import Excel
                </button>
                <a href="api/export_data.php?type=aset" id="export-aset-btn" class="bg-yellow-600 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded-lg inline-flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM6.293 6.707a1 1 0 010-1.414l3-3a1 1 0 011.414 0l3 3a1 1 0 01-1.414 1.414L11 5.414V13a1 1 0 11-2 0V5.414L7.707 6.707a1 1 0 01-1.414-1.414z" clip-rule="evenodd" />
                    </svg>
                    Export Data
                </a>
                <a href="api/download_template.php?type=aset" id="download-aset-template-btn" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-lg inline-flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM6.293 6.707a1 1 0 010-1.414l3-3a1 1 0 011.414 0l3 3a1 1 0 01-1.414 1.414L11 5.414V13a1 1 0 11-2 0V5.414L7.707 6.707a1 1 0 01-1.414-1.414z" clip-rule="evenodd" />
                    </svg>
                    Unduh Template
                </a>
            </div>
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

<div id="riwayat-aset-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden">
    <div class="bg-white p-6 rounded-lg shadow-xl w-full max-w-3xl max-h-[90vh] overflow-y-auto">
        <div class="flex justify-between items-center mb-4">
            <h2 id="riwayat-aset-title" class="text-2xl font-bold">Riwayat Aset</h2>
            <div class="flex items-center gap-2">
                <button id="print-riwayat-aset-btn" class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold py-2 px-4 rounded-lg">Cetak PDF</button>
                <button id="close-riwayat-aset-modal" class="text-gray-500 hover:text-gray-800 text-2xl">&times;</button>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="py-2 px-4 text-left text-sm font-semibold">Tanggal</th>
                        <th class="py-2 px-4 text-left text-sm font-semibold">Jenis Transaksi</th>
                        <th class="py-2 px-4 text-left text-sm font-semibold">Keterangan</th>
                        <th class="py-2 px-4 text-left text-sm font-semibold">Nomor Dokumen</th>
                    </tr>
                </thead>
                <tbody id="riwayat-aset-table-body"></tbody>
            </table>
        </div>
    </div>
</div>

<div id="import-aset-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden">
    <div class="bg-white p-6 rounded-lg shadow-xl w-full max-w-md">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-2xl font-bold">Import Data Aset</h2>
            <button id="close-import-aset-modal-btn" class="text-gray-500 hover:text-gray-800 text-2xl">&times;</button>
        </div>
        <form id="import-aset-form" enctype="multipart/form-data">
            <input type="hidden" name="file_type" value="aset">
            <div class="mb-4">
                <label for="aset-file" class="block text-sm font-medium text-gray-700">Pilih file Excel (.xlsx)</label>
                <input type="file" id="aset-file" name="excel_file" required accept=".xlsx" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
            </div>
            <div class="flex justify-end">
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Import</button>
            </div>
        </form>
    </div>
</div>


<div id="toast-notification" class="fixed bottom-5 right-5 p-4 rounded-lg shadow-lg text-white hidden">
    <p id="toast-message"></p>
</div>

<script src="assets/js/aset.js"></script>

<?php require 'includes/footer.php'; ?>