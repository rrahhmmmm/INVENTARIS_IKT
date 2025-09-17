<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\M_user;
use Illuminate\Support\Facades\Hash;

class M_USERCONTROLLER extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
{
    $users = M_user::with(['role','divisi','subdivisi'])->get();
    return response()->json($users);
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
            "username"     => "sometimes|string|max:100|unique:M_USER,username,".$id.",ID_USER",
            "password"     => "sometimes|string|min:6",
            "email"        => "sometimes|email|unique:M_USER,email,".$id.",ID_USER",
            "full_name"    => "sometimes|string|max:255",
            "ID_DIVISI"    => "nullable|integer",
            "ID_SUBDIVISI" => "nullable|integer",
            "ID_ROLE"      => "nullable|integer",
        ]);

        // Kalau password diupdate, hash ulang
        if (isset($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        }

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

        return response()->json(["message" => "Deleted successfully"]);
    }
}
