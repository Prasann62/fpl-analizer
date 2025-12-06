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
    <title>Fixtures | FPL Manager</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="style.css" rel="stylesheet">
</head>
<body>
<?php include 'navbar.php';?>

<div class="container py-5">
    <!-- Hero Header -->
    <div class="hero-header shadow-lg mb-5 text-center">
        <h1 class="display-4 fw-extrabold mb-2">Match Centre</h1>
        <p class="lead opacity-75 mb-0">Upcoming fixtures and schedule.</p>
    </div>

    <div id="fixtures" class="row g-4">
        <div class="col-12 text-center">
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
    const fixturesContainer = document.getElementById('fixtures');

    async function loadFixtures() {
        try {
            // 1. Get Next Gameweek
            const bootstrapRes = await fetch('api.php?endpoint=bootstrap-static/');
            const bootstrapData = await bootstrapRes.json();
            
            const nextEvent = bootstrapData.events.find(e => e.is_next);
            if (!nextEvent) {
                fixturesContainer.innerHTML = `
                    <div class="col-12">
                        <div class="alert alert-info text-center">
                            <i class="bi bi-calendar-x display-4 d-block mb-3"></i>
                            No upcoming fixtures found.
                        </div>
                    </div>
                `;
                return;
            }

            // Map teams
            const teams = {};
            bootstrapData.teams.forEach(t => {
                teams[t.id] = t;
            });

            // 2. Get Fixtures for Next Gameweek
            const fixturesRes = await fetch(`api.php?endpoint=fixtures/?event=${nextEvent.id}`);
            const fixtures = await fixturesRes.json();

            // 3. Render
            let html = `
                <div class="col-12 mb-2">
                    <div class="d-flex align-items-center justify-content-center gap-3">
                        <div class="h-px bg-secondary flex-grow-1" style="height: 2px; opacity: 0.2;"></div>
                        <h3 class="fw-bold text-primary m-0">Gameweek ${nextEvent.id}</h3>
                        <div class="h-px bg-secondary flex-grow-1" style="height: 2px; opacity: 0.2;"></div>
                    </div>
                </div>
            `;
            
            fixtures.forEach(match => {
                const homeTeam = teams[match.team_h];
                const awayTeam = teams[match.team_a];
                const date = new Date(match.kickoff_time);
                const dateStr = date.toLocaleDateString('en-GB', { weekday: 'short', day: 'numeric', month: 'short' });
                const timeStr = date.toLocaleTimeString('en-GB', { hour: '2-digit', minute: '2-digit' });

                html += `
                    <div class="col-md-6 col-lg-4">
                        <a href="match-details.php?id=${match.id}&event=${match.event}" class="text-decoration-none">
                            <div class="card h-100 shadow-hover border-0">
                                <div class="card-body position-relative overflow-hidden">
                                    <!-- Background Decoration -->
                                    <div class="position-absolute top-0 start-0 w-100 h-100" 
                                         style="background: linear-gradient(45deg, rgba(55,0,60,0.03) 0%, rgba(0,255,133,0.03) 100%); z-index: 0;">
                                    </div>
                                    
                                    <div class="position-relative z-1">
                                        <div class="text-center mb-3">
                                            <span class="badge bg-light text-dark border shadow-sm">
                                                <i class="bi bi-calendar3 me-1"></i> ${dateStr} • ${timeStr}
                                            </span>
                                        </div>
                                        
                                        <div class="d-flex justify-content-between align-items-center px-2">
                                            <div class="text-center" style="width: 40%;">
                                                <div class="fw-bold text-dark mb-1 h5">${homeTeam.short_name}</div>
                                                <div class="small text-muted">Home</div>
                                            </div>
                                            
                                            <div class="text-center" style="width: 20%;">
                                                <div class="bg-light rounded-circle d-flex align-items-center justify-content-center mx-auto shadow-sm" style="width: 40px; height: 40px;">
                                                    <span class="fw-bold text-primary">VS</span>
                                                </div>
                                            </div>
                                            
                                            <div class="text-center" style="width: 40%;">
                                                <div class="fw-bold text-dark mb-1 h5">${awayTeam.short_name}</div>
                                                <div class="small text-muted">Away</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                `;
            });

            fixturesContainer.innerHTML = html;

        } catch (error) {
            console.error(error);
            fixturesContainer.innerHTML = `
                <div class="col-12">
                    <div class="alert alert-danger text-center">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                        Error loading fixtures: ${error.message}
                    </div>
                </div>
            `;
        }
    }

    loadFixtures();
</script>
</body>
</html>