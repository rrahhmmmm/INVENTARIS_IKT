<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Manajemen Arsip</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <style>
    .modal { display: none; }
    .modal.show { display: flex; }
    .toast { transform: translateX(100%); transition: transform 0.3s ease-in-out; }
    .toast.show { transform: translateX(0); }
  </style>
</head>
<body class="bg-gray-100">

@include('components.TA_navbar')

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

<main class="container mx-auto px-4 py-6">

  <!-- Kontrol -->
  <div class="bg-white rounded-lg shadow-lg p-4 mb-6 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
    <div class="flex flex-wrap items-center gap-2">
      <button id="addArsipBtn" class="bg-blue-600 hover:bg-blue-700 text-white px-3 md:px-4 py-2 rounded-lg flex items-center space-x-2 text-sm md:text-base">
        <i class="fas fa-plus"></i> <span>Tambah Arsip</span>
      </button>
    </div>
    
    <div class="relative space-x-2">
      
      <input id="searchInput" type="text" placeholder="Cari arsip..." class="border px-3 py-2 w-full md:w-auto text-sm md:text-base" />
      
      <button id="notificationBtn" class="relative">
          <i class="fas fa-bell text-xl text-gray-700"></i>
          <span id="notificationCount" class="absolute -top-1 -right-2 bg-red-500 text-white text-xs rounded-full px-1">0</span>
        </button>



      <div id="notificationDropdown" class="absolute right-0 mt-2 w-80 bg-red-600 shadow-lg rounded-lg overflow-hidden z-50 max-h-96 overflow-y-auto">
        <ul id="notificationList"></ul>
      </div>
  </div>
</div>

  

  <!-- Tabel Arsip -->
    <div class="pb-2">
      <select id="perPageSelect" class="border rounded px-2 py-1 text-sm">
              <option value="10">10</option>
              <option value="25">25</option>
              <option value="50">50</option>
              <option value="100">100</option>
      </select>
    </div>

  <div class="bg-white rounded-lg shadow-sm overflow-hidden">
  <div class="overflow-x-auto">
    <table class="min-w-[2500px] table-fixed text-sm md:text-base">
          <thead class="bg-blue-600 text-white">
        <tr>
          <th class="sticky top-0 bg-blue-600 px-4 py-3 min-w-[60px] z-10">NO</th>
          <th class="sticky top-0 bg-blue-600 px-4 py-3 min-w-[200px] z-10">Divisi</th>
          <th class="sticky top-0 bg-blue-600 px-4 py-3 min-w-[200px] z-10">Subdivisi</th>
          <th class="sticky top-0 bg-blue-600 px-4 py-3 w-[120px] z-10">No Indeks</th>
          <th class="sticky top-0 bg-blue-600 px-4 py-3 min-w-[150px] z-10">No Berkas</th>
          <th class="sticky top-0 bg-blue-600 px-4 py-3 min-w-[300px] z-10">Judul Berkas</th>
          <th class="sticky top-0 bg-blue-600 px-4 py-3 min-w-[150px] z-10">No Isi Berkas</th>
          <th class="sticky top-0 bg-blue-600 px-4 py-3 min-w-[150px] z-10">Jenis Naskah Dinas</th>
          <th class="sticky top-0 bg-blue-600 px-4 py-3 min-w-[150px] z-10">Kode Klasifikasi</th>
          <th class="sticky top-0 bg-blue-600 px-4 py-3 min-w-[300px] z-10">No Nota Dinas</th>
          <th class="sticky top-0 bg-blue-600 px-4 py-3 min-w-[200px] z-10">Tanggal Berkas</th>
          <th class="sticky top-0 bg-blue-600 px-4 py-3 min-w-[400px] z-10">Perihal</th>
          <th class="sticky top-0 bg-blue-600 px-4 py-3 w-40 z-10">Tingkat Pengembangan</th>
          <th class="sticky top-0 bg-blue-600 px-4 py-3 w-[120px] z-10">Kondisi</th>
          <th class="sticky top-0 bg-blue-600 px-4 py-3 w-40 z-10">Lokasi Simpan</th>
          <th class="sticky top-0 bg-blue-600 px-4 py-3 w-[200px] z-10">Keterangan Simpan</th>
          <th class="sticky top-0 bg-blue-600 px-4 py-3 w-[150px] z-10">Tipe Retensi</th>
          <th class="sticky top-0 bg-blue-600 px-4 py-3 min-w-[200px] z-10">Tanggal Retensi</th>
          <th class="sticky top-0 bg-blue-600 px-4 py-3 w-[150px] z-10">Keterangan</th>
          <th class="sticky top-0 bg-blue-600 px-4 py-3 w-[140px] z-10">Create By</th>
          <th class="sticky top-0 bg-blue-600 px-4 py-3 w-[120px] z-10">File Arsip</th>
          <th class="sticky top-0 bg-blue-600 px-4 py-3 w-[100px] text-center z-10">Aksi</th>
        </tr>
      </thead>
            <tbody id="arsipTableBody" class="divide-y divide-gray-200 text-gray-700"></tbody>
      </table>
    </div>
  </div>

      <div id="loadingState" class="text-center py-8">
        <i class="fas fa-spinner fa-spin text-2xl text-blue-600"></i>
        <p class="mt-2 text-gray-600">Memuat data...</p>
      </div>
      <div id="emptyState" class="text-center py-8 hidden">
        <i class="fas fa-inbox text-4xl text-gray-400 mb-4"></i>
        <p class="text-gray-600">Tidak ada data arsip</p>
      </div>
  </main>

