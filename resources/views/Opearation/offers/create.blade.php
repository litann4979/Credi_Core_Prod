<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Operations Notification Portal</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap');

        :root {
            --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --secondary-gradient: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            --success-gradient: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            --warning-gradient: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
            --glass-bg: rgba(255, 255, 255, 0.25);
            --glass-border: rgba(255, 255, 255, 0.18);
            --shadow-light: 0 8px 32px 0 rgba(31, 38, 135, 0.37);
            --shadow-medium: 0 15px 35px rgba(31, 38, 135, 0.2);
            --shadow-heavy: 0 25px 50px rgba(31, 38, 135, 0.25);
        }

        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            min-height: 100vh;
        }

        .main-content {
            margin-left: 280px;
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(20px);
            min-height: 100vh;
        }

        .modern-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 18px; /* reduced from 24px */
            box-shadow: var(--shadow-medium);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            overflow: hidden;
        }

        .modern-card:hover {
            transform: translateY(-8px);
            box-shadow: var(--shadow-heavy);
            border-color: rgba(255, 255, 255, 0.3);
        }

        .card-header {
            background: var(--primary-gradient);
            color: white;
            padding: 1.2rem 1.5rem; /* reduced from 2rem */
            border-radius: 18px 18px 0 0;
            display: flex;
            align-items: center;
            gap: 1rem;
            font-weight: 700;
            font-size: 1.15rem;
            box-shadow: 0 4px 20px rgba(102, 126, 234, 0.3);
        }

        .form-input {
            background: rgba(255, 255, 255, 0.9);
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-radius: 16px;
            padding: 1rem 1.25rem;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            backdrop-filter: blur(10px);
            color: #374151;
            font-weight: 500;
            font-size: 0.95rem;
        }

        .form-input:focus {
            outline: none;
            border-color: #667eea;
            background: rgba(255, 255, 255, 0.95);
            box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1), 0 8px 25px rgba(102, 126, 234, 0.15);
            transform: translateY(-2px);
        }

        .form-textarea {
            background: rgba(255, 255, 255, 0.9);
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-radius: 16px;
            padding: 1rem 1.25rem;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            backdrop-filter: blur(10px);
            color: #374151;
            font-weight: 500;
            font-size: 0.95rem;
            resize: vertical;
            min-height: 120px;
        }

        .form-textarea:focus {
            outline: none;
            border-color: #667eea;
            background: rgba(255, 255, 255, 0.95);
            box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1), 0 8px 25px rgba(102, 126, 234, 0.15);
            transform: translateY(-2px);
        }

        .file-input {
            background: rgba(255, 255, 255, 0.9);
            border: 2px dashed rgba(102, 126, 234, 0.3);
            border-radius: 16px;
            padding: 2rem;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            backdrop-filter: blur(10px);
            text-align: center;
            cursor: pointer;
        }

        .file-input:hover {
            border-color: #667eea;
            background: rgba(255, 255, 255, 0.95);
            transform: translateY(-2px);
        }

        .btn-primary {
            background: var(--primary-gradient);
            border: none;
            color: white;
            font-weight: 700;
            border-radius: 16px;
            padding: 1rem 2rem;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.75rem;
            font-size: 1rem;
            cursor: pointer;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 35px rgba(102, 126, 234, 0.4);
            filter: brightness(1.1);
        }

        .form-label {
            display: block;
            font-size: 0.875rem;
            font-weight: 700;
            color: #374151;
            margin-bottom: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .alert-success {
            background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
            border: 2px solid rgba(16, 185, 129, 0.3);
            color: #065f46;
            padding: 1rem 1.5rem;
            border-radius: 16px;
            margin-bottom: 1.5rem;
            backdrop-filter: blur(10px);
            box-shadow: 0 8px 25px rgba(16, 185, 129, 0.15);
            display: flex;
            align-items: center;
            gap: 0.75rem;
            font-weight: 600;
        }

        .alert-error {
            background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
            border: 2px solid rgba(239, 68, 68, 0.3);
            color: #991b1b;
            padding: 1rem 1.5rem;
            border-radius: 16px;
            margin-bottom: 1.5rem;
            backdrop-filter: blur(10px);
            box-shadow: 0 8px 25px rgba(239, 68, 68, 0.15);
            display: flex;
            align-items: center;
            gap: 0.75rem;
            font-weight: 600;
        }

        .table-modern {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 20px;
            overflow: hidden;
            box-shadow: var(--shadow-medium);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .table-modern thead {
            background: var(--primary-gradient);
        }

        .table-modern thead th {
            color: white;
            font-weight: 700;
            padding: 1.5rem 1.5rem;
            font-size: 0.75rem;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            text-align: left;
            border: none;
        }

        .table-modern tbody tr {
            transition: all 0.3s ease;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
        }

        .table-modern tbody tr:last-child {
            border-bottom: none;
        }

        .table-modern tbody tr:hover {
            background: rgba(102, 126, 234, 0.08);
            transform: scale(1.01);
        }

        .table-modern tbody td {
            padding: 1.5rem 1.5rem;
            font-weight: 500;
            color: #475569;
            border: none;
        }

        .page-title {
            font-size: 2.5rem;
            font-weight: 800;
            color: white;
            margin-bottom: 2rem;
            text-align: center;
            text-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
            background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .section-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .attachment-link {
            background: linear-gradient(135deg, #e0e7ff 0%, #c7d2fe 100%);
            color: #3730a3;
            padding: 0.5rem 1rem;
            border-radius: 12px;
            text-decoration: none;
            font-weight: 600;
            font-size: 0.875rem;
            display: inline-block;
            margin: 0.25rem;
            transition: all 0.3s ease;
            border: 1px solid rgba(55, 48, 163, 0.2);
        }

        .attachment-link:hover {
            background: linear-gradient(135deg, #c7d2fe 0%, #a5b4fc 100%);
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(55, 48, 163, 0.2);
        }

        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
            color: #6b7280;
        }

        .empty-state i {
            font-size: 4rem;
            margin-bottom: 1rem;
            opacity: 0.5;
        }

        .form-section {
            background: rgba(255, 255, 255, 0.98);
            border-radius: 20px;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: var(--shadow-light);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        .error-text {
            color: #dc2626;
            font-size: 0.875rem;
            font-weight: 600;
            margin-top: 0.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }

        ::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb {
            background: var(--primary-gradient);
            border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb:hover {
            filter: brightness(1.2);
        }

        @media (max-width: 1024px) {
            .main-content {
                margin-left: 0;
            }
        }

        @media (max-width: 768px) {
            .page-title {
                font-size: 2rem;
            }
            .card-header {
                padding: 1.5rem;
                font-size: 1.25rem;
            }
            .form-section {
                padding: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="flex min-h-screen">
        <!-- Sidebar -->
        @include('Opearation.Components.sidebar')

        <!-- Main Content -->
        <div class="main-content flex-1 flex flex-col">
            <!-- Header -->
            @include('Opearation.Components.header',['title'=>'offers', 'subtitle' => 'Send and manage offers'])
        <div class="dashboard-container">

            <!-- Content -->
            <main class="flex-1 p-8 pt-24">
                <div class="max-w-4xl mx-auto">
                    {{-- <h1 class="page-title">Operations Notification Portal</h1> --}}

                    <!-- Notification Form Card -->
                    <div class="modern-card mb-8">
                        <div class="card-header">
                            <i class="fas fa-paper-plane"></i>
                            <span>Send Offer Notification</span>
                        </div>

                        <div class="p-4">
                            @if (session('success'))
                                <div class="alert-success">
                                    <i class="fas fa-check-circle text-xl"></i>
                                    <span>{{ session('success') }}</span>
                                </div>
                            @endif

                            @if (session('error'))
                                <div class="alert-error">
                                    <i class="fas fa-exclamation-circle text-xl"></i>
                                    <span>{{ session('error') }}</span>
                                </div>
                            @endif

                            <form action="{{ route('operations.offers.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                                @csrf

                                <div>
                                    <label for="title" class="form-label">
                                        <i class="fas fa-heading mr-2"></i>
                                        Title
                                    </label>
                                    <input type="text" name="title" id="title" value="{{ old('title') }}"
                                           class="form-input w-full" required
                                           placeholder="Enter notification title...">
                                    @error('title')
                                        <div class="error-text">
                                            <i class="fas fa-exclamation-triangle"></i>
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <div>
                                    <label for="description" class="form-label">
                                        <i class="fas fa-align-left mr-2"></i>
                                        Description
                                    </label>
                                    <textarea name="description" id="description" rows="6"
                                              class="form-textarea w-full" required
                                              placeholder="Enter detailed description...">{{ old('description') }}</textarea>
                                    @error('description')
                                        <div class="error-text">
                                            <i class="fas fa-exclamation-triangle"></i>
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <div>
                                    <label for="attachment" class="form-label">
                                        <i class="fas fa-paperclip mr-2"></i>
                                        Attachments
                                    </label>
                                    <div class="file-input">
                                        <i class="fas fa-cloud-upload-alt text-4xl text-indigo-400 mb-4"></i>
                                        <p class="text-lg font-semibold text-gray-700 mb-2">Drop files here or click to browse</p>
                                        <p class="text-sm text-gray-500 mb-4">Support for multiple files</p>
                                        <input type="file" name="attachment[]" id="attachment" multiple
                                               class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                                    </div>
                                    @error('attachment')
                                        <div class="error-text">
                                            <i class="fas fa-exclamation-triangle"></i>
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <div class="pt-4">
                                    <button type="submit" class="btn-primary w-full">
                                        <i class="fas fa-rocket"></i>
                                        <span>Send Notification</span>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                 <!-- Recent Offers Card -->
                    <div class="modern-card">
                        <div class="card-header">
                            <i class="fas fa-history"></i>
                            <span>Recent Offers Sent</span>
                        </div>

                        <div class="p-8">
                            @if ($offers === null || $offers->isEmpty())
                                <div class="empty-state">
                                    <i class="fas fa-inbox"></i>
                                    <h3 class="text-xl font-semibold mb-2">No offers sent yet</h3>
                                    <p class="text-gray-500">Your sent notifications will appear here</p>
                                </div>
                            @else
                                <div class="overflow-x-auto">
                                    <table class="table-modern w-full">
                                        <thead>
                                            <tr>
                                                <th>
                                                    <i class="fas fa-heading mr-2"></i>
                                                    Title
                                                </th>
                                                <th>
                                                    <i class="fas fa-align-left mr-2"></i>
                                                    Description
                                                </th>
                                                <th>
                                                    <i class="fas fa-paperclip mr-2"></i>
                                                    Attachments
                                                </th>
                                                <th>
                                                    <i class="fas fa-users mr-2"></i>
                                                    Recipients
                                                </th>
                                                <th>
                                                    <i class="fas fa-clock mr-2"></i>
                                                    Sent At
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($offers as $offer)
                                                <tr>
                                                    <td class="font-semibold text-gray-900">
                                                        {{ $offer->title }}
                                                    </td>
                                                    <td class="text-gray-700">
                                                        {{ Str::limit($offer->description, 100) }}
                                                    </td>
                                                    <td>
                                                        @if ($offer->attachment)
                                                            <div class="flex flex-wrap">
                                                                @foreach (json_decode($offer->attachment, true) as $attachment)
                                                                    <a href="{{ Storage::url($attachment) }}"
                                                                       class="attachment-link" target="_blank">

                                                                        {{ basename($attachment) }}
                                                                    </a>
                                                                @endforeach
                                                            </div>
                                                        @else
                                                            <span class="text-gray-400 italic">
                                                                <i class="fas fa-minus mr-1"></i>
                                                                None
                                                            </span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-blue-100 text-blue-800">
                                                            <i class="fas fa-user-friends mr-1"></i>
                                                            {{ $offer->notifications->count() }}
                                                        </span>
                                                    </td>
                                                    <td class="font-medium text-gray-600">
                                                        <i class="fas fa-calendar-alt mr-2 text-gray-400"></i>
                                                        {{ $offer->created_at->format('d M Y H:i') }}
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endif
                        </div>
                    </div>
            </main>
        </div>
    </div>

    <script>
        // File input enhancement
        document.getElementById('attachment').addEventListener('change', function(e) {
            const fileInput = e.target;
            const fileContainer = fileInput.closest('.file-input');
            const files = Array.from(fileInput.files);

            if (files.length > 0) {
                const fileNames = files.map(file => file.name).join(', ');
                fileContainer.querySelector('p').textContent = `Selected: ${fileNames}`;
                fileContainer.style.borderColor = '#667eea';
                fileContainer.style.background = 'rgba(102, 126, 234, 0.05)';
            }
        });

        // Form validation enhancement
        document.querySelector('form').addEventListener('submit', function(e) {
            const submitBtn = e.target.querySelector('button[type="submit"]');
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i><span>Sending...</span>';
            submitBtn.disabled = true;
        });
    </script>
</body>
</html>
