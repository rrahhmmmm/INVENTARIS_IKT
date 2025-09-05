<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\M_model;

class M_MODELCONTROLLER extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return M_model::all();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            "NAMA_MODEL"=> "required|string|max:20|unique:M_MODEL,NAMA_MODEL",
            "KETERANGAN"=> "nullable|string|max:200",
            'create_by' => 'nullable|string|max:100'
        
        ]);

        $model = M_model::create($validated);
        return response()->json($model,201);
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $model = M_model::findOrFail($id);
        return response()->json($model);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $model = M_model::findOrFail($id);

        $validated = $request->validate([
            "NAMA_MODEL"=> "sometimes|string|max:20|",
            "KETERANGAN"=> "nullable|string|max:200",
            'create_by' => 'nullable|string|max:100'
        ]);
        $model->update($validated);
        return response()->json($model);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $model = M_model::findOrFail($id);
        $model->delete();
        return response()->json(['message' => 'Deleted succesfully']);
    }
}
