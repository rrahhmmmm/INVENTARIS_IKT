<?php

namespace App\Http\Controllers;

use App\Models\M_instal;
use Illuminate\Http\Request;
use App\Exports\InstalExport;
use App\Exports\InstalExportTemplate;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\InstalImport;

class M_INSTALCONTROLLER extends Controller
{
    /**
     * Helper function untuk normalize nama instal
     * Hapus spasi dan convert ke uppercase
     */
    private function normalizeNamaInstal($nama)
    {
        return strtoupper(str_replace(' ', '', $nama));
    }

    /**
     * Cek apakah nama instal sudah ada di database (normalized)
     * @param string $namaInstal - nama instal yang akan dicek
     * @param int|null $excludeId - ID yang dikecualikan (untuk update)
     * @return bool - true jika sudah ada, false jika belum
     */
    private function isDuplicateNamaInstal($namaInstal, $excludeId = null)
    {
        $normalizedInput = $this->normalizeNamaInstal($namaInstal);

        $query = M_instal::whereRaw("UPPER(REPLACE(NAMA_INSTAL, ' ', '')) = ?", [$normalizedInput]);

        if ($excludeId) {
            $query->where('ID_INSTAL', '!=', $excludeId);
        }

        return $query->exists();
    }

    public function index(Request $request)
    {
        $query = M_instal::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('NAMA_INSTAL', 'like', '%' . $search . '%')
                    ->orWhere('CREATE_BY', 'like', '%' . $search . '%');
            });
        }

        return response()->json($query->get());
    }

    public function indexPaginated(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $query = M_instal::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('NAMA_INSTAL', 'like', '%' . $search . '%')
                    ->orWhere('CREATE_BY', 'like', '%' . $search . '%');
            });
        }

        return response()->json($query->paginate($perPage));
    }

    public function store(Request $request)
    {
        $request->validate([
            'NAMA_INSTAL' => 'required|string|max:100',
            'CREATE_BY'   => 'nullable|string|max:100'
        ]);

        // Cek duplicate dengan normalisasi
        if ($this->isDuplicateNamaInstal($request->NAMA_INSTAL)) {
            return response()->json([
                'message' => 'Nama instal sudah ada (duplikat terdeteksi)',
                'errors' => [
                    'NAMA_INSTAL' => ['Nama instal sudah ada di database']
                ]
            ], 422);
        }

        $instal = M_instal::create([
            'NAMA_INSTAL' => $request->NAMA_INSTAL,
            'CREATE_BY'   => $request->CREATE_BY
        ]);

        return response()->json($instal, 201);
    }

    public function show(string $id)
    {
        $instal = M_instal::findOrFail($id);
        return response()->json($instal);
    }

    public function update(Request $request, string $id)
    {
        $instal = M_instal::findOrFail($id);

        $request->validate([
            'NAMA_INSTAL' => 'sometimes|string|max:100',
            'CREATE_BY'   => 'nullable|string|max:100'
        ]);

        // Cek duplicate dengan normalisasi (exclude ID yang sedang di-update)
        if ($request->has('NAMA_INSTAL') && $this->isDuplicateNamaInstal($request->NAMA_INSTAL, $id)) {
            return response()->json([
                'message' => 'Nama instal sudah ada (duplikat terdeteksi)',
                'errors' => [
                    'NAMA_INSTAL' => ['Nama instal sudah ada di database']
                ]
            ], 422);
        }

        $instal->update($request->only(['NAMA_INSTAL', 'CREATE_BY']));

        return response()->json($instal);
    }

    public function destroy(string $id)
    {
        $instal = M_instal::findOrFail($id);
        $instal->delete();

        return response()->json(null, 204);
    }

    public function exportExcel()
    {
        return Excel::download(new InstalExport, 'instal.xlsx');
    }

    public function exportTemplate()
    {
        return Excel::download(new InstalExportTemplate, 'instal_template.xlsx');
    }

    public function importExcel(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv'
        ]);

        $import = new InstalImport;
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
