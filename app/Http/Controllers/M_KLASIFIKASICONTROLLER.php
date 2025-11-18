<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\M_klasifikasi;
use App\Exports\KlasifikasiExport;
use App\Exports\KlasifikasiExportTemplate;
use App\Imports\KlasifikasiImport;
use Maatwebsite\Excel\Facades\Excel;

class M_KLASIFIKASICONTROLLER extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $query = M_klasifikasi::query();

        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('KODE_KLASIFIKASI', 'like', '%' . $search . '%')
                ->orWhere('KATEGORI', 'like', '%' . $search . '%')
                ->orWhere('DESKRIPSI', 'like', '%' . $search . '%')
                ->orWhere('CREATE_BY', 'like', '%' . $search . '%');
            });
        }

        return $query->paginate($perPage);
    }

    public function all()
    {
            // Return semua data tanpa pagination untuk suggestion
            return M_klasifikasi::all();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            "KODE_KLASIFIKASI"=> "required|string|max:100|unique:M_KLASIFIKASI,KODE_KLASIFIKASI",
            "KATEGORI"=> "required|string|max:100",
            "DESKRIPSI"=> "required|string|max:1000",
            "START_DATE"=> "nullable|date",
            "END_DATE"=> "nullable|date",
            "CREATE_BY"=> "nullable|string|max:100"
        ]);

        $klasifikasi = M_klasifikasi::create($validated);
        return response()->json($klasifikasi,201);

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $klasifikasi = M_klasifikasi::findOrFail($id);  
        return response()->json($klasifikasi);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $klasifikasi = M_klasifikasi::findOrFail($id);  

        $validated = $request->validate([
            "KODE_KLASIFIKASI"=> "sometimes|string|max:100",
            "KATEGORI"=> "sometimes|string|max:100",
            "DESKRIPSI"=> "sometimes|string|max:1000",
            "START_DATE"=> "nullable|date",
            "END_DATE"=> "nullable|date",
            "CREATE_BY"=> "nullable|string|max:100"
        ]);

        $klasifikasi->update($validated);
        return response()->json($klasifikasi);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $klasifikasi = M_klasifikasi::findOrFail($id);
        $klasifikasi->delete();
        return response()->json(["message" => "Deleted succesfully"]);
    }

    public function exportExcel()
    {
        return Excel::download(new KlasifikasiExport, 'klasifikasi.xlsx');
    }

    public function exportTemplate()
    {
        return Excel::download(new KlasifikasiExportTemplate, 'template_import_klasifikasi.xlsx');
    }   

    public function importExcel(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv'  
        ]);

        Excel::import(new KlasifikasiImport, $request -> file('file'));
        
        return response()->json(['message' => 'Data berhasil diimport']);
    }

    
}
