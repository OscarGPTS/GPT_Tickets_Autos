@extends('layouts.app')

@section('title', 'Editar Usuario')

@section('content')
    <div class="max-w-2xl mx-auto">
        <!-- Header -->
        <div class="mb-8 flex items-center">
            <a href="{{ route('admin.users.index') }}"
                class="mr-4 p-2 rounded-full bg-white border border-gray-200 text-gray-500 hover:text-blue-600 hover:border-blue-300 transition-all duration-200 shadow-sm">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div>
                <h1 class="text-3xl font-bold text-gray-900 tracking-tight">Editar Usuario</h1>
                <p class="mt-2 text-gray-600">Actualiza la información del usuario y sus roles</p>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden p-8">
            <form action="{{ route('admin.users.update', $user) }}" method="POST">
                @csrf
                @method('PUT')

                <!-- Información del Usuario -->
                <div class="grid grid-cols-1 gap-6 mb-8">
                    <!-- Nombre Completo -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nombre Completo
                            <span class="text-red-500">*</span></label>
                        <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}"
                            class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm py-2.5 px-3"
                            required>
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email
                            <span class="text-red-500">*</span></label>
                        <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}"
                            class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm py-2.5 px-3"
                            required>
                        @error('email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Teléfono -->
                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Teléfono</label>
                        <input type="text" name="phone" id="phone" value="{{ old('phone', $user->phone) }}"
                            class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm py-2.5 px-3">
                        @error('phone')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Departamento -->
                    <div>
                        <label for="department" class="block text-sm font-medium text-gray-700 mb-1">Departamento</label>
                        <input type="text" name="department" id="department"
                            value="{{ old('department', $user->department) }}"
                            class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm py-2.5 px-3">
                        @error('department')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Estado -->
                    <div>
                        <label for="is_active" class="block text-sm font-medium text-gray-700 mb-3">Estado</label>
                        <div class="flex items-center gap-4">
                            <label class="flex items-center cursor-pointer">
                                <input type="radio" name="is_active" value="1"
                                    {{ old('is_active', $user->is_active) ? 'checked' : '' }}
                                    class="rounded-full border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <span class="ml-2 text-sm text-gray-700">Activo</span>
                            </label>
                            <label class="flex items-center cursor-pointer">
                                <input type="radio" name="is_active" value="0"
                                    {{ !old('is_active', $user->is_active) ? 'checked' : '' }}
                                    class="rounded-full border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <span class="ml-2 text-sm text-gray-700">Inactivo</span>
                            </label>
                        </div>
                        @error('is_active')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Asignar Roles -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-3">Roles <span
                                class="text-red-500">*</span></label>
                        <div class="space-y-3">
                            @foreach ($roles as $role)
                                <div class="flex items-center">
                                    <input type="checkbox" name="roles[]" id="role_{{ $role->id }}"
                                        value="{{ $role->id }}"
                                        {{ in_array($role->id, $userRoles) ? 'checked' : '' }}
                                        class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <label for="role_{{ $role->id }}" class="ml-2 text-sm text-gray-700">
                                        <span
                                            class="font-medium">{{ ucfirst($role->name) }}</span>
                                        <span class="text-gray-500 block text-xs mt-1">
                                            {{ match($role->name) {
                                                'encargado' => 'Acceso completo al panel de administración',
                                                'despachador' => 'Puede asignar y gestionar despachadores',
                                                'usuario' => 'Usuario regular del sistema',
                                                default => $role->description ?? ''
                                            } }}
                                        </span>
                                    </label>
                                </div>
                            @endforeach
                        </div>
                        @error('roles')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Botones -->
                <div class="flex gap-4">
                    <button type="submit"
                        class="flex-1 inline-flex justify-center items-center px-6 py-3 border border-transparent text-sm font-medium rounded-xl shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200">
                        <i class="fas fa-save mr-2"></i>Guardar Cambios
                    </button>
                    <a href="{{ route('admin.users.index') }}"
                        class="flex-1 inline-flex justify-center items-center px-6 py-3 border border-gray-300 text-sm font-medium rounded-xl shadow-sm text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200">
                        <i class="fas fa-times mr-2"></i>Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection
