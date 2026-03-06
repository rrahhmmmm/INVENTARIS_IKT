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
    public function getStatistics(Request $request)
    {
        $currentYear = date('Y');
        $thresholdYear = $currentYear - 5;
        $perangkatId = $request->input('perangkat_id');
        $terminalId = $request->input('terminal_id');

        // Base query builder with optional perangkat and terminal filter
        $baseQuery = function() use ($perangkatId, $terminalId) {
            $query = T_inventaris::where('STATUS', 1);
            if ($perangkatId) {
                $query->where('ID_PERANGKAT', $perangkatId);
            }
            if ($terminalId) {
                $query->where('ID_TERMINAL', $terminalId);
            }
            return $query;
        };

        // Total inventaris aktif
        $totalInventaris = $baseQuery()->count();

        // Inventaris per Merek
        $perMerkQuery = T_inventaris::select('M_MERK.NAMA_MERK', DB::raw('COUNT(*) as total'))
            ->join('M_MERK', 'T_INVENTARIS.ID_MERK', '=', 'M_MERK.ID_MERK')
            ->where('T_INVENTARIS.STATUS', 1);
        if ($perangkatId) {
            $perMerkQuery->where('T_INVENTARIS.ID_PERANGKAT', $perangkatId);
        }
        if ($terminalId) {
            $perMerkQuery->where('T_INVENTARIS.ID_TERMINAL', $terminalId);
        }
        $perMerk = $perMerkQuery->groupBy('T_INVENTARIS.ID_MERK', 'M_MERK.NAMA_MERK')
            ->orderBy('total', 'asc')
            ->get();

        // Inventaris per Tahun Pengadaan
        $perTahunQuery = T_inventaris::select('TAHUN_PENGADAAN', DB::raw('COUNT(*) as total'))
            ->where('STATUS', 1)
            ->whereNotNull('TAHUN_PENGADAAN')
            ->where('TAHUN_PENGADAAN', '!=', '');
        if ($perangkatId) {
            $perTahunQuery->where('ID_PERANGKAT', $perangkatId);
        }
        if ($terminalId) {
            $perTahunQuery->where('ID_TERMINAL', $terminalId);
        }
        $perTahun = $perTahunQuery->groupBy('TAHUN_PENGADAAN')
            ->orderBy('TAHUN_PENGADAAN', 'asc')
            ->get();

        // Hitung butuh pembaharuan (> 5 tahun)
        $butuhPembaharuanQuery = T_inventaris::where('STATUS', 1)
            ->whereNotNull('TAHUN_PENGADAAN')
            ->where('TAHUN_PENGADAAN', '!=', '')
            ->where('TAHUN_PENGADAAN', '<=', $thresholdYear);
        if ($perangkatId) {
            $butuhPembaharuanQuery->where('ID_PERANGKAT', $perangkatId);
        }
        if ($terminalId) {
            $butuhPembaharuanQuery->where('ID_TERMINAL', $terminalId);
        }
        $butuhPembaharuan = $butuhPembaharuanQuery->count();

        // Inventaris per Terminal
        $perTerminalQuery = T_inventaris::select('M_TERMINAL.NAMA_TERMINAL', DB::raw('COUNT(*) as total'))
            ->join('M_TERMINAL', 'T_INVENTARIS.ID_TERMINAL', '=', 'M_TERMINAL.ID_TERMINAL')
            ->where('T_INVENTARIS.STATUS', 1);
        if ($perangkatId) {
            $perTerminalQuery->where('T_INVENTARIS.ID_PERANGKAT', $perangkatId);
        }
        if ($terminalId) {
            $perTerminalQuery->where('T_INVENTARIS.ID_TERMINAL', $terminalId);
        }
        $perTerminal = $perTerminalQuery->groupBy('T_INVENTARIS.ID_TERMINAL', 'M_TERMINAL.NAMA_TERMINAL')
            ->orderBy('total', 'asc')
            ->get();

        // Inventaris per Jenis Perangkat
        $perPerangkat = T_inventaris::select('M_PERANGKAT.NAMA_PERANGKAT as nama', DB::raw('COUNT(*) as total'))
            ->join('M_PERANGKAT', 'T_INVENTARIS.ID_PERANGKAT', '=', 'M_PERANGKAT.ID_PERANGKAT')
            ->where('T_INVENTARIS.STATUS', 1)
            ->groupBy('T_INVENTARIS.ID_PERANGKAT', 'M_PERANGKAT.NAMA_PERANGKAT')
            ->orderBy('total', 'asc')
            ->get();

        // Inventaris per Kondisi
        $perKondisiQuery = T_inventaris::select('M_KONDISI.NAMA_KONDISI as nama', DB::raw('COUNT(*) as total'))
            ->join('M_KONDISI', 'T_INVENTARIS.ID_KONDISI', '=', 'M_KONDISI.ID_KONDISI')
            ->where('T_INVENTARIS.STATUS', 1);
        if ($perangkatId) {
            $perKondisiQuery->where('T_INVENTARIS.ID_PERANGKAT', $perangkatId);
        }
        if ($terminalId) {
            $perKondisiQuery->where('T_INVENTARIS.ID_TERMINAL', $terminalId);
        }
        $perKondisi = $perKondisiQuery->groupBy('T_INVENTARIS.ID_KONDISI', 'M_KONDISI.NAMA_KONDISI')
            ->orderBy('total', 'asc')
            ->get();

        // Inventaris per Anggaran
        $perAnggaranQuery = T_inventaris::select('M_ANGGARAN.NAMA_ANGGARAN as nama', DB::raw('COUNT(*) as total'))
            ->join('M_ANGGARAN', 'T_INVENTARIS.ID_ANGGARAN', '=', 'M_ANGGARAN.ID_ANGGARAN')
            ->where('T_INVENTARIS.STATUS', 1);
        if ($perangkatId) {
            $perAnggaranQuery->where('T_INVENTARIS.ID_PERANGKAT', $perangkatId);
        }
        if ($terminalId) {
            $perAnggaranQuery->where('T_INVENTARIS.ID_TERMINAL', $terminalId);
        }
        $perAnggaran = $perAnggaranQuery->groupBy('T_INVENTARIS.ID_ANGGARAN', 'M_ANGGARAN.NAMA_ANGGARAN')
            ->orderBy('total', 'asc')
            ->get();

        // Hitung kondisi baik vs perlu perhatian
        $kondisiBaik = 0;
        $kondisiPerluPerhatian = 0;
        foreach ($perKondisi as $kondisi) {
            $namaLower = strtolower($kondisi->nama);
            if (str_contains($namaLower, 'baik') || str_contains($namaLower, 'good') || str_contains($namaLower, 'normal')) {
                $kondisiBaik += $kondisi->total;
            } else {
                $kondisiPerluPerhatian += $kondisi->total;
            }
        }

        return response()->json([
            'total_inventaris' => $totalInventaris,
            'butuh_pembaharuan' => $butuhPembaharuan,
            'per_merk' => $perMerk,
            'per_tahun' => $perTahun,
            'per_terminal' => $perTerminal,
            'threshold_year' => $thresholdYear,
            'per_perangkat' => $perPerangkat,
            'per_kondisi' => $perKondisi,
            'per_anggaran' => $perAnggaran,
            'kondisi_baik' => $kondisiBaik,
            'kondisi_perlu_perhatian' => $kondisiPerluPerhatian,
        ]);
    }
}
