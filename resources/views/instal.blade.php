<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pelindo Multi Terminal - Instal Management</title>
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
  <!-- Controls -->
  <div class="bg-white rounded-lg shadow-lg p-3 md:p-4 mb-6 flex flex-col md:flex-row justify-between items-start md:items-center space-y-3 md:space-y-0">
    <div class="flex flex-wrap items-center gap-3">
      <button id="addInstalBtn" class="bg-blue-600 hover:bg-blue-700 text-white px-3 md:px-4 py-2 rounded-lg flex items-center space-x-2 text-sm md:text-base">
        <i class="fas fa-plus"></i> <span>Tambah Instal</span>
      </button>
      <a href="{{ url('/api/instal/export') }}" class="bg-green-600 hover:bg-green-700 text-white px-3 md:px-4 py-2 rounded-lg flex items-center space-x-2 text-sm md:text-base">
        <span>Export Excel</span> <i class="fas fa-file-excel"></i>
      </a>
    </div>
    <input type="text" id="searchInput" placeholder="Cari instal..." class="border rounded-lg px-3 py-2 w-full md:w-64 focus:ring-2 focus:ring-blue-500 focus:outline-none" />
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

  <!-- Instal Table -->
  <div class="bg-white rounded-lg shadow-sm overflow-x-auto">
    <table class="w-full min-w-[600px]">
      <thead class="bg-blue-600 text-white text-sm md:text-base">
        <tr>
          <th class="px-4 md:px-6 py-3 text-left font-medium">NO</th>
          <th class="px-4 md:px-6 py-3 text-left font-medium">Nama Instal</th>
          <th class="px-4 md:px-6 py-3 text-left font-medium">Dibuat Oleh</th>
          <th class="px-4 md:px-6 py-3 text-center font-medium">Aksi</th>
        </tr>
      </thead>
      <tbody id="instalTableBody" class="divide-y divide-gray-200 text-sm md:text-base"></tbody>
    </table>
  </div>

  <!-- Loading & Empty -->
  <div id="loadingState" class="text-center py-8">
    <i class="fas fa-spinner fa-spin text-2xl text-blue-600"></i>
    <p class="mt-2 text-gray-600">Memuat data...</p>
  </div>
  <div id="emptyState" class="text-center py-8 hidden">
    <i class="fas fa-inbox text-4xl text-gray-400 mb-4"></i>
    <p class="text-gray-600">Tidak ada data instal</p>
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

<!-- Modal Add/Edit -->
<div id="instalModal" class="modal fixed inset-0 bg-black bg-opacity-50 items-center justify-center z-50 px-2">
  <div class="bg-white rounded-lg p-5 md:p-8 w-full max-w-md md:max-w-4xl mx-auto max-h-[90vh] overflow-y-auto">
    <div class="flex items-center justify-between mb-4">
      <h3 id="modalTitle" class="text-lg md:text-xl font-semibold">Tambah Instal</h3>
      <button id="closeModal" class="text-gray-400 hover:text-gray-600">
        <i class="fas fa-times text-xl"></i>
      </button>
    </div>

    <form id="instalForm" class="bg-white rounded-xl space-y-5">
      <input type="hidden" id="instalId">
      <input type="hidden" id="updateBy">

      <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">Nama Instal</label>
        <input type="text" id="namaInstal" required class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none">
        <div id="namaError" class="text-red-600 text-sm mt-1 hidden">
          Nama instal hanya boleh berisi huruf, angka dan spasi.
        </div>
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">Dibuat Oleh</label>
        <input type="text" id="createBy" readonly class="w-full border rounded-lg px-3 py-2 bg-gray-100 text-gray-600">
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
      <a href="{{ url('/api/instal/export-template') }}" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg flex items-center justify-center space-x-2 mb-3">
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
const apiUrl = "/api/m_instal/paginated";
const apiUrlCRUD = "/api/m_instal";
const tableBody = document.getElementById("instalTableBody");
const loadingState = document.getElementById("loadingState");
const emptyState = document.getElementById("emptyState");
const modal = document.getElementById("instalModal");
const addBtn = document.getElementById("addInstalBtn");
const closeModal = document.getElementById("closeModal");
const cancelBtn = document.getElementById("cancelBtn");
const form = document.getElementById("instalForm");
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

// Validasi elements
const namaInput = document.getElementById("namaInstal");
const namaError = document.getElementById("namaError");

// ==== Real-time Validasi Input ====
namaInput.addEventListener("input", function () {
    const regex = /^[A-Za-z0-9\s]*$/;
    if (!regex.test(this.value)) {
        namaError.classList.remove("hidden");
        this.classList.add("border-red-500");
    } else {
        namaError.classList.add("hidden");
        this.classList.remove("border-red-500");
    }
});

