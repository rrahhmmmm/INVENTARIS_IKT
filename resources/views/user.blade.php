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
<header class="bg-white shadow-lg h-20"></header>

<!-- Main Content -->
<main class="container mx-auto px-4 py-6">
  <!-- Controls -->
  <div class="bg-white rounded-lg shadow-lg p-4 mb-6 flex flex-col sm:flex-row justify-between items-stretch sm:items-center gap-3">
    <div class="flex flex-col sm:flex-row sm:space-x-2 gap-2">
      <input type="text" id="searchInput" placeholder="Cari user..." 
             class="border border-gray-300 rounded-lg px-3 py-2 w-full sm:w-64">
      <a href="{{ url('/api/user/export') }}" id="exportBtn" 
         class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg flex items-center justify-center space-x-2">
        <span>Export Excel</span> <i class="fas fa-file-excel"></i>
      </a>
    </div>
    <!-- Tombol tambah user dihapus -->
  </div>

  <!-- User Table -->
  <div class="bg-white rounded-lg shadow-sm overflow-x-auto">
    <table class="w-full min-w-[800px]">
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
      <h3 id="modalTitle" class="text-lg font-semibold">Edit User</h3>
      <button id="closeModal" class="text-gray-400 hover:text-gray-600">
        <i class="fas fa-times"></i>
      </button>
    </div>
    <form id="userForm" class="space-y-4">
      <input type="hidden" id="userId">
      <div>
        <label class="block text-sm font-medium">Username</label>
        <input type="text" id="username" required readonly 
              class="w-full border rounded-lg px-3 py-2 bg-gray-100 cursor-not-allowed">
      </div>
      <div>
        <label class="block text-sm font-medium">Email</label>
        <input type="email" id="email" required class="w-full border rounded-lg px-3 py-2">
      </div>
      <div>
        <label class="block text-sm font-medium">Nama Lengkap</label>
        <input type="text" id="full_name" class="w-full border rounded-lg px-3 py-2">
      </div>
      <div>
        <label class="block text-sm font-medium">Divisi</label>
        <select id="ID_DIVISI" class="w-full border rounded-lg px-3 py-2"></select>
      </div>
      <div>
        <label class="block text-sm font-medium">Subdivisi</label>
        <select id="ID_SUBDIVISI" class="w-full border rounded-lg px-3 py-2"></select>
      </div>
      <div>
        <label class="block text-sm font-medium">Role</label>
        <select id="ID_ROLE" class="w-full border rounded-lg px-3 py-2"></select>
      </div>
      <div class="flex flex-col sm:flex-row gap-2 pt-4">
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
const closeModal = document.getElementById("closeModal");
const cancelBtn = document.getElementById("cancelBtn");
const form = document.getElementById("userForm");
const toast = document.getElementById("toast");
const toastMessage = document.getElementById("toastMessage");
const token = localStorage.getItem("auth_token");

// ==== SEARCH ====
let searchTimeout = null;
document.getElementById("searchInput").addEventListener("input", function() {
  clearTimeout(searchTimeout);
  let keyword = this.value;
  searchTimeout = setTimeout(() => loadUsers(keyword), 500);
});

// Load Roles
async function loadRolesSelect(selected = "") {
  const res = await fetch(roleApi, {
    headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' }
  });
  const json = await res.json();
  const roles = json.data ?? json;
  let select = document.getElementById("ID_ROLE");
  select.innerHTML = `<option value="">-- Pilih Role --</option>`;
  roles.forEach(r => {
    select.innerHTML += `<option value="${r.ID_ROLE}" ${selected == r.ID_ROLE ? "selected" : ""}>${r.Nama_role}</option>`;
  });
}

// Load Divisi
async function loadDivisiSelect(selected = "") {
  const res = await fetch(divisiApi, {
    headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' }
  });
  const json = await res.json();
  const divisi = json.data ?? json;
  let select = document.getElementById("ID_DIVISI");
  select.innerHTML = `<option value="">-- Pilih Divisi --</option>`;
  divisi.forEach(d => {
    select.innerHTML += `<option value="${d.ID_DIVISI}" ${selected == d.ID_DIVISI ? "selected" : ""}>${d.NAMA_DIVISI}</option>`;
  });
}

