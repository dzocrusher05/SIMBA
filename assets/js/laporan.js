document.addEventListener("DOMContentLoaded", () => {
  // --- STATE ---
  let reportData = [];

  // --- PANGGIL LIBRARY ---
  const { jsPDF } = window.jspdf;

  // --- ELEMEN ---
  const tampilkanBtn = document.getElementById("tampilkan-btn");
  const pdfBtn = document.getElementById("pdf-btn");
  const exportBtn = document.getElementById("export-btn");
  const judulLaporanEl = document.getElementById("judul-laporan");
  const periodeLaporanEl = document.getElementById("periode-laporan");
  const tabelWrapper = document.getElementById("tabel-laporan-wrapper");

  // --- INISIALISASI ---
  flatpickr("#tanggal_mulai", { dateFormat: "Y-m-d" });
  flatpickr("#tanggal_selesai", { dateFormat: "Y-m-d" });

  // --- FUNGSI-FUNGSI ---
  const renderTable = (jenis, data) => {
    if (data.length === 0) {
      tabelWrapper.innerHTML =
        '<p class="text-center text-gray-500">Tidak ada data untuk periode ini.</p>';
      exportBtn.classList.add("hidden");
      return;
    }
    let headers, rows;
    if (jenis === "peminjaman") {
      headers = [
        "Peminjam",
        "Aset",
        "Lokasi",
        "Tgl Pengajuan",
        "Tgl Pinjam",
        "Tgl Kembali",
        "Status",
      ];
      rows = data
        .map(
          (d) =>
            `<tr><td class="border px-4 py-2">${
              d.nama_peminjam
            }</td><td class="border px-4 py-2">${
              d.daftar_aset
            }</td><td class="border px-4 py-2">${
              d.lokasi_peminjaman
            }</td><td class="border px-4 py-2">${
              d.tanggal_pengajuan
            }</td><td class="border px-4 py-2">${
              d.tanggal_pinjam || "-"
            }</td><td class="border px-4 py-2">${
              d.tanggal_kembali || "-"
            }</td><td class="border px-4 py-2">${d.status_peminjaman}</td></tr>`
        )
        .join("");
    } else {
      headers = ["Pemohon", "Item Diminta", "Tgl Permintaan", "Status"];
      rows = data
        .map(
          (d) =>
            `<tr><td class="border px-4 py-2">${d.nama_pemohon}</td><td class="border px-4 py-2">${d.daftar_item}</td><td class="border px-4 py-2">${d.tanggal_permintaan}</td><td class="border px-4 py-2">${d.status_permintaan}</td></tr>`
        )
        .join("");
    }
    tabelWrapper.innerHTML = `<table class="table-auto w-full text-sm"><thead><tr class="bg-gray-100">${headers
      .map((h) => `<th class="px-4 py-2">${h}</th>`)
      .join("")}</tr></thead><tbody>${rows}</tbody></table>`;
  };

  const generateReport = async () => {
    const jenis = document.getElementById("jenis_laporan").value;
    const mulai = document.getElementById("tanggal_mulai").value;
    const selesai = document.getElementById("tanggal_selesai").value;
    if (!mulai || !selesai) {
      alert("Silakan pilih rentang tanggal.");
      return;
    }
    tampilkanBtn.textContent = "Memuat...";
    tampilkanBtn.disabled = true;
    try {
      const response = await fetch(
        `api/get_laporan.php?jenis_laporan=${jenis}&tanggal_mulai=${mulai}&tanggal_selesai=${selesai}`
      );
      const result = await response.json();
      if (result.success) {
        reportData = result.data;
        const jenisText =
          jenis.charAt(0).toUpperCase() + jenis.slice(1).replace("_", " ");
        judulLaporanEl.textContent = `Laporan ${jenisText}`;
        periodeLaporanEl.textContent = `Periode: ${mulai} s/d ${selesai}`;
        renderTable(jenis, result.data);
        exportBtn.classList.remove("hidden");
      } else {
        alert(result.message);
        tabelWrapper.innerHTML = `<p class="text-center text-red-500">${result.message}</p>`;
        exportBtn.classList.add("hidden");
      }
    } catch (error) {
      alert("Gagal mengambil data laporan.");
    } finally {
      tampilkanBtn.textContent = "Tampilkan";
      tampilkanBtn.disabled = false;
    }
  };

  const exportToPdf = () => {
    if (reportData.length === 0) {
      alert(
        "Tampilkan data laporan terlebih dahulu sebelum mengekspor ke PDF."
      );
      return;
    }
    const doc = new jsPDF({ orientation: "landscape" });
    const jenisLaporanText = judulLaporanEl.textContent;
    const periodeText = periodeLaporanEl.textContent;
    const jenis = document.getElementById("jenis_laporan").value;

    doc.setFontSize(16);
    doc.text(jenisLaporanText, 14, 22);
    doc.setFontSize(10);
    doc.text(periodeText, 14, 30);

    let head, body;
    if (jenis === "peminjaman") {
      head = [
        [
          "Peminjam",
          "Aset",
          "Lokasi",
          "Tgl Pengajuan",
          "Tgl Pinjam",
          "Tgl Kembali",
          "Status",
        ],
      ];
      body = reportData.map((d) => [
        d.nama_peminjam,
        d.daftar_aset,
        d.lokasi_peminjaman,
        d.tanggal_pengajuan,
        d.tanggal_pinjam || "-",
        d.tanggal_kembali || "-",
        d.status_peminjaman,
      ]);
    } else {
      head = [["Pemohon", "Item Diminta", "Tgl Permintaan", "Status"]];
      body = reportData.map((d) => [
        d.nama_pemohon,
        d.daftar_item,
        d.tanggal_permintaan,
        d.status_permintaan,
      ]);
    }

    doc.autoTable({
      head: head,
      body: body,
      startY: 35,
      theme: "grid",
      styles: { fontSize: 8 },
      headStyles: { fillColor: [41, 128, 185], textColor: 255 },
    });

    doc.save(`Laporan ${jenis}.pdf`);
  };

  const exportToExcel = () => {
    if (reportData.length === 0) {
      alert(
        "Tampilkan data laporan terlebih dahulu sebelum mengekspor ke Excel."
      );
      return;
    }
    const jenis = document
      .getElementById("jenis_laporan")
      .value.replace("_", " ");
    const ws = XLSX.utils.json_to_sheet(reportData);
    const wb = XLSX.utils.book_new();
    XLSX.utils.book_append_sheet(wb, ws, "Laporan");
    XLSX.writeFile(wb, `Laporan ${jenis}.xlsx`);
  };

  // --- EVENT LISTENERS ---
  tampilkanBtn.addEventListener("click", generateReport);
  pdfBtn.addEventListener("click", exportToPdf);
  exportBtn.addEventListener("click", exportToExcel);
});
