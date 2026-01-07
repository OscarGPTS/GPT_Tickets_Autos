<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checklist Completado</title>
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
        .button {
            display: inline-block;
            padding: 14px 35px;
            background: #CF0A2C;
            color: #ffffff;
            text-decoration: none;
            border-radius: 4px;
            margin: 25px 0;
            font-weight: 600;
            font-size: 14px;
            letter-spacing: 0.5px;
        }
        .button:hover {
            background: #a50823;
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
            <h1>{{ $tipo === 'salida' ? 'Check-Out Completado' : 'Check-In Completado' }}</h1>
        </div>
        
        <div class="content">
            <p class="greeting">Estimados,</p>
            
            <p>Se ha completado exitosamente el <strong>{{ $tipo === 'salida' ? 'Check-Out (Salida)' : 'Check-In (Entrada)' }}</strong> del vehículo.</p>
            
            <p style="text-align: center; margin: 25px 0;">
                <span class="badge">{{ $tipo === 'salida' ? 'VEHÍCULO ENTREGADO' : 'VEHÍCULO DEVUELTO' }}</span>
            </p>
            
            <div class="info-box">
                <h3>Información General</h3>
                <div class="info-row">
                    <span class="info-label">Requisición:</span> 
                    {{ $ticket->requisicion }}
                </div>
                <div class="info-row">
                    <span class="info-label">Solicitante:</span> 
                    {{ $ticket->user->name }}
                </div>
                <div class="info-row">
                    <span class="info-label">Vehículo:</span> 
                    {{ $ticket->vehicle->brand }} {{ $ticket->vehicle->model }} ({{ $ticket->vehicle->license_plate }})
                </div>
                <div class="info-row">
                    <span class="info-label">Despachador:</span> 
                    {{ $ticket->dispatcher->name }}
                </div>
            </div>

            <div class="info-box">
                <h3>Detalles del Checklist</h3>
                <div class="info-row">
                    <span class="info-label">Fecha:</span> 
                    {{ \Carbon\Carbon::parse($checklist->fecha)->format('d/m/Y') }}
                </div>
                <div class="info-row">
                    <span class="info-label">Hora:</span> 
                    {{ $tipo === 'salida' ? $checklist->hora_salida : $checklist->hora_entrada }}
                </div>
                <div class="info-row">
                    <span class="info-label">Nivel de Combustible:</span> 
                    {{ $tipo === 'salida' ? $checklist->nivel_combustible_inicial : $checklist->nivel_combustible_final }}
                </div>
                <div class="info-row">
                    <span class="info-label">Kilometraje:</span> 
                    {{ $tipo === 'salida' ? number_format($checklist->kilometraje_inicial, 2) : number_format($checklist->kilometraje_final, 2) }} km
                </div>
                @if($checklist->observaciones)
                <div class="info-row">
                    <span class="info-label">Observaciones:</span> 
                    {{ $checklist->observaciones }}
                </div>
                @endif
            </div>

            <p style="text-align: center;">
                <a href="{{ route('tickets.show', $ticket->id) }}" class="button">
                    VER CHECKLIST COMPLETO
                </a>
            </p>

            @if($tipo === 'salida')
            <p style="color: #666666; font-size: 14px;">
                El vehículo ha sido entregado satisfactoriamente. El solicitante debe devolverlo para completar el Check-In.
            </p>
            @else
            <p style="color: #666666; font-size: 14px;">
                El vehículo ha sido devuelto y registrado exitosamente. La solicitud ha sido completada.
            </p>
            @endif
        </div>
        
        <div class="footer">
            <p style="margin: 0 0 10px 0;">Este es un correo automático generado por el sistema.</p>
            <p style="margin: 0;"><span class="footer-accent">GPT Services</span> | Sistema de Gestión Vehicular (SIGEV)</p>
            <p style="margin: 10px 0 0 0;">&copy; {{ date('Y') }} Todos los derechos reservados.</p>
        </div>
    </div>
</body>
</html>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">
                <svg width="140" height="57" viewBox="0 0 93.133331 37.835415" xmlns="http://www.w3.org/2000/svg">
                    <g transform="translate(-58.223892,-108.28795)">
                        <path style="fill:#ffffff" d="m 66.454435,117.49915 c -2.816387,1.1181 -5.310212,2.71303 -6.619458,6.05908 1.47735,3.71076 4.619124,6.10255 9.596462,7.03974 l 16.706249,0.1401 -0.07004,-12.81864 -13.27394,5e-5 -3.607429,4.41296 10.577123,0.1401 v 3.01203 l -10.787264,-0.17512 c -2.92795,-1.95664 -4.271273,-4.40429 -2.521703,-7.8103 z"/>
                        <path style="fill:#ffffff" d="M 89.815165,119.98583 V 130.633 l 6.33927,0.035 0.03503,-5.14847 13.168855,-0.14009 c 13.10808,-2.6908 8.64852-14.21576 0.035,-15.51545 l -39.751761,0.17507 c -7.9867,1.18021 -10.636765,6.50987 -10.121816,11.24257 2.429541,-4.16438 6.111582,-5.10007 9.911674,-5.84894 l 39.436553,-0.21014 c 2.65766,0.99397 3.35436,4.03851 0,4.9033 z"/>
                        <path style="fill:#ffffff" d="m 124.89239,115.32133 0.19812,15.30502 6.33995,0.0495 -0.1486,-15.35455 7.67728,-0.0495 4.012,-5.10168 -29.02507,-0.0495 c 2.30505,1.31576 3.96832,2.96117 4.32069,5.25328 z"/>
                        <ellipse style="fill:none;stroke:#ffffff;stroke-width:0.429953" cx="146.76024" cy="112.72095" rx="2.5674231" ry="2.5178928"/>
                        <text style="font-weight:bold;font-size:4.23333px;font-family:Helvetica;fill:#ffffff" x="145.24956" y="114.18214"><tspan x="145.24956" y="114.18214">R</tspan></text>
                        <text style="font-weight:bold;font-size:13.8441px;font-family:Helvetica;fill:#F9BE00" transform="scale(1.2263456,0.81543083)" x="48.113392" y="174.89142"><tspan x="48.113392" y="174.89142">SERVICES</tspan></text>
                    </g>
                </svg>
            </div>
            <h1>{{ $tipo === 'salida' ? 'Check-Out Completado' : 'Check-In Completado' }}</h1>
        </div>
        
        <div class="content">
            <p class="greeting">Estimados,</p>
            
            <p>Se ha completado exitosamente el <strong>{{ $tipo === 'salida' ? 'Check-Out (Salida)' : 'Check-In (Entrada)' }}</strong> del vehículo.</p>
            
            <p style="text-align: center; margin: 25px 0;">
                <span class="badge">{{ $tipo === 'salida' ? 'VEHÍCULO ENTREGADO' : 'VEHÍCULO DEVUELTO' }}</span>
            </p>
            
            <div class="info-box">
                <h3>Información General</h3>
                <div class="info-row">
                    <span class="info-label">Requisición:</span> 
                    {{ $ticket->requisicion }}
                </div>
                <div class="info-row">
                    <span class="info-label">Solicitante:</span> 
                    {{ $ticket->user->name }}
                </div>
                <div class="info-row">
                    <span class="info-label">Vehículo:</span> 
                    {{ $ticket->vehicle->brand }} {{ $ticket->vehicle->model }} ({{ $ticket->vehicle->license_plate }})
                </div>
                <div class="info-row">
                    <span class="info-label">Despachador:</span> 
                    {{ $ticket->dispatcher->name }}
                </div>
            </div>

            <div class="info-box">
                <h3>Detalles del Checklist</h3>
                <div class="info-row">
                    <span class="info-label">Fecha:</span> 
                    {{ \Carbon\Carbon::parse($checklist->fecha)->format('d/m/Y') }}
                </div>
                <div class="info-row">
                    <span class="info-label">Hora:</span> 
                    {{ $tipo === 'salida' ? $checklist->hora_salida : $checklist->hora_entrada }}
                </div>
                <div class="info-row">
                    <span class="info-label">Nivel de Combustible:</span> 
                    {{ $tipo === 'salida' ? $checklist->nivel_combustible_inicial : $checklist->nivel_combustible_final }}
                </div>
                <div class="info-row">
                    <span class="info-label">Kilometraje:</span> 
                    {{ $tipo === 'salida' ? number_format($checklist->kilometraje_inicial, 2) : number_format($checklist->kilometraje_final, 2) }} km
                </div>
                @if($checklist->observaciones)
                <div class="info-row">
                    <span class="info-label">Observaciones:</span> 
                    {{ $checklist->observaciones }}
                </div>
                @endif
            </div>

            <p style="text-align: center;">
                <a href="{{ route('tickets.show', $ticket->id) }}" class="button">
                    VER CHECKLIST COMPLETO
                </a>
            </p>

            @if($tipo === 'salida')
            <p style="color: #666666; font-size: 14px;">
                El vehículo ha sido entregado satisfactoriamente. El solicitante debe devolverlo para completar el Check-In.
            </p>
            @else
            <p style="color: #666666; font-size: 14px;">
                El vehículo ha sido devuelto y registrado exitosamente. La solicitud ha sido completada.
            </p>
            @endif
        </div>
        
        <div class="footer">
            <p style="margin: 0 0 10px 0;">Este es un correo automático generado por el sistema.</p>
            <p style="margin: 0;"><span class="footer-accent">GPT Services</span> | Sistema de Gestión Vehicular (SIGEV)</p>
            <p style="margin: 10px 0 0 0;">&copy; {{ date('Y') }} Todos los derechos reservados.</p>
        </div>
    </div>
</body>
</html>
