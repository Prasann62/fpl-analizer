<div class="sidebar bg-white border-r border-gray-200" id="sidebar">
    <a class="logo-box sticky top-0 flex min-h-topbar-height items-center justify-start px-6 bg-white border-b border-gray-100 z-10" href="index.php">
        <!-- Logo -->
        <span class="text-xl font-bold text-gray-800 tracking-tight">FPL<span class="text-emerald-600">Master</span></span>
    </a>
    
    <ul class="side-nav p-3 hs-accordion-group">
        <li class="menu-title text-xs font-bold text-gray-400 uppercase tracking-wider px-4 mt-2 mb-1">
            <span>Overview</span>
        </li>

        <li class="menu-item">
            <a href="index.php" class="menu-link group <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>">
                <span class="menu-icon group-hover:text-emerald-600"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-home"><path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg></span>
                <span class="menu-text font-medium text-gray-600 group-hover:text-emerald-700">Home</span>
            </a>
        </li>

        <li class="menu-item">
            <a href="Dashboard.php" class="menu-link group <?php echo basename($_SERVER['PHP_SELF']) == 'Dashboard.php' ? 'active' : ''; ?>">
                <span class="menu-icon group-hover:text-emerald-600"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-layout-dashboard"><rect width="7" height="9" x="3" y="3" rx="1"/><rect width="7" height="5" x="14" y="3" rx="1"/><rect width="7" height="9" x="14" y="12" rx="1"/><rect width="7" height="5" x="3" y="16" rx="1"/></svg></span>
                <span class="menu-text font-medium text-gray-600 group-hover:text-emerald-700">Dashboard</span>
            </a>
        </li>

        <li class="menu-item">
            <a href="team.php" class="menu-link group <?php echo basename($_SERVER['PHP_SELF']) == 'team.php' ? 'active' : ''; ?>">
                <span class="menu-icon group-hover:text-emerald-600"><i class="bi bi-people-fill"></i></span>
                <span class="menu-text font-medium text-gray-600 group-hover:text-emerald-700">My Team</span>
            </a>
        </li>

        <li class="menu-item">
            <a href="planner.php" class="menu-link group <?php echo basename($_SERVER['PHP_SELF']) == 'planner.php' ? 'active' : ''; ?>">
                 <span class="menu-icon group-hover:text-emerald-600"><i class="bi bi-calendar-check"></i></span>
                <span class="menu-text font-medium text-gray-600 group-hover:text-emerald-700">Planner</span>
            </a>
        </li>

        <li class="menu-title text-xs font-bold text-gray-400 uppercase tracking-wider px-4 mt-6 mb-1">
            <span>Stats & Data</span>
        </li>

        <li class="menu-item">
            <a href="fixtures.php" class="menu-link group <?php echo basename($_SERVER['PHP_SELF']) == 'fixtures.php' ? 'active' : ''; ?>">
                <span class="menu-icon group-hover:text-emerald-600"><i class="bi bi-calendar3"></i></span>
                <span class="menu-text font-medium text-gray-600 group-hover:text-emerald-700">Fixtures</span>
            </a>
        </li>
        <li class="menu-item">
            <a href="players.php" class="menu-link group <?php echo basename($_SERVER['PHP_SELF']) == 'players.php' ? 'active' : ''; ?>">
                <span class="menu-icon group-hover:text-emerald-600"><i class="bi bi-person-lines-fill"></i></span>
                <span class="menu-text font-medium text-gray-600 group-hover:text-emerald-700">Players</span>
            </a>
        </li>
        <li class="menu-item">
            <a href="leagues.php" class="menu-link group <?php echo basename($_SERVER['PHP_SELF']) == 'leagues.php' ? 'active' : ''; ?>">
                <span class="menu-icon group-hover:text-emerald-600"><i class="bi bi-trophy"></i></span>
                <span class="menu-text font-medium text-gray-600 group-hover:text-emerald-700">Leagues</span>
            </a>
        </li>
        <li class="menu-item">
            <a href="rank.php" class="menu-link group <?php echo basename($_SERVER['PHP_SELF']) == 'rank.php' ? 'active' : ''; ?>">
                <span class="menu-icon group-hover:text-emerald-600"><i class="bi bi-bar-chart-line"></i></span>
                <span class="menu-text font-medium text-gray-600 group-hover:text-emerald-700">Live Rank</span>
            </a>
        </li>
        <li class="menu-item">
            <a href="live-score.php" class="menu-link group <?php echo basename($_SERVER['PHP_SELF']) == 'live-score.php' ? 'active' : ''; ?>">
                <span class="menu-icon group-hover:text-emerald-600"><i class="bi bi-activity"></i></span>
                <span class="menu-text font-medium text-gray-600 group-hover:text-emerald-700">Live Score</span>
            </a>
        </li>

        <li class="menu-title text-xs font-bold text-gray-400 uppercase tracking-wider px-4 mt-6 mb-1">
            <span>Tools</span>
        </li>
        
        <li class="menu-item">
             <a href="compare.php" class="menu-link group <?php echo basename($_SERVER['PHP_SELF']) == 'compare.php' ? 'active' : ''; ?>">
                <span class="menu-icon group-hover:text-emerald-600"><i class="bi bi-arrow-left-right"></i></span>
                <span class="menu-text font-medium text-gray-600 group-hover:text-emerald-700">Comparison</span>
            </a>
        </li>
         <li class="menu-item">
             <a href="price-changes.php" class="menu-link group <?php echo basename($_SERVER['PHP_SELF']) == 'price-changes.php' ? 'active' : ''; ?>">
                <span class="menu-icon group-hover:text-emerald-600"><i class="bi bi-currency-pound"></i></span>
                <span class="menu-text font-medium text-gray-600 group-hover:text-emerald-700">Price Changes</span>
            </a>
        </li>

        <li class="menu-item hs-accordion">
            <a href="javascript:void(0)" class="hs-accordion-toggle menu-link group">
                <span class="menu-icon group-hover:text-emerald-600"><i class="bi bi-cpu"></i></span>
                <span class="menu-text font-medium text-gray-600 group-hover:text-emerald-700">AI Hub</span>
                <span class="menu-arrow text-gray-400 group-hover:text-emerald-600"></span>
            </a>

            <ul class="sub-menu hs-accordion-content hidden bg-gray-50/50">
                <li class="menu-item">
                    <a class="menu-link group" href="ai-team-rating.php">
                        <span class="menu-text font-medium text-gray-600 group-hover:text-emerald-700">Team Rating</span>
                    </a>
                </li>
                 <li class="menu-item">
                    <a class="menu-link group" href="ai-team-picker.php">
                        <span class="menu-text font-medium text-gray-600 group-hover:text-emerald-700">Team Picker</span>
                    </a>
                </li>
                 <li class="menu-item">
                    <a class="menu-link group" href="ai-team-improver.php">
                        <span class="menu-text font-medium text-gray-600 group-hover:text-emerald-700">Team Improver</span>
                    </a>
                </li>
                 <li class="menu-item">
                    <a class="menu-link group" href="ai-team-point-predictor.php">
                        <span class="menu-text font-medium text-gray-600 group-hover:text-emerald-700">Point Predictor</span>
                    </a>
                </li>
            </ul>
        </li>

        <li class="menu-title text-xs font-bold text-gray-400 uppercase tracking-wider px-4 mt-6 mb-1">
            <span>Extra</span>
        </li>

        <li class="menu-item">
            <a href="expert-reveals.php" class="menu-link group <?php echo basename($_SERVER['PHP_SELF']) == 'expert-reveals.php' ? 'active' : ''; ?>">
                <span class="menu-icon group-hover:text-emerald-600"><i class="bi bi-star"></i></span>
                <span class="menu-text font-medium text-gray-600 group-hover:text-emerald-700">Expert Reveals</span>
            </a>
        </li>
        <li class="menu-item">
            <a href="draft.php" class="menu-link group <?php echo basename($_SERVER['PHP_SELF']) == 'draft.php' ? 'active' : ''; ?>">
                <span class="menu-icon group-hover:text-emerald-600"><i class="bi bi-shuffle"></i></span>
                <span class="menu-text font-medium text-gray-600 group-hover:text-emerald-700">FPL Draft</span>
            </a>
        </li>
         <li class="menu-item mt-4">
            <a href="logout.php" class="menu-link group text-red-500 hover:bg-red-50/50 hover:text-red-600">
                <span class="menu-icon"><i class="bi bi-box-arrow-left"></i></span>
                <span class="menu-text font-medium">Logout</span>
            </a>
        </li>

    </ul>
    
    <div class="mt-auto p-4 border-t border-gray-100 d-md-none">
         <button type="button" id="closeSidebar" class="btn btn-outline-danger w-100">Close Menu</button>
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
