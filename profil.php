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
            <form id="update-profile-form">
                <div id="profile-info">
                    <p class="text-gray-500 text-center">Memuat data...</p>
                </div>
                <div class="mt-6">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </main>
</div>

<div id="toast-notification" class="fixed bottom-5 right-5 p-4 rounded-lg shadow-lg text-white hidden">
    <p id="toast-message"></p>
</div>

<script src="assets/js/profil.js"></script>
<?php require 'includes/footer.php'; ?>