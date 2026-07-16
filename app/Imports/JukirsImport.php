<?php

namespace App\Imports;

use App\Models\Jukir;
use App\Models\JukirHistory;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;

class JukirsImport implements
    ToCollection,
    WithHeadingRow,
    WithValidation,
    WithBatchInserts,
    WithChunkReading
{
    public int $rowCount = 0;

    /**
     * Proses setiap baris dari file Excel/CSV.
     */
    public function collection(Collection $rows)
    {
        $userId = Auth::id();

        foreach ($rows as $row) {
            $dataToStore = [
                'id_jukir'    => (string) $row['id_jukir'],
                'nama_jukir'  => (string) $row['nama_jukir'],
                'no_ktp'      => isset($row['no_ktp']) ? (string) $row['no_ktp'] : null,
                'is_active'   => false,
            ];

            $jukir = Jukir::create($dataToStore);

            JukirHistory::create([
                'jukir_id'    => $jukir->id,
                'user_id'     => $userId,
                'action'      => 'Create',
                'description' => 'Data jukir ditambahkan melalui Impor Data Massal (CSV/Excel).',
                'new_values'  => $dataToStore,
            ]);

            $this->rowCount++;
        }
    }

    /**
     * Aturan validasi untuk setiap baris.
     */
    public function rules(): array
    {
        return [
            'id_jukir'   => 'required|max:255',
            'nama_jukir' => 'required|max:255',
            'no_ktp'     => 'nullable|max:255',
        ];
    }

    /**
     * Cast kolom numerik ke string sebelum validasi.
     * Excel membaca NIK (no_ktp) sebagai angka, bukan string.
     */
    public function prepareForValidation($data, $index)
    {
        $data['id_jukir'] = isset($data['id_jukir']) ? (string) $data['id_jukir'] : null;
        $data['no_ktp']   = isset($data['no_ktp']) ? (string) $data['no_ktp'] : null;

        return $data;
    }

    /**
     * Pesan error kustom.
     */
    public function customValidationMessages()
    {
        return [
            'id_jukir.required'   => 'Kolom "id_jukir" wajib diisi.',
            'id_jukir.string'     => 'Kolom "id_jukir" harus berupa teks.',
            'nama_jukir.required' => 'Kolom "nama_jukir" wajib diisi.',
            'nama_jukir.string'   => 'Kolom "nama_jukir" harus berupa teks.',
        ];
    }

    public function batchSize(): int
    {
        return 500;
    }

    public function chunkSize(): int
    {
        return 500;
    }
}
