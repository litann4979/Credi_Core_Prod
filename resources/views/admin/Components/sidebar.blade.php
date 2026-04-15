<aside id="sidebar" class="sidebar">
    <div class="sidebar-content">
        <div class="sidebar-header">
            <div class="logo">
                <img id="sidebarKredipalLogo" src="{{ asset('storage/kredipalfinallogo.png') }}" alt="Kredipal Logo" class="logo-image">
            </div>
            <div class="logo-text">
                <h3>Admin</h3>
                <span>Dashboard</span>
            </div>
        </div>

        <nav class="sidebar-nav">
            <ul class="nav-list">

                <li class="nav-item">
                    <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                        <i class="fas fa-home"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.live-dashboard') }}" class="nav-link {{ request()->routeIs('admin.live-dashboard') ? 'active' : '' }}">
                        <i class="fas fa-broadcast-tower"></i>
                        <span>Live Dashboard</span>
                    </a>
                </li>
                {{-- <li class="nav-item">
                    <a href="{{ route('admin.live-dashboard.teamlead-index') }}" class="nav-link {{ request()->routeIs('admin.live-dashboard.teamlead-index') ? 'active' : '' }}">
                        <i class="fas fa-broadcast-tower"></i>
                        <span>Teamlead Live Dashboard</span>
                    </a>
                </li> --}}

                <li class="nav-item">
                    <a href="#" class="nav-link has-submenu {{ request()->routeIs('admin.operations.*') || request()->routeIs('admin.teamlead.*') || request()->routeIs('admin.employees.*') || request()->routeIs('admin.hr.*') ? 'active' : '' }}" data-submenu="operations">
                        <i class="fas fa-user-plus"></i>
                        <span>User Management</span>
                        <i class="fas fa-chevron-right submenu-arrow"></i>
                    </a>
                    <ul class="submenu {{ request()->routeIs('admin.operations.*') || request()->routeIs('admin.teamlead.*') || request()->routeIs('admin.employees.*') || request()->routeIs('admin.hr.*') ? 'open' : '' }}" id="operations-submenu">
                        <li><a href="{{ route('admin.hr.index') }}" class="{{ request()->routeIs('admin.hr.*') ? 'active' : '' }}">HR Managers</a></li>
                        <li><a href="{{ route('admin.operations.index') }}" class="{{ request()->routeIs('admin.operations.*') ? 'active' : '' }}">Operations</a></li>
                        <li><a href="{{ route('admin.teamlead.index') }}" class="{{ request()->routeIs('admin.teamlead.*') ? 'active' : '' }}">Team Leads</a></li>
                        <li><a href="{{ route('admin.employees.index') }}" class="{{ request()->routeIs('admin.employees.*') ? 'active' : '' }}">Employees</a></li>
                    </ul>
                </li>

                <li class="nav-item">
                    <a href="#" class="nav-link has-submenu {{ request()->routeIs('admin.leads.*') ? 'active' : '' }}" data-submenu="leads">
                        <i class="fas fa-funnel-dollar"></i>
                        <span>Leads</span>
                        <i class="fas fa-chevron-right submenu-arrow"></i>
                    </a>
                    <ul class="submenu {{ request()->routeIs('admin.leads.*') ? 'active' : '' }}" id="leads-submenu">
                        <li><a href="{{ route('admin.leads.index') }}" class="{{ request()->routeIs('admin.leads.*') ? 'active' : '' }}">Assigned Leads</a></li>
                        <li><a href="{{ route('admin.leads.deleted') }}" class="{{ request()->routeIs('admin.leads.*') ? 'active' : '' }}">Deleted Leads</a></li>
                    </ul>
                </li>

                <li class="nav-item">
                    <a href="{{ route('admin.tasks.index') }}" class="nav-link {{ request()->routeIs('admin.tasks.*') ? 'active' : '' }}">
                        <i class="fas fa-tasks"></i>
                        <span>Tasks</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('admin.banks.index') }}" class="nav-link {{ request()->routeIs('admin.banks.*') ? 'active' : '' }}">
                        <i class="fas fa-university"></i>
                        <span>Banks</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('admin.targets.index') }}" class="nav-link {{ request()->routeIs('admin.targets.*') ? 'active' : '' }}">
                        <i class="fas fa-bullseye"></i>
                        <span>Targets</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('admin.geofence.index') }}" class="nav-link {{ request()->routeIs('admin.geofence.*') ? 'active' : '' }}">
                        <i class="fas fa-map-marked-alt"></i>
                        <span>Geofence Settings</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('admin.employee-movements.index') }}" class="nav-link {{ request()->routeIs('admin.employee-movements.*') ? 'active' : '' }}">
                        <i class="fas fa-route"></i>
                        <span>Work Outside</span>
                    </a>
                </li>



                <li class="nav-item">
                    <a href="#" class="nav-link has-submenu {{ request()->routeIs('admin.report') ? 'active' : '' }}" data-submenu="reports">
                        <i class="fas fa-chart-pie"></i>
                        <span>Reports</span>
                        <i class="fas fa-chevron-right submenu-arrow"></i>
                    </a>
                    <ul class="submenu {{ request()->routeIs('admin.report') ? 'active' : '' }}" id="reports-submenu">
                        <li><a href="{{ route('admin.report') }}" class="{{ request()->routeIs('admin.report') ? 'active' : '' }}">View Reports</a></li>
                    </ul>
                </li>

                <li class="nav-item">
                    <a href="#" class="nav-link has-submenu {{ request()->routeIs('admin.attendance.*') ? 'active' : '' }}" data-submenu="attendance">
                        <i class="fas fa-calendar-check"></i>
                        <span>Attendance</span>
                        <i class="fas fa-chevron-right submenu-arrow"></i>
                    </a>
                    <ul class="submenu {{ request()->routeIs('admin.attendance.*') ? 'active' : '' }}" id="attendance-submenu">
                        <li><a href="{{ route('admin.attendance.index') }}" class="{{ request()->routeIs('admin.attendance.*') ? 'active' : '' }}">Daily Logs</a></li>
                    </ul>
                </li>

                <li class="nav-item">
                    <a href="#" class="nav-link has-submenu {{ request()->routeIs('admin.leave.*') ? 'active' : '' }}" data-submenu="leave">
                        <i class="fas fa-envelope-open-text"></i>
                        <span>Leaves</span>
                        <i class="fas fa-chevron-right submenu-arrow"></i>
                    </a>
                    <ul class="submenu {{ request()->routeIs('admin.leave.*') ? 'active' : '' }}" id="leave-submenu">
                        <li><a href="{{ route('admin.leave.approvals') }}" class="{{ request()->routeIs('admin.leave.approvals') ? 'active' : '' }}">Leave Approvals</a></li>
                    </ul>
                </li>

                <li class="nav-item">
                    <a href="{{ route('admin.password.edit') }}" class="nav-link {{ request()->routeIs('admin.password.*') ? 'active' : '' }}">
                        <i class="fas fa-lock"></i>
                        <span>Security</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('admin.office_rules.edit') }}" class="nav-link {{ request()->routeIs('admin.office_rules.*') ? 'active' : '' }}">
                        <i class="fas fa-sliders-h"></i>
                        <span>Office Rules</span>
                    </a>
                </li>

                <li class="nav-spacer"></li>

                <li class="nav-item px-4 mb-4">
                    <div class="export-box">
                        <button id="updateExportCsvBtn" class="btn-export">
                            <div class="icon-box">
                                <i class="fas fa-cloud-download-alt"></i>
                            </div>
                            <div class="text-box">
                                <span class="title">Export Data</span>
                                <span class="subtitle">Update & Download CSV</span>
                            </div>
                        </button>
                        <div id="updateStatus" class="status-text"></div>
                    </div>
                </li>

            </ul>
        </nav>
    </div>
