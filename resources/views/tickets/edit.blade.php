@extends('layouts.app')

@section('title', 'Editar Solicitud #' . $ticket->id)

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('tickets.show', $ticket) }}" class="text-blue-600 hover:text-blue-800 font-medium">
            <i class="fas fa-arrow-left mr-2"></i>Volver a Detalle
        </a>
    </div>

    <div class="bg-white rounded-lg shadow-md p-6">
        <h1 class="text-3xl font-bold text-gray-800 mb-6">Editar Solicitud #{{ $ticket->id }}</h1>

        @if($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form action="{{ route('tickets.update', $ticket) }}" method="POST">
            @csrf
            @method('PUT')

            <!-- Destino -->
            <div class="mb-4">
                <label for="destination" class="block text-sm font-medium text-gray-700 mb-2">
                    Destino <span class="text-red-500">*</span>
                </label>
                <input type="text" 
                       id="destination" 
                       name="destination" 
                       value="{{ old('destination', $ticket->destination) }}"
                       class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('destination') border-red-500 @enderror"
                       required>
                @error('destination')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Propósito -->
            <div class="mb-4">
                <label for="purpose" class="block text-sm font-medium text-gray-700 mb-2">
                    Propósito del Viaje <span class="text-red-500">*</span>
                </label>
                <textarea id="purpose" 
                          name="purpose" 
                          rows="3"
                          class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('purpose') border-red-500 @enderror"
                          required>{{ old('purpose', $ticket->purpose) }}</textarea>
                @error('purpose')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Fechas -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label for="requested_date" class="block text-sm font-medium text-gray-700 mb-2">
                        Fecha de Salida <span class="text-red-500">*</span>
                    </label>
                    <input type="date" 
                           id="requested_date" 
                           name="requested_date" 
                           value="{{ old('requested_date', \Carbon\Carbon::parse($ticket->requested_date)->format('Y-m-d')) }}"
                           class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('requested_date') border-red-500 @enderror"
                           required>
                    @error('requested_date')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="requested_time_start" class="block text-sm font-medium text-gray-700 mb-2">
                        Hora de Salida <span class="text-red-500">*</span>
                    </label>
                    <input type="time" 
                           id="requested_time_start" 
                           name="requested_time_start" 
                           value="{{ old('requested_time_start', $ticket->requested_time_start) }}"
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
                       value="{{ old('requested_time_end', $ticket->requested_time_end) }}"
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
                       value="{{ old('passenger_count', $ticket->passenger_count) }}"
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
                          class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('additional_notes', $ticket->additional_notes) }}</textarea>
            </div>

            <!-- Botones -->
            <div class="flex justify-end gap-4">
                <a href="{{ route('tickets.show', $ticket) }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold py-2 px-6 rounded-lg transition duration-200">
                    Cancelar
                </a>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-6 rounded-lg transition duration-200">
                    <i class="fas fa-save mr-2"></i>Guardar Cambios
                </button>
            </div>
        </form>
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
