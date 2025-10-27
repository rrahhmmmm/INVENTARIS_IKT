<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>PELINDO - Portal</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen flex flex-col">

  @include('components.A_navbar')

  <!-- Main Portal -->
  <main class="flex-1 relative">
    <div class="absolute inset-0 bg-white bg-opacity-30"
      style="background: url('{{ asset('images/bghome.jpg') }}') center center / cover no-repeat;">
    </div>

    <div class="relative z-10 min-h-full flex items-center justify-center p-6">
      <div class="grid md:grid-cols-2 gap-8 max-w-4xl w-full mt-[13rem]">

        <!-- Card Inventaris -->
        <a 
          id="cardInventaris"
          href="#"
          class="group relative bg-gray-400 cursor-not-allowed opacity-70 
              rounded-2xl shadow-lg p-8 flex flex-col items-center justify-center transition transform">
          <div class="bg-gray-200 text-gray-500 rounded-full p-6 mb-4 transition">
            <svg class="w-10 h-10" fill="currentColor" viewBox="0 0 24 24">
              <path d="M3 4a1 1 0 0 1 1-1h4V2h8v1h4a1 1 0 0 1 1 1v4h-2V5H5v14h6v2H4a1 1 0 0 1-1-1V4zm17 7a1 1 0 0 0-1-1h-7a1 1 0 0 0-1 1v10h2v-4h5v4h2V11zm-2 3h-5v-2h5v2z"/>
            </svg>
          </div>
          <h2 class="text-2xl font-semibold mb-2 text-white">INVENTARIS</h2>
          <p class="text-white text-center opacity-90">Anda tidak memiliki akses ke menu ini.</p>
        </a>

        <!-- Card Arsip -->
        <a 
          id="cardArsip"
          href="#"
          class="group relative bg-gray-400 cursor-not-allowed opacity-70 
              rounded-2xl shadow-lg p-8 flex flex-col items-center justify-center transition transform">
          <div class="bg-gray-200 text-gray-500 rounded-full p-6 mb-4 transition">
            <svg class="w-10 h-10" fill="currentColor" viewBox="0 0 24 24">
              <path d="M3 4a1 1 0 0 1 1-1h16a1 1 0 0 1 1 1v4H3V4zm0 6h18v10a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V10zm5 3v2h8v-2H8z"/>
            </svg>
          </div>
          <h2 class="text-2xl font-semibold mb-2 text-white">ARSIP</h2>
          <p class="text-white text-center opacity-90">Anda tidak memiliki akses ke menu ini.</p>
        </a>

      </div>
    </div>
  </main>

  <script>
    async function checkUserRole() {
      const token = localStorage.getItem('auth_token');
      if (!token) return;

      try {
        const res = await fetch('/api/me', {
          headers: {
            'Authorization': `Bearer ${token}`,
            'Accept': 'application/json'
          }
        });

        if (!res.ok) return;
        const user = await res.json();

        const role = (user.role?.Nama_role || user.role?.nama_role || '').toUpperCase();
        const allowed = ['ADMIN', 'USER'].includes(role);

        const cardInventaris = document.getElementById('cardInventaris');
        const cardArsip = document.getElementById('cardArsip');

        if (allowed) {
          // --- AKTIFKAN CARD INVENTARIS ---
          cardInventaris.href = '/inventaris';
          cardInventaris.classList.remove('bg-gray-400', 'cursor-not-allowed', 'opacity-70');
          cardInventaris.classList.add('bg-[#224E9F]', 'cursor-pointer', 'hover:-translate-y-2', 'hover:shadow-2xl', 'hover:bg-[#1b3f80]');
          cardInventaris.querySelector('div').classList.remove('bg-gray-200', 'text-gray-500');
          cardInventaris.querySelector('div').classList.add('bg-white', 'text-[#224E9F]', 'group-hover:bg-[#1b3f80]', 'group-hover:text-white');
          cardInventaris.querySelector('p').textContent = 'Kelola data barang dan aset perusahaan dengan mudah.';

          // --- AKTIFKAN CARD ARSIP ---
          cardArsip.href = '/dashboard';
          cardArsip.classList.remove('bg-gray-400', 'cursor-not-allowed', 'opacity-70');
          cardArsip.classList.add('bg-[#224E9F]', 'cursor-pointer', 'hover:-translate-y-2', 'hover:shadow-2xl', 'hover:bg-[#1b3f80]');
          cardArsip.querySelector('div').classList.remove('bg-gray-200', 'text-gray-500');
          cardArsip.querySelector('div').classList.add('bg-white', 'text-[#224E9F]', 'group-hover:bg-[#1b3f80]', 'group-hover:text-white');
          cardArsip.querySelector('p').textContent = 'Akses dan kelola dokumen arsip secara cepat dan aman.';
        }

      } catch (err) {
        console.error('Role check error:', err);
      }
    }

    checkUserRole();
  </script>
</body>
</html>
