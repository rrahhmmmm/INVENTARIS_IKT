<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\M_klasifikasi;
use App\Exports\KlasifikasiExport;
use App\Exports\KlasifikasiExportTemplate;
use App\Imports\KlasifikasiImport;
use Maatwebsite\Excel\Facades\Excel;

class M_KLASIFIKASICONTROLLER extends Controller
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
     * Cek apakah KODE_KLASIFIKASI sudah ada di database (normalized)
     * @param string $kodeKlasifikasi - kode yang akan dicek
     * @param int|null $excludeId - ID yang dikecualikan (untuk update)
     * @return bool - true jika sudah ada, false jika belum
     */
    private function isDuplicateKode($kodeKlasifikasi, $excludeId = null)
    {
        $normalizedInput = $this->normalizeString($kodeKlasifikasi);

        $query = M_klasifikasi::whereRaw("UPPER(REPLACE(KODE_KLASIFIKASI, ' ', '')) = ?", [$normalizedInput]);

        if ($excludeId) {
            $query->where('ID_KLASIFIKASI', '!=', $excludeId);
        }

        return $query->exists();
    }

    /**
     * Cek apakah KATEGORI sudah ada di database (normalized)
     * @param string $kategori - kategori yang akan dicek
     * @param int|null $excludeId - ID yang dikecualikan (untuk update)
     * @return bool - true jika sudah ada, false jika belum
     */
    private function isDuplicateKategori($kategori, $excludeId = null)
    {
        $normalizedInput = $this->normalizeString($kategori);

        $query = M_klasifikasi::whereRaw("UPPER(REPLACE(KATEGORI, ' ', '')) = ?", [$normalizedInput]);

        if ($excludeId) {
            $query->where('ID_KLASIFIKASI', '!=', $excludeId);
        }

        return $query->exists();
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $query = M_klasifikasi::query();

        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('KODE_KLASIFIKASI', 'like', '%' . $search . '%')
                    ->orWhere('KATEGORI', 'like', '%' . $search . '%')
                    ->orWhere('DESKRIPSI', 'like', '%' . $search . '%')
                    ->orWhere('CREATE_BY', 'like', '%' . $search . '%');
            });
        }

        return $query->paginate($perPage);
    }

    public function all()
    {
        // Return semua data tanpa pagination untuk suggestion
        return M_klasifikasi::all();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            "KODE_KLASIFIKASI" => "required|string|max:100",
            "KATEGORI"         => "required|string|max:100",
            "DESKRIPSI"        => "required|string|max:1000",
            "START_DATE"       => "nullable|date",
            "END_DATE"         => "nullable|date",
            "CREATE_BY"        => "nullable|string|max:100"
        ]);

        // Cek duplicate KODE_KLASIFIKASI dengan normalisasi
        if ($this->isDuplicateKode($request->KODE_KLASIFIKASI)) {
            return response()->json([
                'message' => 'Kode klasifikasi sudah ada (duplikat terdeteksi)',
                'errors' => [
                    'KODE_KLASIFIKASI' => ['Kode klasifikasi sudah ada di database']
                ]
            ], 422);
        }

        // Cek duplicate KATEGORI dengan normalisasi
        if ($this->isDuplicateKategori($request->KATEGORI)) {
            return response()->json([
                'message' => 'Kategori sudah ada (duplikat terdeteksi)',
                'errors' => [
                    'KATEGORI' => ['Kategori sudah ada di database']
                ]
            ], 422);
        }

        $klasifikasi = M_klasifikasi::create([
            'KODE_KLASIFIKASI' => $request->KODE_KLASIFIKASI,
            'KATEGORI'         => $request->KATEGORI,
            'DESKRIPSI'        => $request->DESKRIPSI,
            'START_DATE'       => $request->START_DATE,
            'END_DATE'         => $request->END_DATE,
            'CREATE_BY'        => $request->CREATE_BY
        ]);

        return response()->json($klasifikasi, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $klasifikasi = M_klasifikasi::findOrFail($id);
        return response()->json($klasifikasi);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $klasifikasi = M_klasifikasi::findOrFail($id);

        $request->validate([
            "KODE_KLASIFIKASI" => "sometimes|string|max:100",
            "KATEGORI"         => "sometimes|string|max:100",
            "DESKRIPSI"        => "sometimes|string|max:1000",
            "START_DATE"       => "nullable|date",
            "END_DATE"         => "nullable|date",
            "CREATE_BY"        => "nullable|string|max:100"
        ]);

        // Cek duplicate KODE_KLASIFIKASI dengan normalisasi (exclude ID sendiri)
        if ($request->has('KODE_KLASIFIKASI') && $this->isDuplicateKode($request->KODE_KLASIFIKASI, $id)) {
            return response()->json([
                'message' => 'Kode klasifikasi sudah ada (duplikat terdeteksi)',
                'errors' => [
                    'KODE_KLASIFIKASI' => ['Kode klasifikasi sudah ada di database']
                ]
            ], 422);
        }

        // Cek duplicate KATEGORI dengan normalisasi (exclude ID sendiri)
        if ($request->has('KATEGORI') && $this->isDuplicateKategori($request->KATEGORI, $id)) {
            return response()->json([
                'message' => 'Kategori sudah ada (duplikat terdeteksi)',
                'errors' => [
                    'KATEGORI' => ['Kategori sudah ada di database']
                ]
            ], 422);
        }

        $klasifikasi->update($request->only([
            'KODE_KLASIFIKASI',
            'KATEGORI',
            'DESKRIPSI',
            'START_DATE',
            'END_DATE',
            'CREATE_BY'
        ]));

        return response()->json($klasifikasi);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $klasifikasi = M_klasifikasi::findOrFail($id);
        $klasifikasi->delete();
        return response()->json(["message" => "Deleted succesfully"]);
    }

    public function exportExcel()
    {
        return Excel::download(new KlasifikasiExport, 'klasifikasi.xlsx');
    }

    public function exportTemplate()
    {
        return Excel::download(new KlasifikasiExportTemplate, 'template_import_klasifikasi.xlsx');
    }

    public function importExcel(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv'
        ]);

        $import = new KlasifikasiImport;
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