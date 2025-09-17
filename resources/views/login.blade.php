<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Login - IKT Inventory</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen flex items-center justify-center bg-cover bg-center" 
      style="background-image: url('/storage/bglogin.png');">
  <div class="bg-white rounded-xl shadow-lg w-full max-w-md pb-8 px-8 pt-1">
  <div class="flex justify-between items-center ">
      <!-- <img src="/storage/logopel.png" alt="PELINDO Logo" class="h-10 items-start" />
      <img src="/storage/bumn.png" alt="BUMN Logo" class="h-24 items-end" /> -->
    </div>
    <div class="flex justify-center items-center">
      <img src="/storage/iktinven.png" alt="IKT Logo" class="h-48 " />
    </div>
    <form id="loginForm" class="space-y-4">
      <div>
        <label class="block text-sm font-medium mb-1">Username</label>
        <div class="relative">
          <input id="username" type="text" required
                 class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" />
        </div>
      </div>

      <div>
        <label class="block text-sm font-medium mb-1">Password</label>
        <div class="relative">
          <input id="password" type="password" required
                 class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" />
        </div>
      </div>

      <div class="flex items-center">
        <input id="remember" type="checkbox" class="mr-2" />
        <label for="remember" class="text-sm">Ingat saya</label>
      </div>

      <div class="flex justify-between">
        <button type="submit"
                class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-2 rounded-lg">LOGIN</button>

        <a href="{{ url('/register') }}" class="bg-red-500 hover:bg-red-600 text-white px-6 py-2 rounded-lg">REGISTRASI</a>
      </div>
    </form>
  </div>

  <script>
    document.getElementById("loginForm").addEventListener("submit", async function (e) {
        e.preventDefault();

        const username = document.getElementById("username").value;
        const password = document.getElementById("password").value;

        try {
            const response = await fetch("/api/login", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "Accept": "application/json"
                },
                body: JSON.stringify({ username, password })
            });

            const data = await response.json();

            if (!response.ok) {
                throw new Error(data.message || "Login gagal");
            }

            // simpan token di localStorage
            localStorage.setItem("auth_token", data.token);
            localStorage.setItem("auth_user", JSON.stringify(data.user));

            alert("Login berhasil!");
            window.location.href = "/home"; // 
        } catch (err) {
            alert("Error: " + err.message);
        }
    });
  </script>
</body>
</html>
