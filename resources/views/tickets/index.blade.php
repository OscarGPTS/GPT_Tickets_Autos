@extends('layouts.app')

@section('title', 'Mis Solicitudes de Vehículos')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-800">Mis Solicitudes de Vehículos</h1>
        @can('create', App\Models\Ticket::class)
        <a href="{{ route('tickets.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-200">
            <i class="fas fa-plus mr-2"></i>Nueva Solicitud
        </a>
        @endcan
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

    <!-- Filtros -->
    <div class="bg-white rounded-lg shadow-md p-4 mb-6">
        <form method="GET" action="{{ route('tickets.index') }}" class="flex flex-wrap gap-4">
            <div class="flex-1 min-w-[200px]">
                <label class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
                <select name="status" class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">Todos</option>
                    <option value="pendiente" {{ request('status') == 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                    <option value="aprobado" {{ request('status') == 'aprobado' ? 'selected' : '' }}>Aprobado</option>
                    <option value="rechazado" {{ request('status') == 'rechazado' ? 'selected' : '' }}>Rechazado</option>
                    <option value="en_uso" {{ request('status') == 'en_uso' ? 'selected' : '' }}>En Uso</option>
                    <option value="completado" {{ request('status') == 'completado' ? 'selected' : '' }}>Completado</option>
                </select>
            </div>
            <div class="flex-1 min-w-[200px]">
                <label class="block text-sm font-medium text-gray-700 mb-1">Fecha Desde</label>
                <input type="date" name="date_from" value="{{ request('date_from') }}" class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
            </div>
            <div class="flex-1 min-w-[200px]">
                <label class="block text-sm font-medium text-gray-700 mb-1">Fecha Hasta</label>
                <input type="date" name="date_to" value="{{ request('date_to') }}" class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
            </div>
            <div class="flex items-end">
                <button type="submit" class="bg-gray-600 hover:bg-gray-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-200">
                    <i class="fas fa-filter mr-2"></i>Filtrar
                </button>
            </div>
        </form>
    </div>

    <!-- Lista de Tickets -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        @if($tickets->count() > 0)
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Folio</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Usuario</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Destino</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha Salida</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Vehículo</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($tickets as $ticket)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            #{{ $ticket->id }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $ticket->user->name }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900">
                            {{ Str::limit($ticket->destination, 30) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ \Carbon\Carbon::parse($ticket->requested_date)->format('d/m/Y') }}
                            @if($ticket->requested_time_start)
                            {{ \Carbon\Carbon::parse($ticket->requested_time_start)->format('H:i') }}
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @php
                                $statusColors = [
                                    'pendiente' => 'bg-yellow-100 text-yellow-800',
                                    'aprobado' => 'bg-green-100 text-green-800',
                                    'rechazado' => 'bg-red-100 text-red-800',
                                    'en_uso' => 'bg-blue-100 text-blue-800',
                                    'completado' => 'bg-gray-100 text-gray-800',
                                ];
                            @endphp
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusColors[$ticket->status] ?? 'bg-gray-100 text-gray-800' }}">
                                {{ ucfirst($ticket->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            @if($ticket->vehicle)
                                {{ $ticket->vehicle->brand }} {{ $ticket->vehicle->model }}
                            @else
                                <span class="text-gray-400">Sin asignar</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <a href="{{ route('tickets.show', $ticket) }}" class="text-blue-600 hover:text-blue-900 mr-3">
                                <i class="fas fa-eye"></i>
                            </a>
                            @can('update', $ticket)
                            @if($ticket->status === 'pendiente')
                            <a href="{{ route('tickets.edit', $ticket) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">
                                <i class="fas fa-edit"></i>
                            </a>
                            @endif
                            @endcan
                            
                            @can('approve', $ticket)
                            @if($ticket->status === 'pendiente')
                            <form action="{{ route('tickets.approve', $ticket) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="text-green-600 hover:text-green-900 mr-3" onclick="return confirm('¿Aprobar esta solicitud?')">
                                    <i class="fas fa-check-circle"></i>
                                </button>
                            </form>
                            <form action="{{ route('tickets.reject', $ticket) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('¿Rechazar esta solicitud?')">
                                    <i class="fas fa-times-circle"></i>
                                </button>
                            </form>
                            @endif
                            @endcan
                            
                            @if($ticket->status === 'aprobado' && auth()->user()->hasRole('despachador'))
                            <a href="{{ route('checklists.checkout', $ticket) }}" class="text-purple-600 hover:text-purple-900">
                                <i class="fas fa-clipboard-check"></i> Checkout
                            </a>
                            @endif
                            
                            @if($ticket->status === 'en_uso' && auth()->user()->hasRole('despachador'))
                            <a href="{{ route('checklists.checkin', $ticket) }}" class="text-orange-600 hover:text-orange-900">
                                <i class="fas fa-clipboard-check"></i> Checkin
                            </a>
                            @endif
                            
                            @if($ticket->status === 'completado' && $ticket->user_id === auth()->id() && !$ticket->service_rating)
                            <a href="{{ route('tickets.rate', $ticket) }}" class="text-yellow-600 hover:text-yellow-900">
                                <i class="fas fa-star"></i> Calificar
                            </a>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $tickets->links() }}
        </div>
        @else
        <div class="text-center py-12">
            <i class="fas fa-inbox text-gray-400 text-5xl mb-4"></i>
            <p class="text-gray-500 text-lg">No hay solicitudes registradas</p>
            @can('create', App\Models\Ticket::class)
            <a href="{{ route('tickets.create') }}" class="inline-block mt-4 bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-200">
                Crear primera solicitud
            </a>
            @endcan
        </div>
        @endif
    </div>
</div>
@endsection
