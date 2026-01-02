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
    <title>AI Team Improver | FPL Manager</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="style.css?v=<?php echo time(); ?>" rel="stylesheet">
    <style>
        /* Animated Gradient Background */
        .hero-gradient {
            background: linear-gradient(135deg, rgba(55, 0, 60, 0.03) 0%, rgba(0, 255, 133, 0.03) 100%);
            border-radius: 20px;
            padding: 2rem;
            position: relative;
            overflow: hidden;
        }
        .hero-gradient::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 100%;
            height: 100%;
            background: radial-gradient(circle, rgba(0,255,133,0.1) 0%, transparent 70%);
            animation: float 8s ease-in-out infinite;
        }
        @keyframes float {
            0%, 100% { transform: translate(0, 0) rotate(0deg); }
            50% { transform: translate(-20px, 20px) rotate(5deg); }
        }

        /* Step Cards with Glassmorphism */
        .step-card {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.5);
            border-radius: 16px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
        }
        .step-card:hover {
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.12);
            transform: translateY(-2px);
        }
        .step-number {
            width: 32px;
            height: 32px;
            background: linear-gradient(135deg, var(--primary-color), #00ff85);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #000;
            font-weight: bold;
            font-size: 0.9rem;
        }

        /* Enhanced Transfer Cards */
        .transfer-card {
            border-left: 4px solid var(--accent-color);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border-radius: 12px;
            overflow: hidden;
            background: linear-gradient(135deg, #fff 0%, #f8f9fa 100%);
        }
        .transfer-card:hover {
            transform: translateX(8px) scale(1.01);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        }
        .transfer-card.diff-card {
            border-left-color: #fd7e14;
            background: linear-gradient(135deg, #fff 0%, #fff5ed 100%);
        }
        .transfer-card.block-card {
            border-left-color: #20c997;
            background: linear-gradient(135deg, #fff 0%, #e8fff6 100%);
        }
        .player-out {
            background: rgba(220, 53, 69, 0.05);
        }
        .player-in {
            background: rgba(25, 135, 84, 0.05);
        }
        .ownership-bar {
            height: 6px;
            border-radius: 3px;
            background: #e9ecef;
        }
        .ownership-fill {
            height: 100%;
            border-radius: 3px;
            background: var(--secondary-color);
        }

        /* Stat Cards with Gradient Backgrounds */
        .stat-card {
            border-radius: 16px;
            border: none;
            overflow: hidden;
            transition: all 0.3s ease;
        }
        .stat-card:hover {
            transform: translateY(-3px);
        }
        .stat-card.stat-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        .stat-card.stat-success {
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
            color: white;
        }
        .stat-card.stat-danger {
            background: linear-gradient(135deg, #eb3349 0%, #f45c43 100%);
            color: white;
        }
        .stat-card.stat-warning {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
        }
        .stat-card.stat-info {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            color: white;
        }
        .stat-card.stat-dark {
            background: linear-gradient(135deg, #434343 0%, #000000 100%);
            color: white;
        }
        .stat-card .stat-icon {
            position: absolute;
            right: -10px;
            top: -10px;
            font-size: 4rem;
            opacity: 0.15;
        }

        /* Enhanced Loading Animation */
        .loading-container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 3rem;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.1);
        }
        .pulse-loader {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, var(--primary-color) 0%, #00ff85 100%);
            border-radius: 50%;
            animation: pulse 1.5s ease-in-out infinite;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .pulse-loader i {
            font-size: 2rem;
            color: #000;
        }
        @keyframes pulse {
            0%, 100% { transform: scale(1); box-shadow: 0 0 0 0 rgba(0, 255, 133, 0.4); }
            50% { transform: scale(1.05); box-shadow: 0 0 0 20px rgba(0, 255, 133, 0); }
        }

        /* Strategy Toggle Enhancement */
        .strategy-btn {
            border-radius: 12px !important;
            padding: 1rem !important;
            transition: all 0.3s ease;
        }
        .btn-check:checked + .strategy-btn {
            background: linear-gradient(135deg, var(--primary-color) 0%, #00ff85 100%) !important;
            border-color: transparent !important;
            color: #000 !important;
            box-shadow: 0 8px 25px rgba(0, 255, 133, 0.3);
        }

        /* Section Headers */
        .section-header {
            position: relative;
            padding-left: 1rem;
        }
        .section-header::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            height: 100%;
            width: 4px;
            background: linear-gradient(180deg, var(--primary-color) 0%, #00ff85 100%);
            border-radius: 2px;
        }

        /* Animated Reveal */
        .fade-in-up {
            animation: fadeInUp 0.5s ease forwards;
        }
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Position Filter Pills */
        .position-filters {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
            margin-bottom: 1rem;
        }
        .pos-filter-btn {
            padding: 0.4rem 1rem;
            border-radius: 50px;
            border: 2px solid #e9ecef;
            background: white;
            font-weight: 600;
            font-size: 0.8rem;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        .pos-filter-btn:hover {
            border-color: var(--primary-color);
            background: rgba(0, 255, 133, 0.05);
        }
        .pos-filter-btn.active {
            background: linear-gradient(135deg, var(--primary-color), #00ff85);
            border-color: transparent;
            color: #000;
            box-shadow: 0 4px 15px rgba(0, 255, 133, 0.3);
        }

        /* Fixture Ticker */
        .fixture-ticker {
            display: flex;
            gap: 3px;
            margin-top: 6px;
        }
        .fixture-dot {
            width: 24px;
            height: 24px;
            border-radius: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.6rem;
            font-weight: 700;
            color: white;
            text-shadow: 0 1px 2px rgba(0,0,0,0.3);
        }
        .fdr-1, .fdr-2 { background: linear-gradient(135deg, #00c853, #69f0ae); }
        .fdr-3 { background: linear-gradient(135deg, #ffc107, #ffeb3b); color: #333 !important; }
        .fdr-4 { background: linear-gradient(135deg, #ff5722, #ff8a65); }
        .fdr-5 { background: linear-gradient(135deg, #d32f2f, #ef5350); }
        .fdr-blank { background: #e0e0e0; color: #999; }

        /* Confidence Meter */
        .confidence-meter {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .confidence-bar {
            width: 60px;
            height: 6px;
            background: #e9ecef;
            border-radius: 3px;
            overflow: hidden;
        }
        .confidence-fill {
            height: 100%;
            border-radius: 3px;
            transition: width 0.5s ease;
        }
        .confidence-high { background: linear-gradient(90deg, #00c853, #69f0ae); }
        .confidence-med { background: linear-gradient(90deg, #ffc107, #ffeb3b); }
        .confidence-low { background: linear-gradient(90deg, #ff5722, #ff8a65); }

        /* Priority Badge */
        .priority-badge {
            position: absolute;
            top: -8px;
            left: 12px;
            padding: 2px 10px;
            border-radius: 20px;
            font-size: 0.65rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            z-index: 10;
            box-shadow: 0 2px 8px rgba(0,0,0,0.15);
        }
        .priority-1 { background: linear-gradient(135deg, #ff4444, #ff6b6b); color: white; }
        .priority-2 { background: linear-gradient(135deg, #ffa726, #ffcc02); color: #333; }
        .priority-3 { background: linear-gradient(135deg, #4fc3f7, #29b6f6); color: white; }

        /* Expected Points Badge */
        .xp-badge {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 0.7rem;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }
        .xp-badge i { font-size: 0.6rem; }

        /* Chip Strategy Panel */
        .chip-panel {
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
            border-radius: 16px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            color: white;
        }
        .chip-panel h6 {
            color: rgba(255,255,255,0.9);
            font-weight: 700;
            margin-bottom: 1rem;
        }
        .chip-suggestion {
            background: rgba(255,255,255,0.1);
            backdrop-filter: blur(5px);
            border-radius: 12px;
            padding: 1rem;
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        .chip-icon {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }
        .chip-wc { background: linear-gradient(135deg, #43e97b, #38f9d7); }
        .chip-fh { background: linear-gradient(135deg, #fa709a, #fee140); }
        .chip-bb { background: linear-gradient(135deg, #a8edea, #fed6e3); color: #333; }
        .chip-tc { background: linear-gradient(135deg, #667eea, #764ba2); }

        /* Points Gain Estimate */
        .points-gain {
            display: flex;
            align-items: center;
            gap: 4px;
            font-weight: 700;
            font-size: 0.85rem;
        }
        .points-gain.positive { color: #00c853; }
        .points-gain.neutral { color: #ffc107; }
        .points-gain.negative { color: #ff5722; }

        /* Transfer Card Enhancements */
        .transfer-card {
            position: relative;
            overflow: visible !important;
        }
        .transfer-card .player-stats {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
            margin-top: 0.5rem;
        }
        .mini-stat {
            background: #f8f9fa;
            padding: 2px 8px;
            border-radius: 4px;
            font-size: 0.7rem;
            font-weight: 600;
        }

        /* Summary Cards Grid */
        .summary-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
            gap: 0.75rem;
        }

        @media (max-width: 576px) {
            .fixture-ticker { gap: 2px; }
            .fixture-dot { width: 20px; height: 20px; font-size: 0.5rem; }
            .transfer-card .card-body { padding: 0.75rem !important; }
        }
    </style>
</head>
<body>
<?php include 'navbar.php';?>

<div class="main-content">
    <div class="container py-5">
        <!-- Hero Header -->
        <div class="hero-gradient mb-5">
            <div class="row align-items-center">
                <div class="col-md-8 text-center text-md-start position-relative z-1">
                    <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill mb-3">
                        <i class="bi bi-robot me-1"></i> AI-Powered Analysis
                    </span>
                    <h1 class="display-4 fw-bold mb-3">League-Smart Transfers</h1>
                    <p class="lead opacity-75 mb-0">Analyze your mini-league rivals and find the winning edge with fixture-weighted AI recommendations.</p>
                </div>
                <div class="col-md-4 text-center d-none d-md-block position-relative z-1">
                    <i class="bi bi-graph-up-arrow display-1 opacity-25" style="font-size: 8rem;"></i>
                </div>
            </div>
        </div>

        <!-- Step 1: Manager ID -->
        <div class="step-card mb-4" id="step1">
            <div class="card-body p-4">
                <div class="d-flex align-items-center mb-3">
                    <div class="step-number me-3">1</div>
                    <h5 class="mb-0 fw-bold">Load Your Team</h5>
                </div>
                <div class="input-group input-group-lg">
                    <span class="input-group-text bg-white border-end-0"><i class="bi bi-person-badge text-primary"></i></span>
                    <input type="number" id="managerId" class="form-control border-start-0 ps-0" placeholder="Enter your Manager ID">
                    <button class="btn btn-primary px-4" id="loadLeaguesBtn">
                        <i class="bi bi-arrow-right-circle me-2"></i>Load Leagues
                    </button>
                </div>
                <div class="text-muted small mt-2"><i class="bi bi-info-circle me-1"></i>Find your ID on the FPL website under "Points" page URL</div>
            </div>
        </div>

        <!-- Step 2: Select League -->
        <div class="step-card mb-4 d-none" id="step2">
            <div class="card-body p-4">
                <div class="d-flex align-items-center mb-3">
                    <div class="step-number me-3">2</div>
                    <h5 class="mb-0 fw-bold">Select Target League & Strategy</h5>
                </div>
                <select class="form-select form-select-lg mb-4" id="leagueSelect">
                    <option selected disabled>Choose a classic league...</option>
                </select>
                
                <div class="section-header mb-3">
                    <h6 class="fw-bold text-dark m-0">Analysis Strategy</h6>
                </div>
                <div class="row g-3 mb-4">
                    <div class="col-6">
                        <input type="radio" class="btn-check" name="strategy" id="strategyShort" value="short" checked>
                        <label class="btn btn-outline-primary strategy-btn w-100 h-100 d-flex flex-column align-items-center justify-content-center text-center" for="strategyShort">
                            <i class="bi bi-lightning-charge-fill fs-3 mb-2"></i>
                            <div class="fw-bold">Short Term</div>
                            <div class="small opacity-75">Next 5 Gameweeks</div>
                        </label>
                    </div>
                    <div class="col-6">
                        <input type="radio" class="btn-check" name="strategy" id="strategyLong" value="long">
                        <label class="btn btn-outline-primary strategy-btn w-100 h-100 d-flex flex-column align-items-center justify-content-center text-center" for="strategyLong">
                            <i class="bi bi-graph-up-arrow fs-3 mb-2"></i>
                            <div class="fw-bold">Long Term</div>
                            <div class="small opacity-75">Next 6-8 Gameweeks</div>
                        </label>
                    </div>
                </div>

                <button class="btn btn-success w-100 p-3 fs-5 fw-bold rounded-3" id="analyzeBtn" style="background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); border: none;">
                    <i class="bi bi-magic me-2"></i>Find Perfect Transfers
                </button>
            </div>
        </div>

        <!-- Loading -->
        <div id="loading" class="text-center py-5 d-none">
            <div class="loading-container d-inline-block">
                <div class="pulse-loader mx-auto mb-4">
                    <i class="bi bi-search"></i>
                </div>
                <h5 class="fw-bold mb-2" id="loadingText">Scanning league...</h5>
                <p class="text-muted small mb-3">Analyzing rival teams & fixture difficulty</p>
                <div class="progress mx-auto" style="width: 250px; height: 8px; border-radius: 4px;">
                    <div class="progress-bar progress-bar-striped progress-bar-animated" id="progressBar" style="width: 0%; background: linear-gradient(90deg, var(--primary-color), #00ff85);"></div>
                </div>
            </div>
        </div>

        <!-- Results -->
        <div id="results" class="d-none fade-in-up">
            
            <!-- Summary Stats -->
            <div class="row g-3 mb-4">
                <div class="col-6 col-md-2">
                    <div class="stat-card stat-primary text-center h-100 position-relative overflow-hidden">
                        <div class="card-body p-3">
                            <i class="bi bi-people-fill stat-icon"></i>
                            <div class="small text-white-50 text-uppercase fw-bold" style="font-size: 0.65rem;">Teams Scanned</div>
                            <h3 class="fw-bold mb-0" id="teamsScanned">0</h3>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-2">
                    <div class="stat-card stat-dark text-center h-100 position-relative overflow-hidden">
                        <div class="card-body p-3">
                            <i class="bi bi-trophy-fill stat-icon"></i>
                            <div class="small text-white-50 text-uppercase fw-bold" style="font-size: 0.65rem;">Your Rank</div>
                            <h3 class="fw-bold mb-0" id="yourRank">-</h3>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-2">
                    <div class="stat-card stat-danger text-center h-100 position-relative overflow-hidden">
                        <div class="card-body p-3">
                            <i class="bi bi-arrow-down-circle-fill stat-icon"></i>
                            <div class="small text-white-50 text-uppercase fw-bold" style="font-size: 0.65rem;">Pts Behind #1</div>
                            <h3 class="fw-bold mb-0" id="pointsBehind">0</h3>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-2">
                    <div class="stat-card stat-success text-center h-100 position-relative overflow-hidden">
                        <div class="card-body p-3">
                            <i class="bi bi-arrow-left-right stat-icon"></i>
                            <div class="small text-white-50 text-uppercase fw-bold" style="font-size: 0.65rem;">Suggested Moves</div>
                            <h3 class="fw-bold mb-0" id="movesCount">0</h3>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-2">
                    <div class="stat-card stat-warning text-center h-100 position-relative overflow-hidden">
                        <div class="card-body p-3">
                            <i class="bi bi-ticket-perforated-fill stat-icon"></i>
                            <div class="small text-white-50 text-uppercase fw-bold" style="font-size: 0.65rem;">Free Transfers</div>
                            <h3 class="fw-bold mb-0" id="freeTransfersDisplay">-</h3>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-2">
                    <div class="stat-card stat-info text-center h-100 position-relative overflow-hidden">
                        <div class="card-body p-3">
                            <i class="bi bi-bank stat-icon"></i>
                            <div class="small text-white-50 text-uppercase fw-bold" style="font-size: 0.65rem;">Bank</div>
                            <h3 class="fw-bold mb-0" id="bankDisplay">-</h3>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Chip Strategy Panel (Dynamic) -->
            <div id="chipPanel" class="chip-panel d-none">
                <h6><i class="bi bi-cpu me-2"></i>AI Chip Strategy Suggestion</h6>
                <div id="chipSuggestion" class="chip-suggestion">
                    <!-- Dynamically populated -->
                </div>
            </div>

            <!-- Position Filter -->
            <div class="position-filters mb-4">
                <button class="pos-filter-btn active" data-pos="all"><i class="bi bi-grid-fill me-1"></i>All</button>
                <button class="pos-filter-btn" data-pos="1"><i class="bi bi-person-fill me-1"></i>GK</button>
                <button class="pos-filter-btn" data-pos="2"><i class="bi bi-shield-fill me-1"></i>DEF</button>
                <button class="pos-filter-btn" data-pos="3"><i class="bi bi-bullseye me-1"></i>MID</button>
                <button class="pos-filter-btn" data-pos="4"><i class="bi bi-lightning-fill me-1"></i>FWD</button>
            </div>

            <!-- Transfer Suggestions -->
            <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
                <div class="card-header bg-white py-3 d-flex align-items-center justify-content-between border-0 flex-wrap gap-2">
                    <div class="d-flex align-items-center">
                        <div class="bg-primary bg-opacity-10 p-2 rounded-3 me-3">
                            <i class="bi bi-arrow-left-right text-primary fs-5"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold mb-0">Recommended Transfers</h5>
                            <small class="text-muted">Based on League Ownership, Fixtures & xPts</small>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <span id="strategyBadge" class="badge bg-primary rounded-pill px-3 py-2"></span>
                        <span id="expectedGain" class="badge bg-success rounded-pill px-3 py-2 d-none">
                            <i class="bi bi-graph-up me-1"></i><span id="expectedGainValue">+0</span> xPts
                        </span>
                    </div>
                </div>
                <div class="card-body pt-0" id="transferList">
                    <!-- Top Pick Highlight -->
                    <div id="topPickContainer" class="mb-4 d-none"></div>

                    <div class="row g-4">
                        <div class="col-lg-6">
                            <div class="section-header d-flex justify-content-between align-items-center mb-3">
                                <h6 class="fw-bold m-0"><i class="bi bi-lightning-charge-fill text-warning me-2"></i>Catch Up (Differentials)</h6>
                            </div>
                            <p class="small text-muted mb-3">High potential players with <strong class="text-success">Good Fixtures</strong> that your rivals don't own.</p>
                            <div id="diffList"></div>
                        </div>
                        <div class="col-lg-6">
                            <div class="section-header d-flex align-items-center mb-3">
                                <h6 class="fw-bold m-0"><i class="bi bi-shield-lock-fill text-success me-2"></i>Block Rivals (Template)</h6>
                            </div>
                            <p class="small text-muted mb-3">High ownership players you're missing out on.</p>
                            <div id="blockList"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Ownership Insights -->
            <div class="card mt-4 shadow-sm border-0 rounded-4 overflow-hidden">
                <div class="card-header bg-white py-3 d-flex align-items-center border-0">
                    <div class="bg-info bg-opacity-10 p-2 rounded-3 me-3">
                        <i class="bi bi-pie-chart-fill text-info fs-5"></i>
                    </div>
                    <h5 class="fw-bold mb-0">League Ownership Insights</h5>
                </div>
                <ul class="list-group list-group-flush" id="ownershipList">
                    <!-- Injected -->
                </ul>
            </div>

        </div>
    </div>
</div>

<?php include 'footer.php';?>
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
    const step1 = document.getElementById('step1');
    const step2 = document.getElementById('step2');
    const loading = document.getElementById('loading');
    const results = document.getElementById('results');
    
    const managerIdInput = document.getElementById('managerId');
    const loadLeaguesBtn = document.getElementById('loadLeaguesBtn');
    const leagueSelect = document.getElementById('leagueSelect');
    const analyzeBtn = document.getElementById('analyzeBtn');

    let bootstrapData = null;
    let managerId = null;

    // Auto-load from URL
    const urlParams = new URLSearchParams(window.location.search);
    if(urlParams.get('manager_id')) {
        managerIdInput.value = urlParams.get('manager_id');
        // Auto click load
        setTimeout(() => loadLeaguesBtn.click(), 500);
    }

    // Step 1: Load Leagues
    loadLeaguesBtn.addEventListener('click', async () => {
        managerId = managerIdInput.value.trim();
        if(!managerId) return alert('Enter Manager ID');

        loadLeaguesBtn.disabled = true;
        loadLeaguesBtn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';

        try {
            const res = await fetch(`api.php?endpoint=entry/${managerId}/`);
            if(!res.ok) throw new Error('Manager not found');
            const data = await res.json();
            
            leagueSelect.innerHTML = '<option selected disabled>Choose a classic league...</option>';
            data.leagues.classic.forEach(l => {
                const opt = document.createElement('option');
                opt.value = l.id;
                opt.text = l.name;
                leagueSelect.appendChild(opt);
            });

            step2.classList.remove('d-none');

            // Auto Select League if in URL
            if(urlParams.get('league_id')) {
                const lid = urlParams.get('league_id');
                // check if exists
                if([...leagueSelect.options].some(o => o.value == lid)) {
                    leagueSelect.value = lid;
                    setTimeout(() => analyzeBtn.click(), 500);
                }
            }
            
        } catch (e) {
            console.error(e);
            alert('Error loading leagues: ' + e.message + '. See console for details.');
        } finally {
            loadLeaguesBtn.disabled = false;
            loadLeaguesBtn.innerHTML = 'Load Leagues';
        }
    });

    // Step 2: Analyze
    analyzeBtn.addEventListener('click', async () => {
        const leagueId = leagueSelect.value;
        if(!leagueId) return alert('Select a league');

        step1.classList.add('d-none');
        step2.classList.add('d-none');
        loading.classList.remove('d-none');
        results.classList.add('d-none');

        const updateProgress = (pct, txt) => {
            document.getElementById('progressBar').style.width = pct + '%';
            document.getElementById('loadingText').innerText = txt;
        };

        try {
            // A. Fetch Static Data
            updateProgress(10, 'Loading FPL database...');
            if(!bootstrapData) {
                const res = await fetch('api.php?endpoint=bootstrap-static/');
                bootstrapData = await res.json();
            }
            const players = {};
            bootstrapData.elements.forEach(p => players[p.id] = p);
            const currentGw = bootstrapData.events.find(e => e.is_current)?.id || 1;

            // B. Fetch ALL League Standings (with Pagination)
            updateProgress(25, 'Loading league standings...');
            
            let allManagers = [];
            let page = 1;
            let hasNextPage = true;
            const maxManagersToFetch = 1000; // Total limit for standings fetching

            while (hasNextPage && allManagers.length < maxManagersToFetch) {
                updateProgress(15 + Math.min(15, page * 1), `Loading league standings (page ${page})...`);
                const standingsRes = await fetch(`api.php?endpoint=leagues-classic/${leagueId}/standings/?page_new_entries=1&page_standings=${page}`);
                const standingsData = await standingsRes.json();
                
                if (standingsData.standings && standingsData.standings.results) {
                    allManagers = allManagers.concat(standingsData.standings.results);
                    hasNextPage = standingsData.standings.has_next;
                    page++;
                } else {
                    hasNextPage = false;
                }
                
                // If it's the first page, we can show initial rank info
                if (page === 2) { 
                    const leader = allManagers[0];
                    const myEntry = allManagers.find(m => m.entry == managerId);
                    document.getElementById('yourRank').innerText = myEntry ? `#${myEntry.rank}` : 'N/A';
                    document.getElementById('pointsBehind').innerText = myEntry ? (leader.total - myEntry.total) : '-';
                }
            }

            // Get Transfers Made for the user specifically & Bank
            updateProgress(28, 'Fetching your transfer history...');
            let myBank = 0;
            let freeTransfers = 1;
            try {
                // 1. Fetch Entry Info for Bank
                const entryRes = await fetch(`api.php?endpoint=entry/${managerId}/`);
                const entryData = await entryRes.json();
                myBank = entryData.last_deadline_bank || 0;
                document.getElementById('bankDisplay').innerText = '£' + (myBank/10).toFixed(1) + 'm';

                // 2. Fetch History for Free Transfer Calculation
                const historyRes = await fetch(`api.php?endpoint=entry/${managerId}/history/`);
                const historyData = await historyRes.json();
                
                // Calculate FTs
                // Rules: 1 FT per week, caps at 5 (new rule for 24/25). Min 1.
                // We simulate from GW 1.
                let storedFT = 1; // GW1 starts with 1? Actually usually 1. 24/25 rules might imply start with 1. 
                // Let's assume standard logic: 
                // Each GW you get +1. 
                // You spend 'event_transfers'.
                // Cap is 5.
                
                // However, detailed simulation is complex without knowing exact chip usage etc easily.
                // Simplified approach often used: 
                // Look at current 'transfers_limit' if available? No, API doesn't give 'current available FT'.
                
                // Better estimation:
                // Start with 1. 
                // For each finished GW:
                // storedFT = Math.min(5, storedFT + 1);
                // storedFT -= transfers_made_in_gameweek;
                // if (wildcard or free hit played) storedFT = 1; // Reset to 1 after chips? check rules.
                
                // Actually, let's use a simpler heuristic for now or strict simulation if we trust history.
                // Let's rely on the latest data point if possible? No.
                
                // Simulation:
                if(historyData.current && historyData.current.length > 0) {
                     storedFT = 0; // Pre-season has 0? GW1 you get 1? 
                     // Actually usually you have 1 for GW1.
                     // Let's iterate.
                     let fts = 1; 
                     for(const gw of historyData.current) {
                        // Chips?
                         const chipsUsed = historyData.chips ? historyData.chips.find(c => c.event === gw.event) : null;
                         
                         // If WC or FH used, FTs usually reset to 1 the next week.
                         if(chipsUsed && (chipsUsed.name === 'wildcard' || chipsUsed.name === 'freehit')) {
                             // effectively transfers cost 0 this week, but next week we start fresh?
                             // Rule: WC/FH resets saved FTs to 1.
                             fts = 1;
                             continue;
                         }

                         // Normal week
                         // Spend transfers
                         fts -= gw.event_transfers;
                         if(fts < 0) fts = 0; // Can't have neg, means hits were taken
                         
                         // Accrue for NEXT week
                         fts = Math.min(5, fts + 1);
                     }
                     freeTransfers = fts;
                }
                
                document.getElementById('freeTransfersDisplay').innerText = freeTransfers;

            } catch(e) { console.warn('Entry fetch fail', e); }

            // C. Analyze Managers
            updateProgress(30, 'Preparing team analysis...');
            // To be efficient, we scan the Top 50 AND the 50 closest rivals to the user
            // If the league is small, we scan everyone.
            let managersToScan = [];
            const userIdx = allManagers.findIndex(m => m.entry == managerId);
            
            if (allManagers.length <= 100) {
                managersToScan = allManagers;
            } else {
                // Top 50
                const top50 = allManagers.slice(0, 50);
                // Users around the manager
                let start = Math.max(0, userIdx - 25);
                let end = Math.min(allManagers.length, userIdx + 25);
                const rivals = allManagers.slice(start, end);
                
                // Merge and unique
                const combined = [...top50, ...rivals];
                const seen = new Set();
                managersToScan = combined.filter(m => {
                    const duplicate = seen.has(m.entry);
                    seen.add(m.entry);
                    return !duplicate;
                });
            }

            updateProgress(40, `Scanning ${managersToScan.length} teams...`);

            const playerOwnership = {};
            let scanned = 0;

            // Batch processing for picks fetching to avoid browser request limits
            const batchSize = 10;
            for (let i = 0; i < managersToScan.length; i += batchSize) {
                const batch = managersToScan.slice(i, i + batchSize);
                await Promise.all(batch.map(async (mgr) => {
                    try {
                        const picksRes = await fetch(`api.php?endpoint=entry/${mgr.entry}/event/${currentGw}/picks/`);
                        if(picksRes.ok) {
                            const picksData = await picksRes.json();
                            if(picksData && picksData.picks) {
                                picksData.picks.forEach(p => {
                                    playerOwnership[p.element] = (playerOwnership[p.element] || 0) + 1;
                                });
                                scanned++;
                            }
                        }
                    } catch(e) { console.warn('Skip manager', mgr.entry, e); }
                }));
                const progressPct = 30 + (Math.min(50, (i / managersToScan.length) * 50));
                updateProgress(progressPct, `Scanning teams (${scanned}/${managersToScan.length})...`);
                
                // Rate Limit Delay
                await new Promise(r => setTimeout(r, 500));
            }
            document.getElementById('teamsScanned').innerText = scanned;

            // D. Fetch My Picks
            updateProgress(80, 'Comparing with your team...');
            const myPicksRes = await fetch(`api.php?endpoint=entry/${managerId}/event/${currentGw}/picks/`);
            const myPicksData = await myPicksRes.json();
            const myPlayerIds = new Set(myPicksData.picks.map(p => p.element));
            const mySquad = myPicksData.picks.map(p => players[p.element]);

            // E. Identify Transfers
            updateProgress(90, 'Generating transfer plan...');

            // Future Fixtures for Difficulty
             updateProgress(85, 'Analyzing upcoming fixtures...');
             let futureFixtures = [];
             try {
                 const fixRes = await fetch('api.php?endpoint=fixtures/?future=1');
                 futureFixtures = await fixRes.json();
             } catch(e) { console.error('Fix fetch fail', e); }

             const strategy = document.querySelector('input[name="strategy"]:checked').value;
             document.getElementById('strategyBadge').innerText = strategy === 'short' ? 'Short Term (5 GW)' : 'Long Term (8 GW)';
             
             // Helper: Calculate Fixture Score for a Team
             // Higher is better.
             const getFixtureScore = (teamId) => {
                 const lookahead_gw = strategy === 'short' ? 5 : 8;
                 let score = 0;
                 let count = 0;
                 const startGw = currentGw + 1; // Next GW
                 
                 // Find fixtures for next X GWs
                 for(let i = 0; i < lookahead_gw; i++) {
                     const gw = startGw + i;
                     if(gw > 38) break;
                     const fixtures = futureFixtures.filter(f => f.event === gw && (f.team_h === teamId || f.team_a === teamId));
                     
                     if(fixtures.length === 0) {
                         // Blank Gameweek? Penalty.
                         score -= 2; 
                     } else {
                         fixtures.forEach(f => {
                             const difficulty = f.team_h === teamId ? f.team_h_difficulty : f.team_a_difficulty;
                             // FDR: 2 (Easy) to 5 (Hard)
                             // We want high score for easy. 
                             // 2 -> +3, 3 -> +1, 4 -> -1, 5 -> -3? 
                             // Let's use simpler: Score += (6 - difficulty)
                             score += (6 - difficulty);
                             count++;
                         });
                     }
                 }
                 return score; 
             };
            
            // Re-Evaluate scores with Fixtures
            // Helper to get score
            const getSmartScore = (p, baseOwnershipScore) => {
                 const fixScore = getFixtureScore(p.team);
                 // Normalize fix score roughly? 
                 // 5 games: max score approx 5*4=20.
                 // ownership factor is 0 to 1. 
                 // form is 0 to 10+. 
                 
                 // We want to boost players with good fixtures.
                 // Multiplier: 1.0 to 1.5? 
                 // Let's just add it as a weighted factor. 
                 
                 const form = parseFloat(p.form) || 0;
                 
                 // Combined Metric
                 // Base: Form
                 // Multiplier: Fixture Difficulty (1.0 = neutral 3 diff, >1.0 good, <1.0 bad)
                 
                 // Average difficulty is 3. (6-3)=3 per game. 
                 // If 5 games, score 15 is neutral.
                 // Factor = Score / (Games * 3)
                 
                 const games = strategy === 'short' ? 5 : 8;
                 const neutralScore = games * 3; 
                 const fixFactor = Math.max(0.5, fixScore / neutralScore); // Cap low at 0.5 to punish hard runs
                 
                 return (baseOwnershipScore + (form * 0.2)) * fixFactor;
            };

            // My players with low ownership/form (potential sells) - defined before filtering buys

            // My players with low ownership/form (potential sells)
            // We need 'toSell' derived BEFORE we filter buys to check affordability
            const lowOwnershipMine = mySquad
                .filter(p => {
                    const own = playerOwnership[p.id] || 0;
                    const fixScore = getFixtureScore(p.team);
                    const games = strategy === 'short' ? 5 : 8;
                    const neutralScore = games * 3;
                    
                    // Sell if:
                    // 1. Injured/Suspended (status != a)
                    // 2. Low Ownership AND (Poor form OR Bad Fixtures)
                    const isPoorAsset = (parseFloat(p.form) < 3.0) || (fixScore < neutralScore * 0.8);
                    
                    return (p.status !== 'a') || ((own / scanned) < 0.25 && isPoorAsset);
                })
                .sort((a, b) => {
                    if (a.status !== 'a') return -1;
                    if (b.status !== 'a') return 1;
                    const scoreA = parseFloat(a.form) * getFixtureScore(a.team);
                    const scoreB = parseFloat(b.form) * getFixtureScore(b.team);
                    return scoreA - scoreB; 
                });

            // STRICT AFFORDABILITY CHECK
            // We look at ALL players in that position for affordability, not just 'marked to sell' ones.
            // This ensures we don't hide good buys just because we didn't flag a "bad" player.
            
            const canAfford = (buyPrice, position) => {
                // All players in my squad in this position
                const potentialSells = mySquad.filter(s => s.element_type === position);
                if(potentialSells.length === 0) return false; // Should not happen if full squad
                
                // Max budget available = Bank + Max(SellPrice of ANY player in that pos)
                const maxSellPrice = Math.max(...potentialSells.map(s => s.now_cost));
                return (myBank + maxSellPrice) >= buyPrice;
            };

            // Template players I'm missing (high ownership)
            const missingHighOwnership = Object.entries(playerOwnership)
                .filter(([id, count]) => !myPlayerIds.has(parseInt(id)) && (count / scanned) >= 0.25)
                .map(([id, count]) => {
                    const p = players[id];
                    const ownPct = count / scanned;
                    const smartScore = getFixtureScore(p.team) * parseFloat(p.form) * ownPct; 
                    return { 
                        player: p, 
                        ownership: ownPct,
                        score: smartScore,
                        fixScore: getFixtureScore(p.team)
                    };
                })
                .filter(item => canAfford(item.player.now_cost, item.player.element_type)) // FILTER BY BUDGET
                .sort((a, b) => b.score - a.score)
                .slice(0, 10);
            
            // Differentials
            const potentialDifferentials = Object.values(players)
                .filter(p => 
                    p.status === 'a' &&
                    parseFloat(p.form) > 3.0 && 
                    !myPlayerIds.has(p.id) &&
                    ((playerOwnership[p.id] || 0) / scanned) < 0.15 
                )
                .map(p => {
                     const ownPct = (playerOwnership[p.id] || 0) / scanned;
                     const fixScore = getFixtureScore(p.team);
                     const games = strategy === 'short' ? 5 : 8;
                     const neutralScore = games * 3; 
                     
                     if(fixScore < neutralScore) return { ...p, diffScore: -1 };

                     const diffScore = parseFloat(p.form) * (fixScore / neutralScore) * (1 - ownPct);
                     return {
                        ...p,
                        diffScore: diffScore,
                        fixScore: fixScore
                     };
                })
                .filter(p => p.diffScore > 0)
                .filter(p => canAfford(p.now_cost, p.element_type)) // FILTER BY BUDGET
                .sort((a,b) => b.diffScore - a.diffScore)
                .slice(0, 10);

            // F. Render Transfers
            updateProgress(100, 'Done!');
            
            // Map team ID to Team Object for easy access in render
            const teamMap = {};
            bootstrapData.teams.forEach(t => teamMap[t.id] = t);
            
            // Store globally for position filtering
            window.aiData = {
                blockers: missingHighOwnership,
                differentials: potentialDifferentials,
                toSell: lowOwnershipMine,
                total: scanned,
                ownership: playerOwnership,
                players: players,
                bank: myBank,
                teamMap: teamMap,
                mySquad: mySquad,
                futureFixtures: futureFixtures,
                currentGw: currentGw,
                strategy: strategy,
                freeTransfers: freeTransfers
            };
            
            // Check for Chip Strategy
            checkChipStrategy(lowOwnershipMine, freeTransfers, strategy, futureFixtures, currentGw);
            
            renderTransfers(missingHighOwnership, potentialDifferentials, lowOwnershipMine, scanned, playerOwnership, players, myBank, teamMap, mySquad, futureFixtures, currentGw, strategy);

            loading.classList.add('d-none');
            results.classList.remove('d-none');
            
            // Setup Position Filter Listeners
            setupPositionFilters();

        } catch (e) {
            console.error(e);
            alert('Error: ' + e.message);
            loading.classList.add('d-none');
            step1.classList.remove('d-none');
        }
    });

    // Position Filter Logic
    function setupPositionFilters() {
        document.querySelectorAll('.pos-filter-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                document.querySelectorAll('.pos-filter-btn').forEach(b => b.classList.remove('active'));
                btn.classList.add('active');
                
                const pos = btn.dataset.pos;
                filterTransfersByPosition(pos);
            });
        });
    }

    function filterTransfersByPosition(pos) {
        document.querySelectorAll('.transfer-card').forEach(card => {
            if(pos === 'all') {
                card.style.display = 'block';
            } else {
                const cardPos = card.dataset.position;
                card.style.display = (cardPos == pos) ? 'block' : 'none';
            }
        });
    }

    // Chip Strategy Check
    function checkChipStrategy(toSell, freeTransfers, strategy, futureFixtures, currentGw) {
        const chipPanel = document.getElementById('chipPanel');
        const chipSuggestion = document.getElementById('chipSuggestion');
        
        // Count issues
        const injuredCount = toSell.filter(p => p.status !== 'a').length;
        const poorAssets = toSell.length;
        
        let showChip = false;
        let chipHtml = '';
        
        // Wildcard suggestion: Many poor assets
        if(poorAssets >= 5 && freeTransfers <= 2) {
            showChip = true;
            chipHtml = `
                <div class="chip-icon chip-wc"><i class="bi bi-arrow-repeat"></i></div>
                <div>
                    <div class="fw-bold">Consider Using Wildcard</div>
                    <div class="small opacity-75">${poorAssets} players flagged for potential removal. A Wildcard could restructure your squad efficiently.</div>
                </div>
            `;
        }
        // Free Hit: Too many blanks/DGWs coming
        else if(injuredCount >= 3) {
            showChip = true;
            chipHtml = `
                <div class="chip-icon chip-fh"><i class="bi bi-lightning"></i></div>
                <div>
                    <div class="fw-bold">Free Hit Could Help</div>
                    <div class="small opacity-75">${injuredCount} injured/doubtful players in your squad. Consider a Free Hit for the next GW.</div>
                </div>
            `;
        }
        // Bench Boost: Good fixtures across squad
        else if(freeTransfers >= 2 && poorAssets <= 1) {
            showChip = true;
            chipHtml = `
                <div class="chip-icon chip-bb"><i class="bi bi-people"></i></div>
                <div>
                    <div class="fw-bold">Bench Boost Opportunity</div>
                    <div class="small opacity-75">Your squad looks strong! If bench has good fixtures, consider Bench Boost.</div>
                </div>
            `;
        }
        
        if(showChip && chipPanel && chipSuggestion) {
            chipSuggestion.innerHTML = chipHtml;
            chipPanel.classList.remove('d-none');
        } else if(chipPanel) {
            chipPanel.classList.add('d-none');
        }
    }

    // Get Fixture Ticker HTML for a team
    function getFixtureTicker(teamId, futureFixtures, currentGw, teamMap, strategy) {
        const lookahead = strategy === 'short' ? 5 : 8;
        let html = '<div class="fixture-ticker">';
        
        for(let i = 0; i < Math.min(lookahead, 5); i++) { // Show max 5 dots
            const gw = currentGw + 1 + i;
            if(gw > 38) break;
            
            const fixtures = futureFixtures.filter(f => f.event === gw && (f.team_h === teamId || f.team_a === teamId));
            
            if(fixtures.length === 0) {
                html += '<div class="fixture-dot fdr-blank" title="GW' + gw + ': Blank">-</div>';
            } else if(fixtures.length > 1) {
                // DGW
                html += '<div class="fixture-dot fdr-1" title="GW' + gw + ': DGW">2</div>';
            } else {
                const fix = fixtures[0];
                const isHome = fix.team_h === teamId;
                const opponent = teamMap[isHome ? fix.team_a : fix.team_h];
                const difficulty = isHome ? fix.team_h_difficulty : fix.team_a_difficulty;
                const shortName = opponent?.short_name || '?';
                const homeAway = isHome ? 'H' : 'a';
                
                html += `<div class="fixture-dot fdr-${difficulty}" title="GW${gw}: ${shortName} (${homeAway})">${shortName.substring(0,3)}</div>`;
            }
        }
        
        html += '</div>';
        return html;
    }

    // Calculate Expected Points
    function calculateExpectedPoints(player, fixScore, strategy) {
        const form = parseFloat(player.form) || 0;
        const ppg = parseFloat(player.points_per_game) || 0;
        const games = strategy === 'short' ? 5 : 8;
        
        // Simple xPts model: (form * 0.6 + ppg * 0.4) * weeks * fixture factor
        const baseXpts = (form * 0.6 + ppg * 0.4);
        const neutralScore = games * 3;
        const fixFactor = Math.max(0.7, Math.min(1.3, fixScore / neutralScore));
        
        return (baseXpts * games * fixFactor).toFixed(1);
    }

    // Calculate Confidence Score
    function calculateConfidence(player, ownership, fixScore, strategy) {
        const form = parseFloat(player.form) || 0;
        const games = strategy === 'short' ? 5 : 8;
        const neutralScore = games * 3;
        
        // Factors: Form (40%), Fixtures (30%), Ownership relevance (30%)
        const formScore = Math.min(10, form) / 10 * 40;
        const fixScoreNorm = Math.min(1.5, Math.max(0.5, fixScore / neutralScore));
        const fixtureScore = ((fixScoreNorm - 0.5) / 1) * 30;
        const ownershipScore = Math.min(ownership, 0.5) / 0.5 * 30;
        
        return Math.round(formScore + fixtureScore + ownershipScore);
    }

    function renderTransfers(blockers, differentials, toSell, total, ownership, players, bank, teamMap, mySquad, futureFixtures, currentGw, strategy) {
        // 1. Render Transfers
        const diffList = document.getElementById('diffList');
        const blockList = document.getElementById('blockList');
        if(diffList) diffList.innerHTML = '';
        if(blockList) blockList.innerHTML = '';

        let totalMoves = 0;
        let totalExpectedGain = 0;

        // Helper to find best sell candidate for a buy target
        const findSellFor = (buyPlayer) => {
            // Priority 1: Check 'toSell' (marked bad assets)
            let candidates = toSell.filter(s => s.element_type === buyPlayer.element_type);
            
            // Priority 2: If no bad assets, check ALL players in that pos (sorted by lowest form/fix)
            if(candidates.length === 0) {
                 // Sort mySquad by some metric (lowest form * fixScore)
                 // We need to re-calculate score here or just use form
                 candidates = mySquad.filter(s => s.element_type === buyPlayer.element_type)
                    .sort((a,b) => parseFloat(a.form) - parseFloat(b.form));
            }
            
            // Try to find one where money works
            const moneyCandidates = candidates.filter(s => (s.now_cost + bank) >= buyPlayer.now_cost);
            
            if(moneyCandidates.length > 0) return moneyCandidates[0]; // First one (worst)
            
            // If no one directly affords, return the absolute worst (might need 2 transfers to afford)
            return candidates[0] || null;
        };
        
        // Helper to get fixture score for a player
        const getPlayerFixScore = (teamId) => {
            const lookahead_gw = strategy === 'short' ? 5 : 8;
            let score = 0;
            const startGw = currentGw + 1;
            
            for(let i = 0; i < lookahead_gw; i++) {
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

        // Render Blockers
        if(blockers.length === 0 && blockList) blockList.innerHTML = '<p class="text-muted small">You own all the key template players!</p>';
        blockers.forEach((item, idx) => {
            if(blockList) {
                const p = item.player;
                const sellP = findSellFor(p);
                const team = teamMap[p.team];
                const fixScore = getPlayerFixScore(p.team);
                const xPts = calculateExpectedPoints(p, fixScore, strategy);
                const confidence = calculateConfidence(p, item.ownership, fixScore, strategy);
                const priority = idx < 2 ? 1 : (idx < 5 ? 2 : 3);
                
                const card = createTransferCard(p, item.ownership, 'block', sellP, bank, team, futureFixtures, currentGw, teamMap, strategy, xPts, confidence, priority);
                blockList.appendChild(card);
                
                // Calculate gain (simplified: xPts of new - form*games of old)
                if(sellP && idx < 3) {
                    const oldXpts = parseFloat(sellP.form || 0) * (strategy === 'short' ? 5 : 8);
                    totalExpectedGain += (parseFloat(xPts) - oldXpts);
                }
            }
            totalMoves++;
        });

        // Render Differentials
        if(differentials.length === 0 && diffList) diffList.innerHTML = '<p class="text-muted small">No hidden gems with good fixtures found.</p>';
        differentials.forEach((p, idx) => {
             const ownCount = ownership[p.id] || 0;
             if(diffList) {
                const sellP = findSellFor(p);
                const team = teamMap[p.team];
                const fixScore = p.fixScore || getPlayerFixScore(p.team);
                const xPts = calculateExpectedPoints(p, fixScore, strategy);
                const confidence = calculateConfidence(p, ownCount/total, fixScore, strategy);
                const priority = idx < 2 ? 1 : (idx < 5 ? 2 : 3);
                
                const card = createTransferCard(p, ownCount/total, 'diff', sellP, bank, team, futureFixtures, currentGw, teamMap, strategy, xPts, confidence, priority);
                diffList.appendChild(card);
                
                if(sellP && idx < 3) {
                    const oldXpts = parseFloat(sellP.form || 0) * (strategy === 'short' ? 5 : 8);
                    totalExpectedGain += (parseFloat(xPts) - oldXpts);
                }
             }
             totalMoves++;
        });

        // 1.5 Render "Perfect Transfer" (Top Pick)
        // Find the absolute highest score from both lists
        const allMoves = [...blockers.map(b => ({...b, type: 'block'})), ...differentials.map(d => ({...d, type: 'diff', score: d.diffScore}))];
        allMoves.sort((a,b) => b.score - a.score);
        
        const topPickContainer = document.getElementById('topPickContainer');
        if(topPickContainer) {
            if(allMoves.length > 0) {
                const top = allMoves[0];
                const sellP = findSellFor(top.player);
                const team = teamMap[top.player.team];
                const fixScore = getPlayerFixScore(top.player.team);
                const xPts = calculateExpectedPoints(top.player, fixScore, strategy);
                topPickContainer.innerHTML = createTopPickCard(top.player, top.ownership, top.type, sellP, bank, team, futureFixtures, currentGw, teamMap, strategy, xPts);
                topPickContainer.classList.remove('d-none');
            } else {
                topPickContainer.classList.add('d-none');
            }
        }

        const movesCountEl = document.getElementById('movesCount');
        if(movesCountEl) movesCountEl.innerText = totalMoves;
        
        // Show expected gain
        const expectedGainEl = document.getElementById('expectedGain');
        const expectedGainValueEl = document.getElementById('expectedGainValue');
        if(expectedGainEl && expectedGainValueEl && totalExpectedGain > 0) {
            expectedGainValueEl.innerText = '+' + totalExpectedGain.toFixed(1);
            expectedGainEl.classList.remove('d-none');
        }

        // 2. Ownership Insights
        const ownershipList = document.getElementById('ownershipList');
        if(ownershipList) {
            ownershipList.innerHTML = '';

            // Top 10 most owned
            const topOwned = Object.entries(ownership)
                .sort((a, b) => b[1] - a[1])
                .slice(0, 10);

            topOwned.forEach(([id, count]) => {
                const p = players[id]; 
                if(!p) return;
                const pct = Math.round(count / total * 100);
                const li = document.createElement('li');
                li.className = 'list-group-item d-flex justify-content-between align-items-center';
                const posName = ['','GK','DEF','MID','FWD'][p.element_type] || '';
                li.innerHTML = `
                    <span><strong>${p.web_name}</strong> <span class="text-muted small">(${posName})</span></span>
                    <span class="badge bg-primary">${pct}%</span>
                `;
                ownershipList.appendChild(li);
            });
        }
    }

    function getTeamLogo(teamName) {
        if(!teamName) return null;
        const name = teamName.toLowerCase();
        const map = {
            'arsenal': 'arsenal.svg',
            'aston villa': 'aston villa.svg',
            'bournemouth': 'boumemouth.svg', // file typo conserved
            'brentford': 'brentford.svg',
            'brighton': 'brighton.svg',
            'burnley': 'burnley.svg',
            'chelsea': 'chelsea.svg',
            'crystal palace': 'crystal palace.svg',
            'everton': 'everton.svg',
            'fulham': 'fulham.svg',
            'liverpool': 'liverpool.svg',
            'man city': 'man city.svg',
            'man utd': 'man utd.svg',
            'newcastle': null, 
            "nott'm forest": 'forest.svg',
            'sheffield utd': null, 
            'spurs': 'spurs.svg',
            'tottenham': 'spurs.svg',
            'luton': null, 
            'west ham': 'west ham.svg',
            'wolves': 'wolves.svg',
            'leicester': null,
            'southampton': null,
            'ipswich': null
        };
        return map[name] ? 'f_logo/' + map[name] : null;
    }

    function createTransferCard(p, ownPct, type, sellPlayer, bank, team, futureFixtures, currentGw, teamMap, strategy, xPts, confidence, priority) {
        const div = document.createElement('div');
        div.className = `transfer-card card mb-3 ${type === 'diff' ? 'diff-card' : 'block-card'}`;
        div.dataset.position = p.element_type; // For position filtering
        
        const badgeColor = type === 'diff' ? 'bg-warning text-dark' : 'bg-success';
        const badgeText = type === 'diff' ? 'DIFF' : 'BLOCK';
        const priorityText = priority === 1 ? '🔥 Priority' : (priority === 2 ? '⚡ Consider' : '');
        const priorityClass = `priority-${priority}`;
        
        const logoPath = getTeamLogo(team?.name);
        const logoHtml = logoPath 
            ? `<img src="${logoPath}" alt="${team?.name}" style="height: 28px; width: 28px; object-fit: contain;" class="me-2">`
            : `<span class="badge bg-light text-dark border me-2">${team?.short_name || ''}</span>`;
        
        // Fixture Ticker
        const fixtureTicker = getFixtureTicker(p.team, futureFixtures, currentGw, teamMap, strategy);
        
        // Confidence meter
        const confClass = confidence >= 70 ? 'confidence-high' : (confidence >= 40 ? 'confidence-med' : 'confidence-low');
        const confidenceHtml = `
            <div class="confidence-meter" title="AI Confidence: ${confidence}%">
                <span class="small text-muted">AI</span>
                <div class="confidence-bar">
                    <div class="confidence-fill ${confClass}" style="width: ${confidence}%"></div>
                </div>
                <span class="small fw-bold">${confidence}%</span>
            </div>
        `;
        
        // Position name
        const posName = ['','GK','DEF','MID','FWD'][p.element_type] || '';
        
        let sellHtml = '';
        if(sellPlayer) {
            const needed = (p.now_cost - (sellPlayer.now_cost+bank));
            sellHtml = `
                <div class="border-top pt-2 mt-2">
                    <div class="d-flex justify-content-between align-items-center small">
                         <span class="text-danger fw-bold"><i class="bi bi-arrow-right-circle me-1"></i>Out: ${sellPlayer.web_name}</span>
                         <span class="text-muted">£${(sellPlayer.now_cost/10).toFixed(1)}m</span>
                    </div>
                     ${needed > 0 ? '<div class="text-danger fw-bold text-end" style="font-size:0.7rem">Need £' + (needed/10).toFixed(1) + 'm more</div>' : '<div class="text-success fw-bold text-end" style="font-size:0.7rem"><i class="bi bi-check-circle me-1"></i>Affordable</div>'}
                </div>
            `;
        } else {
             sellHtml = `
                <div class="border-top pt-2 mt-2">
                    <div class="small text-muted text-center fst-italic">No obvious sell candidate</div>
                </div>
            `;
        }

        div.innerHTML = `
            ${priority <= 2 ? `<div class="priority-badge ${priorityClass}">${priorityText}</div>` : ''}
            <div class="card-body p-3">
                <div class="d-flex align-items-start justify-content-between mb-2">
                    <div class="d-flex align-items-center">
                         ${logoHtml}
                         <div>
                            <div class="fw-bold text-dark fs-6">${p.web_name}</div>
                            <div class="d-flex align-items-center gap-2 flex-wrap mt-1">
                                <span class="badge ${badgeColor}" style="font-size:0.6rem;">${badgeText}</span>
                                <span class="badge bg-secondary" style="font-size:0.6rem;">${posName}</span>
                                <span class="xp-badge"><i class="bi bi-graph-up"></i>${xPts} xPts</span>
                            </div>
                         </div>
                    </div>
                    <div class="text-end" style="min-width: 65px;">
                        <div class="fw-bold small">${Math.round(ownPct*100)}%</div>
                        <div class="progress mb-1" style="height: 4px; width: 60px">
                            <div class="progress-bar ${type==='diff'?'bg-warning':'bg-success'}" role="progressbar" style="width: ${Math.round(ownPct*100)}%"></div>
                        </div>
                        <div class="text-muted" style="font-size: 0.6rem">Owned</div>
                    </div>
                </div>
                
                <div class="player-stats mb-2">
                    <span class="mini-stat"><i class="bi bi-fire text-danger me-1"></i>Form ${p.form}</span>
                    <span class="mini-stat"><i class="bi bi-currency-pound text-success me-1"></i>${(p.now_cost/10).toFixed(1)}m</span>
                    <span class="mini-stat"><i class="bi bi-bullseye text-primary me-1"></i>${p.total_points} pts</span>
                </div>
                
                <div class="mb-2">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="small text-muted fw-bold">Fixtures</span>
                        ${confidenceHtml}
                    </div>
                    ${fixtureTicker}
                </div>
                
                ${sellHtml}
            </div>
        `;
        return div;
    }

    function createTopPickCard(p, ownPct, type, sellPlayer, bank, team, futureFixtures, currentGw, teamMap, strategy, xPts) {
        // Star / "Perfect Transfer" Styling
        const isDiff = type === 'diff';
        const badgeColor = isDiff ? 'bg-warning text-dark' : 'bg-success';
        const badgeText = isDiff ? 'TOP DIFFERENTIAL' : 'TOP BLOCKER';
        
        const logoPath = getTeamLogo(team?.name);
        const logoHtml = logoPath 
            ? `<img src="${logoPath}" alt="${team?.name}" style="height: 80px; auto; object-fit: contain; opacity:0.15; position: absolute; right: -10px; bottom: -10px;">`
            : '';
            
        const logoHeader = logoPath
            ? `<img src="${logoPath}" alt="${team?.name}" style="height: 24px; width: 24px; object-fit: contain;" class="me-2">`
            : `<span>${team?.short_name}</span> `;
        
        // Fixture Ticker
        const fixtureTicker = getFixtureTicker(p.team, futureFixtures, currentGw, teamMap, strategy);
        
        let sellHtml = '';
        if(sellPlayer) {
            const needed = (p.now_cost - (sellPlayer.now_cost+bank));
            sellHtml = `
                <div class="mt-3 p-2 rounded bg-light border border-dashed">
                    <div class="d-flex justify-content-between align-items-center">
                         <span class="text-danger fw-bold"><i class="bi bi-arrow-right-circle me-2"></i>Out: ${sellPlayer.web_name}</span>
                         <span class="text-muted small">£${(sellPlayer.now_cost/10).toFixed(1)}m</span>
                    </div>
                     ${needed > 0 ? '<div class="text-danger fw-bold text-end mt-1" style="font-size:0.8rem">Short by £' + (needed/10).toFixed(1) + 'm</div>' : '<div class="text-success fw-bold text-end mt-1" style="font-size:0.8rem"><i class="bi bi-check-circle me-1"></i>Affordable</div>'}
                </div>
            `;
        }

        return `
            <div class="card border-0 shadow-lg" style="background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%); border-left: 5px solid #ffc107 !important;">
                <div class="card-body position-relative overflow-hidden text-white">
                    <div class="position-absolute top-0 end-0 p-3">
                        <i class="bi bi-star-fill text-warning display-3 opacity-25"></i>
                    </div>
                    ${logoHtml}
                    
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <span class="badge bg-warning text-dark"><i class="bi bi-cpu me-1"></i>AI PICK</span>
                        <span class="badge ${badgeColor}">${badgeText}</span>
                    </div>
                    
                    <div class="row align-items-center">
                        <div class="col-md-7">
                             <div class="d-flex align-items-center mb-2">
                                ${logoHeader} <span class="small opacity-75">${team?.name}</span>
                             </div>
                             <h2 class="display-5 fw-bold mb-2">${p.web_name}</h2>
                             <div class="d-flex align-items-center gap-2 flex-wrap mb-3">
                                <span class="xp-badge" style="font-size: 0.85rem; padding: 6px 14px;"><i class="bi bi-graph-up me-1"></i>${xPts} xPts</span>
                                <span class="badge bg-white text-dark border">Form ${p.form}</span>
                                <span class="badge bg-white text-dark border">${p.total_points} pts</span>
                             </div>
                             <div class="mb-2">
                                <span class="small opacity-75 d-block mb-1">Upcoming Fixtures</span>
                                ${fixtureTicker}
                             </div>
                        </div>
                        <div class="col-md-5 text-md-end mt-3 mt-md-0">
                             <div class="display-4 fw-bold text-warning">£${(p.now_cost/10).toFixed(1)}m</div>
                             <div class="opacity-75 small">Price</div>
                        </div>
                    </div>
                    ${sellHtml}
                </div>
            </div>
        `;
    }
</script>
</body>
</html>
