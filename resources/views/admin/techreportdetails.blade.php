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
      </div>
    </main>
  </div>
</body>
</html>
