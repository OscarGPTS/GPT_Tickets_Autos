@extends('layouts.app')

@section('title', 'Calificar Servicio - Solicitud #' . ($ticket->folio ?? 'Calificar Servicio'))

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('tickets.show', $ticket) }}" class="text-blue-600 hover:text-blue-800 font-medium">
            <i class="fas fa-arrow-left mr-2"></i>Volver a Detalle
        </a>
    </div>

    <div class="bg-white rounded-lg shadow-md p-6">
        <h1 class="text-3xl font-bold text-gray-800 mb-2">Calificar Servicio</h1>
        <p class="text-gray-600 mb-6">Solicitud #{{ $ticket->folio }}</p>

        @if($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <!-- Resumen del Viaje -->
        <div class="bg-gray-50 rounded-lg p-4 mb-6">
            <h3 class="font-semibold text-gray-700 mb-2">Resumen del Viaje</h3>
            <div class="text-sm text-gray-600 space-y-1">
                <p><strong>Destino:</strong> {{ $ticket->destination }}</p>
                @if($ticket->vehicle)
                <p><strong>Vehículo:</strong> {{ $ticket->vehicle->brand }} {{ $ticket->vehicle->model }} ({{ $ticket->vehicle->license_plate }})</p>
                @endif
                @if($ticket->departure_date)
                <p><strong>Salida:</strong> {{ \Carbon\Carbon::parse($ticket->departure_date)->format('d/m/Y H:i') }}</p>
                @endif
                @if($ticket->return_date)
                <p><strong>Regreso:</strong> {{ \Carbon\Carbon::parse($ticket->return_date)->format('d/m/Y H:i') }}</p>
                @endif
            </div>
        </div>

        <form action="{{ route('tickets.rate.store', $ticket) }}" method="POST">
            @csrf

            <!-- Calificación con Estrellas -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-3">
                    Calificación del Servicio <span class="text-red-500">*</span>
                </label>
                <div class="flex items-center gap-2">
                    <div id="star-rating" class="flex text-4xl cursor-pointer">
                        <i class="far fa-star star" data-rating="1"></i>
                        <i class="far fa-star star" data-rating="2"></i>
                        <i class="far fa-star star" data-rating="3"></i>
                        <i class="far fa-star star" data-rating="4"></i>
                        <i class="far fa-star star" data-rating="5"></i>
                    </div>
                    <span id="rating-text" class="text-gray-600 ml-2"></span>
                </div>
                <input type="hidden" name="service_rating" id="rating-input" value="{{ old('service_rating', 0) }}" required>
                @error('service_rating')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Comentarios -->
            <div class="mb-6">
                <label for="rating_comments" class="block text-sm font-medium text-gray-700 mb-2">
                    Comentarios sobre el Servicio
                </label>
                <textarea id="rating_comments" 
                          name="rating_comments" 
                          rows="5"
                          class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                          placeholder="Cuéntanos sobre tu experiencia con el servicio...">{{ old('rating_comments') }}</textarea>
                <p class="mt-1 text-sm text-gray-500">Comparte tu opinión sobre el vehículo, el proceso de checkout/checkin, o cualquier sugerencia de mejora.</p>
            </div>

            <!-- Botones -->
            <div class="flex justify-end gap-4">
                <a href="{{ route('tickets.show', $ticket) }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold py-2 px-6 rounded-lg transition duration-200">
                    Cancelar
                </a>
                <button type="submit" id="submit-btn" class="bg-yellow-500 hover:bg-yellow-600 text-white font-semibold py-2 px-6 rounded-lg transition duration-200" disabled>
                    <i class="fas fa-star mr-2"></i>Enviar Calificación
                </button>
            </div>
        </form>
    </div>

    <!-- Información Adicional -->
    <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
        <h3 class="text-sm font-semibold text-blue-800 mb-2">
            <i class="fas fa-info-circle mr-2"></i>Tu Opinión es Importante
        </h3>
        <p class="text-sm text-blue-700">
            Tu calificación nos ayuda a mejorar continuamente el servicio de vehículos de GPT Services. 
            Todas las opiniones son consideradas para optimizar procesos y garantizar la mejor experiencia para todos los usuarios.
        </p>
    </div>
</div>

<style>
    .star {
        transition: color 0.2s;
    }
    .star:hover,
    .star.active {
        color: #fbbf24;
    }
    .star.fas {
        color: #fbbf24;
    }
</style>

<script>
    const stars = document.querySelectorAll('.star');
    const ratingInput = document.getElementById('rating-input');
    const ratingText = document.getElementById('rating-text');
    const submitBtn = document.getElementById('submit-btn');
    let currentRating = 0;

    const ratingLabels = {
        1: 'Muy Insatisfecho',
        2: 'Insatisfecho',
        3: 'Regular',
        4: 'Satisfecho',
        5: 'Muy Satisfecho'
    };

    stars.forEach(star => {
        star.addEventListener('mouseenter', function() {
            const rating = parseInt(this.dataset.rating);
            highlightStars(rating);
        });

        star.addEventListener('click', function() {
            currentRating = parseInt(this.dataset.rating);
            ratingInput.value = currentRating;
            ratingText.textContent = ratingLabels[currentRating];
            submitBtn.disabled = false;
            submitBtn.classList.remove('opacity-50', 'cursor-not-allowed');
        });
    });

    document.getElementById('star-rating').addEventListener('mouseleave', function() {
        highlightStars(currentRating);
    });

    function highlightStars(rating) {
        stars.forEach(star => {
            const starRating = parseInt(star.dataset.rating);
            if (starRating <= rating) {
                star.classList.remove('far');
                star.classList.add('fas');
            } else {
                star.classList.remove('fas');
                star.classList.add('far');
            }
        });
    }

    // Inicializar si hay un valor previo
    const oldRating = parseInt(ratingInput.value);
    if (oldRating > 0) {
        currentRating = oldRating;
        highlightStars(currentRating);
        ratingText.textContent = ratingLabels[currentRating];
        submitBtn.disabled = false;
        submitBtn.classList.remove('opacity-50', 'cursor-not-allowed');
    } else {
        submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
    }
</script>
@endsection
