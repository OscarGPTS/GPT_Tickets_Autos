@extends('layouts.app')

@section('title', 'Mis Solicitudes de Vehículos')

@section('content')
    <div class="max-w-7xl mx-auto">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-8">
            <div>
                @if(Auth::user()->hasRole('encargado') && (!request()->has('view') || request('view') !== 'todas'))
                    <h1 class="text-3xl font-bold text-gray-900 tracking-tight">Requisiciones Pendientes</h1>
                    <p class="mt-2 text-gray-600">Solicitudes que requieren tu aprobación</p>
                @else
                    <h1 class="text-3xl font-bold text-gray-900 tracking-tight">Mis Solicitudes</h1>
                    <p class="mt-2 text-gray-600">Historial y estado de tus requisiciones de vehículos</p>
                @endif
            </div>
            <div class="mt-4 sm:mt-0 flex gap-3">
                @if(Auth::user()->hasRole('encargado'))
                    @if(!request()->has('view') || request('view') !== 'todas')
                        <a href="{{ route('tickets.index', ['view' => 'todas']) }}"
                            class="inline-flex items-center px-5 py-2.5 border border-gray-300 shadow-sm text-sm font-medium rounded-xl text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200">
                            <i class="fas fa-list mr-2"></i>Ver Todas
                        </a>
                    @else
                        <a href="{{ route('tickets.index') }}"
                            class="inline-flex items-center px-5 py-2.5 border border-gray-300 shadow-sm text-sm font-medium rounded-xl text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200">
                            <i class="fas fa-clock mr-2"></i>Solo Pendientes
                        </a>
                    @endif
                @endif
                @can('create', App\Models\Ticket::class)
                    <a href="{{ route('tickets.create') }}"
                        class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-xl shadow-lg text-white bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200 transform hover:-translate-y-0.5">
                        <i class="fas fa-plus mr-2"></i>Nueva Solicitud
                    </a>
                @endcan
            </div>
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
            <form method="GET" action="{{ route('tickets.index') }}" class="flex flex-wrap gap-4 items-end">
                <div class="flex-1 min-w-[200px]">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
                    <div class="relative">
                        <select name="status"
                            class="block w-full pl-3 pr-10 py-2.5 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-xl">
                            <option value="">Todos</option>
                            <option value="pendiente" {{ request('status') == 'pendiente' ? 'selected' : '' }}>Pendiente
                            </option>
                            <option value="aprobado" {{ request('status') == 'aprobado' ? 'selected' : '' }}>Aprobado
                            </option>
                            <option value="rechazado" {{ request('status') == 'rechazado' ? 'selected' : '' }}>Rechazado
                            </option>
                            <option value="en_uso" {{ request('status') == 'en_uso' ? 'selected' : '' }}>En Uso</option>
                            <option value="completado" {{ request('status') == 'completado' ? 'selected' : '' }}>Completado
                            </option>
                        </select>
                    </div>
                </div>
                <div class="flex-1 min-w-[200px]">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Fecha Desde</label>
                    <input type="date" name="date_from" value="{{ request('date_from') }}"
                        class="block w-full py-2.5 px-3 border border-gray-300 rounded-xl shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                </div>
                <div class="flex-1 min-w-[200px]">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Fecha Hasta</label>
                    <input type="date" name="date_to" value="{{ request('date_to') }}"
                        class="block w-full py-2.5 px-3 border border-gray-300 rounded-xl shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                </div>
                <div class="flex-none">
                    <button type="submit"
                        class="inline-flex items-center px-5 py-2.5 border border-transparent text-sm font-medium rounded-xl shadow-sm text-white bg-gray-800 hover:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-all duration-200">
                        <i class="fas fa-filter mr-2"></i>Filtrar
                    </button>
                </div>
            </form>
        </div>

        <!-- Lista de Tickets -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            @if ($tickets->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-100">
                        <thead class="bg-gray-50">
                            <tr>
                                <th
                                    class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                    Folio</th>
                                <th
                                    class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                    Usuario</th>
                                <th
                                    class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                    Destino</th>
                                <th
                                    class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                    Fecha Salida</th>
                                <th
                                    class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                    Estado</th>
                                <th
                                    class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                    Vehículo</th>
                                <th
                                    class="px-6 py-4 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                    Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            @foreach ($tickets as $ticket)
                                <tr class="hover:bg-gray-50 transition-colors duration-150">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="text-sm font-bold text-gray-900 bg-gray-100 px-2 py-1 rounded-md">
                                            #{{ $ticket->id }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div
                                                class="h-8 w-8 rounded-full bg-gray-200 flex items-center justify-center text-gray-500 mr-3">
                                                <span
                                                    class="text-xs font-bold">{{ substr($ticket->user->name, 0, 2) }}</span>
                                            </div>
                                            <div class="text-sm font-medium text-gray-900">{{ $ticket->user->name }}</div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-700">
                                        {{ Str::limit($ticket->destination, 30) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <div class="flex items-center">
                                            <i class="far fa-calendar-alt mr-2 text-gray-400"></i>
                                            {{ \Carbon\Carbon::parse($ticket->requested_date)->format('d/m/Y') }}
                                            @if ($ticket->requested_time_start)
                                                <span class="ml-2 text-xs bg-gray-100 px-1.5 py-0.5 rounded">
                                                    {{ \Carbon\Carbon::parse($ticket->requested_time_start)->format('H:i') }}
                                                </span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @php
                                            $statusColors = [
                                                'pendiente' => 'bg-amber-100 text-amber-800 border border-amber-200',
                                                'aprobado' => 'bg-blue-100 text-blue-800 border border-blue-200',
                                                'rechazado' => 'bg-red-100 text-red-800 border border-red-200',
                                                'en_uso' => 'bg-indigo-100 text-indigo-800 border border-indigo-200',
                                                'completado' => 'bg-green-100 text-green-800 border border-green-200',
                                            ];
                                        @endphp
                                        <span
                                            class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusColors[$ticket->status] ?? 'bg-gray-100 text-gray-800' }}">
                                            {{ ucfirst($ticket->status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        @if ($ticket->vehicle)
                                            <div class="flex items-center">
                                                <i class="fas fa-car-side mr-2 text-gray-400"></i>
                                                {{ $ticket->vehicle->brand }} {{ $ticket->vehicle->model }}
                                            </div>
                                        @else
                                            <span class="text-gray-400 italic">Sin asignar</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex justify-end items-center space-x-2">
                                            <a href="{{ route('tickets.show', $ticket) }}"
                                                class="inline-flex items-center px-3 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors"
                                                title="Ver detalles">
                                                <i class="fas fa-eye mr-2"></i>
                                                Ver
                                            </a>

                                            @if ($ticket->status === 'aprobado' && auth()->user()->hasRole('despachador'))
                                                <a href="{{ route('checklists.checkout', $ticket) }}"
                                                    class="text-purple-600 hover:text-purple-900 p-2 hover:bg-purple-50 rounded-lg transition-colors"
                                                    title="Checkout">
                                                    <i class="fas fa-clipboard-check"></i>
                                                </a>
                                            @endif

                                            @if ($ticket->status === 'en_uso' && auth()->user()->hasRole('despachador'))
                                                <a href="{{ route('checklists.checkin', $ticket) }}"
                                                    class="text-orange-600 hover:text-orange-900 p-2 hover:bg-orange-50 rounded-lg transition-colors"
                                                    title="Checkin">
                                                    <i class="fas fa-clipboard-check"></i>
                                                </a>
                                            @endif

                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                    {{ $tickets->links() }}
                </div>
            @else
                <div class="text-center py-16">
                    <div class="bg-gray-50 rounded-full p-6 inline-block mb-4">
                        <i class="fas fa-inbox text-gray-400 text-5xl"></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900">No hay solicitudes</h3>
                    <p class="text-gray-500 mt-1 mb-6">No se encontraron solicitudes con los filtros actuales.</p>
                    @can('create', App\Models\Ticket::class)
                        <a href="{{ route('tickets.create') }}"
                            class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-xl shadow-sm text-white bg-blue-600 hover:bg-blue-700 transition-all duration-200">
                            <i class="fas fa-plus mr-2"></i> Crear primera solicitud
                        </a>
                    @endcan
                </div>
            @endif
        </div>
    </div>
@endsection
