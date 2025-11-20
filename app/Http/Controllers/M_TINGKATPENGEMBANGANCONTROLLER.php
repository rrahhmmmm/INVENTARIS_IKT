<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\M_tingkatpengembangan;

class M_TINGKATPENGEMBANGANCONTROLLER extends Controller
{
    /**
     * Display a listing of the resource (with pagination + search).
     */
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 10);

        $query = M_tingkatpengembangan::query();

        // Search
        if ($request->has('search')) {
            $search = $request->input('search');

            $query->where(function ($q) use ($search) {
                $q->where('NAMA_PENGEMBANGAN', 'like', '%' . $search . '%')
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
        return M_tingkatpengembangan::all();
    }

    /**
     * Store a newly created resource.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            "NAMA_PENGEMBANGAN" => "required|string|max:150",
            "CREATE_BY"         => "nullable|string|max:100"
        ]);

        $data = M_tingkatpengembangan::create($validated);

        return response()->json($data, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $data = M_tingkatpengembangan::findOrFail($id);
        return response()->json($data);
    }

    /**
     * Update the specified resource.
     */
    public function update(Request $request, string $id)
    {
        $data = M_tingkatpengembangan::findOrFail($id);

        $validated = $request->validate([
            "NAMA_PENGEMBANGAN" => "sometimes|string|max:150",
            "CREATE_BY"         => "nullable|string|max:100"
        ]);

        $data->update($validated);

        return response()->json($data);
    }

    /**
     * Remove the specified resource.
     */
    public function destroy(string $id)
    {
        $data = M_tingkatpengembangan::findOrFail($id);
        $data->delete();

        return response()->json(['message' => 'Deleted successfully']);
    }
}
