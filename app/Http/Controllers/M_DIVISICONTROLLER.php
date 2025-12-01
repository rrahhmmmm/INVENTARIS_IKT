<?php

namespace App\Http\Controllers;

use App\Models\M_divisi;
use Illuminate\Http\Request;
use App\Exports\DivisiExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\DivisiImport;
use Illuminate\Support\Facades\DB;

class M_DIVISICONTROLLER extends Controller
{
    /**
     * Helper function untuk normalize nama divisi
     * Hapus spasi dan convert ke uppercase
     */
    private function normalizeNamaDivisi($nama)
    {
        // Hapus semua spasi dan convert ke uppercase
        return strtoupper(str_replace(' ', '', $nama));
    }

    /**
     * Cek apakah nama divisi sudah ada di database (normalized)
     * @param string $namaDivisi - nama divisi yang akan dicek
     * @param int|null $excludeId - ID yang dikecualikan (untuk update)
     * @return bool - true jika sudah ada, false jika belum
     */
    private function isDuplicateNamaDivisi($namaDivisi, $excludeId = null)
    {
        $normalizedInput = $this->normalizeNamaDivisi($namaDivisi);

        $query = M_divisi::whereRaw("UPPER(REPLACE(NAMA_DIVISI, ' ', '')) = ?", [$normalizedInput]);

        if ($excludeId) {
            $query->where('ID_DIVISI', '!=', $excludeId);
        }

        return $query->exists();
    }

    public function index(Request $request)
    {
        $query = M_divisi::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('NAMA_DIVISI', 'like', '%' . $search . '%')
                    ->orWhere('CREATE_BY', 'like', '%' . $search . '%');
            });
        }

        return response()->json($query->get());
    }

    public function indexPaginated(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $query = M_divisi::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('NAMA_DIVISI', 'like', '%' . $search . '%')
                    ->orWhere('CREATE_BY', 'like', '%' . $search . '%');
            });
        }

        return response()->json($query->paginate($perPage));
    }

    public function store(Request $request)
    {
        $request->validate([
            'NAMA_DIVISI' => 'required|string|max:100',
            'CREATE_BY'   => 'nullable|string|max:100'
        ]);

        // Cek duplicate dengan normalisasi
        if ($this->isDuplicateNamaDivisi($request->NAMA_DIVISI)) {
            return response()->json([
                'message' => 'Nama divisi sudah ada (duplikat terdeteksi)',
                'errors' => [
                    'NAMA_DIVISI' => ['Nama divisi sudah ada di database']
                ]
            ], 422);
        }

        $divisi = M_divisi::create([
            'NAMA_DIVISI' => $request->NAMA_DIVISI,
            'CREATE_BY'   => $request->CREATE_BY
        ]);

        return response()->json($divisi, 201);
    }

    public function show(string $id)
    {
        $divisi = M_divisi::findOrFail($id);
        return response()->json($divisi);
    }

    public function update(Request $request, string $id)
    {
        $divisi = M_divisi::findOrFail($id);

        $request->validate([
            'NAMA_DIVISI' => 'sometimes|string|max:100',
            'CREATE_BY'   => 'nullable|string|max:100'
        ]);

        // Cek duplicate dengan normalisasi (exclude ID yang sedang di-update)
        if ($request->has('NAMA_DIVISI') && $this->isDuplicateNamaDivisi($request->NAMA_DIVISI, $id)) {
            return response()->json([
                'message' => 'Nama divisi sudah ada (duplikat terdeteksi)',
                'errors' => [
                    'NAMA_DIVISI' => ['Nama divisi sudah ada di database']
                ]
            ], 422);
        }

        $divisi->update($request->only(['NAMA_DIVISI', 'CREATE_BY']));

        return response()->json($divisi);
    }

    public function destroy(string $id)
    {
        $divisi = M_divisi::findOrFail($id);
        $divisi->delete();

        return response()->json(null, 204);
    }

    public function exportExcel()
    {
        return Excel::download(new DivisiExport, 'divisi.xlsx');
    }

    public function exportTemplate()
    {
        return Excel::download(new \App\Exports\DivisiExportTemplate, 'divisi_template.xlsx');
    }

    public function importExcel(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv'
        ]);

        $import = new DivisiImport;
        Excel::import($import, $request->file('file'));

        $results = $import->getResults();

        $message = "Import selesai: {$results['imported']} data berhasil diimport";
        
        if ($results['skipped'] > 0) {
            $message .= ", {$results['skipped']} data dilewati (duplikat)";
        }

        return response()->json([
            'message'    => $message,
            'imported'   => $results['imported'],
            'skipped'    => $results['skipped'],
            'duplicates' => $results['duplicates']
        ]);
    }
}