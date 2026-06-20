<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>System Tickets</title>
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
    />
    <link rel="stylesheet" href="/dist/css/style.css" />
    <style>
      .ticket-subject {
        max-width: 150px; /* Reduced from 220px */
        white-space: normal;
        word-wrap: break-word;
        overflow-wrap: anywhere;
        word-break: break-all;
        line-height: 1.3;
        display: block;
        font-size: 0.85rem;
      }
      .admin-actions-container {
        display: flex;
        flex-direction: column;
        gap: 4px;
        width: 100%;
      }
      .btn-action-sm {
        padding: 6px 8px;
        font-size: 0.7rem;
        border-radius: 4px;
        font-weight: 800;
        text-decoration: none;
        text-align: center;
        display: block;
        white-space: nowrap;
        text-transform: uppercase;
        letter-spacing: 0.3px;
        transition: all 0.2s;
      }
      .btn-assign-blue {
        background: #095ce4ff;
        color: white !important;
        border: none;
        cursor: pointer;
        /* box-shadow: 0 2px 4px rgba(0,0,0,0.1); */
      }
      .btn-assign-blue:hover {
        background: #002266;
        transform: translateY(-1px);
      }
      .btn-view-light {
        background: #f8fafc;
        color: #1e40af;
        border: 1px solid #e2e8f0;
      }
      .btn-view-light:hover {
        background: #f1f5f9;
        border-color: #cbd5e1;
      }
      .tickets-table th {
        white-space: nowrap;
        padding: 12px 16px;
        text-align: left;
        font-size: 0.75rem;
      }
      .tickets-table td {
        padding: 12px 16px;
        font-size: 0.875rem;
      }
      .tickets-table th:last-child, .tickets-table td:last-child {
        text-align: center;
        padding-right: 16px;
      }
      .tickets-card {
        width: 100%;
        overflow-x: auto;
        background: white;
        border-radius: 12px;
        scrollbar-width: none;
      }
      .tickets-card::-webkit-scrollbar { display: none; }
      .tickets-table {
        width: 100%;
        border-collapse: collapse;
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
        @include('admin.partials.sidebar')

      <main class="main-content">
        <header class="top-header">
          <button class="menu-toggle" id="menuToggle">
            <i class="fa-solid fa-bars"></i>
          </button>
          <form method="GET" action="{{ route('admin.tickets') }}" style="flex: 1; max-width: 500px; display: flex; align-items: center; margin: 0 1.5rem;">
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
              <h1>System Tickets</h1>
              <p>Manage and monitor all active support requests across the enterprise.</p>
            </div>
            <a href="{{ route('admin.tickets.create') }}" class="btn-create-ticket" style="text-decoration: none">
              <i class="fa-solid fa-pen-to-square"></i> Create Ticket
            </a>
          </div>
          
          <div class="filter-section" style="padding: 0 0 1.5rem 0;">
            <form action="{{ route('admin.tickets') }}" method="GET" id="statusFilterForm" style="display: flex; align-items: center; gap: 1rem; flex-wrap: wrap;">
                <select name="status" onchange="this.form.submit()" style="padding: 0.5rem 1rem; border-radius: 6px; border: 1px solid #e5e7eb; font-size: 0.85rem; font-weight: 600; color: #374151; background: #f9fafb; cursor: pointer; outline: none; min-width: 180px;">
                    <option value="">All Statuses</option>
                    <option value="open" {{ request('status') == 'open' ? 'selected' : '' }}>Open</option>
                    <option value="assigned" {{ request('status') == 'assigned' ? 'selected' : '' }}>Assigned</option>
                    <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                    <option value="resolved" {{ request('status') == 'resolved' ? 'selected' : '' }}>Resolved</option>
                    <option value="closed" {{ request('status') == 'closed' ? 'selected' : '' }}>Closed</option>
                    <option value="overdue" {{ request('status') == 'overdue' ? 'selected' : '' }}>Overdue</option>
                </select>

                <select name="company" onchange="this.form.submit()" style="padding: 0.5rem 1rem; border-radius: 6px; border: 1px solid #e5e7eb; font-size: 0.85rem; font-weight: 600; color: #374151; background: #f9fafb; cursor: pointer; outline: none; min-width: 180px;">
                    <option value="">All Companies</option>
                    @foreach($companies as $company)
                    <option value="{{ $company->id }}" {{ request('company') == $company->id ? 'selected' : '' }}>{{ $company->name }}</option>
                    @endforeach
                </select>
            </form>
          </div>

          @if(session()->has('success'))
          <div id="successAlert" style="background-color: #d4edda; color: #155724; padding: 12px 20px; border-radius: 4px; margin-bottom: 20px; border: 1px solid #c3e6cb;">
            {{ session()->get('success') }}
          </div>
          @endif
          
          <div id="jsSuccessAlert" style="display: none; background-color: #d4edda; color: #155724; padding: 12px 20px; border-radius: 4px; margin-bottom: 20px; border: 1px solid #c3e6cb;">
          </div>

          <div class="tickets-card">
            <table class="tickets-table">
              <thead>
                <tr>
                  <th>TICKET ID</th>
                  <th>SUBJECT</th>
                  <th>PERSONNEL</th>
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
                    <div style="background: ; color: ; padding: 0.5rem; border-radius: 4px; font-weight: bold; text-align: center; width: 60px;">
                      {{ str_replace('-', "- ", $ticket->ticket_id) }}
                    </div>
                  </td>
                  <td>
                    <div class="ticket-subject"><strong>{{ $ticket->subject }}</strong></div>
                  </td>
                  <td>
                    <div class="ticket-personnel" style="font-size: 0.9rem;">
                      <div style="margin-bottom: 2px;">
                        <span style="color: #111111; font-weight: bold;">E:</span> 
                        <span style="color: #111111;">{{ $ticket->user ? $ticket->user->name : 'Unknown' }}</span>
                      </div>
                      <div>
                        <span style="color: #111111; font-weight: bold;">T:</span> 
                        @if($ticket->technician)
                          <span style="color: #111111;">{{ $ticket->technician->name }}</span>
                        @else
                          <span style="color: #ef4444;">Unassigned</span>
                        @endif
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
                    <div class="admin-actions-container">
                      <button style="background-color: #111111;   border-radius: 20px; border: 2px solid #111111;"
                        class="btn-action-sm btn-assign-blue assign-btn-modal" 
                        data-ticket-id="{{ $ticket->id }}"
                        data-display-id="{{ $ticket->ticket_id }}"
                        data-subject="{{ $ticket->subject }}"
                      >
                        <i class="fa-solid fa-user-plus"></i> Assign
                      </button>
                      <a href="{{ route('admin.tickets.details', $ticket->id) }}" class="btn-action-sm btn-view-light" style="background-color: #FBBF24 ;   border-radius: 20px; border: 1px solid #111111; color: #ffff">
                        </i> View More
                      </a>
                    </div>
                  </td>
                </tr>
                @empty
                <tr>
                  <td colspan="7" style="text-align: center; padding: 2rem; color: #6b7280;">
                    <i class="fa-solid fa-ticket" style="font-size: 2rem; margin-bottom: 0.5rem; display: block;"></i>
                    No tickets found in the system yet.
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

    <!-- Assignment Modal -->
<div id="assignModal" class="modal-overlay" style="display: none; position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background: rgba(0,0,0,0.6); z-index: 10000; justify-content: center; align-items: center; backdrop-filter: blur(4px);">
    <div class="assign-modal-box" style="background: white; padding: 1.5rem; border-radius: 12px; width: 380px; max-width: 95%; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1), 0 10px 10px -5px rgba(0,0,0,0.04); position: relative; display: flex; flex-direction: column;">
            <div class="modal-header" style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1.5rem;">
                <div>
                    <h2 style="margin: 0; font-size: 1.5rem; color: #111827; font-weight: bold; line-height: 1.2;">Ticket Assignment</h2>
                    <p style="margin: 2px 0 0; color: #6b7280; font-size: 0.85rem;">Configuration for Ticket <span id="modalTicketId" style="font-weight: bold; color: #111111;"></span></p>
                </div>
                <button id="closeModal" style="background: none; border: none; font-size: 1.75rem; cursor: pointer; color: #9ca3af; line-height: 1; padding: 0;">&times;</button>
            </div>

            <form id="assignForm" method="POST">
                @csrf
                <input type="hidden" id="ticket_db_id" name="ticket_id">
                
                <div class="input-box" style="margin-bottom: 1.25rem;">
                    <label style="display: block; font-size: 0.8rem; font-weight: bold; color: #374151; margin-bottom: 0.5rem; text-transform: uppercase; letter-spacing: 0.05em;">SUBJECT</label>
                    <input type="text" id="modalSubject" readonly style="width: 100%; padding: 0.75rem; border: 1px solid #d1d5db; border-radius: 6px; background: #f9fafb; color: #4b5563; font-size: 0.95rem;">
                </div>

                <div class="input-box" style="margin-bottom: 1.25rem;">
                    <label style="display: block; font-size: 0.8rem; font-weight: bold; color: #374151; margin-bottom: 0.5rem; text-transform: uppercase; letter-spacing: 0.05em;">Select Technician</label>
                    <select name="technician_id" required style="width: 100%; padding: 0.75rem; border: 1px solid #d1d5db; border-radius: 6px; background: white; cursor: pointer; font-size: 0.95rem; color: #111827;">
                        <option value="">Choose a specialist...</option>
                        @foreach($technicians as $tech)
                            <option value="{{ $tech->id }}">{{ $tech->name }} ({{ $tech->speciality ?? 'Specialist' }})</option>
                        @endforeach
                    </select>
                </div>

                <div class="input-box" style="margin-bottom: 1.25rem;">
                    <label style="display: block; font-size: 0.8rem; font-weight: bold; color: #374151; margin-bottom: 0.5rem; text-transform: uppercase; letter-spacing: 0.05em;">Priority Level</label>
                    <select name="priority" style="width: 100%; padding: 0.75rem; border: 1px solid #d1d5db; border-radius: 6px; background: white; cursor: pointer; font-size: 0.95rem; color: #111827;">
                        <option value="low">Low</option>
                        <option value="medium">Medium</option>
                        <option value="high">High</option>
                    </select>
                </div>

                <!-- SLA Selection Section -->
                <div class="sla-box" style="margin-bottom: 1.5rem;">
                    <label style="display: block; font-size: 0.8rem; font-weight: bold; color: #374151; margin-bottom: 0.6rem; text-transform: uppercase; letter-spacing: 0.05em;">SLA Selection</label>
                    <div style="display: flex; gap: 1rem; margin-bottom: 1rem;">
                        <div class="sla-option" data-value="default" style="flex: 1; display: flex; align-items: center; gap: 0.5rem; padding: 0.75rem; border: 1px solid #111111; border-radius: 6px; cursor: pointer; background: #eff6ff; color: #4338ca; font-weight: bold;">
                            <input type="radio" name="sla_type" value="default" checked style="accent-color: #111111; width: 1.2rem; height: 1.2rem;">
                            Default SLA
                        </div>
                        <div class="sla-option" data-value="custom" style="flex: 1; display: flex; align-items: center; gap: 0.5rem; padding: 0.75rem; border: 1px solid #d1d5db; border-radius: 6px; cursor: pointer; color: #6b7280;">
                            <input type="radio" name="sla_type" value="custom" style="accent-color: #111111; width: 1.2rem; height: 1.2rem;">
                            Custom SLA
                        </div>
                    </div>
                    <!-- Custom SLA Input Field (Hidden by default) -->
                    <div id="customSlaContainer" style="display: none;">
                        <label style="display: block; font-size: 0.75rem; font-weight: bold; color: #374151; margin-bottom: 0.4rem;">ENTER CUSTOM SLA (HOURS)</label>
                        <input type="number" name="custom_sla" placeholder="e.g. 24" style="width: 100%; padding: 0.75rem; border: 1px solid #d1d5db; border-radius: 6px; font-size: 0.9rem;">
                    </div>
                </div>
                
                <!-- Reminder Interval Section -->
                <div class="input-box" style="margin-bottom: 1.5rem;">
                    <label style="display: block; font-size: 0.8rem; font-weight: bold; color: #374151; margin-bottom: 0.5rem; text-transform: uppercase; letter-spacing: 0.05em;">Reminder Interval</label>
                    <select name="reminder_interval" style="width: 100%; padding: 0.75rem; border: 1px solid #d1d5db; border-radius: 6px; background: white; cursor: pointer; font-size: 0.95rem; color: #111827;">
                        <option value="">No Reminder</option>
                        <option value="30mins">Every 30 Minutes</option>
                        <option value="1hour">Every 1 Hour</option>
                        <option value="24hours">Every 24 Hours</option>
                        <option value="1day">Every 1 Day</option>
                    </select>
                </div>

                <button type="submit" style="width: 100%; padding: 0.85rem; background: #FBBF24; color: white; border: none; border-radius: 8px; font-weight: bold; cursor: pointer; margin-bottom: 0.75rem; font-size: 1rem; transition: background 0.2s;">Confirm Assignment</button>
                <button type="button" id="cancelBtn" style="width: 100%; padding: 0.85rem; background: #f3f4f6; color: #374151; border: none; border-radius: 8px; font-weight: bold; cursor: pointer; font-size: 1rem; transition: background 0.2s;">Cancel and Reset</button>
            </form>
        </div>
    </div>

    <script>
      // Assignment Modal Logic
      document.addEventListener('DOMContentLoaded', function() {
          const assignModal = document.getElementById('assignModal');
          const closeBtn = document.getElementById('closeModal');
          const cancelBtn = document.getElementById('cancelBtn');
          const assignForm = document.getElementById('assignForm');

          if (!assignForm) {
              console.error('Assign form not found');
              return;
          }

          // Open Modal
          document.querySelectorAll('.assign-btn-modal').forEach(btn => {
              btn.addEventListener('click', function(e) {
                  e.preventDefault();
                  const ticketId = this.dataset.ticketId;
                  const displayId = this.dataset.displayId;
                  const subject = this.dataset.subject;

                  document.getElementById('modalTicketId').innerText = displayId;
                  document.getElementById('modalSubject').value = subject;
                  document.getElementById('ticket_db_id').value = ticketId;
                  
                  // Set the form action dynamically
                  assignForm.action = `/tickets/${ticketId}/assign`;
                  
                  assignModal.style.display = 'flex';
              });
          });

          // Close Modal
          const closeModal = () => {
              assignModal.style.display = 'none';
              assignForm.reset();
              document.getElementById('customSlaContainer').style.display = 'none';
              // Reset SLA options UI
              document.querySelectorAll('.sla-option').forEach(opt => {
                  const radio = opt.querySelector('input');
                  if(radio && radio.value === 'default') {
                      opt.style.background = '#eff6ff';
                      opt.style.borderColor = '#4338ca';
                      opt.style.color = '#4338ca';
                      opt.style.fontWeight = 'bold';
                  } else {
                      opt.style.background = 'transparent';
                      opt.style.borderColor = '#d1d5db';
                      opt.style.color = '#6b7280';
                      opt.style.fontWeight = 'normal';
                  }
              });
          };

          if (closeBtn) closeBtn.addEventListener('click', closeModal);
          if (cancelBtn) cancelBtn.addEventListener('click', closeModal);
          
          window.addEventListener('click', (e) => {
              if (e.target === assignModal) closeModal();
          });

          // SLA Option Toggle UI
          document.querySelectorAll('.sla-option').forEach(option => {
              option.addEventListener('click', function() {
                  const radio = this.querySelector('input');
                  if (!radio) return;
                  
                  radio.checked = true;
                  const value = this.dataset.value;
                  
                  // Update UI for all options
                  document.querySelectorAll('.sla-option').forEach(opt => {
                      opt.style.background = 'transparent';
                      opt.style.borderColor = '#d1d5db';
                      opt.style.color = '#6b7280';
                      opt.style.fontWeight = 'normal';
                  });
                  
                  // Set active state for clicked one
                  this.style.background = '#eff6ff';
                  this.style.borderColor = '#4338ca';
                  this.style.color = '#4338ca';
                  this.style.fontWeight = 'bold';

                  // Show/Hide Custom SLA input
                  const customSlaContainer = document.getElementById('customSlaContainer');
                  if (value === 'custom') {
                      customSlaContainer.style.display = 'block';
                      const input = customSlaContainer.querySelector('input');
                      if (input) input.focus();
                  } else {
                      customSlaContainer.style.display = 'none';
                  }
              });
          });

          // Handle Form Submission
          assignForm.addEventListener('submit', function(e) {
              // Close modal immediately
              assignModal.style.display = 'none';
              // Allow natural submission so browser reload spinner turns
          });
      });
    </script>

    
  </body>
</html>