<div id="paginationControls" class="mt-1 mb-4 hidden">

<!-- pagination -->
<div class="flex flex-col items-start mx-[100px] ">

  <!-- Pagination Buttons -->
  <div class="flex items-center gap-2  mb-2">
    

    <button id="prevPageBtn" class="px-3 py-1 border rounded hover:bg-gray-100 disabled:opacity-50 disabled:cursor-not-allowed">
      <i class="fas fa-angle-left"></i> 
    </button>

    <div id="pageNumbers" class="flex gap-1"></div>

    <button id="nextPageBtn" class="px-3 py-1 border rounded hover:bg-gray-100 disabled:opacity-50 disabled:cursor-not-allowed">
      <i class="fas fa-angle-right"></i>
    </button>
  
  </div>

   <!-- Info -->
   <div class="text-sm text-gray-600">
        Menampilkan <span id="showingFrom">0</span> Hingga 
        <span id="showingTo">0</span> dari 
        <span id="totalRecords">0</span> data
      </div>
</div>


<!-- Modal Arsip -->
<div id="arsipModal" class="modal fixed inset-0 bg-black bg-opacity-50 items-center justify-center z-50">
  <div class="bg-white rounded-lg p-6 w-full max-w-3xl mx-4 max-h-screen overflow-y-auto">
    <div class="flex items-center justify-between mb-4">
      <h3 id="modalTitle" class="text-lg font-semibold">Tambah Arsip</h3>
      <button id="closeModal" class="text-gray-400 hover:text-gray-600">
        <i class="fas fa-times"></i>
      </button>
    </div>

    <form id="arsipForm" class="grid grid-cols-2 gap-4" enctype="multipart/form-data">
  <input type="hidden" id="arsipId">


  <!-- Kolom Utama -->
          <div>
          <label class="block text-sm font-medium mb-1">Divisi</label>
          <input id="DIVISI_NAME" class="w-full border rounded-lg px-3 py-2 bg-gray-100" readonly>
        </div>
        <input type="hidden" id="ID_DIVISI" name="ID_DIVISI">


        <div>
          <label class="block text-sm font-medium mb-1">Subdivisi</label>
          <input id="SUBDIVISI_NAME"  class="w-full border rounded-lg px-3 py-2 bg-gray-100" readonly>
        </div>
        <input type="hidden" id="ID_SUBDIVISI" name="ID_SUBDIVISI">
        
        <div class="relative">
          <label class="block text-sm font-medium mb-1">No Indeks</label>
          <input id="NO_INDEKS" name="NO_INDEKS" class="w-full border rounded-lg px-3 py-2" required autocomplete="off">
          <ul id="indeksSuggestions" class="absolute bg-white border border-gray-300 rounded-lg shadow-lg mt-1 w-full hidden z-50 max-h-60 overflow-y-auto"></ul>
        </div>

          <div>
            <label class="block text-sm font-medium mb-1">No Berkas</label>
            <input id="NO_BERKAS" name="NO_BERKAS" class="w-full border rounded-lg px-3 py-2" required>
          </div>

          <div class="col-span-2">
            <label class="block text-sm font-medium mb-1">Judul Berkas</label>
            <input id="JUDUL_BERKAS" name="JUDUL_BERKAS" class="w-full border rounded-lg px-3 py-2" required>
          </div>

          <div>
            <label class="block text-sm font-medium mb-1">No Isi Berkas</label>
            <input id="NO_ISI_BERKAS" name="NO_ISI_BERKAS" class="w-full border rounded-lg px-3 py-2">
          </div>

          <div>
            <label class="block text-sm font-medium mb-1">Jenis Naskah Dinas</label>
            <input id="JENIS_ARSIP" name="JENIS_ARSIP" class="w-full border rounded-lg px-3 py-2">
          </div>

          <div class="relative">
            <label class="block text-sm font-medium mb-1">Kode Klasifikasi</label>
            <input id="KODE_KLASIFIKASI" name="KODE_KLASIFIKASI" class="w-full border rounded-lg px-3 py-2" required autocomplete="off">
            <ul id="klasifikasiSuggestions" class="absolute bg-white border border-gray-300 rounded-lg shadow-lg mt-1 w-full hidden z-50 max-h-60 overflow-y-auto"></ul>
          </div>

          <div>
            <label class="block text-sm font-medium mb-1">No Nota Dinas</label>
            <input id="NO_NOTA_DINAS" name="NO_NOTA_DINAS"class="w-full border rounded-lg px-3 py-2">
          </div>

          <div>
            <label class="block text-sm font-medium mb-1">Tanggal Berkas</label>
            <input type="date" name="TANGGAL_BERKAS" id="TANGGAL_BERKAS" class="w-full border rounded-lg px-3 py-2">
          </div>

          <div>
            <label class="block text-sm font-medium mb-1">Perihal</label>
            <input id="PERIHAL" name="PERIHAL" class="w-full border rounded-lg px-3 py-2">
          </div>

          <div>
            <label class="block text-sm font-medium mb-1">Tingkat Pengembangan</label>
            <input id="TINGKAT_PENGEMBANGAN" name="TINGKAT_PENGEMBANGAN" class="w-full border rounded-lg px-3 py-2">
          </div>

          <div>
            <label class="block text-sm font-medium mb-1">Kondisi</label>
            <select id="KONDISI" name="KONDISI" class="w-full border rounded-lg px-3 py-2">
              <option value="">-- Pilih Kondisi --</option>
            </select>
          </div>

          <div>
            <label class="block text-sm font-medium mb-1">Lokasi Simpan</label>
            <div class="flex gap-2">
              <input id="RAK_INPUT" type="text" placeholder="Lemari" class="w-1/3 border rounded-lg px-3 py-2">
              <input id="BAK_INPUT" type="text" placeholder="Baris" class="w-1/3 border rounded-lg px-3 py-2">
              <input id="ARSIP_INPUT" type="text" placeholder="Box" class="w-1/3 border rounded-lg px-3 py-2">
            </div>
            <input type="hidden" id="RAK_BAK_URUTAN" name="RAK_BAK_URUTAN">
          </div>

          <div class="col-span-2">
            <label class="block text-sm font-medium mb-1">Keterangan Simpan</label>
            <textarea id="KETERANGAN_SIMPAN" name="KETERANGAN_SIMPAN" class="w-full border rounded-lg px-3 py-2"></textarea>
          </div>

          <div class="relative">
            <label class="block text-sm font-medium mb-1">Tipe Retensi</label>
            <input id="TIPE_RETENSI" name="TIPE_RETENSI" class="w-full border rounded-lg px-3 py-2" autocomplete="off">
            <ul id="retensiSuggestions" class="absolute bg-white border border-gray-300 rounded-lg shadow-lg mt-1 w-full hidden z-50 max-h-60 overflow-y-auto"></ul>
          </div>

          <div>
            <label class="block text-sm font-medium mb-1">Tanggal Retensi</label>
            <input type="date" id="TANGGAL_RETENSI"  name="TANGGAL_RETENSI" class="w-full border rounded-lg px-3 py-2">
          </div>

          <div>
            <label class="block text-sm font-medium mb-1">Keterangan</label>
            <input id="KETERANGAN" name="KETERANGAN" class="w-full border rounded-lg px-3 py-2">
          </div>

          <div class="col-span-2">
            <label class="block text-sm font-medium mb-1">Di Buat Oleh</label>
            <input id="CREATE_BY" name="CREATE_BY" readonly class="w-full border rounded-lg px-3 py-2 bg-gray-100">
          </div>

          <div class="col-span-2">
            <label class="block text-sm font-medium mb-1">Upload File Arsip</label>
            <input type="file" id="FILE" name="FILE" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" class="w-full border rounded-lg px-3 py-2">
          </div>

          <div class="col-span-2 flex justify-end gap-2 mt-4">
            <button type="button" id="cancelBtn" name="cancleBtn "class="bg-gray-300 hover:bg-gray-400 text-gray-700 py-2 px-4 rounded-lg">Batal</button>
            <button type="submit" id="saveBtn" name="saveBtn" class="bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded-lg">Simpan</button>
          </div>
        </form>

          </div>
        </div>

        <!-- Toast -->
        <div id="toast" class="toast fixed top-4 right-4 bg-green-500 text-white px-4 py-3 rounded-lg shadow-lg z-50 max-w-xs sm:max-w-sm">
          <div class="flex items-center space-x-2">
            <i id="toastIcon" class="fas fa-check-circle"></i>
            <span id="toastMessage">Pesan berhasil</span>
          </div>
        </div>

