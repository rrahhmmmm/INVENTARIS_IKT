<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pelindo Multi Terminal - Klasifikasi Management</title>
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
            <button id="addKlasifikasiBtn" class="bg-blue-600 hover:bg-blue-700 text-white px-3 md:px-4 py-2 rounded-lg flex items-center space-x-2 text-sm md:text-base">
                <i class="fas fa-plus"></i> <span>Tambah Klasifikasi</span>
            </button>

            <!-- export excel -->
            <a href="{{ url('api/klasifikasi/export') }}" id="exportBtn" 
                class="bg-green-600 hover:bg-green-700 text-white px-3 md:px-4 py-2 rounded-lg flex items-center space-x-2 text-sm md:text-base">
                <span>Export Excel</span> <i class="fas fa-file-excel"></i>
            </a>
        </div>

        <input id="searchInput" type="text" placeholder="Cari..." 
            class="border rounded-lg px-3 py-2 w-full md:w-64 focus:ring-2 focus:ring-blue-500 focus:outline-none" />
    </div>

    <!-- Table -->
    <div class="bg-white rounded-lg shadow-sm overflow-x-auto">
        <table class="w-full min-w-[700px]">
            <thead class="bg-blue-600 text-white text-sm md:text-base">
                <tr>
                    <th class="px-4 md:px-6 py-3 text-left font-medium">NO</th>
                    <th class="px-4 md:px-6 py-3 text-left font-medium">Kode Klasifikasi</th>
                    <th class="px-4 md:px-6 py-3 text-left font-medium">Kategori</th>
                    <th class="px-4 md:px-6 py-3 text-left font-medium">Deskripsi</th>
                    <th class="px-4 md:px-6 py-3 text-left font-medium">Start Date</th>
                    <th class="px-4 md:px-6 py-3 text-left font-medium">End Date</th>
                    <th class="px-4 md:px-6 py-3 text-left font-medium">Dibuat Oleh</th>
                    <th class="px-4 md:px-6 py-3 text-center font-medium">Aksi</th>
                </tr>
            </thead>
            <tbody id="klasifikasiTableBody" class="divide-y divide-gray-200 text-sm md:text-base"></tbody>
        </table>
    </div>
    
    <div id="pagination" class="mt-6 flex justify-center"></div>

    <!-- States -->
    <div id="loadingState" class="text-center py-8">
        <i class="fas fa-spinner fa-spin text-2xl text-blue-600"></i>
        <p class="mt-2 text-gray-600">Memuat data...</p>
    </div>

    <div id="emptyState" class="text-center py-8 hidden">
        <i class="fas fa-inbox text-4xl text-gray-400 mb-4"></i>
        <p class="text-gray-600">Tidak ada data klasifikasi</p>
    </div>
</main>

<!-- Modal -->
<div id="klasifikasiModal" class="modal fixed inset-0 bg-black bg-opacity-50 items-center justify-center z-50 px-2">
    <div class="bg-white rounded-lg p-5 md:p-8 w-full max-w-md md:max-w-4xl mx-auto max-h-[90vh] overflow-y-auto">
        
        <!-- Header -->
        <div class="flex items-center justify-between mb-4">
            <h3 id="modalTitle" class="text-lg md:text-xl font-semibold">Tambah Klasifikasi</h3>
            <button id="closeModal" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>

        <!-- Form -->
        <form id="klasifikasiForm" class="bg-white rounded-xl space-y-5">
            <input type="hidden" id="klasifikasiId">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Kode Klasifikasi</label>
                    <input type="text" id="kodeKlasifikasi" required 
                        class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Kategori</label>
                    <input type="text" id="kategori" required 
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
                <label class="block text-sm font-medium text-gray-700 mb-2">Deskripsi</label>
                <textarea id="deskripsi" required rows="4"
                    class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none"></textarea>
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

        <!-- Import Excel -->
        <div class="border-t mt-6 pt-4">
            <h4 class="text-md font-semibold mb-3">Tambah Data dengan Import Excel</h4>
            <a href="{{ url('/api/klasifikasi/export-template') }}" id="templateBtn" 
                class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg flex items-center justify-center space-x-2 mb-3">
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
const apiUrl = "/api/m_klasifikasi"; 
const tableBody = document.getElementById("klasifikasiTableBody");
const loadingState = document.getElementById("loadingState");
const emptyState = document.getElementById("emptyState");
const modal = document.getElementById("klasifikasiModal");
const addBtn = document.getElementById("addKlasifikasiBtn");
const closeModal = document.getElementById("closeModal");
const cancelBtn = document.getElementById("cancelBtn");
const form = document.getElementById("klasifikasiForm");
const formErrors = document.getElementById("formErrors");
const toast = document.getElementById("toast");
const toastMessage = document.getElementById("toastMessage");
const token = localStorage.getItem('auth_token'); 

