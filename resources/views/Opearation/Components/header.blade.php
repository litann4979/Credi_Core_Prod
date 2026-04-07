<header class="header">
    <div class="header-content">
        <div class="header-left">
            <button class="mobile-menu-toggle" id="mobileMenuToggle">
                <i class="fas fa-bars"></i>
            </button>
            {{-- <div class="breadcrumb">
                <span class="breadcrumb-item home-icon"><i class="fas fa-home"></i></span>
                <span class="breadcrumb-separator">/</span>
                <span class="breadcrumb-item active">{{ $title1 ?? 'Dashboard' }}</span>
                @if(isset($subtitle))
                     <span class="breadcrumb-separator">/</span>
                     <span class="breadcrumb-item">{{ $subtitle }}</span>
                @endif
            </div> --}}
        </div>

        <div class="header-right">
            <div class="quick-actions">
                <button class="action-btn notification-btn" title="Notifications" data-action="notifications">
                    <i class="fas fa-bell"></i>
                    <span class="notification-badge"></span>
                </button>
            </div>

            <form method="POST" action="{{ route('logout') }}" class="logout-form">
                @csrf
                <button type="submit" class="action-btn logout-btn-header" title="Sign Out">
                    <i class="fas fa-power-off"></i>
                </button>
            </form>

            <div class="divider"></div>

            <div class="user-profile" id="userProfile">
                <div class="user-info">
                    <span class="user-name">{{ Auth::user()->name ?? 'User' }}</span>
                    <span class="user-role">{{ Auth::user()->role ?? 'Operation' }}</span>
                </div>
                <div class="user-avatar">
                    <img src="https://ui-avatars.com/api/?name={{ Auth::user()->name ?? 'User' }}&background=f97316&color=fff&bold=true" alt="Avatar">
                    <div class="status-indicator"></div>
                </div>
                <i class="fas fa-chevron-down dropdown-arrow"></i>

                <div class="user-dropdown" id="userDropdown">
                    <div class="dropdown-header">
                        <div class="user-avatar-large">
                            <img src="https://ui-avatars.com/api/?name={{ Auth::user()->name ?? 'User' }}&background=f97316&color=fff&bold=true" alt="Avatar">
                        </div>
                        <div class="user-details">
                            <h4>{{ Auth::user()->name ?? 'User' }}</h4>
                            <p>{{ Auth::user()->email ?? '' }}</p>
                        </div>
                    </div>
                    <div class="dropdown-menu">
                        </div>
                </div>
            </div>
        </div>
    </div>
</header>

