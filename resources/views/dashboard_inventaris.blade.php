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
        <!-- Header with Filter -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6 gap-4">
            <h1 class="text-2xl md:text-3xl font-bold text-gray-800">Dashboard Inventaris</h1>

            <!-- Filter Dropdown -->
            <div class="flex items-center gap-3">
                <label for="filterPerangkat" class="text-sm font-medium text-gray-600">Filter Perangkat:</label>
                <select id="filterPerangkat"
                    class="px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white min-w-[200px]">
                    <option value="">Semua Perangkat</option>
                </select>
            </div>
        </div>

        <!-- Summary Cards Row 1 -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
            <!-- Total Inventaris -->
            <div class="bg-white rounded-lg shadow-md p-5 border-l-4 border-blue-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-gray-500 uppercase">Total Inventaris</p>
                        <p id="totalInventaris" class="text-2xl font-bold text-gray-800">-</p>
                    </div>
                    <div class="bg-blue-100 p-2 rounded-full">
                        <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Butuh Pembaharuan -->
            <div class="bg-white rounded-lg shadow-md p-5 border-l-4 border-orange-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-gray-500 uppercase">Butuh Pembaharuan</p>
                        <p id="butuhPembaharuan" class="text-2xl font-bold text-gray-800">-</p>
                        <p class="text-xs text-gray-400">Lebih dari 5 tahun</p>
                    </div>
                    <div class="bg-orange-100 p-2 rounded-full">
                        <svg class="w-6 h-6 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Kondisi Baik -->
            <div class="bg-white rounded-lg shadow-md p-5 border-l-4 border-green-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-gray-500 uppercase">Kondisi Baik</p>
                        <p id="kondisiBaik" class="text-2xl font-bold text-gray-800">-</p>
                    </div>
                    <div class="bg-green-100 p-2 rounded-full">
                        <svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Kondisi Perlu Perhatian -->
            <div class="bg-white rounded-lg shadow-md p-5 border-l-4 border-red-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-gray-500 uppercase">Perlu Perhatian</p>
                        <p id="kondisiPerluPerhatian" class="text-2xl font-bold text-gray-800">-</p>
                    </div>
                    <div class="bg-red-100 p-2 rounded-full">
                        <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>

        </div>

        <!-- Charts Grid Row 1 -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
            <!-- Chart: Inventaris per Jenis Perangkat -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Inventaris per Jenis Perangkat</h2>
                <div class="relative h-72">
                    <canvas id="chartPerangkat"></canvas>
                </div>
            </div>

            <!-- Chart: Kondisi Perangkat -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Kondisi Perangkat</h2>
                <div class="relative h-72">
                    <canvas id="chartKondisi"></canvas>
                </div>
            </div>
        </div>

        <!-- Charts Grid Row 2 -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
            <!-- Chart: Distribusi Anggaran -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Distribusi Anggaran</h2>
                <div class="relative h-72">
                    <canvas id="chartAnggaran"></canvas>
                </div>
            </div>

            <!-- Chart: Inventaris per Merek -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Inventaris per Merek</h2>
                <div class="relative h-72">
                    <canvas id="chartMerk"></canvas>
                </div>
            </div>
        </div>

        <!-- Charts Grid Row 3 -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
            <!-- Chart: Inventaris per Tahun Pengadaan -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Inventaris per Tahun Pengadaan</h2>
                <div class="relative h-72">
                    <canvas id="chartTahun"></canvas>
                </div>
            </div>

            <!-- Chart: Inventaris per Terminal -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Inventaris per Terminal</h2>
                <div class="relative h-72">
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
        let chartPerangkat, chartKondisi, chartAnggaran, chartMerk, chartTahun, chartTerminal;

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

        // Device type colors (consistent)
        const perangkatColors = {
            'PC/Laptop': 'rgba(59, 130, 246, 0.8)',      // blue
            'Printer & Scan': 'rgba(245, 158, 11, 0.8)', // amber
            'CCTV': 'rgba(139, 92, 246, 0.8)',           // purple
            'Handheld (HENHEL)': 'rgba(16, 185, 129, 0.8)', // green
            'AP (Access Point)': 'rgba(236, 72, 153, 0.8)'  // pink
        };

        const borderColors = colors.map(c => c.replace('0.8', '1'));

        // Current filter
        let currentPerangkatId = '';

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

        // Load perangkat options for filter
        async function loadPerangkatOptions() {
            try {
                const data = await fetchWithAuth('/api/m_perangkat/all');
                const select = document.getElementById('filterPerangkat');

                data.forEach(perangkat => {
                    const option = document.createElement('option');
                    option.value = perangkat.ID_PERANGKAT;
                    option.textContent = perangkat.NAMA_PERANGKAT;
                    select.appendChild(option);
                });
            } catch (error) {
                console.error('Error loading perangkat options:', error);
            }
        }

        // Load dashboard data
        async function loadDashboard() {
            try {
                document.getElementById('loadingOverlay').classList.remove('hidden');

                let url = '/api/dashboard-inventaris/statistics';
                if (currentPerangkatId) {
                    url += `?perangkat_id=${currentPerangkatId}`;
                }

                const data = await fetchWithAuth(url);

                // Update summary cards
                document.getElementById('totalInventaris').textContent = data.total_inventaris.toLocaleString('id-ID');
                document.getElementById('butuhPembaharuan').textContent = data.butuh_pembaharuan.toLocaleString('id-ID');
                document.getElementById('kondisiBaik').textContent = data.kondisi_baik.toLocaleString('id-ID');
                document.getElementById('kondisiPerluPerhatian').textContent = data.kondisi_perlu_perhatian.toLocaleString('id-ID');

                // Render charts
                renderChartPerangkat(data.per_perangkat);
                renderChartKondisi(data.per_kondisi);
                renderChartAnggaran(data.per_anggaran);
                renderChartMerk(data.per_merk);
                renderChartTahun(data.per_tahun, data.threshold_year);
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

        // Chart: Inventaris per Jenis Perangkat (Doughnut)
        function renderChartPerangkat(data) {
            const ctx = document.getElementById('chartPerangkat').getContext('2d');

            if (chartPerangkat) chartPerangkat.destroy();

            const chartColors = data.map(item => perangkatColors[item.nama] || colors[0]);

            chartPerangkat = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: data.map(item => item.nama),
                    datasets: [{
                        data: data.map(item => item.total),
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

        // Chart: Kondisi Perangkat (Bar)
        function renderChartKondisi(data) {
            const ctx = document.getElementById('chartKondisi').getContext('2d');

            if (chartKondisi) chartKondisi.destroy();

            // Color based on condition name
            const kondisiColors = data.map(item => {
                const nama = item.nama.toLowerCase();
                if (nama.includes('baik') || nama.includes('good') || nama.includes('normal')) {
                    return 'rgba(16, 185, 129, 0.8)'; // green
                } else if (nama.includes('rusak berat') || nama.includes('broken')) {
                    return 'rgba(239, 68, 68, 0.8)'; // red
                } else {
                    return 'rgba(245, 158, 11, 0.8)'; // amber for others
                }
            });

            chartKondisi = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: data.map(item => item.nama),
                    datasets: [{
                        label: 'Jumlah',
                        data: data.map(item => item.total),
                        backgroundColor: kondisiColors,
                        borderColor: kondisiColors.map(c => c.replace('0.8', '1')),
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
                        }
                    }
                }
            });
        }

        // Chart: Distribusi Anggaran (Pie)
        function renderChartAnggaran(data) {
            const ctx = document.getElementById('chartAnggaran').getContext('2d');

            if (chartAnggaran) chartAnggaran.destroy();

            chartAnggaran = new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: data.map(item => item.nama),
                    datasets: [{
                        data: data.map(item => item.total),
                        backgroundColor: colors.slice(0, data.length),
                        borderColor: borderColors.slice(0, data.length),
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
            if (butuhUpdate > 0) {
                labels.push('Butuh Pembaharuan (>' + (new Date().getFullYear() - 5) + ')');
            }

            const values = labels.map((label, idx) => {
                if (idx === labels.length - 1 && butuhUpdate > 0 && label.includes('Butuh')) return butuhUpdate;
                return masihBaik[label] || 0;
            });

            const chartColors = labels.map((label, idx) => {
                if (label.includes('Butuh')) return 'rgba(239, 68, 68, 0.8)'; // red for butuh update
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

        // Handle filter change
        document.getElementById('filterPerangkat').addEventListener('change', function() {
            currentPerangkatId = this.value;
            loadDashboard();
        });

        // Initialize
        document.addEventListener('DOMContentLoaded', async function() {
            await loadPerangkatOptions();
            await loadDashboard();
        });
    </script>
</body>
</html>
