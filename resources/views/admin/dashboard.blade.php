@extends('layouts.app')

@section('title', 'Dashboard de Administración')

@section('content')
    <div class="max-w-7xl mx-auto">
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 tracking-tight">
                    <i class="fas fa-chart-line mr-2 text-blue-600"></i>Dashboard de Administración
                </h1>
                <p class="mt-2 text-gray-600">Visión general del rendimiento del sistema</p>
            </div>
        </div>

        <!-- Filtros de Fecha -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-8">
            <form method="GET" action="{{ route('admin.dashboard') }}" class="flex flex-wrap gap-4 items-end">
                <div class="flex-1 min-w-[150px]">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Periodo</label>
                    <div class="relative">
                        <select name="period"
                            class="block w-full pl-3 pr-10 py-2.5 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-xl"
                            onchange="this.form.submit()">
                            <option value="day" {{ ($period ?? 'month') == 'day' ? 'selected' : '' }}>Por Día</option>
                            <option value="week" {{ ($period ?? 'month') == 'week' ? 'selected' : '' }}>Por Semana
                            </option>
                            <option value="month" {{ ($period ?? 'month') == 'month' ? 'selected' : '' }}>Por Mes</option>
                            <option value="year" {{ ($period ?? 'month') == 'year' ? 'selected' : '' }}>Por Año</option>
                        </select>
                    </div>
                </div>
                <div class="flex-1 min-w-[150px]">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Fecha Inicio</label>
                    <input type="date" name="start_date"
                        value="{{ $startDate ?? now()->startOfMonth()->toDateString() }}"
                        class="block w-full py-2.5 px-3 border border-gray-300 rounded-xl shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                </div>
                <div class="flex-1 min-w-[150px]">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Fecha Fin</label>
                    <input type="date" name="end_date" value="{{ $endDate ?? now()->endOfMonth()->toDateString() }}"
                        class="block w-full py-2.5 px-3 border border-gray-300 rounded-xl shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                </div>
                <div class="flex gap-3">
                    <button type="submit"
                        class="inline-flex items-center px-5 py-2.5 border border-transparent text-sm font-medium rounded-xl shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200">
                        <i class="fas fa-filter mr-2"></i>Filtrar
                    </button>
                    <a href="{{ route('admin.dashboard.export', ['start_date' => $startDate ?? now()->startOfMonth()->toDateString(), 'end_date' => $endDate ?? now()->endOfMonth()->toDateString(), 'period' => $period ?? 'month']) }}"
                        class="inline-flex items-center px-5 py-2.5 border border-transparent text-sm font-medium rounded-xl shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-all duration-200">
                        <i class="fas fa-file-excel mr-2"></i>Exportar
                    </a>
                </div>
            </form>
        </div>

        <!-- Estadísticas Rápidas -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Total Solicitudes -->
            <div
                class="bg-white rounded-2xl shadow-sm hover:shadow-md transition-shadow duration-300 p-6 border border-gray-100">
                <div class="flex items-center">
                    <div class="p-3 rounded-xl bg-blue-50 text-blue-600">
                        <i class="fas fa-ticket-alt text-2xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Total Solicitudes</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $stats['totalTickets'] ?? 0 }}</p>
                    </div>
                </div>
            </div>

            <!-- Pendientes -->
            <div
                class="bg-white rounded-2xl shadow-sm hover:shadow-md transition-shadow duration-300 p-6 border border-gray-100">
                <div class="flex items-center">
                    <div class="p-3 rounded-xl bg-yellow-50 text-yellow-600">
                        <i class="fas fa-clock text-2xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Pendientes</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $stats['pendingTickets'] ?? 0 }}</p>
                    </div>
                </div>
            </div>

            <!-- Vehículos Disponibles -->
            <div
                class="bg-white rounded-2xl shadow-sm hover:shadow-md transition-shadow duration-300 p-6 border border-gray-100">
                <div class="flex items-center">
                    <div class="p-3 rounded-xl bg-green-50 text-green-600">
                        <i class="fas fa-car text-2xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Vehículos Disponibles</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $stats['availableVehicles'] ?? 0 }}<span
                                class="text-sm text-gray-400 font-normal">/{{ $stats['totalVehicles'] ?? 0 }}</span></p>
                    </div>
                </div>
            </div>

            <!-- Completadas -->
            <div
                class="bg-white rounded-2xl shadow-sm hover:shadow-md transition-shadow duration-300 p-6 border border-gray-100">
                <div class="flex items-center">
                    <div class="p-3 rounded-xl bg-purple-50 text-purple-600">
                        <i class="fas fa-check-circle text-2xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Completadas</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $stats['completedTickets'] ?? 0 }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tiempos de Respuesta -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h2 class="text-lg font-bold text-gray-900 mb-6 flex items-center">
                    <i class="fas fa-stopwatch mr-2 text-blue-500"></i>Tiempo de Respuesta
                </h2>
                <div class="space-y-6">
                    <div>
                        <div class="flex justify-between mb-2">
                            <span class="text-sm font-medium text-gray-700">Aprobación Promedio</span>
                            <span
                                class="text-sm font-bold text-blue-600">{{ number_format($responseTime['approvalTime'] ?? 0, 1) }}
                                hrs</span>
                        </div>
                        <div class="w-full bg-gray-100 rounded-full h-2.5">
                            <div class="bg-blue-600 h-2.5 rounded-full transition-all duration-500"
                                style="width: {{ min(($responseTime['approvalTime'] ?? 0) * 10, 100) }}%"></div>
                        </div>
                    </div>
                    <div>
                        <div class="flex justify-between mb-2">
                            <span class="text-sm font-medium text-gray-700">Proceso Completo</span>
                            <span
                                class="text-sm font-bold text-green-600">{{ number_format($responseTime['completionTime'] ?? 0, 1) }}
                                hrs</span>
                        </div>
                        <div class="w-full bg-gray-100 rounded-full h-2.5">
                            <div class="bg-green-600 h-2.5 rounded-full transition-all duration-500"
                                style="width: {{ min(($responseTime['completionTime'] ?? 0) / 2, 100) }}%"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Calificaciones Promedio -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h2 class="text-lg font-bold text-gray-900 mb-6 flex items-center">
                    <i class="fas fa-star mr-2 text-yellow-500"></i>Calificaciones Promedio
                </h2>
                <div class="space-y-6">
                    <div>
                        <div class="flex justify-between mb-2">
                            <span class="text-sm font-medium text-gray-700">Servicio</span>
                            <span class="text-sm font-bold text-yellow-600">{{ $ratings['service'] ?? 0 }}/5 ⭐</span>
                        </div>
                        <div class="w-full bg-gray-100 rounded-full h-2.5">
                            <div class="bg-yellow-400 h-2.5 rounded-full transition-all duration-500"
                                style="width: {{ (($ratings['service'] ?? 0) / 5) * 100 }}%"></div>
                        </div>
                    </div>
                    <div>
                        <div class="flex justify-between mb-2">
                            <span class="text-sm font-medium text-gray-700">Vehículo</span>
                            <span class="text-sm font-bold text-yellow-600">{{ $ratings['vehicle'] ?? 0 }}/5 ⭐</span>
                        </div>
                        <div class="w-full bg-gray-100 rounded-full h-2.5">
                            <div class="bg-yellow-400 h-2.5 rounded-full transition-all duration-500"
                                style="width: {{ (($ratings['vehicle'] ?? 0) / 5) * 100 }}%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Gráficos -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <!-- Gráfico de Estados -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h2 class="text-lg font-bold text-gray-900 mb-6 flex items-center">
                    <i class="fas fa-chart-pie mr-2 text-purple-500"></i>Solicitudes por Estado
                </h2>
                <div class="relative h-80 w-full flex justify-center">
                    <canvas id="statusChart"></canvas>
                </div>
            </div>

            <!-- Gráfico de Timeline -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h2 class="text-lg font-bold text-gray-900 mb-6 flex items-center">
                    <i class="fas fa-chart-area mr-2 text-blue-500"></i>Evolución de Solicitudes
                </h2>
                <div class="relative h-80 w-full">
                    <canvas id="timelineChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Top Vehículos y Usuarios -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <!-- Vehículos más utilizados -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h2 class="text-lg font-bold text-gray-900 mb-6 flex items-center">
                    <i class="fas fa-car mr-2 text-indigo-500"></i>Vehículos Más Utilizados
                </h2>
                @if ($topVehicles->count() > 0)
                    <div class="space-y-4">
                        @foreach ($topVehicles as $item)
                            <div
                                class="flex items-center justify-between p-4 bg-gray-50 rounded-xl hover:bg-gray-100 transition-colors">
                                <div class="flex items-center">
                                    <div
                                        class="h-10 w-10 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600 mr-4">
                                        <i class="fas fa-car"></i>
                                    </div>
                                    <div>
                                        <p class="font-bold text-gray-900">{{ $item->vehicle->brand }}
                                            {{ $item->vehicle->model }}</p>
                                        <p class="text-sm text-gray-500">{{ $item->vehicle->plates }}</p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <span class="block text-2xl font-bold text-indigo-600">{{ $item->total }}</span>
                                    <span class="text-xs text-gray-500 uppercase tracking-wider">viajes</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <p class="text-gray-500">No hay datos disponibles</p>
                    </div>
                @endif
            </div>

            <!-- Usuarios más activos -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h2 class="text-lg font-bold text-gray-900 mb-6 flex items-center">
                    <i class="fas fa-users mr-2 text-green-500"></i>Usuarios Más Activos
                </h2>
                @if ($topUsers->count() > 0)
                    <div class="space-y-4">
                        @foreach ($topUsers as $item)
                            <div
                                class="flex items-center justify-between p-4 bg-gray-50 rounded-xl hover:bg-gray-100 transition-colors">
                                <div class="flex items-center">
                                    <div
                                        class="h-10 w-10 rounded-full bg-green-100 flex items-center justify-center text-green-600 mr-4">
                                        <span class="font-bold text-sm">{{ substr($item->user->name, 0, 2) }}</span>
                                    </div>
                                    <div>
                                        <p class="font-bold text-gray-900">{{ $item->user->name }}</p>
                                        <p class="text-sm text-gray-500">{{ $item->user->email }}</p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <span class="block text-2xl font-bold text-green-600">{{ $item->total }}</span>
                                    <span class="text-xs text-gray-500 uppercase tracking-wider">solicitudes</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <p class="text-gray-500">No hay datos disponibles</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script>
        // Configuración global de fuentes
        Chart.defaults.font.family = "'Inter', sans-serif";
        Chart.defaults.color = '#6B7280';

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
                        '#A78BFA' // Púrpura
                    ],
                    borderWidth: 0,
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '70%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            usePointStyle: true,
                            padding: 20,
                            font: {
                                size: 12
                            }
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
                    backgroundColor: (context) => {
                        const ctx = context.chart.ctx;
                        const gradient = ctx.createLinearGradient(0, 0, 0, 300);
                        gradient.addColorStop(0, 'rgba(59, 130, 246, 0.2)');
                        gradient.addColorStop(1, 'rgba(59, 130, 246, 0)');
                        return gradient;
                    },
                    fill: true,
                    tension: 0.4,
                    borderWidth: 3,
                    pointRadius: 4,
                    pointBackgroundColor: '#ffffff',
                    pointBorderColor: '#3B82F6',
                    pointBorderWidth: 2,
                    pointHoverRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false,
                        backgroundColor: 'rgba(255, 255, 255, 0.9)',
                        titleColor: '#111827',
                        bodyColor: '#4B5563',
                        borderColor: '#E5E7EB',
                        borderWidth: 1,
                        padding: 10,
                        displayColors: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            borderDash: [2, 4],
                            color: '#F3F4F6',
                            drawBorder: false
                        },
                        ticks: {
                            stepSize: 1
                        }
                    },
                    x: {
                        grid: {
                            display: false,
                            drawBorder: false
                        }
                    }
                },
                interaction: {
                    mode: 'nearest',
                    axis: 'x',
                    intersect: false
                }
            }
        });
    </script>
@endsection
