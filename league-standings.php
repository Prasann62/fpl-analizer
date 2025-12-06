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
    <title>League Standings | FPL Manager</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="style.css" rel="stylesheet">
</head>
<body>
<?php include 'navbar.php';?>

<div class="container py-5">
    <!-- Hero Header -->
    <div class="hero-header shadow-lg mb-5 text-center text-md-start d-flex flex-column flex-md-row align-items-center justify-content-between">
        <div class="z-1">
            <h1 class="display-4 fw-extrabold mb-2" id="leagueName">Loading...</h1>
            <p class="lead opacity-75 mb-0" id="leagueSubtitle">League Standings</p>
        </div>
        <div class="mt-4 mt-md-0 z-1 d-flex flex-column align-items-end">
            <a href="leagues.php" id="backToLeaguesBtn" class="btn btn-outline-light mb-3">
                <i class="bi bi-arrow-left me-2"></i>Back to Leagues
            </a>
            <i class="bi bi-list-ol display-1 opacity-25"></i>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div id="loadingSpinner" class="text-center py-5">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>

            <div id="standingsContainer" class="d-none">
                
                <!-- Analysis Section -->
                <div class="card shadow-sm mb-4 border-primary border-opacity-25">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="fw-bold text-primary mb-1">League Analysis</h5>
                            <p class="text-muted small mb-0">Analyze teams on this page</p>
                        </div>
                        <button id="analyzeBtn" class="btn btn-primary">
                            <i class="bi bi-magic me-2"></i>Analyze Page
                        </button>
                    </div>
                    <div id="analysisProgress" class="progress d-none rounded-0" style="height: 4px;">
                        <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 100%"></div>
                    </div>
                </div>

                <!-- Stats Container -->
                <div id="statsContainer" class="d-none mb-4">
                    <div class="row g-4">
                        <!-- Most Picked -->
                        <div class="col-md-4">
                            <div class="card h-100 shadow-sm">
                                <div class="card-header bg-success text-white">
                                    <h6 class="mb-0 fw-bold"><i class="bi bi-people-fill me-2"></i>Most Picked</h6>
                                </div>
                                <ul class="list-group list-group-flush" id="mostPickedList"></ul>
                            </div>
                        </div>
                        <!-- Least Picked -->
                        <div class="col-md-4">
                            <div class="card h-100 shadow-sm">
                                <div class="card-header bg-warning text-dark">
                                    <h6 class="mb-0 fw-bold"><i class="bi bi-gem me-2"></i>Differentials</h6>
                                </div>
                                <ul class="list-group list-group-flush" id="leastPickedList"></ul>
                            </div>
                        </div>
                        <!-- Captains -->
                        <div class="col-md-4">
                            <div class="card h-100 shadow-sm">
                                <div class="card-header bg-info text-white">
                                    <h6 class="mb-0 fw-bold"><i class="bi bi-star-fill me-2"></i>Captaincy</h6>
                                </div>
                                <ul class="list-group list-group-flush" id="captainList"></ul>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Standings Table -->
                <div class="card shadow-sm">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead>
                                    <tr id="tableHeaderRow">
                                        <!-- Headers injected via JS -->
                                    </tr>
                                </thead>
                                <tbody id="standingsBody"></tbody>
                            </table>
                        </div>
                    </div>
                </div>
                
                <!-- Pagination (Simple) -->
                <div class="d-flex justify-content-between mt-4">
                    <button id="prevBtn" class="btn btn-outline-primary" disabled>Previous</button>
                    <button id="nextBtn" class="btn btn-outline-primary" disabled>Next</button>
                </div>
            </div>
            
            <div id="errorAlert" class="alert alert-danger d-none mt-3" role="alert"></div>
        </div>
    </div>
</div>

