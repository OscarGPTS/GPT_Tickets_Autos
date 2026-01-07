<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RHService
{
    private string $baseUrl = 'https://services.satechenergy.com/api/rh';

    /**
     * Obtener información del usuario y su jefe directo por email
     * 
     * @param string $email Email del usuario
     * @return array|null Datos del usuario incluyendo jefe directo, o null si falla
     */
    public function obtenerUsuarioConJefe(string $email): ?array
    {
        try {
            $response = Http::timeout(10)
                ->post("{$this->baseUrl}/users/buscar-por-email", [
                    'email' => $email
                ]);

            if ($response->successful()) {
                $data = $response->json();
                
                if ($data['success'] ?? false) {
                    return $data['data'] ?? null;
                }
            }

            Log::warning('Error obtaining user from RH API', [
                'email' => $email,
                'status' => $response->status(),
                'body' => $response->body()
            ]);

            return null;
        } catch (\Exception $e) {
            Log::error('Exception calling RH API', [
                'email' => $email,
                'exception' => $e->getMessage()
            ]);

            return null;
        }
    }

    /**
     * Obtener email del jefe directo
     * 
     * @param string $email Email del usuario
     * @return string|null Email del jefe directo, o null si no existe
     */
    public function obtenerEmailJefeDirecto(string $email): ?string
    {
        $usuario = $this->obtenerUsuarioConJefe($email);

        if ($usuario && isset($usuario['jefe_directo']['email'])) {
            return $usuario['jefe_directo']['email'];
        }

        return null;
    }

    /**
     * Obtener datos completos del jefe directo
     * 
     * @param string $email Email del usuario
     * @return array|null Datos del jefe directo o null
     */
    public function obtenerJefeDirecto(string $email): ?array
    {
        $usuario = $this->obtenerUsuarioConJefe($email);

        if ($usuario && isset($usuario['jefe_directo'])) {
            return $usuario['jefe_directo'];
        }

        return null;
    }
}
