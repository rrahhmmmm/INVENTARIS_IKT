<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\M_kondisi;

class M_KONDISICONTROLLER extends Controller
{
    /**
     * Display a listing of the resource (with pagination + search).
     */
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 10);

        $query = M_kondisi::query();

        // Search
        if ($request->has('search')) {
            $search = $request->input('search');

            $query->where(function ($q) use ($search) {
                $q->where('NAMA_KONDISI', 'like', '%' . $search . '%')
                  ->orWhere('CREATE_BY', 'like', '%' . $search . '%')
                  ->orWhere('UPDATE_BY', 'like', '%' . $search . '%');
            });
        }

        return $query->paginate($perPage);
    }

    /**
     * Get all data without pagination.
     */
    public function all()
    {
        return M_kondisi::all();
    }

    /**
     * Store a newly created resource.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            "NAMA_KONDISI" => "required|string|max:150",
            "CREATE_BY"    => "nullable|string|max:100",
            "UPDATE_BY"    => "nullable|string|max:100",
        ]);

        $kondisi = M_kondisi::create($validated);

        return response()->json($kondisi, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $kondisi = M_kondisi::findOrFail($id);
        return response()->json($kondisi);
    }

    /**
     * Update the specified resource.
     */
    public function update(Request $request, string $id)
    {
        $kondisi = M_kondisi::findOrFail($id);

        $validated = $request->validate([
            "NAMA_KONDISI" => "sometimes|string|max:150",
            "CREATE_BY"    => "nullable|string|max:100",
            "UPDATE_BY"    => "nullable|string|max:100",
        ]);

        $kondisi->update($validated);

        return response()->json($kondisi);
    }

    /**
     * Remove the specified resource.
     */
    public function destroy(string $id)
    {
        $kondisi = M_kondisi::findOrFail($id);
        $kondisi->delete();

        return response()->json(['message' => 'Deleted successfully']);
    }
}
