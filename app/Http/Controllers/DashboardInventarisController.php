<?php

namespace App\Http\Controllers;

use App\Models\T_inventaris;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardInventarisController extends Controller
{
    /**
     * Display the dashboard view
     */
    public function index()
    {
        return view('dashboard_inventaris');
    }

    /**
     * Get all statistics for the dashboard
     */
    public function getStatistics()
    {
        $currentYear = date('Y');
        $thresholdYear = $currentYear - 5;

        // Total inventaris aktif
        $totalInventaris = T_inventaris::where('STATUS', 1)->count();

        // Inventaris per Merek
        $perMerk = T_inventaris::select('M_MERK.NAMA_MERK', DB::raw('COUNT(*) as total'))
            ->join('M_MERK', 'T_INVENTARIS.ID_MERK', '=', 'M_MERK.ID_MERK')
            ->where('T_INVENTARIS.STATUS', 1)
            ->groupBy('T_INVENTARIS.ID_MERK', 'M_MERK.NAMA_MERK')
            ->orderBy('total', 'desc')
            ->get();

        // Inventaris per Tahun Pengadaan
        $perTahun = T_inventaris::select('TAHUN_PENGADAAN', DB::raw('COUNT(*) as total'))
            ->where('STATUS', 1)
            ->whereNotNull('TAHUN_PENGADAAN')
            ->where('TAHUN_PENGADAAN', '!=', '')
            ->groupBy('TAHUN_PENGADAAN')
            ->orderBy('TAHUN_PENGADAAN', 'desc')
            ->get();

        // Hitung butuh pembaharuan (> 5 tahun)
        $butuhPembaharuan = T_inventaris::where('STATUS', 1)
            ->whereNotNull('TAHUN_PENGADAAN')
            ->where('TAHUN_PENGADAAN', '!=', '')
            ->where('TAHUN_PENGADAAN', '<=', $thresholdYear)
            ->count();

        // Inventaris per Status Antivirus
        $perAntivirus = T_inventaris::select('M_INSTAL.NAMA_INSTAL', DB::raw('COUNT(*) as total'))
            ->join('M_INSTAL', 'T_INVENTARIS.ID_INSTAL', '=', 'M_INSTAL.ID_INSTAL')
            ->where('T_INVENTARIS.STATUS', 1)
            ->groupBy('T_INVENTARIS.ID_INSTAL', 'M_INSTAL.NAMA_INSTAL')
            ->get();

        // Inventaris per Terminal
        $perTerminal = T_inventaris::select('M_TERMINAL.NAMA_TERMINAL', DB::raw('COUNT(*) as total'))
            ->join('M_TERMINAL', 'T_INVENTARIS.ID_TERMINAL', '=', 'M_TERMINAL.ID_TERMINAL')
            ->where('T_INVENTARIS.STATUS', 1)
            ->groupBy('T_INVENTARIS.ID_TERMINAL', 'M_TERMINAL.NAMA_TERMINAL')
            ->orderBy('total', 'desc')
            ->get();

        // Hitung statistik antivirus untuk summary cards
        $sudahInstallAV = 0;
        $belumInstallAV = 0;
        foreach ($perAntivirus as $av) {
            $namaLower = strtolower($av->NAMA_INSTAL);
            if (str_contains($namaLower, 'sudah') || str_contains($namaLower, 'yes') || str_contains($namaLower, 'installed')) {
                $sudahInstallAV += $av->total;
            } else {
                $belumInstallAV += $av->total;
            }
        }

        return response()->json([
            'total_inventaris' => $totalInventaris,
            'butuh_pembaharuan' => $butuhPembaharuan,
            'sudah_install_av' => $sudahInstallAV,
            'belum_install_av' => $belumInstallAV,
            'per_merk' => $perMerk,
            'per_tahun' => $perTahun,
            'per_antivirus' => $perAntivirus,
            'per_terminal' => $perTerminal,
            'threshold_year' => $thresholdYear
        ]);
    }
}
