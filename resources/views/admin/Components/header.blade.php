<header class="header">
    <div class="header-content">
        
        <div class="header-left">
            <button class="mobile-menu-toggle" id="mobileMenuToggle">
                <i class="fas fa-bars"></i>
            </button>
            
            <div class="breadcrumb">
                <span class="breadcrumb-item text-muted">{{ $title ?? 'Dashboard' }}</span>
                @if(isset($subtitle))
                    <i class="fas fa-chevron-right text-xs mx-2 text-gray-400"></i>
                    <span class="breadcrumb-item active">{{ $subtitle }}</span>
                @endif
            </div>
        </div>

        <div class="header-right">
            
            <div class="quick-actions">
                {{-- <button class="action-btn" title="Notifications" data-action="notifications">
                    <i class="fas fa-bell"></i>
                    <span class="notification-badge">0</span>
                </button> --}}

                <form method="POST" action="{{ route('logout') }}" class="d-inline">
                    @csrf
                    <button type="submit" class="action-btn logout-btn" title="Sign Out">
                        <i class="fas fa-power-off"></i>
                    </button>
                </form>
            </div>

            <div class="divider"></div>

            <div class="user-profile-container" id="userProfile">
                <div class="user-info">
                    <span class="user-name">{{ Auth::user()->name ?? 'User' }}</span>
                    <span class="user-role">{{ ucfirst(Auth::user()->designation ?? 'Admin') }}</span>
                </div>
                
                <div class="user-avatar">
                    <img src="https://ui-avatars.com/api/?name={{ Auth::user()->name ?? 'User' }}&background=f97316&color=fff" alt="Avatar">
                    <div class="status-dot"></div>
                </div>

                <div class="user-dropdown" id="userDropdown">
                    <div class="dropdown-header">
                        <div class="dropdown-avatar">
                            <img src="https://ui-avatars.com/api/?name={{ Auth::user()->name ?? 'User' }}&background=f97316&color=fff" alt="Avatar">
                        </div>
                        <div class="dropdown-details">
                            <h4>{{ Auth::user()->name ?? 'User' }}</h4>
                            <p>{{ Auth::user()->email ?? '' }}</p>
                        </div>
                    </div>
                    
                    <ul class="dropdown-list">
                        <li>
                            <a href="{{ route('profile.edit') }}" class="dropdown-item">
                                <i class="fas fa-user-circle"></i> My Profile
                            </a>
                        </li>
                        
                        <li class="dropdown-divider"></li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item text-danger">
                                    <i class="fas fa-sign-out-alt"></i> Sign Out
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>

        </div>
    </div>
</header>

