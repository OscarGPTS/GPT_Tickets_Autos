<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use App\Models\VehicleDocument;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class VehicleController extends Controller
{
    /**
     * Listar vehículos
     */
    public function index(Request $request): View
    {
        $query = Vehicle::query();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('vehicle_type')) {
            $query->where('vehicle_type', $request->vehicle_type);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('internal_code', 'like', "%{$search}%")
                  ->orWhere('brand', 'like', "%{$search}%")
                  ->orWhere('model', 'like', "%{$search}%")
                  ->orWhere('plates', 'like', "%{$search}%");
            });
        }

        $vehicles = $query->latest()->paginate(15);

        return view('vehicles.index', compact('vehicles'));
    }

    /**
     * Mostrar formulario de creación
     */
    public function create(): View
    {
        return view('vehicles.create');
    }

    /**
     * Guardar vehículo
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'internal_code' => 'required|string|unique:vehicles,internal_code',
            'brand' => 'required|string|max:255',
            'model' => 'required|string|max:255',
            'year' => 'required|integer|min:1900|max:' . (date('Y') + 1),
            'plates' => 'required|string|unique:vehicles,plates',
            'serial_number' => 'nullable|string|unique:vehicles,serial_number',
            'color' => 'nullable|string|max:50',
            'vehicle_type' => 'required|in:sedan,suv,pickup,van,camioneta',
            'capacity_passengers' => 'required|integer|min:1',
            'capacity_cargo' => 'nullable|numeric|min:0',
            'fuel_type' => 'required|in:gasolina,diesel,electrico,hibrido',
            'current_mileage' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $vehicle = Vehicle::create($validated);

        return redirect()->route('vehicles.show', $vehicle)
            ->with('success', 'Vehículo registrado exitosamente.');
    }

    /**
     * Mostrar detalle del vehículo
     */
    public function show(Vehicle $vehicle): View
    {
        $vehicle->load(['documents', 'tickets', 'maintenances']);

        return view('vehicles.show', compact('vehicle'));
    }

    /**
     * Mostrar formulario de edición
     */
    public function edit(Vehicle $vehicle): View
    {
        return view('vehicles.edit', compact('vehicle'));
    }

    /**
     * Actualizar vehículo
     */
    public function update(Request $request, Vehicle $vehicle): RedirectResponse
    {
        $validated = $request->validate([
            'internal_code' => 'required|string|unique:vehicles,internal_code,' . $vehicle->id,
            'brand' => 'required|string|max:255',
            'model' => 'required|string|max:255',
            'year' => 'required|integer|min:1900|max:' . (date('Y') + 1),
            'plates' => 'required|string|unique:vehicles,plates,' . $vehicle->id,
            'serial_number' => 'nullable|string|unique:vehicles,serial_number,' . $vehicle->id,
            'color' => 'nullable|string|max:50',
            'vehicle_type' => 'required|in:sedan,suv,pickup,van,camioneta',
            'capacity_passengers' => 'required|integer|min:1',
            'capacity_cargo' => 'nullable|numeric|min:0',
            'fuel_type' => 'required|in:gasolina,diesel,electrico,hibrido',
            'current_mileage' => 'required|numeric|min:0',
            'status' => 'required|in:disponible,en_uso,mantenimiento,fuera_servicio',
            'notes' => 'nullable|string',
        ]);

        $vehicle->update($validated);

        return redirect()->route('vehicles.show', $vehicle)
            ->with('success', 'Vehículo actualizado exitosamente.');
    }

    /**
     * Eliminar vehículo
     */
    public function destroy(Vehicle $vehicle): RedirectResponse
    {
        $vehicle->delete();

        return redirect()->route('vehicles.index')
            ->with('success', 'Vehículo eliminado exitosamente.');
    }
}
