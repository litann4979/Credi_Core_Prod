<aside id="sidebar" class="sidebar">
    <div class="sidebar-content">
        <div class="sidebar-header">
            <div class="logo-container">
                <div class="logo-icon">
                    <i class="fas fa-chart-pie"></i>
                </div>
                <div class="logo-text">
                    <h3>HR Portal</h3>
                    <span>Management System</span>
                </div>
            </div>
        </div>

        <nav class="sidebar-nav">
            <ul class="nav-list">
                
                <li class="nav-header">Overview</li>

              

                <li class="nav-header">Employee Management</li>

                <li class="nav-item">
                    <a href="#" class="nav-link has-submenu {{ request()->routeIs('hr.leave.*') || request()->routeIs('hr.compoff.*') ? 'active' : '' }}" data-submenu="leave">
                        <i class="fas fa-calendar-check"></i>
                        <span>Leave Mgmt</span>
                        <i class="fas fa-chevron-right submenu-arrow"></i>
                    </a>
                    <ul class="submenu {{ request()->routeIs('hr.leave.*') || request()->routeIs('hr.compoff.*') ? 'open' : '' }}" id="leave-submenu">
                        <li>
                            <a href="{{ route('hr.leave.index') }}" class="{{ request()->routeIs('hr.leave.index') ? 'active' : '' }}">
                                <span class="bullet"></span>Apply for Leave
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('hr.leave.approvals') }}" class="{{ request()->routeIs('hr.leave.approvals') ? 'active' : '' }}">
                                <span class="bullet"></span>Leave Approvals
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('hr.compoff.approvals') }}" class="{{ request()->routeIs('hr.compoff.approvals') ? 'active' : '' }}">
                                <span class="bullet"></span>Comp-Off Approvals
                            </a>
                        </li>
                    </ul>
                </li>

                <li class="nav-item">
                    <a href="#" class="nav-link has-submenu {{ request()->routeIs('hr.operations.*') || request()->routeIs('hr.teamlead.*') || request()->routeIs('hr.employees.*') ? 'active' : '' }}" data-submenu="hr">
                        <i class="fas fa-user-plus"></i>
                        <span>Onboarding</span>
                        <i class="fas fa-chevron-right submenu-arrow"></i>
                    </a>
                    <ul class="submenu {{ request()->routeIs('hr.operations.*') || request()->routeIs('hr.teamlead.*') || request()->routeIs('hr.employees.*') ? 'open' : '' }}" id="hr-submenu">
                        <li>
                            <a href="{{ route('hr.operations.index') }}" class="{{ request()->routeIs('hr.operations.*') ? 'active' : '' }}">
                                <span class="bullet"></span>Operations Team
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('hr.teamlead.index') }}" class="{{ request()->routeIs('hr.teamlead.*') ? 'active' : '' }}">
                                <span class="bullet"></span>Team Leads
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('hr.employees.index') }}" class="{{ request()->routeIs('hr.employees.*') ? 'active' : '' }}">
                                <span class="bullet"></span>Employees
                            </a>
                        </li>
                    </ul>
                </li>

                <li class="nav-item">
                    <a href="#" class="nav-link has-submenu {{ request()->routeIs('hr.attendance.*') ? 'active' : '' }}" data-submenu="attendance">
                        <i class="fas fa-clock"></i>
                        <span>Attendance</span>
                        <i class="fas fa-chevron-right submenu-arrow"></i>
                    </a>
                    <ul class="submenu {{ request()->routeIs('hr.attendance.*') ? 'open' : '' }}" id="attendance-submenu">
                        <li>
                            <a href="{{ route('hr.attendance.index') }}" class="{{ request()->routeIs('hr.attendance.index') ? 'active' : '' }}">
                                <span class="bullet"></span>View Records
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('hr.attendance.attendance') }}" class="{{ request()->routeIs('hr.attendance.attendance') ? 'active' : '' }}">
                                <span class="bullet"></span>My Attendance
                            </a>
                        </li>
                    </ul>
                </li>

                <li class="nav-header">Finance & Payroll</li>

                <li class="nav-item">
                    <a href="#" class="nav-link has-submenu {{ request()->routeIs('hr.salary_slips.*') ? 'active' : '' }}" data-submenu="salaryslip">
                        <i class="fas fa-file-invoice-dollar"></i>
                        <span>Payroll</span>
                        <i class="fas fa-chevron-right submenu-arrow"></i>
                    </a>
                    <ul class="submenu {{ request()->routeIs('hr.salary_slips.*') ? 'open' : '' }}" id="salaryslip-submenu">
                        <li>
                            <a href="{{ route('hr.salary_slips.index') }}" class="{{ request()->routeIs('hr.salary_slips.index') ? 'active' : '' }}">
                                <span class="bullet"></span>Generate Payslips
                            </a>
                        </li>
                    </ul>
                </li>

                <li class="nav-header">Communication & Settings</li>

                <li class="nav-item">
                    <a href="#" class="nav-link has-submenu {{ request()->routeIs('hr.holidays.*') ? 'active' : '' }}" data-submenu="holiday">
                        <i class="fas fa-umbrella-beach"></i>
                        <span>Holidays</span>
                        <i class="fas fa-chevron-right submenu-arrow"></i>
                    </a>
                    <ul class="submenu {{ request()->routeIs('hr.holidays.*') ? 'open' : '' }}" id="holiday-submenu">
                        <li>
                            <a href="{{ route('hr.holidays.index') }}">
                                <span class="bullet"></span>Manage Calendar
                            </a>
                        </li>
                    </ul>
                </li>

                <li class="nav-item">
                    <a href="#" class="nav-link has-submenu {{ request()->routeIs('hr.holiday.notification.*') ? 'active' : '' }}" data-submenu="holiday-notification">
                        <i class="fas fa-bell"></i>
                        <span>Notifications</span>
                        <i class="fas fa-chevron-right submenu-arrow"></i>
                    </a>
                    <ul class="submenu {{ request()->routeIs('hr.holiday.notification.*') ? 'open' : '' }}" id="holiday-notification-submenu">
                        <li>
                            <a href="{{ route('hr.holiday.notification.index') }}">
                                <span class="bullet"></span>Send Alerts
                            </a>
                        </li>
                    </ul>
                </li>

                <li class="nav-item">
                    <a href="{{ route('hr.password.edit') }}" class="nav-link {{ request()->routeIs('hr.password.*') ? 'active' : '' }}">
                        <i class="fas fa-lock"></i>
                        <span>Security</span>
                    </a>
                </li>

            </ul>
        </nav>
    </div>