// ==== Fetch Instal with Pagination ====
async function loadInstal(keyword = "", page = 1) {
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

        data.forEach((instal, i) => {
            const rowNumber = ((response.current_page - 1) * perPage) + i + 1;

            let row = `
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4">${rowNumber}</td>
                    <td class="px-6 py-4">${instal.NAMA_INSTAL}</td>
                    <td class="px-6 py-4">${instal.CREATE_BY ?? '-'}</td>
                    <td class="px-6 py-4 text-center space-x-2">
                        <button onclick="editInstal(${instal.ID_INSTAL})" class="text-blue-600 hover:text-blue-800"><i class="fas fa-edit"></i></button>
                        <button onclick="deleteInstal(${instal.ID_INSTAL})" class="text-red-600 hover:text-red-800"><i class="fas fa-trash"></i></button>
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
        pageBtn.addEventListener("click", () => loadInstal(lastSearchKeyword, i));
        pageNumbers.appendChild(pageBtn);
    }
}

// ===== PAGINATION EVENT LISTENERS =====
prevPageBtn.addEventListener("click", () => {
    if (currentPage > 1) loadInstal(lastSearchKeyword, currentPage - 1);
});

nextPageBtn.addEventListener("click", () => {
    if (currentPage < totalPages) loadInstal(lastSearchKeyword, currentPage + 1);
});

perPageSelect.addEventListener("change", (e) => {
    perPage = parseInt(e.target.value);
    loadInstal(lastSearchKeyword, 1);
});

// ==== Load Username ====
async function loadUsername(forField = 'createBy') {
    try {
        const res = await fetch('/api/me', {
            headers: {
                'Authorization': `Bearer ${token}`,
                'Accept': 'application/json'
            }
        });

        if (!res.ok) throw new Error('Gagal memuat user');
        const data = await res.json();

        if (forField === 'createBy') document.getElementById('createBy').value = data.username || '';
    } catch(err) {
        console.error(err);
    }
}

// ==== Add Modal ====
addBtn.addEventListener("click", () => {
    modal.classList.add("show");
    document.getElementById("modalTitle").innerText = "Tambah Instal";
    form.reset();
    document.getElementById("instalId").value = "";
    namaError.classList.add("hidden");
    namaInput.classList.remove("border-red-500");
    loadUsername('createBy');
});

closeModal.addEventListener("click", () => modal.classList.remove("show"));
cancelBtn.addEventListener("click", () => modal.classList.remove("show"));

// ==== Submit Form ====
form.addEventListener("submit", async function(e) {
    e.preventDefault();

    const namaValue = namaInput.value.trim();
    const regex = /^[A-Za-z0-9\s]+$/;

    if (!regex.test(namaValue)) {
        namaError.classList.remove("hidden");
        namaInput.classList.add("border-red-500");
        showToast("Nama instal hanya boleh huruf, angka dan spasi");
        return;
    }

    const id = document.getElementById("instalId").value;
    const payload = {
        NAMA_INSTAL: namaValue,
        CREATE_BY: document.getElementById("createBy").value
    };

    try {
        let res;
        if (id) {
            res = await fetch(`${apiUrlCRUD}/${id}`, {
                method: "PUT",
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${token}`
                },
                body: JSON.stringify(payload)
            });
        } else {
            res = await fetch(apiUrlCRUD, {
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
            loadInstal(lastSearchKeyword, currentPage);
        } else {
            showToast("Gagal menyimpan data");
        }
    } catch(err) {
        console.error(err);
        showToast("Terjadi kesalahan");
    }
});

// ==== Edit Instal ====
async function editInstal(id) {
    try {
        const res = await fetch(`${apiUrlCRUD}/${id}`, {
            headers: { 'Authorization': `Bearer ${token}` }
        });
        const json = await res.json();
        const data = json.data ?? json;

        modal.classList.add("show");
        document.getElementById("modalTitle").innerText = "Edit Instal";
        document.getElementById("instalId").value = data.ID_INSTAL;
        document.getElementById("namaInstal").value = data.NAMA_INSTAL;
        document.getElementById("createBy").value = data.CREATE_BY ?? "";
        namaError.classList.add("hidden");
        namaInput.classList.remove("border-red-500");
    } catch(err) {
        console.error(err);
        showToast("Gagal memuat data edit");
    }
}

// ==== Delete Instal ====
async function deleteInstal(id) {
    if (!confirm("Yakin ingin menghapus data ini?")) return;

    const res = await fetch(`${apiUrlCRUD}/${id}`, {
        method: "DELETE",
        headers: { 'Authorization': `Bearer ${token}` }
    });

    if (res.ok) {
        showToast("Data berhasil dihapus");
        loadInstal(lastSearchKeyword, currentPage);
    } else {
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

    try {
        const res = await fetch("/api/instal/import", {
            method: "POST",
            headers: { 'Authorization': `Bearer ${token}` },
            body: formData
        });
        const data = await res.json();
        if (res.ok) {
            showToast(data.message || "Import berhasil");
            modal.classList.remove("show");
            loadInstal(lastSearchKeyword, currentPage);
        } else {
            showToast(data.message || "Gagal import");
        }
    } catch(err) {
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
    searchTimeout = setTimeout(() => loadInstal(keyword, 1), 500);
});

loadInstal();
</script>

</body>
</html>
