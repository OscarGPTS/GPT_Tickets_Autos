@extends('layouts.app')

@section('title', 'Detalle de Solicitud #' . $ticket->folio)

@section('content')
<div class="max-w-5xl mx-auto">
    <div class="mb-6">
        <a href="/dashboard" class="text-blue-600 hover:text-blue-800 font-medium">
            <i class="fas fa-arrow-left mr-2"></i>Volver a Solicitudes
        </a>
    </div>

    @if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
        {{ session('success') }}
    </div>
    @endif

    @if(session('error'))
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
        {{ session('error') }}
    </div>
    @endif

    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <!-- Header -->
        <div class="bg-gradient-to-r from-blue-600 to-blue-800 px-6 py-4">
            <div class="flex justify-between items-start">
                <div>
                    <h1 class="text-2xl font-bold text-white">Requisición #{{ $ticket->folio }}</h1>
                </div>
                <div>
                    @php
                        $statusColors = [
                            'pendiente' => 'bg-amber-100 text-amber-800 border-amber-300',
                            'aprobado' => 'bg-blue-100 text-blue-800 border-blue-300',
                            'rechazado' => 'bg-red-100 text-red-800 border-red-300',
                            'en_uso' => 'bg-indigo-100 text-indigo-800 border-indigo-300',
                            'completado' => 'bg-green-100 text-green-800 border-green-300',
                        ];
                    @endphp
                    <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full border-2 {{ $statusColors[$ticket->status] ?? 'bg-gray-100 text-gray-800' }}">
                        {{ strtoupper($ticket->status) }}
                    </span>
                </div>
            </div>
        </div>

        <div class="p-6">
            <!-- Información del Viaje -->
            <div class="mb-6">
                <h2 class="text-xl font-bold text-gray-800 mb-4 border-b pb-2">Información del Viaje</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @if($ticket->requisicion)
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Requisición</p>
                        <p class="text-lg font-semibold text-gray-900">{{ $ticket->requisicion }}</p>
                    </div>
                    @endif
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Destino</p>
                        <p class="text-lg font-semibold text-gray-900">{{ $ticket->destination }}</p>
                    </div>
                    @if($ticket->cliente)
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Cliente</p>
                        <p class="text-lg font-semibold text-gray-900">{{ $ticket->cliente }}</p>
                    </div>
                    @endif

                    <div>
                        <p class="text-sm text-gray-600 mb-1">Fecha de Salida</p>
                        <p class="text-lg font-semibold text-gray-900">{{ \Carbon\Carbon::parse($ticket->requested_date)->format('d/m/Y') }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Hora de Salida</p>
                        <p class="text-lg font-semibold text-gray-900">{{ $ticket->requested_time_start ? \Carbon\Carbon::parse($ticket->requested_time_start)->format('H:i') : 'N/A' }}</p>
                    </div>
                    @if($ticket->requested_time_end)
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Hora de Regreso Estimada</p>
                        <p class="text-lg font-semibold text-gray-900">{{ \Carbon\Carbon::parse($ticket->requested_time_end)->format('H:i') }}</p>
                    </div>
                    @endif
                </div>
                <div class="mt-4">
                    <p class="text-sm text-gray-600 mb-1">Propósito del Viaje</p>
                    <p class="text-lg font-semibold text-gray-900">{{ $ticket->purpose }}</p>
                </div>

            </div>

            <!-- Información del Solicitante -->
            <div class="mb-6">
                <h2 class="text-xl font-bold text-gray-800 mb-4 border-b pb-2">Vehículo Asignado</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Vehiculo</p>
                        <p class="text-lg font-semibold text-gray-900">{{ $ticket->vehicle ? $ticket->vehicle->brand . ' ' . $ticket->vehicle->model : 'Pendiente de asignar' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Despachador</p>
                        <p class="text-lg font-semibold text-gray-900">{{ $ticket->dispatcher ? $ticket->dispatcher->name : 'Pendiente de asignar' }}</p>
                    </div>
                </div>
            </div>

            <!-- Información del Vehículo Asignado -->
            @if($ticket->vehicle)
            <div class="mb-6">
                <h2 class="text-xl font-bold text-gray-800 mb-4 border-b pb-2">Vehículo Asignado</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Marca y Modelo</p>
                        <p class="text-lg font-semibold text-gray-900">{{ $ticket->vehicle->brand }} {{ $ticket->vehicle->model }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Placas</p>
                        <p class="text-lg font-semibold text-gray-900">{{ $ticket->vehicle->license_plate }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Capacidad</p>
                        <p class="text-lg font-semibold text-gray-900">{{ $ticket->vehicle->capacity }} pasajeros</p>
                    </div>
                </div>
            </div>
            @endif

            <!-- Asignar Despachador (Solo para encargados) -->
            @if(auth()->user()->hasRole('encargado') || auth()->user()->hasRole('admin'))
                @if($ticket->status === 'aprobado' && !$ticket->dispatcher_id)
                <div class="bg-blue-50 border border-blue-300 rounded-lg p-4 mb-6">
                    <h3 class="text-sm font-semibold text-blue-800 mb-3">
                        <i class="fas fa-user-tie mr-2"></i>Asignar Despachador
                    </h3>
                    <form action="{{ route('tickets.assign.dispatcher', $ticket) }}" method="POST" class="flex flex-col sm:flex-row gap-3">
                        @csrf
                        <select name="dispatcher_id" class="flex-1 border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                            <option value="">Seleccionar despachador...</option>
                            @foreach($dispatchers as $dispatcher)
                                <option value="{{ $dispatcher->id }}">{{ $dispatcher->name }}</option>
                            @endforeach
                        </select>
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-200">
                            <i class="fas fa-check mr-2"></i>Asignar
                        </button>
                    </form>
                </div>
                @endif
            @endif

            @if($ticket->dispatcher)
            <div class="bg-green-50 rounded-lg p-3 mb-6">
                <p class="text-sm text-green-800">
                    <i class="fas fa-user-check mr-2"></i>
                    <strong>Despachador asignado:</strong> {{ $ticket->dispatcher->name }}
                </p>
            </div>
            @endif

            <!-- Aprobación/Rechazo -->
            @if($ticket->status !== 'pendiente')
            <div class="mb-6">
                <h2 class="text-xl font-bold text-gray-800 mb-4 border-b pb-2">
                    {{ $ticket->status === 'rechazado' ? 'Rechazo' : 'Aprobación' }}
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @if($ticket->approvedBy)
                    <div>
                        <p class="text-sm text-gray-600 mb-1">{{ $ticket->status === 'rechazado' ? 'Rechazado' : 'Aprobado' }} por</p>
                        <p class="text-lg font-semibold text-gray-900">{{ $ticket->approvedBy->name }}</p>
                    </div>
                    @endif
                    @if($ticket->approved_at)
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Fecha</p>
                        <p class="text-lg font-semibold text-gray-900">{{ \Carbon\Carbon::parse($ticket->approved_at)->format('d/m/Y H:i') }}</p>
                    </div>
                    @endif
                    @if($ticket->rejection_reason)
                    <div class="md:col-span-2">
                        <p class="text-sm text-gray-600 mb-1">Motivo del Rechazo</p>
                        <p class="text-red-700">{{ $ticket->rejection_reason }}</p>
                    </div>
                    @endif
                </div>
            </div>
            @endif

            <!-- Calificación -->
            @if($ticket->service_rating)
            <div class="mb-6">
                <h2 class="text-xl font-bold text-gray-800 mb-4 border-b pb-2 flex items-center gap-2">
                    <i class="fas fa-star text-amber-500"></i>
                    Calificación del Servicio
                </h2>
                <div class="bg-gradient-to-r from-amber-50 to-yellow-50 rounded-xl p-5 border-2 border-amber-200 shadow-sm">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center gap-3">
                            <div class="bg-white rounded-full px-4 py-2 shadow-md">
                                <span class="text-3xl font-bold text-amber-600">{{ $ticket->service_rating }}</span>
                                <span class="text-gray-500 text-lg">/5</span>
                            </div>
                            <div class="flex gap-1 text-2xl">
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="fas fa-star {{ $i <= $ticket->service_rating ? 'text-amber-400' : 'text-gray-300' }}"></i>
                                @endfor
                            </div>
                        </div>
                        @php
                            $ratingLabels = [
                                1 => ['text' => 'Insatisfecho', 'color' => 'text-red-600', 'emoji' => '😞'],
                                2 => ['text' => 'Regular', 'color' => 'text-orange-600', 'emoji' => '😐'],
                                3 => ['text' => 'Bueno', 'color' => 'text-yellow-600', 'emoji' => '🙂'],
                                4 => ['text' => 'Muy Bueno', 'color' => 'text-lime-600', 'emoji' => '😊'],
                                5 => ['text' => 'Excelente', 'color' => 'text-green-600', 'emoji' => '🌟'],
                            ];
                            $ratingData = $ratingLabels[$ticket->service_rating] ?? ['text' => '', 'color' => '', 'emoji' => ''];
                        @endphp
                        <div class="text-right">
                            <span class="text-2xl">{{ $ratingData['emoji'] }}</span>
                            <p class="text-lg font-semibold {{ $ratingData['color'] }}">{{ $ratingData['text'] }}</p>
                        </div>
                    </div>
                    @if($ticket->rating_comments)
                    <div class="bg-white rounded-lg p-4 mt-3 border border-amber-200">
                        <p class="text-sm font-semibold text-gray-700 mb-1 flex items-center gap-2">
                            <i class="fas fa-comment-dots text-amber-500"></i>
                            Comentarios:
                        </p>
                        <p class="text-gray-700 text-sm leading-relaxed">{{ $ticket->rating_comments }}</p>
                    </div>
                    @endif
                    <div class="mt-3 text-xs text-gray-500 flex items-center gap-1">
                        <i class="fas fa-clock"></i>
                        <span>Calificado el {{ $ticket->updated_at->format('d/m/Y \a \l\a\s H:i') }}</span>
                    </div>
                </div>
            </div>
            @endif

            <!-- Formulario de Aprobación (Encargados) -->
            @can('approve', $ticket)
            @if($ticket->status === 'pendiente')
            <div class="bg-green-50 border-2 border-green-400 rounded-lg p-6 mb-6 shadow-sm">
                <div class="flex items-center mb-4">
                    <div class="bg-green-600 text-white rounded-full p-3 mr-3">
                        <i class="fas fa-check-circle text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-green-800">Aprobar Requisición y Asignar Recursos</h3>
                        <p class="text-sm text-green-700">Complete los campos requeridos</p>
                    </div>
                </div>
                <form action="{{ route('tickets.approve', $ticket) }}" method="POST" class="space-y-4">
                    @csrf
                    <div class="bg-white rounded-lg p-4 space-y-4">
                        <div>
                            <label for="vehicle_id" class="block text-sm font-medium text-gray-800 mb-2">
                                <i class="fas fa-car text-green-600 mr-1"></i>Vehículo <span class="text-red-600">*</span>
                            </label>
                            <select name="vehicle_id" id="vehicle_id" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-green-500 focus:ring-2 focus:ring-green-200 py-2.5 px-3" required>
                                <option value="">Seleccionar vehículo...</option>
                                @foreach(\App\Models\Vehicle::where('status', 'disponible')->get() as $vehicle)
                                    <option value="{{ $vehicle->id }}">
                                        {{ $vehicle->brand }} {{ $vehicle->model }} - {{ $vehicle->plates }} (Cap: {{ $vehicle->color }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="dispatcher_id" class="block text-sm font-medium text-gray-800 mb-2">
                                <i class="fas fa-user-tie text-green-600 mr-1"></i>Despachador <span class="text-red-600">*</span>
                            </label>
                            <select name="dispatcher_id" id="dispatcher_id" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-green-500 focus:ring-2 focus:ring-green-200 py-2.5 px-3" required>
                                @foreach($dispatchers as $dispatcher)
                                    <option value="{{ $dispatcher->id }}">{{ $dispatcher->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        {{-- Campo opcional de conductor ocultado por ahora; se reactiva si es necesario --}}
                        {{--
                        <div>
                            <label for="conductor_name" class="block text-sm font-medium text-gray-800 mb-2">
                                <i class="fas fa-id-card text-green-600 mr-1"></i>Nombre del Conductor (Opcional)
                            </label>
                            <input type="text" name="conductor_name" id="conductor_name" value="{{ $ticket->user->name }}" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-green-500 focus:ring-2 focus:ring-green-200 py-2.5 px-3" placeholder="Nombre completo del conductor">
                        </div>
                        --}}
                    </div>
                    <div class="flex gap-3 pt-2">
                        <button type="submit" class="flex-1 bg-green-600 hover:bg-green-700 text-white font-semibold py-3 px-6 rounded-lg shadow-sm transition duration-200">
                            <i class="fas fa-check-circle mr-2"></i>Aprobar y Asignar
                        </button>
                        <button type="button" onclick="if(confirm('¿Está seguro que desea rechazar esta requisición?')) document.getElementById('rejectForm').submit()" class="bg-red-600 hover:bg-red-700 text-white font-semibold py-3 px-6 rounded-lg shadow-sm transition duration-200">
                            <i class="fas fa-times-circle mr-2"></i>Rechazar
                        </button>
                    </div>
                </form>
                <form id="rejectForm" action="{{ route('tickets.reject', $ticket) }}" method="POST" class="hidden" onsubmit="return handleReject(event)">
                    @csrf
                    <input type="hidden" name="rejection_reason" id="rejection_reason">
                </form>
            </div>
            @endif
            @endcan

            <!-- Acciones -->
            <div class="flex flex-wrap gap-3 pt-6 border-t">
                
                @if(auth()->user()->hasRole('usuario') && $ticket->status !== 'completado')
                <a href="{{ route('tickets.edit', $ticket) }}" class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-200">
                    <i class="fas fa-edit mr-2"></i>Editar
                </a>
                @endif

                @if($ticket->status === 'aprobado' && auth()->user()->hasRole('despachador'))
                <a href="{{ route('checklists.checkout', $ticket) }}" class="bg-purple-600 hover:bg-purple-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-200">
                    <i class="fas fa-clipboard-check mr-2"></i>Realizar Checkout
                </a>
                @endif

                @if($ticket->status === 'en_curso' && auth()->user()->hasRole('despachador'))
                <a href="{{ route('checklists.checkin', $ticket) }}" class="bg-orange-600 hover:bg-orange-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-200">
                    <i class="fas fa-clipboard-check mr-2"></i>Realizar Checkin
                </a>
                @endif

                {{-- Botones para revisar checklists completados --}}
                @if($ticket->checkoutChecklist)
                    @if($ticket->checkinChecklist)
                        <a href="{{ route('checklists.checkin.view', $ticket) }}" class="bg-orange-100 hover:bg-orange-200 text-orange-800 font-semibold py-2 px-4 rounded-lg transition duration-200 border border-orange-300">
                            <i class="fas fa-eye mr-2"></i>Ver Avance
                        </a>
                    @else
                        <a href="{{ route('checklists.checkout.view', $ticket) }}" class="bg-purple-100 hover:bg-purple-200 text-purple-800 font-semibold py-2 px-4 rounded-lg transition duration-200 border border-purple-300">
                            <i class="fas fa-eye mr-2"></i>Ver Avance
                        </a>
                    @endif
              
                @endif

               

                @if(
                    $ticket->status === 'completado'
                    && !$ticket->service_rating
                    && (
                        $ticket->user_id === auth()->id()
                        || auth()->user()->isDespachador()
                        || auth()->user()->isEncargado()
                        || auth()->user()->hasRole('admin')
                    )
                )
                <button onclick="openRatingModal()" class="bg-amber-600 hover:bg-amber-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-200 shadow-lg">
                    <i class="fas fa-star mr-2"></i>Calificar Servicio
                </button>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Modal de Calificación -->
@if(
    $ticket->status === 'completado'
    && !$ticket->service_rating
    && (
        $ticket->user_id === auth()->id()
        || auth()->user()->isDespachador()
        || auth()->user()->isEncargado()
        || auth()->user()->hasRole('admin')
    )
)
<div id="ratingModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center p-4 transition-opacity duration-300">
    <div class="bg-white rounded-2xl shadow-2xl max-w-lg w-full transform transition-all duration-300 scale-95" id="modalContent">
        <!-- Header -->
        <div class="bg-gradient-to-r from-amber-500 to-amber-600 px-6 py-4 rounded-t-2xl">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="bg-white bg-opacity-20 rounded-full p-2">
                        <i class="fas fa-star text-white text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-white">Calificar Servicio</h3>
                        <p class="text-amber-100 text-sm">Solicitud #{{ $ticket->folio }}</p>
                    </div>
                </div>
                <button onclick="closeRatingModal()" class="text-white hover:bg-white hover:bg-opacity-20 rounded-full p-2 transition duration-200">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
        </div>

        <!-- Body -->
        <form action="{{ route('tickets.rate.store', $ticket) }}" method="POST" id="ratingForm">
            @csrf
            <div class="p-6 space-y-6">
                <!-- Resumen del Viaje -->
                <div class="bg-gradient-to-r from-gray-50 to-gray-100 rounded-xl p-4 border border-gray-200">
                    <h4 class="font-semibold text-gray-700 mb-3 flex items-center gap-2">
                        <i class="fas fa-route text-amber-500"></i>
                        Resumen del Viaje
                    </h4>
                    <div class="text-sm text-gray-600 space-y-2">
                        <div class="flex items-start gap-2">
                            <i class="fas fa-map-marker-alt text-gray-400 mt-1"></i>
                            <span><strong>Destino:</strong> {{ $ticket->destination }}</span>
                        </div>
                        @if($ticket->vehicle)
                        <div class="flex items-start gap-2">
                            <i class="fas fa-car text-gray-400 mt-1"></i>
                            <span><strong>Vehículo:</strong> {{ $ticket->vehicle->brand }} {{ $ticket->vehicle->model }} ({{ $ticket->vehicle->plates }})</span>
                        </div>
                        @endif
                        @if($ticket->checkout_at)
                        <div class="flex items-start gap-2">
                            <i class="fas fa-calendar-check text-gray-400 mt-1"></i>
                            <span><strong>Fecha:</strong> {{ $ticket->checkout_at->format('d/m/Y') }}</span>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Calificación con Estrellas -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-3">
                        ¿Cómo calificarías el servicio? <span class="text-red-500">*</span>
                    </label>
                    <div class="flex flex-col items-center gap-3 bg-gray-50 rounded-xl p-6 border-2 border-gray-200">
                        <div id="starRating" class="flex gap-2 text-5xl cursor-pointer select-none">
                            <i class="far fa-star star text-gray-300 hover:text-amber-400 transition-all duration-200" data-rating="1"></i>
                            <i class="far fa-star star text-gray-300 hover:text-amber-400 transition-all duration-200" data-rating="2"></i>
                            <i class="far fa-star star text-gray-300 hover:text-amber-400 transition-all duration-200" data-rating="3"></i>
                            <i class="far fa-star star text-gray-300 hover:text-amber-400 transition-all duration-200" data-rating="4"></i>
                            <i class="far fa-star star text-gray-300 hover:text-amber-400 transition-all duration-200" data-rating="5"></i>
                        </div>
                        <div id="ratingText" class="text-lg font-medium text-gray-600 min-h-[28px]"></div>
                    </div>
                    <input type="hidden" name="service_rating" id="ratingInput" value="0" required>
                    <p id="ratingError" class="mt-2 text-sm text-red-600 hidden">
                        <i class="fas fa-exclamation-circle mr-1"></i>Por favor selecciona una calificación
                    </p>
                </div>

                <!-- Comentarios -->
                <div>
                    <label for="ratingComments" class="block text-sm font-semibold text-gray-700 mb-2">
                        Comentarios Adicionales <span class="text-gray-400 text-xs font-normal">(Opcional)</span>
                    </label>
                    <textarea id="ratingComments" 
                              name="rating_comments" 
                              rows="4"
                              maxlength="255"
                              class="w-full border-gray-300 rounded-lg shadow-sm focus:border-amber-500 focus:ring-2 focus:ring-amber-200 transition duration-200 resize-none"
                              placeholder="Cuéntanos sobre tu experiencia: ¿Qué te gustó? ¿Qué podríamos mejorar?"></textarea>
                    <div class="flex justify-between items-center mt-1">
                        <p class="text-xs text-gray-500">
                            <i class="fas fa-info-circle mr-1"></i>Máximo 255 caracteres
                        </p>
                        <p class="text-xs text-gray-500">
                            <span id="charCount">0</span>/255
                        </p>
                    </div>
                </div>

                <!-- Info adicional -->
                <div class="bg-blue-50 border-l-4 border-blue-400 p-4 rounded-r-lg">
                    <div class="flex items-start gap-3">
                        <i class="fas fa-lightbulb text-blue-500 mt-1"></i>
                        <div>
                            <p class="text-sm font-medium text-blue-800">Tu opinión es importante</p>
                            <p class="text-xs text-blue-700 mt-1">
                                Nos ayuda a mejorar continuamente nuestro servicio y garantizar la mejor experiencia para todos.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="px-6 py-4 bg-gray-50 rounded-b-2xl flex justify-end gap-3 border-t border-gray-200">
                <button type="button" onclick="closeRatingModal()" class="px-6 py-2.5 bg-white border-2 border-gray-300 text-gray-700 font-semibold rounded-lg hover:bg-gray-50 transition duration-200">
                    <i class="fas fa-times mr-2"></i>Cancelar
                </button>
                <button type="submit" id="submitRatingBtn" disabled class="px-6 py-2.5 bg-gradient-to-r from-amber-500 to-amber-600 text-white font-semibold rounded-lg hover:from-amber-600 hover:to-amber-700 transition duration-200 shadow-lg disabled:opacity-50 disabled:cursor-not-allowed">
                    <i class="fas fa-paper-plane mr-2"></i>Enviar Calificación
                </button>
            </div>
        </form>
    </div>
</div>
@endif

<script>
function handleReject(event) {
    event.preventDefault();
    const reason = prompt('Por favor ingresa el motivo del rechazo:');
    if (reason && reason.trim()) {
        document.getElementById('rejection_reason').value = reason.trim();
        event.target.submit();
    } else if (reason !== null) {
        alert('Debes proporcionar un motivo para el rechazo');
    }
    return false;
}

// Modal de Calificación
function openRatingModal() {
    const modal = document.getElementById('ratingModal');
    const modalContent = document.getElementById('modalContent');
    modal.classList.remove('hidden');
    setTimeout(() => {
        modal.classList.add('opacity-100');
        modalContent.classList.remove('scale-95');
        modalContent.classList.add('scale-100');
    }, 10);
    document.body.style.overflow = 'hidden';
}

function closeRatingModal() {
    const modal = document.getElementById('ratingModal');
    const modalContent = document.getElementById('modalContent');
    modal.classList.remove('opacity-100');
    modalContent.classList.remove('scale-100');
    modalContent.classList.add('scale-95');
    setTimeout(() => {
        modal.classList.add('hidden');
        document.body.style.overflow = 'auto';
    }, 300);
}

// Sistema de calificación por estrellas
document.addEventListener('DOMContentLoaded', function() {
    const stars = document.querySelectorAll('#starRating .star');
    const ratingInput = document.getElementById('ratingInput');
    const ratingText = document.getElementById('ratingText');
    const submitBtn = document.getElementById('submitRatingBtn');
    const ratingError = document.getElementById('ratingError');
    const commentsTextarea = document.getElementById('ratingComments');
    const charCount = document.getElementById('charCount');
    
    const ratingMessages = {
        1: '😞 Insatisfecho',
        2: '😐 Regular',
        3: '🙂 Bueno',
        4: '😊 Muy Bueno',
        5: '🌟 ¡Excelente!'
    };
    
    const ratingColors = {
        1: 'text-red-500',
        2: 'text-orange-500',
        3: 'text-yellow-500',
        4: 'text-lime-500',
        5: 'text-green-500'
    };
    
    let currentRating = 0;
    
    // Hover effect
    stars.forEach(star => {
        star.addEventListener('mouseenter', function() {
            const rating = parseInt(this.dataset.rating);
            highlightStars(rating);
        });
        
        star.addEventListener('mouseleave', function() {
            highlightStars(currentRating);
        });
        
        star.addEventListener('click', function() {
            currentRating = parseInt(this.dataset.rating);
            ratingInput.value = currentRating;
            highlightStars(currentRating);
            updateRatingText(currentRating);
            submitBtn.disabled = false;
            ratingError.classList.add('hidden');
        });
    });
    
    function highlightStars(rating) {
        stars.forEach((star, index) => {
            if (index < rating) {
                star.classList.remove('far', 'text-gray-300');
                star.classList.add('fas', 'text-amber-400');
            } else {
                star.classList.remove('fas', 'text-amber-400');
                star.classList.add('far', 'text-gray-300');
            }
        });
    }
    
    function updateRatingText(rating) {
        ratingText.textContent = ratingMessages[rating] || '';
        ratingText.className = 'text-lg font-medium min-h-[28px] ' + (ratingColors[rating] || 'text-gray-600');
    }
    
    // Contador de caracteres
    if (commentsTextarea && charCount) {
        commentsTextarea.addEventListener('input', function() {
            charCount.textContent = this.value.length;
        });
    }
    
    // Validación del formulario
    const form = document.getElementById('ratingForm');
    if (form) {
        form.addEventListener('submit', function(e) {
            if (currentRating === 0) {
                e.preventDefault();
                ratingError.classList.remove('hidden');
                return false;
            }
        });
    }
    
    // Cerrar modal con ESC
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            const modal = document.getElementById('ratingModal');
            if (modal && !modal.classList.contains('hidden')) {
                closeRatingModal();
            }
        }
    });
    
    // Cerrar modal al hacer click fuera
    const modal = document.getElementById('ratingModal');
    if (modal) {
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                closeRatingModal();
            }
        });
    }
});
</script>
@endsection
