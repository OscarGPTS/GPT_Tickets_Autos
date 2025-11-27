@extends('layouts.app')

@section('title', 'Checkout Vehículo - Ticket #' . $ticket->id)

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
        <h1 class="text-xl sm:text-2xl md:text-3xl font-bold text-gray-800 mb-3 sm:mb-4 md:mb-6">Checklist de Salida (Checkout) - Ticket #{{ $ticket->id }}</h1>

        @if($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form action="{{ route('checklists.checkout.store', $ticket) }}" method="POST" id="checkoutForm" onsubmit="return validateForm()">
            @csrf

            <div class="scroll-indicator">
                <i class="fas fa-arrows-alt-h"></i> Desliza horizontalmente para ver toda la tabla
            </div>

            <div class="overflow-x-auto overflow-y-visible -mx-3 sm:mx-0 table-scroll-container relative rounded-lg shadow-sm">
            <table class="w-full mb-6 border border-black rounded-lg text-xs sm:text-sm" style="min-width: 800px;">
                
                <tr>
                    <td class="border border-gray-200 table-cell-text font-semibold title-yellow">Destino</td>
                    <td class="border border-gray-200 table-cell-text" colspan="3">
                        <input type="text" name="destino" value="{{ old('destino', $ticket->destination) }}" class="w-full border-gray-300 rounded table-input" required>
                    </td>
                    <td class="border border-gray-200 table-cell-text font-semibold title-yellow">Modelo</td>
                    <td class="border border-gray-200 table-cell-text" colspan="3">
                        <input type="text" name="modelo" value="{{ old('modelo', $ticket->vehicle->model) }}" class="w-full border-gray-300 rounded table-input bg-gray-50" readonly required>
                    </td>
                    <td class="border border-gray-200 table-cell-text font-semibold title-yellow">Folio</td>
                    <td class="border border-gray-200 table-cell-text" colspan="3">
                        <input type="text" name="folio" value="{{ old('folio', $ticket->folio) }}" class="w-full border-gray-300 rounded table-input bg-gray-50" placeholder="Auto-generado" readonly>
                    </td>
                </tr>

                <tr>
                    <td class="border border-gray-200 table-cell-text font-semibold title-yellow">Hora de salida</td>
                    <td class="border border-gray-200 table-cell-text" colspan="3">
                        <input type="time" name="hora_salida" value="{{ old('hora_salida', $ticket->hora_salida) }}" class="w-full border-gray-300 rounded table-input" required>
                    </td>
                    <td class="border border-gray-200 table-cell-text font-semibold title-yellow">Hora de entrada</td>
                    <td class="border border-gray-200 table-cell-text" colspan="3">
                        <input type="time" name="hora_entrada" value="{{ old('hora_entrada', $ticket->hora_entrada) }}" class="w-full border-gray-300 rounded table-input bg-gray-50" readonly required>
                    </td>
                    <td class="border border-gray-200 table-cell-text font-semibold title-yellow">Fecha</td>
                    <td class="border border-gray-200 table-cell-text" colspan="3">
                        <input type="date" name="fecha" value="{{ old('fecha', $ticket->fecha) }}" class="w-full border-gray-300 rounded table-input bg-gray-50" placeholder="Auto-generado" readonly>
                    </td>
                </tr>

                
                <tr>
                    <td class="border border-gray-200 table-cell-text font-semibold title-yellow">Kilometraje inicial</td>
                    <td class="border border-gray-200 table-cell-text" colspan="3">
                        <input type="number" step="0.01" name="kilometraje_inicial" value="{{ old('kilometraje_inicial', $ticket->kilometraje_salida) }}" class="w-full border-gray-300 rounded table-input" required>
                    </td>
                    <td class="border border-gray-200 table-cell-text font-semibold title-yellow">Nivel de combustible inicial</td>
                    <td class="border border-gray-200 table-cell-text" colspan="3">
                        <select name="nivel_combustible_inicial" class="w-full border-gray-300 rounded table-input" required>
                            <option value="">Seleccionar...</option>
                            <option value="1/4" {{ old('nivel_combustible_inicial') == '1/4' ? 'selected' : '' }}>1/4</option>
                            <option value="1/2" {{ old('nivel_combustible_inicial') == '1/2' ? 'selected' : '' }}>1/2</option>
                            <option value="3/4" {{ old('nivel_combustible_inicial') == '3/4' ? 'selected' : '' }}>3/4</option>
                            <option value="Lleno" {{ old('nivel_combustible_inicial') == 'Lleno' ? 'selected' : '' }}>Lleno</option>
                        </select>
                    </td>
                    
                    <td class="border border-gray-200 table-cell-text font-semibold title-yellow">Placas</td>
                    <td class="border border-gray-200 table-cell-text" colspan="3">
                        <input type="text" name="placas" value="{{ old('placas', $ticket->vehicle->plates) }}" class="w-full border-gray-300 rounded table-input" required readonly>
                    </td>
                </tr>

                <tr>
                    <td class="border border-gray-200 table-cell-text font-semibold title-yellow">Kilometraje final</td>
                    <td class="border border-gray-200 table-cell-text" colspan="3">
                        <input type="number" step="0.01" name="kilometraje_final" value="{{ old('kilometraje_final', now()->format('H:i')) }}" class="w-full border-gray-300 rounded table-input" required>
                    </td>
                    <td class="border border-gray-200 table-cell-text font-semibold title-yellow">Nivel de combustible final</td>
                    <td class="border border-gray-200 table-cell-text" colspan="3">
                        <select name="nivel_combustible_final" class="w-full border-gray-300 rounded table-input" required>
                            <option value="">Seleccionar...</option>
                            <option value="1/4" {{ old('nivel_combustible_final') == '1/4' ? 'selected' : '' }}>1/4</option>
                            <option value="1/2" {{ old('nivel_combustible_final') == '1/2' ? 'selected' : '' }}>1/2</option>
                            <option value="3/4" {{ old('nivel_combustible_final') == '3/4' ? 'selected' : '' }}>3/4</option>
                            <option value="Lleno" {{ old('nivel_combustible_final') == 'Lleno' ? 'selected' : '' }}>Lleno</option>
                        </select>
                    </td>
                    <td class="border border-gray-200 table-cell-text font-semibold title-yellow">Marca</td>
                    <td class="border border-gray-200 table-cell-text" colspan="3">
                        <input type="text" name="marca" value="{{ old('marca', $ticket->vehicle->brand) }}" class="w-full border-gray-300 rounded table-input bg-gray-50" readonly required>
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
                    <td class="border border-gray-200 table-cell-text text-center"><input type="radio" name="llanta_delantera_vida" value="1" {{ old('llanta_delantera_vida', 1) == '1' ? 'checked' : '' }}></td>
                    <td class="border border-gray-200 table-cell-text text-center"><input type="radio" name="llanta_delantera_vida" value="0" {{ old('llanta_delantera_vida') == '0' ? 'checked' : '' }}></td>
                   
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
                    <td class="border border-gray-200 table-cell-text text-center"><input type="radio" name="llanta_trasera_vida" value="1" {{ old('llanta_trasera_vida', 1) == '1' ? 'checked' : '' }}></td>
                    <td class="border border-gray-200 table-cell-text text-center"><input type="radio" name="llanta_trasera_vida" value="0" {{ old('llanta_trasera_vida') == '0' ? 'checked' : '' }}></td>
                    
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
                        <textarea name="mantenimiento_preventivo" rows="3" class="w-full border-gray-300 rounded table-input" placeholder="Describa el mantenimiento preventivo realizado...">{{ old('mantenimiento_preventivo') }}</textarea>
                    </td>
                </tr>

                <tr>
                    <td class="border border-gray-200 p-2 font-semibold" colspan="12">Mantenimiento Correctivo</td>
                </tr>
                <tr>
                    <td class="border border-gray-200 table-cell-text" colspan="12">
                        <textarea name="mantenimiento_correctivo" rows="3" class="w-full border-gray-300 rounded table-input" placeholder="Describa el mantenimiento correctivo realizado...">{{ old('mantenimiento_correctivo') }}</textarea>
                    </td>
                </tr>

                <tr>
                    <td class="border border-gray-200 p-2 font-semibold" colspan="12">Condición de Carrocería</td>
                </tr>
                <tr>
                    <td class="border border-gray-200 table-cell-text" colspan="12">
                        <div class="mb-3">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Marcar Daños en el Vehículo (Dibuja sobre la imagen)</label>
                            
                            <!-- Botón fullscreen -->
                            <div class="mb-3 flex justify-end">
                                <button type="button" onclick="toggleFullscreen()" class="bg-purple-600 text-white px-3 py-2 sm:px-4 sm:py-2 rounded hover:bg-purple-700 text-sm">
                                    <i class="fas fa-expand mr-1"></i><span id="fullscreenText">Pantalla Completa</span>
                                </button>
                            </div>
                            
                            <!-- Contenedor del canvas -->
                            <div id="canvasContainer" class="border-2 border-gray-300 rounded bg-white relative">
                                <canvas id="carDamageCanvas" class="w-full h-auto touch-none" style="display: block; max-width: 100%;"></canvas>
                                
                                <!-- Controles del canvas -->
                                <div id="canvasControls" class="mt-2 sm:mt-3 flex flex-wrap gap-2 items-center p-2 bg-gray-50 rounded">
                                    <button type="button" onclick="clearCanvas()" class="bg-red-500 text-white px-2 py-1.5 sm:px-3 sm:py-2 rounded hover:bg-red-600 text-xs sm:text-sm flex items-center gap-1">
                                        <i class="fas fa-trash"></i><span class="hidden sm:inline">Limpiar</span>
                                    </button>
                                    <button type="button" onclick="undoLastStroke()" class="bg-yellow-500 text-white px-2 py-1.5 sm:px-3 sm:py-2 rounded hover:bg-yellow-600 text-xs sm:text-sm flex items-center gap-1">
                                        <i class="fas fa-undo"></i><span class="hidden sm:inline">Deshacer</span>
                                    </button>
                                    <select id="drawColor" class="border rounded px-2 py-1.5 text-xs sm:text-sm flex-grow sm:flex-grow-0">
                                        <option value="#ff0000">🔴 Severo</option>
                                        <option value="#ff8800">🟠 Moderado</option>
                                        <option value="#ffff00">🟡 Leve</option>
                                        <option value="#0088ff">🔵 Abolladura</option>
                                        <option value="#00ff00">🟢 Desgaste</option>
                                    </select>
                                    <label class="text-xs sm:text-sm flex items-center gap-1">
                                        <i class="fas fa-paint-brush"></i>
                                        <input type="range" id="lineWidth" min="2" max="20" value="5" class="w-16 sm:w-24">
                                        <span id="lineWidthValue" class="font-mono w-6 text-xs sm:text-sm">5</span>
                                    </label>
                                </div>
                            </div>
                            
                            <input type="hidden" name="condicion_carroceria_imagen" id="carDamageImageData">
                        </div>
                        <textarea name="condicion_carroceria_log" rows="3" class="w-full border-gray-300 rounded table-input" placeholder="Describa detalladamente los daños marcados en la imagen (ej: 1. Rayón en puerta delantera izquierda, 2. Abolladura en cofre...)">{{ old('condicion_carroceria_log') }}</textarea>
                    </td>
                </tr>

                <tr>
                    <td class="border border-gray-200 p-2 font-semibold" colspan="6">Responsable de recibo o uso:</td>
                    <td class="border border-gray-200 p-2 font-semibold" colspan="6">Responsable de entrega:</td>
                </tr>
                <tr>
                    <td class="border border-gray-200 table-cell-text" colspan="6">
                        <input type="text" name="responsable_recibo_uso" value="{{ old('responsable_recibo_uso', $ticket->user->name) }}" class="w-full border-gray-300 rounded table-input" placeholder="Nombre del responsable" required>
                    </td>
                    <td class="border border-gray-200 table-cell-text" colspan="6">
                        <input type="text" name="responsable_entrega" value="{{ old('responsable_entrega', auth()->user()->name) }}" class="w-full border-gray-300 rounded table-input" placeholder="Nombre del responsable" required>
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
                <a href="{{ route('tickets.show', $ticket) }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold py-2 px-4 sm:px-6 rounded-lg transition duration-200 text-center text-sm sm:text-base">
                    Cancelar
                </a>
                <button type="submit" class="bg-purple-600 hover:bg-purple-700 text-white font-semibold py-2 px-4 sm:px-6 rounded-lg transition duration-200 text-sm sm:text-base">
                    <i class="fas fa-check-circle mr-2"></i>Completar Checkout
                </button>
            </div>
        </form>
    </div>

    <!-- Información Adicional -->
    <div class="mt-4 sm:mt-6 bg-purple-50 border border-purple-200 rounded-lg p-3 sm:p-4">
        <h3 class="text-xs sm:text-sm font-semibold text-purple-800 mb-2">
            <i class="fas fa-info-circle mr-2"></i>Información del Checkout
        </h3>
        <ul class="text-xs sm:text-sm text-purple-700 space-y-1">
            <li>• Verifica cuidadosamente el estado del vehículo antes de salir</li>
            <li>• Los campos marcados con <span class="text-red-500">*</span> son obligatorios</li>
            <li>• El folio se generará automáticamente al completar el checkout</li>
            <li>• Asegúrate de revisar todos los items del checklist</li>
            <li>• Al completar, se enviará notificación al solicitante y encargados</li>
        </ul>
    </div>
</div>

<script>
    // Mejorar experiencia de scroll en móvil
    document.addEventListener('DOMContentLoaded', function() {
        const scrollContainer = document.querySelector('.table-scroll-container');
        const scrollIndicator = document.querySelector('.scroll-indicator');
        
        if (scrollContainer && scrollIndicator) {
            let hasScrolled = false;
            
            scrollContainer.addEventListener('scroll', function() {
                if (!hasScrolled) {
                    scrollIndicator.style.display = 'none';
                    hasScrolled = true;
                }

                const isScrolledToEnd = scrollContainer.scrollLeft + scrollContainer.clientWidth >= scrollContainer.scrollWidth - 10;
                
                if (isScrolledToEnd) {
                    scrollContainer.classList.add('scrolled-end');
                } else {
                    scrollContainer.classList.remove('scrolled-end');
                }
            });

            if (window.innerWidth < 640) {
                const needsScroll = scrollContainer.scrollWidth > scrollContainer.clientWidth;
                if (!needsScroll) {
                    scrollIndicator.style.display = 'none';
                    scrollContainer.classList.add('scrolled-end');
                }
            }
        }
    });

    // Canvas para dibujar sobre el vehículo
    const canvas = document.getElementById('carDamageCanvas');
    const ctx = canvas.getContext('2d');
    const canvasContainer = document.getElementById('canvasContainer');
    let vehicleImage = new Image();
    let imageLoaded = false;
    
    // Variables para el dibujo de líneas
    let isDrawing = false;
    let strokes = []; // Array de trazos
    let currentStroke = []; // Trazo actual
    
    // Cargar imagen del vehículo
    function loadVehicleImage() {
        imageLoaded = false;
        vehicleImage = new Image();
        vehicleImage.crossOrigin = 'anonymous';
        vehicleImage.onload = function() {
            imageLoaded = true;
            canvas.width = vehicleImage.width;
            canvas.height = vehicleImage.height;
            redrawCanvas();
        };
        vehicleImage.onerror = function() {
            console.error('Error al cargar la imagen del vehículo');
        };
        vehicleImage.src = '{{ asset('images/autos.png') }}';
    }

    function redrawCanvas() {
        if (!imageLoaded) return;
        
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        ctx.drawImage(vehicleImage, 0, 0, canvas.width, canvas.height);
        
        // Redibujar todos los trazos
        strokes.forEach(stroke => {
            if (stroke.points.length < 2) return;
            
            ctx.beginPath();
            ctx.strokeStyle = stroke.color;
            ctx.lineWidth = stroke.width;
            ctx.lineCap = 'round';
            ctx.lineJoin = 'round';
            
            ctx.moveTo(stroke.points[0].x, stroke.points[0].y);
            for (let i = 1; i < stroke.points.length; i++) {
                ctx.lineTo(stroke.points[i].x, stroke.points[i].y);
            }
            ctx.stroke();
        });
    }

    function getCanvasPoint(e) {
        const rect = canvas.getBoundingClientRect();
        const scaleX = canvas.width / rect.width;
        const scaleY = canvas.height / rect.height;
        
        let clientX, clientY;
        
        if (e.touches && e.touches.length > 0) {
            clientX = e.touches[0].clientX;
            clientY = e.touches[0].clientY;
        } else {
            clientX = e.clientX;
            clientY = e.clientY;
        }
        
        return {
            x: (clientX - rect.left) * scaleX,
            y: (clientY - rect.top) * scaleY
        };
    }

    function startDrawing(e) {
        if (!imageLoaded) return;
        e.preventDefault();
        
        isDrawing = true;
        const point = getCanvasPoint(e);
        const color = document.getElementById('drawColor').value;
        const width = parseInt(document.getElementById('lineWidth').value);
        
        currentStroke = {
            points: [point],
            color: color,
            width: width
        };
    }

    function draw(e) {
        if (!isDrawing || !imageLoaded) return;
        e.preventDefault();
        
        const point = getCanvasPoint(e);
        currentStroke.points.push(point);
        
        // Dibujar el segmento actual
        ctx.beginPath();
        ctx.strokeStyle = currentStroke.color;
        ctx.lineWidth = currentStroke.width;
        ctx.lineCap = 'round';
        ctx.lineJoin = 'round';
        
        const prevPoint = currentStroke.points[currentStroke.points.length - 2];
        ctx.moveTo(prevPoint.x, prevPoint.y);
        ctx.lineTo(point.x, point.y);
        ctx.stroke();
    }

    function stopDrawing(e) {
        if (!isDrawing) return;
        e.preventDefault();
        
        isDrawing = false;
        if (currentStroke.points.length > 0) {
            strokes.push(currentStroke);
            saveCanvasData();
        }
        currentStroke = [];
    }

    // Event listeners para mouse
    canvas.addEventListener('mousedown', startDrawing);
    canvas.addEventListener('mousemove', draw);
    canvas.addEventListener('mouseup', stopDrawing);
    canvas.addEventListener('mouseleave', stopDrawing);

    // Event listeners para touch (móvil)
    canvas.addEventListener('touchstart', startDrawing);
    canvas.addEventListener('touchmove', draw);
    canvas.addEventListener('touchend', stopDrawing);
    canvas.addEventListener('touchcancel', stopDrawing);

    function clearCanvas() {
        if (strokes.length === 0) return;
        
        if (confirm('¿Estás seguro de que quieres borrar todos los trazos?')) {
            strokes = [];
            redrawCanvas();
            saveCanvasData();
        }
    }

    function undoLastStroke() {
        if (strokes.length > 0) {
            strokes.pop();
            redrawCanvas();
            saveCanvasData();
        }
    }

    function saveCanvasData() {
        if (!imageLoaded) return;
        const dataURL = canvas.toDataURL('image/png');
        document.getElementById('carDamageImageData').value = dataURL;
    }

    // Actualizar valor del grosor de línea
    document.getElementById('lineWidth').addEventListener('input', function(e) {
        document.getElementById('lineWidthValue').textContent = e.target.value;
    });

    // Pantalla completa
    let isFullscreen = false;
    function toggleFullscreen() {
        const container = canvasContainer;
        const controls = document.getElementById('canvasControls');
        const fullscreenText = document.getElementById('fullscreenText');
        
        isFullscreen = !isFullscreen;
        
        if (isFullscreen) {
            container.style.cssText = 'position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; z-index: 9999; margin: 0; border-radius: 0; display: flex; flex-direction: column; padding: 1rem; background: white;';
            controls.style.cssText = 'position: absolute; bottom: 1rem; left: 1rem; right: 1rem; background: white; box-shadow: 0 4px 6px rgba(0,0,0,0.1); z-index: 10000; border-radius: 0.5rem;';
            canvas.style.cssText = 'width: 100%; height: calc(100% - 80px); object-fit: contain; touch-action: none;';
            fullscreenText.textContent = 'Salir';
            document.body.style.overflow = 'hidden';
        } else {
            container.style.cssText = '';
            controls.style.cssText = '';
            canvas.style.cssText = 'display: block; max-width: 100%; width: 100%; height: auto; touch-action: none;';
            fullscreenText.textContent = 'Pantalla Completa';
            document.body.style.overflow = '';
            
            // Redibujar después de salir de fullscreen
            setTimeout(() => redrawCanvas(), 100);
        }
    }

    function validateForm() {
        // Asegurar que la imagen del canvas se guarde antes de enviar
        if (imageLoaded && strokes.length > 0) {
            saveCanvasData();
            console.log('Imagen del vehículo guardada correctamente');
        }
        return true;
    }

    // Inicializar al cargar la página
    loadVehicleImage();
</script>
@endsection
