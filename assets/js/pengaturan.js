document.addEventListener("DOMContentLoaded", () => {
  const form = document.getElementById("pengaturan-form");
  const toast = document.getElementById("toast-notification");
  const toastMessage = document.getElementById("toast-message");

  const loadPengaturan = async () => {
    const response = await fetch("api/get_pengaturan.php");
    const result = await response.json();
    if (result.success) {
      document.getElementById("nomor_terakhir_spb").value =
        result.data.nomor_terakhir_spb;
      document.getElementById("nomor_terakhir_sbbk").value =
        result.data.nomor_terakhir_sbbk;
    }
  };

  const showToast = (message) => {
    toastMessage.textContent = message;
    toast.className =
      "fixed bottom-5 right-5 p-4 rounded-lg shadow-lg text-white bg-green-500";
    toast.classList.remove("hidden");
    setTimeout(() => toast.classList.add("hidden"), 3000);
  };

  form.addEventListener("submit", async (e) => {
    e.preventDefault();
    const formData = new FormData();
    const data = {
      nomor_terakhir_spb: document.getElementById("nomor_terakhir_spb").value,
      nomor_terakhir_sbbk: document.getElementById("nomor_terakhir_sbbk").value,
    };
    formData.append("pengaturan", JSON.stringify(data)); // Mengirim sebagai JSON tidak ideal untuk PHP $_POST

    // Mengirim data dengan cara yang lebih kompatibel dengan PHP
    const postData = new URLSearchParams();
    postData.append("pengaturan[nomor_terakhir_spb]", data.nomor_terakhir_spb);
    postData.append(
      "pengaturan[nomor_terakhir_sbbk]",
      data.nomor_terakhir_sbbk
    );

    const response = await fetch("api/update_pengaturan.php", {
      method: "POST",
      body: postData,
    });
    const result = await response.json();
    if (result.success) {
      showToast("Pengaturan berhasil disimpan!");
    }
  });

  loadPengaturan();
});
