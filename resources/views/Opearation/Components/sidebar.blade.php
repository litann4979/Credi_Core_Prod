<aside id="sidebar" class="sidebar">
    <div class="sidebar-content">
        <div class="sidebar-header">
            <div class="logo">
                <i class="fas fa-chart-line"></i>
            </div>
            <div class="logo-text">
                <h3>Operation</h3>
                <span>Dashboard</span>
            </div>
        </div>

        <nav class="sidebar-nav">
            <ul class="nav-list">
                <li class="nav-item">
                    <a href="{{ route('operations.dashboard') }}" class="nav-link {{ request()->routeIs('operations.dashboard') ? 'active' : '' }}">
                        <i class="fas fa-home"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                
                {{-- Team Leads Section (Commented out in original) --}}
                {{-- <li class="nav-item">
                    <a href="#" class="nav-link has-submenu {{ request()->routeIs('operations.teamlead.*') ? 'active' : '' }}" data-submenu="teamleads">
                        <i class="fas fa-bullhorn"></i>
                        <span>Manage Team Leads</span>
                        <i class="fas fa-chevron-down submenu-arrow"></i>
                    </a>
                    <ul class="submenu" id="teamleads-submenu">
                        <li><a href="{{ route('operations.teamlead.index') }}">All Team Leads</a></li>
                    </ul>
                </li> --}}

                <li class="nav-item">
                    <a href="#" class="nav-link has-submenu {{ request()->routeIs('operations.leads.*') ? 'active' : '' }}" data-submenu="all-leads">
                        <i class="fas fa-users"></i>
                        <span>Manage Leads</span>
                        <i class="fas fa-chevron-down submenu-arrow"></i>
                    </a>
                    <ul class="submenu {{ request()->routeIs('operations.leads.*') ? 'active' : '' }}" id="all-leads-submenu">
                        <li><a href="{{ route('operations.leads.index') }}" class="{{ request()->routeIs('operations.leads.index') ? 'active' : '' }}">Assigned Leads</a></li>
                    </ul>
                </li>

                {{-- Employees Section (Commented out in original) --}}
                {{-- <li class="nav-item">
                    <a href="#" class="nav-link has-submenu {{ request()->routeIs('operations.employees.*') ? 'active' : '' }}" data-submenu="employees">
                        <i class="fas fa-user-friends"></i>
                        <span>Manage All Employees</span>
                        <i class="fas fa-chevron-down submenu-arrow"></i>
                    </a>
                    <ul class="submenu" id="employees-submenu">
                        <li><a href="{{ route('operations.employees.index') }}">All Employees</a></li>
                    </ul>
                </li> --}}

                <li class="nav-item">
                    <a href="{{ route('operations.tasks.index') }}" class="nav-link {{ request()->routeIs('operations.tasks.index') ? 'active' : '' }}">
                        <i class="fas fa-tasks"></i>
                        <span>Tasks</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('operations.offers.create') }}" class="nav-link {{ request()->routeIs('operations.offers.create') ? 'active' : '' }}">
                        <i class="fas fa-gift"></i>
                        <span>Send Offer Notification</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="#" class="nav-link has-submenu {{ request()->routeIs('operations.attendance.*') ? 'active' : '' }}" data-submenu="attendance">
                        <i class="fas fa-clock"></i>
                        <span>Attendance</span>
                        <i class="fas fa-chevron-down submenu-arrow"></i>
                    </a>
                    <ul class="submenu {{ request()->routeIs('operations.attendance.*') ? 'active' : '' }}" id="attendance-submenu">
                        <li><a href="{{ route('operations.attendance.index') }}" class="{{ request()->routeIs('operations.attendance.index') ? 'active' : '' }}">Give Your Attendance</a></li>
                    </ul>
                </li>

                <li class="nav-item">
                    <a href="#" class="nav-link has-submenu {{ request()->routeIs('operations.leave.*') ? 'active' : '' }}" data-submenu="leave">
                        <i class="fas fa-calendar-minus"></i>
                        <span>Leave</span>
                        <i class="fas fa-chevron-down submenu-arrow"></i>
                    </a>
                    <ul class="submenu {{ request()->routeIs('operations.leave.*') || request()->routeIs('operations.comp-off.*') ? 'active' : '' }}" id="leave-submenu">
                        <li><a href="{{ route('operations.leave.index') }}" class="{{ request()->routeIs('operations.leave.index') ? 'active' : '' }}">Apply For Leave</a></li>
                        <li><a href="{{ route('operations.comp-off.index') }}" class="{{ request()->routeIs('operations.comp-off.index') ? 'active' : '' }}">Apply For Comp-off</a></li>
                    </ul>
                </li>

                <li class="nav-item">
                    <a href="{{ route('operations.password.edit') }}" class="nav-link {{ request()->routeIs('operations.password.*') ? 'active' : '' }}">
                        <i class="fas fa-key"></i>
                        <span>Change Password</span>
                    </a>
                </li>
            </ul>
        </nav>
    </div>