<script>
const apiUrl = "/api/t_arsip";
const tableBody = document.getElementById("arsipTableBody");
const loadingState = document.getElementById("loadingState");
const emptyState = document.getElementById("emptyState");
const modal = document.getElementById("arsipModal");
const addBtn = document.getElementById("addArsipBtn");
const closeModal = document.getElementById("closeModal");
const cancelBtn = document.getElementById("cancelBtn");
const form = document.getElementById("arsipForm");
const toast = document.getElementById("toast");
const toastMessage = document.getElementById("toastMessage");
const token = localStorage.getItem("auth_token"); 
// notif
const notificationBtn = document.getElementById("notificationBtn");
const notificationDropdown = document.getElementById("notificationDropdown");
const notificationCount = document.getElementById("notificationCount");
const notificationList = document.getElementById("notificationList");
// indeks suggest
const indeksInput = document.getElementById("NO_INDEKS");
const suggestionBox = document.getElementById("indeksSuggestions");
// klasifikasi suggest
const klasifikasiInput = document.getElementById("KODE_KLASIFIKASI");
const suggestionKlasifikasi = document.getElementById("klasifikasiSuggestions");

// retensi suggest
const retensiInput = document.getElementById("TIPE_RETENSI");
const suggestionRetensi = document.getElementById("retensiSuggestions");

