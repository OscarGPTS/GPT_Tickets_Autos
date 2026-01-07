<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class TestEmailController extends Controller
{
    /**
     * Envía un correo de prueba a ochavez@gptservices.com
     * Usado para validar que el sistema de correos funciona correctamente en producción
     * 
     * @return Response
     */
    public function sendTestEmail()
    {
        try {
            $testEmail = config('mail.test_email');
            
            // Obtener los emails de CC desde la configuración
            $ccEmails = explode(',', config('mail.encargados_emails'));
            $ccEmails = array_map('trim', $ccEmails);
            
            // Enviar el correo de prueba
            Mail::html($this->getTestEmailContent(), function ($mail) use ($testEmail, $ccEmails) {
                $mail->to($testEmail)
                     ->cc($ccEmails)
                     ->subject('Prueba de Correo - SIGEV Sistema');
            });

            Log::info('Correo de prueba enviado exitosamente', [
                'to' => $testEmail,
                'cc' => $ccEmails,
                'timestamp' => now()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Correo de prueba enviado exitosamente',
                'details' => [
                    'to' => $testEmail,
                    'cc' => $ccEmails,
                    'timestamp' => now()
                ]
            ], 200);

        } catch (\Exception $e) {
            Log::error('Error al enviar correo de prueba', [
                'error' => $e->getMessage(),
                'timestamp' => now()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al enviar el correo de prueba',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Retorna el contenido HTML del correo de prueba
     */
    private function getTestEmailContent(): string
    {
        return <<<'HTML'
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prueba de Correo</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            line-height: 1.6;
            color: #333333;
            margin: 0;
            padding: 0;
            background-color: #f5f5f5;
        }
        .container {
            max-width: 650px;
            margin: 30px auto;
            background: #ffffff;
            border-radius: 0;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .header {
            background: #CF0A2C;
            color: #ffffff;
            padding: 40px 30px;
            text-align: center;
        }
        .logo {
            margin-bottom: 20px;
        }
        .header h1 {
            margin: 0;
            font-size: 22px;
            font-weight: 600;
            letter-spacing: 0.5px;
        }
        .content {
            padding: 40px 30px;
        }
        .greeting {
            font-size: 16px;
            margin-bottom: 20px;
            color: #333333;
        }
        .info-box {
            background: #fafafa;
            border-left: 3px solid #CF0A2C;
            padding: 20px;
            margin: 25px 0;
        }
        .info-box h3 {
            margin: 0 0 15px 0;
            color: #CF0A2C;
            font-size: 15px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .info-row {
            margin: 10px 0;
            font-size: 14px;
        }
        .info-label {
            font-weight: 600;
            color: #555555;
            display: inline-block;
            min-width: 140px;
        }
        .badge {
            display: inline-block;
            padding: 6px 12px;
            background: #F9BE00;
            color: #333333;
            border-radius: 3px;
            font-size: 12px;
            font-weight: 600;
            letter-spacing: 0.3px;
        }
        .footer {
            background: #2c2c2c;
            padding: 30px;
            text-align: center;
            font-size: 12px;
            color: #999999;
        }
        .footer-accent {
            color: #F9BE00;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">
                <svg width="140" height="57" viewBox="0 0 93.133331 37.835415" xmlns="http://www.w3.org/2000/svg">
                    <g transform="translate(-58.223892,-108.28795)">
                        <path style="fill:#ffffff" d="m 66.454435,117.49915 c -2.816387,1.1181 -5.310212,2.71303 -6.619458,6.05908 1.47735,3.71076 4.619124,6.10255 9.596462,7.03974 l 16.706249,0.1401 -0.07004,-12.81864 -13.27394,5e-5 -3.607429,4.41296 10.577123,0.1401 v 3.01203 l -10.787264,-0.17512 c -2.92795,-1.95664 -4.271273,-4.40429 -2.521703,-7.8103 z"/>
                        <path style="fill:#ffffff" d="M 89.815165,119.98583 V 130.633 l 6.33927,0.035 0.03503,-5.14847 13.168855,-0.14009 c 13.10808,-2.6908 8.64852,-14.21576 0.035,-15.51545 l -39.751761,0.17507 c -7.9867,1.18021 -10.636765,6.50987 -10.121816,11.24257 2.429541,-4.16438 6.111582,-5.10007 9.911674,-5.84894 l 39.436553,-0.21014 c 2.65766,0.99397 3.35436,4.03851 0,4.9033 z"/>
                        <path style="fill:#ffffff" d="m 124.89239,115.32133 0.19812,15.30502 6.33995,0.0495 -0.1486,-15.35455 7.67728,-0.0495 4.012,-5.10168 -29.02507,-0.0495 c 2.30505,1.31576 3.96832,2.96117 4.32069,5.25328 z"/>
                        <ellipse style="fill:none;stroke:#ffffff;stroke-width:0.429953" cx="146.76024" cy="112.72095" rx="2.5674231" ry="2.5178928"/>
                        <text style="font-weight:bold;font-size:4.23333px;font-family:Helvetica;fill:#ffffff" x="145.24956" y="114.18214"><tspan x="145.24956" y="114.18214">R</tspan></text>
                        <text style="font-weight:bold;font-size:13.8441px;font-family:Helvetica;fill:#F9BE00" transform="scale(1.2263456,0.81543083)" x="48.113392" y="174.89142"><tspan x="48.113392" y="174.89142">SERVICES</tspan></text>
                    </g>
                </svg>
            </div>
            <h1>Prueba de Correo</h1>
        </div>
        
        <div class="content">
            <p class="greeting">Estimado,</p>
            
            <p>Este es un correo de prueba para validar que el sistema de correos del SIGEV está funcionando correctamente en el ambiente actual.</p>
            
            <div class="info-box">
                <h3>Detalles de la Prueba</h3>
                <div class="info-row">
                    <span class="info-label">Tipo:</span> 
                    Prueba de Funcionalidad
                </div>
                <div class="info-row">
                    <span class="info-label">Fecha:</span> 
                    <?php echo date('d/m/Y H:i:s'); ?>
                </div>
                <div class="info-row">
                    <span class="info-label">Sistema:</span> 
                    SIGEV - Gestión Vehicular
                </div>
                <div class="info-row">
                    <span class="info-label">Estado:</span> 
                    <span class="badge">EXITOSO</span>
                </div>
            </div>

            <p style="color: #666666; font-size: 14px;">
                Si recibiste este correo, significa que el sistema de notificaciones está funcionando correctamente y los correos se están entregando como se espera.
            </p>
        </div>
        
        <div class="footer">
            <p style="margin: 0 0 10px 0;">Este es un correo automático de prueba generado por el sistema.</p>
            <p style="margin: 0;"><span class="footer-accent">GPT Services</span> | Sistema de Gestión Vehicular (SIGEV)</p>
            <p style="margin: 10px 0 0 0;">&copy; 2025 Todos los derechos reservados.</p>
        </div>
    </div>
</body>
</html>
HTML;
    }
}
