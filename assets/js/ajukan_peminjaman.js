document.addEventListener("DOMContentLoaded", () => {
  // --- Elemen & State ---
  const steps = document.querySelectorAll(".form-step");
  const asetGrid = document.getElementById("aset-grid");
  const searchInput = document.getElementById("search-aset-input");
  const peminjamanForm = document.getElementById("peminjaman-form");
  let selectedAset = [];

  const signatureModal = document.getElementById("signature-modal");
  const openSignatureBtn = document.getElementById("open-signature-modal-btn");
  const closeSignatureModalBtn = document.getElementById(
    "close-signature-modal-btn"
  );
  const saveSignatureBtn = document.getElementById("save-signature-btn");
  const clearSignatureBtn = document.getElementById("clear-signature");
  const signatureStatus = document.getElementById("signature-status");
  const canvas = document.getElementById("signature-pad");
  const signaturePad = new SignaturePad(canvas, {
    backgroundColor: "rgb(255, 255, 255)",
  });
  const tandaTanganPeminjamHiddenInput = document.getElementById(
    "tanda_tangan_peminjam"
  );

  // --- Tombol Navigasi & Form ---
  const nextToStep2Btn = document.getElementById("next-to-step-2");
  const nextToStep3Btn = document.getElementById("next-to-step-3"); // Tombol baru
  const backToStep1Btn = document.getElementById("back-to-step-1");
  const backToStep2Btn = document.getElementById("back-to-step-2");
  const submitBtn = document.getElementById("submit-peminjaman-confirm"); // Tombol submit final

  // --- Toast Notification ---
  const toast = document.getElementById("toast-notification");
  const toastMessage = document.getElementById("toast-message");
  const showToast = (message, isSuccess = true) => {
    toastMessage.textContent = message;
    toast.className = `fixed bottom-5 right-5 p-4 rounded-lg shadow-lg text-white ${
      isSuccess ? "bg-green-500" : "bg-red-500"
    }`;
    toast.classList.remove("hidden");
    setTimeout(() => toast.classList.add("hidden"), 3000);
  };

  // --- Fungsi Navigasi ---
  const goToStep = (stepNumber) => {
    steps.forEach((step) => step.classList.remove("active"));
    document.getElementById(`step-${stepNumber}`).classList.add("active");
  };

  // --- Fungsi untuk Merender Aset ---
  const loadAset = async () => {
    try {
      const response = await fetch("api/get_aset_tersedia.php");
      const result = await response.json();
      if (result.success) {
        renderAsetGrid(result.data);
      } else {
        showToast("Gagal memuat daftar aset.", false);
      }
    } catch (error) {
      showToast("Terjadi kesalahan jaringan saat memuat aset.", false);
    }
  };

  const renderAsetGrid = (asets) => {
    asetGrid.innerHTML = "";
    asets.forEach((aset) => {
      const card = document.createElement("div");
      card.className =
        "aset-card border-2 p-3 rounded-lg cursor-pointer transition-all";
      card.dataset.id = aset.id;
      card.innerHTML = `<h3 class="font-bold text-sm text-slate-800">${
        aset.nama_bmn
      }</h3><p class="text-xs text-slate-500">${aset.kode_bmn} - ${
        aset.nup
      }</p><p class="text-xs text-slate-500">Merek: ${aset.merek || "-"}</p>`;
      card.addEventListener("click", () => toggleAsetSelection(aset, card));
      asetGrid.appendChild(card);
    });
  };

  // --- Logika Pemilihan Aset ---
  const toggleAsetSelection = (aset, card) => {
    const index = selectedAset.findIndex((item) => item.id === aset.id);
    if (index > -1) {
      selectedAset.splice(index, 1);
      card.classList.remove("selected");
    } else {
      selectedAset.push({
        id: aset.id,
        nama: aset.nama_bmn,
        kode_bmn: aset.kode_bmn,
      });
      card.classList.add("selected");
    }
    nextToStep2Btn.disabled = selectedAset.length === 0;
  };

  // --- Filter Aset ---
  searchInput.addEventListener("input", (e) => {
    const searchTerm = e.target.value.toLowerCase();
    const allCards = asetGrid.querySelectorAll(".aset-card");
    allCards.forEach((card) => {
      const cardText = card.textContent.toLowerCase();
      card.style.display = cardText.includes(searchTerm) ? "block" : "none";
    });
  });

  // --- Tampilkan Ringkasan ---
  const displaySummary = () => {
    const summaryDetails = document.getElementById("summary-details");
    const summaryAset = document.getElementById("summary-aset");
    const lokasiSelect = document.getElementById("lokasi");
    const lokasiKustomInput = document.getElementById("lokasi_kustom");
    let lokasiValue =
      lokasiSelect.value === "lainnya"
        ? lokasiKustomInput.value
        : lokasiSelect.value;
    const tanggalPeminjaman =
      document.getElementById("tanggal_peminjaman").value;

    summaryDetails.innerHTML = `
            <p><strong>Nama Peminjam:</strong> ${peminjamanForm.nama_peminjam.value}</p>
            <p><strong>Nomor Telepon:</strong> ${peminjamanForm.nomor_telepon_peminjam.value}</p>
            <p><strong>Periode Pinjam:</strong> ${tanggalPeminjaman}</p>
            <p><strong>Lokasi Tujuan:</strong> ${lokasiValue}</p>
            <p><strong>Alasan:</strong> ${peminjamanForm.alasan_peminjaman.value}</p>
        `;
    summaryAset.innerHTML = "";
    selectedAset.forEach((aset) => {
      summaryAset.innerHTML += `<li>${aset.nama} (${aset.kode_bmn})</li>`;
    });
  };

  // --- Event Listener Navigasi ---
  nextToStep2Btn.addEventListener("click", () => {
    // Cek validitas form pada langkah 1
    const requiredInputsStep1 =
      peminjamanForm.querySelectorAll("#step-1 [required]");
    let isStep1Valid = true;
    requiredInputsStep1.forEach((input) => {
      if (!input.value) {
        isStep1Valid = false;
      }
    });

    if (!isStep1Valid) {
      peminjamanForm.reportValidity();
      return;
    }

    if (selectedAset.length === 0) {
      alert("Pilih setidaknya satu aset.");
      return;
    }
    goToStep(2);
  });

  nextToStep3Btn.addEventListener("click", () => {
    const requiredInputsStep2 =
      peminjamanForm.querySelectorAll("#step-2 [required]");
    let isStep2Valid = true;
    requiredInputsStep2.forEach((input) => {
      if (!input.value) {
        isStep2Valid = false;
      }
    });
    if (!isStep2Valid) {
      peminjamanForm.reportValidity();
      return;
    }

    if (!tandaTanganPeminjamHiddenInput.value) {
      alert("Mohon bubuhkan tanda tangan Anda.");
      return;
    }

    displaySummary();
    goToStep(3);
  });

  backToStep1Btn.addEventListener("click", () => goToStep(1));
  backToStep2Btn.addEventListener("click", () => goToStep(2));

  // --- Logika Modal Tanda Tangan ---
  const resizeCanvas = () => {
    const ratio = Math.max(window.devicePixelRatio || 1, 1);
    canvas.width = canvas.offsetWidth * ratio;
    canvas.height = canvas.offsetHeight * ratio;
    canvas.getContext("2d").scale(ratio, ratio);
    signaturePad.clear();
  };
  openSignatureBtn.addEventListener("click", () => {
    signatureModal.classList.add("active");
    setTimeout(resizeCanvas, 50);
  });
  closeSignatureModalBtn.addEventListener("click", () => {
    signatureModal.classList.remove("active");
  });
  saveSignatureBtn.addEventListener("click", () => {
    if (signaturePad.isEmpty()) {
      alert("Kanvas tanda tangan masih kosong.");
      return;
    }
    const signatureDataUrl = signaturePad.toDataURL("image/png");
    tandaTanganPeminjamHiddenInput.value = signatureDataUrl;
    signatureStatus.textContent = "Tanda tangan telah ditambahkan";
    signatureStatus.classList.remove("text-gray-500");
    signatureStatus.classList.add("text-green-600");
    signatureModal.classList.remove("active");
  });
  clearSignatureBtn.addEventListener("click", () => {
    signaturePad.clear();
  });

  // --- Logika Form Submission ---
  submitBtn.addEventListener("click", async () => {
    submitBtn.disabled = true;
    submitBtn.textContent = "Mengirim...";

    const formData = new FormData(peminjamanForm);
    formData.append("aset_ids", JSON.stringify(selectedAset.map((a) => a.id)));

    try {
      const response = await fetch("api/add_peminjaman.php", {
        method: "POST",
        body: formData,
      });
      const result = await response.json();
      if (result.success) {
        alert(
          `Pengajuan Anda telah berhasil dikirim! Nomor SPA: ${result.data.nomor_surat}.`
        );
        window.location.reload();
      } else {
        alert(`Gagal mengirim pengajuan: ${result.message}`);
      }
    } catch (error) {
      alert("Terjadi kesalahan koneksi. Silakan coba lagi.");
    } finally {
      submitBtn.disabled = false;
      submitBtn.textContent = "Kirim Pengajuan";
    }
  });

  // --- Inisialisasi ---
  loadAset();
  flatpickr("#tanggal_peminjaman", {
    mode: "range",
    minDate: "today",
    dateFormat: "d F Y",
  });
  const lokasiSelect = document.getElementById("lokasi");
  const lokasiKustomInput = document.getElementById("lokasi_kustom");
  lokasiSelect.addEventListener("change", () => {
    lokasiKustomInput.classList.toggle(
      "hidden",
      lokasiSelect.value !== "lainnya"
    );
  });
});
