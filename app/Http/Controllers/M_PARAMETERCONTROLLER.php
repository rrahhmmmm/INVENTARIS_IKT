<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\M_parameter;

class M_PARAMETERCONTROLLER extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = M_parameter::query();
    
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nilai_parameter', 'like', "%{$search}%")
                  ->orWhere('keterangan', 'like', "%{$search}%")
                  ->orWhere('create_by', 'like', "%{$search}%");
            });
        }
    
        return response()->json($query->get());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'Nilai_parameter' => 'required|string|max:100',
            'keterangan'     => 'nullable|string|max:200',
            'create_by'      => 'nullable|string|max:100'
        ]);

        $parameter = M_parameter::create($validated);

        return response()->json($parameter, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $parameter = M_parameter::findOrFail($id);
        return response()->json($parameter);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $parameter = M_parameter::findOrFail($id);

        $validated = $request->validate([
            'Nilai_parameter' => 'sometimes|string|max:100',
            'keterangan'     => 'nullable|string|max:200',
            'create_by'      => 'nullable|string|max:100'
        ]);

        $parameter->update($validated);

        return response()->json($parameter);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $parameter = M_parameter::findOrFail($id);
        $parameter->delete();

        return response()->json(['message' => 'Deleted successfully']);
    }
}
