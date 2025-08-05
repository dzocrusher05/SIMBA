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
                    <label for="item-select" class="block text-sm font-medium text-gray-700">Pilih Item yang Tersedia</label>
                    <select id="item-select" class="mt-1 block w-full p-3 border border-gray-300 rounded-lg"></select>
                </div>
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
                    <div class="border rounded-lg bg-white">
                        <canvas id="signature-pad" class="w-full h-40"></canvas>
                    </div>
                    <button type="button" id="clear-signature" class="text-sm text-blue-600 hover:underline mt-1">Bersihkan</button>
                </div>
                <div class="flex justify-between mt-8">
                    <button id="back-to-step-2" class="bg-gray-200 text-gray-800 font-bold py-2 px-6 rounded-lg hover:bg-gray-300">Kembali</button>
                    <button id="submit-permintaan" class="bg-green-600 text-white font-bold py-2 px-6 rounded-lg hover:bg-green-700">Kirim Permintaan</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const steps = document.querySelectorAll('.form-step');
            const itemSelect = document.getElementById('item-select');
            const selectedItemsList = document.getElementById('selected-items-list');
            const permintaanForm = document.getElementById('permintaan-form');
            let availableItems = [];
            let selectedItems = {};
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
            const clearSignatureBtn = document.getElementById('clear-signature');

            const goToStep = (stepNumber) => {
                steps.forEach(step => step.classList.remove('active'));
                document.getElementById(`step-${stepNumber}`).classList.add('active');
            };

            const loadAvailableItems = async () => {
                const response = await fetch('api/get_list_persediaan.php');
                availableItems = await response.json();
                itemSelect.innerHTML = '<option value="">-- Tambah Item --</option>';
                availableItems.forEach(item => {
                    itemSelect.innerHTML += `<option value="${item.id}">[Stok: ${item.stok}] ${item.nama_persediaan} (${item.satuan})</option>`;
                });
            };

            const renderSelectedItems = () => {
                selectedItemsList.innerHTML = '';
                if (Object.keys(selectedItems).length === 0) {
                    selectedItemsList.innerHTML = '<p class="text-center text-gray-500">Belum ada item yang dipilih.</p>';
                    nextToStep2Btn.disabled = true;
                    return;
                }
                nextToStep2Btn.disabled = false;
                for (const id in selectedItems) {
                    const item = availableItems.find(i => i.id == id);
                    const quantity = selectedItems[id];
                    selectedItemsList.innerHTML += `<div class="flex justify-between items-center bg-gray-100 p-3 rounded-lg"><div><p class="font-semibold">${item.nama_persediaan}</p><p class="text-xs text-gray-600">Maks: ${item.stok} ${item.satuan}</p></div><div class="flex items-center gap-2"><input type="number" value="${quantity}" min="1" max="${item.stok}" data-id="${id}" class="quantity-input w-20 p-1 border rounded text-center"><button data-id="${id}" class="remove-item-btn text-red-500 hover:text-red-700">✖</button></div></div>`;
                }
            };

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
            resizeCanvas();
            nextToStep2Btn.addEventListener('click', () => goToStep(2));
            backToStep1Btn.addEventListener('click', () => goToStep(1));
            nextToStep3Btn.addEventListener('click', () => {
                if (permintaanForm.checkValidity()) {
                    displaySummary();
                    goToStep(3);
                } else {
                    permintaanForm.reportValidity();
                }
            });
            backToStep2Btn.addEventListener('click', () => goToStep(2));
            clearSignatureBtn.addEventListener('click', () => {
                signaturePad.clear();
            });
            itemSelect.addEventListener('change', (e) => {
                const selectedId = e.target.value;
                if (selectedId && !selectedItems[selectedId]) {
                    selectedItems[selectedId] = 1;
                    renderSelectedItems();
                }
                e.target.value = '';
            });
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
                if (signaturePad.isEmpty()) {
                    alert('Mohon bubuhkan tanda tangan Anda.');
                    return;
                }
                const signatureDataUrl = signaturePad.toDataURL('image/png');
                document.getElementById('tanda_tangan_pemohon').value = signatureDataUrl;
                if (!permintaanForm.checkValidity()) {
                    permintaanForm.reportValidity();
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