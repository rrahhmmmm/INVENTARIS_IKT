<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\T_arsip;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\QueryException;

class T_ARSIPCONTROLLER extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = T_arsip::with(['divisi', 'subdivisi']);

        if ($user->ID_ROLE != 1) { 
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

        $perPage = $request->input('per_page', 10);
        return response()->json($query->orderBy('ID_ARSIP', 'desc')->paginate($perPage));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
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
                'NO_NOTA_DINAS'         => 'nullable|string|max:100|unique:t_arsip,NO_NOTA_DINAS',
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
            ], [
                // Custom error messages
                'NO_NOTA_DINAS.unique' => 'Nomor Nota Dinas sudah digunakan',
                'NO_INDEKS.required' => 'No Indeks wajib diisi',
                'NO_BERKAS.required' => 'No Berkas wajib diisi',
                'JUDUL_BERKAS.required' => 'Judul Berkas wajib diisi',
                'FILE.mimes' => 'File harus berformat: pdf, doc, docx, jpg, jpeg, atau png',
                'FILE.max' => 'Ukuran file maksimal 20MB',
            ]);

            // Handle file upload
            if ($request->hasFile('FILE')) {
                $file = $request->file('FILE');
                $path = $file->store('arsip_files', 'public');
                $validated['FILE'] = 'storage/' . $path;
            }

            $arsip = T_arsip::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Data berhasil disimpan',
                'data' => $arsip
            ], 201);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->errors()
            ], 422);

        } catch (QueryException $e) {
            // Handle database errors (duplicate entry, foreign key, etc)
            $errorCode = $e->errorInfo[1];
            
            if ($errorCode == 1062) { // Duplicate entry
                return response()->json([
                    'success' => false,
                    'message' => 'Data duplikat ditemukan',
                    'errors' => ['NO_NOTA_DINAS' => ['Nomor Nota Dinas sudah digunakan']]
                ], 422);
            }

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan database',
                'errors' => ['database' => ['Gagal menyimpan data ke database']]
            ], 500);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan sistem',
                'errors' => ['system' => [$e->getMessage()]]
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $arsip = T_arsip::findOrFail($id);
            return response()->json([
                'success' => true,
                'data' => $arsip
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukan'
            ], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            $arsip = T_arsip::findOrFail($id);

            $validated = $request->validate([
                'NO_INDEKS'             => 'required|string|max:100',
                'NO_BERKAS'             => 'required|string|max:100',
                'JUDUL_BERKAS'          => 'required|string|max:255',
                'NO_ISI_BERKAS'         => 'nullable|string|max:255',
                'JENIS_ARSIP'           => 'nullable|string|max:100',
                'KODE_KLASIFIKASI'      => 'nullable|string|max:100',
                'NO_NOTA_DINAS'         => 'nullable|string|max:100|unique:t_arsip,NO_NOTA_DINAS,' . $id . ',ID_ARSIP',
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
            ], [
                'NO_NOTA_DINAS.unique' => 'Nomor Nota Dinas sudah digunakan',
                'NO_INDEKS.required' => 'No Indeks wajib diisi',
                'NO_BERKAS.required' => 'No Berkas wajib diisi',
                'JUDUL_BERKAS.required' => 'Judul Berkas wajib diisi',
                'FILE.mimes' => 'File harus berformat: pdf, doc, docx, jpg, jpeg, atau png',
                'FILE.max' => 'Ukuran file maksimal 20MB',
            ]);

            // Handle file upload
            if ($request->hasFile('FILE')) {
                if ($arsip->FILE && file_exists(public_path($arsip->FILE))) {
                    unlink(public_path($arsip->FILE));
                }
                $file = $request->file('FILE');
                $path = $file->store('arsip_files', 'public');
                $validated['FILE'] = 'storage/' . $path;
            }

            $validated['UPDATE_BY'] = Auth::user()->username ?? '-';
            $arsip->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Data berhasil diperbarui',
                'data' => $arsip
            ], 200);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->errors()
            ], 422);

        } catch (QueryException $e) {
            $errorCode = $e->errorInfo[1];
            
            if ($errorCode == 1062) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data duplikat ditemukan',
                    'errors' => ['NO_NOTA_DINAS' => ['Nomor Nota Dinas sudah digunakan']]
                ], 422);
            }

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan database',
                'errors' => ['database' => ['Gagal memperbarui data']]
            ], 500);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan sistem',
                'errors' => ['system' => [$e->getMessage()]]
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $arsip = T_arsip::findOrFail($id);
            
            // Hapus file jika ada
            if ($arsip->FILE && file_exists(public_path($arsip->FILE))) {
                unlink(public_path($arsip->FILE));
            }
            
            $arsip->delete();

            return response()->json([
                'success' => true,
                'message' => 'Data berhasil dihapus'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus data',
                'errors' => ['system' => [$e->getMessage()]]
            ], 500);
        }
    }

    /**
     * Get overdue archives
     */
    public function overdue(Request $request)
    {
        try {
            $user = Auth::user();
            $today = now()->format('Y-m-d');

            $query = T_arsip::with(['divisi', 'subdivisi'])
                ->whereDate('TANGGAL_RETENSI', '<', $today)
                ->where('KETERANGAN', 'AKTIF');

            if ($user->ID_ROLE != 1) {
                $query->where('ID_DIVISI', $user->ID_DIVISI);
            }

            if ($request->filled('divisi_id')) {
                $query->where('ID_DIVISI', $request->divisi_id);
            }

            $arsip = $query->orderBy('ID_ARSIP', 'desc')->get();

            return response()->json([
                'success' => true,
                'data' => $arsip
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat notifikasi',
                'errors' => ['system' => [$e->getMessage()]]
            ], 500);
        }
    }

    public function checkNotaDinas(Request $request)
    {
        $notaDinas = $request->query('no_nota_dinas');
        
        $exists = T_arsip::where('NO_NOTA_DINAS', $notaDinas)->exists();
        
        return response()->json([
            'exists' => $exists,
            'no_nota_dinas' => $notaDinas
        ]);
    }
}

// // ❌ NO_NOTA_DINAS sudah digunakan (duplicate)
// ❌ Field required yang kosong (NO_INDEKS, NO_BERKAS, JUDUL_BERKAS)
// ❌ Format file salah (harus PDF, DOC, DOCX, JPG, JPEG, PNG)
// ❌ Ukuran file terlalu besar (max 20MB)
// ❌ Error database lainnya
// ❌ Error sistem umum