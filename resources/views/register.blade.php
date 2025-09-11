<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PELINDO - Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .bg-pelindo-blue {
            background: linear-gradient(135deg, #4A90E2 0%, #357ABD 100%);
        }
        .bg-terminal {
    background-image: url('/storage/bgimg.svg');
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    }

    </style>
</head>

    <body class="min-h-screen flex flex-col">
    <!-- Header -->
    <header class="bg-white">
    <div class="container flex flex-col justify-start">
        <div>
            <img src="/storage/logopel.png" alt="PELINDO Logo" class="h-20 w-25">
        </div>
    </div>

    <!-- Garis biru full screen -->
    <div class="bg-[#1698E1] w-full h-10 "></div>
    </header>



    <!-- Main Content -->
    <main class="flex-1 bg-terminal relative">
        <div class="absolute inset-0 bg-black bg-opacity-20"></div>
        <div class="relative z-10 min-h-full flex items-center justify-center p-4">
            <!-- Login Form -->
            <div class="bg-white bg-opacity-65 backdrop-blur-sm rounded-lg shadow-xl p-8 w-full max-w-md">
                <form class="space-y-6">
                    <!-- Username Field -->
                    <div>
                        <label for="username" class="block text-sm font-medium text-gray-700 ">
                            Nama Lengkap
                        </label>
                        <input 
                            type="text" 
                            id="username" 
                            name="username" 
                            required
                            class="w-full px-4 py-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        >
                    </div>

                    <!-- Password Field -->
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 ">
                            Username
                        </label>
                        <input 
                            type="password" 
                            id="password" 
                            name="password" 
                            required
                            class="w-full px-4 py-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        >
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 ">
                            Password
                        </label>
                        <input 
                            type="password" 
                            id="password" 
                            name="password" 
                            required
                            class="w-full px-4 py-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        >
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 ">
                            Divisi
                        </label>
                        <input 
                            type="password" 
                            id="password" 
                            name="password" 
                            required
                            class="w-full px-4 py-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        >
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700">
                            SubDivisi
                        </label>
                        <input 
                            type="password" 
                            id="password" 
                            name="password" 
                            required
                            class="w-full px-4 py-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        >
                    </div>

                    <!-- Login Button -->
                    <button 
                        type="submit" 
                        class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-4 rounded-md transition duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                    >
                        Register
                    </button>

                    <!-- Register Link -->
                    <div class="text-center">
                        <span class="text-gray-600"> Sudah Punya Account? </span>
                        <a href="{{ url('/login') }}" class="text-blue-600 hover:text-blue-800 font-medium">Login</a>
                    </div>
                </form>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-pelindo-blue text-white p-6">
        <div class="container mx-auto">
            <div class="grid md:grid-cols-2 gap-6">
                <!-- Company Info -->
                <div>
                    <h3 class="font-semibold mb-2">PT Indonesia Kendaraan Terminal Tbk</h3>
                    <address class="text-sm opacity-90 not-italic leading-relaxed">
                        Jl. Sindang Laut No.100, RW.11, Kali<br>
                        Baru, Kec. Cilincing, Kota Jkt Utara,<br>
                        Daerah Khusus Ibukota Jakarta 14110,<br>
                        Indonesia
                    </address>
                </div>

                <!-- Social Media -->
                <div>
                    <h3 class="font-semibold mb-3">Media Sosial</h3>
                    <div class="space-y-2">
                        <div class="flex items-center space-x-3">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
                            </svg>
                            <span class="text-sm">@IPCC Terminal Kendaraan</span>
                        </div>
                        <div class="flex items-center space-x-3">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/>
                            </svg>
                            <span class="text-sm">@IPCC Terminal Kendaraan</span>
                        </div>
                        <div class="flex items-center space-x-3">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12.017 0C5.396 0 .029 5.367.029 11.987c0 5.079 3.158 9.417 7.618 11.174-.105-.949-.199-2.403.041-3.439.219-.937 1.406-5.957 1.406-5.957s-.359-.72-.359-1.781c0-1.663.967-2.911 2.168-2.911 1.024 0 1.518.769 1.518 1.688 0 1.029-.653 2.567-.992 3.992-.285 1.193.6 2.165 1.775 2.165 2.128 0 3.768-2.245 3.768-5.487 0-2.861-2.063-4.869-5.008-4.869-3.41 0-5.409 2.562-5.409 5.199 0 1.033.394 2.143.889 2.741.097.118.112.221.085.345-.09.375-.293 1.199-.334 1.363-.053.225-.172.271-.402.165-1.495-.69-2.433-2.878-2.433-4.646 0-3.776 2.748-7.252 7.92-7.252 4.158 0 7.392 2.967 7.392 6.923 0 4.135-2.607 7.462-6.233 7.462-1.214 0-2.357-.629-2.748-1.378l-.748 2.853c-.271 1.043-1.002 2.35-1.492 3.146C9.57 23.812 10.763 24.009 12.017 24.009c6.624 0 11.99-5.367 11.99-11.988C24.007 5.367 18.641.001.012.001z"/>
                            </svg>
                            <span class="text-sm">@IPCC Terminal Kendaraan</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </footer>
</body>
</html>
