<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Manajemen Arsip</title>
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

@include('components.TA_navbar')

<header class="bg-white shadow-lg h-16 md:h-20 w-full"></header>

<main class="container mx-auto px-4 py-6">

  <!-- Kontrol -->
  <div class="bg-white rounded-lg shadow-lg p-4 mb-6 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
    <div class="flex flex-wrap items-center gap-2">
      <button id="addArsipBtn" class="bg-blue-600 hover:bg-blue-700 text-white px-3 md:px-4 py-2 rounded-lg flex items-center space-x-2 text-sm md:text-base">
        <i class="fas fa-plus"></i> <span>Tambah Arsip</span>
      </button>
    </div>
    <input id="searchInput" type="text" placeholder="Cari arsip..." class="border px-3 py-2 w-full md:w-auto text-sm md:text-base" />
  </div>

  <!-- Tabel Arsip -->
  <div class="bg-white rounded-lg shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
      <table class="w-full min-w-[1100px]">
        <thead class="bg-blue-600 text-white text-sm md:text-base">
        <tr>
          <th class="px-4 py-3">NO</th>
          <th class="px-4 py-3">No Indeks</th>
          <th class="px-4 py-3">No Berkas</th>
          <th class="px-4 py-3">Judul Berkas</th>
          <th class="px-4 py-3">No Isi Berkas</th>
          <th class="px-4 py-3">Jenis Arsip</th>
          <th class="px-4 py-3">Kode Klasifikasi</th>
          <th class="px-4 py-3">No Nota Dinas</th>
          <th class="px-4 py-3">Tanggal Berkas</th>
          <th class="px-4 py-3">Perihal</th>
          <th class="px-4 py-3">Tingkat Pengembangan</th>
          <th class="px-4 py-3">Kondisi</th>
          <th class="px-4 py-3">Lokasi Simpan</th>
          <th class="px-4 py-3">Keterangan Simpan</th>
          <th class="px-4 py-3">Tipe Retensi</th>
          <th class="px-4 py-3">Tanggal Retensi</th>
          <th class="px-4 py-3">Keterangan</th>
          <th class="px-4 py-3">File Arsip</th>
          <th class="px-4 py-3 text-center">Aksi</th>
        </tr>
        </thead>
        <tbody id="arsipTableBody" class="divide-y divide-gray-200"></tbody>
      </table>
    </div>
  </div>

  <div id="loadingState" class="text-center py-8">
    <i class="fas fa-spinner fa-spin text-2xl text-blue-600"></i>
    <p class="mt-2 text-gray-600">Memuat data...</p>
  </div>
  <div id="emptyState" class="text-center py-8 hidden">
    <i class="fas fa-inbox text-4xl text-gray-400 mb-4"></i>
    <p class="text-gray-600">Tidak ada data arsip</p>
  </div>
</main>

