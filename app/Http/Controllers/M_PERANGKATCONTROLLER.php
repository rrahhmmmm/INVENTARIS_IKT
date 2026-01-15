<?php

namespace App\Http\Controllers;

use App\Models\M_perangkat;
use Illuminate\Http\Request;

class M_PERANGKATCONTROLLER extends Controller
{
    /**
     * Display a listing of all perangkat (no pagination)
     */
    public function index(Request $request)
    {
        $query = M_perangkat::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('NAMA_PERANGKAT', 'like', '%' . $search . '%')
                    ->orWhere('KODE_PERANGKAT', 'like', '%' . $search . '%');
            });
        }

        return response()->json($query->where('STATUS', 1)->orderBy('ID_PERANGKAT', 'asc')->get());
    }

    /**
     * Display a paginated listing of perangkat
     */
    public function indexPaginated(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $query = M_perangkat::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('NAMA_PERANGKAT', 'like', '%' . $search . '%')
                    ->orWhere('KODE_PERANGKAT', 'like', '%' . $search . '%');
            });
        }

        return response()->json($query->where('STATUS', 1)->orderBy('ID_PERANGKAT', 'asc')->paginate($perPage));
    }

    /**
     * Store a newly created perangkat
     */
    public function store(Request $request)
    {
        $request->validate([
            'NAMA_PERANGKAT' => 'required|string|max:100',
            'KODE_PERANGKAT' => 'required|string|max:20|unique:M_PERANGKAT,KODE_PERANGKAT',
        ]);

        try {
            $data = [
                'NAMA_PERANGKAT' => $request->NAMA_PERANGKAT,
                'KODE_PERANGKAT' => strtoupper($request->KODE_PERANGKAT),
                'CREATE_BY' => $request->CREATE_BY ?? auth()->user()->username ?? 'system',
                'STATUS' => 1
            ];

            // Add param1-param16
            for ($i = 1; $i <= 16; $i++) {
                $paramKey = "param$i";
                if ($request->has($paramKey)) {
                    $data[$paramKey] = $request->input($paramKey);
                }
            }

            $perangkat = M_perangkat::create($data);

            return response()->json([
                'success' => true,
                'message' => 'Jenis perangkat berhasil ditambahkan',
                'data' => $perangkat
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan jenis perangkat: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified perangkat
     */
    public function show(string $id)
    {
        $perangkat = M_perangkat::findOrFail($id);
        return response()->json($perangkat);
    }

    /**
     * Update the specified perangkat
     */
    public function update(Request $request, string $id)
    {
        $perangkat = M_perangkat::findOrFail($id);

        $request->validate([
            'NAMA_PERANGKAT' => 'sometimes|string|max:100',
            'KODE_PERANGKAT' => 'sometimes|string|max:20|unique:M_PERANGKAT,KODE_PERANGKAT,' . $id . ',ID_PERANGKAT',
        ]);

        try {
            $data = [
                'NAMA_PERANGKAT' => $request->NAMA_PERANGKAT ?? $perangkat->NAMA_PERANGKAT,
                'KODE_PERANGKAT' => $request->KODE_PERANGKAT ? strtoupper($request->KODE_PERANGKAT) : $perangkat->KODE_PERANGKAT,
                'UPDATE_BY' => $request->UPDATE_BY ?? auth()->user()->username ?? 'system'
            ];

            // Update param1-param16
            for ($i = 1; $i <= 16; $i++) {
                $paramKey = "param$i";
                if ($request->has($paramKey)) {
                    $data[$paramKey] = $request->input($paramKey);
                }
            }

            $perangkat->update($data);

            return response()->json([
                'success' => true,
                'message' => 'Jenis perangkat berhasil diperbarui',
                'data' => $perangkat
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui jenis perangkat: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified perangkat (soft delete by setting STATUS = 99)
     */
    public function destroy(string $id)
    {
        $perangkat = M_perangkat::findOrFail($id);

        try {
            // Check if perangkat is being used in inventaris
            $usageCount = \App\Models\T_inventaris::where('ID_PERANGKAT', $id)->count();
            if ($usageCount > 0) {
                return response()->json([
                    'success' => false,
                    'message' => "Tidak dapat menghapus. Jenis perangkat ini digunakan oleh {$usageCount} data inventaris."
                ], 400);
            }

            // Soft delete
            $perangkat->update(['STATUS' => 99]);

            return response()->json([
                'success' => true,
                'message' => 'Jenis perangkat berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus jenis perangkat: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get field schema for a specific device type
     * Used by frontend to render dynamic forms and tables
     */
    public function getFieldSchema(string $id)
    {
        $perangkat = M_perangkat::findOrFail($id);

        return response()->json([
            'perangkat' => $perangkat,
            'schema' => $perangkat->getDynamicSchema(),
            'headers' => $perangkat->getDynamicHeaders()
        ]);
    }

    /**
     * Get all schemas for all device types
     * Useful for preloading all schemas at once
     */
    public function getAllSchemas()
    {
        $perangkatList = M_perangkat::where('STATUS', 1)->get();
        $schemas = [];

        foreach ($perangkatList as $perangkat) {
            $schemas[$perangkat->KODE_PERANGKAT] = [
                'perangkat' => $perangkat,
                'schema' => $perangkat->getDynamicSchema(),
                'headers' => $perangkat->getDynamicHeaders()
            ];
        }

        return response()->json($schemas);
    }
}
