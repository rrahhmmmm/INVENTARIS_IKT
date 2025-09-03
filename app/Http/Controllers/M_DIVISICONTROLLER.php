<?php

namespace App\Http\Controllers;

use App\Models\M_divisi;
use Illuminate\Http\Request;

class M_DIVISICONTROLLER extends Controller
{
    public function index()
    {
        return M_divisi::all();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'NAMA_DIVISI' => 'required|string|max:100|unique:M_DIVISI,NAMA_DIVISI',
            'CREATE_BY'     => 'nullable|string|max:100'
        ]);

        $divisi = M_divisi::create($validated);
        return response()->json($divisi);
    }

    public function show(string $id)
    {
        $divisi = M_divisi::findOrFail($id);
        return response()->json($divisi);
    }

    public function update(Request $request, string $id)
    {
        $divisi = M_divisi::findOrFail($id);
        $validated = $request->validate([
            'NAMA_DIVISI' => 'sometimes|string|max:100|unique:M_DIVISI,NAMA_DIVISI,' . $id . ',ID_DIVISI',
            'CREATE_BY'     => 'nullable|string|max:100'
        ]);

        $divisi->update($validated);
        return response()->json($divisi);
    }

    public function destroy(string $id)
    {
        $divisi = M_divisi::findOrFail($id);
        $divisi->delete();
        return response()->json(null, 204);
    }
}