// ==== Fetch Data ====
async function loadKlasifikasi(keyword = "") {
    loadingState.classList.remove("hidden");
    emptyState.classList.add("hidden");
    tableBody.innerHTML = "";

    try {
        let url = apiUrl;
        if (keyword && keyword.trim() !== "") {
            url += `?search=${encodeURIComponent(keyword)}`;
        }

        let res = await fetch(url, {
            headers: {
                'Authorization': `Bearer ${token}`,
                'Accept': 'application/json'
            }
        });

        let data = await res.json();
        loadingState.classList.add("hidden");

        if (!Array.isArray(data) || data.length === 0) {
            emptyState.classList.remove("hidden");
            return;
        }

        data.forEach((item, i) => {
            let row = `
                <tr>
                    <td class="px-6 py-4">${i+1}</td>
                    <td class="px-6 py-4">${item.KODE_KLASIFIKASI}</td>
                    <td class="px-6 py-4">${item.KATEGORI}</td>
                    <td class="px-6 py-4 whitespace-normal break-words max-w-xs">${item.DESKRIPSI}</td>
                    <td class="px-6 py-4">${item.START_DATE ?? '-'}</td>
                    <td class="px-6 py-4">${item.END_DATE ?? '-'}</td>
                    <td class="px-6 py-4">${item.CREATE_BY ?? '-'}</td>
                    <td class="px-6 py-4 text-center space-x-2">
                        <button onclick="editKlasifikasi(${item.ID_KLASIFIKASI})" class="text-blue-600 hover:text-blue-800"><i class="fas fa-edit"></i></button>
                        <button onclick="deleteKlasifikasi(${item.ID_KLASIFIKASI})" class="text-red-600 hover:text-red-800"><i class="fas fa-trash"></i></button>
                    </td>
                </tr>
            `;
            tableBody.insertAdjacentHTML("beforeend", row);
        });
    } catch(err) {
        console.error(err);
        loadingState.classList.add("hidden");
        showToast("Gagal memuat data");
    }
}

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
    document.getElementById("modalTitle").innerText = "Tambah Klasifikasi";
    form.reset();
    formErrors.classList.add("hidden");
    document.getElementById("klasifikasiId").value = "";
    loadUsername();
});
closeModal.addEventListener("click", () => modal.classList.remove("show"));
cancelBtn.addEventListener("click", () => modal.classList.remove("show"));

// ==== Submit Form ====
form.addEventListener("submit", async function(e) {
    e.preventDefault();
    formErrors.classList.add("hidden");

    const id = document.getElementById("klasifikasiId").value;
    const payload = {
        KODE_KLASIFIKASI: document.getElementById("kodeKlasifikasi").value,
        KATEGORI: document.getElementById("kategori").value,
        DESKRIPSI: document.getElementById("deskripsi").value,
        START_DATE: document.getElementById("startDate").value,
        END_DATE: document.getElementById("endDate").value,
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
            loadKlasifikasi();
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
async function editKlasifikasi(id) {
    try {
        const res = await fetch(`${apiUrl}/${id}`, {
            method: "GET",
            headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' }
        });
        const json = await res.json();
        const data = json.data ?? json;

        modal.classList.add("show");
        document.getElementById("modalTitle").innerText = "Edit Klasifikasi";

        document.getElementById("klasifikasiId").value = data.ID_KLASIFIKASI;
        document.getElementById("kodeKlasifikasi").value = data.KODE_KLASIFIKASI;
        document.getElementById("kategori").value = data.KATEGORI;
        document.getElementById("deskripsi").value = data.DESKRIPSI;
        document.getElementById("startDate").value = data.START_DATE ?? "";
        document.getElementById("endDate").value = data.END_DATE ?? "";
        document.getElementById("createBy").value = data.CREATE_BY ?? "";
    } catch (err) {
        console.error(err);
        showToast("Gagal memuat data edit");
    }
}

// ==== Delete ====
async function deleteKlasifikasi(id) {
    if (!confirm("Yakin ingin menghapus data ini?")) return;
    const res = await fetch(`${apiUrl}/${id}`, {
        method: "DELETE",
        headers: { 'Authorization': `Bearer ${token}` }
    });
    if (res.ok) {
        showToast("Data berhasil dihapus");
        loadKlasifikasi();
    } else {
        showToast("Gagal menghapus data");
    }
}


// ==== Import Excel (Placeholder) ====
document.getElementById("importForm").addEventListener("submit", async function(e) {
    e.preventDefault();
    const fileInput = document.getElementById("importFile").files[0];
    if (!fileInput) { showToast("Pilih file terlebih dahulu"); return; }

    const formData = new FormData();
    formData.append("file", fileInput);

    try {
        const res = await fetch("/api/klasifikasi/import", {
            method: "POST",
            headers: { 'Authorization': `Bearer ${token}` },
            body: formData
        });
        const data = await res.json();
        if (res.ok) {
            showToast(data.message || "Data berhasil diimport");
            modal.classList.remove("show");
            loadKlasifikasi();
        } else {
            showToast(data.message || "Gagal import data");
        }
    } catch(err) {
        console.error(err);
        showToast("Terjadi error saat import");
    }
});

// ==== Toast ====
function showToast(msg) {
    toastMessage.innerText = msg;
    toast.classList.add("show");
    setTimeout(() => toast.classList.remove("show"), 3000);
}

// ==== Search ====
let searchTimeout = null;
document.getElementById("searchInput").addEventListener("input", function() {
    clearTimeout(searchTimeout);
    let keyword = this.value;
    searchTimeout = setTimeout(() => loadKlasifikasi(keyword), 500);
});

loadKlasifikasi();
</script>

</body>
</html>
