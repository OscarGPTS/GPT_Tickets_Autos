@extends('layouts.app')

@section('title', 'Ver Checin - Ticket #' . $ticket->folio)

@section('content')

<style>
    .title-yellow {
        background-color: #F9BE00;
        font-size: 0.75rem;
    }

    .title-red {
        background-color: #CF0A2C;
        color: #FFFFFF;
        font-size: 0.75rem;
    }

    .table-cell-text {
        font-size: 0.75rem;
        padding: 0.375rem;
    }

    .table-input {
        font-size: 0.75rem;
        padding: 0.25rem 0.5rem;
    }

    .form-wrapper {
        max-width: 1400px;
        margin: 0 auto;
        padding: 0 1rem;
    }

    @media (min-width: 1024px) {
        .form-wrapper {
            padding: 0 2rem;
        }
    }

    @media (min-width: 1536px) {
        .form-wrapper {
            padding: 0 4rem;
        }
    }

    @media (max-width: 640px) {
        .form-wrapper {
            padding: 0 0.5rem;
        }

        .title-yellow,
        .title-red,
        .table-cell-text {
            font-size: 0.625rem;
            padding: 0.25rem;
        }
        
        .table-input {
            font-size: 0.625rem;
            padding: 0.25rem;
        }

        .main-container {
            padding: 0.5rem;
        }

        h1 {
            font-size: 1.25rem !important;
        }
    }

    input[type="radio"] {
        transform: scale(0.9);
    }

    @media (max-width: 640px) {
        input[type="radio"] {
            transform: scale(0.75);
        }
    }

    .table-scroll-container {
        -webkit-overflow-scrolling: touch;
        scrollbar-width: thin;
        scrollbar-color: #CBD5E0 #F7FAFC;
        position: relative;
    }

    @media (max-width: 640px) {
        .table-scroll-container::after {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            bottom: 0;
            width: 30px;
            background: linear-gradient(to left, rgba(0,0,0,0.1), transparent);
            pointer-events: none;
            transition: opacity 0.3s;
        }

        .table-scroll-container.scrolled-end::after {
            opacity: 0;
        }
    }

    .table-scroll-container::-webkit-scrollbar {
        height: 8px;
    }

    .table-scroll-container::-webkit-scrollbar-track {
        background: #F7FAFC;
        border-radius: 4px;
    }

    .table-scroll-container::-webkit-scrollbar-thumb {
        background: #CBD5E0;
        border-radius: 4px;
    }

    .table-scroll-container::-webkit-scrollbar-thumb:hover {
        background: #A0AEC0;
    }

    @media (max-width: 640px) {
        .scroll-indicator {
            display: block;
            text-align: center;
            color: #4299E1;
            font-size: 0.75rem;
            margin-bottom: 0.5rem;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }
    }

    @media (min-width: 641px) {
        .scroll-indicator {
            display: none;
        }
    }
</style>

