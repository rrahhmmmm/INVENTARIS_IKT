<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Data Inventaris - {{ $nama_terminal }}</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <style>
    .modal { display: none; }
    .modal.show { display: flex; }
    .toast { transform: translateX(100%); transition: transform 0.3s ease-in-out; }
    .toast.show { transform: translateX(0); }

    .table-container {
      max-height: 70vh;
      overflow-y: auto;
      overflow-x: auto;
      position: relative;
    }

    .table-container thead th {
      position: sticky;
      top: 0;
      z-index: 20;
      background-color: #2563eb;
      box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .status-baik {
      background-color: #10b981;
      color: white;
      padding: 4px 12px;
      border-radius: 6px;
    }

    .status-rusak {
      background-color: #ef4444;
      color: white;
      padding: 4px 12px;
      border-radius: 6px;
    }
  </style>
</head>
<body class="bg-gray-100 min-h-screen flex flex-col">

@include('components.TI_navbar')

<script>
(async () => {
  const token = localStorage.getItem('auth_token');
  if (!token) {
    window.location.href = "/";
    return;
  }
  try {
    const res = await fetch('/api/me', {
      headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' }
    });
    if (res.status === 401) {
      localStorage.removeItem('auth_token');
      window.location.href = "/";
      return;
    }
  } catch (err) {
    console.error("Auth check failed:", err);
    window.location.href = "/";
  }
})();
</script>

<header class="bg-white shadow-lg h-16 md:h-20 w-full"></header>

<!-- Main Content -->
<main class="container mx-auto px-3 md:px-6 py-6 flex-1">
  <!-- Header -->
  <div class="mb-6">
    <h1 class="text-2xl font-bold text-blue-600">Data Inventaris - {{ $nama_terminal }}</h1>
  </div>

  <!-- Controls -->
  <div class="bg-white rounded-lg shadow-lg p-3 md:p-4 mb-6 flex flex-col md:flex-row justify-between items-start md:items-center space-y-3 md:space-y-0">
    <div class="flex flex-wrap items-center gap-3">
      <button id="addInventarisBtn" class="bg-blue-600 hover:bg-blue-700 text-white px-3 md:px-4 py-2 rounded-lg flex items-center space-x-2 text-sm md:text-base">
        <i class="fas fa-plus"></i> <span>Tambah Inventaris</span>
      </button>
      <a href="{{ url('/api/inventaris/export') }}?terminal_id={{ $id_terminal }}" class="bg-green-600 hover:bg-green-700 text-white px-3 md:px-4 py-2 rounded-lg flex items-center space-x-2 text-sm md:text-base">
        <span>Export Excel</span> <i class="fas fa-file-excel"></i>
      </a>
    </div>
    <input type="text" id="searchInput" placeholder="Cari inventaris..." class="border rounded-lg px-3 py-2 w-full md:w-64 focus:ring-2 focus:ring-blue-500 focus:outline-none" />
  </div>

  <!-- Per Page Selection -->
  <div class="pb-2">
    <select id="perPageSelect" class="border rounded px-2 py-1 text-sm">
      <option value="10">10</option>
      <option value="25">25</option>
      <option value="50">50</option>
      <option value="100">100</option>
    </select>
  </div>

  <!-- Inventaris Table -->
  <div class="bg-white rounded-lg shadow-sm table-container">
    <table class="w-full min-w-[2500px] text-sm">
      <thead class="bg-blue-600 text-white">
        <tr>
          <th class="px-3 py-3 text-left font-medium min-w-[50px]">NO</th>
          <th class="px-3 py-3 text-left font-medium min-w-[120px]">Model</th>
          <th class="px-3 py-3 text-left font-medium min-w-[120px]">Tipe</th>
          <th class="px-3 py-3 text-left font-medium min-w-[130px]">Serial Number</th>
          <th class="px-3 py-3 text-left font-medium min-w-[80px]">Tahun</th>
          <th class="px-3 py-3 text-left font-medium min-w-[140px]">Kapasitas Prosessor</th>
          <th class="px-3 py-3 text-left font-medium min-w-[100px]">Memori</th>
          <th class="px-3 py-3 text-left font-medium min-w-[100px]">Penyimpanan</th>
          <th class="px-3 py-3 text-left font-medium min-w-[120px]">Sistem Operasi</th>
          <th class="px-3 py-3 text-left font-medium min-w-[120px]">User</th>
          <th class="px-3 py-3 text-left font-medium min-w-[150px]">Lokasi/Posisi</th>
          <th class="px-3 py-3 text-left font-medium min-w-[100px]">Kondisi</th>
          <th class="px-3 py-3 text-left font-medium min-w-[150px]">Keterangan</th>
          <th class="px-3 py-3 text-left font-medium min-w-[120px]">Terinstall AV</th>
          <th class="px-3 py-3 text-left font-medium min-w-[120px]">Mata Anggaran</th>
          <th class="px-3 py-3 text-left font-medium min-w-[150px]">Ket. Asset</th>
          <th class="px-3 py-3 text-center font-medium min-w-[100px]">Aksi</th>
        </tr>
      </thead>
      <tbody id="inventarisTableBody" class="divide-y divide-gray-200"></tbody>
    </table>
  </div>

  <!-- Loading & Empty -->
  <div id="loadingState" class="text-center py-8">
    <i class="fas fa-spinner fa-spin text-2xl text-blue-600"></i>
    <p class="mt-2 text-gray-600">Memuat data...</p>
  </div>
  <div id="emptyState" class="text-center py-8 hidden">
    <i class="fas fa-inbox text-4xl text-gray-400 mb-4"></i>
    <p class="text-gray-600">Tidak ada data inventaris</p>
  </div>
</main>

<!-- Pagination Controls -->
<div id="paginationControls" class="mt-1 mb-4 hidden">
  <div class="flex flex-col items-start mx-[100px]">
    <div class="flex items-center gap-2 mb-2">
      <button id="prevPageBtn" class="px-3 py-1 border rounded hover:bg-gray-100 disabled:opacity-50 disabled:cursor-not-allowed">
        <i class="fas fa-angle-left"></i>
      </button>
      <div id="pageNumbers" class="flex gap-1"></div>
      <button id="nextPageBtn" class="px-3 py-1 border rounded hover:bg-gray-100 disabled:opacity-50 disabled:cursor-not-allowed">
        <i class="fas fa-angle-right"></i>
      </button>
    </div>
    <div class="text-sm text-gray-600">
      Menampilkan <span id="showingFrom">0</span> Hingga
      <span id="showingTo">0</span> dari
      <span id="totalRecords">0</span> data
    </div>
  </div>
</div>

<!-- Modal Add/Edit -->
<div id="inventarisModal" class="modal fixed inset-0 bg-black bg-opacity-50 items-center justify-center z-50 px-2">
  <div class="bg-white rounded-lg p-5 md:p-8 w-full max-w-4xl mx-auto max-h-[90vh] overflow-y-auto">
    <div class="flex items-center justify-between mb-4">
      <h3 id="modalTitle" class="text-lg md:text-xl font-semibold">Tambah Inventaris</h3>
      <button id="closeModal" class="text-gray-400 hover:text-gray-600">
        <i class="fas fa-times text-xl"></i>
      </button>
    </div>

    <form id="inventarisForm" class="bg-white rounded-xl space-y-4">
      <input type="hidden" id="inventarisId">
      <input type="hidden" id="terminalId" value="{{ $id_terminal }}">

      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <!-- Model (Merk) -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Model</label>
          <select id="ID_MERK" class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none">
            <option value="">-- Pilih Model --</option>
          </select>
        </div>

        <!-- Tipe -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Tipe</label>
          <input type="text" id="TIPE" class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none">
        </div>

        <!-- Serial Number -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Serial Number</label>
          <input type="text" id="SERIAL_NUMBER" class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none">
        </div>

        <!-- Tahun Pengadaan -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Tahun Pengadaan</label>
          <input type="text" id="TAHUN_PENGADAAN" maxlength="4" placeholder="2024" class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none">
        </div>

        <!-- Kapasitas Prosessor -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Kapasitas Prosessor</label>
          <input type="text" id="KAPASITAS_PROSESSOR" placeholder="Intel Core i7" class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none">
        </div>

        <!-- Memori Utama -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Memori Utama</label>
          <input type="text" id="MEMORI_UTAMA" placeholder="16 GB" class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none">
        </div>

        <!-- Kapasitas Penyimpanan -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Kapasitas Penyimpanan</label>
          <input type="text" id="KAPASITAS_PENYIMPANAN" placeholder="512 GB SSD" class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none">
        </div>

        <!-- Sistem Operasi -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Sistem Operasi</label>
          <input type="text" id="SISTEM_OPERASI" placeholder="Windows 11 Pro" class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none">
        </div>

        <!-- User -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">User</label>
          <input type="text" id="USER_PENANGGUNG" class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none">
        </div>

        <!-- Lokasi/Posisi -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Lokasi/Posisi</label>
          <input type="text" id="LOKASI_POSISI" class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none">
        </div>

        <!-- Kondisi -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Kondisi</label>
          <select id="ID_KONDISI" class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none">
            <option value="">-- Pilih Kondisi --</option>
          </select>
        </div>

        <!-- Terinstall AV -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Terinstall AV</label>
          <select id="ID_INSTAL" class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none">
            <option value="">-- Pilih --</option>
          </select>
        </div>

        <!-- Mata Anggaran -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Mata Anggaran</label>
          <select id="ID_ANGGARAN" class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none">
            <option value="">-- Pilih Anggaran --</option>
          </select>
        </div>
      </div>

      <!-- Keterangan -->
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Keterangan</label>
        <textarea id="KETERANGAN" rows="2" class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none"></textarea>
      </div>

      <!-- Keterangan Asset -->
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Keterangan Asset</label>
        <textarea id="KETERANGAN_ASSET" rows="2" class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none"></textarea>
      </div>

      <!-- Dibuat Oleh -->
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Dibuat Oleh</label>
        <input type="text" id="CREATE_BY" readonly class="w-full border rounded-lg px-3 py-2 bg-gray-100 text-gray-600">
      </div>

      <div class="flex flex-col md:flex-row gap-3 mt-4">
        <button type="button" id="cancelBtn" class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-700 py-2 rounded-lg">
          Batal
        </button>
        <button type="submit" id="saveBtn" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white py-2 rounded-lg">
          Simpan
        </button>
      </div>
    </form>

    <!-- Import Excel -->
    <div class="border-t mt-6 pt-4">
      <h4 class="text-md font-semibold mb-3">Tambah Data Dengan Excel</h4>
      <a href="{{ url('/api/inventaris/export-template') }}" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg flex items-center justify-center space-x-2 mb-3">
        <span>Download Template</span> <i class="fas fa-download"></i>
      </a>
      <form id="importForm" class="flex flex-col md:flex-row items-start md:items-center gap-2">
        <input type="file" name="file" id="importFile" class="border px-2 py-1 rounded" required>
        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">
          Import
        </button>
      </form>
    </div>
  </div>
</div>

<!-- Toast -->
<div id="toast" class="toast fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50 text-sm md:text-base">
  <div class="flex items-center space-x-2">
    <i id="toastIcon" class="fas fa-check-circle"></i>
    <span id="toastMessage">Pesan berhasil</span>
  </div>
</div>

<script>
const terminalId = {{ $id_terminal }};
const apiUrl = "/api/t_inventaris";
const tableBody = document.getElementById("inventarisTableBody");
const loadingState = document.getElementById("loadingState");
const emptyState = document.getElementById("emptyState");
const modal = document.getElementById("inventarisModal");
const addBtn = document.getElementById("addInventarisBtn");
const closeModal = document.getElementById("closeModal");
const cancelBtn = document.getElementById("cancelBtn");
const form = document.getElementById("inventarisForm");
const toast = document.getElementById("toast");
const toastMessage = document.getElementById("toastMessage");
const token = localStorage.getItem('auth_token');

// Pagination elements
const paginationControls = document.getElementById("paginationControls");
const prevPageBtn = document.getElementById("prevPageBtn");
const nextPageBtn = document.getElementById("nextPageBtn");
const pageNumbers = document.getElementById("pageNumbers");
const showingFrom = document.getElementById("showingFrom");
const showingTo = document.getElementById("showingTo");
const totalRecords = document.getElementById("totalRecords");
const perPageSelect = document.getElementById("perPageSelect");

// Pagination state
let currentPage = 1;
let perPage = 10;
let totalPages = 1;
let lastSearchKeyword = "";

// ==== Load Dropdown Options ====
async function loadDropdownOptions() {
    try {
        // Load Merk
        const merkRes = await fetch('/api/m_merk/all', {
            headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' }
        });
        const merkData = await merkRes.json();
        const merkSelect = document.getElementById('ID_MERK');
        merkSelect.innerHTML = '<option value="">-- Pilih Model --</option>';
        (merkData.data || merkData).forEach(item => {
            merkSelect.innerHTML += `<option value="${item.ID_MERK}">${item.NAMA_MERK}</option>`;
        });

        // Load Kondisi
        const kondisiRes = await fetch('/api/m_kondisi/all', {
            headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' }
        });
        const kondisiData = await kondisiRes.json();
        const kondisiSelect = document.getElementById('ID_KONDISI');
        kondisiSelect.innerHTML = '<option value="">-- Pilih Kondisi --</option>';
        (kondisiData.data || kondisiData).forEach(item => {
            kondisiSelect.innerHTML += `<option value="${item.ID_KONDISI}">${item.NAMA_KONDISI}</option>`;
        });

        // Load Instal
        const instalRes = await fetch('/api/m_instal/all', {
            headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' }
        });
        const instalData = await instalRes.json();
        const instalSelect = document.getElementById('ID_INSTAL');
        instalSelect.innerHTML = '<option value="">-- Pilih --</option>';
        (instalData.data || instalData).forEach(item => {
            instalSelect.innerHTML += `<option value="${item.ID_INSTAL}">${item.NAMA_INSTAL}</option>`;
        });

        // Load Anggaran
        const anggaranRes = await fetch('/api/m_anggaran/all', {
            headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' }
        });
        const anggaranData = await anggaranRes.json();
        const anggaranSelect = document.getElementById('ID_ANGGARAN');
        anggaranSelect.innerHTML = '<option value="">-- Pilih Anggaran --</option>';
        (anggaranData.data || anggaranData).forEach(item => {
            anggaranSelect.innerHTML += `<option value="${item.ID_ANGGARAN}">${item.NAMA_ANGGARAN}</option>`;
        });

    } catch (err) {
        console.error('Error loading dropdowns:', err);
    }
}

// ==== Fetch Inventaris with Pagination ====
async function loadInventaris(keyword = "", page = 1) {
    loadingState.classList.remove("hidden");
    emptyState.classList.add("hidden");
    tableBody.innerHTML = "";
    paginationControls.classList.add("hidden");

    lastSearchKeyword = keyword;

    try {
        let url = `${apiUrl}?terminal_id=${terminalId}&page=${page}&per_page=${perPage}`;
        if (keyword && keyword.trim() !== "") {
            url += `&search=${encodeURIComponent(keyword)}`;
        }

        const res = await fetch(url, {
            headers: {
                'Authorization': `Bearer ${token}`,
                'Accept': 'application/json'
            }
        });

        const response = await res.json();
        loadingState.classList.add("hidden");

        const data = response.data || [];

        if (!Array.isArray(data) || data.length === 0) {
            emptyState.classList.remove("hidden");
            return;
        }

        data.forEach((item, i) => {
            const rowNumber = ((response.current_page - 1) * perPage) + i + 1;

            let row = `
                <tr class="hover:bg-gray-50">
                    <td class="px-3 py-3">${rowNumber}</td>
                    <td class="px-3 py-3">${item.merk?.NAMA_MERK ?? '-'}</td>
                    <td class="px-3 py-3">${item.TIPE ?? '-'}</td>
                    <td class="px-3 py-3">${item.SERIAL_NUMBER ?? '-'}</td>
                    <td class="px-3 py-3">${item.TAHUN_PENGADAAN ?? '-'}</td>
                    <td class="px-3 py-3">${item.KAPASITAS_PROSESSOR ?? '-'}</td>
                    <td class="px-3 py-3">${item.MEMORI_UTAMA ?? '-'}</td>
                    <td class="px-3 py-3">${item.KAPASITAS_PENYIMPANAN ?? '-'}</td>
                    <td class="px-3 py-3">${item.SISTEM_OPERASI ?? '-'}</td>
                    <td class="px-3 py-3">${item.USER_PENANGGUNG ?? '-'}</td>
                    <td class="px-3 py-3">${item.LOKASI_POSISI ?? '-'}</td>
                    <td class="px-3 py-3">${item.kondisi?.NAMA_KONDISI ?? '-'}</td>
                    <td class="px-3 py-3">${item.KETERANGAN ?? '-'}</td>
                    <td class="px-3 py-3">${item.instal?.NAMA_INSTAL ?? '-'}</td>
                    <td class="px-3 py-3">${item.anggaran?.NAMA_ANGGARAN ?? '-'}</td>
                    <td class="px-3 py-3">${item.KETERANGAN_ASSET ?? '-'}</td>
                    <td class="px-3 py-3 text-center space-x-2">
                        <button onclick="editInventaris(${item.ID_INVENTARIS})" class="text-blue-600 hover:text-blue-800"><i class="fas fa-edit"></i></button>
                        <button onclick="deleteInventaris(${item.ID_INVENTARIS})" class="text-red-600 hover:text-red-800"><i class="fas fa-trash"></i></button>
                    </td>
                </tr>
            `;
            tableBody.insertAdjacentHTML("beforeend", row);
        });

        renderPaginationControls({
            current_page: response.current_page,
            last_page: response.last_page,
            from: response.from,
            to: response.to,
            total: response.total
        });

    } catch (err) {
        console.error(err);
        loadingState.classList.add("hidden");
        showToast("Gagal memuat data");
    }
}

// ===== PAGINATION RENDER =====
function renderPaginationControls(paginationData) {
    const { current_page, last_page, from, to, total } = paginationData;

    currentPage = current_page;
    totalPages = last_page;

    if (total === 0) {
        paginationControls.classList.add("hidden");
        return;
    }

    paginationControls.classList.remove("hidden");

    showingFrom.textContent = from || 0;
    showingTo.textContent = to || 0;
    totalRecords.textContent = total;

    prevPageBtn.disabled = current_page === 1;
    nextPageBtn.disabled = current_page === last_page;

    pageNumbers.innerHTML = "";
    const maxVisiblePages = 5;
    let startPage = Math.max(1, current_page - Math.floor(maxVisiblePages / 2));
    let endPage = Math.min(last_page, startPage + maxVisiblePages - 1);

    if (endPage - startPage < maxVisiblePages - 1) {
        startPage = Math.max(1, endPage - maxVisiblePages + 1);
    }

    for (let i = startPage; i <= endPage; i++) {
        const pageBtn = document.createElement("button");
        pageBtn.textContent = i;
        pageBtn.className = `px-3 py-1 border rounded ${i === current_page ? 'bg-blue-600 text-white' : 'hover:bg-gray-100'}`;
        pageBtn.addEventListener("click", () => loadInventaris(lastSearchKeyword, i));
        pageNumbers.appendChild(pageBtn);
    }
}

// ===== PAGINATION EVENT LISTENERS =====
prevPageBtn.addEventListener("click", () => {
    if (currentPage > 1) loadInventaris(lastSearchKeyword, currentPage - 1);
});

nextPageBtn.addEventListener("click", () => {
    if (currentPage < totalPages) loadInventaris(lastSearchKeyword, currentPage + 1);
});

perPageSelect.addEventListener("change", (e) => {
    perPage = parseInt(e.target.value);
    loadInventaris(lastSearchKeyword, 1);
});

// ==== Load Username ====
async function loadUsername() {
    try {
        const res = await fetch('/api/me', {
            headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' }
        });
        if (!res.ok) throw new Error('Gagal memuat user');
        const data = await res.json();
        document.getElementById('CREATE_BY').value = data.username || '';
    } catch (err) {
        console.error(err);
    }
}

// ==== Add Modal ====
addBtn.addEventListener("click", () => {
    modal.classList.add("show");
    document.getElementById("modalTitle").innerText = "Tambah Inventaris";
    form.reset();
    document.getElementById("inventarisId").value = "";
    loadUsername();
});

closeModal.addEventListener("click", () => modal.classList.remove("show"));
cancelBtn.addEventListener("click", () => modal.classList.remove("show"));

// ==== Submit Form ====
form.addEventListener("submit", async function(e) {
    e.preventDefault();

    const id = document.getElementById("inventarisId").value;
    const payload = {
        ID_TERMINAL: terminalId,
        ID_MERK: document.getElementById("ID_MERK").value || null,
        TIPE: document.getElementById("TIPE").value,
        SERIAL_NUMBER: document.getElementById("SERIAL_NUMBER").value,
        TAHUN_PENGADAAN: document.getElementById("TAHUN_PENGADAAN").value,
        KAPASITAS_PROSESSOR: document.getElementById("KAPASITAS_PROSESSOR").value,
        MEMORI_UTAMA: document.getElementById("MEMORI_UTAMA").value,
        KAPASITAS_PENYIMPANAN: document.getElementById("KAPASITAS_PENYIMPANAN").value,
        SISTEM_OPERASI: document.getElementById("SISTEM_OPERASI").value,
        USER_PENANGGUNG: document.getElementById("USER_PENANGGUNG").value,
        LOKASI_POSISI: document.getElementById("LOKASI_POSISI").value,
        ID_KONDISI: document.getElementById("ID_KONDISI").value || null,
        KETERANGAN: document.getElementById("KETERANGAN").value,
        ID_INSTAL: document.getElementById("ID_INSTAL").value || null,
        ID_ANGGARAN: document.getElementById("ID_ANGGARAN").value || null,
        KETERANGAN_ASSET: document.getElementById("KETERANGAN_ASSET").value,
        CREATE_BY: document.getElementById("CREATE_BY").value
    };

    try {
        let res;
        if (id) {
            res = await fetch(`${apiUrl}/${id}`, {
                method: "PUT",
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${token}`
                },
                body: JSON.stringify(payload)
            });
        } else {
            res = await fetch(apiUrl, {
                method: "POST",
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${token}`
                },
                body: JSON.stringify(payload)
            });
        }

        const result = await res.json();

        if (res.ok) {
            showToast(result.message || "Data berhasil disimpan");
            modal.classList.remove("show");
            loadInventaris(lastSearchKeyword, currentPage);
        } else {
            showToast(result.message || "Gagal menyimpan data");
        }
    } catch (err) {
        console.error(err);
        showToast("Terjadi kesalahan");
    }
});

