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
    <style>
        .tickets-table {
            width: 100%;
            border-collapse: collapse;
        }
        .tickets-table th {
            padding: 12px 1rem;
            text-align: left;
            font-size: 0.8rem;
            color: #6b7280;
            text-transform: uppercase;
            border-bottom: 1px solid #e5e7eb;
            background: #f9fafb;
        }
        .tickets-table td {
            padding: 12px 1rem;
            font-size: 0.9rem;
            border-bottom: 1px solid #e5e7eb;
            vertical-align: middle;
        }
        .tickets-card {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            overflow: auto;
            margin-bottom: 2rem;
        }
        .action-btns {
            display: flex;
            gap: 10px;
            align-items: center;
            justify-content: center;
        }
        .btn-icon {
            background: none;
            border: none;
            cursor: pointer;
            padding: 6px;
            border-radius: 4px;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .btn-edit {
            color: #3b82f6;
            background: #eff6ff;
        }
        .btn-edit:hover {
            background: #dbeafe;
        }
        .btn-delete {
            color: #ef4444;
            background: #fef2f2;
        }
        .btn-delete:hover {
            background: #fee2e2;
        }
        
        /* Pagination Styling */
        .pagination-links nav {
            display: flex;
            align-items: center;
        }
        .pagination-links .pagination {
            display: flex;
            list-style: none;
            gap: 5px;
            margin: 0;
            padding: 0;
        }
        .pagination-links .page-item .page-link {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            color: #374151;
            text-decoration: none;
            font-size: 0.85rem;
            font-weight: 600;
            transition: all 0.2s;
        }
        .pagination-links .page-item.active .page-link {
            background-color: #2563eb;
            color: white;
            border-color: #2563eb;
        }
        .pagination-links .page-item.disabled .page-link {
            color: #9ca3af;
            cursor: not-allowed;
            background-color: #f9fafb;
        }
        .pagination-links .page-item:not(.active):not(.disabled) .page-link:hover {
            background-color: #f3f4f6;
            border-color: #d1d5db;
        }
        .pagination-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem 1.5rem;
            border-top: 1px solid #e5e7eb;
            background: #fff;
            border-bottom-left-radius: 12px;
            border-bottom-right-radius: 12px;
        }
    </style>
    <title>Incident Management</title>
</head>

