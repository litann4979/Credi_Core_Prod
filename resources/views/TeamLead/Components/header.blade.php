<header class="header">
    <div class="header-content">
        <!-- Left Section -->
        <div class="header-left">
            <button class="mobile-menu-toggle" id="mobileMenuToggle">
                <i class="fas fa-bars"></i>
            </button>
            <div class="breadcrumb">
                <span class="breadcrumb-item">{{ $title1 ?? 'Dashboard' }}</span>
                @if(isset($subtitle))
                    {{-- <i class="fas fa-chevron-right"></i>
                    <span class="breadcrumb-item active">{{ $subtitle }}</span> --}}
                @endif
            </div>
        </div>
        <!-- Right Section -->
        <div class="header-right">
            <!-- Quick Actions -->
            <div class="quick-actions">
                <button class="action-btn" title="Notifications" data-action="notifications">
                    <i class="fas fa-bell"></i>
                    <span class="notification-badge"></span>
                </button>
            </div>
            <!-- Sign Out Button -->
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="action-btn logout-btn-header" title="Sign Out">
                    <i class="fas fa-sign-out-alt"></i>
                </button>
            </form>
            <!-- User Profile -->
            <div class="user-profile" id="userProfile">
                <div class="user-avatar">
                    <img src="https://ui-avatars.com/api/?name={{ Auth::user()->name ?? 'User' }}&background=f97316&color=fff" alt="User Avatar">
                    <div class="status-indicator"></div>
                </div>
                <div class="user-info">
                    <span class="user-name">{{ Auth::user()->name ?? 'John Doe' }}</span>
                    <span class="user-role">{{ Auth::user()->role ?? 'Team Lead' }}</span>
                </div>
                <i class="fas fa-chevron-down dropdown-arrow"></i>
                <!-- User Dropdown -->
                <div class="user-dropdown" id="userDropdown">
                    <div class="dropdown-header">
                        <div class="user-avatar-large">
                            <img src="https://ui-avatars.com/api/?name={{ Auth::user()->name ?? 'User' }}&background=f97316&color=fff" alt="User Avatar">
                        </div>
                        <div class="user-details">
                            <h4>{{ Auth::user()->name ?? 'John Doe' }}</h4>
                            <p>{{ Auth::user()->email ?? 'john@example.com' }}</p>
                        </div>
                    </div>
                    <div class="dropdown-menu">
                        <div class="dropdown-divider"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>
<style>
.header {
    position: fixed;
    top: 0;
    left: 280px;
    right: 0;
    height: 80px;
    background: #ffffff;
    border-bottom: 1px solid #e5e7eb;
    z-index: 999;
    transition: left 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
}

.sidebar.collapsed + .main-content .header {
    left: 80px;
}

.header-content {
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0 24px;
    gap: 24px;
}

.header-left {
    display: flex;
    align-items: center;
    gap: 16px;
}

.mobile-menu-toggle {
    display: none;
    background: none;
    border: none;
    font-size: 20px;
    color: #6b7280;
    cursor: pointer;
    padding: 8px;
    border-radius: 8px;
    transition: all 0.3s ease;
}

.mobile-menu-toggle:hover {
    background: #f3f4f6;
    color: #f97316;
}

.breadcrumb {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 14px;
}

.breadcrumb-item {
    color: #6b7280;
    font-weight: 500;
}

.breadcrumb-item.active {
    color: #1f2937;
    font-weight: 600;
}

.breadcrumb i {
    font-size: 12px;
    color: #d1d5db;
}

.header-right {
    display: flex;
    align-items: center;
    gap: 20px;
}

.quick-actions {
    display: flex;
    align-items: center;
    gap: 8px;
}

.action-btn {
    position: relative;
    width: 44px;
    height: 44px;
    background: #f9fafb;
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.3s ease;
    font-size: 16px;
    color: #6b7280;
}

.action-btn:hover {
    background: #f97316;
    border-color: #f97316;
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(249, 115, 22, 0.3);
}

.logout-btn-header {
    background: #fef2f2;
    border-color: #fecaca;
    color: #ef4444;
}

.logout-btn-header:hover {
    background: #ef4444;
    border-color: #ef4444;
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);
}

