<!-- Top Navbar -->
<?php include_once 'sidebar.php'; ?>
<script src="https://cdn.tailwindcss.com"></script>
<script>
    tailwind.config = {
        theme: {
            extend: {
                colors: {
                    primary: '#00ff85',
                    card: 'rgba(255, 255, 255, 0.1)',
                }
            }
        }
    }
</script>

<nav class="navbar-top relative flex items-center justify-between h-16 px-4 py-2 bg-card rounded-md shadow-md">
    <div class="relative group">
        <button type="button" class="btn btn-icon bg-primary text-white" id="sidebarToggle">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" data-lucide="menu" class="lucide lucide-menu"><path d="M4 5h16"></path><path d="M4 12h16"></path><path d="M4 19h16"></path></svg>
        </button>

        <ul class="absolute start-0 z-50 lg:w-352 md:w-175 w-75 top-full mt-2 hidden flex-col bg-card shadow-lg rounded-md py-3 group-focus-within:flex transition-all duration-300">
            <li>
                <a href="index.php" class="block px-4 py-2.5 text-[15px] font-medium text-default-800 hover:text-primary transition-all">Home</a>
            </li>
            <li>
                <a href="Dashboard.php" class="block px-4 py-2.5 text-[15px] font-medium text-default-800 hover:text-primary transition-all">Dashboard</a>
            </li>
            <li>
                <a href="logout.php" class="block px-4 py-2.5 text-[15px] font-medium text-danger transition-all">Logout</a>
            </li>
        </ul>
    </div>

    <a href="index.php">
        <img src="../assets/logo-dark-BRT9tiBX.png" alt="" class="h-5 block dark:hidden">
        <img src="../assets/logo-light-CCjoJosn.png" alt="" class="h-5 dark:block hidden">
    </a>
</nav>
