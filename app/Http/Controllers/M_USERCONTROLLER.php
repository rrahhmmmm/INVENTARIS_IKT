<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\M_user;

class M_USERCONTROLLER extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return M_user::all();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'username' => 'required|string|max:100',
            'password'=> 'required|string|max:250',
            'email'=> 'nullable|string|max:150',
            'full_name'=> 'nullable|string|max:150',
            'ID_DIVISI'=> 'required|integer',
            'ID_SUBDIVISI'=> 'required|integer',
            'ID_ROLE'=> 'required|integer',
            'create_by'=> 'nullable|string|max:150'
        ]);

        $user = M_user::create($validated);
        return response()->json($user,201); 
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $user = M_user::findOrFail($id);
        return response()->json($user);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $user = M_user::findOrFail($id);

        $validated = $request->validate([
            'username' => 'sometimes|string|max:100',
            'password'=> 'sometimes|string|max:250',
            'email'=> 'nullable|string|max:150',
            'full_name'=> 'nullable|string|max:150',
            'ID_DIVISI'=> 'sometimes|integer',
            'ID_SUBDIVISI'=> 'sometimes|integer',
            'ID_ROLE'=> 'sometimes|integer',
            'create_by'=> 'nullable|string|max:150'
        ]);

        $user->update($validated);
        return response()->json($user);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = M_user::findOrFail($id);
        $user->delete();
        return response()->json(['message'=> 'Deleted successfully']);
    }
}
