<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Send Notifications</title>

    <!-- ✅ Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Added Google Fonts and Font Awesome for professional typography and icons -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

    <style>
        /* Added CSS custom properties for consistent design system */
        :root {
            --primary-color: #2563eb;
            --primary-hover: #1d4ed8;
            --success-color: #059669;
            --success-hover: #047857;
            --secondary-color: #64748b;
            --danger-color: #dc2626;
            --warning-color: #d97706;
            --background-color: #f8fafc;
            --surface-color: #ffffff;
            --text-primary: #1e293b;
            --text-secondary: #64748b;
            --border-color: #e2e8f0;
            --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
            --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
            --shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
            --radius: 12px;
            --radius-sm: 8px;
        }

        * {
            box-sizing: border-box;
        }

        body {
            /* Enhanced background with subtle gradient and professional typography */
            background: linear-gradient(135deg, var(--background-color) 0%, #f1f5f9 100%);
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            color: var(--text-primary);
            line-height: 1.6;
            min-height: 100vh;
        }

        .container {
            /* Enhanced container with modern styling and better spacing */
            margin-top: 60px;
            max-width: 700px;
            background: var(--surface-color);
            padding: 40px;
            border-radius: var(--radius);
            box-shadow: var(--shadow-lg);
            border: 1px solid var(--border-color);
            position: relative;
            overflow: hidden;
        }

        .container::before {
            /* Added subtle gradient overlay for visual depth */
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--primary-color), var(--success-color));
        }

        h2 {
            /* Enhanced heading typography with icon */
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 32px;
            font-size: 28px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        h2::before {
            content: '\f0f3';
            font-family: 'Font Awesome 6 Free';
            font-weight: 900;
            color: var(--primary-color);
            font-size: 24px;
        }

        .form-label {
            /* Enhanced form labels with better typography */
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 8px;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .form-select {
            /* Enhanced select styling with modern appearance */
            border: 2px solid var(--border-color);
            border-radius: var(--radius-sm);
            padding: 12px 16px;
            font-size: 16px;
            transition: all 0.2s ease;
            background-color: var(--surface-color);
            color: var(--text-primary);
        }

        .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgb(37 99 235 / 0.1);
            outline: none;
        }

        .btn {
            /* Enhanced button styling with modern design and icons */
            border-radius: var(--radius-sm);
            padding: 12px 24px;
            font-weight: 600;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            transition: all 0.2s ease;
            border: none;
            position: relative;
            overflow: hidden;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: left 0.5s;
        }

        .btn:hover::before {
            left: 100%;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary-color), var(--primary-hover));
            color: white;
            box-shadow: var(--shadow-sm);
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, var(--primary-hover), #1e40af);
            transform: translateY(-1px);
            box-shadow: var(--shadow-md);
        }

        .btn-success {
            background: linear-gradient(135deg, var(--success-color), var(--success-hover));
            color: white;
            box-shadow: var(--shadow-sm);
        }

        .btn-success:hover {
            background: linear-gradient(135deg, var(--success-hover), #065f46);
            transform: translateY(-1px);
            box-shadow: var(--shadow-md);
        }

        .btn-secondary {
            background: linear-gradient(135deg, var(--secondary-color), #475569);
            color: white;
        }

        .btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none !important;
        }

        #fetchHolidayBtn::before {
            content: '\f073';
            font-family: 'Font Awesome 6 Free';
            font-weight: 900;
        }

        #fetchBirthdayBtn::before {
            content: '\f1fd';
            font-family: 'Font Awesome 6 Free';
            font-weight: 900;
        }

        #sendHolidayNotificationBtn::before {
            content: '\f1d8';
            font-family: 'Font Awesome 6 Free';
            font-weight: 900;
        }

        #holidayResult, #birthdayResult {
            /* Enhanced result sections with better styling */
            padding: 20px;
            border-radius: var(--radius-sm);
            background: linear-gradient(135deg, #f8fafc, #f1f5f9);
            border: 1px solid var(--border-color);
            margin: 16px 0;
            min-height: 60px;
            display: flex;
            align-items: center;
            font-weight: 500;
        }

        #holidayResult {
            color: var(--primary-color);
        }

        .list-group {
            /* Enhanced list group styling */
            border-radius: var(--radius-sm);
            overflow: hidden;
            box-shadow: var(--shadow-sm);
        }

        .list-group-item {
            /* Enhanced list items with better spacing and hover effects */
            border: none;
            border-bottom: 1px solid var(--border-color);
            padding: 20px;
            transition: all 0.2s ease;
            background: var(--surface-color);
        }

        .list-group-item:hover {
            background: #f8fafc;
            transform: translateX(4px);
        }

        .list-group-item:last-child {
            border-bottom: none;
        }

        .notification-section {
            /* Added styling for notification sections */
            background: #f8fafc;
            border: 1px solid var(--border-color);
            border-radius: var(--radius-sm);
            padding: 24px;
            margin: 20px 0;
            transition: all 0.3s ease;
        }

        .notification-section.active {
            background: var(--surface-color);
            box-shadow: var(--shadow-md);
            border-color: var(--primary-color);
        }

        /* Added loading animation for buttons */
        .btn-loading {
            position: relative;
            color: transparent !important;
        }

        .btn-loading::after {
            content: '';
            position: absolute;
            width: 16px;
            height: 16px;
            top: 50%;
            left: 50%;
            margin-left: -8px;
            margin-top: -8px;
            border: 2px solid transparent;
            border-top-color: currentColor;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Added responsive design improvements */
        @media (max-width: 768px) {
            .container {
                margin: 20px;
                padding: 24px;
                max-width: none;
            }

            h2 {
                font-size: 24px;
            }

            .btn {
                padding: 10px 20px;
                font-size: 13px;
            }
        }
    </style>
</head>
<body>
 @include('hr.Components.sidebar')
<div class="container">
     @include('hr.Components.header')
    <h2 class="mb-4">Send Notifications</h2>

    <!-- Notification Type Dropdown -->
    <div class="mb-4">
        <label for="notificationType" class="form-label">Notification Type</label>
        <select id="notificationType" class="form-select">
            <option value="" selected disabled>-- Select Type --</option>
            <option value="holiday">Holiday</option>
            <option value="birthday">Birthday</option>
        </select>
    </div>

    <!-- Holiday Section -->
    <!-- Added notification-section class for better styling -->
    <div id="holidaySection" class="notification-section d-none">
        <button id="fetchHolidayBtn" class="btn btn-primary mb-3">Fetch Today's Holiday</button>
        <div id="holidayResult" class="mb-3 text-info fw-bold"></div>
        <button id="sendHolidayNotificationBtn" class="btn btn-success d-none">Send Notification</button>
    </div>

    <!-- Birthday Section -->
    <!-- Added notification-section class for better styling -->
    <div id="birthdaySection" class="notification-section d-none">
        <button id="fetchBirthdayBtn" class="btn btn-primary mb-3">Fetch Today's Birthdays</button>
        <div id="birthdayResult" class="mb-3"></div>
    </div>
</div>

<!-- ✅ Script -->
<script>
    document.getElementById('notificationType').addEventListener('change', function () {
        let type = this.value;

        // Show/hide based on selected type
        const holidaySection = document.getElementById('holidaySection');
        const birthdaySection = document.getElementById('birthdaySection');

        holidaySection.classList.toggle('d-none', type !== 'holiday');
        birthdaySection.classList.toggle('d-none', type !== 'birthday');

        // Add active class for better visual feedback
        holidaySection.classList.toggle('active', type === 'holiday');
        birthdaySection.classList.toggle('active', type === 'birthday');

        // Clear results when switching
        document.getElementById('holidayResult').innerHTML = "";
        document.getElementById('birthdayResult').innerHTML = "";
        document.getElementById('sendHolidayNotificationBtn').classList.add('d-none');
    });

    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute("content");

    document.getElementById('fetchHolidayBtn').addEventListener('click', function () {
        // Add loading state
        this.classList.add('btn-loading');
        this.disabled = true;

        fetch("{{ route('hr.holiday.notification.fetch') }}", {
            method: "POST",
            headers: {
                 "X-CSRF-TOKEN": csrfToken,
                "Content-Type": "application/json",
            },
            body: JSON.stringify({})
        })
        .then(response => response.json())
        .then(data => {
            let resultDiv = document.getElementById('holidayResult');
            let sendBtn = document.getElementById('sendHolidayNotificationBtn');

            if (data.status) {
                resultDiv.innerHTML = "Today's Holiday: <b>" + data.holiday + "</b>";
                sendBtn.classList.remove('d-none');
                sendBtn.disabled = data.already_sent;
                sendBtn.innerText = data.already_sent ? "Sent" : "Send Notification";
            } else {
                resultDiv.innerHTML = data.message;
                sendBtn.classList.add('d-none');
            }
        })
        .finally(() => {
            // Remove loading state
            this.classList.remove('btn-loading');
            this.disabled = false;
        });
    });

    document.getElementById('sendHolidayNotificationBtn').addEventListener('click', function () {
        this.classList.add('btn-loading');
        this.disabled = true;

        fetch("{{ route('hr.holiday.notification.send') }}", {
            method: "POST",
            headers: {
                 "X-CSRF-TOKEN": csrfToken,
                "Content-Type": "application/json",
            },
            body: JSON.stringify({})
        })
        .then(response => response.json())
        .then(data => {
            alert(data.message);
            if (data.status) {
                let sendBtn = document.getElementById('sendHolidayNotificationBtn');
                sendBtn.disabled = true;
                sendBtn.innerText = "Sent";
                sendBtn.classList.remove('btn-success');
                sendBtn.classList.add('btn-secondary');
            }
        })
        .finally(() => {
            this.classList.remove('btn-loading');
        });
    });

    document.getElementById('fetchBirthdayBtn').addEventListener('click', function () {
        // Add loading state
        this.classList.add('btn-loading');
        this.disabled = true;

        fetch("{{ route('hr.birthday.notification.fetch') }}", {
            method: "POST",
            headers: {
                "X-CSRF-TOKEN": "{{ csrf_token() }}",
                "Content-Type": "application/json",
            },
            body: JSON.stringify({})
        })
        .then(response => response.json())
        .then(data => {
            let resultDiv = document.getElementById('birthdayResult');
            resultDiv.innerHTML = "";

            if (data.status) {
                if (data.users.length === 0) {
                    resultDiv.innerHTML = "<p>No birthdays today.</p>";
                } else {
                    let list = "<ul class='list-group'>";
                    data.users.forEach(user => {
                        list += `
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <strong>${user.name}</strong>
                                    <br>
                                    <small class="text-muted">${user.email}</small>
                                </div>
                                <button class="btn btn-sm ${user.already_sent ? 'btn-secondary' : 'btn-success'}"
                                    ${user.already_sent ? 'disabled' : ''}
                                    onclick="sendBirthdayNotification(${user.id}, this)">
                                    <i class="fas ${user.already_sent ? 'fa-check' : 'fa-paper-plane'}"></i>
                                    ${user.already_sent ? 'Sent' : 'Send'}
                                </button>
                            </li>
                        `;
                    });
                    list += "</ul>";
                    resultDiv.innerHTML = list;
                }
            } else {
                resultDiv.innerHTML = `<p>${data.message}</p>`;
            }
        })
        .finally(() => {
            // Remove loading state
            this.classList.remove('btn-loading');
            this.disabled = false;
        });
    });

   function sendBirthdayNotification(userId, btn) {
    btn.classList.add('btn-loading');
    btn.disabled = true;

    fetch("{{ route('hr.birthday.notification.send') }}", {
        method: "POST",
        headers: {
            "X-CSRF-TOKEN": csrfToken,
            "Content-Type": "application/json",
        },
        body: JSON.stringify({ user_id: userId })
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(data => {
        alert(data.message);
        if (data.status) {
            btn.disabled = true;
            btn.classList.remove('btn-success');
            btn.classList.add('btn-secondary');
            btn.innerHTML = '<i class="fas fa-check"></i> Sent';
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Failed to send notification');
        btn.disabled = false;
    })
    .finally(() => {
        btn.classList.remove('btn-loading');
    });
}
</script>

<!-- ✅ Bootstrap JS (optional, for dropdowns/buttons animations) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
