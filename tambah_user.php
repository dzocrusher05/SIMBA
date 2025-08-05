<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit();
}
require 'includes/head.php';
?>
<div class="flex">
    <?php require 'includes/sidebar.php'; ?>
    <main class="main-content flex-1 p-8">
        <h1 class="text-3xl font-bold text-slate-800 mb-6">Manajemen Pengguna</h1>

        <div class="flex justify-end mb-4">
            <button id="open-add-user-modal" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                </svg>
                Tambah Pengguna
            </button>
        </div>

        <div class="bg-white p-6 rounded-lg shadow-sm">
            <div class="overflow-x-auto">
                <table class="min-w-full bg-white">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="text-left py-3 px-4 uppercase font-semibold text-sm text-slate-600">No</th>
                            <th class="text-left py-3 px-4 uppercase font-semibold text-sm text-slate-600">Username</th>
                            <th class="text-left py-3 px-4 uppercase font-semibold text-sm text-slate-600">Peran</th>
                            <th class="text-left py-3 px-4 uppercase font-semibold text-sm text-slate-600">Peran Notifikasi</th>
                            <th class="text-center py-3 px-4 uppercase font-semibold text-sm text-slate-600">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="users-table-body">
                        <tr>
                            <td colspan="5" class="text-center py-4">Memuat data...</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>

<div id="add-user-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden">
    <div class="bg-white p-6 rounded-lg shadow-xl w-full max-w-lg">
        <h2 class="text-2xl font-bold mb-4">Tambah Pengguna Baru</h2>
        <form id="add-user-form">
            <div class="space-y-4">
                <div>
                    <label for="username" class="block font-medium">Username</label>
                    <input type="text" id="username" name="username" required class="w-full p-2 border rounded-md">
                </div>
                <div>
                    <label for="password" class="block font-medium">Password</label>
                    <input type="password" id="password" name="password" required class="w-full p-2 border rounded-md">
                </div>
                <div>
                    <label for="peran" class="block font-medium">Peran Akses</label>
                    <select id="peran" name="peran" required class="w-full p-2 border rounded-md">
                        <option value="user">User</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>
                <div>
                    <label for="notifikasi_peran" class="block font-medium">Peran Notifikasi</label>
                    <select id="notifikasi_peran" name="notifikasi_peran" required class="w-full p-2 border rounded-md">
                        <option value="none">Tidak Ada</option>
                        <option value="admin_aset">Admin Aset</option>
                        <option value="admin_persediaan">Admin Persediaan</option>
                    </select>
                </div>
            </div>
            <div class="mt-6 text-right">
                <button type="button" id="close-add-user-modal" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-2 px-4 rounded-lg">Batal</button>
                <button type="submit" id="submit-btn" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg">Tambahkan Pengguna</button>
            </div>
        </form>
    </div>
</div>

<div id="edit-user-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden">
    <div class="bg-white p-6 rounded-lg shadow-xl w-full max-w-lg">
        <h2 class="text-2xl font-bold mb-4">Edit Pengguna</h2>
        <form id="edit-user-form">
            <input type="hidden" id="edit_user_id" name="user_id">
            <div class="space-y-4">
                <div>
                    <label for="edit_username" class="block font-medium">Username</label>
                    <input type="text" id="edit_username" name="username" required class="w-full p-2 border rounded-md bg-gray-100 cursor-not-allowed" disabled>
                </div>
                <div>
                    <label for="edit_peran" class="block font-medium">Peran Akses</label>
                    <select id="edit_peran" name="peran" required class="w-full p-2 border rounded-md">
                        <option value="user">User</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>
                <div>
                    <label for="edit_notifikasi_peran" class="block font-medium">Peran Notifikasi</label>
                    <select id="edit_notifikasi_peran" name="notifikasi_peran" required class="w-full p-2 border rounded-md">
                        <option value="none">Tidak Ada</option>
                        <option value="admin_aset">Admin Aset</option>
                        <option value="admin_persediaan">Admin Persediaan</option>
                    </select>
                </div>
            </div>
            <div class="mt-6 text-right">
                <button type="button" id="close-edit-user-modal" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-2 px-4 rounded-lg">Batal</button>
                <button type="submit" id="edit-submit-btn" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<div id="delete-user-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden">
    <div class="bg-white p-6 rounded-lg shadow-xl w-full max-w-sm">
        <h2 class="text-xl font-bold mb-4">Konfirmasi Hapus</h2>
        <p class="mb-6 text-gray-700">Apakah Anda yakin ingin menghapus pengguna <span id="delete-username" class="font-bold"></span>?</p>
        <div class="flex justify-end gap-4">
            <button id="cancel-delete-btn" class="px-4 py-2 bg-gray-200 rounded-lg">Batal</button>
            <button id="confirm-delete-btn" class="px-4 py-2 bg-red-600 text-white rounded-lg">Ya, Hapus</button>
        </div>
    </div>
</div>

<div id="change-password-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden">
    <div class="bg-white p-6 rounded-lg shadow-xl w-full max-w-md">
        <h2 class="text-2xl font-bold mb-4">Ubah Kata Sandi</h2>
        <form id="change-password-form">
            <input type="hidden" id="password_user_id" name="user_id">
            <div class="space-y-4">
                <div>
                    <label for="new_password" class="block font-medium">Kata Sandi Baru</label>
                    <input type="password" id="new_password" name="new_password" required class="w-full p-2 border rounded-md">
                </div>
                <div>
                    <label for="confirm_password" class="block font-medium">Konfirmasi Kata Sandi</label>
                    <input type="password" id="confirm_password" name="confirm_password" required class="w-full p-2 border rounded-md">
                </div>
            </div>
            <div class="mt-6 text-right">
                <button type="button" id="close-password-modal" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-2 px-4 rounded-lg">Batal</button>
                <button type="submit" id="change-password-submit-btn" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded-lg">Ubah Kata Sandi</button>
            </div>
        </form>
    </div>
</div>

<div id="toast-notification" class="fixed bottom-5 right-5 p-4 rounded-lg shadow-lg text-white hidden">
    <p id="toast-message"></p>
</div>
<script src="assets/js/tambah_user.js"></script>
<?php require 'includes/footer.php'; ?>