@extends('layouts.app')

@section('title', 'Editar Vehículo')

@section('content')
<style>
    .form-wrapper {
        max-width: 1000px;
        margin: 0 auto;
        padding: 0 1rem;
    }
</style>

<div class="form-wrapper">
    <div class="mb-4 sm:mb-6">
        <a href="{{ route('vehicles.show', $vehicle) }}" class="text-blue-600 hover:text-blue-800 font-medium text-sm sm:text-base">
            <i class="fas fa-arrow-left mr-2"></i>Volver al Detalle
        </a>
    </div>

    <div class="bg-white rounded-lg shadow-md p-6">
        <h1 class="text-2xl md:text-3xl font-bold text-gray-800 mb-6">Editar Vehículo</h1>

        @if($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form action="{{ route('vehicles.update', $vehicle) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Información General -->
                <div class="md:col-span-2">
                    <h2 class="text-xl font-semibold text-gray-800 mb-4 border-b pb-2">
                        <i class="fas fa-info-circle mr-2"></i>Información General
                    </h2>
                </div>

                <!-- Código Interno -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Código Interno <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="internal_code" value="{{ old('internal_code', $vehicle->internal_code) }}" 
                           class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2" 
                           placeholder="Ej: VEH-001" required>
                </div>

                <!-- Placas -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Placas <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="plates" value="{{ old('plates', $vehicle->plates) }}" 
                           class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2" 
                           placeholder="Ej: ABC-1234" required>
                </div>

                <!-- Marca -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Marca <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="brand" value="{{ old('brand', $vehicle->brand) }}" 
                           class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2" 
                           placeholder="Ej: Toyota, Ford, Nissan" required>
                </div>

                <!-- Modelo -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Modelo <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="model" value="{{ old('model', $vehicle->model) }}" 
                           class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2" 
                           placeholder="Ej: Corolla, F-150, Sentra" required>
                </div>

                <!-- Año -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Año <span class="text-red-500">*</span>
                    </label>
                    <input type="number" name="year" value="{{ old('year', $vehicle->year) }}" 
                           min="1900" max="{{ date('Y') + 1 }}"
                           class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2" 
                           placeholder="Ej: 2024" required>
                </div>

                <!-- Número de Serie -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Número de Serie (VIN)
                    </label>
                    <input type="text" name="serial_number" value="{{ old('serial_number', $vehicle->serial_number) }}" 
                           class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2" 
                           placeholder="Ej: 1HGBH41JXMN109186">
                </div>

                <!-- Color -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Color
                    </label>
                    <input type="text" name="color" value="{{ old('color', $vehicle->color) }}" 
                           class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2" 
                           placeholder="Ej: Blanco, Negro, Gris">
                </div>

                <!-- Tipo de Vehículo -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Tipo de Vehículo <span class="text-red-500">*</span>
                    </label>
                    <select name="vehicle_type" class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2" required>
                        <option value="">Seleccione un tipo</option>
                        <option value="sedan" {{ old('vehicle_type', $vehicle->vehicle_type) == 'sedan' ? 'selected' : '' }}>Sedán</option>
                        <option value="suv" {{ old('vehicle_type', $vehicle->vehicle_type) == 'suv' ? 'selected' : '' }}>SUV</option>
                        <option value="pickup" {{ old('vehicle_type', $vehicle->vehicle_type) == 'pickup' ? 'selected' : '' }}>Pickup</option>
                        <option value="van" {{ old('vehicle_type', $vehicle->vehicle_type) == 'van' ? 'selected' : '' }}>Van</option>
                        <option value="camioneta" {{ old('vehicle_type', $vehicle->vehicle_type) == 'camioneta' ? 'selected' : '' }}>Camioneta</option>
                    </select>
                </div>

                <!-- Especificaciones Técnicas -->
                <div class="md:col-span-2 mt-4">
                    <h2 class="text-xl font-semibold text-gray-800 mb-4 border-b pb-2">
                        <i class="fas fa-cogs mr-2"></i>Especificaciones Técnicas
                    </h2>
                </div>

                <!-- Capacidad de Pasajeros -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Capacidad de Pasajeros <span class="text-red-500">*</span>
                    </label>
                    <input type="number" name="capacity_passengers" value="{{ old('capacity_passengers', $vehicle->capacity_passengers) }}" 
                           min="1" max="50"
                           class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2" 
                           required>
                </div>

                <!-- Capacidad de Carga -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Capacidad de Carga (kg)
                    </label>
                    <input type="number" name="capacity_cargo" value="{{ old('capacity_cargo', $vehicle->capacity_cargo) }}" 
                           min="0" step="0.01"
                           class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2" 
                           placeholder="Ej: 500">
                </div>

                <!-- Tipo de Combustible -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Tipo de Combustible <span class="text-red-500">*</span>
                    </label>
                    <select name="fuel_type" class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2" required>
                        <option value="">Seleccione un tipo</option>
                        <option value="gasolina" {{ old('fuel_type', $vehicle->fuel_type) == 'gasolina' ? 'selected' : '' }}>Gasolina</option>
                        <option value="diesel" {{ old('fuel_type', $vehicle->fuel_type) == 'diesel' ? 'selected' : '' }}>Diésel</option>
                        <option value="electrico" {{ old('fuel_type', $vehicle->fuel_type) == 'electrico' ? 'selected' : '' }}>Eléctrico</option>
                        <option value="hibrido" {{ old('fuel_type', $vehicle->fuel_type) == 'hibrido' ? 'selected' : '' }}>Híbrido</option>
                    </select>
                </div>

                <!-- Kilometraje Actual -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Kilometraje Actual <span class="text-red-500">*</span>
                    </label>
                    <input type="number" name="current_mileage" value="{{ old('current_mileage', $vehicle->current_mileage) }}" 
                           min="0" step="0.01"
                           class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2" 
                           placeholder="Ej: 15000" required>
                </div>

                <!-- Estado -->
                <div class="md:col-span-2 mt-4">
                    <h2 class="text-xl font-semibold text-gray-800 mb-4 border-b pb-2">
                        <i class="fas fa-toggle-on mr-2"></i>Estado del Vehículo
                    </h2>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Estado <span class="text-red-500">*</span>
                    </label>
                    <select name="status" class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2" required>
                        <option value="disponible" {{ old('status', $vehicle->status) == 'disponible' ? 'selected' : '' }}>Disponible</option>
                        <option value="en_uso" {{ old('status', $vehicle->status) == 'en_uso' ? 'selected' : '' }}>En Uso</option>
                        <option value="mantenimiento" {{ old('status', $vehicle->status) == 'mantenimiento' ? 'selected' : '' }}>Mantenimiento</option>
                        <option value="fuera_servicio" {{ old('status', $vehicle->status) == 'fuera_servicio' ? 'selected' : '' }}>Fuera de Servicio</option>
                    </select>
                </div>

                <!-- Notas -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Notas / Observaciones
                    </label>
                    <textarea name="notes" rows="3" 
                              class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2" 
                              placeholder="Información adicional sobre el vehículo">{{ old('notes', $vehicle->notes) }}</textarea>
                </div>
            </div>

            <!-- Botones -->
            <div class="flex flex-col sm:flex-row gap-3 pt-6 mt-6 border-t">
                <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-6 rounded-lg transition duration-200">
                    <i class="fas fa-save mr-2"></i>Guardar Cambios
                </button>
                <a href="{{ route('vehicles.show', $vehicle) }}" class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold py-3 px-6 rounded-lg transition duration-200 text-center">
                    <i class="fas fa-times mr-2"></i>Cancelar
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
