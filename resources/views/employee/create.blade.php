<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
     <link rel="stylesheet" href="/dist/css/style.css" />
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
    />
    <title>Create Ticket</title>
</head>
<body>
     <div class="dashboard-layout">
        @include('employee.partials.sidebar')

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

      <div class="dashboard-content">

          <!-- Success Flash Message Banner -->
          @if(session('success'))
            <div style="background: #d4edda; color: #155724; padding: 1rem; border-radius: 8px; margin-bottom: 1rem; border: 1px solid #c3e6cb;">
              <i class="fa-solid fa-circle-check" style="margin-right: 0.5rem;"></i>
              {{ session('success') }}
            </div>
          @endif

          <!-- Validation Errors Banner -->
          @if($errors->any())
            <div style="background: #f8d7da; color: #721c24; padding: 1rem; border-radius: 8px; margin-bottom: 1rem; border: 1px solid #f5c6cb;">
              <i class="fa-solid fa-circle-exclamation" style="margin-right: 0.5rem;"></i>
              <strong>Please fix the following errors:</strong>
              <ul style="margin: 0.5rem 0 0 1.5rem;">
                @foreach($errors->all() as $error)
                  <li>{{ $error }}</li>
                @endforeach
              </ul>
            </div>
          @endif

          <!-- Breadcrumbs -->
          <div class="breadcrumb" style="text-transform: uppercase">
            <a href="{{ route('employee.tickets') }}" style="color: #6b7280; text-decoration: none"
              >TICKETS MANAGEMENT</a
            >
            &gt;
            <span style="color: #111111; font-weight: 600"
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
            <!-- Form submits via POST to the store route with file upload support -->
            <form method="POST" action="{{ route('employee.tickets.store') }}" enctype="multipart/form-data" id="employeeCreateTicketForm">
              @csrf

              <!-- Category and Type -->
              <div class="form-row">
                <div class="form-group half-width">
                  <label>INCIDENT CATEGORY</label>
                  <div class="custom-select">
                    <!-- Dynamically populated from the database categories -->
                    <select name="category_id" id="category_id">
                      <option value="" disabled selected hidden>
                        Select Category
                      </option>
                      @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                          {{ $category->name }}
                        </option>
                      @endforeach
                    </select>
                    <i class="fa-solid fa-chevron-down"></i>
                  </div>
                </div>
                <div class="form-group half-width">
                  <label>INCIDENT TYPE</label>
                  <div class="custom-select">
                    <!-- Dynamically populated from the database types -->
                    <select name="type_id" id="type_id">
                      <option value="" disabled selected hidden>
                        Select Type
                      </option>
                      @foreach($types as $type)
                        <option value="{{ $type->id }}" data-category="{{ $type->category_id }}" {{ old('type_id') == $type->id ? 'selected' : '' }}>
                          {{ $type->name }}
                        </option>
                      @endforeach
                      <option value="other" {{ old('type_id') == 'other' ? 'selected' : '' }}>Other</option>
                    </select>
                    <i class="fa-solid fa-chevron-down"></i>
                  </div>
                </div>
              </div>

           

              <!-- Custom Incident Type (Shown only if 'Other' is selected) -->
              <div class="form-group" id="custom_type_group" style="display: {{ old('type_id') == 'other' ? 'block' : 'none' }};">
                <label>CUSTOM INCIDENT TYPE (IF NOT LISTED)</label>
                <input
                  type="text"
                  name="custom_type"
                  id="custom_type"
                  value="{{ old('custom_type') }}"
                  placeholder="Enter your specific incident type here..."
                />
              </div>

              <!-- Subject -->
              <div class="form-group">
                <label>SUBJECT</label>
                <input
                  type="text"
                  name="subject"
                  value="{{ old('subject') }}"
                  placeholder="Short summary of the problem (e.g., Cannot connect to VPN)"
                  required
                />
              </div>

              <!-- Description -->
              <div class="form-group">
                <label>DESCRIPTION</label>
                <textarea
                  name="description"
                  placeholder="Provide as much detail as possible, including steps to reproduce the error..."
                  rows="5"
                  required
                >{{ old('description') }}</textarea>
              </div>

              <!-- Priority Level -->
              <div class="form-group priority-row">
                <div class="form-group-inner">
                  <label>PRIORITY LEVEL</label>
                  <div class="custom-select">
                    <select name="priority">
                      <option value="low" {{ old('priority', 'low') == 'low' ? 'selected' : '' }}>
                        Low - General Request
                      </option>
                      <option value="medium" {{ old('priority') == 'medium' ? 'selected' : '' }}>Medium - System Issue</option>
                      <option value="high" {{ old('priority') == 'high' ? 'selected' : '' }}>High - Critical Failure</option>
                    </select>
                    <i class="fa-solid fa-chevron-down"></i>
                  </div>
                </div>
                <!-- Display Priority Deadline -->
                <div class="priority-time"></div>
              </div>

            

              <!-- File Upload — supports multiple images and videos -->
              <div class="form-group">
                <label>EVIDENCE & ATTACHMENTS</label>
                <div class="file-upload-box" id="uploadBox" style="cursor: pointer; position: relative;">
                  <!-- Hidden file input allowing multiple file selection -->
                  <input
                    type="file"
                    name="attachments[]"
                    id="fileInput"
                    multiple
                    accept="image/png,image/jpg,image/jpeg,video/mp4,video/avi,video/quicktime"
                    style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; opacity: 0; cursor: pointer;"
                  />
                  <div class="upload-icon">
                    <!-- Cloud Upload Icon -->
                    <i class="fa-solid fa-cloud-arrow-up"></i>
                  </div>
                  <strong>Click to upload or drag and drop</strong>
                  <p>Supports PNG, JPG, MP4 (Max 25MB each) — Multiple files allowed</p>
                </div>
                <!-- Display selected file names -->
                <div id="fileList" style="margin-top: 0.5rem; font-size: 0.85rem; color: #6b7280;"></div>
              </div>

              <!-- Action Buttons -->
              <div class="form-actions" style="display: flex; justify-content: space-between; align-items: center; margin-top: 2rem;">
                <button type="button" class="btn-cancel" onclick="window.history.back()" style="background: none; border: none; color: #6b7280; font-weight: 600; cursor: pointer;">Cancel</button>
                <button type="submit" class="btn-submit" id="submitBtn" style="width: auto; padding: 0.8rem 2rem; background: #111111; color: #ffff !important; border: none; border-radius: 8px; font-weight: bold; cursor: pointer; display: flex; align-items: center; gap: 0.5rem;">
                  Submit Ticket <i class="fa-solid fa-paper-plane"></i>
                </button>
              </div>
            </form>
          </div>
        </div>
      

    </main>

    </div>

    <!-- JavaScript to filter types by selected category and show file names -->
    <script>
      document.addEventListener('DOMContentLoaded', function() {
        const categorySelect = document.getElementById('category_id');
        const typeSelect = document.getElementById('type_id');
        const customTypeGroup = document.getElementById('custom_type_group');
        const customTypeInput = document.getElementById('custom_type');
        
        // Save the original options
        const allTypeOptions = Array.from(typeSelect.options);
        
        function filterTypes() {
          const selectedCategoryId = categorySelect.value;
          
          // Clear current options
          typeSelect.innerHTML = '';
          
          // Add back the default "Select Type" and matching options
          allTypeOptions.forEach(option => {
            if (option.value === "" || option.value === "other" || option.dataset.category === selectedCategoryId) {
              typeSelect.appendChild(option);
            }
          });
          
          // Try to preserve the old selection if it's still valid
          let oldVal = "{{ old('type_id') }}";
          if (oldVal && Array.from(typeSelect.options).some(opt => opt.value === oldVal)) {
            typeSelect.value = oldVal;
          } else if (typeSelect.value !== "other") {
            typeSelect.value = "";
          }
          toggleCustomType();
        }
        
        function toggleCustomType() {
          if (typeSelect.value === 'other') {
            customTypeGroup.style.display = 'block';
            customTypeInput.setAttribute('required', 'required');
          } else {
            customTypeGroup.style.display = 'none';
            customTypeInput.removeAttribute('required');
            customTypeInput.value = '';
          }
        }
        
        categorySelect.addEventListener('change', filterTypes);
        typeSelect.addEventListener('change', toggleCustomType);
        
        // Initial setup on page load
        if(categorySelect.value) {
          filterTypes();
        }
        toggleCustomType();

        // File upload UI logic
        const fileInput = document.getElementById('fileInput');
        const fileList = document.getElementById('fileList');
        if (fileInput && fileList) {
          fileInput.addEventListener('change', function() {
            if (this.files && this.files.length > 0) {
              let names = Array.from(this.files).map(f => f.name).join(', ');
              fileList.textContent = 'Selected: ' + names;
            } else {
              fileList.textContent = '';
            }
          });
        }
      });
    </script>
</body>
</html>
