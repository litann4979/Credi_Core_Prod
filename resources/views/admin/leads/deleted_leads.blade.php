<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Deleted Leads</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap');

        :root {
            --primary-500: #3b82f6;
            --primary-600: #2563eb;
            --gray-50: #f9fafb;
            --gray-100: #f3f4f6;
            --gray-200: #e5e7eb;
            --gray-500: #6b7280;
            --gray-600: #4b5563;
            --gray-700: #374151;
            --gray-900: #111827;
            --error-500: #ef4444;
            --error-600: #dc2626;
            --success-500: #10b981;
            --success-600: #059669;
            --warning-500: #f59e0b;
        }

        * { box-sizing: border-box; }

        body {
            background: linear-gradient(135deg, var(--gray-50) 0%, #ffffff 100%);
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            color: var(--gray-700);
            line-height: 1.6;
        }

        .main-content {
            margin-left: 280px;
            margin-top: 80px;
            min-height: calc(100vh - 80px);
            padding: 2rem;
        }

        .dashboard-card {
            background: #ffffff;
            border: 1px solid var(--gray-200);
            border-radius: 20px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            overflow: hidden;
        }

        .card-header {
            background: linear-gradient(135deg, #c4d6e9 100%, #f8f9fa 0%);
            padding: 2rem;
            position: relative;
        }

        .card-header h2 {
            font-size: 1.5rem;
            font-weight: 700;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .page-title {
            font-size: 2.5rem;
            font-weight: 900;
            color: var(--gray-900);
            margin-bottom: 2.5rem;
            background: linear-gradient(135deg, var(--gray-900) 0%, var(--primary-600) 50%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .table-modern {
            border-radius: 16px;
            overflow: hidden;
            border: 1px solid var(--gray-200);
            margin-top: 1.5rem;
        }

        .table-modern thead {
            background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 100%);
            color: white;
        }

        .table-modern thead th {
            color: white;
            font-weight: 600;
            padding: 0.75rem 1rem;
            font-size: 0.68rem;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            text-align: left;
            border: none;
            position: sticky;
            top: 0;
            z-index: 5;
        }

        .table-modern tbody tr {
            transition: all 0.2s ease;
            border-bottom: 1px solid var(--gray-100);
        }

        .table-modern tbody tr:hover {
            background: linear-gradient(135deg, #f0f9ff 0%, var(--gray-50) 100%);
        }

        .table-modern tbody tr:last-child {
            border-bottom: none;
        }

        .table-modern tbody td {
            padding: 0.65rem 1rem;
            font-weight: 500;
            color: var(--gray-600);
            font-size: 0.82rem;
            line-height: 1.2;
        }

        .btn-success {
            background: linear-gradient(135deg, var(--success-500) 0%, var(--success-600) 100%);
            border: none;
            color: white;
            font-weight: 600;
            border-radius: 12px;
            padding: 0.4rem 0.75rem;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            cursor: pointer;
            font-size: 0.78rem;
        }

        .btn-success:hover {
            background: linear-gradient(135deg, var(--success-600) 0%, #047857 100%);
            transform: translateY(-2px);
        }

        .btn-secondary {
            background: linear-gradient(135deg, var(--gray-500) 0%, var(--gray-600) 100%);
            border: none;
            color: white;
            font-weight: 600;
            border-radius: 12px;
            padding: 0.75rem 1.5rem;
            cursor: pointer;
            font-size: 0.875rem;
            transition: all 0.3s ease;
        }

        .btn-secondary:hover {
            background: linear-gradient(135deg, var(--gray-600) 0%, var(--gray-700) 100%);
            transform: translateY(-2px);
        }

        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.7);
            backdrop-filter: blur(12px);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 9999;
            opacity: 0;
            visibility: hidden;
            transition: all 0.4s ease;
        }

        .modal-overlay.active {
            opacity: 1;
            visibility: visible;
        }

        .modal-container {
            background: white;
            border-radius: 24px;
            max-width: 600px;
            width: 95%;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            transform: scale(0.9) translateY(40px);
            transition: all 0.4s ease;
            border: 1px solid var(--gray-200);
            overflow: hidden;
        }

        .modal-overlay.active .modal-container {
            transform: scale(1) translateY(0);
        }

        .modal-header {
            padding: 2rem;
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .modal-title {
            font-size: 1.5rem;
            font-weight: 700;
            margin: 0;
        }

        .modal-close {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            border: none;
            background: rgba(255, 255, 255, 0.2);
            color: white;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0;
        }

        .modal-close:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: scale(1.1) rotate(90deg);
        }

        .modal-content {
            padding: 2rem;
        }

        .modal-footer {
            padding: 24px 32px;
            background: #f8fafc;
            border-top: 1px solid var(--gray-200);
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 16px;
        }

        .confirm-message {
            text-align: center;
            padding: 1rem 0;
        }

        .confirm-icon {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: linear-gradient(135deg, #059669 0%, #10b981 100%);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            font-size: 2rem;
        }

        .confirm-message h3 {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--gray-900);
            margin-bottom: 0.75rem;
        }

        .confirm-message p {
            color: var(--gray-600);
            font-size: 1rem;
            line-height: 1.6;
        }

        .notification {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 1rem 1.5rem;
            border-radius: 16px;
            color: white;
            z-index: 10000;
            transition: all 0.3s ease;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
            display: flex;
            align-items: center;
            gap: 0.75rem;
            font-weight: 500;
        }

        .notification.success {
            background: linear-gradient(135deg, var(--success-500) 0%, var(--success-600) 100%);
        }

        .notification.error {
            background: linear-gradient(135deg, var(--error-500) 0%, var(--error-600) 100%);
        }

        .loading-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.7);
            backdrop-filter: blur(8px);
            z-index: 2000;
            justify-content: center;
            align-items: center;
        }

        .loading-overlay.active {
            display: flex;
        }

        .loading-overlay::after {
            content: '';
            border: 4px solid rgba(255, 255, 255, 0.3);
            border-top: 4px solid var(--primary-500);
            border-radius: 50%;
            width: 50px;
            height: 50px;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .empty-state {
            text-align: center;
            padding: 3rem 2rem;
            color: var(--gray-500);
        }

        .empty-state i {
            font-size: 3rem;
            margin-bottom: 1rem;
            opacity: 0.5;
        }

        .back-button {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 2rem;
        }

        .deletion-history {
            font-size: 0.875rem;
            color: var(--gray-500);
            margin-top: 0.5rem;
        }

        @media (max-width: 768px) {
            .main-content {
                margin-left: 0;
                padding: 1.25rem;
            }

            .page-title {
                font-size: 2rem;
                margin-bottom: 1.5rem;
            }

            .table-modern {
                font-size: 0.875rem;
            }

            .table-modern thead th,
            .table-modern tbody td {
                padding: 0.55rem 0.75rem;
                font-size: 0.74rem;
            }

            .btn-success {
                padding: 0.5rem 0.75rem;
                font-size: 0.75rem;
            }
        }
    </style>
</head>
<body>
    @include('admin.Components.sidebar')

    <div class="main-content">
        @include('admin.Components.header')

        <div class="w-full max-w-7xl mx-auto">
            <a href="{{ route('admin.dashboard') }}" class="btn-secondary back-button">
                <i class="fas fa-arrow-left"></i>
                Back to Dashboard
            </a>

            <h1 class="page-title">
                <i class="fas fa-trash-alt"></i>
                Deleted Leads
            </h1>

            <div class="dashboard-card">
                <div class="card-header flex justify-between items-center">
    <h2 class="flex items-center gap-2">
        <i class="fas fa-list"></i>
        Trash Bin
    </h2>
    <span id="deletedLeadCount"
          class="px-4 py-1.5 text-sm font-semibold rounded-full bg-blue-100 text-blue-800 border border-blue-300 shadow-sm">
        0 Leads
    </span>
</div>

                <div style="padding: 2rem;">
                    <div id="deletedLeadsContainer">
                        <div class="empty-state">
                            <i class="fas fa-spinner fa-spin"></i>
                            <p>Loading deleted leads...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Restore Confirmation Modal -->
    <div class="modal-overlay" id="restoreModal">
        <div class="modal-container">
            <div class="modal-header">
                <h2 class="modal-title">Restore Lead</h2>
                <button class="modal-close" onclick="closeRestoreModal()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-content">
                <div class="confirm-message">
                    <div class="confirm-icon">
                        <i class="fas fa-undo"></i>
                    </div>
                    <h3>Restore This Lead?</h3>
                    <p id="restoreLeadInfo"></p>
                    <div class="deletion-history" id="deletionHistory"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn-secondary" onclick="closeRestoreModal()">Cancel</button>
                <button class="btn-success" onclick="confirmRestore()">
                    <i class="fas fa-check"></i> Yes, Restore
                </button>
            </div>
        </div>
    </div>

    <!-- Loading Overlay -->
    <div class="loading-overlay" id="loadingOverlay"></div>

    <script>
        let currentLeadId = null;
        let allDeletedLeads = [];

        document.addEventListener('DOMContentLoaded', function () {
            // Use inline table loader on first render; avoid full-page dim overlay on page open.
            loadDeletedLeads(false);
        });

        function loadDeletedLeads(showOverlay = false) {
            if (showOverlay) {
                showLoading(true);
            }

            const controller = new AbortController();
            const timeoutId = setTimeout(() => controller.abort(), 15000);

            // Use the API endpoint specifically
            fetch('/admin/lead/deleted', {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                signal: controller.signal
            })
            .then(res => {
                if (!res.ok) {
                    return res.text().then(text => {
                        console.error('Server response:', text);
                        throw new Error(`HTTP ${res.status}: ${text.substring(0, 100)}`);
                    });
                }
                return res.json();
            })
            .then(data => {
                console.log('Deleted leads loaded:', data);
                allDeletedLeads = data.data || [];
                renderDeletedLeads();
            })
            .catch(error => {
                console.error('Error loading deleted leads:', error);
                if (error.name === 'AbortError') {
                    showNotification('Deleted leads request timed out. Please try again.', 'error');
                    renderEmptyState('Request timed out while loading deleted leads.');
                    return;
                }
                showNotification(`Failed to load deleted leads: ${error.message}`, 'error');
                renderEmptyState('Error loading deleted leads. Check console for details.');
            })
            .finally(() => {
                clearTimeout(timeoutId);
                if (showOverlay) {
                    showLoading(false);
                }
            });
        }

        function renderDeletedLeads() {
           const container = document.getElementById('deletedLeadsContainer');

    // ✅ Update deleted lead count
    document.getElementById('deletedLeadCount').textContent =
        `${allDeletedLeads.length} ${allDeletedLeads.length === 1 ? 'Lead' : 'Leads'}`;

    if (!allDeletedLeads || allDeletedLeads.length === 0) {
        renderEmptyState('No deleted leads found');
        return;
    }

            let tableHTML = `
                <div class="table-modern" style="overflow-x: auto;">
                    <table style="width: 100%; min-width: 900px;">
                        <thead>
                            <tr>
                                <th>Lead Name</th>
                                <th>Phone</th>
                                <th>Company</th>
                                <th>Loan Amount</th>
                                <th>Employee</th>
                                <th>Deleted Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
            `;

            allDeletedLeads.forEach(lead => {
                const deletedDate = new Date(lead.deleted_at).toLocaleString('en-IN', {
                    year: 'numeric',
                    month: 'short',
                    day: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit'
                });

                tableHTML += `
                    <tr>
                        <td class="font-semibold">${lead.name || 'N/A'}</td>
                        <td>${lead.phone || 'N/A'}</td>
                        <td>${lead.company_name || 'N/A'}</td>
                        <td style="color: #10b981; font-weight: 600;">₹${parseInt(lead.lead_amount || 0).toLocaleString('en-IN')}</td>
                        <td>${lead.employee?.name || 'N/A'}</td>
                        <td>${deletedDate}</td>
                        <td>
                            <button class="btn-success" onclick="showRestoreModal(${lead.id})">
                                <i class="fas fa-undo"></i> Restore
                            </button>
                        </td>
                    </tr>
                `;
            });

            tableHTML += `
                        </tbody>
                    </table>
                </div>
            `;

            container.innerHTML = tableHTML;
        }

        function renderEmptyState(message) {
            const container = document.getElementById('deletedLeadsContainer');
            container.innerHTML = `
                <div class="empty-state">
                    <i class="fas fa-inbox"></i>
                    <p>${message}</p>
                </div>
            `;
        }

        function showRestoreModal(leadId) {
            currentLeadId = leadId;
            const lead = allDeletedLeads.find(l => l.id === leadId);

            if (!lead) return;

            const deletedDate = new Date(lead.deleted_at).toLocaleString('en-IN');
            const deletedBy = lead.histories?.find(h => h.action === 'soft_deleted')?.user?.name || 'Unknown';

            document.getElementById('restoreLeadInfo').textContent =
                `Are you sure you want to restore this lead? (${lead.name} - ₹${parseInt(lead.lead_amount || 0).toLocaleString('en-IN')})`;

            document.getElementById('deletionHistory').innerHTML =
                `<strong>Deleted by:</strong> ${deletedBy} on ${deletedDate}`;

            document.getElementById('restoreModal').classList.add('active');
        }

        function closeRestoreModal() {
            document.getElementById('restoreModal').classList.remove('active');
            currentLeadId = null;
        }

        function confirmRestore() {
            if (!currentLeadId) return;

            showLoading(true);

            fetch(`/admin/leads/${currentLeadId}/restore`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                }
            })
            .then(res => {
                if (!res.ok) {
                    return res.json().then(err => {
                        throw new Error(err.message || `HTTP ${res.status}`);
                    });
                }
                return res.json();
            })
            .then(data => {
                showNotification('Lead restored successfully!', 'success');
                closeRestoreModal();
                loadDeletedLeads(false);
            })
            .catch(error => {
                console.error('Error restoring lead:', error);
                showNotification(`Failed to restore lead: ${error.message}`, 'error');
            })
            .finally(() => showLoading(false));
        }

        function showNotification(message, type) {
            const notification = document.createElement('div');
            notification.className = `notification ${type}`;
            notification.innerHTML = `
                <i class="fas fa-${type === 'error' ? 'exclamation-circle' : 'check-circle'}"></i>
                ${message}
            `;
            document.body.appendChild(notification);

            setTimeout(() => {
                notification.style.opacity = '0';
                setTimeout(() => notification.remove(), 300);
            }, 3000);
        }

        function showLoading(show) {
            const overlay = document.getElementById('loadingOverlay');
            if (overlay) {
                if (show) {
                    overlay.classList.add('active');
                } else {
                    overlay.classList.remove('active');
                }
            }
        }
    </script>
</body>
</html>
