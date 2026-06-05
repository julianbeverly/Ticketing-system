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
        <div style="flex: 1;"></div>
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
      </div>
    </main>
  </div>
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
