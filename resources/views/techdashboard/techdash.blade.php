<!doctype html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="stylesheet" href="/dist/css/style.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
  <title>Document</title>
  <style>
    .ticket-subject {
      max-width: 250px;
      white-space: normal;
      word-wrap: break-word;
      overflow-wrap: break-word;
      line-height: 1.4;
    }
    .view-details-link {
      background-color: #0b57d0;
      color: #ffffff !important;
      padding: 6px 12px;
      border-radius: 6px;
      font-weight: bold;
      text-decoration: none;
      display: inline-block;
      font-size: 0.85rem;
      white-space: nowrap;
    }
    .view-details-link:hover {
      background-color: #0842a0;
    }
  </style>
</head>

<body>
  <div class="dashboard-layout">
        @include('techdashboard.partials.sidebar')

    <!-- Main Content -->
    <main class="main-content">
      <!-- Top Header -->
      <header class="top-header">
        <div style="flex: 1;"></div>
        <div class="header-actions" id="content">
          
          <button class="icon-btn profile-btn">
            <i class="fa-regular fa-circle-user"></i>
          </button>
        </div>
      </header>
      <div class="dashboard-content">
        <div class="metrics-grid">
          <div class="metric-card card-blue">
            <div class="icon-circle">
              <i class="fa-regular fa-envelope"></i>
            </div>
            <h4>ASSIGNED<br />TICKETS</h4>
            <div class="value">{{ number_format($counts['assigned']) }}</div>
          </div>
          <div class="metric-card card-pink">
            <div class="icon-circle">
              <i class="fa-regular fa-circle-check"></i>
            </div>
            <h4>INPROGRESS<br />TICKETS</h4>
            <div class="value">{{ number_format($counts['in_progress']) }}</div>
          </div>
          <div class="metric-card card-beige">
            <div class="icon-circle">
               <i class="fa-solid fa-check-double" style="color: #854d0e;"></i>
            </div>
            <h4>Closed</h4>
            <div class="value">{{ number_format($counts['closed']) }}</div>
          </div>
          <div class="metric-card card-green">
            <div class="icon-circle">
              <i class="fa-solid fa-clock-rotate-left" style="color: #dc2626;"></i>
            </div>
            <h4>OVERDUE</h4>
            <div class="value">{{ number_format($counts['overdue']) }}</div>
          </div>
        </div>
      </div>



      <!-- Recent Activity -->
      <div class="dashboard-content" style="margin-top: 0;">
        <div class="activity-card mt-card" style="margin-top: 0;">
          <div class="activity-header">
            <h3>Recent Activity</h3>
            <a href="{{ route('tech.tickets') }}" class="view-all">View All Updates</a>
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
                  @if($activity->action == 'assigned')
                    Ticket by Admin was assigned to you
                  @elseif($activity->action == 'status_updated')
                    Ticket status changed to <strong>{{ ucwords(str_replace('changed status to ', '', $activity->description)) }}</strong>
                  @elseif($activity->action == 'message_sent')
                    @if($activity->user_id === Auth::id())
                      You sent a message
                    @else
                      {{ $activity->user->name ?? 'User' }} sent a message
                    @endif
                  @elseif($activity->action == 'overdue')
                    Ticket is <strong>Overdue</strong>
                  @elseif(in_array($activity->action, ['resolved', 'closed', 'reopened']))
                    Ticket has been {{ $activity->action }}
                  @elseif($activity->action == 'created')
                    New ticket created
                  @else
                    {{ ucfirst($activity->description) }}
                  @endif

                  @if($activity->ticket)
                  <a href="{{ route('tech.tickets.details', $activity->ticket->id) }}" class="text-blue" style="text-decoration: none;">
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
</body>

</html>
