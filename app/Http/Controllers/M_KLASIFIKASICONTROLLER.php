<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\M_klasifikasi;

class M_KLASIFIKASICONTROLLER extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return M_klasifikasi::all();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            "KODE_KLASIFIKASI"=> "required|string|max:100|unique:M_KLASIFIKASI,KODE_KLASIFIKASI",
            "KATEGORI"=> "required|string|max:100",
            "DESKRIPSI"=> "required|string|max:1000",
            "START_DATE"=> "nullable|date",
            "END_DATE"=> "nullable|date",
            "CREATE_BY"=> "nullable|string|max:100"
        ]);

        $klasifikasi = M_klasifikasi::create($validated);
        return response()->json($klasifikasi,201);

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

        $validated = $request->validate([
            "KODE_KLASIFIKASI"=> "sometimes|string|max:100",
            "KATEGORI"=> "sometimes|string|max:100",
            "DESKRIPSI"=> "sometimes|string|max:1000",
            "START_DATE"=> "nullable|date",
            "END_DATE"=> "nullable|date",
            "CREATE_BY"=> "nullable|string|max:100"
        ]);

        $klasifikasi->update($validated);
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
}
