{{-- resources/views/dashboard.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>Performance Dashboard | Loan DSA</title>
    <link rel="icon" type="image/png" href="{{ asset('logo1.png') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Outfit:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Outfit', 'Inter', system-ui, -apple-system, sans-serif;
            background-color: #0b1d3a;
            background-image: linear-gradient(165deg, #0b1d3a 0%, #0a1a32 100%);
            background-attachment: fixed;
            min-height: 100vh;
            letter-spacing: 0.01em;
            font-feature-settings: "kern" 1, "liga" 1;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        /* LED / large-display: hero & section titles */
        .font-dashboard-display {
            font-family: 'Outfit', 'Inter', sans-serif;
            font-weight: 800;
            letter-spacing: 0.06em;
            text-rendering: geometricPrecision;
        }

        .font-dashboard-sub {
            font-family: 'Outfit', 'Inter', sans-serif;
            font-weight: 600;
            letter-spacing: 0.04em;
        }

        /* Solid card panels - no glass, no blur */
        .glass-card {
            background: #0f2a44;
            border: 1px solid #1e3a6b;
            border-radius: 20px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.3);
            transition: all 0.2s ease;
        }

        .glass-card:hover {
            border-color: #2c4c7a;
            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.4);
        }

        /* Custom scrollbar - solid */
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }

        ::-webkit-scrollbar-track {
            background: #1e2f4a;
            border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb {
            background: #3b5a8c;
            border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #4c6e9e;
        }

        /* Table rows: compact, textured, premium TV-style */
        .table-row {
            --row-base: #0a1b35;
            --row-alt: #123256;
            --row-sep: rgba(168, 194, 230, 0.15);
            transition: none;
        }

        .table-row td {
            padding-top: 0.55rem !important;
            padding-bottom: 0.55rem !important;
            line-height: 1.15;
            border-bottom: 1px solid var(--row-sep);
            background:
                linear-gradient(90deg, rgba(255, 255, 255, 0.03) 0%, rgba(255, 255, 255, 0) 45%, rgba(255, 255, 255, 0.04) 100%),
                radial-gradient(circle at 20% 25%, rgba(255, 255, 255, 0.035) 0%, rgba(255, 255, 255, 0) 45%),
                repeating-linear-gradient(18deg, rgba(255, 255, 255, 0.02) 0 2px, rgba(255, 255, 255, 0) 2px 6px),
                linear-gradient(180deg, var(--row-alt) 0%, var(--row-base) 100%);
            background-blend-mode: screen, soft-light, soft-light, normal;
        }

        tbody tr.table-row:nth-child(odd) td {
            --row-base: #071830;
            --row-alt: #0e2949;
        }

        tbody tr.table-row:nth-child(even) td {
            --row-base: #0d2442;
            --row-alt: #16375f;
        }

        /* Keep hover minimal to preserve row colors */
        tbody tr.table-row:hover td {
            filter: brightness(1.02);
        }

        /* Status badge styles - solid colors, no transparency */
        .badge-excellent {
            background: #15803d;
            color: white;
            border: none;
        }

        .badge-good {
            background: #ca8a04;
            color: black;
            border: none;
        }

        .badge-average {
            background: #ea580c;
            color: white;
            border: none;
        }

        .badge-low {
            background: #b91c1c;
            color: white;
            border: none;
        }

        /* Progress bar styling - solid vibrant */
        .progress-bar {
            transition: width 0.8s ease-out;
        }

        /* Floating animation for icons */
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-3px); }
        }

        .float-icon {
            animation: float 3s ease-in-out infinite;
        }

        /* Rank medal styling */
        .rank-medal {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 36px;
            height: 36px;
            border-radius: 12px;
            font-weight: 800;
            background: #1e3a5f;
            color: #ffd966;
        }

        /* Live indicator - solid */
        .live-dot {
            width: 8px;
            height: 8px;
            background: #10b981;
            border-radius: 50%;
            display: inline-block;
            animation: pulse 1.5s ease-in-out infinite;
            box-shadow: 0 0 4px #10b981;
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.7; transform: scale(1.15); }
        }

        /* Live activity item blink animation for new items */
        @keyframes subtleBlink {
            0%, 100% { background: #1e3a5f; border-left-color: #10b981; }
            50% { background: #2a4a6e; border-left-color: #10b981; }
        }

        .live-activity-item {
            animation: subtleBlink 2s ease-in-out 3;
            border-left: 3px solid #10b981;
        }

        /* TV-optimized font sizes with responsive scaling */
        .tv-title {
            font-size: 1.75rem;
            font-weight: 800;
        }

        .tv-stat {
            font-size: 2.5rem;
            font-weight: 800;
        }

        /* Dynamic scaling for LED TV (based on viewport width) */
        @media (min-width: 1600px) {
            .tv-title { font-size: 2rem; }
            .tv-stat { font-size: 3rem; }
            .glass-card { padding: 1.75rem; }
            .activity-text { font-size: 1.1rem; }
        }

        @media (min-width: 1920px) {
            .tv-title { font-size: 2.25rem; }
            .tv-stat { font-size: 3.5rem; }
            .glass-card { padding: 2rem; }
            .activity-text { font-size: 1.2rem; }
            .activity-icon { font-size: 2rem; }
        }

        @media (min-width: 2560px) {
            .tv-title { font-size: 2.5rem; }
            .tv-stat { font-size: 4rem; }
            .glass-card { padding: 2.25rem; }
            .activity-text { font-size: 1.3rem; }
        }

        /* Activity item hover effect */
        .activity-item {
            transition: all 0.2s ease;
            cursor: pointer;
            background: #0f2a44;
        }
        .activity-item:hover {
            transform: translateX(5px);
            background: #1a3a5a;
        }

        /* Target alert card styling */
        .target-alert {
            transition: all 0.2s ease;
            background: #0f2a44;
        }
        .target-alert:hover {
            transform: translateX(5px);
            background: #1a3a5a;
        }

        /* Section color themes - solid backgrounds */
        .theme-table {
            background: transparent !important;
            border: none !important;
            border-radius: 0 !important;
            box-shadow: none !important;
        }
        .theme-table:hover {
            border: none !important;
            box-shadow: none !important;
        }
        .theme-live {
            background: #0c2340;
            border-color: #2c7a6e;
        }
        .theme-target {
            background: #0c2340;
            border-color: #8b5cf6;
        }
        .theme-performance {
            background: #0c2340;
            border-color: #3b82f6;
        }
        .theme-leave {
            background: #0c2340;
            border-color: #ec489a;
        }

        /* KPI Cards - solid bold gradients */
        .kpi-a {
            background: linear-gradient(135deg, #0f766e 0%, #0b3b3a 100%);
            border-color: #2dd4bf;
        }
        .kpi-b {
            background: linear-gradient(135deg, #7c3aed 0%, #5b21b6 100%);
            border-color: #c084fc;
        }
        .kpi-c {
            background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%);
            border-color: #60a5fa;
        }
        .kpi-d {
            background: linear-gradient(135deg, #ea580c 0%, #9a3412 100%);
            border-color: #fbbf24;
        }

        /* Table header: higher contrast and clearly visible */
        thead tr {
            background: linear-gradient(180deg, #173e6d 0%, #12345b 100%);
            border-bottom: 1px solid rgba(129, 170, 221, 0.45);
        }

        thead th {
            color: #d7e9ff !important;
            text-shadow: 0 1px 0 rgba(0, 0, 0, 0.35);
        }

        /* Alternating row colors handled on table-row td */

        /* Avatar image styling */
        .avatar-img {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #3b82f6;
        }

        /* Leader KPI card — same style as table avatars, larger */
        .leader-avatar-img {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #3b82f6;
            display: block;
        }
    </style>
</head>
<body class="p-4 md:p-6 lg:p-6">

    {{-- Main Container with dynamic scaling --}}
    <div class="max-w-[1920px] mx-auto">

        {{-- HEADER --}}
        <div class="glass-card px-8 py-6 mb-6 flex flex-wrap justify-between items-center">
            <div>
                <h1 class="font-dashboard-display text-3xl md:text-4xl lg:text-5xl text-white">
                    DAILY PERFORMANCE DASHBOARD
                </h1>
                <p class="font-dashboard-sub text-blue-300 text-sm md:text-base mt-1.5 flex items-center gap-3">
                    Loan DSA – Live Tracking System
                    <span class="flex items-center gap-1 text-sky-200/90 text-xs font-semibold tracking-wide">
                        <span class="live-dot"></span>
                        LIVE
                    </span>
                </p>
            </div>
            <div class="mt-3 md:mt-0 md:text-right w-full md:w-auto">
                <div class="text-white text-xl md:text-2xl lg:text-3xl font-semibold" id="current-date">24 April 2024</div>
                <div class="text-gray-400 text-sm font-mono">Real-time metrics</div>
                <div class="mt-3 flex flex-wrap items-center justify-start md:justify-end gap-2.5">
                    <label class="sr-only" for="header-month-filter">Month</label>
                    <select id="header-month-filter" class="bg-[#0f2a44] border border-[#2c4c7a] text-gray-100 text-sm md:text-base rounded-lg px-3.5 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500/50">
                        <option>Jan</option>
                        <option>Feb</option>
                        <option>Mar</option>
                        <option>Apr</option>
                        <option>May</option>
                        <option>Jun</option>
                        <option>Jul</option>
                        <option>Aug</option>
                        <option>Sep</option>
                        <option>Oct</option>
                        <option>Nov</option>
                        <option>Dec</option>
                    </select>

                    <label class="sr-only" for="header-year-filter">Year</label>
                    <select id="header-year-filter" class="bg-[#0f2a44] border border-[#2c4c7a] text-gray-100 text-sm md:text-base rounded-lg px-3.5 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500/50">
                        <option>2023</option>
                        <option>2024</option>
                        <option>2025</option>
                        <option>2026</option>
                        <option>2027</option>
                        <option>2028</option>
                        <option>2029</option>
                        <option>2030</option>
                    </select>

                    <label class="sr-only" for="header-date-filter">Date</label>
                    <input id="header-date-filter" type="date" class="bg-[#0f2a44] border border-[#2c4c7a] text-gray-100 text-sm md:text-base rounded-lg px-3.5 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500/50" />

                    <button
                        type="button"
                        class="bg-[#13355a] border border-[#2c4c7a] text-gray-100 text-sm md:text-base rounded-lg px-3.5 py-2.5 hover:bg-[#1a3d66] transition-colors"
                        onclick="document.getElementById('header-month-filter').selectedIndex = 0; document.getElementById('header-year-filter').selectedIndex = 0; document.getElementById('header-date-filter').value = '';"
                    >
                        Clear
                    </button>
                </div>
            </div>
        </div>

        {{-- TOP SUMMARY CARDS (4 cards) --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 mb-4">
            <!-- Total Leads -->
            <div class="glass-card kpi-a p-3 hover:scale-[1.01] transition-all cursor-pointer">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-gray-200 font-semibold uppercase tracking-wider text-sm">Total Leads</span>
                    <span class="text-2xl float-icon">📊</span>
                </div>
                <div class="text-4xl md:text-5xl text-white font-black leading-tight" id="total-leads">125</div>
                <div class="text-emerald-300 text-xs mt-1.5 flex items-center gap-1">↑ 8% vs last week</div>
            </div>
            <!-- Total Disbursement -->
            <div class="glass-card kpi-b p-3 hover:scale-[1.01] transition-all cursor-pointer">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-gray-200 font-semibold uppercase tracking-wider text-sm">Disbursement</span>
                    <span class="text-2xl float-icon">💰</span>
                </div>
                <div class="text-4xl md:text-5xl text-white font-black leading-tight" id="total-disbursement">₹32,50,000</div>
                <div class="text-emerald-300 text-xs mt-1.5">Target: ₹40L (81%)</div>
            </div>
            <!-- Average Score -->
            <div class="glass-card kpi-c p-3 hover:scale-[1.01] transition-all cursor-pointer">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-gray-200 font-semibold uppercase tracking-wider text-sm">Avg Score</span>
                    <span class="text-2xl float-icon">🎯</span>
                </div>
                <div class="text-4xl md:text-5xl text-white font-black leading-tight">76<span class="text-xl text-gray-300">/100</span></div>
                <div class="text-blue-300 text-xs mt-1.5">Team performance: B+</div>
            </div>
            <!-- Leader of the Day -->
            <div class="glass-card kpi-d p-3 sm:p-4 hover:scale-[1.01] transition-all cursor-pointer relative overflow-hidden flex items-center justify-between gap-3 min-h-[120px]">
                <div class="relative z-10 flex-1 min-w-0 pr-2">
                    <div class="flex items-center gap-1.5 mb-1">
                        <span class="text-lg" aria-hidden="true">🏆</span>
                        <span class="text-amber-200 font-semibold uppercase tracking-wider text-sm">Leader</span>
                    </div>
                    <div class="text-3xl sm:text-4xl md:text-5xl text-white font-black leading-tight truncate" id="leader-name">Rahul</div>
                    <div class="text-amber-200 text-xs mt-1.5 flex items-center gap-1 flex-wrap">
                        <span class="tracking-tight">★★★★★</span>
                        <span class="text-white/80 text-xs font-medium">5.0 rating</span>
                    </div>
                </div>
                {{-- Same portrait URLs as employee table; initials fallback if image fails --}}
                <div class="relative z-10 flex-shrink-0 flex flex-col items-center justify-center">
                    <div class="relative">
                        <span class="absolute -top-1 -right-1 z-20 text-lg leading-none drop-shadow-md pointer-events-none" aria-hidden="true" title="Top performer">⭐</span>
                        <div
                            id="leader-avatar"
                            class="relative w-16 h-16 sm:w-20 sm:h-20 rounded-full overflow-hidden bg-amber-400/35 shadow-inner"
                            role="img"
                            aria-label="Leader"
                        >
                            <img
                                id="leader-avatar-img"
                                src="https://randomuser.me/api/portraits/men/32.jpg"
                                alt=""
                                class="leader-avatar-img"
                                width="80"
                                height="80"
                                loading="eager"
                                decoding="async"
                            />
                            <span id="leader-initials" class="absolute inset-0 hidden text-white font-black text-xl sm:text-2xl tracking-tight select-none bg-amber-500/50">RA</span>
                        </div>
                    </div>
                    <div class="text-2xl leading-none opacity-25 -mt-0.5 select-none" aria-hidden="true">🏆</div>
                </div>
                <div class="absolute -bottom-6 -right-2 text-8xl opacity-[0.12] pointer-events-none select-none" aria-hidden="true">🏆</div>
            </div>
        </div>

        {{-- MAIN GRID: Employee Table (Left) + Enhanced Right Panel --}}
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 mb-6 items-stretch">
            {{-- LEFT: Employee Table --}}
            <div class="lg:col-span-8 h-full">
                <div class="glass-card theme-table overflow-hidden h-full flex flex-col">
                    <div class="overflow-x-auto overflow-y-auto flex-1">
                        <table class="w-full min-w-[820px] table-auto">
                        <thead>
                            <tr class="border-b border-[#2a4b7a] bg-[#0a1733]">
                                <th class="px-6 py-4 text-left text-blue-200 font-bold text-sm uppercase tracking-wider">Rank</th>
                                <th class="px-6 py-4 text-left text-blue-200 font-bold text-sm uppercase tracking-wider">Name</th>
                                <th class="px-6 py-4 text-right text-blue-200 font-bold text-sm uppercase tracking-wider">Disbursement</th>
                                <th class="px-6 py-4 text-center text-blue-200 font-bold text-sm uppercase tracking-wider">% Achieved</th>
                                <th class="px-6 py-4 text-center text-blue-200 font-bold text-sm uppercase tracking-wider">Score</th>
                                <th class="px-6 py-4 text-center text-blue-200 font-bold text-sm uppercase tracking-wider">Status</th>
                               </tr>
                        </thead>
                        <tbody>
                            @php
                                $employees = [
                                    ['rank' => 1, 'name' => 'Rahul', 'disbursement' => 320000, 'achieved' => 92, 'score' => 94, 'status' => 'Excellent', 'avatar' => 'https://randomuser.me/api/portraits/men/32.jpg'],
                                    ['rank' => 2, 'name' => 'Priya', 'disbursement' => 280000, 'achieved' => 85, 'score' => 88, 'status' => 'Good', 'avatar' => 'https://randomuser.me/api/portraits/women/68.jpg'],
                                    ['rank' => 3, 'name' => 'Amit', 'disbursement' => 210000, 'achieved' => 70, 'score' => 75, 'status' => 'Good', 'avatar' => 'https://randomuser.me/api/portraits/men/45.jpg'],
                                    ['rank' => 4, 'name' => 'Aditya', 'disbursement' => 150000, 'achieved' => 40, 'score' => 55, 'status' => 'Average', 'avatar' => 'https://randomuser.me/api/portraits/men/52.jpg'],
                                    ['rank' => 5, 'name' => 'Suresh', 'disbursement' => 120000, 'achieved' => 35, 'score' => 55, 'status' => 'Average', 'avatar' => 'https://randomuser.me/api/portraits/men/74.jpg'],
                                    ['rank' => 6, 'name' => 'Asif', 'disbursement' => 150000, 'achieved' => 28, 'score' => 55, 'status' => 'Low', 'avatar' => 'https://randomuser.me/api/portraits/men/91.jpg'],
                                    ['rank' => 7, 'name' => 'Ankit', 'disbursement' => 155000, 'achieved' => 25, 'score' => 44, 'status' => 'Low', 'avatar' => 'https://randomuser.me/api/portraits/men/22.jpg'],
                                    ['rank' => 8, 'name' => 'Rina', 'disbursement' => 160000, 'achieved' => 20, 'score' => 40, 'status' => 'Low', 'avatar' => 'https://randomuser.me/api/portraits/women/44.jpg'],
                                    ['rank' => 9, 'name' => 'Neha', 'disbursement' => 500000, 'achieved' => 17, 'score' => 30, 'status' => 'Low', 'avatar' => 'https://randomuser.me/api/portraits/women/23.jpg'],
                                    ['rank' => 10, 'name' => 'Ramesh', 'disbursement' => 500000, 'achieved' => 20, 'score' => 30, 'status' => 'Low', 'avatar' => 'https://randomuser.me/api/portraits/men/67.jpg'],
                                    ['rank' => 11, 'name' => 'Asif', 'disbursement' => 40000, 'achieved' => 20, 'score' => 30, 'status' => 'Low', 'avatar' => 'https://randomuser.me/api/portraits/men/88.jpg'],
                                    ['rank' => 12, 'name' => 'Ankit', 'disbursement' => 155000, 'achieved' => 25, 'score' => 44, 'status' => 'Low', 'avatar' => 'https://randomuser.me/api/portraits/men/22.jpg'],
                                    ['rank' => 13, 'name' => 'Rina', 'disbursement' => 160000, 'achieved' => 20, 'score' => 40, 'status' => 'Low', 'avatar' => 'https://randomuser.me/api/portraits/women/44.jpg'],
                                    ['rank' => 14, 'name' => 'Neha', 'disbursement' => 500000, 'achieved' => 17, 'score' => 30, 'status' => 'Low', 'avatar' => 'https://randomuser.me/api/portraits/women/23.jpg']
                                ];
                            @endphp
                            @foreach($employees as $emp)
                            <tr class="table-row">
                                <td class="px-6 py-3">
                                    @if($emp['rank'] <= 3)
                                        <div class="inline-flex flex-col items-center justify-center gap-1 leading-none align-middle">
                                            <span class="text-lg leading-none" title="Top rank">
                                                @if($emp['rank'] == 1) 🥇
                                                @elseif($emp['rank'] == 2) 🥈
                                                @else 🥉
                                                @endif
                                            </span>
                                            <span class="rank-medal">
                                                {{ $emp['rank'] }}
                                            </span>
                                        </div>
                                    @else
                                        <span class="text-gray-300 font-bold text-lg">{{ $emp['rank'] }}</span>
                                    @endif
                                </td>
                                <td class="px-6 py-3">
                                    <div class="flex items-center gap-3">
                                        <img src="{{ $emp['avatar'] }}" alt="{{ $emp['name'] }}" class="avatar-img" onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name={{ urlencode($emp['name']) }}&background=3b82f6&color=fff&size=40';">
                                        <span class="text-white font-semibold text-lg">{{ $emp['name'] }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-3 text-right text-emerald-400 font-bold text-lg">₹{{ number_format($emp['disbursement']) }}</td>
                                <td class="px-6 py-3 text-center">
                                    <div class="flex flex-col items-center gap-1">
                                        <span class="text-white font-bold text-lg">{{ $emp['achieved'] }}%</span>
                                        <div class="w-16 bg-gray-700 rounded-full h-1.5">
                                            <div class="bg-emerald-500 h-1.5 rounded-full" style="width: {{ $emp['achieved'] }}%"></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-3 text-center">
                                    <span class="inline-block px-3 py-1 rounded-lg bg-[#1e3a5f] text-white font-bold text-lg">{{ $emp['score'] }}</span>
                                </td>
                                <td class="px-6 py-3 text-center">
                                    @if($emp['status'] == 'Excellent')
                                        <span class="badge-excellent px-4 py-1.5 rounded-full text-sm font-bold uppercase tracking-wide">✅ Excellent</span>
                                    @elseif($emp['status'] == 'Good')
                                        <span class="badge-good px-4 py-1.5 rounded-full text-sm font-bold uppercase tracking-wide">✅ Good</span>
                                    @elseif($emp['status'] == 'Average')
                                        <span class="badge-average px-4 py-1.5 rounded-full text-sm font-bold uppercase tracking-wide">✅ Average</span>
                                    @else
                                        <span class="badge-low px-4 py-1.5 rounded-full text-sm font-bold uppercase tracking-wide">✅ Low</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- ENHANCED RIGHT PANEL: Live Activity + Target Tracking --}}
            <div class="lg:col-span-4 space-y-4 h-full">

                <!-- LIVE ACTIVITY FEED -->
                <div class="glass-card theme-live p-5">
                    <div class="flex items-center justify-between mb-5">
                        <h3 class="text-gray-200 font-bold text-xl uppercase tracking-wider flex items-center gap-2">
                            <span class="text-2xl">⚡</span> LIVE ACTIVITY
                        </h3>
                        <div class="flex items-center gap-2">
                            <span class="live-dot"></span>
                            <span class="text-green-400 text-xs font-mono font-semibold">LIVE FEED</span>
                        </div>
                    </div>
                    <div class="space-y-3" id="live-activity-feed">
                        <!-- Activity items will be populated dynamically with animation -->
                        <div class="activity-item flex items-center gap-4 p-3 rounded-xl transition-all">
                            <img src="https://randomuser.me/api/portraits/men/32.jpg" alt="Rahul" class="avatar-img" onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name=Rahul&background=3b82f6&color=fff&size=40';">
                            <div>
                                <p class="text-white font-bold text-lg"><span class="font-black">Rahul</span> <span class="text-gray-300">closed ₹2L loan</span></p>
                                <p class="text-gray-400 text-xs mt-0.5">Just now</p>
                            </div>
                        </div>
                        <div class="activity-item flex items-center gap-4 p-3 rounded-xl transition-all">
                            <img src="https://randomuser.me/api/portraits/women/68.jpg" alt="Priya" class="avatar-img" onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name=Priya&background=3b82f6&color=fff&size=40';">
                            <div>
                                <p class="text-white font-bold text-lg"><span class="font-black">Priya</span> <span class="text-gray-300">added new lead</span></p>
                                <p class="text-gray-400 text-xs mt-0.5">2 mins ago</p>
                            </div>
                        </div>
                        <div class="activity-item flex items-center gap-4 p-3 rounded-xl transition-all">
                            <img src="https://randomuser.me/api/portraits/men/45.jpg" alt="Amit" class="avatar-img" onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name=Amit&background=3b82f6&color=fff&size=40';">
                            <div>
                                <p class="text-white font-bold text-lg"><span class="font-black">Amit</span> <span class="text-gray-300">completed follow-up</span></p>
                                <p class="text-gray-400 text-xs mt-0.5">5 mins ago</p>
                            </div>
                        </div>
                        <div class="activity-item flex items-center gap-4 p-3 rounded-xl transition-all">
                            <img src="https://randomuser.me/api/portraits/women/23.jpg" alt="Neha" class="avatar-img" onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name=Neha&background=3b82f6&color=fff&size=40';">
                            <div>
                                <p class="text-white font-bold text-lg"><span class="font-black">Neha</span> <span class="text-gray-300">verified documents</span></p>
                                <p class="text-gray-400 text-xs mt-0.5">8 mins ago</p>
                            </div>
                        </div>
                        <div class="activity-item flex items-center gap-4 p-3 rounded-xl transition-all live-activity-item">
                            <img src="https://randomuser.me/api/portraits/men/52.jpg" alt="Vikram" class="avatar-img" onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name=Vikram&background=3b82f6&color=fff&size=40';">
                            <div>
                                <p class="text-white font-bold text-lg"><span class="font-black">Vikram</span> <span class="text-gray-300">disbursed ₹1.5L loan</span></p>
                                <p class="text-gray-400 text-xs mt-0.5">Just now • NEW</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- TARGET TRACKING PANEL -->
                <div class="glass-card theme-target p-5">
                    <h3 class="text-gray-200 font-bold text-xl uppercase tracking-wider mb-5 flex items-center gap-2">
                        <span class="text-2xl">🎯</span> TARGET TRACKING
                    </h3>
                    <div class="space-y-4">
                        <!-- Alert 1: Achieved -->
                        <div class="target-alert flex items-center gap-4 p-4 rounded-xl border-l-4 border-emerald-500">
                            <div class="w-12 h-12 rounded-full bg-emerald-600/20 flex items-center justify-center text-2xl">🔥</div>
                            <div class="flex-1">
                                <p class="text-white font-bold text-lg">Rahul crossed 100%</p>
                                <p class="text-emerald-400 text-sm font-semibold">Achieved 114% of monthly target</p>
                            </div>
                            <span class="text-emerald-400 text-2xl">🚀</span>
                        </div>
                        <!-- Alert 2: Near Target -->
                        <div class="target-alert flex items-center gap-4 p-4 rounded-xl border-l-4 border-yellow-500">
                            <div class="w-12 h-12 rounded-full bg-yellow-600/20 flex items-center justify-center text-2xl">🎯</div>
                            <div class="flex-1">
                                <p class="text-white font-bold text-lg">Priya nearing target</p>
                                <p class="text-yellow-400 text-sm font-semibold">92% achieved | ₹2.8L disbursed</p>
                            </div>
                            <span class="text-yellow-400 text-2xl">📈</span>
                        </div>
                        <!-- Alert 3: Low Performance -->
                        <div class="target-alert flex items-center gap-4 p-4 rounded-xl border-l-4 border-red-500">
                            <div class="w-12 h-12 rounded-full bg-red-600/20 flex items-center justify-center text-2xl">⚠️</div>
                            <div class="flex-1">
                                <p class="text-white font-bold text-lg">Amit at 45%</p>
                                <p class="text-red-400 text-sm font-semibold">Below target | Needs immediate attention</p>
                            </div>
                            <span class="text-red-400 text-2xl">🔴</span>
                        </div>
                        <!-- Alert 4: Team Achievement -->
                        <div class="target-alert flex items-center gap-4 p-4 rounded-xl border-l-4 border-blue-500">
                            <div class="w-12 h-12 rounded-full bg-blue-600/20 flex items-center justify-center text-2xl">🏆</div>
                            <div class="flex-1">
                                <p class="text-white font-bold text-lg">Team achievement 78%</p>
                                <p class="text-blue-400 text-sm font-semibold">₹32.5L disbursed of ₹40L target</p>
                            </div>
                            <span class="text-blue-400 text-2xl">📊</span>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        {{-- BOTTOM ALIGNED ROW: Performance Metrics + Leave Summary --}}
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 mb-6 items-stretch">
            <div class="lg:col-span-8 h-full">
                <div class="glass-card theme-performance p-5 h-full lg:min-h-[250px] flex flex-col">
                    <h3 class="text-gray-200 font-bold text-xl uppercase tracking-wider mb-6">Performance Metrics</h3>
                    <div class="space-y-6">
                        <div>
                            <div class="flex justify-between mb-2">
                                <span class="text-gray-300 font-semibold text-lg">Target Achievement</span>
                                <span class="text-blue-400 font-bold text-xl">82%</span>
                            </div>
                            <div class="w-full bg-gray-700 rounded-full h-3 overflow-hidden">
                                <div class="bg-gradient-to-r from-blue-500 to-indigo-600 h-3 rounded-full progress-bar" style="width: 82%"></div>
                            </div>
                        </div>
                        <div>
                            <div class="flex justify-between mb-2">
                                <span class="text-gray-300 font-semibold text-lg">Lead Entry</span>
                                <span class="text-emerald-400 font-bold text-xl">75%</span>
                            </div>
                            <div class="w-full bg-gray-700 rounded-full h-3 overflow-hidden">
                                <div class="bg-gradient-to-r from-emerald-500 to-green-600 h-3 rounded-full progress-bar" style="width: 75%"></div>
                            </div>
                        </div>
                        <div>
                            <div class="flex justify-between mb-2">
                                <span class="text-gray-300 font-semibold text-lg">Authorised Leads</span>
                                <span class="text-purple-400 font-bold text-xl">68%</span>
                            </div>
                            <div class="w-full bg-gray-700 rounded-full h-3 overflow-hidden">
                                <div class="bg-gradient-to-r from-purple-500 to-fuchsia-600 h-3 rounded-full progress-bar" style="width: 68%"></div>
                            </div>
                        </div>
                        <div>
                            <div class="flex justify-between mb-2">
                                <span class="text-gray-300 font-semibold text-lg">Discipline</span>
                                <span class="text-amber-400 font-bold text-xl">90%</span>
                            </div>
                            <div class="w-full bg-gray-700 rounded-full h-3 overflow-hidden">
                                <div class="bg-gradient-to-r from-amber-500 to-orange-600 h-3 rounded-full progress-bar" style="width: 90%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="lg:col-span-4 h-full">
                <div class="glass-card theme-leave p-4 lg:min-h-[250px] h-full flex flex-col">
                    <h3 class="text-gray-200 font-bold text-lg uppercase tracking-wider mb-4 flex items-center gap-2">
                        <span class="text-xl">📅</span> LEAVE SUMMARY
                    </h3>
                    <div class="grid grid-cols-2 gap-3 text-center flex-1">
                        <div class="bg-[#1e3a5f] rounded-xl p-3">
                            <div class="text-3xl font-black text-blue-400 leading-none">4</div>
                            <div class="text-gray-300 text-sm font-semibold mt-1.5">Casual Leave</div>
                        </div>
                        <div class="bg-[#1e3a5f] rounded-xl p-3">
                            <div class="text-3xl font-black text-pink-400 leading-none">1</div>
                            <div class="text-gray-300 text-sm font-semibold mt-1.5">Maternity Leave</div>
                        </div>
                        <div class="bg-[#1e3a5f] rounded-xl p-3">
                            <div class="text-3xl font-black text-emerald-400 leading-none">2</div>
                            <div class="text-gray-300 text-sm font-semibold mt-1.5">Sick Leave</div>
                        </div>
                        <div class="bg-[#1e3a5f] rounded-xl p-3">
                            <div class="text-3xl font-black text-amber-400 leading-none">3</div>
                            <div class="text-gray-300 text-sm font-semibold mt-1.5">Comp-off Leave</div>
                        </div>
                    </div>
                    <div class="mt-3 pt-2 text-center text-gray-300 text-xs border-t border-[#1e3a5f]">
                        📊 Attendance Rate: <span class="text-emerald-400 font-bold">92.5%</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Footer --}}
        <div class="text-center mt-6 text-gray-400 text-sm tracking-wide">
            © Loan DSA Command Center | Last updated: <span id="update-time"></span> • Real-time performance tracking
        </div>
    </div>

    <script>
        // Set current date
        const options = { year: 'numeric', month: 'long', day: 'numeric' };
        document.getElementById('current-date').innerText = new Date().toLocaleDateString('en-US', options);

        // Set update time with live refresh
        function updateTime() {
            const now = new Date();
            const timeString = now.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
            document.getElementById('update-time').innerText = timeString;
        }
        updateTime();
        setInterval(updateTime, 1000);

        // Animate progress bars on load
        window.addEventListener('load', () => {
            const progressBars = document.querySelectorAll('.progress-bar');
            progressBars.forEach(bar => {
                const width = bar.style.width;
                bar.style.width = '0%';
                setTimeout(() => {
                    bar.style.width = width;
                }, 100);
            });
        });

        const activityAvatarByName = {
            Rahul: 'https://randomuser.me/api/portraits/men/32.jpg',
            Priya: 'https://randomuser.me/api/portraits/women/68.jpg',
            Amit: 'https://randomuser.me/api/portraits/men/45.jpg',
            Neha: 'https://randomuser.me/api/portraits/women/23.jpg',
            Vikram: 'https://randomuser.me/api/portraits/men/52.jpg',
            Anjali: 'https://randomuser.me/api/portraits/women/44.jpg',
        };

        function activityAvatarFallbackUrl(name) {
            return 'https://ui-avatars.com/api/?name=' + encodeURIComponent(name || 'User') + '&background=3b82f6&color=fff&size=40';
        }

        // Dynamic live activity feed simulation (rotates through activities for TV liveliness)
        const activities = [
            { icon: '🟢', name: 'Rahul', action: 'closed ₹2.2L loan', time: 'Just now', color: 'emerald' },
            { icon: '🟡', name: 'Priya', action: 'added new high-value lead', time: 'Just now', color: 'yellow' },
            { icon: '🔵', name: 'Amit', action: 'completed follow-up call', time: 'Just now', color: 'blue' },
            { icon: '🟢', name: 'Neha', action: 'verified 3 documents', time: 'Just now', color: 'emerald' },
            { icon: '🟣', name: 'Vikram', action: 'disbursed ₹1.8L loan', time: 'Just now', color: 'purple' },
            { icon: '🔷', name: 'Anjali', action: 'submitted KYC report', time: 'Just now', color: 'cyan' }
        ];

        let activityIndex = 0;
        setInterval(() => {
            const feedContainer = document.getElementById('live-activity-feed');
            if (feedContainer) {
                const newActivity = activities[activityIndex % activities.length];
                const newItem = document.createElement('div');
                newItem.className = 'activity-item flex items-center gap-4 p-3 rounded-xl transition-all live-activity-item';
                const avatarSrc = activityAvatarByName[newActivity.name] || activityAvatarFallbackUrl(newActivity.name);
                newItem.innerHTML = `
                    <img src="${avatarSrc}" alt="${newActivity.name}" class="avatar-img" onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name=${encodeURIComponent(newActivity.name)}&background=3b82f6&color=fff&size=40';">
                    <div>
                        <p class="text-white font-bold text-lg"><span class="font-black">${newActivity.name}</span> <span class="text-gray-300">${newActivity.action}</span></p>
                        <p class="text-gray-400 text-xs mt-0.5">${newActivity.time}</p>
                    </div>
                `;
                feedContainer.insertBefore(newItem, feedContainer.firstChild);

                // Keep only the latest 5 items
                while (feedContainer.children.length > 5) {
                    feedContainer.removeChild(feedContainer.lastChild);
                }

                // Remove animation class after animation completes
                setTimeout(() => {
                    newItem.classList.remove('live-activity-item');
                }, 6000);

                activityIndex++;
            }
        }, 12000);

        /* Same portrait URLs as employee table ($employees) */
        const leaderAvatarByName = {
            Rahul: 'https://randomuser.me/api/portraits/men/32.jpg',
            Priya: 'https://randomuser.me/api/portraits/women/68.jpg',
            Vikram: 'https://randomuser.me/api/portraits/men/52.jpg',
            Neha: 'https://randomuser.me/api/portraits/women/23.jpg',
        };

        function leaderInitialsFromName(name) {
            if (!name || typeof name !== 'string') return '--';
            const t = name.trim();
            if (t.length >= 2) return t.substring(0, 2).toUpperCase();
            return (t + '?').toUpperCase();
        }

        function leaderAvatarFallbackUrl(name) {
            return 'https://ui-avatars.com/api/?name=' + encodeURIComponent(name || 'Leader') + '&background=3b82f6&color=fff&size=128';
        }

        function syncLeaderAvatar(name) {
            const wrap = document.getElementById('leader-avatar');
            const img = document.getElementById('leader-avatar-img');
            const initialsEl = document.getElementById('leader-initials');
            const n = (name || '').trim();
            if (wrap) wrap.setAttribute('aria-label', 'Leader avatar: ' + n);
            if (initialsEl) {
                initialsEl.textContent = leaderInitialsFromName(n);
                initialsEl.classList.add('hidden');
                initialsEl.classList.remove('flex', 'items-center', 'justify-center');
            }
            if (img) {
                img.style.display = '';
                img.alt = n || 'Leader';
                img.src = leaderAvatarByName[n] || leaderAvatarFallbackUrl(n);
            }
        }

        document.getElementById('leader-avatar-img')?.addEventListener('error', function () {
            this.style.display = 'none';
            const initialsEl = document.getElementById('leader-initials');
            if (initialsEl) {
                initialsEl.classList.remove('hidden');
                initialsEl.classList.add('flex', 'items-center', 'justify-center');
            }
        });

        // Rotate leader name for dynamic feel
        let leaderIndex = 0;
        const leaders = ['Rahul', 'Priya', 'Vikram', 'Neha'];
        syncLeaderAvatar(document.getElementById('leader-name')?.textContent || 'Rahul');

        setInterval(() => {
            leaderIndex = (leaderIndex + 1) % leaders.length;
            const leaderElement = document.getElementById('leader-name');
            if (leaderElement) {
                leaderElement.style.opacity = '0';
                leaderElement.style.transform = 'translateY(10px)';
                setTimeout(() => {
                    const name = leaders[leaderIndex];
                    leaderElement.textContent = name;
                    leaderElement.style.opacity = '1';
                    leaderElement.style.transform = 'translateY(0)';
                    syncLeaderAvatar(name);
                }, 200);
            }
        }, 5000);

        // Simulate dynamic stats update for TV realism
        let statIndex = 0;
        const totalLeadsValues = [125, 128, 126, 129, 131];
        const disbursementValues = ['₹32,50,000', '₹32,85,000', '₹33,10,000', '₹33,45,000', '₹33,80,000'];

        setInterval(() => {
            statIndex = (statIndex + 1) % totalLeadsValues.length;
            const leadsElement = document.getElementById('total-leads');
            const disbursementElement = document.getElementById('total-disbursement');
            if (leadsElement) {
                leadsElement.style.opacity = '0.5';
                setTimeout(() => {
                    leadsElement.textContent = totalLeadsValues[statIndex];
                    leadsElement.style.opacity = '1';
                }, 200);
            }
            if (disbursementElement) {
                disbursementElement.style.opacity = '0.5';
                setTimeout(() => {
                    disbursementElement.textContent = disbursementValues[statIndex];
                    disbursementElement.style.opacity = '1';
                }, 200);
            }
        }, 30000);
    </script>
</body>
</html>
