<?php

namespace App\Console\Commands;

use App\Models\Role;
use App\Models\User;
use Illuminate\Console\Command;

class AssignUserRole extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:assign-role {email} {role}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Asignar un rol a un usuario por email';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');
        $roleName = $this->argument('role');

        // Buscar usuario
        $user = User::where('email', $email)->first();
        
        if (!$user) {
            $this->error("Usuario con email '{$email}' no encontrado.");
            return 1;
        }

        // Buscar rol
        $role = Role::where('name', $roleName)->first();
        
        if (!$role) {
            $this->error("Rol '{$roleName}' no encontrado.");
            $this->info("Roles disponibles: usuario, encargado, despachador");
            return 1;
        }

        // Verificar si ya tiene el rol
        if ($user->hasRole($roleName)) {
            $this->info("El usuario ya tiene el rol '{$roleName}'.");
            return 0;
        }

        // Asignar rol (sin eliminar roles existentes)
        $user->roles()->attach($role->id);

        $this->info("✅ Rol '{$roleName}' asignado exitosamente a {$user->name} ({$user->email})");
        $this->info("Roles actuales del usuario: " . $user->roles->pluck('name')->implode(', '));

        return 0;
    }
}
