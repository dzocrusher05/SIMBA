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
                    <input type="text" id="search-aset-input" placeholder="🔍 Cari nama atau nomor aset..." class="w-full p-3 border border-gray-300 rounded-lg">
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
                    <button id="submit-peminjaman" class="bg-green-600 text-white font-bold py-2 px-6 rounded-lg hover:bg-green-700 transition-all">Lanjut ke Konfirmasi</button>
                </div>
            </div>

            <div id="step-3" class="form-step">
                <h2 class="text-xl font-semibold mb-4 text-slate-700">Langkah 3: Konfirmasi Pengajuan Anda</h2>
                <div class="bg-slate-50 p-6 rounded-lg">
                    <h3 class="font-bold text-lg mb-2">Ringkasan Peminjaman:</h3>
                    <div id="summary-details" class="text-slate-600 space-y-2"></div>
                    <h3 class="font-bold text-lg mt-4 mb-2">Aset yang Dipinjam:</h3>
                    <ul id="summary-aset" class="list-disc list-inside text-slate-600 space-y-1"></ul>
                </div>
                <div class="flex justify-between mt-8">
                    <button id="back-to-step-2" class="bg-gray-200 text-gray-800 font-bold py-2 px-6 rounded-lg hover:bg-gray-300 transition-all">Kembali</button>
                    <button id="submit-peminjaman-confirm" class="bg-green-600 text-white font-bold py-2 px-6 rounded-lg hover:bg-green-700 transition-all">Kirim Pengajuan</button>
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

    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // --- Elemen & State ---
            const steps = document.querySelectorAll('.form-step');
            const asetGrid = document.getElementById('aset-grid');
            const searchInput = document.getElementById('search-aset-input');
            const peminjamanForm = document.getElementById('peminjaman-form');
            let selectedAset = [];

            const signatureModal = document.getElementById('signature-modal');
            const openSignatureBtn = document.getElementById('open-signature-modal-btn');
            const closeSignatureModalBtn = document.getElementById('close-signature-modal-btn');
            const saveSignatureBtn = document.getElementById('save-signature-btn');
            const clearSignatureBtn = document.getElementById('clear-signature');
            const signatureStatus = document.getElementById('signature-status');
            const canvas = document.getElementById('signature-pad');
            const signaturePad = new SignaturePad(canvas, {
                backgroundColor: 'rgb(255, 255, 255)'
            });

            // --- Tombol Navigasi ---
            const nextToStep2Btn = document.getElementById('next-to-step-2');
            const backToStep1Btn = document.getElementById('back-to-step-1');
            const submitBtn = document.getElementById('submit-peminjaman');
            const submitConfirmBtn = document.getElementById('submit-peminjaman-confirm');
            const backToStep2Btn = document.getElementById('back-to-step-2');

            // --- Fungsi Navigasi ---
            const goToStep = (stepNumber) => {
                steps.forEach(step => step.classList.remove('active'));
                document.getElementById(`step-${stepNumber}`).classList.add('active');
            };

            // --- Event Listener Navigasi ---
            nextToStep2Btn.addEventListener('click', () => goToStep(2));
            backToStep1Btn.addEventListener('click', () => goToStep(1));
            submitBtn.addEventListener('click', () => {
                // Konfirmasi bahwa tanda tangan sudah ada
                if (!document.getElementById('tanda_tangan_peminjam').value) {
                    alert('Mohon bubuhkan tanda tangan peminjam terlebih dahulu.');
                    return;
                }
                displaySummary();
                goToStep(3);
            });
            backToStep2Btn.addEventListener('click', () => goToStep(2));
            submitConfirmBtn.addEventListener('click', async () => {
                // ... FUNGSI KIRIM PENGAJUAN ...
                submitConfirmBtn.disabled = true;
                submitConfirmBtn.textContent = 'Mengirim...';

                const formData = new FormData(peminjamanForm);
                const asetIdsArray = selectedAset.map(aset => aset.id);
                formData.append('aset_ids', JSON.stringify(asetIdsArray));
                formData.append('tanggal_peminjaman', document.getElementById('tanggal_peminjaman').value);

                try {
                    const response = await fetch('api/add_peminjaman.php', {
                        method: 'POST',
                        body: formData
                    });
                    const result = await response.json();

                    if (result.success) {
                        alert('Pengajuan Anda telah berhasil dikirim! Terima kasih.');
                        window.location.reload();
                    } else {
                        alert('Gagal mengirim pengajuan: ' + result.message);
                    }
                } catch (error) {
                    alert('Terjadi kesalahan koneksi. Silakan coba lagi.');
                } finally {
                    submitConfirmBtn.disabled = false;
                    submitConfirmBtn.textContent = 'Kirim Pengajuan';
                }
            });

            // --- Logika Modal Tanda Tangan ---
            const resizeCanvas = () => {
                const ratio = Math.max(window.devicePixelRatio || 1, 1);
                canvas.width = canvas.offsetWidth * ratio;
                canvas.height = canvas.offsetHeight * ratio;
                canvas.getContext("2d").scale(ratio, ratio);
                signaturePad.clear();
            };
            openSignatureBtn.addEventListener('click', () => {
                signatureModal.classList.add('active');
                setTimeout(resizeCanvas, 50);
            });
            closeSignatureModalBtn.addEventListener('click', () => {
                signatureModal.classList.remove('active');
            });
            saveSignatureBtn.addEventListener('click', () => {
                if (signaturePad.isEmpty()) {
                    alert('Kanvas tanda tangan masih kosong.');
                    return;
                }
                const signatureDataUrl = signaturePad.toDataURL('image/png');
                document.getElementById('tanda_tangan_peminjam').value = signatureDataUrl;
                signatureStatus.textContent = "Tanda tangan telah ditambahkan";
                signatureStatus.classList.remove("text-gray-500");
                signatureStatus.classList.add("text-green-600");
                signatureModal.classList.remove('active');
            });
            clearSignatureBtn.addEventListener('click', () => {
                signaturePad.clear();
            });

            // --- Inisialisasi Flatpickr & Lokasi ---
            flatpickr("#tanggal_peminjaman", {
                mode: "range",
                minDate: "today",
                dateFormat: "d F Y"
            });
            const lokasiSelect = document.getElementById('lokasi');
            const lokasiKustomInput = document.getElementById('lokasi_kustom');
            lokasiSelect.addEventListener('change', () => {
                lokasiKustomInput.classList.toggle('hidden', lokasiSelect.value !== 'lainnya');
            });

            // --- FUNGSI BARU: Filter Aset ---
            searchInput.addEventListener('input', (e) => {
                const searchTerm = e.target.value.toLowerCase();
                const allCards = asetGrid.querySelectorAll('.aset-card');
                allCards.forEach(card => {
                    const cardText = card.textContent.toLowerCase();
                    card.style.display = cardText.includes(searchTerm) ? 'block' : 'none';
                });
            });

            // --- Logika Pemilihan Aset ---
            const loadAset = async () => {
                const response = await fetch('api/get_aset_tersedia.php');
                const asets = await response.json();
                asetGrid.innerHTML = '';
                asets.forEach(aset => {
                    const card = document.createElement('div');
                    card.className = 'aset-card border-2 p-3 rounded-lg cursor-pointer transition-all';
                    card.dataset.id = aset.id; // Simpan id di dataset
                    card.innerHTML = `<h3 class="font-bold text-sm text-slate-800">${aset.nama_bmn}</h3><p class="text-xs text-slate-500">${aset.no_bmn}</p>`;
                    card.addEventListener('click', () => toggleAsetSelection(aset, card));
                    asetGrid.appendChild(card);
                });
            };

            const toggleAsetSelection = (aset, card) => {
                const index = selectedAset.findIndex(item => item.id === aset.id);
                if (index > -1) {
                    selectedAset.splice(index, 1);
                    card.classList.remove('selected');
                } else {
                    selectedAset.push({
                        id: aset.id,
                        nama: aset.nama_bmn
                    });
                    card.classList.add('selected');
                }
                nextToStep2Btn.disabled = selectedAset.length === 0;
            };

            // --- Tampilkan Ringkasan ---
            const displaySummary = () => {
                const summaryDetails = document.getElementById('summary-details');
                const summaryAset = document.getElementById('summary-aset');
                let lokasiValue = lokasiSelect.value === 'lainnya' ? lokasiKustomInput.value : lokasiSelect.value;

                summaryDetails.innerHTML = `<p><strong>Nama Peminjam:</strong> ${peminjamanForm.nama_peminjam.value}</p>
                                          <p><strong>Periode Pinjam:</strong> ${document.getElementById('tanggal_peminjaman').value}</p>
                                          <p><strong>Lokasi Tujuan:</strong> ${lokasiValue}</p>`;
                summaryAset.innerHTML = '';
                selectedAset.forEach(aset => {
                    summaryAset.innerHTML += `<li>${aset.nama}</li>`;
                });
            };

            // --- Panggil fungsi awal ---
            loadAset();
        });
    </script>
</body>

</html>