@extends('layouts.app')

@section('title', 'Detalles del Vehículo')

@section('content')
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <div class="flex items-center">
                    <a href="{{ route('vehicles.index') }}"
                        class="mr-4 p-2 rounded-full bg-white border border-gray-200 text-gray-500 hover:text-blue-600 hover:border-blue-300 transition-all duration-200 shadow-sm">
                        <i class="fas fa-arrow-left"></i>
                    </a>
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900 tracking-tight flex items-center gap-3">
                            {{ $vehicle->brand }} {{ $vehicle->model }}
                            <span
                                class="text-sm font-normal text-gray-500 bg-gray-100 px-3 py-1 rounded-full border border-gray-200">{{ $vehicle->year }}</span>
                        </h1>
                        <div class="mt-2 flex items-center gap-4 text-sm text-gray-600">
                            <span class="flex items-center gap-1">
                                <i class="fas fa-hashtag text-gray-400"></i> {{ $vehicle->internal_code }}
                            </span>
                            <span class="flex items-center gap-1">
                                <i class="fas fa-barcode text-gray-400"></i> {{ $vehicle->plates }}
                            </span>
                        </div>
                    </div>
                </div>
                <div class="mt-4 sm:mt-0 flex gap-3">
                    @can('update', $vehicle)
                        <a href="{{ route('vehicles.edit', $vehicle) }}"
                            class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-xl text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200">
                            <i class="fas fa-edit mr-2 text-gray-400"></i> Editar
                        </a>
                    @endcan
                    @can('delete', $vehicle)
                        <form action="{{ route('vehicles.destroy', $vehicle) }}" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-xl text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-all duration-200"
                                onclick="return confirm('¿Está seguro de eliminar este vehículo?')">
                                <i class="fas fa-trash-alt mr-2"></i> Eliminar
                            </button>
                        </form>
                    @endcan
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Columna Izquierda: Información Principal -->
            <div class="lg:col-span-2 space-y-8">
                <!-- Tarjeta Principal -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="px-6 py-5 border-b border-gray-100 bg-gray-50/50 flex justify-between items-center">
                        <h3 class="text-lg font-bold text-gray-900">Información General</h3>
                        @php
                            $statusColors = [
                                'disponible' => 'bg-green-100 text-green-800 border border-green-200',
                                'en_uso' => 'bg-indigo-100 text-indigo-800 border border-indigo-200',
                                'mantenimiento' => 'bg-amber-100 text-amber-800 border border-amber-200',
                                'inactivo' => 'bg-gray-100 text-gray-800 border border-gray-200',
                            ];
                        @endphp
                        <span
                            class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full {{ $statusColors[$vehicle->status] ?? 'bg-gray-100 text-gray-800' }}">
                            {{ ucfirst(str_replace('_', ' ', $vehicle->status)) }}
                        </span>
                    </div>
                    <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Marca</label>
                            <p class="text-lg font-semibold text-gray-900">{{ $vehicle->brand }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Modelo</label>
                            <p class="text-lg font-semibold text-gray-900">{{ $vehicle->model }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Año</label>
                            <p class="text-lg font-semibold text-gray-900">{{ $vehicle->year }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Color</label>
                            <div class="flex items-center gap-2">
                                @if ($vehicle->color)
                                    <span class="w-4 h-4 rounded-full border border-gray-200 shadow-sm"
                                        style="background-color: {{ $vehicle->color }}"></span>
                                @endif
                                <p class="text-lg font-semibold text-gray-900">{{ $vehicle->color ?? 'N/A' }}</p>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Tipo</label>
                            <p class="text-lg font-semibold text-gray-900">{{ ucfirst($vehicle->vehicle_type) }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Kilometraje Actual</label>
                            <p class="text-lg font-semibold text-gray-900">{{ number_format($vehicle->current_mileage) }}
                                km</p>
                        </div>
                    </div>
                </div>

                <!-- Especificaciones Técnicas -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="px-6 py-5 border-b border-gray-100 bg-gray-50/50">
                        <h3 class="text-lg font-bold text-gray-900">Especificaciones Técnicas</h3>
                    </div>
                    <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">VIN (Número de Serie)</label>
                            <p
                                class="text-base font-mono text-gray-900 bg-gray-50 p-2 rounded border border-gray-200 inline-block">
                                {{ $vehicle->vin ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Número de Motor</label>
                            <p
                                class="text-base font-mono text-gray-900 bg-gray-50 p-2 rounded border border-gray-200 inline-block">
                                {{ $vehicle->engine_number ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Placas</label>
                            <p
                                class="text-base font-mono text-gray-900 bg-gray-50 p-2 rounded border border-gray-200 inline-block">
                                {{ $vehicle->plates }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Tarjeta de Circulación</label>
                            <p class="text-base text-gray-900">{{ $vehicle->circulation_card ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Notas -->
                @if ($vehicle->notes)
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                        <div class="px-6 py-5 border-b border-gray-100 bg-gray-50/50">
                            <h3 class="text-lg font-bold text-gray-900">Notas Adicionales</h3>
                        </div>
                        <div class="p-6">
                            <p class="text-gray-700 whitespace-pre-line">{{ $vehicle->notes }}</p>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Columna Derecha: Historial y Documentos -->
            <div class="space-y-8">
                <!-- Historial Reciente -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="px-6 py-5 border-b border-gray-100 bg-gray-50/50 flex justify-between items-center">
                        <h3 class="text-lg font-bold text-gray-900">Historial de Viajes</h3>
                    </div>
                    <div class="divide-y divide-gray-100">
                        @forelse($vehicle->tickets()->latest()->take(5)->get() as $ticket)
                            <div class="p-4 hover:bg-gray-50 transition-colors">
                                <div class="flex justify-between items-start mb-1">
                                    <span
                                        class="text-xs font-bold text-blue-600 bg-blue-50 px-2 py-0.5 rounded">#{{ $ticket->id }}</span>
                                    <span class="text-xs text-gray-500">{{ $ticket->created_at->format('d/m/Y') }}</span>
                                </div>
                                <p class="text-sm font-medium text-gray-900 mb-1">{{ $ticket->user->name }}</p>
                                <p class="text-xs text-gray-500 truncate">{{ $ticket->destination }}</p>
                            </div>
                        @empty
                            <div class="p-6 text-center text-gray-500">
                                <p>No hay viajes registrados</p>
                            </div>
                        @endforelse

                        @if ($vehicle->tickets()->count() > 5)
                            <div class="p-3 text-center bg-gray-50">
                                <a href="#" class="text-sm font-medium text-blue-600 hover:text-blue-800">Ver todo el
                                    historial</a>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Mantenimientos (Placeholder) -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="px-6 py-5 border-b border-gray-100 bg-gray-50/50 flex justify-between items-center">
                        <h3 class="text-lg font-bold text-gray-900">Mantenimientos</h3>
                        <button class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                            <i class="fas fa-plus"></i>
                        </button>
                    </div>
                    <div class="p-6 text-center text-gray-500">
                        <div class="bg-gray-50 rounded-full p-3 inline-block mb-3">
                            <i class="fas fa-tools text-gray-400 text-xl"></i>
                        </div>
                        <p class="text-sm">No hay registros de mantenimiento</p>
                    </div>
                </div>

                <!-- Documentos (Placeholder) -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="px-6 py-5 border-b border-gray-100 bg-gray-50/50 flex justify-between items-center">
                        <h3 class="text-lg font-bold text-gray-900">Documentos</h3>
                        <button class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                            <i class="fas fa-upload"></i>
                        </button>
                    </div>
                    <div class="p-6 text-center text-gray-500">
                        <div class="bg-gray-50 rounded-full p-3 inline-block mb-3">
                            <i class="fas fa-file-alt text-gray-400 text-xl"></i>
                        </div>
                        <p class="text-sm">No hay documentos adjuntos</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
