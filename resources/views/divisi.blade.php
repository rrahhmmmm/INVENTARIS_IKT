<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pelindo Multi Terminal - Divisi Management</title>
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

<header class="bg-white shadow-lg h-20"></header>

<!-- Main Content -->
<main class="container mx-auto px-4 py-6">
  <!-- Controls -->
  <div class="bg-white rounded-lg shadow-lg p-4 mb-6 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
    <div class="flex flex-wrap items-center gap-2">
      <button id="addDivisiBtn" class="bg-blue-600 hover:bg-blue-700 text-white px-3 md:px-4 py-2 rounded-lg flex items-center space-x-2 text-sm md:text-base">
        <i class="fas fa-plus"></i> <span>Tambah Divisi</span>
      </button>
      <a href="{{ url('/api/divisi/export') }}" class="bg-green-600 hover:bg-green-700 text-white px-3 md:px-4 py-2 rounded-lg flex items-center space-x-2 text-sm md:text-base">
        Export Excel <i class="fas fa-file-excel"></i>
      </a>
    </div>
    <input type="text" id="searchInput" placeholder="Cari divisi..." class="border border-gray-300 rounded-lg px-3 py-2 w-full md:w-64 text-sm md:text-base" />
  </div>

  <!-- Divisi Table -->
  <div class="bg-white rounded-lg shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
      <table class="w-full min-w-[500px]">
        <thead class="bg-blue-600 text-white text-sm md:text-base">
          <tr>
            <th class="px-4 md:px-6 py-2 md:py-3 text-left">NO</th>
            <th class="px-4 md:px-6 py-2 md:py-3 text-left">Nama Divisi</th>
            <th class="px-4 md:px-6 py-2 md:py-3 text-left">Dibuat Oleh</th>
            <th class="px-4 md:px-6 py-2 md:py-3 text-center">Aksi</th>
          </tr>
        </thead>
        <tbody id="divisiTableBody" class="divide-y divide-gray-200"></tbody>
      </table>
    </div>
  </div>

  <!-- Loading & Empty -->
  <div id="loadingState" class="text-center py-8">
    <i class="fas fa-spinner fa-spin text-2xl text-blue-600"></i>
    <p class="mt-2 text-gray-600">Memuat data...</p>
  </div>
  <div id="emptyState" class="text-center py-8 hidden">
    <i class="fas fa-inbox text-4xl text-gray-400 mb-4"></i>
    <p class="text-gray-600">Tidak ada data divisi</p>
  </div>
</main>

<!-- Modal Add/Edit -->
<div id="divisiModal" class="modal fixed inset-0 bg-black bg-opacity-50 items-center justify-center z-50">
  <div class="bg-white rounded-lg p-6 w-full max-w-md mx-4 max-h-screen overflow-y-auto">
    <div class="flex items-center justify-between mb-4">
      <h3 id="modalTitle" class="text-lg font-semibold">Tambah Divisi</h3>
      <button id="closeModal" class="text-gray-400 hover:text-gray-600">
        <i class="fas fa-times"></i>
      </button>
    </div>
    
    <form id="divisiForm">
      <input type="hidden" id="divisiId">
      <input type="hidden" id="updateBy">
      <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700 mb-2">Nama Divisi</label>
        <input type="text" id="namaDivisi" required class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500">
      </div>
      <div class="mb-6">
        <label class="block text-sm font-medium text-gray-700 mb-2">Dibuat Oleh</label>
        <input type="text" id="createBy" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 bg-gray-100" readonly>
      </div>
      <div class="flex flex-col sm:flex-row gap-2">
        <button type="button" id="cancelBtn" class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-700 py-2 px-4 rounded-lg">Batal</button>
        <button type="submit" id="saveBtn" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded-lg">Simpan</button>
      </div>
    </form>

    <!-- Import Excel -->
    <div class="mt-8">
      <h4 class="text-md font-semibold mb-3">Tambah Data Dengan Excel</h4>
      <a href="{{ url('/api/divisi/export-template') }}" class="bg-green-600 hover:bg-green-700 text-white px-3 py-2 rounded-lg flex items-center space-x-2 text-sm md:text-base mb-4">
        Download Template <i class="fas fa-download"></i>
      </a>
      <form id="importForm" class="flex flex-col sm:flex-row gap-2">
        <input type="file" name="file" id="importFile" class="border px-2 py-1 text-sm md:text-base">
        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded text-sm md:text-base">Import</button>
      </form>
    </div>
  </div>
</div>

<!-- Toast -->
<div id="toast" class="toast fixed top-4 right-4 bg-green-500 text-white px-4 py-2 rounded-lg shadow-lg z-50 max-w-xs sm:max-w-sm">
  <div class="flex items-center space-x-2">
    <span id="toastMessage">Pesan berhasil</span>
  </div>
</div>

    <script>
