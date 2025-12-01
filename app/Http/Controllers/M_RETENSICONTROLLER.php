<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\M_retensi;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\RetensiImport;
use App\Exports\RetensiExport;
use App\Exports\RetensiExportTemplate;

class M_RETENSICONTROLLER extends Controller
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
     * Cek apakah kombinasi JENIS_ARSIP + BIDANG_ARSIP + TIPE_ARSIP + DETAIL_TIPE_ARSIP sudah ada
     * @param string $jenisArsip
     * @param string $bidangArsip
     * @param string $tipeArsip
     * @param string $detailTipeArsip
     * @param int|null $excludeId - ID yang dikecualikan (untuk update)
     * @return bool - true jika sudah ada, false jika belum
     */
    private function isDuplicateRetensi($jenisArsip, $bidangArsip, $tipeArsip, $detailTipeArsip, $excludeId = null)
    {
        $normalizedJenis = $this->normalizeString($jenisArsip);
        $normalizedBidang = $this->normalizeString($bidangArsip);
        $normalizedTipe = $this->normalizeString($tipeArsip);
        $normalizedDetail = $this->normalizeString($detailTipeArsip);

        $query = M_retensi::whereRaw("UPPER(REPLACE(JENIS_ARSIP, ' ', '')) = ?", [$normalizedJenis])
            ->whereRaw("UPPER(REPLACE(BIDANG_ARSIP, ' ', '')) = ?", [$normalizedBidang])
            ->whereRaw("UPPER(REPLACE(TIPE_ARSIP, ' ', '')) = ?", [$normalizedTipe])
            ->whereRaw("UPPER(REPLACE(DETAIL_TIPE_ARSIP, ' ', '')) = ?", [$normalizedDetail]);

        if ($excludeId) {
            $query->where('ID_RETENSI', '!=', $excludeId);
        }

        return $query->exists();
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $query = M_retensi::query();

        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('JENIS_ARSIP', 'like', '%' . $search . '%')
                    ->orWhere('BIDANG_ARSIP', 'like', '%' . $search . '%')
                    ->orWhere('TIPE_ARSIP', 'like', '%' . $search . '%')
                    ->orWhere('DETAIL_TIPE_ARSIP', 'like', '%' . $search . '%')
                    ->orWhere('MASA_AKTIF', 'like', '%' . $search . '%')
                    ->orWhere('MASA_INAKTIF', 'like', '%' . $search . '%')
                    ->orWhere('KETERANGAN', 'like', '%' . $search . '%')
                    ->orWhere('CREATE_BY', 'like', '%' . $search . '%');
            });
        }

        return $query->paginate($perPage);
    }

    public function all()
    {
        return M_retensi::all();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            "JENIS_ARSIP"       => "required|string|max:150",
            "BIDANG_ARSIP"      => "required|string|max:150",
            "TIPE_ARSIP"        => "required|string|max:150",
            "DETAIL_TIPE_ARSIP" => "required|string|max:500",
            "MASA_AKTIF"        => "required|string",
            "DESC_AKTIF"        => "nullable|string|max:150",
            "MASA_INAKTIF"      => "required|string",
            "DESC_INAKTIF"      => "nullable|string|max:150",
            "KETERANGAN"        => "nullable|string|max:250",
            "CREATE_BY"         => "nullable|string|max:100"
        ]);

        // Cek duplicate kombinasi dengan normalisasi
        if ($this->isDuplicateRetensi(
            $request->JENIS_ARSIP,
            $request->BIDANG_ARSIP,
            $request->TIPE_ARSIP,
            $request->DETAIL_TIPE_ARSIP
        )) {
            return response()->json([
                'message' => 'Data retensi sudah ada (duplikat terdeteksi)',
                'errors' => [
                    'DUPLICATE' => ['Kombinasi Jenis Arsip, Bidang Arsip, Tipe Arsip, dan Detail Tipe Arsip sudah ada di database']
                ]
            ], 422);
        }

        $retensi = M_retensi::create([
            'JENIS_ARSIP'       => $request->JENIS_ARSIP,
            'BIDANG_ARSIP'      => $request->BIDANG_ARSIP,
            'TIPE_ARSIP'        => $request->TIPE_ARSIP,
            'DETAIL_TIPE_ARSIP' => $request->DETAIL_TIPE_ARSIP,
            'MASA_AKTIF'        => $request->MASA_AKTIF,
            'DESC_AKTIF'        => $request->DESC_AKTIF,
            'MASA_INAKTIF'      => $request->MASA_INAKTIF,
            'DESC_INAKTIF'      => $request->DESC_INAKTIF,
            'KETERANGAN'        => $request->KETERANGAN,
            'CREATE_BY'         => $request->CREATE_BY
        ]);

        return response()->json($retensi, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $m_retensi = M_retensi::findOrFail($id);
        return response()->json($m_retensi);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $retensi = M_retensi::findOrFail($id);

        $request->validate([
            "JENIS_ARSIP"       => "sometimes|string|max:150",
            "BIDANG_ARSIP"      => "sometimes|string|max:150",
            "TIPE_ARSIP"        => "sometimes|string|max:150",
            "DETAIL_TIPE_ARSIP" => "sometimes|string|max:500",
            "MASA_AKTIF"        => "sometimes|string",
            "DESC_AKTIF"        => "nullable|string|max:150",
            "MASA_INAKTIF"      => "sometimes|string",
            "DESC_INAKTIF"      => "nullable|string|max:150",
            "KETERANGAN"        => "nullable|string|max:250",
            "CREATE_BY"         => "nullable|string|max:100"
        ]);

        // Ambil nilai yang akan dicek (gunakan nilai baru jika ada, atau nilai lama)
        $jenisArsip = $request->input('JENIS_ARSIP', $retensi->JENIS_ARSIP);
        $bidangArsip = $request->input('BIDANG_ARSIP', $retensi->BIDANG_ARSIP);
        $tipeArsip = $request->input('TIPE_ARSIP', $retensi->TIPE_ARSIP);
        $detailTipeArsip = $request->input('DETAIL_TIPE_ARSIP', $retensi->DETAIL_TIPE_ARSIP);

        // Cek duplicate kombinasi dengan normalisasi (exclude ID sendiri)
        if ($this->isDuplicateRetensi($jenisArsip, $bidangArsip, $tipeArsip, $detailTipeArsip, $id)) {
            return response()->json([
                'message' => 'Data retensi sudah ada (duplikat terdeteksi)',
                'errors' => [
                    'DUPLICATE' => ['Kombinasi Jenis Arsip, Bidang Arsip, Tipe Arsip, dan Detail Tipe Arsip sudah ada di database']
                ]
            ], 422);
        }

        $retensi->update($request->only([
            'JENIS_ARSIP',
            'BIDANG_ARSIP',
            'TIPE_ARSIP',
            'DETAIL_TIPE_ARSIP',
            'MASA_AKTIF',
            'DESC_AKTIF',
            'MASA_INAKTIF',
            'DESC_INAKTIF',
            'KETERANGAN',
            'CREATE_BY'
        ]));

        return response()->json($retensi);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $retensi = M_retensi::findOrFail($id);
        $retensi->delete();
        return response()->json(['message' => 'Deleted successfully']);
    }

    public function importExcel(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls'
        ]);

        $import = new RetensiImport;
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

    public function exportExcel()
    {
        return Excel::download(new RetensiExport, 'Retensi.xlsx');
    }

    public function exportTemplate()
    {
        return Excel::download(new RetensiExportTemplate, 'template_import_retensi.xlsx');
    }
}