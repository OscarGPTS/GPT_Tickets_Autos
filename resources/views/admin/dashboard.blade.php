@extends('layouts.app')

@section('title', 'Dashboard de Administración')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-800">
            <i class="fas fa-chart-line mr-2"></i>Dashboard de Administración
        </h1>
    </div>

    <!-- Filtros de Fecha -->
    <div class="bg-white rounded-lg shadow-md p-4 mb-6">
        <form method="GET" action="{{ route('admin.dashboard') }}" class="flex flex-wrap gap-4">
            <div class="flex-1 min-w-[150px]">
                <label class="block text-sm font-medium text-gray-700 mb-1">Periodo</label>
                <select name="period" class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500" onchange="this.form.submit()">
                    <option value="day" {{ ($period ?? 'month') == 'day' ? 'selected' : '' }}>Por Día</option>
                    <option value="week" {{ ($period ?? 'month') == 'week' ? 'selected' : '' }}>Por Semana</option>
                    <option value="month" {{ ($period ?? 'month') == 'month' ? 'selected' : '' }}>Por Mes</option>
                    <option value="year" {{ ($period ?? 'month') == 'year' ? 'selected' : '' }}>Por Año</option>
                </select>
            </div>
            <div class="flex-1 min-w-[150px]">
                <label class="block text-sm font-medium text-gray-700 mb-1">Fecha Inicio</label>
                <input type="date" name="start_date" value="{{ $startDate ?? now()->startOfMonth()->toDateString() }}" class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
            </div>
            <div class="flex-1 min-w-[150px]">
                <label class="block text-sm font-medium text-gray-700 mb-1">Fecha Fin</label>
                <input type="date" name="end_date" value="{{ $endDate ?? now()->endOfMonth()->toDateString() }}" class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
            </div>
            <div class="flex items-end gap-2">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-200">
                    <i class="fas fa-filter mr-2"></i>Filtrar
                </button>
                <a href="{{ route('admin.dashboard.export', ['start_date' => $startDate ?? now()->startOfMonth()->toDateString(), 'end_date' => $endDate ?? now()->endOfMonth()->toDateString(), 'period' => $period ?? 'month']) }}" 
                   class="bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-200">
                    <i class="fas fa-file-excel mr-2"></i>Exportar Excel
                </a>
            </div>
        </form>
    </div>

    <!-- Estadísticas Rápidas -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
        <!-- Total Solicitudes -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                    <i class="fas fa-ticket-alt text-2xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-500">Total Solicitudes</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['totalTickets'] ?? 0 }}</p>
                </div>
            </div>
        </div>

        <!-- Pendientes -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                    <i class="fas fa-clock text-2xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-500">Pendientes</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['pendingTickets'] ?? 0 }}</p>
                </div>
            </div>
        </div>

        <!-- Vehículos Disponibles -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100 text-green-600">
                    <i class="fas fa-car text-2xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-500">Vehículos Disponibles</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['availableVehicles'] ?? 0 }}/{{ $stats['totalVehicles'] ?? 0 }}</p>
                </div>
            </div>
        </div>

        <!-- Completadas -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                    <i class="fas fa-check-circle text-2xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-500">Completadas</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['completedTickets'] ?? 0 }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Tiempos de Respuesta -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold text-gray-800 mb-4 border-b pb-2">
                <i class="fas fa-stopwatch mr-2"></i>Tiempo de Respuesta Entre Áreas
            </h2>
            <div class="space-y-4">
                <div>
                    <div class="flex justify-between mb-1">
                        <span class="text-gray-700">Tiempo de Aprobación Promedio</span>
                        <span class="font-bold text-blue-600">{{ number_format($responseTime['approvalTime'] ?? 0, 1) }} hrs</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-blue-600 h-2 rounded-full" style="width: {{ min(($responseTime['approvalTime'] ?? 0) * 10, 100) }}%"></div>
                    </div>
                </div>
                <div>
                    <div class="flex justify-between mb-1">
                        <span class="text-gray-700">Tiempo Total de Proceso Promedio</span>
                        <span class="font-bold text-green-600">{{ number_format($responseTime['completionTime'] ?? 0, 1) }} hrs</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-green-600 h-2 rounded-full" style="width: {{ min(($responseTime['completionTime'] ?? 0) / 2, 100) }}%"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Calificaciones Promedio -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold text-gray-800 mb-4 border-b pb-2">
                <i class="fas fa-star mr-2"></i>Calificaciones Promedio
            </h2>
            <div class="space-y-4">
                <div>
                    <div class="flex justify-between mb-1">
                        <span class="text-gray-700">Servicio</span>
                        <span class="font-bold text-yellow-600">{{ $ratings['service'] ?? 0 }}/5 ⭐</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-yellow-500 h-2 rounded-full" style="width: {{ (($ratings['service'] ?? 0) / 5) * 100 }}%"></div>
                    </div>
                </div>
                <div>
                    <div class="flex justify-between mb-1">
                        <span class="text-gray-700">Vehículo</span>
                        <span class="font-bold text-yellow-600">{{ $ratings['vehicle'] ?? 0 }}/5 ⭐</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-yellow-500 h-2 rounded-full" style="width: {{ (($ratings['vehicle'] ?? 0) / 5) * 100 }}%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Gráficos -->
    <div class="grid grid-cols-1 gap-6 mb-6">
        <!-- Gráfico de Estados -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold text-gray-800 mb-4 border-b pb-2">
                <i class="fas fa-chart-pie mr-2"></i>Solicitudes por Estado
            </h2>
            <div style="position: relative; height: 350px; width: 100%; max-width: 500px; margin: 0 auto;">
                <canvas id="statusChart"></canvas>
            </div>
        </div>

        <!-- Gráfico de Timeline -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold text-gray-800 mb-4 border-b pb-2">
                <i class="fas fa-chart-line mr-2"></i>Evolución de Solicitudes
            </h2>
            <div style="position: relative; height: 350px; width: 100%;">
                <canvas id="timelineChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Top Vehículos y Usuarios -->
    <div class="grid grid-cols-1 gap-6 mb-6">
        <!-- Vehículos más utilizados -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold text-gray-800 mb-4 border-b pb-2">
                <i class="fas fa-car mr-2"></i>Vehículos Más Utilizados
            </h2>
            @if($topVehicles->count() > 0)
            <div class="space-y-4">
                @foreach($topVehicles as $item)
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                    <div class="flex-1">
                        <p class="font-semibold text-lg text-gray-900">{{ $item->vehicle->brand }} {{ $item->vehicle->model }}</p>
                        <p class="text-base text-gray-600">{{ $item->vehicle->plates }}</p>
                    </div>
                    <div class="text-right bg-white px-4 py-2 rounded-lg shadow-sm">
                        <span class="text-3xl font-bold text-blue-600">{{ $item->total }}</span>
                        <p class="text-sm text-gray-500">viajes</p>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <p class="text-gray-500 text-center py-8 text-lg">No hay datos disponibles</p>
            @endif
        </div>

        <!-- Usuarios más activos -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold text-gray-800 mb-4 border-b pb-2">
                <i class="fas fa-users mr-2"></i>Usuarios Más Activos
            </h2>
            @if($topUsers->count() > 0)
            <div class="space-y-4">
                @foreach($topUsers as $item)
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                    <div class="flex-1">
                        <p class="font-semibold text-lg text-gray-900">{{ $item->user->name }}</p>
                        <p class="text-base text-gray-600">{{ $item->user->email }}</p>
                    </div>
                    <div class="text-right bg-white px-4 py-2 rounded-lg shadow-sm">
                        <span class="text-3xl font-bold text-green-600">{{ $item->total }}</span>
                        <p class="text-sm text-gray-500">solicitudes</p>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <p class="text-gray-500 text-center py-8 text-lg">No hay datos disponibles</p>
            @endif
        </div>
    </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
    // Gráfico de Estados (Donut)
    const statusCtx = document.getElementById('statusChart').getContext('2d');
    new Chart(statusCtx, {
        type: 'doughnut',
        data: {
            labels: ['Pendiente', 'Aprobado', 'Rechazado', 'En Uso', 'Completado'],
            datasets: [{
                data: [
                    {{ $ticketsByStatus['pendiente'] ?? 0 }},
                    {{ $ticketsByStatus['aprobado'] ?? 0 }},
                    {{ $ticketsByStatus['rechazado'] ?? 0 }},
                    {{ $ticketsByStatus['en_uso'] ?? 0 }},
                    {{ $ticketsByStatus['completado'] ?? 0 }}
                ],
                backgroundColor: [
                    '#FCD34D', // Amarillo
                    '#34D399', // Verde
                    '#F87171', // Rojo
                    '#60A5FA', // Azul
                    '#A78BFA'  // Púrpura
                ],
                borderWidth: 2,
                borderColor: '#ffffff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            aspectRatio: 1,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        font: {
                            size: 14
                        },
                        padding: 15
                    }
                }
            }
        }
    });

    // Gráfico de Timeline (Línea)
    const timelineCtx = document.getElementById('timelineChart').getContext('2d');
    new Chart(timelineCtx, {
        type: 'line',
        data: {
            labels: {!! json_encode($ticketsTimeline->pluck('period')) !!},
            datasets: [{
                label: 'Solicitudes',
                data: {!! json_encode($ticketsTimeline->pluck('total')) !!},
                borderColor: '#3B82F6',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                fill: true,
                tension: 0.4,
                borderWidth: 3,
                pointRadius: 5,
                pointHoverRadius: 7
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1,
                        font: {
                            size: 14
                        }
                    }
                },
                x: {
                    ticks: {
                        font: {
                            size: 14
                        }
                    }
                }
            }
        }
    });
</script>
@endsection
