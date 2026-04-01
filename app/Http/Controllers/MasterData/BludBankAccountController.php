<?php

namespace App\Http\Controllers\MasterData;

use App\Http\Controllers\Controller;
use App\Models\BludBankAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class BludBankAccountController extends Controller
{
    /**
     * Menampilkan daftar semua rekening bank.
     */
    public function index()
    {
        $accounts = BludBankAccount::latest()->paginate(10);
        return view('masterdata.blud_bank_accounts.index', compact('accounts'));
    }

    /**
     * Menampilkan form untuk membuat rekening baru.
     */
    public function create()
    {
        abort_if(Auth::user()->role === 'leader', 403, 'Akses Ditolak! Pimpinan hanya memiliki akses Lihat (View-Only).');
        return view('masterdata.blud_bank_accounts.create');
    }

    /**
     * Menyimpan rekening baru ke database.
     */
    public function store(Request $request)
    {
        abort_if(Auth::user()->role === 'leader', 403, 'Akses Ditolak! Pimpinan hanya memiliki akses Lihat (View-Only).');

        $validatedData = $request->validate([
            'bank_name' => 'required|string|max:255',
            'account_number' => 'required|string|max:255|unique:blud_bank_accounts,account_number',
            'account_name' => 'required|string|max:255',
            'start_date' => 'required|date',
        ]);

        // Nonaktifkan semua rekening lain jika ini yang pertama atau jika ada perubahan
        if (BludBankAccount::count() > 0) {
            BludBankAccount::where('is_active', true)->update(['is_active' => false]);
        }

        BludBankAccount::create(array_merge($validatedData, ['is_active' => true]));

        return redirect()->route('admin.blud-bank-accounts.index')
            ->with('success', 'Rekening bank berhasil ditambahkan.');
    }

    /**
     * Menampilkan form untuk mengedit rekening.
     */
    public function edit(BludBankAccount $bludBankAccount)
    {
        abort_if(Auth::user()->role === 'leader', 403, 'Akses Ditolak! Pimpinan hanya memiliki akses Lihat (View-Only).');
        return view('masterdata.blud_bank_accounts.edit', ['account' => $bludBankAccount]);
    }

    /**
     * Mengupdate rekening di database.
     */
    public function update(Request $request, BludBankAccount $bludBankAccount)
    {
        abort_if(Auth::user()->role === 'leader', 403, 'Akses Ditolak! Pimpinan hanya memiliki akses Lihat (View-Only).');

        $validatedData = $request->validate([
            'bank_name' => 'required|string|max:255',
            'account_number' => ['required', 'string', 'max:255', Rule::unique('blud_bank_accounts')->ignore($bludBankAccount->id)],
            'account_name' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'is_active' => 'required|boolean',
        ]);

        // Jika rekening ini diaktifkan, pastikan yang lain nonaktif
        if ($validatedData['is_active']) {
            BludBankAccount::where('id', '!=', $bludBankAccount->id)
                ->where('is_active', true)
                ->update(['is_active' => false]);
        }

        $bludBankAccount->update($validatedData);

        return redirect()->route('admin.blud-bank-accounts.index')
            ->with('success', 'Rekening bank berhasil diperbarui.');
    }

    /**
     * Menghapus rekening (soft delete).
     */
    public function destroy(BludBankAccount $bludBankAccount)
    {
        $bludBankAccount->delete();
        return redirect()->route('admin.blud-bank-accounts.index')
            ->with('success', 'Rekening bank berhasil dihapus.');
    }
}
