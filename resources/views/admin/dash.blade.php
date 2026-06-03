<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>admin dashboard</title>
    <!-- Font Awesome Icons  -->
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
    />
    <link rel="stylesheet" href="/dist/css/style.css" />
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
      .modern-select-wrapper {
        position: relative;
        display: inline-block;
        min-width: 160px;
      }
      .modern-select {
        appearance: none;
        background: #f8faff;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 0.6rem 2.5rem 0.6rem 1rem;
        font-size: 0.9rem;
        font-weight: 600;
        color: #1e293b;
        cursor: pointer;
        width: 100%;
        transition: all 0.2s;
        outline: none;
      }
      .modern-select:hover {
        border-color: #0b57d0;
        background: #fff;
      }
      .modern-select-wrapper i {
        position: absolute;
        right: 1rem;
        top: 50%;
        transform: translateY(-50%);
        color: #64748b;
        pointer-events: none;
        font-size: 0.8rem;
      }
      .bar-chart-container {
          transition: opacity 0.3s;
      }
      .bar-chart-container.loading {
          opacity: 0.5;
          pointer-events: none;
      }
    </style>
  </head>

  <body>
    <div class="dashboard-layout">
      <!-- Sidebar -->
        @include('admin.partials.sidebar')

      <!-- Main Content -->
      <main class="main-content">
        <!-- Top Header -->
        <header class="top-header">
          <button class="menu-toggle" id="menuToggle">
            <i class="fa-solid fa-bars"></i>
          </button>
          <div style="flex: 1;"></div>
          <div class="header-actions" id="content">
            
            <a href="{{ route('employee.profile') }}" class="icon-btn profile-btn">
              <i class="fa-regular fa-circle-user"></i>
            </a>
          </div>
        </header>

        <!-- Dashboard Content -->
        <div class="dashboard-content">
          <!-- Metric Cards -->
          <div class="metrics-grid">
            <div class="metric-card card-blue">
              <div class="icon-circle">
                <i class="fa-regular fa-envelope"></i>
              </div>
        <!-- <div class="welcome">Hello Admin</div> -->
              <div class="metric-info">
                <h4>TOTAL OPEN</h4>
                <div class="value">{{ number_format($totalStats['open']) }}</div>
              </div>
            </div>
            <div class="metric-card card-pink">
              <div class="icon-circle">
                <i class="fa-solid fa-clock-rotate-left" style="color: #dc2626;"></i>
              </div>
              <div class="metric-info">
                <h4>OVERDUE</h4>
                <div class="value">{{ number_format($totalStats['overdue']) }}</div>
              </div>
            </div>
            <div class="metric-card card-beige">
              <div class="icon-circle">
                <i class="fa-solid fa-user-group"></i>
              </div>
              <div class="metric-info">
                <h4>TOTAL USERS</h4>
                <div class="value">{{ number_format($totalStats['users']) }}</div>
              </div>
            </div>
            <div class="metric-card card-green">
              <div class="icon-circle">
                <i class="fa-solid fa-circle-check"></i>
              </div>
              <div class="metric-info">
                <h4>CLOSED</h4>
                <div class="value">{{ number_format($totalStats['closed']) }}</div>
              </div>
            </div>
          </div>

          <!-- Charts Section -->
          <div class="charts-section">
            <!-- Bar Chart -->
            <div class="chart-card">
              <div class="chart-header">
                <div>
                  <h3>Ticket Status Distribution</h3>
                </div>
                <div class="chart-controls">
                  <div class="modern-select-wrapper">
                    <select id="periodSelector" class="modern-select">
                      <option value="this_month">This Month</option>
                      <option value="last_month">Last Month</option>
                      <option disabled>──────────</option>
                      @php
                        $months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
                        $currentMonth = date('n');
                      @endphp
                      @foreach($months as $index => $month)
                        <option value="{{ $index + 1 }}">{{ $month }}</option>
                      @endforeach
                    </select>
                    <i class="fa-solid fa-chevron-down"></i>
                  </div>
                </div>
              </div>
              @php 
                $maxCount = max($counts['open'], $counts['in_progress'], $counts['closed'], $counts['overdue'], 1);
              @endphp
              <div class="bar-chart-container">
                <div class="y-axis">
                  <span>{{ $maxCount }}</span>
                  <span>{{ round($maxCount * 0.75) }}</span>
                  <span>{{ round($maxCount * 0.5) }}</span>
                  <span>{{ round($maxCount * 0.25) }}</span>
                </div>
                <div class="chart-grid">
                  <div class="grid-line"></div>
                  <div class="grid-line"></div>
                  <div class="grid-line"></div>
                  <div class="grid-line"></div>
                </div>
                <div class="bars-container">
                  <div class="bar-column">
                    <div class="bar open-bar" style="height: {{ ($counts['open'] / $maxCount) * 100 }}%"></div>
                    <span class="bar-label">OPEN</span>
                  </div>

                  <div class="bar-column">
                    <div class="bar inprogress-bar" style="height: {{ ($counts['in_progress'] / $maxCount) * 100 }}%"></div>
                    <span class="bar-label">IN PROGRESS</span>
                  </div>
                  <div class="bar-column">
                    <div class="bar closed-bar" style="height: {{ ($counts['closed'] / $maxCount) * 100 }}%"></div>
                    <span class="bar-label">CLOSED</span>
                  </div>
                  <div class="bar-column">
                    <div class="bar overdue-bar" style="height: {{ ($counts['overdue'] / $maxCount) * 100 }}%"></div>
                    <span class="bar-label">OVERDUE</span>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- SLA Line Chart -->

          <div class="chart-card mt-card">
            <div class="chart-header sl-header">
              <div>
                <h3>Technician SLA Compliance</h3>
                <p>
                  Professional performance tracking (Resolution within SLA vs Assigned)
                </p>
              </div>
              <div class="chart-controls">
                <div class="modern-select-wrapper">
                  <select id="slaPeriodSelector" class="modern-select">
                    <option value="this_month">This Month</option>
                    <option value="last_month">Last Month</option>
                    <option disabled>──────────</option>
                    @foreach(['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'] as $index => $month)
                      <option value="{{ $index + 1 }}">{{ $month }}</option>
                    @endforeach
                  </select>
                  <i class="fa-solid fa-chevron-down"></i>
                </div>
              </div>
            </div>
            <div class="sla-chart-canvas-wrapper" style="height: 400px; padding: 20px; position: relative;">
              <canvas id="slaChart"></canvas>
            </div>
          </div>







          <!-- Recent Activity -->
          <div class="activity-card mt-card">
            <div class="activity-header">
              <h3>Recent Activity</h3>
              <!-- <a href="#" class="view-all">View All Updates</a> -->
              <a href="{{ route('admin.tickets') }}" class="view-all">View All Updates</a>
            </div>

            <div class="activity-list">
              <!-- Dynamic Ticket Activities -->
              @forelse($recentActivities as $activity)
              <div class="activity-item">
                <div class="activity-icon {{ in_array($activity->action, ['reopened', 'overdue']) ? 'icon-red' : (in_array($activity->action, ['resolved', 'closed']) ? 'icon-green' : 'icon-blue') }}">
                  @if($activity->action == 'created')
                    <i class="fa-solid fa-plus"></i>
                  @elseif($activity->action == 'assigned')
                    <i class="fa-regular fa-user"></i>
                  @elseif(in_array($activity->action, ['resolved', 'closed']))
                    <i class="fa-solid fa-check"></i>
                  @else
                    <i class="fa-solid fa-clock-rotate-left"></i>
                  @endif
                </div>
                <div class="activity-content">
                  <p class="activity-text">
                    @if($activity->action == 'overdue')
                      <strong>System:</strong> Ticket is <strong>Overdue</strong>
                    @else
                      <strong>{{ optional($activity->user)->name ?? 'System' }}</strong> {{ $activity->description }}
                    @endif
                    @if($activity->ticket)
                    <a href="{{ route('admin.tickets.details', $activity->ticket->id) }}" class="text-blue" style="text-decoration: none;">
                        <strong>{{ $activity->ticket->ticket_id }}</strong>
                    </a>
                    @endif
                  </p>
                  <p class="activity-sub">
                    {{ optional($activity->ticket)->subject }}
                  </p>
                </div>
                <div class="activity-time">{{ $activity->created_at->diffForHumans() }}</div>
              </div>
              @empty
              <div class="activity-item">
                  <p class="activity-text" style="color: #64748b; margin-left: 1rem;">No recent activity found.</p>
              </div>
              @endforelse
            </div>
          </div>
        </div>
      </main>
    </div>
    <script>
      // Sidebar Toggle Logic
      const menuToggle = document.getElementById("menuToggle");
      const sidebar = document.querySelector(".sidebar-nav");
      const overlay = document.getElementById("sidebarOverlay");

      if (menuToggle && sidebar) {
          menuToggle.addEventListener("click", () => {
              sidebar.classList.toggle("active");
              if (overlay) overlay.classList.toggle("show");
          });
      }

      if (overlay) {
          overlay.addEventListener("click", () => {
              sidebar.classList.remove("active");
              overlay.classList.remove("show");
          });
      }

      // Chart Dynamic Scaling and Update Logic
      const periodSelector = document.getElementById('periodSelector');
      const chartContainer = document.querySelector('.bar-chart-container');
      const bars = {
          open: document.querySelector('.bar.open-bar'),
          inprogress: document.querySelector('.bar.inprogress-bar'),
          closed: document.querySelector('.bar.closed-bar'),
          overdue: document.querySelector('.bar.overdue-bar')
      };
      const yAxis = document.querySelector('.y-axis');

      function updateChart(data) {
          const values = [data.open, data.in_progress, data.closed, data.overdue];
          const highest = Math.max(...values, 1); // Ensure at least 1 for scaling
          const maxCount = highest + 2;
          
          // Update Y-axis labels dynamically
          const steps = [
              maxCount,
              Math.round(maxCount * 0.75),
              Math.round(maxCount * 0.5),
              Math.round(maxCount * 0.25),
              0
          ];
          
          // Remove duplicates and sort descending
          const uniqueSteps = [...new Set(steps)].sort((a, b) => b - a);
          
          yAxis.innerHTML = uniqueSteps.map(step => `<span>${step}</span>`).join('');

          // Update Bars
          bars.open.style.height = `${(data.open / maxCount) * 100}%`;
          bars.inprogress.style.height = `${(data.in_progress / maxCount) * 100}%`;
          bars.closed.style.height = `${(data.closed / maxCount) * 100}%`;
          bars.overdue.style.height = `${(data.overdue / maxCount) * 100}%`;
      }

      if (periodSelector) {
          periodSelector.addEventListener('change', function() {
              const period = this.value;
              chartContainer.classList.add('loading');

              fetch(`{{ route('admin.chart.data') }}?period=${period}`)
                  .then(response => response.json())
                  .then(data => {
                      updateChart(data);
                      chartContainer.classList.remove('loading');
                  })
                  .catch(error => {
                      console.error('Error fetching chart data:', error);
                      chartContainer.classList.remove('loading');
                  });
          });

          // Initial scale fix on load
          const initialData = {
              open: {{ $counts['open'] }},
              in_progress: {{ $counts['in_progress'] }},
              closed: {{ $counts['closed'] }},
              overdue: {{ $counts['overdue'] }}
          };
          updateChart(initialData);
      }

      // SLA Chart Logic
      const slaCtx = document.getElementById('slaChart');
      if (slaCtx) {
          let slaChart;

          function initSlaChart(data) {
              const labels = data.map(tech => [tech.name, tech.role, tech.sla_score + '%']);
              const scores = data.map(tech => tech.sla_score);
              const backgroundColors = data.map(tech => {
                  if (tech.sla_score >= 80) return '#10b981'; // Green
                  if (tech.sla_score >= 50) return '#f59e0b'; // Orange
                  return '#ef4444'; // Red
              });

              if (slaChart) {
                  slaChart.destroy();
              }

              slaChart = new Chart(slaCtx, {
                  type: 'line',
                  data: {
                      labels: labels,
                      datasets: [{
                          label: 'SLA Compliance %',
                          data: scores,
                          borderColor: '#2563eb',
                          backgroundColor: 'rgba(37, 99, 235, 0.1)',
                          borderWidth: 3,
                          tension: 0.4, // Smooth curve
                          fill: true,
                          pointBackgroundColor: backgroundColors,
                          pointBorderColor: '#fff',
                          pointRadius: 6,
                          pointHoverRadius: 8
                      }]
                  },
                  options: {
                      responsive: true,
                      maintainAspectRatio: false,
                      scales: {
                          y: {
                              beginAtZero: true,
                              max: 100,
                              ticks: {
                                  stepSize: 20,
                                  callback: value => value + '%'
                              },
                              grid: {
                                  color: '#e5e7eb'
                              }
                          },
                          x: {
                              grid: {
                                  display: false
                              },
                              ticks: {
                                  autoSkip: false,
                                  maxRotation: 0,
                                  font: {
                                      size: 11
                                  },
                                  padding: 10
                              }
                          }
                      },
                      plugins: {
                          tooltip: {
                              backgroundColor: '#1e293b',
                              titleFont: { size: 14, weight: 'bold' },
                              bodyFont: { size: 13 },
                              padding: 12,
                              callbacks: {
                                  label: function(context) {
                                      const tech = data[context.dataIndex];
                                      return [
                                          `SLA Score: ${tech.sla_score}%`,
                                          `Assigned: ${tech.assigned}`,
                                          `Resolved: ${tech.resolved}`,
                                          `Overdue: ${tech.overdue}`,
                                          `Avg. Time: ${tech.avg_time}h`
                                      ];
                                  },
                                  afterLabel: function(context) {
                                      const tech = data[context.dataIndex];
                                      return `Role: ${tech.role}`;
                                  }
                              }
                          },
                          legend: {
                              display: false
                          }
                      }
                  }
              });
          }

          // Initial Load
          initSlaChart({!! json_encode($slaData) !!});

          // Dropdown Change
          document.getElementById('slaPeriodSelector').addEventListener('change', function() {
              const period = this.value;
              fetch(`{{ route('admin.sla.data') }}?period=${period}`)
                  .then(response => response.json())
                  .then(data => {
                      initSlaChart(data);
                  });
          });
      }
    </script>
  </body>
</html>
