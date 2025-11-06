<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pelindo Multi Terminal - Parameter Management</title>
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

<main class="container mx-auto px-4 py-6">
    <!-- Controls -->
    <div class="bg-white rounded-lg shadow-lg p-4 mb-6">
        <div class="flex items-center justify-between">
            <button id="addParameterBtn" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center space-x-2">
                <i class="fas fa-plus"></i>
                <span>Tambah Parameter</span>
            </button>
            <!-- <div class="flex items-center space-x-4">
                <input 
                    id="searchInput" 
                    type="text" 
                    placeholder="Cari..." 
                    class="border px-2 py-1" 
                />
            </div>

            <script>
            let searchTimeout = null;

            document.getElementById("searchInput").addEventListener("input", function() {
                clearTimeout(searchTimeout);
                let keyword = this.value;
                searchTimeout = setTimeout(() => {
                    loadParameters(keyword);
                }, 500); 
            });
            </script> -->
        </div>
    </div>

    <!-- Parameter Table -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <table class="w-full">
            <thead class="bg-blue-600 text-white">
                <tr>
                    <th class="px-6 py-3 text-left text-sm font-medium">NO</th>
                    <th class="px-6 py-3 text-left text-sm font-medium">Nilai Parameter</th>
                    <th class="px-6 py-3 text-left text-sm font-medium">Keterangan</th>
                    <th class="px-6 py-3 text-left text-sm font-medium">Dibuat Oleh</th>
                    <th class="px-6 py-3 text-center text-sm font-medium">Aksi</th>
                </tr>
            </thead>
            <tbody id="parameterTableBody" class="divide-y divide-gray-200"></tbody>
        </table>
    </div>

    <!-- Loading & Empty -->
    <div id="loadingState" class="text-center py-8">
        <i class="fas fa-spinner fa-spin text-2xl text-blue-600"></i>
        <p class="mt-2 text-gray-600">Memuat data...</p>
    </div>
    <div id="emptyState" class="text-center py-8 hidden">
        <i class="fas fa-inbox text-4xl text-gray-400 mb-4"></i>
        <p class="text-gray-600">Tidak ada data parameter</p>
    </div>
</main>

<!-- Modal Add/Edit -->
<div id="parameterModal" class="modal fixed inset-0 bg-black bg-opacity-50 items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 w-full max-w-md mx-4">
        <div class="flex items-center justify-between mb-4">
            <h3 id="modalTitle" class="text-lg font-semibold">Tambah Parameter</h3>
            <button id="closeModal" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <form id="parameterForm">
            <input type="hidden" id="parameterId">
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Nilai Parameter</label>
                <input type="text" id="nilaiParameter" required class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500">
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Keterangan</label>
                <input type="text" id="keterangan" class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500">
            </div>
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Dibuat Oleh</label>
                <input type="text" id="createBy" class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500">
            </div>
            <div id="formErrors" class="text-red-600 text-sm mb-3 hidden"></div>
            <div class="flex space-x-3">
                <button type="button" id="cancelBtn" class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-700 py-2 rounded-lg">Batal</button>
                <button type="submit" id="saveBtn" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white py-2 rounded-lg">Simpan</button>
            </div>
        </form>
    </div>
</div>

<!-- Toast -->
<div id="toast" class="toast fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50">
    <div class="flex items-center space-x-2">
        <i id="toastIcon" class="fas fa-check-circle"></i>
        <span id="toastMessage">Pesan berhasil</span>
    </div>
</div>