// kondisi select
const kondisiSelect = document.getElementById("KONDISI");

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


// === TOAST ===
function showToast(msg, success = true) {
  toastMessage.textContent = msg;
  toast.classList.remove("bg-red-500", "bg-green-500");
  toast.classList.add(success ? "bg-green-500" : "bg-red-500");
  toast.classList.add("show");
  setTimeout(() => toast.classList.remove("show"), 3000);
}

// === FUNGSI PEMBANTU FETCH (dengan header standar) ===
async function fetchWithAuth(url, options = {}) {
  const headers = options.headers || {};
  headers["Authorization"] = `Bearer ${token}`;
  headers["Accept"] = "application/json";

  // Hanya tambahkan Content-Type JSON kalau bukan FormData
  if (!(options.body instanceof FormData) && !headers["Content-Type"]) {
    headers["Content-Type"] = "application/json";
  }

  const res = await fetch(url, { ...options, headers });
  if (res.status === 401) {
    showToast("Token tidak valid atau sesi sudah berakhir", false);
    throw new Error("Unauthenticated");
  }
  return res;
}

// LOAD DATA USER
async function loadUserInfo() {
  try {
    const res = await fetchWithAuth('/api/me');
    if (!res.ok) throw new Error('Gagal ambil data user');

    const user = await res.json();

    // Isi field hidden ID
    document.getElementById("ID_DIVISI").value = user.ID_DIVISI ?? "";
    document.getElementById("ID_SUBDIVISI").value = user.ID_SUBDIVISI ?? "";

    // Isi field tampilan nama
    document.getElementById("DIVISI_NAME").value = user.divisi?.NAMA_DIVISI ?? "-";
    document.getElementById("SUBDIVISI_NAME").value = user.subdivisi?.NAMA_SUBDIVISI ?? "-";

    // Isi create_by sesuai nama user
    document.getElementById("CREATE_BY").value = user.username ?? "-";
  } catch (err) {
    console.error("Gagal memuat user info:", err);
    showToast("Gagal memuat data user", false);
  }
}

