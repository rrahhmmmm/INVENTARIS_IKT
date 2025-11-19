<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pelindo Multi Terminal - Jenis Naskah Management</title>
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
            <button id="addJenisNaskahBtn" class="bg-blue-600 hover:bg-blue-700 text-white px-3 md:px-4 py-2 rounded-lg flex items-center space-x-2 text-sm md:text-base">
                <i class="fas fa-plus"></i> <span>Tambah Jenis Naskah</span>
            </button>
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
        <table class="w-full min-w-[700px]">
            <thead class="bg-blue-600 text-white text-sm md:text-base">
                <tr>
                    <th class="px-4 md:px-6 py-3 text-left font-medium">NO</th>
                    <th class="px-4 md:px-6 py-3 text-left font-medium">Nama Jenis Naskah</th>
                    <th class="px-4 md:px-6 py-3 text-left font-medium">Dibuat Oleh</th>
                    <th class="px-4 md:px-6 py-3 text-center font-medium">Aksi</th>
                </tr>
            </thead>
            <tbody id="jenisNaskahTableBody" class="divide-y divide-gray-200 text-sm md:text-base"></tbody>
        </table>
    </div>

    <!-- States -->
    <div id="loadingState" class="text-center py-8">
        <i class="fas fa-spinner fa-spin text-2xl text-blue-600"></i>
        <p class="mt-2 text-gray-600">Memuat data...</p>
    </div>

    <div id="emptyState" class="text-center py-8 hidden">
        <i class="fas fa-inbox text-4xl text-gray-400 mb-4"></i>
        <p class="text-gray-600">Tidak ada data jenis naskah</p>
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
<div id="jenisNaskahModal" class="modal fixed inset-0 bg-black bg-opacity-50 items-center justify-center z-50 px-2">
    <div class="bg-white rounded-lg p-5 md:p-8 w-full max-w-md mx-auto max-h-[90vh] overflow-y-auto">
        
        <!-- Header -->
        <div class="flex items-center justify-between mb-4">
            <h3 id="modalTitle" class="text-lg md:text-xl font-semibold">Tambah Jenis Naskah</h3>
            <button id="closeModal" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>

        <!-- Form -->
        <form id="jenisNaskahForm" class="bg-white rounded-xl space-y-5">
            <input type="hidden" id="jenisNaskahId">

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Nama Jenis Naskah</label>
                <input type="text" id="namaJenis" required 
                    class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Dibuat Oleh</label>
                <input type="text" id="createBy" readonly
                    class="w-full border rounded-lg px-3 py-2 bg-gray-100 text-gray-600">
            </div>

            <div id="formErrors" class="text-red-600 text-sm hidden"></div>

            <div class="flex flex-col md:flex-row gap-3 mt-4">
                <button type="button" id="cancelBtn" class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-700 py-2 rounded-lg">
                    Batal
                </button>
                <button type="submit" id="saveBtn" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white py-2 rounded-lg">
                    Simpan
                </button>
            </div>
        </form>
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
const apiUrl = "/api/m_jenisnaskah"; 
const tableBody = document.getElementById("jenisNaskahTableBody");
const loadingState = document.getElementById("loadingState");
const emptyState = document.getElementById("emptyState");
const modal = document.getElementById("jenisNaskahModal");
const addBtn = document.getElementById("addJenisNaskahBtn");
const closeModal = document.getElementById("closeModal");
const cancelBtn = document.getElementById("cancelBtn");
const form = document.getElementById("jenisNaskahForm");
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
async function loadJenisNaskah(keyword = "", page = 1) {
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
            
            let row = `
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4">${rowNumber}</td>
                    <td class="px-6 py-4">${item.NAMA_JENIS}</td>
                    <td class="px-6 py-4">${item.CREATE_BY ?? '-'}</td>
                    <td class="px-6 py-4 text-center space-x-2">
                        <button onclick="editJenisNaskah(${item.ID_JENISNASKAH})" class="text-blue-600 hover:text-blue-800"><i class="fas fa-edit"></i></button>
                        <button onclick="deleteJenisNaskah(${item.ID_JENISNASKAH})" class="text-red-600 hover:text-red-800"><i class="fas fa-trash"></i></button>
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
        pageBtn.addEventListener("click", () => loadJenisNaskah(lastSearchKeyword, i));
        pageNumbers.appendChild(pageBtn);
    }
}

// ===== PAGINATION EVENT LISTENERS =====
prevPageBtn.addEventListener("click", () => {
    if (currentPage > 1) loadJenisNaskah(lastSearchKeyword, currentPage - 1);
});

nextPageBtn.addEventListener("click", () => {
    if (currentPage < totalPages) loadJenisNaskah(lastSearchKeyword, currentPage + 1);
});

perPageSelect.addEventListener("change", (e) => {
    perPage = parseInt(e.target.value);
    loadJenisNaskah(lastSearchKeyword, 1);
});

// ==== Load Username ====
async function loadUsername() {
    try {
        const res = await fetch('/api/me', {
            headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' }
        });
        if (!res.ok) throw new Error('Gagal memuat user');
        const data = await res.json();
        document.getElementById('createBy').value = data.username || '';
    } catch(err) {
        console.error(err);
    }
}

// ==== Add/Edit Modal ====
addBtn.addEventListener("click", () => {
    modal.classList.add("show");
    document.getElementById("modalTitle").innerText = "Tambah Jenis Naskah";
    form.reset();
    formErrors.classList.add("hidden");
    document.getElementById("jenisNaskahId").value = "";
    loadUsername();
});
closeModal.addEventListener("click", () => modal.classList.remove("show"));
cancelBtn.addEventListener("click", () => modal.classList.remove("show"));

// ==== Submit Form ====
form.addEventListener("submit", async function(e) {
    e.preventDefault();
    formErrors.classList.add("hidden");

    const id = document.getElementById("jenisNaskahId").value;
    const payload = {
        NAMA_JENIS: document.getElementById("namaJenis").value,
        CREATE_BY: document.getElementById("createBy").value
    };

    try {
        let res;
        if (id) {
            res = await fetch(`${apiUrl}/${id}`, {
                method: "PUT",
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${token}`,  
                    "Accept": "application/json"
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

        if (res.ok) {
            showToast("Data berhasil disimpan");
            modal.classList.remove("show");
            loadJenisNaskah(lastSearchKeyword, currentPage);
        } else if (res.status === 422) {
            const err = await res.json();
            const messages = [];
            Object.values(err.errors || {}).forEach(arr => messages.push(...arr));
            formErrors.innerText = messages.join('\n');
            formErrors.classList.remove("hidden");
        } else {
            showToast("Gagal menyimpan data");
        }
    } catch(err) {
        console.error(err);
        showToast("Terjadi kesalahan");
    }
});

