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
    <title>Price Change Predictor | FPL Manager</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="style.css?v=<?php echo time(); ?>" rel="stylesheet">
    <style>
        .hero-header {
            background: linear-gradient(135deg, rgba(16, 185, 129, 0.95) 0%, rgba(5, 150, 105, 0.95) 100%);
            border-radius: 1rem;
            padding: 2rem;
            color: white;
        }
        .price-card {
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
            border-radius: 16px;
            border: none;
            overflow: hidden;
            transition: all 0.3s ease;
        }
        .price-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 30px rgba(0,0,0,0.3);
        }
        .riser { border-left: 4px solid #10b981; }
        .faller { border-left: 4px solid #ef4444; }
        .price-change-badge {
            font-weight: bold;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 14px;
        }
        .badge-rise { background: #10b981; color: white; }
        .badge-fall { background: #ef4444; color: white; }
        .progress-bar-custom {
            height: 10px;
            border-radius: 5px;
            background: #374151;
            overflow: hidden;
        }
        .progress-fill-rise {
            height: 100%;
            background: linear-gradient(90deg, #10b981 0%, #34d399 100%);
            transition: width 0.5s ease;
        }
        .progress-fill-fall {
            height: 100%;
            background: linear-gradient(90deg, #ef4444 0%, #f87171 100%);
            transition: width 0.5s ease;
        }
        .probability-badge {
            background: rgba(255,255,255,0.1);
            padding: 8px 15px;
            border-radius: 12px;
            font-size: 13px;
        }
        .filter-btn {
            background: transparent;
            border: 1px solid rgba(255,255,255,0.2);
            color: rgba(255,255,255,0.7);
            padding: 8px 20px;
            border-radius: 25px;
            transition: all 0.3s ease;
        }
        .filter-btn.active, .filter-btn:hover {
            background: rgba(255,255,255,0.2);
            color: white;
            border-color: rgba(255,255,255,0.4);
        }
        .watchlist-btn {
            background: rgba(245, 158, 11, 0.2);
            border: 1px solid rgba(245, 158, 11, 0.5);
            color: #f59e0b;
            padding: 5px 12px;
            border-radius: 8px;
            font-size: 12px;
        }
        .watchlist-btn.active {
            background: #f59e0b;
            color: #000;
        }
        .transfer-net {
            font-size: 12px;
            padding: 3px 8px;
            border-radius: 10px;
        }
        .net-positive { background: rgba(16, 185, 129, 0.2); color: #10b981; }
        .net-negative { background: rgba(239, 68, 68, 0.2); color: #ef4444; }
        .deadline-timer {
            background: linear-gradient(135deg, rgba(239, 68, 68, 0.2) 0%, rgba(239, 68, 68, 0.1) 100%);
            border: 1px solid rgba(239, 68, 68, 0.3);
            border-radius: 12px;
            padding: 15px 20px;
        }
        .section-card {
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 16px;
        }
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }
        .live-indicator {
            animation: pulse 2s infinite;
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
                    <h1 class="display-5 fw-bold mb-2"><i class="bi bi-graph-up-arrow me-2"></i>Price Change Predictor</h1>
                    <p class="lead mb-0 opacity-75">AI-powered predictions for tonight's price changes based on transfer activity.</p>
                </div>
                <div class="col-md-5 text-end">
                    <div class="deadline-timer">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <small class="text-white-50">Next Price Change</small>
                                <div class="text-white fw-bold" id="nextChange">~2:30 AM GMT</div>
                            </div>
                            <div class="live-indicator badge bg-danger me-0">LIVE DATA</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="card section-card h-100">
                    <div class="card-body text-center">
                        <div class="text-success fs-1 fw-bold" id="risersCount">0</div>
                        <small class="text-white-50">Predicted Risers</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card section-card h-100">
                    <div class="card-body text-center">
                        <div class="text-danger fs-1 fw-bold" id="fallersCount">0</div>
                        <small class="text-white-50">Predicted Fallers</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card section-card h-100">
                    <div class="card-body text-center">
                        <div class="text-warning fs-1 fw-bold" id="watchlistCount">0</div>
                        <small class="text-white-50">Your Watchlist</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card section-card h-100">
                    <div class="card-body text-center">
                        <div class="text-info fs-1 fw-bold" id="lastUpdated">-</div>
                        <small class="text-white-50">Minutes Ago</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="d-flex justify-content-center gap-2 mb-4 flex-wrap">
            <button class="filter-btn active" data-filter="all" onclick="filterPlayers('all')">
                <i class="bi bi-grid me-1"></i> All
            </button>
            <button class="filter-btn" data-filter="risers" onclick="filterPlayers('risers')">
                <i class="bi bi-arrow-up me-1"></i> Risers
            </button>
            <button class="filter-btn" data-filter="fallers" onclick="filterPlayers('fallers')">
                <i class="bi bi-arrow-down me-1"></i> Fallers
            </button>
            <button class="filter-btn" data-filter="watchlist" onclick="filterPlayers('watchlist')">
                <i class="bi bi-star me-1"></i> Watchlist
            </button>
        </div>

        <!-- Search -->
        <div class="row mb-4">
            <div class="col-md-6 mx-auto">
                <div class="input-group">
                    <span class="input-group-text bg-dark border-secondary text-white"><i class="bi bi-search"></i></span>
                    <input type="text" id="searchBox" class="form-control bg-dark border-secondary text-white" placeholder="Search player name...">
                </div>
            </div>
        </div>

        <!-- Players Grid -->
        <div class="row g-3" id="playersGrid">
            <!-- Will be populated by JS -->
        </div>

        <!-- Load More -->
        <div class="text-center mt-4">
            <button class="btn btn-outline-light" id="loadMoreBtn" onclick="loadMore()">
                <i class="bi bi-plus-circle me-2"></i>Load More
            </button>
        </div>

    </div>
</div>

<?php include 'footer.php';?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
    let staticData = null;
    let playersMap = {};
    let teamsMap = {};
    let allPriceChanges = [];
    let displayedCount = 20;
    let currentFilter = 'all';
    let watchlist = JSON.parse(localStorage.getItem('fpl_watchlist') || '[]');

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

    function getTeamLogoHtml(teamName, size = 20) {
        const logoPath = getTeamLogo(teamName);
        if (logoPath) {
            return `<img src="${logoPath}" alt="${teamName}" style="height: ${size}px; width: ${size}px; object-fit: contain;" class="me-1">`;
        }
        return '';
    }

    // Initialize
    async function init() {
        try {
            const res = await fetch('api.php?endpoint=bootstrap-static/');
            staticData = await res.json();
            
            staticData.elements.forEach(p => playersMap[p.id] = p);
            staticData.teams.forEach(t => teamsMap[t.id] = t);

            // Calculate price change predictions
            calculatePredictions();
            
            // Update counts
            updateCounts();
            
            // Render
            renderPlayers();

            // Last updated
            document.getElementById('lastUpdated').textContent = '1';

        } catch(e) {
            console.error('Init error:', e);
        }
    }

    function calculatePredictions() {
        // Calculate transfer net and price change probability
        allPriceChanges = staticData.elements.map(p => {
            const team = teamsMap[p.team];
            const netTransfers = p.transfers_in_event - p.transfers_out_event;
            
            // Estimate price change threshold (simplified algorithm)
            // Real FPL uses ownership % thresholds, this is an approximation
            const ownershipChange = (netTransfers / 10000000) * 100; // Rough percentage impact
            const currentPrice = p.now_cost / 10;
            
            let probability = 0;
            let direction = 'stable';
            
            if(netTransfers > 50000) {
                probability = Math.min(95, 40 + (netTransfers / 10000));
                direction = 'rise';
            } else if(netTransfers < -50000) {
                probability = Math.min(95, 40 + (Math.abs(netTransfers) / 10000));
                direction = 'fall';
            } else if(netTransfers > 20000) {
                probability = 20 + (netTransfers / 5000);
                direction = 'rise';
            } else if(netTransfers < -20000) {
                probability = 20 + (Math.abs(netTransfers) / 5000);
                direction = 'fall';
            }

            return {
                ...p,
                teamData: team,
                netTransfers: netTransfers,
                probability: Math.min(99, Math.max(0, probability)),
                direction: direction,
                newPrice: direction === 'rise' ? currentPrice + 0.1 : direction === 'fall' ? currentPrice - 0.1 : currentPrice,
                inWatchlist: watchlist.includes(p.id)
            };
        });

        // Sort by probability (most likely changes first)
        allPriceChanges.sort((a, b) => b.probability - a.probability);
    }

    function updateCounts() {
        const risers = allPriceChanges.filter(p => p.direction === 'rise' && p.probability > 30);
        const fallers = allPriceChanges.filter(p => p.direction === 'fall' && p.probability > 30);
        
        document.getElementById('risersCount').textContent = risers.length;
        document.getElementById('fallersCount').textContent = fallers.length;
        document.getElementById('watchlistCount').textContent = watchlist.length;
    }

    function filterPlayers(filter) {
        currentFilter = filter;
        displayedCount = 20;
        
        // Update button states
        document.querySelectorAll('.filter-btn').forEach(btn => {
            btn.classList.remove('active');
            if(btn.dataset.filter === filter) btn.classList.add('active');
        });

        renderPlayers();
    }

    function renderPlayers() {
        let filtered = [...allPriceChanges];
        const searchTerm = document.getElementById('searchBox').value.toLowerCase();

        // Apply filter
        switch(currentFilter) {
            case 'risers':
                filtered = filtered.filter(p => p.direction === 'rise' && p.probability > 20);
                break;
            case 'fallers':
                filtered = filtered.filter(p => p.direction === 'fall' && p.probability > 20);
                break;
            case 'watchlist':
                filtered = filtered.filter(p => watchlist.includes(p.id));
                break;
            default:
                filtered = filtered.filter(p => p.probability > 15);
        }

        // Apply search
        if(searchTerm) {
            filtered = filtered.filter(p => 
                (p.first_name + ' ' + p.second_name).toLowerCase().includes(searchTerm) ||
                p.web_name.toLowerCase().includes(searchTerm)
            );
        }

        const toShow = filtered.slice(0, displayedCount);
        const grid = document.getElementById('playersGrid');

        if(toShow.length === 0) {
            grid.innerHTML = `
                <div class="col-12 text-center py-5">
                    <i class="bi bi-emoji-neutral display-1 text-muted opacity-50"></i>
                    <p class="text-white-50 mt-3">No price changes predicted matching your criteria</p>
                </div>
            `;
            return;
        }

        let html = '';
        toShow.forEach(player => {
            const isRiser = player.direction === 'rise';
            const isFaller = player.direction === 'fall';
            const borderClass = isRiser ? 'riser' : isFaller ? 'faller' : '';
            const badgeClass = isRiser ? 'badge-rise' : 'badge-fall';
            const progressClass = isRiser ? 'progress-fill-rise' : 'progress-fill-fall';
            const netClass = player.netTransfers > 0 ? 'net-positive' : 'net-negative';
            const netSymbol = player.netTransfers > 0 ? '+' : '';
            const watchActive = watchlist.includes(player.id) ? 'active' : '';

            if(player.probability < 10) return; // Skip very low probability

            html += `
                <div class="col-md-6 col-lg-4" data-player-id="${player.id}">
                    <div class="card price-card ${borderClass} h-100">
                        <div class="card-body p-3">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div class="d-flex align-items-center gap-2">
                                    ${getTeamLogoHtml(player.teamData?.name, 24)}
                                    <div>
                                        <h6 class="text-white fw-bold mb-0">${player.web_name}</h6>
                                        <small class="text-white-50">${player.teamData?.short_name || ''}</small>
                                    </div>
                                </div>
                                <button class="watchlist-btn ${watchActive}" onclick="toggleWatchlist(${player.id})">
                                    <i class="bi bi-star${watchActive ? '-fill' : ''}"></i>
                                </button>
                            </div>

                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div>
                                    <span class="text-white-50 small">Current</span>
                                    <div class="text-white fw-bold">£${(player.now_cost/10).toFixed(1)}m</div>
                                </div>
                                <div class="text-center">
                                    <i class="bi bi-arrow-${isRiser ? 'up' : 'down'} text-${isRiser ? 'success' : 'danger'} fs-4"></i>
                                </div>
                                <div class="text-end">
                                    <span class="text-white-50 small">Predicted</span>
                                    <div class="text-${isRiser ? 'success' : 'danger'} fw-bold">£${player.newPrice.toFixed(1)}m</div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <div class="d-flex justify-content-between mb-1">
                                    <small class="text-white-50">Probability</small>
                                    <small class="text-white fw-bold">${player.probability.toFixed(0)}%</small>
                                </div>
                                <div class="progress-bar-custom">
                                    <div class="${progressClass}" style="width: ${player.probability}%;"></div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between align-items-center">
                                <span class="transfer-net ${netClass}">
                                    <i class="bi bi-arrow-${player.netTransfers > 0 ? 'up' : 'down'}-short me-1"></i>
                                    ${netSymbol}${(player.netTransfers/1000).toFixed(1)}k
                                </span>
                                <span class="probability-badge">
                                    Owned: ${player.selected_by_percent}%
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        });

        grid.innerHTML = html;

        // Show/hide load more
        document.getElementById('loadMoreBtn').style.display = displayedCount >= filtered.length ? 'none' : 'inline-block';
    }

    function toggleWatchlist(playerId) {
        const idx = watchlist.indexOf(playerId);
        if(idx > -1) {
            watchlist.splice(idx, 1);
        } else {
            watchlist.push(playerId);
        }
        localStorage.setItem('fpl_watchlist', JSON.stringify(watchlist));
        
        // Update the price change data
        allPriceChanges.forEach(p => {
            p.inWatchlist = watchlist.includes(p.id);
        });

        updateCounts();
        renderPlayers();
    }

    function loadMore() {
        displayedCount += 20;
        renderPlayers();
    }

    // Search handler
    document.getElementById('searchBox').addEventListener('input', () => {
        displayedCount = 20;
        renderPlayers();
    });

    init();
</script>
</body>
</html>
