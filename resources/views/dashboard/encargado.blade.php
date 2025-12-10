@extends('layouts.app')

@section('title', 'Dashboard - Encargado')

@section('content')
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 tracking-tight">Bienvenido, {{ Auth::user()->name }}</h1>
                <p class="mt-2 text-gray-600 text-lg">Panel de Encargado - Gestión de Requisiciones</p>
            </div>
            <div class="mt-4 sm:mt-0 flex gap-3">
                <a href="{{ route('tickets.index') }}"
                    class="inline-flex items-center px-5 py-2.5 border border-gray-300 shadow-sm text-sm font-medium rounded-xl text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200">
                    <i class="fas fa-list mr-2 text-gray-400"></i>
                    Ver Requisiciones
                </a>
                <a href="{{ route('vehicles.index') }}"
                    class="inline-flex items-center px-5 py-2.5 border border-transparent shadow-sm text-sm font-medium rounded-xl text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200">
                    <i class="fas fa-car mr-2"></i>
                    Gestionar Vehículos
                </a>
            </div>
        </div>

        <!-- Stats -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
            <!-- Pendientes -->
            <div
                class="bg-white rounded-2xl shadow-sm hover:shadow-md transition-shadow duration-300 p-6 border border-gray-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500 uppercase tracking-wider">Pendientes</p>
                        <p class="text-3xl font-bold text-gray-900 mt-1">{{ $stats['pendientes'] }}</p>
                    </div>
                    <div class="p-3 rounded-xl bg-amber-50 text-amber-600">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
                <div class="mt-4">
                    <div class="flex items-center text-sm text-amber-600">
                        <span class="font-medium">Requieren atención</span>
                    </div>
                </div>
            </div>

            <!-- Aprobados -->
            <div
                class="bg-white rounded-2xl shadow-sm hover:shadow-md transition-shadow duration-300 p-6 border border-gray-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500 uppercase tracking-wider">Aprobados</p>
                        <p class="text-3xl font-bold text-gray-900 mt-1">{{ $stats['aprobados'] }}</p>
                    </div>
                    <div class="p-3 rounded-xl bg-green-50 text-green-600">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
                <div class="mt-4">
                    <div class="flex items-center text-sm text-green-600">
                        <span class="font-medium">Listos para entrega</span>
                    </div>
                </div>
            </div>

            <!-- Vehículos Disponibles -->
            <div
                class="bg-white rounded-2xl shadow-sm hover:shadow-md transition-shadow duration-300 p-6 border border-gray-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500 uppercase tracking-wider">Disponibles</p>
                        <p class="text-3xl font-bold text-gray-900 mt-1">{{ $stats['vehiculos_disponibles'] }}</p>
                    </div>
                    <div class="p-3 rounded-xl bg-blue-50 text-blue-600">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0" />
                        </svg>
                    </div>
                </div>
                <div class="mt-4">
                    <div class="w-full bg-gray-100 rounded-full h-1.5">
                        <div class="bg-blue-500 h-1.5 rounded-full"
                            style="width: {{ $stats['vehiculos_disponibles'] + $stats['vehiculos_en_uso'] > 0 ? ($stats['vehiculos_disponibles'] / ($stats['vehiculos_disponibles'] + $stats['vehiculos_en_uso'])) * 100 : 0 }}%">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Vehículos en Uso -->
            <div
                class="bg-white rounded-2xl shadow-sm hover:shadow-md transition-shadow duration-300 p-6 border border-gray-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500 uppercase tracking-wider">En Uso</p>
                        <p class="text-3xl font-bold text-gray-900 mt-1">{{ $stats['vehiculos_en_uso'] }}</p>
                    </div>
                    <div class="p-3 rounded-xl bg-purple-50 text-purple-600">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                    </div>
                </div>
                <div class="mt-4">
                    <div class="flex items-center text-sm text-purple-600">
                        <span class="font-medium">Actualmente fuera</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pending Tickets -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-100 bg-gray-50/50 flex justify-between items-center">
                <div>
                    <h3 class="text-lg font-bold text-gray-900 flex items-center">
                        <i class="fas fa-clock text-amber-500 mr-2"></i> Requisiciones Pendientes de Aprobación
                    </h3>
                    <p class="mt-1 text-sm text-gray-500">Revisa y aprueba las solicitudes de vehículos</p>
                </div>
                <span
                    class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-amber-100 text-amber-800">
                    {{ count($ticketsPendientes) }} pendientes
                </span>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Requisición</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Solicitante</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Destino</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Cliente</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Fecha</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Hora</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        @forelse($ticketsPendientes as $ticket)
                            <tr class="hover:bg-gray-50 transition-colors duration-150">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-sm font-bold text-gray-900 bg-gray-100 px-2 py-1 rounded-md">
                                        {{ $ticket->folio }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div
                                            class="h-8 w-8 rounded-full bg-gray-200 flex items-center justify-center text-gray-500 mr-3">
                                            <span class="text-xs font-bold">{{ substr($ticket->user->name, 0, 2) }}</span>
                                        </div>
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">{{ $ticket->user->name }}</div>
                                            @if ($ticket->user->immediateBoss)
                                                <div class="text-xs text-gray-500">Jefe:
                                                    {{ $ticket->user->immediateBoss->name }}</div>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-700 font-medium">{{ $ticket->destination }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $ticket->cliente ?? 'N/A' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <div class="flex items-center">
                                        <i class="far fa-calendar-alt mr-2 text-gray-400"></i>
                                        {{ $ticket->requested_date->format('d/m/Y') }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <div class="flex items-center">
                                        <i class="far fa-clock mr-2 text-gray-400"></i>
                                        {{ $ticket->requested_time_start ? \Carbon\Carbon::parse($ticket->requested_time_start)->format('H:i') : 'N/A' }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <a href="{{ route('tickets.show', $ticket) }}"
                                        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 shadow-sm hover:shadow transition-all duration-200">
                                        <i class="fas fa-check-circle mr-2"></i> Revisar
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center justify-center">
                                        <div class="bg-gray-50 rounded-full p-4 mb-3">
                                            <i class="fas fa-check-double text-green-500 text-3xl"></i>
                                        </div>
                                        <p class="text-gray-500 text-lg font-medium">No hay requisiciones pendientes</p>
                                        <p class="text-gray-400 text-sm">¡Todo está al día!</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
