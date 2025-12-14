<div class="sidebar" id="sidebar">
    <a class="logo-box sticky top-0 flex min-h-topbar-height items-center justify-start px-6 backdrop-blur-xs" href="/components/">
                <!-- Light Brand Logo -->
                <div class="logo-light"><span class="text-xl font-bold p-4">My App</span></div>
        
                <!-- Dark Brand Logo -->
                <div class="logo-dark"><span class="text-xl font-bold p-4">My App</span></div>
            </a>
    
    <div class="sidebar-menu mt-4">
        <small class="text-uppercase text-muted fw-bold ms-3" style="font-size: 0.7rem;">Main</small>
        <a href="index.php" class="sidebar-item <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>">
            <i class="bi bi-house-door"></i> Home
        </a>
        <a href="Dashboard.php" class="sidebar-item <?php echo basename($_SERVER['PHP_SELF']) == 'Dashboard.php' ? 'active' : ''; ?>">
             <i class="bi bi-speedometer2"></i> Dashboard
        </a>
        <a href="team.php" class="sidebar-item <?php echo basename($_SERVER['PHP_SELF']) == 'team.php' ? 'active' : ''; ?>">
             <i class="bi bi-people-fill"></i> My Team
        </a>
        <a href="planner.php" class="sidebar-item <?php echo basename($_SERVER['PHP_SELF']) == 'planner.php' ? 'active' : ''; ?>">
             <i class="bi bi-calendar-check"></i> Planner
        </a>

        <small class="text-uppercase text-muted fw-bold ms-3 mt-3 d-block" style="font-size: 0.7rem;">Stats & Data</small>
        <a href="fixtures.php" class="sidebar-item <?php echo basename($_SERVER['PHP_SELF']) == 'fixtures.php' ? 'active' : ''; ?>">
             <i class="bi bi-calendar3"></i> Fixtures
        </a>
        <a href="players.php" class="sidebar-item <?php echo basename($_SERVER['PHP_SELF']) == 'players.php' ? 'active' : ''; ?>">
             <i class="bi bi-person-lines-fill"></i> Players
        </a>
        <a href="leagues.php" class="sidebar-item <?php echo basename($_SERVER['PHP_SELF']) == 'leagues.php' ? 'active' : ''; ?>">
             <i class="bi bi-trophy"></i> Leagues
        </a>
        <a href="rank.php" class="sidebar-item <?php echo basename($_SERVER['PHP_SELF']) == 'rank.php' ? 'active' : ''; ?>">
             <i class="bi bi-bar-chart-line"></i> Live Rank
        </a>
        <a href="live-score.php" class="sidebar-item <?php echo basename($_SERVER['PHP_SELF']) == 'live-score.php' ? 'active' : ''; ?>">
             <i class="bi bi-activity"></i> Live Score
        </a>

        <small class="text-uppercase text-muted fw-bold ms-3 mt-3 d-block" style="font-size: 0.7rem;">Tools</small>
        <a href="compare.php" class="sidebar-item <?php echo basename($_SERVER['PHP_SELF']) == 'compare.php' ? 'active' : ''; ?>">
             <i class="bi bi-arrow-left-right"></i> Comparison
        </a>
        <a href="price-changes.php" class="sidebar-item <?php echo basename($_SERVER['PHP_SELF']) == 'price-changes.php' ? 'active' : ''; ?>">
             <i class="bi bi-currency-pound"></i> Price Changes
        </a>
        
        <div class="accordion accordion-flush bg-transparent mt-1" id="aiAccordion">
            <div class="accordion-item bg-transparent border-0">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed sidebar-item bg-transparent text-white shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#aiCollapse">
                        <i class="bi bi-cpu me-3"></i> AI Hub
                    </button>
                </h2>
                <div id="aiCollapse" class="accordion-collapse collapse" data-bs-parent="#aiAccordion">
                   <div class="accordion-body p-0 ps-3">
                        <a href="ai-team-rating.php" class="sidebar-item small py-2"><i class="bi bi-speedometer me-2"></i> Team Rating</a>
                        <a href="ai-team-picker.php" class="sidebar-item small py-2"><i class="bi bi-robot me-2"></i> Team Picker</a>
                        <a href="ai-team-improver.php" class="sidebar-item small py-2"><i class="bi bi-magic me-2"></i> Team Improver</a>
                        <a href="ai-team-point-predictor.php" class="sidebar-item small py-2"><i class="bi bi-graph-up-arrow me-2"></i> Point Predictor</a>
                   </div>
                </div>
            </div>
        </div>

        <small class="text-uppercase text-muted fw-bold ms-3 mt-3 d-block" style="font-size: 0.7rem;">Extra</small>
        <a href="expert-reveals.php" class="sidebar-item <?php echo basename($_SERVER['PHP_SELF']) == 'expert-reveals.php' ? 'active' : ''; ?>">
             <i class="bi bi-star"></i> Expert Reveals
        </a>
        <a href="draft.php" class="sidebar-item <?php echo basename($_SERVER['PHP_SELF']) == 'draft.php' ? 'active' : ''; ?>">
             <i class="bi bi-shuffle"></i> FPL Draft
        </a>

        <div class="mt-4 px-3">
             <a href="logout.php" class="btn btn-outline-danger w-100 btn-sm"><i class="bi bi-box-arrow-left me-2"></i> Logout</a>
        </div>
    </div>
</div>

<div class="sidebar-overlay" id="sidebarOverlay"></div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
         const toggleBtn = document.getElementById('sidebarToggle');
         const closeBtn = document.getElementById('closeSidebar');
         const sidebar = document.getElementById('sidebar');
         const overlay = document.getElementById('sidebarOverlay');
         
         if(toggleBtn) {
             toggleBtn.addEventListener('click', () => {
                 sidebar.classList.toggle('active');
                 overlay.classList.toggle('active');
             });
         }
         
         if(closeBtn) {
              closeBtn.addEventListener('click', () => {
                 sidebar.classList.remove('active');
                 overlay.classList.remove('active');
             });
         }
         
         if(overlay) {
              overlay.addEventListener('click', () => {
                 sidebar.classList.remove('active');
                 overlay.classList.remove('active');
             });
         }
    });
</script>
