<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\M_status;

class M_STATUSCONTROLLER extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return M_status::all();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            "nama_status"=> "required|string|max:20|unique:M_STATUS,nama_status",
            "keterangan"=> "nullable|string|max:200",
            'create_by' => 'nullable|string|max:100'
        ]);

        $status = M_status::create($validated);
        return response()->json($status,201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $status = M_status::find($id);
        return response()->json($status);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $status = M_status::findOrFail($id);

        $validated = $request->validate([
            "nama_status"=> "sometimes|string|max:20|",
            "keterangan"=> "nullable|string|max:200",
            'create_by' => 'nullable|string|max:100'

        ]);
        $status->update($validated);
        return response()->json($status);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $status = M_status::find($id);
        $status->delete();
        return response()->json(['message' => 'Deleted succesfully']);
    }
}
