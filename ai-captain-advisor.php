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
    <title>AI Captain Advisor | FPL Manager</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="style.css?v=<?php echo time(); ?>" rel="stylesheet">
    <style>
        .hero-header {
            background: linear-gradient(135deg, rgba(245, 158, 11, 0.95) 0%, rgba(217, 119, 6, 0.95) 100%);
            border-radius: 1rem;
            padding: 2rem;
            color: #000;
        }
        .captain-card {
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
            border-radius: 20px;
            border: none;
            overflow: hidden;
            transition: all 0.3s ease;
        }
        .captain-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.4);
        }
        .captain-card.recommended {
            border: 3px solid #f59e0b;
            box-shadow: 0 0 30px rgba(245, 158, 11, 0.3);
        }
        .confidence-ring {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
        }
        .confidence-ring::before {
            content: '';
            position: absolute;
            inset: 0;
            border-radius: 50%;
            padding: 5px;
            background: conic-gradient(#10b981 var(--progress), #374151 var(--progress));
            -webkit-mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
            mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
            -webkit-mask-composite: xor;
            mask-composite: exclude;
        }
        .confidence-value {
            font-size: 1.5rem;
            font-weight: bold;
            color: white;
        }
        .stat-bar {
            height: 8px;
            border-radius: 4px;
            background: #374151;
            overflow: hidden;
        }
        .stat-bar-fill {
            height: 100%;
            border-radius: 4px;
            transition: width 0.5s ease;
        }
        .fixture-badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
        }
        .fixture-easy { background: #10b981; color: white; }
        .fixture-medium { background: #f59e0b; color: #000; }
        .fixture-hard { background: #ef4444; color: white; }
        .differential-badge {
            background: linear-gradient(135deg, #8b5cf6 0%, #6d28d9 100%);
            color: white;
            padding: 3px 10px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: bold;
        }
        .chip-suggestion {
            background: linear-gradient(135deg, rgba(139, 92, 246, 0.2) 0%, rgba(109, 40, 217, 0.2) 100%);
            border: 1px solid rgba(139, 92, 246, 0.5);
            border-radius: 12px;
            padding: 15px;
        }
        .vs-badge {
            background: rgba(255,255,255,0.1);
            border-radius: 8px;
            padding: 8px 12px;
            font-size: 12px;
        }
        .rank-up { color: #10b981; }
        .rank-down { color: #ef4444; }
        .section-card {
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 16px;
        }
        .loading-skeleton {
            background: linear-gradient(90deg, #2d3748 25%, #4a5568 50%, #2d3748 75%);
            background-size: 200% 100%;
            animation: shimmer 1.5s infinite;
            border-radius: 8px;
        }
        @keyframes shimmer {
            0% { background-position: 200% 0; }
            100% { background-position: -200% 0; }
        }
    </style>
</head>
<body>
<?php include 'navbar.php';?>

<div class="main-content">
    <div class="container py-4">
        <!-- Hero Header -->
        <div class="hero-header mb-4">
            <div class="row align-items-center">
                <div class="col-md-7">
                    <h1 class="display-5 fw-bold mb-2"><i class="bi bi-cpu me-2"></i>AI Captain Advisor</h1>
                    <p class="lead mb-0 opacity-75">Data-driven captain recommendations with confidence scores and fixture analysis.</p>
                </div>
                <div class="col-md-5 text-end">
                    <div class="d-inline-block text-start p-3 rounded" style="background: rgba(0,0,0,0.2);">
                        <small class="opacity-75 d-block">Analyzing For</small>
                        <div class="fs-3 fw-bold" id="currentGWDisplay">
                            <span class="spinner-border spinner-border-sm me-2"></span>Loading...
                        </div>
                        <small class="opacity-75" id="gwDeadline">Deadline: -</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Manager Input -->
        <div class="card mb-4 section-card">
            <div class="card-body">
                <div class="row align-items-end g-3">
                    <div class="col-md-8">
                        <label class="form-label text-muted fw-bold small text-uppercase">Your Manager ID</label>
                        <div class="input-group input-group-lg">
                            <span class="input-group-text bg-dark border-secondary text-white"><i class="bi bi-person-badge"></i></span>
                            <input type="number" id="managerId" class="form-control bg-dark border-secondary text-white" placeholder="Enter your FPL Team ID">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <button class="btn btn-warning btn-lg w-100 fw-bold" id="analyzeBtn" onclick="analyzeTeam()">
                            <i class="bi bi-magic me-2"></i>Get Captain Advice
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Results Container -->
        <div id="resultsContainer" class="d-none">
            
            <!-- Top Recommendation -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card captain-card recommended">
                        <div class="card-body p-4">
                            <div class="row align-items-center">
                                <div class="col-md-3 text-center">
                                    <div class="badge bg-warning text-dark mb-2">🏆 TOP PICK</div>
                                    <div class="confidence-ring" id="topConfidenceRing" style="--progress: 85%;">
                                        <span class="confidence-value" id="topConfidence">85%</span>
                                    </div>
                                    <small class="text-white-50">Confidence Score</small>
                                </div>
                                <div class="col-md-5">
                                    <h2 class="text-white fw-bold mb-1" id="topPlayerName">Loading...</h2>
                                    <p class="text-white-50 mb-3" id="topPlayerTeam">-</p>
                                    <div class="d-flex gap-2 mb-3">
                                        <span class="fixture-badge fixture-easy" id="topFixture">vs -</span>
                                        <span id="topDifferential" class="d-none differential-badge">Differential</span>
                                    </div>
                                    <p class="text-white-50 small" id="topReasoning">Analyzing data...</p>
                                </div>
                                <div class="col-md-4">
                                    <div class="bg-dark rounded p-3">
                                        <div class="d-flex justify-content-between mb-2">
                                            <span class="text-white-50">Expected Points</span>
                                            <span class="text-success fw-bold" id="topXPts">-</span>
                                        </div>
                                        <div class="d-flex justify-content-between mb-2">
                                            <span class="text-white-50">Form (Last 5)</span>
                                            <span class="text-white fw-bold" id="topForm">-</span>
                                        </div>
                                        <div class="d-flex justify-content-between mb-2">
                                            <span class="text-white-50">Ownership</span>
                                            <span class="text-white fw-bold" id="topOwnership">-</span>
                                        </div>
                                        <div class="d-flex justify-content-between">
                                            <span class="text-white-50">ICT Index</span>
                                            <span class="text-white fw-bold" id="topICT">-</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Other Candidates -->
            <h5 class="text-white mb-3"><i class="bi bi-list-ol me-2"></i>Other Captain Options</h5>
            <div class="row g-4 mb-4" id="candidatesGrid">
                <!-- Will be populated by JS -->
            </div>

            <!-- Differential Captain -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card section-card">
                        <div class="card-header bg-transparent border-0 pt-4 px-4">
                            <h5 class="text-white mb-0"><i class="bi bi-lightning-charge me-2 text-purple"></i>Differential Captain Pick</h5>
                        </div>
                        <div class="card-body px-4 pb-4">
                            <div class="chip-suggestion" id="differentialSection">
                                <div class="row align-items-center">
                                    <div class="col-md-2 text-center">
                                        <div class="differential-badge mb-2">LOW OWNED</div>
                                        <div class="fs-1">🎯</div>
                                    </div>
                                    <div class="col-md-6">
                                        <h4 class="text-white fw-bold mb-1" id="diffPlayerName">Loading...</h4>
                                        <p class="text-white-50 mb-2" id="diffPlayerTeam">-</p>
                                        <p class="small text-white-50" id="diffReasoning">Analyzing differentials...</p>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="d-flex justify-content-between mb-2">
                                            <span class="text-white-50">Ownership</span>
                                            <span class="text-success fw-bold" id="diffOwnership">-</span>
                                        </div>
                                        <div class="d-flex justify-content-between mb-2">
                                            <span class="text-white-50">xPts</span>
                                            <span class="text-white fw-bold" id="diffXPts">-</span>
                                        </div>
                                        <div class="d-flex justify-content-between">
                                            <span class="text-white-50">Upside Rank</span>
                                            <span class="rank-up fw-bold" id="diffUpside">-</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Captain Comparison vs Template -->
            <div class="card section-card mb-4">
                <div class="card-header bg-transparent border-0 pt-4 px-4">
                    <h5 class="text-white mb-0"><i class="bi bi-bar-chart me-2"></i>How Your Captain Compares</h5>
                </div>
                <div class="card-body px-4 pb-4">
                    <div class="row g-3" id="comparisonSection">
                        <!-- Will be populated -->
                    </div>
                </div>
            </div>

        </div>

        <!-- Initial Loading State -->
        <div id="loadingState" class="d-none text-center py-5">
            <div class="spinner-border text-warning" style="width: 3rem; height: 3rem;"></div>
            <p class="text-white-50 mt-3">Analyzing your squad and fixtures...</p>
        </div>

    </div>
</div>

<?php include 'footer.php';?>
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
    let staticData = null;
    let playersMap = {};
    let teamsMap = {};
    let fixturesData = [];

    // Team Logo Helper
    function getTeamLogo(teamName) {
        if(!teamName) return null;
        const name = teamName.toLowerCase();
        const map = {
            'arsenal': 'arsenal.svg', 'aston villa': 'aston villa.svg', 'bournemouth': 'boumemouth.svg',
            'brentford': 'brentford.svg', 'brighton': 'brighton.svg', 'burnley': 'burnley.svg',
            'chelsea': 'chelsea.svg', 'crystal palace': 'crystal palace.svg', 'everton': 'everton.svg',
            'fulham': 'fulham.svg', 'liverpool': 'liverpool.svg', 'man city': 'man city.svg',
            'man utd': 'man utd.svg', 'newcastle': 'newcastle.svg', "nott'm forest": 'forest.svg',
            'spurs': 'spurs.svg', 'tottenham': 'spurs.svg', 'west ham': 'west ham.svg', 'wolves': 'wolves.svg',
            'leicester': 'leicester.png', 'southampton': 'southampton.png', 'ipswich': 'ipswich.png'
        };
        return map[name] ? 'f_logo/' + map[name] : null;
    }

    // Load stored ID
    const storedId = localStorage.getItem('fpl_manager_id');
    if(storedId) document.getElementById('managerId').value = storedId;

    // Initialize
    async function init() {
        try {
            const res = await fetch('api.php?endpoint=bootstrap-static/');
            staticData = await res.json();
            
            staticData.elements.forEach(p => playersMap[p.id] = p);
            staticData.teams.forEach(t => teamsMap[t.id] = t);

            const fixturesRes = await fetch('api.php?endpoint=fixtures/');
            fixturesData = await fixturesRes.json();

            // Update gameweek display
            const currentEvent = staticData.events.find(e => e.is_current);
            const nextEvent = staticData.events.find(e => e.is_next);
            
            const displayEvent = nextEvent || currentEvent;
            
            if(displayEvent) {
                const gwLabel = nextEvent ? `Gameweek ${displayEvent.id}` : `Gameweek ${displayEvent.id} (Live)`;
                const isLive = currentEvent && !nextEvent;
                
                document.getElementById('currentGWDisplay').innerHTML = `
                    ${isLive ? '<span class="badge bg-success me-2">LIVE</span>' : ''}
                    ${gwLabel}
                `;
                
                // Format deadline
                if(displayEvent.deadline_time) {
                    const deadline = new Date(displayEvent.deadline_time);
                    const options = { weekday: 'short', day: 'numeric', month: 'short', hour: '2-digit', minute: '2-digit' };
                    document.getElementById('gwDeadline').textContent = `Deadline: ${deadline.toLocaleDateString('en-GB', options)}`;
                }
            } else {
                document.getElementById('currentGWDisplay').textContent = 'Gameweek 1';
                document.getElementById('gwDeadline').textContent = 'Season not started';
            }

        } catch(e) {
            console.error('Init error:', e);
            document.getElementById('currentGWDisplay').textContent = 'Error loading';
        }
    }

    async function analyzeTeam() {
        const managerId = document.getElementById('managerId').value.trim();
        if(!managerId) return alert('Please enter your Manager ID');

        localStorage.setItem('fpl_manager_id', managerId);

        document.getElementById('loadingState').classList.remove('d-none');
        document.getElementById('resultsContainer').classList.add('d-none');

        try {
            // Get current GW
            const currentGW = staticData.events.find(e => e.is_current || e.is_next)?.id || 1;

            // Get manager's picks
            const picksRes = await fetch(`api.php?endpoint=entry/${managerId}/event/${currentGW}/picks/`);
            const picksData = await picksRes.json();

            // Get player data for squad
            const squadPlayers = picksData.picks.map(pick => {
                const player = playersMap[pick.element];
                const team = teamsMap[player.team];
                
                // Get next fixture
                const nextFixture = getNextFixture(player.team, currentGW);
                
                return {
                    ...player,
                    teamData: team,
                    fixture: nextFixture,
                    captainScore: calculateCaptainScore(player, nextFixture)
                };
            });

            // Sort by captain score
            squadPlayers.sort((a, b) => b.captainScore - a.captainScore);

            // Render results
            renderTopPick(squadPlayers[0]);
            renderCandidates(squadPlayers.slice(1, 5));
            renderDifferential(squadPlayers);
            renderComparison(squadPlayers[0]);

            document.getElementById('loadingState').classList.add('d-none');
            document.getElementById('resultsContainer').classList.remove('d-none');

        } catch(e) {
            console.error(e);
            alert('Failed to load data. Please check your Manager ID.');
            document.getElementById('loadingState').classList.add('d-none');
        }
    }

    function getNextFixture(teamId, gw) {
        const fixture = fixturesData.find(f => f.event === gw && (f.team_h === teamId || f.team_a === teamId));
        if(!fixture) return { opponent: 'TBD', difficulty: 3, isHome: true };

        const isHome = fixture.team_h === teamId;
        const opponentId = isHome ? fixture.team_a : fixture.team_h;
        const opponent = teamsMap[opponentId];
        const difficulty = isHome ? fixture.team_h_difficulty : fixture.team_a_difficulty;

        return {
            opponent: opponent?.short_name || 'TBD',
            opponentFull: opponent?.name || 'TBD',
            difficulty: difficulty,
            isHome: isHome
        };
    }

    function calculateCaptainScore(player, fixture) {
        // Weighted scoring algorithm
        const form = parseFloat(player.form) || 0;
        const xPts = parseFloat(player.ep_next) || 0;
        const ict = parseFloat(player.ict_index) || 0;
        const ppg = parseFloat(player.points_per_game) || 0;
        
        // Fixture difficulty modifier (lower is easier = higher score)
        const fdrModifier = fixture ? (6 - fixture.difficulty) / 5 : 0.5;
        
        // Home advantage
        const homeBonus = fixture?.isHome ? 1.1 : 1.0;

        // Base score from stats
        const baseScore = (form * 3) + (xPts * 5) + (ict * 0.5) + (ppg * 2);
        
        // Apply modifiers
        return baseScore * fdrModifier * homeBonus;
    }

    function renderTopPick(player) {
        const confidence = Math.min(95, Math.max(60, 50 + (player.captainScore / 2)));
        
        document.getElementById('topConfidence').textContent = confidence.toFixed(0) + '%';
        document.getElementById('topConfidenceRing').style.setProperty('--progress', confidence + '%');
        document.getElementById('topPlayerName').textContent = player.web_name;
        document.getElementById('topPlayerTeam').textContent = player.teamData?.name || '';
        
        // Fixture badge
        const fixtureBadge = document.getElementById('topFixture');
        const diffClass = player.fixture.difficulty <= 2 ? 'fixture-easy' : player.fixture.difficulty <= 3 ? 'fixture-medium' : 'fixture-hard';
        fixtureBadge.className = 'fixture-badge ' + diffClass;
        fixtureBadge.textContent = `${player.fixture.isHome ? '(H)' : '(A)'} vs ${player.fixture.opponent}`;

        // Differential badge
        const ownership = parseFloat(player.selected_by_percent) || 0;
        if(ownership < 15) {
            document.getElementById('topDifferential').classList.remove('d-none');
        }

        // Stats
        document.getElementById('topXPts').textContent = player.ep_next || '-';
        document.getElementById('topForm').textContent = player.form;
        document.getElementById('topOwnership').textContent = player.selected_by_percent + '%';
        document.getElementById('topICT').textContent = player.ict_index;

        // Reasoning
        const reasons = [];
        if(player.fixture.difficulty <= 2) reasons.push(`Favorable fixture vs ${player.fixture.opponent}`);
        if(parseFloat(player.form) >= 6) reasons.push(`Excellent recent form (${player.form})`);
        if(parseFloat(player.ep_next) >= 5) reasons.push(`High expected points (${player.ep_next})`);
        if(player.fixture.isHome) reasons.push('Home advantage');
        
        document.getElementById('topReasoning').textContent = reasons.length > 0 ? reasons.join(' • ') : 'Solid all-round pick based on multiple factors.';
    }

    function renderCandidates(candidates) {
        const grid = document.getElementById('candidatesGrid');
        let html = '';

        candidates.forEach((player, idx) => {
            const confidence = Math.min(90, Math.max(40, 45 + (player.captainScore / 2)));
            const diffClass = player.fixture.difficulty <= 2 ? 'fixture-easy' : player.fixture.difficulty <= 3 ? 'fixture-medium' : 'fixture-hard';
            
            html += `
                <div class="col-md-6 col-lg-3">
                    <div class="card captain-card h-100">
                        <div class="card-body text-center p-4">
                            <div class="badge bg-secondary mb-2">#${idx + 2}</div>
                            <h5 class="text-white fw-bold mb-1">${player.web_name}</h5>
                            <small class="text-white-50 d-block mb-3">${player.teamData?.short_name || ''}</small>
                            
                            <div class="confidence-ring mx-auto mb-3" style="--progress: ${confidence}%; width:70px; height:70px;">
                                <span class="confidence-value" style="font-size:1rem;">${confidence.toFixed(0)}%</span>
                            </div>
                            
                            <span class="fixture-badge ${diffClass} mb-3">${player.fixture.isHome ? '(H)' : '(A)'} ${player.fixture.opponent}</span>
                            
                            <div class="mt-3 small">
                                <div class="d-flex justify-content-between text-white-50 mb-1">
                                    <span>xPts</span><span class="text-white">${player.ep_next || '-'}</span>
                                </div>
                                <div class="d-flex justify-content-between text-white-50">
                                    <span>Form</span><span class="text-white">${player.form}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        });

        grid.innerHTML = html;
    }

    function renderDifferential(squadPlayers) {
        // Find best differential (low ownership, good stats)
        const differentials = squadPlayers.filter(p => parseFloat(p.selected_by_percent) < 15 && parseFloat(p.form) > 3);
        
        if(differentials.length === 0) {
            document.getElementById('differentialSection').innerHTML = `
                <p class="text-white-50 text-center mb-0">No strong differential options in your squad for this gameweek.</p>
            `;
            return;
        }

        const bestDiff = differentials[0];
        
        document.getElementById('diffPlayerName').textContent = bestDiff.web_name;
        document.getElementById('diffPlayerTeam').textContent = bestDiff.teamData?.name || '';
        document.getElementById('diffOwnership').textContent = bestDiff.selected_by_percent + '%';
        document.getElementById('diffXPts').textContent = bestDiff.ep_next || '-';
        document.getElementById('diffUpside').textContent = '+' + Math.round(parseFloat(bestDiff.ep_next || 0) * 2 * (1 - parseFloat(bestDiff.selected_by_percent)/100)) + ' rank pts';
        
        document.getElementById('diffReasoning').textContent = `Only ${bestDiff.selected_by_percent}% owned, but ${bestDiff.fixture.difficulty <= 2 ? 'great' : 'decent'} fixture and form of ${bestDiff.form}. High ceiling for rank gains.`;
    }

    function renderComparison(topPick) {
        // Compare with template captain picks
        const templatePicks = staticData.elements
            .filter(p => parseFloat(p.selected_by_percent) > 20)
            .sort((a, b) => parseFloat(b.form) - parseFloat(a.form))
            .slice(0, 3);

        let html = '';

        templatePicks.forEach(player => {
            const team = teamsMap[player.team];
            const isYourPick = player.id === topPick.id;
            
            html += `
                <div class="col-md-4">
                    <div class="vs-badge ${isYourPick ? 'border border-warning' : ''}">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <strong class="text-white">${player.web_name}</strong>
                                ${isYourPick ? '<span class="badge bg-warning text-dark ms-2">YOUR PICK</span>' : ''}
                                <div class="small text-white-50">${team?.short_name || ''}</div>
                            </div>
                            <div class="text-end">
                                <div class="text-white fw-bold">${player.selected_by_percent}%</div>
                                <div class="small text-white-50">owned</div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        });

        document.getElementById('comparisonSection').innerHTML = html;
    }

    // Initialize and auto-fetch if manager ID exists
    init().then(() => {
        const storedId = localStorage.getItem('fpl_manager_id');
        if(storedId && document.getElementById('managerId').value) {
            // Auto-analyze after a short delay to ensure everything is loaded
            setTimeout(() => {
                analyzeTeam();
            }, 500);
        }
    });
</script>
</body>
</html>
