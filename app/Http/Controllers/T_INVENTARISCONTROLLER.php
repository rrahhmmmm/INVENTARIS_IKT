<?php

namespace App\Http\Controllers;

use App\Models\T_inventaris;
use Illuminate\Http\Request;
use App\Exports\InventarisExport;
use App\Exports\InventarisExportTemplate;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\InventarisImport;
use Illuminate\Support\Facades\Auth;

class T_INVENTARISCONTROLLER extends Controller
{
    /**
     * Display a listing with pagination and filters
     */
    public function index(Request $request)
    {
        $query = T_inventaris::with(['terminal', 'merk', 'kondisi', 'instal', 'anggaran']);

        // Filter by terminal
        if ($request->filled('terminal_id')) {
            $query->where('ID_TERMINAL', $request->terminal_id);
        }

        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('TIPE', 'like', '%' . $search . '%')
                  ->orWhere('SERIAL_NUMBER', 'like', '%' . $search . '%')
                  ->orWhere('USER_PENANGGUNG', 'like', '%' . $search . '%')
                  ->orWhere('LOKASI_POSISI', 'like', '%' . $search . '%')
                  ->orWhere('SISTEM_OPERASI', 'like', '%' . $search . '%')
                  ->orWhere('KETERANGAN', 'like', '%' . $search . '%');
            });
        }

        $perPage = $request->input('per_page', 10);
        return response()->json($query->orderBy('ID_INVENTARIS', 'desc')->paginate($perPage));
    }

    /**
     * Store a newly created resource
     */
    public function store(Request $request)
    {
        $request->validate([
            'ID_TERMINAL' => 'required|integer',
            'ID_MERK' => 'nullable|integer',
            'TIPE' => 'nullable|string|max:100',
            'SERIAL_NUMBER' => 'nullable|string|max:100',
            'TAHUN_PENGADAAN' => 'nullable|string|max:4',
            'KAPASITAS_PROSESSOR' => 'nullable|string|max:100',
            'MEMORI_UTAMA' => 'nullable|string|max:50',
            'KAPASITAS_PENYIMPANAN' => 'nullable|string|max:50',
            'SISTEM_OPERASI' => 'nullable|string|max:100',
            'USER_PENANGGUNG' => 'nullable|string|max:100',
            'LOKASI_POSISI' => 'nullable|string|max:150',
            'ID_KONDISI' => 'nullable|integer',
            'KETERANGAN' => 'nullable|string',
            'ID_INSTAL' => 'nullable|integer',
            'ID_ANGGARAN' => 'nullable|integer',
            'KETERANGAN_ASSET' => 'nullable|string',
            'CREATE_BY' => 'nullable|string|max:50'
        ]);

        try {
            $inventaris = T_inventaris::create([
                'ID_TERMINAL' => $request->ID_TERMINAL,
                'ID_MERK' => $request->ID_MERK,
                'TIPE' => $request->TIPE,
                'SERIAL_NUMBER' => $request->SERIAL_NUMBER,
                'TAHUN_PENGADAAN' => $request->TAHUN_PENGADAAN,
                'KAPASITAS_PROSESSOR' => $request->KAPASITAS_PROSESSOR,
                'MEMORI_UTAMA' => $request->MEMORI_UTAMA,
                'KAPASITAS_PENYIMPANAN' => $request->KAPASITAS_PENYIMPANAN,
                'SISTEM_OPERASI' => $request->SISTEM_OPERASI,
                'USER_PENANGGUNG' => $request->USER_PENANGGUNG,
                'LOKASI_POSISI' => $request->LOKASI_POSISI,
                'ID_KONDISI' => $request->ID_KONDISI,
                'KETERANGAN' => $request->KETERANGAN,
                'ID_INSTAL' => $request->ID_INSTAL,
                'ID_ANGGARAN' => $request->ID_ANGGARAN,
                'KETERANGAN_ASSET' => $request->KETERANGAN_ASSET,
                'CREATE_BY' => $request->CREATE_BY ?? Auth::user()->username ?? 'system'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Data inventaris berhasil disimpan',
                'data' => $inventaris
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource
     */
    public function show(string $id)
    {
        $inventaris = T_inventaris::with(['terminal', 'merk', 'kondisi', 'instal', 'anggaran'])->findOrFail($id);
        return response()->json($inventaris);
    }

    /**
     * Update the specified resource
     */
    public function update(Request $request, string $id)
    {
        $inventaris = T_inventaris::findOrFail($id);

        $request->validate([
            'ID_MERK' => 'nullable|integer',
            'TIPE' => 'nullable|string|max:100',
            'SERIAL_NUMBER' => 'nullable|string|max:100',
            'TAHUN_PENGADAAN' => 'nullable|string|max:4',
            'KAPASITAS_PROSESSOR' => 'nullable|string|max:100',
            'MEMORI_UTAMA' => 'nullable|string|max:50',
            'KAPASITAS_PENYIMPANAN' => 'nullable|string|max:50',
            'SISTEM_OPERASI' => 'nullable|string|max:100',
            'USER_PENANGGUNG' => 'nullable|string|max:100',
            'LOKASI_POSISI' => 'nullable|string|max:150',
            'ID_KONDISI' => 'nullable|integer',
            'KETERANGAN' => 'nullable|string',
            'ID_INSTAL' => 'nullable|integer',
            'ID_ANGGARAN' => 'nullable|integer',
            'KETERANGAN_ASSET' => 'nullable|string'
        ]);

        try {
            $inventaris->update([
                'ID_MERK' => $request->ID_MERK,
                'TIPE' => $request->TIPE,
                'SERIAL_NUMBER' => $request->SERIAL_NUMBER,
                'TAHUN_PENGADAAN' => $request->TAHUN_PENGADAAN,
                'KAPASITAS_PROSESSOR' => $request->KAPASITAS_PROSESSOR,
                'MEMORI_UTAMA' => $request->MEMORI_UTAMA,
                'KAPASITAS_PENYIMPANAN' => $request->KAPASITAS_PENYIMPANAN,
                'SISTEM_OPERASI' => $request->SISTEM_OPERASI,
                'USER_PENANGGUNG' => $request->USER_PENANGGUNG,
                'LOKASI_POSISI' => $request->LOKASI_POSISI,
                'ID_KONDISI' => $request->ID_KONDISI,
                'KETERANGAN' => $request->KETERANGAN,
                'ID_INSTAL' => $request->ID_INSTAL,
                'ID_ANGGARAN' => $request->ID_ANGGARAN,
                'KETERANGAN_ASSET' => $request->KETERANGAN_ASSET,
                'UPDATE_BY' => Auth::user()->username ?? 'system'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Data inventaris berhasil diperbarui',
                'data' => $inventaris
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource
     */
    public function destroy(string $id)
    {
        try {
            $inventaris = T_inventaris::findOrFail($id);
            $inventaris->delete();

            return response()->json([
                'success' => true,
                'message' => 'Data inventaris berhasil dihapus'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export to Excel
     */
    public function exportExcel(Request $request)
    {
        $terminalId = $request->input('terminal_id');
        $filename = 'inventaris';

        if ($terminalId) {
            $filename .= '_terminal_' . $terminalId;
        }

        return Excel::download(new InventarisExport($terminalId), $filename . '.xlsx');
    }

    /**
     * Export template
     */
    public function exportTemplate()
    {
        return Excel::download(new InventarisExportTemplate, 'inventaris_template.xlsx');
    }

    /**
     * Import from Excel
     */
    public function importExcel(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv',
            'terminal_id' => 'required|integer'
        ]);

        $import = new InventarisImport($request->terminal_id);
        Excel::import($import, $request->file('file'));

        $results = $import->getResults();

        $message = "Import selesai: {$results['imported']} data berhasil diimport";

        if ($results['skipped'] > 0) {
            $message .= ", {$results['skipped']} data dilewati";
        }

        return response()->json([
            'message'  => $message,
            'imported' => $results['imported'],
            'skipped'  => $results['skipped'],
            'errors'   => $results['errors']
        ]);
    }
}
