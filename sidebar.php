<div class="sidebar" id="sidebar">
    <a class="logo-box sticky top-0 flex min-h-topbar-height items-center justify-start px-6 backdrop-blur-xs" href="index.php">
        <!-- Light Brand Logo -->
        <div class="logo-light"><span class="text-xl font-bold p-4">My App</span></div>
        <!-- Dark Brand Logo -->
        <div class="logo-dark"><span class="text-xl font-bold p-4">My App</span></div>
    </a>
    
    <ul class="side-nav p-3 hs-accordion-group">
        <li class="menu-title">
            <span>Overview</span>
        </li>

        <li class="menu-item">
            <a href="index.php" class="menu-link <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>">
                <span class="menu-icon"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-home"><path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg></span>
                <span class="menu-text"> Home </span>
            </a>
        </li>

        <li class="menu-item">
            <a href="Dashboard.php" class="menu-link <?php echo basename($_SERVER['PHP_SELF']) == 'Dashboard.php' ? 'active' : ''; ?>">
                <span class="menu-icon"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-layout-dashboard"><rect width="7" height="9" x="3" y="3" rx="1"/><rect width="7" height="5" x="14" y="3" rx="1"/><rect width="7" height="9" x="14" y="12" rx="1"/><rect width="7" height="5" x="3" y="16" rx="1"/></svg></span>
                <span class="menu-text"> Dashboard </span>
            </a>
        </li>

        <li class="menu-item">
            <a href="team.php" class="menu-link <?php echo basename($_SERVER['PHP_SELF']) == 'team.php' ? 'active' : ''; ?>">
                <span class="menu-icon"><i class="bi bi-people-fill"></i></span>
                <span class="menu-text"> My Team </span>
            </a>
        </li>

        <li class="menu-item">
            <a href="planner.php" class="menu-link <?php echo basename($_SERVER['PHP_SELF']) == 'planner.php' ? 'active' : ''; ?>">
                 <span class="menu-icon"><i class="bi bi-calendar-check"></i></span>
                <span class="menu-text"> Planner </span>
            </a>
        </li>

        <li class="menu-title">
            <span>Stats & Data</span>
        </li>

        <li class="menu-item">
            <a href="fixtures.php" class="menu-link <?php echo basename($_SERVER['PHP_SELF']) == 'fixtures.php' ? 'active' : ''; ?>">
                <span class="menu-icon"><i class="bi bi-calendar3"></i></span>
                <span class="menu-text"> Fixtures </span>
            </a>
        </li>
        <li class="menu-item">
            <a href="players.php" class="menu-link <?php echo basename($_SERVER['PHP_SELF']) == 'players.php' ? 'active' : ''; ?>">
                <span class="menu-icon"><i class="bi bi-person-lines-fill"></i></span>
                <span class="menu-text"> Players </span>
            </a>
        </li>
        <li class="menu-item">
            <a href="leagues.php" class="menu-link <?php echo basename($_SERVER['PHP_SELF']) == 'leagues.php' ? 'active' : ''; ?>">
                <span class="menu-icon"><i class="bi bi-trophy"></i></span>
                <span class="menu-text"> Leagues </span>
            </a>
        </li>
        <li class="menu-item">
             <a href="rank.php" class="menu-link <?php echo basename($_SERVER['PHP_SELF']) == 'rank.php' ? 'active' : ''; ?>">
                <span class="menu-icon"><i class="bi bi-bar-chart-line"></i></span>
                <span class="menu-text"> Live Rank </span>
            </a>
        </li>
        <li class="menu-item">
             <a href="live-score.php" class="menu-link <?php echo basename($_SERVER['PHP_SELF']) == 'live-score.php' ? 'active' : ''; ?>">
                <span class="menu-icon"><i class="bi bi-activity"></i></span>
                <span class="menu-text"> Live Score </span>
            </a>
        </li>

        <li class="menu-title">
            <span>Tools</span>
        </li>
        
        <li class="menu-item">
             <a href="compare.php" class="menu-link <?php echo basename($_SERVER['PHP_SELF']) == 'compare.php' ? 'active' : ''; ?>">
                <span class="menu-icon"><i class="bi bi-arrow-left-right"></i></span>
                <span class="menu-text"> Comparison </span>
            </a>
        </li>
         <li class="menu-item">
             <a href="price-changes.php" class="menu-link <?php echo basename($_SERVER['PHP_SELF']) == 'price-changes.php' ? 'active' : ''; ?>">
                <span class="menu-icon"><i class="bi bi-currency-pound"></i></span>
                <span class="menu-text"> Price Changes </span>
            </a>
        </li>

        <li class="menu-item hs-accordion">
            <a href="javascript:void(0)" class="hs-accordion-toggle menu-link">
                <span class="menu-icon"><i class="bi bi-cpu"></i></span>
                <span class="menu-text"> AI Hub </span>
                <span class="menu-arrow"></span>
            </a>

            <ul class="sub-menu hs-accordion-content hidden">
                <li class="menu-item">
                    <a class="menu-link" href="ai-team-rating.php">
                        <span class="menu-text">Team Rating</span>
                    </a>
                </li>
                 <li class="menu-item">
                    <a class="menu-link" href="ai-team-picker.php">
                        <span class="menu-text">Team Picker</span>
                    </a>
                </li>
                 <li class="menu-item">
                    <a class="menu-link" href="ai-team-improver.php">
                        <span class="menu-text">Team Improver</span>
                    </a>
                </li>
                 <li class="menu-item">
                    <a class="menu-link" href="ai-team-point-predictor.php">
                        <span class="menu-text">Point Predictor</span>
                    </a>
                </li>
            </ul>
        </li>

        <li class="menu-title">
            <span>Extra</span>
        </li>

        <li class="menu-item">
            <a href="expert-reveals.php" class="menu-link <?php echo basename($_SERVER['PHP_SELF']) == 'expert-reveals.php' ? 'active' : ''; ?>">
                <span class="menu-icon"><i class="bi bi-star"></i></span>
                <span class="menu-text">Expert Reveals</span>
            </a>
        </li>
        <li class="menu-item">
            <a href="draft.php" class="menu-link <?php echo basename($_SERVER['PHP_SELF']) == 'draft.php' ? 'active' : ''; ?>">
                <span class="menu-icon"><i class="bi bi-shuffle"></i></span>
                <span class="menu-text">FPL Draft</span>
            </a>
        </li>
         <li class="menu-item">
            <a href="logout.php" class="menu-link text-danger">
                <span class="menu-icon"><i class="bi bi-box-arrow-left"></i></span>
                <span class="menu-text">Logout</span>
            </a>
        </li>

    </ul>
    
    <div class="mt-auto p-4 border-t border-[rgba(255,255,255,0.05)] d-md-none">
         <button type="button" id="closeSidebar" class="btn btn-outline-light w-100">Close Menu</button>
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
                 sidebar.classList.toggle('translate-x-0');
                 sidebar.classList.toggle('-translate-x-full');
                 if(overlay) overlay.classList.toggle('active');
             });
         }
         
         const closeSidebar = () => {
             sidebar.classList.add('-translate-x-full');
             sidebar.classList.remove('translate-x-0');
             if(overlay) overlay.classList.remove('active');
         };

         if(closeBtn) closeBtn.addEventListener('click', closeSidebar);
         if(overlay) overlay.addEventListener('click', closeSidebar);

         // Accordion functionality for hs-accordion
         const accordions = document.querySelectorAll('.hs-accordion-toggle');
         accordions.forEach(acc => {
             acc.addEventListener('click', function(e) {
                 e.preventDefault();
                 const parent = this.closest('.hs-accordion');
                 const content = parent.querySelector('.hs-accordion-content');
                 
                 // Toggle current
                 if (content.classList.contains('hidden')) {
                     content.classList.remove('hidden');
                     content.style.display = 'block';
                     this.classList.add('active');
                     this.setAttribute('aria-expanded', 'true');
                 } else {
                     content.classList.add('hidden');
                     content.style.display = 'none';
                     this.classList.remove('active');
                     this.setAttribute('aria-expanded', 'false');
                 }
             });
         });
    });
</script>
