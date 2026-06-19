<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ticket;
use App\Models\TicketAttachment;
use App\Models\IncidentCategory;
use App\Models\IncidentType;
use App\Models\User;
use App\Models\TicketActivity;
use App\Mail\NewTicketNotification;
use App\Mail\TicketAssigned;
use App\Mail\TicketStatusUpdated;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\TicketReportExport;
use App\Exports\TechReportExport;
use App\Exports\TechOwnReportExport;
use App\Exports\EmployeeReportExport;

class TicketController extends Controller
{
    /**
     * Admin: Display all tickets from all users.
     * Loads related user, category, and type data for the table columns.
     */
    public function index(Request $request)
    {
        $query = Ticket::with(['user', 'technician', 'category', 'type']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('ticket_id', 'like', "%{$search}%")
                  ->orWhere('subject', 'like', "%{$search}%")
                  ->orWhereHas('user', function($qu) use ($search) {
                      $qu->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $tickets = $query->orderBy('created_at', 'desc')->paginate(10)->withQueryString();

        // Fetch all technicians for the assignment modal
        $technicians = User::where('role', 'technician')->get();

        return view('admin.ticket', compact('tickets', 'technicians'));
    }

    /**
     * Admin: Show the create ticket form.
     */
    public function create()
    {
        // Fetch employees, categories, and types for the admin create ticket form
        $employees = User::where('role', 'employee')->get();
        $categories = IncidentCategory::all();
        $types = IncidentType::with('category')->get();

        return view('admin.createticket', compact('employees', 'categories', 'types'));
    }

    /**
     * Admin: Show ticket details page.
     */
    public function details(Ticket $ticket)
    {
        $ticket->load(['user', 'technician', 'category', 'type', 'attachments']);
        
        $attachmentsData = $ticket->attachments->map(function($a) {
            return [
                'url' => Storage::url($a->file_path),
                'name' => $a->original_name,
                'mime' => $a->mime_type,
                'time' => $a->created_at->format('M d, h:i A')
            ];
        });

        return view('admin.ticketdetails', compact('ticket', 'attachmentsData'));
    }


    /**
     * Admin: Show SLA configuration page.
     */
    public function slaConfig()
    {
        // reads data from the db
        $configs = \App\Models\SlaConfig::all()->keyBy('priority');
        return view('admin.slaconfig', compact('configs'));
    }

    /**
     * Admin: Update SLA configuration.
     */
    public function updateSlaConfig(Request $request)
    {
        $request->validate([
            'high' => 'required|integer|min:1',
            'medium' => 'required|integer|min:1',
            'low' => 'required|integer|min:1',
        ]);

        \App\Models\SlaConfig::updateOrCreate(['priority' => 'high'], ['hours' => $request->high]);
        \App\Models\SlaConfig::updateOrCreate(['priority' => 'medium'], ['hours' => $request->medium]);
        \App\Models\SlaConfig::updateOrCreate(['priority' => 'low'], ['hours' => $request->low]);

        session()->flash('success', 'SLA configuration successfully changed.');

        return response()->json(['success' => true, 'message' => 'SLA Configuration updated successfully.']);
    }

    /**
     * Admin: Show the technician performance report for all technicians.
     */
    public function report(Request $request)
    {
        $query = User::where('role', 'technician');

        if ($request->filled('search')) {
            $query->where('name', 'like', "%{$request->search}%");
        }

        $technicians = $query->paginate(10)->withQueryString();
        
        $reportType = $request->input('report_type', 'custom');

        $technicians->getCollection()->transform(function($tech) use ($request, $reportType) {
            $ticketQuery = Ticket::where('technician_id', $tech->id);

            if ($reportType === 'daily' && $request->filled('daily_date')) {
                $ticketQuery->whereDate('created_at', $request->daily_date);
            } elseif ($reportType === 'weekly' && $request->filled('weekly_date')) {
                $weekStr = $request->weekly_date;
                $start = \Carbon\Carbon::now()->setISODate(
                    (int) substr($weekStr, 0, 4),
                    (int) substr($weekStr, 6)
                )->startOfWeek();
                $end = $start->copy()->endOfWeek();
                $ticketQuery->whereBetween('created_at', [$start, $end]);
            } elseif ($reportType === 'monthly' && $request->filled('monthly_date')) {
                [$year, $month] = explode('-', $request->monthly_date);
                $ticketQuery->whereYear('created_at', $year)->whereMonth('created_at', $month);
            } elseif ($reportType === 'custom') {
                if ($request->filled('start_date')) {
                    $ticketQuery->whereDate('created_at', '>=', $request->start_date);
                }
                if ($request->filled('end_date')) {
                    $ticketQuery->whereDate('created_at', '<=', $request->end_date);
                }
            }

            $tech->assigned_count   = (clone $ticketQuery)->count();
            $tech->inprogress_count = (clone $ticketQuery)->where('status', 'in_progress')->count();
            $tech->resolved_count   = (clone $ticketQuery)->where('status', 'resolved')->count();
            $tech->closed_count     = (clone $ticketQuery)->where('status', 'closed')->count();
            $tech->overdue_count    = (clone $ticketQuery)->where('status', 'overdue')->count();
            return $tech;
        });

        return view('admin.techreport', compact('technicians'));
    }

    /**
     * Technician: Show the logged-in technician's OWN performance report.
     * Each technician only sees their own assigned/inprogress/closed ticket counts.
     */
    public function techOwnReport(Request $request)
    {
        // gets currently logged in user
        $tech = Auth::user();

        // Scope all counts to the currently authenticated technician only
        $ticketQuery = Ticket::where('technician_id', $tech->id);
        $reportType = $request->input('report_type', 'custom');

        if ($reportType === 'daily' && $request->filled('daily_date')) {
            $ticketQuery->whereDate('created_at', $request->daily_date);
        } elseif ($reportType === 'weekly' && $request->filled('weekly_date')) {
            $weekStr = $request->weekly_date;
            $start = \Carbon\Carbon::now()->setISODate(
                (int) substr($weekStr, 0, 4),
                (int) substr($weekStr, 6)
            )->startOfWeek();
            $end = $start->copy()->endOfWeek();
            $ticketQuery->whereBetween('created_at', [$start, $end]);
        } elseif ($reportType === 'monthly' && $request->filled('monthly_date')) {
            [$year, $month] = explode('-', $request->monthly_date);
            $ticketQuery->whereYear('created_at', $year)->whereMonth('created_at', $month);
        } elseif ($reportType === 'custom') {
            if ($request->filled('start_date')) {
                $ticketQuery->whereDate('created_at', '>=', $request->start_date);
            }
            if ($request->filled('end_date')) {
                $ticketQuery->whereDate('created_at', '<=', $request->end_date);
            }
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $ticketQuery->where(function($q) use ($search) {
                $q->where('ticket_id', 'like', "%{$search}%")
                  ->orWhere('subject', 'like', "%{$search}%");
            });
        }

        $tech->assigned_count   = (clone $ticketQuery)->count();
        $tech->inprogress_count = (clone $ticketQuery)->where('status', 'in_progress')->count();
        $tech->resolved_count   = (clone $ticketQuery)->where('status', 'resolved')->count();
        $tech->closed_count     = (clone $ticketQuery)->where('status', 'closed')->count();
        $tech->overdue_count    = (clone $ticketQuery)->where('status', 'overdue')->count();

        return view('techdashboard.technicianreport', compact('tech'));
    }

    /**
     * Technician: Show detailed performance report for the logged-in technician.
     */
    public function techOwnReportDetails()
    {
        $user = Auth::user();

        $totalTickets = Ticket::where('technician_id', $user->id)->count();
        $overdueCount = Ticket::where('technician_id', $user->id)->where('status', 'overdue')->count();
        
        // SLA Performance: Percentage of tickets NOT overdue
        $slaPerformance = $totalTickets > 0 
            ? round((($totalTickets - $overdueCount) / $totalTickets) * 100, 1) 
            : 0;

        // ── Date filtering based on report_type (by assignment date = created_at) ─
        $query = Ticket::with(['category', 'type'])->where('technician_id', $user->id);
        $reportType = request()->input('report_type', 'custom');

        if ($reportType === 'daily' && request()->filled('daily_date')) {
            $query->whereDate('created_at', request()->daily_date);
        } elseif ($reportType === 'weekly' && request()->filled('weekly_date')) {
            $weekStr = request()->weekly_date;
            $start = \Carbon\Carbon::now()->setISODate(
                (int) substr($weekStr, 0, 4),
                (int) substr($weekStr, 6)
            )->startOfWeek();
            $end = $start->copy()->endOfWeek();
            $query->whereBetween('created_at', [$start, $end]);
        } elseif ($reportType === 'monthly' && request()->filled('monthly_date')) {
            [$year, $month] = explode('-', request()->monthly_date);
            $query->whereYear('created_at', $year)->whereMonth('created_at', $month);
        } elseif ($reportType === 'custom') {
            if (request()->filled('start_date')) {
                $query->whereDate('created_at', '>=', request()->start_date);
            }
            if (request()->filled('end_date')) {
                $query->whereDate('created_at', '<=', request()->end_date);
            }
        }

        if (request()->filled('search')) {
            $search = request()->search;
            $query->where(function($q) use ($search) {
                $q->where('ticket_id', 'like', "%{$search}%")
                  ->orWhere('subject', 'like', "%{$search}%");
            });
        }
        // ────────────────────────────────────────────────────────────────────────

        $tickets = $query->orderBy('created_at', 'desc')->get();

        return view('techdashboard.techreportdetails', compact('user', 'overdueCount', 'slaPerformance', 'tickets'));
    }

    /**
     * Admin: Show detailed report for a specific technician.
     */
    public function techReportDetails(User $user)
    {
        if ($user->role !== 'technician') {
            abort(404);
        }

        // ── Date filtering based on report_type (filters by assignment date = created_at) ─
        $query = Ticket::with(['category', 'type', 'user'])
            ->where('technician_id', $user->id);

        $reportType = request()->input('report_type', 'custom');

        if ($reportType === 'daily' && request()->filled('daily_date')) {
            $query->whereDate('created_at', request()->daily_date);
        } elseif ($reportType === 'weekly' && request()->filled('weekly_date')) {
            $weekStr = request()->weekly_date;
            $start = \Carbon\Carbon::now()->setISODate(
                (int) substr($weekStr, 0, 4),
                (int) substr($weekStr, 6)
            )->startOfWeek();
            $end = $start->copy()->endOfWeek();
            $query->whereBetween('created_at', [$start, $end]);
        } elseif ($reportType === 'monthly' && request()->filled('monthly_date')) {
            [$year, $month] = explode('-', request()->monthly_date);
            $query->whereYear('created_at', $year)->whereMonth('created_at', $month);
        } elseif ($reportType === 'custom') {
            if (request()->filled('start_date')) {
                $query->whereDate('created_at', '>=', request()->start_date);
            }
            if (request()->filled('end_date')) {
                $query->whereDate('created_at', '<=', request()->end_date);
            }
        }
        // ──────────────────────────────────────────────────────────────────────────────────

        $tickets = $query->orderBy('created_at', 'desc')->get();

        $totalTickets = Ticket::where('technician_id', $user->id)->count();
        $overdueCount = Ticket::where('technician_id', $user->id)->where('status', 'overdue')->count();

        $slaPerformance = $totalTickets > 0
            ? round((($totalTickets - $overdueCount) / $totalTickets) * 100, 1)
            : 0;

        return view('admin.techreportdetails', compact('user', 'overdueCount', 'slaPerformance', 'tickets'));
    }

    /**
     * Display the Ticketing Activity Report page.
     * This is a sub-page under the Reporting section in the admin sidebar.
     */
    public function ticketReport(Request $request)
    {
        $query = Ticket::with(['user', 'technician', 'category', 'type']);

        if ($request->filled('status')) {
            // Check if status filter is actually a technician filter (starts with tech_)
            if (str_starts_with($request->status, 'tech_')) {
                $query->where('technician_id', str_replace('tech_', '', $request->status));
            } else {
                $query->where('status', $request->status);
            }
        }
        if ($request->filled('technician')) {
            // Check if status filter is actually a technician filter (starts with tech_)
            if (str_starts_with($request->technician, 'tech_')) {
                $query->where('technician_id', str_replace('tech_', '', $request->technician));
            } else {
                $query->where('technician_id', $request->technician);
            }
        }

        // ── Date filtering based on report_type ──────────────────────────
        $reportType = $request->input('report_type', 'custom');

        if ($reportType === 'daily' && $request->filled('daily_date')) {
            $query->whereDate('created_at', $request->daily_date);
        } elseif ($reportType === 'weekly' && $request->filled('weekly_date')) {
            // HTML week input returns "YYYY-Www"
            $weekStr = $request->weekly_date; // e.g. "2026-W23"
            $start = \Carbon\Carbon::now()->setISODate(
                (int) substr($weekStr, 0, 4),
                (int) substr($weekStr, 6)
            )->startOfWeek();
            $end = $start->copy()->endOfWeek();
            $query->whereBetween('created_at', [$start, $end]);
        } elseif ($reportType === 'monthly' && $request->filled('monthly_date')) {
            // HTML month input returns "YYYY-MM"
            [$year, $month] = explode('-', $request->monthly_date);
            $query->whereYear('created_at', $year)->whereMonth('created_at', $month);
        } elseif ($reportType === 'custom') {
            if ($request->filled('start_date')) {
                $query->whereDate('created_at', '>=', $request->start_date);
            }
            if ($request->filled('end_date')) {
                $query->whereDate('created_at', '<=', $request->end_date);
            }
        }
        // ─────────────────────────────────────────────────────────────────

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('ticket_id', 'like', "%{$search}%")
                  ->orWhere('subject', 'like', "%{$search}%");
            });
        }

        $tickets = $query->orderBy('created_at', 'desc')->paginate(10)->withQueryString();

        // Fetch all technicians to display in the status filter dropdown
        $technicians = User::where('role', 'technician')->get();

        return view('admin.ticketreport', compact('tickets', 'technicians'));
    }

