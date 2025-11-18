<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pelindo Multi Terminal - Retensi Management</title>
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
<body class="bg-gray-100 min-h-screen flex flex-col">

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

<main class="container mx-auto px-3 md:px-6 py-6 flex-1">

    <!-- Controls -->
    <div class="bg-white rounded-lg shadow-lg p-3 md:p-4 mb-6 flex flex-col md:flex-row justify-between items-start md:items-center space-y-3 md:space-y-0">
        <div class="flex flex-wrap items-center gap-3">
            <button id="addRetensiBtn" class="bg-blue-600 hover:bg-blue-700 text-white px-3 md:px-4 py-2 rounded-lg flex items-center space-x-2 text-sm md:text-base">
                <i class="fas fa-plus"></i> <span>Tambah Retensi</span>
            </button>

            <a href="{{ url('api/retensi/export') }}" id="exportBtn" 
                class="bg-green-600 hover:bg-green-700 text-white px-3 md:px-4 py-2 rounded-lg flex items-center space-x-2 text-sm md:text-base">
                <span>Export Excel</span> <i class="fas fa-file-excel"></i>
            </a>
        </div>

        <input id="searchInput" type="text" placeholder="Cari..." 
            class="border rounded-lg px-3 py-2 w-full md:w-64 focus:ring-2 focus:ring-blue-500 focus:outline-none" />
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

    <!-- Table -->
    <div class="bg-white rounded-lg shadow-sm overflow-x-auto">
        <table class="w-full min-w-[900px]">
            <thead class="bg-blue-600 text-white text-sm md:text-base">
                <tr>
                    <th class="px-4 md:px-6 py-3 text-left font-medium">NO</th>
                    <th class="px-4 md:px-6 py-3 text-left font-medium">Jenis Arsip</th>
                    <th class="px-4 md:px-6 py-3 text-left font-medium">Bidang Arsip</th>
                    <th class="px-4 md:px-6 py-3 text-left font-medium">Tipe Arsip</th>
                    <th class="px-4 md:px-6 py-3 text-left font-medium">Detail Tipe Arsip</th>
                    <th class="px-4 md:px-6 py-3 text-left font-medium">Masa Aktif</th>
                    <th class="px-4 md:px-6 py-3 text-left font-medium">Masa Inaktif</th>
                    <th class="px-4 md:px-6 py-3 text-left font-medium">Keterangan</th>
                    <th class="px-4 md:px-6 py-3 text-left font-medium">Dibuat Oleh</th>
                    <th class="px-4 md:px-6 py-3 text-center font-medium">Aksi</th>
                </tr>
            </thead>
            <tbody id="retensiTableBody" class="divide-y divide-gray-200 text-sm md:text-base"></tbody>
        </table>
    </div>

    <!-- States -->
    <div id="loadingState" class="text-center py-8">
        <i class="fas fa-spinner fa-spin text-2xl text-blue-600"></i>
        <p class="mt-2 text-gray-600">Memuat data...</p>
    </div>

    <div id="emptyState" class="text-center py-8 hidden">
        <i class="fas fa-inbox text-4xl text-gray-400 mb-4"></i>
        <p class="text-gray-600">Tidak ada data retensi</p>
    </div>
</main>

<!-- Pagination Controls -->
<div id="paginationControls" class="mt-1 mb-4 hidden">
    <div class="flex flex-col items-start mx-[100px]">
        <!-- Pagination Buttons -->
        <div class="flex items-center gap-2 mb-2">
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
</div>

