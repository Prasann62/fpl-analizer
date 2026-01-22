<?php
session_start();
if(!isset($_SESSION['access'])){
  header('location:loginform.php');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <?php include 'favicon-meta.php'; ?>
    <title>AI Freehit Picker | FPL Manager</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="style.css?v=<?php echo time(); ?>" rel="stylesheet">
    <style>
        /* Hero Gradient */
        .hero-gradient {
            background: linear-gradient(135deg, rgba(255, 59, 48, 0.08) 0%, rgba(255, 149, 0, 0.08) 100%);
            border-radius: 20px;
            padding: 2rem;
            margin-bottom: 2rem;
            border: 2px solid rgba(255, 59, 48, 0.2);
        }

        /* Pitch Background */
        .pitch-bg {
            background: linear-gradient(0deg, #1a4d2e 0%, #2a7a4b 50%, #1a4d2e 100%);
            border: 2px solid rgba(255,255,255,0.2);
            border-radius: 16px;
            padding: 2rem 1rem;
            position: relative;
            box-shadow: 0 12px 40px rgba(0,0,0,0.15);
        }
        
        .pitch-line {
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            height: 2px;
            background: rgba(255,255,255,0.2);
        }

        /* Player Cards */
        .player-card {
            background: linear-gradient(145deg, #ffffff, #f8f9fa);
            border-radius: 10px;
            padding: 0.6rem;
            text-align: center;
            font-size: 0.8rem;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            transition: all 0.3s ease;
            cursor: pointer;
            margin: 0.3rem;
            position: relative;
            border: 2px solid transparent;
        }
        
        .player-card:hover {
            transform: translateY(-5px) scale(1.05);
            box-shadow: 0 8px 25px rgba(0,0,0,0.25);
            border-color: var(--primary-color);
            z-index: 10;
        }

        .player-name {
            font-weight: 800;
            color: var(--primary-color);
            font-size: 0.85rem;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            margin: 0.3rem 0;
        }

        .player-team {
            font-size: 0.65rem;
            color: #666;
            text-transform: uppercase;
            font-weight: 600;
        }

        .player-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 0.4rem;
            font-size: 0.7rem;
        }

        .player-price {
            background: var(--secondary-color);
            color: var(--primary-color);
            padding: 2px 6px;
            border-radius: 4px;
            font-weight: 700;
        }

        .player-form {
            background: linear-gradient(135deg, #4CAF50, #81C784);
            color: white;
            padding: 2px 6px;
            border-radius: 4px;
            font-weight: 700;
        }

        /* Fixture Ticker */
        .fixture-ticker {
            display: flex;
            gap: 3px;
            justify-content: center;
            margin-top: 0.3rem;
        }

        .fixture-dot {
            width: 24px;
            height: 24px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.6rem;
            font-weight: 700;
            color: white;
            text-shadow: 0 1px 2px rgba(0,0,0,0.3);
        }

        .diff-1 { background: linear-gradient(135deg, #1b5e20, #4caf50); }
        .diff-2 { background: linear-gradient(135deg, #388e3c, #81c784); }
        .diff-3 { background: linear-gradient(135deg, #f57c00, #ffb74d); }
        .diff-4 { background: linear-gradient(135deg, #d84315, #ff7043); }
        .diff-5 { background: linear-gradient(135deg, #b71c1c, #e57373); }

        /* Badge Styles */
        .news-badge {
            position: absolute;
            top: -5px;
            right: -5px;
            background: linear-gradient(135deg, #f44336, #e91e63);
            color: white;
            font-size: 0.6rem;
            padding: 2px 5px;
            border-radius: 8px;
            font-weight: 700;
            z-index: 5;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.1); }
        }

        .differential-badge {
            position: absolute;
            top: -5px;
            left: -5px;
            background: linear-gradient(135deg, #9c27b0, #ce93d8);
            color: white;
            font-size: 0.6rem;
            padding: 2px 5px;
            border-radius: 8px;
            font-weight: 700;
            z-index: 5;
        }

        /* Stat Cards */
        .stat-card {
            background: linear-gradient(135deg, #ffffff, #f8f9fa);
            border-radius: 16px;
            padding: 1.5rem;
            text-align: center;
            border: 2px solid #e9ecef;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .stat-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 30px rgba(0,0,0,0.1);
        }

        .stat-card .stat-icon {
            position: absolute;
            right: 10px;
            top: 10px;
            font-size: 3rem;
            opacity: 0.1;
        }

        .stat-value {
            font-size: 2rem;
            font-weight: 800;
            background: linear-gradient(135deg, var(--primary-color), #00ff85);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .stat-label {
            text-transform: uppercase;
            font-size: 0.7rem;
            color: #666;
            font-weight: 700;
            margin-top: 0.5rem;
        }

        /* Chip Badge */
        .chip-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background: linear-gradient(135deg, #ff6b6b, #ee5a6f);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-weight: 700;
            font-size: 0.9rem;
            box-shadow: 0 4px 15px rgba(255, 107, 107, 0.3);
        }

        /* Loading Animation */
        .loading-container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 3rem;
            box-shadow: 0 12px 40px rgba(0,0,0,0.1);
        }

        /* Button Styles */
        .btn-freehit {
            background: linear-gradient(135deg, #ff6b6b, #ee5a6f);
            border: none;
            color: white;
            font-weight: 700;
            padding: 0.75rem 2rem;
            border-radius: 12px;
            box-shadow: 0 6px 20px rgba(255, 107, 107, 0.4);
            transition: all 0.3s ease;
        }

        .btn-freehit:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 30px rgba(255, 107, 107, 0.5);
            color: white;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .player-card { font-size: 0.7rem; padding: 0.4rem; }
            .fixture-dot { width: 18px; height: 18px; font-size: 0.5rem; }
        }
    </style>
</head>
<body>
<?php include 'navbar.php';?>

<div class="main-content">
    <div class="container py-5">
        <!-- Hero Header -->
        <div class="hero-gradient">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <h1 class="display-5 fw-bold mb-2">
                        <i class="bi bi-lightning-charge-fill text-warning"></i>
                        Free Hit Picker AI
                    </h1>
                    <p class="lead mb-3">Optimize your team for a single gameweek with unlimited transfers</p>
                    <div class="chip-badge">
                        <i class="bi bi-stars"></i>
                        <span>Single GW Strategy</span>
                    </div>
                </div>
                <div class="col-lg-4 mt-4 mt-lg-0">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <h6 class="fw-bold mb-2"><i class="bi bi-info-circle me-2"></i>How It Works</h6>
                            <ul class="small mb-0">
                                <li>Analyzes fixtures for chosen GW</li>
                                <li>Considers form, news & injuries</li>
                                <li>Finds best 15-player squad</li>
                                <li>Perfect for DGW/BGW weeks</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Step 1: Select Gameweek -->
        <div class="card mb-4" id="step1">
            <div class="card-body">
                <h5 class="card-title fw-bold mb-3">
                    <span class="badge bg-primary me-2">1</span>
                    Select Target Gameweek
                </h5>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Manager ID</label>
                        <div class="input-group input-group-lg">
                            <span class="input-group-text"><i class="bi bi-person-badge"></i></span>
                            <input type="number" id="managerId" class="form-control" placeholder="Enter your Manager ID">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Gameweek</label>
                        <select class="form-select form-select-lg" id="gwSelect">
                            <option value="" selected disabled>Loading gameweeks...</option>
                        </select>
                    </div>
                </div>
                <div class="mt-4">
                    <button class="btn btn-freehit btn-lg w-100" id="analyzeBtn">
                        <i class="bi bi-cpu me-2"></i>
                        Build Optimal Free Hit Team
                    </button>
                </div>
            </div>
        </div>

        <!-- Loading State -->
        <div id="loading" class="d-none">
            <div class="loading-container text-center">
                <div class="spinner-border text-danger" role="status" style="width: 4rem; height: 4rem;"></div>
                <h4 class="mt-4 fw-bold" id="loadingText">Initializing Free Hit Analysis...</h4>
                <div class="progress mt-3 mx-auto" style="width: 60%; height: 8px;">
                    <div class="progress-bar progress-bar-striped progress-bar-animated bg-danger" id="progressBar" style="width: 0%"></div>
                </div>
            </div>
        </div>

        <!-- Results -->
        <div id="results" class="d-none">
            <!-- Stats Summary -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="stat-card">
                        <i class="bi bi-calendar-event stat-icon"></i>
                        <div class="stat-value" id="statGW">-</div>
                        <div class="stat-label">Gameweek</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card">
                        <i class="bi bi-people stat-icon"></i>
                        <div class="stat-value" id="statPlayers">15</div>
                        <div class="stat-label">Players Selected</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card">
                        <i class="bi bi-currency-pound stat-icon"></i>
                        <div class="stat-value" id="statCost">£0.0m</div>
                        <div class="stat-label">Total Cost</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card">
                        <i class="bi bi-trophy stat-icon"></i>
                        <div class="stat-value" id="statxP">0</div>
                        <div class="stat-label">Expected Points</div>
                    </div>
                </div>
            </div>

            <!-- Pitch View -->
            <div class="row">
                <div class="col-lg-9">
                    <div class="card shadow-lg border-0">
                        <div class="card-header bg-white border-0">
                            <h5 class="fw-bold mb-0">
                                <i class="bi bi-diagram-3 me-2"></i>
                                Starting XI
                            </h5>
                        </div>
                        <div class="card-body p-0">
                            <div class="pitch-bg" id="pitch">
                                <div class="pitch-line"></div>
                                <div class="row text-center mb-3 justify-content-center" id="gkpRow"></div>
                                <div class="row text-center mb-3 justify-content-center" id="defRow"></div>
                                <div class="row text-center mb-3 justify-content-center" id="midRow"></div>
                                <div class="row text-center justify-content-center" id="fwdRow"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Bench -->
                    <div class="card mt-3 shadow-sm">
                        <div class="card-header bg-light">
                            <h6 class="fw-bold mb-0"><i class="bi bi-box-seam me-2"></i>Substitutes</h6>
                        </div>
                        <div class="card-body">
                            <div class="row" id="benchRow"></div>
                        </div>
                    </div>
                </div>

                <!-- Side Panel -->
                <div class="col-lg-3 mt-4 mt-lg-0">
                    <!-- Budget -->
                    <div class="card shadow-sm mb-3 bg-success text-white">
                        <div class="card-body text-center">
                            <small class="text-uppercase fw-bold opacity-75">Remaining Budget</small>
                            <h2 class="fw-bold mb-0" id="budgetRemaining">£0.0m</h2>
                        </div>
                    </div>

                    <!-- Key Picks -->
                    <div class="card shadow-sm">
                        <div class="card-header bg-white fw-bold">
                            <i class="bi bi-star-fill text-warning me-2"></i>Key Insights
                        </div>
                        <ul class="list-group list-group-flush" id="insightsList">
                            <li class="list-group-item">Analyzing fixtures...</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php';?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
    // DOM Elements
    const step1 = document.getElementById('step1');
    const loading = document.getElementById('loading');
    const results = document.getElementById('results');
    const managerIdInput = document.getElementById('managerId');
    const gwSelect = document.getElementById('gwSelect');
    const analyzeBtn = document.getElementById('analyzeBtn');

    // State
    let bootstrapData = null;
    let fixtures = null;
    let teams = {};

    // Initialize - Load Gameweeks
    (async function init() {
        try {
            const res = await fetch('api.php?endpoint=bootstrap-static/');
            bootstrapData = await res.json();
            
            // Store teams
            bootstrapData.teams.forEach(t => teams[t.id] = t);

            // Populate gameweeks
            gwSelect.innerHTML = '<option value="" selected disabled>Choose gameweek...</option>';
            bootstrapData.events.forEach(gw => {
                const opt = document.createElement('option');
                opt.value = gw.id;
                opt.text = `GW${gw.id} - ${gw.name}${gw.is_current ? ' (Current)' : ''}`;
                if (gw.is_current) opt.selected = true;
                gwSelect.appendChild(opt);
            });

            // Load fixtures
            const fixturesRes = await fetch('api.php?endpoint=fixtures/');
            fixtures = await fixturesRes.json();

        } catch (e) {
            console.error('Init failed:', e);
            alert('Failed to load FPL data. Please refresh.');
        }
    })();

    // Analyze Button
    analyzeBtn.addEventListener('click', async () => {
        const managerId = managerIdInput.value.trim();
        const targetGW = gwSelect.value;

        if (!managerId) return alert('Please enter Manager ID');
        if (!targetGW) return alert('Please select a gameweek');

        // Hide step1, show loading
        step1.classList.add('d-none');
        loading.classList.remove('d-none');
        results.classList.add('d-none');

        const updateProgress = (pct, txt) => {
            document.getElementById('progressBar').style.width = pct + '%';
            document.getElementById('loadingText').innerText = txt;
        };

        try {
            // Step 1: Get GW fixtures
            updateProgress(20, 'Analyzing fixtures for GW' + targetGW + '...');
            const gwFixtures = fixtures.filter(f => f.event == targetGW);
            
            // Step 2: Calculate fixture difficulty per team
            updateProgress(40, 'Calculating fixture difficulty...');
            const teamFixtures = {};
            gwFixtures.forEach(f => {
                teamFixtures[f.team_h] = { opponent: f.team_a, difficulty: f.team_h_difficulty, home: true };
                teamFixtures[f.team_a] = { opponent: f.team_h, difficulty: f.team_a_difficulty, home: false };
            });

            // Step 3: Score players
            updateProgress(60, 'Scoring all players...');
            const players = {};
            bootstrapData.elements.forEach(p => {
                if (p.status !== 'a') return; // Skip unavailable

                const teamFix = teamFixtures[p.team];
                if (!teamFix) return; // No fixture this GW

                // Calculate score
                const formScore = parseFloat(p.form) || 0;
                const fixtureDiffScore = (6 - teamFix.difficulty) * 1.5; // Lower difficulty = better
                const pointsPerGameScore = parseFloat(p.points_per_game) || 0;
                const selectedByScore = (100 - parseFloat(p.selected_by_percent)) / 20; // Differential bonus
                
                p.fhScore = formScore + fixtureDiffScore + pointsPerGameScore + selectedByScore;
                p.fixtureInfo = teamFix;
                players[p.id] = p;
            });

            // Step 4: Build optimal team
            updateProgress(80, 'Building optimal Free Hit team...');
            const team = buildOptimalTeam(players, targetGW);

            // Step 5: Render
            updateProgress(100, 'Complete!');
            renderResults(team, targetGW);

            setTimeout(() => {
                loading.classList.add('d-none');
                results.classList.remove('d-none');
            }, 500);

        } catch (e) {
            console.error('Analysis failed:', e);
            alert('Analysis failed: ' + e.message);
            loading.classList.add('d-none');
            step1.classList.remove('d-none');
        }
    });

    function buildOptimalTeam(players, targetGW) {
        const team = { 1: [], 2: [], 3: [], 4: [] };
        const maxLimits = { 1: 2, 2: 5, 3: 5, 4: 3 };
        const teamCounts = {};
        let totalCost = 0;
        const BUDGET = 1000; // £100.0m

        // Get players by position, sorted by score
        const byPosition = { 1: [], 2: [], 3: [], 4: [] };
        Object.values(players).forEach(p => {
            if (byPosition[p.element_type]) {
                byPosition[p.element_type].push(p);
            }
        });

        // Sort each position by score
        Object.keys(byPosition).forEach(pos => {
            byPosition[pos].sort((a, b) => b.fhScore - a.fhScore);
        });

        // Fill starting XI (best formation: 1-4-4-2 or 1-3-5-2)
        const formations = [
            { 1: 1, 2: 4, 3: 4, 4: 2 },
            { 1: 1, 2: 3, 3: 5, 4: 2 },
            { 1: 1, 2: 3, 3: 4, 4: 3 },
            { 1: 1, 2: 5, 3: 4, 4: 1 }
        ];

        let bestTeam = null;
        let bestScore = -1;

        formations.forEach(formation => {
            const testTeam = { 1: [], 2: [], 3: [], 4: [] };
            const testCounts = {};
            let testCost = 0;
            let teamScore = 0;

            // Try to fill this formation
            for (let pos = 1; pos <= 4; pos++) {
                const needed = formation[pos];
                let added = 0;

                for (const p of byPosition[pos]) {
                    if (added >= needed) break;
                    if ((testCounts[p.team] || 0) >= 3) continue;
                    if (testCost + p.now_cost > BUDGET) continue;

                    testTeam[pos].push(p);
                    testCounts[p.team] = (testCounts[p.team] || 0) + 1;
                    testCost += p.now_cost;
                    teamScore += p.fhScore;
                    added++;
                }

                if (added < needed) {
                    teamScore = -1; // Invalid formation
                    break;
                }
            }

            if (teamScore > bestScore) {
                bestScore = teamScore;
                bestTeam = { team: testTeam, counts: testCounts, cost: testCost };
            }
        });

        if (!bestTeam) throw new Error('Could not build valid team');

        // Now fill bench (1 GK, 3 outfield)
        const bench = { 1: [], 2: [], 3: [], 4: [] };
        
        // Add 1 GK
        for (const p of byPosition[1]) {
            if (bestTeam.team[1].some(tp => tp.id === p.id)) continue;
            if ((bestTeam.counts[p.team] || 0) >= 3) continue;
            if (bestTeam.cost + p.now_cost > BUDGET) continue;
            bench[1].push(p);
            bestTeam.counts[p.team] = (bestTeam.counts[p.team] || 0) + 1;
            bestTeam.cost += p.now_cost;
            break;
        }

        // Add 3 outfield (cheapest possible)
        let benchAdded = 0;
        for (let pos = 2; pos <= 4; pos++) {
            const sorted = [...byPosition[pos]].sort((a, b) => a.now_cost - b.now_cost);
            for (const p of sorted) {
                if (benchAdded >= 3) break;
                if (bestTeam.team[pos].some(tp => tp.id === p.id)) continue;
                if (bench[pos].length > 0) continue;
                if ((bestTeam.counts[p.team] || 0) >= 3) continue;
                if (bestTeam.cost + p.now_cost > BUDGET) continue;

                bench[pos].push(p);
                bestTeam.counts[p.team] = (bestTeam.counts[p.team] || 0) + 1;
                bestTeam.cost += p.now_cost;
                benchAdded++;
            }
        }

        return {
            starting: bestTeam.team,
            bench: bench,
            totalCost: bestTeam.cost,
            score: bestScore
        };
    }

    function renderResults(team, targetGW) {
        // Update stats
        document.getElementById('statGW').innerText = targetGW;
        document.getElementById('statCost').innerText = '£' + (team.totalCost / 10).toFixed(1) + 'm';
        document.getElementById('budgetRemaining').innerText = '£' + ((1000 - team.totalCost) / 10).toFixed(1) + 'm';

        // Calculate expected points
        let totalxP = 0;
        Object.values(team.starting).flat().forEach(p => {
            totalxP += parseFloat(p.event_points) || parseFloat(p.form) || 0;
        });
        document.getElementById('statxP').innerText = Math.round(totalxP);

        // Render pitch
        renderPitch(team.starting);

        // Render bench
        renderBench(team.bench);

        // Render insights
        renderInsights(team);
    }

    function renderPitch(starting) {
        const rows = ['gkpRow', 'defRow', 'midRow', 'fwdRow'];
        
        rows.forEach((rowId, idx) => {
            const pos = idx + 1;
            const container = document.getElementById(rowId);
            container.innerHTML = '';

            starting[pos].forEach(p => {
                const col = document.createElement('div');
                col.className = 'col-auto';

                const teamInfo = teams[p.team];
                const fixtures = getUpcomingFixtures(p.team, 3);
                
                let badges = '';
                if (p.news) badges += '<div class="news-badge">!</div>';
                if (parseFloat(p.selected_by_percent) < 5) badges += '<div class="differential-badge">DIFF</div>';

                col.innerHTML = `
                    <div class="player-card" style="width: 110px;">
                        ${badges}
                        <div class="player-team">${teamInfo.short_name}</div>
                        <div class="player-name" title="${p.web_name}">${p.web_name}</div>
                        <div class="player-meta">
                            <span class="player-price">£${(p.now_cost / 10).toFixed(1)}</span>
                            <span class="player-form">${p.form}</span>
                        </div>
                        <div class="fixture-ticker">
                            ${renderFixtureTicker(fixtures, p.team)}
                        </div>
                    </div>
                `;
                container.appendChild(col);
            });
        });
    }

    function renderBench(bench) {
        const container = document.getElementById('benchRow');
        container.innerHTML = '';

        for (let pos = 1; pos <= 4; pos++) {
            bench[pos].forEach(p => {
                const col = document.createElement('div');
                col.className = 'col-6 col-md-3 mb-2';

                const teamInfo = teams[p.team];

                col.innerHTML = `
                    <div class="card">
                        <div class="card-body p-2 text-center">
                            <small class="text-muted">${teamInfo.short_name}</small>
                            <div class="fw-bold small">${p.web_name}</div>
                            <small class="text-success">£${(p.now_cost / 10).toFixed(1)}m</small>
                        </div>
                    </div>
                `;
                container.appendChild(col);
            });
        }
    }

    function renderInsights(team) {
        const list = document.getElementById('insightsList');
        list.innerHTML = '';

        // Count differentials
        const allPlayers = Object.values(team.starting).flat();
        const diffs = allPlayers.filter(p => parseFloat(p.selected_by_percent) < 5);
        
        list.innerHTML += `<li class="list-group-item"><strong>${diffs.length}</strong> differential picks with &lt;5% ownership</li>`;
        
        // Find best fixture
        const bestFixture = allPlayers.reduce((best, p) => {
            return (!best || p.fixtureInfo.difficulty < best.fixtureInfo.difficulty) ? p : best;
        }, null);
        
        if (bestFixture) {
            const oppTeam = teams[bestFixture.fixtureInfo.opponent];
            list.innerHTML += `<li class="list-group-item">Best fixture: <strong>${bestFixture.web_name}</strong> vs ${oppTeam.short_name}</li>`;
        }

        // News alerts
        const newsPlayers = allPlayers.filter(p => p.news);
        if (newsPlayers.length > 0) {
            list.innerHTML += `<li class="list-group-item text-warning"><i class="bi bi-exclamation-triangle"></i> ${newsPlayers.length} player(s) with news alerts</li>`;
        }
    }

    function getUpcomingFixtures(teamId, count) {
        return fixtures
            .filter(f => (f.team_h === teamId || f.team_a === teamId) && !f.finished)
            .slice(0, count);
    }

    function renderFixtureTicker(fixtures, teamId) {
        return fixtures.slice(0, 3).map(f => {
            const isHome = f.team_h === teamId;
            const difficulty = isHome ? f.team_h_difficulty : f.team_a_difficulty;
            const opponent = teams[isHome ? f.team_a : f.team_h];
            return `<div class="fixture-dot diff-${difficulty}" title="vs ${opponent.short_name}">${opponent.short_name.charAt(0)}</div>`;
        }).join('');
    }
</script>
</body>
</html>
