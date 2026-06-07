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
    <script src="{{ asset('js/chart.min.js') }}"></script>
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
          <div style="display: flex; align-items: center; margin-left: 1rem;">
            <h2 style="font-size: 1.25rem; font-weight: 700; margin: 0;">Welcome, Admin</h2>
          </div>
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
    document.addEventListener('DOMContentLoaded', function() {

        // ============================================================
        // 1. BAR CHART — Dynamic period switching via AJAX
        // ============================================================
        const periodSelector = document.getElementById('periodSelector');
        const barChartContainer = document.querySelector('.bar-chart-container');

        if (periodSelector && barChartContainer) {
            periodSelector.addEventListener('change', function() {
                const period = this.value;
                barChartContainer.classList.add('loading');

                fetch(`{{ route('admin.chart.data') }}?period=${period}`)
                    .then(res => res.json())
                    .then(data => {
                        // Calculate the max for Y-axis scaling
                        const maxCount = Math.max(
                            data.open || 0,
                            data.in_progress || 0,
                            data.closed || 0,
                            data.overdue || 0,
                            1  // minimum 1 to avoid division by zero
                        );

                        // Update Y-axis labels
                        const yAxisSpans = barChartContainer.querySelectorAll('.y-axis span');
                        if (yAxisSpans.length >= 4) {
                            yAxisSpans[0].textContent = maxCount;
                            yAxisSpans[1].textContent = Math.round(maxCount * 0.75);
                            yAxisSpans[2].textContent = Math.round(maxCount * 0.5);
                            yAxisSpans[3].textContent = Math.round(maxCount * 0.25);
                        }

                        // Update bar heights
                        const bars = barChartContainer.querySelectorAll('.bar');
                        const values = [data.open, data.in_progress, data.closed, data.overdue];
                        bars.forEach((bar, index) => {
                            const val = values[index] || 0;
                            bar.style.height = ((val / maxCount) * 100) + '%';
                            bar.style.transition = 'height 0.5s ease';
                        });

                        barChartContainer.classList.remove('loading');
                    })
                    .catch(err => {
                        console.error('Failed to load chart data:', err);
                        barChartContainer.classList.remove('loading');
                    });
            });
        }

        // ============================================================
        // 2. SLA LINE CHART — Chart.js rendering
        // ============================================================
        const slaCanvas = document.getElementById('slaChart');
        const slaPeriodSelector = document.getElementById('slaPeriodSelector');
        let slaChart = null;

        // Initial data from server
        const initialSlaData = @json($slaData);

        function renderSlaChart(data) {
            const ctx = slaCanvas.getContext('2d');

            // Destroy previous chart instance if it exists
            if (slaChart) {
                slaChart.destroy();
            }

            // If no data, show empty state
            if (!data || data.length === 0) {
                slaChart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: ['No technicians found'],
                        datasets: [{
                            label: 'No data',
                            data: [0],
                            backgroundColor: 'rgba(200,200,200,0.3)'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false },
                            title: {
                                display: true,
                                text: 'No SLA data available for this period',
                                font: { size: 14 },
                                color: '#94a3b8'
                            }
                        },
                        scales: {
                            y: { display: true, beginAtZero: true, max: 100 },
                            x: { display: true }
                        }
                    }
                });
                return;
            }

            const labels = data.map(d => d.name);
            const slaScores = data.map(d => d.sla_score);
            const assigned = data.map(d => d.assigned);
            const resolved = data.map(d => d.resolved);

            slaChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [
                        {
                            label: 'SLA Compliance (%)',
                            data: slaScores,
                            borderColor: '#0b57d0',
                            backgroundColor: 'rgba(11, 87, 208, 0.1)',
                            borderWidth: 2.5,
                            pointRadius: 5,
                            pointBackgroundColor: '#0b57d0',
                            pointBorderColor: '#fff',
                            pointBorderWidth: 2,
                            tension: 0.3,
                            fill: true,
                            yAxisID: 'y'
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: {
                        mode: 'index',
                        intersect: false
                    },
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            enabled: false
                        }
                    },
                    scales: {
                        y: {
                            type: 'linear',
                            position: 'left',
                            beginAtZero: true,
                            max: 100,
                            title: {
                                display: true,
                                text: 'SLA Compliance (%)',
                                font: { size: 12, weight: '600' },
                                color: '#64748b'
                            },
                            ticks: {
                                callback: val => val + '%',
                                font: { size: 11 },
                                color: '#94a3b8'
                            },
                            grid: {
                                color: 'rgba(0,0,0,0.06)'
                            }
                        },
                        x: {
                            ticks: {
                                font: { size: 11 },
                                color: '#64748b',
                                maxRotation: 45,
                                minRotation: 0
                            },
                            grid: {
                                display: false
                            }
                        }
                    }
                }
            });
        }

        // Render the initial SLA chart
        if (slaCanvas) {
            renderSlaChart(initialSlaData);
        }

        // SLA period selector change handler
        if (slaPeriodSelector && slaCanvas) {
            slaPeriodSelector.addEventListener('change', function() {
                const period = this.value;

                fetch(`{{ route('admin.sla.data') }}?period=${period}`)
                    .then(res => res.json())
                    .then(data => {
                        renderSlaChart(data);
                    })
                    .catch(err => {
                        console.error('Failed to load SLA data:', err);
                    });
            });
        }

    });
    </script>
    
  </body>
</html>
