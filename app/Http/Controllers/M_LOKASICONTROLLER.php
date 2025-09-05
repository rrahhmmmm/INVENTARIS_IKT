<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\M_lokasi;

class M_LOKASICONTROLLER extends Controller
{
    public function index()
    {
        return M_lokasi::all();
    }

    // insert
    public function store(Request $request)
    {
        $validated = $request->validate([
            'NAMA_LOKASI'=>'required|string|max:20|unique:M_LOKASI,NAMA_LOKASI',
            'ALAMAT' => 'nullable|string|max:200',
            'create_by' => 'nullable|string|max:100'
        ]);

        $lokasi = M_lokasi::create($validated);
        return response()->json($lokasi,201);
    }

    // show by id
    public function show(string $id)
    {
        $lokasi = M_lokasi::findOrFail ($id);
        return response()->json($lokasi); 
    }

    //update
    public function update(Request $request, string $id)
    {
    $lokasi = M_lokasi::findOrFail ($id);

    $validated = $request->validate([
        'NAMA_LOKASI'=>'sometimes|string|max:20|unique:M_LOKASI,NAMA_LOKASI,' . $id. ',ID_LOKASI',
        'ALAMAT' => 'nullable|string|max:200',
        'create_by' => 'nullable|string|max:100'
    ]);
             $lokasi->update($validated);
             return response()->json($lokasi);
    }

    // delete
    public function destroy(string $id)
    {
        $lokasi = M_lokasi::findOrFail ($id);
        $lokasi->delete();
        return response()->json(['message' => 'Deleted successfully']);
    }
}
