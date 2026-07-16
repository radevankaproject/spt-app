<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdatePasswordRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use App\Models\FieldCoordinator;
use App\Models\RoadSection;
use App\Models\ParkingLocationHistory;
use App\Models\AgreementPdfHistory;
use App\Models\DepositTransaction;
use App\Models\Agreement;
use App\Models\Treasurer;
use App\Models\Leader;
use Illuminate\View\View;

class ProfileSettingController extends Controller
{
    /**
     * Menampilkan halaman Profil Saya beserta statistiknya.
     */
        public function index(Request $request): View
    {
        $user = $request->user();
        $stats = [];
        
        $activeTab = $request->get('tab');
        $search = $request->get('search');
        $paginatedData = null;
        $availableYears = collect();
        $selectedYear = null;
        $agreementsInYear = collect();
        $activeParkingLocationsCount = 0;
        $totalValidatedDeposit = 0;
        $totalAgreementsCount = 0;

        if (in_array($user->role, ['admin', 'staff_pks'])) {
            $stats['korlapCount'] = FieldCoordinator::where('last_updated_by', $user->id)->count();
            $stats['roadSectionCount'] = RoadSection::where('last_updated_by', $user->id)->count();
            $stats['agreementPdfCount'] = AgreementPdfHistory::where('generated_by_user_id', $user->id)->count();

            if (!$activeTab) $activeTab = 'korlap';

            if ($activeTab === 'korlap') {
                $query = FieldCoordinator::with('user')->where('last_updated_by', $user->id);
                if ($search) {
                    $query->whereHas('user', function($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    });
                }
                $paginatedData = $query->latest()->paginate(10)->withQueryString();
            } elseif ($activeTab === 'zona') {
                $query = RoadSection::where('last_updated_by', $user->id);
                if ($search) {
                    $query->where('name', 'like', "%{$search}%");
                }
                $paginatedData = $query->latest()->paginate(10)->withQueryString();
            } elseif ($activeTab === 'pks') {
                $query = AgreementPdfHistory::with('agreement.fieldCoordinator.user')->where('generated_by_user_id', $user->id);
                if ($search) {
                    $query->whereHas('agreement', function($q) use ($search) {
                        $q->where('agreement_number', 'like', "%{$search}%");
                    });
                }
                $paginatedData = $query->latest()->paginate(10)->withQueryString();
            }
            
        } elseif ($user->role === 'staff_keu') {
            $stats['validatedDepositsCount'] = DepositTransaction::where('validated_by_user_id', $user->id)->count();
            $stats['validatedDepositsAmount'] = DepositTransaction::where('validated_by_user_id', $user->id)->sum('amount');
            
            if (!$activeTab) $activeTab = 'setoran';
            
            if ($activeTab === 'setoran') {
                $query = DepositTransaction::with('agreement.fieldCoordinator.user')->where('validated_by_user_id', $user->id);
                if ($search) {
                    $query->where(function($q) use ($search) {
                        $q->where('referral_code', 'like', "%{$search}%")
                          ->orWhereHas('agreement', function($q2) use ($search) {
                              $q2->where('agreement_number', 'like', "%{$search}%");
                          });
                    });
                }
                $paginatedData = $query->latest('validation_date')->paginate(10)->withQueryString();
            }
            
        } elseif ($user->role === 'treasurer') {
            $treasurer = Treasurer::where('user_id', $user->id)->first();
            if ($treasurer) {
                $baseQuery = DepositTransaction::where('deposit_date', '>=', $treasurer->start_date);
                if ($treasurer->end_date) {
                    $baseQuery->where('deposit_date', '<=', $treasurer->end_date);
                }
                $baseQuery->where('is_validated', true);
                
                $stats['termDepositsCount'] = (clone $baseQuery)->count();
                $stats['termDepositsAmount'] = (clone $baseQuery)->sum('amount');
                
                if (!$activeTab) $activeTab = 'term_deposits';
                
                if ($activeTab === 'term_deposits') {
                    $query = clone $baseQuery;
                    $query->with('agreement.fieldCoordinator.user');
                    if ($search) {
                        $query->where(function($q) use ($search) {
                            $q->where('referral_code', 'like', "%{$search}%")
                              ->orWhereHas('agreement', function($q2) use ($search) {
                                  $q2->where('agreement_number', 'like', "%{$search}%");
                              });
                        });
                    }
                    $paginatedData = $query->latest('deposit_date')->paginate(10)->withQueryString();
                }
            } else {
                $stats['termDepositsCount'] = 0;
                $stats['termDepositsAmount'] = 0;
                $paginatedData = null;
            }
            
        } elseif ($user->role === 'leader') {
            $leader = Leader::where('user_id', $user->id)->first();
            if ($leader) {
                $stats['signedAgreementsCount'] = Agreement::where('leader_id', $leader->id)->count();
                
                if (!$activeTab) $activeTab = 'signed_agreements';
                
                if ($activeTab === 'signed_agreements') {
                    $query = Agreement::with('fieldCoordinator.user')->where('leader_id', $leader->id);
                    if ($search) {
                        $query->where('agreement_number', 'like', "%{$search}%")
                              ->orWhereHas('fieldCoordinator.user', function($q) use ($search) {
                                  $q->where('name', 'like', "%{$search}%");
                              });
                    }
                    $paginatedData = $query->latest('signed_date')->paginate(10)->withQueryString();
                }
            } else {
                $stats['signedAgreementsCount'] = 0;
                $paginatedData = null;
            }
        }

        // Relasi default untuk tampilan avatar dll
        if ($user->role === 'field_coordinator') {
            $user->load('fieldCoordinator');
            $fieldCoordinator = $user->fieldCoordinator;
            if ($fieldCoordinator) {
                $availableYears = \App\Models\Agreement::where('field_coordinator_id', $fieldCoordinator->id)
                    ->selectRaw('YEAR(start_date) as year')
                    ->distinct()
                    ->orderBy('year', 'desc')
                    ->pluck('year');

                if ($availableYears->isEmpty()) {
                    $availableYears = collect([now()->year]);
                }

                $selectedYear = $request->input('year', $availableYears->first());

                $agreementsQuery = \App\Models\Agreement::where('field_coordinator_id', $fieldCoordinator->id)
                    ->whereYear('start_date', $selectedYear);

                if ($search) {
                    $agreementsQuery->where('agreement_number', 'like', "%{$search}%");
                }

                $agreementsInYear = $agreementsQuery->clone()
                    ->with(['activeParkingLocations.roadSection'])
                    ->withSum(['depositTransactions as total_deposit' => function ($q) {
                        $q->where('is_validated', true);
                    }], 'amount')
                    ->orderByRaw("CASE WHEN status IN ('active', 'pending_renewal') THEN 0 ELSE 1 END")
                    ->orderBy('start_date', 'desc')
                    ->paginate(10)
                    ->withQueryString();

                $totalAgreementsCount = $agreementsQuery->count();

                $activeParkingLocationsCount = \App\Models\ParkingLocation::whereHas('agreements', function($q) use ($selectedYear, $fieldCoordinator) {
                    $q->where('agreements.field_coordinator_id', $fieldCoordinator->id)
                      ->whereYear('agreements.start_date', $selectedYear)
                      ->whereIn('agreements.status', ['active', 'pending_renewal', 'expired'])
                      ->where('agreement_parking_locations.status', 'active');
                })->count();

                $totalValidatedDeposit = $agreementsQuery->clone()
                    ->withSum(['depositTransactions as total_deposit' => function ($q) {
                        $q->where('is_validated', true);
                    }], 'amount')
                    ->get()
                    ->sum('total_deposit');
            }
        } elseif ($user->role === 'leader') {
            $user->load('leader');
        } elseif (in_array($user->role, ['treasurer', 'staff_keu'])) {
            if (method_exists($user, 'treasurer')) {
                $user->load('treasurer');
            }
        }

        if ($activeTab === 'aktivitas') {
            $query = \App\Models\UserActivity::where('user_id', $user->id);
            if ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('action', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%");
                });
            }
            $paginatedData = $query->latest()->paginate(10)->withQueryString();
        }

        return view('profile.index', compact(
            'user', 'stats', 'paginatedData', 'activeTab', 'search',
            'availableYears', 'selectedYear', 'agreementsInYear', 'activeParkingLocationsCount', 'totalValidatedDeposit', 'totalAgreementsCount'
        ));
    }

    /**
     * Menampilkan halaman pengaturan profil disesuaikan dengan Role.
     */
    public function edit(Request $request): View
    {
        $user = $request->user();

        // ✅ LOGIKA LOAD RELASI BERDASARKAN ROLE (Eager Loading)
        if ($user->role === 'field_coordinator') {
            $user->load('fieldCoordinator');
        } elseif ($user->role === 'leader') {
            $user->load('leader');
        } elseif ($user->role === 'treasurer' || $user->role === 'staff_keu') {
            // Asumsi model bendahara berelasi dengan user
            if (method_exists($user, 'treasurer')) {
                $user->load('treasurer');
            }
        }

        return view('profile.edit', [
            'user' => $user,
        ]);
    }

    /**
     * Memperbarui informasi profil pengguna (nama, email, foto).
     */
    public function updateProfile(Request $request): RedirectResponse
    {
        $user = $request->user();

        $validatedData = $request->validateWithBag('updateProfile', [
            'name' => ['required', 'string', 'max:255'],
            'username' => [
                'required', 'string', 'max:255', 'alpha_dash',
                Rule::unique('users')->ignore($user->id),
            ],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'employee_number' => ['nullable', 'string', 'max:50'],
            'img' => ['nullable', 'image', 'mimes:jpeg,png,jpg', 'max:10240'],
        ], [
            'username.unique' => 'Username ini sudah digunakan oleh pengguna lain.',
            'username.alpha_dash' => 'Username hanya boleh mengandung huruf, angka, strip, dan underscore.',
            'img.max' => 'Ukuran foto maksimal 10 MB.',
        ]);

        $user->fill($request->except('img'));

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        if ($request->hasFile('img')) {
            if ($user->img) {
                Storage::disk('public')->delete($user->img);
            }
            $path = $request->file('img')->store('avatars', 'public');
            $user->img = $path;
        }

        $user->save();

        \App\Models\UserActivity::create([
            'user_id' => $user->id,
            'action' => 'Update Profile',
            'description' => 'User memperbarui data profil.',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return Redirect::route('profile.settings')->with('status', 'profile-updated');
    }

    /**
     * Memperbarui kata sandi pengguna.
     */
    public function updatePassword(UpdatePasswordRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $request->user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        \App\Models\UserActivity::create([
            'user_id' => $request->user()->id,
            'action' => 'Update Password',
            'description' => 'User memperbarui kata sandi.',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return Redirect::route('profile.settings')->with('status', 'password-updated');
    }

    /**
     * Menghapus foto profil pengguna.
     */
    public function deleteImage(): JsonResponse
    {
        $user = Auth::user();

        if ($user->img) {
            Storage::disk('public')->delete($user->img);
            $user->img = null;
            $user->save();

            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false, 'message' => 'Tidak ada foto untuk dihapus.']);
    }
}