<!-- Modal Arsip -->
<div id="arsipModal" class="modal fixed inset-0 bg-black bg-opacity-50 items-center justify-center z-50">
  <div class="bg-white rounded-lg p-6 w-full max-w-3xl mx-4 max-h-screen overflow-y-auto">
    <div class="flex items-center justify-between mb-4">
      <h3 id="modalTitle" class="text-lg font-semibold">Tambah Arsip</h3>
      <button id="closeModal" class="text-gray-400 hover:text-gray-600">
        <i class="fas fa-times"></i>
      </button>
    </div>

    <form id="arsipForm" class="grid grid-cols-2 gap-4" enctype="multipart/form-data">
  <input type="hidden" id="arsipId">
  <input type="hidden" id="ID_DIVISI" value="1">
  <input type="hidden" id="ID_SUBDIVISI" value="1">

  <!-- Kolom Utama -->
  <div>
    <label class="block text-sm font-medium mb-1">No Indeks</label>
    <input id="NO_INDEKS" name="NO_INDEKS" class="w-full border rounded-lg px-3 py-2" required>
  </div>

  <div>
    <label class="block text-sm font-medium mb-1">No Berkas</label>
    <input id="NO_BERKAS" name="NO_BERKAS" class="w-full border rounded-lg px-3 py-2" required>
  </div>

  <div class="col-span-2">
    <label class="block text-sm font-medium mb-1">Judul Berkas</label>
    <input id="JUDUL_BERKAS" name="JUDUL_BERKAS" class="w-full border rounded-lg px-3 py-2" required>
  </div>

  <div>
    <label class="block text-sm font-medium mb-1">No Isi Berkas</label>
    <input id="NO_ISI_BERKAS" name="NO_ISI_BERKAS" class="w-full border rounded-lg px-3 py-2">
  </div>

  <div>
    <label class="block text-sm font-medium mb-1">Jenis Arsip</label>
    <input id="JENIS_ARSIP" name="JENIS_ARSIP" class="w-full border rounded-lg px-3 py-2">
  </div>

  <div>
    <label class="block text-sm font-medium mb-1">Kode Klasifikasi</label>
    <input id="KODE_KLASIFIKASI" name="KODE_KLASIFIKASI" class="w-full border rounded-lg px-3 py-2">
  </div>

  <div>
    <label class="block text-sm font-medium mb-1">No Nota Dinas</label>
    <input id="NO_NOTA_DINAS" name="NO_NOTA_DINAS"class="w-full border rounded-lg px-3 py-2">
  </div>

  <div>
    <label class="block text-sm font-medium mb-1">Tanggal Berkas</label>
    <input type="date" name="TANGGAL_BERKAS" id="TANGGAL_BERKAS" class="w-full border rounded-lg px-3 py-2">
  </div>

  <div>
    <label class="block text-sm font-medium mb-1">Perihal</label>
    <input id="PERIHAL" name="PERIHAL" class="w-full border rounded-lg px-3 py-2">
  </div>

  <div>
    <label class="block text-sm font-medium mb-1">Tingkat Pengembangan</label>
    <input id="TINGKAT_PENGEMBANGAN" name="TINGKAT_PENGEMBANGAN" class="w-full border rounded-lg px-3 py-2">
  </div>

  <div>
    <label class="block text-sm font-medium mb-1">Kondisi</label>
    <input id="KONDISI" name="KONDISI" class="w-full border rounded-lg px-3 py-2">
  </div>

  <div>
    <label class="block text-sm font-medium mb-1">Lokasi Simpan</label>
    <input id="RAK_BAK_URUTAN" name="RAK_BAK_URUTAN" class="w-full border rounded-lg px-3 py-2">
  </div>

  <div>
    <label class="block text-sm font-medium mb-1">Tipe Retensi</label>
    <input id="TIPE_RETENSI"name="TIPE_RETENSI" class="w-full border rounded-lg px-3 py-2">
  </div>

  <div>
    <label class="block text-sm font-medium mb-1">Tanggal Retensi</label>
    <input type="date" id="TANGGAL_RETENSI"  name="TANGGAL_RETENSI" class="w-full border rounded-lg px-3 py-2">
  </div>

  <div>
    <label class="block text-sm font-medium mb-1">Keterangan</label>
    <input id="KETERANGAN" name="KETERANGAN" class="w-full border rounded-lg px-3 py-2">
  </div>

  <div class="col-span-2">
    <label class="block text-sm font-medium mb-1">Keterangan Simpan</label>
    <textarea id="KETERANGAN_SIMPAN" name="KETERANGAN_SIMPAN" class="w-full border rounded-lg px-3 py-2"></textarea>
  </div>

  <div class="col-span-2">
    <label class="block text-sm font-medium mb-1">Upload File Arsip</label>
    <input type="file" id="FILE" name="FILE" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" class="w-full border rounded-lg px-3 py-2">
  </div>

  <div class="col-span-2 flex justify-end gap-2 mt-4">
    <button type="button" id="cancelBtn" name="cancleBtn "class="bg-gray-300 hover:bg-gray-400 text-gray-700 py-2 px-4 rounded-lg">Batal</button>
    <button type="submit" id="saveBtn" name="saveBtn" class="bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded-lg">Simpan</button>
  </div>
</form>

  </div>
</div>

