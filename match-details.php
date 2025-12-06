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
    <title>Match Details | FPL Manager</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="style.css" rel="stylesheet">
</head>
<body>
<?php include 'navbar.php';?>

<div class="container py-5">
    <div id="matchContent">
        <div class="text-center py-5">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
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
    const matchContent = document.getElementById('matchContent');
    const urlParams = new URLSearchParams(window.location.search);
    const fixtureId = urlParams.get('id');

    if (!fixtureId) {
        showError('No fixture ID specified.');
        return;
    }

    let staticData = null;

    function updateStatus(msg) {
        matchContent.innerHTML = `
            <div class="text-center py-5">
                <div class="spinner-border text-primary mb-3" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <div class="text-muted small">${msg}</div>
            </div>
        `;
    }

    async function init() {
        try {
            updateStatus('Initializing...');
            
            // 1. Fetch Static Data
            updateStatus('Loading FPL data...');
            const staticRes = await fetch('api.php?endpoint=bootstrap-static/');
            if (!staticRes.ok) throw new Error('Failed to connect to FPL API');
            staticData = await staticRes.json();

            const eventId = urlParams.get('event');
            let fixture = null;

            // 2. Fetch Fixtures
            updateStatus('Finding match details...');
            let targetEventId = eventId;
            
            if (!targetEventId) {
                const currentEvent = staticData.events.find(e => e.is_current) || staticData.events.find(e => e.is_next);
                if (currentEvent) targetEventId = currentEvent.id;
            }

            if (targetEventId) {
                const fixturesRes = await fetch(`api.php?endpoint=fixtures/?event=${targetEventId}`);
                if (fixturesRes.ok) {
                    const fixtures = await fixturesRes.json();
                    fixture = fixtures.find(f => f.id == fixtureId);
                }
            }

            if (!fixture) {
                // Fallback: Try next event if we checked current
                if (!eventId) {
                     const nextEvent = staticData.events.find(e => e.is_next);
                     if (nextEvent && nextEvent.id !== targetEventId) {
                         const nextRes = await fetch(`api.php?endpoint=fixtures/?event=${nextEvent.id}`);
                         if (nextRes.ok) {
                             const nextFixtures = await nextRes.json();
                             fixture = nextFixtures.find(f => f.id == fixtureId);
                         }
                     }
                }
            }

            if (!fixture) throw new Error('Match not found. It may be from a different gameweek.');

            await renderMatch(fixture);

        } catch (error) {
            console.error(error);
            showError(error.message);
        }
    }

    async function renderMatch(fixture) {
        updateStatus('Loading live stats...');
        
        const homeTeam = staticData.teams.find(t => t.id === fixture.team_h);
        const awayTeam = staticData.teams.find(t => t.id === fixture.team_a);
        const date = new Date(fixture.kickoff_time);
        
        let liveData = null;
        try {
            if (fixture.event) {
                const liveRes = await fetch(`api.php?endpoint=event/${fixture.event}/live/`);
                if (liveRes.ok) {
                    liveData = await liveRes.json();
                }
            }
        } catch (e) {
            console.warn('Failed to load live data', e);
        }
        
        // Helper to get player stats from live data
        const getPlayerStats = (id) => liveData?.elements?.find(e => e.id === id)?.stats;

        // Build HTML
        let html = `
            <div class="card shadow-lg mb-4 border-0 overflow-hidden">
                <div class="card-header bg-dark text-white text-center py-4" 
                     style="background: linear-gradient(135deg, var(--primary-color) 0%, #000 100%);">
                    <span class="badge bg-light text-dark mb-3">${date.toLocaleDateString()} • ${date.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})}</span>
                    <div class="d-flex justify-content-center align-items-center">
                        <div class="text-center mx-4">
                            <h2 class="fw-bold mb-0">${homeTeam.name}</h2>
                            <div class="display-1 fw-bold">${fixture.team_h_score !== null ? fixture.team_h_score : '-'}</div>
                        </div>
                        <div class="mx-3"><span class="h2 text-muted">VS</span></div>
                        <div class="text-center mx-4">
                            <h2 class="fw-bold mb-0">${awayTeam.name}</h2>
                            <div class="display-1 fw-bold">${fixture.team_a_score !== null ? fixture.team_a_score : '-'}</div>
                        </div>
                    </div>
                    <div class="mt-3 badge ${fixture.finished ? 'bg-success' : 'bg-warning text-dark'}">
                        ${fixture.finished ? 'Full Time' : (fixture.started ? 'Live' : 'Upcoming')}
                    </div>
                </div>
            </div>
        `;

        // Stats Section
        if (fixture.stats && fixture.stats.length > 0) {
            html += `<div class="card shadow-sm mb-4"><div class="card-header fw-bold text-primary">Match Stats</div><div class="card-body p-0">`;
            html += `<div class="table-responsive"><table class="table table-sm align-middle mb-0">`;
            
            const statOrder = ['goals_scored', 'assists', 'own_goals', 'penalties_saved', 'penalties_missed', 'saves', 'bps', 'bonus', 'yellow_cards', 'red_cards'];
            
            fixture.stats.sort((a, b) => {
                return statOrder.indexOf(a.identifier) - statOrder.indexOf(b.identifier);
            });

            fixture.stats.forEach(stat => {
                const homeEvents = stat.h.map(e => {
                    const p = staticData.elements.find(el => el.id === e.element);
                    return `${p.web_name} (${e.value})`;
                }).join(', ');
                
                const awayEvents = stat.a.map(e => {
                    const p = staticData.elements.find(el => el.id === e.element);
                    return `${p.web_name} (${e.value})`;
                }).join(', ');

                if (homeEvents || awayEvents) {
                    html += `
                        <tr>
                            <td class="text-end w-50 pe-3">${homeEvents}</td>
                            <td class="text-center fw-bold text-muted text-uppercase small" style="width: 100px;">${stat.identifier.replace('_', ' ')}</td>
                            <td class="text-start w-50 ps-3">${awayEvents}</td>
                        </tr>
                    `;
                }
            });
            html += `</table></div></div></div>`;
        }

        // Lineups
        if (fixture.started && liveData) {
            const getTeamLineup = (teamId) => {
                return staticData.elements
                    .filter(p => p.team === teamId)
                    .map(p => {
                        const stats = getPlayerStats(p.id);
                        return { player: p, stats: stats };
                    })
                    .filter(item => item.stats && item.stats.minutes > 0)
                    .sort((a, b) => a.player.element_type - b.player.element_type); 
            };

            const homeLineup = getTeamLineup(homeTeam.id);
            const awayLineup = getTeamLineup(awayTeam.id);

            html += `
                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="card shadow-sm h-100">
                            <div class="card-header fw-bold text-center">${homeTeam.name} Lineup</div>
                            <ul class="list-group list-group-flush">
                                ${renderLineupList(homeLineup)}
                            </ul>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card shadow-sm h-100">
                            <div class="card-header fw-bold text-center">${awayTeam.name} Lineup</div>
                            <ul class="list-group list-group-flush">
                                ${renderLineupList(awayLineup)}
                            </ul>
                        </div>
                    </div>
                </div>
            `;
        } else if (!fixture.started) {
            html += `<div class="alert alert-info text-center">Lineups will be available once the match starts.</div>`;
        } else {
             html += `<div class="alert alert-warning text-center">Live stats currently unavailable.</div>`;
        }

        matchContent.innerHTML = html;
    }

    function renderLineupList(lineup) {
        if (lineup.length === 0) return '<li class="list-group-item text-muted text-center">No data available</li>';
        
        const posMap = {1: 'GK', 2: 'DEF', 3: 'MID', 4: 'FWD'};
        
        return lineup.map(item => `
            <li class="list-group-item d-flex justify-content-between align-items-center">
                <div>
                    <span class="badge bg-light text-dark border me-2" style="width: 40px;">${posMap[item.player.element_type]}</span>
                    ${item.player.web_name}
                </div>
                <div class="small text-muted">
                    ${item.stats.goals_scored > 0 ? '<i class="bi bi-circle-fill text-success mx-1" title="Goal"></i>' : ''}
                    ${item.stats.assists > 0 ? '<span class="badge bg-info text-dark mx-1">A</span>' : ''}
                    ${item.stats.yellow_cards > 0 ? '<span class="badge bg-warning text-dark mx-1">YC</span>' : ''}
                    ${item.stats.red_cards > 0 ? '<span class="badge bg-danger mx-1">RC</span>' : ''}
                    <span class="badge bg-light text-dark border ms-1">${item.stats.total_points} pts</span>
                </div>
            </li>
        `).join('');
    }

    function showError(msg) {
        matchContent.innerHTML = `
            <div class="alert alert-danger text-center">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                <div class="fw-bold">Error</div>
                <div>${msg}</div>
                <div class="mt-3">
                    <a href="fixtures.php" class="btn btn-outline-danger btn-sm">Back to Fixtures</a>
                </div>
            </div>
        `;
    }

    init();
});
</script>
</body>
</html>
