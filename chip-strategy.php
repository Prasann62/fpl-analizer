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
    <title>Chip Strategy & Polls | FPL Manager</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="style.css?v=<?php echo time(); ?>" rel="stylesheet">
    <style>
        .hero-header {
            background: linear-gradient(135deg, rgba(139, 92, 246, 0.95) 0%, rgba(109, 40, 217, 0.95) 100%);
            border-radius: 1rem;
            padding: 2rem;
            color: white;
        }
        .chip-card {
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
            border-radius: 20px;
            border: none;
            overflow: hidden;
            transition: all 0.3s ease;
            position: relative;
        }
        .chip-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.3);
        }
        .chip-card.wildcard { border-top: 4px solid #8b5cf6; }
        .chip-card.freehit { border-top: 4px solid #06b6d4; }
        .chip-card.triple-captain { border-top: 4px solid #f59e0b; }
        .chip-card.bench-boost { border-top: 4px solid #10b981; }
        .chip-icon {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }
        .chip-icon.wildcard { background: linear-gradient(135deg, #8b5cf6 0%, #6d28d9 100%); }
        .chip-icon.freehit { background: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%); }
        .chip-icon.triple-captain { background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); }
        .chip-icon.bench-boost { background: linear-gradient(135deg, #10b981 0%, #059669 100%); }
        .gw-indicator {
            background: rgba(255,255,255,0.1);
            border-radius: 20px;
            padding: 8px 15px;
            font-size: 13px;
        }
        .gw-indicator.optimal { background: rgba(16, 185, 129, 0.3); border: 1px solid rgba(16, 185, 129, 0.5); }
        .gw-indicator.risky { background: rgba(245, 158, 11, 0.3); border: 1px solid rgba(245, 158, 11, 0.5); }
        .gw-indicator.avoid { background: rgba(239, 68, 68, 0.3); border: 1px solid rgba(239, 68, 68, 0.5); }
        .poll-card {
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 16px;
        }
        .poll-option {
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 12px;
            padding: 15px;
            margin-bottom: 10px;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .poll-option:hover {
            background: rgba(255,255,255,0.1);
            border-color: rgba(255,255,255,0.2);
        }
        .poll-option.selected {
            background: rgba(59, 130, 246, 0.2);
            border-color: #3b82f6;
        }
        .poll-bar {
            height: 30px;
            background: #374151;
            border-radius: 8px;
            overflow: hidden;
            position: relative;
        }
        .poll-bar-fill {
            height: 100%;
            transition: width 0.5s ease;
            display: flex;
            align-items: center;
            padding-left: 10px;
            font-weight: bold;
            font-size: 12px;
            color: white;
        }
        .dgw-badge {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            color: #000;
            padding: 3px 10px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: bold;
        }
        .fixture-row {
            background: rgba(255,255,255,0.05);
            border-radius: 8px;
            padding: 10px;
            margin-bottom: 8px;
        }
        .confidence-meter {
            height: 8px;
            border-radius: 4px;
            background: #374151;
            overflow: hidden;
        }
        .confidence-fill {
            height: 100%;
            border-radius: 4px;
        }
        .section-divider {
            height: 1px;
            background: linear-gradient(90deg, transparent 0%, rgba(255,255,255,0.2) 50%, transparent 100%);
            margin: 2rem 0;
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
                    <h1 class="display-5 fw-bold mb-2"><i class="bi bi-lightning-charge me-2"></i>Chip Strategy & Polls</h1>
                    <p class="lead mb-0 opacity-75">AI-powered chip timing recommendations and community polls for gameweek decisions.</p>
                </div>
                <div class="col-md-4 text-end d-none d-md-block">
                    <i class="bi bi-joystick display-1 opacity-25"></i>
                </div>
            </div>
        </div>

        <!-- Chip Strategy Section -->
        <h4 class="text-white mb-3"><i class="bi bi-cpu me-2"></i>AI Chip Recommendations</h4>
        <p class="text-white-50 mb-4">Based on fixture analysis and upcoming double/blank gameweeks.</p>

        <div class="row g-4 mb-5">
            <!-- Wildcard -->
            <div class="col-md-6 col-lg-3">
                <div class="card chip-card wildcard h-100">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div class="chip-icon wildcard">🃏</div>
                            <span class="gw-indicator optimal">
                                <i class="bi bi-check-circle me-1"></i>GW24
                            </span>
                        </div>
                        <h5 class="text-white fw-bold mb-1">Wildcard</h5>
                        <p class="text-white-50 small mb-3">Full squad overhaul</p>
                        
                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-1">
                                <small class="text-white-50">Confidence</small>
                                <small class="text-success">85%</small>
                            </div>
                            <div class="confidence-meter">
                                <div class="confidence-fill bg-success" style="width: 85%;"></div>
                            </div>
                        </div>

                        <div class="small text-white-50">
                            <i class="bi bi-info-circle me-1"></i>
                            DGW24 offers optimal fixture swing. 8 teams double.
                        </div>
                    </div>
                </div>
            </div>

            <!-- Free Hit -->
            <div class="col-md-6 col-lg-3">
                <div class="card chip-card freehit h-100">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div class="chip-icon freehit">🎯</div>
                            <span class="gw-indicator">
                                <i class="bi bi-clock me-1"></i>GW29
                            </span>
                        </div>
                        <h5 class="text-white fw-bold mb-1">Free Hit</h5>
                        <p class="text-white-50 small mb-3">One-week squad</p>
                        
                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-1">
                                <small class="text-white-50">Confidence</small>
                                <small class="text-warning">70%</small>
                            </div>
                            <div class="confidence-meter">
                                <div class="confidence-fill bg-warning" style="width: 70%;"></div>
                            </div>
                        </div>

                        <div class="small text-white-50">
                            <i class="bi bi-info-circle me-1"></i>
                            FA Cup blank expected. Many teams without fixtures.
                        </div>
                    </div>
                </div>
            </div>

            <!-- Triple Captain -->
            <div class="col-md-6 col-lg-3">
                <div class="card chip-card triple-captain h-100">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div class="chip-icon triple-captain">👑</div>
                            <span class="gw-indicator optimal">
                                <i class="bi bi-check-circle me-1"></i>DGW
                            </span>
                        </div>
                        <h5 class="text-white fw-bold mb-1">Triple Captain</h5>
                        <p class="text-white-50 small mb-3">3x captain points</p>
                        
                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-1">
                                <small class="text-white-50">Confidence</small>
                                <small class="text-success">90%</small>
                            </div>
                            <div class="confidence-meter">
                                <div class="confidence-fill bg-success" style="width: 90%;"></div>
                            </div>
                        </div>

                        <div class="small text-white-50">
                            <i class="bi bi-info-circle me-1"></i>
                            Save for Haaland/Salah double with easy fixtures.
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bench Boost -->
            <div class="col-md-6 col-lg-3">
                <div class="card chip-card bench-boost h-100">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div class="chip-icon bench-boost">📈</div>
                            <span class="gw-indicator optimal">
                                <i class="bi bi-check-circle me-1"></i>DGW
                            </span>
                        </div>
                        <h5 class="text-white fw-bold mb-1">Bench Boost</h5>
                        <p class="text-white-50 small mb-3">All 15 players score</p>
                        
                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-1">
                                <small class="text-white-50">Confidence</small>
                                <small class="text-success">88%</small>
                            </div>
                            <div class="confidence-meter">
                                <div class="confidence-fill bg-success" style="width: 88%;"></div>
                            </div>
                        </div>

                        <div class="small text-white-50">
                            <i class="bi bi-info-circle me-1"></i>
                            Best after wildcard when bench is strong.
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Upcoming DGW/BGW -->
        <div class="card poll-card mb-5">
            <div class="card-header bg-transparent border-0 pt-4 px-4">
                <h5 class="text-white mb-0"><i class="bi bi-calendar-event me-2"></i>Upcoming Special Gameweeks</h5>
            </div>
            <div class="card-body px-4 pb-4">
                <div class="row g-3">
                    <div class="col-md-4">
                        <div class="fixture-row">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <span class="dgw-badge me-2">DGW</span>
                                    <strong class="text-white">Gameweek 24</strong>
                                </div>
                                <span class="text-white-50">~4 weeks</span>
                            </div>
                            <p class="text-white-50 small mb-0 mt-2">8 teams expected to double. Arsenal, Liverpool, Chelsea likely.</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="fixture-row">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <span class="badge bg-secondary me-2">BGW</span>
                                    <strong class="text-white">Gameweek 29</strong>
                                </div>
                                <span class="text-white-50">~9 weeks</span>
                            </div>
                            <p class="text-white-50 small mb-0 mt-2">FA Cup weekend. 6+ teams expected to blank.</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="fixture-row">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <span class="dgw-badge me-2">DGW</span>
                                    <strong class="text-white">Gameweek 37</strong>
                                </div>
                                <span class="text-white-50">~17 weeks</span>
                            </div>
                            <p class="text-white-50 small mb-0 mt-2">Rescheduled fixtures. Potential massive double.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="section-divider"></div>

        <!-- Community Polls -->
        <h4 class="text-white mb-3"><i class="bi bi-people me-2"></i>Community Polls</h4>
        <p class="text-white-50 mb-4">See what other FPL managers are planning this gameweek.</p>

        <div class="row g-4">
            <!-- Poll 1: Captain -->
            <div class="col-lg-6">
                <div class="card poll-card h-100">
                    <div class="card-header bg-transparent border-0 pt-4 px-4">
                        <h5 class="text-white mb-0"><i class="bi bi-person-badge me-2"></i>GW20 Captain Pick</h5>
                        <small class="text-white-50"><span id="poll1Votes">2,847</span> votes</small>
                    </div>
                    <div class="card-body px-4 pb-4" id="poll1">
                        <!-- Will be populated -->
                    </div>
                </div>
            </div>

            <!-- Poll 2: Chip Usage -->
            <div class="col-lg-6">
                <div class="card poll-card h-100">
                    <div class="card-header bg-transparent border-0 pt-4 px-4">
                        <h5 class="text-white mb-0"><i class="bi bi-lightning-charge me-2"></i>Are you using a chip this GW?</h5>
                        <small class="text-white-50"><span id="poll2Votes">1,523</span> votes</small>
                    </div>
                    <div class="card-body px-4 pb-4" id="poll2">
                        <!-- Will be populated -->
                    </div>
                </div>
            </div>

            <!-- Poll 3: Transfer -->
            <div class="col-lg-6">
                <div class="card poll-card h-100">
                    <div class="card-header bg-transparent border-0 pt-4 px-4">
                        <h5 class="text-white mb-0"><i class="bi bi-arrow-left-right me-2"></i>Top Transfer Target</h5>
                        <small class="text-white-50"><span id="poll3Votes">3,156</span> votes</small>
                    </div>
                    <div class="card-body px-4 pb-4" id="poll3">
                        <!-- Will be populated -->
                    </div>
                </div>
            </div>

            <!-- Poll 4: Strategy -->
            <div class="col-lg-6">
                <div class="card poll-card h-100">
                    <div class="card-header bg-transparent border-0 pt-4 px-4">
                        <h5 class="text-white mb-0"><i class="bi bi-gear me-2"></i>Your GW Strategy</h5>
                        <small class="text-white-50"><span id="poll4Votes">1,892</span> votes</small>
                    </div>
                    <div class="card-body px-4 pb-4" id="poll4">
                        <!-- Will be populated -->
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<?php include 'footer.php';?>
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
    // Poll data (would be from API in production)
    const polls = {
        poll1: {
            question: 'Captain Pick',
            options: [
                { name: 'Haaland', votes: 1450, color: '#3b82f6' },
                { name: 'Salah', votes: 890, color: '#ef4444' },
                { name: 'Palmer', votes: 320, color: '#06b6d4' },
                { name: 'Other', votes: 187, color: '#6b7280' }
            ],
            userVote: localStorage.getItem('poll1_vote')
        },
        poll2: {
            question: 'Chip Usage',
            options: [
                { name: 'No chip', votes: 980, color: '#6b7280' },
                { name: 'Free Hit', votes: 210, color: '#06b6d4' },
                { name: 'Wildcard', votes: 180, color: '#8b5cf6' },
                { name: 'Triple Captain', votes: 95, color: '#f59e0b' },
                { name: 'Bench Boost', votes: 58, color: '#10b981' }
            ],
            userVote: localStorage.getItem('poll2_vote')
        },
        poll3: {
            question: 'Transfer Target',
            options: [
                { name: 'Palmer', votes: 1250, color: '#3b82f6' },
                { name: 'Isak', votes: 780, color: '#10b981' },
                { name: 'Gordon', votes: 520, color: '#f59e0b' },
                { name: 'Saka', votes: 350, color: '#ef4444' },
                { name: 'Other', votes: 256, color: '#6b7280' }
            ],
            userVote: localStorage.getItem('poll3_vote')
        },
        poll4: {
            question: 'Strategy',
            options: [
                { name: 'Roll transfer', votes: 720, color: '#10b981' },
                { name: 'One free transfer', votes: 680, color: '#3b82f6' },
                { name: 'Take -4 hit', votes: 320, color: '#f59e0b' },
                { name: 'Take -8 or more', votes: 172, color: '#ef4444' }
            ],
            userVote: localStorage.getItem('poll4_vote')
        }
    };

    function renderPoll(pollId, poll) {
        const container = document.getElementById(pollId);
        const totalVotes = poll.options.reduce((sum, o) => sum + o.votes, 0);
        const hasVoted = poll.userVote !== null;

        let html = '';
        poll.options.forEach((option, idx) => {
            const pct = totalVotes > 0 ? (option.votes / totalVotes * 100).toFixed(1) : 0;
            const isSelected = poll.userVote === option.name;

            if(hasVoted) {
                // Show results
                html += `
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-white ${isSelected ? 'fw-bold' : ''}">${option.name} ${isSelected ? '✓' : ''}</span>
                            <span class="text-white-50">${pct}%</span>
                        </div>
                        <div class="poll-bar">
                            <div class="poll-bar-fill" style="width: ${pct}%; background: ${option.color};">${pct > 10 ? pct + '%' : ''}</div>
                        </div>
                    </div>
                `;
            } else {
                // Show voting options
                html += `
                    <div class="poll-option" onclick="vote('${pollId}', '${option.name}')">
                        <span class="text-white">${option.name}</span>
                    </div>
                `;
            }
        });

        container.innerHTML = html;
        document.getElementById(pollId + 'Votes').textContent = totalVotes.toLocaleString();
    }

    function vote(pollId, optionName) {
        localStorage.setItem(pollId + '_vote', optionName);
        polls[pollId].userVote = optionName;
        
        // Increment vote count (would be API call in production)
        const option = polls[pollId].options.find(o => o.name === optionName);
        if(option) option.votes++;
        
        renderPoll(pollId, polls[pollId]);
    }

    // Initialize all polls
    Object.keys(polls).forEach(pollId => {
        renderPoll(pollId, polls[pollId]);
    });
</script>
</body>
</html>