<!-- Toast -->
<div id="toast" class="toast fixed top-4 right-4 bg-green-500 text-white px-4 py-3 rounded-lg shadow-lg z-50 max-w-xs sm:max-w-sm">
  <div class="flex items-center space-x-2">
    <i id="toastIcon" class="fas fa-check-circle"></i>
    <span id="toastMessage">Pesan berhasil</span>
  </div>
</div>

<script>
const apiUrl = "/api/t_arsip";
const tableBody = document.getElementById("arsipTableBody");
const loadingState = document.getElementById("loadingState");
const emptyState = document.getElementById("emptyState");
const modal = document.getElementById("arsipModal");
const addBtn = document.getElementById("addArsipBtn");
const closeModal = document.getElementById("closeModal");
const cancelBtn = document.getElementById("cancelBtn");
const form = document.getElementById("arsipForm");
const toast = document.getElementById("toast");
const toastMessage = document.getElementById("toastMessage");
const token = localStorage.getItem("auth_token"); // ✅ pastikan gunakan key yang sama dengan login

// === TOAST ===
function showToast(msg, success = true) {
  toastMessage.textContent = msg;
  toast.classList.remove("bg-red-500", "bg-green-500");
  toast.classList.add(success ? "bg-green-500" : "bg-red-500");
  toast.classList.add("show");
  setTimeout(() => toast.classList.remove("show"), 3000);
}

// === FUNGSI PEMBANTU FETCH (dengan header standar) ===
async function fetchWithAuth(url, options = {}) {
  const headers = options.headers || {};
  headers["Authorization"] = `Bearer ${token}`;
  headers["Accept"] = "application/json";

  // Hanya tambahkan Content-Type JSON kalau bukan FormData
  if (!(options.body instanceof FormData) && !headers["Content-Type"]) {
    headers["Content-Type"] = "application/json";
  }

  const res = await fetch(url, { ...options, headers });
  if (res.status === 401) {
    showToast("Token tidak valid atau sesi sudah berakhir", false);
    throw new Error("Unauthenticated");
  }
  return res;
}

// === LOAD DATA ===
async function loadArsip(keyword = "") {
  loadingState.classList.remove("hidden");
  emptyState.classList.add("hidden");
  tableBody.innerHTML = "";

  try {
    let url = apiUrl;
    if (keyword.trim()) url += `?search=${encodeURIComponent(keyword)}`;

    const res = await fetchWithAuth(url);
    const data = await res.json();

    loadingState.classList.add("hidden");
    if (!Array.isArray(data) || data.length === 0) {
      emptyState.classList.remove("hidden");
      return;
    }

    data.forEach((arsip, i) => {
      const fileLink = arsip.FILE
        ? `<a href="/${arsip.FILE}" target="_blank" class="text-blue-600 underline">Lihat</a>`
        : "-";

      const row = `
        <tr class="hover:bg-gray-50">
          <td class="px-4 py-3">${i + 1}</td>
          <td class="px-4 py-3">${arsip.NO_INDEKS ?? "-"}</td>
          <td class="px-4 py-3">${arsip.NO_BERKAS ?? "-"}</td>
          <td class="px-4 py-3">${arsip.JUDUL_BERKAS ?? "-"}</td>
          <td class="px-4 py-3">${arsip.NO_ISI_BERKAS ?? "-"}</td>
          <td class="px-4 py-3">${arsip.JENIS_ARSIP ?? "-"}</td>
          <td class="px-4 py-3">${arsip.KODE_KLASIFIKASI ?? "-"}</td>
          <td class="px-4 py-3">${arsip.NO_NOTA_DINAS ?? "-"}</td>
          <td class="px-4 py-3">${arsip.TANGGAL_BERKAS ?? "-"}</td>
          <td class="px-4 py-3">${arsip.PERIHAL ?? "-"}</td>
          <td class="px-4 py-3">${arsip.TINGKAT_PENGEMBANGAN ?? "-"}</td>
          <td class="px-4 py-3">${arsip.KONDISI ?? "-"}</td>
          <td class="px-4 py-3">${arsip.RAK_BAK_URUTAN ?? "-"}</td>
          <td class="px-4 py-3">${arsip.KETERANGAN_SIMPAN ?? "-"}</td>
          <td class="px-4 py-3">${arsip.TIPE_RETENSI ?? "-"}</td>
          <td class="px-4 py-3">${arsip.TANGGAL_RETENSI ?? "-"}</td>
          <td class="px-4 py-3">${arsip.KETERANGAN ?? "-"}</td>
          <td class="px-4 py-3">${fileLink}</td>
          <td class="px-4 py-3 text-center space-x-2">
            <button onclick="editArsip(${arsip.ID_ARSIP})" class="text-blue-600 hover:text-blue-800"><i class="fas fa-edit"></i></button>
            <button onclick="deleteArsip(${arsip.ID_ARSIP})" class="text-red-600 hover:text-red-800"><i class="fas fa-trash"></i></button>
          </td>
        </tr>`;
      tableBody.insertAdjacentHTML("beforeend", row);
    });
  } catch (err) {
    console.error(err);
    loadingState.classList.add("hidden");
    showToast("Gagal memuat data", false);
  }
}

