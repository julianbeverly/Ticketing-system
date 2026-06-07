<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Technician Detailed Performance</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
  <link rel="stylesheet" href="/dist/css/style.css" />
</head>
<body>
  <div class="dashboard-layout">
        @include('admin.partials.sidebar')

    <main class="main-content">
      <header class="top-header">
        <button class="menu-toggle" id="menuToggle">
          <i class="fa-solid fa-bars"></i>
        </button>
        <div style="flex: 1;"></div>
        <div class="header-actions">
          
          <a href="{{ route('employee.profile') }}" class="icon-btn profile-btn">
            <i class="fa-regular fa-circle-user"></i>
          </a>
        </div>
      </header>

      <div class="dashboard-content">
        <div class="breadcrumb">
          <a href="{{ route('tech.reporting') }}">REPORTS</a> /
          <span>TECHNICIAN DETAILS</span>
        </div>

        <div class="page-header-title">
          <div>
            <h1>{{ $user->name }} - Detailed Performance</h1>
          </div>
        </div>

        <div class="tickets-card">
          <div class="table-header-box">
            <h3>Technician Performance Metrics</h3>
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
                <td>{{ $user->email }}</td>
                <td>{{ $user->phone ?? 'N/A' }}</td>
                <td>
                    <span style="color: {{ $overdueCount > 0 ? '#dc2626' : '#059669' }}; font-weight: bold;">
                        {{ $overdueCount }}
                    </span>
                </td>
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

        <div class="tickets-card mt-4" style="margin-top: 2rem;">
          <div class="table-header-box">
            <h3>Assigned Tickets</h3>
          </div>

          <table class="tickets-table">
            <thead>
              <tr>
                <th>TICKET ID</th>
                <th>SUBJECT</th>
                <th>CLASSIFICATION</th>
                <th>EMPLOYEE</th>
                <th>SLA PRIORITY</th>
                <th>STATUS</th>
              </tr>
            </thead>
            <tbody>
              @forelse($tickets as $ticket)
                @php
                    $isOverdue = $ticket->due_at && now()->greaterThan($ticket->due_at) && !in_array($ticket->status, ['resolved', 'closed']);
                @endphp
                <tr>
                  <td style="white-space: nowrap;">
                    <a href="{{ route('admin.tickets.details', $ticket->id) }}" style="color: #2563eb; text-decoration: none; font-weight: bold;">
                      {{ $ticket->ticket_id }}
                    </a>
                  </td>
                  <td style="font-weight: bold;">{{ $ticket->subject }}</td>
                  <td>
                    <div style="display: flex; flex-direction: column; color: #64748b; font-size: 0.9rem;">
                      <span>{{ optional($ticket->category)->name }}</span>
                      <span>{{ optional($ticket->type)->name ?? $ticket->custom_type }}</span>
                    </div>
                  </td>
                  <td style="font-weight: bold;">{{ optional($ticket->user)->name ?? 'Unknown' }}</td>
                  <td style="white-space: nowrap;">
                    @if($isOverdue || $ticket->status === 'overdue')
                      <span style="background: #ffedd5; color: #c2410c; padding: 4px 12px; border-radius: 12px; font-size: 0.75rem; font-weight: bold; white-space: nowrap;">OVERDUE</span>
                    @else
                      <span style="background: #dcfce7; color: #166534; padding: 4px 12px; border-radius: 12px; font-size: 0.75rem; font-weight: bold; white-space: nowrap;">WITHIN SLA</span>
                    @endif
                  </td>
                  <td style="white-space: nowrap;">
                    @php
                        $statusClass = '';
                        $statusText = strtoupper(str_replace('_', ' ', $ticket->status));
                        if($ticket->status === 'open') $statusClass = 'background: #e0e7ff; color: #3730a3;';
                        elseif($ticket->status === 'assigned') $statusClass = 'background: #e0e7ff; color: #3730a3;';
                        elseif($ticket->status === 'in_progress') $statusClass = 'background: #fef3c7; color: #92400e;';
                        elseif($ticket->status === 'resolved') $statusClass = 'background: #dcfce7; color: #166534;';
                        elseif($ticket->status === 'closed') $statusClass = 'font-weight: bold; color: #000;';
                        elseif($ticket->status === 'overdue') $statusClass = 'background: #fee2e2; color: #dc2626;';
                    @endphp
                    <span style="padding: 4px 12px; border-radius: 12px; font-size: 0.75rem; font-weight: bold; {{ $statusClass }}">
                        {{ $statusText }}
                    </span>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="6" style="text-align: center; color: #64748b; padding: 20px;">No tickets assigned to this technician.</td>
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
