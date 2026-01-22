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
    <title>AI Wildcard Picker | FPL Manager</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="style.css?v=<?php echo time(); ?>" rel="stylesheet">
    <style>
        /* Hero Gradient */
        .hero-gradient {
            background: linear-gradient(135deg, rgba(138, 43, 226, 0.08) 0%, rgba(75, 0, 130, 0.08) 100%);
            border-radius: 20px;
            padding: 2rem;
            margin-bottom: 2rem;
            border: 2px solid rgba(138, 43, 226, 0.2);
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

        .player-ownership {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            padding: 2px 6px;
            border-radius: 4px;
            font-weight: 700;
            font-size: 0.65rem;
        }

        /* Fixture Run Display */
        .fixture-run {
            display: flex;
            gap: 2px;
            justify-content: center;
            margin-top: 0.3rem;
        }

        .fixture-box {
            width: 18px;
            height: 18px;
            border-radius: 3px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.5rem;
            font-weight: 700;
            color: white;
        }

        .diff-1 { background: linear-gradient(135deg, #1b5e20, #4caf50); }
        .diff-2 { background: linear-gradient(135deg, #388e3c, #81c784); }
        .diff-3 { background: linear-gradient(135deg, #f57c00, #ffb74d); }
        .diff-4 { background: linear-gradient(135deg, #d84315, #ff7043); }
        .diff-5 { background: linear-gradient(135deg, #b71c1c, #e57373); }

        /* Badge Styles */
        .template-badge {
            position: absolute;
            top: -5px;
            left: -5px;
            background: linear-gradient(135deg, #2196F3, #64B5F6);
            color: white;
            font-size: 0.6rem;
            padding: 2px 5px;
            border-radius: 8px;
            font-weight: 700;
            z-index: 5;
        }

        .differential-badge {
            position: absolute;
            top: -5px;
            right: -5px;
            background: linear-gradient(135deg, #9c27b0, #ce93d8);
            color: white;
            font-size: 0.6rem;
            padding: 2px 5px;
            border-radius: 8px;
            font-weight: 700;
            z-index: 5;
        }

        .rising-badge {
            position: absolute;
            bottom: -5px;
            right: -5px;
            background: linear-gradient(135deg, #4CAF50, #81C784);
            color: white;
            font-size: 0.55rem;
            padding: 2px 4px;
            border-radius: 6px;
            font-weight: 700;
        }

        /* Transfer Card */
        .transfer-card {
            background: white;
            border-radius: 12px;
            padding: 1rem;
            margin-bottom: 1rem;
            border: 2px solid #e9ecef;
            transition: all 0.3s ease;
        }

        .transfer-card:hover {
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            transform: translateY(-2px);
        }

        .player-out {
            background: linear-gradient(135deg, #ffebee, #ffcdd2);
            border-left: 4px solid #f44336;
            padding: 0.75rem;
            border-radius: 8px;
        }

        .player-in {
            background: linear-gradient(135deg, #e8f5e9, #c8e6c9);
            border-left: 4px solid #4caf50;
            padding: 0.75rem;
            border-radius: 8px;
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
            background: linear-gradient(135deg, #8a2be2, #9370db);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-weight: 700;
            font-size: 0.9rem;
            box-shadow: 0 4px 15px rgba(138, 43, 226, 0.3);
        }

        /* Button Styles */
        .btn-wildcard {
            background: linear-gradient(135deg, #8a2be2, #9370db);
            border: none;
            color: white;
            font-weight: 700;
            padding: 0.75rem 2rem;
            border-radius: 12px;
            box-shadow: 0 6px 20px rgba(138, 43, 226, 0.4);
            transition: all 0.3s ease;
        }

        .btn-wildcard:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 30px rgba(138, 43, 226, 0.5);
            color: white;
        }

        /* Fixture Difficulty Table */
        .fixture-table {
            font-size: 0.75rem;
        }

        .fixture-cell {
            padding: 0.5rem;
            text-align: center;
            font-weight: 600;
            border-radius: 4px;
        }

        /* Loading */
        .loading-container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 3rem;
            box-shadow: 0 12px 40px rgba(0,0,0,0.1);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .player-card { font-size: 0.7rem; padding: 0.4rem; }
            .fixture-box { width: 15px; height: 15px; }
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
                        <i class="bi bi-stars text-warning"></i>
                        Wildcard Picker AI
                    </h1>
                    <p class="lead mb-3">Build the optimal long-term squad with unlimited transfers</p>
                    <div class="chip-badge">
                        <i class="bi bi-infinity"></i>
                        <span>Multi-GW Optimization</span>
                    </div>
                </div>
                <div class="col-lg-4 mt-4 mt-lg-0">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <h6 class="fw-bold mb-2"><i class="bi bi-info-circle me-2"></i>Strategy</h6>
                            <ul class="small mb-0">
                                <li>Analyzes next 5-8 gameweeks</li>
                                <li>Balances template & differentials</li>
                                <li>Maximizes team value</li>
                                <li>Optimal fixture swing timing</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Step 1: Load Current Team -->
        <div class="card mb-4" id="step1">
            <div class="card-body">
                <h5 class="card-title fw-bold mb-3">
                    <span class="badge bg-primary me-2">1</span>
                    Load Your Current Team
                </h5>
                <div class="row g-3">
                    <div class="col-md-8">
                        <label class="form-label fw-bold">Manager ID</label>
                        <div class="input-group input-group-lg">
                            <span class="input-group-text"><i class="bi bi-person-badge"></i></span>
                            <input type="number" id="managerId" class="form-control" placeholder="Enter your Manager ID">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Fixtures to Analyze</label>
                        <select class="form-select form-select-lg" id="fixtureDepth">
                            <option value="5">Next 5 GWs</option>
                            <option value="6" selected>Next 6 GWs</option>
                            <option value="8">Next 8 GWs</option>
                        </select>
                    </div>
                </div>
                <div class="mt-4">
                    <button class="btn btn-wildcard btn-lg w-100" id="analyzeBtn">
                        <i class="bi bi-cpu me-2"></i>
                        Build Optimal Wildcard Team
                    </button>
                </div>
            </div>
        </div>

        <!-- Loading State -->
        <div id="loading" class="d-none">
            <div class="loading-container text-center">
                <div class="spinner-border text-primary" role="status" style="width: 4rem; height: 4rem;"></div>
                <h4 class="mt-4 fw-bold" id="loadingText">Analyzing your team...</h4>
                <div class="progress mt-3 mx-auto" style="width: 60%; height: 8px;">
                    <div class="progress-bar progress-bar-striped progress-bar-animated" id="progressBar" style="width: 0%"></div>
                </div>
            </div>
        </div>

        <!-- Results -->
        <div id="results" class="d-none">
            <!-- Stats Summary -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="stat-card">
                        <div class="stat-value" id="statTransfers">0</div>
                        <div class="stat-label">Suggested Transfers</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card">
                        <div class="stat-value" id="statCost">£0.0m</div>
                        <div class="stat-label">New Team Cost</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card">
                        <div class="stat-value" id="statValue">£0.0m</div>
                        <div class="stat-label">Team Value</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card">
                        <div class="stat-value" id="statxP">+0</div>
                        <div class="stat-label">Projected Gain</div>
                    </div>
                </div>
            </div>

            <!-- Transfer Suggestions -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card shadow-sm">
                        <div class="card-header bg-white">
                            <h5 class="fw-bold mb-0">
                                <i class="bi bi-arrow-left-right me-2"></i>
                                Recommended Transfers
                            </h5>
                        </div>
                        <div class="card-body" id="transfersList">
                            <!-- Injected by JS -->
                        </div>
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
                                Your New Wildcard Team
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

                    <!-- Key Insights -->
                    <div class="card shadow-sm">
                        <div class="card-header bg-white fw-bold">
                            <i class="bi bi-lightbulb-fill text-warning me-2"></i>Key Insights
                        </div>
                        <ul class="list-group list-group-flush" id="insightsList">
                            <li class="list-group-item">Loading insights...</li>
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
    const fixtureDepthSelect = document.getElementById('fixtureDepth');
    const analyzeBtn = document.getElementById('analyzeBtn');

    // State
    let bootstrapData = null;
    let fixtures = null;
    let teams = {};
    let currentTeam = null;

    // Initialize - Load Data
    (async function init() {
        try {
            const res = await fetch('api.php?endpoint=bootstrap-static/');
            bootstrapData = await res.json();
            
            // Store teams
            bootstrapData.teams.forEach(t => teams[t.id] = t);

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
        const fixtureDepth = parseInt(fixtureDepthSelect.value);

        if (!managerId) return alert('Please enter Manager ID');

        // Hide step1, show loading
        step1.classList.add('d-none');
        loading.classList.remove('d-none');
        results.classList.add('d-none');

        const updateProgress = (pct, txt) => {
            document.getElementById('progressBar').style.width = pct + '%';
            document.getElementById('loadingText').innerText = txt;
        };

        try {
            // Step 1: Get current team
            updateProgress(15, 'Loading your current team...');
            const currentGW = bootstrapData.events.find(e => e.is_current)?.id || 1;
            
            const picksRes = await fetch(`api.php?endpoint=entry/${managerId}/event/${currentGW}/picks/`);
            const picksData = await picksRes.json();
            
            const entryRes = await fetch(`api.php?endpoint=entry/${managerId}/`);
            const entryData = await entryRes.json();

            currentTeam = {
                picks: picksData.picks,
                bank: entryData.last_deadline_bank,
                value: entryData.last_deadline_value
            };

            // Step 2: Calculate fixture difficulty scores
            updateProgress(30, 'Analyzing fixture runs...');
            const fixtureScores = calculateFixtureScores(fixtureDepth, currentGW);

            // Step 3: Score all available players
            updateProgress(50, 'Evaluating all FPL players...');
            const players = {};
            bootstrapData.elements.forEach(p => {
                if (p.status !== 'a') return;

                const teamFixScore = fixtureScores[p.team] || 0;
                const formScore = parseFloat(p.form) * 2 || 0;
                const ppgScore = parseFloat(p.points_per_game) * 1.5 || 0;
                const valueScore = (ppgScore / (p.now_cost / 10)) * 10; // Points per million
                const ownershipScore = parseFloat(p.selected_by_percent) / 10; // Template bonus
                
                p.wcScore = teamFixScore + formScore + ppgScore + valueScore + ownershipScore;
                p.fixtureScore = teamFixScore;
                p.isTemplate = parseFloat(p.selected_by_percent) > 30;
                p.isDifferential = parseFloat(p.selected_by_percent) < 10;
                p.isRising = p.cost_change_event > 0;
                
                players[p.id] = p;
            });

            // Step 4: Build optimal wildcard team
            updateProgress(70, 'Building optimal wildcard team...');
            const newTeam = buildOptimalTeam(players, currentTeam);

            // Step 5: Generate transfer suggestions
            updateProgress(85, 'Generating transfer suggestions...');
            const transfers = generateTransfers(currentTeam, newTeam, players);

            // Step 6: Render results
            updateProgress(100, 'Complete!');
            renderResults(newTeam, transfers, fixtureDepth);

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

    function calculateFixtureScores(depth, currentGW) {
        const scores = {};
        
        // Initialize all teams
        Object.keys(teams).forEach(tid => scores[tid] = 0);

        // Get upcoming fixtures
        const upcomingFixtures = fixtures.filter(f => 
            f.event >= currentGW && f.event < currentGW + depth
        );

        upcomingFixtures.forEach(f => {
            // Home team - lower difficulty = better score
            scores[f.team_h] += (6 - f.team_h_difficulty);
            // Away team
            scores[f.team_a] += (6 - f.team_a_difficulty);
        });

        return scores;
    }

    function buildOptimalTeam(players, currentTeam) {
        const team = { 1: [], 2: [], 3: [], 4: [] };
        const maxLimits = { 1: 2, 2: 5, 3: 5, 4: 3 };
        const teamCounts = {};
        let totalCost = 0;
        const BUDGET = 1000; // £100.0m

        // Get players by position, sorted by wildcard score
        const byPosition = { 1: [], 2: [], 3: [], 4: [] };
        Object.values(players).forEach(p => {
            if (byPosition[p.element_type]) {
                byPosition[p.element_type].push(p);
            }
        });

        // Sort each position by score
        Object.keys(byPosition).forEach(pos => {
            byPosition[pos].sort((a, b) => b.wcScore - a.wcScore);
        });

        // Build best possible team
        const formations = [
            { 1: 1, 2: 4, 3: 4, 4: 2 },
            { 1: 1, 2: 3, 3: 5, 4: 2 },
            { 1: 1, 2: 3, 3: 4, 4: 3 },
            { 1: 1, 2: 5, 3: 3, 4: 2 }
        ];

        let bestTeam = null;
        let bestScore = -1;

        formations.forEach(formation => {
            const testTeam = { 1: [], 2: [], 3: [], 4: [] };
            const testCounts = {};
            let testCost = 0;
            let teamScore = 0;

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
                    teamScore += p.wcScore;
                    added++;
                }

                if (added < needed) {
                    teamScore = -1;
                    break;
                }
            }

            if (teamScore > bestScore) {
                bestScore = teamScore;
                bestTeam = { team: testTeam, counts: testCounts, cost: testCost };
            }
        });

        if (!bestTeam) throw new Error('Could not build valid team');

        // Fill bench
        const bench = { 1: [], 2: [], 3: [], 4: [] };
        
        // 1 GK
        for (const p of byPosition[1]) {
            if (bestTeam.team[1].some(tp => tp.id === p.id)) continue;
            if ((bestTeam.counts[p.team] || 0) >= 3) continue;
            if (bestTeam.cost + p.now_cost > BUDGET) continue;
            bench[1].push(p);
            bestTeam.counts[p.team] = (bestTeam.counts[p.team] || 0) + 1;
            bestTeam.cost += p.now_cost;
            break;
        }

        // 3 cheapest outfield
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

    function generateTransfers(oldTeam, newTeam, players) {
        const transfers = [];
        const currentPlayerIds = new Set(oldTeam.picks.map(p => p.element));
        const newPlayerIds = new Set();
        
        Object.values(newTeam.starting).flat().forEach(p => newPlayerIds.add(p.id));
        Object.values(newTeam.bench).flat().forEach(p => newPlayerIds.add(p.id));

        // Find players to remove
        const toRemove = oldTeam.picks.filter(p => !newPlayerIds.has(p.element));
        const toAdd = [...newPlayerIds].filter(id => !currentPlayerIds.has(id));

        // Create transfer pairs
        toRemove.forEach((oldPick, idx) => {
            if (idx < toAdd.length) {
                const oldPlayer = players[oldPick.element];
                const newPlayer = players[toAdd[idx]];
                
                if (oldPlayer && newPlayer) {
                    transfers.push({
                        out: oldPlayer,
                        in: newPlayer,
                        reason: getReason(oldPlayer, newPlayer)
                    });
                }
            }
        });

        return transfers;
    }

    function getReason(oldP, newP) {
        if (newP.fixtureScore > oldP.fixtureScore + 5) return 'Better fixtures';
        if (parseFloat(newP.form) > parseFloat(oldP.form) + 1) return 'Superior form';
        if (newP.isTemplate && !oldP.isTemplate) return 'Template coverage';
        if (newP.isDifferential && parseFloat(newP.form) > 4) return 'High-upside differential';
        return 'Value upgrade';
    }

    function renderResults(team, transfers, fixtureDepth) {
        // Update stats
        document.getElementById('statTransfers').innerText = transfers.length;
        document.getElementById('statCost').innerText = '£' + (team.totalCost / 10).toFixed(1) + 'm';
        document.getElementById('statValue').innerText = '£' + ((team.totalCost) / 10).toFixed(1) + 'm';
        document.getElementById('budgetRemaining').innerText = '£' + ((1000 - team.totalCost) / 10).toFixed(1) + 'm';

        // Projected gain (simplified)
        const projectedGain = transfers.length * 2; // Rough estimate
        document.getElementById('statxP').innerText = '+' + projectedGain;

        // Render transfers
        renderTransfers(transfers);

        // Render pitch
        renderPitch(team.starting, fixtureDepth);

        // Render bench
        renderBench(team.bench);

        // Render insights
        renderInsights(team, transfers);
    }

    function renderTransfers(transfers) {
        const container = document.getElementById('transfersList');
        container.innerHTML = '';

        if (transfers.length === 0) {
            container.innerHTML = '<p class="text-muted text-center">Your team is already optimal!</p>';
            return;
        }

        transfers.forEach(transfer => {
            const div = document.createElement('div');
            div.className = 'transfer-card';
            div.innerHTML = `
                <div class="row align-items-center">
                    <div class="col-md-5">
                        <div class="player-out">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <small class="text-muted">${teams[transfer.out.team].short_name}</small>
                                    <div class="fw-bold">${transfer.out.web_name}</div>
                                </div>
                                <div class="text-end">
                                    <div class="small">£${(transfer.out.now_cost / 10).toFixed(1)}m</div>
                                    <div class="small text-muted">Form: ${transfer.out.form}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2 text-center my-2">
                        <i class="bi bi-arrow-right-circle-fill fs-3 text-primary"></i>
                        <div class="small fw-bold text-muted mt-1">${transfer.reason}</div>
                    </div>
                    <div class="col-md-5">
                        <div class="player-in">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <small class="text-muted">${teams[transfer.in.team].short_name}</small>
                                    <div class="fw-bold">${transfer.in.web_name}</div>
                                </div>
                                <div class="text-end">
                                    <div class="small">£${(transfer.in.now_cost / 10).toFixed(1)}m</div>
                                    <div class="small text-success">Form: ${transfer.in.form}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            container.appendChild(div);
        });
    }

    function renderPitch(starting, fixtureDepth) {
        const rows = ['gkpRow', 'defRow', 'midRow', 'fwdRow'];
        const currentGW = bootstrapData.events.find(e => e.is_current)?.id || 1;
        
        rows.forEach((rowId, idx) => {
            const pos = idx + 1;
            const container = document.getElementById(rowId);
            container.innerHTML = '';

            starting[pos].forEach(p => {
                const col = document.createElement('div');
                col.className = 'col-auto';

                const teamInfo = teams[p.team];
                const upcomingFixtures = getUpcomingFixtures(p.team, currentGW, fixtureDepth);
                
                let badges = '';
                if (p.isTemplate) badges += '<div class="template-badge">TMP</div>';
                if (p.isDifferential) badges += '<div class="differential-badge">DIFF</div>';
                if (p.isRising) badges += '<div class="rising-badge">↑</div>';

                col.innerHTML = `
                    <div class="player-card" style="width: 110px;">
                        ${badges}
                        <div class="player-team">${teamInfo.short_name}</div>
                        <div class="player-name" title="${p.web_name}">${p.web_name}</div>
                        <div class="player-meta">
                            <span class="player-price">£${(p.now_cost / 10).toFixed(1)}</span>
                            <span class="player-ownership">${p.selected_by_percent}%</span>
                        </div>
                        <div class="fixture-run">
                            ${renderFixtureRun(upcomingFixtures, p.team)}
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

    function renderInsights(team, transfers) {
        const list = document.getElementById('insightsList');
        list.innerHTML = '';

        const allPlayers = Object.values(team.starting).flat();
        
        // Template count
        const templates = allPlayers.filter(p => p.isTemplate);
        list.innerHTML += `<li class="list-group-item"><strong>${templates.length}</strong> template players (30%+ ownership)</li>`;
        
        // Differentials
        const diffs = allPlayers.filter(p => p.isDifferential);
        list.innerHTML += `<li class="list-group-item"><strong>${diffs.length}</strong> differential picks (&lt;10% ownership)</li>`;

        // Rising players
        const rising = allPlayers.filter(p => p.isRising);
        if (rising.length > 0) {
            list.innerHTML += `<li class="list-group-item text-success"><i class="bi bi-graph-up-arrow"></i> ${rising.length} player(s) rising in price</li>`;
        }

        // Best fixture team
        const teamFixtures = {};
        allPlayers.forEach(p => {
            teamFixtures[p.team] = (teamFixtures[p.team] || 0) + 1;
        });
        const topTeam = Object.entries(teamFixtures).sort((a, b) => b[1] - a[1])[0];
        if (topTeam) {
            list.innerHTML += `<li class="list-group-item">Stacked on <strong>${teams[topTeam[0]].name}</strong> (${topTeam[1]} players)</li>`;
        }
    }

    function getUpcomingFixtures(teamId, currentGW, depth) {
        return fixtures
            .filter(f => (f.team_h === teamId || f.team_a === teamId) && f.event >= currentGW && f.event < currentGW + depth)
            .sort((a, b) => a.event - b.event);
    }

    function renderFixtureRun(fixtures, teamId) {
        return fixtures.slice(0, 6).map(f => {
            const isHome = f.team_h === teamId;
            const difficulty = isHome ? f.team_h_difficulty : f.team_a_difficulty;
            return `<div class="fixture-box diff-${difficulty}"></div>`;
        }).join('');
    }
</script>
</body>
</html>
