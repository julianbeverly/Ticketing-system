<aside class="sidebar-nav" id="sidebar">
    <div class="logo-container">
        <div class="logo">Resolve</div>
    </div>
    <nav class="nav-menu">
        <div class="nav-group-title">Main Menu</div>
        <a href="{{ route('admin.dashboard') }}" class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            <i class="fa-solid fa-gauge-high"></i>
            <span>Dashboard</span>
        </a>

        <div class="nav-group-title">Ticket Operations</div>
        <div class="nav-item-dropdown">
            <a href="#" class="nav-item {{ request()->routeIs('admin.tickets*') || request()->routeIs('admin.slaconfig') ? 'active' : '' }}" onclick="
                const submenu = this.nextElementSibling;
                submenu.style.display = submenu.style.display === 'none' ? 'block' : 'none';
                return false;
            ">
                <i class="fa-solid fa-ticket-simple"></i>
                <span>Ticket</span>
                <i class="fa-solid fa-chevron-down" style="margin-left: auto; font-size: 0.8rem"></i>
            </a>
            <div class="submenu" style="display: {{ request()->routeIs('admin.tickets*') || request()->routeIs('admin.slaconfig') ? 'block' : 'none' }}; padding-left: 1.5rem">
                <a href="{{ route('admin.tickets') }}" class="nav-item {{ request()->routeIs('admin.tickets') ? 'active' : '' }}" style="padding: 0.5rem 1rem; margin-top: 0.2rem; font-size: 0.9rem">
                    <i class="fa-solid fa-list-check"></i>
                    <span>Tickets List</span>
                </a>
                <a href="{{ route('admin.tickets.create') }}" class="nav-item {{ request()->routeIs('admin.tickets.create') ? 'active' : '' }}" style="padding: 0.5rem 1rem; font-size: 0.9rem">
                    <i class="fa-solid fa-circle-plus"></i>
                    <span>Create Ticket</span>
                </a>
                <a href="{{ route('admin.slaconfig') }}" class="nav-item {{ request()->routeIs('admin.slaconfig') ? 'active' : '' }}" style="padding: 0.5rem 1rem; font-size: 0.9rem">
                    <i class="fa-solid fa-gears"></i>
                    <span>SLA Configuration</span>
                </a>
            </div>
        </div>

        <div class="nav-group-title">Administration</div>
        <a href="{{ route('user.index') }}" class="nav-item {{ request()->routeIs('user.*') ? 'active' : '' }}">
            <i class="fa-solid fa-users-gear"></i>
            <span>Users</span>
        </a>

        <a href="{{ route('admin.incidents') }}" class="nav-item {{ request()->routeIs('admin.incidents') ? 'active' : '' }}">
            <i class="fa-solid fa-shield-halved"></i>
            <span>Incident</span>
        </a>
        <a href="{{ route('admin.dept') }}" class="nav-item {{ request()->routeIs('admin.dept') ? 'active' : '' }}">
            <i class="fa-solid fa-shield-halved"></i>
            <span>Department</span>
        </a>

        <div class="nav-group-title">Reports & Analytics</div>
        <a href="{{ route('admin.reports') }}" class="nav-item {{ request()->routeIs('admin.reports') ? 'active' : '' }}">
            <i class="fa-solid fa-chart-pie"></i>
            <span>Reports</span>
        </a>
    </nav>

    <div class="logout-container">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button class="nav-item logout-link" type="submit" style="background: transparent; border: none; cursor: pointer; width: 100%; text-align: left; padding: 0.75rem 1.25rem;">
                <i class="fa-solid fa-arrow-right-from-bracket"></i>
                <span>Logout</span>
            </button>
        </form>
    </div>
</aside>
<div id="overlay" class="overlay"></div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const menuToggle = document.getElementById('menuToggle');
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('overlay');

        if(menuToggle && sidebar && overlay) {
            menuToggle.addEventListener('click', () => {
                sidebar.classList.toggle('show');
                overlay.classList.toggle('active');
            });

            overlay.addEventListener('click', () => {
                sidebar.classList.remove('show');
                overlay.classList.remove('active');
            });
        }
    });
</script>
