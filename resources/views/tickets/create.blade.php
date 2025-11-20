@extends('layouts.app')

@section('title', 'Nueva Requisición de Vehículo')

@section('content')
<style>
    .form-wrapper {
        max-width: 800px;
        margin: 0 auto;
        padding: 0 1rem;
    }
</style>

<div class="form-wrapper">
    <div class="mb-4 sm:mb-6">
        <a href="{{ route('tickets.index') }}" class="text-blue-600 hover:text-blue-800 font-medium text-sm sm:text-base">
            <i class="fas fa-arrow-left mr-2"></i>Volver a Mis Solicitudes
        </a>
    </div>

    <div class="bg-white rounded-lg shadow-md p-6">
        <h1 class="text-2xl md:text-3xl font-bold text-gray-800 mb-6">Nueva Requisición de Vehículo</h1>

        @if($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form action="{{ route('tickets.store') }}" method="POST">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Requisición -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        📋 Requisición <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="requisicion" value="{{ old('requisicion') }}" 
                           class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500" 
                           placeholder="Ej: GRC-061/25" required>
                    <p class="text-xs text-gray-500 mt-1">Número de requisición interna</p>
                </div>

                <!-- Destino -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        📍 Destino <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="destination" value="{{ old('destination') }}" 
                           class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500" 
                           placeholder="Ej: IGASAMEX, Monterrey" required>
                </div>

                <!-- Cliente -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        🏢 Cliente (Opcional)
                    </label>
                    <input type="text" name="cliente" value="{{ old('cliente') }}" 
                           class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500" 
                           placeholder="Ej: @Sergio Antonio Ordaz Espinoza">
                </div>

                <!-- Fecha -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        📅 Fecha Necesaria <span class="text-red-500">*</span>
                    </label>
                    <input type="date" name="requested_date" value="{{ old('requested_date', now()->toDateString()) }}" 
                           class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500" 
                           required>
                </div>

                <!-- Hora de Salida -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        🕐 Hora de Salida <span class="text-red-500">*</span>
                    </label>
                    <input type="time" name="requested_time_start" value="{{ old('requested_time_start') }}" 
                           class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500" 
                           required>
                </div>

                <!-- Hora de Regreso (Opcional) -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        🕐 Hora de Regreso Estimada
                    </label>
                    <input type="time" name="requested_time_end" value="{{ old('requested_time_end') }}" 
                           class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>

                <!-- Propósito -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        📝 Propósito del Viaje <span class="text-red-500">*</span>
                    </label>
                    <textarea name="purpose" rows="3" 
                              class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500" 
                              placeholder="Ej: Visita a cliente para presentación de servicios" required>{{ old('purpose') }}</textarea>
                </div>
            </div>

            <!-- Información del Mensaje -->
            <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mt-6 mb-6">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-info-circle text-blue-500"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-blue-700">
                            <strong>Nota:</strong> Su requisición será enviada a los encargados (Ana Lilia y José) quienes asignarán el vehículo y despachador correspondiente.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Botones -->
            <div class="flex flex-col sm:flex-row gap-3 pt-6 border-t">
                <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-6 rounded-lg transition duration-200">
                    <i class="fas fa-paper-plane mr-2"></i>Enviar Requisición
                </button>
                <a href="{{ route('tickets.index') }}" class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold py-3 px-6 rounded-lg transition duration-200 text-center">
                    <i class="fas fa-times mr-2"></i>Cancelar
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