// === LOAD DATA WITH PAGINATION ===
async function loadArsip(keyword = "", page = 1) {
  loadingState.classList.remove("hidden");
  emptyState.classList.add("hidden");
  tableBody.innerHTML = "";
  paginationControls.classList.add("hidden");
  
  lastSearchKeyword = keyword;
  
  try {
    let url = `${apiUrl}?page=${page}&per_page=${perPage}`;
    if (keyword.trim()) url += `&search=${encodeURIComponent(keyword)}`;
    
    const res = await fetchWithAuth(url);
    const response = await res.json();
    
    loadingState.classList.add("hidden");
    
    const data = response.data || [];
    
    if (!Array.isArray(data) || data.length === 0) {
      emptyState.classList.remove("hidden");
      return;
    }
    
    // Render table rows
    data.forEach((arsip, i) => {
      const fileLink = arsip.FILE
        ? `<a href="/${arsip.FILE}" target="_blank" class="text-blue-600 underline">DOWNLOAD</a>`
        : "-";
      
      const rowNumber = ((response.current_page - 1) * perPage) + i + 1;
      
      const row = `
        <tr class="hover:bg-gray-50">
          <td class="px-4 py-3 w-[60px]">${rowNumber}</td>
          <td class="px-4 py-3 w-[150px]">${arsip.divisi?.NAMA_DIVISI ?? "-"}</td>
          <td class="px-4 py-3 w-[150px]">${arsip.subdivisi?.NAMA_SUBDIVISI ?? "-"}</td>
          <td class="px-4 py-3 w-[120px]">${arsip.NO_INDEKS ?? "-"}</td>
          <td class="px-4 py-3 w-[60px]">${arsip.NO_BERKAS ?? "-"}</td>
          <td class="px-4 py-3 w-[150px]">${arsip.JUDUL_BERKAS ?? "-"}</td>
          <td class="px-4 py-3 w-[60px]">${arsip.NO_ISI_BERKAS ?? "-"}</td>
          <td class="px-4 py-3 w-[60px]">${arsip.JENIS_ARSIP ?? "-"}</td>
          <td class="px-4 py-3 w-[150px]">${arsip.KODE_KLASIFIKASI ?? "-"}</td>
          <td class="px-4 py-3 w-[150px]">${arsip.NO_NOTA_DINAS ?? "-"}</td>
          <td class="px-4 py-3 w-[140px]">${arsip.TANGGAL_BERKAS ?? "-"}</td>
          <td class="px-4 py-3 w-[200px]">${arsip.PERIHAL ?? "-"}</td>
          <td class="px-4 py-3 w-[160px]">${arsip.TINGKAT_PENGEMBANGAN ?? "-"}</td>
          <td class="px-4 py-3 w-[120px]">${arsip.KONDISI ?? "-"}</td>
          <td class="px-4 py-3 w-[600px]">${arsip.RAK_BAK_URUTAN ?? "-"}</td>
          <td class="px-4 py-3 w-[150px]">${arsip.KETERANGAN_SIMPAN ?? "-"}</td>
          <td class="px-4 py-3 w-[150px]">${arsip.TIPE_RETENSI ?? "-"}</td>
          <td class="px-4 py-3 w-[140px]">${arsip.TANGGAL_RETENSI ?? "-"}</td>
          <td class="px-4 py-3 w-[150px]">${arsip.KETERANGAN ?? "-"}</td>
          <td class="px-4 py-3 w-[140px]">${arsip.CREATE_BY ?? "-"}</td>
          <td class="px-4 py-3 w-[120px]">${fileLink}</td>
          <td class="px-4 py-3 text-center space-x-2">
            <button onclick="editArsip(${arsip.ID_ARSIP})" class="text-blue-600 hover:text-blue-800"><i class="fas fa-edit"></i></button>
            <button onclick="deleteArsip(${arsip.ID_ARSIP})" class="text-red-600 hover:text-red-800"><i class="fas fa-trash"></i></button>
          </td>
        </tr>`;
      tableBody.insertAdjacentHTML("beforeend", row);
    });
    
    // Render pagination controls
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
    showToast("Gagal memuat data", false);
  }
}

// === PAGINATION RENDER ===
function renderPaginationControls(paginationData) {
  const { current_page, last_page, from, to, total } = paginationData;
  
  currentPage = current_page;
  totalPages = last_page;
  
  // Hide if no data
  if (total === 0) {
    paginationControls.classList.add("hidden");
    return;
  }
  
  paginationControls.classList.remove("hidden");
  
  // Update info text
  showingFrom.textContent = from || 0;
  showingTo.textContent = to || 0;
  totalRecords.textContent = total;
  
  // Enable/disable navigation buttons
  prevPageBtn.disabled = current_page === 1;
  nextPageBtn.disabled = current_page === last_page;

  
  // Generate page number buttons
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
    pageBtn.addEventListener("click", () => loadArsip(lastSearchKeyword, i));
    pageNumbers.appendChild(pageBtn);
  }
}

