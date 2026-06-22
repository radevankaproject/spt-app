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
            
            // For field_coordinator, we might need a default active tab as well if they had stats, 
            // but the original code didn't have stats for them. 
            // Let's add simple logic for them just in case.
            if (!$activeTab) $activeTab = 'profile';
        } elseif ($user->role === 'leader') {
            $user->load('leader');
        } elseif (in_array($user->role, ['treasurer', 'staff_keu'])) {
            if (method_exists($user, 'treasurer')) {
                $user->load('treasurer');
            }
        }

        return view('profile.index', compact('user', 'stats', 'paginatedData', 'activeTab', 'search'));
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
