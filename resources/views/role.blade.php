<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pelindo Multi Terminal - Role Management</title>
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

    <header class="bg-white shadow-lg h-20">
</header>

    <!-- Main Content -->
    <main class="container mx-auto px-4 py-6">
        <!-- Controls -->
        <div class="bg-white rounded-lg shadow-lg p-4 mb-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <button id="addRoleBtn" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center space-x-2">
                        <i class="fas fa-plus"></i>
                        <span>Tambah Role</span>
                    </button>

                    <a href="{{ url('/api/role/export') }}" 
               class="rounded bg-green-600 hover:bg-green-700 text-white px-4 py-2 flex items-center space-x-2">
                Export Excel <i class="fas fa-file-excel"></i>
            </a>
                </div>
                <!-- <div class="flex items-center space-x-4">
                    <input type="text" id="searchInput" placeholder="Cari role..." class="border border-gray-300 rounded-lg px-3 py-2 w-64">
                    <button id="searchBtn" class="bg-gray-100 hover:bg-gray-200 p-2 rounded-lg">
                        <i class="fas fa-search text-gray-600"></i>
                    </button>
                </div> -->
            </div>
        </div>

        <!-- Role Table -->
        <div class="bg-white rounded-lg shadow-sm overflow-hidden">
            <table class="w-full">
                <thead class="bg-blue-600 text-white">
                    <tr>
                        <th class="px-6 py-3 text-left text-sm font-medium">ID</th>
                        <th class="px-6 py-3 text-left text-sm font-medium">Nama Role</th>
                        <th class="px-6 py-3 text-left text-sm font-medium">Keterangan</th>
                        <th class="px-6 py-3 text-center text-sm font-medium">Aksi</th>
                    </tr>
                </thead>
                <tbody id="roleTableBody" class="divide-y divide-gray-200"></tbody>
            </table>
        </div>

        <!-- Loading & Empty State -->
        <div id="loadingState" class="text-center py-8">
            <i class="fas fa-spinner fa-spin text-2xl text-blue-600"></i>
            <p class="mt-2 text-gray-600">Memuat data...</p>
        </div>
        <div id="emptyState" class="text-center py-8 hidden">
            <i class="fas fa-inbox text-4xl text-gray-400 mb-4"></i>
            <p class="text-gray-600">Tidak ada data role</p>
        </div>
    </main>

    <!-- Modal Add/Edit -->
    <div id="roleModal" class="modal fixed inset-0 bg-black bg-opacity-50 items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 w-full max-w-md mx-4">
            <div class="flex items-center justify-between mb-4">
                <h3 id="modalTitle" class="text-lg font-semibold">Tambah Role</h3>
                <button id="closeModal" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <form id="roleForm">
                <input type="hidden" id="roleIdRole"> <!-- ganti hidden input jadi ID_ROLE -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nama Role</label>
                    <input type="text" id="namaRole" required class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Keterangan</label>
                    <input type="text" id="keteranganRole" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="flex space-x-3">
                    <button type="button" id="cancelBtn" class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-700 py-2 px-4 rounded-lg">Batal</button>
                    <button type="submit" id="saveBtn" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded-lg">Simpan</button>
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
    const apiUrl = "/api/m_role"; 
    const tableBody = document.getElementById("roleTableBody");
    const loadingState = document.getElementById("loadingState");
    const emptyState = document.getElementById("emptyState");

    const modal = document.getElementById("roleModal");
    const addBtn = document.getElementById("addRoleBtn");
    const closeModal = document.getElementById("closeModal");
    const cancelBtn = document.getElementById("cancelBtn");
    const form = document.getElementById("roleForm");

    const toast = document.getElementById("toast");
    const toastMessage = document.getElementById("toastMessage");

    // === Ambil token dari localStorage ===
    const token = localStorage.getItem("auth_token");

    // ==== Fetch Data ====
    async function loadRoles() {
        loadingState.classList.remove("hidden");
        emptyState.classList.add("hidden");
        tableBody.innerHTML = "";

        try {
            let res = await fetch(apiUrl, {
                headers: {
                    "Authorization": `Bearer ${token}`,
                    "Accept": "application/json"
                }
            });
            let data = await res.json();

            loadingState.classList.add("hidden");

            if (data.length === 0) {
                emptyState.classList.remove("hidden");
                return;
            }

            data.forEach((role) => {
                let row = `
                    <tr>
                        <td class="px-6 py-4">${role.ID_ROLE}</td>
                        <td class="px-6 py-4">${role.Nama_role}</td>
                        <td class="px-6 py-4">${role.keterangan ?? '-'}</td>
                        <td class="px-6 py-4 text-center space-x-2">
                            <button onclick="editRole(${role.ID_ROLE})" class="text-blue-600 hover:text-blue-800"><i class="fas fa-edit"></i></button>
                            <button onclick="deleteRole(${role.ID_ROLE})" class="text-red-600 hover:text-red-800"><i class="fas fa-trash"></i></button>
                        </td>
                    </tr>
                `;
                tableBody.insertAdjacentHTML("beforeend", row);
            });
        } catch (err) {
            console.error("Error:", err);
        }
    }

    // ==== Add/Edit ====
    addBtn.addEventListener("click", () => {
        modal.classList.add("show");
        document.getElementById("modalTitle").innerText = "Tambah Role";
        form.reset();
        document.getElementById("roleIdRole").value = "";
    });
    closeModal.addEventListener("click", () => modal.classList.remove("show"));
    cancelBtn.addEventListener("click", () => modal.classList.remove("show"));

    form.addEventListener("submit", async (e) => {
        e.preventDefault();
        let ID_ROLE = document.getElementById("roleIdRole").value;
        let payload = {
            Nama_role: document.getElementById("namaRole").value,
            keterangan: document.getElementById("keteranganRole").value,
            create_by: "admin"
        };

        try {
            let res;
            if (ID_ROLE) {
                res = await fetch(`${apiUrl}/${ID_ROLE}`, {
                    method: "PUT",
                    headers: {
                        "Authorization": `Bearer ${token}`,
                        "Accept": "application/json",
                        "Content-Type": "application/json"
                    },
                    body: JSON.stringify(payload),
                });
            } else {
                res = await fetch(apiUrl, {
                    method: "POST",
                    headers: {
                        "Authorization": `Bearer ${token}`,
                        "Accept": "application/json",
                        "Content-Type": "application/json"
                    },
                    body: JSON.stringify(payload),
                });
            }
            if (res.ok) {
                showToast("Data berhasil disimpan");
                modal.classList.remove("show");
                loadRoles();
            }
        } catch (err) {
            console.error(err);
        }
    });

    // ==== Edit Function ====
    async function editRole(ID_ROLE) {
        let res = await fetch(`${apiUrl}/${ID_ROLE}`, {
            headers: {
                "Authorization": `Bearer ${token}`,
                "Accept": "application/json"
            }
        });
        let data = await res.json();

        modal.classList.add("show");
        document.getElementById("modalTitle").innerText = "Edit Role";
        document.getElementById("roleIdRole").value = data.ID_ROLE;
        document.getElementById("namaRole").value = data.Nama_role;
        document.getElementById("keteranganRole").value = data.keterangan ?? '';
    }

    // ==== Delete Function ====
    async function deleteRole(ID_ROLE) {
        if (!confirm("Yakin ingin menghapus data ini?")) return;

        let res = await fetch(`${apiUrl}/${ID_ROLE}`, {
            method: "DELETE",
            headers: {
                "Authorization": `Bearer ${token}`,
                "Accept": "application/json"
            }
        });
        if (res.ok) {
            showToast("Data berhasil dihapus");
            loadRoles();
        }
    }

    // ==== Toast ====
    function showToast(msg) {
        toastMessage.innerText = msg;
        toast.classList.add("show");
        setTimeout(() => toast.classList.remove("show"), 3000);
    }

    // Load pertama kali
    loadRoles();
</script>
</body>
</html>
