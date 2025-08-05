<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Permintaan Persediaan - SIMBA</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
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

        .item-card.selected {
            border-color: #2563EB;
            box-shadow: 0 0 0 2px #3B82F6;
        }
    </style>
</head>

<body class="bg-slate-50">

    <div class="container mx-auto p-4 md:p-8 max-w-4xl">
        <div class="bg-white p-8 rounded-2xl shadow-lg">

            <div class="text-center mb-8">
                <h1 class="text-3xl font-bold text-slate-800">Formulir Permintaan Persediaan</h1>
                <p class="text-slate-500">Pilih item yang dibutuhkan dan isi detail Anda.</p>
            </div>

            <div id="step-1" class="form-step active">
                <h2 class="text-xl font-semibold mb-4 text-slate-700">Langkah 1: Pilih Item & Tentukan Jumlah</h2>
                <div class="mb-4">
                    <input type="text" id="search-item-input" placeholder="🔍 Cari nama item..." class="w-full p-3 border border-gray-300 rounded-lg">
                </div>
                <div id="item-grid" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 mb-4 max-h-96 overflow-y-auto p-2 bg-slate-50 rounded-lg">
                </div>
                <h3 class="text-lg font-semibold mb-2 mt-4 text-slate-700">Item yang Dipilih:</h3>
                <div id="selected-items-list" class="space-y-3 mb-6"></div>
                <div class="text-right">
                    <button id="next-to-step-2" class="bg-blue-600 text-white font-bold py-2 px-6 rounded-lg hover:bg-blue-700 disabled:bg-gray-400" disabled>Lanjut</button>
                </div>
            </div>

            <div id="step-2" class="form-step">
                <h2 class="text-xl font-semibold mb-4 text-slate-700">Langkah 2: Isi Detail Pemohon</h2>
                <form id="permintaan-form">
                    <input type="hidden" id="tanda_tangan_pemohon" name="tanda_tangan_pemohon">
                    <div class="space-y-4">
                        <input type="text" name="nama_pemohon" placeholder="Nama Lengkap Pemohon" required class="w-full p-3 border rounded-lg">
                        <input type="text" name="nomor_telepon_pemohon" placeholder="Nomor Telepon (WA)" required class="w-full p-3 border rounded-lg">
                    </div>
                </form>
                <div class="flex justify-between mt-8">
                    <button id="back-to-step-1" class="bg-gray-200 text-gray-800 font-bold py-2 px-6 rounded-lg hover:bg-gray-300">Kembali</button>
                    <button id="next-to-step-3" class="bg-blue-600 text-white font-bold py-2 px-6 rounded-lg hover:bg-blue-700">Lanjut ke Konfirmasi</button>
                </div>
            </div>

            <div id="step-3" class="form-step">
                <h2 class="text-xl font-semibold mb-4 text-slate-700">Langkah 3: Konfirmasi & Tanda Tangan</h2>
                <div class="bg-slate-50 p-6 rounded-lg">
                    <h3 class="font-bold text-lg mb-2">Ringkasan Permintaan:</h3>
                    <div id="summary-details" class="text-slate-600 space-y-2 mb-4"></div>
                    <h3 class="font-bold text-lg mt-4 mb-2">Item yang Diminta:</h3>
                    <div id="summary-items" class="text-slate-600 space-y-1"></div>
                </div>
                <div class="mt-6">
                    <h3 class="font-bold text-lg mb-2">Tanda Tangan Pemohon:</h3>
                    <div class="border rounded-lg p-4 bg-gray-100">
                        <p id="signature-status" class="text-center text-sm text-gray-500">Belum ada tanda tangan</p>
                        <button id="open-signature-modal-btn" class="w-full bg-blue-600 text-white font-bold py-2 px-6 rounded-lg hover:bg-blue-700 mt-2">Tambahkan Tanda Tangan</button>
                    </div>
                </div>
                <div class="flex justify-between mt-8">
                    <button id="back-to-step-2" class="bg-gray-200 text-gray-800 font-bold py-2 px-6 rounded-lg hover:bg-gray-300">Kembali</button>
                    <button id="submit-permintaan" class="bg-green-600 text-white font-bold py-2 px-6 rounded-lg hover:bg-green-700">Kirim Permintaan</button>
                </div>
            </div>
        </div>
    </div>

    <div id="signature-modal" class="modal fixed inset-0 bg-black bg-opacity-50 items-center justify-center">
        <div class="bg-white p-6 rounded-lg shadow-xl w-full max-w-md">
            <h2 class="text-2xl font-bold mb-4">Tanda Tangan Pemohon</h2>
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

    <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const steps = document.querySelectorAll('.form-step');
            const itemGrid = document.getElementById('item-grid');
            const searchInput = document.getElementById('search-item-input');
            const selectedItemsList = document.getElementById('selected-items-list');
            const permintaanForm = document.getElementById('permintaan-form');
            let availableItems = [];
            let selectedItems = {};

            // Elemen Modal Tanda Tangan
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

            const resizeCanvas = () => {
                const ratio = Math.max(window.devicePixelRatio || 1, 1);
                canvas.width = canvas.offsetWidth * ratio;
                canvas.height = canvas.offsetHeight * ratio;
                canvas.getContext("2d").scale(ratio, ratio);
                signaturePad.clear();
            };

            const nextToStep2Btn = document.getElementById('next-to-step-2');
            const backToStep1Btn = document.getElementById('back-to-step-1');
            const nextToStep3Btn = document.getElementById('next-to-step-3');
            const backToStep2Btn = document.getElementById('back-to-step-2');
            const submitBtn = document.getElementById('submit-permintaan');

            const goToStep = (stepNumber) => {
                steps.forEach(step => step.classList.remove('active'));
                document.getElementById(`step-${stepNumber}`).classList.add('active');
            };

            const loadAvailableItems = async () => {
                const response = await fetch('api/get_list_persediaan.php');
                availableItems = await response.json();
                renderItemGrid();
            };

            const renderItemGrid = () => {
                itemGrid.innerHTML = '';
                availableItems.forEach(item => {
                    const isSelected = selectedItems[item.id] !== undefined;
                    const card = document.createElement('div');
                    card.className = `item-card border-2 p-3 rounded-lg cursor-pointer transition-all ${isSelected ? 'selected' : ''}`;
                    card.dataset.id = item.id;
                    card.innerHTML = `<h3 class="font-bold text-sm text-slate-800">${item.nama_persediaan}</h3><p class="text-xs text-slate-500">Stok: ${item.stok} ${item.satuan}</p>`;
                    card.addEventListener('click', () => toggleItemSelection(item, card));
                    itemGrid.appendChild(card);
                });
            };

            const toggleItemSelection = (item, card) => {
                if (selectedItems[item.id]) {
                    delete selectedItems[item.id];
                    card.classList.remove('selected');
                } else {
                    selectedItems[item.id] = 1; // Default quantity
                    card.classList.add('selected');
                }
                renderSelectedItems();
                nextToStep2Btn.disabled = Object.keys(selectedItems).length === 0;
            };

            const renderSelectedItems = () => {
                selectedItemsList.innerHTML = '';
                if (Object.keys(selectedItems).length === 0) {
                    selectedItemsList.innerHTML = '<p class="text-center text-gray-500">Belum ada item yang dipilih.</p>';
                    return;
                }
                for (const id in selectedItems) {
                    const item = availableItems.find(i => i.id == id);
                    const quantity = selectedItems[id];
                    selectedItemsList.innerHTML += `<div class="flex justify-between items-center bg-gray-100 p-3 rounded-lg"><div><p class="font-semibold">${item.nama_persediaan}</p><p class="text-xs text-gray-600">Maks: ${item.stok} ${item.satuan}</p></div><div class="flex items-center gap-2"><input type="number" value="${quantity}" min="1" max="${item.stok}" data-id="${id}" class="quantity-input w-20 p-1 border rounded text-center"><button data-id="${id}" class="remove-item-btn text-red-500 hover:text-red-700">✖</button></div></div>`;
                }
            };

            searchInput.addEventListener('input', (e) => {
                const searchTerm = e.target.value.toLowerCase();
                itemGrid.innerHTML = '';
                availableItems.filter(item => item.nama_persediaan.toLowerCase().includes(searchTerm)).forEach(item => {
                    const isSelected = selectedItems[item.id] !== undefined;
                    const card = document.createElement('div');
                    card.className = `item-card border-2 p-3 rounded-lg cursor-pointer transition-all ${isSelected ? 'selected' : ''}`;
                    card.dataset.id = item.id;
                    card.innerHTML = `<h3 class="font-bold text-sm text-slate-800">${item.nama_persediaan}</h3><p class="text-xs text-slate-500">Stok: ${item.stok} ${item.satuan}</p>`;
                    card.addEventListener('click', () => toggleItemSelection(item, card));
                    itemGrid.appendChild(card);
                });
            });

            const displaySummary = () => {
                document.getElementById('summary-details').innerHTML = `<p><strong>Nama Penerima:</strong> ${permintaanForm.nama_pemohon.value}</p><p><strong>Nomor Telepon:</strong> ${permintaanForm.nomor_telepon_pemohon.value}</p>`;
                const summaryItems = document.getElementById('summary-items');
                summaryItems.innerHTML = '';
                for (const id in selectedItems) {
                    const item = availableItems.find(i => i.id == id);
                    summaryItems.innerHTML += `<p>${selectedItems[id]} ${item.satuan} - ${item.nama_persediaan}</p>`;
                }
            };

            window.addEventListener("resize", resizeCanvas);

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
                document.getElementById('tanda_tangan_pemohon').value = signatureDataUrl;
                signatureStatus.textContent = "Tanda tangan telah ditambahkan";
                signatureStatus.classList.remove("text-gray-500");
                signatureStatus.classList.add("text-green-600");
                signatureModal.classList.remove('active');
            });

            clearSignatureBtn.addEventListener('click', () => {
                signaturePad.clear();
            });

            nextToStep2Btn.addEventListener('click', () => goToStep(2));
            nextToStep3Btn.addEventListener('click', () => {
                if (permintaanForm.checkValidity()) {
                    displaySummary();
                    goToStep(3);
                } else {
                    permintaanForm.reportValidity();
                }
            });
            backToStep1Btn.addEventListener('click', () => goToStep(1));
            backToStep2Btn.addEventListener('click', () => goToStep(2));

            selectedItemsList.addEventListener('click', (e) => {
                if (e.target.classList.contains('remove-item-btn')) {
                    delete selectedItems[e.target.dataset.id];
                    renderSelectedItems();
                }
            });
            selectedItemsList.addEventListener('change', (e) => {
                if (e.target.classList.contains('quantity-input')) {
                    const id = e.target.dataset.id;
                    const max = parseInt(e.target.max);
                    let value = parseInt(e.target.value);
                    if (value > max) value = max;
                    if (value < 1) value = 1;
                    e.target.value = value;
                    selectedItems[id] = value;
                }
            });
            submitBtn.addEventListener('click', async () => {
                const signatureDataUrl = document.getElementById('tanda_tangan_pemohon').value;
                if (!signatureDataUrl) {
                    alert('Mohon bubuhkan tanda tangan Anda.');
                    return;
                }

                if (!permintaanForm.checkValidity()) {
                    permintaanForm.reportValidity();
                    return;
                }

                if (Object.keys(selectedItems).length === 0) {
                    alert('Mohon pilih setidaknya satu item persediaan.');
                    return;
                }

                submitBtn.disabled = true;
                submitBtn.textContent = 'Mengirim...';
                const formData = new FormData(permintaanForm);
                formData.append('items', JSON.stringify(selectedItems));
                try {
                    const response = await fetch('api/add_permintaan.php', {
                        method: 'POST',
                        body: formData
                    });
                    const result = await response.json();
                    if (result.success) {
                        alert('Permintaan Anda telah berhasil dikirim! Terima kasih.');
                        window.location.reload();
                    } else {
                        alert('Gagal mengirim permintaan: ' + (result.message || 'Error tidak diketahui.'));
                    }
                } catch (error) {
                    alert('Terjadi kesalahan koneksi. Silakan coba lagi.');
                } finally {
                    submitBtn.disabled = false;
                    submitBtn.textContent = 'Kirim Permintaan';
                }
            });
            loadAvailableItems();
            renderSelectedItems();
        });
    </script>
</body>

</html>