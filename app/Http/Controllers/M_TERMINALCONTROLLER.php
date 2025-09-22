<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\M_terminal;

class M_TERMINALCONTROLLER extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
{
    $query = M_terminal::query();

    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function ($q) use ($search) {
            $q->where('KODE_TERMINAL', 'like', '%' . $search . '%')
              ->orWhere('NAMA_TERMINAL', 'like', '%' . $search . '%')
              ->orWhere('LOKASI', 'like', '%' . $search . '%')
              ->orWhere('CREATE_BY', 'like', '%' . $search . '%');
        });
    }

    return $query->get();
}

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'KODE_TERMINAL' => 'required|string|max:50|unique:M_TERMINAL,KODE_TERMINAL',
            'NAMA_TERMINAL' => 'required|string|max:100',
            'LOKASI'        => 'nullable|string|max:255',
            'CREATE_BY'     => 'nullable|string|max:100'
        ]);
    
        $terminal = M_terminal::create($validated);
    
        return response()->json($terminal, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $terminal = M_terminal::findOrFail($id);
        return response()->json($terminal);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        
    $terminal = M_terminal::findOrFail($id);

    $validated = $request->validate([
        'KODE_TERMINAL' => 'sometimes|string|max:50|unique:M_TERMINAL,KODE_TERMINAL,' . $id . ',ID_TERMINAL',
        'NAMA_TERMINAL' => 'sometimes|string|max:100',
        'LOKASI'        => 'nullable|string|max:255',
        'CREATE_BY'     => 'nullable|string|max:100'
    ]);

    $terminal->update($validated);

    return response()->json($terminal);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $terminal = M_terminal::findOrFail($id);
        $terminal->delete();
    
        return response()->json(['message' => 'Deleted successfully']);
    }
}
