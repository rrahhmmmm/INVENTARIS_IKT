<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pelindo Multi Terminal - Jenis Perangkat Management</title>
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
  <!-- Header -->
  <div class="mb-6">
    <h1 class="text-2xl font-bold text-blue-600">Master Jenis Perangkat</h1>
    <p class="text-gray-600 text-sm mt-1">Kelola jenis perangkat untuk inventaris (PC/Laptop, Printer, CCTV, Handheld, AP)</p>
  </div>

  <!-- Controls -->
  <div class="bg-white rounded-lg shadow-lg p-3 md:p-4 mb-6 flex flex-col md:flex-row justify-between items-start md:items-center space-y-3 md:space-y-0">
    <div class="flex flex-wrap items-center gap-3">
      <button id="addPerangkatBtn" class="bg-blue-600 hover:bg-blue-700 text-white px-3 md:px-4 py-2 rounded-lg flex items-center space-x-2 text-sm md:text-base">
        <i class="fas fa-plus"></i> <span>Tambah Jenis Perangkat</span>
      </button>
    </div>
    <input type="text" id="searchInput" placeholder="Cari jenis perangkat..." class="border rounded-lg px-3 py-2 w-full md:w-64 focus:ring-2 focus:ring-blue-500 focus:outline-none" />
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

  <!-- Perangkat Table -->
  <div class="bg-white rounded-lg shadow-sm overflow-x-auto">
    <table class="w-full min-w-[700px]">
      <thead class="bg-blue-600 text-white text-sm md:text-base">
        <tr>
          <th class="px-4 md:px-6 py-3 text-left font-medium">NO</th>
          <th class="px-4 md:px-6 py-3 text-left font-medium">Nama Perangkat</th>
          <th class="px-4 md:px-6 py-3 text-left font-medium">Kode</th>
          <th class="px-4 md:px-6 py-3 text-left font-medium">Dibuat Oleh</th>
          <th class="px-4 md:px-6 py-3 text-center font-medium">Aksi</th>
        </tr>
      </thead>
      <tbody id="perangkatTableBody" class="divide-y divide-gray-200 text-sm md:text-base"></tbody>
    </table>
  </div>

  <!-- Loading & Empty -->
  <div id="loadingState" class="text-center py-8">
    <i class="fas fa-spinner fa-spin text-2xl text-blue-600"></i>
    <p class="mt-2 text-gray-600">Memuat data...</p>
  </div>
  <div id="emptyState" class="text-center py-8 hidden">
    <i class="fas fa-inbox text-4xl text-gray-400 mb-4"></i>
    <p class="text-gray-600">Tidak ada data jenis perangkat</p>
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
<div id="perangkatModal" class="modal fixed inset-0 bg-black bg-opacity-50 items-center justify-center z-50 px-2">
  <div class="bg-white rounded-lg p-5 md:p-8 w-full max-w-md md:max-w-lg mx-auto max-h-[90vh] overflow-y-auto">
    <div class="flex items-center justify-between mb-4">
      <h3 id="modalTitle" class="text-lg md:text-xl font-semibold">Tambah Jenis Perangkat</h3>
      <button id="closeModal" class="text-gray-400 hover:text-gray-600">
        <i class="fas fa-times text-xl"></i>
      </button>
    </div>

    <form id="perangkatForm" class="bg-white rounded-xl space-y-5">
      <input type="hidden" id="perangkatId">

      <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">Nama Perangkat <span class="text-red-500">*</span></label>
        <input type="text" id="namaPerangkat" required placeholder="Contoh: PC/Laptop, Printer & Scan" class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none">
        <div id="namaError" class="text-red-600 text-sm mt-1 hidden">
          Nama perangkat hanya boleh berisi huruf, angka, spasi, dan karakter / & ( ).
        </div>
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">Kode Perangkat <span class="text-red-500">*</span></label>
        <input type="text" id="kodePerangkat" required placeholder="Contoh: PC, PRINTER, CCTV" class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none uppercase">
        <p class="text-gray-500 text-xs mt-1">Kode unik untuk identifikasi perangkat (akan diubah ke uppercase)</p>
        <div id="kodeError" class="text-red-600 text-sm mt-1 hidden">
          Kode perangkat hanya boleh berisi huruf dan angka tanpa spasi.
        </div>
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">Dibuat Oleh</label>
        <input type="text" id="createBy" readonly class="w-full border rounded-lg px-3 py-2 bg-gray-100 text-gray-600">
      </div>

      <!-- Field Configuration Section -->
      <div class="border-t pt-4 mt-4">
        <h4 class="text-md font-semibold mb-3 text-gray-700">
          Konfigurasi Field <span class="text-gray-400 text-sm font-normal">(Nama field yang akan muncul di form inventaris)</span>
        </h4>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
          <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">Field 1</label>
            <input type="text" id="param1" placeholder="Contoh: SERIAL NUMBER" class="w-full border rounded-lg px-3 py-2 text-sm">
          </div>
          <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">Field 2</label>
            <input type="text" id="param2" placeholder="Contoh: PROCESSOR" class="w-full border rounded-lg px-3 py-2 text-sm">
          </div>
          <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">Field 3</label>
            <input type="text" id="param3" placeholder="Contoh: RAM" class="w-full border rounded-lg px-3 py-2 text-sm">
          </div>
          <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">Field 4</label>
            <input type="text" id="param4" placeholder="Contoh: STORAGE" class="w-full border rounded-lg px-3 py-2 text-sm">
          </div>
          <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">Field 5</label>
            <input type="text" id="param5" placeholder="Contoh: SISTEM OPERASI" class="w-full border rounded-lg px-3 py-2 text-sm">
          </div>
          <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">Field 6</label>
            <input type="text" id="param6" placeholder="Contoh: USER" class="w-full border rounded-lg px-3 py-2 text-sm">
          </div>
          <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">Field 7</label>
            <input type="text" id="param7" placeholder="Contoh: KETERANGAN" class="w-full border rounded-lg px-3 py-2 text-sm">
          </div>
          <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">Field 8</label>
            <input type="text" id="param8" class="w-full border rounded-lg px-3 py-2 text-sm">
          </div>
          <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">Field 9</label>
            <input type="text" id="param9" class="w-full border rounded-lg px-3 py-2 text-sm">
          </div>
          <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">Field 10</label>
            <input type="text" id="param10" class="w-full border rounded-lg px-3 py-2 text-sm">
          </div>
          <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">Field 11</label>
            <input type="text" id="param11" class="w-full border rounded-lg px-3 py-2 text-sm">
          </div>
          <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">Field 12</label>
            <input type="text" id="param12" class="w-full border rounded-lg px-3 py-2 text-sm">
          </div>
          <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">Field 13</label>
            <input type="text" id="param13" class="w-full border rounded-lg px-3 py-2 text-sm">
          </div>
          <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">Field 14</label>
            <input type="text" id="param14" class="w-full border rounded-lg px-3 py-2 text-sm">
          </div>
          <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">Field 15</label>
            <input type="text" id="param15" class="w-full border rounded-lg px-3 py-2 text-sm">
          </div>
          <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">Field 16</label>
            <input type="text" id="param16" class="w-full border rounded-lg px-3 py-2 text-sm">
          </div>
        </div>
        <p class="text-gray-500 text-xs mt-2">Kosongkan field yang tidak diperlukan. Field yang berisi "KETERANGAN" akan tampil sebagai textarea.</p>
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
const apiUrl = "/api/m_perangkat/paginated";
const apiUrlCRUD = "/api/m_perangkat";
const tableBody = document.getElementById("perangkatTableBody");
const loadingState = document.getElementById("loadingState");
const emptyState = document.getElementById("emptyState");
const modal = document.getElementById("perangkatModal");
const addBtn = document.getElementById("addPerangkatBtn");
const closeModal = document.getElementById("closeModal");
const cancelBtn = document.getElementById("cancelBtn");
const form = document.getElementById("perangkatForm");
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
const namaInput = document.getElementById("namaPerangkat");
const kodeInput = document.getElementById("kodePerangkat");
const namaError = document.getElementById("namaError");
const kodeError = document.getElementById("kodeError");

