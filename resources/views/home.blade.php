<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>PELINDO - Portal</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <!-- <style>
    .bg-terminal {
      background-image: url('/storage/bgimg.svg');
      background-size: cover;
      background-position: center;
      background-repeat: no-repeat;
    }
  </style> -->
</head>
<body class="min-h-screen flex flex-col">

  <!-- Header -->
  <header class="bg-white shadow-sm">
  <div class="container mx-auto flex justify-between items-center p-4">
    <!-- Logo -->
    <div class="flex items-center space-x-4">
      <img src="/storage/logopel.png" alt="PELINDO Logo" class="h-20 ">
    </div>

    <!-- Navbar -->
    <nav class="flex items-center space-x-6">
      <!-- Dropdown Master -->
      <div class="relative group">
        <button class="flex items-center space-x-1 text-black hover:text-blue-600 focus:outline-none">
          <span>Master Admin</span>
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                  d="M19 9l-7 7-7-7" />
          </svg>
        </button>
        <!-- Dropdown Menu -->
        <div class="absolute hidden group-hover:block bg-white border rounded-md shadow-lg mt-2 w-40">
          <a href="/divisi" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Divisi</a>
          <a href="/subdivisi" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Subdivisi</a>
          <a href="/user" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">User</a>
        </div>
      </div>

      <!-- Tombol Logout -->
      <form action="/logout" method="POST">
        <!-- csrf token kalau pakai Laravel -->
        @csrf
        <button type="submit" class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600">
          Logout
        </button>
      </form>
    </nav>
  </div>
</header>


  <!-- Main Portal -->
  <main class="flex-1 bg- relative">
    <div class="absolute inset-0 bg-white bg-opacity-30"></div>
    <div class="relative z-10 min-h-full flex items-center justify-center p-6">
      
      <div class="grid md:grid-cols-2 gap-8 max-w-4xl w-full mt-32">

        <!-- Card Inventaris -->
        <a href="/inventaris" 
          class="group relative bg-[#224E9F] rounded-2xl shadow-lg p-8 flex flex-col items-center justify-center transition transform hover:-translate-y-2 hover:shadow-2xl hover:bg-[#1b3f80]">
          
          <div class="bg-white text-[#224E9F] rounded-full p-6 mb-4 group-hover:bg-[#1b3f80] group-hover:text-white transition">
            <!-- Icon Inventaris -->
            <svg class="w-10 h-10" fill="currentColor" viewBox="0 0 24 24">
              <path d="M3 4a1 1 0 0 1 1-1h4V2h8v1h4a1 1 0 0 1 1 1v4h-2V5H5v14h6v2H4a1 1 0 0 1-1-1V4zm17 7a1 1 0 0 0-1-1h-7a1 1 0 0 0-1 1v10h2v-4h5v4h2V11zm-2 3h-5v-2h5v2z"/>
            </svg>
          </div>

          <h2 class="text-2xl font-semibold mb-2 text-white">INVENTARIS</h2>
          <p class="text-white text-center opacity-90">Kelola data barang dan aset perusahaan dengan mudah.</p>
        </a>

        <!-- Card Arsip -->
        <a href="/arsip" 
          class="group relative bg-[#224E9F] rounded-2xl shadow-lg p-8 flex flex-col items-center justify-center transition transform hover:-translate-y-2 hover:shadow-2xl hover:bg-[#1b3f80]">
          
          <div class="bg-white text-[#224E9F] rounded-full p-6 mb-4 group-hover:bg-[#1b3f80] group-hover:text-white transition">
            <!-- Icon Arsip -->
            <svg class="w-10 h-10" fill="currentColor" viewBox="0 0 24 24">
              <path d="M3 4a1 1 0 0 1 1-1h16a1 1 0 0 1 1 1v4H3V4zm0 6h18v10a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V10zm5 3v2h8v-2H8z"/>
            </svg>
          </div>

          <h2 class="text-2xl font-semibold mb-2 text-white">ARSIP</h2>
          <p class="text-white text-center opacity-90">Akses dan kelola dokumen arsip secara cepat dan aman.</p>
        </a>

      </div>

    </div>
  </main>

  <!-- Footer -->
  <footer class="bg-white text-[#357ABD] p-6 text-center">
    <p class="text-sm">© PT Indonesia Kendaraan Terminal Tbk</p>
  </footer>

</body>
</html>
