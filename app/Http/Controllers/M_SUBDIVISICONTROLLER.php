<?php
namespace App\Http\Controllers;

use App\Models\M_subdivisi;
use App\Models\M_divisi;
use Illuminate\Http\Request;

class M_SUBDIVISICONTROLLER extends Controller
{
    public function index()
{
    $subdivisi = M_subdivisi::with('divisi')->get();
    return response()->json($subdivisi);
}

    public function getByDivisi($id_divisi)
    {
        $subdivisi = M_subdivisi::where('ID_DIVISI', $id_divisi)->get();
        return response()->json($subdivisi);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'ID_DIVISI'      => 'required|integer|exists:M_DIVISI,ID_DIVISI',
            'NAMA_SUBDIVISI' => 'required|string|max:100|unique:M_SUBDIVISI,NAMA_SUBDIVISI',
            'CREATE_BY'      => 'nullable|string|max:100'
        ]);

        $subdivisi = M_subdivisi::create($validated);
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

        $validated = $request->validate([
            'ID_DIVISI'      => 'required|integer|exists:M_DIVISI,ID_DIVISI',
            // ignore current record pada unique validation (ID_SUBDIVISI adalah primary key Anda)
            'NAMA_SUBDIVISI' => 'required|string|max:100|unique:M_SUBDIVISI,NAMA_SUBDIVISI,'.$id.',ID_SUBDIVISI',
            'CREATE_BY'      => 'nullable|string|max:100'
        ]);

        $subdivisi->update($validated);
        $subdivisi->load('divisi');
        return response()->json($subdivisi);
    }

    public function destroy($id)
    {
        $subdivisi = M_subdivisi::findOrFail($id);
        $subdivisi->delete();
        return response()->json(null, 204);
    }
}
