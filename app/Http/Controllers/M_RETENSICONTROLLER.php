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

            "jenis_arsip"=> "required|string|max:150",
            "bidang_arsip"=> "required|string|max:150",
            "tipe_arsip"=> "required|string|max:150",
            "detail_tipe_arsip"=> "required|string|max:500",
            "masa_aktif"=> "required|integer",
            "DESC_AKTIF"=> "nullable|string|max:150",
            "masa_inaktif"=> "required|integer",
            "DESC_INAKTIF"=> "nullable|string|max:150",
            "keterangan"=> "nullable|string|max:250",
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
            "jenis_arsip"=> "sometimes|string|max:150",
            "bidang_arsip"=> "sometimes|string|max:150",
            "tipe_arsip"=> "sometimes|string|max:150",
            "detail_tipe_arsip"=> "sometimes|string|max:500",
            "masa_aktif"=> "sometimes|integer",
            "DESC_AKTIF"=> "nullable|string|max:150",
            "masa_inaktif"=> "sometimes|integer",
            "DESC_INAKTIF"=> "nullable|string|max:150",
            "keterangan"=> "nullable|string|max:250",
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
