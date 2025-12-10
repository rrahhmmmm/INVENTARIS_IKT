<?php

namespace App\Http\Controllers;

use App\Models\M_merk;
use Illuminate\Http\Request;
use App\Exports\MerkExport;
use App\Exports\MerkExportTemplate;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\MerkImport;

class M_MERKCONTROLLER extends Controller
{
    /**
     * Helper function untuk normalize nama merk
     * Hapus spasi dan convert ke uppercase
     */
    private function normalizeNamaMerk($nama)
    {
        return strtoupper(str_replace(' ', '', $nama));
    }

    /**
     * Cek apakah nama merk sudah ada di database (normalized)
     * @param string $namaMerk - nama merk yang akan dicek
     * @param int|null $excludeId - ID yang dikecualikan (untuk update)
     * @return bool - true jika sudah ada, false jika belum
     */
    private function isDuplicateNamaMerk($namaMerk, $excludeId = null)
    {
        $normalizedInput = $this->normalizeNamaMerk($namaMerk);

        $query = M_merk::whereRaw("UPPER(REPLACE(NAMA_MERK, ' ', '')) = ?", [$normalizedInput]);

        if ($excludeId) {
            $query->where('ID_MERK', '!=', $excludeId);
        }

        return $query->exists();
    }

    public function index(Request $request)
    {
        $query = M_merk::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('NAMA_MERK', 'like', '%' . $search . '%')
                    ->orWhere('CREATE_BY', 'like', '%' . $search . '%');
            });
        }

        return response()->json($query->get());
    }

    public function indexPaginated(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $query = M_merk::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('NAMA_MERK', 'like', '%' . $search . '%')
                    ->orWhere('CREATE_BY', 'like', '%' . $search . '%');
            });
        }

        return response()->json($query->paginate($perPage));
    }

    public function store(Request $request)
    {
        $request->validate([
            'NAMA_MERK' => 'required|string|max:100',
            'CREATE_BY' => 'nullable|string|max:100'
        ]);

        // Cek duplicate dengan normalisasi
        if ($this->isDuplicateNamaMerk($request->NAMA_MERK)) {
            return response()->json([
                'message' => 'Nama merk sudah ada (duplikat terdeteksi)',
                'errors' => [
                    'NAMA_MERK' => ['Nama merk sudah ada di database']
                ]
            ], 422);
        }

        $merk = M_merk::create([
            'NAMA_MERK' => $request->NAMA_MERK,
            'CREATE_BY' => $request->CREATE_BY
        ]);

        return response()->json($merk, 201);
    }

    public function show(string $id)
    {
        $merk = M_merk::findOrFail($id);
        return response()->json($merk);
    }

    public function update(Request $request, string $id)
    {
        $merk = M_merk::findOrFail($id);

        $request->validate([
            'NAMA_MERK' => 'sometimes|string|max:100',
            'CREATE_BY' => 'nullable|string|max:100'
        ]);

        // Cek duplicate dengan normalisasi (exclude ID yang sedang di-update)
        if ($request->has('NAMA_MERK') && $this->isDuplicateNamaMerk($request->NAMA_MERK, $id)) {
            return response()->json([
                'message' => 'Nama merk sudah ada (duplikat terdeteksi)',
                'errors' => [
                    'NAMA_MERK' => ['Nama merk sudah ada di database']
                ]
            ], 422);
        }

        $merk->update($request->only(['NAMA_MERK', 'CREATE_BY']));

        return response()->json($merk);
    }

    public function destroy(string $id)
    {
        $merk = M_merk::findOrFail($id);
        $merk->delete();

        return response()->json(null, 204);
    }

    public function exportExcel()
    {
        return Excel::download(new MerkExport, 'merk.xlsx');
    }

    public function exportTemplate()
    {
        return Excel::download(new MerkExportTemplate, 'merk_template.xlsx');
    }

    public function importExcel(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv'
        ]);

        $import = new MerkImport;
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
