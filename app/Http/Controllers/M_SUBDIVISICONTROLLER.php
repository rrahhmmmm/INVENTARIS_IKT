<?php

namespace App\Http\Controllers;

use App\Models\M_subdivisi;
use Illuminate\Http\Request;

class M_SUBDIVISICONTROLLER extends Controller
{
    public function index()
    {
        return M_subdivisi::all();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            "ID_DIVISI"=> 'required|integer',
            'NAMA_SUBDIVISI'=> 'required|string|max:100|unique:M_SUBDIVISI,NAMA_SUBDIVISI',
            'CREATE_BY'=> 'nullable|string|max:100'
        ]);


        $subdivisi = M_subdivisi::create($validated);
        return response()->json($subdivisi);
    }

    //show by id
    public function show($id)
    {
        $subdivisi = M_subdivisi::findOrFail($id);
        return response()->json($subdivisi);
    }
    
    //update by id
    public function update(Request $request, $id)
    {
        $subdivisi = M_subdivisi::findOrFail($id);
        $validated = $request->validate([
            "ID_DIVISI"=> 'required|integer',
            'NAMA_SUBDIVISI'=> 'required|string|max:100|unique:M_SUBDIVISI,NAMA_SUBDIVISI',
            'CREATE_BY'=> 'nullable|string|max:100'
        ]);
        $subdivisi->update($validated);
        return response()->json($subdivisi);
    }

    //delete by id

    public function destroy($id)
    {
        $subdivisi = M_subdivisi::findOrFail($id);
        $subdivisi->delete();
        return response()->json(null,204);
    }
}
