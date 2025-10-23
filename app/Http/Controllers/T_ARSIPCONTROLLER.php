<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\T_arsip;
use Illuminate\Support\Facades\Auth;

class T_ARSIPCONTROLLER extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = T_arsip::with(['divisi', 'subdivisi']);

        if ($user->ID_ROLE != 1) { // misal role 1 = admin
            $query->where('ID_DIVISI', $user->ID_DIVISI);
        }

        if ($request->filled('divisi_id')) {
            $query->where('ID_DIVISI', $request->divisi_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('NO_INDEKS', 'like', '%' . $search . '%')
                  ->orWhere('NO_BERKAS', 'like', '%' . $search . '%')
                  ->orWhere('JUDUL_BERKAS', 'like', '%' . $search . '%')
                  ->orWhere('PERIHAL', 'like', '%' . $search . '%')
                  ->orWhere('CREATE_BY', 'like', '%' . $search . '%');
            });
        }

        return response()->json($query->orderBy('ID_ARSIP', 'desc')->get());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'ID_DIVISI'             => 'required|integer',
            'ID_SUBDIVISI'          => 'required|integer',
            'NO_INDEKS'             => 'required|string|max:100',
            'NO_BERKAS'             => 'required|string|max:100',
            'JUDUL_BERKAS'          => 'required|string|max:255',
            'NO_ISI_BERKAS'         => 'nullable|string|max:255',
            'JENIS_ARSIP'           => 'nullable|string|max:100',
            'KODE_KLASIFIKASI'      => 'nullable|string|max:100',
            'NO_NOTA_DINAS'         => 'nullable|string|max:100',
            'TANGGAL_BERKAS'        => 'nullable|date',
            'PERIHAL'               => 'nullable|string|max:255',
            'TINGKAT_PENGEMBANGAN'  => 'nullable|string|max:100',
            'KONDISI'               => 'nullable|string|max:100',
            'RAK_BAK_URUTAN'        => 'nullable|string|max:100',
            'KETERANGAN_SIMPAN'     => 'nullable|string|max:255',
            'TIPE_RETENSI'          => 'nullable|string|max:50',
            'TANGGAL_RETENSI'       => 'nullable|date',
            'KETERANGAN'            => 'nullable|string|max:255',
            'STATUS'                => 'nullable|string|max:20',
            'param1'                => 'nullable|string|max:255',
            'param2'                => 'nullable|string|max:255',
            'param3'                => 'nullable|string|max:255',
            'FILE'                  => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:20480',
            'CREATE_BY'             => 'nullable|string|max:100',
        ]);

        // Handle file upload
        if ($request->hasFile('FILE')) {
            $file = $request->file('FILE');
            $path = $file->store('arsip_files', 'public');
            $validated['FILE'] = 'storage/' . $path; // simpan path publik
        }

        $arsip = T_arsip::create($validated);

        return response()->json($arsip, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $arsip = T_arsip::findOrFail($id);
        return response()->json($arsip);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $arsip = T_arsip::findOrFail($id);

        $validated = $request->validate([
            'ID_DIVISI'             => 'required|integer',
            'ID_SUBDIVISI'          => 'required|integer',
            'NO_INDEKS'             => 'required|string|max:100',
            'NO_BERKAS'             => 'required|string|max:100',
            'JUDUL_BERKAS'          => 'required|string|max:255',
            'NO_ISI_BERKAS'         => 'nullable|string|max:255',
            'JENIS_ARSIP'           => 'nullable|string|max:100',
            'KODE_KLASIFIKASI'      => 'nullable|string|max:100',
            'NO_NOTA_DINAS'         => 'nullable|string|max:100',
            'TANGGAL_BERKAS'        => 'nullable|date',
            'PERIHAL'               => 'nullable|string|max:255',
            'TINGKAT_PENGEMBANGAN'  => 'nullable|string|max:100',
            'KONDISI'               => 'nullable|string|max:100',
            'RAK_BAK_URUTAN'        => 'nullable|string|max:100',
            'KETERANGAN_SIMPAN'     => 'nullable|string|max:255',
            'TIPE_RETENSI'          => 'nullable|string|max:50',
            'TANGGAL_RETENSI'       => 'nullable|date',
            'KETERANGAN'            => 'nullable|string|max:255',
            'STATUS'                => 'nullable|string|max:20',
            'param1'                => 'nullable|string|max:255',
            'param2'                => 'nullable|string|max:255',
            'param3'                => 'nullable|string|max:255',
            'FILE'                  => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:20480',
            'CREATE_BY'             => 'nullable|string|max:100',
        ]);

        // Jika ada file baru
        if ($request->hasFile('FILE')) {
            // Hapus file lama jika ada
            if ($arsip->FILE && file_exists(public_path($arsip->FILE))) {
                unlink(public_path($arsip->FILE));
            }

            // Simpan file baru
            $file = $request->file('FILE');
            $path = $file->store('arsip_files', 'public');
            $validated['FILE'] = 'storage/' . $path;
        }

        $arsip->update($validated);

        return response()->json($arsip, 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $arsip = T_arsip::findOrFail($id);
        $arsip->delete();

        return response()->json(['message' => 'Deleted successfully']);
    }
}
