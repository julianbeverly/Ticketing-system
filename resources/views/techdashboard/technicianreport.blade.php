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
<script>
        const menuToggle = document.getElementById("menuToggle");
        const sidebar = document.querySelector(".sidebar-nav");
        const overlay = document.getElementById("sidebarOverlay");
        
        if (menuToggle && sidebar && overlay) {
            menuToggle.addEventListener("click", () => {
                sidebar.classList.toggle("active");
                overlay.classList.toggle("show");
            });
            overlay.addEventListener("click", () => {
                sidebar.classList.remove("active");
                overlay.classList.remove("show");
            });
        }
    </script>
</body>

</html>
