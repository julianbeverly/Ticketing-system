<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ticket;
use App\Models\User;
use App\Models\Department;
use App\Models\Company;
use App\Models\IncidentCategory;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportController extends Controller
{
    private function getFilteredQuery(Request $request)
    {
        $query = Ticket::query();

        // Date filtering
        if ($request->filled('report_type')) {
            $type = $request->report_type;
            if ($type === 'daily' && $request->filled('daily_date')) {
                $query->whereDate('created_at', $request->daily_date);
            } elseif ($type === 'weekly' && $request->filled('weekly_date')) {
                $date = Carbon::parse($request->weekly_date)->startOfWeek();
                $query->whereBetween('created_at', [$date->copy()->startOfWeek(), $date->copy()->endOfWeek()]);
            } elseif ($type === 'monthly' && $request->filled('monthly_date')) {
                $date = Carbon::parse($request->monthly_date);
                $query->whereMonth('created_at', $date->month)
                      ->whereYear('created_at', $date->year);
            } elseif ($type === 'custom') {
                if ($request->filled('start_date')) {
                    $query->whereDate('created_at', '>=', $request->start_date);
                }
                if ($request->filled('end_date')) {
                    $query->whereDate('created_at', '<=', $request->end_date);
                }
            }
        }

        // Global Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('id', 'LIKE', "%{$search}%")
                  ->orWhere('title', 'LIKE', "%{$search}%")
                  ->orWhere('description', 'LIKE', "%{$search}%")
                  ->orWhereHas('user', function($uq) use ($search) {
                      $uq->where('name', 'LIKE', "%{$search}%");
                  })
                  ->orWhereHas('technician', function($tq) use ($search) {
                      $tq->where('name', 'LIKE', "%{$search}%");
                  });
            });
        }

        $activeTab = $request->input('active_tab', 'company');

        // Company and Department filtering
        if ($request->filled('company_id') || $request->filled('department_id')) {
            if ($activeTab === 'staff') {
                // Staff Tab: Filter by technician's company/department
                $query->whereHas('technician', function($q) use ($request) {
                    if ($request->filled('company_id')) {
                        $q->where('company_id', $request->company_id);
                    }
                    if ($request->filled('department_id')) {
                        $q->where('department_id', $request->department_id);
                    }
                });
            } elseif ($activeTab === 'company') {
                // Company/Department Tab: Filter by creator's (employee's) company/department
                $query->whereHas('user', function($q) use ($request) {
                    if ($request->filled('company_id')) {
                        $q->where('company_id', $request->company_id);
                    }
                    if ($request->filled('department_id')) {
                        $q->where('department_id', $request->department_id);
                    }
                });
            }
            // If active_tab === 'period', DO NOT apply company/department filters
        }

        // Technician filtering (only applies to Staff tab usually, but can be global)
        if ($request->filled('technician_id')) {
            $query->where('technician_id', $request->technician_id);
        }

        return $query;
    }

    /**
     * Helper: determine if a ticket is SLA-breached.
     */
    private function isSlaBreach(Ticket $ticket): bool
    {
        return $ticket->sla_breached;
    }

    /**
     * Helper: determine if an active (unresolved/unclosed) ticket is currently overdue.
     */
    private function isActiveOverdue(Ticket $ticket): bool
    {
        if (in_array($ticket->status, ['resolved', 'closed'])) return false;
        return $ticket->status === 'overdue' || ($ticket->due_at && now()->greaterThan(Carbon::parse($ticket->due_at)));
    }

    public function index(Request $request)
    {
        $activeTab = $request->input('active_tab', 'company');

        $companies   = Company::all();
        $departments = Department::all();
        
        $techniciansQuery = User::where('role', 'technician');
        if ($activeTab === 'staff') {
            if ($request->filled('company_id')) {
                $techniciansQuery->where('company_id', $request->company_id);
            }
            if ($request->filled('department_id')) {
                $techniciansQuery->where('department_id', $request->department_id);
            }
        }
        $technicians = $techniciansQuery->get();

        $baseQuery = $this->getFilteredQuery($request);

        // ── 1. Company/Department Tab Data ─────────────────────────────────────
        $totalTickets      = (clone $baseQuery)->count();
        $openTickets       = (clone $baseQuery)->where('status', '!=', 'closed')->count();
        $closedTickets     = (clone $baseQuery)->where('status', 'closed')->count();
        $completedTickets  = (clone $baseQuery)->whereIn('status', ['resolved', 'closed'])->count();

        // Avg Resolution in Days
        $resolvedTickets    = (clone $baseQuery)->whereNotNull('resolved_at')->get();
        $totalDays          = 0;
        foreach ($resolvedTickets as $ticket) {
            $totalDays += ($ticket->created_at->diffInHours($ticket->resolved_at) / 24);
        }
        $avgResolutionDays = $resolvedTickets->count() > 0
            ? round($totalDays / $resolvedTickets->count(), 1)
            : 0;

        // SLA Compliance (global) — based on completed tickets (resolved + closed)
        $completedForSla  = (clone $baseQuery)->whereIn('status', ['resolved', 'closed'])->count();
        $slaMetForKpi     = (clone $baseQuery)->whereIn('status', ['resolved', 'closed'])->where('sla_breached', false)->count();
        $slaCompliance    = $completedForSla > 0 ? round(($slaMetForKpi / $completedForSla) * 100) : null;

        // Donut Data (Company tab — all statuses)
        $statusCounts = (clone $baseQuery)->select('status', DB::raw('count(*) as count'))->groupBy('status')->get()->pluck('count', 'status')->toArray();
        $donutData = [
            ['name' => 'Open',        'value' => $statusCounts['open'] ?? 0,        'color' => '#3B82F6'],
            ['name' => 'Assigned',    'value' => $statusCounts['assigned'] ?? 0,    'color' => '#8B5CF6'],
            ['name' => 'In Progress', 'value' => $statusCounts['in_progress'] ?? 0, 'color' => '#F59E0B'],
            ['name' => 'Resolved',    'value' => $statusCounts['resolved'] ?? 0,    'color' => '#10B981'],
            ['name' => 'Closed',      'value' => $statusCounts['closed'] ?? 0,      'color' => '#64748B'],
        ];
        $donutData = array_values(array_filter($donutData, fn($item) => $item['value'] > 0));

        // Dept Data
        $allTickets = (clone $baseQuery)->with('user.department')->get();

        $deptCounts = [];
        foreach ($allTickets as $ticket) {
            if ($ticket->user && $ticket->user->department) {
                $deptName = $ticket->user->department->name;
                if (!isset($deptCounts[$deptName])) {
                    $deptCounts[$deptName] = ['total' => 0, 'closed' => 0, 'open' => 0, 'overdue' => 0, 'res_days' => 0, 'res_count' => 0];
                }
                $deptCounts[$deptName]['total']++;
                if (in_array($ticket->status, ['resolved', 'closed'])) {
                    $deptCounts[$deptName]['closed']++;
                    if ($ticket->resolved_at) {
                        $deptCounts[$deptName]['res_days'] += ($ticket->created_at->diffInHours($ticket->resolved_at) / 24);
                        $deptCounts[$deptName]['res_count']++;
                    }
                } elseif ($this->isActiveOverdue($ticket)) {
                    // Active ticket that has breached SLA
                    $deptCounts[$deptName]['overdue']++;
                } else {
                    $deptCounts[$deptName]['open']++;
                }
            }
        }

        $deptData  = [];
        $deptTable = [];
        foreach ($deptCounts as $dept => $data) {
            $deptData[] = ['dept' => $dept, 'tickets' => $data['total']];
            $avg = $data['res_count'] > 0 ? round($data['res_days'] / $data['res_count'], 1) . ' days' : 'N/A';
            $deptTable[] = [
                'dept'    => $dept,
                'total'   => $data['total'],
                'closed'  => $data['closed'],
                'open'    => $data['open'],
                'overdue' => $data['overdue'],
                'avg'     => $avg,
            ];
        }

        // Monthly Ticket Trend
        $monthlyTickets = (clone $baseQuery)->select(
                DB::raw('MONTH(created_at) as month_num'),
                DB::raw('count(*) as tickets')
            )
            ->whereYear('created_at', date('Y'))
            ->groupBy('month_num')
            ->orderBy('month_num')
            ->get();

        $monthNames  = [1=>'Jan', 2=>'Feb', 3=>'Mar', 4=>'Apr', 5=>'May', 6=>'Jun', 7=>'Jul', 8=>'Aug', 9=>'Sep', 10=>'Oct', 11=>'Nov', 12=>'Dec'];
        $monthlyTrend = [];
        for ($i = 1; $i <= 12; $i++) {
            $monthObj      = $monthlyTickets->firstWhere('month_num', $i);
            $monthlyTrend[] = [
                'month'   => $monthNames[$i],
                'tickets' => $monthObj ? $monthObj->tickets : 0,
            ];
        }

        // ── 2. Staff Tab Data ──────────────────────────────────────────────────
        $totalTechnicians  = $technicians->count();
        // Staff Tab: Overall SLA compliance KPI — based on completed (resolved+closed) tickets
        $staffCompletedTotal = (clone $baseQuery)->whereNotNull('technician_id')->whereIn('status', ['resolved', 'closed'])->count();
        $staffSlaMetTotal    = (clone $baseQuery)->whereNotNull('technician_id')->whereIn('status', ['resolved', 'closed'])->where('sla_breached', false)->count();
        $overallSlaCompliance = $staffCompletedTotal > 0 ? round(($staffSlaMetTotal / $staffCompletedTotal) * 100) : null;

        // Build per-technician stats from all tickets
        $staffCounts = [];
        foreach ($allTickets as $ticket) {
            if (!$ticket->technician_id) continue;
            $techId = $ticket->technician_id;

            if (!isset($staffCounts[$techId])) {
                $staffCounts[$techId] = [
                    'workload'     => 0, // active (assigned + in_progress + overdue) only
                    'closed'       => 0,
                    'open'         => 0, // not closed, not overdue
                    'overdue'      => 0, // active + past due_at
                    'res_days'     => 0,
                    'res_count'    => 0,
                    'sla_met'      => 0,
                    'sla_breached' => 0,
                ];
            }

            $isCompleted = in_array($ticket->status, ['resolved', 'closed']);

            if ($isCompleted) {
                $staffCounts[$techId]['closed']++; // 'closed' key now means 'completed'
                if ($ticket->resolved_at) {
                    $staffCounts[$techId]['res_days'] += ($ticket->created_at->diffInHours($ticket->resolved_at) / 24);
                    $staffCounts[$techId]['res_count']++;
                }
                // SLA tracking for completed tickets
                if ($ticket->due_at) {
                    if (!$ticket->sla_breached) {
                        $staffCounts[$techId]['sla_met']++;
                    } else {
                        $staffCounts[$techId]['sla_breached']++;
                    }
                }
            } elseif ($this->isActiveOverdue($ticket)) {
                // Active ticket past its deadline — counts as overdue workload
                $staffCounts[$techId]['workload']++;
                $staffCounts[$techId]['overdue']++;
            } else {
                // Active ticket within deadline
                $staffCounts[$techId]['workload']++;
                $staffCounts[$techId]['open']++;
            }
        }

        $staffWorkload = [];
        $staffClosed   = [];
        $staffTable    = [];
        $staffResTime  = [];

        foreach ($technicians as $tech) {
            $data = $staffCounts[$tech->id] ?? [
                'workload' => 0, 'closed' => 0, 'open' => 0, 'overdue' => 0,
                'res_days' => 0, 'res_count' => 0, 'sla_met' => 0, 'sla_breached' => 0,
            ];

            $avg           = $data['res_count'] > 0 ? round($data['res_days'] / $data['res_count'], 1) : null;
            $avgDisplay    = $avg !== null ? $avg . ' days' : 'N/A';
            $totalCompleted = $data['closed']; // 'closed' key = resolved + closed
            $slaComplPct   = $totalCompleted > 0
                ? round(($data['sla_met'] / $totalCompleted) * 100) . '%'
                : 'N/A';

            // Workload chart: only active tickets
            if ($data['workload'] > 0) {
                $staffWorkload[] = ['name' => $tech->name, 'assigned' => $data['workload']];
            }
            // Completed chart
            if ($data['closed'] > 0) {
                $staffClosed[] = ['name' => $tech->name, 'closed' => $data['closed']];
                $staffResTime[]  = ['name' => $tech->name, 'days'     => $avg ?? 0];
            }

            $staffTable[] = [
                'name'         => $tech->name,
                'assigned'     => $data['workload'],
                'closed'       => $totalCompleted,
                'open'         => $data['open'],
                'overdue'      => $data['overdue'],
                'avg'          => $avgDisplay,
                'sla_met'      => $data['sla_met'],
                'sla_breached' => $data['sla_breached'],
                'sla_pct'      => $slaComplPct,
            ];
        }

        // Sort workload descending
        usort($staffWorkload, fn($a, $b) => $b['assigned'] <=> $a['assigned']);
        $staffWorkload = array_slice($staffWorkload, 0, 10);

        usort($staffClosed, fn($a, $b) => $b['closed'] <=> $a['closed']);
        $staffClosed = array_slice($staffClosed, 0, 10);

        usort($staffResTime, fn($a, $b) => $a['days'] <=> $b['days']); // ascending (lower is better)
        $staffResTime = array_slice($staffResTime, 0, 10);

        $ticketsAssigned = (clone $baseQuery)->whereNotNull('technician_id')->whereIn('status', ['assigned', 'in_progress', 'overdue'])->count();
        $ticketsClosed   = (clone $baseQuery)->whereNotNull('technician_id')->where('status', 'closed')->count();
        $ticketsOverdue  = (clone $baseQuery)->whereNotNull('technician_id')->where('status', 'overdue')->count();

        // Staff Donut — Status Distribution
        $now = now();
        $assignedCount    = (clone $baseQuery)->whereNotNull('technician_id')->where('status', 'assigned')->count();
        $inProgressCount  = (clone $baseQuery)->whereNotNull('technician_id')->where('status', 'in_progress')->count();
        $resolvedCount    = (clone $baseQuery)->whereNotNull('technician_id')->where('status', 'resolved')->count();
        $closedCount      = $ticketsClosed;

        $staffDonut = [
            ['name' => 'Assigned',    'value' => $assignedCount,   'color' => '#8B5CF6'],
            ['name' => 'In Progress', 'value' => $inProgressCount, 'color' => '#F59E0B'],
            ['name' => 'Resolved',    'value' => $resolvedCount,   'color' => '#10B981'],
            ['name' => 'Closed',      'value' => $closedCount,     'color' => '#64748B'],
        ];
        $staffDonut = array_values(array_filter($staffDonut, fn($item) => $item['value'] > 0));

        // ── 3. Period Tab Data ─────────────────────────────────────────────────
        $creationTrend = $monthlyTrend;
        $monthlyClosedTickets = (clone $baseQuery)->select(
                DB::raw('MONTH(updated_at) as month_num'),
                DB::raw('count(*) as tickets')
            )
            ->where('status', 'closed')
            ->whereYear('updated_at', date('Y'))
            ->groupBy('month_num')
            ->orderBy('month_num')
            ->get();

        $openClosed = [];
        for ($i = 1; $i <= 12; $i++) {
            $monthObjC = $monthlyTickets->firstWhere('month_num', $i);
            $monthObjR = $monthlyClosedTickets->firstWhere('month_num', $i);
            $openClosed[] = [
                'month'  => $monthNames[$i],
                'open'   => $monthObjC ? $monthObjC->tickets : 0, // Total opened in month
                'closed' => $monthObjR ? $monthObjR->tickets : 0, // Total closed in month
            ];
        }

        // Category Usage
        $categoryTrend = [];
        $categoryCounts = (clone $baseQuery)->select('category_id', DB::raw('count(*) as count'))
            ->whereNotNull('category_id')
            ->groupBy('category_id')
            ->get();

        foreach ($categoryCounts as $catCount) {
            $cat = IncidentCategory::find($catCount->category_id);
            if ($cat) {
                $categoryTrend[] = [
                    'name'  => $cat->name,
                    'count' => $catCount->count,
                ];
            }
        }
        usort($categoryTrend, fn($a, $b) => $b['count'] <=> $a['count']);

        return view('admin.reports', compact(
            'companies', 'departments', 'technicians',
            'totalTickets', 'openTickets', 'closedTickets', 'completedTickets', 'avgResolutionDays', 'slaCompliance',
            'donutData', 'deptData', 'monthlyTrend', 'deptTable',
            'totalTechnicians', 'overallSlaCompliance', 'ticketsAssigned', 'ticketsClosed', 'ticketsOverdue',
            'staffWorkload', 'staffClosed', 'staffTable', 'staffResTime', 'staffDonut',
            'creationTrend', 'openClosed', 'categoryTrend'
        ));
    }
}
