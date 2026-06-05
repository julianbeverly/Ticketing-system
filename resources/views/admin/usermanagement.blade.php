<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>User Management</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
    <link rel="stylesheet" href="/dist/css/style.css" />
    <style>
        .custom-modal-box {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: #ffffff;
            padding: 2rem;
            border-radius: 8px;
            z-index: 999999 !important;
            width: 400px;
            max-width: 90%;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
            visibility: visible !important;
            opacity: 1 !important;
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
    </style>
</head>

<body>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function confirmSuspend(button) {
            try {
                const form = button.closest("form");
                const userName = button.closest("tr").querySelector("strong").innerText;

                Swal.fire({
                    title: "Are you sure?",
                    text: "Do you really want to suspend " + userName + "?",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#d33",
                    cancelButtonColor: "#6c757d",
                    confirmButtonText: "Yes, suspend",
                    cancelButtonText: "Cancel"
                }).then((result) => {
                    if (result.isConfirmed) {
                        HTMLFormElement.prototype.submit.call(form);
                    }
                });
            } catch (error) {
                console.error("Error in confirmSuspend:", error);
                alert("Failed to initiate suspension. Please check the console.");
            }
        }

        function confirmUnsuspend(button) {
            try {
                const form = button.closest("form");
                const userName = button.closest("tr").querySelector("strong").innerText;

                Swal.fire({
                    title: "Are you sure?",
                    text: "Do you really want to activate " + userName + "?",
                    icon: "info",
                    showCancelButton: true,
                    confirmButtonColor: "#28a745",
                    cancelButtonColor: "#6c757d",
                    confirmButtonText: "Yes, activate",
                    cancelButtonText: "Cancel"
                }).then((result) => {
                    if (result.isConfirmed) {
                        HTMLFormElement.prototype.submit.call(form);
                    }
                });
            } catch (error) {
                console.error("Error in confirmUnsuspend:", error);
                alert("Failed to initiate activation. Please check the console.");
            }
        }
    </script>
    <div class="dashboard-layout">
        @include('admin.partials.sidebar')

        <main class="main-content">
            <!-- Top Header (Search & Actions) -->
            <header class="top-header">
                <form method="GET" action="{{ route('user.index') }}"
                    style="flex: 1; max-width: 500px; display: flex; align-items: center; margin: 0 1.5rem;">
                    @if (request('role'))
                        <input type="hidden" name="role" value="{{ request('role') }}">
                    @endif
                    <div class="search-container" style="width: 100%; margin: 0;">
                        <i class="fa-solid fa-magnifying-glass" onclick="this.closest('form').submit();"
                            style="cursor: pointer;"></i>
                        <input type="text" name="search" value="{{ request('search') }}"
                            placeholder="Search directives, tickets, or users..." />
                    </div>
                </form>
                <div class="header-actions">
                    
                    <a href="{{ route('employee.profile') }}" class="icon-btn profile-btn">
                        <i class="fa-regular fa-circle-user"></i>
                    </a>
                </div>
            </header>
            <div class="dashboard-content">
                <div class="breadcrumb">
                    <a href="#">User Management</a> &gt;
                    <!-- Text showing current specific page location -->
                    <span>Users</span>
                </div>
                <div class="page-header-title">
                    <div>
                        <h1>User Management</h1>
                    </div>

                    <div style="display: flex; gap: 1rem; align-items: center;">
                        <form action="{{ route('user.index') }}" method="GET" id="roleFilterForm">
                            <select name="role" onchange="this.form.submit()"
                                style="padding: 0.6rem 1rem; border-radius: 8px; border: 1px solid #e2e8f0; font-size: 0.9rem; font-weight: 600; color: #1e293b; background: #f8faff; cursor: pointer; outline: none; transition: all 0.2s;">
                                <option value="">All Roles</option>
                                <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                                <option value="technician" {{ request('role') == 'technician' ? 'selected' : '' }}>
                                    Technician</option>
                                <option value="employee" {{ request('role') == 'employee' ? 'selected' : '' }}>Employee
                                </option>
                            </select>
                        </form>

                        <button id="addUserBtn" class="btn-create-ticket" style="border: none; cursor: pointer;" onclick="openModal()">
                            <i class="fa-solid fa-user-plus"></i> Add Users
                        </button>
                    </div>
                </div>

                @if (session()->has('success'))
                    <div
                        style="background-color: #d4edda; color: #155724; padding: 12px 20px; border-radius: 4px; margin-bottom: 20px; border: 1px solid #c3e6cb;">
                        {{ session()->get('success') }}
                    </div>
                @endif

                <div class="users-card">
                    <table class="users-table">
                        <!-- Table header containing column names -->
                        <thead>
                            <tr>
                                <th>USER IDENTITY</th>
                                <th>ROLE</th>
                                <th>SPECIALITY</th>
                                <th>CONTACT</th>
                                <th>STATUS</th>
                                <th>EDIT</th>
                                <th>ACTION</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($users as $user)
                                <tr>
                                    <td>
                                        <div class="user-name">
                                            <strong>{{ $user->name }}</strong><br />
                                            <span>{{ $user->email }}</span>
                                        </div>
                                    </td>

                                    <td>
                                        <span>{{ $user->role }}</span>
                                    </td>

                                    <td>
                                        <span>{{ $user->speciality }}</span>
                                    </td>

                                    <td>
                                        <div class="user-contact">{{ $user->phone }}</div>
                                    </td>

                                    <td>
                                        @if ($user->status === 'suspended')
                                            <span class="user-status"
                                                style="background: #fee2e2; color: #b91c1c;">Suspended</span>
                                        @else
                                            <span class="user-status status-active">Active</span>
                                        @endif
                                    </td>
                                    <td>
                                        <button type="button" class="contact-link"
                                            style="border:none; background:none; cursor:pointer;"
                                            data-id="{{ $user->id }}" data-name="{{ $user->name }}"
                                            data-email="{{ $user->email }}" data-role="{{ $user->role }}"
                                            data-phone="{{ $user->phone }}" data-speciality="{{ $user->speciality }}"
                                            data-supname="{{ $user->supervisor_name }}" data-supemail="{{ $user->supervisor_email }}"
                                            onclick="openEditModal(this)">Edit</button>
                                    </td>
                                    <td>
                                        <form method="post" action="{{ route('user.suspend', ['user' => $user]) }}">
                                            @csrf
                                            @if ($user->status === 'active')
                                                <button type="button"
                                                    style="background-color: #ef4444; color: white; padding: 4px 8px; border-radius: 4px; border: none; cursor: pointer;"
                                                    onclick="confirmSuspend(this)">Suspend</button>
                                            @else
                                                <button type="button"
                                                    style="background-color: #10b981; color: white; padding: 4px 8px; border-radius: 4px; border: none; cursor: pointer;"
                                                    onclick="confirmUnsuspend(this)">Unsuspend</button>
                                            @endif
                                        </form>
                                    </td>

                                    <!-- <td>
    <i class="fa-solid fa-pen"></i>
    <i class="fa-solid fa-ban"></i>
  </td> -->
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <div class="pagination-container">
                        <div>
                            Showing {{ $users->firstItem() ?? 0 }} to {{ $users->lastItem() ?? 0 }} of
                            <strong>{{ $users->total() }}</strong> users
                        </div>
                        <div class="pagination-links">
                            {{ $users->links('pagination::bootstrap-4') }}
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- MODAL -->

    <!-- MODAL -->

    <!-- ADD USER BUTTON -->

    <!-- DARK BACKGROUND -->
    <div style="" id="modalOverlay" onclick="closeModal()"></div>

    <!-- MODAL BOX -->
    <div id="sampleModal">
        <h3>Add New User</h3>

        @if ($errors->any())
            <div id="validationErrors"
                style="background: #fee2e2; color: #b91c1c; padding: 10px; border-radius: 4px; margin-bottom: 15px; font-size: 0.85rem;">
                <ul style="margin: 0; padding-left: 15px;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- add user modal -->

        <form action="{{ url('/users/store') }}" method="POST">
            @csrf

            <!-- ROLE SELECT -->
            <select id="roleSelect" name="role" onchange="toggleSupervisorFields()">
                <option value="" selected disabled>-- Select Role --</option>
                <option value="admin">Admin</option>
                <option value="technician">Technician</option>
                <option value="employee">Employee</option>
            </select>

            <!-- TECHNICIAN ONLY FIELDS -->
            <div id="supervisorFields" style="display: none; margin-top: 10px;">

                <!-- Supervisor Name -->
                <input type="text" name="supervisor_name" placeholder="Supervisor Name" />

                <!-- Supervisor Email -->
                <input type="email" name="supervisor_email" placeholder="Supervisor Email" />

            </div>

            <div id="commonFields" style="margin-top: 10px;">

                <!-- FULL NAME -->
                <input type="text" name="name" placeholder="e.g. Marcus" />

                <!-- EMAIL -->
                <input type="email" name="email" placeholder="juli@.com" />

                <!-- PHONE NUMBER -->
                <input type="tel" name="phone" placeholder="+237 66789890" />

                <!-- SPECIALTY -->
                <div id="specialityField" style="display: none;">
                    <select name="speciality">
                        <option value="" selected disabled>Select a primary discipline</option>
                        <option value="networking">Networking</option>
                        <option value="software">Software</option>
                        <option value="hardware">Hardware</option>
                    </select>
                </div>

                <!-- BUTTONS -->
                <button type="submit" id="submitBtn">Create User</button>
                <button type="button" id="closeBtn" onclick="closeModal()">Cancel</button>

            </div>

        </form>


    </div>

    <!-- EDIT USER MODAL BOX -->
    <div id="editModal" class="custom-modal-box">
        <h3>Edit User</h3>
        <form id="editUserForm" action="" method="POST">
            @csrf
            <!-- <input type="hidden" name="_token" value="{{ csrf_token() }}"> -->
            @method('PUT')
            <!-- <input type="hidden" name="_method" value="PUT"> -->
            <!-- ROLE SELECT -->
            <select id="editRole" name="role" onchange="toggleEditSupervisorFields()"
                style="width: 100%; margin-bottom: 1rem; padding: 0.5rem; border: 1px solid #ccc; border-radius: 4px;">
                <option value="admin">Admin</option>
                <option value="technician">Technician</option>
                <option value="employee">Employee</option>
            </select>

            <!-- TECHNICIAN ONLY FIELDS (EDIT) -->
            <div id="editSupervisorFields" style="display: none; margin-bottom: 1rem;">
                <!-- Supervisor Name -->
                <input type="text" id="editSupervisorName" name="supervisor_name" placeholder="Supervisor Name"
                    style="width: 100%; margin-bottom: 1rem; padding: 0.5rem; border: 1px solid #ccc; border-radius: 4px;" />
                
                <!-- Supervisor Email -->
                <input type="email" id="editSupervisorEmail" name="supervisor_email" placeholder="Supervisor Email"
                    style="width: 100%; padding: 0.5rem; border: 1px solid #ccc; border-radius: 4px;" />
            </div>

            <!-- FULL NAME -->
            <input type="text" id="editName" name="name"
                style="width: 100%; margin-bottom: 1rem; padding: 0.5rem; border: 1px solid #ccc; border-radius: 4px;"
                required />

            <!-- EMAIL -->
            <input type="email" id="editEmail" name="email"
                style="width: 100%; margin-bottom: 1rem; padding: 0.5rem; border: 1px solid #ccc; border-radius: 4px;"
                required />

            <!-- PHONE NUMBER -->
            <input type="tel" id="editPhone" name="phone"
                style="width: 100%; margin-bottom: 1rem; padding: 0.5rem; border: 1px solid #ccc; border-radius: 4px;" />

            <!-- SPECIALTY -->
            <div id="editSpecialityField" style="display: none;">
                <select id="editSpeciality" name="speciality"
                    style="width: 100%; margin-bottom: 1rem; padding: 0.5rem; border: 1px solid #ccc; border-radius: 4px;">
                    <option value="" selected disabled>Select a primary discipline</option>
                    <option value="networking">Networking</option>
                    <option value="software">Software</option>
                    <option value="hardware">Hardware</option>
                </select>
            </div>

            <!-- BUTTONS -->
            <button type="submit"
                style="background: #2563eb; color: white; padding: 0.5rem 1rem; border: none; border-radius: 4px; cursor: pointer;">Save
                Changes</button>
            <button type="button" onclick="closeEditModal()"
                style="background: #6c757d; color: white; padding: 0.5rem 1rem; border: none; border-radius: 4px; cursor: pointer; margin-left: 0.5rem;">Cancel</button>
        </form>
    </div>



    <!-- <script src="{{ asset('dist/js/modals.js') }}"></script> -->

    <script>
        function toggleSupervisorFields() {

            const role = document.getElementById('roleSelect').value;
            const supervisorFields = document.getElementById('supervisorFields');
            const specialityField = document.getElementById('specialityField');

            if (role === 'technician') {
                supervisorFields.style.display = 'block';
                if (specialityField) specialityField.style.display = 'block';
            } else {
                supervisorFields.style.display = 'none';
                if (specialityField) specialityField.style.display = 'none';
                // hide supervisorfields
            }
        }

        function toggleEditSupervisorFields() {
            const role = document.getElementById('editRole').value;
            const supervisorFields = document.getElementById('editSupervisorFields');
            const specialityField = document.getElementById('editSpecialityField');

            if (role === 'technician') {
                supervisorFields.style.display = 'block';
                if (specialityField) specialityField.style.display = 'block';
            } else {
                supervisorFields.style.display = 'none';
                if (specialityField) specialityField.style.display = 'none';
            }
        }
        const modal = document.getElementById("sampleModal");
        const modalOverlay = document.getElementById("modalOverlay");
        const closeBtn = document.getElementById("closeBtn");

        const openModal = () => {
            modalOverlay.style.display = "block";
            modal.style.display = "block";
        };

        const closeModal = () => {
            modalOverlay.style.display = "none";
            modal.style.display = "none";
            if (typeof editModal !== 'undefined' && editModal) {
                editModal.style.display = "none";
            }
        };
        // get edit popup
        const editModal = document.getElementById("editModal");
        const editUserForm = document.getElementById("editUserForm");

        const openEditModal = (btn) => {
            try {
                document.getElementById("editName").value = btn.dataset.name;
                document.getElementById("editEmail").value = btn.dataset.email;
                document.getElementById("editRole").value = btn.dataset.role;
                document.getElementById("editPhone").value = btn.dataset.phone || '';
                document.getElementById("editSpeciality").value = btn.dataset.speciality || '';
                document.getElementById("editSupervisorName").value = btn.dataset.supname || '';
                document.getElementById("editSupervisorEmail").value = btn.dataset.supemail || '';
                
                toggleEditSupervisorFields(); // Show or hide supervisor fields appropriately

                editUserForm.action = `/users/${btn.dataset.id}`;
                modalOverlay.style.display = "block";
                editModal.style.display = "block";
            } catch (error) {
                console.error("Error opening edit modal:", error);
                alert("An error occurred while opening the edit modal.");
            }
        };
        const closeEditModal = () => {
            modalOverlay.style.display = "none";
            editModal.style.display = "none";
        }; // Keep modal open if there are validation errors @if ($errors->any()) openModal(); @endif
    </script>


</body>

</html>