// Load Subdivisi
async function loadSubdivisiSelect(divisiId, selected = "") {
  let select = document.getElementById("ID_SUBDIVISI");
  if (!divisiId) {
    select.innerHTML = `<option value="">-- Pilih Subdivisi --</option>`;
    return;
  }
  const res = await fetch(`${subdivisiApi}/divisi/${divisiId}`, {
    headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' }
  });
  const json = await res.json();
  const subdivisi = json.data ?? json;
  select.innerHTML = `<option value="">-- Pilih Subdivisi --</option>`;
  subdivisi.forEach(s => {
    select.innerHTML += `<option value="${s.ID_SUBDIVISI}" ${selected == s.ID_SUBDIVISI ? "selected" : ""}>${s.NAMA_SUBDIVISI}</option>`;
  });
}

// Load Users
async function loadUsers(keyword = "") {
  loadingState.classList.remove("hidden");
  emptyState.classList.add("hidden");
  tableBody.innerHTML = "";

  try {
    let url = apiUrl;
    if (keyword && keyword.trim() !== "") {
      url += `?search=${encodeURIComponent(keyword)}`;
    }

    const res = await fetch(url, {
      headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' }
    });
    const json = await res.json();
    const data = json.data ?? json;

    loadingState.classList.add("hidden");

    if (!Array.isArray(data) || data.length === 0) {
      emptyState.classList.remove("hidden");
      return;
    }

    data.forEach(user => {
      let row = `<tr>
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
      </tr>`;
      tableBody.insertAdjacentHTML("beforeend", row);
    });
  } catch (err) {
    console.error(err);
    loadingState.classList.add("hidden");
    emptyState.classList.remove("hidden");
  }
}

closeModal.addEventListener("click", () => modal.classList.remove("show"));
cancelBtn.addEventListener("click", () => modal.classList.remove("show"));

// Handle change divisi → filter subdivisi
document.getElementById("ID_DIVISI").addEventListener("change", async (e) => {
  await loadSubdivisiSelect(e.target.value);
});

// Save (Update)
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
    if (ID_USER) {
      let res = await fetch(`${apiUrl}/${ID_USER}`, {
        method: "PUT",
        headers: {
          "Content-Type": "application/json",
          "Authorization": `Bearer ${token}`
        },
        body: JSON.stringify(payload),
      });
      if (res.ok) {
        showToast("Data berhasil diperbarui");
        modal.classList.remove("show");
        loadUsers();
      } else {
        showToast("Gagal update data");
      }
    }
  } catch (err) { 
    console.error(err); 
  }
});

// Edit
async function editUser(ID_USER) {
  try {
    const res = await fetch(`${apiUrl}/${ID_USER}`, {
      headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' }
    });
    if (!res.ok) throw new Error("Gagal fetch user");

    const json = await res.json();
    const data = json.data ?? json;

    await loadDivisiSelect(data.ID_DIVISI);
    await loadRolesSelect(data.ID_ROLE);
    await loadSubdivisiSelect(data.ID_DIVISI, data.ID_SUBDIVISI);

    modal.classList.add("show");
    document.getElementById("modalTitle").innerText = "Edit User";
    document.getElementById("userId").value = data.ID_USER;
    document.getElementById("username").value = data.username;
    document.getElementById("email").value = data.email;
    document.getElementById("full_name").value = data.full_name ?? '';
  } catch(err) {
    console.error(err);
    showToast("Gagal memuat data user");
  }
}

// Delete
async function deleteUser(ID_USER) {
  if (!confirm("Yakin ingin menghapus data ini?")) return;
  const res = await fetch(`${apiUrl}/${ID_USER}`, {
    method: "DELETE",
    headers: { 'Authorization': `Bearer ${token}` }
  });
  if (res.ok) {
    showToast("Data berhasil dihapus");
    loadUsers();
  } else {
    showToast("Gagal menghapus data");
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