<style>
    /* Variables */
    :root {
        --header-height: 80px;
        --sidebar-width: 280px;
        --primary-color: #f97316;
        --text-main: #1f2937;
        --text-light: #6b7280;
        --border-color: #e5e7eb;
        --bg-hover: #f9fafb;
    }

    /* Header Container */
    .header {
        position: fixed;
        top: 0;
        left: var(--sidebar-width);
        right: 0;
        height: var(--header-height);
        background: #ffffff;
        border-bottom: 1px solid var(--border-color);
        z-index: 900;
        transition: left 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .header-content {
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0 32px;
    }

    /* Left Section */
    .header-left {
        display: flex;
        align-items: center;
        gap: 20px;
    }

    .mobile-menu-toggle {
        display: none;
        background: transparent;
        border: none;
        font-size: 20px;
        color: var(--text-light);
        cursor: pointer;
    }

    .breadcrumb {
        display: flex;
        align-items: center;
        font-size: 14px;
        font-weight: 500;
    }

    .breadcrumb-item {
        color: var(--text-light);
    }

    .breadcrumb-item.active {
        color: var(--primary-color);
        font-weight: 600;
    }

    /* Right Section */
    .header-right {
        display: flex;
        align-items: center;
        gap: 24px;
    }

    .quick-actions {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .action-btn {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        border: 1px solid var(--border-color);
        background: white;
        color: var(--text-light);
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        position: relative;
        transition: all 0.2s;
    }

    .action-btn:hover {
        background: var(--bg-hover);
        color: var(--primary-color);
        border-color: var(--primary-color);
        transform: translateY(-2px);
    }

    .logout-btn:hover {
        color: #ef4444;
        border-color: #ef4444;
        background: #fef2f2;
    }

    .notification-badge {
        position: absolute;
        top: -5px;
        right: -5px;
        background: #ef4444;
        color: white;
        font-size: 10px;
        font-weight: 700;
        padding: 2px 6px;
        border-radius: 10px;
        border: 2px solid white;
        display: none; /* JS toggles this */
    }

    .divider {
        width: 1px;
        height: 32px;
        background: var(--border-color);
    }

    /* User Profile */
    .user-profile-container {
        display: flex;
        align-items: center;
        gap: 12px;
        cursor: pointer;
        position: relative;
        padding: 6px 12px;
        border-radius: 12px;
        transition: background 0.2s;
    }

    .user-profile-container:hover {
        background: var(--bg-hover);
    }

    .user-info {
        text-align: right;
    }

    .user-name {
        display: block;
        font-size: 14px;
        font-weight: 600;
        color: var(--text-main);
    }

    .user-role {
        display: block;
        font-size: 12px;
        color: var(--text-light);
    }

    .user-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        position: relative;
    }

    .user-avatar img {
        width: 100%;
        height: 100%;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid white;
        box-shadow: 0 0 0 2px var(--primary-color);
    }

    .status-dot {
        position: absolute;
        bottom: 0;
        right: 0;
        width: 10px;
        height: 10px;
        background: #10b981;
        border: 2px solid white;
        border-radius: 50%;
    }

    /* User Dropdown */
    .user-dropdown {
        position: absolute;
        top: 120%;
        right: 0;
        width: 260px;
        background: white;
        border-radius: 16px;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
        border: 1px solid var(--border-color);
        opacity: 0;
        visibility: hidden;
        transform: translateY(-10px);
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .user-profile-container.active .user-dropdown {
        opacity: 1;
        visibility: visible;
        transform: translateY(0);
    }

    .dropdown-header {
        padding: 20px;
        background: linear-gradient(to right, #fff7ed, #fff);
        border-bottom: 1px solid var(--border-color);
        border-radius: 16px 16px 0 0;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .dropdown-avatar img {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        border: 2px solid white;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }

    .dropdown-details h4 {
        margin: 0;
        font-size: 15px;
        font-weight: 700;
        color: var(--text-main);
    }

    .dropdown-details p {
        margin: 0;
        font-size: 12px;
        color: var(--text-light);
    }

    .dropdown-list {
        list-style: none;
        padding: 8px;
        margin: 0;
    }

    .dropdown-item {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 10px 16px;
        text-decoration: none;
        color: var(--text-main);
        font-size: 14px;
        border-radius: 8px;
        transition: background 0.2s;
        width: 100%;
        border: none;
        background: transparent;
        cursor: pointer;
        text-align: left;
    }

    .dropdown-item:hover {
        background: var(--bg-hover);
        color: var(--primary-color);
    }

    .dropdown-item i {
        font-size: 16px;
        color: var(--text-light);
        transition: color 0.2s;
    }

    .dropdown-item:hover i {
        color: var(--primary-color);
    }

    .dropdown-item.text-danger:hover {
        background: #fef2f2;
        color: #ef4444;
    }
    
    .dropdown-item.text-danger:hover i {
        color: #ef4444;
    }

    .dropdown-divider {
        height: 1px;
        background: var(--border-color);
        margin: 8px 0;
    }

    /* Mobile Responsive */
    @media (max-width: 768px) {
        .header {
            left: 0;
        }

        .mobile-menu-toggle {
            display: block;
        }

        .user-info {
            display: none;
        }
        
        .header-content {
            padding: 0 20px;
        }
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const userProfile = document.getElementById('userProfile');
    const mobileMenuToggle = document.getElementById('mobileMenuToggle');
    const sidebar = document.getElementById('sidebar');
    const notificationBadge = document.querySelector('.notification-badge');
    
    // 1. Fetch Notification Count
    function fetchNotificationCount() {
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        if (!csrfToken) return;

        fetch('/operations/notifications/count', {
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
            }
        })
        .then(response => response.ok ? response.json() : Promise.reject(response))
        .then(data => {
            if (data.count > 0) {
                notificationBadge.textContent = data.count > 99 ? '99+' : data.count;
                notificationBadge.style.display = 'block';
            } else {
                notificationBadge.style.display = 'none';
            }
        })
        .catch(err => console.error('Notification fetch error:', err));
    }

    // Initial Fetch
    fetchNotificationCount();
    
    // Listen for custom event if needed
    window.addEventListener('notificationMarkedAsRead', fetchNotificationCount);

    // 2. User Profile Dropdown Toggle
    if (userProfile) {
        userProfile.addEventListener('click', function(e) {
            e.stopPropagation();
            this.classList.toggle('active');
        });

        document.addEventListener('click', function(e) {
            if (!userProfile.contains(e.target)) {
                userProfile.classList.remove('active');
            }
        });
    }

    // 3. Mobile Sidebar Toggle
    if (mobileMenuToggle && sidebar) {
        mobileMenuToggle.addEventListener('click', function(e) {
            e.stopPropagation();
            sidebar.classList.toggle('mobile-open');
        });

        // Close sidebar when clicking outside on mobile
        document.addEventListener('click', function(e) {
            if (window.innerWidth <= 768 && 
                !sidebar.contains(e.target) && 
                !mobileMenuToggle.contains(e.target)) {
                sidebar.classList.remove('mobile-open');
            }
        });
    }

    // 4. Quick Action Buttons
    document.querySelectorAll('[data-action]').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.stopPropagation();
            const action = this.dataset.action;
            if (action === 'notifications') {
                window.location.href = '/operations/notifications';
            }
            // Add other actions here
        });
    });
});
</script>