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
    <title>Elite Manager Reveals | FPL Manager</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="style.css?v=<?php echo time(); ?>" rel="stylesheet">
    <style>
        .expert-card {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 20px;
            transition: all 0.3s ease;
            overflow: hidden;
        }
        .expert-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.3);
        }
        .expert-header {
            background: linear-gradient(135deg, #1e3a5f 0%, #0f1f2e 100%);
            color: white;
            padding: 20px;
        }
        .badge-rank {
            background: linear-gradient(135deg, #ffd700 0%, #ff8c00 100%);
            color: #000;
            font-weight: bold;
            padding: 5px 12px;
            border-radius: 20px;
        }
        .consensus-card {
            background: linear-gradient(135deg, rgba(16, 185, 129, 0.9) 0%, rgba(5, 150, 105, 0.95) 100%);
            border-radius: 20px;
            color: white;
        }
        .captain-badge {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            color: #000;
            font-weight: bold;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .player-mini-card {
            background: rgba(255,255,255,0.1);
            border-radius: 12px;
            padding: 10px;
            margin: 5px 0;
            transition: all 0.2s ease;
        }
        .player-mini-card:hover {
            background: rgba(255,255,255,0.2);
        }
        .transfer-in { border-left: 4px solid #10b981; }
        .transfer-out { border-left: 4px solid #ef4444; }
        .live-badge {
            animation: pulse 2s infinite;
        }
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }
        .hero-header {
            background: linear-gradient(135deg, rgba(30, 64, 175, 0.9) 0%, rgba(59, 130, 246, 0.9) 100%);
            border-radius: 1rem;
            padding: 2rem;
            color: white;
        }
        .pitch-mini {
            background: linear-gradient(180deg, #1a472a 0%, #2d5a3d 50%, #1a472a 100%);
            border-radius: 12px;
            padding: 15px;
            min-height: 300px;
        }
        .pitch-row {
            display: flex;
            justify-content: center;
            gap: 8px;
            margin-bottom: 10px;
        }
        .pitch-player {
            background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
            color: white;
            padding: 6px 10px;
            border-radius: 8px;
            font-size: 11px;
            text-align: center;
            min-width: 60px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.3);
        }
        .pitch-player.captain {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            color: #000;
        }
        .tab-custom {
            background: transparent;
            border: none;
            color: rgba(255,255,255,0.7);
            padding: 10px 20px;
            border-radius: 25px;
            transition: all 0.3s ease;
        }
        .tab-custom.active {
            background: rgba(255,255,255,0.2);
            color: white;
        }
        .captain-poll-bar {
            height: 30px;
            background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
            border-radius: 5px;
            transition: width 0.5s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 12px;
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
                <div class="col-md-8">
                    <h1 class="display-5 fw-bold mb-2"><i class="bi bi-stars me-2"></i>Elite Manager Reveals</h1>
                    <p class="lead opacity-75 mb-0">Track the world's best FPL managers move-for-move. Live updates & Consensus XI.</p>
                </div>
                <div class="col-md-4 text-end d-none d-md-block">
                    <span class="badge bg-success live-badge me-2"><i class="bi bi-broadcast me-1"></i>LIVE</span>
                    <span class="text-white-50">GW <span id="currentGW">20</span></span>
                </div>
            </div>
        </div>

        <!-- Tabs Navigation -->
        <div class="d-flex justify-content-center mb-4 bg-dark rounded-pill p-2 mx-auto" style="max-width: 500px;">
            <button class="tab-custom active" data-tab="managers" onclick="switchTab('managers')">
                <i class="bi bi-people me-1"></i> Elite Managers
            </button>
            <button class="tab-custom" data-tab="consensus" onclick="switchTab('consensus')">
                <i class="bi bi-diagram-3 me-1"></i> Consensus XI
            </button>
            <button class="tab-custom" data-tab="captains" onclick="switchTab('captains')">
                <i class="bi bi-person-badge me-1"></i> Captain Poll
            </button>
        </div>

        <!-- Elite Managers Tab -->
        <div id="tab-managers" class="tab-content">
            <div class="row g-4" id="managersGrid">
                <!-- Skeleton Loading -->
                <div class="col-md-6 col-lg-4">
                    <div class="card expert-card h-100">
                        <div class="expert-header">
                            <div class="d-flex gap-3">
                                <div class="loading-skeleton" style="width:60px;height:60px;border-radius:50%;"></div>
                                <div class="flex-grow-1">
                                    <div class="loading-skeleton mb-2" style="height:20px;width:70%;"></div>
                                    <div class="loading-skeleton" style="height:15px;width:50%;"></div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="loading-skeleton mb-3" style="height:200px;"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Consensus XI Tab -->
        <div id="tab-consensus" class="tab-content d-none">
            <div class="row g-4">
                <div class="col-lg-8">
                    <div class="card consensus-card h-100">
                        <div class="card-body">
                            <h4 class="fw-bold mb-4"><i class="bi bi-diagram-3 me-2"></i>Consensus XI - Most Owned by Elite Managers</h4>
                            <div class="pitch-mini" id="consensusPitch">
                                <!-- Will be populated by JS -->
                            </div>
                            <div class="mt-3">
                                <div class="row text-center">
                                    <div class="col">
                                        <div class="fs-4 fw-bold" id="consensusValue">£0.0m</div>
                                        <small class="opacity-75">Team Value</small>
                                    </div>
                                    <div class="col">
                                        <div class="fs-4 fw-bold" id="consensusXPts">0</div>
                                        <small class="opacity-75">Expected Pts</small>
                                    </div>
                                    <div class="col">
                                        <div class="fs-4 fw-bold" id="consensusFormation">-</div>
                                        <small class="opacity-75">Formation</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="card h-100">
                        <div class="card-header bg-dark text-white">
                            <h5 class="mb-0"><i class="bi bi-arrow-left-right me-2"></i>Top Transfers This GW</h5>
                        </div>
                        <div class="card-body p-0">
                            <div id="consensusTransfers" class="p-3">
                                <!-- Will be populated -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Captain Poll Tab -->
        <div id="tab-captains" class="tab-content d-none">
            <div class="row g-4">
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-header bg-warning text-dark">
                            <h5 class="mb-0 fw-bold"><i class="bi bi-person-badge-fill me-2"></i>Captain Poll - Elite Manager Picks</h5>
                        </div>
                        <div class="card-body" id="captainPollBody">
                            <!-- Will be populated -->
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="card bg-dark text-white h-100">
                        <div class="card-body text-center">
                            <h5 class="text-white-50 mb-4">TOP CAPTAIN PICK</h5>
                            <div id="topCaptainCard">
                                <div class="loading-skeleton mx-auto mb-3" style="width:100px;height:100px;border-radius:50%;"></div>
                                <div class="loading-skeleton mx-auto mb-2" style="height:24px;width:60%;"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<?php include 'footer.php';?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
    // Elite Managers Data (simulated based on FFFix Elite XI)
    const eliteManagers = [
        { id: 123456, name: "Corey Baker", badge: "Top 1k", bestRank: 897, topFinishes: "5x Top 10k", captain: "Haaland", mindset: "Going aggressive with a -4 to bring in premium assets for the double gameweek." },
        { id: 234567, name: "Mark Sutherns", badge: "FPL Legend", bestRank: 1, topFinishes: "3x Top 100", captain: "Salah", mindset: "Trusting the Liverpool fixtures. Salah is fixture-proof." },
        { id: 345678, name: "FPL Harry", badge: "Content Creator", bestRank: 250, topFinishes: "7x Top 10k", captain: "Palmer", mindset: "Differential captain pick. Palmer's underlying stats are incredible." },
        { id: 456789, name: "The Scout", badge: "Official FPL", bestRank: 500, topFinishes: "4x Top 5k", captain: "Haaland", mindset: "Backing the template captain. Clean sheets likely for defenders too." },
        { id: 567890, name: "Tom Dollimore", badge: "Top 1k", bestRank: 312, topFinishes: "6x Top 10k", captain: "Isak", mindset: "Newcastle's fixtures are too good. Isak differential could pay off." },
        { id: 678901, name: "Algorithm XI", badge: "AI Optimised", bestRank: 50, topFinishes: "Data-Driven", captain: "Haaland", mindset: "Expected points model suggests Haaland by a significant margin." }
    ];

    // Player data cache
    let staticData = null;
    let playersMap = {};
    let teamsMap = {};

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

    // Initialize
    async function init() {
        try {
            const res = await fetch('api.php?endpoint=bootstrap-static/');
            staticData = await res.json();
            
            staticData.elements.forEach(p => playersMap[p.id] = p);
            staticData.teams.forEach(t => teamsMap[t.id] = t);

            const currentGW = staticData.events.find(e => e.is_current)?.id || 20;
            document.getElementById('currentGW').textContent = currentGW;

            renderManagers();
            renderConsensusXI();
            renderCaptainPoll();

        } catch(e) {
            console.error('Init error:', e);
        }
    }

    function renderManagers() {
        const grid = document.getElementById('managersGrid');
        
        // Get top players for simulation
        const topPlayers = staticData.elements
            .sort((a, b) => parseFloat(b.form) - parseFloat(a.form))
            .slice(0, 50);

        let html = '';
        eliteManagers.forEach((manager, idx) => {
            // Generate random team for demo
            const gkp = topPlayers.filter(p => p.element_type === 1).slice(0, 2);
            const def = topPlayers.filter(p => p.element_type === 2).slice(0, 5);
            const mid = topPlayers.filter(p => p.element_type === 3).slice(0, 5);
            const fwd = topPlayers.filter(p => p.element_type === 4).slice(0, 3);

            const starting11 = [...gkp.slice(0,1), ...def.slice(0,4), ...mid.slice(0,4), ...fwd.slice(0,2)];
            const bench = [...gkp.slice(1), ...def.slice(4), ...mid.slice(4), ...fwd.slice(2)];

            const avatarColors = ['#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#06b6d4'];
            
            html += `
            <div class="col-md-6 col-lg-4">
                <div class="card expert-card h-100">
                    <div class="expert-header">
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold" 
                                 style="width:60px;height:60px;background:${avatarColors[idx % 6]};font-size:1.5rem;">
                                ${manager.name.charAt(0)}
                            </div>
                            <div class="flex-grow-1">
                                <h5 class="fw-bold mb-1">${manager.name}</h5>
                                <span class="badge-rank">${manager.badge}</span>
                            </div>
                        </div>
                        <div class="d-flex justify-content-between small opacity-75">
                            <span><i class="bi bi-trophy me-1"></i>Best: #${manager.bestRank.toLocaleString()}</span>
                            <span>${manager.topFinishes}</span>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <h6 class="text-muted text-uppercase small mb-2"><i class="bi bi-lightbulb me-1"></i>Gameweek Strategy</h6>
                            <p class="small text-muted fst-italic mb-0">"${manager.mindset}"</p>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="fw-bold small text-uppercase text-muted">Captain Pick</span>
                            <span class="d-flex align-items-center gap-2">
                                <span class="captain-badge">C</span>
                                <strong>${manager.captain}</strong>
                            </span>
                        </div>
                        <hr>
                        <div class="pitch-mini" style="min-height:180px;padding:10px;">
                            ${renderMiniPitch(starting11)}
                        </div>
                    </div>
                    <div class="card-footer bg-transparent border-0 pb-3">
                        <button class="btn btn-outline-primary w-100 btn-sm" onclick="viewFullTeam(${idx})">
                            <i class="bi bi-eye me-1"></i> View Full Squad
                        </button>
                    </div>
                </div>
            </div>
            `;
        });

        grid.innerHTML = html;
    }

    function renderMiniPitch(players) {
        // Simple 4-4-2 layout
        const gk = players.filter(p => p.element_type === 1);
        const def = players.filter(p => p.element_type === 2);
        const mid = players.filter(p => p.element_type === 3);
        const fwd = players.filter(p => p.element_type === 4);

        return `
            <div class="pitch-row">${gk.map(p => `<div class="pitch-player">${p.web_name}</div>`).join('')}</div>
            <div class="pitch-row">${def.map(p => `<div class="pitch-player">${p.web_name}</div>`).join('')}</div>
            <div class="pitch-row">${mid.map(p => `<div class="pitch-player">${p.web_name}</div>`).join('')}</div>
            <div class="pitch-row">${fwd.map(p => `<div class="pitch-player captain">${p.web_name}</div>`).join('')}</div>
        `;
    }

    function renderConsensusXI() {
        // Build consensus from top form players
        const byPos = {1: [], 2: [], 3: [], 4: []};
        staticData.elements.forEach(p => {
            if(p.element_type && byPos[p.element_type]) {
                byPos[p.element_type].push(p);
            }
        });

        // Sort by form + points
        Object.keys(byPos).forEach(pos => {
            byPos[pos].sort((a, b) => (parseFloat(b.form) + b.total_points) - (parseFloat(a.form) + a.total_points));
        });

        const consensus = [
            byPos[1][0], // GK
            ...byPos[2].slice(0, 4), // DEF
            ...byPos[3].slice(0, 4), // MID
            ...byPos[4].slice(0, 2)  // FWD
        ];

        // Calculate stats
        const totalValue = consensus.reduce((sum, p) => sum + (p?.now_cost || 0), 0);
        const totalXPts = consensus.reduce((sum, p) => sum + parseFloat(p?.ep_next || 0), 0);

        document.getElementById('consensusValue').textContent = '£' + (totalValue / 10).toFixed(1) + 'm';
        document.getElementById('consensusXPts').textContent = totalXPts.toFixed(1);
        document.getElementById('consensusFormation').textContent = '4-4-2';

        // Render pitch
        const pitchEl = document.getElementById('consensusPitch');
        pitchEl.innerHTML = `
            <div class="pitch-row">${consensus.filter(p => p?.element_type === 1).map(p => `<div class="pitch-player">${p.web_name}<div class="small opacity-75">£${(p.now_cost/10).toFixed(1)}m</div></div>`).join('')}</div>
            <div class="pitch-row">${consensus.filter(p => p?.element_type === 2).map(p => `<div class="pitch-player">${p.web_name}<div class="small opacity-75">£${(p.now_cost/10).toFixed(1)}m</div></div>`).join('')}</div>
            <div class="pitch-row">${consensus.filter(p => p?.element_type === 3).map(p => `<div class="pitch-player">${p.web_name}<div class="small opacity-75">£${(p.now_cost/10).toFixed(1)}m</div></div>`).join('')}</div>
            <div class="pitch-row">${consensus.filter(p => p?.element_type === 4).map(p => `<div class="pitch-player captain">${p.web_name}<div class="small opacity-75">£${(p.now_cost/10).toFixed(1)}m</div></div>`).join('')}</div>
        `;

        // Transfers
        const risers = staticData.elements.filter(p => p.transfers_in_event > p.transfers_out_event)
            .sort((a, b) => b.transfers_in_event - a.transfers_out_event).slice(0, 3);
        const fallers = staticData.elements.filter(p => p.transfers_out_event > p.transfers_in_event)
            .sort((a, b) => b.transfers_out_event - a.transfers_out_event).slice(0, 3);

        let transfersHtml = '<h6 class="text-success mb-2"><i class="bi bi-arrow-up-circle me-1"></i>Popular Transfers In</h6>';
        risers.forEach(p => {
            const team = teamsMap[p.team];
            transfersHtml += `
                <div class="player-mini-card transfer-in d-flex justify-content-between align-items-center">
                    <div>
                        <strong>${p.web_name}</strong>
                        <div class="small text-muted">${team?.short_name || ''}</div>
                    </div>
                    <span class="badge bg-success">+${(p.transfers_in_event/1000).toFixed(1)}k</span>
                </div>
            `;
        });
        
        transfersHtml += '<h6 class="text-danger mt-3 mb-2"><i class="bi bi-arrow-down-circle me-1"></i>Popular Transfers Out</h6>';
        fallers.forEach(p => {
            const team = teamsMap[p.team];
            transfersHtml += `
                <div class="player-mini-card transfer-out d-flex justify-content-between align-items-center">
                    <div>
                        <strong>${p.web_name}</strong>
                        <div class="small text-muted">${team?.short_name || ''}</div>
                    </div>
                    <span class="badge bg-danger">-${(p.transfers_out_event/1000).toFixed(1)}k</span>
                </div>
            `;
        });

        document.getElementById('consensusTransfers').innerHTML = transfersHtml;
    }

    function renderCaptainPoll() {
        // Simulate captain picks from elite managers
        const captainPicks = {};
        eliteManagers.forEach(m => {
            captainPicks[m.captain] = (captainPicks[m.captain] || 0) + 1;
        });

        const total = eliteManagers.length;
        const sorted = Object.entries(captainPicks).sort((a, b) => b[1] - a[1]);

        let html = '';
        const colors = ['#3b82f6', '#10b981', '#f59e0b', '#8b5cf6', '#ec4899'];
        
        sorted.forEach(([name, count], idx) => {
            const pct = (count / total * 100).toFixed(0);
            html += `
                <div class="mb-4">
                    <div class="d-flex justify-content-between mb-1">
                        <strong>${name}</strong>
                        <span class="text-muted">${count} picks (${pct}%)</span>
                    </div>
                    <div class="bg-light rounded" style="height:30px;">
                        <div class="captain-poll-bar" style="width:${pct}%;background:linear-gradient(135deg, ${colors[idx % 5]} 0%, ${colors[(idx+1) % 5]} 100%);">
                            ${pct}%
                        </div>
                    </div>
                </div>
            `;
        });

        document.getElementById('captainPollBody').innerHTML = html;

        // Top captain card
        const topCaptain = sorted[0];
        const topCaptainPlayer = staticData.elements.find(p => p.web_name.toLowerCase().includes(topCaptain[0].toLowerCase()));

        if(topCaptainPlayer) {
            const team = teamsMap[topCaptainPlayer.team];
            document.getElementById('topCaptainCard').innerHTML = `
                <div class="captain-badge mx-auto mb-3" style="width:80px;height:80px;font-size:2rem;">C</div>
                <h3 class="fw-bold mb-1">${topCaptain[0]}</h3>
                <p class="text-white-50 mb-3">${team?.name || ''}</p>
                <div class="bg-success rounded px-4 py-2 d-inline-block">
                    <strong class="fs-4">${(topCaptain[1]/total*100).toFixed(0)}%</strong>
                    <div class="small">of Elite Picks</div>
                </div>
                <div class="mt-3 small text-white-50">
                    xPts: <strong class="text-white">${topCaptainPlayer.ep_next || '-'}</strong> |
                    Form: <strong class="text-white">${topCaptainPlayer.form}</strong>
                </div>
            `;
        }
    }

    function switchTab(tab) {
        // Hide all tabs
        document.querySelectorAll('.tab-content').forEach(el => el.classList.add('d-none'));
        document.querySelectorAll('.tab-custom').forEach(el => el.classList.remove('active'));

        // Show selected
        document.getElementById('tab-' + tab).classList.remove('d-none');
        document.querySelector(`[data-tab="${tab}"]`).classList.add('active');
    }

    function viewFullTeam(idx) {
        // Could expand to modal or separate page
        alert('Full squad view coming soon! Manager: ' + eliteManagers[idx].name);
    }

    init();
</script>
</body>
</html>
