<?php

namespace Database\Seeders;

use App\Models\Vehicle;
use Illuminate\Database\Seeder;

class VehicleSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        $vehicles = [
            [
                'internal_code' => 'GPT-001',
                'brand' => 'Nissan',
                'model' => 'Versa',
                'year' => 2022,
                'plates' => 'ABC-123-DE',
                'serial_number' => '3N1CN7AP1KL123456',
                'color' => 'Blanco',
                'vehicle_type' => 'sedan',
                'capacity_passengers' => 5,
                'capacity_cargo' => 400.00,
                'fuel_type' => 'gasolina',
                'current_mileage' => 25000.00,
                'status' => 'disponible',
                'notes' => 'Vehículo en excelente estado, último servicio hace 2 meses',
            ],
            [
                'internal_code' => 'GPT-002',
                'brand' => 'Toyota',
                'model' => 'Hilux',
                'year' => 2021,
                'plates' => 'XYZ-456-FG',
                'serial_number' => '3TMCZ5AN1JM123789',
                'color' => 'Gris',
                'vehicle_type' => 'pickup',
                'capacity_passengers' => 5,
                'capacity_cargo' => 1000.00,
                'fuel_type' => 'diesel',
                'current_mileage' => 45000.00,
                'status' => 'disponible',
                'notes' => 'Pick up con caja de carga, ideal para transporte de equipo',
            ],
            [
                'internal_code' => 'GPT-003',
                'brand' => 'Chevrolet',
                'model' => 'Aveo',
                'year' => 2020,
                'plates' => 'LMN-789-HI',
                'serial_number' => 'KL1TD66E1KB123456',
                'color' => 'Rojo',
                'vehicle_type' => 'sedan',
                'capacity_passengers' => 5,
                'capacity_cargo' => 350.00,
                'fuel_type' => 'gasolina',
                'current_mileage' => 60000.00,
                'status' => 'disponible',
                'notes' => 'Vehículo económico, ideal para trayectos cortos',
            ],
            [
                'internal_code' => 'GPT-004',
                'brand' => 'Ford',
                'model' => 'Transit',
                'year' => 2023,
                'plates' => 'PQR-012-JK',
                'serial_number' => 'NM0GS9F70N1123456',
                'color' => 'Blanco',
                'vehicle_type' => 'van',
                'capacity_passengers' => 12,
                'capacity_cargo' => 1500.00,
                'fuel_type' => 'diesel',
                'current_mileage' => 15000.00,
                'status' => 'disponible',
                'notes' => 'Van de pasajeros, ideal para grupos grandes',
            ],
            [
                'internal_code' => 'GPT-005',
                'brand' => 'Honda',
                'model' => 'CR-V',
                'year' => 2022,
                'plates' => 'STU-345-LM',
                'serial_number' => '2HKRM4H79NH123456',
                'color' => 'Negro',
                'vehicle_type' => 'suv',
                'capacity_passengers' => 7,
                'capacity_cargo' => 600.00,
                'fuel_type' => 'gasolina',
                'current_mileage' => 30000.00,
                'status' => 'disponible',
                'notes' => 'SUV familiar, amplia y confortable',
            ],
            [
                'internal_code' => 'GPT-006',
                'brand' => 'Nissan',
                'model' => 'NP300',
                'year' => 2021,
                'plates' => 'VWX-678-NO',
                'serial_number' => '3N6PD23Y8ZK123456',
                'color' => 'Azul',
                'vehicle_type' => 'camioneta',
                'capacity_passengers' => 5,
                'capacity_cargo' => 1200.00,
                'fuel_type' => 'diesel',
                'current_mileage' => 50000.00,
                'status' => 'disponible',
                'notes' => 'Camioneta de trabajo con doble cabina',
            ],
            [
                'internal_code' => 'GPT-007',
                'brand' => 'Volkswagen',
                'model' => 'Jetta',
                'year' => 2023,
                'plates' => 'YZA-901-PQ',
                'serial_number' => '3VW2B7AJ0KM123456',
                'color' => 'Plata',
                'vehicle_type' => 'sedan',
                'capacity_passengers' => 5,
                'capacity_cargo' => 450.00,
                'fuel_type' => 'gasolina',
                'current_mileage' => 8000.00,
                'status' => 'disponible',
                'notes' => 'Vehículo nuevo, equipamiento completo',
            ],
            [
                'internal_code' => 'GPT-008',
                'brand' => 'Mazda',
                'model' => 'CX-5',
                'year' => 2022,
                'plates' => 'BCD-234-RS',
                'serial_number' => 'JM3KFBDM0N0123456',
                'color' => 'Rojo',
                'vehicle_type' => 'suv',
                'capacity_passengers' => 5,
                'capacity_cargo' => 550.00,
                'fuel_type' => 'gasolina',
                'current_mileage' => 28000.00,
                'status' => 'en_uso',
                'notes' => 'SUV deportivo, actualmente en uso',
            ],
        ];

        foreach ($vehicles as $vehicleData) {
            Vehicle::create($vehicleData);
        }

        $this->command->info('✅ 8 vehículos creados exitosamente');
    }
}
