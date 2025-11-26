@extends('layouts.app')

@section('title', 'Dashboard - Despachador')

@section('content')
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 tracking-tight">Bienvenido, {{ Auth::user()->name }}</h1>
            <p class="mt-2 text-gray-600 text-lg">Panel de Despachador - Gestión de Checklists</p>
        </div>

        <!-- Stats -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-10">
            <!-- Pendientes Checkout -->
            <div
                class="bg-white rounded-2xl shadow-sm hover:shadow-md transition-shadow duration-300 p-6 border border-gray-100 relative overflow-hidden group">
                <div
                    class="absolute right-0 top-0 h-full w-1/3 bg-gradient-to-l from-yellow-50 to-transparent opacity-50 group-hover:opacity-100 transition-opacity">
                </div>
                <div class="relative z-10 flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500 uppercase tracking-wider">Pendientes Checkout</p>
                        <p class="text-4xl font-bold text-gray-900 mt-2">{{ $stats['pendientes_checkout'] }}</p>
                        <p class="text-sm text-gray-500 mt-1">Vehículos por entregar</p>
                    </div>
                    <div class="p-4 rounded-2xl bg-yellow-100 text-yellow-600 shadow-sm">
                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                    </div>
                </div>
            </div>

            <!-- En Curso -->
            <div
                class="bg-white rounded-2xl shadow-sm hover:shadow-md transition-shadow duration-300 p-6 border border-gray-100 relative overflow-hidden group">
                <div
                    class="absolute right-0 top-0 h-full w-1/3 bg-gradient-to-l from-blue-50 to-transparent opacity-50 group-hover:opacity-100 transition-opacity">
                </div>
                <div class="relative z-10 flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500 uppercase tracking-wider">En Curso</p>
                        <p class="text-4xl font-bold text-gray-900 mt-2">{{ $stats['en_curso'] }}</p>
                        <p class="text-sm text-gray-500 mt-1">Vehículos fuera</p>
                    </div>
                    <div class="p-4 rounded-2xl bg-blue-100 text-blue-600 shadow-sm">
                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Assigned Tickets -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-100 bg-gray-50/50">
                <h3 class="text-lg font-bold text-gray-900 flex items-center">
                    <i class="fas fa-clipboard-list text-gray-400 mr-2"></i> Tickets Asignados
                </h3>
                <p class="mt-1 text-sm text-gray-500">Completa los checklists de salida y entrada de vehículos</p>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Folio</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Solicitante</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Vehículo</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Destino</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Fecha</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Estado</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        @forelse($ticketsAsignados as $ticket)
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
                                        <div class="text-sm font-medium text-gray-900">{{ $ticket->user->name }}</div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $ticket->vehicle->brand }}
                                        {{ $ticket->vehicle->model }}</div>
                                    <div
                                        class="text-xs text-gray-500 bg-gray-100 inline-block px-1.5 py-0.5 rounded mt-0.5">
                                        {{ $ticket->vehicle->license_plate }}</div>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-700">{{ $ticket->destination }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <div class="flex items-center">
                                        <i class="far fa-calendar-alt mr-2 text-gray-400"></i>
                                        {{ $ticket->requested_date->format('d/m/Y') }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if ($ticket->status === 'aprobado')
                                        <span
                                            class="px-3 py-1 inline-flex items-center text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800 border border-yellow-200">
                                            <span class="w-2 h-2 bg-yellow-400 rounded-full mr-1.5 animate-pulse"></span>
                                            Pendiente Checkout
                                        </span>
                                    @elseif($ticket->status === 'en_curso')
                                        <span
                                            class="px-3 py-1 inline-flex items-center text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800 border border-blue-200">
                                            <span class="w-2 h-2 bg-blue-400 rounded-full mr-1.5 animate-pulse"></span>
                                            En Curso
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                    @if ($ticket->status === 'aprobado')
                                        <a href="{{ route('checklists.checkout', $ticket) }}"
                                            class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-bold rounded-lg text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 shadow-sm hover:shadow transition-all duration-200">
                                            <i class="fas fa-sign-out-alt mr-1.5"></i> Checkout
                                        </a>
                                    @elseif($ticket->status === 'en_curso')
                                        <a href="{{ route('checklists.checkin', $ticket) }}"
                                            class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-bold rounded-lg text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 shadow-sm hover:shadow transition-all duration-200">
                                            <i class="fas fa-sign-in-alt mr-1.5"></i> Checkin
                                        </a>
                                    @endif
                                    <a href="{{ route('tickets.show', $ticket) }}"
                                        class="inline-flex items-center px-3 py-1.5 border border-gray-300 text-xs font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                                        <i class="fas fa-eye mr-1"></i> Ver
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center justify-center">
                                        <div class="bg-gray-50 rounded-full p-4 mb-3">
                                            <i class="fas fa-clipboard-check text-gray-300 text-3xl"></i>
                                        </div>
                                        <p class="text-gray-500 text-lg font-medium">No tienes tickets asignados</p>
                                        <p class="text-gray-400 text-sm">¡Buen trabajo! Todo está al día.</p>
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
