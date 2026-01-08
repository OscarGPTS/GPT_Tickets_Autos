@extends('layouts.app')

@section('title', 'Dashboard - Usuario')

@section('content')
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 tracking-tight">Bienvenido, {{ Auth::user()->name }}</h1>
                <p class="mt-2 text-gray-600 text-lg">Panel de Usuario - Gestión de Requisiciones</p>
            </div>
            <div class="mt-4 sm:mt-0">
                <a href="{{ route('tickets.create') }}"
                    class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-xl shadow-lg text-white bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200 transform hover:-translate-y-0.5">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Nueva Requisición
                </a>
            </div>
        </div>

        <!-- Stats -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-10">
            <!-- Total -->
            <div
                class="bg-white rounded-2xl shadow-sm hover:shadow-md transition-shadow duration-300 p-6 border border-gray-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500 uppercase tracking-wider">Total</p>
                        <p class="text-3xl font-bold text-gray-900 mt-1">{{ $stats['total'] }}</p>
                    </div>
                    <div class="p-3 rounded-xl bg-blue-50 text-blue-600">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                </div>
                <div class="mt-4">
                    <div class="w-full bg-gray-100 rounded-full h-1.5">
                        <div class="bg-blue-500 h-1.5 rounded-full" style="width: 100%"></div>
                    </div>
                </div>
            </div>

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
                    <div class="w-full bg-gray-100 rounded-full h-1.5">
                        <div class="bg-amber-500 h-1.5 rounded-full"
                            style="width: {{ $stats['total'] > 0 ? ($stats['pendientes'] / $stats['total']) * 100 : 0 }}%">
                        </div>
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
                    <div class="w-full bg-gray-100 rounded-full h-1.5">
                        <div class="bg-green-500 h-1.5 rounded-full"
                            style="width: {{ $stats['total'] > 0 ? ($stats['aprobados'] / $stats['total']) * 100 : 0 }}%">
                        </div>
                    </div>
                </div>
            </div>

            <!-- En Curso -->
            <div
                class="bg-white rounded-2xl shadow-sm hover:shadow-md transition-shadow duration-300 p-6 border border-gray-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500 uppercase tracking-wider">En Curso</p>
                        <p class="text-3xl font-bold text-gray-900 mt-1">{{ $stats['en_curso'] }}</p>
                    </div>
                    <div class="p-3 rounded-xl bg-purple-50 text-purple-600">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                    </div>
                </div>
                <div class="mt-4">
                    <div class="w-full bg-gray-100 rounded-full h-1.5">
                        <div class="bg-purple-500 h-1.5 rounded-full"
                            style="width: {{ $stats['total'] > 0 ? ($stats['en_curso'] / $stats['total']) * 100 : 0 }}%">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Tickets -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                <h3 class="text-lg font-bold text-gray-900 flex items-center">
                    <i class="fas fa-history text-gray-400 mr-2"></i> Mis Requisiciones Recientes
                </h3>
                <a href="{{ route('tickets.index') }}"
                    class="text-sm font-medium text-blue-600 hover:text-blue-800 transition-colors">
                    Ver todas <i class="fas fa-arrow-right ml-1"></i>
                </a>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Folio</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Destino</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Fecha</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Vehículo</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Estado</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        @forelse($misTickets as $ticket)
                            <tr class="hover:bg-gray-50 transition-colors duration-150">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-sm font-bold text-gray-900 bg-gray-100 px-2 py-1 rounded-md">
                                        {{ $ticket->folio ?? 'Pendiente' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 font-medium">
                                    {{ $ticket->destination }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <div class="flex items-center">
                                        <i class="far fa-calendar-alt mr-2 text-gray-400"></i>
                                        {{ $ticket->requested_date->format('d/m/Y') }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    @if ($ticket->vehicle)
                                        <div class="flex items-center">
                                            <i class="fas fa-car-side mr-2 text-gray-400"></i>
                                            {{ $ticket->vehicle->brand }} {{ $ticket->vehicle->model }}
                                        </div>
                                    @else
                                        <span class="text-gray-400 italic">Sin asignar</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        $statusColors = [
                                            'pendiente' => 'bg-amber-100 text-amber-800 border border-amber-200',
                                            'aprobado' => 'bg-blue-100 text-blue-800 border border-blue-200',
                                            'rechazado' => 'bg-red-100 text-red-800 border border-red-200',
                                            'en_curso' => 'bg-indigo-100 text-indigo-800 border border-indigo-200',
                                            'finalizado' => 'bg-green-100 text-green-800 border border-green-200',
                                            'completado' => 'bg-green-100 text-green-800 border border-green-200',
                                        ];
                                        $statusIcons = [
                                            'pendiente' => 'fa-clock',
                                            'aprobado' => 'fa-check-circle',
                                            'rechazado' => 'fa-times-circle',
                                            'en_curso' => 'fa-road',
                                            'finalizado' => 'fa-flag-checkered',
                                            'completado' => 'fa-archive',
                                        ];
                                    @endphp
                                    <span
                                        class="px-3 py-1 inline-flex items-center text-xs leading-5 font-semibold rounded-full {{ $statusColors[$ticket->status] ?? 'bg-gray-100 text-gray-800' }}">
                                        <i class="fas {{ $statusIcons[$ticket->status] ?? 'fa-circle' }} mr-1.5"></i>
                                        {{ ucfirst(str_replace('_', ' ', $ticket->status)) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                    <a href="{{ route('tickets.show', $ticket) }}"
                                        class="text-blue-600 hover:text-blue-900 bg-blue-50 hover:bg-blue-100 px-3 py-1.5 rounded-lg transition-colors duration-200 inline-flex items-center">
                                        <i class="fas fa-eye mr-1"></i> Ver
                                    </a>

                                    @if($ticket->status === 'completado' && !$ticket->service_rating)
                                        <a href="{{ route('tickets.rate', $ticket) }}"
                                            class="text-amber-700 hover:text-amber-900 bg-amber-100 hover:bg-amber-200 px-3 py-1.5 rounded-lg transition-colors duration-200 inline-flex items-center">
                                            <i class="fas fa-star mr-1"></i> Calificar
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center justify-center">
                                        <div class="bg-gray-50 rounded-full p-4 mb-3">
                                            <i class="fas fa-ticket-alt text-gray-300 text-3xl"></i>
                                        </div>
                                        <p class="text-gray-500 text-lg font-medium">No tienes requisiciones aún</p>
                                        <p class="text-gray-400 text-sm mb-4">Comienza creando tu primera solicitud de
                                            vehículo</p>
                                        <a href="{{ route('tickets.create') }}"
                                            class="text-blue-600 hover:text-blue-800 font-medium">
                                            Crear una nueva solicitud &rarr;
                                        </a>
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
