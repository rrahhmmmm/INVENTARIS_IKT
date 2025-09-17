<?php

namespace App\Http\Controllers;

use App\Models\M_user;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    // REGISTER
    public function register(Request $request)
    {
        $request->validate([
            'username' => 'required|unique:M_USER,username',
            'email'    => 'required|email|unique:M_USER,email',
            'password' => 'required|min:6',
        ]);

        $user = M_user::create([
            'username' => $request->username,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'full_name'=> $request->full_name ?? null,
            'ID_DIVISI'=> $request->ID_DIVISI ?? null,
            'ID_SUBDIVISI'=> $request->ID_SUBDIVISI ?? null,
            'ID_ROLE'  => $request->ID_ROLE ?? null,
        ]);

        $token = $user->createToken('api_token')->plainTextToken;

        return response()->json([
            'message' => 'Registrasi berhasil',
            'user'    => $user,
            'token'   => $token,
        ]);
    }

    // LOGIN
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        $user = M_user::where('username', $request->username)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Username atau password salah',
            ], 401);
        }

        // Hapus token lama (opsional)
        $user->tokens()->delete();

        $token = $user->createToken('api_token')->plainTextToken;

        return response()->json([
            'message' => 'Login berhasil',
            'user'    => $user,
            'token'   => $token,
        ]);
    }

    // LOGOUT
    public function logout(Request $request)
    {
       
        $request->user()->tokens()->delete();
        

        return response()->json([
            'message' => 'Logout berhasil',
        ]);
    }

    // GET USER LOGIN
    public function me(Request $request)
    {
        return response()->json($request->user());
    }

    
}
