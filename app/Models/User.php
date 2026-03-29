<?php
namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'role',
        'img',
        'is_active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
        ];
    }

    //Relasi ke Leader
    public function leader()
    {
        return $this->hasOne(Leader::class);
    }

    //relaso ke FieldCoordinator (Korlap)
    public function fieldCoordinator()
    {
        return $this->hasOne(FieldCoordinator::class);
    }

    // --- Start: Metode Helper Role ---

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isLeader(): bool
    {
        return $this->role === 'leader';
    }

    public function isFieldCoordinator(): bool
    {
        return $this->role === 'field_coordinator';
    }

    public function isStaffKeu(): bool
    {
        return $this->role === 'staff_keu';
    }

    public function isStaffPks(): bool
    {
        return $this->role === 'staff_pks';
    }

    public function hasRole(string $roleName): bool
    {
        return $this->role === $roleName;
    }

    // --- End: Metode Helper Role ---

}
