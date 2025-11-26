<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Registrasi Akun</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen flex items-center justify-center bg-cover bg-center bg-no-repeat bg-fixed"
      style="background-image: url('{{ asset('images/bglogin.png') }}');">

  <div class="bg-white shadow-lg rounded-2xl w-full max-w-md p-8 m-8">
    <!-- Logo -->
    <div class="flex justify-center items-center">
      <img src="{{ asset('images/iktinven.png') }}" alt="IKT Logo" class="h-48" />
    </div>

    <!-- Alert -->
    <div id="alertBox" class="hidden p-3 mb-4 rounded-lg text-sm"></div>

    <!-- Form -->
    <form id="registerForm" class="space-y-4">
      <!-- Username -->
      <div>
        <label class="block text-sm font-medium">Username</label>
        <input type="text" name="username" id="usernameField" required placeholder="NIPP/NRP"
          class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-400 focus:outline-none">
      </div>

      <!-- Password -->
      <div>
        <label class="block text-sm font-medium">Password</label>
        <input type="password" name="password" id="passwordField" required 
          class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-400 focus:outline-none">
      </div>

      <!-- Confirm Password -->
      <div>
        <label class="block text-sm font-medium">Re-type Password</label>
        <input type="password" name="password_confirmation" id="confirmPasswordField" required 
          class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-400 focus:outline-none">
      </div>

      <!-- Email -->
      <div>
        <label class="block text-sm font-medium">Email</label>
        <input type="email" name="email" id="emailField" required 
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
        <label class="block text-sm font-medium text-gray-700 mb-2">Divisi</label>
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
        <label class="block text-sm font-medium text-gray-700 mb-2">Subdivisi</label>
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
    
    // Field elements
    const usernameField = document.getElementById('usernameField');
    const passwordField = document.getElementById('passwordField');
    const confirmPasswordField = document.getElementById('confirmPasswordField');
    const emailField = document.getElementById('emailField');

    // Load Divisi
    async function loadDivisi() {
      try {
        const res = await fetch("/api/m_divisi");
        const data = await res.json();

        data.forEach(div => {
          const option = document.createElement("option");
          option.value = div.ID_DIVISI;
          option.textContent = div.NAMA_DIVISI;
          divisiSelect.appendChild(option);
        });
      } catch (err) {
        console.error("Gagal load divisi:", err);
      }
    }
    loadDivisi();

    // Load Subdivisi berdasarkan divisi
    async function loadSubdivisi(idDivisi) {
      subdivisiSelect.innerHTML = '<option value="">Pilih Subdivisi</option>';
      if (!idDivisi) return;

      try {
        const res = await fetch(`/api/m_subdivisi/divisi/${idDivisi}`);
        const data = await res.json();

        data.forEach(sub => {
          const option = document.createElement("option");
          option.value = sub.ID_SUBDIVISI;
          option.textContent = sub.NAMA_SUBDIVISI;
          subdivisiSelect.appendChild(option);
        });
      } catch (err) {
        console.error("Gagal load subdivisi:", err);
      }
    }

    divisiSelect.addEventListener("change", (e) => {
      loadSubdivisi(e.target.value);
    });

    // Show Alert Function
    function showAlert(message, classes) {
      alertBox.textContent = message;
      alertBox.className = `p-3 mb-4 rounded-lg text-sm ${classes}`;
      alertBox.classList.remove("hidden");
    }

    function hideAlert() {
      alertBox.classList.add("hidden");
    }

    // VALIDASI REAL-TIME USERNAME - Hanya Angka
    usernameField.addEventListener("input", function () {
      const value = usernameField.value;

      if (value && !/^[0-9]*$/.test(value)) {
        showAlert("Username hanya NRP/NIPP!", "bg-red-100 text-red-600");
        usernameField.value = value.replace(/[^0-9]/g, "");
      } else {
        hideAlert();
      }
    });

    // VALIDASI REAL-TIME PASSWORD - Minimal 6 karakter
    passwordField.addEventListener("input", function () {
      const value = passwordField.value;

      if (value && value.length < 6) {
        showAlert("Password minimal 6 karakter!", "bg-red-100 text-red-600");
      } else {
        hideAlert();
        // Cek ulang confirm password jika sudah diisi
        if (confirmPasswordField.value) {
          validateConfirmPassword();
        }
      }
    });

    // VALIDASI REAL-TIME CONFIRM PASSWORD - Harus sama
    function validateConfirmPassword() {
      const password = passwordField.value;
      const confirmPassword = confirmPasswordField.value;

      if (confirmPassword && password !== confirmPassword) {
        showAlert("Password tidak sama!", "bg-red-100 text-red-600");
        return false;
      } else {
        hideAlert();
        return true;
      }
    }

    confirmPasswordField.addEventListener("input", validateConfirmPassword);

    // Form Submit Handler
    form.addEventListener('submit', async (e) => {
      e.preventDefault();

      // Validasi final sebelum submit
      const username = usernameField.value;
      const password = passwordField.value;
      const confirmPassword = confirmPasswordField.value;
      const email = emailField.value;

      // Cek username hanya angka
      if (!/^[0-9]+$/.test(username)) {
        showAlert("Username hanya NRP/NIPP!", "bg-red-100 text-red-600");
        return;
      }

      // Cek password minimal 6 karakter
      if (password.length < 6) {
        showAlert("Password minimal 6 karakter!", "bg-red-100 text-red-600");
        return;
      }

      // Cek password sama
      if (password !== confirmPassword) {
        showAlert("Password tidak sama!", "bg-red-100 text-red-600");
        return;
      }

      // Cek email ada @
      if (!email.includes("@")) {
        showAlert("Email harus mengandung @!", "bg-red-100 text-red-600");
        return;
      }

      // Ambil data dari form
      const formData = new FormData(form);
      const data = Object.fromEntries(formData);

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
          localStorage.setItem("token", result.token);
          
          setTimeout(() => {
            window.location.href = "/";
          }, 1500);
        }
      } catch (error) {
        console.error(error);
        showAlert("Terjadi kesalahan pada server.", "bg-red-100 text-red-600");
      }
    });
  </script>

</body>
</html