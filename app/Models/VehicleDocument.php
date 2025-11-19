<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class VehicleDocument extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'vehicle_id',
        'document_type',
        'document_number',
        'issue_date',
        'expiry_date',
        'issuer',
        'amount',
        'status',
        'document_path',
        'notes',
    ];

    protected $casts = [
        'issue_date' => 'date',
        'expiry_date' => 'date',
        'amount' => 'decimal:2',
    ];

    /**
     * Vehículo al que pertenece el documento
     */
    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    /**
     * Verificar si el documento está vigente
     */
    public function isValid(): bool
    {
        if (!$this->expiry_date) {
            return true; // Documentos sin vencimiento
        }
        
        return $this->status === 'vigente' && $this->expiry_date->isFuture();
    }

    /**
     * Verificar si el documento está por vencer
     */
    public function isExpiringSoon(): bool
    {
        if (!$this->expiry_date) {
            return false;
        }
        
        return $this->expiry_date->diffInDays(now()) <= 30;
    }
}