// === PAGINATION EVENT LISTENERS ===

prevPageBtn.addEventListener("click", () => {
  if (currentPage > 1) loadArsip(lastSearchKeyword, currentPage - 1);
});

nextPageBtn.addEventListener("click", () => {
  if (currentPage < totalPages) loadArsip(lastSearchKeyword, currentPage + 1);
});

perPageSelect.addEventListener("change", (e) => {
  perPage = parseInt(e.target.value);
  loadArsip(lastSearchKeyword, 1);
});

// === SEARCH WITH DEBOUNCE ===
document.getElementById("searchInput").addEventListener("input", (e) => {
  clearTimeout(window.searchDelay);
  window.searchDelay = setTimeout(() => {
    loadArsip(e.target.value, 1); // Reset to page 1 on new search
  }, 400);
});
// modal
addBtn.addEventListener("click", async () => {
  modal.classList.add("show");
  form.reset();
  document.getElementById("arsipId").value = "";
  document.getElementById("modalTitle").innerText = "Tambah Arsip";
  await loadUserInfo(); 
});


closeModal.addEventListener("click", () => modal.classList.remove("show"));
cancelBtn.addEventListener("click", () => modal.classList.remove("show"));

// === SIMPAN / UPDATE ===
form.addEventListener("submit", async (e) => {
  e.preventDefault();

  // lokasi simpan
  const rak = document.getElementById("RAK_INPUT").value.trim();
  const bak = document.getElementById("BAK_INPUT").value.trim();
  const arsip = document.getElementById("ARSIP_INPUT").value.trim();
  document.getElementById("RAK_BAK_URUTAN").value = `${rak}/${bak}/${arsip}`;

  const id = document.getElementById("arsipId").value;
  const method = "POST";
  const url = id ? `${apiUrl}/${id}?_method=PUT` : apiUrl;

  const formData = new FormData(form);
  formData.append("ID_DIVISI", document.getElementById("ID_DIVISI").value);
  formData.append("ID_SUBDIVISI", document.getElementById("ID_SUBDIVISI").value);
  formData.append("CREATE_BY", document.getElementById("CREATE_BY").value);

  try {
    const res = await fetchWithAuth(url, { method, body: formData });
    if (!res.ok) throw new Error("Gagal menyimpan data");

    modal.classList.remove("show");
    showToast("Data berhasil disimpan");
    loadArsip();
  } catch (err) {
    console.error(err);
    showToast("Gagal menyimpan data", false);
  }
});

// === EDIT ===
async function editArsip(id) {
  try {
    const res = await fetchWithAuth(`${apiUrl}/${id}`);
    const data = await res.json();

    modal.classList.add("show");
    document.getElementById("modalTitle").innerText = "Edit Arsip";
    document.getElementById("arsipId").value = data.ID_ARSIP ?? "";

    for (const key of [
      "NO_INDEKS","NO_BERKAS","JUDUL_BERKAS","NO_ISI_BERKAS","JENIS_ARSIP",
      "KODE_KLASIFIKASI","NO_NOTA_DINAS","TANGGAL_BERKAS","PERIHAL",
      "TINGKAT_PENGEMBANGAN","KONDISI","RAK_BAK_URUTAN","KETERANGAN_SIMPAN",
      "TIPE_RETENSI","TANGGAL_RETENSI","KETERANGAN"
    ]) {
      if (document.getElementById(key)) {
        document.getElementById(key).value = data[key] ?? "";
      }
    }
  } catch (err) {
    console.error(err);
    showToast("Gagal memuat data edit", false);
  }
}

// === HAPUS ===
async function deleteArsip(id) {
  if (!confirm("Yakin ingin menghapus arsip ini?")) return;

  try {
    const res = await fetchWithAuth(`${apiUrl}/${id}`, { method: "DELETE" });
    if (!res.ok) throw new Error("Gagal menghapus");
    showToast("Data berhasil dihapus");
    loadArsip();
  } catch (err) {
    console.error(err);
    showToast("Gagal menghapus data", false);
  }
}

// === PENCARIAN ===
document.getElementById("searchInput").addEventListener("input", (e) => {
  clearTimeout(window.searchDelay);
  window.searchDelay = setTimeout(() => loadArsip(e.target.value), 400);
});

