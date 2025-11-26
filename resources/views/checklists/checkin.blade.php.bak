@extends('layouts.app')

@section('title', 'Checkin Vehículo - Ticket #' . $ticket->id)

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('tickets.show', $ticket) }}" class="text-blue-600 hover:text-blue-800 font-medium">
            <i class="fas fa-arrow-left mr-2"></i>Volver al Ticket
        </a>
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

    <form action="{{ route('checklists.checkin.store', $ticket) }}" method="POST" class="space-y-6">
        @csrf

        <!-- Header con información del ticket -->
        <div class="bg-gradient-to-r from-orange-600 to-orange-800 text-white rounded-lg shadow-md p-6">
            <h1 class="text-3xl font-bold mb-2">Checklist de Entrada (Checkin)</h1>
            <p class="text-orange-100">Ticket #{{ $ticket->id }} - {{ $ticket->destination }}</p>
        </div>

        <!-- Información General -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-2xl font-bold text-gray-800 mb-4 border-b pb-2">
                <i class="fas fa-info-circle mr-2"></i>Información General
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Fecha <span class="text-red-500">*</span></label>
                    <input type="date" name="fecha" value="{{ old('fecha', now()->toDateString()) }}" class="w-full border-gray-300 rounded-md shadow-sm focus:border-orange-500 focus:ring-orange-500" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Hora de Entrada <span class="text-red-500">*</span></label>
                    <input type="time" name="hora_entrada" value="{{ old('hora_entrada', now()->format('H:i')) }}" class="w-full border-gray-300 rounded-md shadow-sm focus:border-orange-500 focus:ring-orange-500" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Destino <span class="text-red-500">*</span></label>
                    <input type="text" name="destino" value="{{ old('destino', $ticket->destination) }}" class="w-full border-gray-300 rounded-md shadow-sm focus:border-orange-500 focus:ring-orange-500" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Marca <span class="text-red-500">*</span></label>
                    <input type="text" name="marca" value="{{ old('marca', $ticket->vehicle->brand) }}" class="w-full border-gray-300 rounded-md shadow-sm focus:border-orange-500 focus:ring-orange-500" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Modelo <span class="text-red-500">*</span></label>
                    <input type="text" name="modelo" value="{{ old('modelo', $ticket->vehicle->model) }}" class="w-full border-gray-300 rounded-md shadow-sm focus:border-orange-500 focus:ring-orange-500" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Placas <span class="text-red-500">*</span></label>
                    <input type="text" name="placas" value="{{ old('placas', $ticket->vehicle->license_plate) }}" class="w-full border-gray-300 rounded-md shadow-sm focus:border-orange-500 focus:ring-orange-500" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Kilometraje Final <span class="text-red-500">*</span></label>
                    <input type="number" step="0.01" name="kilometraje_final" value="{{ old('kilometraje_final') }}" class="w-full border-gray-300 rounded-md shadow-sm focus:border-orange-500 focus:ring-orange-500" required>
                    @if($checkoutChecklist)
                    <p class="text-xs text-gray-500 mt-1">Kilometraje inicial: {{ number_format($checkoutChecklist->kilometraje_inicial, 2) }} km</p>
                    @endif
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nivel de Combustible Final <span class="text-red-500">*</span></label>
                    <select name="nivel_combustible_final" class="w-full border-gray-300 rounded-md shadow-sm focus:border-orange-500 focus:ring-orange-500" required>
                        <option value="">Seleccionar...</option>
                        <option value="1/4" {{ old('nivel_combustible_final') == '1/4' ? 'selected' : '' }}>1/4</option>
                        <option value="1/2" {{ old('nivel_combustible_final') == '1/2' ? 'selected' : '' }}>1/2</option>
                        <option value="3/4" {{ old('nivel_combustible_final') == '3/4' ? 'selected' : '' }}>3/4</option>
                        <option value="Lleno" {{ old('nivel_combustible_final') == 'Lleno' ? 'selected' : '' }}>Lleno</option>
                    </select>
                    @if($checkoutChecklist)
                    <p class="text-xs text-gray-500 mt-1">Combustible inicial: {{ $checkoutChecklist->nivel_combustible_inicial }}</p>
                    @endif
                </div>
            </div>
        </div>

        @if($checkoutChecklist)
        <!-- Comparación con Checkout -->
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
            <h3 class="text-sm font-semibold text-blue-800 mb-2">
                <i class="fas fa-info-circle mr-2"></i>Información del Checkout
            </h3>
            <div class="text-sm text-blue-700 grid grid-cols-2 md:grid-cols-4 gap-2">
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
                    <strong>Combustible:</strong> {{ $checkoutChecklist->nivel_combustible_inicial }}
                </div>
            </div>
        </div>
        @endif

        <!-- Llantas -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-2xl font-bold text-gray-800 mb-4 border-b pb-2">
                <i class="fas fa-circle-notch mr-2"></i>Llantas
            </h2>
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                @php
                    $llantas = [
                        'llanta_delantera_derecha' => 'Delantera Derecha',
                        'llanta_delantera_izquierda' => 'Delantera Izquierda',
                        'llanta_trasera_derecha' => 'Trasera Derecha',
                        'llanta_trasera_izquierda' => 'Trasera Izquierda',
                        'llanta_refaccion' => 'Llanta de Refacción',
                        'presion_adecuada' => 'Presión Adecuada',
                    ];
                @endphp
                @foreach($llantas as $field => $label)
                <label class="flex items-center space-x-2 p-3 border rounded-lg hover:bg-gray-50 cursor-pointer">
                    <input type="checkbox" name="{{ $field }}" value="1" {{ old($field, true) ? 'checked' : '' }} class="form-checkbox h-5 w-5 text-orange-600 rounded focus:ring-orange-500">
                    <span class="text-sm font-medium text-gray-700">{{ $label }}</span>
                </label>
                @endforeach
            </div>
        </div>

        <!-- Frontal -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-2xl font-bold text-gray-800 mb-4 border-b pb-2">
                <i class="fas fa-car mr-2"></i>Frontal
            </h2>
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                @php
                    $frontal = [
                        'parabrisas' => 'Parabrisas',
                        'cofre' => 'Cofre',
                        'parrilla' => 'Parrilla',
                        'defensas' => 'Defensas',
                        'molduras' => 'Molduras',
                        'placa' => 'Placa',
                        'salpicadera' => 'Salpicadera',
                        'antena' => 'Antena',
                    ];
                @endphp
                @foreach($frontal as $field => $label)
                <label class="flex items-center space-x-2 p-3 border rounded-lg hover:bg-gray-50 cursor-pointer">
                    <input type="checkbox" name="{{ $field }}" value="1" {{ old($field, true) ? 'checked' : '' }} class="form-checkbox h-5 w-5 text-orange-600 rounded focus:ring-orange-500">
                    <span class="text-sm font-medium text-gray-700">{{ $label }}</span>
                </label>
                @endforeach
            </div>
        </div>

        <!-- Luces -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-2xl font-bold text-gray-800 mb-4 border-b pb-2">
                <i class="fas fa-lightbulb mr-2"></i>Luces
            </h2>
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                @php
                    $luces = [
                        'intermitentes' => 'Intermitentes',
                        'direccional_derecha' => 'Direccional Derecha',
                        'direccional_izquierda' => 'Direccional Izquierda',
                        'luz_stop' => 'Luz Stop',
                        'faros' => 'Faros',
                        'luces_altas' => 'Luces Altas',
                        'luz_interior' => 'Luz Interior',
                        'calaveras_buen_estado' => 'Calaveras Buen Estado',
                    ];
                @endphp
                @foreach($luces as $field => $label)
                <label class="flex items-center space-x-2 p-3 border rounded-lg hover:bg-gray-50 cursor-pointer">
                    <input type="checkbox" name="{{ $field }}" value="1" {{ old($field, true) ? 'checked' : '' }} class="form-checkbox h-5 w-5 text-orange-600 rounded focus:ring-orange-500">
                    <span class="text-sm font-medium text-gray-700">{{ $label }}</span>
                </label>
                @endforeach
            </div>
        </div>

        <!-- Otros (Seguridad) -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-2xl font-bold text-gray-800 mb-4 border-b pb-2">
                <i class="fas fa-shield-alt mr-2"></i>Otros (Seguridad y Documentos)
            </h2>
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                @php
                    $otros = [
                        'mata_chispas' => 'Mata Chispas',
                        'alarma' => 'Alarma',
                        'extintor' => 'Extintor',
                        'botiquin' => 'Botiquín',
                        'tarjeta_circulacion' => 'Tarjeta de Circulación',
                        'licencia_conducir_vigente' => 'Licencia de Conducir Vigente',
                        'poliza_seguro' => 'Póliza de Seguro',
                        'triangulo_emergencia' => 'Triángulo de Emergencia',
                    ];
                @endphp
                @foreach($otros as $field => $label)
                <label class="flex items-center space-x-2 p-3 border rounded-lg hover:bg-gray-50 cursor-pointer">
                    <input type="checkbox" name="{{ $field }}" value="1" {{ old($field, true) ? 'checked' : '' }} class="form-checkbox h-5 w-5 text-orange-600 rounded focus:ring-orange-500">
                    <span class="text-sm font-medium text-gray-700">{{ $label }}</span>
                </label>
                @endforeach
            </div>
        </div>

        <!-- Interior -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-2xl font-bold text-gray-800 mb-4 border-b pb-2">
                <i class="fas fa-couch mr-2"></i>Interior
            </h2>
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                @php
                    $interior = [
                        'tablero_indicadores' => 'Tablero Indicadores',
                        'switch_encendido' => 'Switch de Encendido',
                        'controles_ac' => 'Controles A/C',
                        'defroster' => 'Defroster',
                        'radio' => 'Radio',
                        'volante' => 'Volante',
                        'bolsas_aire' => 'Bolsas de Aire',
                        'cinturon_seguridad' => 'Cinturón de Seguridad',
                        'coderas' => 'Coderas',
                        'espejo_interior' => 'Espejo Interior',
                        'freno_mano' => 'Freno de Mano',
                        'encendedor' => 'Encendedor',
                        'guantera' => 'Guantera',
                        'manijas_interiores' => 'Manijas Interiores',
                        'seguros' => 'Seguros',
                        'asientos' => 'Asientos',
                        'tapetes_delanteros_traseros' => 'Tapetes Delanteros y Traseros',
                    ];
                @endphp
                @foreach($interior as $field => $label)
                <label class="flex items-center space-x-2 p-3 border rounded-lg hover:bg-gray-50 cursor-pointer">
                    <input type="checkbox" name="{{ $field }}" value="1" {{ old($field, true) ? 'checked' : '' }} class="form-checkbox h-5 w-5 text-orange-600 rounded focus:ring-orange-500">
                    <span class="text-sm font-medium text-gray-700">{{ $label }}</span>
                </label>
                @endforeach
            </div>
        </div>

        <!-- Motor -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-2xl font-bold text-gray-800 mb-4 border-b pb-2">
                <i class="fas fa-cog mr-2"></i>Motor
            </h2>
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                @php
                    $motor = [
                        'nivel_aceite_motor' => 'Nivel Aceite Motor',
                        'nivel_anticongelante' => 'Nivel Anticongelante',
                        'nivel_liquido_frenos' => 'Nivel Líquido Frenos',
                        'bateria' => 'Batería',
                        'bayoneta_aceite_motor' => 'Bayoneta de Aceite Motor',
                        'tapones' => 'Tapones',
                        'bocina_claxon' => 'Bocina / Claxon',
                        'radiador' => 'Radiador',
                    ];
                @endphp
                @foreach($motor as $field => $label)
                <label class="flex items-center space-x-2 p-3 border rounded-lg hover:bg-gray-50 cursor-pointer">
                    <input type="checkbox" name="{{ $field }}" value="1" {{ old($field, true) ? 'checked' : '' }} class="form-checkbox h-5 w-5 text-orange-600 rounded focus:ring-orange-500">
                    <span class="text-sm font-medium text-gray-700">{{ $label }}</span>
                </label>
                @endforeach
            </div>
        </div>

        <!-- Herramienta -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-2xl font-bold text-gray-800 mb-4 border-b pb-2">
                <i class="fas fa-toolbox mr-2"></i>Herramienta
            </h2>
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                @php
                    $herramienta = [
                        'gato' => 'Gato',
                        'llave_ruedas' => 'Llave de Ruedas',
                        'cables_pasa_corriente' => 'Cables Pasa Corriente',
                        'caja_bolsa_herramientas' => 'Caja o Bolsa de Herramientas',
                        'dado_birlo_seguridad' => 'Dado o Birlo de Seguridad',
                    ];
                @endphp
                @foreach($herramienta as $field => $label)
                <label class="flex items-center space-x-2 p-3 border rounded-lg hover:bg-gray-50 cursor-pointer">
                    <input type="checkbox" name="{{ $field }}" value="1" {{ old($field, true) ? 'checked' : '' }} class="form-checkbox h-5 w-5 text-orange-600 rounded focus:ring-orange-500">
                    <span class="text-sm font-medium text-gray-700">{{ $label }}</span>
                </label>
                @endforeach
            </div>
        </div>

        <!-- Calcomanías -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-2xl font-bold text-gray-800 mb-4 border-b pb-2">
                <i class="fas fa-tag mr-2"></i>Calcomanías
            </h2>
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                @php
                    $calcomanias = [
                        'calcomanias_permisos' => 'Calcomanías de Permisos',
                        'calcomania_velocidad_maxima' => 'Calcomanía Velocidad Máxima',
                    ];
                @endphp
                @foreach($calcomanias as $field => $label)
                <label class="flex items-center space-x-2 p-3 border rounded-lg hover:bg-gray-50 cursor-pointer">
                    <input type="checkbox" name="{{ $field }}" value="1" {{ old($field, true) ? 'checked' : '' }} class="form-checkbox h-5 w-5 text-orange-600 rounded focus:ring-orange-500">
                    <span class="text-sm font-medium text-gray-700">{{ $label }}</span>
                </label>
                @endforeach
            </div>
        </div>

        <!-- Observaciones y Firmas -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-2xl font-bold text-gray-800 mb-4 border-b pb-2">
                <i class="fas fa-clipboard-list mr-2"></i>Observaciones y Responsables
            </h2>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Mantenimiento Preventivo</label>
                    <textarea name="mantenimiento_preventivo" rows="2" class="w-full border-gray-300 rounded-md shadow-sm focus:border-orange-500 focus:ring-orange-500" placeholder="Detalles del mantenimiento preventivo requerido...">{{ old('mantenimiento_preventivo') }}</textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Mantenimiento Correctivo</label>
                    <textarea name="mantenimiento_correctivo" rows="2" class="w-full border-gray-300 rounded-md shadow-sm focus:border-orange-500 focus:ring-orange-500" placeholder="Detalles del mantenimiento correctivo necesario...">{{ old('mantenimiento_correctivo') }}</textarea>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Responsable de Recibo o Uso <span class="text-red-500">*</span></label>
                        <input type="text" name="responsable_recibo_uso" value="{{ old('responsable_recibo_uso', auth()->user()->name) }}" class="w-full border-gray-300 rounded-md shadow-sm focus:border-orange-500 focus:ring-orange-500" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Responsable de Entrega <span class="text-red-500">*</span></label>
                        <input type="text" name="responsable_entrega" value="{{ old('responsable_entrega', $ticket->user->name) }}" class="w-full border-gray-300 rounded-md shadow-sm focus:border-orange-500 focus:ring-orange-500" required>
                    </div>
                </div>
            </div>
        </div>

        <!-- Botones -->
        <div class="flex justify-end gap-4 pb-6">
            <a href="{{ route('tickets.show', $ticket) }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold py-3 px-6 rounded-lg transition duration-200">
                <i class="fas fa-times mr-2"></i>Cancelar
            </a>
            <button type="submit" class="bg-orange-600 hover:bg-orange-700 text-white font-semibold py-3 px-6 rounded-lg transition duration-200">
                <i class="fas fa-check-circle mr-2"></i>Completar Checkin
            </button>
        </div>
    </form>
</div>

<script>
    // Marcar/desmarcar todos los checkboxes de una sección al hacer doble click en el encabezado
    document.querySelectorAll('h2').forEach(header => {
        if (header.querySelector('i')) {
            header.style.cursor = 'pointer';
            header.addEventListener('dblclick', function() {
                const section = this.closest('.bg-white');
                const checkboxes = section.querySelectorAll('input[type="checkbox"]');
                const allChecked = Array.from(checkboxes).every(cb => cb.checked);
                checkboxes.forEach(cb => cb.checked = !allChecked);
            });
        }
    });

    // Validar que el kilometraje final sea mayor al inicial
    @if($checkoutChecklist)
    const kmFinalInput = document.querySelector('input[name="kilometraje_final"]');
    const kmInicial = {{ $checkoutChecklist->kilometraje_inicial }};
    
    kmFinalInput.addEventListener('blur', function() {
        const kmFinal = parseFloat(this.value);
        if (kmFinal < kmInicial) {
            alert('El kilometraje final (' + kmFinal + ' km) no puede ser menor al kilometraje inicial (' + kmInicial + ' km)');
            this.value = '';
            this.focus();
        }
    });
    @endif
</script>
@endsection
