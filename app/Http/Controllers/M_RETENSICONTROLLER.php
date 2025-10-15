<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\M_retensi;

class M_RETENSICONTROLLER extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return M_retensi::all();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([

            "JENIS_ARSIP"=> "required|string|max:150",
            "BIDANG_ARSIP"=> "required|string|max:150",
            "TIPE_ARSIP"=> "required|string|max:150",
            "DETAIL_TIPE_ARSIP"=> "required|string|max:500",
            "MASA_AKTIF"=> "required|string",
            "DESC_AKTIF"=> "nullable|string|max:150",
            "MASA_INAKTIF"=> "required|string",
            "DESC_INAKTIF"=> "nullable|string|max:150",
            "KETERANGAN"=> "nullable|string|max:250",
            "CREATE_BY"=> "nullable|string|max:100"
        ]);

        $retensi = M_retensi::create($validated);
        return response()->json($retensi,201);

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

        $validated = $request->validate([
            "JENIS_ARSIP"=> "sometimes|string|max:150",
            "BIDANG_ARSIP"=> "sometimes|string|max:150",
            "TIPE_ARSIP"=> "sometimes|string|max:150",
            "DETAIL_TIPE_ARSIP"=> "sometimes|string|max:500",
            "MASA_AKTIF"=> "sometimes|string",
            "DESC_AKTIF"=> "nullable|string|max:150",
            "MASA_INAKTIF"=> "sometimes|string",
            "DESC_INAKTIF"=> "nullable|string|max:150",
            "KETERANGAN"=> "nullable|string|max:250",
            "CREATE_BY"=> "nullable|string|max:100"

        ]);

        $retensi->update($validated);
        return response()->json($retensi);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $retensi = M_retensi::findOrFail($id);
        $retensi->delete();
        return response()->json(['message ' => 'Deleted successfully']);
    }
}
