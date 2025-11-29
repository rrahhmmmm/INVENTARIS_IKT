<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Manajemen Arsip</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <style>
    .modal { display: none; }
    .modal.show { display: flex; }
    .toast { transform: translateX(100%); transition: transform 0.3s ease-in-out; }
    .toast.show { transform: translateX(0); }
    .error-message { 
      font-size: 0.875rem; 
      color: #ef4444; 
      margin-top: 0.25rem;
      display: none;
    }
    .error-message.show { display: block; }
    .input-error {
      border-color: #ef4444 !important;
      background-color: #fef2f2 !important;
    }

    /* Animasi untuk BUTTON notification */
    .annoying-btn {
        animation: 
            annoying-blink 0.4s infinite,
            annoying-pulse 0.6s infinite,
            annoying-rotate 1s infinite,
            annoying-glow 0.8s infinite;
    }

    /* Kelap-kelip opacity */
    @keyframes annoying-blink {
        0%, 49% {
            opacity: 1;
        }
        50%, 100% {
            opacity: 0.3;
        }
    }

    /* Membesar mengecil ekstrim */
    @keyframes annoying-pulse {
        0%, 100% {
            transform: scale(1);
        }
        25% {
            transform: scale(1.4);
        }
        50% {
            transform: scale(0.8);
        }
        75% {
            transform: scale(1.3);
        }
    }

    /* Rotasi goyang */
    @keyframes annoying-rotate {
        0% {
            transform: rotate(0deg);
        }
        25% {
            transform: rotate(10deg);
        }
        50% {
            transform: rotate(-10deg);
        }
        75% {
            transform: rotate(10deg);
        }
        100% {
            transform: rotate(0deg);
        }
    }

    /* Glow rainbow untuk icon */
    @keyframes annoying-glow {
        0% {
            filter: drop-shadow(0 0 10px #ef4444);
        }
        20% {
            filter: drop-shadow(0 0 10px #f59e0b);
        }
        40% {
            filter: drop-shadow(0 0 10px #10b981);
        }
        60% {
            filter: drop-shadow(0 0 10px #3b82f6);
        }
        80% {
            filter: drop-shadow(0 0 10px #8b5cf6);
        }
        100% {
            filter: drop-shadow(0 0 10px #ef4444);
        }
    }

    /* Badge count - styling normal tanpa animasi berlebihan */
    .notification-badge {
        background-color: #ef4444;
        color: white;
        font-weight: bold;
    }

    /* BONUS: Shake button (opsional) */
    @keyframes shake {
        0%, 100% { transform: translateX(0); }
        10%, 30%, 50%, 70%, 90% { transform: translateX(-5px); }
        20%, 40%, 60%, 80% { transform: translateX(5px); }
    }

    .shake-btn {
        animation: shake 0.5s;
    }

    /* notifikasi + inaktif arsip */
    /* Status badges */
    .status-aktif {
        background-color: #10b981;
        color: white;
        font-weight: bold;
        padding: 4px 12px;
        border-radius: 6px;
        display: inline-block;
    }

    .status-inaktif {
        background-color: #ef4444;
        color: white;
        font-weight: bold;
        padding: 4px 12px;
        border-radius: 6px;
        display: inline-block;
    }

    .row-inaktif {
        background-color: #fee !important;
        opacity: 0.7;
    }

    /* Confirmation modal */
    .confirm-modal {
        display: none;
        position: fixed;
        inset: 0;
        background-color: rgba(0, 0, 0, 0.6);
        z-index: 60;
        align-items: center;
        justify-content: center;
    }

    .confirm-modal.show {
        display: flex;
    }

    .confirm-modal-content {
        background: white;
        border-radius: 12px;
        padding: 24px;
        max-width: 500px;
        width: 90%;
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.3);
    }

    .confirm-title {
        font-size: 1.5rem;
        font-weight: bold;
        color: #dc2626;
        text-align: center;
        margin-bottom: 16px;
        animation: pulse 0.5s infinite alternate;
    }

    @keyframes pulse {
        from { transform: scale(1); }
        to { transform: scale(1.05); }
    }

    .notification-item {
        position: relative;
    }

    .notification-delete-btn {
        position: absolute;
        top: 8px;
        right: 8px;
        background: rgba(255, 255, 255, 0.2);
        border: none;
        color: white;
        width: 24px;
        height: 24px;
        border-radius: 50%;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        transition: all 0.2s;
    }

    .notification-delete-btn:hover {
        background: rgba(255, 255, 255, 0.4);
        transform: scale(1.1);
    }

        /* Filter Button Styles */
    .filter-btn {
        border: 2px solid transparent;
        background-color: #e5e7eb;
        color: #6b7280;
    }

    .filter-btn.active {
        border-color: currentColor;
        font-weight: 600;
    }

    #filterAktifBtn.active {
        background-color: #d1fae5;
        color: #059669;
        border-color: #059669;
    }

    #filterInaktifBtn.active {
        background-color: #fee2e2;
        color: #dc2626;
        border-color: #dc2626;
    }

    .filter-btn:hover {
        opacity: 0.8;
        transform: scale(1.05);
    }
</style>
</head>
<body class="bg-gray-100">

@include('components.TA_navbar')

<!-- Include navbar here -->

<header class="bg-white shadow-lg h-16 md:h-20 w-full"></header>

<main class="container mx-auto px-4 py-6">

  <!-- Kontrol -->
  <div class="bg-white rounded-lg shadow-lg p-4 mb-6 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
    <div class="flex flex-wrap items-center gap-2">
      <button id="addArsipBtn" class="bg-blue-600 hover:bg-blue-700 text-white px-3 md:px-4 py-2 rounded-lg flex items-center space-x-2 text-sm md:text-base">
        <i class="fas fa-plus"></i> <span>Tambah Arsip</span>
      </button>
      <div class="flex items-center gap-2 ml-4">
          <button id="filterAktifBtn" class="filter-btn active px-3 py-2 rounded-lg flex items-center space-x-1 text-sm font-medium transition-all">
            <i class="fas fa-check-circle"></i>
            <span>AKTIF</span>
          </button>
          <button id="filterInaktifBtn" class="filter-btn active px-3 py-2 rounded-lg flex items-center space-x-1 text-sm font-medium transition-all">
            <i class="fas fa-times-circle"></i>
            <span>INAKTIF</span>
          </button>
        </div>
    </div>
    
    <div class="relative space-x-2">
      <input id="searchInput" type="text" placeholder="Cari arsip..." class="border px-3 py-2 w-full md:w-auto text-sm md:text-base" />
      
    <!-- Button dengan animasi kelap-kelip -->
      <button id="notificationBtn" class="relative">
          <i class="fas fa-bell text-xl text-gray-700"></i>
          <!-- Badge count tanpa animasi berlebihan -->
          <span id="notificationCount" class="notification-badge absolute -top-1 -right-2 text-xs rounded-full px-1 min-w-5 h-5 flex items-center justify-center">0</span>
      </button>

      <div id="notificationDropdown" class="hidden absolute right-0 mt-2 w-80 bg-red-600 shadow-lg rounded-lg overflow-hidden z-50 max-h-96 overflow-y-auto">
          <ul id="notificationList"></ul>
      </div>
    </div>  
  </div>

  <!-- Per Page Select -->
  <div class="pb-2">
    <select id="perPageSelect" class="border rounded px-2 py-1 text-sm">
      <option value="10">10</option>
      <option value="25">25</option>
      <option value="50">50</option>
      <option value="100">100</option>
    </select>
  </div>

  <!-- Tabel Arsip -->
  <div class="bg-white rounded-lg shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
      <table class="min-w-[2500px] table-fixed text-sm md:text-base">
        <thead class="bg-blue-600 text-white">
          <tr>
            <th class="sticky top-0 bg-blue-600 px-4 py-3 min-w-[60px] z-10">NO</th>
            <th class="sticky top-0 bg-blue-600 px-4 py-3 min-w-[200px] z-10">Divisi</th>
            <th class="sticky top-0 bg-blue-600 px-4 py-3 min-w-[200px] z-10">Subdivisi</th>
            <th class="sticky top-0 bg-blue-600 px-4 py-3 w-[120px] z-10">No Indeks</th>
            <th class="sticky top-0 bg-blue-600 px-4 py-3 min-w-[150px] z-10">No Berkas</th>
            <th class="sticky top-0 bg-blue-600 px-4 py-3 min-w-[300px] z-10">Judul Berkas</th>
            <th class="sticky top-0 bg-blue-600 px-4 py-3 min-w-[150px] z-10">No Isi Berkas</th>
            <th class="sticky top-0 bg-blue-600 px-4 py-3 min-w-[150px] z-10">Jenis Naskah Dinas</th>
            <th class="sticky top-0 bg-blue-600 px-4 py-3 min-w-[150px] z-10">Kode Klasifikasi</th>
            <th class="sticky top-0 bg-blue-600 px-4 py-3 min-w-[300px] z-10">No Nota Dinas</th>
            <th class="sticky top-0 bg-blue-600 px-4 py-3 min-w-[200px] z-10">Tanggal Berkas</th>
            <th class="sticky top-0 bg-blue-600 px-4 py-3 min-w-[400px] z-10">Perihal</th>
            <th class="sticky top-0 bg-blue-600 px-4 py-3 w-40 z-10">Tingkat Pengembangan</th>
            <th class="sticky top-0 bg-blue-600 px-4 py-3 w-[120px] z-10">Kondisi</th>
            <th class="sticky top-0 bg-blue-600 px-4 py-3 w-40 z-10">Lokasi Simpan</th>
            <th class="sticky top-0 bg-blue-600 px-4 py-3 w-[200px] z-10">Keterangan Simpan</th>
            <th class="sticky top-0 bg-blue-600 px-4 py-3 w-[150px] z-10">Tipe Retensi</th>
            <th class="sticky top-0 bg-blue-600 px-4 py-3 min-w-[200px] z-10">Tanggal Retensi</th>
            <th class="sticky top-0 bg-blue-600 px-4 py-3 w-[150px] z-10">Keterangan</th>
            <th class="sticky top-0 bg-blue-600 px-4 py-3 w-[140px] z-10">Create By</th>
            <th class="sticky top-0 bg-blue-600 px-4 py-3 w-[120px] z-10">File Arsip</th>
            <th class="sticky top-0 bg-blue-600 px-4 py-3 w-[100px] text-center z-10">Aksi</th>
          </tr>
        </thead>
        <tbody id="arsipTableBody" class="divide-y divide-gray-200 text-gray-700"></tbody>
      </table>
    </div>

    <div id="loadingState" class="text-center py-8">
      <i class="fas fa-spinner fa-spin text-2xl text-blue-600"></i>
      <p class="mt-2 text-gray-600">Memuat data...</p>
    </div>
    <div id="emptyState" class="text-center py-8 hidden">
      <i class="fas fa-inbox text-4xl text-gray-400 mb-4"></i>
      <p class="text-gray-600">Tidak ada data arsip</p>
    </div>
  </div>

  <!-- Pagination -->
  <div id="paginationControls" class="mt-4 mb-4 hidden">
    <div class="flex flex-col items-start mx-4">
      <div class="flex items-center gap-2 mb-2">
        <button id="prevPageBtn" class="px-3 py-1 border rounded hover:bg-gray-100 disabled:opacity-50 disabled:cursor-not-allowed">
          <i class="fas fa-angle-left"></i> 
        </button>
        <div id="pageNumbers" class="flex gap-1"></div>
        <button id="nextPageBtn" class="px-3 py-1 border rounded hover:bg-gray-100 disabled:opacity-50 disabled:cursor-not-allowed">
          <i class="fas fa-angle-right"></i>
        </button>
      </div>
      <div class="text-sm text-gray-600">
        Menampilkan <span id="showingFrom">0</span> Hingga 
        <span id="showingTo">0</span> dari 
        <span id="totalRecords">0</span> data
      </div>
    </div>
  </div>

</main>

<!-- modal notif -->
<div id="confirmModal" class="confirm-modal">
  <div class="confirm-modal-content">
    <h2 class="confirm-title">⚠️ SERAHKAN KE SDM SEKARANG!!! ⚠️</h2>
    <p class="text-center text-gray-700 mb-6">Arsip ini sudah melewati masa retensi. Apakah Anda siap menyerahkannya ke SDM?</p>
    <div class="flex gap-3 justify-center">
      <button id="btnNantiMager" class="bg-gray-400 hover:bg-gray-500 text-white px-6 py-3 rounded-lg font-semibold transition">
        😴 LAIN KALI 😴 
      </button>
      <button id="btnSiapLaksanakan" class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg font-semibold transition">
        ✅ SIAP LAKSANAKAN OTW SDM BANG ✅
      </button>
    </div>
  </div>
</div>

<!-- Modal Arsip -->
<div id="arsipModal" class="modal fixed inset-0 bg-black bg-opacity-50 items-center justify-center z-50">
  <div class="bg-white rounded-lg p-6 w-full max-w-3xl mx-4 max-h-screen overflow-y-auto">
    <div class="flex items-center justify-between mb-4">
      <h3 id="modalTitle" class="text-lg font-semibold">Tambah Arsip</h3>
      <button id="closeModal" class="text-gray-400 hover:text-gray-600">
        <i class="fas fa-times"></i>
      </button>
    </div>

    <!-- Error Summary -->
    <div id="errorSummary" class="hidden bg-red-50 border border-red-300 text-red-800 px-4 py-3 rounded-lg mb-4">
      <div class="flex items-start">
        <i class="fas fa-exclamation-circle mt-0.5 mr-2"></i>
        <div>
          <p class="font-semibold">Terdapat kesalahan pada form:</p>
          <ul id="errorList" class="list-disc list-inside mt-1 text-sm"></ul>
        </div>
      </div>
    </div>

    <form id="arsipForm" class="grid grid-cols-2 gap-4" enctype="multipart/form-data">
      <input type="hidden" id="arsipId">

      <!-- Divisi -->
      <div>
        <label class="block text-sm font-medium mb-1">Divisi</label>
        <input id="DIVISI_NAME" class="w-full border rounded-lg px-3 py-2 bg-gray-100" readonly>
      </div>
      <input type="hidden" id="ID_DIVISI" name="ID_DIVISI">

      <!-- Subdivisi -->
      <div>
        <label class="block text-sm font-medium mb-1">Subdivisi</label>
        <input id="SUBDIVISI_NAME" class="w-full border rounded-lg px-3 py-2 bg-gray-100" readonly>
      </div>
      <input type="hidden" id="ID_SUBDIVISI" name="ID_SUBDIVISI">

      <!-- No Indeks -->
      <div class="relative">
        <label class="block text-sm font-medium mb-1">No Indeks <span class="text-red-500">*</span></label>
        <input id="NO_INDEKS" name="NO_INDEKS" class="w-full border rounded-lg px-3 py-2" required autocomplete="off">
        <div class="error-message" id="error_NO_INDEKS"></div>
        <ul id="indeksSuggestions" class="absolute bg-white border border-gray-300 rounded-lg shadow-lg mt-1 w-full hidden z-50 max-h-60 overflow-y-auto"></ul>
      </div>

      <!-- No Berkas -->
      <div>
        <label class="block text-sm font-medium mb-1">No Berkas <span class="text-red-500">*</span></label>
        <input id="NO_BERKAS" name="NO_BERKAS" class="w-full border rounded-lg px-3 py-2" required>
        <div class="error-message" id="error_NO_BERKAS"></div>
      </div>

      <!-- Judul Berkas -->
      <div class="col-span-2">
        <label class="block text-sm font-medium mb-1">Judul Berkas <span class="text-red-500">*</span></label>
        <input id="JUDUL_BERKAS" name="JUDUL_BERKAS" class="w-full border rounded-lg px-3 py-2" required>
        <div class="error-message" id="error_JUDUL_BERKAS"></div>
      </div>

      <!-- No Isi Berkas -->
      <div>
        <label class="block text-sm font-medium mb-1">No Isi Berkas</label>
        <input id="NO_ISI_BERKAS" name="NO_ISI_BERKAS" class="w-full border rounded-lg px-3 py-2">
        <div class="error-message" id="error_NO_ISI_BERKAS"></div>
      </div>

      <!-- Jenis Naskah Dinas -->
      <div>
        <label class="block text-sm font-medium mb-1">Jenis Naskah Dinas</label>
        <select id="JENIS_ARSIP" name="JENIS_ARSIP" class="w-full border rounded-lg px-3 py-2">
          <option value="">-- Pilih Jenis Naskah Dinas --</option>
        </select>
        <div class="error-message" id="error_JENIS_ARSIP"></div>
      </div>

      <!-- Kode Klasifikasi -->
      <div class="relative">
        <label class="block text-sm font-medium mb-1">Kode Klasifikasi</label>
        <input id="KODE_KLASIFIKASI" name="KODE_KLASIFIKASI" class="w-full border rounded-lg px-3 py-2" autocomplete="off">
        <div class="error-message" id="error_KODE_KLASIFIKASI"></div>
        <ul id="klasifikasiSuggestions" class="absolute bg-white border border-gray-300 rounded-lg shadow-lg mt-1 w-full hidden z-50 max-h-60 overflow-y-auto"></ul>
      </div>

      <!-- No Nota Dinas -->
      <div>
        <label class="block text-sm font-medium mb-1">No Nota Dinas</label>
        <input id="NO_NOTA_DINAS" name="NO_NOTA_DINAS" class="w-full border rounded-lg px-3 py-2">
        <div class="error-message" id="error_NO_NOTA_DINAS"></div>
      </div>

      <!-- Tanggal Berkas -->
      <div>
        <label class="block text-sm font-medium mb-1">Tanggal Berkas</label>
        <input type="date" name="TANGGAL_BERKAS" id="TANGGAL_BERKAS" class="w-full border rounded-lg px-3 py-2">
        <div class="error-message" id="error_TANGGAL_BERKAS"></div>
      </div>

      <!-- Perihal -->
      <div>
        <label class="block text-sm font-medium mb-1">Perihal</label>
        <input id="PERIHAL" name="PERIHAL" class="w-full border rounded-lg px-3 py-2">
        <div class="error-message" id="error_PERIHAL"></div>
      </div>

      <!-- Tingkat Pengembangan -->
      <div>
        <label class="block text-sm font-medium mb-1">Tingkat Pengembangan</label>
        <select id="TINGKAT_PENGEMBANGAN" name="TINGKAT_PENGEMBANGAN" class="w-full border rounded-lg px-3 py-2">
          <option value="">-- Pilih Tingkat Pengembangan</option>
        </select>
        <div class="error-message" id="error_TINGKAT_PENGEMBANGAN"></div>
      </div>

      <!-- Kondisi -->
      <div>
        <label class="block text-sm font-medium mb-1">Kondisi</label>
        <select id="KONDISI" name="KONDISI" class="w-full border rounded-lg px-3 py-2">
          <option value="">-- Pilih Kondisi --</option>
        </select>
        <div class="error-message" id="error_KONDISI"></div>
      </div>

      <!-- Lokasi Simpan -->
      <div>
        <label class="block text-sm font-medium mb-1">Lokasi Simpan</label>
        <div class="flex gap-2">
          <input id="RAK_INPUT" type="text" placeholder="Lemari" class="w-1/3 border rounded-lg px-3 py-2">
          <input id="BAK_INPUT" type="text" placeholder="Baris" class="w-1/3 border rounded-lg px-3 py-2">
          <input id="ARSIP_INPUT" type="text" placeholder="Box" class="w-1/3 border rounded-lg px-3 py-2">
        </div>
        <input type="hidden" id="RAK_BAK_URUTAN" name="RAK_BAK_URUTAN">
        <div class="error-message" id="error_RAK_BAK_URUTAN"></div>
      </div>

      <!-- Keterangan Simpan -->
      <div class="col-span-2">
        <label class="block text-sm font-medium mb-1">Keterangan Simpan</label>
        <textarea id="KETERANGAN_SIMPAN" name="KETERANGAN_SIMPAN" class="w-full border rounded-lg px-3 py-2"></textarea>
        <div class="error-message" id="error_KETERANGAN_SIMPAN"></div>
      </div>

      <!-- Tipe Retensi -->
      <div class="relative">
        <label class="block text-sm font-medium mb-1">Tipe Retensi</label>
        <input id="TIPE_RETENSI" name="TIPE_RETENSI" class="w-full border rounded-lg px-3 py-2" autocomplete="off">
        <div class="error-message" id="error_TIPE_RETENSI"></div>
        <ul id="retensiSuggestions" class="absolute bg-white border border-gray-300 rounded-lg shadow-lg mt-1 w-full hidden z-50 max-h-60 overflow-y-auto"></ul>
      </div>

      <!-- Tanggal Retensi -->
      <div>
        <label class="block text-sm font-medium mb-1">Tanggal Retensi</label>
        <input type="date" id="TANGGAL_RETENSI" name="TANGGAL_RETENSI" class="w-full border rounded-lg px-3 py-2">
        <div class="error-message" id="error_TANGGAL_RETENSI"></div>
      </div>

      <!-- Keterangan -->
      <div>
        <label class="block text-sm font-medium mb-1">Keterangan</label>
        <select id="KETERANGAN" name="KETERANGAN" class="w-full border rounded-lg px-3 py-2">
          <option value="AKTIF">AKTIF</option>
          <option value="INAKTIF">INAKTIF</option>
        </select>
        <div class="error-message" id="error_KETERANGAN"></div>
      </div>

      <!-- Create By -->
      <div class="col-span-2">
        <label class="block text-sm font-medium mb-1">Di Buat Oleh</label>
        <input id="CREATE_BY" name="CREATE_BY" readonly class="w-full border rounded-lg px-3 py-2 bg-gray-100">
      </div>

      <!-- File Upload -->
      <div class="col-span-2">
        <label class="block text-sm font-medium mb-1">Upload File Arsip</label>
        <input type="file" id="FILE" name="FILE" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" class="w-full border rounded-lg px-3 py-2">
        <div class="text-xs text-gray-500 mt-1">Format: PDF, DOC, DOCX, JPG, JPEG, PNG (Maks. 20MB)</div>
        <div class="error-message" id="error_FILE"></div>
      </div>

      <!-- Action Buttons -->
      <div class="col-span-2 flex justify-end gap-2 mt-4">
        <button type="button" id="cancelBtn" class="bg-gray-300 hover:bg-gray-400 text-gray-700 py-2 px-4 rounded-lg">Batal</button>
        <button type="submit" id="saveBtn" class="bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded-lg">
          <i class="fas fa-save mr-1"></i> Simpan
        </button>
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
const errorSummary = document.getElementById("errorSummary");
const errorList = document.getElementById("errorList");
const token = localStorage.getItem("auth_token");

// Notification elements
const notificationBtn = document.getElementById("notificationBtn");
const notificationDropdown = document.getElementById("notificationDropdown");
const notificationCount = document.getElementById("notificationCount");
const notificationList = document.getElementById("notificationList");

// Autocomplete elements
const indeksInput = document.getElementById("NO_INDEKS");
const suggestionBox = document.getElementById("indeksSuggestions");
const klasifikasiInput = document.getElementById("KODE_KLASIFIKASI");
const suggestionKlasifikasi = document.getElementById("klasifikasiSuggestions");
const retensiInput = document.getElementById("TIPE_RETENSI");
const suggestionRetensi = document.getElementById("retensiSuggestions");

// Select elements
const kondisiSelect = document.getElementById("KONDISI");
const pengembangan = document.getElementById("TINGKAT_PENGEMBANGAN");
const jenisNaskahDinas = document.getElementById('JENIS_ARSIP');

// Pagination elements
const paginationControls = document.getElementById("paginationControls");
const prevPageBtn = document.getElementById("prevPageBtn");
const nextPageBtn = document.getElementById("nextPageBtn");
const pageNumbers = document.getElementById("pageNumbers");
const showingFrom = document.getElementById("showingFrom");
const showingTo = document.getElementById("showingTo");
const totalRecords = document.getElementById("totalRecords");
const perPageSelect = document.getElementById("perPageSelect");

// // Confirmation modal elements
const confirmModal = document.getElementById("confirmModal");
const btnNantiMager = document.getElementById("btnNantiMager");
const btnSiapLaksanakan = document.getElementById("btnSiapLaksanakan");
let currentDeleteArsipId = null;

// Pagination state
let currentPage = 1;
let perPage = 10;
let totalPages = 1;
let lastSearchKeyword = "";
let filterAktif = true;
let filterInaktif = true;

// Data storage
let indeksData = [];
let klasifikasiData = [];
let retensiData = [];

// === ERROR HANDLING FUNCTIONS ===
function clearErrors() {
  // Hide error summary
  errorSummary.classList.add("hidden");
  errorList.innerHTML = "";
  
  // Clear all field errors
  const errorMessages = document.querySelectorAll(".error-message");
  errorMessages.forEach(msg => {
    msg.classList.remove("show");
    msg.textContent = "";
  });
  
  // Remove error styling from inputs
  const inputs = form.querySelectorAll("input, select, textarea");
  inputs.forEach(input => {
    input.classList.remove("input-error");
  });
}

function displayErrors(errors) {
  clearErrors();
  
  if (!errors || Object.keys(errors).length === 0) return;
  
  // Show error summary
  errorSummary.classList.remove("hidden");
  
  // Display errors
  Object.keys(errors).forEach(fieldName => {
    const errorMessages = Array.isArray(errors[fieldName]) ? errors[fieldName] : [errors[fieldName]];
    
    // Add to summary list
    errorMessages.forEach(msg => {
      const li = document.createElement("li");
      li.textContent = msg;
      errorList.appendChild(li);
    });
    
    // Display error below field
    const errorDiv = document.getElementById(`error_${fieldName}`);
    const inputField = document.getElementById(fieldName);
    
    if (errorDiv) {
      errorDiv.textContent = errorMessages[0];
      errorDiv.classList.add("show");
    }
    
    if (inputField) {
      inputField.classList.add("input-error");
      
      // Remove error on input change
      inputField.addEventListener("input", function clearFieldError() {
        inputField.classList.remove("input-error");
        if (errorDiv) {
          errorDiv.classList.remove("show");
        }
        inputField.removeEventListener("input", clearFieldError);
      }, { once: true });
    }
  });
  
  // Scroll to first error
  const firstError = form.querySelector(".input-error");
  if (firstError) {
    firstError.scrollIntoView({ behavior: "smooth", block: "center" });
    firstError.focus();
  }
}

// === TOAST ===
function showToast(msg, success = true) {
  toastMessage.textContent = msg;
  toast.classList.remove("bg-red-500", "bg-green-500");
  toast.classList.add(success ? "bg-green-500" : "bg-red-500");
  toast.classList.add("show");
  setTimeout(() => toast.classList.remove("show"), 3000);
}

// === FETCH WITH AUTH ===
async function fetchWithAuth(url, options = {}) {
  const headers = options.headers || {};
  headers["Authorization"] = `Bearer ${token}`;
  headers["Accept"] = "application/json";

  if (!(options.body instanceof FormData) && !headers["Content-Type"]) {
    headers["Content-Type"] = "application/json";
  }

  const res = await fetch(url, { ...options, headers });
  if (res.status === 401) {
    showToast("Token tidak valid atau sesi sudah berakhir", false);
    setTimeout(() => window.location.href = "/", 1500);
    throw new Error("Unauthenticated");
  }
  return res;
}

// === LOAD USER INFO ===
async function loadUserInfo() {
  try {
    const res = await fetchWithAuth('/api/me');
    if (!res.ok) throw new Error('Gagal ambil data user');

    const user = await res.json();

    document.getElementById("ID_DIVISI").value = user.ID_DIVISI ?? "";
    document.getElementById("ID_SUBDIVISI").value = user.ID_SUBDIVISI ?? "";
    document.getElementById("DIVISI_NAME").value = user.divisi?.NAMA_DIVISI ?? "-";
    document.getElementById("SUBDIVISI_NAME").value = user.subdivisi?.NAMA_SUBDIVISI ?? "-";
    document.getElementById("CREATE_BY").value = user.username ?? "-";
  } catch (err) {
    console.error("Gagal memuat user info:", err);
    showToast("Gagal memuat data user", false);
  }
}

// === LOAD DATA WITH PAGINATION ===
// === LOAD DATA WITH PAGINATION ===
async function loadArsip(keyword = "", page = 1) {
  loadingState.classList.remove("hidden");
  emptyState.classList.add("hidden");
  tableBody.innerHTML = "";
  paginationControls.classList.add("hidden");
  
  lastSearchKeyword = keyword;
  
  try {
    let url = `${apiUrl}?page=${page}&per_page=${perPage}`;
    if (keyword.trim()) url += `&search=${encodeURIComponent(keyword)}`;
    
    const res = await fetchWithAuth(url);
    const response = await res.json();
    
    loadingState.classList.add("hidden");
    
    let data = response.data || [];
    
    // Filter berdasarkan status aktif/inaktif
    data = data.filter(arsip => {
      if (filterAktif && arsip.KETERANGAN === 'AKTIF') return true;
      if (filterInaktif && arsip.KETERANGAN === 'INAKTIF') return true;
      return false;
    });
    
    if (!Array.isArray(data) || data.length === 0) {
      emptyState.classList.remove("hidden");
      return;
    }
    
    data.forEach((arsip, i) => {
      const fileLink = arsip.FILE
        ? `<a href="/${arsip.FILE}" target="_blank" class="text-blue-600 underline">DOWNLOAD</a>`
        : "-";
      
      const rowNumber = ((response.current_page - 1) * perPage) + i + 1;
      
      let statusBadge = '-';
      if (arsip.KETERANGAN === 'AKTIF') {
        statusBadge = '<span class="status-aktif">AKTIF</span>';
      } else if (arsip.KETERANGAN === 'INAKTIF') {
        statusBadge = '<span class="status-inaktif">INAKTIF</span>';
      }
      
      const rowClass = arsip.KETERANGAN === 'INAKTIF' ? 'row-inaktif' : '';
      
      const row = `
        <tr class="hover:bg-gray-50 ${rowClass}">
        <td class="px-4 py-3 w-[60px]">${rowNumber}</td>
          <td class="px-4 py-3 w-[150px]">${arsip.divisi?.NAMA_DIVISI ?? "-"}</td>
          <td class="px-4 py-3 w-[150px]">${arsip.subdivisi?.NAMA_SUBDIVISI ?? "-"}</td>
          <td class="px-4 py-3 w-[120px]">${arsip.NO_INDEKS ?? "-"}</td>
          <td class="px-4 py-3 w-[60px]">${arsip.NO_BERKAS ?? "-"}</td>
          <td class="px-4 py-3 w-[150px]">${arsip.JUDUL_BERKAS ?? "-"}</td>
          <td class="px-4 py-3 w-[60px]">${arsip.NO_ISI_BERKAS ?? "-"}</td>
          <td class="px-4 py-3 w-[60px]">${arsip.JENIS_ARSIP ?? "-"}</td>
          <td class="px-4 py-3 w-[150px]">${arsip.KODE_KLASIFIKASI ?? "-"}</td>
          <td class="px-4 py-3 w-[150px]">${arsip.NO_NOTA_DINAS ?? "-"}</td>
          <td class="px-4 py-3 w-[140px]">${arsip.TANGGAL_BERKAS ?? "-"}</td>
          <td class="px-4 py-3 w-[200px]">${arsip.PERIHAL ?? "-"}</td>
          <td class="px-4 py-3 w-[160px]">${arsip.TINGKAT_PENGEMBANGAN ?? "-"}</td>
          <td class="px-4 py-3 w-[120px]">${arsip.KONDISI ?? "-"}</td>
          <td class="px-4 py-3 w-[600px]">${arsip.RAK_BAK_URUTAN ?? "-"}</td>
          <td class="px-4 py-3 w-[150px]">${arsip.KETERANGAN_SIMPAN ?? "-"}</td>
          <td class="px-4 py-3 w-[150px]">${arsip.TIPE_RETENSI ?? "-"}</td>
          <td class="px-4 py-3 w-[140px]">${arsip.TANGGAL_RETENSI ?? "-"}</td>
          <td class="px-4 py-3 w-[150px]">${statusBadge}</td>
          <td class="px-4 py-3 w-[140px]">${arsip.CREATE_BY ?? "-"}</td>
          <td class="px-4 py-3 w-[120px]">${fileLink}</td>
          <td class="px-4 py-3 text-center space-x-2">
            <button onclick="editArsip(${arsip.ID_ARSIP})" class="text-blue-600 hover:text-blue-800"><i class="fas fa-edit"></i></button>
            <button onclick="deleteArsip(${arsip.ID_ARSIP})" class="text-red-600 hover:text-red-800"><i class="fas fa-trash"></i></button>
          </td>
        </tr>`;
      tableBody.insertAdjacentHTML("beforeend", row);
    });
    
    renderPaginationControls({
      current_page: response.current_page,
      last_page: response.last_page,
      from: response.from,
      to: response.to,
      total: response.total
    });
    
  } catch (err) {
    console.error(err);
    loadingState.classList.add("hidden");
    showToast("Gagal memuat data", false);
  }
}

// === PAGINATION RENDER ===
function renderPaginationControls(paginationData) {
  const { current_page, last_page, from, to, total } = paginationData;
  
  currentPage = current_page;
  totalPages = last_page;
  
  if (total === 0) {
    paginationControls.classList.add("hidden");
    return;
  }
  
  paginationControls.classList.remove("hidden");
  
  showingFrom.textContent = from || 0;
  showingTo.textContent = to || 0;
  totalRecords.textContent = total;
  
  prevPageBtn.disabled = current_page === 1;
  nextPageBtn.disabled = current_page === last_page;
  
  pageNumbers.innerHTML = "";
  const maxVisiblePages = 5;
  let startPage = Math.max(1, current_page - Math.floor(maxVisiblePages / 2));
  let endPage = Math.min(last_page, startPage + maxVisiblePages - 1);
  
  if (endPage - startPage < maxVisiblePages - 1) {
    startPage = Math.max(1, endPage - maxVisiblePages + 1);
  }
  
  for (let i = startPage; i <= endPage; i++) {
    const pageBtn = document.createElement("button");
    pageBtn.textContent = i;
    pageBtn.className = `px-3 py-1 border rounded ${i === current_page ? 'bg-blue-600 text-white' : 'hover:bg-gray-100'}`;
    pageBtn.addEventListener("click", () => loadArsip(lastSearchKeyword, i));
    pageNumbers.appendChild(pageBtn);
  }
}

// === PAGINATION EVENT LISTENERS ===
prevPageBtn.addEventListener("click", () => {
  if (currentPage > 1) loadArsip(lastSearchKeyword, currentPage - 1);
});

nextPageBtn.addEventListener("click", () => {
  if (currentPage < totalPages) loadArsip(lastSearchKeyword, currentPage + 1);
});

perPageSelect.addEventListener("change", (e) => {
  perPage = parseInt(e.target.value);
  loadArsip(lastSearchKeyword, 1);
});

// === SEARCH WITH DEBOUNCE ===
document.getElementById("searchInput").addEventListener("input", (e) => {
  clearTimeout(window.searchDelay);
  window.searchDelay = setTimeout(() => {
    loadArsip(e.target.value, 1);
  }, 400);
});

// === MODAL CONTROLS ===
addBtn.addEventListener("click", async () => {
  modal.classList.add("show");
  form.reset();
  clearErrors();
  document.getElementById("arsipId").value = "";
  document.getElementById("modalTitle").innerText = "Tambah Arsip";
  await loadUserInfo();
});

closeModal.addEventListener("click", () => {
  modal.classList.remove("show");
  clearErrors();
});

cancelBtn.addEventListener("click", () => {
  modal.classList.remove("show");
  clearErrors();
});

// === FORM SUBMIT ===
form.addEventListener("submit", async (e) => {
  e.preventDefault();
  clearErrors();

  // Lokasi simpan
  const rak = document.getElementById("RAK_INPUT").value.trim();
  const bak = document.getElementById("BAK_INPUT").value.trim();
  const arsip = document.getElementById("ARSIP_INPUT").value.trim();
  document.getElementById("RAK_BAK_URUTAN").value = `${rak}/${bak}/${arsip}`;

  const id = document.getElementById("arsipId").value;
  const method = "POST";
  const url = id ? `${apiUrl}/${id}?_method=PUT` : apiUrl;

  // Set default KETERANGAN to AKTIF if creating new record
  if (!id && !document.getElementById("KETERANGAN").value) {
    document.getElementById("KETERANGAN").value = "AKTIF";
  }

  const formData = new FormData(form);
  formData.append("ID_DIVISI", document.getElementById("ID_DIVISI").value);
  formData.append("ID_SUBDIVISI", document.getElementById("ID_SUBDIVISI").value);
  formData.append("CREATE_BY", document.getElementById("CREATE_BY").value);

  // Disable submit button
  const saveBtn = document.getElementById("saveBtn");
  const originalText = saveBtn.innerHTML;
  saveBtn.disabled = true;
  saveBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Menyimpan...';

  try {
    const res = await fetchWithAuth(url, { method, body: formData });
    const data = await res.json();

    if (!res.ok) {
      if (res.status === 422 && data.errors) {
        // Validation errors
        displayErrors(data.errors);
        showToast(data.message || "Terdapat kesalahan pada form", false);
      } else {
        throw new Error(data.message || "Gagal menyimpan data");
      }
      return;
    }

    modal.classList.remove("show");
    showToast(data.message || "Data berhasil disimpan");
    loadArsip(lastSearchKeyword, currentPage);
    
  } catch (err) {
    console.error(err);
    showToast(err.message || "Gagal menyimpan data", false);
  } finally {
    saveBtn.disabled = false;
    saveBtn.innerHTML = originalText;
  }
});

// === EDIT ===
async function editArsip(id) {
  try {
    const res = await fetchWithAuth(`${apiUrl}/${id}`);
    const response = await res.json();
    
    if (!response.success) {
      showToast(response.message || "Gagal memuat data", false);
      return;
    }
    
    const data = response.data;

    modal.classList.add("show");
    clearErrors();
    document.getElementById("modalTitle").innerText = "Edit Arsip";
    document.getElementById("arsipId").value = data.ID_ARSIP ?? "";

    // Load user info first
    await loadUserInfo();

    // Fill form fields
    const fields = [
      "NO_INDEKS", "NO_BERKAS", "JUDUL_BERKAS", "NO_ISI_BERKAS", "JENIS_ARSIP",
      "KODE_KLASIFIKASI", "NO_NOTA_DINAS", "TANGGAL_BERKAS", "PERIHAL",
      "TINGKAT_PENGEMBANGAN", "KONDISI", "RAK_BAK_URUTAN", "KETERANGAN_SIMPAN",
      "TIPE_RETENSI", "TANGGAL_RETENSI", "KETERANGAN"
    ];

    fields.forEach(key => {
      const el = document.getElementById(key);
      if (el) el.value = data[key] ?? "";
    });

    // Parse lokasi simpan
    if (data.RAK_BAK_URUTAN) {
      const parts = data.RAK_BAK_URUTAN.split("/");
      document.getElementById("RAK_INPUT").value = parts[0] || "";
      document.getElementById("BAK_INPUT").value = parts[1] || "";
      document.getElementById("ARSIP_INPUT").value = parts[2] || "";
    }

  } catch (err) {
    console.error(err);
    showToast("Gagal memuat data edit", false);
  }
}

// === DELETE ===
async function deleteArsip(id) {
  if (!confirm("Yakin ingin menghapus arsip ini?")) return;

  try {
    const res = await fetchWithAuth(`${apiUrl}/${id}`, { method: "DELETE" });
    const data = await res.json();
    
    if (!res.ok) throw new Error(data.message || "Gagal menghapus");
    
    showToast(data.message || "Data berhasil dihapus");
    loadArsip(lastSearchKeyword, currentPage);
  } catch (err) {
    console.error(err);
    showToast(err.message || "Gagal menghapus data", false);
  }
}

// === NOTIFICATIONS ===
async function loadOverdueNotifications() {
  try {
    const res = await fetchWithAuth(`${apiUrl}/overdue`);
    const response = await res.json();
    const data = response.data || response;

    notificationCount.textContent = data.length;
    
    // TAMBAHKAN BARIS INI
    updateNotificationAnimation(data.length);

    notificationList.innerHTML = '';
    if (data.length === 0) {
      notificationList.innerHTML = `<li class="p-3 text-gray-200 text-sm">Tidak ada arsip retensi lewat</li>`;
      return;
    }

    data.forEach(arsip => {
      const li = document.createElement('li');
      li.className = "notification-item p-3 hover:bg-red-700 cursor-pointer border-b border-red-500 last:border-b-0";
      li.innerHTML = `
        <button class="notification-delete-btn" onclick="handleNotificationDelete(event, ${arsip.ID_ARSIP})">
          <i class="fas fa-times"></i>
        </button>
        <div class="font-semibold text-white text-sm pr-8">${arsip.JUDUL_BERKAS ?? '-'}</div>
        <div class="text-xs text-red-100">Retensi: ${arsip.TANGGAL_RETENSI ?? '-'}</div>
      `;
      li.addEventListener('click', (e) => {
        if (!e.target.closest('.notification-delete-btn')) {
          handleNotificationDelete(e, arsip.ID_ARSIP);
          notificationDropdown.classList.add('hidden');
        }
      });
      notificationList.appendChild(li);
    });
  } catch (err) {
    console.error(err);
    showToast("Gagal memuat notifikasi", false);
  }
}

function updateNotificationAnimation(count) {
  const btn = document.getElementById('notificationBtn');
  if (count >= 1) {
    btn.classList.add('annoying-btn');
  } else {
    btn.classList.remove('annoying-btn');
  }
}


notificationBtn.addEventListener("click", () => {
  notificationDropdown.classList.toggle("hidden");
});

// Close dropdown when clicking outside
document.addEventListener("click", (e) => {
  if (!notificationBtn.contains(e.target) && !notificationDropdown.contains(e.target)) {
    notificationDropdown.classList.add("hidden");
  }
});

// === AUTOCOMPLETE: INDEKS ===
async function loadIndeksData() {
  try {
    const res = await fetchWithAuth("/api/m_indeks/all");
    if (!res.ok) throw new Error("Gagal memuat data indeks");
    indeksData = await res.json();
  } catch (err) {
    console.error("Gagal ambil data indeks:", err);
  }
}

indeksInput.addEventListener("input", () => {
  const query = indeksInput.value.toLowerCase();
  suggestionBox.innerHTML = "";

  if (!query.trim()) {
    suggestionBox.classList.add("hidden");
    return;
  }

  const filtered = indeksData.filter(item =>
    (item.NO_INDEKS?.toLowerCase().includes(query) ||
     item.NAMA_INDEKS?.toLowerCase().includes(query))
  );

  if (filtered.length === 0) {
    suggestionBox.classList.add("hidden");
    return;
  }

  filtered.slice(0, 50).forEach(item => {
    const li = document.createElement("li");
    li.className = "px-3 py-2 hover:bg-blue-100 cursor-pointer text-sm border-b last:border-b-0";
    li.innerHTML = `<strong>${item.NO_INDEKS}</strong> - ${item.NAMA_INDEKS}`;
    li.addEventListener("click", () => {
      indeksInput.value = item.NO_INDEKS;

      const judulBerkasInput = document.getElementById("JUDUL_BERKAS");
      if (judulBerkasInput) {
        judulBerkasInput.value = item.NAMA_INDEKS;
      }
      
      suggestionBox.classList.add("hidden");
    });
    suggestionBox.appendChild(li);
  });

  suggestionBox.classList.remove("hidden");
});

// === AUTOCOMPLETE: KLASIFIKASI ===
async function loadKlasifikasiData() {
  try {
    const res = await fetchWithAuth("/api/m_klasifikasi/all");
    if (!res.ok) throw new Error("Gagal memuat data klasifikasi");
    klasifikasiData = await res.json();
  } catch (err) {
    console.error("Gagal ambil data klasifikasi:", err);
  }
}

klasifikasiInput.addEventListener("input", () => {
  const query = klasifikasiInput.value.toLowerCase();
  suggestionKlasifikasi.innerHTML = "";

  if (!query.trim()) {
    suggestionKlasifikasi.classList.add("hidden");
    return;
  }

  const filtered = klasifikasiData.filter(item =>
    (item.KODE_KLASIFIKASI?.toLowerCase().includes(query) ||
     item.DESKRIPSI?.toLowerCase().includes(query))
  );

  if (filtered.length === 0) {
    suggestionKlasifikasi.classList.add("hidden");
    return;
  }

  filtered.slice(0, 50).forEach(item => {
    const li = document.createElement("li");
    li.className = "px-3 py-2 hover:bg-blue-100 cursor-pointer text-sm border-b last:border-b-0";
    li.innerHTML = `<strong>${item.KODE_KLASIFIKASI} ${item.KATEGORI}</strong> - ${item.DESKRIPSI}`;
    li.addEventListener("click", () => {
      klasifikasiInput.value = item.KODE_KLASIFIKASI;
      suggestionKlasifikasi.classList.add("hidden");
    });
    suggestionKlasifikasi.appendChild(li);
  });

  suggestionKlasifikasi.classList.remove("hidden");
});

// === AUTOCOMPLETE: RETENSI ===
async function loadRetensiData() {
  try {
    const res = await fetchWithAuth("/api/m_retensi/all");
    if (!res.ok) throw new Error("Gagal memuat data retensi");
    retensiData = await res.json();
  } catch (err) {
    console.error("Gagal ambil data retensi:", err);
  }
}

retensiInput.addEventListener("input", () => {
  const query = retensiInput.value.toLowerCase();
  suggestionRetensi.innerHTML = "";

  if (!query.trim()) {
    suggestionRetensi.classList.add("hidden");
    return;
  }

  const filtered = retensiData.filter(item =>
    (item.JENIS_ARSIP?.toLowerCase().includes(query) ||
     item.BIDANG_ARSIP?.toLowerCase().includes(query) ||
     item.TIPE_ARSIP?.toLowerCase().includes(query) ||
     item.DETAIL_TIPE_ARSIP?.toLowerCase().includes(query) ||
     item.MASA_AKTIF?.toLowerCase().includes(query))
  );

  if (filtered.length === 0) {
    suggestionRetensi.classList.add("hidden");
    return;
  }

  filtered.slice(0, 50).forEach(item => {
    const li = document.createElement("li");
    li.className = "px-3 py-2 hover:bg-blue-100 cursor-pointer text-sm border-b last:border-b-0";
    
    let displayText = '';
    if (item.JENIS_ARSIP) displayText += `<div class="font-semibold text-blue-600">${item.JENIS_ARSIP}</div>`;
    if (item.BIDANG_ARSIP) displayText += `<div class="text-gray-700">Bidang: ${item.BIDANG_ARSIP}</div>`;
    if (item.TIPE_ARSIP) displayText += `<div class="text-gray-600">Tipe: ${item.TIPE_ARSIP}</div>`;
    if (item.DETAIL_TIPE_ARSIP) displayText += `<div class="text-gray-600">Detail: ${item.DETAIL_TIPE_ARSIP}</div>`;
    if (item.MASA_AKTIF) displayText += `<div class="text-green-600 font-medium">Masa Aktif: ${item.MASA_AKTIF}</div>`;
    
    li.innerHTML = displayText;
    
    li.addEventListener("click", () => {
      retensiInput.value = item.MASA_AKTIF || '';
      suggestionRetensi.classList.add("hidden");
    });
    suggestionRetensi.appendChild(li);
  });

  suggestionRetensi.classList.remove("hidden");
});

// Close autocomplete dropdowns when clicking outside
document.addEventListener("click", (e) => {
  if (!suggestionBox.contains(e.target) && e.target !== indeksInput) {
    suggestionBox.classList.add("hidden");
  }
  if (!suggestionKlasifikasi.contains(e.target) && e.target !== klasifikasiInput) {
    suggestionKlasifikasi.classList.add("hidden");
  }
  if (!suggestionRetensi.contains(e.target) && e.target !== retensiInput) {
    suggestionRetensi.classList.add("hidden");
  }
});

// === LOAD DROPDOWN DATA ===
async function loadKondisiData() {
  try {
    const res = await fetchWithAuth("/api/m_kondisi/all");
    if (!res.ok) throw new Error("Gagal memuat data kondisi");
    const kondisiData = await res.json();
    
    kondisiSelect.innerHTML = '<option value="">-- Pilih Kondisi --</option>';
    kondisiData.forEach(item => {
      const option = document.createElement("option");
      option.value = item.NAMA_KONDISI;
      option.textContent = item.NAMA_KONDISI;
      kondisiSelect.appendChild(option);
    });
  } catch (err) {
    console.error("Gagal ambil data kondisi:", err);
  }
}

async function loadTingkatpengembanganData() {
  try {
    const res = await fetchWithAuth("/api/m_tingkatpengembangan/all");
    if (!res.ok) throw new Error("Gagal memuat data tingkat pengembangan");
    const pengembanganData = await res.json();
    
    pengembangan.innerHTML = '<option value="">-- Pilih Tingkat Pengembangan</option>';
    pengembanganData.forEach(item => {
      const option = document.createElement("option");
      option.value = item.NAMA_PENGEMBANGAN;
      option.textContent = item.NAMA_PENGEMBANGAN;
      pengembangan.appendChild(option);
    });
  } catch (err) {
    console.error("Gagal ambil data tingkat pengembangan:", err);
  }
}

async function loadJenisNaskahDinasData() {
  try {
    const res = await fetchWithAuth("/api/m_jenisnaskah/all");
    if (!res.ok) throw new Error("Gagal memuat data jenis naskah dinas");
    const jenisNaskahData = await res.json();
    
    jenisNaskahDinas.innerHTML = '<option value="">-- Pilih Jenis Naskah Dinas --</option>';
    jenisNaskahData.forEach(item => {
      const option = document.createElement("option");
      option.value = item.NAMA_JENIS;
      option.textContent = item.NAMA_JENIS;
      jenisNaskahDinas.appendChild(option);
    });
  } catch (err) {
    console.error("Gagal ambil data jenis naskah dinas:", err);
  }
}

  // === HANDLE NOTIFICATION DELETE ===
function handleNotificationDelete(event, arsipId) {
  event.stopPropagation();
  currentDeleteArsipId = arsipId;
  confirmModal.classList.add("show");
  }

  // === MODAL CONFIRMATION HANDLERS ===
  btnNantiMager.addEventListener("click", () => {
    confirmModal.classList.remove("show");
    currentDeleteArsipId = null;
    showToast("Oke, santai aja dulu 😴", true);
  });

  

  btnSiapLaksanakan.addEventListener("click", async () => {
  if (!currentDeleteArsipId) return;
  
  try {
    // 1. AMBIL DATA LENGKAP ARSIP DULU
    const getRes = await fetchWithAuth(`${apiUrl}/${currentDeleteArsipId}`);
    const getResponse = await getRes.json();
    
    if (!getResponse.success) {
      throw new Error(getResponse.message || "Gagal mengambil data arsip");
    }
    
    const arsipData = getResponse.data;
    
    // 2. BUAT FORMDATA DENGAN SEMUA FIELD YANG ADA
    const formData = new FormData();
    
    // Append semua field yang ada di arsipData
    formData.append("ID_DIVISI", arsipData.ID_DIVISI || "");
    formData.append("ID_SUBDIVISI", arsipData.ID_SUBDIVISI || "");
    formData.append("NO_INDEKS", arsipData.NO_INDEKS || "");
    formData.append("NO_BERKAS", arsipData.NO_BERKAS || "");
    formData.append("JUDUL_BERKAS", arsipData.JUDUL_BERKAS || "");
    formData.append("NO_ISI_BERKAS", arsipData.NO_ISI_BERKAS || "");
    formData.append("JENIS_ARSIP", arsipData.JENIS_ARSIP || "");
    formData.append("KODE_KLASIFIKASI", arsipData.KODE_KLASIFIKASI || "");
    formData.append("NO_NOTA_DINAS", arsipData.NO_NOTA_DINAS || "");
    formData.append("TANGGAL_BERKAS", arsipData.TANGGAL_BERKAS || "");
    formData.append("PERIHAL", arsipData.PERIHAL || "");
    formData.append("TINGKAT_PENGEMBANGAN", arsipData.TINGKAT_PENGEMBANGAN || "");
    formData.append("KONDISI", arsipData.KONDISI || "");
    formData.append("RAK_BAK_URUTAN", arsipData.RAK_BAK_URUTAN || "");
    formData.append("KETERANGAN_SIMPAN", arsipData.KETERANGAN_SIMPAN || "");
    formData.append("TIPE_RETENSI", arsipData.TIPE_RETENSI || "");
    formData.append("TANGGAL_RETENSI", arsipData.TANGGAL_RETENSI || "");
    formData.append("CREATE_BY", arsipData.CREATE_BY || "");
    
    // 3. UPDATE FIELD KETERANGAN JADI INAKTIF (INI YANG BERUBAH)
    formData.append("KETERANGAN", "INAKTIF");
    
    // 4. METHOD PUT
    formData.append("_method", "PUT");
    
    // 5. KIRIM REQUEST UPDATE
    const res = await fetchWithAuth(`${apiUrl}/${currentDeleteArsipId}`, {
      method: "POST",
      body: formData
    });
    
    const data = await res.json();
    
    if (!res.ok) throw new Error(data.message || "Gagal update status");
    
    confirmModal.classList.remove("show");
    showToast("OKE DITUNGGU HATI HATI DI JALAN!✅ ", true);
    
    // Reload data
    await loadOverdueNotifications();
    loadArsip(lastSearchKeyword, currentPage);
    
    currentDeleteArsipId = null;
  } catch (err) {
    console.error(err);
    showToast(err.message || "Gagal update status arsip", false);
  }
});

  // Close modal when clicking outside
  confirmModal.addEventListener("click", (e) => {
    if (e.target === confirmModal) {
      confirmModal.classList.remove("show");
      currentDeleteArsipId = null;
    }
  });

  // === FILTER BUTTON HANDLERS ===
const filterAktifBtn = document.getElementById("filterAktifBtn");
const filterInaktifBtn = document.getElementById("filterInaktifBtn");

filterAktifBtn.addEventListener("click", () => {
  filterAktif = !filterAktif;
  filterAktifBtn.classList.toggle("active");
  
  // Jika kedua filter dimatikan, nyalakan keduanya
  if (!filterAktif && !filterInaktif) {
    filterAktif = true;
    filterInaktif = true;
    filterAktifBtn.classList.add("active");
    filterInaktifBtn.classList.add("active");
  }
  
  loadArsip(lastSearchKeyword, 1);
});

filterInaktifBtn.addEventListener("click", () => {
  filterInaktif = !filterInaktif;
  filterInaktifBtn.classList.toggle("active");
  
  // Jika kedua filter dimatikan, nyalakan keduanya
  if (!filterAktif && !filterInaktif) {
    filterAktif = true;
    filterInaktif = true;
    filterAktifBtn.classList.add("active");
    filterInaktifBtn.classList.add("active");
  }
  
  loadArsip(lastSearchKeyword, 1);
});
// === INITIALIZATION ===
(async () => {
  // Check authentication
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
    return;
  }
  setInterval(() => {
  const btn = document.getElementById('notificationBtn');
  const count = parseInt(document.getElementById('notificationCount').textContent);
  
  // Hanya shake jika ada notifikasi
  if (btn && count >= 1) {
    btn.classList.add('shake-btn');
    setTimeout(() => {
      btn.classList.remove('shake-btn');
    }, 500);
  }
  }, 2000);

  // Load all data
  await Promise.all([
    loadIndeksData(),
    loadKlasifikasiData(),
    loadRetensiData(),
    loadKondisiData(),
    loadTingkatpengembanganData(),
    loadJenisNaskahDinasData(),
    loadOverdueNotifications()
  ]);
  
  loadArsip();
})();
</script>

</body>
