<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Ticket extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'folio',
        'requisicion',
        'user_id',
        'vehicle_id',
        'dispatcher_id',
        'approved_by',
        'destination',
        'cliente',
        'purpose',
        'requested_date',
        'requested_time_start',
        'requested_time_end',
        'conductor_name',
        'conductor_phone',
        'conductor_license_id',
        'status',
        'approved_at',
        'rejected_at',
        'checkout_at',
        'checkin_at',
        'completed_at',
        'rejection_reason',
        'service_rating',
        'vehicle_rating',
        'rating_comments',
    ];

    protected $casts = [
        'requested_date' => 'date',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
        'checkout_at' => 'datetime',
        'checkin_at' => 'datetime',
        'completed_at' => 'datetime',
        'service_rating' => 'integer',
        'vehicle_rating' => 'integer',
    ];

    /**
     * Usuario solicitante
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Vehículo asignado
     */
    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    /**
     * Despachador asignado
     */
    public function dispatcher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dispatcher_id');
    }

    /**
     * Encargado que aprobó
     */
    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Licencia del conductor
     */
    public function conductorLicense(): BelongsTo
    {
        return $this->belongsTo(DriverLicense::class, 'conductor_license_id');
    }

    /**
     * Checklist de salida
     */
    public function checkoutChecklist(): HasOne
    {
        return $this->hasOne(CheckoutChecklist::class);
    }

    /**
     * Checklist de entrada
     */
    public function checkinChecklist(): HasOne
    {
        return $this->hasOne(CheckinChecklist::class);
    }

    /**
     * Generar folio único para el ticket
     */
    public static function generateFolio(): string
    {
        $year = now()->year;
        $latest = self::whereYear('created_at', $year)
            ->whereNotNull('folio')
            ->latest('id')
            ->first();

        $number = $latest ? (int) substr($latest->folio, 5) + 1 : 1;

        // Ensure uniqueness
        while (self::where('folio', sprintf('%d-%04d', $year, $number))->exists()) {
            $number++;
        }

        return sprintf('%d-%04d', $year, $number);
    }

    /**
     * Verificar si el ticket puede ser editado
     */
    public function canBeEdited(): bool
    {
        return in_array($this->status, ['pendiente']);
    }

    /**
     * Verificar si el ticket puede ser aprobado
     */
    public function canBeApproved(): bool
    {
        return $this->status === 'pendiente';
    }

    /**
     * Verificar si el ticket puede ser rechazado
     */
    public function canBeRejected(): bool
    {
        return $this->status === 'pendiente';
    }

    /**
     * Verificar si se puede hacer checkout
     */
    public function canCheckout(): bool
    {
        return $this->status === 'aprobado';
    }

    /**
     * Verificar si se puede hacer checkin
     */
    public function canCheckin(): bool
    {
        return $this->status === 'en_curso';
    }

    /**
     * Verificar si se puede calificar
     */
    public function canBeRated(): bool
    {
        return $this->status === 'completado' && !$this->service_rating;
    }

    /**
     * Scope para tickets pendientes
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pendiente');
    }

    /**
     * Scope para tickets aprobados
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'aprobado');
    }

    /**
     * Scope para tickets en curso
     */
    public function scopeInProgress($query)
    {
        return $query->where('status', 'en_curso');
    }

    /**
     * Scope para tickets del usuario
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope para tickets del despachador
     */
    public function scopeForDispatcher($query, $dispatcherId)
    {
        return $query->where('dispatcher_id', $dispatcherId);
    }
}
