<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Lead Details</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');
        :root {
            --primary-500: #3b82f6;
            --primary-600: #2563eb;
            --gray-200: #e5e7eb;
        }
        body {
            background: #f8fafc;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            color: #334155;
            margin: 0;
            padding: 0;
            min-height: 100vh;
        }
        .main-content {
            margin: 0 auto;
            max-width: 1200px;
            padding: 32px;
            background-color: #ffffff;
            border-radius: 16px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            margin-top: 5rem;
            margin-left:22rem;
            position: relative;
            display: flex;
            flex-direction: column;
            min-height: calc(100vh - 40px);
        }
        .lead-detail-container {
            background: #ffffff;
            border-radius: 16px;
            border: 1px solid var(--gray-200, #e5e7eb);
            padding: 24px;
            /* margin-left:12rem; */
        }
        .lead-detail-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-bottom: 1px solid #e2e8f0;
            padding: 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-radius: 16px 16px 0 0;
            margin: -24px -24px 24px -24px;
        }
        .lead-detail-title {
            font-size: 22px;
            font-weight: 700;
            color: #ffffff;
        }
        .lead-detail-close {
            background: rgba(255, 255, 255, 0.2);
            border: none;
            font-size: 1.5rem;
            color: #ffffff;
            cursor: pointer;
            transition: all 0.2s ease;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .lead-detail-close:hover {
            background: rgba(255, 255, 255, 0.3);
        }
        .lead-detail-grid {
            display: grid;
            grid-template-columns: 1fr 2fr;
            gap: 24px;
        }
        .lead-detail-left {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 16px;
        }
        .lead-avatar-large {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            font-weight: 600;
            box-shadow: 0 8px 32px rgba(102, 126, 234, 0.3);
        }
        .lead-basic-info {
            text-align: center;
        }
        .lead-basic-info h3 {
            font-size: 1.5rem;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 8px;
        }
        .lead-contact {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }
        .contact-item {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 0.9rem;
            color: #475569;
            background: #f8fafc;
            padding: 8px 12px;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
        }
        .contact-item i {
            color: #3b82f6;
        }
        .editable-field {
            border: none;
            background: transparent;
            padding: 0;
            width: 100%;
            color: #475569;
            font-size: 0.9rem;
        }
        .editable-field:focus {
            outline: none;
        }
        .lead-detail-right {
            display: flex;
            flex-direction: column;
            gap: 24px;
        }
        .detail-section {
            display: flex;
            flex-direction: column;
            gap: 16px;
        }
        .detail-section h4 {
            font-size: 1.25rem;
            font-weight: 600;
            color: #1e293b;
            border-left: 4px solid #3b82f6;
            padding-left: 12px;
        }
        .detail-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }
        .detail-item {
            display: flex;
            flex-direction: column;
            gap: 4px;
            background: #f8fafc;
            padding: 12px;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
        }
        .detail-item label {
            font-size: 0.75rem;
            font-weight: 600;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        .detail-item input,
        .detail-item select,
        .detail-item span {
            font-size: 0.9rem;
            color: #1e293b;
            font-weight: 500;
            border: none;
            background: transparent;
            padding: 0;
        }
        .detail-item input:focus,
        .detail-item select:focus {
            outline: none;
        }
        .status {
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: #fbbf24;
            color: #92400e;
        }
        .remarks-box {
            background: #f8fafc;
            padding: 16px;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
        }
        .remarks-box p {
            font-size: 0.9rem;
            color: #475569;
        }
        .document-list {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }
        .document-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px;
            background: #f8fafc;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
        }
        .document-item a {
            color: #3b82f6;
            text-decoration: none;
        }
        .document-item a:hover {
            text-decoration: underline;
        }
        .lead-detail-footer {
            padding: 16px 0;
            display: flex;
            gap: 12px;
            justify-content: flex-end; /* Changed to flex-end */
            background-color: #ffffff;
            border-top: 1px solid var(--gray-200, #e5e7eb);
            width: 100%;
            box-shadow: 0 -4px 12px rgba(0, 0, 0, 0.05);
            margin-top: auto;
        }
        .lead-detail-content {
            max-height: calc(100vh - 300px);
            overflow-y: auto;
            padding-bottom: 20px;
        }
        .btn-primary {
            background: #3b82f6;
            border: none;
            color: white;
            font-weight: 600;
            border-radius: 8px;
            padding: 10px 20px;
            transition: all 0.2s ease;
            box-shadow: 0 2px 8px rgba(59, 130, 246, 0.2);
            display: inline-flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
        }
        .btn-primary:hover:not(:disabled) {
            background: #2563eb;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
        }
        .btn-primary:disabled {
            opacity: 0.5;
            cursor: not-allowed;
            background: #93c5fd; /* Lighter shade for faded effect */
        }
        /* Compact styling for Add Document button */
        #addDocumentButton {
            width: auto !important;
            min-width: auto !important;
            max-width: 180px;
            padding: 8px 16px !important;
            font-size: 0.875rem !important;
            white-space: nowrap;
            align-self: flex-start; /* Prevent button from stretching in flex container */
            margin-bottom: 8px; /* Add some space below the button */
        }
        .btn-success {
            background: #10b981;
            border: none;
            color: white;
            font-weight: 600;
            border-radius: 8px;
            padding: 10px 20px;
            transition: all 0.2s ease;
            box-shadow: 0 2px 8px rgba(16, 185, 129, 0.2);
            display: inline-flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
        }
        .btn-success:hover:not(:disabled) {
            background: #059669;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
        }
        .btn-success:disabled {
            opacity: 0.5;
            cursor: not-allowed;
            background: #6ee7b7; /* Lighter shade for faded effect */
        }
        .btn-danger {
            background: #ef4444;
            border: none;
            color: white;
            font-weight: 600;
            border-radius: 8px;
            padding: 10px 20px;
            transition: all 0.2s ease;
            box-shadow: 0 2px 8px rgba(239, 68, 68, 0.2);
            display: inline-flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
        }
        .btn-danger:hover:not(:disabled) {
            background: #dc2626;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);
        }
        .btn-danger:disabled {
            opacity: 0.5;
            cursor: not-allowed;
            background: #f87171; /* Lighter shade for faded effect */
        }
        .btn-authorize {
            background: #8b5cf6;
            border: none;
            color: white;
            font-weight: 600;
            border-radius: 8px;
            padding: 10px 20px;
            transition: all 0.2s ease;
            box-shadow: 0 2px 8px rgba(139, 92, 246, 0.2);
            display: inline-flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
        }
        .btn-authorize:hover:not(:disabled) {
            background: #7c3aed;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(139, 92, 246, 0.3);
        }
        .btn-authorize:disabled {
            opacity: 0.5;
            cursor: not-allowed;
            background: #a78bfa; /* Lighter shade for faded effect */
        }
        .btn-future {
            background: #f59e0b;
            border: none;
            color: white;
            font-weight: 600;
            border-radius: 8px;
            padding: 10px 20px;
            transition: all 0.2s ease;
            box-shadow: 0 2px 8px rgba(245, 158, 11, 0.2);
            display: inline-flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
        }
        .btn-future:hover:not(:disabled) {
            background: #d97706;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(245, 158, 11, 0.3);
        }
        .btn-future:disabled {
            opacity: 0.5;
            cursor: not-allowed;
            background: #f5b041; /* Lighter shade for faded effect */
        }
        .btn-secondary {
            background: #64748b;
            border: none;
            color: white;
            font-weight: 600;
            border-radius: 8px;
            padding: 10px 20px;
            transition: all 0.2s ease;
            box-shadow: 0 2px 8px rgba(100, 116, 139, 0.2);
            display: inline-flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
        }
        .btn-secondary:hover:not(:disabled) {
            background: #475569;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(100, 116, 139, 0.3);
        }
        .btn-secondary:disabled {
            opacity: 0.5;
            cursor: not-allowed;
            background: #94a3b8; /* Lighter shade for faded effect */
        }
        .btn-disburse {
            background: #059669;
            border: none;
            color: white;
            font-weight: 600;
            border-radius: 8px;
            padding: 10px 20px;
            transition: all 0.2s ease;
            box-shadow: 0 2px 8px rgba(5, 150, 105, 0.2);
            display: inline-flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
        }
        .btn-disburse:hover:not(:disabled) {
            background: #047857;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(5, 150, 105, 0.3);
        }
        .btn-disburse:disabled {
            opacity: 0.5;
            cursor: not-allowed;
            background: #34d399; /* Lighter shade for faded effect */
        }
        .status.personal_lead { background-color: #fbbf24; color: #92400e; }
        .status.authorized { background-color: #8b5cf6; color: #ffffff; }
        .status.login { background-color: #3b82f6; color: #ffffff; }
        .status.approved { background-color: #06b6d4; color: #ffffff; }
        .status.rejected { background-color: #ef4444; color: #ffffff; }
        .status.disbursed { background-color: #10b981; color: #ffffff; }
        .status.future_lead { background-color: #f59e0b; color: #ffffff; }
        @media (max-width: 768px) {
            .main-content {
                margin-left: 0;
                padding: 20px;
            }
            .lead-detail-grid {
                grid-template-columns: 1fr;
            }
            .lead-detail-title {
                font-size: 20px;
            }
            .lead-detail-footer {
                justify-content: flex-end; /* Maintain flex-end for consistency */
            }
        }

        /* Follow-Up Section Styles */
        .followup-section {
            margin-top: 24px;
            padding: 16px;
            background: var(--gray-50, #f9fafb);
            border-radius: 12px;
            border: 1px solid var(--gray-200, #e5e7eb);
        }

        .followup-section h4 {
            font-size: 18px;
            font-weight: 700;
            color: var(--gray-900, #111827);
            margin-bottom: 16px;
            position: relative;
            padding-left: 16px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .followup-section h4::before {
            content: '';
            position: absolute;
            left: 0;
            top: 50%;
            transform: translateY(-50%);
            width: 4px;
            height: 20px;
            background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
            border-radius: 2px;
        }

        .followup-section h4 i {
            color: #8b5cf6;
            font-size: 16px;
        }

        .followup-item {
            margin-bottom: 16px;
            padding: 16px;
            background: white;
            border-radius: 12px;
            border: 1px solid var(--gray-200, #e5e7eb);
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
            transition: all 0.2s ease;
        }

        .followup-item:hover {
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            transform: translateY(-1px);
        }

        .followup-item:last-child {
            margin-bottom: 0;
            border-bottom: none;
        }

        .followup-message {
            color: var(--gray-700, #374151);
            font-size: 14px;
            line-height: 1.5;
            margin: 0 0 12px 0;
            padding: 0;
        }

        .followup-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 12px;
            color: var(--gray-500, #6b7280);
            margin-top: 8px;
        }

        .followup-user {
            font-weight: 600;
            color: var(--gray-700, #374151);
        }

        .followup-date {
            color: var(--gray-500, #6b7280);
        }

        .followup-audio {
            width: 100%;
            margin-top: 12px;
            border-radius: 8px;
            background: var(--gray-100, #f3f4f6);
        }

        .followup-audio::-webkit-media-controls-panel {
            background: var(--gray-100, #f3f4f6);
        }

        .no-followups {
            text-align: center;
            padding: 24px;
            color: var(--gray-500, #6b7280);
            font-style: italic;
        }

        .no-followups i {
            font-size: 24px;
            margin-bottom: 8px;
            color: var(--gray-400, #9ca3af);
            display: block;
        }

        /* Animation for new follow-up items */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .followup-item {
            animation: fadeInUp 0.3s ease-out;
        }

    </style>
<body>
      @include('Opearation.Components.sidebar')
    <div class="main-content">
         @include('Opearation.Components.header')
        <div class="lead-detail-container">
            <div class="lead-detail-header">
                <h2 class="lead-detail-title">Lead Details</h2>
               <button class="lead-detail-close" onclick="goBackAndReload()">
    <i class="fas fa-times"></i>
</button>


            </div>
            <div class="lead-detail-content">
                <div class="lead-detail-grid">
                    <div class="lead-detail-left">
                        <div class="lead-avatar-large" id="leadInitials"></div>
                        <div class="lead-basic-info">
                            <h3 id="leadName"></h3>
                            <div class="lead-contact">
                                <div class="contact-item">
                                    <i class="fas fa-phone"></i>
                                    <input type="text" id="leadPhone" class="editable-field" disabled/>
                                </div>
                                <div class="contact-item">
                                    <i class="fas fa-envelope"></i>
                                    <input type="email" id="leadEmail" class="editable-field" disabled/>
                                </div>
                                <div class="contact-item">
                                    <i class="fas fa-university"></i>
                                    <input type="text" id="leadBank" class="editable-field" disabled>
                                </div>

                                <div class="followup-section">
                                    <h4>
                                        <i class="fas fa-history"></i>
                                        Follow-Up History
                                    </h4>
                                    <div id="followupList">
                                        <div class="no-followups">
                                            <i class="fas fa-clock"></i>
                                            <p>Loading follow-ups...</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="lead-detail-right">
                        <div class="detail-section">
                            <h4>Lead Information</h4>
                            <div class="detail-grid">
                                <div class="detail-item">
                                    <label>NAME</label>
                                    <input type="text" id="leadNameInput" class="editable-field" disabled>
                                </div>
                                <div class="detail-item">
                                    <label>DATE OF BIRTH</label>
                                    <input type="date" id="leadDob" class="editable-field" disabled>
                                </div>
                                <div class="detail-item">
                                    <label>STATE</label>
                                    <input type="text" id="leadState" class="editable-field" disabled>
                                    <select id="leadStateDropdown" class="editable-field" style="display: none;" onchange="loadDistricts(this.value)">
                                        <option value="">Select State</option>
                                    </select>
                                </div>
                                <div class="detail-item">
                                    <label>DISTRICT</label>
                                    <input type="text" id="leadDistrict" class="editable-field" disabled>
                                    <select id="leadDistrictDropdown" class="editable-field" style="display: none;" onchange="loadCities(this.value)">
                                        <option value="">Select District</option>
                                    </select>
                                </div>
                                <div class="detail-item">
                                    <label>CITY</label>
                                    <input type="text" id="leadCity" class="editable-field" disabled>
                                    <select id="leadCityDropdown" class="editable-field" style="display: none;">
                                        <option value="">Select City</option>
                                    </select>
                                </div>
                                <div class="detail-item">
                                    <label>LEAD AMOUNT</label>
                                    <input type="number" id="leadAmount" class="editable-field" disabled>
                                </div>
                                <div class="detail-item">
                                    <label>Amount in Words</label>
                                    <span id="leadAmountInWords"></span>
                                </div>
                                <div class="detail-item">
                                    <label>COMPANY</label>
                                    <input type="text" id="leadCompany" class="editable-field" disabled>
                                </div>
                                <div class="detail-item">
                                    <label>STATUS</label>
                                    <span id="leadStatus" class="status"></span>
                                </div>
                                {{-- <div class="detail-item">
                                    <label>Expected Month</label>
                                    <select id="leadExpectedMonth" class="editable-field" disabled>
                                        <option value="">Select Month</option>
                                        <option value="January">January</option>
                                        <option value="February">February</option>
                                        <option value="March">March</option>
                                        <option value="April">April</option>
                                        <option value="May">May</option>
                                        <option value="June">June</option>
                                        <option value="July">July</option>
                                        <option value="August">August</option>
                                        <option value="September">September</option>
                                        <option value="October">October</option>
                                        <option value="November">November</option>
                                        <option value="December">December</option>
                                    </select>
                                </div> --}}
                                <div class="detail-item">
                                    <label>LEAD TYPE</label>
                                    <span id="leadTypeDisplay" class="lead-type-display"></span>
                                    <select id="leadType" class="editable-field" style="display: none;" disabled>
                                        <option value="">Select Lead Type</option>
                                        <option value="personal_loan">Personal Loan</option>
                                        <option value="business_loan">Business Loan</option>
                                        <option value="home_loan">Home Loan</option>
                                        <option value="creditcard_loan">Credit Card Loan</option>
                                    </select>
                                </div>
                                {{-- <div class="detail-item">
                                    <label>LOAN AC No</label>
                                    <input type="text" id="leadAccountNumber" class="editable-field" disabled>
                                </div> --}}
                                {{-- <div class="detail-item">
                                    <label>TURNOVER AMOUNT</label>
                                    <input type="number" id="leadTurnoverAmount" class="editable-field" disabled>
                                </div> --}}
                                <div class="detail-item">
                                    <label>SALARY</label>
                                    <input type="number" id="leadSalary" class="editable-field" disabled>
                                </div>
                                <div class="detail-item">
                                    <label>VOICE RECORDING</label>
                                    <span id="leadVoiceRecording"></span>
                                </div>
                            </div>
                        </div>
                        <div class="detail-section">
                            <h4>EMPLOYEE INFORMATION</h4>
                            <div class="detail-grid">
                                <div class="detail-item">
                                    <label>CREATED BY</label>
                                    <span id="leadEmployeeName"></span>
                                </div>
                                <div class="detail-item">
                                    <label>TEAM LEAD</label>
                                    <span id="leadTeamLeadName"></span>
                                </div>
                            </div>
                        </div>
                        <div class="detail-section">
                            <h4>Documents</h4>
                            <button class="btn-primary" id="addDocumentButton" onclick="openAddDocumentModal()">
                                <i class="fas fa-plus"></i> Add Document
                            </button>
                            <div id="documentList" class="document-list" style="margin-top: 16px;">
                                <p>Loading documents...</p>
                            </div>
                        </div>
                        <div class="detail-section" id="rejectionReasonSection" style="display: none;">
                            <h4>Rejection Reason</h4>
                            <div class="remarks-box">
                                <p id="leadReason"></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="lead-detail-footer">
            <button class="btn-primary" id="editLeadButton" onclick="enableLeadEdit()">
                <i class="fas fa-edit"></i> Edit
            </button>
            <button class="btn-success" id="saveLeadButton" style="display:none;" onclick="saveLeadChanges()">
                <i class="fas fa-save"></i> Save
            </button>
            @if(auth()->user()->hasDesignation('operations'))
                {{-- <button class="btn-primary" id="loginButton" onclick="showLoginModal(currentLeadId)">
                    <i class="fas fa-sign-in-alt"></i> Login
                </button>
                <button class="btn-authorize" id="authorizeButton" onclick="showAuthorizeModal(currentLeadId)">
                    <i class="fas fa-check-double"></i> Authorize
                </button> --}}
                <button class="btn-success" id="approveButton" onclick="showApproveModal(currentLeadId)">
                    <i class="fas fa-check-circle"></i> Approve
                </button>
                <button class="btn-danger" id="rejectButton" onclick="showRejectModal(currentLeadId)">
                    <i class="fas fa-times-circle"></i> Reject
                </button>
                {{-- <button class="btn-disburse" id="disburseButton" onclick="showDisburseModal(currentLeadId)">
                    <i class="fas fa-rupee-sign"></i> Disburse
                </button>
                <button class="btn-future" id="futureLeadButton" onclick="showFutureLeadModal(currentLeadId)">
                    <i class="fas fa-clock"></i> Mark as Future Lead
                </button> --}}
            @endif
        </div>

        <!-- Confirmation Modals -->
        <div class="modal-overlay" id="authorizeModal">
            <div class="modal-container modal-sm">
                <div class="modal-header">
                    <h2 class="modal-title">Authorize Lead</h2>
                    <button class="modal-close" onclick="closeModal('authorizeModal')">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="modal-content" style="max-height: 70vh; overflow-y: auto;">
                    <div class="confirm-message">
                        <div class="confirm-icon authorize">
                            <i class="fas fa-check-double"></i>
                        </div>
                        <h3>Authorize This Lead?</h3>
                        <p>This action will change the lead status to "Authorized".</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn-secondary" onclick="closeModal('authorizeModal')">Cancel</button>
                    <button class="btn-authorize" onclick="confirmAuthorize()">
                        <i class="fas fa-check"></i> Yes, Authorize
                    </button>
                </div>
            </div>
        </div>

        <div class="modal-overlay" id="loginModal">
            <div class="modal-container modal-sm">
                <div class="modal-header">
                    <h2 class="modal-title">Login Lead</h2>
                    <button class="modal-close" onclick="closeModal('loginModal')">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="modal-content" style="max-height: 70vh; overflow-y: auto;">
                    <div class="confirm-message">
                        <div class="confirm-icon login">
                            <i class="fas fa-sign-in-alt"></i>
                        </div>
                        <h3>Login This Lead?</h3>
                        <p>Please provide the loan account number:</p>
                        <div class="form-group full-width" style="margin-top: 16px;">
                            <input type="text" id="loanAccountNumber" class="form-control" placeholder="Enter loan account number..." required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn-secondary" onclick="closeModal('loginModal')">Cancel</button>
                    <button class="btn-primary" onclick="confirmLogin()">
                        <i class="fas fa-check"></i> Yes, Login
                    </button>
                </div>
            </div>
        </div>

        <div class="modal-overlay" id="approveModal">
            <div class="modal-container modal-sm">
                <div class="modal-header">
                    <h2 class="modal-title">Approve Lead</h2>
                    <button class="modal-close" onclick="closeModal('approveModal')">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="modal-content" style="max-height: 70vh; overflow-y: auto;">
                    <div class="confirm-message">
                        <div class="confirm-icon approve">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <h3>Approve This Lead?</h3>
                        <p>Once approved, no further documents can be uploaded to this lead. This action will change the lead status to "Approved".</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn-secondary" onclick="closeModal('approveModal')">Cancel</button>
                    <button class="btn-success" onclick="confirmApprove()">
                        <i class="fas fa-check"></i> Yes, Approve
                    </button>
                </div>
            </div>
        </div>

        <div class="modal-overlay" id="rejectModal">
            <div class="modal-container modal-sm">
                <div class="modal-header">
                    <h2 class="modal-title">Reject Lead</h2>
                    <button class="modal-close" onclick="closeModal('rejectModal')">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="modal-content" style="max-height: 70vh; overflow-y: auto;">
                    <div class="confirm-message">
                        <div class="confirm-icon reject">
                            <i class="fas fa-times-circle"></i>
                        </div>
                        <h3>Reject This Lead?</h3>
                        <p>Please provide a reason for rejecting this lead:</p>
                        <div class="form-group full-width" style="margin-top: 16px;">
                            <textarea id="rejectionReason" class="form-control" placeholder="Enter rejection reason..." rows="4" required></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn-secondary" onclick="closeModal('rejectModal')">Cancel</button>
                    <button class="btn-danger" onclick="confirmReject()">
                        <i class="fas fa-times"></i> Yes, Reject
                    </button>
                </div>
            </div>
        </div>

        <div class="modal-overlay" id="disburseModal">
            <div class="modal-container modal-sm">
                <div class="modal-header">
                    <h2 class="modal-title">Disburse Lead</h2>
                    <button class="modal-close" onclick="closeModal('disburseModal')">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="modal-content" style="max-height: 70vh; overflow-y: auto;">
                    <div class="confirm-message">
                        <div class="confirm-icon disburse">
                            <i class="fas fa-rupee-sign"></i>
                        </div>
                        <h3>Disburse This Lead?</h3>
                        <p>This action will mark the lead as "Disbursed" and complete the lead process. This action cannot be undone.</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn-secondary" onclick="closeModal('disburseModal')">Cancel</button>
                    <button class="btn-success" onclick="confirmDisburse()">
                        <i class="fas fa-check"></i> Yes, Disburse
                    </button>
                </div>
            </div>
        </div>

        <div class="modal-overlay" id="futureLeadModal">
            <div class="modal-container modal-sm">
                <div class="modal-header">
                    <h2 class="modal-title">Mark as Future Lead</h2>
                    <button class="modal-close" onclick="closeModal('futureLeadModal')">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="modal-content" style="max-height: 70vh; overflow-y: auto;">
                    <div class="confirm-message">
                        <div class="confirm-icon future_lead">
                            <i class="fas fa-clock"></i>
                        </div>
                        <h3>Mark as Future Lead?</h3>
                        <p>This action will mark the lead as "Future Lead" and complete the lead process. This action cannot be undone.</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn-secondary" onclick="closeModal('futureLeadModal')">Cancel</button>
                    <button class="btn-future" onclick="confirmFutureLead()">
                        <i class="fas fa-check"></i> Yes, Mark as Future Lead
                    </button>
                </div>
            </div>
        </div>

        <div class="modal-overlay" id="addDocumentModal">
            <div class="modal-container modal-sm">
                <div class="modal-header">
                    <h2 class="modal-title">Add New Document</h2>
                    <button class="modal-close" onclick="closeModal('addDocumentModal')">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="modal-content" style="max-height: 70vh; overflow-y: auto;">
                    <form id="addDocumentForm" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="lead_id" id="documentLeadId">
                        <div class="form-group full-width">
                            <label for="documentName">Document Name <span class="required">*</span></label>
                            <input type="text" id="documentName" name="name" class="form-control" placeholder="e.g., ID Proof" required>
                        </div>
                        <div class="form-group full-width">
                            <label for="documentType">Document Type</label>
                            <select id="documentType" name="type" class="form-control">
                                <option value="">Select Type</option>
                                <option value="id_proof">ID Proof</option>
                                <option value="address_proof">Address Proof</option>
                                <option value="financial">Financial Document</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        <div class="form-group full-width">
                            <label for="documentDescription">Description</label>
                            <textarea id="documentDescription" name="description" class="form-control" placeholder="Enter description"></textarea>
                        </div>
                        <div class="form-group full-width">
                            <label for="documentFile">Upload File <span class="required">*</span></label>
                            <input type="file" id="documentFile" name="document_file" class="form-control" accept=".pdf,.jpg,.jpeg,.png" required>
                        </div>
                        <div class="form-actions">
                            <button type="button" class="btn-secondary" onclick="closeModal('addDocumentModal')">Cancel</button>
                            <button type="submit" class="btn-primary">
                                <i class="fas fa-upload"></i> Save & Upload
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="loading-overlay" id="loadingOverlay">
            <div class="loading-spinner"></div>
        </div>
    </div>

    <style>
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(15, 23, 42, 0.6);
            backdrop-filter: blur(6px);
            display: none;
            z-index: 50;
            overflow-y: auto;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .modal-overlay.active {
            display: block;
            opacity: 1;
            visibility: visible;
        }
        .modal-container {
            background: #ffffff;
            border-radius: 16px;
            max-width: 500px;
            width: 95%;
            margin: 20px auto;
            box-shadow: 0 12px 32px rgba(0, 0, 0, 0.15);
            transform: scale(0.9) translateY(40px);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .modal-overlay.active .modal-container {
            transform: scale(1) translateY(0);
        }
        .modal-header {
            background: #f1f5f9;
            border-bottom: 1px solid #e2e8f0;
            padding: 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .modal-title {
            font-size: 22px;
            font-weight: 700;
            color: #1e293b;
        }
        .modal-close {
            background: none;
            border: none;
            font-size: 1.5rem;
            color: #64748b;
            cursor: pointer;
            transition: color 0.2s ease;
        }
        .modal-close:hover {
            color: #475569;
        }
        .modal-content {
            padding: 24px;
            max-height: 70vh;
            overflow-y: auto;
        }
        .modal-footer {
            padding: 16px 24px;
            border-top: 1px solid #e2e8f0;
            display: flex;
            gap: 12px;
            justify-content: flex-end;
        }
        .confirm-message {
            text-align: center;
            padding: 24px;
        }
        .confirm-icon {
            width: 60px;
            height: 60px;
            background: #f8fafc;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 16px;
            font-size: 1.5rem;
        }
        .confirm-icon.authorize {
            background: #cffafe;
            color: #0e7490;
        }
        .confirm-icon.login {
            background: #fefce8;
            color: #854d09;
        }
        .confirm-icon.approve {
            background: #ede9fe;
            color: #5b21b6;
        }
        .confirm-icon.reject {
            background: #fee2e2;
            color: #991b1b;
        }
        .confirm-icon.disburse {
            background: #ccfbf1;
            color: #0f766e;
        }
        .confirm-icon.future_lead {
            background: #ecfccb;
            color: #3f6212;
        }
        .form-group {
            margin-bottom: 16px;
        }
        .form-group.full-width {
            width: 100%;
        }
        .form-control {
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            padding: 10px 14px;
            width: 100%;
            background: #ffffff;
            transition: all 0.2s ease;
        }
        .form-control:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15);
            outline: none;
        }
        .form-actions {
            display: flex;
            gap: 12px;
            justify-content: flex-end;
            margin-top: 16px;
        }
        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(15, 23, 42, 0.6);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 100;
        }
        .loading-overlay.active {
            display: flex;
        }
        .loading-spinner {
            border: 4px solid #f3f3f3;
            border-top: 4px solid #3b82f6;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        .notification {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 16px;
            border-radius: 8px;
            color: white;
            z-index: 1000;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: opacity 0.3s ease;
        }
        .notification.success {
            background: #10b981;
        }
        .notification.error {
            background: #ef4444;
        }
    </style>

    <script>


        function numberToWords(number) {
            const units = ['', 'One', 'Two', 'Three', 'Four', 'Five', 'Six', 'Seven', 'Eight', 'Nine'];
            const teens = ['Ten', 'Eleven', 'Twelve', 'Thirteen', 'Fourteen', 'Fifteen', 'Sixteen', 'Seventeen', 'Eighteen', 'Nineteen'];
            const tens = ['', '', 'Twenty', 'Thirty', 'Forty', 'Fifty', 'Sixty', 'Seventy', 'Eighty', 'Ninety'];
            const thousands = ['', 'Thousand', 'Lakh', 'Crore'];

            if (number === 0) return 'Zero Rupees';

            function convertLessThanThousand(num) {
                if (num === 0) return '';
                if (num < 10) return units[num];
                if (num < 20) return teens[num - 10];
                if (num < 100) {
                    return tens[Math.floor(num / 10)] + (num % 10 ? ' ' + units[num % 10] : '');
                }
                return units[Math.floor(num / 100)] + ' Hundred' + (num % 100 ? ' and ' + convertLessThanThousand(num % 100) : '');
            }

            let result = '';
            let thousandIndex = 0;

            while (number > 0) {
                if (thousandIndex === 0) {
                    if (number % 1000 > 0) {
                        result = convertLessThanThousand(number % 1000) + (result ? ' ' + thousands[thousandIndex] + ' ' + result : '');
                    }
                    number = Math.floor(number / 1000);
                } else {
                    if (number % 100 > 0) {
                        result = convertLessThanThousand(number % 100) + ' ' + thousands[thousandIndex] + (result ? ' ' + result : '');
                    }
                    number = Math.floor(number / 100);
                }
                thousandIndex++;
            }

            return result.trim() + ' Rupees';
        }

        document.getElementById('addDocumentForm').addEventListener('submit', function (e) {
            e.preventDefault();
            const fileInput = document.getElementById('documentFile');
            const file = fileInput.files[0];
            const maxSize = 2 * 1024 * 1024;
            const allowedTypes = ['application/pdf', 'image/jpeg', 'image/png'];

            if (file && file.size > maxSize) {
                showNotification('File size exceeds 2MB limit.', 'error');
                return;
            }

            if (file && !allowedTypes.includes(file.type)) {
                showNotification('Only PDF, JPG, and PNG files are allowed.', 'error');
                return;
            }

            const formData = new FormData(this);
            const leadId = document.getElementById('documentLeadId').value;

            showLoading(true);

            fetch(`/operations/leads/${leadId}/documents`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: formData
            })
            .then(res => {
                if (!res.ok) {
                    return res.json().then(err => { throw new Error(err.message || `HTTP ${res.status}`); });
                }
                return res.json();
            })
            .then(data => {
                showNotification(data.message || 'Document added successfully.', 'success');
                closeModal('addDocumentModal');
                loadLeadDetails();
            })
            .catch(error => {
                console.error("Error adding document:", error);
                showNotification(`Failed to add document: ${error.message}`, 'error');
            })
            .finally(() => showLoading(false));
        });

        window.currentLeadId = null;

        function setButtonStates(lead) {
            const isDisbursedOrFuture = ['disbursed', 'future_lead'].includes(lead.status);
            const buttons = [
                'editLeadButton',
                'saveLeadButton',
                'loginButton',
                'authorizeButton',
                'approveButton',
                'rejectButton',
                'disburseButton',
                'futureLeadButton',
                'addDocumentButton'
            ];
            buttons.forEach(id => {
                const button = document.getElementById(id);
                if (button) {
                    button.disabled = isDisbursedOrFuture;
                }
            });
        }

        function loadLeadDetails() {
            const urlParams = new URLSearchParams(window.location.search);
            const leadId = urlParams.get('leadId');
            window.currentLeadId = leadId;
            document.getElementById('documentLeadId').value = leadId;
            const docList = document.getElementById('documentList');

            if (!leadId) {
                showNotification('No lead ID provided.', 'error');
                return;
            }

            docList.innerHTML = '<p>Loading documents...</p>';
            showLoading(true);

            fetch(`/operations/leads/${leadId}/details`, {
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                }
            })
            .then(res => {
                if (!res.ok) {
                    return res.text().then(text => { throw new Error(`HTTP ${res.status}: ${text}`); });
                }
                const contentType = res.headers.get('content-type');
                if (contentType && contentType.includes('application/json')) {
                    return res.json();
                } else {
                    return res.text().then(text => { throw new Error('Response is not JSON: ' + text.substring(0, 50)); });
                }
            })
            .then(data => {
                const lead = data.lead;
                // Store lead data globally for use in modals
                window.currentLeadData = lead;
                document.getElementById('leadName').textContent = lead.name ?? 'N/A';
                document.getElementById('leadInitials').textContent = lead.name ? lead.name.charAt(0).toUpperCase() : '';
                document.getElementById('leadNameInput').value = lead.name ?? '';
                document.getElementById('leadCompany').value = lead.company_name ?? 'N/A';
                document.getElementById('leadPhone').value = lead.phone ?? 'N/A';
                document.getElementById('leadEmail').value = lead.email ?? '';
                document.getElementById('leadDob').value = lead.dob ?? '';
                document.getElementById('leadState').value = lead.state ?? '';
                document.getElementById('leadDistrict').value = lead.district ?? '';
                document.getElementById('leadCity').value = lead.city ?? '';
                document.getElementById('leadAmount').value = lead.lead_amount ? `${lead.lead_amount}` : '';
                document.getElementById('leadAmountInWords').textContent = lead.lead_amount ? numberToWords(parseInt(lead.lead_amount)) : 'N/A';
                document.getElementById('leadStatus').textContent = lead.status ? lead.status.replace(/_/g, ' ').replace(/\b\w/g, c => c.toUpperCase()) : 'N/A';
                document.getElementById('leadStatus').className = `status ${lead.status}`;
                // document.getElementById('leadExpectedMonth').value = lead.expected_month ?? '';

                const leadTypeDisplay = lead.lead_type ? lead.lead_type.replace(/_/g, ' ').replace(/\b\w/g, c => c.toUpperCase()) : 'N/A';
                document.getElementById('leadTypeDisplay').textContent = leadTypeDisplay;
                document.getElementById('leadType').value = lead.lead_type || '';

                // document.getElementById('leadAccountNumber').value = lead.loan_account_number ?? '';
                // document.getElementById('leadTurnoverAmount').value = lead.turnover_amount ? `${lead.turnover_amount}` : '';
                document.getElementById('leadSalary').value = lead.salary ? `${lead.salary}` : '';
                document.getElementById('leadBank').value = lead.bank_name ?? '';

                // const voiceElement = document.getElementById('leadVoiceRecording');
                // if (lead.voice_recording) {
                //     voiceElement.innerHTML = `<a href="${lead.voice_recording}" target="_blank">Play Recording</a>`;
                // } else {
                //     voiceElement.textContent = 'N/A';
                // }

                   const voiceElement = document.getElementById('leadVoiceRecording');
                 if (lead.voice_recording) {
    voiceElement.innerHTML = `
        <audio controls style="width:100%;">
            <source src="${lead.voice_recording}" type="audio/mpeg">
            Your browser does not support the audio element.
        </audio>
    `;
} else {
    voiceElement.textContent = 'N/A';
}

                document.getElementById('leadEmployeeName').textContent = lead.employee_name ?? 'N/A';
                document.getElementById('leadTeamLeadName').textContent = lead.team_lead_name ?? 'N/A';

                const rejectionReasonSection = document.getElementById('rejectionReasonSection');
                const rejectionReason = document.getElementById('leadReason');
                rejectionReason.textContent = lead.rejection_reason ?? 'N/A';
                rejectionReasonSection.style.display = lead.status === 'rejected' ? 'block' : 'none';

                setButtonStates(lead);

                docList.innerHTML = '';
                if (data.documents && data.documents.length > 0) {
                    data.documents.forEach(doc => {
                        const docItem = document.createElement('div');
                        docItem.className = 'document-item';
                        docItem.innerHTML = `
                            <div>
                                <label>${doc.document_name}</label>
                                ${doc.filepath ?
                                    `<a href="/storage/${doc.filepath}" target="_blank"><i class="fas fa-eye"></i> View File</a>` :
                                    `<form id="uploadForm_${doc.document_id}" method="POST" enctype="multipart/form-data" action="/operations/leads/${leadId}/documents/${doc.document_id}/upload">
                                        <input type="hidden" name="_token" value="${document.querySelector('meta[name="csrf-token"]').content}">
                                        <input type="file" name="document_file" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx" required>
                                        <button type="submit" class="btn-primary"><i class="fas fa-upload"></i> Upload</button>
                                    </form>`}
                            </div>
                            ${doc.filepath && !['approved', 'disbursed'].includes(lead.status) ?
                                `<button class="btn-danger" onclick="deleteDocument(${leadId}, ${doc.document_id})"><i class="fas fa-trash"></i> Delete</button>` :
                                ''}
                        `;
                        docList.appendChild(docItem);
                    });
                } else {
                    docList.innerHTML = '<p>No documents uploaded.</p>';
                }

                // Update follow-up section
                const followupList = document.getElementById('followupList');
                followupList.innerHTML = '';

                if (data.followUps && data.followUps.length > 0) {
                    data.followUps.forEach(fu => {
                        const formattedDate = new Date(fu.timestamp).toLocaleString('en-IN', {
                            year: 'numeric',
                            month: 'short',
                            day: 'numeric',
                            hour: '2-digit',
                            minute: '2-digit'
                        });

                        followupList.innerHTML += `
                            <div class="followup-item">
                                <p class="followup-message">${fu.message || 'No message provided'}</p>
                                ${fu.recording_path ? `
                                    <audio controls class="followup-audio">
                                        <source src="${fu.recording_path}" type="audio/mpeg">
                                        Your browser does not support the audio element.
                                    </audio>
                                ` : ''}
                                <div class="followup-meta">
                                    <span><strong>${fu.user?.name ?? 'Unknown User'}</strong></span>
                                    <span class="followup-date">${formattedDate}</span>
                                </div>
                            </div>
                        `;
                    });
                } else {
                    followupList.innerHTML = `
                        <div class="no-followups">
                            <i class="fas fa-inbox"></i>
                            <p>No follow-ups found for this lead.</p>
                        </div>
                    `;
                }

                document.querySelectorAll('form[id^="uploadForm_"]').forEach(form => {
                    form.addEventListener('submit', function (e) {
                        e.preventDefault();
                        const formData = new FormData(this);
                        const documentId = this.id.split('_')[1];

                        showLoading(true);

                        fetch(this.action, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json'
                            },
                            body: formData
                        })
                        .then(res => {
                            if (!res.ok) {
                                return res.json().then(err => {
                                    throw new Error(err.message || `HTTP ${res.status}: ${err.error || 'Unknown error'}`);
                                });
                            }
                            return res.json();
                        })
                        .then(data => {
                            showNotification('Document uploaded successfully.', 'success');
                            loadLeadDetails();
                        })
                        .catch(error => {
                            console.error("Error uploading document:", error);
                            showNotification(`Failed to upload document: ${error.message}`, 'error');
                        })
                        .finally(() => showLoading(false));
                    });
                });
            })
            .catch(error => {
                console.error("Error fetching lead details:", error);
                showNotification(`Failed to load lead details: ${error.message}`, 'error');
            })
            .finally(() => showLoading(false));
        }

        // Add event listeners for dynamic updates after DOM is loaded
        function addDynamicUpdateListeners() {
            // Update name dynamically
            const nameInput = document.getElementById('leadNameInput');
            nameInput.addEventListener('input', function() {
                const name = this.value.trim();
                if (name) {
                    // Update proper case name
                    const properName = name.toLowerCase().split(' ').map(word =>
                        word.charAt(0).toUpperCase() + word.slice(1)
                    ).join(' ');
                    document.getElementById('leadName').textContent = properName;
                    document.getElementById('leadInitials').textContent = properName.charAt(0).toUpperCase();
                } else {
                    document.getElementById('leadName').textContent = 'N/A';
                    document.getElementById('leadInitials').textContent = '';
                }
            });

            // Update amount in words dynamically
            const amountInput = document.getElementById('leadAmount');
            amountInput.addEventListener('input', function() {
                const amount = parseFloat(this.value);
                const amountInWordsElement = document.getElementById('leadAmountInWords');
                if (!isNaN(amount) && amount > 0) {
                    amountInWordsElement.textContent = numberToWords(Math.floor(amount));
                } else {
                    amountInWordsElement.textContent = 'N/A';
                }
            });
        }

        function enableLeadEdit() {
            const editButton = document.getElementById('editLeadButton');
            const saveButton = document.getElementById('saveLeadButton');
            const fields = document.querySelectorAll('.editable-field');

            fields.forEach(field => field.disabled = false);
            editButton.style.display = 'none';
            saveButton.style.display = 'inline-flex';

            document.getElementById('leadTypeDisplay').style.display = 'none';
            document.getElementById('leadType').style.display = 'block';

            // Show dropdowns and hide text inputs for location fields
            document.getElementById('leadState').style.display = 'none';
            document.getElementById('leadStateDropdown').style.display = 'block';
            document.getElementById('leadDistrict').style.display = 'none';
            document.getElementById('leadDistrictDropdown').style.display = 'block';
            document.getElementById('leadCity').style.display = 'none';
            document.getElementById('leadCityDropdown').style.display = 'block';

            // Load states if not already loaded
            if (document.getElementById('leadStateDropdown').options.length <= 1) {
                loadStates();
            }
        }

        function disableLeadEdit() {
            const fields = document.querySelectorAll('.editable-field');
            fields.forEach(field => field.disabled = true);

            document.getElementById('editLeadButton').style.display = 'inline-flex';
            document.getElementById('saveLeadButton').style.display = 'none';

            document.getElementById('leadTypeDisplay').style.display = 'inline-block';
            document.getElementById('leadType').style.display = 'none';

            // Hide dropdowns and show text inputs for location fields
            document.getElementById('leadState').style.display = 'block';
            document.getElementById('leadStateDropdown').style.display = 'none';
            document.getElementById('leadDistrict').style.display = 'block';
            document.getElementById('leadDistrictDropdown').style.display = 'none';
            document.getElementById('leadCity').style.display = 'block';
            document.getElementById('leadCityDropdown').style.display = 'none';
        }

        function saveLeadChanges() {
            const leadId = window.currentLeadId;
            const data = {
                name: document.getElementById('leadNameInput').value,
                phone: document.getElementById('leadPhone').value,
                email: document.getElementById('leadEmail').value,
                dob: document.getElementById('leadDob').value,
                state: document.getElementById('leadStateDropdown').options[document.getElementById('leadStateDropdown').selectedIndex]?.text || document.getElementById('leadState').value,
                district: document.getElementById('leadDistrictDropdown').options[document.getElementById('leadDistrictDropdown').selectedIndex]?.text || document.getElementById('leadDistrict').value,
                city: document.getElementById('leadCityDropdown').options[document.getElementById('leadCityDropdown').selectedIndex]?.text || document.getElementById('leadCity').value,
                company_name: document.getElementById('leadCompany').value,
                lead_amount: document.getElementById('leadAmount').value,
                // expected_month: document.getElementById('leadExpectedMonth').value,
                lead_type: document.getElementById('leadType').value,
                // loan_account_number: document.getElementById('leadAccountNumber').value,
                // turnover_amount: document.getElementById('leadTurnoverAmount').value,
                salary: document.getElementById('leadSalary').value,
                bank_name: document.getElementById('leadBank').value,
                _token: document.querySelector('meta[name="csrf-token"]').content
            };

            showLoading(true);

            fetch(`/operations/creditcardleads/${leadId}/update`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': data._token
                },
                body: JSON.stringify(data)
            })
            .then(async res => {
                if (!res.ok) {
                    const err = await res.json();
                    let errorMessage = 'An unexpected error occurred';
                    if (err.message && typeof err.message === 'object') {
                        if (err.message.phone) {
                            errorMessage = err.message.phone.join(', ');
                        } else {
                            errorMessage = Object.values(err.message).flat().join(', ');
                        }
                    } else if (err.message) {
                        errorMessage = err.message;
                    } else {
                        errorMessage = `HTTP ${res.status}`;
                    }
                    throw new Error(err.error || errorMessage);
                }
                return res.json();
            })
            .then(data => {
                showNotification(data.message || 'Lead updated successfully.', 'success');
                disableLeadEdit();

                const leadType = document.getElementById('leadType').value;
                const formattedType = leadType ? leadType.replace(/_/g, ' ').replace(/\b\w/g, c => c.toUpperCase()) : 'N/A';
                document.getElementById('leadTypeDisplay').textContent = formattedType;

                loadLeadDetails();
            })
            .catch(error => {
                console.error("Error updating lead:", error);
                let errorMessage = 'An unexpected error occurred';
                if (error.message && typeof error.message === 'object') {
                    const firstError = Object.values(error.message)[0]?.[0];
                    errorMessage = firstError || 'Validation error';
                } else if (typeof error.message === 'string') {
                    errorMessage = error.message;
                }
                showNotification(`Failed to update lead: ${errorMessage}`, 'error');
            })
            .finally(() => showLoading(false));
        }

        async function loadStates() {
            try {
                console.log('Loading states...');

                const response = await fetch('/operations/locations/states', {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Authorization': `Bearer ${localStorage.getItem('auth_token')}`
                    },
                    credentials: 'include'
                });

                console.log('Response status:', response.status);

                if (!response.ok) {
                    const errorData = await response.json();
                    throw new Error(errorData.message || 'Failed to load states');
                }

                const data = await response.json();
                console.log('States data:', data);

                const dropdown = document.getElementById('leadStateDropdown');
                if (!dropdown) throw new Error('State dropdown element not found');

                // Clear and populate dropdown
                dropdown.innerHTML = '<option value="">Select State</option>';

                if (data.data && data.data.length > 0) {
                    data.data.forEach(state => {
                        const option = new Option(state.state_title, state.state_id);
                        dropdown.add(option);
                    });

                    // Set current selection if available
                    const currentState = document.getElementById('leadState').value;
                    if (currentState) {
                        const selectedOption = [...dropdown.options].find(
                            opt => opt.text === currentState
                        );
                        if (selectedOption) {
                            selectedOption.selected = true;
                            await loadDistricts(selectedOption.value);
                        }
                    }
                } else {
                    dropdown.innerHTML += '<option value="" disabled>No states available</option>';
                }
            } catch (error) {
                console.error('Error loading states:', error);
                showNotification(error.message || 'Failed to load states', 'error');
            }
        }

        async function loadDistricts(stateId) {
            if (!stateId) return;

            try {
                console.log(`Loading districts for state ${stateId}...`);

                const response = await fetch(`/operations/locations/districts/${stateId}`, {
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    credentials: 'include'
                });

                console.log('Districts response status:', response.status);

                if (!response.ok) {
                    const errorData = await response.json();
                    throw new Error(errorData.message || 'Failed to load districts');
                }

                const data = await response.json();
                console.log('Districts data:', data);

                const dropdown = document.getElementById('leadDistrictDropdown');
                if (!dropdown) throw new Error('District dropdown element not found');

                // Clear and populate dropdown
                dropdown.innerHTML = '<option value="">Select District</option>';

                if (data.data && data.data.length > 0) {
                    data.data.forEach(district => {
                        const option = new Option(district.district_title, district.districtid);
                        dropdown.add(option);
                    });

                    // Set current selection if available
                    const currentDistrict = document.getElementById('leadDistrict').value;
                    if (currentDistrict) {
                        const selectedOption = [...dropdown.options].find(
                            opt => opt.text === currentDistrict
                        );
                        if (selectedOption) {
                            selectedOption.selected = true;
                            await loadCities(selectedOption.value);
                        }
                    }
                } else {
                    dropdown.innerHTML += '<option value="" disabled>No districts available</option>';
                }
            } catch (error) {
                console.error('Error loading districts:', error);
                showNotification(error.message || 'Failed to load districts', 'error');
            }
        }

        async function loadCities(districtId) {
            if (!districtId) return;

            try {
                console.log(`Loading cities for district ${districtId}...`);

                const response = await fetch(`/operations/locations/cities/${districtId}`, {
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    credentials: 'include'
                });

                console.log('Cities response status:', response.status);

                if (!response.ok) {
                    const errorData = await response.json();
                    throw new Error(errorData.message || 'Failed to load cities');
                }

                const data = await response.json();
                console.log('Cities data:', data);

                const dropdown = document.getElementById('leadCityDropdown');
                if (!dropdown) throw new Error('City dropdown element not found');

                // Clear and populate dropdown
                dropdown.innerHTML = '<option value="">Select City</option>';

                if (data.data && data.data.length > 0) {
                    data.data.forEach(city => {
                        const option = new Option(city.name, city.id);
                        dropdown.add(option);
                    });

                    // Set current selection if available
                    const currentCity = document.getElementById('leadCity').value;
                    if (currentCity) {
                        const selectedOption = [...dropdown.options].find(
                            opt => opt.text === currentCity
                        );
                        if (selectedOption) {
                            selectedOption.selected = true;
                        }
                    }
                } else {
                    dropdown.innerHTML += '<option value="" disabled>No cities available</option>';
                }
            } catch (error) {
                console.error('Error loading cities:', error);
                showNotification(error.message || 'Failed to load cities', 'error');
            }
        }

        function openAddDocumentModal() {
            const modal = document.getElementById('addDocumentModal');
            if (modal) {
                modal.classList.add('active');
                document.getElementById('documentName').value = '';
                document.getElementById('documentType').value = '';
                document.getElementById('documentDescription').value = '';
                document.getElementById('documentFile').value = '';
            }
        }

       function deleteDocument(leadId, documentId) {
            if (!confirm('Are you sure you want to delete this document?')) return;

            showLoading(true);

            fetch(`/operations/leads/${leadId}/documents/${documentId}/delete`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            })
            .then(res => {
                if (!res.ok) {
                    return res.text().then(text => {
                        // Check if response is JSON
                        try {
                            const json = JSON.parse(text);
                            throw new Error(json.message || `HTTP ${res.status}`);
                        } catch (e) {
                            // If not JSON, it's likely an HTML error page
                            throw new Error(`Server returned HTML error page (Status ${res.status})`);
                        }
                    });
                }
                return res.json();
            })
            .then(data => {
                showNotification(data.message || 'Document deleted successfully.', 'success');
                loadLeadDetails();
            })
            .catch(error => {
                console.error("Error deleting document:", error);
                showNotification(`Failed to delete document: ${error.message}`, 'error');
            })
            .finally(() => showLoading(false));
        }


        function closeModal(modalId) {
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.classList.remove('active');
                if (modalId === 'rejectModal') {
                    document.getElementById('rejectionReason').value = '';
                } else if (modalId === 'loginModal') {
                    document.getElementById('loanAccountNumber').value = '';
                }
            }

        }

        function showAuthorizeModal(leadId) {
            window.currentLeadId = leadId;
            document.getElementById('authorizeModal').classList.add('active');
        }

        function confirmAuthorize() {
            updateLeadStatus(window.currentLeadId, 'authorized');
        }

        function showLoginModal(leadId) {
            window.currentLeadId = leadId;
            document.getElementById('loginModal').classList.add('active');
        }

        function confirmLogin() {
            const loanAccountNumber = document.getElementById('loanAccountNumber').value;
            if (!loanAccountNumber) {
                showNotification('Please provide a loan account number.', 'error');
                return;
            }
            updateLeadStatus(window.currentLeadId, 'login', null, null, loanAccountNumber);
        }

        function showApproveModal(leadId) {
            window.currentLeadId = leadId;
            document.getElementById('approveModal').classList.add('active');
        }

        function confirmApprove() {
            updateLeadStatus(window.currentLeadId, 'approved');
        }

        function showRejectModal(leadId) {
            window.currentLeadId = leadId;
            document.getElementById('rejectModal').classList.add('active');
        }

        function confirmReject() {
            const reason = document.getElementById('rejectionReason').value;
            if (!reason) {
                showNotification('Please provide a rejection reason.', 'error');
                return;
            }
            updateLeadStatus(window.currentLeadId, 'rejected', reason);
        }

        function showDisburseModal(leadId) {
            window.currentLeadId = leadId;
            document.getElementById('disburseModal').classList.add('active');
        }

        function confirmDisburse() {
            updateLeadStatus(window.currentLeadId, 'disbursed');
        }

        function showFutureLeadModal(leadId) {
            window.currentLeadId = leadId;
            document.getElementById('futureLeadModal').classList.add('active');
        }

        function confirmFutureLead() {
            updateLeadStatus(window.currentLeadId, 'future_lead');
        }

        function updateLeadStatus(leadId, status, reason = null, expectedMonth = null, loanAccountNumber = null) {
            const data = {
                status: status,
                reason: reason,
                loan_account_number: loanAccountNumber,
                _token: document.querySelector('meta[name="csrf-token"]').content
            };

            showLoading(true);

            fetch(`/operations/leads/${leadId}/creditcardstatus`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify(data)
            })
            .then(res => {
                if (!res.ok) {
                    return res.json().then(err => { throw new Error(err.message || `HTTP ${res.status}`); });
                }
                return res.json();
            })
            .then(data => {
                showNotification(data.message || `Lead status updated to ${status}.`, 'success');

                let modalId;
                switch (status) {
                    case 'authorized':
                        modalId = 'authorizeModal';
                        break;
                    case 'approved':
                        modalId = 'approveModal';
                        break;
                    case 'rejected':
                        modalId = 'rejectModal';
                        break;
                    case 'disbursed':
                        modalId = 'disburseModal';
                        break;
                    case 'future_lead':
                        modalId = 'futureLeadModal';
                        break;
                    case 'login':
                        modalId = 'loginModal';
                        break;
                }

                if (modalId) closeModal(modalId);
                loadLeadDetails();
            })
            .catch(error => {
                console.error(`Error updating lead status to ${status}:`, error);
                showNotification(`Failed to update lead status: ${error.message}`, 'error');
            })
            .finally(() => showLoading(false));
        }

        function showNotification(message, type) {
            const notification = document.createElement('div');
            notification.className = `notification ${type === 'error' ? 'error' : 'success'}`;
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

        function goBackAndReload() {
            // Check if we have a referrer (previous page)
            if (document.referrer && document.referrer !== window.location.href) {
                const referrerUrl = new URL(document.referrer);

                // Check if referrer is the dashboard
                if (referrerUrl.pathname.includes('/operations/dashboard') || referrerUrl.pathname === '/operations') {
                    // Add hash to scroll to credit card section
                    referrerUrl.hash = 'credit-card-section';
                    referrerUrl.searchParams.set('_reload', Date.now());
                    referrerUrl.searchParams.set('_scroll_to', 'credit-card-section');

                    // Navigate to dashboard with credit card section focus
                    window.location.href = referrerUrl.toString();
                } else {
                    // Add a timestamp to force reload for other pages
                    referrerUrl.searchParams.set('_reload', Date.now());
                    window.location.href = referrerUrl.toString();
                }
            } else if (window.history.length > 1) {
                // Fallback: Use history back with popstate event to reload
                window.addEventListener('popstate', function(event) {
                    // Check if we're now on dashboard and scroll to credit card section
                    if (window.location.pathname.includes('/operations/dashboard') || window.location.pathname === '/operations') {
                        setTimeout(() => {
                            const creditCardSection = document.getElementById('credit-card-section');
                            if (creditCardSection) {
                                creditCardSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
                                // Add visual highlight
                                creditCardSection.style.boxShadow = '0 0 0 3px #3b82f6';
                                setTimeout(() => {
                                    creditCardSection.style.boxShadow = '';
                                }, 2000);
                            }
                        }, 100);
                    }
                    window.location.reload();
                }, { once: true });
                window.history.back();
            } else {
                // If no history or referrer, redirect to dashboard with credit card focus
                window.location.href = '/operations/dashboard?_reload=' + Date.now() + '&_scroll_to=credit-card-section#credit-card-section';
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
              // Handle URL parameters for scrolling to specific sections
    const urlParams = new URLSearchParams(window.location.search);
    const scrollToSection = urlParams.get('_scroll_to');

    // Check for credit card section scroll parameter or hash
    if (scrollToSection === 'credit-card-section' || window.location.hash === '#credit-card-section') {
        setTimeout(() => {
            const creditCardSection = document.getElementById('credit-card-section');
            if (creditCardSection) {
                creditCardSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
                // Add visual highlight
                creditCardSection.style.boxShadow = '0 0 0 3px #3b82f6';
                setTimeout(() => {
                    creditCardSection.style.boxShadow = '';
                }, 2000);
            }
        }, 300); // Increased delay to ensure all elements are loaded
        return; // Skip the regular scroll restoration if we're scrolling to a specific section
    }
            loadLeadDetails();
            addDynamicUpdateListeners();
        });
    </script>
</body>
</html>
