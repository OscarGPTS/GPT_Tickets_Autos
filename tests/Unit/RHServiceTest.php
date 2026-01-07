<?php

namespace Tests\Unit;

use App\Services\RHService;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class RHServiceTest extends TestCase
{
    /**
     * Test obtener usuario con jefe directo exitosamente
     */
    public function test_obtener_usuario_con_jefe_exitosamente()
    {
        Http::fake([
            'services.satechenergy.com/*' => Http::response([
                'success' => true,
                'data' => [
                    'id' => 333,
                    'nombre_completo' => 'Oscar Chávez Rosales',
                    'email' => 'ochavez@gptservices.com',
                    'jefe_directo' => [
                        'id' => 331,
                        'nombre_completo' => 'José Carmen Rodríguez Lara',
                        'email' => 'jrlara@gptservices.com',
                    ]
                ]
            ], 200),
        ]);

        $service = new RHService();
        $result = $service->obtenerUsuarioConJefe('ochavez@gptservices.com');

        $this->assertNotNull($result);
        $this->assertEquals('ochavez@gptservices.com', $result['email']);
        $this->assertArrayHasKey('jefe_directo', $result);
        $this->assertEquals('jrlara@gptservices.com', $result['jefe_directo']['email']);
    }

    /**
     * Test obtener email del jefe directo
     */
    public function test_obtener_email_jefe_directo()
    {
        Http::fake([
            'services.satechenergy.com/*' => Http::response([
                'success' => true,
                'data' => [
                    'email' => 'usuario@gptservices.com',
                    'jefe_directo' => [
                        'email' => 'jefe@gptservices.com',
                    ]
                ]
            ], 200),
        ]);

        $service = new RHService();
        $email = $service->obtenerEmailJefeDirecto('usuario@gptservices.com');

        $this->assertEquals('jefe@gptservices.com', $email);
    }

    /**
     * Test cuando API retorna usuario sin jefe
     */
    public function test_usuario_sin_jefe_directo()
    {
        Http::fake([
            'services.satechenergy.com/*' => Http::response([
                'success' => true,
                'data' => [
                    'email' => 'usuario@gptservices.com',
                    'jefe_directo' => null
                ]
            ], 200),
        ]);

        $service = new RHService();
        $email = $service->obtenerEmailJefeDirecto('usuario@gptservices.com');

        $this->assertNull($email);
    }

    /**
     * Test cuando API retorna error
     */
    public function test_api_retorna_error()
    {
        Http::fake([
            'services.satechenergy.com/*' => Http::response([
                'success' => false,
                'message' => 'Usuario no encontrado'
            ], 404),
        ]);

        $service = new RHService();
        $result = $service->obtenerUsuarioConJefe('inexistente@gptservices.com');

        $this->assertNull($result);
    }

    /**
     * Test timeout en API
     */
    public function test_api_timeout()
    {
        Http::fake([
            'services.satechenergy.com/*' => Http::response([], 500),
        ]);

        $service = new RHService();
        $email = $service->obtenerEmailJefeDirecto('usuario@gptservices.com');

        $this->assertNull($email);
    }

    /**
     * Test exception en llamada HTTP
     */
    public function test_exception_en_llamada_http()
    {
        Http::fake([
            'services.satechenergy.com/*' => Http::throw(
                new \Exception('Connection timeout')
            ),
        ]);

        $service = new RHService();
        $email = $service->obtenerEmailJefeDirecto('usuario@gptservices.com');

        $this->assertNull($email);
    }

    /**
     * Test obtener datos completos del jefe directo
     */
    public function test_obtener_datos_completos_jefe_directo()
    {
        Http::fake([
            'services.satechenergy.com/*' => Http::response([
                'success' => true,
                'data' => [
                    'email' => 'usuario@gptservices.com',
                    'jefe_directo' => [
                        'id' => 331,
                        'nombre_completo' => 'José Carmen Rodríguez Lara',
                        'email' => 'jrlara@gptservices.com',
                        'telefono' => '(+52) 81 8473 8500',
                    ]
                ]
            ], 200),
        ]);

        $service = new RHService();
        $jefe = $service->obtenerJefeDirecto('usuario@gptservices.com');

        $this->assertNotNull($jefe);
        $this->assertEquals('jrlara@gptservices.com', $jefe['email']);
        $this->assertEquals('José Carmen Rodríguez Lara', $jefe['nombre_completo']);
    }
}
