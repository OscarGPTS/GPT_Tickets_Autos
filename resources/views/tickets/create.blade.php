@extends('layouts.app')

@section('title', 'Nueva Solicitud de Vehículo')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('tickets.index') }}" class="text-blue-600 hover:text-blue-800 font-medium">
            <i class="fas fa-arrow-left mr-2"></i>Volver a Solicitudes
        </a>
    </div>

    <div class="bg-white rounded-lg shadow-md p-6">
        <h1 class="text-3xl font-bold text-gray-800 mb-6">Nueva Solicitud de Vehículo</h1>

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

            <table class="w-full mb-6 border border-gray-200 rounded-lg">
                <tr>
                    <td class="border border-gray-200 p-2">Destino</td>
                    <td class="border border-gray-200 p-2" colspan="3"></td>
                    <td class="border border-gray-200 p-2">Modelo</td>
                    <td class="border border-gray-200 p-2" colspan="3"></td>
                    <td class="border border-gray-200 p-2">Folio</td>
                    <td class="border border-gray-200 p-2" colspan="3"></td>
                </tr>

                <tr>
                    <td class="border border-gray-200 p-2">Hora de Salida</td>
                    <td class="border border-gray-200 p-2" colspan="3"></td>
                    <td class="border border-gray-200 p-2">Hora de entrada</td>
                    <td class="border border-gray-200 p-2" colspan="3"></td>
                    <td class="border border-gray-200 p-2">Fecha</td>
                    <td class="border border-gray-200 p-2" colspan="3"></td>
                </tr>

                <tr>
                    <td class="border border-gray-200 p-2">Kilometraje Inicial</td>
                    <td class="border border-gray-200 p-2" colspan="3"></td>
                    <td class="border border-gray-200 p-2">Nivel de combustible Inicial</td>
                    <td class="border border-gray-200 p-2" colspan="3"></td>
                    <td class="border border-gray-200 p-2">Placas</td>
                    <td class="border border-gray-200 p-2" colspan="3"></td>
                </tr>

                <tr>
                    <td class="border border-gray-200 p-2">Llantas</td>
                    <td class="border border-gray-200 p-2">Si</td>
                    <td class="border border-gray-200 p-2">No</td>
                    <td class="border border-gray-200 p-2">Frontal</td>
                    <td class="border border-gray-200 p-2">Si</td>
                    <td class="border border-gray-200 p-2">No</td>
                    <td class="border border-gray-200 p-2">Interior</td>
                    <td class="border border-gray-200 p-2">Si</td>
                    <td class="border border-gray-200 p-2">No</td>
                    <td class="border border-gray-200 p-2">Motor</td>
                    <td class="border border-gray-200 p-2">Si</td>
                    <td class="border border-gray-200 p-2">No</td>
                </tr>

                <tr>
                    <td class="border border-gray-200 p-2">Delantera derecha</td>
                    <td class="border border-gray-200 p-2"></td>
                    <td class="border border-gray-200 p-2"></td>
                    <td class="border border-gray-200 p-2">Parabrisas</td>
                    <td class="border border-gray-200 p-2"></td>
                    <td class="border border-gray-200 p-2"></td>
                    <td class="border border-gray-200 p-2">Tablero Indicadores</td>
                    <td class="border border-gray-200 p-2"></td>
                    <td class="border border-gray-200 p-2"></td>
                    <td class="border border-gray-200 p-2">Nivel aceite motor</td>
                    <td class="border border-gray-200 p-2"></td>
                    <td class="border border-gray-200 p-2"></td>
                </tr>

                <tr>
                    <td class="border border-gray-200 p-2">Delantera izquierda</td>
                    <td class="border border-gray-200 p-2"></td>
                    <td class="border border-gray-200 p-2"></td>
                    <td class="border border-gray-200 p-2">Cofre</td>
                    <td class="border border-gray-200 p-2"></td>
                    <td class="border border-gray-200 p-2"></td>
                    <td class="border border-gray-200 p-2">Switch de encendido</td>
                    <td class="border border-gray-200 p-2"></td>
                    <td class="border border-gray-200 p-2"></td>
                    <td class="border border-gray-200 p-2">Nivel anticongelante</td>
                    <td class="border border-gray-200 p-2"></td>
                    <td class="border border-gray-200 p-2"></td>
                </tr>


                <tr>
                    <td class="border border-gray-200 p-2">Vida</td>
                    <td class="border border-gray-200 p-2"></td>
                    <td class="border border-gray-200 p-2"></td>
                    <td class="border border-gray-200 p-2">Parrilla</td>
                    <td class="border border-gray-200 p-2"></td>
                    <td class="border border-gray-200 p-2"></td>
                    <td class="border border-gray-200 p-2">Controles A/C</td>
                    <td class="border border-gray-200 p-2"></td>
                    <td class="border border-gray-200 p-2"></td>
                    <td class="border border-gray-200 p-2">Nivel liquido frenos</td>
                    <td class="border border-gray-200 p-2"></td>
                    <td class="border border-gray-200 p-2"></td>
                </tr>


                <tr>
                    <td class="border border-gray-200 p-2">Trasera derecha</td>
                    <td class="border border-gray-200 p-2"></td>
                    <td class="border border-gray-200 p-2"></td>
                    <td class="border border-gray-200 p-2">Defensa</td>
                    <td class="border border-gray-200 p-2"></td>
                    <td class="border border-gray-200 p-2"></td>
                    <td class="border border-gray-200 p-2">Defroster</td>
                    <td class="border border-gray-200 p-2"></td>
                    <td class="border border-gray-200 p-2"></td>
                    <td class="border border-gray-200 p-2">Batería</td>
                    <td class="border border-gray-200 p-2"></td>
                    <td class="border border-gray-200 p-2"></td>
                </tr>

                <tr>
                    <td class="border border-gray-200 p-2">Trasera izquierda</td>
                    <td class="border border-gray-200 p-2"></td>
                    <td class="border border-gray-200 p-2"></td>
                    <td class="border border-gray-200 p-2">Molduras</td>
                    <td class="border border-gray-200 p-2"></td>
                    <td class="border border-gray-200 p-2"></td>
                    <td class="border border-gray-200 p-2">Radio</td>
                    <td class="border border-gray-200 p-2"></td>
                    <td class="border border-gray-200 p-2"></td>
                    <td class="border border-gray-200 p-2">Bayoneta de aceite motor</td>
                    <td class="border border-gray-200 p-2"></td>
                    <td class="border border-gray-200 p-2"></td>
                </tr>

                <tr>
                    <td class="border border-gray-200 p-2">Vida</td>
                    <td class="border border-gray-200 p-2"></td>
                    <td class="border border-gray-200 p-2"></td>
                    <td class="border border-gray-200 p-2">Placa</td>
                    <td class="border border-gray-200 p-2"></td>
                    <td class="border border-gray-200 p-2"></td>
                    <td class="border border-gray-200 p-2">Volante</td>
                    <td class="border border-gray-200 p-2"></td>
                    <td class="border border-gray-200 p-2"></td>
                    <td class="border border-gray-200 p-2">Tapones</td>
                    <td class="border border-gray-200 p-2"></td>
                    <td class="border border-gray-200 p-2"></td>
                </tr>

                <tr>
                    <td class="border border-gray-200 p-2">Llanta de refacción</td>
                    <td class="border border-gray-200 p-2"></td>
                    <td class="border border-gray-200 p-2"></td>
                    <td class="border border-gray-200 p-2">Salpicadera</td>
                    <td class="border border-gray-200 p-2"></td>
                    <td class="border border-gray-200 p-2"></td>
                    <td class="border border-gray-200 p-2">Bolsa de aire</td>
                    <td class="border border-gray-200 p-2"></td>
                    <td class="border border-gray-200 p-2"></td>
                    <td class="border border-gray-200 p-2">Bocina claxon</td>
                    <td class="border border-gray-200 p-2"></td>
                    <td class="border border-gray-200 p-2"></td>
                </tr>

                <tr>
                    <td class="border border-gray-200 p-2">Presión Adecuada</td>
                    <td class="border border-gray-200 p-2"></td>
                    <td class="border border-gray-200 p-2"></td>
                    <td class="border border-gray-200 p-2">Antena</td>
                    <td class="border border-gray-200 p-2"></td>
                    <td class="border border-gray-200 p-2"></td>
                    <td class="border border-gray-200 p-2">Cinturón de seguridad</td>
                    <td class="border border-gray-200 p-2"></td>
                    <td class="border border-gray-200 p-2"></td>
                    <td class="border border-gray-200 p-2">Radiador</td>
                    <td class="border border-gray-200 p-2"></td>
                    <td class="border border-gray-200 p-2"></td>
                </tr>

                <tr>
                    <td class="border border-gray-200 p-2">Luces</td>
                    <td class="border border-gray-200 p-2">Si</td>
                    <td class="border border-gray-200 p-2">No</td>
                    <td class="border border-gray-200 p-2">Otros</td>
                    <td class="border border-gray-200 p-2">Si</td>
                    <td class="border border-gray-200 p-2">No</td>
                    <td class="border border-gray-200 p-2">Coderas</td>
                    <td class="border border-gray-200 p-2"></td>
                    <td class="border border-gray-200 p-2"></td>
                    <td class="border border-gray-200 p-2">Herramienta</td>
                    <td class="border border-gray-200 p-2">Si</td>
                    <td class="border border-gray-200 p-2">No</td>
                </tr>

                <tr>
                    <td class="border border-gray-200 p-2">Intermitentes</td>
                    <td class="border border-gray-200 p-2"></td>
                    <td class="border border-gray-200 p-2"></td>
                    <td class="border border-gray-200 p-2">Mata Chispas</td>
                    <td class="border border-gray-200 p-2"></td>
                    <td class="border border-gray-200 p-2"></td>
                    <td class="border border-gray-200 p-2">Espejo interior</td>
                    <td class="border border-gray-200 p-2"></td>
                    <td class="border border-gray-200 p-2"></td>
                    <td class="border border-gray-200 p-2">Gato</td>
                    <td class="border border-gray-200 p-2"></td>
                    <td class="border border-gray-200 p-2"></td>
                </tr>

                <tr>
                    <td class="border border-gray-200 p-2">Direccional Derecha</td>
                    <td class="border border-gray-200 p-2"></td>
                    <td class="border border-gray-200 p-2"></td>
                    <td class="border border-gray-200 p-2">Alarma</td>
                    <td class="border border-gray-200 p-2"></td>
                    <td class="border border-gray-200 p-2"></td>
                    <td class="border border-gray-200 p-2">Freno de mano</td>
                    <td class="border border-gray-200 p-2"></td>
                    <td class="border border-gray-200 p-2"></td>
                    <td class="border border-gray-200 p-2">Llave de ruedas</td>
                    <td class="border border-gray-200 p-2"></td>
                    <td class="border border-gray-200 p-2"></td>
                </tr>

                <tr>
                    <td class="border border-gray-200 p-2">Direccional Izquierda</td>
                    <td class="border border-gray-200 p-2"></td>
                    <td class="border border-gray-200 p-2"></td>
                    <td class="border border-gray-200 p-2">Extintor</td>
                    <td class="border border-gray-200 p-2"></td>
                    <td class="border border-gray-200 p-2"></td>
                    <td class="border border-gray-200 p-2">Encendedor</td>
                    <td class="border border-gray-200 p-2"></td>
                    <td class="border border-gray-200 p-2"></td>
                    <td class="border border-gray-200 p-2">Cables pasa corrientes</td>
                    <td class="border border-gray-200 p-2"></td>
                    <td class="border border-gray-200 p-2"></td>
                </tr>

                <tr>
                    <td class="border border-gray-200 p-2">Luz stop</td>
                    <td class="border border-gray-200 p-2"></td>
                    <td class="border border-gray-200 p-2"></td>
                    <td class="border border-gray-200 p-2">Botiquín</td>
                    <td class="border border-gray-200 p-2"></td>
                    <td class="border border-gray-200 p-2"></td>
                    <td class="border border-gray-200 p-2">Guantera</td>
                    <td class="border border-gray-200 p-2"></td>
                    <td class="border border-gray-200 p-2"></td>
                    <td class="border border-gray-200 p-2">Caja o bolsa de herramientas</td>
                    <td class="border border-gray-200 p-2"></td>
                    <td class="border border-gray-200 p-2"></td>
                </tr>

                <tr>
                    <td class="border border-gray-200 p-2">Faros</td>
                    <td class="border border-gray-200 p-2"></td>
                    <td class="border border-gray-200 p-2"></td>
                    <td class="border border-gray-200 p-2">Tarjeta de circulación</td>
                    <td class="border border-gray-200 p-2"></td>
                    <td class="border border-gray-200 p-2"></td>
                    <td class="border border-gray-200 p-2">Manijas interiores</td>
                    <td class="border border-gray-200 p-2"></td>
                    <td class="border border-gray-200 p-2"></td>
                    <td class="border border-gray-200 p-2">Dado o birlo de seguridad</td>
                    <td class="border border-gray-200 p-2"></td>
                    <td class="border border-gray-200 p-2"></td>
                </tr>

                <tr>
                    <td class="border border-gray-200 p-2">Luces Altas</td>
                    <td class="border border-gray-200 p-2"></td>
                    <td class="border border-gray-200 p-2"></td>
                    <td class="border border-gray-200 p-2">Licencia de Conductor Vigente</td>
                    <td class="border border-gray-200 p-2"></td>
                    <td class="border border-gray-200 p-2"></td>
                    <td class="border border-gray-200 p-2">Seguros</td>
                    <td class="border border-gray-200 p-2"></td>
                    <td class="border border-gray-200 p-2"></td>
                    <td class="border border-gray-200 p-2">Calcomanías</td>
                    <td class="border border-gray-200 p-2">Si</td>
                    <td class="border border-gray-200 p-2">No</td>
                </tr>

                <tr>
                    <td class="border border-gray-200 p-2">Luz Interior</td>
                    <td class="border border-gray-200 p-2"></td>
                    <td class="border border-gray-200 p-2"></td>
                    <td class="border border-gray-200 p-2">Póliza de seguro</td>
                    <td class="border border-gray-200 p-2"></td>
                    <td class="border border-gray-200 p-2"></td>
                    <td class="border border-gray-200 p-2">Asientos</td>
                    <td class="border border-gray-200 p-2"></td>
                    <td class="border border-gray-200 p-2"></td>
                    <td class="border border-gray-200 p-2">Calcomanías de permisos</td>
                    <td class="border border-gray-200 p-2"></td>
                    <td class="border border-gray-200 p-2"></td>
                </tr>

                <tr>
                    <td class="border border-gray-200 p-2">Calaveras buen estado</td>
                    <td class="border border-gray-200 p-2"></td>
                    <td class="border border-gray-200 p-2"></td>
                    <td class="border border-gray-200 p-2">Triangulo de Emergencia</td>
                    <td class="border border-gray-200 p-2"></td>
                    <td class="border border-gray-200 p-2"></td>
                    <td class="border border-gray-200 p-2">Tapetes delanteros y traseros</td>
                    <td class="border border-gray-200 p-2"></td>
                    <td class="border border-gray-200 p-2"></td>
                    <td class="border border-gray-200 p-2">Calcomanías velocidad máxima</td>
                    <td class="border border-gray-200 p-2"></td>
                    <td class="border border-gray-200 p-2"></td>
                </tr>

                <tr>
                    <td class="border border-gray-200 p-2" colspan="12" >Mantenimiento Preventivo</td>
                </tr>

                <tr>
                    <td class="border border-gray-200 p-2" colspan="12" >Mantenimiento Correctivo</td>
                </tr>

                <tr>
                    <td class="border border-gray-200 p-2" colspan="12" >Condicion de Carrocería</td>
                </tr>
                <tr>
                    <td class="border border-gray-200 p-2" colspan="6" >Responsable de recibo o uso:</td>
                    <td class="border border-gray-200 p-2" colspan="6" >Responsable de entrega:</td>
                </tr>
            </table>

            <!-- Destino -->

            <div class="grid grid-cols-3">
                <div class="mb-4">
                    <label for="destination" class="block text-sm font-medium text-gray-700 mb-2">
                        Destino <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                        id="destination" 
                        name="destination" 
                        value="{{ old('destination') }}"
                        class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('destination') border-red-500 @enderror"
                        placeholder="Ej: Ciudad de México, CDMX"
                        required>
                    @error('destination')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="model" class="block text-sm font-medium text-gray-700 mb-2">
                        Modelo <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                        id="model" 
                        name="model" 
                        value="{{ old('model') }}"
                        class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('model') border-red-500 @enderror"
                        placeholder="Ej: Aveo"
                        required>
                    @error('model')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="folio" class="block text-sm font-medium text-gray-700 mb-2">
                        Folio <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                        id="folio" 
                        name="folio" 
                        value="{{ old('folio') }}"
                        class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('folio') border-red-500 @enderror"
                        placeholder="Ej: Aveo"
                        required>
                    @error('folio')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="departure_time" class="block text-sm font-medium text-gray-700 mb-2">
                        Hora de salida <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                        id="departure_time" 
                        name="departure_time" 
                        value="{{ old('departure_time') }}"
                        class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('departure_time') border-red-500 @enderror"
                        placeholder="Ej: Aveo"
                        required>
                    @error('departure_time')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="arrival_time" class="block text-sm font-medium text-gray-700 mb-2">
                        Hora de entrada <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                        id="arrival_time" 
                        name="arrival_time" 
                        value="{{ old('arrival_time') }}"
                        class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('arrival_time') border-red-500 @enderror"
                        placeholder="Ej: Aveo"
                        required>
                    @error('arrival_time')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="date" class="block text-sm font-medium text-gray-700 mb-2">
                        Fecha <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                        id="date" 
                        name="date" 
                        value="{{ old('date') }}"
                        class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('date') border-red-500 @enderror"
                        placeholder="Ej: Aveo"
                        required>
                    @error('date')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="initial_mileage" class="block text-sm font-medium text-gray-700 mb-2">
                        Kilometraje inicial <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                        id="initial_mileage" 
                        name="initial_mileage" 
                        value="{{ old('initial_mileage') }}"
                        class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('initial_mileage') border-red-500 @enderror"
                        placeholder="Ej: Aveo"
                        required>
                    @error('initial_mileage')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="initial_fuel_level" class="block text-sm font-medium text-gray-700 mb-2">
                        Nivel de combustible inicial <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                        id="initial_fuel_level" 
                        name="initial_fuel_level" 
                        value="{{ old('initial_fuel_level') }}"
                        class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('initial_fuel_level') border-red-500 @enderror"
                        placeholder="Ej: Aveo"
                        required>
                    @error('initial_fuel_level')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="license_plate" class="block text-sm font-medium text-gray-700 mb-2">
                        Placas <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                        id="license_plate" 
                        name="license_plate" 
                        value="{{ old('license_plate') }}"
                        class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('license_plate') border-red-500 @enderror"
                        placeholder="Ej: Aveo"
                        required>
                    @error('license_plate')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="final_mileage" class="block text-sm font-medium text-gray-700 mb-2">
                        Kilometraje Final <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                        id="final_mileage" 
                        name="final_mileage" 
                        value="{{ old('final_mileage') }}"
                        class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('final_mileage') border-red-500 @enderror"
                        placeholder="Ej: Aveo"
                        required>
                    @error('final_mileage')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>


                <div class="mb-4">
                    <label for="final_fuel_level" class="block text-sm font-medium text-gray-700 mb-2">
                        Nivel de combustible Final <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                        id="final_fuel_level" 
                        name="final_fuel_level" 
                        value="{{ old('final_fuel_level') }}"
                        class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('final_fuel_level') border-red-500 @enderror"
                        placeholder="Ej: Aveo"
                        required>
                    @error('final_fuel_level')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="brand" class="block text-sm font-medium text-gray-700 mb-2">
                        Marca <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                        id="brand" 
                        name="brand" 
                        value="{{ old('brand') }}"
                        class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('brand') border-red-500 @enderror"
                        placeholder="Ej: Aveo"
                        required>
                    @error('brand')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

            </div>
            

            <!-- Propósito -->
            

            <!-- Fechas -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                

                <div>
                    <label for="requested_time_start" class="block text-sm font-medium text-gray-700 mb-2">
                        Hora de Salida <span class="text-red-500">*</span>
                    </label>
                    <input type="time" 
                           id="requested_time_start" 
                           name="requested_time_start" 
                           value="{{ old('requested_time_start') }}"
                           class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('requested_time_start') border-red-500 @enderror"
                           required>
                    @error('requested_time_start')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="mb-4">
                <label for="requested_time_end" class="block text-sm font-medium text-gray-700 mb-2">
                    Hora de Regreso Estimada
                </label>
                <input type="time" 
                       id="requested_time_end" 
                       name="requested_time_end" 
                       value="{{ old('requested_time_end') }}"
                       class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
            </div>

            <!-- Número de Pasajeros -->
            <div class="mb-4">
                <label for="passenger_count" class="block text-sm font-medium text-gray-700 mb-2">
                    Número de Pasajeros <span class="text-red-500">*</span>
                </label>
                <input type="number" 
                       id="passenger_count" 
                       name="passenger_count" 
                       value="{{ old('passenger_count', 1) }}"
                       min="1" 
                       max="20"
                       class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('passenger_count') border-red-500 @enderror"
                       required>
                @error('passenger_count')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Notas Adicionales -->
            <div class="mb-6">
                <label for="additional_notes" class="block text-sm font-medium text-gray-700 mb-2">
                    Notas Adicionales
                </label>
                <textarea id="additional_notes" 
                          name="additional_notes" 
                          rows="3"
                          class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                          placeholder="Información adicional relevante (opcional)">{{ old('additional_notes') }}</textarea>
            </div>

            <!-- Información del Usuario -->
            <div class="bg-gray-50 rounded-lg p-4 mb-6">
                <h3 class="text-sm font-semibold text-gray-700 mb-2">Información del Solicitante</h3>
                <div class="text-sm text-gray-600">
                    <p><strong>Nombre:</strong> {{ auth()->user()->name }}</p>
                    <p><strong>Email:</strong> {{ auth()->user()->email }}</p>
                    @if(auth()->user()->driverLicense)
                    <p><strong>Licencia:</strong> {{ auth()->user()->driverLicense->license_number }} (Vence: {{ \Carbon\Carbon::parse(auth()->user()->driverLicense->expiration_date)->format('d/m/Y') }})</p>
                    @else
                    <p class="text-red-600"><strong>⚠️ No tienes licencia de conducir registrada</strong></p>
                    @endif
                </div>
            </div>

            @if(!auth()->user()->driverLicense)
            <div class="bg-yellow-50 border border-yellow-400 text-yellow-800 px-4 py-3 rounded mb-4">
                <p class="text-sm">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    <strong>Importante:</strong> Es necesario registrar tu licencia de conducir antes de que tu solicitud sea aprobada. Contacta al administrador.
                </p>
            </div>
            @endif

            <!-- Botones -->
            <div class="flex justify-end gap-4">
                <a href="{{ route('tickets.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold py-2 px-6 rounded-lg transition duration-200">
                    Cancelar
                </a>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-6 rounded-lg transition duration-200">
                    <i class="fas fa-paper-plane mr-2"></i>Enviar Solicitud
                </button>
            </div>
        </form>
    </div>

    <!-- Información Adicional -->
    <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
        <h3 class="text-sm font-semibold text-blue-800 mb-2">
            <i class="fas fa-info-circle mr-2"></i>Información Importante
        </h3>
        <ul class="text-sm text-blue-700 space-y-1">
            <li>• Tu solicitud será revisada por los encargados (Ana Lilia, José Carmen)</li>
            <li>• Recibirás una notificación por correo cuando sea aprobada o rechazada</li>
            <li>• Una vez aprobada, se te asignará un vehículo disponible</li>
            <li>• El despachador realizará el checkout antes de tu salida</li>
            <li>• Al regresar, el despachador realizará el checkin del vehículo</li>
            <li>• Podrás calificar el servicio al finalizar tu viaje</li>
        </ul>
    </div>
</div>

<script>
    // Validar que la fecha de regreso sea posterior a la de salida
    document.getElementById('departure_date').addEventListener('change', function() {
        const departureDate = new Date(this.value);
        const returnDateInput = document.getElementById('return_date');
        
        if (returnDateInput.value) {
            const returnDate = new Date(returnDateInput.value);
            if (returnDate <= departureDate) {
                alert('La fecha de regreso debe ser posterior a la fecha de salida');
                returnDateInput.value = '';
            }
        }
    });

    document.getElementById('return_date').addEventListener('change', function() {
        const returnDate = new Date(this.value);
        const departureDateInput = document.getElementById('departure_date');
        
        if (departureDateInput.value) {
            const departureDate = new Date(departureDateInput.value);
            if (returnDate <= departureDate) {
                alert('La fecha de regreso debe ser posterior a la fecha de salida');
                this.value = '';
            }
        }
    });
</script>
@endsection
