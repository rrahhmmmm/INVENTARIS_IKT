<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pelindo Multi Terminal - Terminal Management</title>
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

@include('components.A_navbar')

<header class="bg-white shadow-lg h-20"></header>

<main class="container mx-auto px-4 py-6">

  <!-- Controls -->
  <div class="bg-white rounded-lg shadow-lg p-4 mb-6 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
    <div class="flex flex-wrap items-center gap-2">
      <button id="addTerminalBtn" class="bg-blue-600 hover:bg-blue-700 text-white px-3 md:px-4 py-2 rounded-lg flex items-center space-x-2 text-sm md:text-base">
        <i class="fas fa-plus"></i> <span>Tambah Terminal</span>
      </button>
      <a href="{{ url('/api/terminal/export') }}" id="exportBtn" class="bg-green-600 hover:bg-green-700 text-white px-3 md:px-4 py-2 rounded-lg flex items-center space-x-2 text-sm md:text-base">
        Export Excel <i class="fas fa-file-excel"></i>
      </a>
    </div>
    <input id="searchInput" type="text" placeholder="Cari..." class="border px-3 py-2 w-full md:w-auto text-sm md:text-base" />
  </div>

  <!-- Terminal Table -->
  <div class="bg-white rounded-lg shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
      <table class="w-full min-w-[600px]">
        <thead class="bg-blue-600 text-white text-sm md:text-base">
          <tr>
            <th class="px-4 md:px-6 py-2 md:py-3 text-left">NO</th>
            <th class="px-4 md:px-6 py-2 md:py-3 text-left">Kode Terminal</th>
            <th class="px-4 md:px-6 py-2 md:py-3 text-left">Nama Terminal</th>
            <th class="px-4 md:px-6 py-2 md:py-3 text-left">Lokasi</th>
            <th class="px-4 md:px-6 py-2 md:py-3 text-left">Dibuat Oleh</th>
            <th class="px-4 md:px-6 py-2 md:py-3 text-center">Aksi</th>
          </tr>
        </thead>
        <tbody id="terminalTableBody" class="divide-y divide-gray-200"></tbody>
      </table>
    </div>
  </div>

  <div id="loadingState" class="text-center py-8">
    <i class="fas fa-spinner fa-spin text-2xl text-blue-600"></i>
    <p class="mt-2 text-gray-600">Memuat data...</p>
  </div>
  <div id="emptyState" class="text-center py-8 hidden">
    <i class="fas fa-inbox text-4xl text-gray-400 mb-4"></i>
    <p class="text-gray-600">Tidak ada data terminal</p>
  </div>
</main>

<!-- Modal Add/Edit -->
<div id="terminalModal" class="modal fixed inset-0 bg-black bg-opacity-50 items-center justify-center z-50">
  <div class="bg-white rounded-lg p-6 w-full max-w-md mx-4 max-h-screen overflow-y-auto">
    <div class="flex items-center justify-between mb-4">
      <h3 id="modalTitle" class="text-lg font-semibold">Tambah Terminal</h3>
      <button id="closeModal" class="text-gray-400 hover:text-gray-600">
        <i class="fas fa-times"></i>
      </button>
    </div>

    <form id="terminalForm">
      <input type="hidden" id="terminalId">
      <input type="hidden" id="updateBy">
      <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700 mb-2">Kode Terminal</label>
        <input type="text" id="kodeTerminal" required class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500">
      </div>
      <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700 mb-2">Nama Terminal</label>
        <input type="text" id="namaTerminal" required class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500">
      </div>
      <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700 mb-2">Lokasi Terminal</label>
        <input type="text" id="lokasiTerminal" class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500">
      </div>
      <div class="mb-6">
        <label class="block text-sm font-medium text-gray-700 mb-2">Dibuat Oleh</label>
        <input type="text" id="createBy" class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500" readonly>
      </div>
      <div id="formErrors" class="text-red-600 text-sm mb-3 hidden"></div>
      <div class="flex flex-col sm:flex-row gap-2">
        <button type="button" id="cancelBtn" class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-700 py-2 rounded-lg">Batal</button>
        <button type="submit" id="saveBtn" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white py-2 rounded-lg">Simpan</button>
      </div>
    </form>

    <!-- Import Excel -->
    <div class="mt-8 gap-4">
      <h4 class="text-md font-semibold mb-3">Tambah Data dengan Import Excel</h4>

      <a href="{{ url('/api/terminal/export-template') }}" 
        id="templateBtn" 
        class="bg-green-600 hover:bg-green-700 text-white px-2 py-2 rounded-lg flex items-center space-x-2 mb-4 text-sm md:text-base">
        Download Template <i class="fas fa-download"></i>
      </a>
      
      <form id="importForm" class="flex flex-col sm:flex-row gap-2">
        <input type="file" name="file" id="importFile" class="border px-2 py-1">
        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Import</button>
      </form>
    </div>
  </div>
