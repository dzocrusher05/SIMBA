document.addEventListener("DOMContentLoaded", () => {
  // --- Variabel Global & State ---
  let currentPage = 1;
  let currentSortBy = "id";
  let currentSortOrder = "DESC";
  const searchInput = document.getElementById("search-aset");
  const tableBody = document.getElementById("aset-table-body");
  const paginationContainer = document.getElementById("pagination-container");

  // --- Elemen Modal & Toast ---
  const addModal = document.getElementById("add-aset-modal");
  const addForm = document.getElementById("add-aset-form");
  const openModalButton = document.getElementById("open-add-modal");

  const editModal = document.getElementById("edit-aset-modal");
  const editForm = document.getElementById("edit-aset-form");

  const deleteModal = document.getElementById("delete-confirm-modal");
  let assetToDeleteId = null;

  const toast = document.getElementById("toast-notification");
  const toastMessage = document.getElementById("toast-message");

  const riwayatAsetModal = document.getElementById("riwayat-aset-modal");
  const riwayatAsetTableBody = document.getElementById(
    "riwayat-aset-table-body"
  );
  const riwayatAsetTitle = document.getElementById("riwayat-aset-title");

  const { jsPDF } = window.jspdf;

  // --- Fungsi Utama untuk Fetch Data ---
  const fetchAsets = async () => {
    const searchQuery = searchInput.value;
    const url = `api/get_asets.php?page=${currentPage}&search=${searchQuery}&sort_by=${currentSortBy}&sort_order=${currentSortOrder}`;

    try {
      const response = await fetch(url);
      const result = await response.json();
      renderTable(result.data);
      renderPagination(result.pagination);
    } catch (error) {
      console.error("Error fetching data:", error);
      tableBody.innerHTML = `<tr><td colspan="5" class="text-center py-4">Gagal memuat data.</td></tr>`;
    }
  };

  // --- Fungsi untuk Merender Tabel ---
  const renderTable = (data) => {
    tableBody.innerHTML = ""; // Kosongkan tabel
    if (data.length === 0) {
      tableBody.innerHTML = `<tr><td colspan="5" class="text-center py-4 text-gray-500">Tidak ada data ditemukan.</td></tr>`;
      return;
    }

    data.forEach((aset, index) => {
      const statusBadge =
        aset.status === "Tersedia"
          ? `<span class="bg-green-100 text-green-800 text-xs font-medium mr-2 px-2.5 py-0.5 rounded-full">Tersedia</span>`
          : `<span class="bg-yellow-100 text-yellow-800 text-xs font-medium mr-2 px-2.5 py-0.5 rounded-full">Dipinjam</span>`;

      const row = `
                <tr class="border-b border-gray-200 hover:bg-gray-50">
                    <td class="py-3 px-4">${
                      (currentPage - 1) * 10 + index + 1
                    }</td>
                    <td class="py-3 px-4">${aset.no_bmn}</td>
                    <td class="py-3 px-4">${aset.nama_bmn}</td>
                    <td class="py-3 px-4">${statusBadge}</td>
                    <td class="py-3 px-4 text-center">
                        <button data-id="${aset.id}" data-nama="${
        aset.nama_bmn
      }" class="riwayat-btn text-purple-500 p-1">Riwayat</button>
                        <button data-id="${
                          aset.id
                        }" class="edit-btn text-blue-500 hover:text-blue-700 p-1">Edit</button>
                        <button data-id="${
                          aset.id
                        }" class="delete-btn text-red-500 hover:text-red-700 p-1">Hapus</button>
                    </td>
                </tr>
            `;
      tableBody.innerHTML += row;
    });
  };

  // --- Fungsi untuk Merender Paginasi ---
  const renderPagination = (pagination) => {
    paginationContainer.innerHTML = "";
    if (pagination.total_pages <= 1) return;

    for (let i = 1; i <= pagination.total_pages; i++) {
      const pageButton = document.createElement("button");
      pageButton.textContent = i;
      pageButton.className = `px-3 py-1 mx-1 rounded-md text-sm ${
        i === pagination.current_page
          ? "bg-blue-600 text-white"
          : "bg-white text-gray-700 hover:bg-gray-100 border"
      }`;
      pageButton.addEventListener("click", () => {
        currentPage = i;
        fetchAsets();
      });
      paginationContainer.appendChild(pageButton);
    }
  };

  const fetchRiwayatAset = async (id) => {
    riwayatAsetTableBody.innerHTML = `<tr><td colspan="4" class="text-center py-4">Memuat data...</td></tr>`;
    riwayatAsetModal.classList.remove("hidden");
    try {
      const response = await fetch(`api/get_riwayat_aset.php?id=${id}`);
      const result = await response.json();
      if (result.success) {
        renderRiwayatAsetTable(result.data);
      } else {
        riwayatAsetTableBody.innerHTML = `<tr><td colspan="4" class="text-center py-4 text-red-500">${result.message}</td></tr>`;
      }
    } catch (error) {
      riwayatAsetTableBody.innerHTML = `<tr><td colspan="4" class="text-center py-4 text-red-500">Gagal memuat data riwayat.</td></tr>`;
    }
  };

  const renderRiwayatAsetTable = (data) => {
    riwayatAsetTableBody.innerHTML = "";
    if (data.length === 0) {
      riwayatAsetTableBody.innerHTML = `<tr><td colspan="4" class="text-center py-4 text-gray-500">Tidak ada riwayat pergerakan untuk aset ini.</td></tr>`;
      return;
    }
    data.forEach((item) => {
      const jenis =
        item.jenis_transaksi === "peminjaman"
          ? `<span class="bg-blue-100 text-blue-800 px-2 py-1 rounded-full text-xs">Peminjaman</span>`
          : `<span class="bg-gray-100 text-gray-800 px-2 py-1 rounded-full text-xs">Pengembalian</span>`;
      riwayatAsetTableBody.innerHTML += `
            <tr class="border-b">
                <td class="py-2 px-4">${item.tanggal}</td>
                <td class="py-2 px-4">${jenis}</td>
                <td class="py-2 px-4">${item.keterangan || "-"}</td>
                <td class="py-2 px-4">${item.nomor_dokumen || "-"}</td>
            </tr>
        `;
    });
  };

  // --- Fungsi untuk Toast Notification ---
  const showToast = (message, isSuccess = true) => {
    toastMessage.textContent = message;
    toast.className = `fixed bottom-5 right-5 p-4 rounded-lg shadow-lg text-white ${
      isSuccess ? "bg-green-500" : "bg-red-500"
    }`;
    toast.classList.remove("hidden");
    setTimeout(() => {
      toast.classList.add("hidden");
    }, 3000);
  };

  const generateRiwayatAsetPDF = (riwayatData) => {
    const doc = new jsPDF();
    const data = riwayatData.data;
    const title = riwayatAsetTitle.textContent;

    doc.setFontSize(18);
    doc.setFont("helvetica", "bold");
    doc.text(title, 105, 20, { align: "center" });
    doc.setLineWidth(0.5);
    doc.line(15, 25, 195, 25);

    doc.setFontSize(10);
    doc.setFont("helvetica", "normal");

    const tableBody = data.map((item, index) => [
      item.tanggal,
      item.jenis_transaksi,
      item.keterangan || "-",
      item.nomor_dokumen || "-",
    ]);

    doc.autoTable({
      head: [["Tanggal", "Jenis Transaksi", "Keterangan", "Nomor Dokumen"]],
      body: tableBody,
      startY: 35,
      theme: "grid",
      headStyles: { fillColor: [44, 62, 80] },
    });

    doc.save(`${title.replace(/ /g, "_")}.pdf`);
  };

  // --- Event Listeners ---
  searchInput.addEventListener("input", () => {
    currentPage = 1;
    fetchAsets();
  });

  document
    .getElementById("close-riwayat-aset-modal")
    .addEventListener("click", () => {
      riwayatAsetModal.classList.add("hidden");
    });

  document
    .getElementById("print-riwayat-aset-btn")
    .addEventListener("click", async () => {
      const id = document.getElementById("riwayat-aset-modal").dataset.id;
      try {
        const response = await fetch(`api/get_riwayat_aset.php?id=${id}`);
        const result = await response.json();
        if (result.success) {
          generateRiwayatAsetPDF(result);
        } else {
          showToast(result.message, false);
        }
      } catch (error) {
        showToast("Gagal mengambil data untuk dicetak.", false);
      }
    });

  document.querySelectorAll(".sortable").forEach((header) => {
    header.addEventListener("click", () => {
      const sortBy = header.dataset.sort;
      if (currentSortBy === sortBy) {
        currentSortOrder = currentSortOrder === "ASC" ? "DESC" : "ASC";
      } else {
        currentSortBy = sortBy;
        currentSortOrder = "ASC";
      }
      fetchAsets();
    });
  });

  // --- Event Listener untuk Modal Tambah ---
  openModalButton.addEventListener("click", () =>
    addModal.classList.remove("hidden")
  );
  addModal
    .querySelector(".close-modal")
    .addEventListener("click", () => addModal.classList.add("hidden"));
  addForm.addEventListener("submit", async (e) => {
    e.preventDefault();
    const formData = new FormData(addForm);
    try {
      const response = await fetch("api/add_aset.php", {
        method: "POST",
        body: formData,
      });
      const result = await response.json();
      if (result.success) {
        addModal.classList.add("hidden");
        addForm.reset();
        fetchAsets();
        showToast("Aset berhasil ditambahkan!");
      } else {
        showToast(result.message || "Gagal menambahkan aset.", false);
      }
    } catch (error) {
      showToast("Terjadi kesalahan jaringan.", false);
    }
  });

  // --- Event Listener Dinamis untuk Tombol Aksi di Tabel ---
  tableBody.addEventListener("click", async (e) => {
    const target = e.target;
    if (target.classList.contains("edit-btn")) {
      const id = target.dataset.id;
      try {
        const response = await fetch(`api/get_aset.php?id=${id}`);
        const result = await response.json();
        if (result.success) {
          document.getElementById("edit_aset_id").value = result.data.id;
          document.getElementById("edit_no_bmn").value = result.data.no_bmn;
          document.getElementById("edit_nama_bmn").value = result.data.nama_bmn;
          document.getElementById("edit_status").value = result.data.status;
          editModal.classList.remove("hidden");
        } else {
          showToast(result.message, false);
        }
      } catch (error) {
        showToast("Gagal mengambil data untuk diedit.", false);
      }
    }
    if (target.classList.contains("delete-btn")) {
      assetToDeleteId = target.dataset.id;
      deleteModal.classList.remove("hidden");
    }
    if (e.target.classList.contains("riwayat-btn")) {
      const id = e.target.dataset.id;
      const nama = e.target.dataset.nama;
      riwayatAsetTitle.textContent = `Riwayat Aset: ${nama}`;
      riwayatAsetModal.dataset.id = id;
      fetchRiwayatAset(id);
    }
  });

  // --- Event Listener untuk Form Edit ---
  editForm.addEventListener("submit", async (e) => {
    e.preventDefault();
    const formData = new FormData(editForm);
    try {
      const response = await fetch("api/update_aset.php", {
        method: "POST",
        body: formData,
      });
      const result = await response.json();
      if (result.success) {
        editModal.classList.add("hidden");
        fetchAsets();
        showToast("Aset berhasil diperbarui!");
      } else {
        showToast(result.message, false);
      }
    } catch (error) {
      showToast("Terjadi kesalahan jaringan.", false);
    }
  });

  // --- Event Listener untuk Modal Hapus ---
  document.getElementById("cancel-delete-btn").addEventListener("click", () => {
    deleteModal.classList.add("hidden");
    assetToDeleteId = null;
  });

  document
    .getElementById("confirm-delete-btn")
    .addEventListener("click", async () => {
      if (assetToDeleteId) {
        const formData = new FormData();
        formData.append("id", assetToDeleteId);
        try {
          const response = await fetch("api/delete_aset.php", {
            method: "POST",
            body: formData,
          });
          const result = await response.json();
          if (result.success) {
            fetchAsets();
            showToast("Aset berhasil dihapus!");
          } else {
            showToast(result.message, false);
          }
        } catch (error) {
          showToast("Terjadi kesalahan jaringan.", false);
        } finally {
          deleteModal.classList.add("hidden");
          assetToDeleteId = null;
        }
      }
    });

  // Event listener untuk menutup semua modal jika diklik di luar area
  window.addEventListener("click", (event) => {
    if (event.target === addModal) addModal.classList.add("hidden");
    if (event.target === editModal) editModal.classList.add("hidden");
    if (event.target === deleteModal) deleteModal.classList.add("hidden");
    if (event.target === riwayatAsetModal)
      riwayatAsetModal.classList.add("hidden");
  });
  editModal
    .querySelector(".close-modal")
    .addEventListener("click", () => editModal.classList.add("hidden"));

  // --- Panggil data pertama kali saat halaman dimuat ---
  fetchAsets();
});
