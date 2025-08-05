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
