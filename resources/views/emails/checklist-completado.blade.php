<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checklist Completado</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: {{ $tipo === 'salida' ? '#8b5cf6' : '#f97316' }};
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 8px 8px 0 0;
        }
        .content {
            background-color: #f9fafb;
            padding: 30px;
            border: 1px solid #e5e7eb;
            border-radius: 0 0 8px 8px;
        }
        .info-row {
            margin-bottom: 15px;
            padding: 10px;
            background-color: white;
            border-radius: 4px;
        }
        .label {
            font-weight: bold;
            color: #1f2937;
        }
        .value {
            color: #4b5563;
            margin-left: 10px;
        }
        .status-badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 4px;
            font-size: 14px;
            font-weight: bold;
            background-color: {{ $tipo === 'salida' ? '#ddd6fe' : '#fed7aa' }};
            color: {{ $tipo === 'salida' ? '#6d28d9' : '#c2410c' }};
        }
        .button {
            display: inline-block;
            padding: 12px 24px;
            background-color: {{ $tipo === 'salida' ? '#8b5cf6' : '#f97316' }};
            color: white;
            text-decoration: none;
            border-radius: 6px;
            margin-top: 20px;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            color: #6b7280;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $tipo === 'salida' ? '🚗 Check-Out Realizado' : '✅ Check-In Realizado' }}</h1>
    </div>
    
    <div class="content">
        <p>Se ha completado el <strong>{{ $tipo === 'salida' ? 'Check-Out (Salida)' : 'Check-In (Entrada)' }}</strong> del vehículo:</p>
        
        <center>
            <span class="status-badge">{{ $tipo === 'salida' ? 'VEHÍCULO ENTREGADO' : 'VEHÍCULO DEVUELTO' }}</span>
        </center>
        
        <div style="margin-top: 20px;">
            <div class="info-row">
                <span class="label">📋 Requisición:</span>
                <span class="value">{{ $ticket->requisicion }}</span>
            </div>
            
            <div class="info-row">
                <span class="label">👤 Solicitante:</span>
                <span class="value">{{ $ticket->user->name }}</span>
            </div>
            
            <div class="info-row">
                <span class="label">🚙 Vehículo:</span>
                <span class="value">{{ $ticket->vehicle->brand }} {{ $ticket->vehicle->model }} ({{ $ticket->vehicle->license_plate }})</span>
            </div>
            
            <div class="info-row">
                <span class="label">🛠️ Despachador:</span>
                <span class="value">{{ $ticket->dispatcher->name }}</span>
            </div>
            
            <div class="info-row">
                <span class="label">📅 Fecha:</span>
                <span class="value">{{ \Carbon\Carbon::parse($checklist->fecha)->format('d/m/Y') }}</span>
            </div>
            
            <div class="info-row">
                <span class="label">🕐 Hora:</span>
                <span class="value">{{ $tipo === 'salida' ? $checklist->hora_salida : $checklist->hora_entrada }}</span>
            </div>
            
            <div class="info-row">
                <span class="label">⛽ Nivel de Combustible:</span>
                <span class="value">{{ $tipo === 'salida' ? $checklist->nivel_combustible_inicial : $checklist->nivel_combustible_final }}</span>
            </div>
            
            <div class="info-row">
                <span class="label">📊 Kilometraje:</span>
                <span class="value">{{ $tipo === 'salida' ? number_format($checklist->kilometraje_inicial, 2) : number_format($checklist->kilometraje_final, 2) }} km</span>
            </div>
            
            @if($checklist->observaciones)
            <div class="info-row">
                <span class="label">📝 Observaciones:</span>
                <span class="value">{{ $checklist->observaciones }}</span>
            </div>
            @endif
        </div>
        
        <center>
            <a href="{{ route('tickets.show', $ticket->id) }}" class="button">Ver Checklist Completo</a>
        </center>
        
        @if($tipo === 'salida')
        <p style="margin-top: 30px; font-size: 14px; color: #6b7280;">
            El vehículo ha sido entregado satisfactoriamente. El solicitante debe devolverlo para completar el Check-In.
        </p>
        @else
        <p style="margin-top: 30px; font-size: 14px; color: #6b7280;">
            El vehículo ha sido devuelto y registrado exitosamente. La solicitud ha sido completada.
        </p>
        @endif
    </div>
    
    <div class="footer">
        <p>GPT Services - Sistema de Gestión de Vehículos</p>
        <p>Este es un correo automático, por favor no responder.</p>
    </div>
</body>
</html>
