<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kredipal Team Leader Live Performance Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <style>
        /* CSS STYLES */
        :root {
            --bg-dark: #121827; /* Main screen background */
            --panel-dark: #1e2a3a; /* Darker grey for panels */
            --orange-primary: #e67e22;
            --orange-dim: #965318;
            --red-alert: #e74c3c;
            --green-primary: #27ae60;
            --blue-primary: #3498db;
            --gold: #f1c40f;
            --text-white: #ecf0f1;
            --text-grey: #bdc3c7;
            --lime-green: #2ecc71;
            --font-primary: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background-color: #000; /* Outer screen context */
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            font-family: var(--font-primary);
            overflow: hidden;
        }

        /* simulated TV monitor frame */
        .monitor-bezel {
            width: 1280px;
            height: 720px;
            border: 20px solid #222;
            border-radius: 10px;
            background-color: var(--bg-dark);
            box-shadow: 0 0 50px rgba(0,0,0,0.7);
            overflow: hidden;
            display: flex;
            flex-direction: column;
            position: relative;
        }

        /* TV reflection effect */
        .monitor-bezel::after {
            content: '';
            position: absolute;
            top: -20%;
            left: -20%;
            width: 140%;
            height: 140%;
            background: radial-gradient(circle at 75% 10%, rgba(255, 255, 255, 0.05) 0%, rgba(255, 255, 255, 0) 60%);
            pointer-events: none;
            z-index: 10;
        }

        /* Generic Helper Classes */
        .orange { color: var(--orange-primary); }
        .green { color: var(--green-primary); }
        .blue { color: var(--blue-primary); }
        .red { color: var(--red-alert); }
        .gold { color: var(--gold); }
        .lime { color: var(--lime-green); }
        .text-dim { color: var(--text-grey); }
        .bold { font-weight: bold; }

        /* HEADER SECTION */
        .main-header {
            background-color: #0d121c;
            height: 70px;
            border-bottom: 3px solid var(--orange-dim);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 30px;
            box-sizing: border-box;
        }

        .logo-area {
            display: flex;
            align-items: center;
            color: var(--orange-primary);
            font-size: 26px;
            font-weight: bold;
        }
        .logo-icon { font-size: 32px; margin-right: 12px; }

        .title-area {
            text-align: center;
            display: flex;
            flex-direction: column;
        }
        .main-title { color: #fff; margin: 0; font-size: 20px; letter-spacing: 1px;}
        .subtitle { color: var(--text-grey); margin: 0; font-size: 11px; text-transform: uppercase; letter-spacing: 2px;}

        .status-area {
            text-align: right;
            font-size: 13px;
            color: var(--text-grey);
            display: flex;
            flex-direction: column;
        }
        .time-date-row { margin-bottom: 4px; }
        .total-tl-count { font-size: 16px; color: #fff; }
        .total-tl-count span { color: var(--orange-primary); font-weight: bold; }
        .update-time { color: var(--gold); }

        /* DASHBOARD CONTENT */
        .dashboard-content {
            flex: 1;
            display: flex;
            padding: 10px 20px;
            box-sizing: border-box;
            gap: 20px;
        }

        /* LEFT PANEL: The Grid */
        .left-panel {
            flex: 7;
            background-color: rgba(20, 30, 45, 0.7);
            border-radius: 5px;
            padding: 15px;
            box-sizing: border-box;
            display: flex;
            flex-direction: column;
        }

        .growth-ticker {
            text-align: center;
            font-size: 14px;
            color: var(--orange-primary);
            margin-bottom: 10px;
            border-bottom: 1px solid #333;
            padding-bottom: 5px;
        }

        .ranking-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
        }
        .ranking-table th, .ranking-table td {
            text-align: left;
            padding: 11px 10px;
            border-bottom: 1px solid rgba(255,255,255,0.05);
        }
        .ranking-table th {
            color: var(--text-grey);
            font-weight: normal;
            text-transform: uppercase;
            font-size: 11px;
            border-bottom: 1px solid #333;
        }

        /* Column Specific Styles */
        .col-rank { width: 5%; color: var(--gold); font-weight: bold; }
        .col-tl { width: 25%; }
        .tl-profile { display: flex; align-items: center; }
        .tl-avatar { width: 30px; height: 30px; border-radius: 50%; background-color: #333; margin-right: 12px; border: 1px solid #fff;}

        .score-cell { font-weight: bold; width: 60px; text-align: center !important; }

        /* Right Panel: Overall Stats & Spotlights */
        .right-panel {
            flex: 3;
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .summary-box {
            background-color: var(--panel-dark);
            border-radius: 5px;
            padding: 15px;
            box-sizing: border-box;
            box-shadow: 0 5px 15px rgba(0,0,0,0.4);
        }
        .summary-box h3 { margin: 0 0 10px 0; font-size: 16px; color: #fff; }

        .stat-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 14px;
            margin-bottom: 10px;
        }
        .stat-label { color: var(--text-grey); }
        .stat-value { font-weight: bold; color: #fff; }

        .stat-value.large { font-size: 22px; display: flex; align-items: baseline; gap: 5px; }
        .money-box { border: 2px solid var(--gold); padding: 2px 8px; border-radius: 3px; color: var(--gold); }
        .lakh-label { font-size: 13px; color: var(--gold); }

        .update-bar {
            background-color: rgba(20, 30, 45, 0.7);
            padding: 10px;
            border-radius: 5px;
            text-align: center;
            font-size: 14px;
        }

        /* Spotlights Row */
        .spotlights-container {
            flex: 1;
            display: flex;
            gap: 15px;
        }

        .star-section { flex: 6; }
        .alert-section { flex: 4; }

        .panel-header-special {
            text-align: center;
            font-weight: bold;
            font-size: 14px;
            padding: 8px 0;
            border-radius: 5px 5px 0 0;
        }

        .panel-header-star { background-color: var(--gold); color: #333; }
        .panel-header-alert { background-color: var(--red-alert); color: #fff; }

        .panel-body {
            background-color: rgba(255,255,255,0.03);
            border-radius: 0 0 5px 5px;
            padding: 12px;
            display: flex;
            align-items: center;
        }

        /* Star TL Details */
        .star-profile-alt {
            display: flex;
            flex-direction: column;
            align-items: center;
            flex: 1;
            padding-left: 10px;
            border-left: 1px solid #444;
        }
        .star-avatar-large {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background-color: #333;
            border: 2px solid var(--gold);
            margin-bottom: 5px;
        }
        .star-name { font-size: 15px; color: #fff; font-weight: bold; }

        .progress-side {
            flex: 2;
            padding-right: 15px;
        }
        .progress-labels { display: flex; justify-content: space-between; font-size: 11px; color: var(--text-grey); margin-bottom: 3px;}
        .progress-bar-container { height: 12px; background-color: #333; border-radius: 6px; position: relative; overflow: hidden; }
        .progress-bar-fill { height: 100%; border-radius: 6px; background: linear-gradient(90deg, #27ae60, #2ecc71); }

        /* Alert Box */
        .alert-body {
            flex-direction: column;
            gap: 10px;
            justify-content: center;
        }
        .alert-tl-item {
            display: flex;
            justify-content: space-between;
            width: 100%;
            background-color: rgba(231, 76, 60, 0.15);
            padding: 5px 10px;
            border-radius: 3px;
            border: 1px solid rgba(231, 76, 60, 0.3);
            box-sizing: border-box;
            color: #fff;
            font-size: 13px;
        }

        /* FOOTER */
        .main-footer {
            height: 30px;
            background-color: #0b1118;
            border-top: 1px solid #2a3a5a;
            color: var(--text-grey);
            font-size: 11px;
            display: flex;
            justify-content: center;
            align-items: center;
        }

    </style>
</head>
<body>

    <div class="monitor-bezel">
        <header class="main-header">
            <div class="logo-area">
                <i class="fas fa-chart-line logo-icon"></i>
                <span>Kredipal</span>
            </div>
            <div class="title-area">
                <h1 class="main-title">KREDIPAL FINANCIAL SERVICES</h1>
                <p class="subtitle">TEAM LEADER LIVE PERFORMANCE DASHBOARD</p>
            </div>
            <div class="status-area">
                <div class="time-date-row">
                    <span id="currentDate">Tuesday, April 30, 2024</span> |
                    <span id="currentTime">10:45 AM</span> |
                    <i class="far fa-clock"></i> <span id="updateTime" class="update-time">10:44 AM</span>
                </div>
                <div class="total-tl-count">
                    <i class="fas fa-users"></i>
                    TOTAL TL: <span>3</span>
                </div>
            </div>
        </header>

        <main class="dashboard-content">
            <div class="left-panel">
                <div class="growth-ticker">
                    <i class="fas fa-star orange"></i> Daily Leads = Daily Growth <i class="fas fa-star orange"></i>
                </div>
                <table class="ranking-table" id="tlTable">
                    <thead>
                        <tr>
                            <th class="col-rank">Rank</th>
                            <th class="col-tl">Team Leader</th>
                            <th>Team Size</th>
                            <th>Required Leads</th>
                            <th>Achieved Leads</th>
                            <th>Loan Amount</th>
                            <th>Target %</th>
                            <th>Discipline</th>
                            <th class="score-cell">Score</th>
                        </tr>
                    </thead>
                    <tbody>
                        </tbody>
                </table>
            </div>

            <div class="right-panel">
                <div class="summary-box">
                    <h3>Overall Today</h3>
                    <div class="stat-item">
                        <span class="stat-label"><i class="fas fa-user-friends green"></i> Team Members:</span>
                        <span class="stat-value">50+</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-label"><i class="fas fa-tasks blue"></i> Today's Required Leads:</span>
                        <span class="stat-value">50</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-label"><i class="fas fa-chart-line orange"></i> Today's Achieved Leads:</span>
                        <span class="stat-value">42</span>
                    </div>
                    <div class="stat-item large-stat">
                        <span class="stat-label"><i class="fas fa-rupee-sign gold"></i> Total Amount:</span>
                        <div class="stat-value large"><span class="money-box">₹9,000</span> <span class="lakh-label">Lakh</span></div>
                    </div>
                    <div class="stat-item text-dim" style="font-size: 11px;">
                        <span><i class="fas fa-calendar-alt"></i> MTD Target:</span>
                        <span>45 L</span>
                    </div>
                </div>

                <div class="update-bar">
                    <p class="green bold" style="margin: 0;"><i class="fas fa-check-circle"></i> DATA UPDATED AT: <span id="syncTime">10:45 AM</span></p>
                </div>

                <div class="spotlights-container">
                    <div class="star-section">
                        <div class="panel-header-special panel-header-star">
                            <i class="fas fa-star"></i> STAR TL OF THE DAY <i class="fas fa-star"></i>
                        </div>
                        <div class="panel-body">
                            <div class="progress-side">
                                <div class="progress-labels">
                                    <span>Today (42/50)</span>
                                    <span>95</span>
                                </div>
                                <div class="progress-bar-container">
                                    <div class="progress-bar-fill" style="width: 87%;"></div>
                                </div>
                                <p style="font-size: 10px; color: var(--text-grey); margin: 3px 0 0 0;">Team Activity: Monitor Daily Leads</p>
                            </div>
                            <div class="star-profile-alt">
                                <img src="" alt="Star TL" class="star-avatar-large" id="starTLAvatar">
                                <span class="star-name" id="starTLName">TL-01</span>
                                <span class="lime bold"><i class="fas fa-bolt"></i> <span id="starTLPercent">102%</span></span>
                            </div>
                        </div>
                    </div>

                    <div class="alert-section">
                        <div class="panel-header-special panel-header-alert">LOW PERFORMANCE ALERT</div>
                        <div class="panel-body alert-body">
                            <i class="fas fa-exclamation-triangle red" style="font-size: 24px; margin-bottom: 5px;"></i>
                            <div class="alert-tl-item">
                                <span class="bold">TL-09</span> <span class="red">62%</span>
                            </div>
                            <div class="alert-tl-item">
                                <span class="bold">TL-10</span> <span class="red">44%</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>

        <footer class="main-footer">
            <p>"Top leaders Don't force for results - they produce them daily."</p>
        </footer>
    </div>

    <script>
        // JAVASCRIPT CODE

        // 1. Data Structure based on the image
        const teamData = [
            { rank: 1, name: "TL-01", avatar: "", size: 5, requiredLeads: 5, achievedLeads: 5, loanAmount: 920, targetPercent: 102, discipline: 25, score: 95 },
            { rank: 2, name: "TL-02", avatar: "", size: 6, requiredLeads: 5, achievedLeads: 5, loanAmount: 400, targetPercent: 88, discipline: 25, score: 95 },
            { rank: 3, name: "TL-03", avatar: "", size: 4, requiredLeads: 5, achievedLeads: 5, loanAmount: 590, targetPercent: 17, discipline: 18, score: 80 },
            { rank: 4, name: "TL-04", avatar: "", size: 4, requiredLeads: 5, achievedLeads: 3, loanAmount: 385, targetPercent: 18, discipline: 15, score: 50 },
            { rank: 5, name: "TL-05", avatar: "", size: 4, requiredLeads: 5, achievedLeads: 2, loanAmount: 380, targetPercent: 15, discipline: 12, score: 50 },
            { rank: 6, name: "TL-06", avatar: "", size: 5, requiredLeads: 5, achievedLeads: 3, loanAmount: 390, targetPercent: 14, discipline: 12, score: 56 },
            { rank: 7, name: "TL-07", avatar: "", size: 5, requiredLeads: 5, achievedLeads: 3, loanAmount: 100, targetPercent: 12, discipline: 12, score: 50 },
        ];

        // Placeholder avatars (since we can't bundle images in one file easily)
        const placeholders = [
            'https://via.placeholder.com/30/f1c40f/333?text=🏆', // Rank 1 Gold
            'https://via.placeholder.com/30/bdc3c7/333?text=🥈', // Rank 2 Silver
            'https://via.placeholder.com/30/cd7f32/333?text=🥉', // Rank 3 Bronze
            'https://via.placeholder.com/30/333/fff?text=T4',
            'https://via.placeholder.com/30/333/fff?text=T5',
            'https://via.placeholder.com/30/333/fff?text=T6',
            'https://via.placeholder.com/30/333/fff?text=T7'
        ];

        // Update avatars in data
        teamData.forEach((tl, index) => { tl.avatar = placeholders[index]; });

        // 2. DOM Elements
        const tableBody = document.querySelector('#tlTable tbody');
        const starName = document.getElementById('starTLName');
        const starAvatar = document.getElementById('starTLAvatar');
        const starPercent = document.getElementById('starTLPercent');

        // 3. Render Table
        function renderTable(data) {
            tableBody.innerHTML = ''; // Clear previous data

            data.forEach(tl => {
                const row = document.createElement('tr');

                // Color coding logic
                let targetColor = tl.targetPercent >= 100 ? 'lime' : (tl.targetPercent < 50 ? 'red' : 'gold');
                let scoreColor = tl.score >= 90 ? 'lime' : (tl.score < 60 ? 'red' : 'gold');

                row.innerHTML = `
                    <td class="col-rank">${tl.rank}</td>
                    <td class="col-tl">
                        <div class="tl-profile">
                            <img src="${tl.avatar}" alt="Avatar" class="tl-avatar">
                            <span class="bold text-white">${tl.name}</span>
                        </div>
                    </td>
                    <td>${tl.size}</td>
                    <td>${tl.requiredLeads}</td>
                    <td class="bold text-white">${tl.achievedLeads}</td>
                    <td class="bold gold">₹${tl.loanAmount.toLocaleString()}</td>
                    <td class="bold ${targetColor}">${tl.targetPercent}%</td>
                    <td>${tl.discipline}</td>
                    <td class="score-cell bold ${scoreColor}">${tl.score}</td>
                `;
                tableBody.appendChild(row);
            });
        }

        // 4. Update Star TL Spotlight
        function updateSpotlights(data) {
            // Assume the top performer in sorted data is the star
            const topPerf = data[0];
            starName.textContent = topPerf.name;
            starAvatar.src = topPerf.avatar;
            starPercent.textContent = topPerf.targetPercent + '%';
        }

        // 5. Clock and Date Functionality
        function updateClock() {
            const now = new Date();
            const dateStr = now.toLocaleDateString('en-US', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
            const timeStr = now.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' });

            document.getElementById('currentDate').textContent = dateStr;
            document.getElementById('currentTime').textContent = timeStr;

            // For simulation: update time is just one minute before current time
            const minuteAgo = new Date(now.getTime() - 60000);
            const syncTimeStr = minuteAgo.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' });
            document.getElementById('updateTime').textContent = syncTimeStr;
            document.getElementById('syncTime').textContent = timeStr; // Data is "just updated"
        }

        // Initialize dashboard
        renderTable(teamData);
        updateSpotlights(teamData);
        updateClock();

        // Update clock every minute
        setInterval(updateClock, 60000);

    </script>
</body>
</html>
