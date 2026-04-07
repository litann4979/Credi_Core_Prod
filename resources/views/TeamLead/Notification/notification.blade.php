<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Notifications</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f9fafb;
            margin: 0;
            padding: 0;
        }

        .notifications-container {
            max-width: 800px;
            margin: 100px auto;
            padding: 24px;
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 16px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }

        .panel-header {
            padding: 24px;
            border-bottom: 1px solid #e5e7eb;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .panel-header h3 {
            font-size: 24px;
            font-weight: 700;
            color: #1f2937;
            margin: 0;
        }

        .back-btn {
            background: #f97316;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            font-size: 14px;
            transition: all 0.3s ease;
        }

        .back-btn:hover {
            background: #ea580c;
            transform: translateY(-1px);
        }

        .panel-content {
            padding: 16px 0;
            min-height: 300px;
        }

        .notification-item {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            padding: 16px 24px;
            border-bottom: 1px solid #f3f4f6;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .notification-item:hover {
            background: #f9fafb;
        }

        .notification-item.unread {
            background: #fef3f2;
            border-left: 3px solid #f97316;
        }

        .notification-icon {
            width: 40px;
            height: 40px;
            background: #f3f4f6;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .notification-item.unread .notification-icon {
            background: #f97316;
            color: white;
        }

        .notification-content {
            flex: 1;
        }

        .notification-content h4 {
            font-size: 14px;
            font-weight: 600;
            color: #1f2937;
            margin: 0 0 4px 0;
        }

        .notification-content p {
            font-size: 13px;
            color: #6b7280;
            margin: 0 0 8px 0;
            line-height: 1.4;
        }

        .notification-time {
            font-size: 12px;
            color: #9ca3af;
            font-weight: 500;
        }

        .view-task-btn {
            background: #f97316;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 13px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .view-task-btn:hover {
            background: #ea580c;
            transform: translateY(-1px);
        }

        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1002;
            justify-content: center;
            align-items: center;
        }

        .modal-content {
            background: #fff;
            padding: 20px;
            border-radius: 12px;
            width: 400px;
            max-height: 80vh;
            overflow-y: auto;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #e5e7eb;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }

        .modal-header h3 {
            font-size: 18px;
            font-weight: 700;
            color: #1f2937;
            margin: 0;
        }

        .close-modal {
            background: none;
            border: none;
            font-size: 20px;
            color: #6b7280;
            cursor: pointer;
            padding: 5px;
        }

        .close-modal:hover {
            color: #f97316;
        }

        .modal-body p {
            margin: 10px 0;
            font-size: 14px;
            color: #1f2937;
        }

        .status-update {
            margin-top: 15px;
        }

        .status-update label {
            display: block;
            margin-bottom: 5px;
            font-weight: 500;
            color: #6b7280;
        }

        .status-update select,
        .status-update textarea,
        .status-update input[type="range"] {
            width: 100%;
            padding: 8px;
            margin-bottom: 10px;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            font-size: 14px;
        }

        .status-update textarea {
            height: 80px;
            resize: vertical;
        }

        .status-update input[type="range"] {
            cursor: pointer;
        }

        .status-update .progress-value {
            font-size: 14px;
            color: #1f2937;
            margin-bottom: 10px;
            text-align: center;
        }

        .status-update button {
            background: #f97316;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .status-update button:hover {
            background: #ea580c;
            transform: translateY(-1px);
        }
        .holiday-icon {
    background-color: #4CAF50 !important;
    color: white !important;
}

 .salary-slip-icon {
            background-color: #3B82F6 !important;
            color: white !important;
        }

        .download-btn {
            background: #10B981;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 13px;
            font-weight: 600;
            transition: all 0.3s ease;
            margin-left: 8px;
        }

        .download-btn:hover {
            background: #059669;
            transform: translateY(-1px);
        }
    </style>
</head>
<body>
     @include('TeamLead.Components.sidebar')
    <div class="notifications-container">
        @include('TeamLead.Components.header', ['title' => 'Notification Management', 'subtitle' => 'Check your notifications and manage tasks efficiently.'])
        <div class="panel-header">
            <h3>Notifications</h3>
            {{-- <button class="back-btn" onclick="window.location.href='/team-lead/leads'">Back to Home</button> --}}
        </div>
        <div class="panel-content" id="notificationList">
            <!-- Notifications will be populated dynamically -->
        </div>
    </div>

    <!-- Task Details Modal -->
    <div id="taskModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Task Details</h3>
                <button class="close-modal">×</button>
            </div>
            <div class="modal-body">
                <p><strong>Title:</strong> <span id="modalTaskTitle"></span></p>
                <p><strong>Description:</strong> <span id="modalTaskDescription"></span></p>
                <p><strong>Assigned Date:</strong> <span id="modalTaskAssignedDate"></span></p>
                <p><strong>Due Date:</strong> <span id="modalTaskDueDate"></span></p>
                <p><strong>Priority:</strong> <span id="modalTaskPriority"></span></p>
                <p><strong>Attachements:</strong> <ul id="viewTaskAttachments" style="margin-left: 20px;"></ul>
                <p><strong>Current Status:</strong> <span id="modalTaskStatus"></span></p>
                <p><strong>Progress:</strong> <span id="modalTaskProgress"></span>%</p>
                <div class="status-update">
                    <label for="taskProgress">Update Progress:</label>
                    <input type="range" id="taskProgress" min="1" max="100" value="0">
                    <div class="progress-value" id="progressValue">0%</div>
                    <label for="taskStatus">Update Status:</label>
                    <select id="taskStatus">
                        <option value="pending">Pending</option>
                        <option value="in_progress">In Progress</option>
                        <option value="completed">Completed</option>
                    </select>
                    <textarea id="taskMessage" placeholder="Add a message (optional)"></textarea>
                    <button id="saveTaskStatus">Save</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const notificationList = document.getElementById('notificationList');
            const taskModal = document.getElementById('taskModal');
            const closeModal = document.querySelector('.close-modal');
            const saveTaskStatus = document.getElementById('saveTaskStatus');
            const modalTaskTitle = document.getElementById('modalTaskTitle');
            const modalTaskDescription = document.getElementById('modalTaskDescription');
            const modalTaskAssignedDate = document.getElementById('modalTaskAssignedDate');
            const modalTaskDueDate = document.getElementById('modalTaskDueDate');
            const modalTaskPriority = document.getElementById('modalTaskPriority');
            const attachmentList = document.getElementById('viewTaskAttachments');
            const modalTaskStatus = document.getElementById('modalTaskStatus');
            const modalTaskProgress = document.getElementById('modalTaskProgress');
            const taskStatus = document.getElementById('taskStatus');
            const taskMessage = document.getElementById('taskMessage');
            const taskProgress = document.getElementById('taskProgress');
            const progressValue = document.getElementById('progressValue');

            // Update progress value display when slider changes
            taskProgress.addEventListener('input', function() {
                progressValue.textContent = `${this.value}%`;
            });

            // Fetch notifications on page load
            fetchNotifications();

            // Close modal when clicking outside
            document.addEventListener('click', function(e) {
                if (!taskModal.contains(e.target) && !e.target.closest('.view-task-btn')) {
                    taskModal.style.display = 'none';
                }
            });

            // Close modal button
            closeModal.addEventListener('click', function() {
                console.log('Close modal clicked'); // Debug
                taskModal.style.display = 'none';
            });

            // Save task status
            saveTaskStatus.addEventListener('click', function() {
                const taskId = taskModal.dataset.taskId;
                const status = taskStatus.value;
                const message = taskMessage.value.trim();
                const progress = parseInt(taskProgress.value);
                console.log('Save task status:', { taskId, status, message, progress }); // Debug
                if (!taskId || isNaN(taskId)) {
                    console.error('Invalid taskId:', taskId);
                    alert('Invalid task ID');
                    return;
                }
                updateTaskStatus(taskId, status, message, progress);
            });

            // Event delegation for view-task-btn
            notificationList.addEventListener('click', function(e) {
                if (e.target.classList.contains('view-task-btn')) {
                    e.preventDefault(); // Prevent any default behavior
                    const notificationId = e.target.dataset.notificationId;
                    const taskId = e.target.dataset.taskId;
                    console.log('View task clicked:', { notificationId, taskId }); // Debug
                    if (notificationId && taskId) {
                        showTaskDetails(notificationId, taskId);
                    } else {
                        console.error('Missing notificationId or taskId');
                    }
                }
            });




          // Fetch notifications
  function fetchNotifications() {
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                if (!csrfToken) {
                    console.error('CSRF token not found.');
                    return;
                }

                fetch('/team-lead/notifications/fetch', {
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                    },
                    credentials: 'same-origin',
                })
                .then(response => {
                    if (!response.ok) throw new Error(`Failed to fetch notifications: ${response.status} ${response.statusText}`);
                    return response.json();
                })
                .then(data => {
                    console.log('Notifications fetched:', data);
                    notificationList.innerHTML = '';

                    if (!Array.isArray(data)) {
                        console.error('Expected an array of notifications, got:', data);
                        return;
                    }

                    data.forEach(notification => {
                        if (!notification.id) {
                            console.warn('Invalid notification data (missing ID):', notification);
                            return;
                        }

                        const isHolidayNotification = !notification.task_id;
                        const isSalarySlipNotification = !!notification.salary_slip_id;

                        const div = document.createElement('div');
                        div.className = `notification-item${notification.is_read ? '' : ' unread'}`;
                        div.dataset.notificationId = notification.id;

                        let iconClass = 'fa-tasks';
                        let iconContainerClass = '';

                        if (isHolidayNotification) {
                            iconClass = 'fa-calendar-alt';
                            iconContainerClass = 'holiday-icon';
                        } else if (isSalarySlipNotification) {
                            iconClass = 'fa-file-invoice-dollar';
                            iconContainerClass = 'salary-slip-icon';
                        }

                        div.innerHTML = `
                            <div class="notification-icon ${iconContainerClass}">
                                <i class="fas ${iconClass}"></i>
                            </div>
                            <div class="notification-content">
                                <h4>${notification.message || 'No message'}</h4>
                                <span class="notification-time">${formatDate(notification.created_at) || 'N/A'}</span>
                                ${!isHolidayNotification && !isSalarySlipNotification ?
                                    `<button class="view-task-btn" data-notification-id="${notification.id}"
                                            data-task-id="${notification.task_id}">View</button>`
                                    : ''}
                                ${isSalarySlipNotification ?
                                    `<button class="download-btn" data-salary-slip-id="${notification.salary_slip_id}">
                                        <i class="fas fa-download"></i> Download
                                    </button>`
                                    : ''}
                            </div>
                        `;

                        // Add click handler to mark as read
                        div.addEventListener('click', function(e) {
                            // Don't mark as read if clicking the View or Download button
                            if (!e.target.closest('.view-task-btn') && !e.target.closest('.download-btn')) {
                                markNotificationAsRead(notification.id, div);
                            }
                        });

                        notificationList.appendChild(div);
                    });

                    // Add event listeners to download buttons
                    document.querySelectorAll('.download-btn').forEach(button => {
                        button.addEventListener('click', function(e) {
                            e.stopPropagation(); // Prevent marking as read when clicking download
                            const salarySlipId = this.dataset.salarySlipId;
                            if (salarySlipId) {
                                downloadSalarySlip(salarySlipId);
                            }
                        });
                    });
                })
                .catch(error => {
                    console.error('Error fetching notifications:', error);
                    alert('Failed to load notifications. Please try again.');
                });
            }


              // Format date for display
            function formatDate(dateString) {
                if (!dateString) return 'N/A';

                const date = new Date(dateString);
                return date.toLocaleDateString() + ' ' + date.toLocaleTimeString();
            }

            // Download salary slip
