@extends('layouts.app')

@section('title', 'Iniciar Sesión - GPT Services')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-blue-500 to-blue-700 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8 bg-white p-10 rounded-xl shadow-2xl">
        <div>
            <h1 class="text-center text-4xl font-bold text-gray-900">
                GPT Services
            </h1>
            <p class="mt-2 text-center text-sm text-gray-600">
                Sistema de Gestión Vehicular (SIGEV)
            </p>
        </div>
        
        <div class="mt-8 space-y-6">
            <div class="rounded-md shadow-sm">
                <a href="{{ route('login.redirect') }}" class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all">
                    <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12.545,10.239v3.821h5.445c-0.712,2.315-2.647,3.972-5.445,3.972c-3.332,0-6.033-2.701-6.033-6.032s2.701-6.032,6.033-6.032c1.498,0,2.866,0.549,3.921,1.453l2.814-2.814C17.503,2.988,15.139,2,12.545,2C7.021,2,2.543,6.477,2.543,12s4.478,10,10.002,10c8.396,0,10.249-7.85,9.426-11.748L12.545,10.239z"/>
                        </svg>
                    </span>
                    Iniciar Sesión con Google
                </a>
            </div>
            
            <div class="text-center text-xs text-gray-500">
                <p>Solo usuarios autorizados de GPT Services</p>
            </div>
        </div>
        
        <div class="mt-6 border-t border-gray-200 pt-6">
            <div class="text-sm text-gray-600">
                <p class="font-semibold mb-2">Usuarios de Prueba:</p>
                <ul class="space-y-1 text-xs">
                    <li>• analilia@gptservices.com (Encargada)</li>
                    <li>• josecarmen@gptservices.com (Encargado)</li>
                    <li>• despachador1@gptservices.com (Despachador)</li>
                    <li>• usuario1@gptservices.com (Usuario)</li>
                </ul>
                <p class="mt-2 text-xs text-gray-400">Password: password123</p>
            </div>
        </div>
    </div>
</div>
@endsection