<style>
    /* Modern Header Styles */
    .header {
        position: fixed;
        top: 0;
        left: 280px;
        right: 0;
        height: 80px;
        background: rgba(255, 255, 255, 0.9);
        backdrop-filter: blur(8px);
        border-bottom: 1px solid #e2e8f0;
        z-index: 999;
        transition: left 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02);
    }

    .sidebar.collapsed + .main-content .header {
        left: 0; /* Adjust based on collapsed sidebar logic */
    }

    .header-content {
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0 32px;
    }

    .header-left {
        display: flex;
        align-items: center;
        gap: 20px;
    }

    .mobile-menu-toggle {
        display: none;
        background: transparent;
        border: 1px solid #e2e8f0;
        font-size: 18px;
        color: #64748b;
        cursor: pointer;
        padding: 8px;
        border-radius: 8px;
        transition: all 0.2s;
    }

    .mobile-menu-toggle:hover {
        background: #f8fafc;
        color: #f97316;
        border-color: #f97316;
    }

    .breadcrumb {
        display: flex;
        align-items: center;
        gap: 8px;
        font-family: 'Inter', sans-serif;
    }

    .breadcrumb-item {
        font-size: 14px;
        color: #64748b;
        font-weight: 500;
    }

    .breadcrumb-item.home-icon {
        color: #94a3b8;
    }

    .breadcrumb-item.active {
        color: #0f172a;
        font-weight: 600;
    }

    .breadcrumb-separator {
        color: #cbd5e1;
        font-size: 12px;
    }

    .header-right {
        display: flex;
        align-items: center;
        gap: 16px;
    }

    .action-btn {
        position: relative;
        width: 40px;
        height: 40px;
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.2s ease;
        color: #64748b;
    }

    .action-btn:hover {
        background: #f8fafc;
        border-color: #cbd5e1;
        color: #f97316;
        transform: translateY(-1px);
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }

    .logout-btn-header:hover {
        color: #ef4444;
        border-color: #fecaca;
        background: #fef2f2;
    }

    .notification-badge {
        position: absolute;
        top: -2px;
        right: -2px;
        background: #ef4444;
        color: white;
        font-size: 10px;
        font-weight: 700;
        padding: 0 5px;
        height: 16px;
        min-width: 16px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 2px solid white;
        display: none; /* Controlled by JS */
    }

    .divider {
        width: 1px;
        height: 24px;
        background: #e2e8f0;
        margin: 0 8px;
    }

    .user-profile {
        position: relative;
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 6px 8px 6px 16px;
        border-radius: 50px;
        cursor: pointer;
        transition: all 0.2s ease;
        border: 1px solid transparent;
    }

    .user-profile:hover {
        background: #f8fafc;
        border-color: #e2e8f0;
    }

    .user-info {
        display: flex;
        flex-direction: column;
        align-items: flex-end;
        line-height: 1.3;
    }

    .user-name {
        font-size: 14px;
        font-weight: 600;
        color: #1e293b;
    }

    .user-role {
        font-size: 11px;
        color: #64748b;
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 0.02em;
    }

    .user-avatar {
        position: relative;
        width: 38px;
        height: 38px;
        border-radius: 50%;
        padding: 2px;
        background: white;
        border: 1px solid #e2e8f0;
    }

    .user-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        border-radius: 50%;
    }

    .status-indicator {
        position: absolute;
        bottom: 0;
        right: 0;
        width: 10px;
        height: 10px;
        background: #10b981;
        border: 2px solid white;
        border-radius: 50%;
    }

    .dropdown-arrow {
        font-size: 10px;
        color: #94a3b8;
        margin-left: 4px;
        transition: transform 0.2s;
    }

    .user-profile.active .dropdown-arrow {
        transform: rotate(180deg);
    }

    /* User Dropdown */
    .user-dropdown {
        position: absolute;
        top: calc(100% + 10px);
        right: 0;
        width: 260px;
        background: white;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
        opacity: 0;
        visibility: hidden;
        transform: translateY(-10px);
        transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
        z-index: 1000;
    }

    .user-profile.active .user-dropdown {
        opacity: 1;
        visibility: visible;
        transform: translateY(0);
    }

    .dropdown-header {
        padding: 20px;
        display: flex;
        align-items: center;
        gap: 16px;
        border-bottom: 1px solid #f1f5f9;
        background: #f8fafc;
        border-radius: 16px 16px 0 0;
    }

    .user-avatar-large {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        border: 2px solid white;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }

    .user-avatar-large img {
        width: 100%;
        height: 100%;
        border-radius: 50%;
    }

    .user-details h4 {
        font-size: 15px;
        font-weight: 700;
        color: #1e293b;
        margin: 0 0 2px 0;
    }

    .user-details p {
        font-size: 12px;
        color: #64748b;
        margin: 0;
        word-break: break-all;
    }

    .dropdown-menu {
        padding: 8px;
    }

    /* Mobile Responsive */
    @media (max-width: 768px) {
        .header {
            left: 0;
            padding: 0 16px;
        }

        .header-content {
            padding: 0 16px;
        }

        .mobile-menu-toggle {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .user-info, .divider {
            display: none;
        }
        
        .user-profile {
            padding: 0;
            border: none;
        }
        
        .user-profile:hover {
            background: transparent;
        }

        .dropdown-arrow {
            display: none;
        }
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const userProfile = document.getElementById('userProfile');
    const userDropdown = document.getElementById('userDropdown');
    const mobileMenuToggle = document.getElementById('mobileMenuToggle');
    const sidebar = document.getElementById('sidebar');
    const actionBtns = document.querySelectorAll('.action-btn');
    const notificationBadge = document.querySelector('.notification-badge');

    // Fetch unread notification count
    function fetchNotificationCount() {
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        if (!csrfToken) {
            console.error('CSRF token not found');
            return;
        }

        fetch('/operations/notifications/count', {
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
            },
            credentials: 'same-origin',
        })
        .then(response => {
            if (!response.ok) throw new Error(`Failed to fetch notification count: ${response.status}`);
            return response.json();
        })
        .then(data => {
            if (data.count > 0) {
                notificationBadge.textContent = data.count;
                notificationBadge.style.display = 'flex'; // Changed to flex for proper centering
            } else {
                notificationBadge.style.display = 'none';
            }
        })
        .catch(error => {
            console.error('Error fetching notification count:', error);
        });
    }

    // Initial fetch of notification count
    fetchNotificationCount();

    // Refresh notification count when a notification is marked as read
    window.addEventListener('notificationMarkedAsRead', fetchNotificationCount);

    // User dropdown toggle
    if (userProfile) {
        userProfile.addEventListener('click', function(e) {
            e.stopPropagation();
            this.classList.toggle('active');
        });
    }

    // Close dropdown when clicking outside
    document.addEventListener('click', function(e) {
        if (userProfile && !e.target.closest('#userProfile')) {
            userProfile.classList.remove('active');
        }
    });

    // Mobile menu toggle
    if (mobileMenuToggle && sidebar) {
        mobileMenuToggle.addEventListener('click', function(e) {
            e.stopPropagation();
            sidebar.classList.toggle('mobile-open');
        });
    }

    // Action buttons
    actionBtns.forEach(btn => {
        btn.addEventListener('click', function(e) {
            // Prevent event from bubbling up to logout form if it's not the logout button
            if(!this.classList.contains('logout-btn-header')) {
                e.preventDefault();
            }
            e.stopPropagation();
            
            const action = this.dataset.action;
            if (action === 'notifications') {
                window.location.href = '/operations/notifications';
            }
        });
    });
});
</script>