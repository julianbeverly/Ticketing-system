<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="/dist/css/style.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
  <title>Technician Tickets</title>
  <style>
    .ticket-subject {
      max-width: 300px;
      white-space: normal;
      word-wrap: break-word;
      overflow-wrap: break-word;
      word-break: break-all; /* Ensures long continuous text wraps */
      line-height: 1.5;
    }
    .view-more-btn {
      background-color: #111111;
      color: #ffffff !important;
      padding: 10px 20px;
      border-radius: 20px;
      font-weight: 800;
      text-decoration: none;
      display: inline-block;
      transition: background 0.2s;
      white-space: nowrap;
      text-transform: uppercase;
      font-size: 0.6rem;
      box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }
    .view-more-btn:hover {
      /* background-color: #0842a0;
      transform: translateY(-1px); */
    }
    .tickets-table th {
      white-space: nowrap;
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
  </style>
</head>
<body>
  <div class="dashboard-layout">
        @include('techdashboard.partials.sidebar')

    <main class="main-content">
      <header class="top-header">
        <button class="menu-toggle" id="menuToggle">
          <i class="fa-solid fa-bars"></i>
        </button>
          <form method="GET" action="{{ route('tech.tickets') }}" style="flex: 1; max-width: 500px; display: flex; align-items: center; margin: 0 1.5rem;">
            @if(request('status'))
              <input type="hidden" name="status" value="{{ request('status') }}">
            @endif
            <div class="search-container" style="width: 100%; margin: 0;">
              <i class="fa-solid fa-magnifying-glass" onclick="this.closest('form').submit();" style="cursor: pointer;"></i>
              <input type="text" name="search" value="{{ request('search') }}" placeholder="Search directives, tickets, or users..." />
            </div>
          </form>
                <div class="header-actions">
                    
                    <a href="{{ route('employee.profile') }}" class="icon-btn profile-btn">
                        <i class="fa-regular fa-circle-user"></i>
                    </a>
                </div>
      </header>

      <div class="dashboard-content">
        <div class="breadcrumb">
          <a href="#">Ticket Management</a> &gt; <span>Tickets</span>
        </div>

        <div class="page-header-title">
          <div>
            <h1>Assigned Tickets</h1>
            <p>Manage and monitor all support requests assigned to you.</p>
          </div>
        </div>

        <div class="filter-section">
          <!-- <span class="filter-label">FILTER BY STATUS</span> -->
          <form action="{{ route('tech.tickets') }}" method="GET" id="statusFilterForm">
            @if(request('search'))
              <input type="hidden" name="search" value="{{ request('search') }}">
            @endif
            <div class="modern-select-wrapper" style="position: relative; display: inline-block; min-width: 200px;">
              <select name="status" onchange="this.form.submit()" class="modern-select" style="background: white; border: 1px solid #d1d5db; padding: 0.5rem 2.5rem 0.5rem 1rem; font-size: 0.9rem; font-weight: 600; color: #374151; border-radius: 6px; cursor: pointer; appearance: none; outline: none; width: 100%;">
                <option value="">All Statuses</option>
                <option value="open" {{ request('status') == 'open' ? 'selected' : '' }}>Open</option>
                <option value="assigned" {{ request('status') == 'assigned' ? 'selected' : '' }}>Assigned</option>
                <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                <option value="resolved" {{ request('status') == 'resolved' ? 'selected' : '' }}>Resolved</option>
                <option value="closed" {{ request('status') == 'closed' ? 'selected' : '' }}>Closed</option>
                <option value="overdue" {{ request('status') == 'overdue' ? 'selected' : '' }}>Overdue</option>
              </select>
              <i class="fa-solid fa-chevron-down" style="position: absolute; right: 1rem; top: 50%; transform: translateY(-50%); pointer-events: none; color: #6b7280; font-size: 0.8rem;"></i>
            </div>
          </form>
        </div>

        @if(session()->has('success'))
        <div style="background-color: #d4edda; color: #155724; padding: 12px 20px; border-radius: 4px; margin-bottom: 20px; border: 1px solid #c3e6cb;">
          {{ session()->get('success') }}
        </div>
        @endif

        <div class="tickets-card">
          <table class="tickets-table">
            <thead>
              <tr>
                <th>TICKET ID</th>
                <th>SUBJECT</th>
                <th>EMPLOYEE</th>
                <th>PRIORITY</th>
                <th>STATUS</th>
                <th>DATE CREATED</th>
                <th>ACTIONS</th>
              </tr>
            </thead>
            <tbody>
              @forelse($tickets as $ticket)
              <tr>
                <td>
                  <div style="background: ; color: #111111; padding: 0.5rem; border-radius: 4px; font-weight: bold; text-align: center; width: 60px;">
                    {{ str_replace('-', "- ", $ticket->ticket_id) }}
                  </div>
                </td>
                <td>
                  <div class="ticket-subject"><strong>{{ $ticket->subject }}</strong></div>
                </td>
                <td>
                  <div class="ticket-personnel">
                    <div>
                      <i class="fa-solid fa-user"></i>
                      <span class="name">{{ $ticket->user->name }}</span>
                    </div>
                  </div>
                </td>
                <td>
                  <span class="ticket-priority priority-{{ $ticket->priority }}">{{ strtoupper($ticket->priority) }}</span>
                </td>
                <td>
                  <span class="ticket-status status-{{ str_replace('_', '', $ticket->status) }}">{{ strtoupper(str_replace('_', ' ', $ticket->status)) }}</span>
                </td>
                <td>
                  <div class="ticket-date">{{ $ticket->created_at->format('M d, Y') }}</div>
                </td>
                <td>
                  <a href="{{ route('tech.tickets.details', $ticket->id) }}" class="view-more-btn">View More</a>
                </td>
              </tr>
              @empty
              <tr>
                <td colspan="7" style="text-align: center; padding: 2rem; color: #6b7280;">No tickets assigned to you yet.</td>
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

</body>
</html>
