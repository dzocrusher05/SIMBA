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
        <h1 class="text-3xl font-bold text-slate-800 mb-6">Riwayat Pengaduan Kerusakan</h1>
        <div class="mb-4">
            <input type="text" id="search-input" placeholder="Cari aset atau deskripsi..." class="w-full max-w-sm p-2 border border-gray-300 rounded-lg">
        </div>
        <div class="bg-white p-6 rounded-lg shadow-sm">
            <div class="overflow-x-auto">
                <table class="min-w-full bg-white">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="py-3 px-4 text-left uppercase font-semibold text-sm">Aset</th>
                            <th class="py-3 px-4 text-left uppercase font-semibold text-sm">Deskripsi Laporan</th>
                            <th class="py-3 px-4 text-left uppercase font-semibold text-sm">Tanggal Lapor</th>
                            <th class="py-3 px-4 text-left uppercase font-semibold text-sm">Status</th>
                            <th class="py-3 px-4 text-center uppercase font-semibold text-sm">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="table-body">
                        <tr>
                            <td colspan="5" class="text-center py-4">Memuat data...</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>

<div id="detail-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden">
    <div class="bg-white p-6 rounded-lg shadow-xl w-full max-w-xl max-h-[90vh] overflow-y-auto">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-2xl font-bold">Detail Pengaduan</h2>
            <button id="close-detail-modal" class="text-gray-500 hover:text-gray-800 text-2xl">&times;</button>
        </div>
        <div id="detail-pengaduan-content"></div>
        <div id="processing-section" class="mt-6 border-t pt-4">
            <h3 class="text-xl font-bold mb-4">Proses Pengaduan</h3>
            <form id="proses-pengaduan-form">
                <input type="hidden" id="proses_pengaduan_id" name="id">
                <div class="space-y-4">
                    <div>
                        <label for="status_laporan" class="block font-medium">Status</label>
                        <select id="status_laporan" name="status_laporan" class="w-full p-2 border rounded-md">
                            <option value="diajukan">Diajukan</option>
                            <option value="diproses">Diproses</option>
                            <option value="selesai">Selesai</option>
                        </select>
                    </div>
                    <div>
                        <label for="deskripsi_pekerjaan" class="block font-medium">Deskripsi Pekerjaan</label>
                        <textarea id="deskripsi_pekerjaan" name="deskripsi_pekerjaan" rows="3" class="w-full p-2 border rounded-md"></textarea>
                    </div>
                    <div>
                        <label for="prakiraan_selesai" class="block font-medium">Prakiraan Selesai</label>
                        <input type="text" id="prakiraan_selesai" name="prakiraan_selesai" placeholder="Pilih tanggal..." class="w-full p-2 border rounded-md">
                    </div>
                </div>
                <div class="mt-6 text-right">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div id="toast-notification" class="fixed bottom-5 right-5 p-4 rounded-lg shadow-lg text-white hidden">
    <p id="toast-message"></p>
</div>

<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="assets/js/riwayat_pengaduan.js"></script>
<?php require 'includes/footer.php'; ?>