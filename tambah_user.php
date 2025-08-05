<?php
session_start();
require 'includes/head.php';
?>
<div class="flex">
    <?php require 'includes/sidebar.php'; ?>
    <main class="main-content flex-1 p-8">
        <h1 class="text-3xl font-bold text-slate-800 mb-6">Tambah Pengguna Baru</h1>
        <div class="bg-white p-6 rounded-lg shadow-sm max-w-lg">
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
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg">Tambahkan Pengguna</button>
                </div>
            </form>
        </div>
    </main>
</div>
<div id="toast-notification" class="fixed bottom-5 right-5 p-4 rounded-lg shadow-lg text-white hidden">
    <p id="toast-message"></p>
</div>
<script src="assets/js/tambah_user.js"></script>
<?php require 'includes/footer.php'; ?>