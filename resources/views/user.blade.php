<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Pelindo Multi Terminal - User Management</title>
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
    <div class="bg-white rounded-lg shadow-lg p-4 mb-6 flex justify-between items-center">
      <div class="flex space-x-2">
        <input type="text" id="searchInput" placeholder="Cari user..." class="border border-gray-300 rounded-lg px-3 py-2 w-64">
        <button id="searchBtn" class="bg-gray-100 hover:bg-gray-200 p-2 rounded-lg">
          <i class="fas fa-search text-gray-600"></i>
        </button>
      </div>
      <button id="addUserBtn" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
        <i class="fas fa-plus mr-2"></i>Tambah User
      </button>
    </div>

    <!-- User Table -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
      <table class="w-full">
        <thead class="bg-blue-600 text-white">
          <tr>
            <th class="px-6 py-3 text-left">ID</th>
            <th class="px-6 py-3 text-left">Username</th>
            <th class="px-6 py-3 text-left">Email</th>
            <th class="px-6 py-3 text-left">Nama Lengkap</th>
            <th class="px-6 py-3 text-left">Divisi</th>
            <th class="px-6 py-3 text-left">Subdivisi</th>
            <th class="px-6 py-3 text-left">Role</th>
            <th class="px-6 py-3 text-center">Aksi</th>
          </tr>
        </thead>
        <tbody id="userTableBody" class="divide-y divide-gray-200"></tbody>
      </table>
    </div>

    <!-- Loading & Empty -->
    <div id="loadingState" class="text-center py-8">
      <i class="fas fa-spinner fa-spin text-2xl text-blue-600"></i>
      <p class="mt-2 text-gray-600">Memuat data...</p>
    </div>
    <div id="emptyState" class="text-center py-8 hidden">
      <i class="fas fa-inbox text-4xl text-gray-400 mb-4"></i>
      <p class="text-gray-600">Tidak ada data user</p>
    </div>
  </main>

  <!-- Modal Add/Edit -->
  <div id="userModal" class="modal fixed inset-0 bg-black bg-opacity-50 items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 w-full max-w-lg mx-4">
      <div class="flex justify-between items-center mb-4">
        <h3 id="modalTitle" class="text-lg font-semibold">Tambah User</h3>
        <button id="closeModal" class="text-gray-400 hover:text-gray-600">
          <i class="fas fa-times"></i>
        </button>
      </div>

      <form id="userForm">
        <input type="hidden" id="userId">

        <div class="mb-4">
          <label class="block text-sm font-medium">Username</label>
          <input type="text" id="username" required class="w-full border rounded-lg px-3 py-2">
        </div>
        <div class="mb-4">
          <label class="block text-sm font-medium">Email</label>
          <input type="email" id="email" required class="w-full border rounded-lg px-3 py-2">
        </div>
        <div class="mb-4">
          <label class="block text-sm font-medium">Nama Lengkap</label>
          <input type="text" id="full_name" class="w-full border rounded-lg px-3 py-2">
        </div>

        <div class="mb-4">
          <label class="block text-sm font-medium">Divisi</label>
          <select id="ID_DIVISI" class="w-full border rounded-lg px-3 py-2"></select>
        </div>
        <div class="mb-4">
          <label class="block text-sm font-medium">Subdivisi</label>
          <select id="ID_SUBDIVISI" class="w-full border rounded-lg px-3 py-2"></select>
        </div>
        <div class="mb-4">
          <label class="block text-sm font-medium">Role</label>
          <select id="ID_ROLE" class="w-full border rounded-lg px-3 py-2"></select>
        </div>

        <div class="flex space-x-2 mt-6">
          <button type="button" id="cancelBtn" class="flex-1 bg-gray-300 hover:bg-gray-400 py-2 rounded-lg">Batal</button>
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
    const apiUrl = "/api/m_user";
    const roleApi = "/api/m_role";
    const divisiApi = "/api/m_divisi";
    const subdivisiApi = "/api/m_subdivisi";

    const tableBody = document.getElementById("userTableBody");
    const loadingState = document.getElementById("loadingState");
    const emptyState = document.getElementById("emptyState");

    const modal = document.getElementById("userModal");
    const addBtn = document.getElementById("addUserBtn");
    const closeModal = document.getElementById("closeModal");
    const cancelBtn = document.getElementById("cancelBtn");
    const form = document.getElementById("userForm");

    const toast = document.getElementById("toast");
    const toastMessage = document.getElementById("toastMessage");

    // Load Roles
    async function loadRolesSelect(selected = "") {
      let res = await fetch(roleApi);
      let roles = await res.json();
      let select = document.getElementById("ID_ROLE");
      select.innerHTML = '<option value="">-- Pilih Role --</option>';
      roles.forEach(r => {
        select.innerHTML += `<option value="${r.ID_ROLE}" ${selected == r.ID_ROLE ? "selected" : ""}>${r.Nama_role}</option>`;
      });
    }

    // Load Divisi
    async function loadDivisiSelect(selected = "") {
      let res = await fetch(divisiApi);
      let divisi = await res.json();
      let select = document.getElementById("ID_DIVISI");
      select.innerHTML = '<option value="">-- Pilih Divisi --</option>';
      divisi.forEach(d => {
        select.innerHTML += `<option value="${d.ID_DIVISI}" ${selected == d.ID_DIVISI ? "selected" : ""}>${d.NAMA_DIVISI}</option>`;
      });
    }

    // Load Subdivisi
    async function loadSubdivisiSelect(divisiId, selected = "") {
      let select = document.getElementById("ID_SUBDIVISI");
      if (!divisiId) {
        select.innerHTML = '<option value="">-- Pilih Subdivisi --</option>';
        return;
      }
      let res = await fetch(`${subdivisiApi}/divisi/${divisiId}`);
      let subdivisi = await res.json();
      select.innerHTML = '<option value="">-- Pilih Subdivisi --</option>';
      subdivisi.forEach(s => {
        select.innerHTML += `<option value="${s.ID_SUBDIVISI}" ${selected == s.ID_SUBDIVISI ? "selected" : ""}>${s.NAMA_SUBDIVISI}</option>`;
      });
    }

    // Load Users
    async function loadUsers() {
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

        data.forEach(user => {
          let row = `
            <tr>
              <td class="px-6 py-4">${user.ID_USER}</td>
              <td class="px-6 py-4">${user.username}</td>
              <td class="px-6 py-4">${user.email}</td>
              <td class="px-6 py-4">${user.full_name ?? '-'}</td>
              <td class="px-6 py-4">${user.divisi?.NAMA_DIVISI ?? '-'}</td>
              <td class="px-6 py-4">${user.subdivisi?.NAMA_SUBDIVISI ?? '-'}</td>
              <td class="px-6 py-4">${user.role?.Nama_role ?? '-'}</td>
              <td class="px-6 py-4 text-center space-x-2">
                <button onclick="editUser(${user.ID_USER})" class="text-blue-600 hover:text-blue-800"><i class="fas fa-edit"></i></button>
                <button onclick="deleteUser(${user.ID_USER})" class="text-red-600 hover:text-red-800"><i class="fas fa-trash"></i></button>
              </td>
            </tr>
          `;
          tableBody.insertAdjacentHTML("beforeend", row);
        });
      } catch (err) {
        console.error(err);
      }
    }

    // Tambah
    addBtn.addEventListener("click", async () => {
      modal.classList.add("show");
      document.getElementById("modalTitle").innerText = "Tambah User";
      form.reset();
      document.getElementById("userId").value = "";
      await loadDivisiSelect();
      await loadRolesSelect();
      await loadSubdivisiSelect("");
    });
    closeModal.addEventListener("click", () => modal.classList.remove("show"));
    cancelBtn.addEventListener("click", () => modal.classList.remove("show"));

    // Handle change divisi → filter subdivisi
    document.getElementById("ID_DIVISI").addEventListener("change", async (e) => {
      await loadSubdivisiSelect(e.target.value);
    });

    // Save
    form.addEventListener("submit", async (e) => {
      e.preventDefault();
      let ID_USER = document.getElementById("userId").value;
      let payload = {
        username: document.getElementById("username").value,
        email: document.getElementById("email").value,
        full_name: document.getElementById("full_name").value,
        ID_ROLE: document.getElementById("ID_ROLE").value,
        ID_DIVISI: document.getElementById("ID_DIVISI").value,
        ID_SUBDIVISI: document.getElementById("ID_SUBDIVISI").value,
      };

      try {
        let res;
        if (ID_USER) {
          res = await fetch(`${apiUrl}/${ID_USER}`, {
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
          loadUsers();
        }
      } catch (err) {
        console.error(err);
      }
    });

    // Edit
    async function editUser(ID_USER) {
      let res = await fetch(`${apiUrl}/${ID_USER}`);
      let data = await res.json();
      await loadDivisiSelect(data.ID_DIVISI);
      await loadRolesSelect(data.ID_ROLE);
      await loadSubdivisiSelect(data.ID_DIVISI, data.ID_SUBDIVISI);

      modal.classList.add("show");
      document.getElementById("modalTitle").innerText = "Edit User";
      document.getElementById("userId").value = data.ID_USER;
      document.getElementById("username").value = data.username;
      document.getElementById("email").value = data.email;
      document.getElementById("full_name").value = data.full_name ?? '';
    }

    // Delete
    async function deleteUser(ID_USER) {
      if (!confirm("Yakin ingin menghapus data ini?")) return;
      let res = await fetch(`${apiUrl}/${ID_USER}`, { method: "DELETE" });
      if (res.ok) {
        showToast("Data berhasil dihapus");
        loadUsers();
      }
    }

    // Toast
    function showToast(msg) {
      toastMessage.innerText = msg;
      toast.classList.add("show");
      setTimeout(() => toast.classList.remove("show"), 3000);
    }

    // Load awal
    loadUsers();
  </script>
</body>
</html>
