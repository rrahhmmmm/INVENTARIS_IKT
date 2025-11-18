<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\M_indeks;
use App\Exports\IndeksExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\IndeksExportTemplate;
use App\Imports\IndeksImport;

class M_indekscontroller extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 10); 
        $query = M_indeks::query();
    
        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('NO_INDEKS', 'like', "%{$search}%")
                  ->orWhere('WILAYAH', 'like', "%{$search}%")
                  ->orWhere('NAMA_INDEKS', 'like', "%{$search}%");
            });
        }
    
        return $query->paginate($perPage);
    }

    public function all()
    {
            // Return semua data tanpa pagination untuk suggestion
            return M_indeks::all();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            "NO_INDEKS"=> "required|string|max:100|unique:M_INDEKS,NO_INDEKS",
            "WILAYAH"=> "nullable|string|max:100",
            "NAMA_INDEKS" => "nullable|string|max:250",
            "START_DATE"=> "nullable|date",
            "END_DATE"=> "nullable|date",
            "CREATE_BY"=> "nullable|string|max:100"
        ]);

        $indeks = M_indeks::create($validated);
        return response()->json($indeks,201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $indeks = M_indeks::findOrFail($id);
        return response()->json($indeks);   
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $indeks = M_indeks::findOrFail($id);

        $validated = $request->validate([
            "NO_INDEKS"=> "sometimes|string|max:100|unique:M_INDEKS,NO_INDEKS," . $id . ",ID_INDEKS",
            "WILAYAH"=> "nullable|string|max:100",
            "NAMA_INDEKS" => "nullable|string|max:250",
            "START_DATE"=> "nullable|date",
            "END_DATE"=> "nullable|date",
            "CREATE_BY"=> "nullable|string|max:100"
        ]);
        $indeks->update($validated);
        return response()->json($indeks);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $indeks = M_indeks::findOrFail($id);
        $indeks->delete();
        return response()->json(["message"=> "Deleted succesfully"]);
    }

    public function exportExcel()
    {
        return Excel::download(new IndeksExport, 'indeks.xlsx');
    }

    public function exportTemplate()
    {
        return Excel::download(new IndeksExportTemplate, 'indeks-template.xlsx');   
    }

    public function importExcel(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv'  
        ]);

        Excel::import(new IndeksImport, $request -> file('file'));
        
        return response()->json(['message' => 'Data berhasil diimport']);
    }

}
