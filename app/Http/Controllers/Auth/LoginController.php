<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class LoginController extends Controller
{
    /**
     * Mostrar la página de login
     */
    public function index(): View
    {
        return view('auth.login');
    }

    /**
     * Redirigir a Google para autenticación
     */
    public function redirectToProvider(): RedirectResponse
    {
        $params = [
            'client_id' => config('services.google.client_id'),
            'redirect_uri' => config('services.google.redirect'),
            'response_type' => 'code',
            'scope' => 'openid email profile',
            'access_type' => 'offline',
            'prompt' => 'consent',
        ];

        $url = 'https://accounts.google.com/o/oauth2/auth?' . http_build_query($params);

        return redirect($url);
    }

    /**
     * Manejar el callback de Google
     */
    public function handleProviderCallback(Request $request): RedirectResponse
    {
        try {
            // Verificar si hay código de autorización
            if (!$request->has('code')) {
                return redirect()->route('login')
                    ->with('error', 'No se recibió el código de autorización.');
            }

            // Intercambiar código por token
            $tokenResponse = Http::withOptions([
                'verify' => config('app.env') === 'local' ? false : true,
            ])->asForm()->post('https://oauth2.googleapis.com/token', [
                'client_id' => config('services.google.client_id'),
                'client_secret' => config('services.google.client_secret'),
                'code' => $request->code,
                'grant_type' => 'authorization_code',
                'redirect_uri' => config('services.google.redirect'),
            ]);

            if (!$tokenResponse->successful()) {
                throw new \Exception('Error al obtener el token de acceso.');
            }

            $tokenData = $tokenResponse->json();
            $accessToken = $tokenData['access_token'];

            // Obtener información del usuario
            $userResponse = Http::withOptions([
                'verify' => config('app.env') === 'local' ? false : true,
            ])->withToken($accessToken)
                ->get('https://www.googleapis.com/oauth2/v2/userinfo');

            if (!$userResponse->successful()) {
                throw new \Exception('Error al obtener la información del usuario.');
            }

            $userInfo = $userResponse->json();

            // Buscar o crear usuario
            $user = User::updateOrCreate(
                ['email' => $userInfo['email']],
                [
                    'name' => $userInfo['name'] ?? $userInfo['email'],
                    'auth0_id' => $userInfo['id'],
                    'avatar' => $userInfo['picture'] ?? null,
                    'email_verified_at' => $userInfo['verified_email'] ? now() : null,
                ]
            );

            // Si es un usuario nuevo, asignar rol por defecto
            if ($user->wasRecentlyCreated) {
                $defaultRole = \App\Models\Role::where('name', 'usuario')->first();
                if ($defaultRole) {
                    $user->roles()->attach($defaultRole->id);
                }
            }

            // Verificar si el usuario está activo
            if (!$user->is_active) {
                return redirect()->route('login')
                    ->with('error', 'Tu cuenta ha sido desactivada. Contacta al administrador.');
            }

            // Autenticar el usuario en Laravel
            Auth::login($user, true);

            return redirect()->intended('/dashboard')
                ->with('success', '¡Bienvenido, ' . $user->name . '!');

        } catch (\Exception $e) {
            Log::error('Google Login Error: ' . $e->getMessage());
            
            return redirect()->route('login')
                ->with('error', 'Error al iniciar sesión. Por favor, intenta de nuevo.');
        }
    }

    /**
     * Cerrar sesión
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')
            ->with('success', 'Sesión cerrada correctamente.');
    }
}
