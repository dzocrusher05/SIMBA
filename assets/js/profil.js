document.addEventListener("DOMContentLoaded", async () => {
  const profileInfo = document.getElementById("profile-info");
  const updateProfileForm = document.getElementById("update-profile-form");
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
    profileInfo.innerHTML = `<p class="text-gray-500 text-center">Memuat data...</p>`;
    try {
      const response = await fetch("api/get_profile.php");
      const result = await response.json();

      if (result.success) {
        const user = result.data;
        profileInfo.innerHTML = `
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <label for="user_id" class="block text-sm font-medium text-gray-700">ID Pengguna</label>
                            <input type="text" id="user_id" name="user_id" value="${
                              user.id || ""
                            }" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 bg-gray-100 cursor-not-allowed" readonly>
                        </div>
                        <div>
                            <label for="username" class="block text-sm font-medium text-gray-700">Username</label>
                            <input type="text" id="username" name="username" value="${
                              user.username || ""
                            }" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        </div>
                    </div>
                    <div class="mb-6">
                        <label for="peran" class="block text-sm font-medium text-gray-700">Peran</label>
                        <input type="text" id="peran" name="peran" value="${
                          user.peran || ""
                        }" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 bg-gray-100 cursor-not-allowed" readonly>
                    </div>
                    <div class="mb-6">
                        <label for="nama_lengkap" class="block text-sm font-medium text-gray-700">Nama Lengkap</label>
                        <input type="text" id="nama_lengkap" name="nama_lengkap" value="${
                          user.nama_lengkap || ""
                        }" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div class="mb-6">
                        <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                        <input type="email" id="email" name="email" value="${
                          user.email || ""
                        }" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div class="mb-6">
                        <label for="no_telepon" class="block text-sm font-medium text-gray-700">No. Telepon</label>
                        <input type="text" id="no_telepon" name="no_telepon" value="${
                          user.no_telepon || ""
                        }" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div class="mb-6">
                        <label for="alamat" class="block text-sm font-medium text-gray-700">Alamat</label>
                        <textarea id="alamat" name="alamat" rows="3" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">${
                          user.alamat || ""
                        }</textarea>
                    </div>
                `;
      } else {
        profileInfo.innerHTML = `<p class="text-center text-red-500">${result.message}</p>`;
        showToast(result.message, false);
      }
    } catch (error) {
      profileInfo.innerHTML = `<p class="text-center text-red-500">Gagal memuat data profil.</p>`;
      showToast("Terjadi kesalahan jaringan.", false);
    }
  };

  updateProfileForm.addEventListener("submit", async (e) => {
    e.preventDefault();
    const formData = new FormData(updateProfileForm);
    const submitBtn = updateProfileForm.querySelector('button[type="submit"]');
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

  fetchProfileData();
});
