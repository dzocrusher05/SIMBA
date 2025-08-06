<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajukan Peminjaman - SIMBA</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <style>
        .aset-card.selected {
            border-color: #2563EB;
            box-shadow: 0 0 0 2px #3B82F6;
        }

        .form-step {
            display: none;
        }

        .form-step.active {
            display: block;
            animation: fadeIn 0.5s;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        .modal {
            display: none;
        }

        .modal.active {
            display: flex;
        }
    </style>
</head>

<body class="bg-slate-50">

    <div class="container mx-auto p-4 md:p-8 max-w-4xl">
        <div class="bg-white p-8 rounded-2xl shadow-lg">

            <div class="text-center mb-8">
                <h1 class="text-3xl font-bold text-slate-800">Formulir Peminjaman Aset</h1>
                <p class="text-slate-500">Silakan ikuti langkah-langkah di bawah ini.</p>
            </div>

            <div id="step-1" class="form-step active">
                <h2 class="text-xl font-semibold mb-2 text-slate-700">Langkah 1: Pilih Aset yang Tersedia</h2>

                <div class="mb-4">
                    <input type="text" id="search-aset-input" placeholder="🔍 Cari nama, kode, NUP, atau merek aset..." class="w-full p-3 border border-gray-300 rounded-lg">
                </div>

                <div id="aset-grid" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 mb-4 max-h-96 overflow-y-auto p-2 bg-slate-50 rounded-lg">
                </div>
                <div class="text-right">
                    <button id="next-to-step-2" class="bg-blue-600 text-white font-bold py-2 px-6 rounded-lg hover:bg-blue-700 transition-all disabled:bg-gray-400" disabled>Lanjut</button>
                </div>
            </div>

            <div id="step-2" class="form-step">
                <h2 class="text-xl font-semibold mb-4 text-slate-700">Langkah 2: Isi Detail Peminjaman</h2>
                <form id="peminjaman-form">
                    <input type="hidden" id="tanda_tangan_peminjam" name="tanda_tangan_peminjam">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <input type="text" name="nama_peminjam" placeholder="Nama Lengkap Peminjam" required class="p-3 border rounded-lg">
                        <input type="text" name="nomor_telepon_peminjam" placeholder="Nomor Telepon (WA) dengan format 62XXXXX" required class="p-3 border rounded-lg">
                    </div>
                    <div class="mt-6">
                        <label class="font-medium">Periode Peminjaman</label>
                        <input type="text" id="tanggal_peminjaman" placeholder="Pilih rentang tanggal" required class="w-full p-3 border rounded-lg mt-2">
                    </div>
                    <div class="mt-6">
                        <label for="lokasi" class="font-medium">Lokasi Tujuan</label>
                        <select id="lokasi" name="lokasi_peminjaman" class="w-full p-3 border rounded-lg mt-2">
                            <option value="">-- Pilih Lokasi --</option>
                            <option value="Kota Palopo">Kota Palopo</option>
                            <option value="Kab. Luwu">Kab. Luwu</option>
                            <option value="Kab. Luwu Timur">Kab. Luwu Timur</option>
                            <option value="Kab. Luwu Utara">Kab. Luwu Utara</option>
                            <option value="Kab. Tana Toraja">Kab. Tana Toraja</option>
                            <option value="Kab. Toraja Utara">Kab. Toraja Utara</option>
                            <option value="Kab. Enrekang">Kab. Enrekang</option>
                            <option value="lainnya">Lainnya...</option>
                        </select>
                        <input type="text" id="lokasi_kustom" name="lokasi_kustom" placeholder="Masukkan lokasi tujuan Anda" class="w-full p-3 border rounded-lg mt-2 hidden">
                    </div>
                    <div class="mt-6">
                        <label for="alasan_peminjaman" class="font-medium">Alasan Peminjaman</label>
                        <textarea id="alasan_peminjaman" name="alasan_peminjaman" rows="3" required class="w-full p-3 border rounded-lg mt-2"></textarea>
                    </div>
                    <div class="mt-6">
                        <h3 class="font-bold text-lg mb-2">Tanda Tangan Peminjam:</h3>
                        <div class="border rounded-lg p-4 bg-gray-100">
                            <p id="signature-status" class="text-center text-sm text-gray-500">Belum ada tanda tangan</p>
                            <button type="button" id="open-signature-modal-btn" class="w-full bg-blue-600 text-white font-bold py-2 px-6 rounded-lg hover:bg-blue-700 mt-2">Tambahkan Tanda Tangan</button>
                        </div>
                    </div>
                </form>
                <div class="flex justify-between mt-8">
                    <button id="back-to-step-1" class="bg-gray-200 text-gray-800 font-bold py-2 px-6 rounded-lg hover:bg-gray-300 transition-all">Kembali</button>
                    <button id="submit-peminjaman" class="bg-green-600 text-white font-bold py-2 px-6 rounded-lg hover:bg-green-700 transition-all">Kirim Pengajuan</button>
                </div>
            </div>

        </div>
    </div>

    <div id="signature-modal" class="modal fixed inset-0 bg-black bg-opacity-50 items-center justify-center">
        <div class="bg-white p-6 rounded-lg shadow-xl w-full max-w-md">
            <h2 class="text-2xl font-bold mb-4">Tanda Tangan Peminjam</h2>
            <div class="border rounded-lg bg-white">
                <canvas id="signature-pad" class="w-full h-40"></canvas>
            </div>
            <div class="flex justify-between mt-4">
                <button type="button" id="clear-signature" class="text-sm text-blue-600 hover:underline">Bersihkan</button>
                <div>
                    <button type="button" id="close-signature-modal-btn" class="px-4 py-2 bg-gray-200 rounded-lg">Batal</button>
                    <button type="button" id="save-signature-btn" class="px-4 py-2 bg-green-600 text-white rounded-lg">Simpan</button>
                </div>
            </div>
        </div>
    </div>

    <div id="toast-notification" class="fixed bottom-5 right-5 p-4 rounded-lg shadow-lg text-white hidden">
        <p id="toast-message"></p>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>
    <script src="assets/js/ajukan_peminjaman.js"></script>
</body>

</html>