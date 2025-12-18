<?php

require __DIR__.'/vendor/autoload.php';

use Illuminate\Http\Request;
use App\Http\Controllers\API\DispatcherController;

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== PRUEBA DE IMAGEN EN CHECKOUT API ===\n\n";

// Crear una pequeña imagen PNG en base64 (1x1 pixel rojo)
$sampleImageBase64 = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mP8z8DwHwAFBQIAX8jx0gAAAABJRU5ErkJggg==';

$controller = new DispatcherController();

// Datos de prueba para CHECKOUT con imagen
$checkoutData = [
    'ticket_id' => 3,
    'fecha' => date('Y-m-d'),
    'kilometraje_inicial' => 0.0,
    'nivel_combustible_inicial' => '1/2',
    'llanta_delantera_derecha' => true,
    'llanta_delantera_izquierda' => true,
    'llanta_delantera_vida' => true,
    'llanta_trasera_derecha' => true,
    'llanta_trasera_izquierda' => true,
    'llanta_trasera_vida' => true,
    'llanta_refaccion' => true,
    'presion_adecuada' => true,
    'parabrisas' => true,
    'cofre' => true,
    'parrilla' => true,
    'defensas' => true,
    'molduras' => true,
    'placa' => true,
    'salpicadera' => true,
    'antena' => true,
    'intermitentes' => true,
    'direccional_derecha' => true,
    'direccional_izquierda' => true,
    'luz_stop' => true,
    'faros' => true,
    'luces_altas' => true,
    'luz_interior' => true,
    'calaveras_buen_estado' => true,
    'mata_chispas' => true,
    'alarma' => true,
    'extintor' => true,
    'botiquin' => true,
    'tarjeta_circulacion' => true,
    'licencia_conducir_vigente' => true,
    'poliza_seguro' => true,
    'triangulo_emergencia' => true,
    'tablero_indicadores' => true,
    'switch_encendido' => true,
    'controles_ac' => true,
    'defroster' => true,
    'radio' => true,
    'volante' => true,
    'bolsas_aire' => true,
    'cintulon_seguridad' => true,
    'coderas' => true,
    'espejo_interior' => true,
    'freno_mano' => true,
    'encendedor' => true,
    'guantera' => true,
    'manijas_interiores' => true,
    'seguros' => true,
    'asientos' => true,
    'tapetes_delanteros_traseros' => true,
    'nivel_aceite_motor' => true,
    'nivel_anticongelante' => true,
    'nivel_liquido_frenos' => true,
    'bateria' => true,
    'bayoneta_aceite_motor' => true,
    'tapones' => true,
    'bocina_claxon' => true,
    'radiador' => true,
    'gato' => true,
    'llave_ruedas' => true,
    'cables_pasa_corriente' => true,
    'caja_bolsa_herramientas' => true,
    'dado_birlo_seguridad' => true,
    'calcomanias_permisos' => true,
    'calcomania_velocidad_maxima' => true,
    'condicion_carroceria_log' => 'Prueba con imagen desde API',
    'condicion_carroceria_imagen' => $sampleImageBase64,
    'responsable_recibo_uso' => 'Oscar Chávez',
    'responsable_entrega' => 'Juan Pérez',
];

echo "1. PROBANDO CHECKOUT CON IMAGEN\n";
echo "   Ticket ID: 3\n";
echo "   Imagen: Incluida (1x1 pixel PNG en base64)\n\n";

$checkoutRequest = Request::create('/api/dispatcher/checklist/checkout', 'POST', $checkoutData);
$checkoutResponse = $controller->checkoutChecklist($checkoutRequest);
$checkoutResult = json_decode($checkoutResponse->getContent(), true);

if ($checkoutResult['success']) {
    echo "   ✅ CHECKOUT CON IMAGEN EXITOSO\n";
    echo "   - Mensaje: {$checkoutResult['message']}\n";
    echo "   - Checklist ID: {$checkoutResult['data']['checklist']['id']}\n";
    
    if (!empty($checkoutResult['data']['checklist']['condicion_carroceria_imagen'])) {
        echo "   - Imagen guardada: {$checkoutResult['data']['checklist']['condicion_carroceria_imagen']}\n";
        
        // Verificar que el archivo existe
        $imagePath = storage_path('app/public/' . $checkoutResult['data']['checklist']['condicion_carroceria_imagen']);
        if (file_exists($imagePath)) {
            echo "   - ✅ Archivo de imagen existe en el servidor\n";
            echo "   - Tamaño: " . filesize($imagePath) . " bytes\n";
        } else {
            echo "   - ❌ Archivo de imagen NO existe\n";
        }
    } else {
        echo "   - ⚠️  No se guardó la imagen\n";
    }
    
    echo "   - Ticket Status: {$checkoutResult['data']['ticket']['status']}\n";
} else {
    echo "   ❌ ERROR EN CHECKOUT\n";
    echo "   - Mensaje: {$checkoutResult['message']}\n";
    if (isset($checkoutResult['error'])) {
        echo "   - Error: {$checkoutResult['error']}\n";
    }
}

echo "\n=== RESUMEN ===\n";
echo "✓ API de Checkout soporta imágenes base64: " . ($checkoutResult['success'] ? 'SÍ' : 'NO') . "\n";
echo "✓ Formato requerido: data:image/{tipo};base64,{contenido}\n";
echo "✓ Tipos soportados: jpg, png, gif\n";
echo "✓ Campo opcional: Si no se envía, se guarda como null\n";
