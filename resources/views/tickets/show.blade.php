@extends('layouts.app')

@section('title', 'Detalle de Solicitud #' . $ticket->id)

@section('content')
<div class="max-w-5xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('tickets.index') }}" class="text-blue-600 hover:text-blue-800 font-medium">
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
                <h2 class="text-xl font-bold text-gray-800 mb-4 border-b pb-2">Calificación del Servicio</h2>
                <div>
                    <div class="flex items-center mb-2">
                        <span class="text-2xl font-bold text-yellow-500 mr-2">{{ $ticket->service_rating }}/5</span>
                        <div class="flex">
                            @for($i = 1; $i <= 5; $i++)
                                <i class="fas fa-star {{ $i <= $ticket->service_rating ? 'text-yellow-400' : 'text-gray-300' }}"></i>
                            @endfor
                        </div>
                    </div>
                    @if($ticket->rating_comments)
                    <p class="text-gray-700 mt-2">{{ $ticket->rating_comments }}</p>
                    @endif
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
                @can('update', $ticket)
                @if($ticket->status === 'pendiente')
                <a href="{{ route('tickets.edit', $ticket) }}" class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-200">
                    <i class="fas fa-edit mr-2"></i>Editar
                </a>
                @endif
                @endcan

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
                <a href="{{ route('checklists.checkout.view', $ticket) }}" class="bg-purple-100 hover:bg-purple-200 text-purple-800 font-semibold py-2 px-4 rounded-lg transition duration-200 border border-purple-300">
                    <i class="fas fa-eye mr-2"></i>Ver Checkout
                </a>
                @endif

                @if($ticket->checkinChecklist)
                <a href="{{ route('checklists.checkin.view', $ticket) }}" class="bg-orange-100 hover:bg-orange-200 text-orange-800 font-semibold py-2 px-4 rounded-lg transition duration-200 border border-orange-300">
                    <i class="fas fa-eye mr-2"></i>Ver Checkin
                </a>
                @endif

                @if($ticket->status === 'completado' && $ticket->user_id === auth()->id() && !$ticket->service_rating)
                <a href="{{ route('tickets.rate', $ticket) }}" class="bg-amber-600 hover:bg-amber-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-200">
                    <i class="fas fa-star mr-2"></i>Calificar Servicio
                </a>
                @endif
            </div>
        </div>
    </div>
</div>

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
</script>
@endsection
