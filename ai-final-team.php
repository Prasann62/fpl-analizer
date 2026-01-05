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
    <title>AI Final Team | FPL Manager</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="style.css?v=<?php echo time(); ?>" rel="stylesheet">
    <style>
        /* Premium Pitch Background */
        .pitch-container {
            background: linear-gradient(180deg, #0d4f2a 0%, #1a6b3d 20%, #1a6b3d 80%, #0d4f2a 100%);
            border-radius: 20px;
            padding: 2rem 1rem;
            position: relative;
            overflow: hidden;
            box-shadow: inset 0 0 60px rgba(0,0,0,0.3), 0 20px 60px rgba(0,0,0,0.3);
        }
        
        /* Pitch Lines */
        .pitch-lines {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            pointer-events: none;
        }
        .center-line {
            position: absolute;
            top: calc(50% - 40px);
            left: 10%;
            right: 10%;
            height: 2px;
            background: rgba(255,255,255,0.2);
        }
        .center-circle {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 100px;
            height: 100px;
            border: 2px solid rgba(255,255,255,0.2);
            border-radius: 50%;
        }
        .penalty-box-top {
            position: absolute;
            top: 0;
            left: 25%;
            width: 50%;
            height: 60px;
            border: 2px solid rgba(255,255,255,0.15);
            border-top: none;
            border-radius: 0 0 10px 10px;
        }
        .penalty-box-bottom {
            position: absolute;
            bottom: 0;
            left: 25%;
            width: 50%;
            height: 60px;
            border: 2px solid rgba(255,255,255,0.15);
            border-bottom: none;
            border-radius: 10px 10px 0 0;
        }

        /* Enhanced Player Cards */
        .player-card {
            background: linear-gradient(145deg, #ffffff 0%, #f8f9fa 100%);
            border-radius: 12px;
            padding: 0.6rem 0.4rem;
            text-align: center;
            box-shadow: 0 8px 20px rgba(0,0,0,0.2);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            cursor: pointer;
            position: relative;
            overflow: hidden;
            min-width: 80px;
            border: 2px solid transparent;
        }
        .player-card:hover {
            transform: translateY(-8px) scale(1.05);
            box-shadow: 0 15px 35px rgba(0,0,0,0.3);
            z-index: 100;
        }
        .player-card.captain {
            border-color: #ffc107;
            box-shadow: 0 0 20px rgba(255, 193, 7, 0.5);
        }
        .player-card.vice-captain {
            border-color: #6c757d;
        }
        .player-card.bench {
            opacity: 0.85;
            background: linear-gradient(145deg, #f0f0f0 0%, #e0e0e0 100%);
        }
        
        .player-shirt {
            width: 40px;
            height: 40px;
            margin: 0 auto 4px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 0.7rem;
            color: white;
            text-shadow: 0 1px 2px rgba(0,0,0,0.3);
        }
        .player-name {
            font-weight: 700;
            font-size: 0.7rem;
            color: #1a1a1a;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            margin-bottom: 2px;
        }
        .player-points {
            font-size: 0.6rem;
            color: #666;
        }
        .player-xpts {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            font-size: 0.55rem;
            font-weight: 700;
            padding: 2px 6px;
            border-radius: 10px;
            display: inline-block;
            margin-top: 2px;
        }
        
        /* Badge Types */
        .captain-badge {
            position: absolute;
            top: -5px;
            right: -5px;
            width: 22px;
            height: 22px;
            background: linear-gradient(135deg, #ffc107, #ff8f00);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.6rem;
            font-weight: 800;
            color: #000;
            box-shadow: 0 2px 8px rgba(255, 193, 7, 0.5);
        }
        .vc-badge {
            position: absolute;
            top: -5px;
            right: -5px;
            width: 22px;
            height: 22px;
            background: #6c757d;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.6rem;
            font-weight: 800;
            color: #fff;
        }
        .diff-badge {
            position: absolute;
            top: -5px;
            left: -5px;
            background: linear-gradient(135deg, #ff4444, #ff6b6b);
            color: white;
            font-size: 0.5rem;
            font-weight: 700;
            padding: 2px 5px;
            border-radius: 8px;
        }
        
        /* Bench Section */
        .bench-section {
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
            border-radius: 16px;
            padding: 1.5rem;
            margin-top: 1rem;
        }
        .bench-title {
            color: rgba(255,255,255,0.8);
            font-size: 0.85rem;
            font-weight: 700;
            margin-bottom: 1rem;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .bench-order {
            position: absolute;
            top: -8px;
            left: 50%;
            transform: translateX(-50%);
            background: #ffc107;
            color: #000;
            font-size: 0.6rem;
            font-weight: 800;
            width: 18px;
            height: 18px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        /* Stats Cards */
        .team-stat {
            background: linear-gradient(135deg, rgba(255,255,255,0.1) 0%, rgba(255,255,255,0.05) 100%);
            backdrop-filter: blur(10px);
            border-radius: 16px;
            padding: 1.5rem;
            text-align: center;
            border: 1px solid rgba(255,255,255,0.1);
        }
        .team-stat h2 {
            font-size: 2.5rem;
            font-weight: 800;
            background: linear-gradient(135deg, #00ff85, #00c9ff);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        /* Formation Selector */
        .formation-btn {
            padding: 0.5rem 1rem;
            border-radius: 50px;
            border: 2px solid rgba(255,255,255,0.2);
            background: transparent;
            color: white;
            font-weight: 600;
            font-size: 0.85rem;
            transition: all 0.3s ease;
            cursor: pointer;
        }
        .formation-btn:hover {
            border-color: var(--primary-color);
            background: rgba(0, 255, 133, 0.1);
        }
        .formation-btn.active {
            background: linear-gradient(135deg, var(--primary-color), #00ff85);
            border-color: transparent;
            color: #000;
        }

        /* Hero Section */
        .hero-section {
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
            border-radius: 24px;
            padding: 2.5rem;
            margin-bottom: 2rem;
            position: relative;
            overflow: hidden;
        }
        .hero-section::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -20%;
            width: 60%;
            height: 200%;
            background: radial-gradient(ellipse, rgba(0,255,133,0.1) 0%, transparent 70%);
            pointer-events: none;
        }
        
        /* Budget Bar */
        .budget-bar {
            height: 10px;
            background: rgba(255,255,255,0.1);
            border-radius: 5px;
            overflow: hidden;
        }
        .budget-fill {
            height: 100%;
            background: linear-gradient(90deg, #00ff85, #00c9ff);
            border-radius: 5px;
            transition: width 0.5s ease;
        }
        .budget-fill.over {
            background: linear-gradient(90deg, #ff4444, #ff6b6b);
        }

        /* Team Row Animations */
        .player-row {
            display: flex;
            justify-content: center;
            gap: 0.75rem;
            margin-bottom: 1rem;
            flex-wrap: wrap;
        }
        .player-row .player-wrapper {
            animation: fadeInUp 0.5s ease forwards;
            opacity: 0;
        }
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Loading */
        .ai-brain {
            width: 100px;
            height: 100px;
            background: linear-gradient(135deg, var(--primary-color), #00ff85);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            animation: pulse 2s ease-in-out infinite;
            margin: 0 auto 1.5rem;
        }
        .ai-brain i {
            font-size: 3rem;
            color: #000;
        }
        @keyframes pulse {
            0%, 100% { transform: scale(1); box-shadow: 0 0 0 0 rgba(0, 255, 133, 0.4); }
            50% { transform: scale(1.05); box-shadow: 0 0 0 20px rgba(0, 255, 133, 0); }
        }
    </style>
</head>
<body>
<?php include 'navbar.php';?>

<div class="main-content">
    <div class="container py-5">
        
        <!-- Hero Section -->
        <div class="hero-section text-white">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <span class="badge bg-warning text-dark mb-3"><i class="bi bi-cpu me-1"></i>AI POWERED</span>
                    <h1 class="display-4 fw-bold mb-3">Final Team Builder</h1>
                    <p class="lead opacity-75 mb-0">Build the perfect £100m FPL squad using AI analysis of form, fixtures, and expected points.</p>
                </div>
                <div class="col-lg-4 text-center d-none d-lg-block">
                    <i class="bi bi-trophy-fill display-1 opacity-25"></i>
                </div>
            </div>
        </div>

        <!-- Strategy Selection -->
        <div class="card shadow-sm border-0 rounded-4 mb-4" id="strategyCard">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-4"><i class="bi bi-sliders me-2"></i>Team Strategy</h5>
                
                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <input type="radio" class="btn-check" name="strategy" id="strategyBalanced" value="balanced" checked>
                        <label class="btn btn-outline-primary w-100 h-100 p-3" for="strategyBalanced">
                            <i class="bi bi-pie-chart-fill fs-3 d-block mb-2"></i>
                            <div class="fw-bold">Balanced</div>
                            <div class="small opacity-75">Safe template picks</div>
                        </label>
                    </div>
                    <div class="col-md-4">
                        <input type="radio" class="btn-check" name="strategy" id="strategyDifferential" value="differential">
                        <label class="btn btn-outline-warning w-100 h-100 p-3" for="strategyDifferential">
                            <i class="bi bi-lightning-charge-fill fs-3 d-block mb-2"></i>
                            <div class="fw-bold">Differential</div>
                            <div class="small opacity-75">High-risk high-reward</div>
                        </label>
                    </div>
                    <div class="col-md-4">
                        <input type="radio" class="btn-check" name="strategy" id="strategyValue" value="value">
                        <label class="btn btn-outline-success w-100 h-100 p-3" for="strategyValue">
                            <i class="bi bi-currency-pound fs-3 d-block mb-2"></i>
                            <div class="fw-bold">Value</div>
                            <div class="small opacity-75">Budget-friendly picks</div>
                        </label>
                    </div>
                </div>
                
                <div class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Budget (£m)</label>
                        <input type="number" id="budgetInput" class="form-control form-control-lg" value="100" min="80" max="100" step="0.5">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Formation</label>
                        <select id="formationSelect" class="form-select form-select-lg">
                            <option value="3-4-3">3-4-3</option>
                            <option value="3-5-2">3-5-2</option>
                            <option value="4-3-3">4-3-3</option>
                            <option value="4-4-2" selected>4-4-2</option>
                            <option value="4-5-1">4-5-1</option>
                            <option value="5-3-2">5-3-2</option>
                            <option value="5-4-1">5-4-1</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <button class="btn btn-lg w-100 fw-bold" id="buildBtn" style="background: linear-gradient(135deg, var(--primary-color), #00ff85); color: #000;">
                            <i class="bi bi-magic me-2"></i>Build Final Team
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Loading -->
        <div id="loading" class="text-center py-5 d-none">
            <div class="ai-brain">
                <i class="bi bi-cpu"></i>
            </div>
            <h4 class="fw-bold mb-2" id="loadingText">Analyzing player data...</h4>
            <p class="text-muted mb-4">Our AI is building your optimal squad</p>
            <div class="progress mx-auto" style="width: 300px; height: 8px; border-radius: 4px; background: rgba(0,0,0,0.1);">
                <div class="progress-bar" id="progressBar" style="width: 0%; background: linear-gradient(90deg, var(--primary-color), #00ff85);"></div>
            </div>
        </div>

        <!-- Results -->
        <div id="results" class="d-none">
            
            <!-- Team Stats -->
            <div class="row g-3 mb-4">
                <div class="col-6 col-md-3">
                    <div class="team-stat">
                        <div class="small text-white-50 text-uppercase fw-bold mb-1">Total xPts</div>
                        <h2 id="totalXpts">0</h2>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="team-stat">
                        <div class="small text-white-50 text-uppercase fw-bold mb-1">Team Value</div>
                        <h2 id="teamValue">£0m</h2>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="team-stat">
                        <div class="small text-white-50 text-uppercase fw-bold mb-1">Avg Form</div>
                        <h2 id="avgForm">0.0</h2>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="team-stat">
                        <div class="small text-white-50 text-uppercase fw-bold mb-1">Bank</div>
                        <h2 id="bankRemaining">£0m</h2>
                    </div>
                </div>
            </div>
            
            <!-- Budget Bar -->
            <div class="mb-4">
                <div class="d-flex justify-content-between mb-2">
                    <span class="small fw-bold">Budget Used</span>
                    <span class="small fw-bold" id="budgetUsedText">£0m / £100m</span>
                </div>
                <div class="budget-bar">
                    <div class="budget-fill" id="budgetFill" style="width: 0%"></div>
                </div>
            </div>

            <!-- Pitch View -->
            <div class="pitch-container mb-4">
                <div class="pitch-lines">
                    <div class="center-line"></div>
                    <div class="center-circle"></div>
                    <div class="penalty-box-top"></div>
                    <div class="penalty-box-bottom"></div>
                </div>
                
                <div id="gkRow" class="player-row"></div>
                <div id="defRow" class="player-row"></div>
                <div id="midRow" class="player-row"></div>
                <div id="fwdRow" class="player-row"></div>
            </div>

            <!-- Bench -->
            <div class="bench-section">
                <div class="bench-title"><i class="bi bi-people-fill me-2"></i>Bench</div>
                <div id="benchRow" class="player-row justify-content-start"></div>
            </div>

            <!-- Actions -->
            <div class="text-center mt-4">
                <button class="btn btn-outline-light me-2" onclick="location.reload()">
                    <i class="bi bi-arrow-repeat me-2"></i>Rebuild Team
                </button>
                <button class="btn btn-warning" id="shareBtn">
                    <i class="bi bi-share me-2"></i>Share Team
                </button>
            </div>

        </div>
    </div>
</div>

<?php include 'footer.php';?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
    let bootstrapData = null;
    let futureFixtures = [];
    
    // Team shirt colors by team ID
    const teamColors = {
        1: '#EF0107', // Arsenal
        2: '#670E36', // Aston Villa
        3: '#DA291C', // Bournemouth
        4: '#D20000', // Brentford
        5: '#0057B8', // Brighton
        6: '#6C1D45', // Burnley (if relevant)
        7: '#034694', // Chelsea
        8: '#1B458F', // Crystal Palace
        9: '#003399', // Everton
        10: '#000000', // Fulham
        11: '#6CABDD', // Ipswich
        12: '#003090', // Leicester
        13: '#C8102E', // Liverpool
        14: '#6CABDD', // Man City
        15: '#DA291C', // Man Utd
        16: '#241F20', // Newcastle
        17: '#E53233', // Nottingham Forest
        18: '#D71920', // Southampton
        19: '#132257', // Spurs
        20: '#7A263A' // West Ham
    };
    
    // Build button click
    document.getElementById('buildBtn').addEventListener('click', async () => {
        const strategy = document.querySelector('input[name="strategy"]:checked').value;
        const budget = parseFloat(document.getElementById('budgetInput').value) * 10; // Convert to 0.1m units
        const formation = document.getElementById('formationSelect').value;
        
        // Show loading
        document.getElementById('strategyCard').classList.add('d-none');
        document.getElementById('loading').classList.remove('d-none');
        document.getElementById('results').classList.add('d-none');
        
        const updateProgress = (pct, txt) => {
            document.getElementById('progressBar').style.width = pct + '%';
            document.getElementById('loadingText').innerText = txt;
        };
        
        try {
            // 1. Fetch Bootstrap Data
            updateProgress(10, 'Loading FPL database...');
            if(!bootstrapData) {
                const res = await fetch('api.php?endpoint=bootstrap-static/');
                bootstrapData = await res.json();
            }
            
            const players = bootstrapData.elements.filter(p => p.status === 'a');
            const teams = {};
            bootstrapData.teams.forEach(t => teams[t.id] = t);
            const currentGw = bootstrapData.events.find(e => e.is_current)?.id || 1;
            
            // 2. Fetch Fixtures for difficulty
            updateProgress(25, 'Analyzing fixtures...');
            try {
                const fixRes = await fetch('api.php?endpoint=fixtures/?future=1');
                futureFixtures = await fixRes.json();
            } catch(e) { console.warn('Fixtures fetch failed'); }
            
            // 3. Calculate player scores
            updateProgress(40, 'Calculating player ratings...');
            
            const getFixtureScore = (teamId) => {
                let score = 0;
                const startGw = currentGw + 1;
                for(let i = 0; i < 5; i++) {
                    const gw = startGw + i;
                    if(gw > 38) break;
                    const fixtures = futureFixtures.filter(f => f.event === gw && (f.team_h === teamId || f.team_a === teamId));
                    if(fixtures.length === 0) {
                        score -= 2;
                    } else {
                        fixtures.forEach(f => {
                            const difficulty = f.team_h === teamId ? f.team_h_difficulty : f.team_a_difficulty;
                            score += (6 - difficulty);
                        });
                    }
                }
                return score;
            };
            
            // Score each player
            players.forEach(p => {
                const form = parseFloat(p.form) || 0;
                const ppg = parseFloat(p.points_per_game) || 0;
                const fixScore = getFixtureScore(p.team);
                const globalOwn = parseFloat(p.selected_by_percent) || 0;
                
                // xPts calculation
                const baseXpts = (form * 0.6 + ppg * 0.4);
                const fixFactor = Math.max(0.7, Math.min(1.3, fixScore / 15));
                p.xpts = (baseXpts * 5 * fixFactor);
                p.fixScore = fixScore;
                
                // Value score (xPts per million)
                p.value = p.xpts / (p.now_cost / 10);
                
                // AI Score based on strategy
                if(strategy === 'balanced') {
                    p.aiScore = p.xpts * (1 + Math.min(globalOwn/100, 0.3));
                } else if(strategy === 'differential') {
                    p.aiScore = p.xpts * (1 + (100 - globalOwn) / 200);
                } else { // value
                    p.aiScore = p.value * 10;
                }
                
                p.isDifferential = globalOwn < 10;
            });
            
            // 4. Build optimal team
            updateProgress(60, 'Building optimal squad...');
            
            const [defCount, midCount, fwdCount] = formation.split('-').map(Number);
            const startingXI = { 1: 1, 2: defCount, 3: midCount, 4: fwdCount };
            const squadLimits = { 1: 2, 2: 5, 3: 5, 4: 3 };
            
            let squad = { 1: [], 2: [], 3: [], 4: [] };
            let teamCount = {};
            let usedBudget = 0;
            
            // Sort players by AI score
            const sortedPlayers = [...players].sort((a, b) => b.aiScore - a.aiScore);
            
            // Pick starting XI first (best players)
            for(const pos of [4, 3, 2, 1]) { // FWD, MID, DEF, GK order
                const needed = startingXI[pos];
                const posPlayers = sortedPlayers.filter(p => p.element_type === pos);
                
                for(const p of posPlayers) {
                    if(squad[pos].length >= needed) break;
                    if((teamCount[p.team] || 0) >= 3) continue;
                    if(usedBudget + p.now_cost > budget) continue;
                    
                    squad[pos].push({ ...p, isStarter: true });
                    teamCount[p.team] = (teamCount[p.team] || 0) + 1;
                    usedBudget += p.now_cost;
                }
            }
            
            // Fill bench (remaining slots)
            for(const pos of [1, 2, 3, 4]) {
                const maxForPos = squadLimits[pos];
                const posPlayers = sortedPlayers.filter(p => p.element_type === pos);
                
                for(const p of posPlayers) {
                    if(squad[pos].length >= maxForPos) break;
                    if(squad[pos].some(sp => sp.id === p.id)) continue;
                    if((teamCount[p.team] || 0) >= 3) continue;
                    if(usedBudget + p.now_cost > budget) continue;
                    
                    squad[pos].push({ ...p, isStarter: false });
                    teamCount[p.team] = (teamCount[p.team] || 0) + 1;
                    usedBudget += p.now_cost;
                }
            }
            
            // 5. Assign Captain and Vice Captain
            updateProgress(80, 'Selecting captain...');
            
            const allSquad = Object.values(squad).flat();
            const starters = allSquad.filter(p => p.isStarter).sort((a, b) => b.xpts - a.xpts);
            if(starters.length > 0) starters[0].isCaptain = true;
            if(starters.length > 1) starters[1].isViceCaptain = true;
            
            // Order bench by xPts
            const bench = allSquad.filter(p => !p.isStarter).sort((a, b) => b.xpts - a.xpts);
            bench.forEach((p, i) => p.benchOrder = i + 1);
            
            // 6. Calculate totals
            updateProgress(90, 'Finalizing team...');
            
            const totalXpts = starters.reduce((sum, p) => sum + p.xpts, 0);
            const capBonus = starters.find(p => p.isCaptain)?.xpts || 0;
            const finalXpts = totalXpts + capBonus;
            
            const avgForm = (allSquad.reduce((sum, p) => sum + parseFloat(p.form), 0) / allSquad.length).toFixed(1);
            const bank = (budget - usedBudget) / 10;
            
            // 7. Render
            updateProgress(100, 'Done!');
            
            renderTeam(squad, startingXI, teams);
            renderBench(bench, teams);
            
            document.getElementById('totalXpts').innerText = finalXpts.toFixed(1);
            document.getElementById('teamValue').innerText = '£' + (usedBudget / 10).toFixed(1) + 'm';
            document.getElementById('avgForm').innerText = avgForm;
            document.getElementById('bankRemaining').innerText = '£' + bank.toFixed(1) + 'm';
            
            const budgetPct = (usedBudget / budget) * 100;
            document.getElementById('budgetFill').style.width = budgetPct + '%';
            document.getElementById('budgetFill').classList.toggle('over', budgetPct > 100);
            document.getElementById('budgetUsedText').innerText = `£${(usedBudget/10).toFixed(1)}m / £${(budget/10).toFixed(1)}m`;
            
            document.getElementById('loading').classList.add('d-none');
            document.getElementById('results').classList.remove('d-none');
            
        } catch(e) {
            console.error(e);
            alert('Error: ' + e.message);
            document.getElementById('loading').classList.add('d-none');
            document.getElementById('strategyCard').classList.remove('d-none');
        }
    });
    
    function renderTeam(squad, startingXI, teams) {
        const rows = {
            1: document.getElementById('gkRow'),
            2: document.getElementById('defRow'),
            3: document.getElementById('midRow'),
            4: document.getElementById('fwdRow')
        };
        
        for(const [pos, container] of Object.entries(rows)) {
            container.innerHTML = '';
            const starters = squad[pos].filter(p => p.isStarter);
            
            starters.forEach((p, idx) => {
                const wrapper = document.createElement('div');
                wrapper.className = 'player-wrapper';
                wrapper.style.animationDelay = `${idx * 0.1}s`;
                
                const color = teamColors[p.team] || '#333';
                let badges = '';
                if(p.isCaptain) badges = '<div class="captain-badge">C</div>';
                else if(p.isViceCaptain) badges = '<div class="vc-badge">V</div>';
                if(p.isDifferential) badges += '<div class="diff-badge">DIFF</div>';
                
                wrapper.innerHTML = `
                    <div class="player-card ${p.isCaptain ? 'captain' : ''} ${p.isViceCaptain ? 'vice-captain' : ''}">
                        ${badges}
                        <div class="player-shirt" style="background: ${color}">${teams[p.team]?.short_name || ''}</div>
                        <div class="player-name">${p.web_name}</div>
                        <div class="player-points">£${(p.now_cost/10).toFixed(1)}m</div>
                        <div class="player-xpts">${p.xpts.toFixed(1)} xPts</div>
                    </div>
                `;
                container.appendChild(wrapper);
            });
        }
    }
    
    function renderBench(bench, teams) {
        const container = document.getElementById('benchRow');
        container.innerHTML = '';
        
        bench.forEach((p, idx) => {
            const wrapper = document.createElement('div');
            wrapper.className = 'player-wrapper';
            wrapper.style.animationDelay = `${idx * 0.1}s`;
            
            const color = teamColors[p.team] || '#333';
            
            wrapper.innerHTML = `
                <div class="player-card bench" style="position: relative;">
                    <div class="bench-order">${p.benchOrder}</div>
                    <div class="player-shirt" style="background: ${color}; opacity: 0.7">${teams[p.team]?.short_name || ''}</div>
                    <div class="player-name">${p.web_name}</div>
                    <div class="player-points">£${(p.now_cost/10).toFixed(1)}m</div>
                    <div class="player-xpts">${p.xpts.toFixed(1)} xPts</div>
                </div>
            `;
            container.appendChild(wrapper);
        });
    }
    
    // Share functionality
    document.getElementById('shareBtn')?.addEventListener('click', () => {
        const text = `🏆 My AI-Generated FPL Team
📊 Total xPts: ${document.getElementById('totalXpts').innerText}
💰 Value: ${document.getElementById('teamValue').innerText}
🔥 Avg Form: ${document.getElementById('avgForm').innerText}

Built with FPL Manager AI Team Builder!`;
        
        if(navigator.share) {
            navigator.share({ title: 'My FPL AI Team', text: text });
        } else {
            navigator.clipboard.writeText(text);
            alert('Team copied to clipboard!');
        }
    });
</script>
</body>
</html>
