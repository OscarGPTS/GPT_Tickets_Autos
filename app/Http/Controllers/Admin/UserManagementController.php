<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use App\Services\RHUserService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class UserManagementController extends Controller
{
    protected RHUserService $rhUserService;

    public function __construct(RHUserService $rhUserService)
    {
        $this->rhUserService = $rhUserService;
    }

    /**
     * Listar usuarios registrados
     */
    public function index(): View
    {
        $users = User::with('roles')
            ->orderBy('name')
            ->paginate(20);

        return view('admin.users.index', compact('users'));
    }

    /**
     * Mostrar formulario de creación de usuario
     */
    public function create(): View
    {
        // Obtener usuarios del RH API
        $rhUsers = $this->rhUserService->getActiveUsers();
        $roles = Role::all();

        // Obtener IDs de usuarios ya registrados
        $registeredUserIds = User::pluck('rh_user_id')->toArray();

        // Filtrar solo usuarios no registrados
        $availableRHUsers = $rhUsers->filter(function ($user) use ($registeredUserIds) {
            return !in_array($user['id'] ?? null, $registeredUserIds);
        })->values();

        return view('admin.users.create', compact('availableRHUsers', 'roles'));
    }

    /**
     * Guardar nuevo usuario
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'rh_user_id' => 'required|integer|unique:users,rh_user_id',
            'email' => 'required|email|unique:users,email',
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'department' => 'nullable|string|max:255',
            'avatar' => 'nullable|url',
            'roles' => 'required|array|min:1',
            'roles.*' => 'exists:roles,id',
        ]);

        // Crear usuario
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'department' => $validated['department'],
            'avatar' => $validated['avatar'],
            'rh_user_id' => $validated['rh_user_id'],
            'is_active' => true,
        ]);

        // Asignar roles
        $user->roles()->attach($validated['roles']);

        return redirect()->route('admin.users.index')
            ->with('success', 'Usuario creado exitosamente.');
    }

    /**
     * Mostrar formulario de edición
     */
    public function edit(User $user): View
    {
        $roles = Role::all();
        $userRoles = $user->roles->pluck('id')->toArray();

        return view('admin.users.edit', compact('user', 'roles', 'userRoles'));
    }

    /**
     * Actualizar usuario
     */
    public function update(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'department' => 'nullable|string|max:255',
            'is_active' => 'required|boolean',
            'roles' => 'required|array|min:1',
            'roles.*' => 'exists:roles,id',
        ]);

        $user->update($validated);
        $user->roles()->sync($validated['roles']);

        return redirect()->route('admin.users.index')
            ->with('success', 'Usuario actualizado exitosamente.');
    }

    /**
     * Eliminar usuario
     */
    public function destroy(User $user): RedirectResponse
    {
        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'Usuario eliminado exitosamente.');
    }

    /**
     * Obtener datos de usuario del RH (AJAX)
     */
    public function getRHUserData(int $rhUserId)
    {
        $rhUser = $this->rhUserService->getUserById($rhUserId);

        if (!$rhUser) {
            return response()->json(['error' => 'Usuario no encontrado'], 404);
        }

        $formattedData = $this->rhUserService->formatUserData($rhUser);

        return response()->json([
            'success' => true,
            'data' => $formattedData,
            'rh_user' => $rhUser
        ]);
    }
}
