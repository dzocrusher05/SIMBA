document.addEventListener("DOMContentLoaded", () => {
  const body = document.body;
  const mobileToggleBtn = document.getElementById("mobile-sidebar-toggle-btn");
  const sidebar = document.getElementById("sidebar");

  if (mobileToggleBtn) {
    mobileToggleBtn.addEventListener("click", () => {
      body.classList.toggle("sidebar-toggled");
    });
  }

  // Logika untuk menutup sidebar saat mengklik di luar area (hanya di mobile)
  document.addEventListener("click", (e) => {
    if (
      window.innerWidth <= 768 &&
      !sidebar.contains(e.target) &&
      !mobileToggleBtn.contains(e.target) &&
      body.classList.contains("sidebar-toggled")
    ) {
      body.classList.remove("sidebar-toggled");
    }
  });
});
