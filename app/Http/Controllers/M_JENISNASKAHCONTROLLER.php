<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\M_jenisnaskah;
use App\Exports\JenisNaskahExport;
use App\Exports\JenisNaskahExportTemplate;
use App\Imports\JenisNaskahImport;
use Maatwebsite\Excel\Facades\Excel;

class M_JENISNASKAHCONTROLLER extends Controller
{
    /**
     * Helper function untuk normalize string
     * Hapus semua spasi dan convert ke uppercase
     */
    private function normalizeString($value)
    {
        return strtoupper(str_replace(' ', '', trim($value)));
    }

    /**
     * Cek apakah NAMA_JENIS sudah ada di database (normalized)
     * @param string $namaJenis - nama yang akan dicek
     * @param int|null $excludeId - ID yang dikecualikan (untuk update)
     * @return bool - true jika sudah ada, false jika belum
     */
    private function isDuplicateNamaJenis($namaJenis, $excludeId = null)
    {
        $normalizedInput = $this->normalizeString($namaJenis);

        $query = M_jenisnaskah::whereRaw("UPPER(REPLACE(NAMA_JENIS, ' ', '')) = ?", [$normalizedInput]);

        if ($excludeId) {
            $query->where('ID_JENISNASKAH', '!=', $excludeId);
        }

        return $query->exists();
    }

    /**
     * Display a listing of the resource (with pagination + search).
     */
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $query = M_jenisnaskah::query();

        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('NAMA_JENIS', 'like', '%' . $search . '%')
                    ->orWhere('CREATE_BY', 'like', '%' . $search . '%');
            });
        }

        return $query->paginate($perPage);
    }

    /**
     * Get all data without pagination.
     */
    public function all()
    {
        return M_jenisnaskah::all();
    }

    /**
     * Store a newly created resource.
     */
    public function store(Request $request)
    {
        $request->validate([
            "NAMA_JENIS" => "required|string|max:150",
            "CREATE_BY"  => "nullable|string|max:100"
        ]);

        // Cek duplicate NAMA_JENIS dengan normalisasi
        if ($this->isDuplicateNamaJenis($request->NAMA_JENIS)) {
            return response()->json([
                'message' => 'Nama jenis naskah sudah ada (duplikat terdeteksi)',
                'errors' => [
                    'NAMA_JENIS' => ['Nama jenis naskah sudah ada di database']
                ]
            ], 422);
        }

        $jenis = M_jenisnaskah::create([
            'NAMA_JENIS' => $request->NAMA_JENIS,
            'CREATE_BY'  => $request->CREATE_BY
        ]);

        return response()->json($jenis, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $jenis = M_jenisnaskah::findOrFail($id);
        return response()->json($jenis);
    }

    /**
     * Update the specified resource.
     */
    public function update(Request $request, string $id)
    {
        $jenis = M_jenisnaskah::findOrFail($id);

        $request->validate([
            "NAMA_JENIS" => "sometimes|string|max:150",
            "CREATE_BY"  => "nullable|string|max:100"
        ]);

        // Cek duplicate NAMA_JENIS dengan normalisasi (exclude ID sendiri)
        if ($request->has('NAMA_JENIS') && $this->isDuplicateNamaJenis($request->NAMA_JENIS, $id)) {
            return response()->json([
                'message' => 'Nama jenis naskah sudah ada (duplikat terdeteksi)',
                'errors' => [
                    'NAMA_JENIS' => ['Nama jenis naskah sudah ada di database']
                ]
            ], 422);
        }

        $jenis->update($request->only([
            'NAMA_JENIS',
            'CREATE_BY'
        ]));

        return response()->json($jenis);
    }

    /**
     * Remove the specified resource.
     */
    public function destroy(string $id)
    {
        $jenis = M_jenisnaskah::findOrFail($id);
        $jenis->delete();
        return response()->json(['message' => 'Deleted successfully']);
    }

    public function exportExcel()
    {
        return Excel::download(new JenisNaskahExport, 'jenis_naskah.xlsx');
    }

    public function exportTemplate()
    {
        return Excel::download(new JenisNaskahExportTemplate, 'template_import_jenis_naskah.xlsx');
    }

    public function importExcel(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv'
        ]);

        $import = new JenisNaskahImport;
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