</aside>

<style>
    /* Variables */
    :root {
        --sidebar-width: 280px;
        --primary-color: #f97316;
        --primary-gradient: linear-gradient(135deg, #f97316 0%, #ea580c 100%);
        --text-main: #334155;
        --text-light: #64748b;
        --bg-hover: #fff7ed;
        --border-color: #f1f5f9;
    }

    /* Sidebar Container */
    .sidebar {
        position: fixed;
        left: 0;
        top: 0;
        height: 100vh;
        width: var(--sidebar-width);
        background: #ffffff;
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

    /* Header */
    .sidebar-header {
        margin: 16px 16px 10px;
        padding: 14px;
        display: flex;
        align-items: center;
        gap: 14px;
        border: 1px solid var(--border-color);
        border-radius: 14px;
        background: linear-gradient(180deg, #ffffff 0%, #fff7ed 100%);
    }

    .logo {
        width: auto;
        height: 34px;
        border-radius: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        background: transparent;
        border: none;
        flex-shrink: 0;
    }

    .logo-image {
        width: auto;
        height: 100%;
        max-width: 96px;
        object-fit: contain;
        object-position: center;
        border-radius: 0;
        display: block;
    }

    .logo-text h3 {
        font-size: 20px;
        font-weight: 800;
        color: var(--text-main);
        margin: 0;
        line-height: 1.1;
        letter-spacing: 0.2px;
    }

    .logo-text span {
        font-size: 11px;
        color: var(--text-light);
        font-weight: 600;
        letter-spacing: 0.8px;
        text-transform: uppercase;
    }

    /* Navigation */
    .sidebar-nav {
        flex: 1;
        padding: 24px 16px;
        overflow-y: auto;
        scrollbar-width: none; /* Firefox */
    }

    .sidebar-nav::-webkit-scrollbar {
        display: none; /* Chrome/Safari */
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
        color: var(--text-light);
        text-decoration: none;
        font-size: 14px;
        font-weight: 500;
        border-radius: 12px;
        transition: all 0.2s ease;
        position: relative;
    }

    .nav-link i {
        width: 24px;
        font-size: 18px;
        margin-right: 12px;
        text-align: center;
        transition: color 0.2s;
    }

    .nav-link span {
        flex: 1;
    }

    .submenu-arrow {
        font-size: 10px;
        transition: transform 0.3s ease;
        opacity: 0.7;
    }

    /* Hover State */
    .nav-link:hover {
        background-color: var(--bg-hover);
        color: var(--primary-color);
    }

    .nav-link:hover i {
        color: var(--primary-color);
    }

    /* Active State */
    .nav-link.active {
        background: var(--primary-gradient);
        color: white;
        box-shadow: 0 4px 12px rgba(249, 115, 22, 0.25);
    }

    .nav-link.active i,
    .nav-link.active .submenu-arrow {
        color: white;
        opacity: 1;
    }

    /* Submenu */
    .submenu {
        list-style: none;
        padding: 0;
        margin: 0;
        max-height: 0;
        overflow: hidden;
        transition: max-height 0.3s ease-out;
        padding-left: 12px;
    }

    .submenu.open {
        max-height: 500px; /* Arbitrary large height for animation */
        padding-top: 4px;
        padding-bottom: 4px;
    }

    .submenu li {
        margin-bottom: 2px;
        position: relative;
    }

    /* Tree line for submenu */
    .submenu li::before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        bottom: 0;
        width: 2px;
        background: var(--border-color);
        border-radius: 2px;
    }

    .submenu a {
        display: block;
        padding: 10px 16px 10px 24px;
        color: var(--text-light);
        text-decoration: none;
        font-size: 13px;
        font-weight: 500;
        border-radius: 8px;
        transition: all 0.2s;
        position: relative;
    }

    .submenu a:hover {
        color: var(--primary-color);
        background-color: transparent;
        padding-left: 28px; /* Slide effect */
    }

    .submenu a.active {
        color: var(--primary-color);
        font-weight: 600;
        background-color: var(--bg-hover);
    }

    .submenu a.active::before {
        content: '';
        position: absolute;
        left: -2px;
        top: 50%;
        transform: translateY(-50%);
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background-color: var(--primary-color);
    }

    /* Rotate arrow when open */
    .nav-link.active[aria-expanded="true"] .submenu-arrow,
    .nav-link[aria-expanded="true"] .submenu-arrow {
        transform: rotate(90deg);
    }

    /* Spacer to push export button down if needed */
    .nav-spacer {
        flex-grow: 1;
    }

    /* Export Box */
    .export-box {
        margin-top: auto;
    }

    .btn-export {
        width: 100%;
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px;
        background: #1e293b; /* Dark bg for contrast */
        border: none;
        border-radius: 12px;
        cursor: pointer;
        transition: transform 0.2s, box-shadow 0.2s;
        text-align: left;
        position: relative;
        overflow: hidden;
    }

    .btn-export::after {
        content: '';
        position: absolute;
        top: 0; left: 0; right: 0; bottom: 0;
        background: linear-gradient(135deg, rgba(255,255,255,0.1) 0%, transparent 100%);
    }

    .btn-export:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 16px rgba(30, 41, 59, 0.2);
    }

    .btn-export:disabled {
        opacity: 0.7;
        cursor: not-allowed;
        transform: none;
    }

    .icon-box {
        width: 36px;
        height: 36px;
        background: rgba(255,255,255,0.1);
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        font-size: 16px;
    }

    .text-box {
        display: flex;
        flex-direction: column;
    }

    .text-box .title {
        color: #fff;
        font-size: 14px;
        font-weight: 600;
        line-height: 1.2;
    }

    .text-box .subtitle {
        color: #94a3b8;
        font-size: 11px;
    }

    .status-text {
        font-size: 11px;
        text-align: center;
        margin-top: 8px;
        min-height: 16px;
        color: var(--text-light);
    }

    .status-text .text-info { color: #3b82f6; }
    .status-text .text-success { color: #10b981; }
    .status-text .text-danger { color: #ef4444; }

    /* Mobile */
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
    // Remove checkerboard pixels from sidebar brand logo.
    function cleanCheckerBackground(imgEl) {
        if (!imgEl || imgEl.dataset.cleaned === '1') {
            return;
        }

        const canvas = document.createElement('canvas');
        const ctx = canvas.getContext('2d');
        if (!ctx) return;

        canvas.width = imgEl.naturalWidth || imgEl.width;
        canvas.height = imgEl.naturalHeight || imgEl.height;
        ctx.drawImage(imgEl, 0, 0);

        const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
        const data = imageData.data;

        for (let i = 0; i < data.length; i += 4) {
            const r = data[i];
            const g = data[i + 1];
            const b = data[i + 2];
            const a = data[i + 3];
            const isGray = Math.abs(r - g) < 12 && Math.abs(g - b) < 12;
            const isLightGray = r > 150 && g > 150 && b > 150;

            if (isGray && isLightGray) {
                data[i + 3] = 0;
            } else if (isGray && r > 120 && g > 120 && b > 120) {
                data[i + 3] = Math.max(0, a - 80);
            }
        }

        ctx.putImageData(imageData, 0, 0);
        imgEl.src = canvas.toDataURL('image/png');
        imgEl.dataset.cleaned = '1';
    }

    const sidebarLogo = document.getElementById('sidebarKredipalLogo');
    if (sidebarLogo) {
        if (sidebarLogo.complete) {
            cleanCheckerBackground(sidebarLogo);
        } else {
            sidebarLogo.addEventListener('load', function() {
                cleanCheckerBackground(sidebarLogo);
            }, { once: true });
        }
    }

    // Apply brand favicon globally for all pages using this sidebar.
    const faviconHref = '{{ asset('logo1.png') }}';
    const faviconRels = ['icon', 'shortcut icon', 'apple-touch-icon'];

    faviconRels.forEach((relValue) => {
        let favicon = document.querySelector(`link[rel='${relValue}']`);
        if (!favicon) {
            favicon = document.createElement('link');
            favicon.rel = relValue;
            document.head.appendChild(favicon);
        }
        favicon.type = 'image/png';
        favicon.href = faviconHref;
    });

    // 1. Submenu Toggle Logic
    const submenuToggles = document.querySelectorAll('.nav-link.has-submenu');

    submenuToggles.forEach(toggle => {
        toggle.addEventListener('click', function(e) {
            e.preventDefault();

            const targetId = this.dataset.submenu + '-submenu';
            const targetSubmenu = document.getElementById(targetId);
            const arrow = this.querySelector('.submenu-arrow');

            // Close other open submenus (Accordion style - optional)
            document.querySelectorAll('.submenu.open').forEach(menu => {
                if (menu.id !== targetId) {
                    menu.classList.remove('open');
                    // Reset arrow of other menus
                    const otherToggle = document.querySelector(`[data-submenu="${menu.id.replace('-submenu','')}"]`);
                    if(otherToggle) {
                        otherToggle.classList.remove('active');
                        otherToggle.setAttribute('aria-expanded', 'false');
                    }
                }
            });

            // Toggle current
            const isOpen = targetSubmenu.classList.contains('open');

            if (isOpen) {
                targetSubmenu.classList.remove('open');
                this.setAttribute('aria-expanded', 'false');
                // Only remove active class if not on a child route
                if (!targetSubmenu.querySelector('.active')) {
                    this.classList.remove('active');
                }
            } else {
                targetSubmenu.classList.add('open');
                this.setAttribute('aria-expanded', 'true');
                this.classList.add('active');
            }
        });

        // Initialize state based on active class (for page reload)
        const targetId = toggle.dataset.submenu + '-submenu';
        const targetSubmenu = document.getElementById(targetId);
        if (targetSubmenu && targetSubmenu.classList.contains('open')) {
            toggle.setAttribute('aria-expanded', 'true');
        }
    });

    // 2. Export Button Logic
    const exportBtn = document.getElementById('updateExportCsvBtn');
    if (exportBtn) {
        exportBtn.addEventListener('click', function() {
            const statusDiv = document.getElementById('updateStatus');
            const originalContent = this.innerHTML;

            this.disabled = true;
            this.innerHTML = `
                <div class="icon-box"><i class="fas fa-circle-notch fa-spin"></i></div>
                <div class="text-box"><span class="title">Processing...</span></div>
            `;
            statusDiv.innerHTML = '<span class="text-info">Preparing CSV...</span>';

            fetch('{{ route("admin.leads.update.export.csv") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(response => {
                if (response.ok) return response.blob();
                throw new Error('Export failed');
            })
            .then(blob => {
                const url = window.URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = 'data_export_' + new Date().toISOString().slice(0,10) + '.csv';
                document.body.appendChild(a);
                a.click();
                window.URL.revokeObjectURL(url);
                statusDiv.innerHTML = '<span class="text-success">Download started!</span>';
            })
            .catch(error => {
                console.error(error);
                statusDiv.innerHTML = '<span class="text-danger">Export failed.</span>';
            })
            .finally(() => {
                setTimeout(() => {
                    this.disabled = false;
                    this.innerHTML = originalContent;
                    setTimeout(() => statusDiv.innerHTML = '', 3000);
                }, 1000);
            });
        });
    }
});
</script>