    // excel export method
    public function exportExcel(Request $request)
    {
        return Excel::download(
            new TicketReportExport($request->status, $request->start_date, $request->end_date),
            'ticket-report.xlsx'
        );
    }
 //   pdf expoort method
    public function exportPdf(Request $request)
    {
        $query = Ticket::with(['category', 'type', 'technician']);

        if ($request->filled('status')) {
            // [ADDED] Check if status filter is actually a technician filter (starts with tech_)
            if (str_starts_with($request->status, 'tech_')) {
                $query->where('technician_id', str_replace('tech_', '', $request->status));
            } else {
                $query->where('status', $request->status);
            }
        }

        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        $tickets = $query->orderBy('created_at', 'desc')->get();

        $pdf = Pdf::loadView(
            'admin.reports.ticket-report-pdf',
            compact('tickets')
        );

        return $pdf->download('ticket-report.pdf');
    }

    // tech excel export method
    public function exportTechExcel()
    {
        return Excel::download(
            new TechReportExport,
            'tech-report.xlsx'
        );
    }

    // tech pdf export method
    public function exportTechPdf()
    {
        $technicians = User::where('role', 'technician')->get()->map(function($tech) {
            $tech->assigned_count   = Ticket::where('technician_id', $tech->id)->where('status', 'assigned')->count();
            $tech->inprogress_count = Ticket::where('technician_id', $tech->id)->where('status', 'in_progress')->count();
            $tech->resolved_count   = Ticket::where('technician_id', $tech->id)->where('status', 'resolved')->count();
            $tech->closed_count     = Ticket::where('technician_id', $tech->id)->where('status', 'closed')->count();
            $tech->overdue_count    = Ticket::where('technician_id', $tech->id)->where('status', 'overdue')->count();
            return $tech;
        });

        $pdf = Pdf::loadView(
            'admin.reports.tech-report-pdf',
            compact('technicians')
        );

        return $pdf->download('tech-report.pdf');
    }

    // technician own excel export method
    public function exportTechOwnExcel()
    {
        return Excel::download(
            new TechOwnReportExport,
            'my-tech-report.xlsx'
        );
    }

    // technician own pdf export method
    public function exportTechOwnPdf()
    {
        $tech = Auth::user();
        $tech->assigned_count   = Ticket::where('technician_id', $tech->id)->where('status', 'assigned')->count();
        $tech->inprogress_count = Ticket::where('technician_id', $tech->id)->where('status', 'in_progress')->count();
        $tech->resolved_count   = Ticket::where('technician_id', $tech->id)->where('status', 'resolved')->count();
        $tech->closed_count     = Ticket::where('technician_id', $tech->id)->where('status', 'closed')->count();
        $tech->overdue_count    = Ticket::where('technician_id', $tech->id)->where('status', 'overdue')->count();
        
        $technicians = collect([$tech]);

        $pdf = Pdf::loadView(
            'admin.reports.tech-report-pdf',
            compact('technicians')
        );

        return $pdf->download('my-tech-report.pdf');
    }

    public function dent(Request $request)
    {
        $search = $request->input('search');
        $tab = $request->input('tab', 'types');

        $categoriesQuery = \App\Models\IncidentCategory::query();
        $typesQuery = \App\Models\IncidentType::with('category');

        if ($request->filled('search')) {
            if ($tab === 'categories') {
                $categoriesQuery->where('name', 'like', "%{$search}%");
            } else {
                $typesQuery->where('name', 'like', "%{$search}%");
            }
        }

        $categories = $categoriesQuery->paginate(10, ['*'], 'categories_page')->withQueryString();
        $types = $typesQuery->paginate(10, ['*'], 'types_page')->withQueryString();

        return view('admin.dent', compact('categories', 'types'));
    }