</div>

<!-- Toast -->
<div id="toast" class="toast fixed top-4 right-4 bg-green-500 text-white px-4 md:px-6 py-2 md:py-3 rounded-lg shadow-lg z-50 max-w-xs sm:max-w-sm">
  <div class="flex items-center space-x-2">
    <i id="toastIcon" class="fas fa-check-circle"></i>
    <span id="toastMessage">Pesan berhasil</span>
  </div>
</div>
<script>
const apiUrl = "/api/m_terminal"; 
const tableBody = document.getElementById("terminalTableBody");
const loadingState = document.getElementById("loadingState");
const emptyState = document.getElementById("emptyState");
const modal = document.getElementById("terminalModal");
const addBtn = document.getElementById("addTerminalBtn");
const closeModal = document.getElementById("closeModal");
const cancelBtn = document.getElementById("cancelBtn");
const form = document.getElementById("terminalForm");
const formErrors = document.getElementById("formErrors");
const toast = document.getElementById("toast");
const toastMessage = document.getElementById("toastMessage");
const token = localStorage.getItem('auth_token'); 

// ==== Fetch Terminals ====
async function loadTerminals(keyword = "") {
    loadingState.classList.remove("hidden");
    emptyState.classList.add("hidden");
    tableBody.innerHTML = "";

    try {
        let url = apiUrl;
        if (keyword && keyword.trim() !== "") {
            url += `?search=${encodeURIComponent(keyword)}`;
        }

        // ==== HEADER AUTH TOKEN BEARER ====
        let res = await fetch(url, {
            headers: {
                'Authorization': `Bearer ${token}`,  // <<< token Bearer dikirim ke API
                'Accept': 'application/json'
            }
        });

        let data = await res.json();
        loadingState.classList.add("hidden");

        if (!Array.isArray(data) || data.length === 0) {
            emptyState.classList.remove("hidden");
            return;
        }

        data.forEach((terminal, i) => {
            let row = `
                <tr>
                    <td class="px-6 py-4">${i+1}</td>
                    <td class="px-6 py-4">${terminal.KODE_TERMINAL}</td>
                    <td class="px-6 py-4">${terminal.NAMA_TERMINAL}</td>
                    <td class="px-6 py-4">${terminal.LOKASI ?? '-'}</td>
                    <td class="px-6 py-4">${terminal.CREATE_BY ?? '-'}</td>
                    <td class="px-6 py-4 text-center space-x-2">
                        <button onclick="editTerminal(${terminal.ID_TERMINAL})" class="text-blue-600 hover:text-blue-800"><i class="fas fa-edit"></i></button>
                        <button onclick="deleteTerminal(${terminal.ID_TERMINAL})" class="text-red-600 hover:text-red-800"><i class="fas fa-trash"></i></button>
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

// ==== Load Username untuk CreateBy / UpdateBy ====
async function loadUsername(forField = 'createBy') {
    try {
        const res = await fetch('/api/me', {
            headers: {
                'Authorization': `Bearer ${token}`, // <<< token Bearer
                'Accept': 'application/json'
            }
        });
        if (!res.ok) throw new Error('Gagal memuat user');
        const data = await res.json();
        if (forField === 'createBy') document.getElementById('createBy').value = data.username || '';
        if (forField === 'updateBy') document.getElementById('updateBy').value = data.username || '';
    } catch(err) {
        console.error(err);
    }
}

// ==== Add/Edit Modal ====
addBtn.addEventListener("click", () => {
    modal.classList.add("show");
    document.getElementById("modalTitle").innerText = "Tambah Terminal";
    form.reset();
    formErrors.classList.add("hidden");
    document.getElementById("terminalId").value = "";
    loadUsername('createBy'); // otomatis isi CREATE_BY
});
closeModal.addEventListener("click", () => modal.classList.remove("show"));
cancelBtn.addEventListener("click", () => modal.classList.remove("show"));

// ==== Submit Form Create/Edit ====
form.addEventListener("submit", async function(e) {
    e.preventDefault();
    formErrors.classList.add("hidden");

    const id = document.getElementById("terminalId").value;
    const payload = {
        KODE_TERMINAL: document.getElementById("kodeTerminal").value,
        NAMA_TERMINAL: document.getElementById("namaTerminal").value,
        LOKASI: document.getElementById("lokasiTerminal").value,
        CREATE_BY: document.getElementById("createBy").value,
        UPDATE_BY: document.getElementById("updateBy").value
    };

    try {
        let res;
        if (id) {
            res = await fetch(`${apiUrl}/${id}`, {
                method: "PUT",
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${token}`,  // <<< token Bearer
                    "Accept": "application/json"
                },
                body: JSON.stringify(payload)
            });
        } else {
            res = await fetch(apiUrl, {
                method: "POST",
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${token}`  // <<< token Bearer
                },
                body: JSON.stringify(payload)
            });
        }

        if (res.ok) {
            showToast("Data berhasil disimpan");
            modal.classList.remove("show");
            loadTerminals();
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

// ==== Edit Terminal ====
async function editTerminal(id) {
    try {
        const res = await fetch(`${apiUrl}/${id}`, {
            method: "GET", // <--- ambil data dulu
            headers: {
                'Authorization': `Bearer ${token}`,
                'Accept': 'application/json'
            }
        });
        const json = await res.json();
        console.log("Response editTerminal:", json);

        // kalau responsenya pakai { data: {...} }
        const data = json.data ?? json;

        modal.classList.add("show");
        document.getElementById("modalTitle").innerText = "Edit Terminal";

        // pastikan hidden input ID keisi
        document.getElementById("terminalId").value = data.ID_TERMINAL;

        document.getElementById("kodeTerminal").value = data.KODE_TERMINAL;
        document.getElementById("namaTerminal").value = data.NAMA_TERMINAL;
        document.getElementById("lokasiTerminal").value = data.LOKASI ?? "";
        document.getElementById("createBy").value = data.CREATE_BY ?? "";
        document.getElementById("updateBy").value = data.UPDATE_BY ?? "";
    } catch (err) {
        console.error(err);
        showToast("Gagal memuat data edit");
    }
}

// ==== Delete Terminal ====
async function deleteTerminal(id) {
    if (!confirm("Yakin ingin menghapus data ini?")) return;
    const res = await fetch(`${apiUrl}/${id}`, {
        method: "DELETE",
        headers: { 'Authorization': `Bearer ${token}` } // <<< token Bearer
    });
    if (res.ok) {
        showToast("Data berhasil dihapus");
        loadTerminals();
    } else {
        showToast("Gagal menghapus data");
    }
}

// ==== Import Excel ====
document.getElementById("importForm").addEventListener("submit", async function(e) {
    e.preventDefault();
    const fileInput = document.getElementById("importFile").files[0];
    if (!fileInput) { showToast("Pilih file terlebih dahulu"); return; }

    const formData = new FormData();
    formData.append("file", fileInput);

    try {
        const res = await fetch("/api/terminal/import", {
            method: "POST",
            headers: { 'Authorization': `Bearer ${token}` }, // <<< token Bearer
            body: formData
        });
        const data = await res.json();
        if (res.ok) {
            showToast(data.message || "Data berhasil diimport");
            modal.classList.remove("show");
            loadTerminals();
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
    searchTimeout = setTimeout(() => loadTerminals(keyword), 500);
});

loadTerminals();
</script>

</body>
</html>
