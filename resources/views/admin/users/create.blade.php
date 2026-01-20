@extends('layouts.app')

@section('title', 'Crear Nuevo Usuario')

@section('content')
    <div class="max-w-2xl mx-auto">
        <!-- Header -->
        <div class="mb-8 flex items-center">
            <a href="{{ route('admin.users.index') }}"
                class="mr-4 p-2 rounded-full bg-white border border-gray-200 text-gray-500 hover:text-blue-600 hover:border-blue-300 transition-all duration-200 shadow-sm">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div>
                <h1 class="text-3xl font-bold text-gray-900 tracking-tight">Crear Nuevo Usuario</h1>
                <p class="mt-2 text-gray-600">Selecciona un usuario del RH para crear su cuenta</p>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden p-8">
            <form action="{{ route('admin.users.store') }}" method="POST">
                @csrf

                <!-- Selector de Usuario del RH -->
                <div class="mb-8">
                    <label for="rh_user_id" class="block text-sm font-medium text-gray-700 mb-3">
                        Seleccionar Usuario del RH <span class="text-red-500">*</span>
                    </label>
                    <select name="rh_user_id" id="rh_user_id"
                        class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm py-2.5 px-3"
                        required onchange="loadRHUserData()">
                        <option value="">-- Selecciona un usuario --</option>
                        @foreach ($availableRHUsers as $rhUser)
                            <option value="{{ $rhUser['id'] }}" data-email="{{ $rhUser['email'] }}">
                                {{ $rhUser['nombre_completo'] }} ({{ $rhUser['email'] }})
                            </option>
                        @endforeach
                    </select>
                    @error('rh_user_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror

                    @if ($availableRHUsers->isEmpty())
                        <div class="mt-4 p-4 bg-amber-50 border border-amber-200 rounded-xl">
                            <p class="text-sm text-amber-800">
                                <i class="fas fa-info-circle mr-2"></i>No hay usuarios disponibles del RH. Todos los
                                usuarios del RH ya están registrados.
                            </p>
                        </div>
                    @endif
                </div>

                <!-- Formulario auto-llenado -->
                <div class="grid grid-cols-1 gap-6 mb-8">
                    <!-- Nombre Completo -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nombre Completo
                            <span class="text-red-500">*</span></label>
                        <input type="text" name="name" id="name" value="{{ old('name') }}"
                            class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm py-2.5 px-3"
                            placeholder="Se llenará automáticamente" required readonly>
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email
                            <span class="text-red-500">*</span></label>
                        <input type="email" name="email" id="email" value="{{ old('email') }}"
                            class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm py-2.5 px-3"
                            placeholder="Se llenará automáticamente" required readonly>
                        @error('email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Teléfono -->
                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Teléfono</label>
                        <input type="text" name="phone" id="phone" value="{{ old('phone') }}"
                            class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm py-2.5 px-3"
                            placeholder="Se llenará automáticamente" readonly>
                        @error('phone')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Departamento -->
                    <div>
                        <label for="department" class="block text-sm font-medium text-gray-700 mb-1">Departamento</label>
                        <input type="text" name="department" id="department" value="{{ old('department') }}"
                            class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm py-2.5 px-3"
                            placeholder="Se llenará automáticamente" readonly>
                        @error('department')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Avatar (oculto) -->
                    <input type="hidden" name="avatar" id="avatar">

                    <!-- Asignar Roles -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-3">Roles <span
                                class="text-red-500">*</span></label>
                        <div class="space-y-3">
                            @foreach ($roles as $role)
                                <div class="flex items-center">
                                    <input type="checkbox" name="roles[]" id="role_{{ $role->id }}"
                                        value="{{ $role->id }}"
                                        {{ in_array($role->id, old('roles', [])) ? 'checked' : '' }}
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
                        <i class="fas fa-save mr-2"></i>Crear Usuario
                    </button>
                    <a href="{{ route('admin.users.index') }}"
                        class="flex-1 inline-flex justify-center items-center px-6 py-3 border border-gray-300 text-sm font-medium rounded-xl shadow-sm text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200">
                        <i class="fas fa-times mr-2"></i>Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>

    <script>
        function loadRHUserData() {
            const select = document.getElementById('rh_user_id');
            const rhUserId = select.value;

            if (!rhUserId) {
                // Limpiar campos
                document.getElementById('name').value = '';
                document.getElementById('email').value = '';
                document.getElementById('phone').value = '';
                document.getElementById('department').value = '';
                document.getElementById('avatar').value = '';
                return;
            }

            // Cargar datos del API
            fetch(`{{ route('admin.users.rh-data', '') }}/${rhUserId}`)
                .then(response => {
                    if (!response.ok) throw new Error('Error al cargar datos');
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        const userData = data.data;
                        document.getElementById('name').value = userData.name || '';
                        document.getElementById('email').value = userData.email || '';
                        document.getElementById('phone').value = userData.phone || '';
                        document.getElementById('department').value = userData.department || '';
                        document.getElementById('avatar').value = userData.avatar || '';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error al cargar los datos del usuario');
                });
        }
    </script>
@endsection
