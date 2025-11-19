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
                    <h1 class="text-2xl font-bold text-white">Solicitud #{{ $ticket->id }}</h1>
                    <p class="text-blue-100 mt-1">Creada el {{ \Carbon\Carbon::parse($ticket->created_at)->format('d/m/Y H:i') }}</p>
                </div>
                <div>
                    @php
                        $statusColors = [
                            'pendiente' => 'bg-yellow-100 text-yellow-800 border-yellow-300',
                            'aprobado' => 'bg-green-100 text-green-800 border-green-300',
                            'rechazado' => 'bg-red-100 text-red-800 border-red-300',
                            'en_uso' => 'bg-blue-100 text-blue-800 border-blue-300',
                            'completado' => 'bg-gray-100 text-gray-800 border-gray-300',
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
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Destino</p>
                        <p class="text-lg font-semibold text-gray-900">{{ $ticket->destination }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Número de Pasajeros</p>
                        <p class="text-lg font-semibold text-gray-900">{{ $ticket->passenger_count }} persona(s)</p>
                    </div>
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
                    <p class="text-gray-900">{{ $ticket->purpose }}</p>
                </div>
                @if($ticket->additional_notes)
                <div class="mt-4">
                    <p class="text-sm text-gray-600 mb-1">Notas Adicionales</p>
                    <p class="text-gray-900">{{ $ticket->additional_notes }}</p>
                </div>
                @endif
            </div>

            <!-- Información del Solicitante -->
            <div class="mb-6">
                <h2 class="text-xl font-bold text-gray-800 mb-4 border-b pb-2">Solicitante</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Nombre</p>
                        <p class="text-lg font-semibold text-gray-900">{{ $ticket->user->name }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Email</p>
                        <p class="text-lg font-semibold text-gray-900">{{ $ticket->user->email }}</p>
                    </div>
                    @if($ticket->user->driverLicense)
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Licencia de Conducir</p>
                        <p class="text-lg font-semibold text-gray-900">{{ $ticket->user->driverLicense->license_number }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Fecha de Expiración</p>
                        <p class="text-lg font-semibold text-gray-900">{{ \Carbon\Carbon::parse($ticket->user->driverLicense->expiration_date)->format('d/m/Y') }}</p>
                    </div>
                    @endif
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

            <!-- Acciones -->
            <div class="flex flex-wrap gap-3 pt-6 border-t">
                @can('update', $ticket)
                @if($ticket->status === 'pendiente')
                <a href="{{ route('tickets.edit', $ticket) }}" class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-200">
                    <i class="fas fa-edit mr-2"></i>Editar
                </a>
                @endif
                @endcan

                @can('approve', $ticket)
                @if($ticket->status === 'pendiente')
                <form action="{{ route('tickets.approve', $ticket) }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-200" onclick="return confirm('¿Aprobar esta solicitud?')">
                        <i class="fas fa-check-circle mr-2"></i>Aprobar
                    </button>
                </form>
                <form action="{{ route('tickets.reject', $ticket) }}" method="POST" class="inline" onsubmit="return handleReject(event)">
                    @csrf
                    <input type="hidden" name="rejection_reason" id="rejection_reason">
                    <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-200">
                        <i class="fas fa-times-circle mr-2"></i>Rechazar
                    </button>
                </form>
                @endif
                @endcan

                @if($ticket->status === 'aprobado' && auth()->user()->hasRole('despachador'))
                <a href="{{ route('checklists.checkout', $ticket) }}" class="bg-purple-600 hover:bg-purple-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-200">
                    <i class="fas fa-clipboard-check mr-2"></i>Realizar Checkout
                </a>
                @endif

                @if($ticket->status === 'en_uso' && auth()->user()->hasRole('despachador'))
                <a href="{{ route('checklists.checkin', $ticket) }}" class="bg-orange-600 hover:bg-orange-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-200">
                    <i class="fas fa-clipboard-check mr-2"></i>Realizar Checkin
                </a>
                @endif

                @if($ticket->status === 'completado' && $ticket->user_id === auth()->id() && !$ticket->service_rating)
                <a href="{{ route('tickets.rate', $ticket) }}" class="bg-yellow-600 hover:bg-yellow-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-200">
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
