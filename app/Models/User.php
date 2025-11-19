<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
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
        'email',
        'password',
        'auth0_id',
        'avatar',
        'phone',
        'department',
        'immediate_boss_id',
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
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Roles del usuario
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class);
    }

    /**
     * Permisos directos del usuario
     */
    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class);
    }

    /**
     * Jefe inmediato
     */
    public function immediateBoss(): BelongsTo
    {
        return $this->belongsTo(User::class, 'immediate_boss_id');
    }

    /**
     * Subordinados directos
     */
    public function subordinates(): HasMany
    {
        return $this->hasMany(User::class, 'immediate_boss_id');
    }

    /**
     * Licencia de conducir del usuario
     */
    public function driverLicense(): HasOne
    {
        return $this->hasOne(DriverLicense::class);
    }

    /**
     * Tickets creados por el usuario
     */
    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class);
    }

    /**
     * Tickets donde el usuario es despachador
     */
    public function dispatchedTickets(): HasMany
    {
        return $this->hasMany(Ticket::class, 'dispatcher_id');
    }

    /**
     * Tickets aprobados por el usuario
     */
    public function approvedTickets(): HasMany
    {
        return $this->hasMany(Ticket::class, 'approved_by');
    }

    /**
     * Verificar si el usuario tiene un rol específico
     */
    public function hasRole(string $role): bool
    {
        return $this->roles()->where('name', $role)->exists();
    }

    /**
     * Verificar si el usuario tiene un permiso específico
     */
    public function hasPermission(string $permission): bool
    {
        // Verificar permiso directo
        if ($this->permissions()->where('name', $permission)->exists()) {
            return true;
        }

        // Verificar permiso a través de roles
        return $this->roles()
            ->whereHas('permissions', function ($query) use ($permission) {
                $query->where('name', $permission);
            })
            ->exists();
    }

    /**
     * Verificar si es encargado
     */
    public function isEncargado(): bool
    {
        return $this->hasRole('encargado');
    }

    /**
     * Verificar si es despachador
     */
    public function isDespachador(): bool
    {
        return $this->hasRole('despachador');
    }

    /**
     * Verificar si es solo usuario
     */
    public function isUsuario(): bool
    {
        return $this->hasRole('usuario') && !$this->isEncargado() && !$this->isDespachador();
    }
}

