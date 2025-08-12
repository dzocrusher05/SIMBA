document.addEventListener("DOMContentLoaded", () => {
  // --- STATE ---
  let currentPage = 1;
  let actionToConfirm = { action: null, id: null };

  // --- ELEMEN ---
  const tableBody = document.getElementById("table-body");
  const searchInput = document.getElementById("search-input");
  const paginationContainer = document.getElementById("pagination-container");
  const modal = document.getElementById("action-confirm-modal");
  const modalTitle = document.getElementById("modal-title");
  const modalMessage = document.getElementById("modal-message");
  const confirmBtn = document.getElementById("confirm-action-btn");
  const cancelBtn = document.getElementById("cancel-action-btn");
  const toast = document.getElementById("toast-notification");
  const toastMessage = document.getElementById("toast-message");
  const approveModal = document.getElementById("approve-modal");
  const approveForm = document.getElementById("approve-form");
  const approveItemsList = document.getElementById("approve-items-list");
  const adminSignaturePadCanvas = document.getElementById(
    "admin-signature-pad"
  );
  const adminSignaturePad = new SignaturePad(adminSignaturePadCanvas, {
    backgroundColor: "rgb(255, 255, 255)",
  });
  const { jsPDF } = window.jspdf;

  // --- FUNGSI ---
  const resizeAdminCanvas = () => {
    const ratio = Math.max(window.devicePixelRatio || 1, 1);
    adminSignaturePadCanvas.width = adminSignaturePadCanvas.offsetWidth * ratio;
    adminSignaturePadCanvas.height =
      adminSignaturePadCanvas.offsetHeight * ratio;
    adminSignaturePadCanvas.getContext("2d").scale(ratio, ratio);
    adminSignaturePad.clear();
  };

  const fetchPeminjaman = async () => {
    try {
      const response = await fetch(
        `api/get_peminjaman.php?page=${currentPage}&search=${searchInput.value}`
      );
      if (!response.ok) throw new Error("Network response was not ok");
      const result = await response.json();
      if (result.success) {
        renderTable(result.data);
        renderPagination(result.pagination);
      } else {
        tableBody.innerHTML = `<tr><td colspan="5" class="text-center py-4 text-gray-500">${result.message}</td></tr>`;
      }
    } catch (error) {
      console.error("Fetch Error:", error);
      tableBody.innerHTML = `<tr><td colspan="5" class="text-center py-4">Gagal memuat data.</td></tr>`;
    }
  };

  const renderTable = (data) => {
    tableBody.innerHTML = "";
    if (data.length === 0) {
      tableBody.innerHTML = `<tr><td colspan="5" class="text-center py-4 text-gray-500">Tidak ada data ditemukan.</td></tr>`;
      return;
    }
    data.forEach((item) => {
      let lamaPinjamStr = "-";
      if (item.tanggal_pinjam && item.tanggal_kembali) {
        const tglPinjam = new Date(item.tanggal_pinjam);
        const tglKembali = new Date(item.tanggal_kembali);
        const diffTime = Math.abs(tglKembali - tglPinjam);
        const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1;
        lamaPinjamStr = `${diffDays} hari`;
      }

      const periodePinjamStr = item.tanggal_pinjam
        ? `${new Date(item.tanggal_pinjam).toLocaleDateString("id-ID", {
            day: "numeric",
            month: "long",
            year: "numeric",
          })}`
        : "-";

      let actions = "";
      let statusClass = "";
      let printButton = "";

      switch (item.status_peminjaman) {
        case "Diajukan":
          statusClass = "bg-yellow-100 text-yellow-800";
          actions = `<button data-id="${item.id}" data-action="approve" class="action-btn bg-green-500 text-white px-2 py-1 text-xs rounded hover:bg-green-600">Setujui</button>
                     <button data-id="${item.id}" data-action="reject" class="action-btn bg-red-500 text-white px-2 py-1 text-xs rounded hover:bg-red-600 ml-1">Tolak</button>`;
          break;
        case "Disetujui":
          statusClass = "bg-blue-100 text-blue-800";
          actions = `<button data-id="${item.id}" data-action="return" class="action-btn bg-blue-500 text-white px-2 py-1 text-xs rounded hover:bg-blue-600">Kembalikan</button>`;
          printButton = `<button data-id="${item.id}" data-type="approve" class="print-btn bg-blue-500 text-white px-2 py-1 text-xs rounded hover:bg-blue-600 ml-1">Cetak SPA</button>`;
          break;
        case "Dikembalikan":
          statusClass = "bg-gray-100 text-gray-800";
          printButton = `<button data-id="${item.id}" data-type="approve" class="print-btn bg-blue-500 text-white px-2 py-1 text-xs rounded hover:bg-blue-600 ml-1">Cetak SPA</button>`;
          break;
        case "Ditolak":
          statusClass = "bg-red-100 text-red-800";
          printButton = `<button data-id="${item.id}" data-type="reject" class="print-btn bg-red-500 text-white px-2 py-1 text-xs rounded hover:bg-red-600 ml-1">Cetak Penolakan</button>`;
          break;
      }

      const tanggalPengajuanFormatted = item.tanggal_pengajuan
        ? new Date(item.tanggal_pengajuan).toLocaleDateString("id-ID", {
            day: "numeric",
            month: "short",
            year: "numeric",
          })
        : "-";

      let asetHtml = '<span class="text-gray-400">-</span>';
      if (item.detail_aset && item.detail_aset.length > 0) {
        asetHtml = '<div class="space-y-3">';
        item.detail_aset.forEach((aset) => {
          asetHtml += `
            <div class="p-2 bg-slate-50 rounded-md border border-slate-200">
              <p class="font-bold text-sm text-slate-800">${aset.nama_bmn}</p>
              <div class="text-xs text-slate-500 mt-1 grid grid-cols-2 gap-x-2">
                <span>Kode: ${aset.kode_bmn || "-"}</span>
                <span>NUP: ${aset.nup || "-"}</span>
                <span class="col-span-2">Merek: ${aset.merek || "-"}</span>
              </div>
            </div>
          `;
        });
        asetHtml += "</div>";
      }

      tableBody.innerHTML += `
                <tr class="border-b hover:bg-gray-50">
                    <td class="py-3 px-4 font-medium text-gray-800 align-top">${asetHtml}</td>
                    <td class="py-3 px-4 align-top">
                        <div class="font-bold text-slate-800">${
                          item.nama_peminjam || "-"
                        }</div>
                        <div class="text-xs text-slate-500 mt-1">
                            ${item.nomor_surat || ""}
                        </div>
                        <div class="flex items-center text-xs text-slate-500 mt-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" /></svg>
                            <span>${item.lokasi_peminjaman || "-"}</span>
                        </div>
                        <div class="flex items-center text-xs text-slate-500 mt-1">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd" /></svg>
                            <span>Diajukan: ${tanggalPengajuanFormatted}</span>
                        </div>
                    </td>
                    <td class="py-3 px-4 align-top text-sm">
                        <div>${periodePinjamStr}</div>
                        <div class="text-xs text-slate-500">(${lamaPinjamStr})</div>
                    </td>
                    <td class="py-3 px-4 align-top"><span class="px-2 py-1 text-xs font-medium rounded-full ${statusClass}">${
        item.status_peminjaman || "-"
      }</span></td>
                    <td class="py-3 px-4 text-center align-top">${actions}${printButton}</td>
                </tr>
            `;
    });
  };

  const renderPagination = (pagination) => {
    paginationContainer.innerHTML = "";
    if (!pagination || pagination.total_pages <= 1) return;
    for (let i = 1; i <= pagination.total_pages; i++) {
      const btn = document.createElement("button");
      btn.textContent = i;
      btn.className = `px-3 py-1 mx-1 rounded-md text-sm ${
        i === pagination.current_page
          ? "bg-blue-600 text-white"
          : "bg-white text-gray-700 hover:bg-gray-100 border"
      }`;
      btn.onclick = () => {
        currentPage = i;
        fetchPeminjaman();
      };
      paginationContainer.appendChild(btn);
    }
  };

  const showToast = (message, isSuccess = true) => {
    toastMessage.textContent = message;
    toast.className = `fixed bottom-5 right-5 p-4 rounded-lg shadow-lg text-white ${
      isSuccess ? "bg-green-500" : "bg-red-500"
    }`;
    toast.classList.remove("hidden");
    setTimeout(() => toast.classList.add("hidden"), 3000);
  };

  const openConfirmationModal = (action, id) => {
    actionToConfirm = { action, id };
    const configs = {
      reject: {
        title: "Tolak Peminjaman",
        message: "Anda yakin ingin menolak pengajuan ini?",
        btnClass: "bg-red-600 hover:bg-red-700",
      },
      return: {
        title: "Proses Pengembalian",
        message:
          'Konfirmasi bahwa aset telah dikembalikan? Status aset akan kembali menjadi "Tersedia".',
        btnClass: "bg-blue-600 hover:bg-blue-700",
      },
    };
    const config = configs[action];
    modalTitle.textContent = config.title;
    modalMessage.textContent = config.message;
    confirmBtn.className = `px-4 py-2 text-white rounded-lg ${config.btnClass}`;
    modal.classList.remove("hidden");
  };

  const performAction = async () => {
    const { action, id } = actionToConfirm;
    if (!action || !id) return;
    const formData = new FormData();
    formData.append("id", id);
    try {
      const response = await fetch(`api/${action}_peminjaman.php`, {
        method: "POST",
        body: formData,
      });
      const result = await response.json();
      if (result.success) {
        showToast(
          `Peminjaman berhasil di-${
            action === "approve"
              ? "setujui"
              : action === "reject"
              ? "tolak"
              : "kembalikan"
          }!`
        );
        fetchPeminjaman();
      } else {
        showToast(result.message || "Aksi gagal.", false);
      }
    } catch (error) {
      showToast("Terjadi kesalahan jaringan.", false);
    } finally {
      modal.classList.add("hidden");
    }
  };

  const openApproveModal = async (peminjamanId) => {
    document.getElementById("approve_peminjaman_id").value = peminjamanId;
    approveItemsList.innerHTML = `<div class="text-center text-gray-500">Sedang memuat detail aset...</div>`;

    try {
      const response = await fetch(
        `api/get_detail_peminjaman.php?id=${peminjamanId}`
      );
      if (!response.ok) {
        throw new Error(`HTTP error! status: ${response.status}`);
      }
      const result = await response.json();

      if (result.success) {
        approveItemsList.innerHTML = "";
        if (result.data.items && result.data.items.length > 0) {
          result.data.items.forEach((item) => {
            approveItemsList.innerHTML += `<div class="bg-gray-100 p-2 rounded-md text-sm">${
              item.nama_bmn
            } (${item.no_bmn || "N/A"})</div>`;
          });
        } else {
          approveItemsList.innerHTML = `<div class="text-center text-gray-500">Tidak ada item aset terlampir.</div>`;
        }

        approveModal.classList.remove("hidden");
        setTimeout(resizeAdminCanvas, 50);
      } else {
        showToast(result.message || "Gagal memuat detail.", false);
      }
    } catch (error) {
      console.error("Fetch detail error:", error);
      showToast(
        "Gagal memuat detail peminjaman. Periksa file api/get_detail_peminjaman.php.",
        false
      );
    }
  };

  const generatePeminjamanPDF = (peminjamanData, type) => {
    const doc = new jsPDF();
    const data = peminjamanData.data;

    let title;
    let titleColor = "#000000";
    if (type === "approve") {
      title = "SURAT PERSETUJUAN PEMINJAMAN ASET (SPA)";
    } else {
      title = "PEMBERITAHUAN PENOLAKAN PEMINJAMAN";
      titleColor = "#ff0000";
    }

    doc.setFontSize(18);
    doc.setFont("helvetica", "bold");
    doc.text("SIMBA - Sistem Informasi Manajemen BMN & Aset", 105, 20, {
      align: "center",
    });
    doc.setLineWidth(0.5);
    doc.line(15, 25, 195, 25);

    doc.setFontSize(14);
    doc.setTextColor(titleColor);
    doc.text(title, 105, 35, { align: "center" });

    doc.setFontSize(10);
    doc.setTextColor("#000000"); // Reset warna teks
    doc.setFont("helvetica", "normal");

    if (type === "approve" && data.nomor_surat) {
      doc.text(`Nomor: ${data.nomor_surat}`, 105, 42, { align: "center" });
    }

    doc.text(`Nama Peminjam: ${data.nama_peminjam}`, 15, 50);
    doc.text(`Alasan Peminjaman: ${data.alasan_peminjaman}`, 15, 57);
    doc.text(`Status: ${data.status_peminjaman}`, 195, 57, { align: "right" });

    if (type === "approve") {
      doc.text(
        `Periode Pinjam: ${data.tanggal_pinjam} s/d ${data.tanggal_kembali}`,
        15,
        64
      );
    }

    const tableBody = data.items.map((item, index) => [
      index + 1,
      item.nama_bmn,
      item.no_bmn,
    ]);
    doc.autoTable({
      head: [["No", "Nama Aset", "No. BMN"]],
      body: tableBody,
      startY: type === "approve" ? 75 : 65,
      theme: "grid",
      headStyles: { fillColor: [44, 62, 80] },
    });

    const finalY = doc.autoTable.previous.finalY + 20;
    const peminjamX = 45;
    const adminX = 165;
    const adminName = "Nama Admin"; // Ganti dengan nama admin yang benar

    doc.text("Peminjam,", peminjamX, finalY, { align: "center" });
    if (data.tanda_tangan_peminjam) {
      doc.addImage(
        data.tanda_tangan_peminjam,
        "PNG",
        peminjamX - 30,
        finalY + 5,
        60,
        20
      );
    }
    doc.text(`(${data.nama_peminjam})`, peminjamX, finalY + 30, {
      align: "center",
    });

    doc.text("Disetujui Oleh,", adminX, finalY, { align: "center" });
    if (data.tanda_tangan_admin) {
      doc.addImage(
        data.tanda_tangan_admin,
        "PNG",
        adminX - 30,
        finalY + 5,
        60,
        20
      );
      doc.text(`(${adminName})`, adminX, finalY + 30, {
        align: "center",
      });
    } else {
      doc.text("(_________________)", adminX, finalY + 30, {
        align: "center",
      });
    }

    const fileName = `${title.replace(/ /g, "_")}_${data.nama_peminjam.replace(
      / /g,
      "_"
    )}.pdf`;
    doc.save(fileName);
  };

  // --- EVENT LISTENERS ---
  searchInput.addEventListener("input", () => {
    currentPage = 1;
    fetchPeminjaman();
  });

  tableBody.addEventListener("click", async (e) => {
    const target = e.target.closest("button"); // More robust event delegation
    if (!target) return;

    if (target.classList.contains("action-btn")) {
      const action = target.dataset.action;
      const id = target.dataset.id;
      if (action === "approve") {
        openApproveModal(id);
      } else {
        openConfirmationModal(action, id);
      }
    }
    if (target.classList.contains("print-btn")) {
      const peminjamanId = target.dataset.id;
      const type = target.dataset.type;
      try {
        const response = await fetch(
          `api/get_detail_peminjaman.php?id=${peminjamanId}`
        );
        const result = await response.json();
        if (result.success) {
          generatePeminjamanPDF(result, type);
        } else {
          showToast(result.message, false);
        }
      } catch (error) {
        showToast("Gagal mengambil data untuk dicetak.", false);
      }
    }
  });

  cancelBtn.addEventListener("click", () => modal.classList.add("hidden"));
  confirmBtn.addEventListener("click", performAction);

  approveModal
    .querySelector(".close-approve-modal")
    .addEventListener("click", () => {
      approveModal.classList.add("hidden");
      adminSignaturePad.clear();
    });

  document
    .getElementById("clear-admin-signature")
    .addEventListener("click", () => {
      adminSignaturePad.clear();
    });

  approveForm.addEventListener("submit", async (e) => {
    e.preventDefault();
    if (adminSignaturePad.isEmpty()) {
      alert("Tanda tangan admin wajib diisi.");
      return;
    }

    const adminSignatureDataUrl = adminSignaturePad.toDataURL("image/png");
    const peminjamanId = document.getElementById("approve_peminjaman_id").value;

    const formData = new FormData();
    formData.append("id", peminjamanId);
    formData.append("tanda_tangan_admin", adminSignatureDataUrl);

    try {
      const response = await fetch("api/approve_peminjaman.php", {
        method: "POST",
        body: formData,
      });
      const result = await response.json();
      if (result.success) {
        showToast("Peminjaman berhasil disetujui!");
        approveModal.classList.add("hidden");
        fetchPeminjaman();
      } else {
        showToast(result.message, false);
      }
    } catch (error) {
      showToast("Terjadi kesalahan jaringan.", false);
    }
  });

  fetchPeminjaman();
});
