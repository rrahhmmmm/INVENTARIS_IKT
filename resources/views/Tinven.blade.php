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

    .select-perangkat-notice {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      border-radius: 12px;
      padding: 40px;
      text-align: center;
      color: white;
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

  <!-- Perangkat Filter (WAJIB dipilih) -->
  <div class="bg-white rounded-lg shadow-lg p-4 mb-6">
    <div class="flex flex-col md:flex-row items-start md:items-center gap-4">
      <div class="flex-1">
        <label class="block text-sm font-medium text-gray-700 mb-2">Pilih Jenis Perangkat <span class="text-red-500">*</span></label>
        <select id="perangkatFilter" class="w-full md:w-64 border-2 border-blue-500 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none font-medium">
          <option value="">-- Pilih Jenis Perangkat --</option>
        </select>
      </div>
      <div id="perangkatInfo" class="text-sm text-gray-500 hidden">
        <i class="fas fa-info-circle"></i> <span id="perangkatInfoText"></span>
      </div>
    </div>
  </div>

  <!-- Notice to select perangkat -->
  <div id="selectPerangkatNotice" class="select-perangkat-notice mb-6">
    <i class="fas fa-mouse-pointer text-5xl mb-4"></i>
    <h2 class="text-xl font-semibold mb-2">Pilih Jenis Perangkat</h2>
    <p class="opacity-80">Silakan pilih jenis perangkat di atas untuk menampilkan data inventaris</p>
  </div>

  <!-- Controls (hidden until perangkat selected) -->
  <div id="inventarisControls" class="hidden">
    <div class="bg-white rounded-lg shadow-lg p-3 md:p-4 mb-6 flex flex-col md:flex-row justify-between items-start md:items-center space-y-3 md:space-y-0">
      <div class="flex flex-wrap items-center gap-3">
        <button id="addInventarisBtn" class="bg-blue-600 hover:bg-blue-700 text-white px-3 md:px-4 py-2 rounded-lg flex items-center space-x-2 text-sm md:text-base">
          <i class="fas fa-plus"></i> <span>Tambah Inventaris</span>
        </button>
        <button id="exportExcelBtn" type="button" class="bg-green-600 hover:bg-green-700 text-white px-3 md:px-4 py-2 rounded-lg flex items-center space-x-2 text-sm md:text-base">
          <span>Export Excel</span> <i class="fas fa-file-excel"></i>
        </button>
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
      <table class="w-full text-sm" id="inventarisTable">
        <thead class="bg-blue-600 text-white" id="tableHeaders">
          <!-- Dynamic headers will be rendered here -->
        </thead>
        <tbody id="inventarisTableBody" class="divide-y divide-gray-200"></tbody>
      </table>
    </div>
  </div>

  <!-- Loading & Empty -->
  <div id="loadingState" class="text-center py-8 hidden">
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
      <input type="hidden" id="formPerangkatId">

      <!-- Jenis Perangkat Display (readonly) -->
      <div class="bg-blue-50 border border-blue-200 rounded-lg p-3 mb-4">
        <label class="block text-sm font-medium text-blue-700 mb-1">Jenis Perangkat</label>
        <div id="formPerangkatDisplay" class="font-semibold text-blue-800"></div>
      </div>

      <!-- Mandatory Fields (always shown) -->
      <div class="border-b pb-4 mb-4">
        <h4 class="text-md font-semibold mb-3 text-gray-700">Field Wajib</h4>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <!-- ID_MERK -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Merk <span class="text-red-500">*</span></label>
            <select id="ID_MERK" required class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none">
              <option value="">-- Pilih Merk --</option>
            </select>
          </div>
          <!-- TIPE -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Tipe <span class="text-red-500">*</span></label>
            <input type="text" id="TIPE" required class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none">
          </div>
          <!-- LOKASI_POSISI -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Lokasi/Posisi <span class="text-red-500">*</span></label>
            <input type="text" id="LOKASI_POSISI" required class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none">
          </div>
          <!-- TAHUN_PENGADAAN -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Tahun Pengadaan <span class="text-red-500">*</span></label>
            <input type="text" id="TAHUN_PENGADAAN" maxlength="4" placeholder="2024" required class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none">
          </div>
          <!-- ID_KONDISI -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Kondisi <span class="text-red-500">*</span></label>
            <select id="ID_KONDISI" required class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none">
              <option value="">-- Pilih Kondisi --</option>
            </select>
          </div>
          <!-- ID_ANGGARAN -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Mata Anggaran <span class="text-red-500">*</span></label>
            <select id="ID_ANGGARAN" required class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none">
              <option value="">-- Pilih Anggaran --</option>
            </select>
          </div>
        </div>
      </div>

      <!-- Device-Specific Fields (dynamically rendered) -->
      <div id="deviceSpecificSection">
        <h4 class="text-md font-semibold mb-3 text-gray-700">Detail Perangkat</h4>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4" id="deviceSpecificFields">
          <!-- Will be populated dynamically based on perangkat type -->
        </div>
      </div>

      <!-- Dibuat Oleh -->
      <div class="border-t pt-4 mt-4">
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
      <button id="downloadTemplateBtn" type="button" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg flex items-center justify-center space-x-2 mb-3 w-full">
        <span>Download Template</span> <i class="fas fa-download"></i>
      </button>
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
const tableHeaders = document.getElementById("tableHeaders");
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

// New elements
const selectPerangkatNotice = document.getElementById("selectPerangkatNotice");
const inventarisControls = document.getElementById("inventarisControls");
const perangkatFilter = document.getElementById("perangkatFilter");
const exportExcelBtn = document.getElementById("exportExcelBtn");
const downloadTemplateBtn = document.getElementById("downloadTemplateBtn");

// Pagination state
let currentPage = 1;
let perPage = 10;
let totalPages = 1;
let lastSearchKeyword = "";

// Perangkat state
let perangkatList = [];
let currentPerangkatSchema = {};
let currentPerangkatHeaders = [];
let selectedPerangkatId = null;
let selectedPerangkat = null;
let allSchemas = {};

// Column filter state
let columnFilters = {};

// Debounce helper function
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

// ==== Load Perangkat Options ====
async function loadPerangkatOptions() {
    try {
        const res = await fetch('/api/m_perangkat/all', {
            headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' }
        });
        const data = await res.json();
        perangkatList = data.data || data;

        // Populate filter dropdown
        perangkatFilter.innerHTML = '<option value="">-- Pilih Jenis Perangkat --</option>';
        perangkatList.forEach(item => {
            perangkatFilter.innerHTML += `<option value="${item.ID_PERANGKAT}">${item.NAMA_PERANGKAT}</option>`;
        });

        // Load all schemas at once for efficiency
        const schemasRes = await fetch('/api/m_perangkat/schemas', {
            headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' }
        });
        allSchemas = await schemasRes.json();

    } catch (err) {
        console.error('Error loading perangkat:', err);
    }
}

// ==== Load Dropdown Options ====
async function loadDropdownOptions() {
    try {
        // Load Merk
        const merkRes = await fetch('/api/m_merk/all', {
            headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' }
        });
        const merkData = await merkRes.json();
        const merkSelect = document.getElementById('ID_MERK');
        merkSelect.innerHTML = '<option value="">-- Pilih Merk --</option>';
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


// ==== Handle Perangkat Selection ====
perangkatFilter.addEventListener('change', function() {
    selectedPerangkatId = this.value;

    // Reset column filters when perangkat changes
    columnFilters = {};

    if (!selectedPerangkatId) {
        // Hide controls, show notice
        selectPerangkatNotice.classList.remove('hidden');
        inventarisControls.classList.add('hidden');
        paginationControls.classList.add('hidden');
        emptyState.classList.add('hidden');
        return;
    }

    // Find selected perangkat
    selectedPerangkat = perangkatList.find(p => p.ID_PERANGKAT == selectedPerangkatId);

    // Update schema and headers
    if (selectedPerangkat && allSchemas[selectedPerangkat.KODE_PERANGKAT]) {
        currentPerangkatSchema = allSchemas[selectedPerangkat.KODE_PERANGKAT].schema;
        currentPerangkatHeaders = allSchemas[selectedPerangkat.KODE_PERANGKAT].headers;
    }

    // Show controls, hide notice
    selectPerangkatNotice.classList.add('hidden');
    inventarisControls.classList.remove('hidden');

    // Update export URLs
    updateExportUrls();

    // Render table headers
    renderTableHeaders();

    // Load data
    loadInventaris("", 1);
});

// ==== Download File with Auth Token ====
async function downloadWithAuth(url, filename) {
    try {
        // Cek token exists
        if (!token) {
            showToast('Sesi telah berakhir, silakan login kembali', 'error');
            window.location.href = "/";
            return;
        }

        // Validasi token dengan memanggil /api/me terlebih dahulu
        const authCheck = await fetch('/api/me', {
            headers: {
                'Authorization': `Bearer ${token}`,
                'Accept': 'application/json'
            }
        });

        if (authCheck.status === 401) {
            localStorage.removeItem('auth_token');
            showToast('Sesi telah berakhir, silakan login kembali', 'error');
            window.location.href = "/";
            return;
        }

        showToast('Sedang mengunduh file...', 'success');

        // Token valid, lanjutkan export
        const response = await fetch(url, {
            method: 'GET',
            headers: {
                'Authorization': `Bearer ${token}`,
                'Accept': 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
            }
        });

        if (!response.ok) {
            throw new Error('Download failed');
        }

        const blob = await response.blob();
        const downloadUrl = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = downloadUrl;
        a.download = filename;
        document.body.appendChild(a);
        a.click();
        a.remove();
        window.URL.revokeObjectURL(downloadUrl);

        showToast('File berhasil didownload', 'success');
    } catch (error) {
        console.error('Download error:', error);
        showToast('Gagal download file', 'error');
    }
}

// ==== Event Listener untuk Export dan Download Template ====
exportExcelBtn.addEventListener('click', function() {
    if (!selectedPerangkatId) {
        showToast('Pilih jenis perangkat terlebih dahulu', 'error');
        return;
    }
    const url = `/api/inventaris/export?terminal_id=${terminalId}&perangkat_id=${selectedPerangkatId}`;
    const perangkatName = selectedPerangkat?.NAMA_PERANGKAT?.replace(/\s+/g, '_') || selectedPerangkatId;
    downloadWithAuth(url, `inventaris_${perangkatName}.xlsx`);
});

downloadTemplateBtn.addEventListener('click', function() {
    if (!selectedPerangkatId) {
        showToast('Pilih jenis perangkat terlebih dahulu', 'error');
        return;
    }
    const url = `/api/inventaris/export-template?perangkat_id=${selectedPerangkatId}`;
    const perangkatName = selectedPerangkat?.NAMA_PERANGKAT?.replace(/\s+/g, '_') || selectedPerangkatId;
    downloadWithAuth(url, `template_inventaris_${perangkatName}.xlsx`);
});

// ==== Update Export URLs (deprecated - using event listeners now) ====
function updateExportUrls() {
    // No longer needed since we use event listeners with fetch
    // Kept for backward compatibility
}

// ==== Render Table Headers ====
function renderTableHeaders() {
    const filterInputClass = "column-filter w-full px-2 py-1 text-sm text-gray-800 rounded border-0 focus:ring-2 focus:ring-blue-300";

    // Row 1: Header labels
    let headers = `
        <tr>
            <th class="px-3 py-3 text-left font-medium min-w-[50px]">NO</th>
            <th class="px-3 py-3 text-left font-medium min-w-[120px]">Merk</th>
            <th class="px-3 py-3 text-left font-medium min-w-[120px]">Tipe</th>
            <th class="px-3 py-3 text-left font-medium min-w-[150px]">Lokasi/Posisi</th>
            <th class="px-3 py-3 text-left font-medium min-w-[80px]">Tahun</th>
    `;

    // Device-specific headers
    if (currentPerangkatHeaders && currentPerangkatHeaders.length > 0) {
        currentPerangkatHeaders.forEach(header => {
            headers += `<th class="px-3 py-3 text-left font-medium min-w-[120px]">${header.label}</th>`;
        });
    }

    headers += `
            <th class="px-3 py-3 text-left font-medium min-w-[100px]">Kondisi</th>
            <th class="px-3 py-3 text-left font-medium min-w-[120px]">Anggaran</th>
            <th class="px-3 py-3 text-center font-medium min-w-[100px]">Aksi</th>
        </tr>
    `;

    // Row 2: Filter inputs
    headers += `
        <tr class="bg-blue-500">
            <th class="px-2 py-2"></th>
            <th class="px-2 py-2"><input type="text" class="${filterInputClass}" placeholder="Cari..." data-column="merk"></th>
            <th class="px-2 py-2"><input type="text" class="${filterInputClass}" placeholder="Cari..." data-column="tipe"></th>
            <th class="px-2 py-2"><input type="text" class="${filterInputClass}" placeholder="Cari..." data-column="lokasi"></th>
            <th class="px-2 py-2"><input type="text" class="${filterInputClass}" placeholder="Cari..." data-column="tahun"></th>
    `;

    // Device-specific filter inputs
    if (currentPerangkatHeaders && currentPerangkatHeaders.length > 0) {
        currentPerangkatHeaders.forEach(header => {
            headers += `<th class="px-2 py-2"><input type="text" class="${filterInputClass}" placeholder="Cari..." data-column="${header.key}"></th>`;
        });
    }

    headers += `
            <th class="px-2 py-2"><input type="text" class="${filterInputClass}" placeholder="Cari..." data-column="kondisi"></th>
            <th class="px-2 py-2"><input type="text" class="${filterInputClass}" placeholder="Cari..." data-column="anggaran"></th>
            <th class="px-2 py-2"></th>
        </tr>
    `;

    tableHeaders.innerHTML = headers;

    // Update table min-width based on number of columns
    const colCount = 8 + (currentPerangkatHeaders?.length || 0);
    document.getElementById('inventarisTable').style.minWidth = (colCount * 120) + 'px';

    // Setup filter listeners after rendering headers
    setupColumnFilterListeners();
}

// ==== Render Device Specific Form Fields ====
// schema format: { param1: { label: "SERIAL NUMBER", type: "text" }, param2: {...}, ... }
function renderDeviceSpecificFields(schema) {
    const container = document.getElementById('deviceSpecificFields');
    container.innerHTML = '';

    if (!schema || Object.keys(schema).length === 0) {
        document.getElementById('deviceSpecificSection').classList.add('hidden');
        return;
    }

    document.getElementById('deviceSpecificSection').classList.remove('hidden');

    Object.entries(schema).forEach(([paramKey, fieldConfig]) => {
        let fieldHtml = '';

        if (fieldConfig.type === 'textarea') {
            fieldHtml = `
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">${fieldConfig.label}</label>
                    <textarea id="${paramKey}" name="${paramKey}" rows="2" class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none"></textarea>
                </div>
            `;
        } else {
            fieldHtml = `
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">${fieldConfig.label}</label>
                    <input type="text" id="${paramKey}" name="${paramKey}" class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                </div>
            `;
        }

        container.insertAdjacentHTML('beforeend', fieldHtml);
    });
}

// ==== Fetch Inventaris with Pagination ====
async function loadInventaris(keyword = "", page = 1) {
    if (!selectedPerangkatId) return;

    loadingState.classList.remove("hidden");
    emptyState.classList.add("hidden");
    tableBody.innerHTML = "";
    paginationControls.classList.add("hidden");

    lastSearchKeyword = keyword;

    try {
        let url = `${apiUrl}?terminal_id=${terminalId}&perangkat_id=${selectedPerangkatId}&page=${page}&per_page=${perPage}`;
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
                    <td class="px-3 py-3">${item.LOKASI_POSISI ?? '-'}</td>
                    <td class="px-3 py-3">${item.TAHUN_PENGADAAN ?? '-'}</td>
            `;

            // Add device-specific columns from param1-param16
            if (currentPerangkatHeaders && currentPerangkatHeaders.length > 0) {
                currentPerangkatHeaders.forEach(header => {
                    // header.key is now 'param1', 'param2', etc.
                    let value = item[header.key] ?? '-';
                    row += `<td class="px-3 py-3">${value}</td>`;
                });
            }

            row += `
                    <td class="px-3 py-3">${item.kondisi?.NAMA_KONDISI ?? '-'}</td>
                    <td class="px-3 py-3">${item.anggaran?.NAMA_ANGGARAN ?? '-'}</td>
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
        showToast("Gagal memuat data", "error");
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
    if (!selectedPerangkatId) {
        showToast("Pilih jenis perangkat terlebih dahulu", "error");
        return;
    }

    modal.classList.add("show");
    document.getElementById("modalTitle").innerText = "Tambah Inventaris";
    form.reset();
    document.getElementById("inventarisId").value = "";
    document.getElementById("formPerangkatId").value = selectedPerangkatId;
    document.getElementById("formPerangkatDisplay").textContent = selectedPerangkat?.NAMA_PERANGKAT || '';

    // Render device-specific fields
    renderDeviceSpecificFields(currentPerangkatSchema);

    loadUsername();
});

closeModal.addEventListener("click", () => modal.classList.remove("show"));
cancelBtn.addEventListener("click", () => modal.classList.remove("show"));

// ==== Collect param values from form ====
function collectParamValues() {
    const params = {};
    if (currentPerangkatSchema) {
        Object.keys(currentPerangkatSchema).forEach(paramKey => {
            // paramKey is 'param1', 'param2', etc.
            const element = document.getElementById(paramKey);
            if (element) {
                params[paramKey] = element.value || null;
            }
        });
    }
    return params;
}

// ==== Submit Form ====
form.addEventListener("submit", async function(e) {
    e.preventDefault();

    const id = document.getElementById("inventarisId").value;
    const payload = {
        ID_TERMINAL: terminalId,
        ID_PERANGKAT: document.getElementById("formPerangkatId").value || selectedPerangkatId,
        ID_MERK: document.getElementById("ID_MERK").value || null,
        TIPE: document.getElementById("TIPE").value,
        LOKASI_POSISI: document.getElementById("LOKASI_POSISI").value,
        TAHUN_PENGADAAN: document.getElementById("TAHUN_PENGADAAN").value,
        ID_KONDISI: document.getElementById("ID_KONDISI").value || null,
        ID_ANGGARAN: document.getElementById("ID_ANGGARAN").value || null,
        CREATE_BY: document.getElementById("CREATE_BY").value,
        ...collectParamValues()  // Spread param1-param16
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
            showToast(result.message || "Gagal menyimpan data", "error");
        }
    } catch (err) {
        console.error(err);
        showToast("Terjadi kesalahan", "error");
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
        document.getElementById("formPerangkatId").value = data.ID_PERANGKAT;
        document.getElementById("formPerangkatDisplay").textContent = data.perangkat?.NAMA_PERANGKAT || selectedPerangkat?.NAMA_PERANGKAT || '';

        // Mandatory fields
        document.getElementById("ID_MERK").value = data.ID_MERK || "";
        document.getElementById("TIPE").value = data.TIPE || "";
        document.getElementById("LOKASI_POSISI").value = data.LOKASI_POSISI || "";
        document.getElementById("TAHUN_PENGADAAN").value = data.TAHUN_PENGADAAN || "";
        document.getElementById("ID_KONDISI").value = data.ID_KONDISI || "";
        document.getElementById("ID_ANGGARAN").value = data.ID_ANGGARAN || "";
        document.getElementById("CREATE_BY").value = data.CREATE_BY || "";

        // Render and populate device-specific fields
        renderDeviceSpecificFields(currentPerangkatSchema);

        // Populate param values from data
        if (currentPerangkatSchema) {
            Object.keys(currentPerangkatSchema).forEach(paramKey => {
                // paramKey is 'param1', 'param2', etc.
                const element = document.getElementById(paramKey);
                if (element) {
                    element.value = data[paramKey] ?? "";
                }
            });
        }

    } catch (err) {
        console.error(err);
        showToast("Gagal memuat data edit", "error");
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
            showToast(result.message || "Gagal menghapus data", "error");
        }
    } catch (err) {
        console.error(err);
        showToast("Gagal menghapus data", "error");
    }
}

// ==== Import Excel ====
document.getElementById("importForm").addEventListener("submit", async function(e) {
    e.preventDefault();

    if (!selectedPerangkatId) {
        showToast("Pilih jenis perangkat terlebih dahulu", "error");
        return;
    }

    const fileInput = document.getElementById("importFile").files[0];
    if (!fileInput) {
        showToast("Pilih file terlebih dahulu", "error");
        return;
    }

    const formData = new FormData();
    formData.append("file", fileInput);
    formData.append("terminal_id", terminalId);
    formData.append("perangkat_id", selectedPerangkatId);

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
            showToast(data.message || "Gagal import", "error");
        }
    } catch (err) {
        console.error(err);
        showToast("Error saat import", "error");
    }
});

