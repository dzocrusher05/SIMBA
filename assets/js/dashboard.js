document.addEventListener("DOMContentLoaded", async () => {
  // Fungsi untuk membuat status badge
  const createStatusBadge = (status) => {
    let statusClass = "";
    switch (status) {
      case "Diajukan":
        statusClass = "bg-yellow-100 text-yellow-800";
        break;
      case "Disetujui":
        statusClass = "bg-blue-100 text-blue-800";
        break;
      case "Dikembalikan":
        statusClass = "bg-gray-100 text-gray-800";
        break;
      case "Ditolak":
        statusClass = "bg-red-100 text-red-800";
        break;
      default:
        statusClass = "bg-gray-100 text-gray-800";
    }
    return `<span class="px-2 py-1 text-xs font-medium rounded-full whitespace-nowrap ${statusClass}">${status}</span>`;
  };

  // Fungsi untuk render riwayat peminjaman
  const renderLoanHistory = (data) => {
    const container = document.getElementById("riwayat-peminjaman-list");
    container.innerHTML = "";
    if (!data || data.length === 0) {
      container.innerHTML =
        '<p class="text-sm text-gray-500">Belum ada riwayat peminjaman.</p>';
      return;
    }
    data.forEach((item) => {
      container.innerHTML += `
                <div class="grid grid-cols-[1fr_auto] items-center gap-4">
                    <div class="min-w-0">
                        <p class="font-semibold text-sm truncate">${
                          item.nama_peminjam
                        }</p>
                        <div class="text-xs text-gray-500 marquee-container">
                            <p class="js-marquee-check" title="${
                              item.daftar_aset
                            }">${item.daftar_aset}</p>
                        </div>
                    </div>
                    ${createStatusBadge(item.status_peminjaman)}
                </div>
            `;
    });
  };

  // Fungsi untuk render riwayat permintaan
  const renderRequestHistory = (data) => {
    const container = document.getElementById("riwayat-permintaan-list");
    container.innerHTML = "";
    if (!data || data.length === 0) {
      container.innerHTML =
        '<p class="text-sm text-gray-500">Belum ada riwayat permintaan.</p>';
      return;
    }
    data.forEach((item) => {
      container.innerHTML += `
                <div class="grid grid-cols-[1fr_auto] items-center gap-4">
                    <div class="min-w-0">
                        <p class="font-semibold text-sm truncate">${
                          item.nama_pemohon
                        }</p>
                        <div class="text-xs text-gray-500 marquee-container">
                             <p class="js-marquee-check" title="${
                               item.daftar_item
                             }">${item.daftar_item}</p>
                        </div>
                    </div>
                    ${createStatusBadge(item.status_permintaan)}
                </div>
            `;
    });
  };

  // Fungsi untuk mengaktifkan animasi jika teks panjang
  const initMarquee = () => {
    const elementsToCheck = document.querySelectorAll(".js-marquee-check");
    elementsToCheck.forEach((el) => {
      if (el.scrollWidth > el.clientWidth) {
        el.classList.add("marquee-text");
      }
    });
  };

  try {
    const response = await fetch("api/get_dashboard_stats.php");
    const result = await response.json();

    if (result.success) {
      const stats = result.data;
      document.getElementById(
        "last-updated"
      ).textContent = `Diperbarui pada: ${new Date().toLocaleTimeString(
        "id-ID"
      )}`;
      document.getElementById("total-aset").textContent = stats.total_aset;
      document.getElementById("total-persediaan").textContent =
        stats.total_persediaan;
      document.getElementById("peminjaman-pending").textContent =
        stats.peminjaman_pending;
      document.getElementById("permintaan-pending").textContent =
        stats.permintaan_pending;

      renderLoanHistory(stats.riwayat_peminjaman);
      renderRequestHistory(stats.riwayat_permintaan);

      initMarquee();

      const ctx = document.getElementById("asetStatusChart").getContext("2d");
      new Chart(ctx, {
        type: "doughnut",
        data: {
          labels: ["Tersedia", "Dipinjam"],
          datasets: [
            {
              label: "Status Aset",
              data: [stats.aset_tersedia, stats.aset_dipinjam],
              backgroundColor: [
                "rgba(22, 163, 74, 0.7)",
                "rgba(234, 179, 8, 0.7)",
              ],
              borderColor: ["rgba(22, 163, 74, 1)", "rgba(234, 179, 8, 1)"],
              borderWidth: 1,
            },
          ],
        },
        options: {
          responsive: true,
          plugins: {
            legend: {
              position: "top",
            },
          },
        },
      });
    } else {
      console.error("Gagal memuat statistik:", result.message);
    }
  } catch (error) {
    console.error("Error:", error);
  }
});
