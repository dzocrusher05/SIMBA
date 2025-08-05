document.addEventListener("DOMContentLoaded", () => {
  // === STATE & ELEMEN ===
  let currentPage = 1;
  let actionToConfirm = { action: null, id: null };
  const { jsPDF } = window.jspdf;
  const tableBody = document.getElementById("table-body");
  const searchInput = document.getElementById("search-input");
  const paginationContainer = document.getElementById("pagination-container");
  const addModal = document.getElementById("add-modal");
  const addForm = document.getElementById("add-form");
  const openAddModalBtn = document.getElementById("open-add-modal");
  const itemSelect = document.getElementById("persediaan_id");
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
  const adminName = document.getElementById("admin-name").value;
  const adminSignaturePadCanvas = document.getElementById(
    "admin-signature-pad"
  );
  const adminSignaturePad = new SignaturePad(adminSignaturePadCanvas, {
    backgroundColor: "rgb(255, 255, 255)",
  });
  const nomorSpbDisplay = document.getElementById("nomor_spb_display");

  // === FUNGSI-FUNGSI ===
  const resizeAdminCanvas = () => {
    const ratio = Math.max(window.devicePixelRatio || 1, 1);
    adminSignaturePadCanvas.width = adminSignaturePadCanvas.offsetWidth * ratio;
    adminSignaturePadCanvas.height =
      adminSignaturePadCanvas.offsetHeight * ratio;
    adminSignaturePadCanvas.getContext("2d").scale(ratio, ratio);
    adminSignaturePad.clear();
  };

  const fetchPermintaan = async () => {
    try {
      const response = await fetch(
        `api/get_permintaan.php?page=${currentPage}&search=${searchInput.value}`
      );
      const result = await response.json();
      renderTable(result.data);
      renderPagination(result.pagination);
    } catch (error) {
      console.error("Fetch error:", error);
      tableBody.innerHTML = `<tr><td colspan="5" class="text-center py-4">Gagal memuat data.</td></tr>`;
    }
  };

  const renderTable = (data) => {
    tableBody.innerHTML = "";
    if (data.length === 0) {
      tableBody.innerHTML = `<tr><td colspan="5" class="text-center py-4 text-gray-500">Tidak ada data permintaan.</td></tr>`;
      return;
    }
    data.forEach((item) => {
      let actions = "";
      let statusClass = "";
      let printButton = "";

      switch (item.status_permintaan) {
        case "Diajukan":
          statusClass = "bg-yellow-100 text-yellow-800";
          actions = `<button data-id="${item.id}" data-action="approve" class="action-btn bg-green-500 text-white px-2 py-1 text-xs rounded hover:bg-green-600">Proses</button>
                               <button data-id="${item.id}" data-action="reject" class="action-btn bg-red-500 text-white px-2 py-1 text-xs rounded hover:bg-red-600 ml-1">Tolak</button>`;
          printButton = `<button data-id="${item.id}" data-type="spb" class="print-btn bg-gray-500 text-white px-2 py-1 text-xs rounded hover:bg-gray-600 ml-1">Cetak SPB</button>`;
          break;
        case "Disetujui":
          statusClass = "bg-green-100 text-green-800";
          const sppButton = `<button data-id="${item.id}" data-type="spb" class="print-btn bg-gray-500 text-white px-2 py-1 text-xs rounded hover:bg-gray-600 ml-1">Cetak SPB</button>`;
          const bbkButton = `<button data-id="${item.id}" data-type="sbbk" class="print-btn bg-blue-500 text-white px-2 py-1 text-xs rounded hover:bg-blue-600 ml-1">Cetak SBBK</button>`;
          printButton = sppButton + bbkButton;
          break;
        case "Ditolak":
          statusClass = "bg-red-100 text-red-800";
          printButton = "";
          break;
      }
      const daftarItemFormatted = item.daftar_item
        ? item.daftar_item.replace(/; /g, "<br>")
        : "-";
      const tanggalFormatted = item.tanggal_permintaan
        ? new Date(item.tanggal_permintaan).toLocaleDateString("id-ID", {
            day: "numeric",
            month: "short",
            year: "numeric",
          })
        : "-";
      tableBody.innerHTML += `
                <tr class="border-b hover:bg-gray-50">
                    <td class="py-3 px-4 font-medium text-gray-800 align-top">${daftarItemFormatted}</td>
                    <td class="py-3 px-4 align-top">${item.nama_pemohon}</td>
                    <td class="py-3 px-4 align-top">${tanggalFormatted}</td>
                    <td class="py-3 px-4 align-top"><span class="px-2 py-1 text-xs font-medium rounded-full ${statusClass}">${item.status_permintaan}</span></td>
                    <td class="py-3 px-4 text-center align-top">${actions}${printButton}</td>
                </tr>
            `;
    });
  };

  const renderPagination = (pagination) => {
    paginationContainer.innerHTML = "";
    if (pagination.total_pages <= 1) return;
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
        fetchPermintaan();
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
    if (action !== "reject") return;
    actionToConfirm = { action, id };
    modalTitle.textContent = "Tolak Permintaan";
    modalMessage.textContent = "Anda yakin ingin menolak permintaan ini?";
    confirmBtn.className =
      "px-4 py-2 text-white rounded-lg bg-red-600 hover:bg-red-700";
    modal.classList.remove("hidden");
  };

  const performAction = async () => {
    const { action, id } = actionToConfirm;
    if (action !== "reject" || !id) return;
    const formData = new FormData();
    formData.append("id", id);
    try {
      const response = await fetch(`api/reject_permintaan.php`, {
        method: "POST",
        body: formData,
      });
      const result = await response.json();
      if (result.success) {
        showToast("Permintaan berhasil ditolak!");
        fetchPermintaan();
      } else {
        showToast(result.message || "Aksi gagal.", false);
      }
    } catch (error) {
      showToast("Terjadi kesalahan jaringan.", false);
    } finally {
      modal.classList.add("hidden");
    }
  };

  const populateItemOptions = async () => {
    try {
      const response = await fetch("api/get_list_persediaan.php");
      const items = await response.json();
      itemSelect.innerHTML = '<option value="">-- Pilih Item --</option>';
      items.forEach((item) => {
        itemSelect.innerHTML += `<option value="${item.id}">[Stok: ${item.stok}] ${item.nama_persediaan}</option>`;
      });
    } catch (error) {
      console.error("Gagal memuat daftar item:", error);
    }
  };

  const generateInvoicePDF = (permintaanData, type) => {
    const doc = new jsPDF();
    const data = permintaanData.data;

    const isSPB = type === "spb";
    const nomorSurat = isSPB ? data.nomor_spb : data.nomor_sbbk;
    const title = isSPB
      ? "SURAT PERMINTAAN BARANG"
      : "SURAT BUKTI BARANG KELUAR (SBBK)";
    const fileNamePrefix = isSPB ? "SPB" : "SBBK";

    doc.setFontSize(18);
    doc.setFont("helvetica", "bold");
    doc.text("SIMBA - Sistem Informasi Manajemen BMN & Aset", 105, 20, {
      align: "center",
    });
    doc.setLineWidth(0.5);
    doc.line(15, 25, 195, 25);
    doc.setFontSize(14);
    doc.text(title, 105, 35, { align: "center" });

    doc.setFontSize(10);
    doc.setFont("helvetica", "normal");
    doc.text(`Nomor: ${nomorSurat}`, 105, 42, { align: "center" });

    doc.text(`Nama Penerima: ${data.nama_pemohon}`, 15, 50);
    doc.text(
      `Tanggal Permintaan: ${new Date(
        data.tanggal_permintaan
      ).toLocaleDateString("id-ID", {
        day: "2-digit",
        month: "long",
        year: "numeric",
      })}`,
      15,
      57
    );
    doc.text(`Status: ${data.status_permintaan}`, 195, 57, { align: "right" });
    const tableBody = data.items.map((item, index) => [
      index + 1,
      item.nama_persediaan,
      item.jumlah_diminta,
      item.satuan,
    ]);
    doc.autoTable({
      head: [["No", "Nama Barang", "Jumlah", "Satuan"]],
      body: tableBody,
      startY: 65,
      theme: "grid",
      headStyles: { fillColor: [44, 62, 80] },
    });

    const finalY = doc.autoTable.previous.finalY + 20;
    const penerimaX = 45;
    const pengelolaX = 165;

    doc.text("Penerima,", penerimaX, finalY, { align: "center" });
    if (data.tanda_tangan_pemohon) {
      doc.addImage(
        data.tanda_tangan_pemohon,
        "PNG",
        penerimaX - 30,
        finalY + 5,
        60,
        20
      );
    }
    doc.text(`(${data.nama_pemohon})`, penerimaX, finalY + 30, {
      align: "center",
    });

    doc.text("Pengelola Gudang,", pengelolaX, finalY, { align: "center" });
    if (data.tanda_tangan_admin) {
      doc.addImage(
        data.tanda_tangan_admin,
        "PNG",
        pengelolaX - 30,
        finalY + 5,
        60,
        20
      );
      doc.text(`(${adminName})`, pengelolaX, finalY + 30, {
        align: "center",
      });
    } else {
      doc.text("(_________________)", pengelolaX, finalY + 30, {
        align: "center",
      });
    }

    const fileName = `${fileNamePrefix}_${data.nama_pemohon.replace(
      / /g,
      "_"
    )}.pdf`;
    doc.save(fileName);
  };

  const openApproveModal = async (permintaanId) => {
    try {
      const response = await fetch(
        `api/get_detail_permintaan.php?id=${permintaanId}`
      );
      const result = await response.json();
      if (result.success) {
        document.getElementById("approve_permintaan_id").value = permintaanId;
        approveItemsList.innerHTML = "";
        result.data.items.forEach((item) => {
          approveItemsList.innerHTML += `<div class="grid grid-cols-[1fr_80px] gap-2 items-center"><label class="col-span-1 text-sm">${item.nama_persediaan} (${item.satuan})</label><input type="number" value="${item.jumlah_diminta}" min="0" name="item_${item.persediaan_id}" data-id="${item.persediaan_id}" class="item-approve-qty w-full p-1 border rounded text-center"></div>`;
        });

        // Menampilkan nomor SPB yang sudah ada dari database
        if (result.data.nomor_spb) {
          nomorSpbDisplay.value = result.data.nomor_spb;
        } else {
          nomorSpbDisplay.value = "Belum Tersedia";
        }

        approveModal.classList.remove("hidden");
        setTimeout(resizeAdminCanvas, 50);
      } else {
        showToast(result.message, false);
      }
    } catch (error) {
      showToast("Gagal memuat detail permintaan.", false);
    }
  };

  // === EVENT LISTENERS ===
  searchInput.addEventListener("input", () => {
    currentPage = 1;
    fetchPermintaan();
  });

  tableBody.addEventListener("click", async (e) => {
    const target = e.target;
    if (target.classList.contains("action-btn")) {
      const action = target.dataset.action;
      const id = target.dataset.id;
      if (action === "approve") {
        openApproveModal(id);
      } else if (action === "reject") {
        openConfirmationModal(action, id);
      }
    }
    if (e.target.classList.contains("print-btn")) {
      const permintaanId = target.dataset.id;
      const type = target.dataset.type;
      try {
        const response = await fetch(
          `api/get_detail_permintaan.php?id=${permintaanId}`
        );
        const result = await response.json();
        if (result.success) {
          generateInvoicePDF(result, type);
        } else {
          showToast(result.message, false);
        }
      } catch (error) {
        showToast("Gagal mengambil data untuk dicetak.", false);
      }
    }
  });

  if (modal) {
    cancelBtn.addEventListener("click", () => modal.classList.add("hidden"));
    confirmBtn.addEventListener("click", performAction);
  }

  if (approveModal) {
    approveForm.addEventListener("submit", async (e) => {
      e.preventDefault();
      if (adminSignaturePad.isEmpty()) {
        alert("Tanda tangan Pengelola Gudang wajib diisi.");
        return;
      }

      const adminSignatureDataUrl = adminSignaturePad.toDataURL("image/png");
      const permintaanId = document.getElementById(
        "approve_permintaan_id"
      ).value;

      const inputs = approveItemsList.querySelectorAll(".item-approve-qty");
      const itemsToApprove = [];
      inputs.forEach((input) => {
        const jumlah = parseInt(input.value);
        if (jumlah > 0) {
          itemsToApprove.push({ id: input.dataset.id, jumlah: jumlah });
        }
      });

      if (itemsToApprove.length === 0) {
        showToast(
          "Setujui setidaknya satu item dengan jumlah lebih dari 0.",
          false
        );
        return;
      }

      const formData = new FormData();
      formData.append("permintaan_id", permintaanId);
      formData.append("items", JSON.stringify(itemsToApprove));
      formData.append("tanda_tangan_admin", adminSignatureDataUrl);

      try {
        const response = await fetch("api/approve_permintaan.php", {
          method: "POST",
          body: formData,
        });
        const result = await response.json();
        if (result.success) {
          showToast("Permintaan berhasil disetujui!");
          approveModal.classList.add("hidden");
          fetchPermintaan();
        } else {
          showToast(result.message, false);
        }
      } catch (error) {
        showToast("Terjadi kesalahan jaringan.", false);
      }
    });

    approveModal
      .querySelector(".close-approve-modal")
      .addEventListener("click", () => approveModal.classList.add("hidden"));
    document
      .getElementById("clear-admin-signature")
      .addEventListener("click", () => adminSignaturePad.clear());
  }

  if (addModal) {
    openAddModalBtn.addEventListener("click", () => {
      addForm.reset();
      populateItemOptions();
      addModal.classList.remove("hidden");
    });
    addModal
      .querySelector(".close-modal")
      .addEventListener("click", () => addModal.classList.add("hidden"));
    addForm.addEventListener("submit", (e) => {
      e.preventDefault();
      showToast("Fungsi ini hanya untuk demo di halaman admin.", false);
    });
  }

  fetchPermintaan();
});
