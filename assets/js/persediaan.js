document.addEventListener("DOMContentLoaded", () => {
  let currentPage = 1,
    currentSortBy = "id",
    currentSortOrder = "DESC",
    itemToDeleteId = null,
    currentEditId = null;
  const searchInput = document.getElementById("search-input"),
    tableBody = document.getElementById("table-body"),
    paginationContainer = document.getElementById("pagination-container");
  const formModal = document.getElementById("form-modal"),
    persediaanForm = document.getElementById("persediaan-form"),
    modalTitle = document.getElementById("modal-title");
  const deleteModal = document.getElementById("delete-confirm-modal"),
    toast = document.getElementById("toast-notification"),
    toastMessage = document.getElementById("toast-message");

  // Elemen baru untuk Input Barang Masuk
  const inputMasukModal = document.getElementById("input-masuk-modal");
  const inputMasukForm = document.getElementById("input-masuk-form");
  const inputMasukNamaItem = document.getElementById("input_masuk_nama_item");

  // Elemen baru untuk Riwayat Stok
  const riwayatStokModal = document.getElementById("riwayat-stok-modal");
  const riwayatStokTableBody = document.getElementById(
    "riwayat-stok-table-body"
  );
  const riwayatStokTitle = document.getElementById("riwayat-stok-title");

  const { jsPDF } = window.jspdf;

  const fetchPersediaan = async () => {
    try {
      const response = await fetch(
        `api/get_persediaan.php?page=${currentPage}&search=${searchInput.value}&sort_by=${currentSortBy}&sort_order=${currentSortOrder}`
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
      tableBody.innerHTML = `<tr><td colspan="5" class="text-center py-4 text-gray-500">Tidak ada data.</td></tr>`;
      return;
    }
    data.forEach((item, index) => {
      tableBody.innerHTML += `<tr class="border-b hover:bg-gray-50">
                <td class="py-3 px-4">${(currentPage - 1) * 10 + index + 1}</td>
                <td class="py-3 px-4">${item.nama_persediaan}</td>
                <td class="py-3 px-4">${item.stok}</td><td class="py-3 px-4">${
        item.satuan
      }</td>
                <td class="py-3 px-4 text-center">
                    <button data-id="${item.id}" data-nama="${
        item.nama_persediaan
      }" class="input-masuk-btn text-green-500 p-1">Masuk</button>
                    <button data-id="${item.id}" data-nama="${
        item.nama_persediaan
      }" class="riwayat-btn text-purple-500 p-1">Riwayat</button>
                    <button data-id="${
                      item.id
                    }" class="edit-btn text-blue-500 p-1">Edit</button>
                    <button data-id="${
                      item.id
                    }" class="delete-btn text-red-500 p-1">Hapus</button>
                </td></tr>`;
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
        fetchPersediaan();
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

  const openFormModal = (id = null) => {
    persediaanForm.reset();
    currentEditId = id;
    if (id) {
      modalTitle.textContent = "Edit Item";
      fetch(`api/get_item_persediaan.php?id=${id}`)
        .then((res) => res.json())
        .then((result) => {
          if (result.success) {
            document.getElementById("edit_id").value = result.data.id;
            document.getElementById("nama_persediaan").value =
              result.data.nama_persediaan;
            document.getElementById("stok").value = result.data.stok;
            document.getElementById("satuan").value = result.data.satuan;
          }
        });
    } else {
      modalTitle.textContent = "Tambah Item Baru";
    }
    formModal.classList.remove("hidden");
  };

  document
    .getElementById("open-add-modal")
    .addEventListener("click", () => openFormModal());
  document
    .getElementById("close-modal-btn")
    .addEventListener("click", () => formModal.classList.add("hidden"));

  persediaanForm.addEventListener("submit", async (e) => {
    e.preventDefault();
    const url = currentEditId
      ? "api/update_persediaan.php"
      : "api/add_persediaan.php";
    const formData = new FormData(persediaanForm);
    if (currentEditId) formData.set("edit_id", currentEditId);

    const response = await fetch(url, { method: "POST", body: formData });
    const result = await response.json();
    if (result.success) {
      formModal.classList.add("hidden");
      fetchPersediaan();
      showToast(
        currentEditId
          ? "Item berhasil diperbarui!"
          : "Item berhasil ditambahkan!"
      );
    } else {
      showToast(result.message, false);
    }
  });

  tableBody.addEventListener("click", (e) => {
    if (e.target.classList.contains("edit-btn"))
      openFormModal(e.target.dataset.id);
    if (e.target.classList.contains("delete-btn")) {
      itemToDeleteId = e.target.dataset.id;
      deleteModal.classList.remove("hidden");
    }
    if (e.target.classList.contains("input-masuk-btn")) {
      const id = e.target.dataset.id;
      const nama = e.target.dataset.nama;
      document.getElementById("input_masuk_persediaan_id").value = id;
      inputMasukNamaItem.textContent = `Input untuk: ${nama}`;
      inputMasukModal.classList.remove("hidden");
    }
    if (e.target.classList.contains("riwayat-btn")) {
      const id = e.target.dataset.id;
      const nama = e.target.dataset.nama;
      riwayatStokTitle.textContent = `Riwayat Stok: ${nama}`;
      riwayatStokModal.dataset.id = id;
      fetchRiwayatPersediaan(id);
    }
  });

  const fetchRiwayatPersediaan = async (id) => {
    riwayatStokTableBody.innerHTML = `<tr><td colspan="5" class="text-center py-4">Memuat data...</td></tr>`;
    riwayatStokModal.classList.remove("hidden");
    try {
      const response = await fetch(`api/get_riwayat_persediaan.php?id=${id}`);
      const result = await response.json();
      if (result.success) {
        renderRiwayatTable(result.data);
      } else {
        riwayatStokTableBody.innerHTML = `<tr><td colspan="5" class="text-center py-4 text-red-500">${result.message}</td></tr>`;
      }
    } catch (error) {
      riwayatStokTableBody.innerHTML = `<tr><td colspan="5" class="text-center py-4 text-red-500">Gagal memuat data riwayat.</td></tr>`;
    }
  };

  const renderRiwayatTable = (data) => {
    riwayatStokTableBody.innerHTML = "";
    if (data.length === 0) {
      riwayatStokTableBody.innerHTML = `<tr><td colspan="5" class="text-center py-4 text-gray-500">Tidak ada riwayat untuk item ini.</td></tr>`;
      return;
    }
    data.forEach((item) => {
      const jenis =
        item.jenis_transaksi === "masuk"
          ? `<span class="bg-green-100 text-green-800 px-2 py-1 rounded-full text-xs">Masuk</span>`
          : `<span class="bg-red-100 text-red-800 px-2 py-1 rounded-full text-xs">Keluar</span>`;
      riwayatStokTableBody.innerHTML += `
              <tr class="border-b">
                  <td class="py-2 px-4">${item.tanggal}</td>
                  <td class="py-2 px-4">${jenis}</td>
                  <td class="py-2 px-4">${item.jumlah}</td>
                  <td class="py-2 px-4">${item.keterangan || "-"}</td>
                  <td class="py-2 px-4">${item.nomor_dokumen || "-"}</td>
              </tr>
          `;
    });
  };

  const generateRiwayatPersediaanPDF = (riwayatData) => {
    const doc = new jsPDF();
    const data = riwayatData.data;
    const title = riwayatStokTitle.textContent;

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
      item.jumlah,
      item.keterangan || "-",
      item.nomor_dokumen || "-",
    ]);

    doc.autoTable({
      head: [
        ["Tanggal", "Jenis Transaksi", "Jumlah", "Keterangan", "Nomor Dokumen"],
      ],
      body: tableBody,
      startY: 35,
      theme: "grid",
      headStyles: { fillColor: [44, 62, 80] },
    });

    doc.save(`${title.replace(/ /g, "_")}.pdf`);
  };

  document
    .getElementById("close-input-masuk-modal")
    .addEventListener("click", () => inputMasukModal.classList.add("hidden"));
  document
    .getElementById("close-riwayat-stok-modal")
    .addEventListener("click", () => riwayatStokModal.classList.add("hidden"));
  document
    .getElementById("print-riwayat-stok-btn")
    .addEventListener("click", async () => {
      const id = riwayatStokModal.dataset.id;
      try {
        const response = await fetch(`api/get_riwayat_persediaan.php?id=${id}`);
        const result = await response.json();
        if (result.success) {
          generateRiwayatPersediaanPDF(result);
        } else {
          showToast(result.message, false);
        }
      } catch (error) {
        showToast("Gagal mengambil data untuk dicetak.", false);
      }
    });

  inputMasukForm.addEventListener("submit", async (e) => {
    e.preventDefault();
    const formData = new FormData(inputMasukForm);
    formData.append("jenis_transaksi", "masuk");
    const response = await fetch("api/add_riwayat_persediaan.php", {
      method: "POST",
      body: formData,
    });
    const result = await response.json();
    if (result.success) {
      inputMasukModal.classList.add("hidden");
      inputMasukForm.reset();
      fetchPersediaan();
      showToast("Barang masuk berhasil dicatat!");
    } else {
      showToast(result.message, false);
    }
  });

  document
    .getElementById("cancel-delete-btn")
    .addEventListener("click", () => deleteModal.classList.add("hidden"));
  document
    .getElementById("confirm-delete-btn")
    .addEventListener("click", async () => {
      const formData = new FormData();
      formData.append("id", itemToDeleteId);
      const response = await fetch("api/delete_persediaan.php", {
        method: "POST",
        body: formData,
      });
      const result = await response.json();
      if (result.success) {
        fetchPersediaan();
        showToast("Item berhasil dihapus!");
      } else {
        showToast(result.message, false);
      }
      deleteModal.classList.add("hidden");
    });

  searchInput.addEventListener("input", () => {
    currentPage = 1;
    fetchPersediaan();
  });
  document.querySelectorAll(".sortable").forEach((h) =>
    h.addEventListener("click", () => {
      if (currentSortBy === h.dataset.sort)
        currentSortOrder = currentSortOrder === "ASC" ? "DESC" : "ASC";
      else {
        currentSortBy = h.dataset.sort;
        currentSortOrder = "ASC";
      }
      fetchPersediaan();
    })
  );
  fetchPersediaan();
});