// notif
async function loadOverdueNotifications() {
  try {
    const res = await fetchWithAuth(`${apiUrl}/overdue`);
    const data = await res.json();

    notificationCount.textContent = data.length;

    notificationList.innerHTML = '';
    if (data.length === 0) {
      notificationList.innerHTML = `<li class="p-3 text-gray-500 text-sm">Tidak ada arsip retensi lewat</li>`;
      return;
    }

    data.forEach(arsip => {
      const li = document.createElement('li');
      li.className = "p-3 hover:bg-red-500 cursor-pointer";
      li.innerHTML = `
        <div class="font-semibold text-white text-sm">${arsip.JUDUL_BERKAS ?? '-'}</div>
        <div class="text-xs text-white">Retensi: ${arsip.TANGGAL_RETENSI ?? '-'}</div>
      `;
      li.addEventListener('click', () => {
        modal.classList.add("show");
        editArsip(arsip.ID_ARSIP); // Buka modal edit untuk arsip tersebut
        notificationDropdown.classList.add('hidden');
      });
      notificationList.appendChild(li);
    });
  } catch (err) {
    console.error(err);
    showToast("Gagal memuat notifikasi", false);
  }
}

// Toggle dropdown
notificationBtn.addEventListener("click", () => { notificationDropdown.classList.toggle("hidden"); });

// indeks
let indeksData = [];
async function loadIndeksData() {
  try {
    const res = await fetchWithAuth("/api/m_indeks/all");
    if (!res.ok) throw new Error("Gagal memuat data indeks");
    indeksData = await res.json();
  } catch (err) {
    console.error("Gagal ambil data indeks:", err);
  }
}

// Tampilkan suggestion berdasarkan ketikan
indeksInput.addEventListener("input", () => {
  const query = indeksInput.value.toLowerCase();
  suggestionBox.innerHTML = "";

  if (!query.trim()) {
    suggestionBox.classList.add("hidden");
    return;
  }

  const filtered = indeksData.filter(item =>
    (item.NO_INDEKS?.toLowerCase().includes(query) ||
     item.NAMA_INDEKS?.toLowerCase().includes(query))
  );

  if (filtered.length === 0) {
    suggestionBox.classList.add("hidden");
    return;
  }

  filtered.slice(0, 50).forEach(item => {
    const li = document.createElement("li");
    li.className = "px-3 py-2 hover:bg-blue-100 cursor-pointer text-sm";
    li.innerHTML = `<strong>${item.NO_INDEKS}</strong> - ${item.NAMA_INDEKS}`;
    li.addEventListener("click", () => {
      indeksInput.value = item.NO_INDEKS;
      suggestionBox.classList.add("hidden");
    });
    suggestionBox.appendChild(li);
  });

  suggestionBox.classList.remove("hidden");
});


document.addEventListener("click", (e) => {
  if (!suggestionBox.contains(e.target) && e.target !== indeksInput) {
    suggestionBox.classList.add("hidden");
  }
});

// === RETENSI AUTOCOMPLETE ===
let retensiData = [];

// Ambil semua data retensi
async function loadRetensiData() {
  try {
    const res = await fetchWithAuth("/api/m_retensi/all");
    if (!res.ok) throw new Error("Gagal memuat data retensi");
    retensiData = await res.json();
  } catch (err) {
    console.error("Gagal ambil data retensi:", err);
  }
}

// Tampilkan suggestion berdasarkan ketikan
retensiInput.addEventListener("input", () => {
  const query = retensiInput.value.toLowerCase();
  suggestionRetensi.innerHTML = "";

  if (!query.trim()) {
    suggestionRetensi.classList.add("hidden");
    return;
  }

  const filtered = retensiData.filter(item =>
    (item.JENIS_ARSIP?.toLowerCase().includes(query) ||
     item.BIDANG_ARSIP?.toLowerCase().includes(query) ||
     item.TIPE_ARSIP?.toLowerCase().includes(query) ||
     item.DETAIL_TIPE_ARSIP?.toLowerCase().includes(query) ||
     item.MASA_AKTIF?.toLowerCase().includes(query))
  );

  if (filtered.length === 0) {
    suggestionRetensi.classList.add("hidden");
    return;
  }

  filtered.slice(0, 50).forEach(item => {
    const li = document.createElement("li");
    li.className = "px-3 py-2 hover:bg-blue-100 cursor-pointer text-sm border-b last:border-b-0";
    
    // Format tampilan suggestion dengan semua field
    let displayText = '';
    if (item.JENIS_ARSIP) displayText += `<div class="font-semibold text-blue-600">${item.JENIS_ARSIP}</div>`;
    if (item.BIDANG_ARSIP) displayText += `<div class="text-gray-700">Bidang: ${item.BIDANG_ARSIP}</div>`;
    if (item.TIPE_ARSIP) displayText += `<div class="text-gray-600">Tipe: ${item.TIPE_ARSIP}</div>`;
    if (item.DETAIL_TIPE_ARSIP) displayText += `<div class="text-gray-600">Detail: ${item.DETAIL_TIPE_ARSIP}</div>`;
    if (item.MASA_AKTIF) displayText += `<div class="text-green-600 font-medium">Masa Aktif: ${item.MASA_AKTIF}</div>`;
    
    li.innerHTML = displayText;
    
    li.addEventListener("click", () => {
      // Isi field TIPE_RETENSI dengan MASA_AKTIF
      retensiInput.value = item.MASA_AKTIF || '';
      suggestionRetensi.classList.add("hidden");
    });
    suggestionRetensi.appendChild(li);
  });

  suggestionRetensi.classList.remove("hidden");
});

