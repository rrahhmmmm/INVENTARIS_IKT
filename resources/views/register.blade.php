<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Registrasi Akun</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-blue-800 min-h-screen flex items-center justify-center"
      style="background-image: url('/storage/bglogin.png');">

  <div class="bg-white shadow-lg rounded-2xl w-full max-w-md p-8 m-8 ">
    <!-- Logo -->
    <div class="flex justify-between items-center ">
      <!-- <img src="/storage/logopel.png" alt="PELINDO Logo" class="h-10 items-start" /> -->
      <!-- <img src="/storage/bumn.png" alt="BUMN Logo" class="h-24 items-end" /> -->
    </div>
    <div class="flex justify-center items-center">
      <img src="/storage/iktinven.png" alt="IKT Logo" class="h-48 " />
    </div>

    <!-- Alert -->
    <div id="alertBox" class="hidden p-3 mb-4 rounded-lg text-sm"></div>

    <!-- Form -->
    <form id="registerForm" class="space-y-4">
      <!-- Username -->
      <div>
        <label class="block text-sm font-medium">Username</label>
        <input type="text" name="username" required placeholder="NIP / NRPP"
          class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-400 focus:outline-none ">
      </div>

      <!-- Password -->
      <div>
        <label class="block text-sm font-medium">Password</label>
        <input type="password" name="password" required 
          class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-400 focus:outline-none">
      </div>

      <!-- Confirm Password -->
      <div>
        <label class="block text-sm font-medium">Re-type Password</label>
        <input type="password" name="password_confirmation" required 
          class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-400 focus:outline-none">
      </div>

      <!-- Email -->
      <div>
        <label class="block text-sm font-medium">Email</label>
        <input type="email" name="email" required 
          class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-400 focus:outline-none">
      </div>

      <!-- Full Name -->
      <div>
        <label class="block text-sm font-medium">Full Name</label>
        <input type="text" name="full_name" required 
          class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-400 focus:outline-none">
      </div>

      <!-- Divisi -->
<div class="mb-4">
  <label class="block text-sm font-medium text-gray-700 mb-2">
    Divisi
  </label>
  <div class="relative">
    <select name="ID_DIVISI" id="divisiSelect" required
      class="w-full appearance-none px-4 py-2 rounded-lg border border-gray-300 
             bg-white text-gray-800 shadow-sm focus:ring-2 focus:ring-blue-500 
             focus:border-blue-500 outline-none transition duration-200 ease-in-out">
      <option value="">Pilih Divisi</option>
    </select>
  </div>
</div>

<!-- Subdivisi -->
<div class="mb-4">
  <label class="block text-sm font-medium text-gray-700 mb-2">
    Subdivisi
  </label>
  <div class="relative">
    <select name="ID_SUBDIVISI" id="subdivisiSelect" required
      class="w-full appearance-none px-4 py-2 rounded-lg border border-gray-300 
             bg-white text-gray-800 shadow-sm focus:ring-2 focus:ring-blue-500 
             focus:border-blue-500 outline-none transition duration-200 ease-in-out">
      <option value="">Pilih Subdivisi</option>
    </select>
  </div>
</div>

      <!-- Submit -->
      <button type="submit" 
        class="w-full bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700 transition">
        Submit
      </button>
    </form>

    <!-- Link ke Login -->
    <p class="text-center text-sm mt-4">
      Sudah punya akun? 
      <a href="{{ url('/') }}" class="text-blue-600 hover:underline">Login</a>
    </p>
  </div>


  <script>
    const form = document.getElementById('registerForm');
    const alertBox = document.getElementById('alertBox');
    const divisiSelect = document.getElementById('divisiSelect');
    const subdivisiSelect = document.getElementById('subdivisiSelect');

    async function loadDivisi() {
try {
  const res = await fetch("/api/m_divisi");
  const data = await res.json();

  data.forEach(div => {
    const option = document.createElement("option");
    option.value = div.ID_DIVISI;   // pastikan nama kolom pk di tabel M_DIVISI
    option.textContent = div.NAMA_DIVISI;
    divisiSelect.appendChild(option);
  });
} catch (err) {
  console.error("Gagal load divisi:", err);
}
}
loadDivisi();

// Load subdivisi berdasarkan divisi
async function loadSubdivisi(idDivisi) {
  subdivisiSelect.innerHTML = '<option value="">Pilih Subdivisi</option>'; // reset

  if (!idDivisi) return;

  try {
    const res = await fetch(`/api/m_subdivisi/divisi/${idDivisi}`);
    const data = await res.json();

    data.forEach(sub => {
      const option = document.createElement("option");
      option.value = sub.ID_SUBDIVISI;  // pastikan sesuai PK tabel M_SUBDIVISI
      option.textContent = sub.NAMA_SUBDIVISI;
      subdivisiSelect.appendChild(option);
    });
  } catch (err) {
    console.error("Gagal load subdivisi:", err);
  }
}
// load ulang subdiv
divisiSelect.addEventListener("change", (e) => {
  loadSubdivisi(e.target.value);
});

    form.addEventListener('submit', async (e) => {
      e.preventDefault();

      // ambil data dari form
      const formData = new FormData(form);
      const data = Object.fromEntries(formData);

      // cek konfirmasi password
      if (data.password !== data.password_confirmation) {
        showAlert("Password dan konfirmasi tidak sama!", "bg-red-100 text-red-600");
        return;
      }

      try {
        const response = await fetch("/api/register", {
          method: "POST",
          headers: {
            "Accept": "application/json",
            "Content-Type": "application/json"
          },
          body: JSON.stringify(data)
        });

        const result = await response.json();

        if (!response.ok) {
          let msg = result.message || "Registrasi gagal!";
          if (result.errors) {
            msg += " " + Object.values(result.errors).join(" ");
          }
          showAlert(msg, "bg-red-100 text-red-600");
        } else {
          showAlert(result.message, "bg-green-100 text-green-600");

          // simpan token ke localStorage
          localStorage.setItem("token", result.token);

          // redirect ke login
          setTimeout(() => {
            window.location.href = "/";
          }, 1500);
        }
      } catch (error) {
        console.error(error);
        showAlert("Terjadi kesalahan pada server.", "bg-red-100 text-red-600");
      }
    });

    function showAlert(message, classes) {
      alertBox.textContent = message;
      alertBox.className = `p-3 mb-4 rounded-lg text-sm ${classes}`;
      alertBox.classList.remove("hidden");
    }
  </script>

</body>
</html>
