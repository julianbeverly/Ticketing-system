<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <!-- Fixed CSS path casing -->
  <link rel="stylesheet" href="/dist/css/style.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
  <title>Reporting - Resolve</title>
</head>

<body>

  <div class="dashboard-layout">

        @include('techdashboard.partials.sidebar')


    <main class="main-content">

      
      <header class="top-header">
        <button class="menu-toggle" id="menuToggle">
          <i class="fa-solid fa-bars"></i>
        </button>
        
        <div style="flex: 1;"></div>

        <!-- Header actions -->
        <div class="header-actions">
          
          <a href="{{ route('employee.profile') }}" class="icon-btn profile-btn">
            <i class="fa-regular fa-circle-user"></i>
          </a>
        </div>
      </header>

        <div class="dashboard-content" style="padding: 2rem;">

          <!-- Page header -->
          <div class="page-header-title" style="margin-bottom: 2rem;">
            <div>
              <h1 style="font-size: 1.8rem; font-weight: 700; color: #111827; margin-bottom: 0.25rem;">Reporting</h1>
              <p style="color: #6b7280; font-size: 0.95rem;">Technician Ticket Performance and Work Summary</p>
            </div>
            <div class="report-actions-wrapper">
              {{-- Export to PDF (outlined style) --}}
              <a href="{{ route('tech.own.report.export.pdf') }}" class="btn-export white-btn">
                <i class="fa-regular fa-file-pdf"></i>
                <span>Export<br />PDF</span>
              </a>
              <a href="{{ route('tech.own.report.export.excel') }}" class="btn-export blue-btn">
                <i class="fa-regular fa-file-excel"></i>
                <span>Export<br />Excel</span>
              </a>
            </div>
          </div>

          <!-- =============== TECHNICIAN SUMMARY TABLE =============== -->
          <div class="tickets-card">
            <div class="table-header-box">
              <h3>Technician Directory</h3>

              {{-- ── Report Type Selector (filters by assignment date) ── --}}
              <form action="{{ route('tech.own.report') }}" method="GET"
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
                {{-- Single row: only the logged-in technician's own data --}}
                <tr>
                  <td>
                    <div class="ticket-subject">
                      <strong>{{ $tech->name }}</strong>
                    </div>
                  </td>
                  {{-- Tickets currently assigned to this technician --}}
                  <td>{{ $tech->assigned_count }}</td>
                  <td>{{ $tech->inprogress_count }}</td>
                  <td>{{ $tech->resolved_count }}</td>
                  <td>{{ $tech->closed_count }}</td>
                  <td>{{ $tech->overdue_count }}</td>
                  <td class="action-col">
                    <a href="{{ route('tech.own.report.details') }}" class="tech-view-btn">View More</a>
                  </td>
                </tr>
              </tbody>
            </table>

            <div class="pagination-container">
              <div>Showing your personal performance report</div>
            </div>
          </div>

      </div>
      <!-- End of dashboard-content -->

    </main>
    <!-- End of main-content -->

  </div>
  <!-- End of dashboard-layout -->
    
  </body>
</html>