</aside>

<style>
    /* --- CSS Variables for Easy Theming --- */
    :root {
        --sidebar-bg: #ffffff;
        --sidebar-width: 280px;
        --primary-color: #f97316; /* Orange Theme */
        --primary-light: #fff7ed; /* Very light orange for background */
        --text-main: #334155;
        --text-light: #94a3b8;
        --border-color: #f1f5f9;
        --active-gradient: linear-gradient(135deg, #f97316, #ea580c);
        --hover-bg: #fff7ed;
    }

    /* --- Layout & Container --- */
    .sidebar {
        position: fixed;
        left: 0;
        top: 0;
        height: 100vh;
        width: var(--sidebar-width);
        background: var(--sidebar-bg);
        border-right: 1px solid var(--border-color);
        box-shadow: 4px 0 24px rgba(0, 0, 0, 0.02);
        z-index: 1000;
        transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .sidebar-content {
        height: 100%;
        display: flex;
        flex-direction: column;
        overflow: hidden;
    }

    /* --- Header / Logo --- */
    .sidebar-header {
        padding: 24px;
        border-bottom: 1px solid var(--border-color);
        background: var(--sidebar-bg);
    }

    .logo-container {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .logo-icon {
        width: 40px;
        height: 40px;
        background: var(--active-gradient);
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 18px;
        box-shadow: 0 4px 10px rgba(249, 115, 22, 0.2);
    }

    .logo-text h3 {
        font-size: 18px;
        font-weight: 800;
        color: var(--text-main);
        margin: 0;
        line-height: 1.1;
        letter-spacing: -0.5px;
    }

    .logo-text span {
        font-size: 11px;
        font-weight: 500;
        color: var(--text-light);
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    /* --- Navigation List --- */
    .sidebar-nav {
        flex: 1;
        overflow-y: auto;
        padding: 20px 16px;
    }

    /* Custom Scrollbar */
    .sidebar-nav::-webkit-scrollbar { width: 4px; }
    .sidebar-nav::-webkit-scrollbar-track { background: transparent; }
    .sidebar-nav::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 4px; }
    .sidebar-nav::-webkit-scrollbar-thumb:hover { background: #cbd5e1; }

    .nav-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .nav-header {
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 1px;
        color: #cbd5e1;
        margin: 20px 0 8px 12px;
    }

    .nav-item {
        margin-bottom: 4px;
    }

    /* --- Nav Links --- */
    .nav-link {
        display: flex;
        align-items: center;
        padding: 11px 16px;
        color: var(--text-main);
        text-decoration: none;
        border-radius: 12px;
        font-size: 14px;
        font-weight: 500;
        transition: all 0.25s ease;
        position: relative;
    }

    .nav-link i:first-child {
        width: 20px;
        font-size: 16px;
        margin-right: 12px;
        color: var(--text-light);
        transition: color 0.2s;
        text-align: center;
    }

    .nav-link span {
        flex: 1;
    }

    /* Hover State */
    .nav-link:hover {
        background: var(--hover-bg);
        color: var(--primary-color);
    }
    .nav-link:hover i:first-child {
        color: var(--primary-color);
    }

    /* Active State */
    .nav-link.active {
        background: var(--active-gradient);
        color: white;
        box-shadow: 0 4px 12px rgba(249, 115, 22, 0.25);
    }

    .nav-link.active i:first-child,
    .nav-link.active .submenu-arrow {
        color: white;
    }

    /* --- Submenu --- */
    .submenu-arrow {
        font-size: 10px;
        color: var(--text-light);
        transition: transform 0.3s ease;
    }

    .nav-link.active .submenu-arrow {
        transform: rotate(90deg);
    }

    .submenu {
        list-style: none;
        padding: 0;
        margin: 0;
        max-height: 0;
        overflow: hidden;
        transition: max-height 0.3s ease-out;
        padding-left: 12px; /* Indent submenus */
    }

    .submenu.open {
        max-height: 500px; /* Arbitrary large number for slide effect */
    }

    .submenu li {
        margin-top: 4px;
        position: relative;
    }

    /* Submenu Line Guide (Optional Polish) */
    .submenu li::before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        bottom: 0;
        width: 1px;
        background: #f1f5f9;
        display: none; /* Can enable for tree view style */
    }

    .submenu a {
        display: flex;
        align-items: center;
        padding: 9px 12px 9px 16px;
        color: var(--text-main);
        text-decoration: none;
        font-size: 13px;
        border-radius: 8px;
        transition: all 0.2s;
    }

    .submenu a .bullet {
        width: 5px;
        height: 5px;
        background: #cbd5e1;
        border-radius: 50%;
        margin-right: 12px;
        transition: all 0.2s;
    }

    .submenu a:hover {
        background: var(--hover-bg);
        color: var(--primary-color);
    }

    .submenu a:hover .bullet {
        background: var(--primary-color);
        transform: scale(1.2);
    }

    .submenu a.active {
        color: var(--primary-color);
        font-weight: 600;
        background: var(--hover-bg);
    }
    
    .submenu a.active .bullet {
        background: var(--primary-color);
    }

    /* --- Responsive --- */
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

    // Handle submenu toggles
    submenuLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Get ID
            const submenuId = this.dataset.submenu + '-submenu';
            const submenu = document.getElementById(submenuId);
            const arrow = this.querySelector('.submenu-arrow');

            // Check current state
            const isOpen = submenu.classList.contains('open');

            // Close all other submenus (Optional - Remove if you want multiple open)
            // document.querySelectorAll('.submenu').forEach(m => m.classList.remove('open'));
            // document.querySelectorAll('.submenu-arrow').forEach(a => a.style.transform = 'rotate(0deg)');
            // document.querySelectorAll('.nav-link.has-submenu').forEach(l => {
            //     if(!l.classList.contains('active-route')) l.classList.remove('active'); 
            // });

            // Toggle current
            if (isOpen) {
                submenu.classList.remove('open');
                arrow.style.transform = 'rotate(0deg)';
                // Only remove active class if it's not the current route's parent
                // Note: The logic in blade handles 'active' class based on route, JS handles toggle visual
            } else {
                submenu.classList.add('open');
                arrow.style.transform = 'rotate(90deg)';
            }
        });
    });

    // Ensure arrow rotation on page load if submenu is active
    document.querySelectorAll('.submenu.open').forEach(menu => {
        const parentLink = document.querySelector(`[data-submenu="${menu.id.replace('-submenu','')}"]`);
        if(parentLink) {
            const arrow = parentLink.querySelector('.submenu-arrow');
            if(arrow) arrow.style.transform = 'rotate(90deg)';
        }
    });
});
</script>