<!-- Modal -->
<div id="retensiModal" class="modal fixed inset-0 bg-black bg-opacity-50 items-center justify-center z-50 px-2">
    <div class="bg-white rounded-lg p-5 md:p-8 w-full max-w-md md:max-w-4xl mx-auto max-h-[90vh] overflow-y-auto">

        <!-- Header -->
        <div class="flex items-center justify-between mb-4">
            <h3 id="modalTitle" class="text-lg md:text-xl font-semibold">Tambah Retensi</h3>
            <button id="closeModal" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>

        <!-- Form -->
        <form id="retensiForm" class="bg-white rounded-xl space-y-5">
            <input type="hidden" id="retensiId">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Jenis Arsip</label>
                    <input type="text" id="jenisArsip" required class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Bidang Arsip</label>
                    <input type="text" id="bidangArsip" required class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tipe Arsip</label>
                    <input type="text" id="tipeArsip" required class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Detail Tipe Arsip</label>
                    <input type="text" id="detailTipeArsip" required class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Masa Aktif (tahun)</label>
                    <input type="text" id="masaAktif" required class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Deskripsi Aktif</label>
                    <input type="text" id="descAktif" class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Masa Inaktif (tahun)</label>
                    <input type="text" id="masaInaktif" required class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Deskripsi Inaktif</label>
                    <input type="text" id="descInaktif" class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Keterangan</label>
                <textarea id="keterangan" rows="3" class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none"></textarea>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Dibuat Oleh</label>
                <input type="text" id="createBy" readonly class="w-full border rounded-lg px-3 py-2 bg-gray-100 text-gray-600">
            </div>

            <div id="formErrors" class="text-red-600 text-sm hidden"></div>

            <div class="flex flex-col md:flex-row gap-3 mt-4">
                <button type="button" id="cancelBtn" class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-700 py-2 rounded-lg">Batal</button>
                <button type="submit" id="saveBtn" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white py-2 rounded-lg">Simpan</button>
            </div>
        </form>
        <div class="border-t mt-6 pt-4">
            <h4 class="text-md font-semibold mb-3">Tambah Data dengan Import Excel</h4>
            <a href="{{ url('/api/retensi/export-template') }}" id="templateBtn" 
                class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg flex items-center justify-center space-x-2 mb-3">
                <span>Download Template</span> <i class="fas fa-download"></i>
            </a>
            <form id="importRetensiForm" class="flex flex-col md:flex-row items-start md:items-center gap-2">
                <input type="file" name="file" id="importRetensiFile" class="border px-2 py-1 rounded" required>
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
const apiUrl = "/api/m_retensi";
const tableBody = document.getElementById("retensiTableBody");
const loadingState = document.getElementById("loadingState");
const emptyState = document.getElementById("emptyState");
const modal = document.getElementById("retensiModal");
const addBtn = document.getElementById("addRetensiBtn");
const closeModal = document.getElementById("closeModal");
const cancelBtn = document.getElementById("cancelBtn");
const form = document.getElementById("retensiForm");
const formErrors = document.getElementById("formErrors");
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

