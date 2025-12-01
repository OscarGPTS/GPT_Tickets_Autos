<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use App\Models\VehicleDocument;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class VehicleDocumentController extends Controller
{
    /**
     * Listar documentos de un vehículo
     */
    public function index(Vehicle $vehicle): View
    {
        $documents = $vehicle->documents()->latest()->paginate(15);
        
        return view('vehicle-documents.index', compact('vehicle', 'documents'));
    }

    /**
     * Mostrar formulario de creación
     */
    public function create(Vehicle $vehicle): View
    {
        return view('vehicle-documents.create', compact('vehicle'));
    }

    /**
     * Guardar documento
     */
    public function store(Request $request, Vehicle $vehicle): RedirectResponse
    {
        $validated = $request->validate([
            'document_type' => 'required|in:seguro,tarjeta_circulacion,verificacion,tenencia,otro',
            'document_number' => 'nullable|string|max:255',
            'issue_date' => 'required|date',
            'expiry_date' => 'nullable|date|after:issue_date',
            'issuer' => 'nullable|string|max:255',
            'amount' => 'nullable|numeric|min:0',
            'status' => 'required|in:vigente,vencido,pendiente',
            'document_path' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'notes' => 'nullable|string',
        ]);

        // Handle file upload
        if ($request->hasFile('document_path')) {
            $file = $request->file('document_path');
            $filename = 'vehicle_doc_' . $vehicle->id . '_' . time() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('vehicle_documents', $filename, 'public');
            $validated['document_path'] = $path;
        }

        $validated['vehicle_id'] = $vehicle->id;
        
        VehicleDocument::create($validated);

        return redirect()->route('vehicle-documents.index', $vehicle)
            ->with('success', 'Documento registrado exitosamente.');
    }

    /**
     * Mostrar detalle del documento
     */
    public function show(Vehicle $vehicle, VehicleDocument $document): View
    {
        if ($document->vehicle_id !== $vehicle->id) {
            abort(404);
        }
        
        return view('vehicle-documents.show', compact('vehicle', 'document'));
    }

    /**
     * Mostrar formulario de edición
     */
    public function edit(Vehicle $vehicle, VehicleDocument $document): View
    {
        if ($document->vehicle_id !== $vehicle->id) {
            abort(404);
        }
        
        return view('vehicle-documents.edit', compact('vehicle', 'document'));
    }

    /**
     * Actualizar documento
     */
    public function update(Request $request, Vehicle $vehicle, VehicleDocument $document): RedirectResponse
    {
        if ($document->vehicle_id !== $vehicle->id) {
            abort(404);
        }

        $validated = $request->validate([
            'document_type' => 'required|in:seguro,tarjeta_circulacion,verificacion,tenencia,otro',
            'document_number' => 'nullable|string|max:255',
            'issue_date' => 'required|date',
            'expiry_date' => 'nullable|date|after:issue_date',
            'issuer' => 'nullable|string|max:255',
            'amount' => 'nullable|numeric|min:0',
            'status' => 'required|in:vigente,vencido,pendiente',
            'document_path' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'notes' => 'nullable|string',
        ]);

        // Handle file upload
        if ($request->hasFile('document_path')) {
            // Delete old file
            if ($document->document_path) {
                Storage::disk('public')->delete($document->document_path);
            }
            
            $file = $request->file('document_path');
            $filename = 'vehicle_doc_' . $vehicle->id . '_' . time() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('vehicle_documents', $filename, 'public');
            $validated['document_path'] = $path;
        }

        $document->update($validated);

        return redirect()->route('vehicle-documents.show', [$vehicle, $document])
            ->with('success', 'Documento actualizado exitosamente.');
    }

    /**
     * Eliminar documento
     */
    public function destroy(Vehicle $vehicle, VehicleDocument $document): RedirectResponse
    {
        if ($document->vehicle_id !== $vehicle->id) {
            abort(404);
        }

        // Delete file
        if ($document->document_path) {
            Storage::disk('public')->delete($document->document_path);
        }

        $document->delete();

        return redirect()->route('vehicle-documents.index', $vehicle)
            ->with('success', 'Documento eliminado exitosamente.');
    }
}
