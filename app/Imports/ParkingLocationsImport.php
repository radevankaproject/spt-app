<?php
namespace App\Imports;

use App\Models\ParkingLocation;
// use Illuminate\Contracts\Queue\ShouldQueue; // Untuk impor di background
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class ParkingLocationsImport implements
ToCollection,
WithHeadingRow,   // Baris pertama adalah header (name, daily_deposit, dll)
WithValidation,   // Untuk memvalidasi setiap baris
WithBatchInserts, // Impor data secara batch
WithChunkReading  // Baca file per bagian (chunks)
// ShouldQueue

{
    protected int $roadSectionId;

    /**
     * Kita akan 'menyuntikkan' ID ruas jalan dari controller ke class ini.
     */
    public function __construct(int $roadSectionId)
    {
        $this->roadSectionId = $roadSectionId;
    }

    /**
     * Proses setiap baris dari file Excel/CSV.
     *
     * @param Collection $rows
     */
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            ParkingLocation::create([
                'road_section_id' => $this->roadSectionId, // Diambil dari constructor
                'name'            => $row['name'],
                'daily_deposit'   => $row['daily_deposit'],
                'latitude'        => $row['latitude'] ?? null,
                'longitude'       => $row['longitude'] ?? null,
                'status'          => 'tersedia', // Status default saat impor
            ]);
        }
    }

    /**
     * Tentukan aturan validasi untuk setiap baris.
     * Jika gagal, baris ini akan dilewati.
     */
    public function rules(): array
    {
        return [
            'name'          => 'required|string|max:255',
            'daily_deposit' => 'required|numeric|min:0',
            'latitude'      => 'nullable|numeric|between:-90,90',
            'longitude'     => 'nullable|numeric|between:-180,180',
        ];
    }

    /**
     * Tentukan pesan error kustom (opsional tapi bagus).
     */
    public function customValidationMessages()
    {
        return [
            'name.required'          => 'Kolom "name" wajib diisi.',
            'daily_deposit.required' => 'Kolom "daily_deposit" wajib diisi.',
            'daily_deposit.numeric'  => 'Kolom "daily_deposit" harus berupa angka.',
        ];
    }

    /**
     * Tentukan ukuran batch untuk performa.
     */
    public function batchSize(): int
    {
        return 500; // Impor 500 data sekaligus
    }

    /**
     * Tentukan ukuran chunk untuk membaca file.
     */
    public function chunkSize(): int
    {
        return 500; // Baca 500 baris file sekaligus
    }
}
