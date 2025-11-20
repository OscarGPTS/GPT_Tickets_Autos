<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vehículo Asignado</title>
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
            background-color: #10b981;
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
        .alert-box {
            background-color: #fef3c7;
            border-left: 4px solid #f59e0b;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .button {
            display: inline-block;
            padding: 12px 24px;
            background-color: #10b981;
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
        <h1>✅ Vehículo Asignado</h1>
    </div>
    
    <div class="content">
        <p>Estimado/a {{ $ticket->dispatcher->name }},</p>
        <p>Se le ha asignado la siguiente requisición de vehículo:</p>
        
        <div class="info-row">
            <span class="label">📋 Requisición:</span>
            <span class="value">{{ $ticket->requisicion }}</span>
        </div>
        
        <div class="info-row">
            <span class="label">👤 Solicitante:</span>
            <span class="value">{{ $ticket->user->name }} ({{ $ticket->user->email }})</span>
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
        
        <div class="info-row">
            <span class="label">🚙 Vehículo Asignado:</span>
            <span class="value">{{ $ticket->vehicle->brand }} {{ $ticket->vehicle->model }} ({{ $ticket->vehicle->license_plate }})</span>
        </div>
        
        <div class="alert-box">
            <strong>⚠️ Acción Requerida:</strong><br>
            Deberá realizar el CHECK-OUT del vehículo antes de entregarlo al solicitante. Por favor, ingrese al sistema para completar el checklist de inspección.
        </div>
        
        <center>
            <a href="{{ route('tickets.show', $ticket->id) }}" class="button">Ver Detalles y Realizar Check-Out</a>
        </center>
    </div>
    
    <div class="footer">
        <p>GPT Services - Sistema de Gestión de Vehículos</p>
        <p>Este es un correo automático, por favor no responder.</p>
    </div>
</body>
</html>
