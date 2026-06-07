<aside class="sidebar-nav" id="sidebar">
    <div class="logo-container">
        <div class="logo">Resolve</div>
    </div>
    <nav class="nav-menu">
        <div class="nav-group-title">Main Menu</div>
        <a href="{{ route('employee.dashboard') }}" class="nav-item {{ request()->routeIs('employee.dashboard') ? 'active' : '' }}">
            <i class="fa-solid fa-gauge-high"></i>
            <span>Dashboard</span>
        </a>

        <div class="nav-group-title">My Tickets</div>
        <div class="nav-item-dropdown">
            <a href="#" class="nav-item {{ request()->routeIs('employee.tickets*') ? 'active' : '' }}" onclick="
                const submenu = this.nextElementSibling;
                submenu.style.display = submenu.style.display === 'none' ? 'block' : 'none';
                return false;
            ">
                <i class="fa-solid fa-ticket-simple"></i>
                <span>Support Tickets</span>
                <i class="fa-solid fa-chevron-down" style="margin-left: auto; font-size: 0.8rem"></i>
            </a>
            <div class="submenu" style="display: {{ request()->routeIs('employee.tickets*') ? 'block' : 'none' }}; padding-left: 1.5rem">
                <a href="{{ route('employee.tickets') }}" class="nav-item {{ request()->routeIs('employee.tickets') ? 'active' : '' }}" style="padding: 0.5rem 1rem; margin-top: 0.2rem; font-size: 0.9rem">
                    <i class="fa-solid fa-list-check"></i>
                    <span>View My Tickets</span>
                </a>
                <a href="{{ route('employee.tickets.create') }}" class="nav-item {{ request()->routeIs('employee.tickets.create') ? 'active' : '' }}" style="padding: 0.5rem 1rem; font-size: 0.9rem">
                    <i class="fa-solid fa-circle-plus"></i>
                    <span>Submit New Ticket</span>
                </a>
            </div>
        </div>

        <div class="nav-group-title">Reports</div>
        <a href="{{ route('employee.ticketreport') }}" class="nav-item {{ request()->routeIs('employee.ticketreport') ? 'active' : '' }}">
            <i class="fa-solid fa-chart-line"></i>
            <span>My Ticketing Activity</span>
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
