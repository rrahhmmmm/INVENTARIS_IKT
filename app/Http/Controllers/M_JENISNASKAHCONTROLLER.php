<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\M_jenisnaskah;

class M_JENISNASKAHCONTROLLER extends Controller
{
    /**
     * Display a listing of the resource (with pagination + search).
     */
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 10);

        $query = M_jenisnaskah::query();

        // Search
        if ($request->has('search')) {
            $search = $request->input('search');

            $query->where(function ($q) use ($search) {
                $q->where('NAMA_JENIS', 'like', '%' . $search . '%')
                  ->orWhere('CREATE_BY', 'like', '%' . $search . '%');
            });
        }

        return $query->paginate($perPage);
    }

    /**
     * Get all data without pagination.
     */
    public function all()
    {
        return M_jenisnaskah::all();
    }

    /**
     * Store a newly created resource.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            "NAMA_JENIS" => "required|string|max:150",
            "CREATE_BY"  => "nullable|string|max:100"
        ]);

        $jenis = M_jenisnaskah::create($validated);

        return response()->json($jenis, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $jenis = M_jenisnaskah::findOrFail($id);
        return response()->json($jenis);
    }

    /**
     * Update the specified resource.
     */
    public function update(Request $request, string $id)
    {
        $jenis = M_jenisnaskah::findOrFail($id);

        $validated = $request->validate([
            "NAMA_JENIS" => "sometimes|string|max:150",
            "CREATE_BY"  => "nullable|string|max:100"
        ]);

        $jenis->update($validated);

        return response()->json($jenis);
    }

    /**
     * Remove the specified resource.
     */
    public function destroy(string $id)
    {
        $jenis = M_jenisnaskah::findOrFail($id);
        $jenis->delete();

        return response()->json(['message' => 'Deleted successfully']);
    }
}
