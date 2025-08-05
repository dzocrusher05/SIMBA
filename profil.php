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
        <h1 class="text-3xl font-bold text-slate-800 mb-6">Profil Pengguna</h1>

        <div class="bg-white p-6 rounded-lg shadow-md max-w-xl mx-auto">
            <div id="profile-details">
                <p class="text-gray-500 text-center">Memuat data...</p>
            </div>
            <div class="mt-6 text-center">
                <button id="open-edit-modal-btn" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg">
                    Edit Profil
                </button>
            </div>
        </div>

        <div id="password-container" class="bg-white p-6 rounded-lg shadow-md max-w-xl mx-auto mt-8">
            <h2 class="text-2xl font-bold text-slate-800 mb-4">Ubah Kata Sandi</h2>
            <form id="change-password-form">
                <div class="space-y-4">
                    <div>
                        <label for="current_password" class="block font-medium">Kata Sandi Saat Ini</label>
                        <input type="password" id="current_password" name="current_password" required class="w-full p-2 border rounded-md">
                    </div>
                    <div>
                        <label for="new_password" class="block font-medium">Kata Sandi Baru</label>
                        <input type="password" id="new_password" name="new_password" required class="w-full p-2 border rounded-md">
                    </div>
                    <div>
                        <label for="confirm_password" class="block font-medium">Konfirmasi Kata Sandi Baru</label>
                        <input type="password" id="confirm_password" name="confirm_password" required class="w-full p-2 border rounded-md">
                    </div>
                </div>
                <div class="mt-6 text-right">
                    <button type="submit" id="change-password-btn" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded-lg">Ubah Kata Sandi</button>
                </div>
            </form>
        </div>
    </main>
</div>

<div id="edit-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden">
    <div class="bg-white p-6 rounded-lg shadow-xl w-full max-w-md">
        <h2 class="text-2xl font-bold mb-4">Edit Data Profil</h2>
        <form id="edit-profile-form">
            <input type="hidden" id="edit_user_id" name="user_id">
            <div class="space-y-4">
                <div>
                    <label for="edit_username" class="block font-medium">Username</label>
                    <input type="text" id="edit_username" name="username" required class="w-full p-2 border rounded-md">
                </div>
                <div>
                    <label for="edit_nama_lengkap" class="block font-medium">Nama Lengkap</label>
                    <input type="text" id="edit_nama_lengkap" name="nama_lengkap" required class="w-full p-2 border rounded-md">
                </div>
                <div>
                    <label for="edit_email" class="block font-medium">Email</label>
                    <input type="email" id="edit_email" name="email" class="w-full p-2 border rounded-md">
                </div>
                <div>
                    <label for="edit_no_telepon" class="block font-medium">No. Telepon</label>
                    <input type="text" id="edit_no_telepon" name="no_telepon" class="w-full p-2 border rounded-md">
                </div>
                <div>
                    <label for="edit_alamat" class="block font-medium">Alamat</label>
                    <textarea id="edit_alamat" name="alamat" rows="3" class="w-full p-2 border rounded-md"></textarea>
                </div>
            </div>
            <div class="mt-6 text-right">
                <button type="button" id="close-edit-modal" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-2 px-4 rounded-lg">Batal</button>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<div id="toast-notification" class="fixed bottom-5 right-5 p-4 rounded-lg shadow-lg text-white hidden">
    <p id="toast-message"></p>
</div>

<script src="assets/js/profil.js"></script>
<?php require 'includes/footer.php'; ?>