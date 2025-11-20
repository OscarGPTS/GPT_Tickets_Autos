<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DispatcherSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        $dispatchers = [
            [
                'name' => 'Juan Carlos Ramírez',
                'email' => 'juan.carlos@gpt.com',
                'password' => Hash::make('dispatcher123'),
                'department' => 'Despacho de Vehículos',
                'phone' => '555-1001',
                'is_active' => true,
            ],
            [
                'name' => 'Martin López García',
                'email' => 'martin.lopez@gpt.com',
                'password' => Hash::make('dispatcher123'),
                'department' => 'Despacho de Vehículos',
                'phone' => '555-1002',
                'is_active' => true,
            ],
            [
                'name' => 'Ernesto Morales Sánchez',
                'email' => 'ernesto.morales@gpt.com',
                'password' => Hash::make('dispatcher123'),
                'department' => 'Despacho de Vehículos',
                'phone' => '555-1003',
                'is_active' => true,
            ],
        ];

        foreach ($dispatchers as $dispatcherData) {
            // Verificar si el usuario ya existe
            $existing = User::where('email', $dispatcherData['email'])->first();
            if (!$existing) {
                $user = User::create($dispatcherData);
                
                // Asignar rol de dispatcher usando Spatie
                $user->assignRole('dispatcher');
                
                $this->command->info("✅ Usuario {$dispatcherData['name']} creado");
            } else {
                // Si existe, actualizar y asegurar que tenga el rol
                $existing->update([
                    'department' => $dispatcherData['department'],
                    'phone' => $dispatcherData['phone'],
                    'is_active' => true,
                ]);
                
                if (!$existing->hasRole('dispatcher')) {
                    $existing->assignRole('dispatcher');
                }
                
                $this->command->info("ℹ️  Usuario {$dispatcherData['name']} ya existía, actualizado");
            }
        }

        $this->command->info('✅ Despachadores procesados exitosamente');
        $this->command->info('📧 Emails: juan.carlos@gpt.com, martin.lopez@gpt.com, ernesto.morales@gpt.com');
        $this->command->info('🔑 Password para todos: dispatcher123');
    }
}