<?php include 'footer.php';?>
<!-- Bootstrap Icons -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const urlParams = new URLSearchParams(window.location.search);
    const leagueId = urlParams.get('id');
    const leagueType = urlParams.get('type') || 'classic'; // 'classic' or 'h2h'
    const managerId = urlParams.get('manager_id');

    const backToLeaguesBtn = document.getElementById('backToLeaguesBtn');
    if (managerId) {
        backToLeaguesBtn.href = `leagues.php?id=${managerId}`;
    }
    
    const leagueNameEl = document.getElementById('leagueName');
    const leagueSubtitleEl = document.getElementById('leagueSubtitle');
    const loadingSpinner = document.getElementById('loadingSpinner');
    const standingsContainer = document.getElementById('standingsContainer');
    const standingsBody = document.getElementById('standingsBody');
    const tableHeaderRow = document.getElementById('tableHeaderRow');
    const errorAlert = document.getElementById('errorAlert');
    const prevBtn = document.getElementById('prevBtn');
    const nextBtn = document.getElementById('nextBtn');
    
    // Analysis Elements
    const analyzeBtn = document.getElementById('analyzeBtn');
    const analysisProgress = document.getElementById('analysisProgress');
    const statsContainer = document.getElementById('statsContainer');
    const mostPickedList = document.getElementById('mostPickedList');
    const leastPickedList = document.getElementById('leastPickedList');
    const captainList = document.getElementById('captainList');

    let currentPage = 1;
    let currentStandings = []; // Store current page standings for analysis
    let staticData = null;

    if (!leagueId) {
        showError('No league ID specified.');
        loadingSpinner.classList.add('d-none');
        return;
    }

    // Fetch static data on load
    async function fetchStaticData() {
        try {
            const response = await fetch('api.php?endpoint=bootstrap-static/');
            if (!response.ok) throw new Error('Failed to fetch static data');
            staticData = await response.json();
        } catch (error) {
            console.error('Error fetching static data:', error);
        }
    }
    fetchStaticData();

    fetchStandings(currentPage);

    prevBtn.addEventListener('click', () => {
        if (currentPage > 1) {
            currentPage--;
            fetchStandings(currentPage);
        }
    });

    nextBtn.addEventListener('click', () => {
        currentPage++;
        fetchStandings(currentPage);
    });

    analyzeBtn.addEventListener('click', () => {
        if (currentStandings.length > 0 && staticData) {
            analyzeLeague();
        } else if (!staticData) {
            showError('Still loading FPL data. Please try again in a moment.');
        }
    });

    async function fetchStandings(page) {
        showLoading(true);
        hideError();
        standingsContainer.classList.add('d-none');
        statsContainer.classList.add('d-none'); // Hide stats on page change

        try {
            const endpoint = `leagues-${leagueType}/${leagueId}/standings/?page_standings=${page}`;
            const response = await fetch(`api.php?endpoint=${endpoint}`);
            
            if (!response.ok) throw new Error('Failed to fetch standings.');
            const data = await response.json();

            renderStandings(data);
        } catch (error) {
            console.error(error);
            showError(error.message);
        } finally {
            showLoading(false);
        }
    }



    function renderStandings(data) {
        const league = data.league;
        const standings = data.standings.results;
        const hasNext = data.standings.has_next;

        currentStandings = standings; // Store for analysis
        leagueNameEl.textContent = league.name;
        
        // Try to find total count
        // Common props: league.count, league.number_of_entries, standings.count
        // Note: FPL API varies. classic standings usually don't send total count in 'standings' obj, 
        // but sometimes league obj has it.
        // If not found, we won't show it or show 'Unknown'.
        // For H2H, usually it's small enough or has it.
        let totalCount = 'Unknown';
        if (data.league.count !== undefined) totalCount = data.league.count;
        else if (data.standings.count !== undefined) totalCount = data.standings.count;
        else if (data.league.number_of_entries !== undefined) totalCount = data.league.number_of_entries;
        
        if (totalCount !== 'Unknown') {
             leagueSubtitleEl.textContent = `League Standings • ${totalCount.toLocaleString()} Managers`;
        } else {
             leagueSubtitleEl.textContent = `League Standings`;
        }

        // Headers
        if (leagueType === 'classic') {
            tableHeaderRow.innerHTML = `
                <th class="ps-4">Rank</th>
                <th>Manager & Team</th>
                <th class="text-center">GW Points</th>
                <th class="text-end pe-4">Total</th>
            `;
        } else {
            tableHeaderRow.innerHTML = `
                <th class="ps-4">Rank</th>
                <th>Manager & Team</th>
                <th class="text-center">W-D-L</th>
                <th class="text-end pe-4">Points</th>
            `;
        }

        standingsBody.innerHTML = '';
        standings.forEach(entry => {
            let row = '';
            const rankChange = entry.rank - entry.last_rank;
            let moveIcon = '<i class="bi bi-dash text-secondary small"></i>';
            if (rankChange < 0) moveIcon = '<i class="bi bi-arrow-up-short text-success"></i>';
            else if (rankChange > 0) moveIcon = '<i class="bi bi-arrow-down-short text-danger"></i>';

            if (leagueType === 'classic') {
                row = `
                    <tr>
                        <td class="ps-4 fw-bold">
                            ${entry.rank} <span class="ms-1">${moveIcon}</span>
                        </td>
                        <td>
                            <a href="team.php?id=${entry.entry}" class="fw-bold text-decoration-none text-primary">${entry.player_name}</a>
                            <div class="small text-muted">${entry.entry_name}</div>
                        </td>
                        <td class="text-center">${entry.event_total}</td>
                        <td class="text-end pe-4 fw-bold text-primary h5 mb-0">${entry.total}</td>
                    </tr>
                `;
            } else {
                // H2H
                row = `
                    <tr>
                        <td class="ps-4 fw-bold">
                            ${entry.rank} <span class="ms-1">${moveIcon}</span>
                        </td>
                        <td>
                            <a href="team.php?id=${entry.entry}" class="fw-bold text-decoration-none text-primary">${entry.player_name}</a>
                            <div class="small text-muted">${entry.entry_name}</div>
                        </td>
                        <td class="text-center text-muted small">
                            ${entry.matches_won || 0}-${entry.matches_drawn || 0}-${entry.matches_lost || 0}
                        </td>
                        <td class="text-end pe-4 fw-bold text-primary h5 mb-0">${entry.total}</td>
                    </tr>
                `;
            }
            standingsBody.innerHTML += row;
        });

        // Pagination State
        prevBtn.disabled = currentPage <= 1;
        nextBtn.disabled = !hasNext;

        standingsContainer.classList.remove('d-none');
    }

    async function analyzeLeague() {
        analyzeBtn.disabled = true;
        analysisProgress.classList.remove('d-none');
        
        const currentEvent = staticData.events.find(e => e.is_current) || staticData.events[0];
        const gw = currentEvent.id;

        const playerCounts = {};
        const captainCounts = {};
        let totalAnalyzed = 0;

        // Fetch all teams in parallel (careful with rate limits, but for 50 it might be ok-ish, let's batch if needed or just go for it)
        // To be safe, let's do batches of 5
        const batchSize = 5;
        for (let i = 0; i < currentStandings.length; i += batchSize) {
            const batch = currentStandings.slice(i, i + batchSize);
            await Promise.all(batch.map(async (entry) => {
                try {
                    const res = await fetch(`api.php?endpoint=entry/${entry.entry}/event/${gw}/picks/`);
                    if (res.ok) {
                        const data = await res.json();
                        data.picks.forEach(pick => {
                            // Count Players
                            playerCounts[pick.element] = (playerCounts[pick.element] || 0) + 1;
                            
                            // Count Captains
                            if (pick.is_captain) {
                                captainCounts[pick.element] = (captainCounts[pick.element] || 0) + 1;
                            }
                        });
                        totalAnalyzed++;
                    }
                } catch (e) {
                    console.error(`Failed to fetch team for ${entry.entry}`, e);
                }
            }));
        }

        renderStats(playerCounts, captainCounts, totalAnalyzed);
        
        analyzeBtn.disabled = false;
        analysisProgress.classList.add('d-none');
        statsContainer.classList.remove('d-none');
    }

    function renderStats(playerCounts, captainCounts, total) {
        // Helper to get player name
        const getPlayerName = (id) => {
            const p = staticData.elements.find(e => e.id == id);
            return p ? p.web_name : 'Unknown';
        };

        // Sort Most Picked
        const sortedPlayers = Object.entries(playerCounts).sort((a, b) => b[1] - a[1]);
        
        // Most Picked (Top 5)
        mostPickedList.innerHTML = '';
        sortedPlayers.slice(0, 5).forEach(([id, count]) => {
            const percent = ((count / total) * 100).toFixed(0);
            mostPickedList.innerHTML += `
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    ${getPlayerName(id)}
                    <span class="badge bg-success rounded-pill">${count}/${total}</span>
                </li>
            `;
        });

        // Least Picked (Bottom 5 of those picked)
        leastPickedList.innerHTML = '';
        // Filter for those picked by at least 1 person but less than 20% maybe? Or just bottom 5
        const differentials = sortedPlayers.filter(([id, count]) => count < (total * 0.1) && count > 0); // < 10% ownership
        differentials.slice(0, 5).forEach(([id, count]) => {
             const percent = ((count / total) * 100).toFixed(0);
             leastPickedList.innerHTML += `
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    ${getPlayerName(id)}
                    <span class="badge bg-warning text-dark rounded-pill">${count}/${total}</span>
                </li>
            `;
        });
        if (differentials.length === 0) {
             leastPickedList.innerHTML = '<li class="list-group-item text-muted">No differentials found (<10%)</li>';
        }

        // Captains
        const sortedCaptains = Object.entries(captainCounts).sort((a, b) => b[1] - a[1]);
        captainList.innerHTML = '';
        sortedCaptains.slice(0, 5).forEach(([id, count]) => {
            const percent = ((count / total) * 100).toFixed(0);
            captainList.innerHTML += `
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    ${getPlayerName(id)}
                    <span class="badge bg-info rounded-pill">${count}/${total}</span>
                </li>
            `;
        });
    }

    function showLoading(show) {
        if (show) {
            loadingSpinner.classList.remove('d-none');
        } else {
            loadingSpinner.classList.add('d-none');
        }
    }

    function showError(msg) {
        errorAlert.innerHTML = `<i class="bi bi-exclamation-triangle-fill me-2"></i>${msg}`;
        errorAlert.classList.remove('d-none');
    }

    function hideError() {
        errorAlert.classList.add('d-none');
    }
});
</script>
</body>
</html>