</aside>

<style>
    /* Modern Sidebar Styles */
    .sidebar {
        position: fixed;
        left: 0;
        top: 0;
        height: 100vh;
        width: 280px;
        background: #ffffff;
        border-right: 1px solid #e5e7eb;
        box-shadow: 4px 0 24px rgba(0, 0, 0, 0.02);
        z-index: 1000;
        font-family: 'Inter', sans-serif;
        transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .sidebar-content {
        height: 100%;
        display: flex;
        flex-direction: column;
        overflow: hidden;
    }

    .sidebar-header {
        padding: 28px 24px;
        display: flex;
        align-items: center;
        gap: 16px;
        background: #ffffff;
        border-bottom: 1px solid #f8fafc;
    }

    .logo {
        width: 42px;
        height: 42px;
        background: linear-gradient(135deg, #f97316, #ea580c);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 4px 12px rgba(249, 115, 22, 0.2);
    }

    .logo i {
        font-size: 18px;
        color: white;
    }

    .logo-text h3 {
        font-size: 18px;
        font-weight: 700;
        color: #1e293b;
        margin: 0;
        letter-spacing: -0.02em;
    }

    .logo-text span {
        font-size: 12px;
        color: #64748b;
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .sidebar-nav {
        flex: 1;
        padding: 24px 16px;
        overflow-y: auto;
        overflow-x: hidden;
    }

    .nav-list {
        list-style: none;
        padding: 0;
        margin: 0;
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .nav-link {
        display: flex;
        align-items: center;
        padding: 12px 16px;
        color: #64748b;
        text-decoration: none;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        border-radius: 12px;
        font-size: 14px;
        font-weight: 500;
    }

    .nav-link:hover {
        background: #fff7ed;
        color: #ea580c;
        transform: translateX(2px);
    }

    .nav-link.active {
        background: linear-gradient(135deg, #f97316, #ea580c);
        color: white;
        box-shadow: 0 4px 12px rgba(249, 115, 22, 0.25);
    }

    .nav-link i:not(.submenu-arrow) {
        width: 20px;
        font-size: 18px;
        margin-right: 14px;
        display: flex;
        justify-content: center;
    }

    .submenu-arrow {
        margin-left: auto;
        font-size: 10px;
        transition: transform 0.3s ease;
    }

    .nav-link.has-submenu.active .submenu-arrow {
        transform: rotate(180deg);
    }

    .submenu {
        list-style: none;
        padding: 0;
        margin: 0;
        max-height: 0;
        overflow: hidden;
        transition: max-height 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        border-radius: 0 0 12px 12px;
    }

    .submenu.active {
        max-height: 500px;
        padding-top: 4px;
        padding-bottom: 8px;
    }

    .submenu a {
        display: flex;
        align-items: center;
        padding: 10px 16px 10px 52px;
        color: #64748b;
        text-decoration: none;
        font-size: 13px;
        font-weight: 500;
        transition: all 0.2s ease;
        border-radius: 8px;
        margin-top: 2px;
    }

    .submenu a:hover {
        color: #ea580c;
        background: rgba(255, 247, 237, 0.5);
    }

    .submenu a.active {
        color: #ea580c;
        background: #fff7ed;
        font-weight: 600;
    }

    /* Scrollbar Styling */
    .sidebar-nav::-webkit-scrollbar {
        width: 4px;
    }
    .sidebar-nav::-webkit-scrollbar-track {
        background: transparent;
    }
    .sidebar-nav::-webkit-scrollbar-thumb {
        background: #e2e8f0;
        border-radius: 4px;
    }

    @media (max-width: 768px) {
        .sidebar {
            transform: translateX(-100%);
        }
        .sidebar.mobile-open {
            transform: translateX(0);
        }
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const submenuLinks = document.querySelectorAll('.nav-link.has-submenu');

    submenuLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();

            const submenuId = this.dataset.submenu + '-submenu';
            const submenu = document.getElementById(submenuId);
            const isActive = this.classList.contains('active');

            // Close other menus
            document.querySelectorAll('.submenu').forEach(menu => {
                if (menu.id !== submenuId) menu.classList.remove('active');
            });
            document.querySelectorAll('.nav-link.has-submenu').forEach(nav => {
                if (nav !== this) nav.classList.remove('active');
            });

            // Toggle current
            this.classList.toggle('active');
            submenu.classList.toggle('active');
        });
    });

    // Keep submenu open if child is active
    document.querySelectorAll('.submenu a.active').forEach(activeLink => {
        const parentSubmenu = activeLink.closest('.submenu');
        const parentToggle = document.querySelector(`[data-submenu="${parentSubmenu.id.replace('-submenu','')}"]`);
        if (parentSubmenu && parentToggle) {
            parentSubmenu.classList.add('active');
            parentToggle.classList.add('active');
        }
    });
});
</script>