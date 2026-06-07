<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>My Performance Details - Resolve</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
  <link rel="stylesheet" href="/dist/css/style.css" />
</head>
<body>
  <div class="dashboard-layout">

    <!-- ===== Technician Sidebar ===== -->
        @include('techdashboard.partials.sidebar')

    <!-- ===== Main Content ===== -->
    <main class="main-content">
      <header class="top-header">
        <button class="menu-toggle" id="menuToggle">
          <i class="fa-solid fa-bars"></i>
        </button>
        <form method="GET" action="{{ route('tech.own.report.details') }}" style="flex: 1; max-width: 500px; display: flex; align-items: center; margin: 0 1.5rem;">
          @if(request('report_type')) <input type="hidden" name="report_type" value="{{ request('report_type') }}"> @endif
          @if(request('daily_date')) <input type="hidden" name="daily_date" value="{{ request('daily_date') }}"> @endif
          @if(request('weekly_date')) <input type="hidden" name="weekly_date" value="{{ request('weekly_date') }}"> @endif
          @if(request('monthly_date')) <input type="hidden" name="monthly_date" value="{{ request('monthly_date') }}"> @endif
          @if(request('start_date')) <input type="hidden" name="start_date" value="{{ request('start_date') }}"> @endif
          @if(request('end_date')) <input type="hidden" name="end_date" value="{{ request('end_date') }}"> @endif
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
        <!-- Breadcrumb back to own report -->
        <div class="breadcrumb">
          <a href="{{ route('tech.own.report') }}">REPORTS</a> /
          <span>MY PERFORMANCE DETAILS</span>
        </div>

        <div class="page-header-title">
          <div>
            <h1>{{ $user->name }} - My Performance Details</h1>
          </div>
        </div>

        <!-- Detail Table -->
        <div class="tickets-card">
          <div class="table-header-box">
            <h3>My Performance Metrics</h3>
          </div>

          <table class="tickets-table">
            <thead>
              <tr>
                <th>TECHNICIAN EMAIL</th>
                <th>PHONE NUMBER</th>
                <th>TOTAL OVERDUE TICKETS</th>
                <th>SLA PERFORMANCE (%)</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                {{-- Email --}}
                <td>{{ $user->email }}</td>

                {{-- Phone number, fallback to N/A --}}
                <td>{{ $user->phone ?? 'N/A' }}</td>

                {{-- Overdue count coloured red if > 0, green if 0 --}}
                <td>
                  <span style="color: {{ $overdueCount > 0 ? '#dc2626' : '#059669' }}; font-weight: bold;">
                    {{ $overdueCount }}
                  </span>
                </td>

                {{-- SLA performance with colour-coded progress bar --}}
                <td>
                  <div style="display: flex; align-items: center; gap: 10px;">
                    <span style="font-weight: bold; color: {{ $slaPerformance >= 90 ? '#059669' : ($slaPerformance >= 70 ? '#d97706' : '#dc2626') }};">
                      {{ $slaPerformance }}%
                    </span>
                    <div style="width: 100px; height: 8px; background: #e5e7eb; border-radius: 4px; overflow: hidden;">
                      <div style="width: {{ $slaPerformance }}%; height: 100%; background: {{ $slaPerformance >= 90 ? '#059669' : ($slaPerformance >= 70 ? '#d97706' : '#dc2626') }};"></div>
                    </div>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Assigned Tickets with Date Filter -->
        <div class="tickets-card" style="margin-top: 2rem;">
          <div class="table-header-box">
            <h3>My Assigned Tickets</h3>
          </div>

          <table class="tickets-table">
            <thead>
              <tr>
                <th>TICKET ID</th>
                <th>SUBJECT</th>
                <th>CLASSIFICATION</th>
                <th>SLA PRIORITY</th>
                <th>STATUS</th>
              </tr>
            </thead>
            <tbody>
              @forelse($tickets as $ticket)
                @php
                    $isOverdue = $ticket->due_at && now()->greaterThan($ticket->due_at) && !in_array($ticket->status, ['resolved', 'closed']);
                    $statusText = strtoupper(str_replace('_', ' ', $ticket->status));
                    if($ticket->status === 'open') $statusClass = 'background:#e0e7ff; color:#3730a3;';
                    elseif($ticket->status === 'assigned') $statusClass = 'background:#e0e7ff; color:#3730a3;';
                    elseif($ticket->status === 'in_progress') $statusClass = 'background:#fef3c7; color:#92400e;';
                    elseif($ticket->status === 'resolved') $statusClass = 'background:#dcfce7; color:#166534;';
                    elseif($ticket->status === 'closed') $statusClass = 'font-weight:bold; color:#000;';
                    elseif($ticket->status === 'overdue') $statusClass = 'background:#fee2e2; color:#dc2626;';
                    else $statusClass = '';
                @endphp
                <tr>
                  <td style="white-space:nowrap;">
                    <a href="{{ route('tech.tickets.details', $ticket->id) }}" style="color:#2563eb; text-decoration:none; font-weight:bold;">
                      {{ $ticket->ticket_id }}
                    </a>
                  </td>
                  <td style="font-weight:bold;">{{ $ticket->subject }}</td>
                  <td>
                    <div style="display:flex; flex-direction:column; color:#64748b; font-size:0.9rem;">
                      <span>{{ optional($ticket->category)->name }}</span>
                      <span>{{ optional($ticket->type)->name ?? $ticket->custom_type }}</span>
                    </div>
                  </td>
                  <td style="white-space:nowrap;">
                    @if($isOverdue || $ticket->status === 'overdue')
                      <span style="background:#ffedd5; color:#c2410c; padding:4px 12px; border-radius:12px; font-size:0.75rem; font-weight:bold; white-space:nowrap;">OVERDUE</span>
                    @else
                      <span style="background:#dcfce7; color:#166534; padding:4px 12px; border-radius:12px; font-size:0.75rem; font-weight:bold; white-space:nowrap;">WITHIN SLA</span>
                    @endif
                  </td>
                  <td style="white-space:nowrap;">
                    <span style="padding:4px 12px; border-radius:12px; font-size:0.75rem; font-weight:bold; {{ $statusClass }}">
                      {{ $statusText }}
                    </span>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="5" style="text-align:center; color:#64748b; padding:20px;">No tickets found for this period.</td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </main>
  </div>

</body>
</html>

