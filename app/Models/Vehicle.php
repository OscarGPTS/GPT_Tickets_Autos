<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Vehicle extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'internal_code',
        'brand',
        'model',
        'year',
        'plates',
        'serial_number',
        'color',
        'vehicle_type',
        'capacity_passengers',
        'capacity_cargo',
        'fuel_type',
        'current_mileage',
        'status',
        'notes',
        'image_path',
    ];

    protected $casts = [
        'year' => 'integer',
        'capacity_passengers' => 'integer',
        'capacity_cargo' => 'decimal:2',
        'current_mileage' => 'decimal:2',
    ];

    /**
     * Documentos del vehículo
     */
    public function documents(): HasMany
    {
        return $this->hasMany(VehicleDocument::class);
    }

    /**
     * Tickets donde se usó este vehículo
     */
    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class);
    }

    /**
     * Historial de mantenimiento
     */
    public function maintenances(): HasMany
    {
        return $this->hasMany(VehicleMaintenance::class);
    }

    /**
     * Verificar si el vehículo está disponible
     */
    public function isAvailable(): bool
    {
        return $this->status === 'disponible';
    }

    /**
     * Obtener el nombre completo del vehículo
     */
    public function getFullNameAttribute(): string
    {
        return "{$this->brand} {$this->model} ({$this->plates})";
    }

    /**
     * Scope para vehículos disponibles
     */
    public function scopeAvailable($query)
    {
        return $query->where('status', 'disponible');
    }

    /**
     * Scope para vehículos en uso
     */
    public function scopeInUse($query)
    {
        return $query->where('status', 'en_uso');
    }
}
