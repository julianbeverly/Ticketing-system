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
                <button type="submit" class="btn-submit" id="submitBtn" style="width: auto; padding: 0.8rem 2rem; background: #0b57d0; color: white; border: none; border-radius: 8px; font-weight: bold; cursor: pointer; display: flex; align-items: center; gap: 0.5rem;">
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
      // Filter incident types based on selected category
      const categorySelect = document.getElementById('category_id');
      const typeSelect = document.getElementById('type_id');
      const allTypeOptions = typeSelect.querySelectorAll('option[data-category]');

      categorySelect.addEventListener('change', function() {
        const selectedCategoryId = this.value;
        
        // Reset type dropdown
        typeSelect.value = '';
        customTypeGroup.style.display = 'none'; // Hide custom type on category change
        
        // Show/hide type options based on selected category
        allTypeOptions.forEach(option => {
          if (option.getAttribute('data-category') === selectedCategoryId) {
            option.style.display = '';
          } else {
            option.style.display = 'none';
          }
        });
      });

      // Show/Hide custom incident type based on 'Other' selection
      const customTypeGroup = document.getElementById('custom_type_group');
      typeSelect.addEventListener('change', function() {
        if (this.value === 'other') {
          customTypeGroup.style.display = 'block';
          document.getElementById('custom_type').setAttribute('required', 'required');
        } else {
          customTypeGroup.style.display = 'none';
          document.getElementById('custom_type').removeAttribute('required');
        }
      });

      // Display selected file names when files are chosen
      const fileInput = document.getElementById('fileInput');
      const fileList = document.getElementById('fileList');

      fileInput.addEventListener('change', function() {
        fileList.innerHTML = '';
        if (this.files.length > 0) {
          const list = document.createElement('ul');
          list.style.listStyle = 'none';
          list.style.padding = '0';
          
          for (let i = 0; i < this.files.length; i++) {
            const li = document.createElement('li');
            li.style.padding = '0.25rem 0';
            // Show file icon based on type
            const icon = this.files[i].type.startsWith('video/') 
              ? '<i class="fa-solid fa-video" style="margin-right: 0.5rem; color: #0b57d0;"></i>' 
              : '<i class="fa-solid fa-image" style="margin-right: 0.5rem; color: #0b57d0;"></i>';
            li.innerHTML = icon + this.files[i].name + ' (' + (this.files[i].size / 1024 / 1024).toFixed(2) + ' MB)';
            list.appendChild(li);
          }
          fileList.appendChild(list);
        }
      });

      // Prevent Double Submission
      const createTicketForm = document.getElementById('employeeCreateTicketForm');
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
