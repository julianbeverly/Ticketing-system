<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="/dist/css/style.css" />
   
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
    />
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <title>Department</title>
</head>
<body>
    <div class="dashboard-layout">
        
        @include('admin.partials.sidebar')

        <main class="main-content">
             <header class="top-header">
                <button class="menu-toggle" id="menuToggle">
                  <i class="fa-solid fa-bars"></i>
                 </button>
                <form method="GET" action="{{ route('admin.dept') }}" style="flex: 1; max-width: 500px; display: flex; align-items: center; margin: 0 1.5rem;">
                    <input type="hidden" name="tab" id="active-tab-input" value="{{ request('tab', 'types') }}">
                    <div class="search-container" style="width: 100%; margin: 0;">
                        <i class="fa-solid fa-magnifying-glass" onclick="this.closest('form').submit();" style="cursor: pointer;"></i>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search parameters..." />
                    </div>
                </form>
                
                <div class="header-actions">
                    
                    <a href="{{ route('employee.profile') }}" class="icon-btn profile-btn">
                        <i class="fa-regular fa-circle-user"></i>
                    </a>
                </div>
            </header>
            <div class="dashboard-content">
                 @if(session('success'))
                    <div style="background-color: #d4edda; color: #155724; padding: 15px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #c3e6cb;">
                        <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
                    </div>
                @endif

                @if($errors->any())
                    <div style="background-color: #f8d7da; color: #721c24; padding: 15px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #f5c6cb;">
                        <ul style="margin: 0; padding-left: 20px;">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="page-header-title">
                    <div>
                       
                        <h1 id="dept-page-title">{{ request('tab') === 'companies' ? 'Company' : 'Department' }}</h1>
                    </div>
                    {{-- if category tab is active hide add type button else show add type --}}
                    <button id="dept-add-department-btn" class="newtype" style="text-decoration: none; display: {{ request('tab') === 'companies' ? 'none' : 'flex' }};">
                        <i class="fa-solid fa-plus"></i>Add Department
                    </button>
                   
                    <button id="dept-add-company-btn" class="newcategory" style="text-decoration: none; display: {{ request('tab') === 'companies' ? 'flex' : 'none' }};">
                        <i class="fa-solid fa-plus"></i>Add Company
                    </button>
                </div>

                <!--  TAB SWITCHER  -->
                <!-- Two tabs: Categories and Types. Types is active by default -->
                <div class="dent-tabs">
                    <!-- Categories tab - clickable to switch view -->
                    <div class="dent-tab {{ request('tab') === 'companies' ? 'active' : '' }}" id="dept-tab-companies">Companies</div>
                    <!-- Types tab - active by default -->
                    <div class="dent-tab {{ request('tab') !== 'companies' ? 'active' : '' }}" id="dept-tab-departments">Departments</div>
                </div>

                <!-- DEPARTMENT TABLE (Default View) -->
                <div class="tickets-card" id="dept-departments-view" style="display: {{ request('tab') === 'companies' ? 'none' : 'block' }};">
                    <table class="tickets-table">
                        <thead>
                            <tr>
                                <th>DEPARTMENT TITLE</th>
                                <th>PARENT COMPANY</th>
                                <th style="text-align: center;">ACTIONS</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($departments as $department)
                            <tr>
                                <td>
                                    <strong>{{ $department->name }}</strong>
                                </td>
                                <td>
                                    <span style="background: #f3f4f6; color: #374151; padding: 4px 10px; border-radius: 6px; font-size: 0.8rem; font-weight: 600;">
                                        {{ $department->company->name ?? 'None' }}
                                    </span>
                                </td>
                                <td style="text-align: center;">
                                    <div style="display: flex; gap: 6px; justify-content: center; align-items: center;">
                                        <button class="btn-icon btn-edit edit-department-btn"
                                            data-id="{{ $department->id }}" 
                                            data-name="{{ $department->name }}" 
                                            data-company-id="{{ $department->company_id }}"
                                            style="background: #FBBF24; color: #111111; border: none; padding: 6px 14px; border-radius: 6px; font-weight: 700; font-size: 0.8rem; cursor: pointer;">
                                            Edit
                                        </button>
                                        <form action="{{ route('departments.destroy', $department) }}" method="POST" style="margin: 0;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" class="btn-icon btn-delete" onclick="confirmDelete(this, '{{ addslashes($department->name) }}')"
                                                style="background: #111111; color: #ffffff !important; border: none; padding: 6px 14px; border-radius: 6px; font-weight: 700; font-size: 0.8rem; cursor: pointer;">
                                                Delete
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="pagination-container">
                      <div>
                        Showing {{ $departments->firstItem() ?? 0 }} to {{ $departments->lastItem() ?? 0 }} of <strong>{{ $departments->total() }}</strong> departments
                      </div>
                      <div class="pagination-links">
                        {{ $departments->appends(['companies_page' => $companies->currentPage(), 'tab' => 'departments', 'search' => request('search')])->links('pagination::bootstrap-4') }}
                      </div>
                    </div>
                </div>

                <div class="tickets-card" id="dept-companies-view" style="display: {{ request('tab') === 'companies' ? 'block' : 'none' }};">
                    <table class="tickets-table">
                        <thead>
                            <tr>
                                <th>COMPANY NAME</th>
                                <th>DESCRIPTION</th>
                                <th style="text-align: center;">ACTIONS</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($companies as $company)
                            <tr>
                                <td>
                                    <strong>{{ $company->name }}</strong>
                                </td>
                                <td>
                                    <div style="color: #6b7280; font-size: 0.85rem;">{{ $company->description }}</div>
                                </td>
                                <td style="text-align: center;">
                                    <div style="display: flex; gap: 6px; justify-content: center; align-items: center;">
                                        <button class="btn-icon btn-edit edit-company-btn"
                                            data-id="{{ $company->id }}" 
                                            data-name="{{ $company->name }}"
                                            data-description="{{ $company->description }}"
                                            style="background: #FBBF24; color: #111111; border: none; padding: 6px 14px; border-radius: 6px; font-weight: 700; font-size: 0.8rem; cursor: pointer;">
                                            Edit
                                        </button>
                                        <form action="{{ route('companies.destroy', $company) }}" method="POST" style="margin: 0;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" class="btn-icon btn-delete" onclick="confirmDelete(this, '{{ addslashes($company->name) }}')"
                                                style="background: #111111; color: #ffffff; border: none; padding: 6px 14px; border-radius: 6px; font-weight: 700; font-size: 0.8rem; cursor: pointer;">
                                                Delete
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="pagination-container">
                      <div>
                        Showing {{ $companies->firstItem() ?? 0 }} to {{ $companies->lastItem() ?? 0 }} of <strong>{{ $companies->total() }}</strong> companies
                      </div>
                      <div class="pagination-links">
                        {{ $companies->appends(['deparments_page' => $departments->currentPage(), 'tab' => 'companies', 'search' => request('search')])->links('pagination::bootstrap-4') }}
                      </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!--  ADD NEW DEPARTMENT MODAL  -->
    <div class="dent-modal" id="dept-department-modal">
        <div class="dent-modal-box">
            <form action="{{ route('departments.store') }}" method="POST">
                @csrf
                <div class="dent-double-input">
                    <!-- Parent Category dropdown -->
                    <div class="dent-form-group">
                        <label>PARENT COMPANY</label>
                        <select name="company_id" required>
                            <option value="">Select a company</option>
                            @foreach($companies as $company)
                                <option value="{{ $company->id }}">{{ $company->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="dent-form-group">
                        <label>DEPARTMENT NAME</label>
                        <input type="text" name="name" placeholder="e.g., Sales, Technology" required />
                    </div>
                </div>



                <hr class="dent-modal-divider" />

                <div class="dent-modal-footer">
                    <button type="button" class="dent-cancel-btn" id="dept-close-department-modal">Cancel</button>
                    <button type="submit" class="dent-create-btn">Create Department<i class="fa-solid fa-arrow-right"></i></button>
                </div>
            </form>
        </div>
    </div>

    <!--  ADD NEW COMPANY MODAL -->

    <div class="dent-modal" id="dept-company-modal">
        <div class="dent-modal-box">
            <form action="{{ route('companies.store') }}" method="POST">
                @csrf
                <div class="dent-form-group">
                    <label>COMPANY NAME</label>
                    <input type="text" name="name" placeholder="e.g., ICLAN, MTN" required />
                    <div class="dent-small-text">
                        Unique identifier used across the ticket lifecycle.
                    </div>
                </div>

                <div class="dent-form-group">
                    <label>DESCRIPTION</label>
                    <textarea name="description" placeholder="Brief description about the company"></textarea>
                </div>

                <hr class="dent-modal-divider" />

                <div class="dent-modal-footer">
                    <button type="button" class="dent-cancel-btn" id="dept-close-company-modal">Cancel</button>
                    <button type="submit" class="dent-create-btn">Create Company</button>
                </div>
            </form>
        </div>
    </div>

    <!-- EDIT COMPANY MODAL -->
    <div class="dent-modal" id="dept-edit-company-modal">
        <div class="dent-modal-box">
            <form method="POST" action="" id="edit-company-form">
                @csrf
                @method('PUT')
                <div class="dent-form-group">
                    <label>CATEGORY NAME</label>
                    <input type="text" name="name" id="edit-company-name" required />
                </div>
                <div class="dent-form-group">
                    <label>DESCRIPTION</label>
                    <textarea name="description" id="edit-company-description"></textarea>
                </div>
                <hr class="dent-modal-divider" />
                <div class="dent-modal-footer">
                    <button type="button" class="dent-cancel-btn" id="dept-close-edit-company-modal">Cancel</button>
                    <button type="submit" class="dent-create-btn">Update Company</button>
                </div>
            </form>
        </div>
    </div>

     <!-- EDIT department MODAL -->
    <div class="dent-modal" id="dept-edit-department-modal">
        <div class="dent-modal-box">
            <form method="POST" action="" id="edit-department-form">
                @csrf
                @method('PUT')
                <div class="dent-double-input">
                    <div class="dent-form-group">
                        <label>COMPANY</label>
                        <select name="company_id" id="edit-department-company-id" required>
                            <option value="">Select a company</option>
                            @foreach($companies as $company)
                                <option value="{{ $company->id }}">{{ $company->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="dent-form-group">
                        <label>DEPARTMENT NAME</label>
                        <input type="text" name="name" id="edit-department-name" required />
                    </div>
                </div>

                <hr class="dent-modal-divider" />
                <div class="dent-modal-footer">
                    <button type="button" class="dent-cancel-btn" id="dept-close-edit-department-modal">Cancel</button>
                    <button type="submit" class="dent-create-btn">Update Department</button>
                </div>
            </form>
        </div>
    </div>

    <script src="/dist/js/modals.js"></script>

    <script>
    /**
     * confirmation modal for delete actions using SweetAlert2.
     * Prevents accidental deletion by requiring user confirmation.
     */
    function confirmDelete(button, name) {
        const form = button.closest("form");
        Swal.fire({
            title: "Are you sure?",
            text: "Do you really want to delete " + name + "?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#d33",
            cancelButtonColor: "#6c757d",
            confirmButtonText: "Yes, delete it",
            cancelButtonText: "Cancel"
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        /**
         * logic for Edit Company Modal.
         * populates the modal fields with the data attributes from the clicked edit button.
         */
        const editCompanyBtns = document.querySelectorAll('.edit-company-btn');
        const editCompanyModal = document.getElementById('dept-edit-company-modal');
        const editCompanyForm = document.getElementById('edit-company-form');
        const editCompanyName = document.getElementById('edit-company-name');
        const editCompanyDescription = document.getElementById('edit-company-description');
        
        editCompanyBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                // Set the form action URL and populate the input values
                editCompanyForm.action = '/companies/' + this.dataset.id;
                editCompanyName.value = this.dataset.name;
                editCompanyDescription.value = this.dataset.description || '';
                editCompanyModal.style.display = 'flex';
            });
        });
        
        // Modal close listener
        if (document.getElementById('dept-close-edit-company-modal')) {
            document.getElementById('dept-close-edit-company-modal').addEventListener('click', function() {
                editCompanyModal.style.display = 'none';
            });
        }

        /**
         * logic for Edit department  Modal.
         * similar to company, it fills the modal with the type's name and its parent company.
         */
        const editDepartmentBtns = document.querySelectorAll('.edit-department-btn');
        const editDepartmentModal = document.getElementById('dept-edit-department-modal');
        const editDepartmentForm = document.getElementById('edit-department-form');
        const editDepartmentName = document.getElementById('edit-department-name');
        const editDepartmentCompany = document.getElementById('edit-department-company-id');
        
        editDepartmentBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                // Set the dynamic form action and fill the inputs
                editDepartmentForm.action = '/departments/' + this.dataset.id;
                editDepartmentName.value = this.dataset.name;
                editDepartmentCompany.value = this.dataset.companyId;

                editDepartmentModal.style.display = 'flex';
            });
        });
        
        // Modal close listener
        if (document.getElementById('dept-close-edit-department-modal')) {
            document.getElementById('dept-close-edit-department-modal').addEventListener('click', function() {
                editDepartmentModal.style.display = 'none';
            });
        }
    });
    </script>
    
</body>
</html>