<script>
    const apiUrl = "/api/m_parameter"; 
    let allData = [];
    const tableBody = document.getElementById("parameterTableBody");
    const loadingState = document.getElementById("loadingState");
    const emptyState = document.getElementById("emptyState");

    const modal = document.getElementById("parameterModal");
    const addBtn = document.getElementById("addParameterBtn");
    const closeModal = document.getElementById("closeModal");
    const cancelBtn = document.getElementById("cancelBtn");
    const form = document.getElementById("parameterForm");
    const formErrors = document.getElementById("formErrors");

    const toast = document.getElementById("toast");
    const toastMessage = document.getElementById("toastMessage");

    // load data
    async function loadParameters(keyword = "") {
    loadingState.classList.remove("hidden");
    emptyState.classList.add("hidden");
    tableBody.innerHTML = "";

    try {
        let url = apiUrl;
        if (keyword && keyword.trim() !== "") {
            url += `?search=${encodeURIComponent(keyword)}`;
        }

        let res = await fetch(url);
        let data = await res.json();

        loadingState.classList.add("hidden");

        if (!Array.isArray(data) || data.length === 0) {
            emptyState.classList.remove("hidden");
            return;
        }

        data.forEach((parameter, i) => {
            let row = `
                <tr>
                    <td class="px-6 py-4">${i + 1}</td>
                    <td class="px-6 py-4">${parameter.Nilai_parameter}</td>
                    <td class="px-6 py-4">${parameter.keterangan ?? '-'}</td>
                    <td class="px-6 py-4">${parameter.create_by ?? '-'}</td>
                    <td class="px-6 py-4 text-center space-x-2">
                        <button onclick="editParameter(${parameter.ID_PARAMETER})" class="text-blue-600 hover:text-blue-800"><i class="fas fa-edit"></i></button>
                        <button onclick="deleteParameter(${parameter.ID_PARAMETER})" class="text-red-600 hover:text-red-800"><i class="fas fa-trash"></i></button>
                    </td>
                </tr>
            `;
            tableBody.insertAdjacentHTML("beforeend", row);
        });
    } catch (err) {
        console.error("Error:", err);
        loadingState.classList.add("hidden");
        showToast("Gagal memuat data");
    }


    }

    // ==== Add/Edit ====
    addBtn.addEventListener("click", () => {
        modal.classList.add("show");
        document.getElementById("modalTitle").innerText = "Tambah Parameter";
        form.reset();
        formErrors.classList.add("hidden");
        document.getElementById("parameterId").value = "";
    });
    closeModal.addEventListener("click", () => modal.classList.remove("show"));
    cancelBtn.addEventListener("click", () => modal.classList.remove("show"));

    form.addEventListener("submit", async (e) => {
        e.preventDefault();
        formErrors.classList.add("hidden");
        let id = document.getElementById("parameterId").value;
        let payload = {
            Nilai_parameter: document.getElementById("nilaiParameter").value,
            keterangan: document.getElementById("keterangan").value,
            create_by: document.getElementById("createBy").value,
        };

        try {
            let res;
            if (id) {
                res = await fetch(`${apiUrl}/${id}`, {
                    method: "PUT",
                    headers: { "Content-Type": "application/json" },
                    body: JSON.stringify(payload),
                });
            } else {
                res = await fetch(apiUrl, {
                    method: "POST",
                    headers: { "Content-Type": "application/json" },
                    body: JSON.stringify(payload),
                });
            }

            if (res.ok) {
                showToast("Data berhasil disimpan");
                modal.classList.remove("show");
                loadParameters();
            } else if (res.status === 422) {
                const err = await res.json();
                const messages = [];
                Object.values(err.errors || {}).forEach(arr => messages.push(...arr));
                formErrors.innerText = messages.join('\n');
                formErrors.classList.remove("hidden");
            } else {
                showToast("Gagal menyimpan data");
            }
        } catch (err) {
            console.error(err);
            showToast("Terjadi kesalahan");
        }
    });

    // ==== Edit Function ====
    async function editParameter(id) {
        try {
            let res = await fetch(`${apiUrl}/${id}`);
            let data = await res.json();

            modal.classList.add("show");
            document.getElementById("modalTitle").innerText = "Edit Parameter";
            document.getElementById("parameterId").value = data.ID_PARAMETER;
            document.getElementById("nilaiParameter").value = data.Nilai_parameter;
            document.getElementById("keterangan").value = data.keterangan ?? "";
            document.getElementById("createBy").value = data.create_by ?? "";
        } catch (err) {
            console.error(err);
            showToast("Gagal memuat data edit");
        }
    }

    // ==== Delete Function ====
    async function deleteParameter(id) {
        if (!confirm("Yakin ingin menghapus data ini?")) return;
        let res = await fetch(`${apiUrl}/${id}`, { method: "DELETE" });
        if (res.ok) {
            showToast("Data berhasil dihapus");
            loadParameters();
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

    loadParameters();
</script>
</body>
</html>
