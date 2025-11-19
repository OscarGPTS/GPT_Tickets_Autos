<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear permisos
        $permissions = [
            // Tickets
            ['name' => 'tickets.create', 'display_name' => 'Crear Requisición'],
            ['name' => 'tickets.view', 'display_name' => 'Ver Requisiciones'],
            ['name' => 'tickets.update', 'display_name' => 'Editar Requisición'],
            ['name' => 'tickets.approve', 'display_name' => 'Aprobar/Rechazar Requisición'],
            ['name' => 'tickets.rate', 'display_name' => 'Calificar Servicio'],
            
            // Checklists
            ['name' => 'checklists.checkout', 'display_name' => 'Realizar Checkout'],
            ['name' => 'checklists.checkin', 'display_name' => 'Realizar Checkin'],
            
            // Vehículos
            ['name' => 'vehicles.view', 'display_name' => 'Ver Vehículos'],
            ['name' => 'vehicles.create', 'display_name' => 'Crear Vehículo'],
            ['name' => 'vehicles.update', 'display_name' => 'Editar Vehículo'],
            ['name' => 'vehicles.delete', 'display_name' => 'Eliminar Vehículo'],
            
            // Administración
            ['name' => 'admin.access', 'display_name' => 'Acceso al Panel Administrador'],
            ['name' => 'admin.users', 'display_name' => 'Gestionar Usuarios'],
            ['name' => 'admin.reports', 'display_name' => 'Ver Reportes'],
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(
                ['name' => $permission['name']],
                ['display_name' => $permission['display_name']]
            );
        }

        // Crear roles
        $usuarioRole = Role::firstOrCreate(
            ['name' => 'usuario'],
            [
                'display_name' => 'Usuario (Solicitante)',
                'description' => 'Usuario que solicita vehículos'
            ]
        );

        $despachadorRole = Role::firstOrCreate(
            ['name' => 'despachador'],
            [
                'display_name' => 'Despachador',
                'description' => 'Encargado de realizar checkout y checkin'
            ]
        );

        $encargadoRole = Role::firstOrCreate(
            ['name' => 'encargado'],
            [
                'display_name' => 'Encargado',
                'description' => 'Administrador del sistema'
            ]
        );

        // Asignar permisos a roles

        // Usuario: solo puede crear, ver sus tickets y calificar
        $usuarioRole->permissions()->sync(
            Permission::whereIn('name', [
                'tickets.create',
                'tickets.view',
                'tickets.update',
                'tickets.rate',
            ])->pluck('id')
        );

        // Despachador: puede ver tickets asignados y hacer checkout/checkin
        $despachadorRole->permissions()->sync(
            Permission::whereIn('name', [
                'tickets.view',
                'checklists.checkout',
                'checklists.checkin',
                'vehicles.view',
            ])->pluck('id')
        );

        // Encargado: tiene todos los permisos
        $encargadoRole->permissions()->sync(Permission::all()->pluck('id'));

        $this->command->info('Roles y permisos creados exitosamente!');
    }
}
