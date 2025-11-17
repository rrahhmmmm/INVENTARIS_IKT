<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pelindo Multi Terminal - Indeks Management</title>
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
            <button id="addIndeksBtn" class="bg-blue-600 hover:bg-blue-700 text-white px-3 md:px-4 py-2 rounded-lg flex items-center space-x-2 text-sm md:text-base">
                <i class="fas fa-plus"></i> <span>Tambah Indeks</span>
            </button>

            <!-- export excel -->
            <a href="{{ url('api/indeks/export') }}" id="exportBtn" 
                class="bg-green-600 hover:bg-green-700 text-white px-3 md:px-4 py-2 rounded-lg flex items-center space-x-2 text-sm md:text-base">
                <span>Export Excel</span> <i class="fas fa-file-excel"></i>
            </a>
        </div>

        <input id="searchInput" type="text" placeholder="Cari No Indeks / Wilayah / Nama Indeks..." 
            class="border rounded-lg px-3 py-2 w-full md:w-72 focus:ring-2 focus:ring-blue-500 focus:outline-none" />
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
        <table class="w-full min-w-[800px]">
            <thead class="bg-blue-600 text-white text-sm md:text-base">
                <tr>
                    <th class="px-4 py-3 text-left font-medium">NO</th>
                    <th class="px-4 py-3 text-left font-medium">No Indeks</th>
                    <th class="px-4 py-3 text-left font-medium">Wilayah</th>
                    <th class="px-4 py-3 text-left font-medium">Nama Indeks</th>
                    <th class="px-4 py-3 text-left font-medium">Start Date</th>
                    <th class="px-4 py-3 text-left font-medium">End Date</th>
                    <th class="px-4 py-3 text-left font-medium">Dibuat Oleh</th>
                    <th class="px-4 py-3 text-center font-medium">Aksi</th>
                </tr>
            </thead>
            <tbody id="indeksTableBody" class="divide-y divide-gray-200 text-sm md:text-base"></tbody>
        </table>
    </div>

    <div id="loadingState" class="text-center py-8">
        <i class="fas fa-spinner fa-spin text-2xl text-blue-600"></i>
        <p class="mt-2 text-gray-600">Memuat data...</p>
    </div>

    <div id="emptyState" class="text-center py-8 hidden">
        <i class="fas fa-inbox text-4xl text-gray-400 mb-4"></i>
        <p class="text-gray-600">Tidak ada data indeks</p>
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
<div id="indeksModal" class="modal fixed inset-0 bg-black bg-opacity-50 items-center justify-center z-50 px-2">
    <div class="bg-white rounded-lg p-5 md:p-8 w-full max-w-md md:max-w-3xl mx-auto max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between mb-4">
            <h3 id="modalTitle" class="text-lg md:text-xl font-semibold">Tambah Indeks</h3>
            <button id="closeModal" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>

        <form id="indeksForm" class="bg-white rounded-xl space-y-5">
            <input type="hidden" id="indeksId">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">No Indeks</label>
                    <input type="text" id="noIndeks" required 
                        class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Wilayah</label>
                    <input type="text" id="wilayah"
                        class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nama Indeks</label>
                    <input type="text" id="namaIndeks"
                        class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Start Date</label>
                    <input type="date" id="startDate"
                        class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">End Date</label>
                    <input type="date" id="endDate"
                        class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Dibuat Oleh</label>
                <input type="text" id="createBy" readonly
                    class="w-full border rounded-lg px-3 py-2 bg-gray-100 text-gray-600">
            </div>

            <div id="formErrors" class="text-red-600 text-sm hidden"></div>

            <div class="flex flex-col md:flex-row gap-3 mt-4">
                <button type="button" id="cancelBtn" class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-700 py-2 rounded-lg">Batal</button>
                <button type="submit" id="saveBtn" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white py-2 rounded-lg">Simpan</button>
            </div>
        </form>
        <div class="border-t mt-6 pt-4">
            <h4 class="text-md font-semibold mb-3">Tambah Data dengan Import Excel</h4>
            <a href="{{ url('/api/indeks/export-template') }}" id="templateBtn" 
                class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg flex items-center justify-center space-x-2 mb-3">
                <span>Download Template</span> <i class="fas fa-download"></i>
            </a>
            <form id="importForm" class="flex flex-col md:flex-row items-start md:items-center gap-2">
                <input type="file" name="file" id="importFile" class="border px-2 py-1 rounded" required>
                <button type="submit" 
                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">
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
const apiUrl = "/api/m_indeks";
const token = localStorage.getItem('auth_token');
const tableBody = document.getElementById("indeksTableBody");
const modal = document.getElementById("indeksModal");
const form = document.getElementById("indeksForm");
const formErrors = document.getElementById("formErrors");
const toast = document.getElementById("toast");
const toastMessage = document.getElementById("toastMessage");
const loadingState = document.getElementById("loadingState");
const emptyState = document.getElementById("emptyState");

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

