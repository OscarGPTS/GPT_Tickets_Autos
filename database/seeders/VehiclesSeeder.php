<?php

namespace Database\Seeders;

use App\Models\Vehicle;
use Illuminate\Database\Seeder;

class VehiclesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $vehicles = [
            [
                'internal_code' => 'GPT-001',
                'brand' => 'Toyota',
                'model' => 'Corolla',
                'year' => 2023,
                'plates' => 'ABC-123-A',
                'serial_number' => 'VIN123456789ABCDE1',
                'color' => 'Blanco',
                'vehicle_type' => 'sedan',
                'capacity_passengers' => 5,
                'fuel_type' => 'gasolina',
                'current_mileage' => 15000,
                'status' => 'disponible',
            ],
            [
                'internal_code' => 'GPT-002',
                'brand' => 'Nissan',
                'model' => 'Frontier',
                'year' => 2022,
                'plates' => 'XYZ-456-B',
                'serial_number' => 'VIN123456789ABCDE2',
                'color' => 'Negro',
                'vehicle_type' => 'pickup',
                'capacity_passengers' => 5,
                'capacity_cargo' => 1000,
                'fuel_type' => 'diesel',
                'current_mileage' => 25000,
                'status' => 'disponible',
            ],
            [
                'internal_code' => 'GPT-003',
                'brand' => 'Chevrolet',
                'model' => 'Suburban',
                'year' => 2024,
                'plates' => 'DEF-789-C',
                'serial_number' => 'VIN123456789ABCDE3',
                'color' => 'Gris',
                'vehicle_type' => 'suv',
                'capacity_passengers' => 7,
                'fuel_type' => 'gasolina',
                'current_mileage' => 5000,
                'status' => 'disponible',
            ],
            [
                'internal_code' => 'GPT-004',
                'brand' => 'Ford',
                'model' => 'Transit',
                'year' => 2021,
                'plates' => 'GHI-012-D',
                'serial_number' => 'VIN123456789ABCDE4',
                'color' => 'Blanco',
                'vehicle_type' => 'van',
                'capacity_passengers' => 12,
                'fuel_type' => 'diesel',
                'current_mileage' => 45000,
                'status' => 'disponible',
            ],
            [
                'internal_code' => 'GPT-005',
                'brand' => 'Honda',
                'model' => 'CR-V',
                'year' => 2023,
                'plates' => 'JKL-345-E',
                'serial_number' => 'VIN123456789ABCDE5',
                'color' => 'Azul',
                'vehicle_type' => 'suv',
                'capacity_passengers' => 5,
                'fuel_type' => 'hibrido',
                'current_mileage' => 12000,
                'status' => 'disponible',
            ],
        ];

        foreach ($vehicles as $vehicle) {
            Vehicle::firstOrCreate(
                ['internal_code' => $vehicle['internal_code']],
                $vehicle
            );
        }

        $this->command->info('Vehículos de ejemplo creados exitosamente!');
    }
}
