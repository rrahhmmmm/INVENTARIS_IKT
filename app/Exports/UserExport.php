<?php

namespace App\Exports;

use App\Models\M_user;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class UserExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    /**
     * Ambil data user dengan relasi
     */
    public function collection()
    {
        return M_user::with(['role', 'divisi', 'subdivisi'])->get();
    }

    /**
     * Header kolom Excel
     */
    public function headings(): array
    {
        return [
            'ID USER',
            'Username',
            'Email',
            'Full Name',
            'Divisi',
            'Subdivisi',
            'Role',
            'Created At'
        ];
    }

    /**
     * Mapping setiap row
     */
    public function map($user): array
    {
        return [
            $user->ID_USER,
            $user->username,
            $user->email,
            $user->full_name,
            $user->divisi->NAMA_DIVISI ?? '-',     // pastikan field di model sesuai
            $user->subdivisi->NAMA_SUBDIVISI ?? '-',
            $user->role->NAMA_ROLE ?? '-',
            $user->created_at ? $user->created_at->format('Y-m-d H:i:s') : '-'
        ];
    }
}