// ==== Edit ====
async function editJenisNaskah(id) {
    try {
        const res = await fetch(`${apiUrl}/${id}`, {
            method: "GET",
            headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' }
        });
        const data = await res.json();

        modal.classList.add("show");
        document.getElementById("modalTitle").innerText = "Edit Jenis Naskah";

        document.getElementById("jenisNaskahId").value = data.ID_JENISNASKAH;
        document.getElementById("namaJenis").value = data.NAMA_JENIS;
        document.getElementById("createBy").value = data.CREATE_BY ?? "";
    } catch (err) {
        console.error(err);
        showToast("Gagal memuat data edit");
    }
}

// ==== Delete ====
async function deleteJenisNaskah(id) {
    if (!confirm("Yakin ingin menghapus data ini?")) return;
    const res = await fetch(`${apiUrl}/${id}`, {
        method: "DELETE",
        headers: { 'Authorization': `Bearer ${token}` }
    });
    if (res.ok) {
        showToast("Data berhasil dihapus");
        loadJenisNaskah(lastSearchKeyword, currentPage);
    } else {
        showToast("Gagal menghapus data");
    }
}

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
    searchTimeout = setTimeout(() => loadJenisNaskah(keyword, 1), 500);
});

loadJenisNaskah();
</script>

</body>
</html>