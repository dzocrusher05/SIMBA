document.addEventListener("DOMContentLoaded", () => {
  const profileDetails = document.getElementById("profile-details");
  const editModal = document.getElementById("edit-modal");
  const openEditModalBtn = document.getElementById("open-edit-modal-btn");
  const closeEditModalBtn = document.getElementById("close-edit-modal");
  const editProfileForm = document.getElementById("edit-profile-form");
  const changePasswordForm = document.getElementById("change-password-form");

  let currentProfileData = {};

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

  const fetchProfileData = async () => {
    profileDetails.innerHTML = `<p class="text-gray-500 text-center">Memuat data...</p>`;
    try {
      const response = await fetch("api/get_profile.php");
      const result = await response.json();

      if (result.success) {
        currentProfileData = result.data;
        renderProfileDetails(currentProfileData);
      } else {
        profileDetails.innerHTML = `<p class="text-center text-red-500">${result.message}</p>`;
        showToast(result.message, false);
      }
    } catch (error) {
      profileDetails.innerHTML = `<p class="text-center text-red-500">Gagal memuat data profil.</p>`;
      showToast("Terjadi kesalahan jaringan.", false);
    }
  };

  const renderProfileDetails = (user) => {
    profileDetails.innerHTML = `
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <p class="font-medium text-gray-700">Username</p>
                    <p class="text-lg font-semibold">${user.username || "-"}</p>
                </div>
                <div>
                    <p class="font-medium text-gray-700">Peran</p>
                    <p class="text-lg font-semibold">${user.peran || "-"}</p>
                </div>
            </div>
            <hr class="my-4">
            <div class="space-y-3">
                <div>
                    <p class="font-medium text-gray-700">Nama Lengkap</p>
                    <p class="text-lg">${user.nama_lengkap || "-"}</p>
                </div>
                <div>
                    <p class="font-medium text-gray-700">Email</p>
                    <p class="text-lg">${user.email || "-"}</p>
                </div>
                <div>
                    <p class="font-medium text-gray-700">No. Telepon</p>
                    <p class="text-lg">${user.no_telepon || "-"}</p>
                </div>
                <div>
                    <p class="font-medium text-gray-700">Alamat</p>
                    <p class="text-lg">${user.alamat || "-"}</p>
                </div>
            </div>
        `;
  };

  openEditModalBtn.addEventListener("click", () => {
    if (currentProfileData) {
      document.getElementById("edit_user_id").value = currentProfileData.id;
      document.getElementById("edit_username").value =
        currentProfileData.username;
      document.getElementById("edit_nama_lengkap").value =
        currentProfileData.nama_lengkap;
      document.getElementById("edit_email").value = currentProfileData.email;
      document.getElementById("edit_no_telepon").value =
        currentProfileData.no_telepon;
      document.getElementById("edit_alamat").value = currentProfileData.alamat;
      editModal.classList.remove("hidden");
    }
  });

  closeEditModalBtn.addEventListener("click", () => {
    editModal.classList.add("hidden");
  });

  editProfileForm.addEventListener("submit", async (e) => {
    e.preventDefault();
    const formData = new FormData(editProfileForm);
    const submitBtn = editProfileForm.querySelector('button[type="submit"]');
    submitBtn.disabled = true;
    submitBtn.textContent = "Menyimpan...";

    try {
      const response = await fetch("api/update_profile.php", {
        method: "POST",
        body: formData,
      });
      const result = await response.json();

      if (result.success) {
        showToast("Profil berhasil diperbarui!");
        editModal.classList.add("hidden");
      } else {
        showToast(result.message || "Gagal memperbarui profil.", false);
      }
    } catch (error) {
      showToast("Terjadi kesalahan jaringan.", false);
    } finally {
      submitBtn.disabled = false;
      submitBtn.textContent = "Simpan Perubahan";
      fetchProfileData();
    }
  });

  changePasswordForm.addEventListener("submit", async (e) => {
    e.preventDefault();
    const formData = new FormData(changePasswordForm);
    const submitBtn = changePasswordForm.querySelector('button[type="submit"]');
    submitBtn.disabled = true;
    submitBtn.textContent = "Menyimpan...";

    try {
      const response = await fetch("api/change_password.php", {
        method: "POST",
        body: formData,
      });
      const result = await response.json();

      if (result.success) {
        showToast("Kata sandi berhasil diperbarui!");
        changePasswordForm.reset();
      } else {
        showToast(result.message || "Gagal memperbarui kata sandi.", false);
      }
    } catch (error) {
      showToast("Terjadi kesalahan jaringan.", false);
    } finally {
      submitBtn.disabled = false;
      submitBtn.textContent = "Ubah Kata Sandi";
    }
  });

  fetchProfileData();
});