<div class="form-wrapper">
    <div class="mb-4 sm:mb-6">
        <a href="{{ route('tickets.show', $ticket) }}" class="text-blue-600 hover:text-blue-800 font-medium text-sm sm:text-base">
            <i class="fas fa-arrow-left mr-2"></i>Volver al Ticket
        </a>
    </div>

    <div class="bg-white rounded-lg shadow-md p-3 sm:p-6 main-container">
        <div class="flex items-center justify-between mb-3 sm:mb-4 md:mb-6">
            <h1 class="text-xl sm:text-2xl md:text-3xl font-bold text-gray-800">Ticket #{{ $ticket->folio }}</h1>
            <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm font-semibold">✓ Completado</span>
        </div>
        <div class="mb-4 bg-orange-50 border border-orange-200 rounded p-3">
            <strong class="text-orange-800">Folio:</strong> {{ $checklist->ticket->folio ?? 'N/A' }}
        </div>

        @if($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form id="checkinForm">
            <input type="hidden" value="readonly">

            @if($checkoutChecklist ?? false)
            <!-- Campos ocultos con datos del checkout -->
            <input type="hidden" name="checkout_id" value="{{ $checkoutChecklist->id }}">
            <input type="hidden" id="kmInicial" value="{{ $checkoutChecklist->kilometraje_inicial }}">
            <input type="hidden" id="combustibleInicial" value="{{ $checkoutChecklist->nivel_combustible_inicial }}">
            
            <!-- Comparación con Checkout -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-3 sm:p-4 mb-4">
                <h3 class="text-xs sm:text-sm font-semibold text-blue-800 mb-2">
                    <i class="fas fa-info-circle mr-2"></i>Información del Checkout (Salida)
                </h3>
                <div class="text-xs sm:text-sm text-blue-700 grid grid-cols-2 md:grid-cols-4 gap-2">
                    <div>
                        <strong>Fecha Salida:</strong> {{ $checkoutChecklist->fecha->format('d/m/Y') }}
                    </div>
                    <div>
                        <strong>Hora Salida:</strong> {{ $checkoutChecklist->hora_salida }}
                    </div>
                    <div>
                        <strong>Km Inicial:</strong> {{ number_format($checkoutChecklist->kilometraje_inicial, 2) }}
                    </div>
                    <div>
                        <strong>Combustible Inicial:</strong> {{ $checkoutChecklist->nivel_combustible_inicial }}
                    </div>
                </div>
            </div>
            @endif

            <div class="scroll-indicator">
                <i class="fas fa-arrows-alt-h"></i> Desliza horizontalmente para ver toda la tabla
            </div>

            <div class="overflow-x-auto overflow-y-visible -mx-3 sm:mx-0 table-scroll-container relative rounded-lg shadow-sm">
            <table class="w-full mb-6 border border-black rounded-lg text-xs sm:text-sm" style="min-width: 800px;">
                
                <tr>
                    <td class="border border-gray-200 table-cell-text font-semibold title-yellow">Destino</td>
                    <td class="border border-gray-200 table-cell-text" colspan="3">
                        <input type="text" value="{{ $checklist->ticket->destination ?? 'N/A' }}" class="w-full border-gray-300 rounded table-input bg-gray-50" readonly>
                    </td>
                    <td class="border border-gray-200 table-cell-text font-semibold title-yellow">Modelo</td>
                    <td class="border border-gray-200 table-cell-text" colspan="3">
                        <input type="text" value="{{ $checklist->vehicle->model ?? 'N/A' }}" class="w-full border-gray-300 rounded table-input bg-gray-50" readonly>
                    </td>
                    <td class="border border-gray-200 table-cell-text font-semibold title-yellow">Folio</td>
                    <td class="border border-gray-200 table-cell-text" colspan="3">
                        <input type="text" value="{{ $checklist->ticket->folio ?? 'N/A' }}" class="w-full border-gray-300 rounded table-input bg-gray-50" readonly>
                    </td>
                </tr>

                <tr>
                    <td class="border border-gray-200 table-cell-text font-semibold title-yellow">Hora de salida</td>
                    <td class="border border-gray-200 table-cell-text" colspan="3">
                        <input type="text" value="{{ $checkoutChecklist->hora_salida ?? 'N/A' }}" class="w-full border-gray-300 rounded table-input bg-gray-50" readonly>
                    </td>
                    <td class="border border-gray-200 table-cell-text font-semibold title-yellow">Hora de entrada</td>
                    <td class="border border-gray-200 table-cell-text" colspan="3">
                        <input type="text" value="{{ $checklist->hora_entrada ?? 'N/A' }}" class="w-full border-gray-300 rounded table-input bg-gray-50" readonly>
                    </td>
                    <td class="border border-gray-200 table-cell-text font-semibold title-yellow">Fecha</td>
                    <td class="border border-gray-200 table-cell-text" colspan="3">
                        <input type="text" value="{{ optional($checklist->fecha)->format('d/m/Y') ?? 'N/A' }}" class="w-full border-gray-300 rounded table-input bg-gray-50" readonly>
                    </td>
                </tr>

                <tr>
                    <td class="border border-gray-200 table-cell-text font-semibold title-yellow">Kilometraje Inicial</td>
                    <td class="border border-gray-200 table-cell-text" colspan="3">
                        <input type="text" value="{{ isset($checkoutChecklist) ? number_format($checkoutChecklist->kilometraje_inicial, 2) : ($checklist->kilometraje_inicial ?? 'N/A') }}" class="w-full border-gray-300 rounded table-input bg-gray-50" readonly>
                    </td>
                    <td class="border border-gray-200 table-cell-text font-semibold title-yellow">Nivel de combustible Inicial</td>
                    <td class="border border-gray-200 table-cell-text" colspan="3">
                        <input type="text" value="{{ $checkoutChecklist->nivel_combustible_inicial ?? $checklist->nivel_combustible_inicial ?? 'N/A' }}" class="w-full border-gray-300 rounded table-input bg-gray-50" readonly>
                    </td>
                    <td class="border border-gray-200 table-cell-text font-semibold title-yellow">Placas</td>
                    <td class="border border-gray-200 table-cell-text" colspan="3">
                        <input type="text" value="{{ $checklist->placas ?? ($checklist->vehicle->plates ?? $ticket->vehicle->plates ?? 'N/A') }}" class="w-full border-gray-300 rounded table-input bg-gray-50" readonly>
                    </td>
                </tr>

                <tr>
                    <td class="border border-gray-200 table-cell-text font-semibold title-yellow">Kilometraje Final</td>
                    <td class="border border-gray-200 table-cell-text" colspan="3">
                        <input type="text" value="{{ $checklist->kilometraje_final ? number_format($checklist->kilometraje_final, 2) : 'N/A' }}" class="w-full border-gray-300 rounded table-input bg-gray-50" readonly>
                    </td>
                    <td class="border border-gray-200 table-cell-text font-semibold title-yellow">Nivel de Combustible Final</td>
                    <td class="border border-gray-200 table-cell-text" colspan="3">
                        <input type="text" value="{{ $checklist->nivel_combustible_final ?? 'N/A' }}" class="w-full border-gray-300 rounded table-input bg-gray-50" readonly>
                    </td>
                    <td class="border border-gray-200 table-cell-text font-semibold title-yellow">Marca</td>
                    <td class="border border-gray-200 table-cell-text" colspan="3">
                        <input type="text" value="{{ $checklist->marca ?? ($checklist->vehicle->brand ?? $ticket->vehicle->brand ?? 'N/A') }}" class="w-full border-gray-300 rounded table-input bg-gray-50" readonly>
                    </td>
                </tr>

                <tr>
                    <td class="border border-gray-200 table-cell-text title-yellow">Llantas</td>
                    <td class="border border-gray-200 table-cell-text title-red">Si</td>
                    <td class="border border-gray-200 table-cell-text title-red">No</td>
                    <td class="border border-gray-200 table-cell-text title-yellow">Frontal</td>
                    <td class="border border-gray-200 table-cell-text title-red">Si</td>
                    <td class="border border-gray-200 table-cell-text title-red">No</td>
                    <td class="border border-gray-200 table-cell-text title-yellow">Interior</td>
                    <td class="border border-gray-200 table-cell-text title-red">Si</td>
                    <td class="border border-gray-200 table-cell-text title-red">No</td>
                    <td class="border border-gray-200 table-cell-text title-yellow">Motor</td>
                    <td class="border border-gray-200 table-cell-text title-red">Si</td>
                    <td class="border border-gray-200 table-cell-text title-red">No</td>
                </tr>

                <tr>
                    <td class="border border-gray-200 table-cell-text">Delantera derecha</td>
                    <td class="border border-gray-200 table-cell-text text-center"><input type="radio" name="llanta_delantera_derecha" value="1" {{ old('llanta_delantera_derecha', 1) == '1' ? 'checked' : '' }}></td>
                    <td class="border border-gray-200 table-cell-text text-center"><input type="radio" name="llanta_delantera_derecha" value="0" {{ old('llanta_delantera_derecha') == '0' ? 'checked' : '' }}></td>
                    <td class="border border-gray-200 table-cell-text">Parabrisas</td>
                    <td class="border border-gray-200 table-cell-text text-center"><input type="radio" name="parabrisas" value="1" {{ old('parabrisas', 1) == '1' ? 'checked' : '' }}></td>
                    <td class="border border-gray-200 table-cell-text text-center"><input type="radio" name="parabrisas" value="0" {{ old('parabrisas') == '0' ? 'checked' : '' }}></td>
                    <td class="border border-gray-200 table-cell-text">Tablero Indicadores</td>
                    <td class="border border-gray-200 table-cell-text text-center"><input type="radio" name="tablero_indicadores" value="1" {{ old('tablero_indicadores', 1) == '1' ? 'checked' : '' }}></td>
                    <td class="border border-gray-200 table-cell-text text-center"><input type="radio" name="tablero_indicadores" value="0" {{ old('tablero_indicadores') == '0' ? 'checked' : '' }}></td>
                    <td class="border border-gray-200 table-cell-text">Nivel aceite motor</td>
                    <td class="border border-gray-200 table-cell-text text-center"><input type="radio" name="nivel_aceite_motor" value="1" {{ old('nivel_aceite_motor', 1) == '1' ? 'checked' : '' }}></td>
                    <td class="border border-gray-200 table-cell-text text-center"><input type="radio" name="nivel_aceite_motor" value="0" {{ old('nivel_aceite_motor') == '0' ? 'checked' : '' }}></td>
                </tr>

                <tr>
                    <td class="border border-gray-200 table-cell-text">Delantera izquierda</td>
                    <td class="border border-gray-200 table-cell-text text-center"><input type="radio" name="llanta_delantera_izquierda" value="1" {{ old('llanta_delantera_izquierda', 1) == '1' ? 'checked' : '' }}></td>
                    <td class="border border-gray-200 table-cell-text text-center"><input type="radio" name="llanta_delantera_izquierda" value="0" {{ old('llanta_delantera_izquierda') == '0' ? 'checked' : '' }}></td>
                    <td class="border border-gray-200 table-cell-text">Cofre</td>
                    <td class="border border-gray-200 table-cell-text text-center"><input type="radio" name="cofre" value="1" {{ old('cofre', 1) == '1' ? 'checked' : '' }}></td>
                    <td class="border border-gray-200 table-cell-text text-center"><input type="radio" name="cofre" value="0" {{ old('cofre') == '0' ? 'checked' : '' }}></td>
                    <td class="border border-gray-200 table-cell-text">Switch de encendido</td>
                    <td class="border border-gray-200 table-cell-text text-center"><input type="radio" name="switch_encendido" value="1" {{ old('switch_encendido', 1) == '1' ? 'checked' : '' }}></td>
                    <td class="border border-gray-200 table-cell-text text-center"><input type="radio" name="switch_encendido" value="0" {{ old('switch_encendido') == '0' ? 'checked' : '' }}></td>
                    <td class="border border-gray-200 table-cell-text">Nivel anticongelante</td>
                    <td class="border border-gray-200 table-cell-text text-center"><input type="radio" name="nivel_anticongelante" value="1" {{ old('nivel_anticongelante', 1) == '1' ? 'checked' : '' }}></td>
                    <td class="border border-gray-200 table-cell-text text-center"><input type="radio" name="nivel_anticongelante" value="0" {{ old('nivel_anticongelante') == '0' ? 'checked' : '' }}></td>
                </tr>

                <tr>
                     <td class="border border-gray-200 table-cell-text">Vida</td>
                    <td class="border border-gray-200 table-cell-text text-center"><input type="radio" name="llanta_delantera_vida" value="1" {{ old('llanta_delantera_vida', $checklist->llanta_delantera_vida ?? 1) == 1 ? 'checked' : '' }}></td>
                    <td class="border border-gray-200 table-cell-text text-center"><input type="radio" name="llanta_delantera_vida" value="0" {{ old('llanta_delantera_vida', $checklist->llanta_delantera_vida ?? 1) == 0 ? 'checked' : '' }}></td>

                    
                    <td class="border border-gray-200 table-cell-text">Parrilla</td>
                    <td class="border border-gray-200 table-cell-text text-center"><input type="radio" name="parrilla" value="1" {{ old('parrilla', 1) == '1' ? 'checked' : '' }}></td>
                    <td class="border border-gray-200 table-cell-text text-center"><input type="radio" name="parrilla" value="0" {{ old('parrilla') == '0' ? 'checked' : '' }}></td>
                    <td class="border border-gray-200 table-cell-text">Controles A/C</td>
                    <td class="border border-gray-200 table-cell-text text-center"><input type="radio" name="controles_ac" value="1" {{ old('controles_ac', 1) == '1' ? 'checked' : '' }}></td>
                    <td class="border border-gray-200 table-cell-text text-center"><input type="radio" name="controles_ac" value="0" {{ old('controles_ac') == '0' ? 'checked' : '' }}></td>
                    <td class="border border-gray-200 table-cell-text">Nivel líquido frenos</td>
                    <td class="border border-gray-200 table-cell-text text-center"><input type="radio" name="nivel_liquido_frenos" value="1" {{ old('nivel_liquido_frenos', 1) == '1' ? 'checked' : '' }}></td>
                    <td class="border border-gray-200 table-cell-text text-center"><input type="radio" name="nivel_liquido_frenos" value="0" {{ old('nivel_liquido_frenos') == '0' ? 'checked' : '' }}></td>
                </tr>

                <tr>

                    <td class="border border-gray-200 table-cell-text">Trasera derecha</td>
                    <td class="border border-gray-200 table-cell-text text-center"><input type="radio" name="llanta_trasera_derecha" value="1" {{ old('llanta_trasera_derecha', 1) == '1' ? 'checked' : '' }}></td>
                    <td class="border border-gray-200 table-cell-text text-center"><input type="radio" name="llanta_trasera_derecha" value="0" {{ old('llanta_trasera_derecha') == '0' ? 'checked' : '' }}></td>

                    
                    
                    <td class="border border-gray-200 table-cell-text">Defensas</td>
                    <td class="border border-gray-200 table-cell-text text-center"><input type="radio" name="defensas" value="1" {{ old('defensas', 1) == '1' ? 'checked' : '' }}></td>
                    <td class="border border-gray-200 table-cell-text text-center"><input type="radio" name="defensas" value="0" {{ old('defensas') == '0' ? 'checked' : '' }}></td>
                    <td class="border border-gray-200 table-cell-text">Defroster</td>
                    <td class="border border-gray-200 table-cell-text text-center"><input type="radio" name="defroster" value="1" {{ old('defroster', 1) == '1' ? 'checked' : '' }}></td>
                    <td class="border border-gray-200 table-cell-text text-center"><input type="radio" name="defroster" value="0" {{ old('defroster') == '0' ? 'checked' : '' }}></td>
                    <td class="border border-gray-200 table-cell-text">Batería</td>
                    <td class="border border-gray-200 table-cell-text text-center"><input type="radio" name="bateria" value="1" {{ old('bateria', 1) == '1' ? 'checked' : '' }}></td>
                    <td class="border border-gray-200 table-cell-text text-center"><input type="radio" name="bateria" value="0" {{ old('bateria') == '0' ? 'checked' : '' }}></td>
                </tr>

                <tr>

                    <td class="border border-gray-200 table-cell-text">Trasera izquierda</td>
                    <td class="border border-gray-200 table-cell-text text-center"><input type="radio" name="llanta_trasera_izquierda" value="1" {{ old('llanta_trasera_izquierda', 1) == '1' ? 'checked' : '' }}></td>
                    <td class="border border-gray-200 table-cell-text text-center"><input type="radio" name="llanta_trasera_izquierda" value="0" {{ old('llanta_trasera_izquierda') == '0' ? 'checked' : '' }}></td>


                    
                    <td class="border border-gray-200 table-cell-text">Molduras</td>
                    <td class="border border-gray-200 table-cell-text text-center"><input type="radio" name="molduras" value="1" {{ old('molduras', 1) == '1' ? 'checked' : '' }}></td>
                    <td class="border border-gray-200 table-cell-text text-center"><input type="radio" name="molduras" value="0" {{ old('molduras') == '0' ? 'checked' : '' }}></td>
                    <td class="border border-gray-200 table-cell-text">Radio</td>
                    <td class="border border-gray-200 table-cell-text text-center"><input type="radio" name="radio" value="1" {{ old('radio', 1) == '1' ? 'checked' : '' }}></td>
                    <td class="border border-gray-200 table-cell-text text-center"><input type="radio" name="radio" value="0" {{ old('radio') == '0' ? 'checked' : '' }}></td>
                    <td class="border border-gray-200 table-cell-text">Bayoneta aceite motor</td>
                    <td class="border border-gray-200 table-cell-text text-center"><input type="radio" name="bayoneta_aceite_motor" value="1" {{ old('bayoneta_aceite_motor', 1) == '1' ? 'checked' : '' }}></td>
                    <td class="border border-gray-200 table-cell-text text-center"><input type="radio" name="bayoneta_aceite_motor" value="0" {{ old('bayoneta_aceite_motor') == '0' ? 'checked' : '' }}></td>
                </tr>

                <tr>

                    <td class="border border-gray-200 table-cell-text">Vida</td>
                    <td class="border border-gray-200 table-cell-text text-center"><input type="radio" name="llanta_trasera_vida" value="1" {{ old('llanta_trasera_vida', 1) == 1 ? 'checked' : '' }}></td>
                    <td class="border border-gray-200 table-cell-text text-center"><input type="radio" name="llanta_trasera_vida" value="0" {{ old('llanta_trasera_vida') == 0 ? 'checked' : '' }}></td>

                    <td class="border border-gray-200 table-cell-text">Placa</td>
                    <td class="border border-gray-200 table-cell-text text-center"><input type="radio" name="placa" value="1" {{ old('placa', 1) == '1' ? 'checked' : '' }}></td>
                    <td class="border border-gray-200 table-cell-text text-center"><input type="radio" name="placa" value="0" {{ old('placa') == '0' ? 'checked' : '' }}></td>
                    <td class="border border-gray-200 table-cell-text">Volante</td>
                    <td class="border border-gray-200 table-cell-text text-center"><input type="radio" name="volante" value="1" {{ old('volante', 1) == '1' ? 'checked' : '' }}></td>
                    <td class="border border-gray-200 table-cell-text text-center"><input type="radio" name="volante" value="0" {{ old('volante') == '0' ? 'checked' : '' }}></td>
                    <td class="border border-gray-200 table-cell-text">Tapones</td>
                    <td class="border border-gray-200 table-cell-text text-center"><input type="radio" name="tapones" value="1" {{ old('tapones', 1) == '1' ? 'checked' : '' }}></td>
                    <td class="border border-gray-200 table-cell-text text-center"><input type="radio" name="tapones" value="0" {{ old('tapones') == '0' ? 'checked' : '' }}></td>
                </tr>

                <tr>

                    <td class="border border-gray-200 table-cell-text">Llanta de refacción</td>
                    <td class="border border-gray-200 table-cell-text text-center"><input type="radio" name="llanta_refaccion" value="1" {{ old('llanta_refaccion', 1) == '1' ? 'checked' : '' }}></td>
                    <td class="border border-gray-200 table-cell-text text-center"><input type="radio" name="llanta_refaccion" value="0" {{ old('llanta_refaccion') == '0' ? 'checked' : '' }}></td>


                    
                    <td class="border border-gray-200 table-cell-text">Salpicadera</td>
                    <td class="border border-gray-200 table-cell-text text-center"><input type="radio" name="salpicadera" value="1" {{ old('salpicadera', 1) == '1' ? 'checked' : '' }}></td>
                    <td class="border border-gray-200 table-cell-text text-center"><input type="radio" name="salpicadera" value="0" {{ old('salpicadera') == '0' ? 'checked' : '' }}></td>
                    <td class="border border-gray-200 table-cell-text">Bolsas de aire</td>
                    <td class="border border-gray-200 table-cell-text text-center"><input type="radio" name="bolsas_aire" value="1" {{ old('bolsas_aire', 1) == '1' ? 'checked' : '' }}></td>
                    <td class="border border-gray-200 table-cell-text text-center"><input type="radio" name="bolsas_aire" value="0" {{ old('bolsas_aire') == '0' ? 'checked' : '' }}></td>
                    <td class="border border-gray-200 table-cell-text">Bocina claxon</td>
                    <td class="border border-gray-200 table-cell-text text-center"><input type="radio" name="bocina_claxon" value="1" {{ old('bocina_claxon', 1) == '1' ? 'checked' : '' }}></td>
                    <td class="border border-gray-200 table-cell-text text-center"><input type="radio" name="bocina_claxon" value="0" {{ old('bocina_claxon') == '0' ? 'checked' : '' }}></td>
                </tr>

                <tr>

                    <td class="border border-gray-200 table-cell-text">Presión Adecuada</td>
                    <td class="border border-gray-200 table-cell-text text-center"><input type="radio" name="presion_adecuada" value="1" {{ old('presion_adecuada', 1) == '1' ? 'checked' : '' }}></td>
                    <td class="border border-gray-200 table-cell-text text-center"><input type="radio" name="presion_adecuada" value="0" {{ old('presion_adecuada') == '0' ? 'checked' : '' }}></td>


                    
                    
                    <td class="border border-gray-200 table-cell-text">Antena</td>
                    <td class="border border-gray-200 table-cell-text text-center"><input type="radio" name="antena" value="1" {{ old('antena', 1) == '1' ? 'checked' : '' }}></td>
                    <td class="border border-gray-200 table-cell-text text-center"><input type="radio" name="antena" value="0" {{ old('antena') == '0' ? 'checked' : '' }}></td>
                    <td class="border border-gray-200 table-cell-text">Cinturón de seguridad</td>
                    <td class="border border-gray-200 table-cell-text text-center"><input type="radio" name="cinturon_seguridad" value="1" {{ old('cinturon_seguridad', 1) == '1' ? 'checked' : '' }}></td>
                    <td class="border border-gray-200 table-cell-text text-center"><input type="radio" name="cinturon_seguridad" value="0" {{ old('cinturon_seguridad') == '0' ? 'checked' : '' }}></td>
                    <td class="border border-gray-200 table-cell-text">Radiador</td>
                    <td class="border border-gray-200 table-cell-text text-center"><input type="radio" name="radiador" value="1" {{ old('radiador', 1) == '1' ? 'checked' : '' }}></td>
                    <td class="border border-gray-200 table-cell-text text-center"><input type="radio" name="radiador" value="0" {{ old('radiador') == '0' ? 'checked' : '' }}></td>
                </tr>

                <tr>
                    <td class="border border-gray-200 table-cell-text title-yellow">Luces</td>
                    <td class="border border-gray-200 table-cell-text title-red">Si</td>
                    <td class="border border-gray-200 table-cell-text title-red">No</td>

                    
                    
                    <td class="border border-gray-200 table-cell-text title-yellow">Otros (Seguridad)</td>
                    <td class="border border-gray-200 table-cell-text title-red">Si</td>
                    <td class="border border-gray-200 table-cell-text title-red">No</td>
                    <td class="border border-gray-200 table-cell-text">Coderas</td>
                    <td class="border border-gray-200 table-cell-text text-center"><input type="radio" name="coderas" value="1" {{ old('coderas', 1) == '1' ? 'checked' : '' }}></td>
                    <td class="border border-gray-200 table-cell-text text-center"><input type="radio" name="coderas" value="0" {{ old('coderas') == '0' ? 'checked' : '' }}></td>
                    <td class="border border-gray-200 table-cell-text title-yellow">Herramienta</td>
                    <td class="border border-gray-200 table-cell-text title-red">Si</td>
                    <td class="border border-gray-200 table-cell-text title-red">No</td>
                </tr>

                <tr>
                    <td class="border border-gray-200 table-cell-text">Intermitentes</td>
                    <td class="border border-gray-200 table-cell-text text-center"><input type="radio" name="intermitentes" value="1" {{ old('intermitentes', 1) == '1' ? 'checked' : '' }}></td>
                    <td class="border border-gray-200 table-cell-text text-center"><input type="radio" name="intermitentes" value="0" {{ old('intermitentes') == '0' ? 'checked' : '' }}></td>
                    
                    
                    <td class="border border-gray-200 table-cell-text">Mata Chispas</td>
                    <td class="border border-gray-200 table-cell-text text-center"><input type="radio" name="mata_chispas" value="1" {{ old('mata_chispas', 1) == '1' ? 'checked' : '' }}></td>
                    <td class="border border-gray-200 table-cell-text text-center"><input type="radio" name="mata_chispas" value="0" {{ old('mata_chispas') == '0' ? 'checked' : '' }}></td>
                    <td class="border border-gray-200 table-cell-text">Espejo interior</td>
                    <td class="border border-gray-200 table-cell-text text-center"><input type="radio" name="espejo_interior" value="1" {{ old('espejo_interior', 1) == '1' ? 'checked' : '' }}></td>
                    <td class="border border-gray-200 table-cell-text text-center"><input type="radio" name="espejo_interior" value="0" {{ old('espejo_interior') == '0' ? 'checked' : '' }}></td>
                    <td class="border border-gray-200 table-cell-text">Gato</td>
                    <td class="border border-gray-200 table-cell-text text-center"><input type="radio" name="gato" value="1" {{ old('gato', 1) == '1' ? 'checked' : '' }}></td>
                    <td class="border border-gray-200 table-cell-text text-center"><input type="radio" name="gato" value="0" {{ old('gato') == '0' ? 'checked' : '' }}></td>
                </tr>

                <tr>
                    <td class="border border-gray-200 table-cell-text">Direccional Derecha</td>
                    <td class="border border-gray-200 table-cell-text text-center"><input type="radio" name="direccional_derecha" value="1" {{ old('direccional_derecha', 1) == '1' ? 'checked' : '' }}></td>
                    <td class="border border-gray-200 table-cell-text text-center"><input type="radio" name="direccional_derecha" value="0" {{ old('direccional_derecha') == '0' ? 'checked' : '' }}></td>
                    
                    
                    <td class="border border-gray-200 table-cell-text">Alarma</td>
                    <td class="border border-gray-200 table-cell-text text-center"><input type="radio" name="alarma" value="1" {{ old('alarma', 1) == '1' ? 'checked' : '' }}></td>
                    <td class="border border-gray-200 table-cell-text text-center"><input type="radio" name="alarma" value="0" {{ old('alarma') == '0' ? 'checked' : '' }}></td>
                    <td class="border border-gray-200 table-cell-text">Freno de mano</td>
                    <td class="border border-gray-200 table-cell-text text-center"><input type="radio" name="freno_mano" value="1" {{ old('freno_mano', 1) == '1' ? 'checked' : '' }}></td>
                    <td class="border border-gray-200 table-cell-text text-center"><input type="radio" name="freno_mano" value="0" {{ old('freno_mano') == '0' ? 'checked' : '' }}></td>
                    <td class="border border-gray-200 table-cell-text">Llave de ruedas</td>
                    <td class="border border-gray-200 table-cell-text text-center"><input type="radio" name="llave_ruedas" value="1" {{ old('llave_ruedas', 1) == '1' ? 'checked' : '' }}></td>
                    <td class="border border-gray-200 table-cell-text text-center"><input type="radio" name="llave_ruedas" value="0" {{ old('llave_ruedas') == '0' ? 'checked' : '' }}></td>
                </tr>

                <tr>

                    <td class="border border-gray-200 table-cell-text">Direccional Izquierda</td>
                    <td class="border border-gray-200 table-cell-text text-center"><input type="radio" name="direccional_izquierda" value="1" {{ old('direccional_izquierda', 1) == '1' ? 'checked' : '' }}></td>
                    <td class="border border-gray-200 table-cell-text text-center"><input type="radio" name="direccional_izquierda" value="0" {{ old('direccional_izquierda') == '0' ? 'checked' : '' }}></td>
                    
                    
                    <td class="border border-gray-200 table-cell-text">Extintor</td>
                    <td class="border border-gray-200 table-cell-text text-center"><input type="radio" name="extintor" value="1" {{ old('extintor', 1) == '1' ? 'checked' : '' }}></td>
                    <td class="border border-gray-200 table-cell-text text-center"><input type="radio" name="extintor" value="0" {{ old('extintor') == '0' ? 'checked' : '' }}></td>
                    <td class="border border-gray-200 table-cell-text">Encendedor</td>
                    <td class="border border-gray-200 table-cell-text text-center"><input type="radio" name="encendedor" value="1" {{ old('encendedor', 1) == '1' ? 'checked' : '' }}></td>
                    <td class="border border-gray-200 table-cell-text text-center"><input type="radio" name="encendedor" value="0" {{ old('encendedor') == '0' ? 'checked' : '' }}></td>
                    <td class="border border-gray-200 table-cell-text">Cables pasa corrientes</td>
                    <td class="border border-gray-200 table-cell-text text-center"><input type="radio" name="cables_pasa_corriente" value="1" {{ old('cables_pasa_corriente', 1) == '1' ? 'checked' : '' }}></td>
                    <td class="border border-gray-200 table-cell-text text-center"><input type="radio" name="cables_pasa_corriente" value="0" {{ old('cables_pasa_corriente') == '0' ? 'checked' : '' }}></td>
                </tr>

                <tr>

                    <td class="border border-gray-200 table-cell-text">Luz stop</td>
                    <td class="border border-gray-200 table-cell-text text-center"><input type="radio" name="luz_stop" value="1" {{ old('luz_stop', 1) == '1' ? 'checked' : '' }}></td>
                    <td class="border border-gray-200 table-cell-text text-center"><input type="radio" name="luz_stop" value="0" {{ old('luz_stop') == '0' ? 'checked' : '' }}></td>
                    
                    
                    <td class="border border-gray-200 table-cell-text">Botiquin</td>
                    <td class="border border-gray-200 table-cell-text text-center"><input type="radio" name="botiquin" value="1" {{ old('botiquin', 1) == '1' ? 'checked' : '' }}></td>
                    <td class="border border-gray-200 table-cell-text text-center"><input type="radio" name="botiquin" value="0" {{ old('botiquin') == '0' ? 'checked' : '' }}></td>
                    <td class="border border-gray-200 table-cell-text">Guantera</td>
                    <td class="border border-gray-200 table-cell-text text-center"><input type="radio" name="guantera" value="1" {{ old('guantera', 1) == '1' ? 'checked' : '' }}></td>
                    <td class="border border-gray-200 table-cell-text text-center"><input type="radio" name="guantera" value="0" {{ old('guantera') == '0' ? 'checked' : '' }}></td>
                    <td class="border border-gray-200 table-cell-text">Caja o bolsa herramientas</td>
                    <td class="border border-gray-200 table-cell-text text-center"><input type="radio" name="caja_bolsa_herramientas" value="1" {{ old('caja_bolsa_herramientas', 1) == '1' ? 'checked' : '' }}></td>
                    <td class="border border-gray-200 table-cell-text text-center"><input type="radio" name="caja_bolsa_herramientas" value="0" {{ old('caja_bolsa_herramientas') == '0' ? 'checked' : '' }}></td>
                </tr>

                <tr>
                    <td class="border border-gray-200 table-cell-text">Faros</td>
                    <td class="border border-gray-200 table-cell-text text-center"><input type="radio" name="faros" value="1" {{ old('faros', 1) == '1' ? 'checked' : '' }}></td>
                    <td class="border border-gray-200 table-cell-text text-center"><input type="radio" name="faros" value="0" {{ old('faros') == '0' ? 'checked' : '' }}></td>

                    
                    
                    <td class="border border-gray-200 table-cell-text">Tarjeta de circulación</td>
                    <td class="border border-gray-200 table-cell-text text-center"><input type="radio" name="tarjeta_circulacion" value="1" {{ old('tarjeta_circulacion', 1) == '1' ? 'checked' : '' }}></td>
                    <td class="border border-gray-200 table-cell-text text-center"><input type="radio" name="tarjeta_circulacion" value="0" {{ old('tarjeta_circulacion') == '0' ? 'checked' : '' }}></td>
                    <td class="border border-gray-200 table-cell-text">Manijas interiores</td>
                    <td class="border border-gray-200 table-cell-text text-center"><input type="radio" name="manijas_interiores" value="1" {{ old('manijas_interiores', 1) == '1' ? 'checked' : '' }}></td>
                    <td class="border border-gray-200 table-cell-text text-center"><input type="radio" name="manijas_interiores" value="0" {{ old('manijas_interiores') == '0' ? 'checked' : '' }}></td>
                    <td class="border border-gray-200 table-cell-text">Dado o birlo seguridad</td>
                    <td class="border border-gray-200 table-cell-text text-center"><input type="radio" name="dado_birlo_seguridad" value="1" {{ old('dado_birlo_seguridad', 1) == '1' ? 'checked' : '' }}></td>
                    <td class="border border-gray-200 table-cell-text text-center"><input type="radio" name="dado_birlo_seguridad" value="0" {{ old('dado_birlo_seguridad') == '0' ? 'checked' : '' }}></td>
                </tr>

                <tr>
                    <td class="border border-gray-200 table-cell-text">Luces Altas</td>
                    <td class="border border-gray-200 table-cell-text text-center"><input type="radio" name="luces_altas" value="1" {{ old('luces_altas', 1) == '1' ? 'checked' : '' }}></td>
                    <td class="border border-gray-200 table-cell-text text-center"><input type="radio" name="luces_altas" value="0" {{ old('luces_altas') == '0' ? 'checked' : '' }}></td>
                    
                    
                    <td class="border border-gray-200 table-cell-text">Licencia Conductor Vigente</td>
                    <td class="border border-gray-200 table-cell-text text-center"><input type="radio" name="licencia_conducir_vigente" value="1" {{ old('licencia_conducir_vigente', 1) == '1' ? 'checked' : '' }}></td>
                    <td class="border border-gray-200 table-cell-text text-center"><input type="radio" name="licencia_conducir_vigente" value="0" {{ old('licencia_conducir_vigente') == '0' ? 'checked' : '' }}></td>
                    <td class="border border-gray-200 table-cell-text">Seguros</td>
                    <td class="border border-gray-200 table-cell-text text-center"><input type="radio" name="seguros" value="1" {{ old('seguros', 1) == '1' ? 'checked' : '' }}></td>
                    <td class="border border-gray-200 table-cell-text text-center"><input type="radio" name="seguros" value="0" {{ old('seguros') == '0' ? 'checked' : '' }}></td>
                    <td class="border border-gray-200 table-cell-text title-yellow">Calcomanías</td>
                    <td class="border border-gray-200 table-cell-text title-red">Si</td>
                    <td class="border border-gray-200 table-cell-text title-red">No</td>
                </tr>

                <tr>
                    <td class="border border-gray-200 table-cell-text">Luz Interior</td>
                    <td class="border border-gray-200 table-cell-text text-center"><input type="radio" name="luz_interior" value="1" {{ old('luz_interior', 1) == '1' ? 'checked' : '' }}></td>
                    <td class="border border-gray-200 table-cell-text text-center"><input type="radio" name="luz_interior" value="0" {{ old('luz_interior') == '0' ? 'checked' : '' }}></td>
                    
                    
                    <td class="border border-gray-200 table-cell-text">Póliza de seguro</td>
                    <td class="border border-gray-200 table-cell-text text-center"><input type="radio" name="poliza_seguro" value="1" {{ old('poliza_seguro', 1) == '1' ? 'checked' : '' }}></td>
                    <td class="border border-gray-200 table-cell-text text-center"><input type="radio" name="poliza_seguro" value="0" {{ old('poliza_seguro') == '0' ? 'checked' : '' }}></td>
                    <td class="border border-gray-200 table-cell-text">Asientos</td>
                    <td class="border border-gray-200 table-cell-text text-center"><input type="radio" name="asientos" value="1" {{ old('asientos', 1) == '1' ? 'checked' : '' }}></td>
                    <td class="border border-gray-200 table-cell-text text-center"><input type="radio" name="asientos" value="0" {{ old('asientos') == '0' ? 'checked' : '' }}></td>
                    <td class="border border-gray-200 table-cell-text">Calcomanías de permisos</td>
                    <td class="border border-gray-200 table-cell-text text-center"><input type="radio" name="calcomanias_permisos" value="1" {{ old('calcomanias_permisos', 1) == '1' ? 'checked' : '' }}></td>
                    <td class="border border-gray-200 table-cell-text text-center"><input type="radio" name="calcomanias_permisos" value="0" {{ old('calcomanias_permisos') == '0' ? 'checked' : '' }}></td>
                </tr>

                <tr>
                    <td class="border border-gray-200 table-cell-text">Calaveras buen estado</td>
                    <td class="border border-gray-200 table-cell-text text-center"><input type="radio" name="calaveras_buen_estado" value="1" {{ old('calaveras_buen_estado', 1) == '1' ? 'checked' : '' }}></td>
                    <td class="border border-gray-200 table-cell-text text-center"><input type="radio" name="calaveras_buen_estado" value="0" {{ old('calaveras_buen_estado') == '0' ? 'checked' : '' }}></td>

                    
                    
                    <td class="border border-gray-200 table-cell-text">Triángulo de Emergencia</td>
                    <td class="border border-gray-200 table-cell-text text-center"><input type="radio" name="triangulo_emergencia" value="1" {{ old('triangulo_emergencia', 1) == '1' ? 'checked' : '' }}></td>
                    <td class="border border-gray-200 table-cell-text text-center"><input type="radio" name="triangulo_emergencia" value="0" {{ old('triangulo_emergencia') == '0' ? 'checked' : '' }}></td>
                    <td class="border border-gray-200 table-cell-text">Tapetes delanteros y traseros</td>
                    <td class="border border-gray-200 table-cell-text text-center"><input type="radio" name="tapetes_delanteros_traseros" value="1" {{ old('tapetes_delanteros_traseros', 1) == '1' ? 'checked' : '' }}></td>
                    <td class="border border-gray-200 table-cell-text text-center"><input type="radio" name="tapetes_delanteros_traseros" value="0" {{ old('tapetes_delanteros_traseros') == '0' ? 'checked' : '' }}></td>
                    <td class="border border-gray-200 table-cell-text">Calcomanías velocidad máxima</td>
                    <td class="border border-gray-200 table-cell-text text-center"><input type="radio" name="calcomania_velocidad_maxima" value="1" {{ old('calcomania_velocidad_maxima', 1) == '1' ? 'checked' : '' }}></td>
                    <td class="border border-gray-200 table-cell-text text-center"><input type="radio" name="calcomania_velocidad_maxima" value="0" {{ old('calcomania_velocidad_maxima') == '0' ? 'checked' : '' }}></td>
                </tr>

                <tr>
                    <td class="border border-gray-200 p-2 font-semibold" colspan="12">Mantenimiento Preventivo</td>
                </tr>
                <tr>
                    <td class="border border-gray-200 table-cell-text" colspan="12">
                        <textarea rows="3" class="w-full border-gray-300 rounded table-input bg-gray-50" readonly>{{ $checklist->mantenimiento_preventivo }}</textarea>
                    </td>
                </tr>

                <tr>
                    <td class="border border-gray-200 p-2 font-semibold" colspan="12">Mantenimiento Correctivo</td>
                </tr>
                <tr>
                    <td class="border border-gray-200 table-cell-text" colspan="12">
                        <textarea rows="3" class="w-full border-gray-300 rounded table-input bg-gray-50" readonly>{{ $checklist->mantenimiento_correctivo }}</textarea>
                    </td>
                </tr>

                <tr>
                    <td class="border border-gray-200 p-2 font-semibold" colspan="12">Condición de Carrocería - Comparación</td>
                </tr>
                <tr>
                    <td class="border border-gray-200 table-cell-text" colspan="12">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @if($checkoutChecklist && $checkoutChecklist->condicion_carroceria_imagen)
                            <div>
                                <h3 class="font-semibold text-purple-700 mb-2 text-sm">Al Salir (Checkout)</h3>
                                <div class="border-2 border-purple-300 rounded bg-white p-2">
                                    <img src="{{ asset('storage/' . $checkoutChecklist->condicion_carroceria_imagen) }}" 
                                         alt="Condición al salir" 
                                         class="w-full h-auto rounded">
                                </div>
                                @if($checkoutChecklist->condicion_carroceria_log)
                                <div class="mt-2">
                                    <strong class="text-xs text-purple-700">Observaciones Salida:</strong>
                                    <p class="text-xs text-gray-600">{{ $checkoutChecklist->condicion_carroceria_log }}</p>
                                </div>
                                @endif
                            </div>
                            @endif

                            @if($checklist->condicion_carroceria_imagen)
                            <div>
                                <h3 class="font-semibold text-orange-700 mb-2 text-sm">Al Regresar (Checkin)</h3>
                                <div class="border-2 border-orange-300 rounded bg-white p-2">
                                    <img src="{{ asset('storage/' . $checklist->condicion_carroceria_imagen) }}" 
                                         alt="Condición al regresar" 
                                         class="w-full h-auto rounded">
                                </div>
                                @if($checklist->condicion_carroceria_log)
                                <div class="mt-2">
                                    <strong class="text-xs text-orange-700">Observaciones Entrada:</strong>
                                    <p class="text-xs text-gray-600">{{ $checklist->condicion_carroceria_log }}</p>
                                </div>
                                @endif
                            </div>
                            @endif
                        </div>
                    </td>
                </tr>

                <tr>
                    <td class="border border-gray-200 p-2 font-semibold" colspan="6">Responsable de recibo o uso:</td>
                    <td class="border border-gray-200 p-2 font-semibold" colspan="6">Responsable de entrega:</td>
                </tr>
                <tr>
                    <td class="border border-gray-200 table-cell-text" colspan="6">
                        <input type="text" value="{{ $checklist->responsable_recibo_uso }}" class="w-full border-gray-300 rounded table-input bg-gray-50" readonly>
                    </td>
                    <td class="border border-gray-200 table-cell-text" colspan="6">
                        <input type="text" value="{{ $checklist->responsable_entrega }}" class="w-full border-gray-300 rounded table-input bg-gray-50" readonly>
                    </td>
                </tr>
                
            </table>
            </div>

            <!-- Mensaje informativo post-tabla (solo móvil) -->
            <div class="sm:hidden text-center text-xs text-gray-500 mb-3 -mt-4">
                <i class="fas fa-info-circle"></i> Si no ves toda la información, desliza la tabla horizontalmente
            </div>

            <!-- Botones -->
            <div class="flex flex-col sm:flex-row justify-end gap-2 sm:gap-4">
                <a href="{{ route('tickets.show', $ticket) }}" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 sm:px-6 rounded-lg transition duration-200 text-center text-sm sm:text-base">
                    <i class="fas fa-arrow-left mr-2"></i>Volver al Ticket
                </a>
            </div>
        </form>
    </div>

    <!-- Información Adicional -->
    <div class="mt-4 sm:mt-6 bg-orange-50 border border-orange-200 rounded-lg p-3 sm:p-4">
        <h3 class="text-xs sm:text-sm font-semibold text-orange-800 mb-2">
            <i class="fas fa-info-circle mr-2"></i>Información del Checkin Completado
        </h3>
        <ul class="text-xs sm:text-sm text-orange-700 space-y-1">
            <li>• Este checklist fue completado el {{ $checklist->created_at->format('d/m/Y') }} a las {{ $checklist->created_at->format('H:i') }}</li>
            <li>• Folio: <strong>{{ $checklist->ticket->folio ?? 'N/A' }}</strong></li>
            @if($checkoutChecklist)
            <li>• Kilómetros recorridos: <strong>{{ number_format($checklist->kilometraje_final - $checkoutChecklist->kilometraje_inicial, 2) }} km</strong></li>
            @endif
            <li>• Los datos mostrados son de solo lectura</li>
        </ul>
    </div>
</div>

<script>
    // Deshabilitar todos los controles del formulario
    document.addEventListener('DOMContentLoaded', function() {
        // Deshabilitar todos los radio buttons
        const radioInputs = document.querySelectorAll('input[type="radio"]');
        radioInputs.forEach(input => {
            input.disabled = true;
        });

        // Marcar los radio buttons según los valores del checklist
        @php
            $checklistFields = [
                'llanta_delantera_derecha', 'llanta_delantera_izquierda', 'llanta_trasera_derecha', 'llanta_trasera_izquierda',
                'llanta_refaccion', 'presion_adecuada', 'parabrisas', 'cofre', 'parrilla', 'defensas',
                'molduras', 'placa', 'salpicadera', 'antena', 'intermitentes', 'direccional_derecha',
                'direccional_izquierda', 'luz_stop', 'faros', 'luces_altas', 'luz_interior', 'calaveras_buen_estado',
                'tablero_indicadores', 'switch_encendido', 'controles_ac', 'defroster', 'radio', 'volante',
                'bolsas_aire', 'cinturon_seguridad', 'coderas', 'espejo_interior', 'freno_mano', 'encendedor',
                'guantera', 'manijas_interiores', 'seguros', 'asientos', 'tapetes_delanteros_traseros',
                'nivel_aceite_motor', 'nivel_anticongelante', 'nivel_liquido_frenos', 'bateria',
                'bayoneta_aceite_motor', 'tapones', 'bocina_claxon', 'radiador', 'mata_chispas', 'alarma',
                'extintor', 'botiquin', 'tarjeta_circulacion', 'licencia_conducir_vigente', 'poliza_seguro',
                'triangulo_emergencia', 'gato', 'llave_ruedas', 'cables_pasa_corriente', 'caja_bolsa_herramientas',
                'dado_birlo_seguridad', 'calcomanias_permisos', 'calcomania_velocidad_maxima'
            ];
        @endphp

        const checklistData = {
            @foreach($checklistFields as $field)
            '{{ $field }}': {{ $checklist->$field ? '1' : '0' }},
            @endforeach
        };

        // Marcar los radio buttons correspondientes
        Object.keys(checklistData).forEach(fieldName => {
            const value = checklistData[fieldName];
            const radio = document.querySelector(`input[type="radio"][name="${fieldName}"][value="${value}"]`);
            if (radio) {
                radio.checked = true;
            }
        });
    });
</script>
@endsection