.notification-badge {
    position: absolute;
    top: -4px;
    right: -4px;
    background: #ef4444;
    color: white;
    font-size: 10px;
    font-weight: 600;
    padding: 2px 6px;
    border-radius: 10px;
    min-width: 16px;
    text-align: center;
    line-height: 1.2;
    display: none;
}

.user-profile {
    position: relative;
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 8px 16px;
    border-radius: 12px;
    cursor: pointer;
    transition: all 0.3s ease;
}

.user-profile:hover {
    background: #f9fafb;
}

.user-avatar {
    position: relative;
    width: 40px;
    height: 40px;
    border-radius: 50%;
    overflow: hidden;
    border: 2px solid #f97316;
}

.user-avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.status-indicator {
    position: absolute;
    bottom: 2px;
    right: 2px;
    width: 10px;
    height: 10px;
    background: #10b981;
    border: 2px solid white;
    border-radius: 50%;
}

.user-info {
    display: flex;
    flex-direction: column;
    gap: 2px;
}

.user-name {
    font-size: 14px;
    font-weight: 600;
    color: #1f2937;
    line-height: 1.2;
}

.user-role {
    font-size: 12px;
    color: #6b7280;
    font-weight: 500;
}

.dropdown-arrow {
    font-size: 12px;
    color: #9ca3af;
    transition: transform 0.3s ease;
}

.user-profile.active .dropdown-arrow {
    transform: rotate(180deg);
}

.user-dropdown {
    position: absolute;
    top: 100%;
    right: 0;
    width: 280px;
    background: white;
    border: 1px solid #e5e7eb;
    border-radius: 16px;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
    opacity: 0;
    visibility: hidden;
    transform: translateY(-10px);
    transition: all 0.3s ease;
    z-index: 1000;
    margin-top: 8px;
}

.user-profile.active .user-dropdown {
    opacity: 1;
    visibility: visible;
    transform: translateY(0);
}

.dropdown-header {
    padding: 20px;
    border-bottom: 1px solid #f3f4f6;
    display: flex;
    align-items: center;
    gap: 12px;
}

.user-avatar-large {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    overflow: hidden;
    border: 2px solid #f97316;
}

.user-avatar-large img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.user-details h4 {
    font-size: 16px;
    font-weight: 600;
    color: #1f2937;
    margin: 0 0 4px 0;
}

.user-details p {
    font-size: 14px;
    color: #6b7280;
    margin: 0;
}

.dropdown-menu {
    padding: 8px 0;
}

.dropdown-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px 20px;
    color: #6b7280;
    text-decoration: none;
    transition: all 0.3s ease;
    border: none;
    background: none;
    width: 100%;
    text-align: left;
    cursor: pointer;
    font-size: 14px;
    font-weight: 500;
}

.dropdown-item:hover {
    background: #fef3f2;
    color: #f97316;
}

.dropdown-item i {
    width: 16px;
    font-size: 14px;
}

.dropdown-divider {
    height: 1px;
    background: #f3f4f6;
    margin: 8px 0;
}

.logout-btn {
    color: #ef4444 !important;
}

.logout-btn:hover {
    background: #fef2f2 !important;
    color: #dc2626 !important;
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

        fetch('/team-lead/notifications/count', {
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
                notificationBadge.style.display = 'block';
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
    userProfile.addEventListener('click', function(e) {
        e.stopPropagation();
        this.classList.toggle('active');
    });

    // Close dropdown when clicking outside
    document.addEventListener('click', function(e) {
        if (!e.target.closest('#userProfile') && !e.target.closest('#userDropdown')) {
            userProfile.classList.remove('active');
        }
    });

    // Mobile menu toggle
    if (mobileMenuToggle && sidebar) {
        mobileMenuToggle.addEventListener('click', function(e) {
            e.stopPropagation();
            sidebar.classList.toggle('mobile-open');
        });
    } else {
        console.warn('Sidebar or mobile menu toggle not found in DOM');
    }

    // Action buttons
    actionBtns.forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.stopPropagation();
            const action = this.dataset.action;
            console.log('Action button clicked:', action); // Debug
            if (action === 'notifications') {
                window.location.href = '/team-lead/notifications';
            }
        });
    });
});
</script>
