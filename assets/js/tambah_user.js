document.addEventListener("DOMContentLoaded", () => {
  const usersTableBody = document.getElementById("users-table-body");
  const addUserModal = document.getElementById("add-user-modal");
  const openAddUserModalBtn = document.getElementById("open-add-user-modal");
  const closeAddUserModalBtn = document.getElementById("close-add-user-modal");
  const addUserForm = document.getElementById("add-user-form");
  const submitBtn = document.getElementById("submit-btn");

  const editUserModal = document.getElementById("edit-user-modal");
  const editUserForm = document.getElementById("edit-user-form");
  const closeEditUserModalBtn = document.getElementById(
    "close-edit-user-modal"
  );
  const editSubmitBtn = document.getElementById("edit-submit-btn");

  const deleteUserModal = document.getElementById("delete-user-modal");
  const deleteUsernameSpan = document.getElementById("delete-username");
  const confirmDeleteBtn = document.getElementById("confirm-delete-btn");
  const cancelDeleteBtn = document.getElementById("cancel-delete-btn");
  let userToDeleteId = null;

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

  const fetchUsers = async () => {
    usersTableBody.innerHTML =
      '<tr><td colspan="5" class="text-center py-4">Memuat data...</td></tr>';
    try {
      const response = await fetch("api/get_users.php");
      const result = await response.json();
      if (result.success) {
        renderUsersTable(result.data);
      } else {
        usersTableBody.innerHTML = `<tr><td colspan="5" class="text-center py-4 text-red-500">${result.message}</td></tr>`;
      }
    } catch (error) {
      usersTableBody.innerHTML = `<tr><td colspan="5" class="text-center py-4 text-red-500">Gagal memuat data pengguna.</td></tr>`;
    }
  };

  const renderUsersTable = (users) => {
    usersTableBody.innerHTML = "";
    if (users.length === 0) {
      usersTableBody.innerHTML = `<tr><td colspan="5" class="text-center py-4 text-gray-500">Tidak ada pengguna yang terdaftar.</td></tr>`;
      return;
    }

    users.forEach((user, index) => {
      const row = `
                <tr class="border-b hover:bg-gray-50">
                    <td class="py-3 px-4">${index + 1}</td>
                    <td class="py-3 px-4">${user.username}</td>
                    <td class="py-3 px-4">${user.peran}</td>
                    <td class="py-3 px-4">${user.notifikasi_peran}</td>
                    <td class="py-3 px-4 text-center">
                        <button data-id="${user.id}" data-username="${
        user.username
      }" data-peran="${user.peran}" data-notifikasi="${
        user.notifikasi_peran
      }" class="edit-btn text-blue-500 p-1">Edit</button>
                        <button data-id="${user.id}" data-username="${
        user.username
      }" class="delete-btn text-red-500 p-1">Hapus</button>
                    </td>
                </tr>
            `;
      usersTableBody.innerHTML += row;
    });
  };

  openAddUserModalBtn.addEventListener("click", () => {
    addUserForm.reset();
    addUserModal.classList.remove("hidden");
  });

  closeAddUserModalBtn.addEventListener("click", () => {
    addUserModal.classList.add("hidden");
  });

  closeEditUserModalBtn.addEventListener("click", () => {
    editUserModal.classList.add("hidden");
  });

  cancelDeleteBtn.addEventListener("click", () => {
    deleteUserModal.classList.add("hidden");
  });

  addUserForm.addEventListener("submit", async (e) => {
    e.preventDefault();

    const formData = new FormData(addUserForm);
    submitBtn.disabled = true;
    submitBtn.textContent = "Menyimpan...";

    try {
      const response = await fetch("api/add_user.php", {
        method: "POST",
        body: formData,
      });

      const result = await response.json();

      if (result.success) {
        showToast(result.message);
        addUserForm.reset();
        addUserModal.classList.add("hidden");
        fetchUsers();
      } else {
        showToast(result.message, false);
      }
    } catch (error) {
      showToast("Terjadi kesalahan jaringan.", false);
    } finally {
      submitBtn.disabled = false;
      submitBtn.textContent = "Tambahkan Pengguna";
    }
  });

  editUserForm.addEventListener("submit", async (e) => {
    e.preventDefault();

    const formData = new FormData(editUserForm);
    editSubmitBtn.disabled = true;
    editSubmitBtn.textContent = "Menyimpan...";

    try {
      const response = await fetch("api/update_user_role.php", {
        method: "POST",
        body: formData,
      });

      const result = await response.json();

      if (result.success) {
        showToast("Pengguna berhasil diperbarui!");
        editUserModal.classList.add("hidden");
        fetchUsers();
      } else {
        showToast(result.message, false);
      }
    } catch (error) {
      showToast("Terjadi kesalahan jaringan.", false);
    } finally {
      editSubmitBtn.disabled = false;
      editSubmitBtn.textContent = "Simpan Perubahan";
    }
  });

  usersTableBody.addEventListener("click", (e) => {
    const target = e.target;
    if (target.classList.contains("edit-btn")) {
      const id = target.dataset.id;
      const username = target.dataset.username;
      const peran = target.dataset.peran;
      const notifikasi = target.dataset.notifikasi;

      document.getElementById("edit_user_id").value = id;
      document.getElementById("edit_username").value = username;
      document.getElementById("edit_peran").value = peran;
      document.getElementById("edit_notifikasi_peran").value = notifikasi;

      editUserModal.classList.remove("hidden");
    } else if (target.classList.contains("delete-btn")) {
      userToDeleteId = target.dataset.id;
      deleteUsernameSpan.textContent = target.dataset.username;
      deleteUserModal.classList.remove("hidden");
    }
  });

  confirmDeleteBtn.addEventListener("click", async () => {
    if (userToDeleteId) {
      const formData = new FormData();
      formData.append("id", userToDeleteId);

      try {
        const response = await fetch("api/delete_user.php", {
          method: "POST",
          body: formData,
        });
        const result = await response.json();
        if (result.success) {
          showToast("Pengguna berhasil dihapus!");
          deleteUserModal.classList.add("hidden");
          fetchUsers();
        } else {
          showToast(result.message, false);
        }
      } catch (error) {
        showToast("Terjadi kesalahan jaringan.", false);
      }
    }
  });

  fetchUsers();
});