// ==== Edit Inventaris ====
async function editInventaris(id) {
    try {
        const res = await fetch(`${apiUrl}/${id}`, {
            headers: { 'Authorization': `Bearer ${token}` }
        });
        const data = await res.json();

        modal.classList.add("show");
        document.getElementById("modalTitle").innerText = "Edit Inventaris";
        document.getElementById("inventarisId").value = data.ID_INVENTARIS;
        document.getElementById("ID_MERK").value = data.ID_MERK || "";
        document.getElementById("TIPE").value = data.TIPE || "";
        document.getElementById("SERIAL_NUMBER").value = data.SERIAL_NUMBER || "";
        document.getElementById("TAHUN_PENGADAAN").value = data.TAHUN_PENGADAAN || "";
        document.getElementById("KAPASITAS_PROSESSOR").value = data.KAPASITAS_PROSESSOR || "";
        document.getElementById("MEMORI_UTAMA").value = data.MEMORI_UTAMA || "";
        document.getElementById("KAPASITAS_PENYIMPANAN").value = data.KAPASITAS_PENYIMPANAN || "";
        document.getElementById("SISTEM_OPERASI").value = data.SISTEM_OPERASI || "";
        document.getElementById("USER_PENANGGUNG").value = data.USER_PENANGGUNG || "";
        document.getElementById("LOKASI_POSISI").value = data.LOKASI_POSISI || "";
        document.getElementById("ID_KONDISI").value = data.ID_KONDISI || "";
        document.getElementById("KETERANGAN").value = data.KETERANGAN || "";
        document.getElementById("ID_INSTAL").value = data.ID_INSTAL || "";
        document.getElementById("ID_ANGGARAN").value = data.ID_ANGGARAN || "";
        document.getElementById("KETERANGAN_ASSET").value = data.KETERANGAN_ASSET || "";
        document.getElementById("CREATE_BY").value = data.CREATE_BY || "";
    } catch (err) {
        console.error(err);
        showToast("Gagal memuat data edit");
    }
}