function downloadSalarySlip(salarySlipId) {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

    // Mark notification as read
    const notificationItem = document.querySelector(`.download-btn[data-salary-slip-id="${salarySlipId}"]`)?.closest('.notification-item');
    if (notificationItem) {
        const notificationId = notificationItem.dataset.notificationId;
        markNotificationAsRead(notificationId, notificationItem);
    }

    fetch(`/team-lead/salary-slips/${salarySlipId}/download`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': csrfToken
        }
    })
    .then(res => res.blob())
    .then(blob => {
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = "salary-slip.pdf"; // you can make dynamic filename
        document.body.appendChild(a);
        a.click();
        a.remove();
        window.URL.revokeObjectURL(url);
    });
}

// New function to mark notification as read
function markNotificationAsRead(notificationId, notificationElement) {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

    fetch(`/team-lead/notifications/${notificationId}/mark-read`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Content-Type': 'application/json',
        },
        credentials: 'same-origin',
    })
    .then(response => {
        if (!response.ok) throw new Error('Failed to mark as read');
        return response.json();
    })
    .then(data => {
        if (data.success) {
            // Update UI to show notification is read
            notificationElement.classList.remove('unread');
            notificationElement.classList.add('read');

            // Optional: Update unread count badge
            updateUnreadCount();
            window.location.reload();
        }
    })
    .catch(error => {
        console.error('Error marking notification as read:', error);
    });
}



