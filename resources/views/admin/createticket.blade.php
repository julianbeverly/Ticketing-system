<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Create Ticket</title>
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
    />

    <link rel="stylesheet" href="/dist/css/style.css" />
  </head>

  <body>
    <div class="dashboard-layout">
      <!-- Sidebar Navigation -->
        @include('admin.partials.sidebar')
      <div class="sidebar-overlay" id="sidebarOverlay"></div>

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

        <!-- Dashboard Content Specific to Create Ticket -->
        <div class="dashboard-content">
          <!-- Breadcrumbs -->
          <div class="breadcrumb" style="text-transform: uppercase">
            <a href="{{ route('admin.tickets') }}" style="color: #6b7280; text-decoration: none"
              >TICKETS MANAGEMENT</a
            >
            &gt;
            <span style="color: #0b57d0; font-weight: 600"
              >CREATE NEW TICKET</span
            >
          </div>

          <!-- Page Title -->
          <div class="page-header-title" style="margin-bottom: 0">
            <div>
              <h1>Open a Support Case</h1>
              <p style="color: #6b7280; margin-top: 0.5rem">
                Report an incident or request technical assistance. Our team
                will review your request shortly.
              </p>
            </div>
          </div>

          <!-- Create Ticket Form Card -->
          <div class="create-ticket-card">
            <form method="POST" action="{{ route('employee.tickets.store') }}" enctype="multipart/form-data" id="adminCreateTicketForm">
              @csrf
              <!-- Employee Selection -->
              <div class="form-group">
                <label>SELECT EMPLOYEE <span class="required">*</span></label>
                <div class="custom-select">
                  <select name="user_id" required>
                    <option value="" disabled selected hidden>
                      Select Employee
                    </option>
                    @foreach($employees as $employee)
                      <option value="{{ $employee->id }}">{{ $employee->name }}</option>
                    @endforeach
                  </select>
                  <i class="fa-solid fa-chevron-down"></i>
                </div>
              </div>

              <!-- Category and Type -->
              <div class="form-row">
                <div class="form-group half-width">
                  <label>INCIDENT CATEGORY</label>
                  <div class="custom-select">
                    <select name="category_id" id="category_id">
                      <option value="" disabled selected hidden>
                        Select Category
                      </option>
                      @foreach($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                      @endforeach
                    </select>
                    <i class="fa-solid fa-chevron-down"></i>
                  </div>
                </div>
                <div class="form-group half-width">
                  <label>INCIDENT TYPE</label>
                  <div class="custom-select">
                    <select name="type_id" id="type_id">
                      <option value="" disabled selected hidden>
                        Select Type
                      </option>
                      @foreach($types as $type)
                        <option value="{{ $type->id }}" data-category="{{ $type->category_id }}">{{ $type->name }}</option>
                      @endforeach
                      <option value="other">Other</option>
                    </select>
                    <i class="fa-solid fa-chevron-down"></i>
                  </div>
                </div>
              </div>

              <!-- Custom Incident Type -->
              <div class="form-group" id="custom_type_group" style="display: none;">
                <label>CUSTOM INCIDENT TYPE (IF NOT LISTED)</label>
                <input
                  type="text"
                  name="custom_type"
                  id="custom_type"
                  placeholder="Enter your specific incident type here..."
                />
              </div>

              <!-- Subject -->
              <div class="form-group">
                <label>SUBJECT</label>
                <input
                  type="text"
                  name="subject"
                  required
                  placeholder="Short summary of the problem (e.g., Cannot connect to VPN)"
                />
              </div>

              <!-- Description -->
              <div class="form-group">
                <label>DESCRIPTION</label>
                <textarea
                  name="description"
                  required
                  placeholder="Provide as much detail as possible, including steps to reproduce the error..."
                  rows="5"
                ></textarea>
              </div>

              <!-- Priority Level -->
              <div class="form-group priority-row">
                <div class="form-group-inner">
                  <label>PRIORITY LEVEL</label>
                  <div class="custom-select">
                    <select name="priority">
                      <option value="low">
                        Low - General Request
                      </option>
                      <option value="medium">Medium - System Issue</option>
                      <option value="high">High - Critical Failure</option>
                    </select>
                    <i class="fa-solid fa-chevron-down"></i>
                  </div>
                </div>
                <!-- Display Priority Deadline -->
                <div class="priority-time">2 hours</div>
              </div>

              <!-- File Upload -->
              <div class="form-group">
                <label>EVIDENCE & ATTACHMENTS</label>
                <div class="file-upload-box" style="cursor: pointer; position: relative;">
                  <input
                    type="file"
                    name="attachments[]"
                    multiple
                    accept="image/*,video/*"
                    style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; opacity: 0; cursor: pointer;"
                  />
                  <div class="upload-icon">
                    <i class="fa-solid fa-cloud-arrow-up"></i>
                  </div>
                  <strong>Click to upload or drag and drop</strong>
                  <p>Supports PNG, JPG, MP4 (Max 25MB)</p>
                </div>
              </div>

              <!-- Action Buttons -->
              <div class="form-actions" style="display: flex; justify-content: space-between; align-items: center; margin-top: 2rem;">
                <button type="button" class="btn-cancel" onclick="window.history.back()" style="background: none; border: none; color: #6b7280; font-weight: 600; cursor: pointer;">Cancel</button>
                <button type="submit" class="btn-submit" id="submitBtn" style="width: auto; padding: 0.8rem 2rem; background: #0b57d0; color: white; border: none; border-radius: 8px; font-weight: bold; cursor: pointer; display: flex; align-items: center; gap: 0.5rem;">
                  Submit Ticket <i class="fa-solid fa-paper-plane"></i>
                </button>
              </div>
            </form>
          </div>
        </div>
      </main>
    </div>
    <script>
      const menuToggle = document.getElementById("menuToggle");
      const sidebar = document.querySelector(".sidebar-nav");
      const overlay = document.getElementById("sidebarOverlay");

      menuToggle.addEventListener("click", () => {
        sidebar.classList.toggle("active");
        overlay.classList.toggle("show");
      });

      overlay.addEventListener("click", () => {
        sidebar.classList.remove("active");
        overlay.classList.remove("show");
      });

      // Dynamic Type Filtering
      const categorySelect = document.getElementById('category_id');
      const typeSelect = document.getElementById('type_id');
      const customTypeGroup = document.getElementById('custom_type_group');
      const allTypeOptions = typeSelect.querySelectorAll('option[data-category]');

      categorySelect.addEventListener('change', function() {
          const selectedCategoryId = this.value;
          typeSelect.value = '';
          customTypeGroup.style.display = 'none';
          
          allTypeOptions.forEach(option => {
              if (option.getAttribute('data-category') === selectedCategoryId) {
                  option.style.display = '';
              } else {
                  option.style.display = 'none';
              }
          });
      });

      typeSelect.addEventListener('change', function() {
          if (this.value === 'other') {
              customTypeGroup.style.display = 'block';
              document.getElementById('custom_type').setAttribute('required', 'required');
          } else {
              customTypeGroup.style.display = 'none';
              document.getElementById('custom_type').removeAttribute('required');
          }
      });

      // Prevent Double Submission
      const createTicketForm = document.getElementById('adminCreateTicketForm');
      const submitBtn = document.getElementById('submitBtn');

      if (createTicketForm) {
          createTicketForm.addEventListener('submit', function() {
              if (submitBtn) {
                  submitBtn.disabled = true;
                  submitBtn.innerHTML = 'Processing... <i class="fa-solid fa-spinner fa-spin"></i>';
                  submitBtn.style.opacity = '0.7';
                  submitBtn.style.cursor = 'not-allowed';
              }
          });
      }
    </script>
  </body>
</html>
