<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\M_indeks;

class M_indekscontroller extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return M_indeks::all();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            "NO_INDEKS"=> "required|string|max:100|unique:M_INDEKS,NO_INDEKS",
            "WILAYAH"=> "nullable|string|max:100",
            "NAMA_INDEKS" => "nullable|string|max:250",
            "START_DATE"=> "nullable|date",
            "END_DATE"=> "nullable|date",
            "CREATE_BY"=> "nullable|string|max:100"
        ]);

        $indeks = M_indeks::create($validated);
        return response()->json($indeks,201);
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

        $validated = $request->validate([
            "NO_INDEKS"=> "sometimes|string|max:100|uniqie:M_INDEKS,NO_INDEKS",
            "WILAYAH"=> "nullable|string|max:100",
            "NAMA_INDEKS" => "nullable|string|max:250",
            "START_DATE"=> "nullable|date",
            "END_DATE"=> "nullable|date",
            "CREATE_BY"=> "nullable|string|max:100"
        ]);
        $indeks->update($validated);
        return response()->json($indeks);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $indeks = M_indeks::findOrFail($id);
        $indeks->delete();
        return response()->json(["message"=> "Deleted succesfully"]);
    }
}
