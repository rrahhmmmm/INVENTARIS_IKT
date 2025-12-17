<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Inventaris</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-gray-100 min-h-screen">
    @include('components.TI_navbar')

    <header class="bg-white shadow-lg h-16 md:h-20 w-full"></header>

    <main class="container mx-auto px-4 py-6">
        <h1 class="text-2xl md:text-3xl font-bold text-gray-800 mb-6">Dashboard Inventaris</h1>

        <!-- Summary Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
            <!-- Total Inventaris -->
            <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-blue-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 uppercase">Total Inventaris</p>
                        <p id="totalInventaris" class="text-3xl font-bold text-gray-800">-</p>
                    </div>
                    <div class="bg-blue-100 p-3 rounded-full">
                        <svg class="w-8 h-8 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Butuh Pembaharuan -->
            <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-orange-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 uppercase">Butuh Pembaharuan</p>
                        <p id="butuhPembaharuan" class="text-3xl font-bold text-gray-800">-</p>
                        <p class="text-xs text-gray-400">Lebih dari 5 tahun</p>
                    </div>
                    <div class="bg-orange-100 p-3 rounded-full">
                        <svg class="w-8 h-8 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Sudah Install AV -->
            <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-green-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 uppercase">Sudah Install AV</p>
                        <p id="sudahInstallAV" class="text-3xl font-bold text-gray-800">-</p>
                    </div>
                    <div class="bg-green-100 p-3 rounded-full">
                        <svg class="w-8 h-8 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Belum Install AV -->
            <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-red-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 uppercase">Belum Install AV</p>
                        <p id="belumInstallAV" class="text-3xl font-bold text-gray-800">-</p>
                    </div>
                    <div class="bg-red-100 p-3 rounded-full">
                        <svg class="w-8 h-8 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Chart: Inventaris per Merek -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Inventaris per Merek</h2>
                <div class="relative h-80">
                    <canvas id="chartMerk"></canvas>
                </div>
            </div>

            <!-- Chart: Inventaris per Tahun Pengadaan -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Inventaris per Tahun Pengadaan</h2>
                <div class="relative h-80">
                    <canvas id="chartTahun"></canvas>
                </div>
            </div>

            <!-- Chart: Status Antivirus -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Status Antivirus</h2>
                <div class="relative h-80">
                    <canvas id="chartAntivirus"></canvas>
                </div>
            </div>

            <!-- Chart: Inventaris per Terminal -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Inventaris per Terminal</h2>
                <div class="relative h-80">
                    <canvas id="chartTerminal"></canvas>
                </div>
            </div>
        </div>

        <!-- Loading Overlay -->
        <div id="loadingOverlay" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white rounded-lg p-6 flex items-center space-x-4">
                <svg class="animate-spin h-8 w-8 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span class="text-gray-700 font-medium">Memuat data...</span>
            </div>
        </div>
    </main>

    <script>
        // Chart instances
        let chartMerk, chartTahun, chartAntivirus, chartTerminal;

        // Color palette
        const colors = [
            'rgba(59, 130, 246, 0.8)',   // blue
            'rgba(16, 185, 129, 0.8)',   // green
            'rgba(245, 158, 11, 0.8)',   // amber
            'rgba(239, 68, 68, 0.8)',    // red
            'rgba(139, 92, 246, 0.8)',   // purple
            'rgba(236, 72, 153, 0.8)',   // pink
            'rgba(20, 184, 166, 0.8)',   // teal
            'rgba(249, 115, 22, 0.8)',   // orange
            'rgba(34, 197, 94, 0.8)',    // green-500
            'rgba(168, 85, 247, 0.8)',   // purple-500
        ];

        const borderColors = colors.map(c => c.replace('0.8', '1'));

        // Fetch with auth
        async function fetchWithAuth(url) {
            const token = localStorage.getItem('auth_token');
            const response = await fetch(url, {
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json'
                }
            });
            if (!response.ok) {
                throw new Error('Failed to fetch data');
            }
            return response.json();
        }

        // Load dashboard data
        async function loadDashboard() {
            try {
                const data = await fetchWithAuth('/api/dashboard-inventaris/statistics');

                // Update summary cards
                document.getElementById('totalInventaris').textContent = data.total_inventaris.toLocaleString('id-ID');
                document.getElementById('butuhPembaharuan').textContent = data.butuh_pembaharuan.toLocaleString('id-ID');
                document.getElementById('sudahInstallAV').textContent = data.sudah_install_av.toLocaleString('id-ID');
                document.getElementById('belumInstallAV').textContent = data.belum_install_av.toLocaleString('id-ID');

                // Render charts
                renderChartMerk(data.per_merk);
                renderChartTahun(data.per_tahun, data.threshold_year);
                renderChartAntivirus(data.per_antivirus);
                renderChartTerminal(data.per_terminal);

                // Hide loading
                document.getElementById('loadingOverlay').classList.add('hidden');
            } catch (error) {
                console.error('Error loading dashboard:', error);
                document.getElementById('loadingOverlay').innerHTML = `
                    <div class="bg-white rounded-lg p-6 text-center">
                        <p class="text-red-500 font-medium mb-2">Gagal memuat data</p>
                        <button onclick="location.reload()" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">Coba Lagi</button>
                    </div>
                `;
            }
        }

        // Chart: Inventaris per Merek (Bar Chart)
        function renderChartMerk(data) {
            const ctx = document.getElementById('chartMerk').getContext('2d');

            if (chartMerk) chartMerk.destroy();

            chartMerk = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: data.map(item => item.NAMA_MERK || 'Tidak Diketahui'),
                    datasets: [{
                        label: 'Jumlah',
                        data: data.map(item => item.total),
                        backgroundColor: colors.slice(0, data.length),
                        borderColor: borderColors.slice(0, data.length),
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: { precision: 0 }
                        },
                        x: {
                            ticks: {
                                maxRotation: 45,
                                minRotation: 45
                            }
                        }
                    }
                }
            });
        }

        // Chart: Inventaris per Tahun Pengadaan (Doughnut)
        function renderChartTahun(data, thresholdYear) {
            const ctx = document.getElementById('chartTahun').getContext('2d');

            if (chartTahun) chartTahun.destroy();

            // Group data: butuh pembaharuan vs masih baik
            let butuhUpdate = 0;
            let masihBaik = {};

            data.forEach(item => {
                const tahun = parseInt(item.TAHUN_PENGADAAN);
                if (tahun <= thresholdYear) {
                    butuhUpdate += item.total;
                } else {
                    masihBaik[item.TAHUN_PENGADAAN] = item.total;
                }
            });

            const labels = Object.keys(masihBaik).sort().reverse();
            labels.push('Butuh Pembaharuan (>' + (new Date().getFullYear() - 5) + ')');

            const values = labels.map((label, idx) => {
                if (idx === labels.length - 1) return butuhUpdate;
                return masihBaik[label];
            });

            const chartColors = labels.map((label, idx) => {
                if (idx === labels.length - 1) return 'rgba(239, 68, 68, 0.8)'; // red for butuh update
                return colors[idx % colors.length];
            });

            chartTahun = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: labels,
                    datasets: [{
                        data: values,
                        backgroundColor: chartColors,
                        borderColor: chartColors.map(c => c.replace('0.8', '1')),
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'right',
                            labels: {
                                boxWidth: 15,
                                padding: 10
                            }
                        }
                    }
                }
            });
        }

        // Chart: Status Antivirus (Pie)
        function renderChartAntivirus(data) {
            const ctx = document.getElementById('chartAntivirus').getContext('2d');

            if (chartAntivirus) chartAntivirus.destroy();

            const avColors = data.map(item => {
                const nama = item.NAMA_INSTAL.toLowerCase();
                if (nama.includes('sudah') || nama.includes('yes') || nama.includes('installed')) {
                    return 'rgba(16, 185, 129, 0.8)'; // green
                }
                return 'rgba(239, 68, 68, 0.8)'; // red
            });

            chartAntivirus = new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: data.map(item => item.NAMA_INSTAL),
                    datasets: [{
                        data: data.map(item => item.total),
                        backgroundColor: avColors,
                        borderColor: avColors.map(c => c.replace('0.8', '1')),
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'right',
                            labels: {
                                boxWidth: 15,
                                padding: 10
                            }
                        }
                    }
                }
            });
        }

        // Chart: Inventaris per Terminal (Horizontal Bar)
        function renderChartTerminal(data) {
            const ctx = document.getElementById('chartTerminal').getContext('2d');

            if (chartTerminal) chartTerminal.destroy();

            chartTerminal = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: data.map(item => item.NAMA_TERMINAL),
                    datasets: [{
                        label: 'Jumlah',
                        data: data.map(item => item.total),
                        backgroundColor: 'rgba(59, 130, 246, 0.8)',
                        borderColor: 'rgba(59, 130, 246, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false }
                    },
                    scales: {
                        x: {
                            beginAtZero: true,
                            ticks: { precision: 0 }
                        }
                    }
                }
            });
        }

        // Initialize
        document.addEventListener('DOMContentLoaded', loadDashboard);
    </script>
</body>
</html>
