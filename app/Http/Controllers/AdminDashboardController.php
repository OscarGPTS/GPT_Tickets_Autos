<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    /**
     * Mostrar dashboard de administración con estadísticas
     */
    public function index(Request $request): View
    {
        // Obtener filtros de fecha
        $startDate = $request->input('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', now()->endOfMonth()->toDateString());
        $period = $request->input('period', 'month'); // day, week, month, year

        // Ajustar fechas según el periodo
        $startDate = $this->adjustStartDate($period, $startDate);
        $endDate = $this->adjustEndDate($period, $endDate);

        // Estadísticas generales
        $stats = $this->getGeneralStats($startDate, $endDate);

        // Estadísticas por estado
        $ticketsByStatus = $this->getTicketsByStatus($startDate, $endDate);

        // Tiempo de respuesta promedio entre áreas
        $responseTime = $this->getAverageResponseTime($startDate, $endDate);

        // Vehículos más utilizados
        $topVehicles = $this->getTopVehicles($startDate, $endDate);

        // Usuarios más activos
        $topUsers = $this->getTopUsers($startDate, $endDate);

        // Tickets por día/semana/mes
        $ticketsTimeline = $this->getTicketsTimeline($startDate, $endDate, $period);

        // Calificaciones promedio
        $ratings = $this->getAverageRatings($startDate, $endDate);

        return view('admin.dashboard', compact(
            'stats',
            'ticketsByStatus',
            'responseTime',
            'topVehicles',
            'topUsers',
            'ticketsTimeline',
            'ratings',
            'startDate',
            'endDate',
            'period'
        ));
    }

    /**
     * Exportar estadísticas a Excel
     */
    public function export(Request $request)
    {
        $startDate = $request->input('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', now()->endOfMonth()->toDateString());
        $period = $request->input('period', 'month');

        $startDate = $this->adjustStartDate($period, $startDate);
        $endDate = $this->adjustEndDate($period, $endDate);

        $tickets = Ticket::with(['user', 'vehicle', 'dispatcher', 'approver'])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get();

        $stats = $this->getGeneralStats($startDate, $endDate);
        $responseTime = $this->getAverageResponseTime($startDate, $endDate);

        // Crear archivo Excel con phpSpreadsheet
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Configurar estilos
        $headerStyle = [
            'font' => ['bold' => true, 'size' => 12, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => '4472C4']],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER]
        ];
        
        $titleStyle = [
            'font' => ['bold' => true, 'size' => 14],
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E7E6E6']]
        ];
        
        // ESTADÍSTICAS GENERALES
        $row = 1;
        $sheet->setCellValue('A' . $row, 'ESTADÍSTICAS GENERALES');
        $sheet->mergeCells('A' . $row . ':B' . $row);
        $sheet->getStyle('A' . $row)->applyFromArray($titleStyle);
        
        $row++;
        $sheet->setCellValue('A' . $row, 'Métrica');
        $sheet->setCellValue('B' . $row, 'Valor');
        $sheet->getStyle('A' . $row . ':B' . $row)->applyFromArray($headerStyle);
        
        $statsData = [
            ['Total de Solicitudes', $stats['totalTickets']],
            ['Pendientes', $stats['pendingTickets']],
            ['Aprobadas', $stats['approvedTickets']],
            ['Rechazadas', $stats['rejectedTickets']],
            ['En Uso', $stats['inUseTickets']],
            ['Completadas', $stats['completedTickets']],
            ['Total Vehículos', $stats['totalVehicles']],
            ['Vehículos Disponibles', $stats['availableVehicles']],
            ['Total Usuarios', $stats['totalUsers']],
        ];
        
        foreach ($statsData as $data) {
            $row++;
            $sheet->setCellValue('A' . $row, $data[0]);
            $sheet->setCellValue('B' . $row, $data[1]);
        }
        
        // TIEMPOS DE RESPUESTA
        $row += 2;
        $sheet->setCellValue('A' . $row, 'TIEMPOS DE RESPUESTA PROMEDIO');
        $sheet->mergeCells('A' . $row . ':B' . $row);
        $sheet->getStyle('A' . $row)->applyFromArray($titleStyle);
        
        $row++;
        $sheet->setCellValue('A' . $row, 'Métrica');
        $sheet->setCellValue('B' . $row, 'Horas');
        $sheet->getStyle('A' . $row . ':B' . $row)->applyFromArray($headerStyle);
        
        $row++;
        $sheet->setCellValue('A' . $row, 'Tiempo de Aprobación');
        $sheet->setCellValue('B' . $row, round($responseTime['approvalTime'], 2));
        
        $row++;
        $sheet->setCellValue('A' . $row, 'Tiempo Total de Proceso');
        $sheet->setCellValue('B' . $row, round($responseTime['completionTime'], 2));
        
        // DETALLE DE SOLICITUDES
        $row += 2;
        $sheet->setCellValue('A' . $row, 'DETALLE DE SOLICITUDES');
        $sheet->mergeCells('A' . $row . ':K' . $row);
        $sheet->getStyle('A' . $row)->applyFromArray($titleStyle);
        
        $row++;
        $headers = ['Folio', 'Usuario', 'Destino', 'Estado', 'Vehículo', 'Despachador', 'Fecha Solicitud', 'Fecha Aprobación', 'Fecha Completado', 'Calif. Servicio', 'Calif. Vehículo'];
        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . $row, $header);
            $col++;
        }
        $sheet->getStyle('A' . $row . ':K' . $row)->applyFromArray($headerStyle);
        
        foreach ($tickets as $ticket) {
            $row++;
            $sheet->setCellValue('A' . $row, $ticket->folio ?? 'N/A');
            $sheet->setCellValue('B' . $row, $ticket->user->name ?? 'N/A');
            $sheet->setCellValue('C' . $row, $ticket->destination);
            $sheet->setCellValue('D' . $row, ucfirst($ticket->status));
            $sheet->setCellValue('E' . $row, $ticket->vehicle ? $ticket->vehicle->brand . ' ' . $ticket->vehicle->model : 'N/A');
            $sheet->setCellValue('F' . $row, $ticket->dispatcher->name ?? 'N/A');
            $sheet->setCellValue('G' . $row, $ticket->created_at->format('Y-m-d H:i'));
            $sheet->setCellValue('H' . $row, $ticket->approved_at ? $ticket->approved_at->format('Y-m-d H:i') : 'N/A');
            $sheet->setCellValue('I' . $row, $ticket->completed_at ? $ticket->completed_at->format('Y-m-d H:i') : 'N/A');
            $sheet->setCellValue('J' . $row, $ticket->service_rating ?? 'N/A');
            $sheet->setCellValue('K' . $row, $ticket->vehicle_rating ?? 'N/A');
        }
        
        // Ajustar ancho de columnas
        foreach (range('A', 'K') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        
        // Generar archivo
        $fileName = 'estadisticas_' . $startDate . '_' . $endDate . '.xlsx';
        
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        
        // Guardar en memoria y enviar
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $fileName . '"');
        header('Cache-Control: max-age=0');
        
        $writer->save('php://output');
        exit;
    }

    /**
     * Obtener estadísticas generales
     */
    private function getGeneralStats($startDate, $endDate): array
    {
        $totalTickets = Ticket::whereBetween('created_at', [$startDate, $endDate])->count();
        
        return [
            'totalTickets' => $totalTickets,
            'pendingTickets' => Ticket::where('status', 'pendiente')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->count(),
            'approvedTickets' => Ticket::where('status', 'aprobado')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->count(),
            'rejectedTickets' => Ticket::where('status', 'rechazado')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->count(),
            'inUseTickets' => Ticket::where('status', 'en_uso')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->count(),
            'completedTickets' => Ticket::where('status', 'completado')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->count(),
            'totalVehicles' => Vehicle::count(),
            'availableVehicles' => Vehicle::where('status', 'disponible')->count(),
            'totalUsers' => User::count(),
        ];
    }

    /**
     * Obtener tickets por estado
     */
    private function getTicketsByStatus($startDate, $endDate): array
    {
        return Ticket::select('status', DB::raw('count(*) as total'))
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();
    }

    /**
     * Calcular tiempo de respuesta promedio
     */
    private function getAverageResponseTime($startDate, $endDate): array
    {
        // Tiempo promedio de aprobación (en horas)
        $approvalTime = Ticket::whereBetween('created_at', [$startDate, $endDate])
            ->whereNotNull('approved_at')
            ->selectRaw('AVG(TIMESTAMPDIFF(HOUR, created_at, approved_at)) as avg_hours')
            ->value('avg_hours') ?? 0;

        // Tiempo promedio hasta completar (en horas)
        $completionTime = Ticket::whereBetween('created_at', [$startDate, $endDate])
            ->whereNotNull('completed_at')
            ->selectRaw('AVG(TIMESTAMPDIFF(HOUR, created_at, completed_at)) as avg_hours')
            ->value('avg_hours') ?? 0;

        return [
            'approvalTime' => $approvalTime,
            'completionTime' => $completionTime,
        ];
    }

    /**
     * Obtener vehículos más utilizados
     */
    private function getTopVehicles($startDate, $endDate, $limit = 5)
    {
        return Ticket::select('vehicle_id', DB::raw('count(*) as total'))
            ->whereBetween('created_at', [$startDate, $endDate])
            ->whereNotNull('vehicle_id')
            ->groupBy('vehicle_id')
            ->orderByDesc('total')
            ->limit($limit)
            ->with('vehicle')
            ->get();
    }

    /**
     * Obtener usuarios más activos
     */
    private function getTopUsers($startDate, $endDate, $limit = 5)
    {
        return Ticket::select('user_id', DB::raw('count(*) as total'))
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('user_id')
            ->orderByDesc('total')
            ->limit($limit)
            ->with('user')
            ->get();
    }

    /**
     * Obtener timeline de tickets
     */
    private function getTicketsTimeline($startDate, $endDate, $period)
    {
        switch($period) {
            case 'day':
                $format = '%Y-%m-%d';
                break;
            case 'week':
                $format = '%Y-%u';
                break;
            case 'month':
                $format = '%Y-%m';
                break;
            case 'year':
                $format = '%Y';
                break;
            default:
                $format = '%Y-%m';
                break;
        }

        return Ticket::select(
                DB::raw("DATE_FORMAT(created_at, '$format') as period"),
                DB::raw('count(*) as total')
            )
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('period')
            ->orderBy('period')
            ->get();
    }

    /**
     * Obtener calificaciones promedio
     */
    private function getAverageRatings($startDate, $endDate): array
    {
        $ratings = Ticket::whereBetween('created_at', [$startDate, $endDate])
            ->whereNotNull('service_rating')
            ->selectRaw('AVG(service_rating) as avg_service, AVG(vehicle_rating) as avg_vehicle')
            ->first();

        return [
            'service' => round($ratings->avg_service ?? 0, 1),
            'vehicle' => round($ratings->avg_vehicle ?? 0, 1),
        ];
    }

    /**
     * Ajustar fecha de inicio según periodo
     */
    private function adjustStartDate($period, $date)
    {
        $carbon = Carbon::parse($date);
        
        switch($period) {
            case 'day':
                return $carbon->startOfDay()->toDateString();
            case 'week':
                return $carbon->startOfWeek()->toDateString();
            case 'month':
                return $carbon->startOfMonth()->toDateString();
            case 'year':
                return $carbon->startOfYear()->toDateString();
            default:
                return $date;
        }
    }

    /**
     * Ajustar fecha de fin según periodo
     */
    private function adjustEndDate($period, $date)
    {
        $carbon = Carbon::parse($date);
        
        switch($period) {
            case 'day':
                return $carbon->endOfDay()->toDateString();
            case 'week':
                return $carbon->endOfWeek()->toDateString();
            case 'month':
                return $carbon->endOfMonth()->toDateString();
            case 'year':
                return $carbon->endOfYear()->toDateString();
            default:
                return $date;
        }
    }
}
