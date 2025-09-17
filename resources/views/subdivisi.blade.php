<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pelindo Multi Terminal - Subdivisi Management</title>
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

    <!-- Main Content -->
    <main class="container mx-auto px-4 py-6">
        <!-- Controls -->
        <div class="bg-white rounded-lg shadow-lg p-4 mb-6">
            <div class="flex items-center justify-between">
                <button id="addSubdivisiBtn" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center space-x-2">
                    <i class="fas fa-plus"></i>
                    <span>Tambah Subdivisi</span>
                </button>

                <div class="flex items-center space-x-4">
                    <input type="text" id="searchInput" placeholder="Cari subdivisi..." class="border border-gray-300 rounded-lg px-3 py-2 w-64">
                    <button id="searchBtn" class="bg-gray-100 hover:bg-gray-200 p-2 rounded-lg">
                        <i class="fas fa-search text-gray-600"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Subdivisi Table -->
        <div class="bg-white rounded-lg shadow-sm overflow-hidden">
            <table class="w-full">
                <thead class="bg-blue-600 text-white">
                    <tr>
                        <th class="px-6 py-3 text-left text-sm font-medium">NO</th>
                        <th class="px-6 py-3 text-left text-sm font-medium">Nama Subdivisi</th>
                        <th class="px-6 py-3 text-left text-sm font-medium">Divisi</th>
                        <th class="px-6 py-3 text-left text-sm font-medium">Dibuat Oleh</th>
                        <th class="px-6 py-3 text-center text-sm font-medium">Aksi</th>
                    </tr>
                </thead>
                <tbody id="subdivisiTableBody" class="divide-y divide-gray-200"></tbody>
            </table>
        </div>

        <!-- Loading & Empty State -->
        <div id="loadingState" class="text-center py-8">
            <i class="fas fa-spinner fa-spin text-2xl text-blue-600"></i>
            <p class="mt-2 text-gray-600">Memuat data...</p>
        </div>
        <div id="emptyState" class="text-center py-8 hidden">
            <i class="fas fa-inbox text-4xl text-gray-400 mb-4"></i>
            <p class="text-gray-600">Tidak ada data subdivisi</p>
        </div>
    </main>

    <!-- Modal Add/Edit -->
    <div id="subdivisiModal" class="modal fixed inset-0 bg-black bg-opacity-50 items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 w-full max-w-md mx-4">
            <div class="flex items-center justify-between mb-4">
                <h3 id="modalTitle" class="text-lg font-semibold">Tambah Subdivisi</h3>
                <button id="closeModal" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <form id="subdivisiForm">
                <input type="hidden" id="subdivisiId">
                
                <!-- Dropdown Divisi -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Divisi</label>
                    <select id="divisiSelect" required class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500">
                        <option value="">-- Pilih Divisi --</option>
                    </select>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nama Subdivisi</label>
                    <input type="text" id="namaSubdivisi" required class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500">
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Dibuat Oleh</label>
                    <input type="text" id="createBy" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500">
                </div>

                <div id="formErrors" class="text-red-600 text-sm mb-3 hidden"></div>

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
        const apiUrl = "/api/m_subdivisi"; 
        const apiDivisiUrl = "/api/m_divisi"; 
        const tableBody = document.getElementById("subdivisiTableBody");
        const loadingState = document.getElementById("loadingState");
        const emptyState = document.getElementById("emptyState");

        const modal = document.getElementById("subdivisiModal");
        const addBtn = document.getElementById("addSubdivisiBtn");
        const closeModal = document.getElementById("closeModal");
        const cancelBtn = document.getElementById("cancelBtn");
        const form = document.getElementById("subdivisiForm");

        const toast = document.getElementById("toast");
        const toastMessage = document.getElementById("toastMessage");
        const formErrors = document.getElementById('formErrors');

        // ==== Load Divisi into Select ====
        async function loadDivisiOptions() {
            try {
                let res = await fetch(apiDivisiUrl);
                if (!res.ok) throw new Error('Gagal memuat data divisi');
                let data = await res.json();
                const select = document.getElementById("divisiSelect");
                select.innerHTML = `<option value="">-- Pilih Divisi --</option>`;
                data.forEach(div => {
                    select.insertAdjacentHTML("beforeend", `<option value="${div.ID_DIVISI}">${div.NAMA_DIVISI}</option>`);
                });
            } catch (err) {
                console.error(err);
                showToast('Gagal memuat daftar divisi');
            }
        }

        // ==== Fetch Subdivisi ====
        async function loadSubdivisi() {
            loadingState.classList.remove("hidden");
            emptyState.classList.add("hidden");
            tableBody.innerHTML = "";

            try {
                let res = await fetch(apiUrl);
                let data = await res.json();

                loadingState.classList.add("hidden");

                if (!Array.isArray(data) || data.length === 0) {
                    emptyState.classList.remove("hidden");
                    return;
                }

                data.forEach((sub, i) => {
                    const divisiName = sub.divisi && sub.divisi.NAMA_DIVISI ? sub.divisi.NAMA_DIVISI : (sub.ID_DIVISI ?? '-');
                    let row = `
                        <tr>
                            <td class="px-6 py-4">${i+1}</td>
                            <td class="px-6 py-4">${sub.NAMA_SUBDIVISI}</td>
                            <td class="px-6 py-4">${divisiName}</td>
                            <td class="px-6 py-4">${sub.CREATE_BY ?? '-'}</td>
                            <td class="px-6 py-4 text-center space-x-2">
                                <button onclick="editSubdivisi(${sub.ID_SUBDIVISI})" class="text-blue-600 hover:text-blue-800"><i class="fas fa-edit"></i></button>
                                <button onclick="deleteSubdivisi(${sub.ID_SUBDIVISI})" class="text-red-600 hover:text-red-800"><i class="fas fa-trash"></i></button>
                            </td>
                        </tr>
                    `;
                    tableBody.insertAdjacentHTML("beforeend", row);
                });
            } catch (err) {
                console.error("Error:", err);
                loadingState.classList.add("hidden");
                showToast('Terjadi kesalahan saat memuat data');
            }
        }

        // ==== Add/Edit ====
        addBtn.addEventListener("click", async () => {
            modal.classList.add("show");
            document.getElementById("modalTitle").innerText = "Tambah Subdivisi";
            form.reset();
            formErrors.classList.add('hidden');
            document.getElementById("subdivisiId").value = "";
            await loadDivisiOptions();
        });
        closeModal.addEventListener("click", () => modal.classList.remove("show"));
        cancelBtn.addEventListener("click", () => modal.classList.remove("show"));

        form.addEventListener("submit", async (e) => {
            e.preventDefault();
            formErrors.classList.add('hidden');
            let id = document.getElementById("subdivisiId").value;
            let payload = {
                ID_DIVISI: document.getElementById("divisiSelect").value,
                NAMA_SUBDIVISI: document.getElementById("namaSubdivisi").value,
                CREATE_BY: document.getElementById("createBy").value,
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
                    loadSubdivisi();
                } else if (res.status === 422) {
                    const err = await res.json();
                    const messages = [];
                    Object.values(err.errors || {}).forEach(arr => messages.push(...arr));
                    formErrors.innerText = messages.join('\n');
                    formErrors.classList.remove('hidden');
                } else {
                    const text = await res.text();
                    console.error('Server error:', text);
                    showToast('Gagal menyimpan data');
                }
            } catch (err) {
                console.error(err);
                showToast('Terjadi kesalahan');
            }
        });

        // ==== Edit Function ====
        async function editSubdivisi(id) {
            try {
                let res = await fetch(`${apiUrl}/${id}`);
                if (!res.ok) { showToast('Gagal memuat data subdivisi'); return; }
                let data = await res.json();

                modal.classList.add("show");
                document.getElementById("modalTitle").innerText = "Edit Subdivisi";
                document.getElementById("subdivisiId").value = data.ID_SUBDIVISI;
                document.getElementById("namaSubdivisi").value = data.NAMA_SUBDIVISI;
                document.getElementById("createBy").value = data.CREATE_BY ?? "";
                await loadDivisiOptions();
                // pastikan value dipilih setelah opsi terisi
                document.getElementById("divisiSelect").value = data.ID_DIVISI;
            } catch (err) {
                console.error(err);
                showToast('Terjadi kesalahan saat memuat data edit');
            }
        }

        // ==== Delete Function ====
        async function deleteSubdivisi(id) {
            if (!confirm("Yakin ingin menghapus data ini?")) return;

            let res = await fetch(`${apiUrl}/${id}`, { method: "DELETE" });
            if (res.ok) {
                showToast("Data berhasil dihapus");
                loadSubdivisi();
            } else {
                showToast('Gagal menghapus data');
            }
        }

        // ==== Toast ====
        function showToast(msg) {
            toastMessage.innerText = msg;
            toast.classList.add("show");
            setTimeout(() => toast.classList.remove("show"), 3000);
        }

        // Load pertama kali
        loadSubdivisi();
    </script>
</body>
</html>