// ==== Column Filter Functions ====
function setupColumnFilterListeners() {
    const columnFilterInputs = document.querySelectorAll(".column-filter");

    columnFilterInputs.forEach(input => {
        input.addEventListener("input", debounce((e) => {
            const column = e.target.dataset.column;
            if (column) {
                columnFilters[column] = e.target.value.toLowerCase().trim();
                applyColumnFilters();
            }
        }, 300));
    });
}

function applyColumnFilters() {
    const rows = tableBody.querySelectorAll("tr");

    // Build column map dynamically based on current headers
    // Fixed columns: 0=NO, 1=Merk, 2=Tipe, 3=Lokasi, 4=Tahun, then params, then Kondisi, Anggaran, Aksi
    const paramCount = currentPerangkatHeaders?.length || 0;
    const kondisiIndex = 5 + paramCount;
    const anggaranIndex = 6 + paramCount;

    const columnMap = {
        1: "merk",
        2: "tipe",
        3: "lokasi",
        4: "tahun"
    };

    // Add param columns to map
    if (currentPerangkatHeaders && currentPerangkatHeaders.length > 0) {
        currentPerangkatHeaders.forEach((header, idx) => {
            columnMap[5 + idx] = header.key;
        });
    }

    // Add kondisi and anggaran
    columnMap[kondisiIndex] = "kondisi";
    columnMap[anggaranIndex] = "anggaran";

    rows.forEach(row => {
        const cells = row.querySelectorAll("td");
        if (cells.length === 0) return;

        let showRow = true;

        for (const [colIndex, filterKey] of Object.entries(columnMap)) {
            const filterValue = columnFilters[filterKey];

            if (filterValue && filterValue !== "") {
                const cellText = cells[colIndex]?.textContent?.toLowerCase() || "";

                if (!cellText.includes(filterValue)) {
                    showRow = false;
                    break;
                }
            }
        }

        row.style.display = showRow ? "" : "none";
    });

    updateVisibleCount();
}

function updateVisibleCount() {
    const rows = tableBody.querySelectorAll("tr");
    let visibleCount = 0;

    rows.forEach(row => {
        if (row.style.display !== "none") {
            visibleCount++;
        }
    });

    // Update the "showing" text if column filters are active
    const hasActiveFilters = Object.values(columnFilters).some(v => v !== "");
    if (hasActiveFilters) {
        showingFrom.textContent = visibleCount > 0 ? "1" : "0";
        showingTo.textContent = visibleCount;
    }
}

// ==== Toast ====
function showToast(msg, type = "success") {
    const toastIcon = document.getElementById("toastIcon");
    toastMessage.innerText = msg;

    if (type === "error") {
        toast.classList.remove("bg-green-500");
        toast.classList.add("bg-red-500");
        toastIcon.classList.remove("fa-check-circle");
        toastIcon.classList.add("fa-exclamation-circle");
    } else {
        toast.classList.remove("bg-red-500");
        toast.classList.add("bg-green-500");
        toastIcon.classList.remove("fa-exclamation-circle");
        toastIcon.classList.add("fa-check-circle");
    }

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
loadPerangkatOptions();
loadDropdownOptions();
</script>

</body>
</html>
