@extends('layouts.app')

@section('title', 'Registrar Nuevo Vehículo')

@section('content')
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="mb-8 flex items-center">
            <a href="{{ route('vehicles.index') }}"
                class="mr-4 p-2 rounded-full bg-white border border-gray-200 text-gray-500 hover:text-blue-600 hover:border-blue-300 transition-all duration-200 shadow-sm">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div>
                <h1 class="text-3xl font-bold text-gray-900 tracking-tight">Registrar Nuevo Vehículo</h1>
                <p class="mt-2 text-gray-600">Complete la información para dar de alta una nueva unidad</p>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <form action="{{ route('vehicles.store') }}" method="POST" class="p-8">
                @csrf

                <!-- Sección 1: Información Básica -->
                <div class="mb-8">
                    <h3 class="text-lg font-bold text-gray-900 mb-4 pb-2 border-b border-gray-100 flex items-center">
                        <span
                            class="bg-blue-100 text-blue-600 w-8 h-8 rounded-full flex items-center justify-center mr-3 text-sm">1</span>
                        Información Básica
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Tipo de Vehículo -->
                        <div>
                            <label for="vehicle_type" class="block text-sm font-medium text-gray-700 mb-1">Tipo de Vehículo
                                <span class="text-red-500">*</span></label>
                            <select name="vehicle_type" id="vehicle_type"
                                class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm py-2.5 px-3"
                                required>
                                <option value="">Seleccione un tipo</option>
                                <option value="sedan" {{ old('vehicle_type') == 'sedan' ? 'selected' : '' }}>Sedán</option>
                                <option value="suv" {{ old('vehicle_type') == 'suv' ? 'selected' : '' }}>SUV</option>
                                <option value="pickup" {{ old('vehicle_type') == 'pickup' ? 'selected' : '' }}>Pickup</option>
                                <option value="van" {{ old('vehicle_type') == 'van' ? 'selected' : '' }}>Van</option>
                                <option value="camioneta" {{ old('vehicle_type') == 'camioneta' ? 'selected' : '' }}>Camioneta</option>
                                <option value="camion" {{ old('vehicle_type') == 'camion' ? 'selected' : '' }}>Camión</option>
                                <option value="motocicleta" {{ old('vehicle_type') == 'motocicleta' ? 'selected' : '' }}>Motocicleta</option>
                                <option value="autobus" {{ old('vehicle_type') == 'autobus' ? 'selected' : '' }}>Autobús</option>
                                <option value="coupe" {{ old('vehicle_type') == 'coupe' ? 'selected' : '' }}>Coupé</option>
                                <option value="hatchback" {{ old('vehicle_type') == 'hatchback' ? 'selected' : '' }}>Hatchback</option>
                                <option value="convertible" {{ old('vehicle_type') == 'convertible' ? 'selected' : '' }}>Convertible</option>
                                <option value="minivan" {{ old('vehicle_type') == 'minivan' ? 'selected' : '' }}>Minivan</option>
                                <option value="crossover" {{ old('vehicle_type') == 'crossover' ? 'selected' : '' }}>Crossover</option>
                                <option value="otro" {{ old('vehicle_type') == 'otro' ? 'selected' : '' }}>Otro</option>
                            </select>
                            @error('vehicle_type')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Marca -->
                        <div>
                            <label for="brand" class="block text-sm font-medium text-gray-700 mb-1">Marca <span
                                    class="text-red-500">*</span></label>
                            <input type="text" name="brand" id="brand" value="{{ old('brand') }}"
                                class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm py-2.5 px-3"
                                placeholder="Ej. Toyota" required>
                            @error('brand')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Modelo -->
                        <div>
                            <label for="model" class="block text-sm font-medium text-gray-700 mb-1">Modelo <span
                                    class="text-red-500">*</span></label>
                            <input type="text" name="model" id="model" value="{{ old('model') }}"
                                class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm py-2.5 px-3"
                                placeholder="Ej. Corolla" required>
                            @error('model')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Año -->
                        <div>
                            <label for="year" class="block text-sm font-medium text-gray-700 mb-1">Año <span
                                    class="text-red-500">*</span></label>
                            <input type="number" name="year" id="year" value="{{ old('year') }}" min="1900"
                                max="{{ date('Y') + 1 }}"
                                class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm py-2.5 px-3"
                                placeholder="Ej. 2023" required>
                            @error('year')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Color -->
                        <div>
                            <label for="color" class="block text-sm font-medium text-gray-700 mb-1">Color</label>
                            <input type="text" name="color" id="color" value="{{ old('color') }}"
                                class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm py-2.5 px-3"
                                placeholder="Ej. Blanco">
                            @error('color')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Sección 2: Detalles Técnicos -->
                <div class="mb-8">
                    <h3 class="text-lg font-bold text-gray-900 mb-4 pb-2 border-b border-gray-100 flex items-center">
                        <span
                            class="bg-blue-100 text-blue-600 w-8 h-8 rounded-full flex items-center justify-center mr-3 text-sm">2</span>
                        Detalles Técnicos
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Placas -->
                        <div>
                            <label for="plates" class="block text-sm font-medium text-gray-700 mb-1">Placas <span
                                    class="text-red-500">*</span></label>
                            <input type="text" name="plates" id="plates" value="{{ old('plates') }}"
                                class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm py-2.5 px-3 uppercase"
                                placeholder="Ej. ABC-123-D" required>
                            @error('plates')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Kilometraje Inicial -->
                        <div>
                            <label for="current_mileage" class="block text-sm font-medium text-gray-700 mb-1">Kilometraje
                                Actual <span class="text-red-500">*</span></label>
                            <div class="relative rounded-md shadow-sm">
                                <input type="number" name="current_mileage" id="current_mileage"
                                    value="{{ old('current_mileage', 0) }}" min="0"
                                    class="block w-full rounded-xl border-gray-300 pl-3 pr-12 focus:border-blue-500 focus:ring-blue-500 sm:text-sm py-2.5"
                                    required>
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                    <span class="text-gray-500 sm:text-sm">km</span>
                                </div>
                            </div>
                            @error('current_mileage')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- VIN -->
                        <div>
                            <label for="vin" class="block text-sm font-medium text-gray-700 mb-1">VIN (Número de
                                Serie)</label>
                            <input type="text" name="vin" id="vin" value="{{ old('vin') }}"
                                class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm py-2.5 px-3 uppercase"
                                placeholder="Número de identificación vehicular">
                            @error('vin')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Número de Motor -->
                        <div>
                            <label for="engine_number" class="block text-sm font-medium text-gray-700 mb-1">Número de
                                Motor</label>
                            <input type="text" name="engine_number" id="engine_number"
                                value="{{ old('engine_number') }}"
                                class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm py-2.5 px-3 uppercase"
                                placeholder="Número de serie del motor">
                            @error('engine_number')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Tarjeta de Circulación -->
                        <div class="md:col-span-2">
                            <label for="circulation_card" class="block text-sm font-medium text-gray-700 mb-1">Tarjeta de
                                Circulación</label>
                            <input type="text" name="circulation_card" id="circulation_card"
                                value="{{ old('circulation_card') }}"
                                class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm py-2.5 px-3"
                                placeholder="Folio de la tarjeta de circulación">
                            @error('circulation_card')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Sección 3: Notas Adicionales -->
                <div class="mb-8">
                    <h3 class="text-lg font-bold text-gray-900 mb-4 pb-2 border-b border-gray-100 flex items-center">
                        <span
                            class="bg-blue-100 text-blue-600 w-8 h-8 rounded-full flex items-center justify-center mr-3 text-sm">3</span>
                        Notas Adicionales
                    </h3>
                    <div>
                        <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">Observaciones</label>
                        <textarea name="notes" id="notes" rows="4"
                            class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm p-3"
                            placeholder="Cualquier detalle adicional sobre el vehículo...">{{ old('notes') }}</textarea>
                        @error('notes')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Botones de Acción -->
                <div class="flex items-center justify-end gap-4 pt-6 border-t border-gray-100">
                    <a href="{{ route('vehicles.index') }}"
                        class="inline-flex items-center px-6 py-3 border border-gray-300 shadow-sm text-base font-medium rounded-xl text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200">
                        Cancelar
                    </a>
                    <button type="submit"
                        class="inline-flex items-center px-6 py-3 border border-transparent shadow-lg text-base font-medium rounded-xl text-white bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200 transform hover:-translate-y-0.5">
                        <i class="fas fa-save mr-2"></i> Guardar Vehículo
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
