<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Send Notification</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8fafc; font-family: 'Inter', sans-serif; }
        .main-container { margin-left: 280px; padding: 100px 30px; }
        .card-box { background: white; border-radius: 12px; padding: 25px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); }
        .section-title { font-weight: 700; color: #4b5563; border-bottom: 1px solid #e5e7eb; padding-bottom: 10px; margin-bottom: 20px; }
        
        /* User List Styling */
        .user-list-container {
            max-height: 300px;
            overflow-y: auto;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 10px;
            background: #f9fafb;
        }
        .user-item {
            padding: 8px;
            border-bottom: 1px solid #eee;
            display: flex;
            align-items: center;
        }
        .user-item:last-child { border-bottom: none; }
        .user-role-badge {
            font-size: 0.75rem;
            padding: 2px 8px;
            border-radius: 10px;
            background: #e0e7ff;
            color: #4338ca;
            margin-left: 10px;
        }
    </style>
</head>
<body>
    @include('hr.Components.sidebar')
    @include('hr.Components.header')

    <div class="main-container">
        <h3 class="mb-4 fw-bold text-dark">Send Notification</h3>

        <div class="row">
            <div class="col-md-8 mx-auto">
                <div class="card-box">
                    <div class="section-title">Notification Details</div>

                    <form id="notificationForm">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label fw-bold">Select Type</label>
                            <select name="type" id="type" class="form-select">
                                <option value="">Select</option>
                                <option value="holiday">Today's Holiday</option>
                                <option value="others">Others (Custom Message)</option>
                            </select>
                        </div>

                        <div id="othersSection" style="display: none;">
                            
                            <div class="mb-3">
                                <label class="form-label fw-bold">Message</label>
                                <textarea name="message" id="message" class="form-control" rows="3" placeholder="Enter your message here..."></textarea>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Target Audience</label>
                                <div class="d-flex gap-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="target" id="targetAll" value="all" checked>
                                        <label class="form-check-label" for="targetAll">Send to All</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="target" id="targetIndividual" value="individual">
                                        <label class="form-check-label" for="targetIndividual">Individual / Specific Users</label>
                                    </div>
                                </div>
                            </div>

                            <div id="individualUserList" class="mb-3" style="display: none;">
                                <label class="form-label fw-bold">Select Users</label>
                                <input type="text" id="userSearch" class="form-control mb-2 form-control-sm" placeholder="Search by name or role...">
                                
                                <div class="user-list-container">
                                    <div class="form-check mb-2 pb-2 border-bottom">
                                        <input class="form-check-input" type="checkbox" id="selectAllUsers">
                                        <label class="form-check-label fw-bold" for="selectAllUsers">Select All Listed</label>
                                    </div>

                                    @foreach($users as $user)
                                    <div class="user-item">
                                        <div class="form-check w-100">
                                            <input class="form-check-input user-checkbox" type="checkbox" name="user_ids[]" value="{{ $user->id }}" id="user_{{ $user->id }}">
                                            <label class="form-check-label w-100" for="user_{{ $user->id }}">
                                                {{ $user->name }}
                                                <span class="user-role-badge">{{ ucfirst(str_replace('_', ' ', $user->designation)) }}</span>
                                            </label>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                                <div class="form-text">Selected: <span id="selectedCount">0</span> users</div>
                            </div>
                        </div>

                        <div id="holidayInfo" class="alert alert-info mt-3" style="display: none;">
                            <i class="fas fa-info-circle me-1"></i> Checking for today's holiday...
                        </div>

                        <div class="mt-4 text-end">
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="fas fa-paper-plane me-1"></i> Send Notification
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const typeSelect = document.getElementById('type');
            const othersSection = document.getElementById('othersSection');
            const holidayInfo = document.getElementById('holidayInfo');
            const targetRadios = document.getElementsByName('target');
            const individualUserList = document.getElementById('individualUserList');
            const userSearch = document.getElementById('userSearch');
            const selectAllUsers = document.getElementById('selectAllUsers');
            const userCheckboxes = document.querySelectorAll('.user-checkbox');
            const selectedCountSpan = document.getElementById('selectedCount');

            // 1. Toggle Sections based on Type
            typeSelect.addEventListener('change', function() {
                if (this.value === 'others') {
                    othersSection.style.display = 'block';
                    holidayInfo.style.display = 'none';
                } else if (this.value === 'holiday') {
                    othersSection.style.display = 'none';
                    holidayInfo.style.display = 'block';
                    checkHoliday();
                } else {
                    othersSection.style.display = 'none';
                    holidayInfo.style.display = 'none';
                }
            });

            // Initial Check
            if (typeSelect.value === 'holiday') checkHoliday();

            // 2. Toggle User List based on Target
            targetRadios.forEach(radio => {
                radio.addEventListener('change', function() {
                    if (this.value === 'individual') {
                        individualUserList.style.display = 'block';
                    } else {
                        individualUserList.style.display = 'none';
                    }
                });
            });

            // 3. Search Filter
            userSearch.addEventListener('keyup', function() {
                const value = this.value.toLowerCase();
                const items = document.querySelectorAll('.user-item');
                items.forEach(item => {
                    const text = item.textContent.toLowerCase();
                    item.style.display = text.includes(value) ? 'flex' : 'none';
                });
            });

            // 4. Select All Logic
            selectAllUsers.addEventListener('change', function() {
                const visibleCheckboxes = Array.from(document.querySelectorAll('.user-item'))
                    .filter(item => item.style.display !== 'none')
                    .map(item => item.querySelector('.user-checkbox'));
                
                visibleCheckboxes.forEach(cb => cb.checked = this.checked);
                updateCount();
            });

            // 5. Update Count
            userCheckboxes.forEach(cb => {
                cb.addEventListener('change', updateCount);
            });

            function updateCount() {
                const count = document.querySelectorAll('.user-checkbox:checked').length;
                selectedCountSpan.textContent = count;
            }

            // 6. Check Holiday AJAX
            function checkHoliday() {
                holidayInfo.innerHTML = '<div class="spinner-border spinner-border-sm"></div> Checking...';
               fetch("{{ route('hr.holiday.notification.fetch') }}", {
    method: 'POST',
    headers: {
        'Accept': 'application/json',
        'X-CSRF-TOKEN': "{{ csrf_token() }}"
    }
})
                .then(res => res.json())
                .then(data => {
                    if (data.status) {
                        let msg = `<strong>Today is ${data.holiday}</strong>. `;
                        if (data.already_sent) msg += '<span class="text-danger">(Notification already sent)</span>';
                        holidayInfo.innerHTML = msg;
                        holidayInfo.className = 'alert alert-success mt-3';
                    } else {
                        holidayInfo.innerHTML = 'No holiday found for today.';
                        holidayInfo.className = 'alert alert-warning mt-3';
                    }
                });
            }

            // 7. Form Submit
            document.getElementById('notificationForm').addEventListener('submit', function(e) {
                e.preventDefault();
                
                // Basic Validation for Individual
                if (typeSelect.value === 'others' && 
                    document.getElementById('targetIndividual').checked && 
                    document.querySelectorAll('.user-checkbox:checked').length === 0) {
                    alert('Please select at least one user.');
                    return;
                }

                const formData = new FormData(this);
                const submitBtn = this.querySelector('button[type="submit"]');
                const originalText = submitBtn.innerHTML;
                submitBtn.innerHTML = 'Sending...';
                submitBtn.disabled = true;

                fetch("{{ route('hr.holiday.notification.send') }}", {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': "{{ csrf_token() }}" },
                    body: formData
                })
                .then(res => res.json())
                .then(data => {
                    alert(data.message);
                    if(data.status) window.location.reload();
                })
                .catch(err => {
                    console.error(err);
                    alert('Something went wrong.');
                })
                .finally(() => {
                    submitBtn.innerHTML = originalText;
                    submitBtn.disabled = false;
                });
            });
        });
    </script>
</body>
</html>