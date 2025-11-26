@extends('layouts.app')

@section('title', 'Detalle del Vehículo')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="mb-4 sm:mb-6">
        <a href="{{ route('vehicles.index') }}" class="text-blue-600 hover:text-blue-800 font-medium text-sm sm:text-base">
            <i class="fas fa-arrow-left mr-2"></i>Volver a Vehículos
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

    <!-- Encabezado del Vehículo -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <div class="flex justify-between items-start mb-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">{{ $vehicle->brand }} {{ $vehicle->model }}</h1>
                <p class="text-gray-600 mt-1">{{ $vehicle->year }} • {{ $vehicle->internal_code }}</p>
            </div>
            <div class="flex gap-2">
                @can('update', $vehicle)
                <a href="{{ route('vehicles.edit', $vehicle) }}" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-200">
                    <i class="fas fa-edit mr-2"></i>Editar
                </a>
                @endcan
                @can('delete', $vehicle)
                <form action="{{ route('vehicles.destroy', $vehicle) }}" method="POST" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" onclick="return confirm('¿Está seguro de eliminar este vehículo?')" class="bg-red-600 hover:bg-red-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-200">
                        <i class="fas fa-trash mr-2"></i>Eliminar
                    </button>
                </form>
                @endcan
            </div>
        </div>

        <!-- Estado -->
        <div class="mb-4">
            @php
                $statusColors = [
                    'disponible' => 'bg-green-100 text-green-800',
                    'en_uso' => 'bg-blue-100 text-blue-800',
                    'mantenimiento' => 'bg-yellow-100 text-yellow-800',
                    'fuera_servicio' => 'bg-red-100 text-red-800',
                ];
            @endphp
            <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full {{ $statusColors[$vehicle->status] ?? 'bg-gray-100 text-gray-800' }}">
                {{ ucfirst(str_replace('_', ' ', $vehicle->status)) }}
            </span>
        </div>
    </div>

    <!-- Información General -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h2 class="text-xl font-semibold text-gray-800 mb-4 border-b pb-2">
            <i class="fas fa-info-circle mr-2"></i>Información General
        </h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Código Interno</p>
                        <p class="text-gray-900 font-semibold">{{ $vehicle->internal_code }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500">Placas</p>
                        <p class="text-gray-900 font-semibold">{{ $vehicle->plates }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500">Marca</p>
                        <p class="text-gray-900">{{ $vehicle->brand }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500">Modelo</p>
                        <p class="text-gray-900">{{ $vehicle->model }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500">Año</p>
                        <p class="text-gray-900">{{ $vehicle->year }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500">Color</p>
                        <p class="text-gray-900">{{ $vehicle->color ?? 'No especificado' }}</p>
                    </div>
                    @if($vehicle->serial_number)
                    <div class="md:col-span-2">
                        <p class="text-sm font-medium text-gray-500">Número de Serie (VIN)</p>
                        <p class="text-gray-900 font-mono">{{ $vehicle->serial_number }}</p>
                    </div>
                    @endif
        </div>
    </div>

    <!-- Especificaciones Técnicas -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h2 class="text-xl font-semibold text-gray-800 mb-4 border-b pb-2">
            <i class="fas fa-cogs mr-2"></i>Especificaciones Técnicas
        </h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Tipo de Vehículo</p>
                        <p class="text-gray-900">{{ ucfirst($vehicle->vehicle_type) }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500">Tipo de Combustible</p>
                        <p class="text-gray-900">{{ ucfirst($vehicle->fuel_type) }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500">Capacidad de Pasajeros</p>
                        <p class="text-gray-900">{{ $vehicle->capacity_passengers }} personas</p>
                    </div>
                    @if($vehicle->capacity_cargo)
                    <div>
                        <p class="text-sm font-medium text-gray-500">Capacidad de Carga</p>
                        <p class="text-gray-900">{{ number_format($vehicle->capacity_cargo) }} kg</p>
                    </div>
                    @endif
                    <div>
                        <p class="text-sm font-medium text-gray-500">Kilometraje Actual</p>
                        <p class="text-gray-900 text-lg font-semibold">{{ number_format($vehicle->current_mileage) }} km</p>
                    </div>
                </div>
            </div>

            <!-- Notas -->
            @if($vehicle->notes)
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4 border-b pb-2">
                    <i class="fas fa-sticky-note mr-2"></i>Notas y Observaciones
                </h2>
                <p class="text-gray-700">{{ $vehicle->notes }}</p>
            </div>
            @endif

            <!-- Historial de Tickets -->
            @if($vehicle->tickets && $vehicle->tickets->count() > 0)
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4 border-b pb-2">
                    <i class="fas fa-history mr-2"></i>Historial de Uso
                </h2>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Folio</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Usuario</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Fecha</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($vehicle->tickets->take(10) as $ticket)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-2 text-sm">{{ $ticket->folio }}</td>
                                <td class="px-4 py-2 text-sm">{{ $ticket->user->name }}</td>
                                <td class="px-4 py-2 text-sm">{{ $ticket->requested_date->format('d/m/Y') }}</td>
                                <td class="px-4 py-2 text-sm">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusColors[$ticket->status] ?? 'bg-gray-100 text-gray-800' }}">
                                        {{ ucfirst($ticket->status) }}
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @if($vehicle->tickets->count() > 10)
                <p class="text-sm text-gray-500 mt-2 text-center">Mostrando los últimos 10 registros de {{ $vehicle->tickets->count() }} totales</p>
                @endif
            </div>
            @endif

            <!-- Documentos -->
            @if($vehicle->documents && $vehicle->documents->count() > 0)
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4 border-b pb-2">
                    <i class="fas fa-file-alt mr-2"></i>Documentos
                </h2>
                <ul class="space-y-2">
                    @foreach($vehicle->documents as $document)
                    <li class="flex items-center justify-between">
                        <span class="text-sm text-gray-700">{{ $document->document_type }}</span>
                        <span class="text-xs text-gray-500">{{ $document->expiration_date ? $document->expiration_date->format('d/m/Y') : 'N/A' }}</span>
                    </li>
                    @endforeach
                </ul>
            </div>
            @endif

            <!-- Último Mantenimiento -->
            @if($vehicle->maintenances && $vehicle->maintenances->count() > 0)
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4 border-b pb-2">
                    <i class="fas fa-wrench mr-2"></i>Último Mantenimiento
                </h2>
                @php
                    $lastMaintenance = $vehicle->maintenances->sortByDesc('maintenance_date')->first();
                @endphp
                <div class="space-y-2">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Tipo</p>
                        <p class="text-gray-900">{{ ucfirst($lastMaintenance->maintenance_type) }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500">Fecha</p>
                        <p class="text-gray-900">{{ $lastMaintenance->maintenance_date->format('d/m/Y') }}</p>
                    </div>
                    @if($lastMaintenance->cost)
                    <div>
                        <p class="text-sm font-medium text-gray-500">Costo</p>
                        <p class="text-gray-900">${{ number_format($lastMaintenance->cost, 2) }}</p>
                    </div>
                    @endif
                </div>
            </div>
            @endif

            <!-- Fechas de Registro -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4 border-b pb-2">
                    <i class="fas fa-calendar mr-2"></i>Registro
                </h2>
                <div class="space-y-2">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Fecha de Registro</p>
                        <p class="text-gray-900">{{ $vehicle->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500">Última Actualización</p>
                        <p class="text-gray-900">{{ $vehicle->updated_at->format('d/m/Y H:i') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
