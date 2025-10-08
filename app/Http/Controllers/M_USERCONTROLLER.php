<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\M_user;
use Illuminate\Support\Facades\Hash;
use App\Exports\UserExport;
use Maatwebsite\Excel\Facades\Excel;

class M_USERCONTROLLER extends Controller
{
    public function index(Request $request)
    {
        $query = M_user::with(['role', 'divisi', 'subdivisi']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('username', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('full_name', 'like', "%{$search}%");
            });
        }


        $users = $query->get();
        return response()->json($users);
    }

    public function show(string $id)
    {
        $user = M_user::with(['role','divisi','subdivisi'])->findOrFail($id);
    return response()->json(['data' => $user]);
    }

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

        if (isset($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        }

        $user->update($validated);

        return response()->json($user);
    }

    public function destroy(string $id)
    {
        $user = M_user::findOrFail($id);
        $user->delete();

        return response()->json(["message" => "Deleted successfully"]);
    }

    public function exportExcel()
    {
        return Excel::download(new UserExport, 'user.xlsx');
    }
}
