<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>PELINDO - Portal Terminal</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    /* Animasi fade */
    .fade {
      transition: opacity 1.5s ease-in-out;
    }

    /* Card hover animation */
    .portal-card {
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
      backdrop-filter: blur(8px);
    }
    .portal-card:hover {
      transform: translateY(-8px) scale(1.02);
    }
    .portal-card:active {
      transform: translateY(-2px) scale(0.98);
    }

    /* Container animation */
    .cards-container {
      animation: fadeInUp 0.6s ease-out;
    }
    @keyframes fadeInUp {
      from {
        opacity: 0;
        transform: translateY(30px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    /* Stagger animation for cards */
    .portal-card {
      animation: cardFadeIn 0.5s ease-out backwards;
    }
    @keyframes cardFadeIn {
      from {
        opacity: 0;
        transform: translateY(20px) scale(0.9);
      }
      to {
        opacity: 1;
        transform: translateY(0) scale(1);
      }
    }
  </style>
</head>
<body class="min-h-screen flex flex-col bg-gray-50">

  @include('components.TI_navbar')

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

  <!-- Main Portal -->
  <main class="flex-1 relative overflow-hidden">
    <!-- Background Slideshow -->
    <div id="bgSlideshow" class="absolute inset-0">
      <img src="{{ asset('images/makasar.jpg') }}" class="fade absolute inset-0 w-full h-full object-cover opacity-100" />
      <img src="{{ asset('images/priuk.jpg') }}" class="fade absolute inset-0 w-full h-full object-cover opacity-0" />
      <img src="{{ asset('images/pontianak.jpg') }}" class="fade absolute inset-0 w-full h-full object-cover opacity-0" />
      <img src="{{ asset('images/balikpapan.jpg') }}" class="fade absolute inset-0 w-full h-full object-cover opacity-0" />
      <img src="{{ asset('images/belawan.jpg') }}" class="fade absolute inset-0 w-full h-full object-cover opacity-0" />
    </div>

    <!-- Konten Portal -->
    <div class="relative z-10 min-h-[calc(100vh-4rem)] flex flex-col items-center justify-center p-4 sm:p-6 md:p-8">

      <!-- Grid Cards -->
      <div id="terminalCards" class="cards-container flex flex-wrap justify-center gap-4 sm:gap-5 md:gap-6 max-w-5xl mx-auto px-4">
        <!-- Card-card terminal akan dibuat otomatis di sini -->
      </div>
    </div>
  </main>

  <script>
    // === Background Slideshow ===
    const slides = document.querySelectorAll('#bgSlideshow img');
    let currentSlide = 0;

    function showNextSlide() {
      slides[currentSlide].classList.remove('opacity-100');
      slides[currentSlide].classList.add('opacity-0');
      currentSlide = (currentSlide + 1) % slides.length;
      slides[currentSlide].classList.remove('opacity-0');
      slides[currentSlide].classList.add('opacity-100');
    }

    setInterval(showNextSlide, 3000); 


    // === Portal Logic ===
    const token = localStorage.getItem('auth_token');
    const cardContainer = document.getElementById('terminalCards');

    async function loadPortalCards() {
      try {
        // --- Cek role user ---
        const resUser = await fetch('/api/me', {
          headers: {
            'Authorization': `Bearer ${token}`,
            'Accept': 'application/json'
          }
        });

        if (!resUser.ok) throw new Error('Gagal mengambil data user');
        const user = await resUser.json();
        const role = (user.role?.Nama_role || user.role?.nama_role || '').toUpperCase();
        const allowed = ['ADMIN', 'USER'].includes(role);

        // --- Ambil semua terminal dari API ---
        const resTerm = await fetch('/api/m_terminal', {
          headers: {
            'Authorization': `Bearer ${token}`,
            'Accept': 'application/json'
          }
        });

        if (!resTerm.ok) throw new Error('Gagal mengambil data terminal');
        const terminals = await resTerm.json();

        // --- Bersihkan kontainer ---
        cardContainer.innerHTML = '';

        if (!terminals.length) {
          cardContainer.innerHTML = `
            <p class="col-span-full text-center text-white text-lg opacity-90">
              Tidak ada data terminal yang tersedia.
            </p>`;
          return;
        }

        // --- Loop setiap terminal dan buat card ---
        terminals.forEach((terminal, index) => {
          const { ID_TERMINAL, NAMA_TERMINAL } = terminal;
          const isActive = allowed;

          const card = document.createElement('a');
          card.href = isActive ? `/portal/terminal/${ID_TERMINAL}` : '#';
          card.className = `
            portal-card group relative rounded-xl shadow-lg
            p-4 sm:p-5 md:p-6
            flex flex-col items-center justify-center
            text-center min-w-[140px] sm:min-w-[160px] md:min-w-[180px]
            ${isActive
              ? 'bg-[#224E9F]/90 hover:bg-[#1b3f80]/95 hover:shadow-2xl cursor-pointer'
              : 'bg-gray-400/80 cursor-not-allowed opacity-70'}
          `;

          // Stagger animation delay
          card.style.animationDelay = `${index * 0.1}s`;

          card.innerHTML = `
            <div class="${isActive
              ? 'bg-white/95 text-[#224E9F] group-hover:bg-white group-hover:text-[#1b3f80]'
              : 'bg-gray-200 text-gray-500'} rounded-full p-3 sm:p-4 mb-3 transition-all duration-300">
              <svg class="w-6 h-6 sm:w-7 sm:h-7 md:w-8 md:h-8" fill="currentColor" viewBox="0 0 24 24">
                <path d="M3 4a1 1 0 0 1 1-1h16a1 1 0 0 1 1 1v16a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V4zm3 3v10h12V7H6zm2 2h8v6H8V9z"/>
              </svg>
            </div>
            <h2 class="text-base sm:text-lg md:text-xl font-semibold text-white leading-tight">${NAMA_TERMINAL}</h2>
          `;

          cardContainer.appendChild(card);
        });

      } catch (err) {
        console.error('Error loading portal:', err);
        cardContainer.innerHTML = `
          <p class="col-span-full text-center text-white text-lg opacity-90">
            Terjadi kesalahan saat memuat portal terminal.
          </p>`;
      }
    }

    loadPortalCards();
  </script>
</body>
</html>
