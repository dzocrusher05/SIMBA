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
        <h1 class="text-3xl font-bold text-slate-800 mb-6">Pengaturan Aplikasi</h1>
        <div class="bg-white p-6 rounded-lg shadow-sm max-w-lg">
            <h2 class="text-xl font-semibold mb-4">Reset Penomoran Surat</h2>
            <p class="text-sm text-gray-600 mb-4">Ubah nomor di bawah ini untuk memulai urutan baru. Nomor berikutnya yang akan dibuat adalah angka yang Anda masukkan + 1.</p>
            <form id="pengaturan-form">
                <div class="space-y-4">
                    <div>
                        <label for="nomor_terakhir_spb" class="block font-medium">Nomor Terakhir SPB</label>
                        <input type="number" id="nomor_terakhir_spb" name="nomor_terakhir_spb" class="w-full p-2 border rounded-md">
                    </div>
                    <div>
                        <label for="nomor_terakhir_sbbk" class="block font-medium">Nomor Terakhir SBBK</label>
                        <input type="number" id="nomor_terakhir_sbbk" name="nomor_terakhir_sbbk" class="w-full p-2 border rounded-md">
                    </div>
                </div>
                <div class="mt-6 text-right">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg">Simpan Pengaturan</button>
                </div>
            </form>
        </div>
    </main>
</div>
<div id="toast-notification" class="fixed bottom-5 right-5 p-4 rounded-lg shadow-lg text-white hidden">
    <p id="toast-message"></p>
</div>
<script src="assets/js/pengaturan.js"></script>
<?php require 'includes/footer.php'; ?>