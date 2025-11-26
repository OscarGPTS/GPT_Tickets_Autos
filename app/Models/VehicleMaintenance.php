<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class VehicleMaintenance extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'vehicle_maintenance';

    protected $fillable = [
        'vehicle_id',
        'maintenance_type',
        'maintenance_date',
        'mileage_at_maintenance',
        'service_provider',
        'description',
        'cost',
        'next_maintenance_date',
        'next_maintenance_mileage',
        'parts_replaced',
        'invoice_number',
        'invoice_path',
        'status',
    ];

    protected $casts = [
        'maintenance_date' => 'date',
        'next_maintenance_date' => 'date',
        'mileage_at_maintenance' => 'decimal:2',
        'next_maintenance_mileage' => 'decimal:2',
        'cost' => 'decimal:2',
    ];

    /**
     * Vehículo al que pertenece el mantenimiento
     */
    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    /**
     * Verificar si el mantenimiento requiere atención
     */
    public function requiresAttention(): bool
    {
        if (!$this->next_maintenance_date) {
            return false;
        }
        
        return $this->next_maintenance_date->isPast() || 
               $this->next_maintenance_date->diffInDays(now()) <= 7;
    }
}
