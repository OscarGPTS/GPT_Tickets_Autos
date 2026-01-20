<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Collection;

class RHUserService
{
    protected string $apiUrl = 'https://services.satechenergy.com/api/rh/users';
    protected ?string $apiToken = null;

    public function __construct()
    {
        $this->apiToken = config('services.rh_api.token');
    }

    /**
     * Obtener todos los usuarios del RH
     */
    public function getAllUsers(): Collection
    {
        try {
            $response = Http::timeout(30)->get($this->apiUrl);

            if ($response->successful()) {
                $data = $response->json();
                return collect($data['data'] ?? []);
            }

            return collect([]);
        } catch (\Exception $e) {
            \Log::error('Error fetching RH users: ' . $e->getMessage());
            return collect([]);
        }
    }

    /**
     * Obtener un usuario específico por ID
     */
    public function getUserById(int $id): ?array
    {
        $users = $this->getAllUsers();
        return $users->firstWhere('id', $id);
    }

    /**
     * Obtener usuarios activos
     */
    public function getActiveUsers(): Collection
    {
        return $this->getAllUsers()->filter(fn($user) => $user['activo'] ?? false);
    }

    /**
     * Buscar usuario por email
     */
    public function getUserByEmail(string $email): ?array
    {
        $users = $this->getAllUsers();
        return $users->firstWhere('email', $email);
    }

    /**
     * Formatear datos del usuario para crear un Usuario en la aplicación
     */
    public function formatUserData(array $rhUser): array
    {
        return [
            'name' => $rhUser['nombre_completo'] ?? '',
            'email' => $rhUser['email'] ?? '',
            'phone' => $rhUser['telefono'] ?? '',
            'department' => $rhUser['departamento']['nombre'] ?? '',
            'avatar' => $rhUser['foto_perfil'] ?? '',
            'is_active' => $rhUser['activo'] ?? true,
            'rh_user_id' => $rhUser['id'] ?? null,
            'rh_uuid' => $rhUser['uuid'] ?? null,
        ];
    }
}
