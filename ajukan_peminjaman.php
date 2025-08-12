<?php
// session_start();
?>
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
            background-color: #DBEAFE;
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
                <h2 class="text-xl font-semibold mb-2 text-slate-700">Langkah 1: Pilih Satu atau Lebih Aset</h2>
                <div id="selected-aset-preview" class="mb-4 p-3 border-2 border-dashed rounded-lg min-h-[60px] bg-slate-50">
                    <p id="selected-placeholder" class="text-slate-400">Aset yang dipilih akan muncul di sini...</p>
                    <div id="selected-aset-tags" class="flex flex-wrap gap-2"></div>
                </div>
                <div class="mb-4">
                    <input type="text" id="search-aset-input" placeholder="🔍 Cari nama, kode, NUP, atau merek aset..." class="w-full p-3 border border-gray-300 rounded-lg">
                </div>
                <div id="aset-grid" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 mb-4 max-h-80 overflow-y-auto p-2 bg-slate-50 rounded-lg"></div>
                <div class="text-right">
                    <button id="next-to-step-2" class="bg-blue-600 text-white font-bold py-2 px-6 rounded-lg hover:bg-blue-700 transition-all disabled:bg-gray-400" disabled>Lanjut</button>
                </div>
            </div>

            <div id="step-2" class="form-step">
                <h2 class="text-xl font-semibold mb-4 text-slate-700">Langkah 2: Isi Detail Peminjaman</h2>
                <div class="mb-6">
                    <h3 class="font-bold text-lg text-slate-700">Aset yang akan dipinjam:</h3>
                    <ul id="selected-aset-confirmation-list" class="list-disc list-inside mt-2 text-slate-600 bg-slate-100 p-4 rounded-lg"></ul>
                </div>
                <form id="peminjaman-form" method="POST">
                    <input type="hidden" id="tanda_tangan_peminjam" name="tanda_tangan_peminjam">
                    <input type="hidden" id="tanggal_mulai" name="tanggal_mulai">
                    <input type="hidden" id="tanggal_selesai" name="tanggal_selesai">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <input type="text" name="nama_peminjam" placeholder="Nama Lengkap Peminjam" required class="p-3 border rounded-lg">
                        <input type="text" name="nomor_telepon_peminjam" placeholder="Nomor Telepon (WA) format 62..." required class="p-3 border rounded-lg">
                    </div>
                    <div class="mt-6">
                        <label class="font-medium">Periode Peminjaman</label>
                        <input type="text" id="tanggal_peminjaman" placeholder="Pilih rentang tanggal" required class="w-full p-3 border rounded-lg mt-2">
                    </div>
                    <div class="mt-6">
                        <label for="lokasi" class="font-medium">Lokasi Tujuan</label>
                        <select id="lokasi" name="lokasi_peminjaman" required class="w-full p-3 border rounded-lg mt-2">
                            <option value="">-- Pilih Lokasi --</option>
                            <option value="Kota Palopo">Kota Palopo</option>
                            <option value="Kabupaten Luwu">Kabupaten Luwu</option>
                            <option value="Kabupaten Luwu Utara">Kabupaten Luwu Utara</option>
                            <option value="Kabupaten Luwu Timur">Kabupaten Luwu Timur</option>
                            <option value="Kabupaten Toraja Utara">Kabupaten Toraja Utara</option>
                            <option value="Kabupaten Tana Toraja">Kabupaten Tana Toraja</option>
                            <option value="Kabupaten Enrekang">Kabupaten Enrekang</option>
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
                    <div class="flex justify-between mt-8">
                        <button type="button" id="back-to-step-1" class="bg-gray-200 text-gray-800 font-bold py-2 px-6 rounded-lg hover:bg-gray-300 transition-all">Kembali</button>
                        <button type="submit" id="submit-peminjaman" class="bg-green-600 text-white font-bold py-2 px-6 rounded-lg hover:bg-green-700 transition-all">Kirim Pengajuan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div id="signature-modal" class="modal fixed inset-0 bg-black bg-opacity-50 items-center justify-center z-50">
        <div class="bg-white p-6 rounded-lg shadow-xl w-full max-w-md">
            <h2 class="text-2xl font-bold mb-4">Tanda Tangan Peminjam</h2>
            <div class="border rounded-lg bg-white"><canvas id="signature-pad" class="w-full h-40"></canvas></div>
            <div class="flex justify-between mt-4"> <button type="button" id="clear-signature" class="text-sm text-blue-600 hover:underline">Bersihkan</button>
                <div> <button type="button" id="close-signature-modal-btn" class="px-4 py-2 bg-gray-200 rounded-lg">Batal</button> <button type="button" id="save-signature-btn" class="px-4 py-2 bg-green-600 text-white rounded-lg">Simpan</button> </div>
            </div>
        </div>
    </div>
    <div id="toast-notification" class="fixed bottom-5 right-5 p-4 rounded-lg shadow-lg text-white hidden z-50">
        <p id="toast-message"></p>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const step1 = document.getElementById('step-1');
            const step2 = document.getElementById('step-2');
            const nextButton = document.getElementById('next-to-step-2');
            const backButton = document.getElementById('back-to-step-1');
            const form = document.getElementById('peminjaman-form');
            const asetGrid = document.getElementById('aset-grid');
            const searchInput = document.getElementById('search-aset-input');
            const selectedAsetTags = document.getElementById('selected-aset-tags');
            const selectedPlaceholder = document.getElementById('selected-placeholder');
            const confirmationList = document.getElementById('selected-aset-confirmation-list');
            const signatureModal = document.getElementById('signature-modal');
            let allAset = [];
            let selectedAset = [];

            // =======================================================
            // === PERUBAHAN 1: FUNGSI NOTIFIKASI (showToast) ===
            // =======================================================
            const showToast = (status, message) => {
                const toast = document.getElementById('toast-notification');
                const toastMessage = document.getElementById('toast-message');
                toastMessage.textContent = message;

                // Menentukan warna notifikasi berdasarkan status
                let bgColor = status === 'success' ? 'bg-green-500' : 'bg-red-500';
                toast.className = `fixed bottom-5 right-5 p-4 rounded-lg shadow-lg text-white z-50 ${bgColor}`;

                toast.classList.remove('hidden');
                setTimeout(() => {
                    toast.classList.add('hidden');
                }, 3000);
            };

            const fetchAset = () => {
                fetch('api/get_aset_tersedia.php')
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.status === 'success') {
                            allAset = data.data;
                            renderAset(allAset);
                        } else {
                            asetGrid.innerHTML = `<p class="text-center text-slate-500 col-span-full">${data.message || 'Gagal memuat data.'}</p>`;
                        }
                    })
                    .catch(error => {
                        console.error('Error fetching aset:', error);
                        asetGrid.innerHTML = `<p class="text-center text-red-500 col-span-full">Gagal terhubung ke server untuk memuat aset.</p>`;
                    });
            };

            const updateSelectedAsetView = () => {
                selectedAsetTags.innerHTML = '';
                nextButton.disabled = selectedAset.length === 0;
                selectedPlaceholder.style.display = selectedAset.length === 0 ? 'block' : 'none';

                selectedAset.forEach(aset => {
                    const tag = document.createElement('div');
                    tag.className = 'bg-blue-100 text-blue-800 text-sm font-semibold px-2.5 py-0.5 rounded-full flex items-center';
                    tag.innerHTML = `<span>${aset.nama_bmn}</span><button type="button" data-id="${aset.id}" class="remove-aset-btn ml-2 text-blue-600 hover:text-blue-900 font-bold">&times;</button>`;
                    selectedAsetTags.appendChild(tag);
                });
                document.querySelectorAll('.remove-aset-btn').forEach(button => {
                    button.addEventListener('click', (e) => {
                        toggleAsetSelection(e.target.dataset.id);
                    });
                });
            };

            const toggleAsetSelection = (id) => {
                const index = selectedAset.findIndex(a => a.id == id);
                if (index > -1) {
                    selectedAset.splice(index, 1);
                } else {
                    const asetToToggle = allAset.find(a => a.id == id);
                    if (asetToToggle) selectedAset.push(asetToToggle);
                }
                document.querySelectorAll('.aset-card').forEach(card => {
                    card.classList.toggle('selected', selectedAset.some(a => a.id == card.dataset.id));
                });
                updateSelectedAsetView();
            };

            const renderAset = (asetList) => {
                asetGrid.innerHTML = '';
                asetList.forEach(aset => {
                    const card = document.createElement('div');
                    card.className = 'aset-card flex flex-col justify-between border-2 border-gray-200 rounded-lg p-3 cursor-pointer hover:border-blue-500 transition-all';
                    card.dataset.id = aset.id;
                    if (selectedAset.some(a => a.id == aset.id)) card.classList.add('selected');
                    card.innerHTML = `<div><p class="font-bold text-sm text-slate-800 truncate" title="${aset.nama_bmn}">${aset.nama_bmn}</p><p class="text-xs text-slate-500">Kode: ${aset.kode_bmn || '-'}</p></div><div class="mt-2 text-xs text-slate-600"><p>NUP: <span class="font-medium">${aset.nup || '-'}</span></p><p>Merek: <span class="font-medium">${aset.merek || '-'}</span></p></div>`;
                    card.addEventListener('click', () => toggleAsetSelection(aset.id));
                    asetGrid.appendChild(card);
                });
            };

            searchInput.addEventListener('input', () => {
                const searchTerm = searchInput.value.toLowerCase();
                const filteredAset = allAset.filter(aset =>
                    aset.nama_bmn.toLowerCase().includes(searchTerm) ||
                    (aset.kode_bmn && aset.kode_bmn.toLowerCase().includes(searchTerm)) ||
                    (aset.nup && aset.nup.toString().toLowerCase().includes(searchTerm)) ||
                    (aset.merek && aset.merek.toLowerCase().includes(searchTerm))
                );
                renderAset(filteredAset);
            });

            nextButton.addEventListener('click', () => {
                if (selectedAset.length > 0) {
                    confirmationList.innerHTML = '';
                    selectedAset.forEach(aset => {
                        const listItem = document.createElement('li');
                        listItem.textContent = `${aset.nama_bmn} (Kode: ${aset.kode_bmn})`;
                        confirmationList.appendChild(listItem);
                    });
                    step1.classList.remove('active');
                    step2.classList.add('active');
                } else {
                    showToast('error', 'Silakan pilih minimal satu aset.');
                }
            });

            backButton.addEventListener('click', () => {
                step2.classList.remove('active');
                step1.classList.add('active');
            });

            const flatpickrInstance = flatpickr("#tanggal_peminjaman", {
                mode: "range",
                dateFormat: "Y-m-d",
                minDate: "today"
            });
            const lokasiSelect = document.getElementById('lokasi');
            const lokasiKustomInput = document.getElementById('lokasi_kustom');
            lokasiSelect.addEventListener('change', function() {
                lokasiKustomInput.classList.toggle('hidden', this.value !== 'lainnya');
                lokasiKustomInput.toggleAttribute('required', this.value === 'lainnya');
            });

            const canvas = document.getElementById('signature-pad');
            const signaturePad = new SignaturePad(canvas, {
                backgroundColor: 'rgb(255, 255, 255)'
            });

            document.getElementById('open-signature-modal-btn').addEventListener('click', () => signatureModal.classList.add('active'));
            document.getElementById('close-signature-modal-btn').addEventListener('click', () => signatureModal.classList.remove('active'));
            document.getElementById('clear-signature').addEventListener('click', () => signaturePad.clear());
            document.getElementById('save-signature-btn').addEventListener('click', () => {
                if (signaturePad.isEmpty()) {
                    showToast('error', 'Tanda tangan tidak boleh kosong.');
                    return;
                }
                document.getElementById('tanda_tangan_peminjam').value = signaturePad.toDataURL('image/png');
                document.getElementById('signature-status').textContent = '✅ Tanda tangan sudah disimpan.';
                signatureModal.classList.remove('active');
            });

            form.addEventListener('submit', function(e) {
                e.preventDefault();
                const submitButton = document.getElementById('submit-peminjaman');
                submitButton.disabled = true;
                submitButton.textContent = 'Mengirim...';

                if (selectedAset.length === 0) {
                    showToast('error', 'Tidak ada aset yang dipilih. Silakan kembali dan pilih aset.');
                    submitButton.disabled = false;
                    submitButton.textContent = 'Kirim Pengajuan';
                    return;
                }
                if (flatpickrInstance.selectedDates.length < 2) {
                    showToast('error', 'Silakan pilih tanggal peminjaman dan tanggal pengembalian.');
                    submitButton.disabled = false;
                    submitButton.textContent = 'Kirim Pengajuan';
                    return;
                }
                if (!document.getElementById('tanda_tangan_peminjam').value) {
                    showToast('error', 'Tanda tangan tidak boleh kosong.');
                    submitButton.disabled = false;
                    submitButton.textContent = 'Kirim Pengajuan';
                    return;
                }

                const formatDate = (date) => {
                    const d = new Date(date);
                    return `${d.getFullYear()}-${('0' + (d.getMonth() + 1)).slice(-2)}-${('0' + d.getDate()).slice(-2)}`;
                };
                document.getElementById('tanggal_mulai').value = formatDate(flatpickrInstance.selectedDates[0]);
                document.getElementById('tanggal_selesai').value = formatDate(flatpickrInstance.selectedDates[1]);

                const formData = new FormData(form);
                selectedAset.forEach(aset => {
                    formData.append('aset_ids[]', aset.id);
                });
                if (lokasiSelect.value === 'lainnya') {
                    formData.set('lokasi_peminjaman', lokasiKustomInput.value);
                }
                formData.delete('lokasi_kustom');

                fetch('api/add_peminjaman.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        const message = data ? data.message : 'Gagal memproses permintaan.';

                        // =======================================================
                        // === PERUBAHAN 2: LOGIKA SETELAH SUBMIT SUKSES ===
                        // =======================================================
                        if (data && (data.status === 'success' || data.success === true)) {
                            showToast('success', message);
                            // Arahkan kembali ke halaman ini (reload) setelah 2 detik
                            setTimeout(() => {
                                window.location.reload();
                            }, 2000);
                        } else {
                            showToast('error', message);
                        }
                    })
                    .catch(error => {
                        console.error('Submit error:', error);
                        showToast('error', 'Tidak dapat terhubung ke server.');
                    })
                    .finally(() => {
                        submitButton.disabled = false;
                        submitButton.textContent = 'Kirim Pengajuan';
                    });
            });

            fetchAset();
        });
    </script>
</body>

</html>