<body>
    
    <div class="dashboard-layout">

    
        @include('admin.partials.sidebar')

       
        <main class="main-content">

            
            <header class="top-header">
        <button class="menu-toggle" id="menuToggle">
          <i class="fa-solid fa-bars"></i>
        </button>
                <form method="GET" action="{{ route('admin.incidents') }}" style="flex: 1; max-width: 500px; display: flex; align-items: center; margin: 0 1.5rem;">
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
                       
                        <h1 id="dent-page-title">{{ request('tab') === 'categories' ? 'Incident Configuration' : 'Incident Management' }}</h1>
                    </div>
                    {{-- if category tab is active hide add type button else show add type --}}
                    <button id="dent-add-type-btn" class="newtype" style="text-decoration: none; display: {{ request('tab') === 'categories' ? 'none' : 'flex' }};">
                        <i class="fa-solid fa-plus"></i>Add New Type
                    </button>
                   
                    <button id="dent-add-category-btn" class="newcategory" style="text-decoration: none; display: {{ request('tab') === 'categories' ? 'flex' : 'none' }};">
                        <i class="fa-solid fa-plus"></i>Add New Category
                    </button>
                </div>

                <!--  TAB SWITCHER  -->
                <!-- Two tabs: Categories and Types. Types is active by default -->
                <div class="dent-tabs">
                    <!-- Categories tab - clickable to switch view -->
                    <div class="dent-tab {{ request('tab') === 'categories' ? 'active' : '' }}" id="dent-tab-categories">Categories</div>
                    <!-- Types tab - active by default -->
                    <div class="dent-tab {{ request('tab') !== 'categories' ? 'active' : '' }}" id="dent-tab-types">Types</div>
                </div>

                <!-- TYPES TABLE (Default View) -->
                <div class="tickets-card" id="dent-types-view" style="display: {{ request('tab') === 'categories' ? 'none' : 'block' }};">
                    <table class="tickets-table">
                        <thead>
                            <tr>
                                <th>INCIDENT TITLE</th>
                                <th>PARENT CATEGORY</th>
                                <th style="text-align: center;">ACTIONS</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($types as $type)
                            <tr>
                                <td>
                                    <strong>{{ $type->name }}</strong>
                                </td>
                                <td>
                                    <span style="background: #f3f4f6; color: #374151; padding: 4px 10px; border-radius: 6px; font-size: 0.8rem; font-weight: 600;">
                                        {{ $type->category->name ?? 'None' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="action-btns">
                                        <!-- Edit icon -->
                                        <button class="btn-icon btn-edit edit-type-btn"
                                            data-id="{{ $type->id }}" 
                                            data-name="{{ $type->name }}" 
                                            data-category-id="{{ $type->category_id }}">
                                            <i class="fa-solid fa-pen"></i>
                                        </button>
                                        <form action="{{ route('types.destroy', $type) }}" method="POST" style="margin: 0;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" class="btn-icon btn-delete" onclick="confirmDelete(this, '{{ addslashes($type->name) }}')">
                                                <i class="fa-solid fa-trash"></i>
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
                        Showing {{ $types->firstItem() ?? 0 }} to {{ $types->lastItem() ?? 0 }} of <strong>{{ $types->total() }}</strong> types
                      </div>
                      <div class="pagination-links">
                        {{ $types->appends(['categories_page' => $categories->currentPage(), 'tab' => 'types', 'search' => request('search')])->links('pagination::bootstrap-4') }}
                      </div>
                    </div>
                </div>

                <!-- CATEGORIES TABLE (Hidden by default) -->
                <div class="tickets-card" id="dent-categories-view" style="display: {{ request('tab') === 'categories' ? 'block' : 'none' }};">
                    <table class="tickets-table">
                        <thead>
                            <tr>
                                <th>CATEGORY NAME</th>
                                <th>DESCRIPTION</th>
                                <th style="text-align: center;">ACTIONS</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($categories as $category)
                            <tr>
                                <td>
                                    <strong>{{ $category->name }}</strong>
                                </td>
                                <td>
                                    <div style="color: #6b7280; font-size: 0.85rem;">{{ $category->description }}</div>
                                </td>
                                <td>
                                    <div class="action-btns">
                                        <button class="btn-icon btn-edit edit-category-btn"
                                            data-id="{{ $category->id }}" 
                                            data-name="{{ $category->name }}"
                                            data-description="{{ $category->description }}">
                                            <i class="fa-solid fa-pen"></i>
                                        </button>
                                        <form action="{{ route('categories.destroy', $category) }}" method="POST" style="margin: 0;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" class="btn-icon btn-delete" onclick="confirmDelete(this, '{{ addslashes($category->name) }}')">
                                                <i class="fa-solid fa-trash"></i>
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
                        Showing {{ $categories->firstItem() ?? 0 }} to {{ $categories->lastItem() ?? 0 }} of <strong>{{ $categories->total() }}</strong> categories
                      </div>
                      <div class="pagination-links">
                        {{ $categories->appends(['types_page' => $types->currentPage(), 'tab' => 'categories', 'search' => request('search')])->links('pagination::bootstrap-4') }}
                      </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!--  ADD NEW TYPE MODAL  -->
    <div class="dent-modal" id="dent-type-modal">
        <div class="dent-modal-box">
            <form action="{{ route('types.store') }}" method="POST">
                @csrf
                <div class="dent-double-input">
                    <!-- Parent Category dropdown -->
                    <div class="dent-form-group">
                        <label>PARENT CATEGORY</label>
                        <select name="category_id" required>
                            <option value="">Select a category</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="dent-form-group">
                        <label>INCIDENT TYPE NAME</label>
                        <input type="text" name="name" placeholder="e.g., Laptop not working" required />
                    </div>
                </div>



                <hr class="dent-modal-divider" />

                <div class="dent-modal-footer">
                    <button type="button" class="dent-cancel-btn" id="dent-close-type-modal">Cancel</button>
                    <button type="submit" class="dent-create-btn">Create Incident Type <i class="fa-solid fa-arrow-right"></i></button>
                </div>
            </form>
        </div>
    </div>

    <!--  ADD NEW CATEGORY MODAL -->

    <div class="dent-modal" id="dent-category-modal">
        <div class="dent-modal-box">
            <form action="{{ route('categories.store') }}" method="POST">
                @csrf
                <div class="dent-form-group">
                    <label>CATEGORY NAME</label>
                    <input type="text" name="name" placeholder="e.g., Hardware, Cloud Services" required />
                    <div class="dent-small-text">
                        Unique identifier used across the ticket lifecycle.
                    </div>
                </div>

                <div class="dent-form-group">
                    <label>DESCRIPTION</label>
                    <textarea name="description" placeholder="What does this category encompass?"></textarea>
                </div>

                <hr class="dent-modal-divider" />

                <div class="dent-modal-footer">
                    <button type="button" class="dent-cancel-btn" id="dent-close-category-modal">Cancel</button>
                    <button type="submit" class="dent-create-btn">Create Category</button>
                </div>
            </form>
        </div>
    </div>

    <!-- EDIT CATEGORY MODAL -->
    <div class="dent-modal" id="dent-edit-category-modal">
        <div class="dent-modal-box">
            <form method="POST" action="" id="edit-category-form">
                @csrf
                @method('PUT')
                <div class="dent-form-group">
                    <label>CATEGORY NAME</label>
                    <input type="text" name="name" id="edit-category-name" required />
                </div>
                <div class="dent-form-group">
                    <label>DESCRIPTION</label>
                    <textarea name="description" id="edit-category-description"></textarea>
                </div>
                <hr class="dent-modal-divider" />
                <div class="dent-modal-footer">
                    <button type="button" class="dent-cancel-btn" id="dent-close-edit-category-modal">Cancel</button>
                    <button type="submit" class="dent-create-btn">Update Category</button>
                </div>

            </form>
        </div>
    </div>

    <!-- EDIT TYPE MODAL -->
    <div class="dent-modal" id="dent-edit-type-modal">
        <div class="dent-modal-box">
            <form method="POST" action="" id="edit-type-form">
                @csrf
                @method('PUT')
                <div class="dent-double-input">
                    <div class="dent-form-group">
                        <label>PARENT CATEGORY</label>
                        <select name="category_id" id="edit-type-category-id" required>
                            <option value="">Select a category</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="dent-form-group">
                        <label>INCIDENT TYPE NAME</label>
                        <input type="text" name="name" id="edit-type-name" required />
                    </div>
                </div>

                <hr class="dent-modal-divider" />
                <div class="dent-modal-footer">
                    <button type="button" class="dent-cancel-btn" id="dent-close-edit-type-modal">Cancel</button>
                    <button type="submit" class="dent-create-btn">Update Type</button>
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
         * logic for Edit Category Modal.
         * populates the modal fields with the data attributes from the clicked edit button.
         */
        const editCategoryBtns = document.querySelectorAll('.edit-category-btn');
        const editCategoryModal = document.getElementById('dent-edit-category-modal');
        const editCategoryForm = document.getElementById('edit-category-form');
        const editCategoryName = document.getElementById('edit-category-name');
        const editCategoryDescription = document.getElementById('edit-category-description');
        
        editCategoryBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                // Set the form action URL and populate the input values
                editCategoryForm.action = '/categories/' + this.dataset.id;
                editCategoryName.value = this.dataset.name;
                editCategoryDescription.value = this.dataset.description || '';
                editCategoryModal.style.display = 'flex';
            });
        });
        
        // Modal close listener
        if (document.getElementById('dent-close-edit-category-modal')) {
            document.getElementById('dent-close-edit-category-modal').addEventListener('click', function() {
                editCategoryModal.style.display = 'none';
            });
        }

        /**
         * logic for Edit Incident Type Modal.
         * similar to category, it fills the modal with the type's name and its parent category.
         */
        const editTypeBtns = document.querySelectorAll('.edit-type-btn');
        const editTypeModal = document.getElementById('dent-edit-type-modal');
        const editTypeForm = document.getElementById('edit-type-form');
        const editTypeName = document.getElementById('edit-type-name');
        const editTypeCategory = document.getElementById('edit-type-category-id');
        
        editTypeBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                // Set the dynamic form action and fill the inputs
                editTypeForm.action = '/types/' + this.dataset.id;
                editTypeName.value = this.dataset.name;
                editTypeCategory.value = this.dataset.categoryId;

                editTypeModal.style.display = 'flex';
            });
        });
        
        // Modal close listener
        if (document.getElementById('dent-close-edit-type-modal')) {
            document.getElementById('dent-close-edit-type-modal').addEventListener('click', function() {
                editTypeModal.style.display = 'none';
            });
        }
    });
    </script>

</body>

</html>
