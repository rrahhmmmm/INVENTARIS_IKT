<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard Arsip</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-gray-100 min-h-screen py-10 px-6">
  @include('components.TA_navbar')

  <div class="max-w-7xl mx-auto">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-2xl font-bold">📊 Dashboard</h1>

      <!-- Notification Bell -->
      <div class="relative">
        <button id="notifBtn" class="relative bg-white p-3 rounded-full shadow hover:bg-gray-100 transition">
          🔔
          <span id="notifCount" class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full px-1.5 py-0.5 hidden"></span>
        </button>

        <!-- Dropdown Notifikasi -->
        <!-- ganti bos jadi untuk balikin ke master -->
        <div id="notifDropdown" class="hidden absolute right-0 mt-2 w-80 bg-white rounded-lg shadow-lg border border-gray-200 z-50">
          <div class="p-3 border-b font-semibold">Notifikasi Pemusnahan Arsip</div>
          <ul id="notifList" class="max-h-64 overflow-y-auto"></ul>
        </div>
      </div>
    </div>


    <!-- Charts -->
    <div class="grid md:grid-cols-2 gap-6 mb-8">
      <!-- Pie Chart -->
      <div class="bg-white p-6 rounded-2xl shadow">
      <div>
        <label for="filterDivisi" class="block text-sm font-semibold text-gray-700 mb-1">
          Filter Berdasarkan Divisi
        </label>
        <select id="filterDivisi" class="px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-400 focus:outline-none">
          <option value="">Semua Divisi</option>
        </select>
      </div>
        <h2 class="text-lg font-semibold mb-4 text-center">Persentase Arsip Aktif vs Non-Aktif</h2>
        <canvas id="pieChart"></canvas>
      </div>

      <!-- Bar Chart -->
      <div class="bg-white p-6 rounded-2xl shadow">
        <h2 class="text-lg font-semibold mb-4 text-center">Jumlah Arsip per Divisi</h2>
        <canvas id="barChart"></canvas>
      </div>
    </div>

    <!-- Tabel Arsip -->
    <div class="flex flex-wrap items-end justify-end mb-4">
  <button class="bg-blue-600 text-white px-5 py-2 rounded-lg shadow hover:bg-blue-700 transition" id="btnTambah">
    + Tambah Arsip / Transaksi
  </button>
