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

  // --- FUNGSI ---
  const fetchPeminjaman = async () => {
    try {
      const response = await fetch(
        `api/get_peminjaman.php?page=${currentPage}&search=${searchInput.value}`
      );
      const result = await response.json();
      renderTable(result.data);
      renderPagination(result.pagination);
    } catch (error) {
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
      switch (item.status_peminjaman) {
        case "Diajukan":
          statusClass = "bg-yellow-100 text-yellow-800";
          actions = `<button data-id="${item.id}" data-action="approve" class="action-btn bg-green-500 text-white px-2 py-1 text-xs rounded hover:bg-green-600">Setujui</button>
                               <button data-id="${item.id}" data-action="reject" class="action-btn bg-red-500 text-white px-2 py-1 text-xs rounded hover:bg-red-600 ml-1">Tolak</button>`;
          break;
        case "Disetujui":
          statusClass = "bg-blue-100 text-blue-800";
          actions = `<button data-id="${item.id}" data-action="return" class="action-btn bg-blue-500 text-white px-2 py-1 text-xs rounded hover:bg-blue-600">Kembalikan</button>`;
          break;
        case "Dikembalikan":
          statusClass = "bg-gray-100 text-gray-800";
          break;
        case "Ditolak":
          statusClass = "bg-red-100 text-red-800";
          break;
      }

      const tanggalPengajuanFormatted = item.tanggal_pengajuan
        ? new Date(item.tanggal_pengajuan).toLocaleDateString("id-ID", {
            day: "numeric",
            month: "short",
            year: "numeric",
          })
        : "-";

      tableBody.innerHTML += `
                <tr class="border-b hover:bg-gray-50">
                    <td class="py-3 px-4 font-medium text-gray-800 align-top">${
                      item.daftar_aset || "-"
                    }</td>
                    <td class="py-3 px-4 align-top">
                        <div class="font-bold text-slate-800">${
                          item.nama_peminjam || "-"
                        }</div>
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
                    <td class="py-3 px-4 text-center align-top">${actions}</td>
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
      approve: {
        title: "Setujui Peminjaman",
        message:
          'Anda yakin ingin menyetujui pengajuan ini? Status aset akan berubah menjadi "Dipinjam".',
        btnClass: "bg-green-600 hover:bg-green-700",
      },
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

  // --- EVENT LISTENERS ---
  searchInput.addEventListener("input", () => {
    currentPage = 1;
    fetchPeminjaman();
  });
  tableBody.addEventListener("click", (e) => {
    if (e.target.classList.contains("action-btn")) {
      openConfirmationModal(e.target.dataset.action, e.target.dataset.id);
    }
  });
  cancelBtn.addEventListener("click", () => modal.classList.add("hidden"));
  confirmBtn.addEventListener("click", performAction);

  fetchPeminjaman();
});
