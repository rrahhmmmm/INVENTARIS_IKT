<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Sidebar Pelindo</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="flex bg-gray-100">

  <!-- Tombol Toggle Sidebar -->
  <button id="toggleSidebar" 
    class="p-3 bg-white shadow-md fixed top-1/2 -translate-y-1/2 left-4 z-50 rounded-lg hover:bg-[#224E9F] 
           transition-all duration-300">
    ☰
  </button>

  <!-- Sidebar -->
  <aside id="sidebar" 
    class="fixed top-0 left-0 h-full w-64 bg-white shadow-2xl transform -translate-x-full 
           transition-transform duration-300 z-40 border-r-8">
    <div class="flex flex-col h-full">
      
      <!-- Logo -->
      <div class="p-4 border-b">
        <img src="/storage/iktinven.png" alt="PELINDO Logo" class="h-30 mx-auto">
      </div>

      <!-- Menu -->
      <nav class="flex-1 p-4">

  <!-- HOME -->
  <a href="home" 
     class="w-full flex justify-between items-center px-3 py-2 rounded-md font-semibold hover:bg-blue-200">
    <span>HOME</span>
  </a>

  <a href="arsip" 
     class="w-full flex justify-between items-center px-3 py-2 rounded-md font-semibold hover:bg-blue-200">
    <span>DASHBOARD</span>
  </a>

  <a href="Tarsip" 
     class="w-full flex justify-between items-center px-3 py-2 rounded-md font-semibold hover:bg-blue-200">
    <span>TRANSAKSI</span>
  </a>

  <!-- Dropdown Master -->
  <div class="relative mt-2">
    <button id="masterBtn" 
      class="w-full flex justify-between items-center font-semibold px-3 py-2 rounded-md hover:bg-blue-200">
      <span>MASTER</span>
      <svg id="masterIcon" xmlns="http://www.w3.org/2000/svg" 
        class="h-4 w-4 transform transition-transform duration-200" 
        fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" 
          stroke-width="2" d="M19 9l-7 7-7-7" />
      </svg>
    </button>

    <div id="masterMenu" class="hidden flex-col mt-1 space-y-1 pl-6">
      <a href="/retensi" class="block px-3 py-2 rounded-md hover:bg-blue-200">Retensi</a>
      <a href="/divisi" class="block px-3 py-2 rounded-md hover:bg-blue-200">Klasifikasi</a>
      <a href="/subdivisi" class="block px-3 py-2 rounded-md hover:bg-blue-200">Status</a>
      <a href="/role" class="block px-3 py-2 rounded-md hover:bg-blue-200">Index</a>
  </div>
</nav>


      <!-- Logout -->
      <div class="p-4 border-t">
      <div class="flex items-center space-x-3 p-4">
        <img src="storage/user.png" alt="User Avatar" 
             class="w-12 h-12 rounded-full border-">
        <div>
        <p id="username" class="font-semibold text-gray-800">Guest</p>
<small id="status" class="text-gray-500">Not Logged In</small>
        </div>
      </div>
        <button onclick="logoutUser()" 
          class="w-full text-left px-3 py-2 rounded-md hover:bg-red-50 text-red-600">
          Logout
        </button>
      </div>

    </div>
  </aside>

  <script>
    let sidebarOpen = false;
    
    // Toggle Sidebar
    const toggleSidebar = document.getElementById("toggleSidebar");
    const sidebar = document.getElementById("sidebar");
    const mainContent = document.getElementById("mainContent");

    toggleSidebar.addEventListener("click", () => {
      sidebar.classList.toggle("-translate-x-full");
      if (mainContent) {
        mainContent.classList.toggle("ml-64");
      }

      if (!sidebarOpen) {
        toggleSidebar.classList.remove("left-4");
        toggleSidebar.classList.add("left-[266px]");
        sidebarOpen = true;
      } else {
        toggleSidebar.classList.remove("left-[266px]");
        toggleSidebar.classList.add("left-4");
        sidebarOpen = false;
      }
    });

    // Dropdown Master
    const masterBtn = document.getElementById("masterBtn");
    const masterMenu = document.getElementById("masterMenu");
    const masterIcon = document.getElementById("masterIcon");

    masterBtn.addEventListener("click", () => {
      masterMenu.classList.toggle("hidden");
      masterIcon.classList.toggle("rotate-180");
    });

    // username user
    async function loadUsername() {
  const token = localStorage.getItem('auth_token');
  if (!token) return;

  try {
    const res = await fetch('/api/me', {
      headers: {
        'Authorization': `Bearer ${token}`,
        'Accept': 'application/json'
      }
    });

    if (res.ok) {
      const user = await res.json();
      document.getElementById('username').textContent = user.username || 'Guest';
      document.getElementById('status').textContent = 'Logged In';
    } else {
      document.getElementById('username').textContent = 'Guest';
      document.getElementById('status').textContent = 'Not Logged In';
    }
  } catch (err) {
    console.error(err);
    document.getElementById('username').textContent = 'Guest';
  }
}

loadUsername();

    // Logout
    async function logoutUser() {
    try {
        const token = localStorage.getItem('auth_token');
        const response = await fetch('/api/logout', {
            method: 'POST',
            headers: {
                'Authorization': `Bearer ${token}`,
                'Accept': 'application/json',
            },
        });
        const data = await response.json();
        if (response.ok) {
            localStorage.removeItem('auth_token');
            localStorage.removeItem('auth_user');
            window.location.href = '/';
        } else {
            alert(data.message || 'Logout gagal');
        }
    } catch (error) {
        console.log(error);
        alert('Terjadi kesalahan saat logout');
    }
}
  </script>
</body>
</html>
