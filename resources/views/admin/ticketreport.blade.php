<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    {{-- Font Awesome Icons CDN --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
    {{-- Main stylesheet shared across all dashboard pages --}}
    <link rel="stylesheet" href="/dist/css/style.css" />
    <style>
        .tickets-table {
            width: 100%;
            table-layout: fixed;
            border-collapse: collapse;
        }
        .tickets-table th, .tickets-table td {
            padding: 12px 8px;
            text-align: left;
            word-wrap: break-word;
        }
        /* Specific column widths to ensure everything fits */
        .tickets-table th:nth-child(1), .tickets-table td:nth-child(1) { width: 80px; } /* ID */
        .tickets-table th:nth-child(2), .tickets-table td:nth-child(2) { width: 25%; }  /* Subject */
        .tickets-table th:nth-child(3), .tickets-table td:nth-child(3) { width: 15%; }  /* Classification */
        .tickets-table th:nth-child(4), .tickets-table td:nth-child(4) { width: 15%; }  /* Assignee */
        .tickets-table th:nth-child(5), .tickets-table td:nth-child(5) { width: 120px; } /* SLA */
        .tickets-table th:nth-child(6), .tickets-table td:nth-child(6) { width: 100px; } /* Status */
        .tickets-table th:nth-child(7), .tickets-table td:nth-child(7) { width: 90px; }  /* Action */

        .ticket-subject {
            white-space: normal;
            word-break: break-word;
            overflow-wrap: break-word;
            line-height: 1.4;
            display: block;
        }
        .ticket-id-link {
            font-weight: 600;
            color: #2563eb;
            text-decoration: none;
        }
        .action-col {
            text-align: right !important;
        }
        .tickets-card {
            overflow-x: auto;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            margin-bottom: 2rem;
        }

        /* Pagination Styling */
        .pagination-links nav {
            display: flex;
            align-items: center;
        }
        .pagination-links .pagination {
            display: flex;
            list-style: none;
            gap: 5px;
            margin: 0;
            padding: 0;
        }
        .pagination-links .page-item .page-link {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            color: #374151;
            text-decoration: none;
            font-size: 0.85rem;
            font-weight: 600;
            transition: all 0.2s;
        }
        .pagination-links .page-item.active .page-link {
            background-color: #2563eb;
            color: white;
            border-color: #2563eb;
        }
        .pagination-links .page-item.disabled .page-link {
            color: #9ca3af;
            cursor: not-allowed;
            background-color: #f9fafb;
        }
        .pagination-links .page-item:not(.active):not(.disabled) .page-link:hover {
            background-color: #f3f4f6;
            border-color: #d1d5db;
        }

        /* for the export buttons */
        .btn-export {
            text-decoration: none;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .tr-controls {
            display: flex;
            align-items: center;
            gap: 1rem;
            flex-wrap: wrap;
        }
    </style>
    <title>Ticketing Activity Report</title>
</head>

<body>

    <div class="dashboard-layout">

     
        @include('admin.partials.sidebar')
 
        <main class="main-content">

            <header class="top-header">
        <button class="menu-toggle" id="menuToggle">
          <i class="fa-solid fa-bars"></i>
        </button>
                <form method="GET" action="{{ route('admin.ticketreport') }}" style="flex: 1; max-width: 500px; display: flex; align-items: center; margin: 0 1.5rem;">
                    @if(request('start_date'))
                        <input type="hidden" name="start_date" value="{{ request('start_date') }}">
                    @endif
                    @if(request('end_date'))
                        <input type="hidden" name="end_date" value="{{ request('end_date') }}">
                    @endif
                    @if(request('status'))
                        <input type="hidden" name="status" value="{{ request('status') }}">
                    @endif
                    <div class="search-container" style="width: 100%; margin: 0;">
                        <i class="fa-solid fa-magnifying-glass" onclick="this.closest('form').submit();" style="cursor: pointer;"></i>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search tickets..." />
                    </div>
                </form>
                <div class="header-actions">
                    
                    <a href="{{ route('employee.profile') }}" class="icon-btn profile-btn">
                        <i class="fa-regular fa-circle-user"></i>
                    </a>
                </div>
            </header>

            <div class="dashboard-content">

                {{-- Breadcrumb trail showing current location in the app --}}
                <div class="breadcrumb">
                    <span>TICKETING ACTIVITY REPORT</span>
                </div>

   
                <div class="page-header-title">
                    <div>
                        <h1>Tickets</h1>
                    </div>

                    {{-- Export buttons — allow downloading the report data --}}
                    <div class="report-actions-wrapper">
                        {{-- Export to PDF (outlined style) --}}
                        <a href="{{ route('admin.ticketreport.export.pdf', request()->query()) }}"
                            class="btn-export white-btn">

                            <i class="fa-regular fa-file-pdf"></i>

                            <span>Export<br />PDF</span>
                       </a>
                        <a href="{{ route('admin.ticketreport.export.excel', request()->query()) }}"
                            class="btn-export blue-btn">

                            <i class="fa-regular fa-file-excel"></i>

                            <span>Export<br />Excel</span>
                        </a>
                    </div>
                </div>

               
                <div class="tickets-card">

              
                    <div class="table-header-box">
                        <!-- <h3>Activity Detail Log</h3> -->

                        <form action="{{ route('admin.ticketreport') }}" method="GET" class="tr-controls" id="filterForm">
                            @if(request('search'))
                                <input type="hidden" name="search" value="{{ request('search') }}">
                            @endif

                            {{-- ── Report Type Selector ─────────────────────────── --}}
                            <div style="display: flex; align-items: center; gap: 0.5rem; border: 1px solid #e5e7eb; border-radius: 6px; background: #f9fafb; padding: 0.5rem 1rem;">
                                <i class="fa-solid fa-chart-bar" style="color:#6b7280;"></i>
                                <select name="report_type" id="reportTypeSelect"
                                    onchange="handleReportTypeChange()"
                                    style="border: none; background: transparent; font-size: 0.85rem; outline: none; color: #374151; font-weight: 600; cursor: pointer;">
                                    <option value="custom"   {{ request('report_type','custom') === 'custom'  ? 'selected' : '' }}>Select type of report</option>
                                    <option value="daily"    {{ request('report_type') === 'daily'   ? 'selected' : '' }}>Daily</option>
                                    <option value="weekly"   {{ request('report_type') === 'weekly'  ? 'selected' : '' }}>Weekly</option>
                                    <option value="monthly"  {{ request('report_type') === 'monthly' ? 'selected' : '' }}>Monthly</option>
                                    <option value="custom"   {{ request('report_type') === 'custom'  ? 'selected' : '' }}>Date Range</option>
                                </select>
                            </div>

                            {{-- ── Daily picker ──────────────────────────────────── --}}
                            <div id="picker-daily" class="tr-date-btn" style="display:none; padding: 0.5rem 1rem; align-items: center; gap: 0.5rem; border: 1px solid #e5e7eb; border-radius: 6px; background: #f9fafb;">
                                <i class="fa-regular fa-calendar" style="color:#6b7280;"></i>
                                <input type="date" name="daily_date"
                                    value="{{ request('daily_date') }}"
                                    onchange="document.getElementById('filterForm').submit()"
                                    style="border: none; background: transparent; font-size: 0.85rem; outline: none; color: #374151;">
                            </div>

                            {{-- ── Weekly picker ─────────────────────────────────── --}}
                            <div id="picker-weekly" class="tr-date-btn" style="display:none; padding: 0.5rem 1rem; align-items: center; gap: 0.5rem; border: 1px solid #e5e7eb; border-radius: 6px; background: #f9fafb;">
                                <i class="fa-regular fa-calendar-week" style="color:#6b7280;"></i>
                                <input type="week" name="weekly_date"
                                    value="{{ request('weekly_date') }}"
                                    onchange="document.getElementById('filterForm').submit()"
                                    style="border: none; background: transparent; font-size: 0.85rem; outline: none; color: #374151;">
                            </div>

                            {{-- ── Monthly picker ────────────────────────────────── --}}
                            <div id="picker-monthly" class="tr-date-btn" style="display:none; padding: 0.5rem 1rem; align-items: center; gap: 0.5rem; border: 1px solid #e5e7eb; border-radius: 6px; background: #f9fafb;">
                                <i class="fa-regular fa-calendar-days" style="color:#6b7280;"></i>
                                <input type="month" name="monthly_date"
                                    value="{{ request('monthly_date') }}"
                                    onchange="document.getElementById('filterForm').submit()"
                                    style="border: none; background: transparent; font-size: 0.85rem; outline: none; color: #374151;">
                            </div>

                            {{-- ── Date Range picker ─────────────────────────────── --}}
                            <div id="picker-custom" class="tr-date-btn" style="display:none; padding: 0.5rem 1rem; align-items: center; gap: 0.5rem; border: 1px solid #e5e7eb; border-radius: 6px; background: #f9fafb;">
                                <i class="fa-regular fa-calendar" style="color:#6b7280;"></i>
                                <input type="date" name="start_date" value="{{ request('start_date') }}" onchange="document.getElementById('filterForm').submit()" style="border: none; background: transparent; font-size: 0.85rem; outline: none; color: #374151;">
                                <span style="color: #6b7280;">to</span>
                                <input type="date" name="end_date"   value="{{ request('end_date') }}"   onchange="document.getElementById('filterForm').submit()" style="border: none; background: transparent; font-size: 0.85rem; outline: none; color: #374151;">
                            </div>

                            {{-- Status Filter --}}
                            <div style="position: relative; display: inline-block;">
                                <select name="status" onchange="document.getElementById('filterForm').submit()" style="padding: 0.5rem 2.5rem 0.5rem 1rem; border-radius: 6px; border: 1px solid #e5e7eb; font-size: 0.85rem; font-weight: 600; color: #374151; background: #f9fafb; cursor: pointer; outline: none; appearance: none; transition: all 0.2s;">
                                    <option value="">All Statuses</option>
                                    <option value="open"        {{ request('status') == 'open'        ? 'selected' : '' }}>Open</option>
                                    <option value="assigned"    {{ request('status') == 'assigned'    ? 'selected' : '' }}>Assigned</option>
                                    <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                    <option value="resolved"    {{ request('status') == 'resolved'    ? 'selected' : '' }}>Resolved</option>
                                    <option value="closed"      {{ request('status') == 'closed'      ? 'selected' : '' }}>Closed</option>
                                    <option value="overdue"     {{ request('status') == 'overdue'     ? 'selected' : '' }}>Overdue</option>
                                    <!-- @if(isset($technicians) && $technicians->count() > 0)
                                        <optgroup label="Technicians">
                                            @foreach($technicians as $tech)
                                                <option value="tech_{{ $tech->id }}" {{ request('status') == 'tech_'.$tech->id ? 'selected' : '' }}>{{ $tech->name }}</option>
                                            @endforeach
                                        </optgroup>
                                    @endif -->
                                </select>
                                <i class="fa-solid fa-chevron-down" style="position: absolute; right: 0.8rem; top: 50%; transform: translateY(-50%); pointer-events: none; color: #6b7280; font-size: 0.7rem;"></i>
                            </div>
                            <div style="position: relative; display: inline-block;">
                                <select name="status" onchange="document.getElementById('filterForm').submit()" style="padding: 0.5rem 2.5rem 0.5rem 1rem; border-radius: 6px; border: 1px solid #e5e7eb; font-size: 0.85rem; font-weight: 600; color: #374151; background: #f9fafb; cursor: pointer; outline: none; appearance: none; transition: all 0.2s;">
                                    @if(isset($technicians) && $technicians->count() > 0)
                                        <option value="">Technicians</option>
                                            @foreach($technicians as $tech)
                                                <option value="tech_{{ $tech->id }}" {{ request('status') == 'tech_'.$tech->id ? 'selected' : '' }}>{{ $tech->name }}</option>
                                            @endforeach
                                     
                                    @endif
                                </select>
                                <i class="fa-solid fa-chevron-down" style="position: absolute; right: 0.8rem; top: 50%; transform: translateY(-50%); pointer-events: none; color: #6b7280; font-size: 0.7rem;"></i>
                            </div>
                        </form>

                        <!-- {{-- Column filter toggle icon --}}
                        <button class="tr-icon-btn">
                            <i class="fa-solid fa-sliders"></i>
                        </button> -->

                        <!-- {{-- Row count selector: controls how many rows to display per page --}}
                        <div class="tr-rows-selector">
                            <span>Show</span>
                            <select class="tr-rows-select">
                                <option>10 rows</option>
                                <option selected>25 rows</option>
                                <option>50 rows</option>
                                <option>100 rows</option>
                            </select>
                            <i class="fa-solid fa-chevron-down" style="font-size: 0.75rem;"></i>
                        </div> -->

                    </div>

                    {{-- Ticket activity data table --}}
                    <table class="tickets-table">
                        <thead>
                            <tr>
                                <th>TICKET ID</th>       {{-- Unique ticket reference number --}}
                                <th>SUBJECT</th>          {{-- Ticket title and short description --}}
                                <th>TIME CREATED</th>     {{-- Time the ticket was created --}}
                                <th>ASSIGNEE</th>         {{-- Assigned technician --}}
                                <th>SLA PRIORITY</th>     {{-- SLA compliance level --}}
                                <th>STATUS</th>           {{-- Current resolution status --}}
                                <th class="action-col">ACTION</th> {{-- Link to full ticket details --}}
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($tickets as $ticket)
                                @php
                                    $isOverdue = $ticket->due_at && \Carbon\Carbon::parse($ticket->due_at)->isPast() && !in_array($ticket->status, ['resolved', 'closed']);
                                @endphp
                                <tr>
                                    <td><a href="{{ route('admin.tickets.details', $ticket->id) }}" class="ticket-id-link">{{ $ticket->ticket_id }}</a></td>
                                    <td>
                                        <div class="ticket-subject">
                                            <strong>{{ $ticket->subject }}</strong>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="ticket-subject">
                                            <span>{{ $ticket->created_at->format('M d, Y h:i A') }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="user-info">
                                            <div class="ticket-subject">
                                                <strong>{{ optional($ticket->technician)->name ?? 'Unassigned' }}</strong>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        @if($isOverdue)
                                            <span class="ticket-status status-inprogress">Overdue</span>
                                        @else
                                            <span class="ticket-status status-resolved">Within SLA</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="ticket-status status-{{ str_replace(' ', '', strtolower($ticket->status)) }}">
                                            {{ ucwords(str_replace('_', ' ', $ticket->status)) }}
                                        </span>
                                    </td>
                                    <td class="action-col">
                                        <a href="{{ route('admin.tickets.details', $ticket->id) }}" class="ticket-actions">
                                            View<br />More
                                            <!-- <i class="fa-solid fa-arrow-right" style="font-size: 0.75rem; margin-left: 4px;"></i> -->
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" style="text-align: center; padding: 2rem;">No tickets found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    <div class="pagination-container">
                        <div>
                            Showing {{ $tickets->firstItem() ?? 0 }} to {{ $tickets->lastItem() ?? 0 }} of <strong>{{ $tickets->total() }}</strong> tickets
                        </div>
                        <div class="pagination-links">
                            {{ $tickets->links('pagination::bootstrap-4') }}
                        </div>
                    </div>

                </div>

            </div>

        </main>

    </div>

<script>
function handleReportTypeChange() {
    const type = document.getElementById('reportTypeSelect').value;

    // Hide all pickers first
    document.querySelectorAll('[id^="picker-"]').forEach(el => {
        el.style.display = 'none';
    });

    // Show the relevant picker
    const picker = document.getElementById('picker-' + type);
    if (picker) {
        picker.style.display = 'flex';
    }
}

// On page load, restore the correct picker based on current selection
document.addEventListener('DOMContentLoaded', function() {
    handleReportTypeChange();
});
</script>

</body>

</html>
