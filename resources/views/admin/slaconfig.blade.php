<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>SLA Configuration</title>
    <!-- Use Font Awesome for icons -->
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
    />
    <link rel="stylesheet" href="/dist/css/style.css" />
  </head>
  <body>
    
    <div class="dashboard-layout">
        @include('admin.partials.sidebar')

      <!-- Main Content Area -->
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
          <!-- Breadcrumb Navigation -->
          <div class="breadcrumb">
            <a href="#">Ticket Management</a> &gt;
            <span>SLA configuration</span>
          </div>

          <!-- Page Title and Description -->
          <div class="page-header-title">
            <div>
              <h1 style="color: #0b1f3c; margin-bottom: 0.5rem;">SLA Configuration</h1>
              <p style="color: #64748b; font-size: 0.95rem; line-height: 1.5; max-width: 800px;">
                Define high-precision response windows for your service operations. These
                resolution times act as the structural framework for automated deadline
                assignment and escalation triggers across all priority tiers.
              </p>
            </div>
          </div>

          @if(session()->has('success'))
          <div style="background-color: #d4edda; color: #155724; padding: 12px 20px; border-radius: 4px; margin-bottom: 20px; border: 1px solid #c3e6cb; width: 100%;">
            {{ session()->get('success') }}
          </div>
          @endif

          <!-- SLA Configuration Card -->
          <div class="sla-card">
            <div class="sla-card-header">
              <h2>Priority Resolution Windows</h2>
              <i class="fa-solid fa-stopwatch" style="color: #2563eb; font-size: 1.2rem;"></i>
            </div>

            <!-- High Priority Row -->
            <div class="sla-row">
              <div class="sla-info">
                <!-- Red indicator dot -->
                <div class="sla-dot" style="background-color: #ef4444;"></div>
                <div class="sla-text">
                  <strong>High Priority</strong>
                  <span>Critical system failures</span>
                </div>
              </div>
              <div class="sla-input-group">
                <!-- Input for hours -->
                <input type="number" id="sla_high" value="{{ $configs['high']->hours ?? 4 }}" class="sla-input" />
                <span class="sla-unit">HOURS</span>
              </div>
              <!-- Progress bar visualization -->
              <div class="sla-bar-container">
                <div class="sla-bar" style="background-color: #ef4444; width: 25%;"></div>
              </div>
            </div>

            <!-- Medium Priority Row -->
            <div class="sla-row">
              <div class="sla-info">
                <!-- Brown indicator dot -->
                <div class="sla-dot" style="background-color: #8b4513;"></div>
                <div class="sla-text">
                  <strong>Medium Priority</strong>
                  <span>Functional degradation</span>
                </div>
              </div>
              <div class="sla-input-group">
                <input type="number" id="sla_medium" value="{{ $configs['medium']->hours ?? 24 }}" class="sla-input" />
                <span class="sla-unit">HOURS</span>
              </div>
              <div class="sla-bar-container">
                <div class="sla-bar" style="background-color: #8b4513; width: 50%;"></div>
              </div>
            </div>

            <!-- Low Priority Row -->
            <div class="sla-row">
              <div class="sla-info">
                <!-- Gray indicator dot -->
                <div class="sla-dot" style="background-color: #4b5563;"></div>
                <div class="sla-text">
                  <strong>Low Priority</strong>
                  <span>General inquiries / minor issues</span>
                </div>
              </div>
              <div class="sla-input-group">
                <input type="number" id="sla_low" value="{{ $configs['low']->hours ?? 72 }}" class="sla-input" />
                <span class="sla-unit">HOURS</span>
              </div>
              <div class="sla-bar-container">
                <div class="sla-bar" style="background-color: #4b5563; width: 100%;"></div>
              </div>
            </div>

            <!-- Action Buttons -->
            <div class="sla-actions">
              <!-- Discard Changes text button -->
              <button class="btn-discard">Discard changes</button>
              <!-- Save Configuration primary button -->
              <button class="btn-save">Save Configuration</button>
            </div>
          </div>
        </div>
      </main>
    </div>
    
    <script>
      document.addEventListener('DOMContentLoaded', function() {
          const btnSave = document.querySelector('.btn-save');
          const btnDiscard = document.querySelector('.btn-discard');

          if (btnSave) {
              btnSave.addEventListener('click', function() {
                  const high = document.getElementById('sla_high').value;
                  const medium = document.getElementById('sla_medium').value;
                  const low = document.getElementById('sla_low').value;
// request sent to the route
                  fetch("{{ route('admin.slaconfig.update') }}", {
                      method: 'POST',
                      headers: {
                          'Content-Type': 'application/json',
                          'X-CSRF-TOKEN': '{{ csrf_token() }}',
                          'Accept': 'application/json'
                      },
                      body: JSON.stringify({ high, medium, low })
                  })
                  // converts server response into js objects
                  .then(response => response.json())
                  .then(data => {
                      if (data.success) {
                          location.reload();
                      } else {
                          alert('Error: ' + (data.message || 'Failed to update configuration'));
                      }
                  })
                  .catch(error => {
                      console.error('Error:', error);
                      alert('An error occurred while saving the configuration.');
                  });
              });
          }

          if (btnDiscard) {
              btnDiscard.addEventListener('click', function() {
                  if (confirm('Discard all unsaved changes?')) {
                      location.reload();
                  }
              });
          }
      });
    </script>
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
