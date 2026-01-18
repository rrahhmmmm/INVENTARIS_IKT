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
        $query = T_inventaris::with(['terminal', 'merk', 'kondisi', 'anggaran', 'perangkat']);

        // Filter by terminal
        if ($request->filled('terminal_id')) {
            $query->where('ID_TERMINAL', $request->terminal_id);
        }

        // Filter by perangkat type
        if ($request->filled('perangkat_id')) {
            $query->where('ID_PERANGKAT', $request->perangkat_id);
        }

        // Search filter - search in param1-param16
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('TIPE', 'like', '%' . $search . '%')
                  ->orWhere('LOKASI_POSISI', 'like', '%' . $search . '%');

                // Search in all param columns
                for ($i = 1; $i <= 16; $i++) {
                    $q->orWhere("param$i", 'like', '%' . $search . '%');
                }
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
        // Validate mandatory fields
        $request->validate([
            'ID_TERMINAL' => 'required|integer',
            'ID_PERANGKAT' => 'required|integer|exists:M_PERANGKAT,ID_PERANGKAT',
            'LOKASI_POSISI' => 'required|string|max:150',
            'ID_KONDISI' => 'required|integer|exists:M_KONDISI,ID_KONDISI',
            'TAHUN_PENGADAAN' => 'required|string|max:4',
            'ID_ANGGARAN' => 'required|integer|exists:M_ANGGARAN,ID_ANGGARAN',
            'TIPE' => 'required|string|max:100',
            'ID_MERK' => 'required|integer|exists:M_MERK,ID_MERK',
            'CREATE_BY' => 'nullable|string|max:50'
        ]);

        try {
            $data = [
                'ID_TERMINAL' => $request->ID_TERMINAL,
                'ID_PERANGKAT' => $request->ID_PERANGKAT,
                'ID_MERK' => $request->ID_MERK,
                'TIPE' => $request->TIPE,
                'LOKASI_POSISI' => $request->LOKASI_POSISI,
                'ID_KONDISI' => $request->ID_KONDISI,
                'TAHUN_PENGADAAN' => $request->TAHUN_PENGADAAN,
                'ID_ANGGARAN' => $request->ID_ANGGARAN,
                'CREATE_BY' => $request->CREATE_BY ?? Auth::user()->username ?? 'system'
            ];

            // Add param1-param16
            for ($i = 1; $i <= 16; $i++) {
                $paramKey = "param$i";
                if ($request->has($paramKey)) {
                    $data[$paramKey] = $request->input($paramKey);
                }
            }

            $inventaris = T_inventaris::create($data);

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
        $inventaris = T_inventaris::with(['terminal', 'merk', 'kondisi', 'anggaran', 'perangkat'])->findOrFail($id);
        return response()->json($inventaris);
    }

    /**
     * Update the specified resource
     */
    public function update(Request $request, string $id)
    {
        $inventaris = T_inventaris::findOrFail($id);

        $request->validate([
            'ID_PERANGKAT' => 'sometimes|integer|exists:M_PERANGKAT,ID_PERANGKAT',
            'LOKASI_POSISI' => 'sometimes|string|max:150',
            'ID_KONDISI' => 'sometimes|integer',
            'TAHUN_PENGADAAN' => 'sometimes|string|max:4',
            'ID_ANGGARAN' => 'sometimes|integer',
            'TIPE' => 'sometimes|string|max:100',
            'ID_MERK' => 'sometimes|integer',
        ]);

        try {
            $data = [
                'ID_PERANGKAT' => $request->ID_PERANGKAT ?? $inventaris->ID_PERANGKAT,
                'ID_MERK' => $request->ID_MERK ?? $inventaris->ID_MERK,
                'TIPE' => $request->TIPE ?? $inventaris->TIPE,
                'LOKASI_POSISI' => $request->LOKASI_POSISI ?? $inventaris->LOKASI_POSISI,
                'ID_KONDISI' => $request->ID_KONDISI ?? $inventaris->ID_KONDISI,
                'TAHUN_PENGADAAN' => $request->TAHUN_PENGADAAN ?? $inventaris->TAHUN_PENGADAAN,
                'ID_ANGGARAN' => $request->ID_ANGGARAN ?? $inventaris->ID_ANGGARAN,
                'UPDATE_BY' => Auth::user()->username ?? 'system'
            ];

            // Update param1-param16
            for ($i = 1; $i <= 16; $i++) {
                $paramKey = "param$i";
                if ($request->has($paramKey)) {
                    $data[$paramKey] = $request->input($paramKey);
                }
            }

            $inventaris->update($data);

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
     * Export to Excel with perangkat filter
     */
    public function exportExcel(Request $request)
    {
        $terminalId = $request->input('terminal_id');
        $perangkatId = $request->input('perangkat_id');
        $filename = 'inventaris';

        if ($terminalId) {
            $filename .= '_terminal_' . $terminalId;
        }
        if ($perangkatId) {
            $filename .= '_perangkat_' . $perangkatId;
        }

        return Excel::download(new InventarisExport($terminalId, $perangkatId), $filename . '.xlsx');
    }

    /**
     * Export template based on device type
     */
    public function exportTemplate(Request $request)
    {
        $perangkatId = $request->input('perangkat_id', 1); // Default to PC/Laptop
        return Excel::download(new InventarisExportTemplate($perangkatId), 'inventaris_template.xlsx');
    }

    /**
     * Import from Excel with perangkat type
     */
    public function importExcel(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv',
            'terminal_id' => 'required|integer',
            'perangkat_id' => 'required|integer|exists:M_PERANGKAT,ID_PERANGKAT'
        ]);

        $import = new InventarisImport($request->terminal_id, $request->perangkat_id);
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