// ==== Fetch Data with Pagination ====
async function loadRetensi(keyword = "", page = 1) {
    loadingState.classList.remove("hidden");
    emptyState.classList.add("hidden");
    tableBody.innerHTML = "";
    paginationControls.classList.add("hidden");
    
    lastSearchKeyword = keyword;

    try {
        let url = `${apiUrl}?page=${page}&per_page=${perPage}`;
        if (keyword && keyword.trim() !== "") {
            url += `&search=${encodeURIComponent(keyword)}`;
        }

        let res = await fetch(url, {
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
            
            const row = `
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3">${rowNumber}</td>
                    <td class="px-4 py-3">${item.JENIS_ARSIP}</td>
                    <td class="px-4 py-3">${item.BIDANG_ARSIP}</td>
                    <td class="px-4 py-3 w-64 whitespace-normal break-words">${item.TIPE_ARSIP}</td>
                    <td class="px-4 py-3 w-64 whitespace-normal break-words">${item.DETAIL_TIPE_ARSIP}</td>
                    <td class="px-4 py-3 w-24 text-center whitespace-nowrap">${item.MASA_AKTIF}</td>
                    <td class="px-4 py-3 w-24 text-center whitespace-nowrap">${item.MASA_INAKTIF}</td>
                    <td class="px-4 py-3 whitespace-normal break-words max-w-xs">${item.KETERANGAN ?? '-'}</td>
                    <td class="px-4 py-3">${item.CREATE_BY ?? '-'}</td>
                    <td class="px-4 py-3 text-center space-x-2">
                        <button onclick="editRetensi(${item.ID_RETENSI})" class="text-blue-600 hover:text-blue-800"><i class="fas fa-edit"></i></button>
                        <button onclick="deleteRetensi(${item.ID_RETENSI})" class="text-red-600 hover:text-red-800"><i class="fas fa-trash"></i></button>
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

    } catch(err) {
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
        pageBtn.addEventListener("click", () => loadRetensi(lastSearchKeyword, i));
        pageNumbers.appendChild(pageBtn);
    }
}

// ===== PAGINATION EVENT LISTENERS =====
prevPageBtn.addEventListener("click", () => {
    if (currentPage > 1) loadRetensi(lastSearchKeyword, currentPage - 1);
});

nextPageBtn.addEventListener("click", () => {
    if (currentPage < totalPages) loadRetensi(lastSearchKeyword, currentPage + 1);
});

perPageSelect.addEventListener("change", (e) => {
    perPage = parseInt(e.target.value);
    loadRetensi(lastSearchKeyword, 1);
});

// ==== Load Username ====
async function loadUsername() {
    try {
        const res = await fetch('/api/me', {
            headers: { 'Authorization': `Bearer ${token}` }
        });
        if (!res.ok) throw new Error();
        const data = await res.json();
        document.getElementById('createBy').value = data.username || '';
    } catch {
        console.warn("Gagal memuat username");
    }
}

// ==== Modal ====
addBtn.addEventListener("click", () => {
    modal.classList.add("show");
    document.getElementById("modalTitle").innerText = "Tambah Retensi";
    form.reset();
    formErrors.classList.add("hidden");
    document.getElementById("retensiId").value = "";
    loadUsername();
});
closeModal.addEventListener("click", () => modal.classList.remove("show"));
cancelBtn.addEventListener("click", () => modal.classList.remove("show"));

// ==== Submit ====
form.addEventListener("submit", async e => {
    e.preventDefault();
    const id = document.getElementById("retensiId").value;

    const payload = {
        JENIS_ARSIP: jenisArsip.value,
        BIDANG_ARSIP: bidangArsip.value,
        TIPE_ARSIP: tipeArsip.value,
        DETAIL_TIPE_ARSIP: detailTipeArsip.value,
        MASA_AKTIF: masaAktif.value,
        DESC_AKTIF: descAktif.value,
        MASA_INAKTIF: masaInaktif.value,
        DESC_INAKTIF: descInaktif.value,
        KETERANGAN: keterangan.value,
        CREATE_BY: createBy.value
    };

    try {
        const res = await fetch(id ? `${apiUrl}/${id}` : apiUrl, {
            method: id ? "PUT" : "POST",
            headers: {
                'Content-Type': 'application/json',
                'Authorization': `Bearer ${token}`
            },
            body: JSON.stringify(payload)
        });

        if (res.ok) {
            showToast("Data berhasil disimpan");
            modal.classList.remove("show");
            loadRetensi(lastSearchKeyword, currentPage);
        } else if (res.status === 422) {
            const err = await res.json();
            const msgs = [];
            Object.values(err.errors || {}).forEach(arr => msgs.push(...arr));
            formErrors.innerText = msgs.join('\n');
            formErrors.classList.remove("hidden");
        } else {
            showToast("Gagal menyimpan data");
        }
    } catch (error) {
        console.error(error);
        showToast("Terjadi kesalahan");
    }
});

// ==== Edit ====
async function editRetensi(id) {
    try {
        const res = await fetch(`${apiUrl}/${id}`, {
            headers: { 'Authorization': `Bearer ${token}` }
        });
        if (!res.ok) throw new Error("Data tidak ditemukan");

        const data = await res.json();

        modal.classList.add("show");
        document.getElementById("modalTitle").innerText = "Edit Retensi";

        retensiId.value = data.ID_RETENSI;
        jenisArsip.value = data.JENIS_ARSIP ?? "";
        bidangArsip.value = data.BIDANG_ARSIP ?? "";
        tipeArsip.value = data.TIPE_ARSIP ?? "";
        detailTipeArsip.value = data.DETAIL_TIPE_ARSIP ?? "";
        masaAktif.value = data.MASA_AKTIF ?? "";
        descAktif.value = data.DESC_AKTIF ?? "";
        masaInaktif.value = data.MASA_INAKTIF ?? "";
        descInaktif.value = data.DESC_INAKTIF ?? "";
        keterangan.value = data.KETERANGAN ?? "";
        createBy.value = data.CREATE_BY ?? "";
    } catch (error) {
        console.error(error);
        showToast("Gagal memuat data edit");
    }
}

// ==== Delete ====
async function deleteRetensi(id) {
    if (!confirm("Yakin ingin menghapus data ini?")) return;
    try {
        const res = await fetch(`${apiUrl}/${id}`, {
            method: "DELETE",
            headers: { 'Authorization': `Bearer ${token}` }
        });
        showToast(res.ok ? "Data berhasil dihapus" : "Gagal menghapus data");
        loadRetensi(lastSearchKeyword, currentPage);
    } catch {
        showToast("Terjadi kesalahan saat menghapus");
    }
}

// ==== Import Excel ====
document.getElementById("importRetensiForm").addEventListener("submit", async function(e) {
    e.preventDefault();

    const fileInput = document.getElementById("importRetensiFile").files[0];
    if (!fileInput) {
        showToast("Pilih file terlebih dahulu");
        return;
    }

    const formData = new FormData();
    formData.append("file", fileInput);

    try {
        const res = await fetch("/api/retensi/import", {
            method: "POST",
            headers: {
                'Authorization': `Bearer ${token}`
            },
            body: formData
        });

        const data = await res.json();

        if (res.ok) {
            showToast(data.message || "Data retensi berhasil diimport");
            modal.classList.remove("show");
            loadRetensi(lastSearchKeyword, currentPage);
        } else {
            showToast(data.message || "Gagal import data retensi");
        }
    } catch (err) {
        console.error(err);
        showToast("Terjadi error saat import data retensi");
    }
});

// ==== Toast ====
function showToast(msg) {
    toastMessage.innerText = msg;
    toast.classList.add("show");
    setTimeout(() => toast.classList.remove("show"), 3000);
}

// ==== Search with Debounce ====
let searchTimeout;
document.getElementById("searchInput").addEventListener("input", function() {
    clearTimeout(searchTimeout);
    const keyword = this.value;
    searchTimeout = setTimeout(() => loadRetensi(keyword, 1), 500);
});

loadRetensi();
</script>

</body>
</html>