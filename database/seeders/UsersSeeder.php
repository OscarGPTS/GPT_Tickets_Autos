<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear usuarios de ejemplo

        // 1. Ana Lilia (Encargada)
        $anaLilia = User::firstOrCreate(
            ['email' => 'analilia@gptservices.com'],
            [
                'name' => 'Ana Lilia',
                'password' => Hash::make('password123'),
                'phone' => '555-0001',
                'department' => 'Administración',
                'is_active' => true,
            ]
        );
        $anaLilia->roles()->sync(Role::where('name', 'encargado')->first()->id);

        // 2. José Carmen (Encargado)
        $joseCarmen = User::firstOrCreate(
            ['email' => 'josecarmen@gptservices.com'],
            [
                'name' => 'José Carmen',
                'password' => Hash::make('password123'),
                'phone' => '555-0002',
                'department' => 'Administración',
                'is_active' => true,
            ]
        );
        $joseCarmen->roles()->sync(Role::where('name', 'encargado')->first()->id);

        // 3. Denisse (Usuario con copia)
        $denisse = User::firstOrCreate(
            ['email' => 'denisse@gptservices.com'],
            [
                'name' => 'Denisse',
                'password' => Hash::make('password123'),
                'phone' => '555-0003',
                'department' => 'Recursos Humanos',
                'is_active' => true,
            ]
        );
        $denisse->roles()->sync(Role::where('name', 'usuario')->first()->id);

        // 4. Despachador de ejemplo
        $despachador1 = User::firstOrCreate(
            ['email' => 'despachador1@gptservices.com'],
            [
                'name' => 'Juan Pérez',
                'password' => Hash::make('password123'),
                'phone' => '555-0004',
                'department' => 'Logística',
                'is_active' => true,
            ]
        );
        $despachador1->roles()->sync(Role::where('name', 'despachador')->first()->id);

        // 5. Usuario de ejemplo
        $usuario1 = User::firstOrCreate(
            ['email' => 'usuario1@gptservices.com'],
            [
                'name' => 'María González',
                'password' => Hash::make('password123'),
                'phone' => '555-0005',
                'department' => 'Ventas',
                'immediate_boss_id' => $denisse->id,
                'is_active' => true,
            ]
        );
        $usuario1->roles()->sync(Role::where('name', 'usuario')->first()->id);

        $this->command->info('Usuarios de ejemplo creados exitosamente!');
        $this->command->info('Credenciales por defecto: password123');
    }
}
