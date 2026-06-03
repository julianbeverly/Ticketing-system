<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Display the admin dashboard.
     * Fetches the 5 most recent tickets to show in the Recent Activity section.
     */
    public function admin()
    {
        // Default Metrics for Admin (Current Month)
        $now = Carbon::now();
        $counts = $this->calculateCounts($now->month, $now->year);
        
        // Total stats (not filtered by month for the top cards)
        $totalStats = [
            'total'       => Ticket::count(),
            'open'        => Ticket::where('status', 'open')->count(),
            'overdue'     => Ticket::where('status', 'overdue')->count(),
            'closed'      => Ticket::where('status', 'closed')->count(),
            'users'       => \App\Models\User::count(),
        ];

        // Get the 5 latest activities for the activity feed
        $recentActivities = \App\Models\TicketActivity::with(['ticket', 'user'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // Initial SLA Data
        $slaData = $this->calculateSlaData($now->month, $now->year);

        return view('admin.dash', [
            'recentActivities' => $recentActivities,
            'counts' => $counts,
            'totalStats' => $totalStats,
            'slaData' => $slaData
        ]);
    }

    /**
     * AJAX endpoint for dynamic chart data
     */
    public function getChartData(Request $request)
    {
        $period = $request->input('period', 'this_month');
        $date = Carbon::now();

        if ($period === 'last_month') {
            $date = $date->subMonth();
        } elseif (is_numeric($period)) {
            // Specific month index (1-12)
            $date->month($period);
        }

        $counts = $this->calculateCounts($date->month, $date->year);

        return response()->json($counts);
    }

    public function getSlaData(Request $request)
    {
        $period = $request->input('period', 'this_month');
        $date = Carbon::now();

        if ($period === 'last_month') {
            $date = $date->subMonth();
        } elseif (is_numeric($period)) {
            $date->month($period);
        }

        $slaData = $this->calculateSlaData($date->month, $date->year);

        return response()->json($slaData);
    }

    /**
     * Helper to calculate counts for a specific month/year
     */
    private function calculateCounts($month, $year)
    {
        $query = Ticket::whereMonth('created_at', $month)
                      ->whereYear('created_at', $year);

        return [
            'open'        => (clone $query)->where('status', 'open')->count(),
            'assigned'    => (clone $query)->where('status', 'assigned')->count(),
            'in_progress' => (clone $query)->where('status', 'in_progress')->count(),
            'closed'      => (clone $query)->where('status', 'closed')->count(),
            'overdue'     => (clone $query)->where('status', 'overdue')->count(),
        ];
    }

    /**
     * Helper to calculate technician SLA data for a specific month/year
     */
    private function calculateSlaData($month, $year)
    {
        $technicians = \App\Models\User::where('role', 'technician')->get();
        $data = [];

        foreach ($technicians as $tech) {
            // Tickets active this month: either created, resolved, or currently assigned
            // This ensures that even if a ticket was created last month, it shows up if it's still assigned to the tech.
            $tickets = Ticket::where('technician_id', $tech->id)
                ->where(function($q) use ($month, $year) {
                    $q->where(function($sq) use ($month, $year) {
                        $sq->whereMonth('created_at', $month)
                           ->whereYear('created_at', $year);
                    })->orWhere(function($sq) use ($month, $year) {
                        $sq->whereMonth('resolved_at', $month)
                           ->whereYear('resolved_at', $year);
                    })->orWhere('status', '!=', 'closed'); // Include any non-closed tickets assigned to them
                })->get();

            $total = $tickets->count();
            
            if ($total === 0) {
                $slaPercentage = 0;
            } else {
                // A ticket failed SLA if:
                // 1. It is currently marked as overdue
                // 2. OR it was resolved/closed and resolved_at > due_at
                $failed = $tickets->filter(function($t) {
                    $isResolvedLate = $t->resolved_at && $t->due_at && $t->resolved_at->gt($t->due_at);
                    $isCurrentlyOverdue = $t->status === 'overdue';
                    return $isResolvedLate || $isCurrentlyOverdue;
                })->count();
                
                $slaPercentage = round((($total - $failed) / $total) * 100, 1);
            }

            $resolved = $tickets->whereIn('status', ['resolved', 'closed'])->count();
            $overdue = $tickets->where('status', 'overdue')->count();
            
            $avgResolutionTime = $tickets->filter(fn($t) => $t->resolved_at && $t->created_at)->map(function($t) {
                return abs($t->resolved_at->diffInHours($t->created_at));
            })->avg() ?? 0;

            $data[] = [
                'name' => $tech->name,
                'role' => $tech->speciality ?? 'Technician',
                'assigned' => $total,
                'resolved' => $resolved,
                'overdue' => $overdue,
                'sla_score' => $slaPercentage,
                'avg_time' => round($avgResolutionTime, 1)
            ];
        }

        return $data;
    }

    /**
     * Display the technician dashboard.
     */
    public function technician()
    {
        $techId = Auth::id();

        // Metrics for Technician
        $counts = [
            'total'       => Ticket::where('technician_id', $techId)->count(),
            'assigned'    => Ticket::where('technician_id', $techId)->count(), // Show total assigned regardless of status
            'in_progress' => Ticket::where('technician_id', $techId)->where('status', 'in_progress')->count(),
            'closed'      => Ticket::where('technician_id', $techId)->where('status', 'closed')->count(),
            'overdue'     => Ticket::where('technician_id', $techId)->where('status', 'overdue')->count(),
        ];

        // Get the 5 latest activities for tickets assigned to this technician
        $recentActivities = \App\Models\TicketActivity::with(['ticket', 'user'])
            ->whereHas('ticket', function ($query) use ($techId) {
                $query->where('technician_id', $techId);
            })
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return view('techdashboard.techdash', compact('recentActivities', 'counts'));
    }

    /**
     * Display the employee dashboard.
     */
    public function employee()
    {
        $userId = Auth::id();

        // Metrics for Employee
        $counts = [
            'total'       => Ticket::where('user_id', $userId)->count(),
            'open'        => Ticket::where('user_id', $userId)->where('status', 'open')->count(),
            'in_progress' => Ticket::where('user_id', $userId)->where('status', 'in_progress')->count(),
            'closed'      => Ticket::where('user_id', $userId)->where('status', 'closed')->count(),
            'overdue'     => Ticket::where('user_id', $userId)->where('status', 'overdue')->count(),
        ];

        // Get the 5 latest activities for tickets created by this employee
        $recentActivities = \App\Models\TicketActivity::with(['ticket', 'user'])
            ->whereHas('ticket', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return view('employee.employeedash', compact('recentActivities', 'counts'));
    }
}
