<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Agreement;
use Illuminate\Support\Str;

class GenerateVerificationCodes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:generate-verification-codes';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate unique verification codes for existing agreements that do not have one.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Mencari perjanjian tanpa kode verifikasi...');

        // Cari semua perjanjian dimana verification_code masih NULL
        $agreementsWithoutCode = Agreement::whereNull('verification_code')->get();

        if ($agreementsWithoutCode->isEmpty()) {
            $this->info('Semua perjanjian sudah memiliki kode verifikasi. Tidak ada yang perlu dilakukan.');
            return 0;
        }

        $this->info('Menemukan ' . $agreementsWithoutCode->count() . ' perjanjian. Memulai proses pembuatan kode...');

        $progressBar = $this->output->createProgressBar($agreementsWithoutCode->count());
        $progressBar->start();

        foreach ($agreementsWithoutCode as $agreement) {
            $agreement->verification_code = Str::uuid()->toString();
            $agreement->save();
            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine();
        $this->info('Selesai! Semua perjanjian lama telah berhasil diperbarui dengan kode verifikasi unik.');

        return 0;
    }
}
