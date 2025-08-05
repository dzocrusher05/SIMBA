document.addEventListener("DOMContentLoaded", () => {
  const form = document.getElementById("add-user-form");
  const toast = document.getElementById("toast-notification");
  const toastMessage = document.getElementById("toast-message");
  const submitBtn = form.querySelector('button[type="submit"]');

  const showToast = (message, isSuccess = true) => {
    toastMessage.textContent = message;
    toast.className = `fixed bottom-5 right-5 p-4 rounded-lg shadow-lg text-white ${
      isSuccess ? "bg-green-500" : "bg-red-500"
    }`;
    toast.classList.remove("hidden");
    setTimeout(() => toast.classList.add("hidden"), 3000);
  };

  form.addEventListener("submit", async (e) => {
    e.preventDefault();

    submitBtn.disabled = true;
    submitBtn.textContent = "Menambahkan...";

    const formData = new FormData(form);

    try {
      const response = await fetch("api/add_user.php", {
        method: "POST",
        body: formData,
      });

      const result = await response.json();

      if (result.success) {
        showToast(result.message);
        form.reset();
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
});