const apiUrl = "/api/m_divisi"; 
const tableBody = document.getElementById("divisiTableBody");
const loadingState = document.getElementById("loadingState");
const emptyState = document.getElementById("emptyState");
const modal = document.getElementById("divisiModal");
const addBtn = document.getElementById("addDivisiBtn");
const closeModal = document.getElementById("closeModal");
const cancelBtn = document.getElementById("cancelBtn");
const form = document.getElementById("divisiForm");
const toast = document.getElementById("toast");
const toastMessage = document.getElementById("toastMessage");
const token = localStorage.getItem('auth_token'); // samakan dengan terminal

// ==== Fetch Divisi ====
async function loadDivisi(keyword = "") {
    loadingState.classList.remove("hidden");
    emptyState.classList.add("hidden");
    tableBody.innerHTML = "";

    try {
        let url = apiUrl;
        if (keyword && keyword.trim() !== "") {
            url += `?search=${encodeURIComponent(keyword)}`;
        }

        const res = await fetch(url, {
            headers: {
                'Authorization': `Bearer ${token}`,
                'Accept': 'application/json'
            }
        });

        let json = await res.json();
        const data = json.data ?? json;

        loadingState.classList.add("hidden");

        if (!Array.isArray(data) || data.length === 0) {
            emptyState.classList.remove("hidden");
            return;
        }

        data.forEach((divisi, i) => {
            let row = `
                <tr>
                    <td class="px-6 py-4">${i+1}</td>
                    <td class="px-6 py-4">${divisi.NAMA_DIVISI}</td>
                    <td class="px-6 py-4">${divisi.CREATE_BY ?? '-'}</td>
                    <td class="px-6 py-4 text-center space-x-2">
                        <button onclick="editDivisi(${divisi.ID_DIVISI})" class="text-blue-600 hover:text-blue-800"><i class="fas fa-edit"></i></button>
                        <button onclick="deleteDivisi(${divisi.ID_DIVISI})" class="text-red-600 hover:text-red-800"><i class="fas fa-trash"></i></button>
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
        // if (forField === 'updateBy') document.getElementById('updateBy').value = data.username || '';
    } catch(err) {
        console.error(err);
    }
}

// ==== Add Modal ====
addBtn.addEventListener("click", () => {
    modal.classList.add("show");
    document.getElementById("modalTitle").innerText = "Tambah Divisi";
    form.reset();
    document.getElementById("divisiId").value = "";
    loadUsername('createBy'); // otomatis isi CREATE_BY
});
closeModal.addEventListener("click", () => modal.classList.remove("show"));
cancelBtn.addEventListener("click", () => modal.classList.remove("show"));

// ==== Submit Form ====
form.addEventListener("submit", async function(e) {
    e.preventDefault();

    const id = document.getElementById("divisiId").value;
    const payload = {
        NAMA_DIVISI: document.getElementById("namaDivisi").value,
        CREATE_BY: document.getElementById("createBy").value
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

        if (res.ok) {
            showToast("Data berhasil disimpan");
            modal.classList.remove("show");
            loadDivisi();
        } else {
            showToast("Gagal menyimpan data");
        }
    } catch(err) {
        console.error(err);
        showToast("Terjadi kesalahan");
    }
});

// ==== Edit Divisi ====
async function editDivisi(id) {
    try {
        const res = await fetch(`${apiUrl}/${id}`, {
            headers: { 'Authorization': `Bearer ${token}` }
        });
        const json = await res.json();
        const data = json.data ?? json;

        modal.classList.add("show");
        document.getElementById("modalTitle").innerText = "Edit Divisi";

        document.getElementById("divisiId").value = data.ID_DIVISI;
        document.getElementById("namaDivisi").value = data.NAMA_DIVISI;
        document.getElementById("createBy").value = data.CREATE_BY ?? "";
    } catch(err) {
        console.error(err);
        showToast("Gagal memuat data edit");
    }
}

// ==== Delete Divisi ====
async function deleteDivisi(id) {
    if (!confirm("Yakin ingin menghapus data ini?")) return;
    const res = await fetch(`${apiUrl}/${id}`, {
        method: "DELETE",
        headers: { 'Authorization': `Bearer ${token}` }
    });
    if (res.ok) {
        showToast("Data berhasil dihapus");
        loadDivisi();
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
        const res = await fetch("/api/divisi/import", {
            method: "POST",
            headers: { 'Authorization': `Bearer ${token}` },
            body: formData
        });
        const data = await res.json();
        if (res.ok) {
            showToast(data.message || "Import berhasil");
            modal.classList.remove("show");
            loadDivisi();
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

// ==== Search ====
let searchTimeout = null;
document.getElementById("searchInput").addEventListener("input", function() {
    clearTimeout(searchTimeout);
    let keyword = this.value;
    searchTimeout = setTimeout(() => loadDivisi(keyword), 500);
});

loadDivisi();
</script>

</body>
</html>