// ==== Real-time Validasi Input ====
namaInput.addEventListener("input", function () {
    const regex = /^[A-Za-z0-9\s\/&()]*$/;
    if (!regex.test(this.value)) {
        namaError.classList.remove("hidden");
        this.classList.add("border-red-500");
    } else {
        namaError.classList.add("hidden");
        this.classList.remove("border-red-500");
    }
});

kodeInput.addEventListener("input", function () {
    const regex = /^[A-Za-z0-9]*$/;
    this.value = this.value.toUpperCase();
    if (!regex.test(this.value)) {
        kodeError.classList.remove("hidden");
        this.classList.add("border-red-500");
    } else {
        kodeError.classList.add("hidden");
        this.classList.remove("border-red-500");
    }
});

// ==== Fetch Perangkat with Pagination ====
async function loadPerangkat(keyword = "", page = 1) {
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

        data.forEach((item, i) => {
            const rowNumber = ((response.current_page - 1) * perPage) + i + 1;

            let row = `
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4">${rowNumber}</td>
                    <td class="px-6 py-4 font-medium">${item.NAMA_PERANGKAT}</td>
                    <td class="px-6 py-4"><span class="bg-blue-100 text-blue-800 px-2 py-1 rounded text-sm">${item.KODE_PERANGKAT}</span></td>
                    <td class="px-6 py-4">${item.CREATE_BY ?? '-'}</td>
                    <td class="px-6 py-4 text-center space-x-2">
                        <button onclick="editPerangkat(${item.ID_PERANGKAT})" class="text-blue-600 hover:text-blue-800"><i class="fas fa-edit"></i></button>
                        <button onclick="deletePerangkat(${item.ID_PERANGKAT})" class="text-red-600 hover:text-red-800"><i class="fas fa-trash"></i></button>
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

    } catch(err) {
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
        pageBtn.addEventListener("click", () => loadPerangkat(lastSearchKeyword, i));
        pageNumbers.appendChild(pageBtn);
    }
}

// ===== PAGINATION EVENT LISTENERS =====
prevPageBtn.addEventListener("click", () => {
    if (currentPage > 1) loadPerangkat(lastSearchKeyword, currentPage - 1);
});

nextPageBtn.addEventListener("click", () => {
    if (currentPage < totalPages) loadPerangkat(lastSearchKeyword, currentPage + 1);
});

perPageSelect.addEventListener("change", (e) => {
    perPage = parseInt(e.target.value);
    loadPerangkat(lastSearchKeyword, 1);
});

// ==== Load Username ====
async function loadUsername() {
    try {
        const res = await fetch('/api/me', {
            headers: {
                'Authorization': `Bearer ${token}`,
                'Accept': 'application/json'
            }
        });

        if (!res.ok) throw new Error('Gagal memuat user');
        const data = await res.json();
        document.getElementById('createBy').value = data.username || '';
    } catch(err) {
        console.error(err);
    }
}

// ==== Add Modal ====
addBtn.addEventListener("click", () => {
    modal.classList.add("show");
    document.getElementById("modalTitle").innerText = "Tambah Jenis Perangkat";
    form.reset();
    document.getElementById("perangkatId").value = "";

    // Reset param1-param16
    for (let i = 1; i <= 16; i++) {
        document.getElementById(`param${i}`).value = "";
    }

    namaError.classList.add("hidden");
    kodeError.classList.add("hidden");
    namaInput.classList.remove("border-red-500");
    kodeInput.classList.remove("border-red-500");
    loadUsername();
});

closeModal.addEventListener("click", () => modal.classList.remove("show"));
cancelBtn.addEventListener("click", () => modal.classList.remove("show"));

// ==== Submit Form ====
form.addEventListener("submit", async function(e) {
    e.preventDefault();

    const namaValue = namaInput.value.trim();
    const kodeValue = kodeInput.value.trim().toUpperCase();
    const namaRegex = /^[A-Za-z0-9\s\/&()]+$/;
    const kodeRegex = /^[A-Za-z0-9]+$/;

    let hasError = false;

    if (!namaRegex.test(namaValue)) {
        namaError.classList.remove("hidden");
        namaInput.classList.add("border-red-500");
        hasError = true;
    }

    if (!kodeRegex.test(kodeValue)) {
        kodeError.classList.remove("hidden");
        kodeInput.classList.add("border-red-500");
        hasError = true;
    }

    if (hasError) {
        showToast("Periksa kembali input Anda", "error");
        return;
    }

    const id = document.getElementById("perangkatId").value;
    const payload = {
        NAMA_PERANGKAT: namaValue,
        KODE_PERANGKAT: kodeValue,
        CREATE_BY: document.getElementById("createBy").value
    };

    // Add param1-param16
    for (let i = 1; i <= 16; i++) {
        const paramValue = document.getElementById(`param${i}`).value.trim();
        payload[`param${i}`] = paramValue || null;
    }

    try {
        let res;
        if (id) {
            payload.UPDATE_BY = document.getElementById("createBy").value;
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

        const result = await res.json();

        if (res.ok) {
            showToast(result.message || "Data berhasil disimpan");
            modal.classList.remove("show");
            loadPerangkat(lastSearchKeyword, currentPage);
        } else {
            showToast(result.message || "Gagal menyimpan data", "error");
        }
    } catch(err) {
        console.error(err);
        showToast("Terjadi kesalahan", "error");
    }
});

// ==== Edit Perangkat ====
async function editPerangkat(id) {
    try {
        const res = await fetch(`${apiUrlCRUD}/${id}`, {
            headers: { 'Authorization': `Bearer ${token}` }
        });
        const json = await res.json();
        const data = json.data ?? json;

        modal.classList.add("show");
        document.getElementById("modalTitle").innerText = "Edit Jenis Perangkat";
        document.getElementById("perangkatId").value = data.ID_PERANGKAT;
        document.getElementById("namaPerangkat").value = data.NAMA_PERANGKAT;
        document.getElementById("kodePerangkat").value = data.KODE_PERANGKAT;
        document.getElementById("createBy").value = data.CREATE_BY ?? "";

        // Load param1-param16
        for (let i = 1; i <= 16; i++) {
            document.getElementById(`param${i}`).value = data[`param${i}`] || "";
        }

        namaError.classList.add("hidden");
        kodeError.classList.add("hidden");
        namaInput.classList.remove("border-red-500");
        kodeInput.classList.remove("border-red-500");
    } catch(err) {
        console.error(err);
        showToast("Gagal memuat data edit", "error");
    }
}

// ==== Delete Perangkat ====
async function deletePerangkat(id) {
    if (!confirm("Yakin ingin menghapus jenis perangkat ini?\n\nCatatan: Tidak dapat menghapus jika masih digunakan oleh data inventaris.")) return;

    try {
        const res = await fetch(`${apiUrlCRUD}/${id}`, {
            method: "DELETE",
            headers: { 'Authorization': `Bearer ${token}` }
        });

        const result = await res.json();

        if (res.ok) {
            showToast(result.message || "Data berhasil dihapus");
            loadPerangkat(lastSearchKeyword, currentPage);
        } else {
            showToast(result.message || "Gagal menghapus data", "error");
        }
    } catch(err) {
        console.error(err);
        showToast("Gagal menghapus data", "error");
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
    searchTimeout = setTimeout(() => loadPerangkat(keyword, 1), 500);
});

loadPerangkat();
</script>

</body>
</html>
