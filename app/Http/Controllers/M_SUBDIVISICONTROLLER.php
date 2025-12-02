<?php

namespace App\Http\Controllers;

use App\Models\M_subdivisi;
use App\Models\M_divisi;
use Illuminate\Http\Request;
use App\Exports\SubdivisiExport;
use App\Exports\SubdivisiExportTemplate;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\SubdivisiImport;

class M_SUBDIVISICONTROLLER extends Controller
{
    /**
     * Helper function untuk normalize string
     * Hapus semua spasi dan convert ke uppercase
     */
    private function normalizeString($value)
    {
        return strtoupper(str_replace(' ', '', trim($value ?? '')));
    }

    /**
     * Cek apakah NAMA_SUBDIVISI sudah ada di database (normalized)
     * @param string $namaSubdivisi - nama subdivisi yang akan dicek
     * @param int|null $excludeId - ID yang dikecualikan (untuk update)
     * @return bool - true jika sudah ada, false jika belum
     */
    private function isDuplicateNamaSubdivisi($namaSubdivisi, $excludeId = null)
    {
        $normalizedInput = $this->normalizeString($namaSubdivisi);

        $query = M_subdivisi::whereRaw("UPPER(REPLACE(NAMA_SUBDIVISI, ' ', '')) = ?", [$normalizedInput]);

        if ($excludeId) {
            $query->where('ID_SUBDIVISI', '!=', $excludeId);
        }

        return $query->exists();
    }

    public function index(Request $request)
    {
        $query = M_subdivisi::with('divisi');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('NAMA_SUBDIVISI', 'like', '%' . $search . '%')
                    ->orWhere('CREATE_BY', 'like', '%' . $search . '%');
            });
        }

        $subdivisi = $query->get();
        return response()->json($subdivisi);
    }

    public function paginated(Request $request)
    {
        $perPage = $request->get('per_page', 10);
        $search = $request->get('search', '');

        $query = M_subdivisi::with('divisi');

        if (!empty($search)) {
            $query->where('NAMA_SUBDIVISI', 'like', "%{$search}%");
        }

        return $query->paginate($perPage);
    }

    public function getByDivisi($id_divisi)
    {
        $subdivisi = M_subdivisi::where('ID_DIVISI', $id_divisi)->get();
        return response()->json($subdivisi);
    }

    public function store(Request $request)
    {
        $request->validate([
            'ID_DIVISI'      => 'required|integer|exists:M_DIVISI,ID_DIVISI',
            'NAMA_SUBDIVISI' => 'required|string|max:100',
            'KODE_LOKASI'    => 'nullable|string|max:50',
            'CREATE_BY'      => 'nullable|string|max:100'
        ]);

        // Cek duplicate NAMA_SUBDIVISI dengan normalisasi
        if ($this->isDuplicateNamaSubdivisi($request->NAMA_SUBDIVISI)) {
            return response()->json([
                'message' => 'Nama subdivisi sudah ada (duplikat terdeteksi)',
                'errors' => [
                    'NAMA_SUBDIVISI' => ['Nama subdivisi sudah ada di database']
                ]
            ], 422);
        }

        $subdivisi = M_subdivisi::create([
            'ID_DIVISI'      => $request->ID_DIVISI,
            'NAMA_SUBDIVISI' => $request->NAMA_SUBDIVISI,
            'KODE_LOKASI'    => $request->KODE_LOKASI,
            'CREATE_BY'      => $request->CREATE_BY
        ]);

        // muat relasi supaya response lengkap
        $subdivisi->load('divisi');

        return response()->json($subdivisi, 201);
    }

    public function show($id)
    {
        $subdivisi = M_subdivisi::with('divisi')->findOrFail($id);
        return response()->json($subdivisi);
    }

    public function update(Request $request, $id)
    {
        $subdivisi = M_subdivisi::findOrFail($id);

        $request->validate([
            'ID_DIVISI'      => 'required|integer|exists:M_DIVISI,ID_DIVISI',
            'NAMA_SUBDIVISI' => 'required|string|max:100',
            'KODE_LOKASI'    => 'nullable|string|max:50',
            'CREATE_BY'      => 'nullable|string|max:100'
        ]);

        // Cek duplicate NAMA_SUBDIVISI dengan normalisasi (exclude ID sendiri)
        if ($this->isDuplicateNamaSubdivisi($request->NAMA_SUBDIVISI, $id)) {
            return response()->json([
                'message' => 'Nama subdivisi sudah ada (duplikat terdeteksi)',
                'errors' => [
                    'NAMA_SUBDIVISI' => ['Nama subdivisi sudah ada di database']
                ]
            ], 422);
        }

        $subdivisi->update([
            'ID_DIVISI'      => $request->ID_DIVISI,
            'NAMA_SUBDIVISI' => $request->NAMA_SUBDIVISI,
            'KODE_LOKASI'    => $request->KODE_LOKASI,
            'CREATE_BY'      => $request->CREATE_BY
        ]);

        $subdivisi->load('divisi');

        return response()->json($subdivisi);
    }

    public function destroy($id)
    {
        $subdivisi = M_subdivisi::findOrFail($id);
        $subdivisi->delete();

        return response()->json(null, 204);
    }

    public function exportExcel()
    {
        return Excel::download(new SubdivisiExport, 'subdivisi.xlsx');
    }

    public function exportTemplate()
    {
        return Excel::download(new SubdivisiExportTemplate, 'template_subdivisi.xlsx');
    }

    public function importExcel(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv'
        ]);

        $import = new SubdivisiImport;
        Excel::import($import, $request->file('file'));

        $results = $import->getResults();

        $message = "Import selesai: {$results['imported']} data berhasil diimport";

        if ($results['skipped'] > 0) {
            $message .= ", {$results['skipped']} data dilewati (duplikat/tidak valid)";
        }

        return response()->json([
            'message'    => $message,
            'imported'   => $results['imported'],
            'skipped'    => $results['skipped'],
            'duplicates' => $results['duplicates']
        ]);
    }
}