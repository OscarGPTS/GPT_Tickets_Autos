<?php

namespace App\Http\Controllers;

use App\Models\DriverLicense;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class DriverLicenseController extends Controller
{
    /**
     * Listar todas las licencias
     */
    public function index(Request $request): View
    {
        $this->authorize('viewAny', DriverLicense::class);

        $query = DriverLicense::with('user');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('license_type')) {
            $query->where('license_type', $request->license_type);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('license_number', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        $licenses = $query->latest()->paginate(15);

        return view('driver-licenses.index', compact('licenses'));
    }

    /**
     * Mostrar formulario de creación
     */
    public function create(): View
    {
        $this->authorize('create', DriverLicense::class);
        
        $users = User::orderBy('name')->get();
        
        return view('driver-licenses.create', compact('users'));
    }

    /**
     * Guardar licencia
     */
    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', DriverLicense::class);

        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'license_number' => 'required|string|unique:driver_licenses,license_number',
            'license_type' => 'required|in:A,B,C,D,E',
            'issue_date' => 'required|date',
            'expiry_date' => 'required|date|after:issue_date',
            'status' => 'required|in:vigente,vencida,suspendida',
            'issuing_authority' => 'nullable|string|max:255',
            'restrictions' => 'nullable|string',
            'document_path' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        // Handle file upload
        if ($request->hasFile('document_path')) {
            $file = $request->file('document_path');
            $filename = 'license_' . $validated['user_id'] . '_' . time() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('driver_licenses', $filename, 'public');
            $validated['document_path'] = $path;
        }

        $license = DriverLicense::create($validated);

        return redirect()->route('driver-licenses.show', $license)
            ->with('success', 'Licencia registrada exitosamente.');
    }

    /**
     * Mostrar detalle de la licencia
     */
    public function show(DriverLicense $license): View
    {
        $this->authorize('view', $license);
        
        $license->load(['user', 'tickets']);

        return view('driver-licenses.show', compact('license'));
    }

    /**
     * Mostrar formulario de edición
     */
    public function edit(DriverLicense $license): View
    {
        $this->authorize('update', $license);
        
        $users = User::orderBy('name')->get();
        
        return view('driver-licenses.edit', compact('license', 'users'));
    }

    /**
     * Actualizar licencia
     */
    public function update(Request $request, DriverLicense $license): RedirectResponse
    {
        $this->authorize('update', $license);

        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'license_number' => 'required|string|unique:driver_licenses,license_number,' . $license->id,
            'license_type' => 'required|in:A,B,C,D,E',
            'issue_date' => 'required|date',
            'expiry_date' => 'required|date|after:issue_date',
            'status' => 'required|in:vigente,vencida,suspendida',
            'issuing_authority' => 'nullable|string|max:255',
            'restrictions' => 'nullable|string',
            'document_path' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        // Handle file upload
        if ($request->hasFile('document_path')) {
            // Delete old file
            if ($license->document_path) {
                Storage::disk('public')->delete($license->document_path);
            }
            
            $file = $request->file('document_path');
            $filename = 'license_' . $validated['user_id'] . '_' . time() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('driver_licenses', $filename, 'public');
            $validated['document_path'] = $path;
        }

        $license->update($validated);

        return redirect()->route('driver-licenses.show', $license)
            ->with('success', 'Licencia actualizada exitosamente.');
    }

    /**
     * Eliminar licencia
     */
    public function destroy(DriverLicense $license): RedirectResponse
    {
        $this->authorize('delete', $license);

        // Delete file
        if ($license->document_path) {
            Storage::disk('public')->delete($license->document_path);
        }

        $license->delete();

        return redirect()->route('driver-licenses.index')
            ->with('success', 'Licencia eliminada exitosamente.');
    }
}
