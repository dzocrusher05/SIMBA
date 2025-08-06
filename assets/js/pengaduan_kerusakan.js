document.addEventListener("DOMContentLoaded", () => {
  // DOM Elements for Steps
  const steps = {
    step1: document.getElementById("step-1"),
    step2: document.getElementById("step-2"),
  };

  // DOM Elements for Forms
  const pengaduanForm = document.getElementById("pengaduan-form");
  const asetSelect = document.getElementById("aset_id");
  const namaPelaporInput = document.getElementById("nama_pelapor");
  const nomorTeleponPelaporInput = document.getElementById(
    "nomor_telepon_pelapor"
  );
  const deskripsiInput = document.getElementById("deskripsi");
  const gambarInput = document.getElementById("gambar");
  const submitBtn = document.getElementById("submit-btn");
  const nextToStep2Btn = document.getElementById("next-to-step-2");
  const backToStep1Btn = document.getElementById("back-to-step-1");

  // DOM Elements for Summary
  const summaryContent = document.getElementById("summary-content");

  // DOM Elements for Signature Pad
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
  const tandaTanganPelaporHiddenInput = document.getElementById(
    "tanda_tangan_pelapor"
  );

  // Toast Notification
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

  // Function to navigate between steps
  const goToStep = (stepNumber) => {
    for (const step in steps) {
      steps[step].classList.remove("active");
      steps[step].classList.add("hidden");
    }
    document.getElementById(`step-${stepNumber}`).classList.remove("hidden");
    document.getElementById(`step-${stepNumber}`).classList.add("active");
  };

  // Function to resize signature canvas
  const resizeCanvas = () => {
    const ratio = Math.max(window.devicePixelRatio || 1, 1);
    canvas.width = canvas.offsetWidth * ratio;
    canvas.height = canvas.offsetHeight * ratio;
    canvas.getContext("2d").scale(ratio, ratio);
    signaturePad.clear();
  };

  // Load assets for the new report form
  const loadAsets = async () => {
    try {
      // Memanggil API baru yang tidak menggunakan paginasi
      const response = await fetch("api/get_aset_list.php");
      const result = await response.json();
      if (result.success) {
        asetSelect.innerHTML = '<option value="">-- Pilih Aset --</option>';
        result.data.forEach((aset) => {
          asetSelect.innerHTML += `<option value="${aset.id}">${aset.nama_bmn} (${aset.no_bmn})</option>`;
        });
      } else {
        showToast("Gagal memuat data aset.", false);
      }
    } catch (error) {
      showToast("Terjadi kesalahan jaringan saat memuat aset.", false);
    }
  };

  // Display summary before final submission
  const displaySummary = () => {
    const selectedAsetOption = asetSelect.options[asetSelect.selectedIndex];
    const asetText = selectedAsetOption
      ? selectedAsetOption.textContent
      : "Tidak dipilih";

    summaryContent.innerHTML = `
            <p><strong>Nama Pelapor:</strong> ${namaPelaporInput.value}</p>
            <p><strong>Nomor Telepon:</strong> ${
              nomorTeleponPelaporInput.value
            }</p>
            <p><strong>Aset Dilaporkan:</strong> ${asetText}</p>
            <p><strong>Deskripsi Kerusakan:</strong> ${deskripsiInput.value}</p>
            ${
              gambarInput.files.length > 0
                ? `<p><strong>Gambar Terlampir:</strong> ${gambarInput.files[0].name}</p>`
                : ""
            }
        `;
  };

  // Form submission for new report
  pengaduanForm.addEventListener("submit", async (e) => {
    e.preventDefault(); // Prevent default form submission as we handle it with fetch
  });

  submitBtn.addEventListener("click", async () => {
    if (!pengaduanForm.checkValidity()) {
      pengaduanForm.reportValidity();
      return;
    }

    const signatureDataUrl = tandaTanganPelaporHiddenInput.value;
    if (!signatureDataUrl) {
      alert("Mohon bubuhkan tanda tangan Anda.");
      return;
    }

    submitBtn.disabled = true;
    submitBtn.textContent = "Mengirim...";

    const formData = new FormData(pengaduanForm);
    formData.append("tanda_tangan_pelapor", signatureDataUrl); // Append signature data

    try {
      const response = await fetch("api/add_pengaduan_kerusakan.php", {
        method: "POST",
        body: formData,
      });
      const result = await response.json();

      if (result.success) {
        showToast(result.message);
        pengaduanForm.reset();
        signatureStatus.textContent = "Belum ada tanda tangan";
        signatureStatus.classList.remove("text-green-600");
        signatureStatus.classList.add("text-gray-500");
        goToStep(1); // Kembali ke langkah pertama setelah sukses
      } else {
        showToast(result.message, false);
      }
    } catch (error) {
      showToast("Terjadi kesalahan jaringan saat mengirim pengaduan.", false);
    } finally {
      submitBtn.disabled = false;
      submitBtn.textContent = "Kirim Pengaduan";
    }
  });

  // Signature Pad logic
  openSignatureBtn.addEventListener("click", () => {
    signatureModal.classList.add("active");
    setTimeout(resizeCanvas, 50);
  });

  closeSignatureModalBtn.addEventListener("click", () => {
    signatureModal.classList.add("hidden"); // Use hidden class
  });

  saveSignatureBtn.addEventListener("click", () => {
    if (signaturePad.isEmpty()) {
      alert("Kanvas tanda tangan masih kosong.");
      return;
    }
    const signatureDataUrl = signaturePad.toDataURL("image/png");
    tandaTanganPelaporHiddenInput.value = signatureDataUrl;
    signatureStatus.textContent = "Tanda tangan telah ditambahkan";
    signatureStatus.classList.remove("text-gray-500");
    signatureStatus.classList.add("text-green-600");
    signatureModal.classList.add("hidden"); // Use hidden class
  });

  clearSignatureBtn.addEventListener("click", () => {
    signaturePad.clear();
  });

  // Navigation buttons
  nextToStep2Btn.addEventListener("click", () => {
    if (!pengaduanForm.checkValidity()) {
      pengaduanForm.reportValidity();
      return;
    }
    displaySummary();
    goToStep(2);
  });
  backToStep1Btn.addEventListener("click", () => goToStep(1));

  // Initial load
  loadAsets();
  goToStep(1); // Start at step 1
});