// === MODAL ===
addBtn.addEventListener("click", () => {
  modal.classList.add("show");
  form.reset();
  document.getElementById("arsipId").value = "";
  document.getElementById("modalTitle").innerText = "Tambah Arsip";
});
closeModal.addEventListener("click", () => modal.classList.remove("show"));
cancelBtn.addEventListener("click", () => modal.classList.remove("show"));

// === SIMPAN / UPDATE ===
form.addEventListener("submit", async (e) => {
  e.preventDefault();

  const id = document.getElementById("arsipId").value;
  const method = "POST";
  const url = id ? `${apiUrl}/${id}?_method=PUT` : apiUrl;

  const formData = new FormData(form);
  formData.append("ID_DIVISI", document.getElementById("ID_DIVISI").value);
  formData.append("ID_SUBDIVISI", document.getElementById("ID_SUBDIVISI").value);
  formData.append("CREATE_BY", "system");

  try {
    const res = await fetchWithAuth(url, { method, body: formData });
    if (!res.ok) throw new Error("Gagal menyimpan data");

    modal.classList.remove("show");
    showToast("Data berhasil disimpan");
    loadArsip();
  } catch (err) {
    console.error(err);
    showToast("Gagal menyimpan data", false);
  }
});

// === EDIT ===
async function editArsip(id) {
  try {
    const res = await fetchWithAuth(`${apiUrl}/${id}`);
    const data = await res.json();

    modal.classList.add("show");
    document.getElementById("modalTitle").innerText = "Edit Arsip";
    document.getElementById("arsipId").value = data.ID_ARSIP ?? "";

    for (const key of [
      "NO_INDEKS","NO_BERKAS","JUDUL_BERKAS","NO_ISI_BERKAS","JENIS_ARSIP",
      "KODE_KLASIFIKASI","NO_NOTA_DINAS","TANGGAL_BERKAS","PERIHAL",
      "TINGKAT_PENGEMBANGAN","KONDISI","RAK_BAK_URUTAN","KETERANGAN_SIMPAN",
      "TIPE_RETENSI","TANGGAL_RETENSI","KETERANGAN"
    ]) {
      if (document.getElementById(key)) {
        document.getElementById(key).value = data[key] ?? "";
      }
    }
  } catch (err) {
    console.error(err);
    showToast("Gagal memuat data edit", false);
  }
}

// === HAPUS ===
async function deleteArsip(id) {
  if (!confirm("Yakin ingin menghapus arsip ini?")) return;

  try {
    const res = await fetchWithAuth(`${apiUrl}/${id}`, { method: "DELETE" });
    if (!res.ok) throw new Error("Gagal menghapus");
    showToast("Data berhasil dihapus");
    loadArsip();
  } catch (err) {
    console.error(err);
    showToast("Gagal menghapus data", false);
  }
}

// === PENCARIAN ===
document.getElementById("searchInput").addEventListener("input", (e) => {
  clearTimeout(window.searchDelay);
  window.searchDelay = setTimeout(() => loadArsip(e.target.value), 400);
});

loadArsip();
</script>


</body>
</html>
