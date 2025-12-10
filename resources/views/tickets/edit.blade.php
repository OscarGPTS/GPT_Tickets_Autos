@extends('layouts.app')

@section('title', 'Editar Solicitud #' . $ticket->id)

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('tickets.show', $ticket) }}" class="text-blue-600 hover:text-blue-800 font-medium">
            <i class="fas fa-arrow-left mr-2"></i>Volver a Detalle
        </a>
    </div>

    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="bg-gradient-to-r from-blue-600 to-blue-800 px-6 py-4">
            <div class="flex justify-between items-start">
                <div>
                    <h1 class="text-2xl font-bold text-white">Requisición #{{ $ticket->folio }}</h1>
                </div>
                <div>
                    @php
                        $statusColors = [
                            'pendiente' => 'bg-amber-100 text-amber-800 border-amber-300',
                            'aprobado' => 'bg-blue-100 text-blue-800 border-blue-300',
                            'rechazado' => 'bg-red-100 text-red-800 border-red-300',
                            'en_uso' => 'bg-indigo-100 text-indigo-800 border-indigo-300',
                            'completado' => 'bg-green-100 text-green-800 border-green-300',
                        ];
                    @endphp
                    <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full border-2 {{ $statusColors[$ticket->status] ?? 'bg-gray-100 text-gray-800' }}">
                        {{ strtoupper($ticket->status) }}
                    </span>
                </div>
            </div>
        </div>

        @if($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form action="{{ route('tickets.update', $ticket) }}" method="POST" class="p-6">
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
                       class="w-full p-2 border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('destination') border-red-500 @enderror"
                       required>
                @error('destination')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>


            <div class="md:col-span-2 mb-4">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Cliente (Opcional)
                    </label>
                    <input type="text" name="cliente" value="{{ old('cliente', $ticket->cliente) }}" 
                           class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2" 
                           placeholder="Ingrese el cliente al que visitará">
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 w-full mb-4">
                <div class="w-full">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Fecha de salida <span class="text-red-500">*</span>
                    </label>
                    <input type="date" name="requested_date" value="{{ old('requested_date', now()->toDateString()) }}" 
                        class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2" 
                        required>
                </div>

                <div class="w-full">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Hora Estimada de Salida <span class="text-red-500">*</span>
                    </label>
                    <input type="time" name="requested_time_start" value="{{ old('requested_time_start', $ticket->requested_time_start) }}" 
                        class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2" 
                        required>
                </div>

                <div class="w-full">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Hora Estimadade Regreso 
                    </label>
                    <input type="time" name="requested_time_end" value="{{ old('requested_time_end', $ticket->requested_time_end) }}" 
                        class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2">
                </div>
            </div>
                
            <div class="md:col-span-2 mb-4">
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    Propósito del Viaje <span class="text-red-500">*</span>
                </label>
                <textarea name="purpose" rows="3" 
                            class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2" 
                            placeholder="Ingrese el propósito del viaje" required>{{ old('purpose' , $ticket->purpose) }}</textarea>
            </div>

            <div class="flex flex-col sm:flex-row gap-3 pt-6 text-center">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-6 rounded-lg transition duration-200">
                    Guardar cambios
                </button>
                <a href="{{ route('tickets.show', $ticket) }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold py-2 px-6 rounded-lg transition duration-200">
                    Cancelar
                </a>
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
