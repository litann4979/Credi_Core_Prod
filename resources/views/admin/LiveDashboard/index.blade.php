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
    font-family: 'Outfit', sans-serif;
    background: radial-gradient(circle at 50% 0%, #1e3a8a, #020617 70%);
}

/* FULL TV SCREEN */
.tv-frame {
    width: 100vw;
    height: 100vh;
    padding: 10px 10px 8px 10px;
    background: linear-gradient(180deg, #0a2a5a, #03142c);
    box-shadow: inset 0 0 120px rgba(0,0,0,0.8);
}

/* HEADER */
.glass-card {
    background: linear-gradient(180deg, #1e40af, #1e3a8a);
    border: 1px solid rgba(96,165,250,0.4);
    border-radius: 10px;
    box-shadow:
        inset 0 2px 12px rgba(255,255,255,0.15),
        0 0 25px rgba(59,130,246,0.4);
}

/* KPI CARDS */
.kpi-a {
    background: linear-gradient(135deg, #2dd4bf 0%, #2563eb 85%);
    box-shadow: inset 0 1px 8px rgba(255,255,255,0.22), 0 0 18px rgba(56,189,248,0.45);
}
.kpi-b {
    background: linear-gradient(135deg, #7c3aed 0%, #4f46e5 85%);
    box-shadow: inset 0 1px 8px rgba(255,255,255,0.2), 0 0 18px rgba(139,92,246,0.5);
}
.kpi-c {
    background: linear-gradient(135deg, #94a3b8 0%, #f59e0b 85%);
    box-shadow: inset 0 1px 8px rgba(255,255,255,0.24), 0 0 18px rgba(245,158,11,0.45);
}
.kpi-d {
    background: linear-gradient(135deg, #f59e0b 0%, #f97316 85%);
    box-shadow: inset 0 1px 8px rgba(255,255,255,0.22), 0 0 20px rgba(249,115,22,0.5);
}

.kpi-strip {
    border-radius: 9px !important;
    min-height: 82px;
}
.kpi-title {
    font-size: 11px;
    letter-spacing: 0.02em;
    font-weight: 700;
}
.kpi-value {
    font-size: 34px;
    line-height: 1;
    font-weight: 900;
    color: #f8fafc;
    text-shadow: 0 0 10px rgba(255,255,255,0.25);
}
.kpi-sub {
    font-size: 10px;
    font-weight: 600;
    color: rgba(236,253,245,0.95);
}

/* TABLE HEADER */
thead tr {
    background: linear-gradient(180deg, #2563eb, #1e40af);
}

/* TABLE ROWS (LED STYLE) */
.table-row td {
    background:
        linear-gradient(180deg, #0f3b82, #0a2c66),
        linear-gradient(90deg, rgba(255,255,255,0.08), transparent);
    border-bottom: 1px solid rgba(255,255,255,0.15);
    color: #e0f2fe;
    padding-top: 0.15rem !important;
    padding-bottom: 0.15rem !important;
    line-height: 0.95 !important;
}

tbody tr:nth-child(even) td {
    background: linear-gradient(180deg, #0c3575, #082a5c);
}

/* HOVER GLOW */
tbody tr:hover td {
    background: linear-gradient(180deg, #1d4ed8, #1e3a8a);
    box-shadow: inset 0 0 10px rgba(255,255,255,0.2);
}

/* STATUS BADGES */
.badge-excellent { background: #22c55e; }
.badge-good { background: #eab308; }
.badge-average { background: #f97316; }
.badge-low { background: #ef4444; }

/* RIGHT PANEL */
.theme-performance,
.theme-leave {
    background: linear-gradient(180deg, #1e40af, #1e3a8a);
    box-shadow: 0 0 20px rgba(59,130,246,0.4);
}

/* MINI CARDS */
.mini-stat {
    background: linear-gradient(180deg, #2563eb, #1e3a8a);
}

/* PROGRESS BAR */
.progress-bar {
    background: linear-gradient(to right, #22c55e, #4ade80);
}

.avatar-img {
    width: 22px;
    height: 22px;
    border-radius: 999px;
    object-fit: cover;
    border: 1px solid rgba(125, 211, 252, 0.85);
    display: block;
}

.page-dot {
    width: 8px;
    height: 8px;
    border-radius: 999px;
    background: rgba(191, 219, 254, 0.35);
    display: inline-block;
}

.page-dot.active {
    background: #22d3ee;
    box-shadow: 0 0 8px rgba(34, 211, 238, 0.9);
}

/* TEXT GLOW */
h1 {
    text-shadow: 0 0 12px rgba(255,255,255,0.5);
}

/* REMOVE EXTRA SPACING */
.grid {
    gap: 10px !important;
}

/* FORCE FULL HEIGHT */
html, body {
    height: 100%;
}

@media (max-width: 1024px) {
    .tv-frame {
        height: auto !important;
        min-height: 100vh;
        padding-bottom: 12px;
        overflow-y: auto;
    }
}
    </style>
</head>
<body class="overflow-x-hidden overflow-y-auto bg-[#020617]">
    @php
        $kpis = $kpis ?? [];
        $employees = collect($employees ?? []);
        $performanceMetrics = $performanceMetrics ?? [];
        $leaveSummary = $leaveSummary ?? [];
        $selectedDate = request('date');
        $selectedMonth = request('month');
        // Hide Daily Score only for month view; keep visible for date filter.
        $hideDailyScore = filled($selectedMonth);

        $leaderName = $kpis['leader']['name'] ?? '—';
        $leaderPhoto = $kpis['leader']['profile_photo'] ?? '';
        $totalLeads = (int) ($kpis['total_lead_entry_count'] ?? 0);
        $positiveLeadAmount = (float) ($kpis['positive_lead_amount'] ?? 0);
        $avgScore = (float) ($kpis['average_score'] ?? 0);
    @endphp

    {{-- Main Container with dynamic scaling --}}
    <div class="w-screen min-h-screen tv-frame">

        {{-- HEADER --}}
        <div class="glass-card px-4 py-2 mb-2 flex flex-wrap justify-between items-center">
            <div>
                <span style="display: inline-flex; align-items: center; justify-content: center; padding: 5px 10px; margin-bottom: 6px; border-radius: 8px; background: rgba(255, 255, 255, 0.96); border: 1px solid rgba(255, 255, 255, 0.7); box-shadow: 0 3px 10px rgba(0, 0, 0, 0.25);">
                    <img id="headerKredipalLogo" src="{{ asset('storage/kredipalfinallogo.png') }}" alt="Kredipal Logo" style="height: 24px; width: auto; display: block;">
                </span>
                <h1 class="font-dashboard-display text-xl md:text-2xl text-white">
                    DAILY PERFORMANCE DASHBOARD
                </h1>
                <p class="font-dashboard-sub text-blue-300 text-xs md:text-sm mt-0.5 flex items-center gap-2">
                    Loan DSA – Live Tracking System
                    <span class="flex items-center gap-1 text-sky-200/90 text-xs font-semibold tracking-wide">
                        <span class="live-dot"></span>
                        LIVE
                    </span>
                </p>
            </div>
            <div class="mt-3 md:mt-0 md:text-right w-full md:w-auto">
                <div class="text-white text-sm md:text-base font-semibold" id="current-date">24 April 2024</div>
                <div class="text-gray-300 text-[10px] font-mono">Real-time metrics</div>
                <div class="mt-2 flex flex-wrap items-center justify-start md:justify-end gap-2">
                    <label class="sr-only" for="header-month-filter">Month</label>
                    <select id="header-month-filter" class="bg-[#0f2a44] border border-[#2c4c7a] text-gray-100 text-xs md:text-sm rounded-lg px-2.5 py-1.5 focus:outline-none focus:ring-2 focus:ring-blue-500/50">
                        <option value="">Month</option>
                        <option value="1">Jan</option>
                        <option value="2">Feb</option>
                        <option value="3">Mar</option>
                        <option value="4">Apr</option>
                        <option value="5">May</option>
                        <option value="6">Jun</option>
                        <option value="7">Jul</option>
                        <option value="8">Aug</option>
                        <option value="9">Sep</option>
                        <option value="10">Oct</option>
                        <option value="11">Nov</option>
                        <option value="12">Dec</option>
                    </select>

                    <label class="sr-only" for="header-year-filter">Year</label>
                    <select id="header-year-filter" class="bg-[#0f2a44] border border-[#2c4c7a] text-gray-100 text-xs md:text-sm rounded-lg px-2.5 py-1.5 focus:outline-none focus:ring-2 focus:ring-blue-500/50">
                        <option value="">Year</option>
                        <option value="2023">2023</option>
                        <option value="2024">2024</option>
                        <option value="2025">2025</option>
                        <option value="2026">2026</option>
                        <option value="2027">2027</option>
                        <option value="2028">2028</option>
                        <option value="2029">2029</option>
                        <option value="2030">2030</option>
                    </select>

                    <label class="sr-only" for="header-date-filter">Date</label>
                    <input id="header-date-filter" type="date" class="bg-[#0f2a44] border border-[#2c4c7a] text-gray-100 text-xs md:text-sm rounded-lg px-2.5 py-1.5 focus:outline-none focus:ring-2 focus:ring-blue-500/50" />

                    <button
                        type="button"
                        class="bg-[#13355a] border border-[#2c4c7a] text-gray-100 text-xs md:text-sm rounded-lg px-2.5 py-1.5 hover:bg-[#1a3d66] transition-colors"
                        id="header-clear-filter"
                    >
                        Clear
                    </button>
                </div>
            </div>
        </div>

        {{-- TOP SUMMARY CARDS (4 cards) --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-2">
            <!-- Total Leads -->
            <div class="glass-card kpi-a kpi-strip p-2.5 transition-all cursor-pointer">
                <div class="flex items-center justify-between mb-2">
                    <span class="kpi-title text-cyan-50">🏹 Total Leads:</span>
                </div>
                <div class="kpi-value" id="total-leads">{{ $totalLeads }}</div>
                <div class="kpi-sub mt-1">Live daily count</div>
            </div>
            <!-- Total Disbursement -->
            <div class="glass-card kpi-b kpi-strip p-2.5 transition-all cursor-pointer">
                <div class="flex items-center justify-between mb-2">
                    <span class="kpi-title text-violet-100">🏆 Positive Leads</span>
                </div>
                <div class="text-[30px] md:text-[34px] text-white font-black leading-tight" id="total-disbursement">₹{{ number_format($positiveLeadAmount, 2) }}</div>
                <div class="kpi-sub mt-1">Positive lead amount</div>
            </div>
            <!-- Average Score -->
            <div class="glass-card kpi-c kpi-strip p-2.5 transition-all cursor-pointer">
                <div class="flex items-center justify-between mb-2">
                    <span class="kpi-title text-slate-50">📊 Avg Score:</span>
                </div>
                <div class="text-[30px] md:text-[34px] text-white font-black leading-tight" id="avg-score">{{ number_format($avgScore, 2) }} <span class="text-xl">/100</span></div>
                <div class="kpi-sub mt-1">Performance baseline</div>
            </div>
            <!-- Leader of the Day -->
            <div class="glass-card kpi-d kpi-strip p-2.5 sm:p-3 transition-all cursor-pointer relative overflow-hidden flex items-center justify-between gap-2 min-h-[82px]">
                <div class="relative z-10 flex-1 min-w-0 pr-2">
                    <div class="flex items-center gap-1.5 mb-1">
                        <span class="kpi-title text-amber-50" aria-hidden="true">🏆 Leader</span>
                    </div>
                    <div class="text-xl sm:text-2xl md:text-3xl text-white font-black leading-tight truncate" id="leader-name">{{ $leaderName }}</div>
                    <div class="text-amber-100 text-[10px] mt-1 flex items-center gap-1 flex-wrap">
                        <span class="tracking-tight">★★★★★</span>
                        <span class="text-white/85 text-[10px] font-medium">5.0</span>
                    </div>
                </div>
                {{-- Same portrait URLs as employee table; initials fallback if image fails --}}
                <div class="relative z-10 flex-shrink-0 flex flex-col items-center justify-center">
                    <div class="relative">
                        <span class="absolute -top-1 -right-1 z-20 text-lg leading-none drop-shadow-md pointer-events-none" aria-hidden="true" title="Top performer">⭐</span>
                        <div
                            id="leader-avatar"
                            class="relative w-12 h-12 sm:w-14 sm:h-14 rounded-full overflow-hidden bg-amber-400/35 shadow-inner"
                            role="img"
                            aria-label="Leader"
                        >
                            <img
                                id="leader-avatar-img"
                                src="{{ $leaderPhoto ?: ('https://ui-avatars.com/api/?name=' . urlencode($leaderName) . '&background=3b82f6&color=fff&size=128') }}"
                                alt=""
                                class="leader-avatar-img"
                                width="56"
                                height="56"
                                loading="eager"
                                decoding="async"
                            />
                            <span id="leader-initials" class="absolute inset-0 hidden text-white font-black text-xl sm:text-2xl tracking-tight select-none bg-amber-500/50">RA</span>
                        </div>
                    </div>
                    <div class="text-lg leading-none opacity-25 -mt-0.5 select-none" aria-hidden="true">🏆</div>
                </div>
                <div class="absolute -bottom-5 -right-1 text-6xl opacity-[0.12] pointer-events-none select-none" aria-hidden="true">🏆</div>
            </div>
        </div>

        {{-- MAIN GRID: Employee Table (Left) + Enhanced Right Panel --}}
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-3 mb-3 items-stretch">
            {{-- LEFT: Employee Table --}}
            <div class="lg:col-span-8 h-full">
                <div class="glass-card theme-table overflow-hidden h-full flex flex-col">
                    <div class="overflow-hidden flex-1">
                        <table class="w-full min-w-[760px] table-auto">
                        <thead>
                            <tr class="border-b border-[#2a4b7a] bg-[#0a1733]">
                                <th class="px-4 py-2 text-left text-blue-200 font-bold text-xs uppercase tracking-wider">No</th>
                                <th class="px-4 py-2 text-left text-blue-200 font-bold text-xs uppercase tracking-wider">Name</th>
                                <th class="px-4 py-2 text-right text-blue-200 font-bold text-xs uppercase tracking-wider">Disbursement</th>
                                <th class="px-4 py-2 text-right text-blue-200 font-bold text-xs uppercase tracking-wider">Positive Leads</th>
                                <th class="px-4 py-2 text-center text-blue-200 font-bold text-xs uppercase tracking-wider">% Achieved</th>
                                <th class="px-4 py-2 text-center text-blue-200 font-bold text-xs uppercase tracking-wider {{ $hideDailyScore ? 'hidden' : '' }}" id="daily-score-header">Daily Score</th>
                                <th class="px-4 py-2 text-center text-blue-200 font-bold text-xs uppercase tracking-wider">Monthly Score</th>
                                <th class="px-4 py-2 text-center text-blue-200 font-bold text-xs uppercase tracking-wider">Status</th>
                               </tr>
                        </thead>
                        <tbody id="employee-table-body">
                            @forelse($employees as $emp)
                            <tr class="table-row employee-row">
                                <td class="px-4 py-2">
                                    @if(($emp['rank'] ?? 0) <= 3)
                                        <div class="inline-flex flex-col items-center justify-center gap-1 leading-none align-middle">
                                            <span class="text-lg leading-none" title="Top rank">
                                                @if(($emp['rank'] ?? 0) == 1) 🥇
                                                @elseif(($emp['rank'] ?? 0) == 2) 🥈
                                                @else 🥉
                                                @endif
                                            </span>
                                            <span class="rank-medal">
                                                {{ $emp['rank'] ?? '-' }}
                                            </span>
                                        </div>
                                    @else
                                        <span class="text-gray-300 font-bold text-lg">{{ $emp['rank'] ?? '-' }}</span>
                                    @endif
                                </td>
                                <td class="px-4 py-2">
                                    <div class="flex items-center gap-3">
                                        <img src="{{ $emp['profile_photo'] ?: ('https://ui-avatars.com/api/?name=' . urlencode($emp['name'] ?? 'User') . '&background=3b82f6&color=fff&size=40') }}" alt="{{ $emp['name'] ?? 'User' }}" class="avatar-img">
                                        <span class="text-white font-semibold text-sm">{{ $emp['name'] ?? '—' }}</span>
                                    </div>
                                </td>
                                <td class="px-4 py-2 text-right text-cyan-300 font-bold text-sm">₹{{ number_format((float) ($emp['disbursement_amount'] ?? 0), 2) }}</td>
                                <td class="px-4 py-2 text-right text-emerald-400 font-bold text-sm">₹{{ number_format((float) ($emp['positive_leads_amount'] ?? 0), 2) }}</td>
                                <td class="px-4 py-2 text-center">
                                    <div class="flex flex-col items-center gap-1">
                                        <span class="text-white font-bold text-sm">{{ number_format((float) ($emp['target_achievement_percent'] ?? 0), 1) }}%</span>
                                        <div class="w-16 bg-gray-700 rounded-full h-1.5">
                                            <div class="bg-emerald-500 h-1.5 rounded-full" style="width: {{ min(100, (float) ($emp['target_achievement_percent'] ?? 0)) }}%"></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-2 text-center data-daily-score-cell {{ $hideDailyScore ? 'hidden' : '' }}">
                                    <span class="inline-block px-2 py-0.5 rounded-lg bg-[#1e3a5f] text-white font-bold text-sm">{{ $emp['daily_score_display'] ?? number_format((float) ($emp['score'] ?? 0), 1) }}</span>
                                </td>
                                <td class="px-4 py-2 text-center">
                                    <span class="inline-block px-2 py-0.5 rounded-lg bg-[#1e3a5f] text-white font-bold text-sm">{{ $emp['monthly_score_display'] ?? ($emp['daily_score_display'] ?? number_format((float) ($emp['score'] ?? 0), 1)) }}</span>
                                </td>
                                <td class="px-4 py-2 text-center">
                                    @if(($emp['status'] ?? '') == 'Excellent')
                                        <span class="badge-excellent inline-flex items-center gap-1 whitespace-nowrap px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wide">✅ Excellent</span>
                                    @elseif(($emp['status'] ?? '') == 'Good')
                                        <span class="badge-good inline-flex items-center gap-1 whitespace-nowrap px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wide">✅ Good</span>
                                    @elseif(($emp['status'] ?? '') == 'Average')
                                        <span class="badge-average inline-flex items-center gap-1 whitespace-nowrap px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wide">✅ Average</span>
                                    @else
                                        <span class="badge-low inline-flex items-center gap-1 whitespace-nowrap px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wide">✅ Low</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="px-6 py-10 text-center text-gray-300 font-semibold">No employee performance data available for selected filter.</td>
                            </tr>
                            @endforelse
                        </tbody>
                        </table>
                    </div>
                    <div id="table-page-indicator" class="flex items-center justify-center gap-2 py-1 text-[10px] text-cyan-200"></div>
                </div>
            </div>

            {{-- RIGHT PANEL: Performance Metrics + Leave Summary --}}
            <div class="lg:col-span-4 space-y-4 h-full">
                <div class="glass-card p-4">
                    <h3 class="text-gray-200 font-bold text-base uppercase tracking-wider mb-3">LEAVE SUMMARY</h3>
                    <div class="grid grid-cols-2 gap-2 text-center mb-4">
                        <div class="bg-[#1e3a5f] rounded-lg p-2">
                            <div class="text-2xl font-black text-blue-400 leading-none" id="leave-casual">{{ (int) ($leaveSummary['casual_leave'] ?? 0) }}</div>
                            <div class="text-gray-300 text-xs font-semibold mt-1">Casual</div>
                        </div>
                        <div class="bg-[#1e3a5f] rounded-lg p-2">
                            <div class="text-2xl font-black text-pink-400 leading-none" id="leave-maternity">{{ (int) ($leaveSummary['maternity_leave'] ?? 0) }}</div>
                            <div class="text-gray-300 text-xs font-semibold mt-1">Maternity</div>
                        </div>
                        <div class="bg-[#1e3a5f] rounded-lg p-2">
                            <div class="text-2xl font-black text-emerald-400 leading-none" id="leave-sick">{{ (int) ($leaveSummary['sick_leave'] ?? 0) }}</div>
                            <div class="text-gray-300 text-xs font-semibold mt-1">Sick</div>
                        </div>
                        <div class="bg-[#1e3a5f] rounded-lg p-2">
                            <div class="text-2xl font-black text-amber-400 leading-none" id="leave-compoff">{{ (int) ($leaveSummary['approved_comp_offs'] ?? 0) }}</div>
                            <div class="text-gray-300 text-xs font-semibold mt-1">Comp-off</div>
                        </div>
                    </div>
                    <h3 class="text-gray-200 font-bold text-base uppercase tracking-wider mb-3">PERFORMANCE METRICS</h3>
                    <div class="space-y-6">
                        <div>
                            <div class="flex justify-between mb-2">
                                <span class="text-gray-300 font-semibold text-lg">Target Achievement</span>
                                <span class="text-blue-400 font-bold text-xl" id="metric-target-value">{{ number_format((float) ($performanceMetrics['target_achievement_percent'] ?? 0), 2) }}%</span>
                            </div>
                            <div class="w-full bg-gray-700 rounded-full h-3 overflow-hidden">
                                <div id="metric-target-bar" class="bg-gradient-to-r from-blue-500 to-indigo-600 h-3 rounded-full progress-bar" style="width: {{ min(100, (float) ($performanceMetrics['target_achievement_percent'] ?? 0)) }}%"></div>
                            </div>
                        </div>
                        <div>
                            <div class="flex justify-between mb-2">
                                <span class="text-gray-300 font-semibold text-lg">Lead Entry</span>
                                <span class="text-emerald-400 font-bold text-xl" id="metric-lead-value">{{ number_format((float) ($performanceMetrics['lead_entry_percent'] ?? 0), 2) }}%</span>
                            </div>
                            <div class="w-full bg-gray-700 rounded-full h-3 overflow-hidden">
                                <div id="metric-lead-bar" class="bg-gradient-to-r from-emerald-500 to-green-600 h-3 rounded-full progress-bar" style="width: {{ min(100, (float) ($performanceMetrics['lead_entry_percent'] ?? 0)) }}%"></div>
                            </div>
                        </div>

                        <div>
                            <div class="flex justify-between mb-2">
                                <span class="text-gray-300 font-semibold text-lg">Discipline</span>
                                <span class="text-amber-400 font-bold text-xl" id="metric-discipline-value">{{ number_format((float) ($performanceMetrics['discipline_percent'] ?? 0), 2) }}%</span>
                            </div>
                            <div class="w-full bg-gray-700 rounded-full h-3 overflow-hidden">
                                <div id="metric-discipline-bar" class="bg-gradient-to-r from-amber-500 to-orange-600 h-3 rounded-full progress-bar" style="width: {{ min(100, (float) ($performanceMetrics['discipline_percent'] ?? 0)) }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        {{-- Footer --}}
        <div class="text-center mt-2 mb-0 text-gray-400 text-sm tracking-wide">
            © Loan DSA Command Center | Last updated: <span id="update-time"></span> • Real-time performance tracking
        </div>
    </div>

    <script src="https://js.pusher.com/8.4.0/pusher.min.js"></script>
    <script>
        function cleanCheckerBackground(imgEl) {
            if (!imgEl || imgEl.dataset.cleaned === '1') return;
            const canvas = document.createElement('canvas');
            const ctx = canvas.getContext('2d');
            if (!ctx) return;

            canvas.width = imgEl.naturalWidth || imgEl.width;
            canvas.height = imgEl.naturalHeight || imgEl.height;
            ctx.drawImage(imgEl, 0, 0);

            const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
            const data = imageData.data;

            for (let i = 0; i < data.length; i += 4) {
                const r = data[i];
                const g = data[i + 1];
                const b = data[i + 2];
                const a = data[i + 3];
                const isGray = Math.abs(r - g) < 12 && Math.abs(g - b) < 12;
                const isLightGray = r > 150 && g > 150 && b > 150;

                if (isGray && isLightGray) {
                    data[i + 3] = 0;
                } else if (isGray && r > 120 && g > 120 && b > 120) {
                    data[i + 3] = Math.max(0, a - 80);
                }
            }

            ctx.putImageData(imageData, 0, 0);
            imgEl.src = canvas.toDataURL('image/png');
            imgEl.dataset.cleaned = '1';
        }

        const options = { year: 'numeric', month: 'long', day: 'numeric' };
        const currentDateEl = document.getElementById('current-date');
        const monthFilterEl = document.getElementById('header-month-filter');
        const yearFilterEl = document.getElementById('header-year-filter');
        const dateFilterEl = document.getElementById('header-date-filter');
        const clearBtnEl = document.getElementById('header-clear-filter');

        const selectedDate = @json($selectedDate);
        const selectedMonth = @json($selectedMonth);
        const initialLeaderPhoto = @json($leaderPhoto);
        const hideDailyScore = @json($hideDailyScore);
        const dashboardDataUrl = @json(route('admin.live-dashboard.data'));
        const pusherKey = @json(config('broadcasting.connections.pusher.key'));
        const pusherCluster = @json(config('broadcasting.connections.pusher.options.cluster'));
        let tablePaginationTimer = null;

        function formatHeaderDate() {
            if (selectedDate) {
                const parsed = new Date(selectedDate + 'T00:00:00');
                currentDateEl.innerText = parsed.toLocaleDateString('en-US', options);
                return;
            }
            if (selectedMonth) {
                const parsed = new Date(selectedMonth + '-01');
                currentDateEl.innerText = parsed.toLocaleDateString('en-US', { year: 'numeric', month: 'long' });
                return;
            }
            currentDateEl.innerText = new Date().toLocaleDateString('en-US', options);
        }
        formatHeaderDate();

        function applyFilters() {
            const params = new URLSearchParams();
            const month = monthFilterEl.value;
            const year = yearFilterEl.value;
            const date = dateFilterEl.value;

            if (date) {
                params.set('date', date);
            } else if (month && year) {
                params.set('month', `${year}-${String(month).padStart(2, '0')}`);
            }

            const query = params.toString();
            window.location.href = query ? `${window.location.pathname}?${query}` : window.location.pathname;
        }

        function formatMoney(value) {
            return Number(value || 0).toLocaleString('en-IN', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2,
            });
        }

        function statusBadge(status) {
            if (status === 'Excellent') {
                return '<span class="badge-excellent inline-flex items-center gap-1 whitespace-nowrap px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wide">✅ Excellent</span>';
            }
            if (status === 'Good') {
                return '<span class="badge-good inline-flex items-center gap-1 whitespace-nowrap px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wide">✅ Good</span>';
            }
            if (status === 'Average') {
                return '<span class="badge-average inline-flex items-center gap-1 whitespace-nowrap px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wide">✅ Average</span>';
            }

            return '<span class="badge-low inline-flex items-center gap-1 whitespace-nowrap px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wide">✅ Low</span>';
        }

        function renderEmployeeRows(employees = []) {
            const tbody = document.getElementById('employee-table-body');
            if (!tbody) return;

            if (!Array.isArray(employees) || employees.length === 0) {
                tbody.innerHTML = '<tr><td colspan="8" class="px-6 py-10 text-center text-gray-300 font-semibold">No employee performance data available for selected filter.</td></tr>';
                initTablePagination();
                return;
            }

            tbody.innerHTML = employees.map((emp) => {
                const rank = Number(emp.rank || 0);
                const rankHtml = rank > 0 && rank <= 3
                    ? `<div class="inline-flex flex-col items-center justify-center gap-1 leading-none align-middle"><span class="text-lg leading-none" title="Top rank">${rank === 1 ? '🥇' : (rank === 2 ? '🥈' : '🥉')}</span><span class="rank-medal">${rank}</span></div>`
                    : `<span class="text-gray-300 font-bold text-lg">${rank || '-'}</span>`;
                const name = emp.name || '—';
                const avatar = emp.profile_photo || `https://ui-avatars.com/api/?name=${encodeURIComponent(name)}&background=3b82f6&color=fff&size=40`;
                const achieved = Number(emp.target_achievement_percent || 0);
                const dailyScoreDisplay = emp.daily_score_display || Number(emp.score || 0).toFixed(1);
                const monthlyScoreDisplay = emp.monthly_score_display || dailyScoreDisplay;
                const disbursement = Number(emp.disbursement_amount || 0);
                const amount = Number(emp.positive_leads_amount || 0);

                return `<tr class="table-row employee-row">
                    <td class="px-4 py-2">${rankHtml}</td>
                    <td class="px-4 py-2">
                        <div class="flex items-center gap-3">
                            <img src="${avatar}" alt="${name}" class="avatar-img">
                            <span class="text-white font-semibold text-sm">${name}</span>
                        </div>
                    </td>
                    <td class="px-4 py-2 text-right text-cyan-300 font-bold text-sm">₹${formatMoney(disbursement)}</td>
                    <td class="px-4 py-2 text-right text-emerald-400 font-bold text-sm">₹${formatMoney(amount)}</td>
                    <td class="px-4 py-2 text-center">
                        <div class="flex flex-col items-center gap-1">
                            <span class="text-white font-bold text-sm">${achieved.toFixed(1)}%</span>
                            <div class="w-16 bg-gray-700 rounded-full h-1.5">
                                <div class="bg-emerald-500 h-1.5 rounded-full" style="width: ${Math.min(100, achieved)}%"></div>
                            </div>
                        </div>
                    </td>
                    <td class="px-4 py-2 text-center data-daily-score-cell ${hideDailyScore ? 'hidden' : ''}">
                        <span class="inline-block px-2 py-0.5 rounded-lg bg-[#1e3a5f] text-white font-bold text-sm">${dailyScoreDisplay}</span>
                    </td>
                    <td class="px-4 py-2 text-center">
                        <span class="inline-block px-2 py-0.5 rounded-lg bg-[#1e3a5f] text-white font-bold text-sm">${monthlyScoreDisplay}</span>
                    </td>
                    <td class="px-4 py-2 text-center">${statusBadge(emp.status || 'Low')}</td>
                </tr>`;
            }).join('');

            initTablePagination();
        }

        function updateDashboardUI(data) {
            const kpis = data?.kpis || {};
            const leaveSummary = data?.leaveSummary || {};
            const performanceMetrics = data?.performanceMetrics || {};
            const employees = Array.isArray(data?.employees) ? data.employees : [];
            const leader = kpis.leader || {};

            const totalLeadsEl = document.getElementById('total-leads');
            const disbursementEl = document.getElementById('total-disbursement');
            const avgScoreEl = document.getElementById('avg-score');
            const leaderNameEl = document.getElementById('leader-name');

            if (totalLeadsEl) totalLeadsEl.textContent = Number(kpis.total_lead_entry_count || 0);
            if (disbursementEl) disbursementEl.textContent = `₹${formatMoney(kpis.positive_lead_amount || 0)}`;
            if (avgScoreEl) avgScoreEl.innerHTML = `${Number(kpis.average_score || 0).toFixed(2)} <span class="text-xl">/100</span>`;
            if (leaderNameEl) leaderNameEl.textContent = leader.name || '—';
            syncLeaderAvatar(leader.name || 'Leader', leader.profile_photo || '');

            const leaveCasualEl = document.getElementById('leave-casual');
            const leaveMaternityEl = document.getElementById('leave-maternity');
            const leaveSickEl = document.getElementById('leave-sick');
            const leaveCompOffEl = document.getElementById('leave-compoff');
            if (leaveCasualEl) leaveCasualEl.textContent = Number(leaveSummary.casual_leave || 0);
            if (leaveMaternityEl) leaveMaternityEl.textContent = Number(leaveSummary.maternity_leave || 0);
            if (leaveSickEl) leaveSickEl.textContent = Number(leaveSummary.sick_leave || 0);
            if (leaveCompOffEl) leaveCompOffEl.textContent = Number(leaveSummary.approved_comp_offs || 0);

            const targetPercent = Number(performanceMetrics.target_achievement_percent || 0);
            const leadPercent = Number(performanceMetrics.lead_entry_percent || 0);
            const disciplinePercent = Number(performanceMetrics.discipline_percent || 0);

            const targetValueEl = document.getElementById('metric-target-value');
            const leadValueEl = document.getElementById('metric-lead-value');
            const disciplineValueEl = document.getElementById('metric-discipline-value');
            const targetBarEl = document.getElementById('metric-target-bar');
            const leadBarEl = document.getElementById('metric-lead-bar');
            const disciplineBarEl = document.getElementById('metric-discipline-bar');

            if (targetValueEl) targetValueEl.textContent = `${targetPercent.toFixed(2)}%`;
            if (leadValueEl) leadValueEl.textContent = `${leadPercent.toFixed(2)}%`;
            if (disciplineValueEl) disciplineValueEl.textContent = `${disciplinePercent.toFixed(2)}%`;
            if (targetBarEl) targetBarEl.style.width = `${Math.min(100, targetPercent)}%`;
            if (leadBarEl) leadBarEl.style.width = `${Math.min(100, leadPercent)}%`;
            if (disciplineBarEl) disciplineBarEl.style.width = `${Math.min(100, disciplinePercent)}%`;

            renderEmployeeRows(employees);
        }

        async function refreshDashboardData() {
            try {
                const response = await fetch(`${dashboardDataUrl}${window.location.search || ''}`, {
                    method: 'GET',
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                });

                if (!response.ok) return;
                const payload = await response.json();
                updateDashboardUI(payload);
            } catch (error) {
                // Keep dashboard running even if realtime request fails.
            }
        }

        // Initialize filter controls from request.
        if (selectedDate) {
            dateFilterEl.value = selectedDate;
        } else if (selectedMonth) {
            const [y, m] = selectedMonth.split('-');
            if (y && m) {
                yearFilterEl.value = y;
                monthFilterEl.value = String(parseInt(m, 10));
            }
        }

        monthFilterEl?.addEventListener('change', () => {
            if (dateFilterEl.value) dateFilterEl.value = '';
            if (monthFilterEl.value && yearFilterEl.value) applyFilters();
        });
        yearFilterEl?.addEventListener('change', () => {
            if (dateFilterEl.value) dateFilterEl.value = '';
            if (monthFilterEl.value && yearFilterEl.value) applyFilters();
        });
        dateFilterEl?.addEventListener('change', () => {
            if (dateFilterEl.value) {
                monthFilterEl.value = '';
                yearFilterEl.value = '';
            }
            applyFilters();
        });
        clearBtnEl?.addEventListener('click', () => {
            monthFilterEl.value = '';
            yearFilterEl.value = '';
            dateFilterEl.value = '';
            window.location.href = window.location.pathname;
        });

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

        function leaderInitialsFromName(name) {
            if (!name || typeof name !== 'string') return '--';
            const t = name.trim();
            if (t.length >= 2) return t.substring(0, 2).toUpperCase();
            return (t + '?').toUpperCase();
        }

        function leaderAvatarFallbackUrl(name) {
            return 'https://ui-avatars.com/api/?name=' + encodeURIComponent(name || 'Leader') + '&background=3b82f6&color=fff&size=128';
        }

        function syncLeaderAvatar(name, profilePhoto = '') {
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
                img.src = profilePhoto || leaderAvatarFallbackUrl(n);
            }
        }

        document.getElementById('leader-avatar-img')?.addEventListener('error', function () {
            this.style.display = 'none';
            const initialsEl = document.getElementById('leader-initials');
            if (initialsEl) {
                initialsEl.textContent = leaderInitialsFromName(document.getElementById('leader-name')?.textContent || 'Leader');
                initialsEl.classList.remove('hidden');
                initialsEl.classList.add('flex', 'items-center', 'justify-center');
            }
        });

        // Keep leader/avatar synced with backend value.
        syncLeaderAvatar(document.getElementById('leader-name')?.textContent || 'Leader', initialLeaderPhoto || '');
        if (hideDailyScore) {
            document.getElementById('daily-score-header')?.classList.add('hidden');
        }

        const headerLogo = document.getElementById('headerKredipalLogo');
        if (headerLogo) {
            if (headerLogo.complete) {
                cleanCheckerBackground(headerLogo);
            } else {
                headerLogo.addEventListener('load', () => cleanCheckerBackground(headerLogo), { once: true });
            }
        }

        // TV auto-pagination for employee rows (10 rows/page).
        function initTablePagination() {
            if (tablePaginationTimer) {
                clearInterval(tablePaginationTimer);
                tablePaginationTimer = null;
            }
            const rows = Array.from(document.querySelectorAll('.employee-row'));
            const indicator = document.getElementById('table-page-indicator');
            const pageSize = 10;

            if (!rows.length || !indicator) return;

            const totalPages = Math.max(1, Math.ceil(rows.length / pageSize));
            let currentPage = 0;

            function renderIndicator() {
                if (totalPages <= 1) {
                    indicator.innerHTML = '';
                    return;
                }

                const dots = Array.from({ length: totalPages }, (_, index) => {
                    const activeClass = index === currentPage ? 'page-dot active' : 'page-dot';
                    return `<span class="${activeClass}"></span>`;
                }).join('');

                indicator.innerHTML = `<span class="mr-1">↻</span>${dots}<span class="ml-1">Page ${currentPage + 1}/${totalPages}</span>`;
            }

            function showPage(pageIndex) {
                const start = pageIndex * pageSize;
                const end = start + pageSize;
                rows.forEach((row, idx) => {
                    row.style.display = idx >= start && idx < end ? '' : 'none';
                });
                currentPage = pageIndex;
                renderIndicator();
            }

            showPage(0);

            if (totalPages > 1) {
                tablePaginationTimer = setInterval(() => {
                    showPage((currentPage + 1) % totalPages);
                }, 10000);
            }
        }

        initTablePagination();

        if (pusherKey && window.Pusher) {
            const pusher = new Pusher(pusherKey, {
                cluster: pusherCluster || 'mt1',
                forceTLS: true,
            });
            const channel = pusher.subscribe('admin-live-dashboard');
            channel.bind('dashboard.updated', () => {
                refreshDashboardData();
            });
        }
    </script>
</body>
</html>