</div>

    <div class="bg-white p-6 rounded-2xl shadow">
      
      <h2 class="text-lg font-semibold mb-4">📁 Data Arsip</h2>
      <div class="overflow-x-auto">
        <table class="w-full border border-gray-200 rounded-lg overflow-hidden">
          <thead class="bg-blue-600 text-white">
            <tr>
              <th class="py-2 px-3 text-left">#</th>
              <th class="py-2 px-3 text-left">Nomor Berkas</th>
              <th class="py-2 px-3 text-left">Judul</th>
              <th class="py-2 px-3 text-left">Divisi</th>
              <th class="py-2 px-3 text-left">Rak / BAK / No Urut</th>
              <th class="py-2 px-3 text-left">Status</th>
              <th class="py-2 px-3 text-left">Tanggal Dokumen</th>
              <th class="py-2 px-3 text-left">File</th>
            </tr>
          </thead>
          <tbody id="tabelArsip" class="divide-y divide-gray-200">
            <!-- Data arsip akan dimuat via JS -->
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <script>
    const filterDivisi = document.getElementById('filterDivisi');
    const tabelArsip = document.getElementById('tabelArsip');
    const notifBtn = document.getElementById('notifBtn');
    const notifDropdown = document.getElementById('notifDropdown');
    const notifList = document.getElementById('notifList');
    const notifCount = document.getElementById('notifCount');

    // Dummy Divisi
    let divisiData = [
      { ID_DIVISI: 1, NAMA_DIVISI: "Keuangan" },
      { ID_DIVISI: 2, NAMA_DIVISI: "SDM" },
      { ID_DIVISI: 3, NAMA_DIVISI: "Operasional" },
      { ID_DIVISI: 4, NAMA_DIVISI: "Teknologi Informasi" },
      { ID_DIVISI: 5, NAMA_DIVISI: "Hukum & Umum" },
    ];

    // Dummy Arsip (mengikuti struktur tabel sebenarnya)
    let arsipData = [
      { ID_ARSIP: 1, ID_DIVISI: 1, NAMA_DIVISI: "Keuangan", NOMOR_BERKAS: "A001", JUDUL: "Laporan Keuangan 2024", RAK_ARSIP: 1, BAK_ARSIP: 1, NO_URUT: 1, STATUS: "Aktif", TANGGAL: "2025-01-10", FILE_PDF: "laporan_keuangan.pdf", RETENSI_TGL: "2025-12-01" },
      { ID_ARSIP: 2, ID_DIVISI: 2, NAMA_DIVISI: "SDM", NOMOR_BERKAS: "B014", JUDUL: "Data Karyawan", RAK_ARSIP: 2, BAK_ARSIP: 1, NO_URUT: 2, STATUS: "Aktif", TANGGAL: "2024-08-15", FILE_PDF: "data_karyawan.pdf", RETENSI_TGL: "2025-08-01" },
      { ID_ARSIP: 3, ID_DIVISI: 3, NAMA_DIVISI: "Operasional", NOMOR_BERKAS: "C007", JUDUL: "Rencana Operasi", RAK_ARSIP: 3, BAK_ARSIP: 2, NO_URUT: 3, STATUS: "Non-Aktif", TANGGAL: "2023-11-30", FILE_PDF: "rencana_operasi.pdf", RETENSI_TGL: "2024-12-01" },
      { ID_ARSIP: 4, ID_DIVISI: 4, NAMA_DIVISI: "Teknologi Informasi", NOMOR_BERKAS: "D009", JUDUL: "Dokumentasi Sistem", RAK_ARSIP: 4, BAK_ARSIP: 1, NO_URUT: 1, STATUS: "Aktif", TANGGAL: "2025-03-05", FILE_PDF: "dokumentasi_sistem.pdf", RETENSI_TGL: "2026-03-01" },
      { ID_ARSIP: 5, ID_DIVISI: 5, NAMA_DIVISI: "Hukum & Umum", NOMOR_BERKAS: "E011", JUDUL: "Peraturan Internal", RAK_ARSIP: 5, BAK_ARSIP: 1, NO_URUT: 2, STATUS: "Non-Aktif", TANGGAL: "2023-06-01", FILE_PDF: "peraturan_internal.pdf", RETENSI_TGL: "2024-06-01" },
    ];

    // isi dropdown divisi
    divisiData.forEach(d => {
      const opt = document.createElement('option');
      opt.value = d.ID_DIVISI;
      opt.textContent = d.NAMA_DIVISI;
      filterDivisi.appendChild(opt);
    });

    // Render semua
    function renderAll() {
      const selectedDiv = filterDivisi.value;
      const filtered = selectedDiv ? arsipData.filter(a => a.ID_DIVISI == selectedDiv) : arsipData;

      renderTable(filtered);
      renderCharts(filtered);
      renderNotif();
    }

    // Render tabel arsip
    function renderTable(data) {
      tabelArsip.innerHTML = '';
      if (data.length === 0) {
        tabelArsip.innerHTML = `<tr><td colspan="8" class="text-center py-3 text-gray-500">Tidak ada data</td></tr>`;
        return;
      }

      data.forEach((a, i) => {
        const row = `
          <tr>
            <td class="py-2 px-3">${i + 1}</td>
            <td class="py-2 px-3">${a.NOMOR_BERKAS}</td>
            <td class="py-2 px-3">${a.JUDUL}</td>
            <td class="py-2 px-3">${a.NAMA_DIVISI}</td>
            <td class="py-2 px-3">${a.RAK_ARSIP}/${a.BAK_ARSIP}/${a.NO_URUT}</td>
            <td class="py-2 px-3">
              <span class="px-2 py-1 rounded text-white text-xs ${a.STATUS === 'Aktif' ? 'bg-green-500' : 'bg-gray-400'}">
                ${a.STATUS}
              </span>
            </td>
            <td class="py-2 px-3">${a.TANGGAL}</td>
            <td class="py-2 px-3">
              <a href="#" class="text-blue-600 underline hover:text-blue-800">📄 PDF</a>
            </td>
          </tr>`;
        tabelArsip.insertAdjacentHTML('beforeend', row);
      });
    }

    // Render chart
    let pieChart, barChart;
    function renderCharts(data) {
      const aktif = data.filter(a => a.STATUS === 'Aktif').length;
      const nonAktif = data.filter(a => a.STATUS !== 'Aktif').length;
      const perDivisi = {};
      data.forEach(a => {
        perDivisi[a.NAMA_DIVISI] = (perDivisi[a.NAMA_DIVISI] || 0) + 1;
      });

      const divisiLabels = Object.keys(perDivisi);
      const divisiCounts = Object.values(perDivisi);

      if (pieChart) pieChart.destroy();
      pieChart = new Chart(document.getElementById('pieChart'), {
        type: 'pie',
        data: {
          labels: ['Aktif', 'Non-Aktif'],
          datasets: [{ data: [aktif, nonAktif], backgroundColor: ['#3b82f6', '#9ca3af'] }]
        }
      });

      if (barChart) barChart.destroy();
      barChart = new Chart(document.getElementById('barChart'), {
        type: 'bar',
        data: {
          labels: divisiLabels,
          datasets: [{ label: 'Jumlah Arsip', data: divisiCounts, backgroundColor: '#3b82f6' }]
        },
        options: { scales: { y: { beginAtZero: true } } }
      });
    }

    // Render notifikasi arsip yang waktunya dimusnahkan
    function renderNotif() {
      const today = new Date();
      const due = arsipData.filter(a => new Date(a.RETENSI_TGL) <= today);

      notifList.innerHTML = '';
      if (due.length === 0) {
        notifList.innerHTML = `<li class="p-3 text-gray-500 text-sm">Belum ada arsip yang harus dimusnahkan.</li>`;
        notifCount.classList.add('hidden');
      } else {
        notifCount.textContent = due.length;
        notifCount.classList.remove('hidden');

        due.forEach(a => {
          notifList.insertAdjacentHTML('beforeend', `
            <li class="p-3 border-b hover:bg-gray-50 cursor-pointer text-sm">
              <div class="font-semibold">${a.NOMOR_BERKAS}</div>
              <div class="text-gray-600 text-xs">Lokasi: ${a.RAK_ARSIP}/${a.BAK_ARSIP}/${a.NO_URUT}</div>
              <div class="text-red-600 text-xs mt-1">Tanggal Retensi: ${a.RETENSI_TGL}</div>
            </li>
          `);
        });
      }
    }

    // Event
    filterDivisi.addEventListener('change', renderAll);
    notifBtn.addEventListener('click', () => notifDropdown.classList.toggle('hidden'));
    document.addEventListener('click', e => {
      if (!notifBtn.contains(e.target) && !notifDropdown.contains(e.target)) notifDropdown.classList.add('hidden');
    });

    renderAll();
  </script>

</body>
</html>
