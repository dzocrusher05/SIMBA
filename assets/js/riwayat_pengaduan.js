document.addEventListener("DOMContentLoaded", () => {
  let searchInput = document.getElementById("search-input");
  let tableBody = document.getElementById("table-body");
  const detailModal = document.getElementById("detail-modal");
  const closeDetailModalBtn = document.getElementById("close-detail-modal");
  const detailPengaduanContent = document.getElementById(
    "detail-pengaduan-content"
  );
  const prosesPengaduanForm = document.getElementById("proses-pengaduan-form");
  const prosesPengaduanId = document.getElementById("proses_pengaduan_id");
  const deskripsiPekerjaanInput = document.getElementById(
    "deskripsi_pekerjaan"
  );
  const prakiraanSelesaiInput = document.getElementById("prakiraan_selesai");
  const statusLaporanSelect = document.getElementById("status_laporan");

  const fetchPengaduan = async () => {
    tableBody.innerHTML =
      '<tr><td colspan="5" class="text-center py-4">Memuat data...</td></tr>';
    try {
      const response = await fetch(
        `api/get_pengaduan.php?search=${searchInput.value}`
      );
      const result = await response.json();
      if (result.success) {
        renderTable(result.data);
      } else {
        tableBody.innerHTML = `<tr><td colspan="5" class="text-center py-4 text-red-500">${result.message}</td></tr>`;
      }
    } catch (error) {
      tableBody.innerHTML = `<tr><td colspan="5" class="text-center py-4 text-red-500">Gagal memuat data.</td></tr>`;
    }
  };

  const renderTable = (data) => {
    tableBody.innerHTML = "";
    if (data.length === 0) {
      tableBody.innerHTML = `<tr><td colspan="5" class="text-center py-4 text-gray-500">Tidak ada pengaduan ditemukan.</td></tr>`;
      return;
    }

    data.forEach((item) => {
      let statusClass = "";
      switch (item.status_laporan) {
        case "diajukan":
          statusClass = "bg-yellow-100 text-yellow-800";
          break;
        case "diproses":
          statusClass = "bg-blue-100 text-blue-800";
          break;
        case "selesai":
          statusClass = "bg-green-100 text-green-800";
          break;
      }
      const row = `
                <tr class="border-b hover:bg-gray-50">
                    <td class="py-3 px-4">${item.nama_bmn} (${item.no_bmn})</td>
                    <td class="py-3 px-4">${item.deskripsi.substring(
                      0,
                      50
                    )}...</td>
                    <td class="py-3 px-4">${item.tanggal_lapor}</td>
                    <td class="py-3 px-4"><span class="px-2 py-1 text-xs font-medium rounded-full ${statusClass}">${
        item.status_laporan
      }</span></td>
                    <td class="py-3 px-4 text-center">
                        <button data-id="${
                          item.id
                        }" class="detail-btn text-blue-500 p-1">Lihat Detail</button>
                    </td>
                </tr>
            `;
      tableBody.innerHTML += row;
    });
  };

  const fetchDetailPengaduan = async (id) => {
    try {
      const response = await fetch(`api/get_detail_pengaduan.php?id=${id}`);
      const result = await response.json();
      if (result.success) {
        renderDetailModal(result.data);
        detailModal.classList.remove("hidden");
      } else {
        alert(result.message);
      }
    } catch (error) {
      alert("Gagal memuat detail pengaduan.");
    }
  };

  const renderDetailModal = (data) => {
    detailPengaduanContent.innerHTML = `
            <p><strong>Aset:</strong> ${data.nama_bmn} (${data.no_bmn})</p>
            <p><strong>Dilaporkan oleh:</strong> ${data.username}</p>
            <p><strong>Tanggal Lapor:</strong> ${data.tanggal_lapor}</p>
            <p><strong>Deskripsi:</strong> ${data.deskripsi}</p>
            ${
              data.gambar_bukti
                ? `<img src="${data.gambar_bukti}" alt="Gambar Kerusakan" class="mt-4 max-w-full h-auto">`
                : ""
            }
        `;
    prosesPengaduanId.value = data.id;
    statusLaporanSelect.value = data.status_laporan;
    deskripsiPekerjaanInput.value = data.deskripsi_pekerjaan || "";
    prakiraanSelesaiInput.value = data.prakiraan_selesai || "";
  };

  prosesPengaduanForm.addEventListener("submit", async (e) => {
    e.preventDefault();
    const formData = new FormData(prosesPengaduanForm);
    const submitBtn = prosesPengaduanForm.querySelector(
      'button[type="submit"]'
    );
    submitBtn.disabled = true;
    submitBtn.textContent = "Menyimpan...";

    try {
      const response = await fetch("api/update_pengaduan_status.php", {
        method: "POST",
        body: formData,
      });
      const result = await response.json();
      if (result.success) {
        alert(result.message);
        detailModal.classList.add("hidden");
        fetchPengaduan();
      } else {
        alert(result.message);
      }
    } catch (error) {
      alert("Terjadi kesalahan jaringan.");
    } finally {
      submitBtn.disabled = false;
      submitBtn.textContent = "Simpan Perubahan";
    }
  });

  closeDetailModalBtn.addEventListener("click", () => {
    detailModal.classList.add("hidden");
  });

  tableBody.addEventListener("click", (e) => {
    if (e.target.classList.contains("detail-btn")) {
      const id = e.target.dataset.id;
      fetchDetailPengaduan(id);
    }
  });

  searchInput.addEventListener("input", () => {
    fetchPengaduan();
  });

  flatpickr("#prakiraan_selesai", { dateFormat: "Y-m-d" });
  fetchPengaduan();
});
