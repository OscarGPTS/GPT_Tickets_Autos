@extends('layouts.guest')

@section('content')


<div class="flex justify-center">

    <div class="card bg-white rounded-xl shadow bg-opacity-95" style="width: 400px; height: 360px; margin-top: 10%;">

        <div class="card-header flex justify-center pt-6 pb-6 bg-stone-800">
            <h2 class="text-xl font-bold text-[#F9BE00] ">Requisiciones de Vehículos</h2>
        </div>

        <div class="flex justify-between items-center px-8 pt-4 pb-8">
            <h1 class="text-xl font-bold">¡Bienvenido!</h1>
            <img src="{{ asset('storage/img/logo.png') }}" alt="Logo" style="width: 80px;">
        </div>
             
        <div class="card-body px-8">

            <p>Para acceder, requerimos que te identifques con tu cuenta empresarial de Google.</p>
            <br>
            <a href="{{ route('login.redirect') }}" class="text-center border-solid border-2 border-stone-500 p-2 rounded-full flex">             
                <img src="{{ asset('/storage/img/google.png') }}" alt="Logo google" style="width: 30px;"> <p class="ml-10 mt-0.5">Iniciar sesión con Google </p>
            </a>

            <div class="flex justify-end mt-8">
                <button data-modal-target="popup-modal" data-modal-toggle="popup-modal"  class="underline">
                    ¿No cuentas con acceso?
                </button>
            </div>
            
            <div id="popup-modal" tabindex="-1" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
                <div class="relative p-4 w-full max-w-md max-h-full">
                    <div class="relative bg-white rounded-lg shadow-sm dark:bg-gray-700">
                        <button type="button" class="absolute top-3 end-2.5 text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white" data-modal-hide="popup-modal">
                            <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                            </svg>
                            <span class="sr-only">Close modal</span>
                        </button>
                        <div class="p-4 md:p-5 text-center">
                            <svg class="mx-auto mb-4 text-gray-400 w-12 h-12 dark:text-gray-200" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 11V6m0 8h.01M19 10a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
                            </svg>
                            <h3 class="mb-5 text-lg font-normal text-gray-500">Comunicate con el equipo de sistemas para ayudarte</h3>
                        </div>
                    </div>
                </div>
            </div>
            
        </div>
    </div>
</div>

@endsection