// Tutup dropdown saat klik di luar
document.addEventListener("click", (e) => {
  if (!suggestionRetensi.contains(e.target) && e.target !== retensiInput) {
    suggestionRetensi.classList.add("hidden");
  }
});

let klasifikasiData = [];

// Ambil semua data klasifikasi
async function loadKlasifikasiData() {
  try {
    const res = await fetchWithAuth("/api/m_klasifikasi/all");
    if (!res.ok) throw new Error("Gagal memuat data klasifikasi");
    klasifikasiData = await res.json();
  } catch (err) {
    console.error("Gagal ambil data klasifikasi:", err);
  }
}

// Tampilkan suggestion berdasarkan ketikan
klasifikasiInput.addEventListener("input", () => {
  const query = klasifikasiInput.value.toLowerCase();
  suggestionKlasifikasi.innerHTML = "";

  if (!query.trim()) {
    suggestionKlasifikasi.classList.add("hidden");
    return;
  }

  const filtered = klasifikasiData.filter(item =>
    (item.KODE_KLASIFIKASI?.toLowerCase().includes(query) ||
     item.DESKRIPSI?.toLowerCase().includes(query))
  );

  if (filtered.length === 0) {
    suggestionKlasifikasi.classList.add("hidden");
    return;
  }

  filtered.slice(0, 50).forEach(item => {
    const li = document.createElement("li");
    li.className = "px-3 py-2 hover:bg-blue-100 cursor-pointer text-sm";
    li.innerHTML = `<strong>${item.KODE_KLASIFIKASI}</strong> - ${item.DESKRIPSI}`;
    li.addEventListener("click", () => {
      klasifikasiInput.value = item.KODE_KLASIFIKASI;
      suggestionKlasifikasi.classList.add("hidden");
    });
    suggestionKlasifikasi.appendChild(li);
  });

  suggestionKlasifikasi.classList.remove("hidden");
});


document.addEventListener("click", (e) => {
  if (!suggestionKlasifikasi.contains(e.target) && e.target !== klasifikasiInput) {
    suggestionKlasifikasi.classList.add("hidden");
  }
});

// === KONDISI DROPDOWN ===
async function loadKondisiData() {
  try {
    const res = await fetchWithAuth("/api/m_kondisi/all", {
      method: "GET"  // ← Tambahkan ini
    });
    if (!res.ok) {
      console.error("Response status:", res.status); // ← Tambahkan log
      throw new Error("Gagal memuat data kondisi");
    }
    kondisiData = await res.json();
    
    // Populate select options
    kondisiSelect.innerHTML = '<option value="">-- Pilih Kondisi --</option>';
    kondisiData.forEach(item => {
      const option = document.createElement("option");
      option.value = item.NAMA_KONDISI;
      option.textContent = item.NAMA_KONDISI;
      kondisiSelect.appendChild(option);
    });
  } catch (err) {
    console.error("Gagal ambil data kondisi:", err);
    console.error("Full error:", err.message); // ← Tambahkan log detail
  }
}

//  pagination
    function renderPaginationControls(paginationData) {
      const { current_page, last_page, from, to, total } = paginationData;
      
      currentPage = current_page;
      totalPages = last_page;
      
      if (total > 0) {
        paginationControls.classList.remove("hidden");
      } else {
        paginationControls.classList.add("hidden");
        return;
      }
      
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
        pageBtn.addEventListener("click", () => loadArsip(lastSearchKeyword, i));
        pageNumbers.appendChild(pageBtn);
      }
    }



// Loader
loadRetensiData();
loadOverdueNotifications();
loadKlasifikasiData();
loadIndeksData();
loadKondisiData();
loadArsip();
</script>


</body>
</html>
