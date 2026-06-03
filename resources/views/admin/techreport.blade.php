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

              <div class="table-icons">
                <i class="fa-solid fa-filter"></i>
                <i class="fa-solid fa-outdent"></i>
              </div>
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
  </body>
</html>
