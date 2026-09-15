<?php

namespace App\Http\Controllers;

use App\Repositories\KpiRepository;
use App\Thrust\Metrics\NewTicketsByMonthMetric;
use App\Thrust\Metrics\NewTicketsMetric;
use App\Thrust\Metrics\RatingAverageMetric;
use App\Thrust\Metrics\SolvedMetric;
use App\Thrust\Metrics\TeamTicketsMetric;
use App\Thrust\Metrics\TicketsCountMetric;
use App\Thrust\Metrics\TicketTypeMetric;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReportsController extends Controller
{
    public function index(KpiRepository $repository)
    {
        $startDate = request('startDate') ?: Carbon::now()->startOfMonth();
        $endDate   = request('endDate') ?: Carbon::now()->endOfMonth();

        return view('reports.index', ['repository' => $repository->forDates($startDate, $endDate)]);
    }

    public function analytics(Request $request)
    {
        // 1. Manejo de Fechas (Filtros)
        $startDate = $request->input('start_date') ? Carbon::parse($request->input('start_date'))->startOfDay() : Carbon::now()->startOfMonth();
        $endDate = $request->input('end_date') ? Carbon::parse($request->input('end_date'))->endOfDay() : Carbon::now()->endOfMonth();
        
        $periodLabel = "Del " . $startDate->format('d/m/Y') . " al " . $endDate->format('d/m/Y');
        
        // Filtro por Agente
        $selectedAgent = $request->input('agent_id');

        // Exportación a Excel / CSV
        if ($request->input('export') === 'csv') {
            return $this->exportCsv($startDate, $endDate, $selectedAgent);
        }

        // Lista de agentes para el combo de filtrado
        $allAgents = \App\Models\User::where('admin', 1)->get();

        // 2. Rendimiento por Agente (Tickets Resueltos en el periodo)
        $agentsDataQuery = \App\Models\User::where('admin', 1)->withCount(['assignedTickets as resolved_count' => function ($query) use ($startDate, $endDate, $selectedAgent) {
            $query->whereIn('status', [4, 5])
                  ->whereBetween('updated_at', [$startDate, $endDate]);
                  
            if ($selectedAgent) {
                $query->where('agent_id', $selectedAgent);
            }
        }]);
        
        if ($selectedAgent) {
            $agentsDataQuery->where('id', $selectedAgent);
        }
        
        $agentsData = $agentsDataQuery->get();

        $agentNames = $agentsData->pluck('name')->toJson();
        $agentScores = $agentsData->pluck('resolved_count')->toJson();

        // 3. Distribución por Categoría en el periodo
        $categoriesQuery = \Illuminate\Support\Facades\DB::table('tickets')
            ->whereBetween('created_at', [$startDate, $endDate]);
            
        if ($selectedAgent) {
            $categoriesQuery->where('agent_id', $selectedAgent);
        }
            
        $categoriesData = $categoriesQuery->select('categoria', \Illuminate\Support\Facades\DB::raw('count(*) as total'))
            ->groupBy('categoria')
            ->get();

        $catNames = $categoriesData->pluck('categoria')->map(fn($c) => $c ?: 'General')->toJson();
        $catCounts = $categoriesData->pluck('total')->toJson();
        
        // 4. Distribución por Prioridad en el periodo
        $priorityQuery = \Illuminate\Support\Facades\DB::table('tickets')
            ->whereBetween('created_at', [$startDate, $endDate]);
            
        if ($selectedAgent) {
            $priorityQuery->where('agent_id', $selectedAgent);
        }
            
        $priorityData = $priorityQuery->select('priority', \Illuminate\Support\Facades\DB::raw('count(*) as total'))
            ->groupBy('priority')
            ->get();
            
        // Map priorities: 1=Baja, 2=Normal, 3=Alta
        $priorityMap = [1 => 'Baja', 2 => 'Normal', 3 => 'Alta / Urgente'];
        $priNames = $priorityData->pluck('priority')->map(fn($p) => $priorityMap[$p] ?? 'Desconocida')->toJson();
        $priCounts = $priorityData->pluck('total')->toJson();

        // 5. Inventario de Laptops (Independiente de la fecha, es actual)
        $devices = \App\Models\Device::where('type', 'shared')->get();
        $invAvailable = $devices->where('status', 'available')->count();
        $invLoaned = $devices->where('status', 'loaned')->count();

        // 6. Métrica Rápida: Tickets en el periodo y MTTR
        $openQuery = \App\Models\Ticket::whereIn('status', [1, 2, 3])->whereBetween('created_at', [$startDate, $endDate]);
        $closedQuery = \App\Models\Ticket::whereIn('status', [4, 5])->whereBetween('updated_at', [$startDate, $endDate]);
        
        if ($selectedAgent) {
            $openQuery->where('agent_id', $selectedAgent);
            $closedQuery->where('agent_id', $selectedAgent);
        }
        
        $totalOpen = $openQuery->count();
        $totalClosed = $closedQuery->count();
        
        // Calculo de MTTR (Mean Time to Resolve) en horas
        $resolvedTickets = $closedQuery->get();
        $mttrHours = 0;
        if ($resolvedTickets->count() > 0) {
            $totalHours = 0;
            foreach ($resolvedTickets as $rt) {
                $totalHours += $rt->created_at->diffInHours($rt->updated_at);
            }
            $mttrHours = round($totalHours / $resolvedTickets->count(), 1);
        }

        return view('reports.analytics', compact(
            'periodLabel', 'startDate', 'endDate',
            'agentNames', 'agentScores',
            'catNames', 'catCounts',
            'priNames', 'priCounts',
            'invAvailable', 'invLoaned',
            'totalOpen', 'totalClosed',
            'allAgents', 'selectedAgent', 'mttrHours'
        ));
    }

    private function exportCsv($startDate, $endDate, $selectedAgent)
    {
        $query = \App\Models\Ticket::with('user', 'agent')
                    ->whereBetween('created_at', [$startDate, $endDate]);
                    
        if ($selectedAgent) {
            $query->where('agent_id', $selectedAgent);
        }
        
        $tickets = $query->get();
        
        $filename = "reporte_tickets_" . date('Y-m-d_H-i-s') . ".csv";
        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];
        
        $callback = function() use($tickets) {
            $file = fopen('php://output', 'w');
            
            // BOM para Excel (UTF-8)
            fputs($file, $bom = (chr(0xEF) . chr(0xBB) . chr(0xBF)));
            
            fputcsv($file, ['ID', 'Creado El', 'Actualizado El', 'Solicitante', 'Agente', 'Categoría', 'Prioridad', 'Estado']);
            
            foreach ($tickets as $t) {
                $priority = match($t->priority) { 1 => 'Baja', 2 => 'Normal', 3 => 'Alta', default => 'N/A' };
                $status = $t->statusName();
                
                fputcsv($file, [
                    'TK-' . $t->id,
                    $t->created_at->format('Y-m-d H:i:s'),
                    $t->updated_at->format('Y-m-d H:i:s'),
                    $t->user->name ?? 'N/A',
                    $t->agent->name ?? 'N/A',
                    $t->categoria ?? 'General',
                    $priority,
                    $status
                ]);
            }
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
}
