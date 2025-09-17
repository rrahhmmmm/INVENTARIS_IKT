<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pelindo Multi Terminal - Management System</title>
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

    <!-- Header -->
    <header class="bg-white shadow-lg h-20">
</header>

    <!-- Main Content -->
    <main class="container mx-auto px-4 py-6">
        <!-- Controls -->
        <div class="bg-white rounded-lg shadow-lg p-4 mb-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <button id="addTerminalBtn" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center space-x-2">
                        <i class="fas fa-plus"></i>
                        <span>Tambah Terminal</span>
                    </button>
                </div>
                <div class="flex items-center space-x-4">
                    <input type="text" id="searchInput" placeholder="Cari terminal..." class="border border-gray-300 rounded-lg px-3 py-2 w-64">
                    <button id="searchBtn" class="bg-gray-100 hover:bg-gray-200 p-2 rounded-lg">
                        <i class="fas fa-search text-gray-600"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Terminal Table -->
        <div class="bg-white rounded-lg shadow-sm overflow-hidden">
            <table class="w-full">
                <thead class="bg-blue-600 text-white">
                    <tr>
                        <th class="px-6 py-3 text-left text-sm font-medium">NO</th>
                        <th class="px-6 py-3 text-left text-sm font-medium">Kode Terminal</th>
                        <th class="px-6 py-3 text-left text-sm font-medium">Nama Terminal</th>
                        <th class="px-6 py-3 text-left text-sm font-medium">Lokasi Terminal</th>
                        <th class="px-6 py-3 text-center text-sm font-medium">Aksi</th>
                    </tr>
                </thead>
                <tbody id="terminalTableBody" class="divide-y divide-gray-200"></tbody>
            </table>
        </div>

        <!-- Loading & Empty State -->
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
        <div class="bg-white rounded-lg p-6 w-full max-w-md mx-4">
            <div class="flex items-center justify-between mb-4">
                <h3 id="modalTitle" class="text-lg font-semibold">Tambah Terminal</h3>
                <button id="closeModal" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <form id="terminalForm">
                <input type="hidden" id="terminalId">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Kode Terminal</label>
                    <input type="text" id="kodeTerminal" required class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nama Terminal</label>
                    <input type="text" id="namaTerminal" required class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Lokasi Terminal</label>
                    <input type="text" id="lokasiTerminal" required class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500">
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
        const apiUrl = "/api/m_terminal"; 
        const tableBody = document.getElementById("terminalTableBody");
        const loadingState = document.getElementById("loadingState");
        const emptyState = document.getElementById("emptyState");

        const modal = document.getElementById("terminalModal");
        const addBtn = document.getElementById("addTerminalBtn");
        const closeModal = document.getElementById("closeModal");
        const cancelBtn = document.getElementById("cancelBtn");
        const form = document.getElementById("terminalForm");

        const toast = document.getElementById("toast");
        const toastMessage = document.getElementById("toastMessage");

        // ==== Fetch Data ====
        async function loadTerminals() {
            loadingState.classList.remove("hidden");
            emptyState.classList.add("hidden");
            tableBody.innerHTML = "";

            try {
                let res = await fetch(apiUrl);
                let data = await res.json();

                loadingState.classList.add("hidden");

                if (data.length === 0) {
                    emptyState.classList.remove("hidden");
                    return;
                }

                data.forEach((terminal, i) => {
                    let row = `
                        <tr>
                            <td class="px-6 py-4">${i+1}</td>
                            <td class="px-6 py-4">${terminal.KODE_TERMINAL}</td>
                            <td class="px-6 py-4">${terminal.NAMA_TERMINAL}</td>
                            <td class="px-6 py-4">${terminal.LOKASI}</td>
                            <td class="px-6 py-4 text-center space-x-2">
                                <button onclick="editTerminal(${terminal.ID_TERMINAL})" class="text-blue-600 hover:text-blue-800"><i class="fas fa-edit"></i></button>
                                <button onclick="deleteTerminal(${terminal.ID_TERMINAL})" class="text-red-600 hover:text-red-800"><i class="fas fa-trash"></i></button>
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
            document.getElementById("modalTitle").innerText = "Tambah Terminal";
            form.reset();
            document.getElementById("terminalId").value = "";
        });
        closeModal.addEventListener("click", () => modal.classList.remove("show"));
        cancelBtn.addEventListener("click", () => modal.classList.remove("show"));

        form.addEventListener("submit", async (e) => {
            e.preventDefault();
            let id = document.getElementById("terminalId").value;
            let payload = {
                KODE_TERMINAL: document.getElementById("kodeTerminal").value,
                NAMA_TERMINAL: document.getElementById("namaTerminal").value,
                LOKASI: document.getElementById("lokasiTerminal").value,
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
                    loadTerminals();
                }
            } catch (err) {
                console.error(err);
            }
        });

        // ==== Edit Function ====
        async function editTerminal(id) {
            let res = await fetch(`${apiUrl}/${id}`);
            let data = await res.json();

            modal.classList.add("show");
            document.getElementById("modalTitle").innerText = "Edit Terminal";
            document.getElementById("terminalId").value = data.ID_TERMINAL;
            document.getElementById("kodeTerminal").value = data.KODE_TERMINAL;
            document.getElementById("namaTerminal").value = data.NAMA_TERMINAL;
            document.getElementById("lokasiTerminal").value = data.LOKASI;
        }

        // ==== Delete Function ====
        async function deleteTerminal(id) {
            if (!confirm("Yakin ingin menghapus data ini?")) return;

            let res = await fetch(`${apiUrl}/${id}`, { method: "DELETE" });
            if (res.ok) {
                showToast("Data berhasil dihapus");
                loadTerminals();
            }
        }

        // ==== Toast ====
        function showToast(msg) {
            toastMessage.innerText = msg;
            toast.classList.add("show");
            setTimeout(() => toast.classList.remove("show"), 3000);
        }

        // Load pertama kali
        loadTerminals();
    </script>
</body>
</html>
