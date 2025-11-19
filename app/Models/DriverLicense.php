<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class DriverLicense extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'license_number',
        'license_type',
        'issue_date',
        'expiry_date',
        'status',
        'issuing_authority',
        'restrictions',
        'document_path',
    ];

    protected $casts = [
        'issue_date' => 'date',
        'expiry_date' => 'date',
    ];

    /**
     * Usuario propietario de la licencia
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Tickets donde se usó esta licencia
     */
    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class, 'conductor_license_id');
    }

    /**
     * Verificar si la licencia está vigente
     */
    public function isValid(): bool
    {
        return $this->status === 'vigente' && $this->expiry_date->isFuture();
    }

    /**
     * Verificar si la licencia está por vencer (próximos 30 días)
     */
    public function isExpiringSoon(): bool
    {
        return $this->expiry_date->diffInDays(now()) <= 30;
    }
}