// ===== LOAD DATA WITH PAGINATION =====
async function loadIndeks(keyword = "", page = 1) {
    loadingState.classList.remove("hidden");
    emptyState.classList.add("hidden");
    tableBody.innerHTML = "";
    paginationControls.classList.add("hidden");
    
    lastSearchKeyword = keyword;

    try {
        let url = `${apiUrl}?page=${page}&per_page=${perPage}`;
        if (keyword.trim()) url += `&search=${encodeURIComponent(keyword)}`;

        let res = await fetch(url, {
            headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' }
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
                    <td class="px-6 py-4">${rowNumber}</td>
                    <td class="px-6 py-4">${item.NO_INDEKS}</td>
                    <td class="px-6 py-4">${item.WILAYAH ?? '-'}</td>
                    <td class="px-6 py-4">${item.NAMA_INDEKS ?? '-'}</td>
                    <td class="px-6 py-4">${item.START_DATE ?? '-'}</td>
                    <td class="px-6 py-4">${item.END_DATE ?? '-'}</td>
                    <td class="px-6 py-4">${item.CREATE_BY ?? '-'}</td>
                    <td class="px-6 py-4 text-center space-x-2">
                        <button onclick="editIndeks(${item.ID_INDEKS})" class="text-blue-600 hover:text-blue-800"><i class="fas fa-edit"></i></button>
                        <button onclick="deleteIndeks(${item.ID_INDEKS})" class="text-red-600 hover:text-red-800"><i class="fas fa-trash"></i></button>
                    </td>
                </tr>
            `;
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
        pageBtn.addEventListener("click", () => loadIndeks(lastSearchKeyword, i));
        pageNumbers.appendChild(pageBtn);
    }
}

// ===== PAGINATION EVENT LISTENERS =====
prevPageBtn.addEventListener("click", () => {
    if (currentPage > 1) loadIndeks(lastSearchKeyword, currentPage - 1);
});

nextPageBtn.addEventListener("click", () => {
    if (currentPage < totalPages) loadIndeks(lastSearchKeyword, currentPage + 1);
});

perPageSelect.addEventListener("change", (e) => {
    perPage = parseInt(e.target.value);
    loadIndeks(lastSearchKeyword, 1);
});

// ===== LOAD USERNAME =====
async function loadUsername() {
    try {
        const res = await fetch('/api/me', {
            headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' }
        });
        const data = await res.json();
        document.getElementById('createBy').value = data.username || '';
    } catch (err) {
        console.error(err);
    }
}

// ===== MODAL ACTIONS =====
document.getElementById("addIndeksBtn").addEventListener("click", () => {
    modal.classList.add("show");
    form.reset();
    document.getElementById("modalTitle").innerText = "Tambah Indeks";
    document.getElementById("indeksId").value = "";
    formErrors.classList.add("hidden");
    loadUsername();
});
document.getElementById("closeModal").addEventListener("click", () => modal.classList.remove("show"));
document.getElementById("cancelBtn").addEventListener("click", () => modal.classList.remove("show"));

// ===== SAVE =====
form.addEventListener("submit", async function(e) {
    e.preventDefault();
    formErrors.classList.add("hidden");

    const id = document.getElementById("indeksId").value;
    const payload = {
        NO_INDEKS: document.getElementById("noIndeks").value,
        WILAYAH: document.getElementById("wilayah").value,
        NAMA_INDEKS: document.getElementById("namaIndeks").value,
        START_DATE: document.getElementById("startDate").value,
        END_DATE: document.getElementById("endDate").value,
        CREATE_BY: document.getElementById("createBy").value
    };

    try {
        let res;
        if (id) {
            res = await fetch(`${apiUrl}/${id}`, {
                method: "PUT",
                headers: { 'Content-Type': 'application/json', 'Authorization': `Bearer ${token}` },
                body: JSON.stringify(payload)
            });
        } else {
            res = await fetch(apiUrl, {
                method: "POST",
                headers: { 'Content-Type': 'application/json', 'Authorization': `Bearer ${token}` },
                body: JSON.stringify(payload)
            });
        }

        if (res.ok) {
            showToast("Data berhasil disimpan");
            modal.classList.remove("show");
            loadIndeks(lastSearchKeyword, currentPage);
        } else if (res.status === 422) {
            const err = await res.json();
            const messages = Object.values(err.errors || {}).flat().join("\n");
            formErrors.innerText = messages;
            formErrors.classList.remove("hidden");
        } else {
            showToast("Gagal menyimpan data");
        }
    } catch (err) {
        console.error(err);
        showToast("Terjadi kesalahan");
    }
});

// ===== EDIT =====
async function editIndeks(id) {
    try {
        const res = await fetch(`${apiUrl}/${id}`, {
            headers: { 'Authorization': `Bearer ${token}` }
        });
        const data = await res.json();

        modal.classList.add("show");
        document.getElementById("modalTitle").innerText = "Edit Indeks";

        document.getElementById("indeksId").value = data.ID_INDEKS;
        document.getElementById("noIndeks").value = data.NO_INDEKS;
        document.getElementById("wilayah").value = data.WILAYAH ?? '';
        document.getElementById("namaIndeks").value = data.NAMA_INDEKS ?? '';
        document.getElementById("startDate").value = data.START_DATE ?? '';
        document.getElementById("endDate").value = data.END_DATE ?? '';
        document.getElementById("createBy").value = data.CREATE_BY ?? '';
    } catch (err) {
        console.error(err);
        showToast("Gagal memuat data edit");
    }
}

// ===== DELETE =====
async function deleteIndeks(id) {
    if (!confirm("Yakin ingin menghapus data ini?")) return;
    try {
        const res = await fetch(`${apiUrl}/${id}`, {
            method: "DELETE",
            headers: { 'Authorization': `Bearer ${token}` }
        });
        if (res.ok) {
            showToast("Data berhasil dihapus");
            loadIndeks(lastSearchKeyword, currentPage);
        } else {
            showToast("Gagal menghapus data");
        }
    } catch (err) {
        console.error(err);
        showToast("Terjadi error saat hapus");
    }
}

// ===== SEARCH WITH DEBOUNCE =====
document.getElementById("searchInput").addEventListener("input", function() {
    clearTimeout(window.searchDelay);
    const keyword = this.value;
    window.searchDelay = setTimeout(() => loadIndeks(keyword, 1), 500);
});

// ===== IMPORT =====
document.getElementById("importForm").addEventListener("submit", async function(e) {
    e.preventDefault();
    const fileInput = document.getElementById("importFile").files[0];
    if (!fileInput) { showToast("Pilih file terlebih dahulu"); return; }

    const formData = new FormData();
    formData.append("file", fileInput);

    try {
        const res = await fetch("/api/indeks/import", {
            method: "POST",
            headers: { 'Authorization': `Bearer ${token}` },
            body: formData
        });
        const data = await res.json();
        if (res.ok) {
            showToast(data.message || "Data berhasil diimport");
            modal.classList.remove("show");
            loadIndeks(lastSearchKeyword, currentPage);
        } else {
            showToast(data.message || "Gagal import data");
        }
    } catch(err) {
        console.error(err);
        showToast("Terjadi error saat import");
    }
});

// ===== TOAST =====
function showToast(msg) {
    toastMessage.innerText = msg;
    toast.classList.add("show");
    setTimeout(() => toast.classList.remove("show"), 3000);
}

loadIndeks();
</script>
</body>
</html>