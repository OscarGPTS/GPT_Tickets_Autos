<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nueva Solicitud de Vehículo</title>
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
            background-color: #2563eb;
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
        .button {
            display: inline-block;
            padding: 12px 24px;
            background-color: #2563eb;
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
        <h1>🚗 Nueva Solicitud de Vehículo</h1>
    </div>
    
    <div class="content">
        <p>Estimada @Ana Lilia López Arreola / @José,</p>
        <p>Se ha generado una nueva solicitud de vehículo que requiere su atención:</p>
        
        <div class="info-row">
            <span class="label">📋 Requisición:</span>
            <span class="value">{{ $ticket->requisicion }}</span>
        </div>
        
        <div class="info-row">
            <span class="label">👤 Solicitante:</span>
            <span class="value">{{ $ticket->user->name }}</span>
        </div>
        
        <div class="info-row">
            <span class="label">📍 Destino:</span>
            <span class="value">{{ $ticket->destination }}</span>
        </div>
        
        @if($ticket->cliente)
        <div class="info-row">
            <span class="label">🏢 Cliente:</span>
            <span class="value">{{ $ticket->cliente }}</span>
        </div>
        @endif
        
        <div class="info-row">
            <span class="label">📅 Fecha:</span>
            <span class="value">{{ \Carbon\Carbon::parse($ticket->requested_date)->format('d/m/Y') }}</span>
        </div>
        
        <div class="info-row">
            <span class="label">🕐 Hora de Salida:</span>
            <span class="value">{{ $ticket->requested_time_start ? \Carbon\Carbon::parse($ticket->requested_time_start)->format('H:i') : 'No especificada' }}</span>
        </div>
        
        @if($ticket->requested_time_end)
        <div class="info-row">
            <span class="label">🕐 Hora de Regreso:</span>
            <span class="value">{{ \Carbon\Carbon::parse($ticket->requested_time_end)->format('H:i') }}</span>
        </div>
        @endif
        
        <div class="info-row">
            <span class="label">📝 Propósito:</span>
            <span class="value">{{ $ticket->purpose }}</span>
        </div>
        
        <center>
            <a href="{{ route('tickets.show', $ticket->id) }}" class="button">Revisar y Aprobar Solicitud</a>
        </center>
        
        <p style="margin-top: 30px; font-size: 14px; color: #6b7280;">
            <strong>Siguiente paso:</strong> Por favor, revise esta solicitud y asigne el vehículo y despachador correspondiente.
        </p>
    </div>
    
    <div class="footer">
        <p>GPT Services - Sistema de Gestión de Vehículos</p>
        <p>Este es un correo automático, por favor no responder.</p>
    </div>
</body>
</html>