// ==== Delete Inventaris ====
async function deleteInventaris(id) {
    if (!confirm("Yakin ingin menghapus data ini?")) return;

    try {
        const res = await fetch(`${apiUrl}/${id}`, {
            method: "DELETE",
            headers: { 'Authorization': `Bearer ${token}` }
        });

        const result = await res.json();

        if (res.ok) {
            showToast(result.message || "Data berhasil dihapus");
            loadInventaris(lastSearchKeyword, currentPage);
        } else {
            showToast(result.message || "Gagal menghapus data");
        }
    } catch (err) {
        console.error(err);
        showToast("Gagal menghapus data");
    }
}

// ==== Import Excel ====
document.getElementById("importForm").addEventListener("submit", async function(e) {
    e.preventDefault();
    const fileInput = document.getElementById("importFile").files[0];
    if (!fileInput) {
        showToast("Pilih file terlebih dahulu");
        return;
    }

    const formData = new FormData();
    formData.append("file", fileInput);
    formData.append("terminal_id", terminalId);

    try {
        const res = await fetch("/api/inventaris/import", {
            method: "POST",
            headers: { 'Authorization': `Bearer ${token}` },
            body: formData
        });
        const data = await res.json();
        if (res.ok) {
            showToast(data.message || "Import berhasil");
            modal.classList.remove("show");
            loadInventaris(lastSearchKeyword, currentPage);
        } else {
            showToast(data.message || "Gagal import");
        }
    } catch (err) {
        console.error(err);
        showToast("Error saat import");
    }
});

// ==== Toast ====
function showToast(msg) {
    toastMessage.innerText = msg;
    toast.classList.add("show");
    setTimeout(() => toast.classList.remove("show"), 3000);
}

// ==== Search with Debounce ====
let searchTimeout = null;
document.getElementById("searchInput").addEventListener("input", function() {
    clearTimeout(searchTimeout);
    let keyword = this.value;
    searchTimeout = setTimeout(() => loadInventaris(keyword, 1), 500);
});

// ==== Initialize ====
loadDropdownOptions();
loadInventaris();
</script>

</body>
</html>
