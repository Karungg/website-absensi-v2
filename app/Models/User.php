<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements FilamentUser
{
    use HasFactory, Notifiable, HasUuids, HasRoles;

    public $incrementing = false;
    protected $keyType = 'string';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nik',
        'nip',
        'name',
        'email',
        'password',
        'place_of_birth',
        'date_of_birth',
        'phone',
        'address',
        'leave_allowance',
        'sick_allowance',
        'give_birth_allowance',
        'date_of_entry',
        'mutation_date',
        'lod_start',
        'lod_mutation',
        'lod_stop',
        'profile_picture',
        'signature',
        'position_id',
        'division_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
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
            'password' => 'hashed',
            'date_of_birth' => 'date',
            'mutation_date' => 'date',
            'date_of_entry' => 'date'
        ];
    }

    public function requests(): HasMany
    {
        return $this->hasMany(Request::class);
    }

    public function position(): BelongsTo
    {
        return $this->belongsTo(Position::class);
    }

    public function division(): BelongsTo
    {
        return $this->belongsTo(Division::class);
    }

    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }

    public function isDirector(): bool
    {
        return $this->hasRole('director');
    }

    public function isResource(): bool
    {
        return $this->hasRole('resource');
    }

    public function isEmployee(): bool
    {
        return $this->hasRole('employee');
    }

    public function isHeadOfDivision(): bool
    {
        return $this->hasRole('headOfDivision');
    }

    public function isAdminDirector(): bool
    {
        return $this->hasAnyRole(['admin', 'director']);
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return true;
    }
}
