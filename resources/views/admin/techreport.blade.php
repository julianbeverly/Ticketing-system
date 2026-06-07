<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Technician Performance</title>
    <!-- Font Awesome for icons -->
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
    />
    <!-- Main styling from style.css -->
    <link rel="stylesheet" href="/dist/css/style.css" />
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
  </head>
  <body>
    <!-- The main layout container -->
    <div class="dashboard-layout">
      <!-- Sidebar Navigation -->
        @include('admin.partials.sidebar')

      <!-- The main content area -->
      <main class="main-content">
        <!-- Top header with search and user actions -->
        <header class="top-header">
        <button class="menu-toggle" id="menuToggle">
          <i class="fa-solid fa-bars"></i>
        </button>
          <form method="GET" action="{{ route('tech.reporting') }}" style="flex: 1; max-width: 500px; display: flex; align-items: center; margin: 0 1.5rem;">
            <div class="search-container" style="width: 100%; margin: 0;">
              <i class="fa-solid fa-magnifying-glass" onclick="this.closest('form').submit();" style="cursor: pointer;"></i>
              <input type="text" name="search" value="{{ request('search') }}" placeholder="Search technician metrics..." />
            </div>
          </form>
                <div class="header-actions">
                    
                    <a href="{{ route('employee.profile') }}" class="icon-btn profile-btn">
                        <i class="fa-regular fa-circle-user"></i>
                    </a>
                </div>
        </header>

        <div class="dashboard-content">
          <!-- Breadcrumb navigation -->
          <div class="breadcrumb">
            <a href="#">REPORTS</a> /
            <span>TECHNICIAN PERFORMANCE</span>
          </div>

          <!-- Page Title & Export Actions -->
          <div class="page-header-title">
            <div>
              <h1>Technician Performance</h1>
            </div>

            <div class="report-actions-wrapper">
              <a href="{{ route('admin.techreport.export.excel') }}" class="btn-export white-btn">
                <i class="fa-regular fa-file-excel"></i>
                <span>Export to<br />Excel</span>
              </a>

              <a href="{{ route('admin.techreport.export.pdf') }}" class="btn-export blue-btn">
                <i class="fa-regular fa-file-pdf"></i>
                <span>Export to<br />PDF</span>
              </a>
            </div>
          </div>

          <!-- Top Performer -->
          <!-- <div class="top-performer-container">
            <div class="top-performer-card">
              <div class="tp-label">TOP PERFORMER</div>
              <div class="tp-name">Marcus Chen</div>
              <div class="tp-stat">SLA Compliance: 99.8%</div>
              <i class="fa-solid fa-star tp-icon-bg"></i>
            </div>
          </div> -->

          <!-- Technician Table -->
          <div class="tickets-card">
            <div class="table-header-box">
              <h3>Technician Directory</h3>

              {{-- ── Report Type Selector (filters by assignment date) ── --}}
              <form action="{{ route('tech.reporting') }}" method="GET"
                    id="filterForm"
                    style="display:flex; gap:0.75rem; align-items:center; flex-wrap:wrap;">

                  <div style="display:flex; align-items:center; gap:0.5rem; border:1px solid #e5e7eb; border-radius:6px; background:#f9fafb; padding:0.5rem 1rem;">
                      <i class="fa-solid fa-chart-bar" style="color:#6b7280;"></i>
                      <select name="report_type" id="reportTypeSelect"
                          onchange="handleReportTypeChange()"
                          style="border:none; background:transparent; font-size:0.85rem; outline:none; color:#374151; font-weight:600; cursor:pointer;">
                          <option value="custom"  {{ request('report_type','custom') === 'custom'  ? 'selected' : '' }}>Select type of report</option>
                          <option value="daily"   {{ request('report_type') === 'daily'   ? 'selected' : '' }}>Daily</option>
                          <option value="weekly"  {{ request('report_type') === 'weekly'  ? 'selected' : '' }}>Weekly</option>
                          <option value="monthly" {{ request('report_type') === 'monthly' ? 'selected' : '' }}>Monthly</option>
                          <option value="custom"  {{ request('report_type') === 'custom'  ? 'selected' : '' }}>Date Range</option>
                      </select>
                  </div>

                  {{-- Daily --}}
                  <div id="picker-daily" style="display:none; padding:0.5rem 1rem; align-items:center; gap:0.5rem; border:1px solid #e5e7eb; border-radius:6px; background:#f9fafb;">
                      <i class="fa-regular fa-calendar" style="color:#6b7280;"></i>
                      <input type="date" name="daily_date" value="{{ request('daily_date') }}" onchange="document.getElementById('filterForm').submit()" style="border:none; background:transparent; font-size:0.85rem; outline:none; color:#374151;">
                  </div>

                  {{-- Weekly --}}
                  <div id="picker-weekly" style="display:none; padding:0.5rem 1rem; align-items:center; gap:0.5rem; border:1px solid #e5e7eb; border-radius:6px; background:#f9fafb;">
                      <i class="fa-regular fa-calendar-week" style="color:#6b7280;"></i>
                      <input type="week" name="weekly_date" value="{{ request('weekly_date') }}" onchange="document.getElementById('filterForm').submit()" style="border:none; background:transparent; font-size:0.85rem; outline:none; color:#374151;">
                  </div>

                  {{-- Monthly --}}
                  <div id="picker-monthly" style="display:none; padding:0.5rem 1rem; align-items:center; gap:0.5rem; border:1px solid #e5e7eb; border-radius:6px; background:#f9fafb;">
                      <i class="fa-regular fa-calendar-days" style="color:#6b7280;"></i>
                      <input type="month" name="monthly_date" value="{{ request('monthly_date') }}" onchange="document.getElementById('filterForm').submit()" style="border:none; background:transparent; font-size:0.85rem; outline:none; color:#374151;">
                  </div>

                  {{-- Date Range --}}
                  <div id="picker-custom" style="display:none; padding:0.5rem 1rem; align-items:center; gap:0.5rem; border:1px solid #e5e7eb; border-radius:6px; background:#f9fafb;">
                      <i class="fa-regular fa-calendar" style="color:#6b7280;"></i>
                      <input type="date" name="start_date" value="{{ request('start_date') }}" onchange="document.getElementById('filterForm').submit()" style="border:none; background:transparent; font-size:0.85rem; outline:none; color:#374151;">
                      <span style="color:#6b7280;">to</span>
                      <input type="date" name="end_date"   value="{{ request('end_date') }}"   onchange="document.getElementById('filterForm').submit()" style="border:none; background:transparent; font-size:0.85rem; outline:none; color:#374151;">
                  </div>
              </form>
            </div>

            <table class="tickets-table">
              <thead>
                <tr>
                  <th>NAME</th>
                  <th>ASSIGNED</th>
                  <th>INPROGRESS</th>
                  <th>RESOLVED</th>
                  <th>CLOSED</th>
                  <th>OVERDUE</th>
                  <th class="action-col">ACTION</th>
                </tr>
              </thead>

              <tbody>
                @forelse($technicians as $tech)
                <tr>
                  <td>
                    <!-- Name only — email removed per requirements -->
                    <div class="ticket-subject">
                      <strong>{{ $tech->name }}</strong>
                    </div>
                  </td>
                  <td>{{ $tech->assigned_count }}</td>
                  <td>{{ $tech->inprogress_count }}</td>
                  <td>{{ $tech->resolved_count }}</td>
                  <td>{{ $tech->closed_count }}</td>
                  <td>{{ $tech->overdue_count }}</td>
                  <td class="action-col">
                    <a href="{{ route('admin.techreport.details', $tech->id) }}" class="tech-view-btn">View More</a>
                  </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" style="text-align: center; padding: 2rem;">No technicians found.</td>
                </tr>
                @endforelse
              </tbody>
            </table>

            <!-- Pagination -->
            <div class="pagination-container">
              <div>
                Showing {{ $technicians->firstItem() ?? 0 }} to {{ $technicians->lastItem() ?? 0 }} of <strong>{{ $technicians->total() }}</strong> technicians
              </div>
              <div class="pagination-links">
                {{ $technicians->links('pagination::bootstrap-4') }}
              </div>
            </div>
          </div>
        </div>

        <!-- MODAL -->
        <div id="techDetailsModal" class="modal-overlay">
          <div class="modal-content">
            <div class="modal-header">
              <h3 id="techModalName">Technician Name</h3>
              <i class="fa-solid fa-times" id="closeTechModalIcon"></i>
            </div>

            <p class="modal-role">
              <i class="fa-solid fa-briefcase"></i>
              <span id="techModalRole">Role</span>
            </p>

            <div class="modal-box">
              <p><strong>Phone:</strong> <span id="techModalPhone"></span></p>
              <p>
                <strong>SLA Compliance:</strong> <span id="techModalSLA"></span>
              </p>
            </div>

            <button id="closeTechModalBtn">Close Overview</button>
          </div>
        </div>
      </main>
    </div>

    <!-- Link to Javascript for Modals logic -->
    <script src="/dist/js/modals.js"></script>

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
