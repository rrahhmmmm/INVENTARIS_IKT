<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\M_role;
use App\Exports\RoleExport;
use Maatwebsite\Excel\Facades\Excel;

class M_ROLECONTROLLER extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return M_role::all();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            "Nama_role"=> "required|string|max:100|unique:M_ROLE,Nama_role",
            "keterangan" => "nullable|string|max:100",
            'create_by' => 'nullable|string|max:100'
        ]);

        $role = M_role::create($validated);
        return response()->json($role,201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $role = M_role::findOrFail($id);
        return response()->json($role);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $role = M_role::findOrFail($id);

        $validated = $request->validate([
            "Nama_role"=> "sometimes|string|max:100",
            "keterangan" => "nullable|string|max:100",
            'create_by' => 'nullable|string|max:100'
        ]);

        $role->update($validated);
        return response()->json($role);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $role = M_role::findOrFail($id);
        $role->delete();
        return response()->json(['message' => 'Deleted succesfully']);
    }

    public function exportExcel()
    {
        return Excel::download(new RoleExport, 'role.xlsx');
    }
}
