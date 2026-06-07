<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
     <link rel="stylesheet" href="/dist/css/style.css" />
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
    />
    <style>
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
    <title>My Tickets</title>
</head>
<body>
    <div class="dashboard-layout">
        @include('employee.partials.sidebar')

    <!-- The main container  -->
      <main class="main-content">
        <header class="top-header">
        <button class="menu-toggle" id="menuToggle">
          <i class="fa-solid fa-bars"></i>
        </button>
          <form method="GET" action="{{ route('employee.tickets') }}" style="flex: 1; max-width: 500px; display: flex; align-items: center; margin: 0 1.5rem;">
            @if(request('status'))
              <input type="hidden" name="status" value="{{ request('status') }}">
            @endif
            <div class="search-container" style="width: 100%; margin: 0;">
              <i class="fa-solid fa-magnifying-glass" onclick="this.closest('form').submit();" style="cursor: pointer;"></i>
              <!-- Text input -->
              <input
                type="text"
                name="search"
                value="{{ request('search') }}"
                placeholder="Search directives, tickets, or users..."
              />
            </div>
          </form>
                <div class="header-actions">
                    
                    <a href="{{ route('employee.profile') }}" class="icon-btn profile-btn">
                        <i class="fa-regular fa-circle-user"></i>
                    </a>
                </div>
        </header>

        <div class="dashboard-content">

          <!-- Success Flash Message -->
          @if(session('success'))
            <div style="background: #d4edda; color: #155724; padding: 1rem; border-radius: 8px; margin-bottom: 1rem; border: 1px solid #c3e6cb;">
              <i class="fa-solid fa-circle-check" style="margin-right: 0.5rem;"></i>
              {{ session('success') }}
            </div>
          @endif

          <div class="breadcrumb">
            <!-- Link to previous Operations page -->
            <a href="#">Ticket Management</a> &gt;
            <!-- Text showing current specific page location -->
            <span>Tickets</span>
          </div>

          <div class="page-header-title">
            <div>
              <h1>System Tickets</h1>
              <p>
                Manage and monitor all active support requests across the
                enterprise.
              </p>
            </div>
            <!-- Create Ticket Link the user to createticket page -->
            <a
              href="{{ route('employee.tickets.create') }}"
              class="btn-create-ticket"
              style="text-decoration: none"
            >
              <!-- Pen icon indicating writing -->
              <i class="fa-solid fa-pen-to-square"></i> Create Ticket
            </a>
          </div>

          <div class="filter-section">
            <span class="filter-label">FILTER BY STATUS</span>
            <form action="{{ route('employee.tickets') }}" method="GET" id="statusFilterForm">
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

          <!-- the main data table -->
          <div class="tickets-card">
            <table class="tickets-table">
              <!-- Table header containing column names -->
              <thead>
                <tr>
                  <th>TICKET ID</th>
                  <th>SUBJECT</th>
                  <th>CATEGORY</th>
                  <th>PRIORITY</th>
                  <th>STATUS</th>
                  <th>DATE CREATED</th>
                  <th>ACTIONS</th>
                </tr>
              </thead>
              <tbody>
                <!-- Dynamically loop through tickets from the database -->
                @forelse($tickets as $ticket)
                <tr>
                  <!-- Ticket ID column -->
                  <td>
                    <span class="ticket-id">{{ $ticket->ticket_id }}</span>
                  </td>

                  <!-- Subject column with main subject and category sub-text -->
                  <td>
                    <div class="ticket-subject">
                      <strong>{{ $ticket->subject }}</strong>
                      <span>{{ $ticket->custom_type ?? ($ticket->type ? $ticket->type->name : '') }}</span>
                    </div>
                  </td>

                  <!-- Category column with icon -->
                  <td>
                    <div class="ticket-category">
                      <i class="fa-solid fa-tag"></i>
                      {{ $ticket->category ? $ticket->category->name : 'N/A' }}
                    </div>
                  </td>

                  <!-- Priority badge with dynamic color class -->
                  <td>
                    <span class="ticket-priority priority-{{ $ticket->priority }}">
                      {{ strtoupper($ticket->priority) }}
                    </span>
                  </td>

                  <!-- Status badge with dynamic color class -->
                  <td>
                    <span class="ticket-status status-{{ str_replace(' ', '', $ticket->status) }}">
                      {{ strtoupper(str_replace('_', ' ', $ticket->status)) }}
                    </span>
                  </td>

                  <!-- Date created, formatted nicely -->
                  <td>
                    <div class="ticket-date">
                      {{ $ticket->created_at->format('M d, Y') }} -<br />
                      {{ $ticket->created_at->format('h:i A') }}
                    </div>
                  </td>

                  <!-- View More link to ticket details -->
                  <td>
                    <a href="{{ route('employee.tickets.details', $ticket->id) }}" class="ticket-actions">
                      View<br />More
                    </a>
                  </td>
                </tr>
                @empty
                <!-- Show a message when no tickets exist yet -->
                <tr>
                  <td colspan="7" style="text-align: center; padding: 2rem; color: #6b7280;">
                    <i class="fa-solid fa-ticket" style="font-size: 2rem; margin-bottom: 0.5rem; display: block;"></i>
                    No tickets found. <a href="{{ route('employee.tickets.create') }}" style="color: #0b57d0;">Create your first ticket</a>.
                  </td>
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