// Optional: Function to update unread count badge
function updateUnreadCount() {
    fetch('/team-lead/notifications/count-unread')
    .then(response => response.json())
    .then(data => {
        // Update your badge element with data.count
        const badge = document.getElementById('notification-badge');
        if (badge) {
            badge.textContent = data.count > 0 ? data.count : '';
            badge.style.display = data.count > 0 ? 'block' : 'none';
        }
    });
}





            // Show task details in modal and mark notification as read
            function showTaskDetails(notificationId, taskId) {
                console.log('showTaskDetails called:', { notificationId, taskId });
                if (!notificationId || !taskId || isNaN(notificationId) || isNaN(taskId)) {
                    console.error('Invalid notificationId or taskId:', { notificationId, taskId });
                    alert('Invalid notification or task ID');
                    return;
                }
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                if (!csrfToken) {
                    console.error('CSRF token not found');
                    alert('CSRF token missing. Please refresh the page.');
                    return;
                }

                fetch(`/team-lead/notifications/${notificationId}/mark-read`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                    },
                    credentials: 'same-origin',
                    body: JSON.stringify({ id: notificationId }),
                })
                .then(response => {
                    console.log('Mark read response status:', response.status);
                    if (!response.ok) {
                        if (response.status === 422) {
                            return response.json().then(data => {
                                throw new Error(data.message || 'Validation failed');
                            });
                        }
                        throw new Error(`Failed to mark notification as read: ${response.status} ${response.statusText}`);
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('Mark read response:', data);
                    if (!data.success) {
                        throw new Error(`Failed to mark notification as read: ${data.message || 'Unknown error'}`);
                    }
                    // Fetch task details
                    fetch(`/team-lead/tasks/${taskId}`, {
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json',
                        },
                        credentials: 'same-origin',
                    })
                    .then(response => {
                        if (!response.ok) throw new Error(`Failed to fetch task details: ${response.status} ${response.statusText}`);
                        return response.json();
                    })
                    .then(task => {
                        console.log('Task details fetched:', task);
                        modalTaskTitle.textContent = task.title || 'N/A';
                        modalTaskDescription.textContent = task.description || 'N/A';
                        modalTaskAssignedDate.textContent = task.assigned_date || 'N/A';
                        modalTaskDueDate.textContent = task.due_date || 'N/A';
                        modalTaskPriority.textContent = task.priority || 'N/A';

                        const userTask = task.assigned_users?.find(u => u.id === parseInt(localStorage.getItem('userId') || 0)) || task.assigned_users?.[0];
                        modalTaskStatus.textContent = userTask?.pivot?.status || 'pending';
                        modalTaskProgress.textContent = userTask?.pivot?.progress || 0;
                        taskProgress.value = userTask?.pivot?.progress || 0;
                        progressValue.textContent = `${userTask?.pivot?.progress || 0}%`;
                        taskModal.dataset.taskId = taskId;
                        taskStatus.value = userTask?.pivot?.status || 'pending';
                        taskMessage.value = userTask?.pivot?.message || '';
                        taskModal.style.display = 'flex';


            attachmentList.innerHTML = '';
            const attachments = Array.isArray(task.attachments)
                ? task.attachments
                : typeof task.attachments === 'string'
                ? [task.attachments]
                : [];

            attachments.forEach(file => {
                const li = document.createElement('li');
                const link = document.createElement('a');
                link.href = `/storage/${file.replace(/^\/?storage\/?/, '')}`;
                link.target = '_blank';
                link.textContent = file.split('/').pop() || 'Unnamed File';
                li.appendChild(link);
                attachmentList.appendChild(li);
            });
                        fetchNotifications(); // Refresh notifications
                    })
                    .catch(error => {
                        console.error('Error fetching task details:', error);
                        alert('Failed to load task details. Please try again.');
                    });
                })
                .catch(error => {
                    console.error('Error marking notification as read:', error);
                    alert(`Failed to mark notification as read: ${error.message}`);
                });
            }

            // Update task status in backend
            function updateTaskStatus(taskId, status, message, progress) {
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                if (!csrfToken) {
                    console.error('CSRF token not found');
                    alert('CSRF token missing. Please refresh the page.');
                    return;
                }

                fetch(`/team-lead/tasks/${taskId}/update-status`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                    },
                    credentials: 'same-origin',
                    body: JSON.stringify({ status, message, progress }),
                })
                .then(response => {
                    if (!response.ok) throw new Error(`Failed to update task status: ${response.status} ${response.statusText}`);
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        alert('Task status updated successfully');
                        taskModal.style.display = 'none';
                        fetchNotifications(); // Refresh notifications
                    } else {
                        alert(`Failed to update task status: ${data.message || 'Unknown error'}`);
                    }
                })
                .catch(error => {
                    console.error('Error updating task status:', error);
                    alert('Failed to update task status. Please try again.');
                });
            }
        });
    </script>
</body>
</html>
