<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Lead Details</title>
    
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary: #f97316;
            --primary-hover: #ea580c;
            --bg-light: #f3f4f6;
            --text-dark: #1f2937;
            --text-muted: #6b7280;
            --border: #e5e7eb;
        }

        body {
            background-color: var(--bg-light);
            font-family: 'Inter', sans-serif;
            color: var(--text-dark);
            overflow-x: hidden;
        }

        /* Layout */
        .main-content {
            margin-left: 280px;
            padding-top: 80px;
            min-height: 100vh;
            transition: all 0.3s ease;
        }

        .page-wrapper {
            padding: 1.5rem 2rem;
            max-width: 1400px;
            margin: 0 auto;
        }

        /* Cards */
        .card-box {
            background: white;
            border-radius: 16px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
            border: 1px solid var(--border);
            margin-bottom: 24px;
            overflow: hidden;
        }

        .card-header {
            padding: 20px 24px;
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: #fff;
        }

        .section-title {
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--text-dark);
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        /* Detail Grid Layout */
        .detail-grid-container {
            display: grid;
            grid-template-columns: 350px 1fr;
            gap: 24px;
            align-items: start;
        }

        /* Profile Section (Left) */
        .profile-card {
            background: #fff;
            padding: 24px;
            border-radius: 16px;
            border: 1px solid var(--border);
            text-align: center;
        }

        .lead-avatar {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            background: linear-gradient(135deg, #f97316, #ea580c);
            color: white;
            font-size: 2.5rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 16px;
            box-shadow: 0 4px 10px rgba(249, 115, 22, 0.2);
        }

        .lead-name { font-size: 1.25rem; font-weight: 700; margin-bottom: 4px; }
        .lead-id { font-size: 0.85rem; color: var(--text-muted); margin-bottom: 20px; }

        .contact-list {
            text-align: left;
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .contact-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 14px;
            background: var(--bg-light);
            border-radius: 8px;
            font-size: 0.9rem;
            color: var(--text-dark);
        }

        .contact-item i { color: var(--primary); width: 20px; text-align: center; }
        .contact-item input { border: none; background: transparent; width: 100%; outline: none; font-weight: 500; }

        /* Detail Section (Right) */
        .info-section-title {
            font-size: 0.85rem;
            font-weight: 700;
            color: var(--text-muted);
            text-transform: uppercase;
            margin-bottom: 12px;
            padding-bottom: 8px;
            border-bottom: 2px solid var(--border);
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 16px;
            margin-bottom: 24px;
        }

        .info-item {
            background: var(--bg-light);
            padding: 12px 16px;
            border-radius: 10px;
            border: 1px solid var(--border);
        }

        .info-label {
            font-size: 0.7rem;
            color: var(--text-muted);
            text-transform: uppercase;
            font-weight: 600;
            margin-bottom: 4px;
            display: block;
        }

        .info-value {
            font-size: 0.95rem;
            font-weight: 600;
            color: var(--text-dark);
            width: 100%;
            background: transparent;
            border: none;
            outline: none;
        }
        
        .info-value:disabled { background: transparent; color: var(--text-dark); opacity: 1; }
        .info-value:not(:disabled) { border-bottom: 2px solid var(--primary); background: #fff7ed; }

        /* Buttons */
        .btn-action {
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.9rem;
            border: none;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.2s;
            text-decoration: none;
        }
        .btn-action:hover { transform: translateY(-1px); box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        .btn-action:disabled { background: #e5e7eb; color: #9ca3af; cursor: not-allowed; transform: none; box-shadow: none; }

        .btn-primary { background: linear-gradient(135deg, #f97316, #ea580c); color: white; }
        .btn-success { background: #10b981; color: white; }
        .btn-danger { background: #ef4444; color: white; }
        .btn-secondary { background: white; border: 1px solid var(--border); color: var(--text-dark); }
        .btn-secondary:hover { background: #f9fafb; border-color: #d1d5db; color: var(--primary); }

        /* Status Colors for Buttons */
        .btn-authorize { background: #8b5cf6; color: white; }
        .btn-approve { background: #10b981; color: white; }
        .btn-reject { background: #ef4444; color: white; }
        .btn-disburse { background: #059669; color: white; }
        .btn-future { background: #f59e0b; color: white; }

        /* Status Badge */
        .status-badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            display: inline-block;
        }
        /* Using class names from previous logic */
        .status-badge.personal_lead { background: #dbeafe; color: #1e40af; }
        .status-badge.authorized { background: #ede9fe; color: #5b21b6; }
        .status-badge.login { background: #fef3c7; color: #92400e; }
        .status-badge.approved { background: #dcfce7; color: #166534; }
        .status-badge.rejected { background: #fee2e2; color: #991b1b; }
        .status-badge.disbursed { background: #ccfbf1; color: #0f766e; }
        .status-badge.future_lead { background: #ffedd5; color: #9a3412; }

        /* Documents */
        .document-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px;
            background: white;
            border: 1px solid var(--border);
            border-radius: 8px;
            margin-bottom: 8px;
        }
        .document-item a { color: var(--primary); text-decoration: none; font-weight: 500; font-size: 0.9rem; }
        
        /* Modals */
        .modal-overlay {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(0, 0, 0, 0.6);
            backdrop-filter: blur(4px);
            z-index: 9999;
            display: none; align-items: center; justify-content: center;
        }
        .modal-overlay.active { display: flex; }

        .modal-container {
            background: white;
            border-radius: 16px;
            width: 90%; max-width: 500px;
            display: flex; flex-direction: column;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            animation: slideUp 0.3s ease-out;
        }
        @keyframes slideUp { from { transform: translateY(20px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }

        .modal-header {
            padding: 20px;
            border-bottom: 1px solid var(--border);
            display: flex; justify-content: space-between; align-items: center;
        }
        .modal-title { font-size: 1.1rem; font-weight: 700; margin: 0; }
        .modal-close { background: none; border: none; font-size: 1.2rem; color: var(--text-muted); cursor: pointer; }
        
        .modal-content { padding: 24px; }
        
        .modal-footer {
            padding: 16px 24px;
            border-top: 1px solid var(--border);
            background: #f9fafb;
            display: flex; justify-content: flex-end; gap: 10px;
            border-radius: 0 0 16px 16px;
        }

        /* Loading */
        #loadingOverlay {
            display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(255,255,255,0.8); z-index: 10000; justify-content: center; align-items: center;
        }
        #loadingOverlay.active { display: flex; }
        .spinner { width: 40px; height: 40px; border: 4px solid #e5e7eb; border-top: 4px solid var(--primary); border-radius: 50%; animation: spin 1s linear infinite; }
        @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }

        /* Notification */
        .notification {
            position: fixed; top: 20px; right: 20px; padding: 12px 20px; border-radius: 8px; color: white; z-index: 10000;
            display: flex; align-items: center; gap: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); animation: fadeIn 0.3s;
        }
        .notification.success { background: #10b981; }
        .notification.error { background: #ef4444; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(-10px); } to { opacity: 1; transform: translateY(0); } }

        /* Responsive */
        @media (max-width: 992px) {
            .main-content { margin-left: 0; padding: 20px; }
            .detail-grid-container { grid-template-columns: 1fr; }
            .form-actions-bar { flex-direction: column; }
            .form-actions-bar button { width: 100%; }
        }
    </style>
</head>
<body>

    @include('admin.Components.sidebar')

    <div class="main-content">
        @include('admin.Components.header')

        <div class="page-wrapper">
            
            <div class="flex items-center justify-between mb-6">
                <h1 class="section-title">
                    <i class="fas fa-credit-card text-primary text-xl"></i>
                    Lead Details
                </h1>
                <button class="btn-action btn-secondary" onclick="goBackAndReload()">
                    <i class="fas fa-arrow-left"></i> Back
                </button>
            </div>

            <div class="detail-grid-container">
                
                <div class="flex flex-col gap-6">
                    <div class="profile-card">
                        <div class="lead-avatar" id="leadInitials">AB</div>
                        <h2 class="lead-name" id="leadName">--</h2>
                        <p class="lead-id">Lead ID: <span id="documentLeadIdDisplay"></span></p>
                        
                        <div class="mt-4">
                            <span id="leadStatus" class="status-badge"></span>
                        </div>
                    </div>

                    <div class="card-box p-6">
                        <h3 class="info-section-title">Contact Details</h3>
                        <div class="contact-list">
                            <div class="contact-item">
                                <i class="fas fa-phone"></i>
                                <input type="text" id="leadPhone" class="editable-field" disabled>
                            </div>
                            <div class="contact-item">
                                <i class="fas fa-envelope"></i>
                                <input type="email" id="leadEmail" class="editable-field" disabled>
                            </div>
                            <div class="contact-item">
                                <i class="fas fa-university"></i>
                                <input type="text" id="leadBank" class="editable-field" disabled>
                            </div>
                        </div>
                    </div>

                    <div class="card-box p-6">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="info-section-title mb-0 border-0 p-0">History</h3>
                            <i class="fas fa-history text-muted"></i>
                        </div>
                        <div id="followupList" class="text-sm text-gray-600 max-h-60 overflow-y-auto">
                            <div class="no-followups text-center p-4 italic text-gray-400">Loading history...</div>
                        </div>
                    </div>
                </div>

                <div class="flex flex-col gap-6">
                    
                    <div class="card-box p-6">
                        <input type="hidden" id="documentLeadId">
                        <input type="hidden" id="leadNameInput"> 
                        <input type="hidden" id="leadType">

                        <h3 class="info-section-title">Financial Information</h3>
                        <div class="info-grid">
                            <div class="info-item">
                                <label class="info-label">Loan Amount</label>
                                <input type="number" id="leadAmount" class="info-value editable-field" disabled>
                            </div>
                            <div class="info-item col-span-2">
                                <label class="info-label">Amount in Words</label>
                                <span id="leadAmountInWords" class="text-primary font-bold text-sm block mt-1"></span>
                            </div>
                            <div class="info-item">
                                <label class="info-label">Salary</label>
                                <input type="number" id="leadSalary" class="info-value editable-field" disabled>
                            </div>
                            </div>

                        <h3 class="info-section-title">Personal & Location</h3>
                        <div class="info-grid">
                            <div class="info-item">
                                <label class="info-label">DOB</label>
                                <input type="date" id="leadDob" class="info-value editable-field" disabled>
                            </div>
                            <div class="info-item">
                                <label class="info-label">Company</label>
                                <input type="text" id="leadCompany" class="info-value editable-field" disabled>
                            </div>
                            <div class="info-item">
                                <label class="info-label">State</label>
                                <input type="text" id="leadState" class="info-value editable-field" disabled>
                                <select id="leadStateDropdown" class="info-value editable-field" style="display:none;" onchange="loadDistricts(this.value)"></select>
                            </div>
                            <div class="info-item">
                                <label class="info-label">District</label>
                                <input type="text" id="leadDistrict" class="info-value editable-field" disabled>
                                <select id="leadDistrictDropdown" class="info-value editable-field" style="display:none;" onchange="loadCities(this.value)"></select>
                            </div>
                            <div class="info-item">
                                <label class="info-label">City</label>
                                <input type="text" id="leadCity" class="info-value editable-field" disabled>
                                <select id="leadCityDropdown" class="info-value editable-field" style="display:none;"></select>
                            </div>
                        </div>

                        <h3 class="info-section-title">Meta Data</h3>
                        <div class="info-grid">
                            <div class="info-item">
                                <label class="info-label">Lead Type</label>
                                <span id="leadTypeDisplay" class="font-semibold text-gray-800 text-sm"></span>
                            </div>
                            <div class="info-item">
                                <label class="info-label">Account No</label>
                                <input type="text" id="leadAccountNumber" class="info-value editable-field" disabled>
                            </div>
                            <div class="info-item">
                                <label class="info-label">Voice Recording</label>
                                <span id="leadVoiceRecording" class="text-sm"></span>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4 mt-4 p-4 bg-gray-50 rounded-lg border border-gray-200">
                            <div>
                                <label class="info-label">Created By</label>
                                <span id="leadEmployeeName" class="font-semibold text-gray-800 text-sm"></span>
                            </div>
                            <div>
                                <label class="info-label">Team Lead</label>
                                <span id="leadTeamLeadName" class="font-semibold text-gray-800 text-sm"></span>
                            </div>
                        </div>

                        <div id="rejectionReasonSection" style="display:none;" class="mt-4 p-4 bg-red-50 border border-red-200 rounded-lg text-red-700 text-sm">
                            <strong class="block mb-1">Reason for Rejection:</strong>
                            <span id="leadReason"></span>
                        </div>
                    </div>

                    <div class="card-box p-6">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="info-section-title mb-0 border-0 p-0">Documents</h3>
                            <button class="btn-action btn-primary py-2 px-3 text-xs" id="addDocumentButton" onclick="openAddDocumentModal()">
                                <i class="fas fa-plus"></i> Add New
                            </button>
                        </div>
                        <div id="documentList" class="document-list">
                            <p class="text-gray-400 italic text-center py-4">Loading documents...</p>
                        </div>
                    </div>

                    <div class="card-box p-6 sticky bottom-4 z-10 shadow-lg border-t-4 border-orange-500">
                        <div class="flex flex-wrap gap-3 justify-end items-center form-actions-bar">
                            <button class="btn-action btn-primary" id="editLeadButton" onclick="enableLeadEdit()">
                                <i class="fas fa-edit"></i> Edit Details
                            </button>
                            <button class="btn-action btn-success" id="saveLeadButton" style="display:none;" onclick="saveLeadChanges()">
                                <i class="fas fa-save"></i> Save Changes
                            </button>
                            
                            @if(auth()->user()->hasDesignation('admin'))
                                <div class="h-8 w-px bg-gray-300 mx-2 hidden md:block"></div>
                                <button class="btn-action btn-secondary" id="loginButton" onclick="showLoginModal(currentLeadId)">Login</button>
                                <button class="btn-action btn-authorize" id="authorizeButton" onclick="showAuthorizeModal(currentLeadId)">Authorize</button>
                                <button class="btn-action btn-approve" id="approveButton" onclick="showApproveModal(currentLeadId)">Approve</button>
                                <button class="btn-action btn-reject" id="rejectButton" onclick="showRejectModal(currentLeadId)">Reject</button>
                                <button class="btn-action btn-disburse" id="disburseButton" onclick="showDisburseModal(currentLeadId)">Disburse</button>
                                <button class="btn-action btn-future" id="futureLeadButton" onclick="showFutureLeadModal(currentLeadId)">Future</button>
                                <button class="btn-action btn-primary" id="forwardOperationsButton" onclick="forwardToOperations(currentLeadId)"><i class="fas fa-share"></i> Ops</button>
                            @endif
                             <button class="btn-action btn-danger" id="deleteButton" onclick="showDeleteModal(currentLeadId)">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>

                </div> </div> </div>
    </div>

    <div class="modal-overlay" id="addDocumentModal">
        <div class="modal-container modal-sm">
            <div class="modal-header">
                <h2 class="modal-title">Add Document</h2>
                <button class="modal-close" onclick="closeDocModal('addDocumentModal')">&times;</button>
            </div>
            <div class="modal-content">
                <form id="addDocumentForm" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="hidden" name="lead_id" id="documentLeadId">
                    <div class="form-group">
                        <label class="info-label">Name</label>
                        <input type="text" id="documentName" name="name" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label class="info-label">Type</label>
                        <select id="documentType" name="type" class="form-control">
                            <option value="id_proof">ID Proof</option>
                            <option value="address_proof">Address Proof</option>
                            <option value="financial">Financial</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="info-label">File</label>
                        <input type="file" id="documentFile" name="document_file" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label class="info-label">Description</label>
                        <textarea id="documentDescription" name="description" class="form-control"></textarea>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn-action btn-secondary" onclick="closeDocModal('addDocumentModal')">Cancel</button>
                        <button type="submit" class="btn-action btn-primary">Upload</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal-overlay" id="authorizeModal">
        <div class="modal-container modal-sm">
            <div class="modal-header">
                <h2 class="modal-title">Authorize Lead</h2>
                <button class="modal-close" onclick="closeDocModal('authorizeModal')">&times;</button>
            </div>
            <div class="modal-content text-center">
                <div class="confirm-icon authorize"><i class="fas fa-check-double"></i></div>
                <h3 class="text-lg font-bold mb-2">Authorize This Lead?</h3>
                <p class="text-gray-600">This action will change the lead status to "Authorized".</p>
            </div>
            <div class="modal-footer">
                <button class="btn-action btn-secondary" onclick="closeDocModal('authorizeModal')">Cancel</button>
                <button class="btn-action btn-authorize" onclick="confirmAuthorize()">Yes, Authorize</button>
            </div>
        </div>
    </div>

    <div class="modal-overlay" id="loginModal">
        <div class="modal-container modal-sm">
            <div class="modal-header">
                <h2 class="modal-title">Login Lead</h2>
                <button class="modal-close" onclick="closeDocModal('loginModal')">&times;</button>
            </div>
            <div class="modal-content text-center">
                 <div class="confirm-icon login"><i class="fas fa-sign-in-alt"></i></div>
                <p class="mb-4 text-gray-600" id="loginModalMessage"></p>
                <input type="text" id="loanAccountNumber" class="form-control" placeholder="Enter Loan Account No">
            </div>
            <div class="modal-footer">
                <button class="btn-action btn-secondary" onclick="closeDocModal('loginModal')">Cancel</button>
                <button class="btn-action btn-primary" onclick="confirmLogin()">Yes, Login</button>
            </div>
        </div>
    </div>

    <div class="modal-overlay" id="approveModal">
        <div class="modal-container modal-sm">
            <div class="modal-header">
                <h2 class="modal-title">Approve Lead</h2>
                <button class="modal-close" onclick="closeDocModal('approveModal')">&times;</button>
            </div>
            <div class="modal-content text-center">
                <div class="confirm-icon approve"><i class="fas fa-check-circle"></i></div>
                <p class="mb-4 text-gray-600" id="approveModalMessage"></p>
                <input type="text" id="loanAccountNumber1" class="form-control" placeholder="Confirm Account No">
            </div>
            <div class="modal-footer">
                <button class="btn-action btn-secondary" onclick="closeDocModal('approveModal')">Cancel</button>
                <button class="btn-action btn-approve" onclick="confirmApprove()">Yes, Approve</button>
            </div>
        </div>
    </div>

    <div class="modal-overlay" id="rejectModal">
        <div class="modal-container modal-sm">
            <div class="modal-header">
                <h2 class="modal-title">Reject Lead</h2>
                <button class="modal-close" onclick="closeDocModal('rejectModal')">&times;</button>
            </div>
            <div class="modal-content text-center">
                <div class="confirm-icon reject"><i class="fas fa-times-circle"></i></div>
                <p class="mb-2 text-gray-600">Please provide a reason:</p>
                <textarea id="rejectionReason" class="form-control" rows="3"></textarea>
            </div>
            <div class="modal-footer">
                <button class="btn-action btn-secondary" onclick="closeDocModal('rejectModal')">Cancel</button>
                <button class="btn-action btn-reject" onclick="confirmReject()">Yes, Reject</button>
            </div>
        </div>
    </div>

    <div class="modal-overlay" id="disburseModal">
        <div class="modal-container modal-sm">
            <div class="modal-header">
                <h2 class="modal-title">Disburse Lead</h2>
                <button class="modal-close" onclick="closeDocModal('disburseModal')">&times;</button>
            </div>
            <div class="modal-content text-center">
                <div class="confirm-icon disburse"><i class="fas fa-rupee-sign"></i></div>
                <p class="mb-4 text-gray-600" id="disburseModalMessage"></p>
                <input type="text" id="loanAccountNumber2" class="form-control" placeholder="Confirm Account No">
            </div>
            <div class="modal-footer">
                <button class="btn-action btn-secondary" onclick="closeDocModal('disburseModal')">Cancel</button>
                <button class="btn-action btn-disburse" onclick="confirmDisburse()">Yes, Disburse</button>
            </div>
        </div>
    </div>

    <div class="modal-overlay" id="futureLeadModal">
        <div class="modal-container modal-sm">
            <div class="modal-header">
                <h2 class="modal-title">Mark as Future</h2>
                <button class="modal-close" onclick="closeDocModal('futureLeadModal')">&times;</button>
            </div>
            <div class="modal-content text-center">
                <div class="confirm-icon future_lead"><i class="fas fa-clock"></i></div>
                <p class="text-gray-600">Mark this lead as Future Lead?</p>
            </div>
            <div class="modal-footer">
                <button class="btn-action btn-secondary" onclick="closeDocModal('futureLeadModal')">Cancel</button>
                <button class="btn-action btn-future" onclick="confirmFutureLead()">Confirm</button>
            </div>
        </div>
    </div>

    <div class="modal-overlay" id="forwardOperationsModal">
        <div class="modal-container modal-sm">
            <div class="modal-header">
                <h2 class="modal-title">Forward to Ops</h2>
                <button class="modal-close" onclick="closeDocModal('forwardOperationsModal')">&times;</button>
            </div>
            <div class="modal-content">
                <p class="mb-2 text-gray-600">Remarks (Optional):</p>
                <textarea id="operation-remarks" class="form-control" rows="3"></textarea>
            </div>
            <div class="modal-footer">
                <button class="btn-action btn-secondary" onclick="closeDocModal('forwardOperationsModal')">Cancel</button>
                <button class="btn-action btn-primary" onclick="submitForwardToOperations()">Forward</button>
            </div>
        </div>
    </div>
    
    <div class="modal-overlay" id="deleteModal">
        <div class="modal-container modal-sm">
            <div class="modal-header bg-red-50">
                <h2 class="modal-title text-red-700">Delete Lead</h2>
                <button class="modal-close text-red-400 hover:text-red-600" onclick="closeDocModal('deleteModal')">&times;</button>
            </div>
            <div class="modal-content text-center">
                <div class="confirm-icon reject"><i class="fas fa-trash-alt"></i></div>
                <p class="text-gray-600">Are you sure? This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button class="btn-action btn-secondary" onclick="closeDocModal('deleteModal')">Cancel</button>
                <button class="btn-action btn-danger" onclick="confirmDelete()">Delete</button>
            </div>
        </div>
    </div>

    <div id="loadingOverlay"><div class="loading-spinner"></div></div>

    <script>
        // Forward Ops Logic
        function forwardToOperations(leadId) {
            window.currentLeadId = leadId;
            document.getElementById('operation-remarks').value = '';
            document.getElementById('forwardOperationsModal').classList.add('active');
        }

        function submitForwardToOperations() {
            const remarks = document.getElementById('operation-remarks').value;
            showLoading(true);
            fetch(`/admin/leads/${currentLeadId}/forward-to-operations-by-admin`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' },
                body: JSON.stringify({ remarks })
            })
            .then(async res => {
                if(!res.ok) { const e = await res.json(); throw new Error(e.message); }
                return res.json();
            })
            .then(d => { showNotification(d.message,'success'); closeDocModal('forwardOperationsModal'); setTimeout(()=>location.reload(),1000); })
            .catch(e => showNotification(e.message,'error'))
            .finally(() => showLoading(false));
        }

        // Helper: Number to Words
        function numberToWords(number) {
            if (number === 0) return 'Zero Rupees';
            const units = ['', 'One', 'Two', 'Three', 'Four', 'Five', 'Six', 'Seven', 'Eight', 'Nine'];
            const teens = ['Ten', 'Eleven', 'Twelve', 'Thirteen', 'Fourteen', 'Fifteen', 'Sixteen', 'Seventeen', 'Eighteen', 'Nineteen'];
            const tens = ['', '', 'Twenty', 'Thirty', 'Forty', 'Fifty', 'Sixty', 'Seventy', 'Eighty', 'Ninety'];
            const thousands = ['', 'Thousand', 'Lakh', 'Crore'];

            function convert(num) {
                if (num === 0) return '';
                if (num < 10) return units[num];
                if (num < 20) return teens[num - 10];
                if (num < 100) return tens[Math.floor(num / 10)] + (num % 10 ? ' ' + units[num % 10] : '');
                return units[Math.floor(num / 100)] + ' Hundred' + (num % 100 ? ' and ' + convert(num % 100) : '');
            }

            let result = '', i = 0;
            while (number > 0) {
                let chunk = number % 1000;
                if (i === 1) chunk = number % 100; // Lakh logic adjustment if needed, keeping simple
                if(i===0) { if(number%1000 > 0) result = convert(number%1000) + result; number=Math.floor(number/1000); }
                else { if(number%100 > 0) result = convert(number%100) + ' ' + thousands[i] + ' ' + result; number=Math.floor(number/100); }
                i++;
            }
            return result.trim() + ' Rupees';
        }

        window.currentLeadId = null;

        document.addEventListener('DOMContentLoaded', function() {
            loadLeadDetails();

            // Name Input Listener
            const nameIn = document.getElementById('leadNameInput');
            if(nameIn) nameIn.addEventListener('input', function() {
                if(!this.disabled) {
                    const val = this.value.toUpperCase();
                    document.getElementById('leadName').textContent = val || 'N/A';
                    document.getElementById('leadInitials').textContent = val.charAt(0) || '-';
                }
            });

            // Amount Input Listener
            const amtIn = document.getElementById('leadAmount');
            if(amtIn) amtIn.addEventListener('input', function() {
                if(!this.disabled) {
                    const val = parseInt(this.value);
                    document.getElementById('leadAmountInWords').textContent = isNaN(val) ? 'N/A' : numberToWords(val);
                }
            });

            // Add Document Form
            document.getElementById('addDocumentForm').addEventListener('submit', function(e){
                e.preventDefault();
                const formData = new FormData(this);
                const lid = document.getElementById('documentLeadId').value;
                showLoading(true);
                fetch(`/admin/leads/${lid}/documents`, {
                    method: 'POST', headers: {'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content}, body: formData
                }).then(res=>res.ok?res.json():res.json().then(e=>{throw new Error(e.message)}))
                  .then(d=>{ showNotification(d.message,'success'); closeDocModal('addDocumentModal'); loadLeadDetails(); })
                  .catch(e=>showNotification(e.message,'error'))
                  .finally(()=>showLoading(false));
            });
        });

        // Main Load Function
        function loadLeadDetails() {
            const params = new URLSearchParams(window.location.search);
            const id = params.get('leadId');
            if(!id) return;
            window.currentLeadId = id;
            document.getElementById('documentLeadId').value = id;

            showLoading(true);
            fetch(`/admin/leads/${id}`, { headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content } })
            .then(r => r.ok ? r.json() : r.text().then(t=>{throw new Error(t)}))
            .then(data => {
                const l = data.lead;
                document.getElementById('documentLeadIdDisplay').textContent = l.id;
                document.getElementById('leadName').textContent = l.name || 'N/A';
                document.getElementById('leadInitials').textContent = l.name ? l.name.charAt(0).toUpperCase() : '-';
                
                // Map inputs
                const map = {
                    'leadNameInput': l.name, 'leadPhone': l.phone, 'leadEmail': l.email, 'leadBank': l.bank_name,
                    'leadAmount': l.lead_amount, 'leadSalary': l.salary, 'leadDob': l.dob, 'leadCompany': l.company_name,
                    'leadState': l.state, 'leadDistrict': l.district, 'leadCity': l.city, 'leadAccountNumber': l.loan_account_number
                };
                for(let k in map) {
                    const el = document.getElementById(k);
                    if(el) el.value = map[k] || '';
                }

                document.getElementById('leadAmountInWords').textContent = l.lead_amount ? numberToWords(parseInt(l.lead_amount)) : 'N/A';
                
                const st = document.getElementById('leadStatus');
                st.textContent = l.status;
                st.className = `status-badge ${l.status}`;

                document.getElementById('leadTypeDisplay').textContent = l.lead_type || 'N/A';
                
                // Voice
                const voice = document.getElementById('leadVoiceRecording');
                voice.innerHTML = l.voice_recording ? `<audio controls class="w-full"><source src="${l.voice_recording}"></audio>` : 'N/A';

                // Employee
                document.getElementById('leadEmployeeName').textContent = l.employee_name || 'N/A';
                document.getElementById('leadTeamLeadName').textContent = l.team_lead_name || 'N/A';

                // Rejection
                const rejBox = document.getElementById('rejectionReasonSection');
                document.getElementById('leadReason').textContent = l.rejection_reason || '';
                rejBox.style.display = l.status === 'rejected' ? 'block' : 'none';

                setButtonStates(l);
                
                // Documents
                const dList = document.getElementById('documentList');
                dList.innerHTML = '';
                if(data.documents && data.documents.length) {
                    data.documents.forEach(d => {
                        dList.innerHTML += `
                        <div class="document-item">
                            <div><label class="text-sm font-bold block">${d.document_name}</label>
                            ${d.filepath ? `<a href="${d.filepath}" target="_blank">View</a>` : '<span>Missing</span>'}
                            </div>
                            ${d.filepath ? `<button class="text-red-500 text-xs" onclick="deleteDocument(${l.id},${d.document_id})"><i class="fas fa-trash"></i></button>` : ''}
                        </div>`;
                    });
                } else dList.innerHTML = '<p class="text-gray-400 italic">No documents.</p>';

                // Followups
                const fList = document.getElementById('followupList');
                fList.innerHTML = '';
                if(data.followUps && data.followUps.length) {
                    data.followUps.forEach(f => {
                        fList.innerHTML += `<div class="mb-3 pb-2 border-b border-gray-100">
                            <p class="mb-1">${f.message}</p>
                            <span class="text-xs text-gray-400">${f.user?.name} - ${new Date(f.timestamp).toLocaleDateString()}</span>
                        </div>`;
                    });
                } else fList.innerHTML = '<div class="text-center p-2 text-gray-400">No history</div>';
            })
            .catch(e => { console.error(e); showNotification(e.message, 'error'); })
            .finally(() => showLoading(false));
        }

        // Logic Functions (Same as before)
        function setButtonStates(lead) {
            const disabled = ['disbursed','future_lead'].includes(lead.status);
            ['editLeadButton','saveLeadButton','loginButton','authorizeButton','approveButton','rejectButton','disburseButton','futureLeadButton','addDocumentButton'].forEach(id => {
                const b = document.getElementById(id); if(b) b.disabled = disabled;
            });
        }

        function enableLeadEdit() {
            document.querySelectorAll('.editable-field').forEach(i => i.disabled = false);
            document.getElementById('editLeadButton').style.display='none';
            document.getElementById('saveLeadButton').style.display='inline-flex';
            // Show dropdowns logic here if needed (e.g. state/city) - simplified for brevity, paste full logic if needed
            document.getElementById('leadState').style.display = 'none';
            document.getElementById('leadStateDropdown').style.display = 'block';
            loadStates(); // ensure states loaded
        }

        function disableLeadEdit() {
            document.querySelectorAll('.editable-field').forEach(i => i.disabled = true);
            document.getElementById('editLeadButton').style.display='inline-flex';
            document.getElementById('saveLeadButton').style.display='none';
            document.getElementById('leadState').style.display = 'block';
            document.getElementById('leadStateDropdown').style.display = 'none';
        }

        function saveLeadChanges() {
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

            fetch(`/admin/creditcardleads/${leadId}/update`, {
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

        
        function goBackAndReload() {
            if(document.referrer) window.location.href = document.referrer;
            else window.history.back();
        }

        // Modals
        function showLoginModal(id) { window.currentLeadId = id; document.getElementById('loginModal').classList.add('active'); }
        function confirmLogin() { updateStatus(window.currentLeadId, 'login', null, document.getElementById('loanAccountNumber').value); }
        
        function showAuthorizeModal(id) { window.currentLeadId = id; document.getElementById('authorizeModal').classList.add('active'); }
        function confirmAuthorize() { updateStatus(window.currentLeadId, 'authorized'); }

        function showApproveModal(id) { window.currentLeadId = id; document.getElementById('approveModal').classList.add('active'); }
        function confirmApprove() { updateStatus(window.currentLeadId, 'approved', null, document.getElementById('loanAccountNumber1').value); }

        function showRejectModal(id) { window.currentLeadId = id; document.getElementById('rejectModal').classList.add('active'); }
        function confirmReject() { updateStatus(window.currentLeadId, 'rejected', document.getElementById('rejectionReason').value); }

        function showDisburseModal(id) { window.currentLeadId = id; document.getElementById('disburseModal').classList.add('active'); }
        function confirmDisburse() { updateStatus(window.currentLeadId, 'disbursed', null, document.getElementById('loanAccountNumber2').value); }

        function showFutureLeadModal(id) { window.currentLeadId = id; document.getElementById('futureLeadModal').classList.add('active'); }
        function confirmFutureLead() { updateStatus(window.currentLeadId, 'future_lead'); }

        function showDeleteModal(id) { window.currentLeadId = id; document.getElementById('deleteModal').classList.add('active'); }
        function confirmDelete() {
            showLoading(true);
            fetch(`/admin/leads/${window.currentLeadId}`, { method:'DELETE', headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content } })
            .then(r=>r.json()).then(d=>{ showNotification('Deleted','success'); closeDocModal('deleteModal'); window.location.href='/admin/dashboard'; })
            .finally(()=>showLoading(false));
        }

        function updateStatus(id, status, reason=null, acc=null) {
            showLoading(true);
            fetch(`/admin/leads/${id}/status`, {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                body: JSON.stringify({ status, reason, loan_account_number: acc })
            }).then(r=>r.json()).then(d=> {
                showNotification(d.message,'success');
                ['loginModal','authorizeModal','approveModal','rejectModal','disburseModal','futureLeadModal'].forEach(m=>closeDocModal(m));
                loadLeadDetails();
            }).catch(e=>showNotification(e.message,'error')).finally(()=>showLoading(false));
        }

        function closeDocModal(id) { document.getElementById(id).classList.remove('active'); }
        function showLoading(b) { document.getElementById('loadingOverlay').classList.toggle('active', b); }
        function showNotification(msg, type) {
            const n = document.createElement('div'); n.className = `notification ${type}`; n.innerHTML = msg;
            document.body.appendChild(n); setTimeout(()=>n.remove(), 3000);
        }

          async function loadStates() {
            try {
                console.log('Loading states...');

                const response = await fetch('/admin/location/states', {
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

                const response = await fetch(`/admin/location/districts/${stateId}`, {
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

                const response = await fetch(`/admin/location/cities/${districtId}`, {
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

            fetch(`/admin/leads/${leadId}/documents/${documentId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(res => {
                if (!res.ok) {
                    return res.json().then(err => { throw new Error(err.message || `HTTP ${res.status}`); });
                }
                return res.json();
            })
            .then(data => {
                showNotification('Document deleted successfully.', 'success');
                loadLeadDetails();
            })
            .catch(error => {
                console.error("Error deleting document:", error);
                showNotification(`Failed to delete document: ${error.message}`, 'error');
            })
            .finally(() => showLoading(false));
        }

    </script>
</body>
</html>