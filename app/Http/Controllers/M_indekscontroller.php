<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\M_indeks;
use App\Exports\IndeksExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\IndeksExportTemplate;
use App\Imports\IndeksImport;

class M_indekscontroller extends Controller
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
     * Cek apakah NO_INDEKS sudah ada di database (normalized)
     * @param string $noIndeks - nomor indeks yang akan dicek
     * @param int|null $excludeId - ID yang dikecualikan (untuk update)
     * @return bool - true jika sudah ada, false jika belum
     */
    private function isDuplicateNoIndeks($noIndeks, $excludeId = null)
    {
        $normalizedInput = $this->normalizeString($noIndeks);

        $query = M_indeks::whereRaw("UPPER(REPLACE(NO_INDEKS, ' ', '')) = ?", [$normalizedInput]);

        if ($excludeId) {
            $query->where('ID_INDEKS', '!=', $excludeId);
        }

        return $query->exists();
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $query = M_indeks::query();

        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('NO_INDEKS', 'like', "%{$search}%")
                    ->orWhere('WILAYAH', 'like', "%{$search}%")
                    ->orWhere('NAMA_INDEKS', 'like', "%{$search}%");
            });
        }

        return $query->paginate($perPage);
    }

    public function all()
    {
        // Return semua data tanpa pagination untuk suggestion
        return M_indeks::all();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            "NO_INDEKS"   => "required|string|max:100",
            "WILAYAH"     => "nullable|string|max:100",
            "NAMA_INDEKS" => "nullable|string|max:250",
            "START_DATE"  => "nullable|date",
            "END_DATE"    => "nullable|date",
            "CREATE_BY"   => "nullable|string|max:100"
        ]);

        // Cek duplicate NO_INDEKS dengan normalisasi
        if ($this->isDuplicateNoIndeks($request->NO_INDEKS)) {
            return response()->json([
                'message' => 'Nomor indeks sudah ada (duplikat terdeteksi)',
                'errors' => [
                    'NO_INDEKS' => ['Nomor indeks sudah ada di database']
                ]
            ], 422);
        }

        $indeks = M_indeks::create([
            'NO_INDEKS'   => $request->NO_INDEKS,
            'WILAYAH'     => $request->WILAYAH,
            'NAMA_INDEKS' => $request->NAMA_INDEKS,
            'START_DATE'  => $request->START_DATE,
            'END_DATE'    => $request->END_DATE,
            'CREATE_BY'   => $request->CREATE_BY
        ]);

        return response()->json($indeks, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $indeks = M_indeks::findOrFail($id);
        return response()->json($indeks);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $indeks = M_indeks::findOrFail($id);

        $request->validate([
            "NO_INDEKS"   => "sometimes|string|max:100",
            "WILAYAH"     => "nullable|string|max:100",
            "NAMA_INDEKS" => "nullable|string|max:250",
            "START_DATE"  => "nullable|date",
            "END_DATE"    => "nullable|date",
            "CREATE_BY"   => "nullable|string|max:100"
        ]);

        // Cek duplicate NO_INDEKS dengan normalisasi (exclude ID sendiri)
        if ($request->has('NO_INDEKS') && $this->isDuplicateNoIndeks($request->NO_INDEKS, $id)) {
            return response()->json([
                'message' => 'Nomor indeks sudah ada (duplikat terdeteksi)',
                'errors' => [
                    'NO_INDEKS' => ['Nomor indeks sudah ada di database']
                ]
            ], 422);
        }

        $indeks->update($request->only([
            'NO_INDEKS',
            'WILAYAH',
            'NAMA_INDEKS',
            'START_DATE',
            'END_DATE',
            'CREATE_BY'
        ]));

        return response()->json($indeks);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $indeks = M_indeks::findOrFail($id);
        $indeks->delete();
        return response()->json(["message" => "Deleted succesfully"]);
    }

    public function exportExcel()
    {
        return Excel::download(new IndeksExport, 'indeks.xlsx');
    }

    public function exportTemplate()
    {
        return Excel::download(new IndeksExportTemplate, 'indeks-template.xlsx');
    }

    public function importExcel(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv'
        ]);

        $import = new IndeksImport;
        Excel::import($import, $request->file('file'));

        $results = $import->getResults();

        $message = "Import selesai: {$results['imported']} data berhasil diimport";

        if ($results['skipped'] > 0) {
            $message .= ", {$results['skipped']} data dilewati (duplikat/kosong)";
        }

        return response()->json([
            'message'    => $message,
            'imported'   => $results['imported'],
            'skipped'    => $results['skipped'],
            'duplicates' => $results['duplicates']
        ]);
    }
}