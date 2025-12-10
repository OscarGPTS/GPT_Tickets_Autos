@extends('layouts.app')

@section('title', 'Gestión de Vehículos')

@section('content')
    <div class="max-w-7xl mx-auto">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 tracking-tight">Gestión de Vehículos</h1>
                <p class="mt-2 text-gray-600">Administración del inventario de la flotilla</p>
            </div>
            @can('create', App\Models\Vehicle::class)
                <div class="mt-4 sm:mt-0">
                    <a href="{{ route('vehicles.create') }}"
                        class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-xl shadow-lg text-white bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200 transform hover:-translate-y-0.5">
                        <i class="fas fa-plus mr-2"></i>Nuevo Vehículo
                    </a>
                </div>
            @endcan
        </div>

        @if (session('success'))
            <div class="bg-green-50 border-l-4 border-green-400 p-4 rounded-r-lg shadow-sm mb-6">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-check-circle text-green-400"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-green-700 font-medium">{{ session('success') }}</p>
                    </div>
                </div>
            </div>
        @endif

        @if (session('error'))
            <div class="bg-red-50 border-l-4 border-red-400 p-4 rounded-r-lg shadow-sm mb-6">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-exclamation-circle text-red-400"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-red-700 font-medium">{{ session('error') }}</p>
                    </div>
                </div>
            </div>
        @endif

        <!-- Filtros -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-8">
            <form method="GET" action="{{ route('vehicles.index') }}" class="flex flex-wrap gap-4 items-end">
                <div class="flex-1 min-w-[200px]">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
                    <div class="relative">
                        <select name="status"
                            class="block w-full pl-3 pr-10 py-2.5 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-xl">
                            <option value="">Todos</option>
                            <option value="disponible" {{ request('status') == 'disponible' ? 'selected' : '' }}>Disponible
                            </option>
                            <option value="en_uso" {{ request('status') == 'en_uso' ? 'selected' : '' }}>En Uso</option>
                            <option value="mantenimiento" {{ request('status') == 'mantenimiento' ? 'selected' : '' }}>
                                Mantenimiento</option>
                            <option value="fuera_servicio" {{ request('status') == 'fuera_servicio' ? 'selected' : '' }}>
                                Fuera de Servicio</option>
                        </select>
                    </div>
                </div>
                <div class="flex-1 min-w-[200px]">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tipo</label>
                    <div class="relative">
                        <select name="vehicle_type"
                            class="block w-full pl-3 pr-10 py-2.5 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-xl">
                            <option value="">Todos</option>
                            <option value="sedan" {{ request('vehicle_type') == 'sedan' ? 'selected' : '' }}>Sedán</option>
                            <option value="suv" {{ request('vehicle_type') == 'suv' ? 'selected' : '' }}>SUV</option>
                            <option value="pickup" {{ request('vehicle_type') == 'pickup' ? 'selected' : '' }}>Pickup
                            </option>
                            <option value="van" {{ request('vehicle_type') == 'van' ? 'selected' : '' }}>Van</option>
                            <option value="camioneta" {{ request('vehicle_type') == 'camioneta' ? 'selected' : '' }}>
                                Camioneta</option>
                        </select>
                    </div>
                </div>
                <div class="flex-1 min-w-[200px]">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Buscar</label>
                    <input type="text" name="search" value="{{ request('search') }}"
                        class="block w-full py-2.5 px-3 border border-gray-300 rounded-xl shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                        placeholder="Código, Marca, Modelo, Placas">
                </div>
                <div class="flex-none">
                    <button type="submit"
                        class="inline-flex items-center px-5 py-2.5 border border-transparent text-sm font-medium rounded-xl shadow-sm text-white bg-gray-800 hover:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-all duration-200">
                        <i class="fas fa-filter mr-2"></i>Filtrar
                    </button>
                </div>
            </form>
        </div>

        <!-- Lista de Vehículos -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            @if ($vehicles->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-100">
                        <thead class="bg-gray-50">
                            <tr>
                                <th
                                    class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                    Código</th>
                                <th
                                    class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                    Vehículo</th>
                                <th
                                    class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                    Placas</th>
                                <th
                                    class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                    Tipo</th>
                                <th
                                    class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                    Estado</th>
                                <th
                                    class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                    Kilometraje</th>
                                <th
                                    class="px-6 py-4 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                    Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            @foreach ($vehicles as $vehicle)
                                <tr class="hover:bg-gray-50 transition-colors duration-150">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="text-sm font-bold text-gray-900 bg-gray-100 px-2 py-1 rounded-md">
                                            {{ $vehicle->internal_code }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div
                                                class="h-10 w-10 rounded-full bg-blue-50 flex items-center justify-center text-blue-600 mr-3">
                                                <i class="fas fa-car"></i>
                                            </div>
                                            <div>
                                                <div class="text-sm font-bold text-gray-900">{{ $vehicle->brand }}
                                                    {{ $vehicle->model }}</div>
                                                <div class="text-xs text-gray-500">{{ $vehicle->year }} •
                                                    {{ $vehicle->color ?? 'N/A' }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div
                                            class="text-sm font-mono bg-gray-100 px-2 py-1 rounded inline-block text-gray-800 border border-gray-200">
                                            {{ $vehicle->plates }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                        {{ ucfirst($vehicle->vehicle_type) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @php
                                            $statusColors = [
                                                'disponible' => 'bg-green-100 text-green-800 border border-green-200',
                                                'en_uso' => 'bg-indigo-100 text-indigo-800 border border-indigo-200',
                                                'mantenimiento' =>
                                                    'bg-amber-100 text-amber-800 border border-amber-200',
                                                'fuera_servicio' => 'bg-red-100 text-red-800 border border-red-200',
                                            ];
                                            $statusIcons = [
                                                'disponible' => 'fa-check-circle',
                                                'en_uso' => 'fa-road',
                                                'mantenimiento' => 'fa-tools',
                                                'fuera_servicio' => 'fa-ban',
                                            ];
                                        @endphp
                                        <span
                                            class="px-3 py-1 inline-flex items-center text-xs leading-5 font-semibold rounded-full {{ $statusColors[$vehicle->status] ?? 'bg-gray-100 text-gray-800' }}">
                                            <i class="fas {{ $statusIcons[$vehicle->status] ?? 'fa-circle' }} mr-1.5"></i>
                                            {{ ucfirst(str_replace('_', ' ', $vehicle->status)) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 font-medium">
                                        {{ number_format($vehicle->current_mileage) }} km
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex justify-end items-center space-x-2">
                                            <a href="{{ route('vehicles.show', $vehicle) }}"
                                                class="text-blue-600 hover:text-blue-900 p-2 hover:bg-blue-50 rounded-lg transition-colors"
                                                title="Ver detalles">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @can('update', $vehicle)
                                                <a href="{{ route('vehicles.edit', $vehicle) }}"
                                                    class="text-indigo-600 hover:text-indigo-900 p-2 hover:bg-indigo-50 rounded-lg transition-colors"
                                                    title="Editar">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            @endcan
                                            @can('delete', $vehicle)
                                                <form action="{{ route('vehicles.destroy', $vehicle) }}" method="POST"
                                                    class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                        class="text-red-600 hover:text-red-900 p-2 hover:bg-red-50 rounded-lg transition-colors"
                                                        onclick="return confirm('¿Está seguro de eliminar este vehículo?')"
                                                        title="Eliminar">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                    {{ $vehicles->links() }}
                </div>
            @else
                <div class="text-center py-16">
                    <div class="bg-gray-50 rounded-full p-6 inline-block mb-4">
                        <i class="fas fa-car text-gray-400 text-5xl"></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900">No hay vehículos registrados</h3>
                    <p class="text-gray-500 mt-1 mb-6">Comienza agregando vehículos a tu flotilla.</p>
                    @can('create', App\Models\Vehicle::class)
                        <a href="{{ route('vehicles.create') }}"
                            class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-xl shadow-sm text-white bg-blue-600 hover:bg-blue-700 transition-all duration-200">
                            <i class="fas fa-plus mr-2"></i> Registrar primer vehículo
                        </a>
                    @endcan
                </div>
            @endif
        </div>
    </div>
@endsection