    // EMPLOYEE METHODS

    /**
     * Employee: Display the ticket listing page.
     * Shows only tickets created by the currently logged-in employee.
     */
    public function employeeTickets(Request $request)
     {
        $query = Ticket::with(['category', 'type'])
            ->where('user_id', Auth::id());

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('ticket_id', 'like', "%{$search}%")
                  ->orWhere('subject', 'like', "%{$search}%");
            });
        }

        $tickets = $query->orderBy('created_at', 'desc')->paginate(10)->withQueryString();

        return view('employee.employticket', compact('tickets'));
    }

    /**
     * Employee: Show ticket details page.
     */
    public function employeeDetails(Ticket $ticket)
    {
        // Ensure the employee only views their own tickets
        if ($ticket->user_id !== Auth::id()) {
            abort(403);
        }
        $ticket->load(['category', 'type', 'attachments']);

        $attachmentsData = $ticket->attachments->map(function($a) {
            return [
                'url' => Storage::url($a->file_path),
                'name' => $a->original_name,
                'mime' => $a->mime_type,
                'time' => $a->created_at->format('M d, h:i A')
            ];
        });

        return view('employee.employticketdetails', compact('ticket', 'attachmentsData'));
    }

    /**
     * Employee: Display the Ticketing Activity Report.
     */
    public function employeeTicketReport(Request $request)
    {
        $query = Ticket::with(['category', 'type'])
            ->where('user_id', Auth::id());

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('ticket_id', 'like', "%{$search}%")
                  ->orWhere('subject', 'like', "%{$search}%");
            });
        }

        // ── Date filtering based on report_type ──────────────────────────
        $reportType = $request->input('report_type', 'custom');

        if ($reportType === 'daily' && $request->filled('daily_date')) {
            $query->whereDate('created_at', $request->daily_date);
        } elseif ($reportType === 'weekly' && $request->filled('weekly_date')) {
            $weekStr = $request->weekly_date;
            $start = \Carbon\Carbon::now()->setISODate(
                (int) substr($weekStr, 0, 4),
                (int) substr($weekStr, 6)
            )->startOfWeek();
            $end = $start->copy()->endOfWeek();
            $query->whereBetween('created_at', [$start, $end]);
        } elseif ($reportType === 'monthly' && $request->filled('monthly_date')) {
            [$year, $month] = explode('-', $request->monthly_date);
            $query->whereYear('created_at', $year)->whereMonth('created_at', $month);
        } elseif ($reportType === 'custom') {
            if ($request->filled('start_date')) {
                $query->whereDate('created_at', '>=', $request->start_date);
            }
            if ($request->filled('end_date')) {
                $query->whereDate('created_at', '<=', $request->end_date);
            }
        }
        // ─────────────────────────────────────────────────────────────────

        $tickets = $query->orderBy('created_at', 'desc')->paginate(10)->withQueryString();

        return view('employee.report', compact('tickets'));
    }

    // employee excel export method
    public function exportEmployeeExcel()
    {
        return Excel::download(
            new EmployeeReportExport,
            'employee-tickets.xlsx'
        );
    }

    // employee pdf export method
    public function exportEmployeePdf()
    {
        $tickets = Ticket::with(['category', 'type', 'technician'])
            ->where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();

        $pdf = Pdf::loadView(
            'admin.reports.ticket-report-pdf',
            compact('tickets')
        );

        return $pdf->download('employee-tickets.pdf');
    }

    /**
     * Employee: Show the create ticket form.
     * Passes incident categories and types so the dropdowns are populated dynamically.
     */
    public function employeeCreateTicket()
    {
        // Fetch all categories and types from the database for the form dropdowns
        $categories = IncidentCategory::all();
        $types = IncidentType::with('category')->get();

        return view('employee.create', compact('categories', 'types'));
    }

    /**
     * Employee: Store a newly created ticket in the database.
     * Validates the form input, creates the ticket record, handles file attachments,
     * and redirects back with a success message.
     */
    public function store(Request $request)
    {
        // Fix for "Other" type_id failing the integer exists validation
        if ($request->input('type_id') === 'other') {
            $request->merge(['type_id' => null]);
        }

        // Validate all incoming form fields
        $request->validate([
            'user_id'      => 'nullable|exists:users,id',
            'category_id'  => 'nullable|exists:categories,id',
            'type_id'      => 'nullable|exists:types,id',
            'custom_type'  => 'nullable|string|max:255',
            'subject'      => 'required|string|max:255',
            'description'  => 'required|string',
            'priority'     => 'required|in:low,medium,high',
            // Allow multiple file uploads; each must be an image or video, max 25MB
            'attachments'   => 'nullable|array',
            'attachments.*' => 'file|mimes:png,jpg,jpeg,mp4,avi,mov|max:25600',
        ]);

        // Determine the owner of the ticket
        // If an admin/technician is creating the ticket and chose an employee, use that user_id
        $authUser = Auth::user();
        $ownerId = Auth::id();

        \Illuminate\Support\Facades\Log::info('Ticket creation attempt', [
            'auth_user_id' => Auth::id(),
            'auth_user_role' => $authUser->role ?? 'N/A',
            'request_user_id' => $request->input('user_id'),
            'has_user_id' => $request->has('user_id'),
        ]);

        if (($authUser->role === 'admin' || $authUser->role === 'technician') && $request->filled('user_id')) {
            $ownerId = $request->user_id;
        }

        // Create the ticket with a temporary ticket_id (will be updated in boot())
        $ticket = Ticket::create([
            'ticket_id'   => 'TEMP',
            'user_id'     => $ownerId,
            'category_id' => $request->category_id,
            'type_id'     => $request->type_id,
            'custom_type' => $request->custom_type,
            'subject'     => $request->subject,
            'description' => $request->description,
            'priority'    => $request->priority,
            'status'      => 'open',
        ]);

        TicketActivity::create([
            'ticket_id' => $ticket->id,
            'user_id' => Auth::id(),
            'action' => 'created',
            'description' => 'created a new ticket',
        ]);

        // Handle multiple file attachments if any were uploaded
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                // Store the file in the 'ticket_attachments' directory under public disk
                $path = $file->store('ticket_attachments', 'public');

                // Create a database record for each attachment
                TicketAttachment::create([
                    'ticket_id'     => $ticket->id,
                    'file_path'     => $path,
                    'original_name' => $file->getClientOriginalName(),
                    'mime_type'     => $file->getClientMimeType(),
                    'file_size'     => $file->getSize(),
                ]);
            }
        }

        // Send email notifications
        try {
            // Send email notification to the employee if created by admin/tech for them
            if ($ownerId !== Auth::id()) {
                $employee = User::find($ownerId);
                if ($employee) {
                    Mail::to($employee->email)->send(new \App\Mail\AdminCreatedTicketNotification($ticket, $authUser->name));
                }
            }

            // Send email notification to all admins
            $admins = User::where('role', 'admin')->get();
            foreach ($admins as $admin) {
                Mail::to($admin->email)->send(new NewTicketNotification($ticket));
            }
        } catch (\Exception $e) {
            \Log::error("Email sending failed on ticket creation: " . $e->getMessage());
        }

        // Redirect back to the appropriate list with a success message
        if ($authUser->role === 'admin') {
            return redirect()->route('admin.tickets')->with('success', 'Ticket created successfully! Ticket ID: ' . $ticket->ticket_id);
        } elseif ($authUser->role === 'technician') {
            return redirect()->route('tech.tickets')->with('success', 'Ticket created successfully! Ticket ID: ' . $ticket->ticket_id);
        }

        return redirect()->route('employee.tickets')->with('success', 'Ticket created successfully! Your ticket ID is ' . $ticket->ticket_id);
    }

    // TECHNICIAN METHODS

    /**
     * Technician: Display the ticket listing page.
     */
    public function techTickets(Request $request)
    {
        $query = Ticket::with(['user', 'category', 'type'])
            ->where('technician_id', Auth::id());

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('ticket_id', 'like', "%{$search}%")
                  ->orWhere('subject', 'like', "%{$search}%");
            });
        }

        $tickets = $query->orderBy('updated_at', 'desc')->paginate(10)->withQueryString();

        return view('techdashboard.techticket', compact('tickets'));
    }

    /**
     * Technician: Show ticket details page.
     */
    public function techDetails(Ticket $ticket)
    {
        $ticket->load(['user', 'technician', 'category', 'type', 'attachments']);
        
        $attachmentsData = $ticket->attachments->map(function($a) {
            return [
                'url' => Storage::url($a->file_path),
                'name' => $a->original_name,
                'mime' => $a->mime_type,
                'time' => $a->created_at->format('M d, h:i A')
            ];
        });

        return view('techdashboard.techdetails', compact('ticket', 'attachmentsData'));
    }

    /**
     * Admin: Assign a technician to a ticket.
     */
    public function assignTechnician(Request $request, Ticket $ticket)
    {
        $request->validate([
            'technician_id' => 'required|exists:users,id',
            'priority' => 'nullable|string|in:low,medium,high',
            'sla_type' => 'required|in:default,custom',
            'custom_sla' => 'required_if:sla_type,custom|nullable|numeric|min:0.5',
            'reminder_interval' => 'nullable|string|in:30mins,1hour,24hours,1day',
        ]);

        $priority = $request->priority ?? $ticket->priority;
        $slaHours = 0;

        if ($request->sla_type === 'custom' && $request->filled('custom_sla')) {
            $slaHours = (int) $request->custom_sla;
        } else {
            // Default SLA values from database
            $slaHours = \App\Models\SlaConfig::where('priority', $priority)->value('hours') ?? 24;
        }

        $dueAt = now()->addHours($slaHours);

        $ticket->update([
            'technician_id' => $request->technician_id,
            'priority' => $priority,
            'status' => 'assigned',
            'due_at' => $dueAt,
            'reminder_interval' => $request->reminder_interval,
            'last_reminder_at' => null, // Reset last reminder on (re)assignment
        ]);

        $ticket->load(['user', 'technician']);

        TicketActivity::create([
            'ticket_id' => $ticket->id,
            'user_id' => Auth::id(),
            'action' => 'assigned',
            'description' => 'assigned ticket to ' . $ticket->technician->name,
        ]);

        $ticket->load(['user', 'technician']);

        // Notify Technician (Primary), Admin (CC), Employee (CC)
        try {
            $adminEmail = Auth::user()->email;
            $employeeEmail = $ticket->user->email;
            $techEmail = $ticket->technician->email;

            Mail::to($techEmail)
                ->cc([$adminEmail, $employeeEmail])
                ->send(new TicketAssigned($ticket));
        } catch (\Exception $e) {
            \Log::error("Email sending failed on ticket assignment: " . $e->getMessage());
        }

        session()->flash('success', 'Technician successfully assigned to ticket.');

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Technician assigned successfully and emails sent.']);
        }
        return back()->with('success', 'Technician assigned successfully and emails sent.');
    }

    /**
     * Technician: Update the status of a ticket.
     */
    public function updateStatus(Request $request, Ticket $ticket)
    {
        $request->validate([
            'status' => 'required|string|in:open,assigned,in_progress,resolved,closed,overdue',
        ]);

        $ticket->update([
            'status' => $request->status,
        ]);

        TicketActivity::create([
            'ticket_id' => $ticket->id,
            'user_id' => Auth::id(),
            'action' => 'status_updated',
            'description' => 'changed status to ' . str_replace('_', ' ', $request->status),
        ]);

        $ticket->load(['user', 'technician']);

        // Notify Employee (Primary), Admin (CC), Technician (CC)
        try {
            $employeeEmail = $ticket->user->email;
            $admins = User::where('role', 'admin')->pluck('email')->toArray();
            $ccEmails = $admins;
            if ($ticket->technician) {
                $ccEmails[] = $ticket->technician->email;
            }

            Mail::to($employeeEmail)
                ->cc($ccEmails)
                ->send(new TicketStatusUpdated($ticket));
        } catch (\Exception $e) {
            \Log::error("Email sending failed on ticket status update: " . $e->getMessage());
        }

        session()->flash('success', 'Ticket status successfully updated to ' . str_replace('_', ' ', $ticket->status) . '.');

        return response()->json(['success' => true, 'message' => 'Status updated successfully and emails sent.']);
    }
    /**
     * Technician: Mark a ticket as resolved with a note.
     */
    public function resolve(Request $request, Ticket $ticket)
    {
        $request->validate([
            'resolution_note' => 'required|string',
        ]);

        $ticket->update([
            'resolution_note' => $request->resolution_note,
            'resolved_at'     => now(),
            'status'          => 'resolved',
        ]);

        TicketActivity::create([
            'ticket_id' => $ticket->id,
            'user_id' => Auth::id(),
            'action' => 'resolved',
            'description' => 'marked the ticket as resolved',
        ]);

        // Notify Employee (Primary), Admin (CC), Technician (CC)
        try {
            $admins = User::where('role', 'admin')->pluck('email')->toArray();
            $ccEmails = $admins;
            if ($ticket->technician) {
                $ccEmails[] = $ticket->technician->email;
            }

            Mail::to($ticket->user->email)
                ->cc($ccEmails)
                ->send(new \App\Mail\TicketResolved($ticket));
        } catch (\Exception $e) {
            \Log::error("Email sending failed on ticket resolution: " . $e->getMessage());
        }

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Ticket marked as resolved and employee notified.']);
        }
        return back()->with('success', 'Ticket marked as resolved and employee notified.');
    }

    /**
     * Employee: Accept or reject the resolution.
     */
    public function respondToResolution(Request $request, Ticket $ticket)
    {
        $request->validate([
            'action' => 'required|in:accept,reject',
            'rejection_note' => 'required_if:action,reject|nullable|string|max:1000',
        ]);

        if ($request->action === 'accept') {
            $ticket->update(['status' => 'closed']);
            $message = 'Solution accepted. Ticket is now closed.';
            TicketActivity::create([
                'ticket_id' => $ticket->id,
                'user_id' => Auth::id(),
                'action' => 'closed',
                'description' => 'accepted the solution and closed the ticket',
            ]);
        } else {
            $ticket->update([
                'status' => 'open',
                'rejection_note' => $request->rejection_note,
                'rejected_at' => now(),
            ]);
            $message = 'Solution rejected. Ticket has been reopened.';
            TicketActivity::create([
                'ticket_id' => $ticket->id,
                'user_id' => Auth::id(),
                'action' => 'reopened',
                'description' => 'rejected the solution: ' . $request->rejection_note,
            ]);
        }

        // Notify Technician (Primary), Admin (CC), Employee (CC)
        try {
            $admins = User::where('role', 'admin')->pluck('email')->toArray();
            $techEmail = $ticket->technician ? $ticket->technician->email : null;
            $employeeEmail = $ticket->user->email;

            $ccEmails = array_merge($admins, [$employeeEmail]);

            if ($techEmail) {
                Mail::to($techEmail)
                    ->cc($ccEmails)
                    ->send(new \App\Mail\TicketStatusUpdated($ticket));
            } else {
                Mail::to($admins)
                    ->cc($employeeEmail)
                    ->send(new \App\Mail\TicketStatusUpdated($ticket));
            }
        } catch (\Exception $e) {
            \Log::error("Email sending failed on resolution response: " . $e->getMessage());
        }

        return response()->json(['success' => true, 'message' => $message]);
    }
}