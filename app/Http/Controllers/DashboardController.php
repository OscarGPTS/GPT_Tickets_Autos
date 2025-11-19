<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Mostrar el dashboard según el rol del usuario
     */
    public function index(): View
    {
        $user = Auth::user();

        if ($user->isEncargado()) {
            return $this->encargadoDashboard();
        } elseif ($user->isDespachador()) {
            return $this->despachadorDashboard();
        } else {
            return $this->usuarioDashboard();
        }
    }

    /**
     * Dashboard para Encargado
     */
    private function encargadoDashboard(): View
    {
        $stats = [
            'pendientes' => Ticket::pending()->count(),
            'aprobados' => Ticket::approved()->count(),
            'en_curso' => Ticket::inProgress()->count(),
            'vehiculos_disponibles' => Vehicle::available()->count(),
            'vehiculos_en_uso' => Vehicle::inUse()->count(),
        ];

        $ticketsPendientes = Ticket::pending()
            ->with(['user', 'user.immediateBoss'])
            ->latest()
            ->take(10)
            ->get();

        return view('dashboard.encargado', compact('stats', 'ticketsPendientes'));
    }

    /**
     * Dashboard para Despachador
     */
    private function despachadorDashboard(): View
    {
        $user = Auth::user();

        $ticketsAsignados = Ticket::forDispatcher($user->id)
            ->whereIn('status', ['aprobado', 'en_curso'])
            ->with(['user', 'vehicle'])
            ->latest()
            ->get();

        $stats = [
            'pendientes_checkout' => $ticketsAsignados->where('status', 'aprobado')->count(),
            'en_curso' => $ticketsAsignados->where('status', 'en_curso')->count(),
        ];

        return view('dashboard.despachador', compact('stats', 'ticketsAsignados'));
    }

    /**
     * Dashboard para Usuario
     */
    private function usuarioDashboard(): View
    {
        $user = Auth::user();

        $misTickets = Ticket::forUser($user->id)
            ->with(['vehicle'])
            ->latest()
            ->take(10)
            ->get();

        $stats = [
            'total' => $misTickets->count(),
            'pendientes' => $misTickets->where('status', 'pendiente')->count(),
            'aprobados' => $misTickets->where('status', 'aprobado')->count(),
            'en_curso' => $misTickets->where('status', 'en_curso')->count(),
        ];

        return view('dashboard.usuario', compact('stats', 'misTickets'));
    }
}
