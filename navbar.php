<!-- Top Navbar -->
<?php include_once 'sidebar.php'; ?>
<script src="https://cdn.tailwindcss.com"></script>
<script>
    tailwind.config = {
        theme: {
            extend: {
                colors: {
                    primary: '#059669', // Emerald 600
                    card: '#ffffff',
                    default: {
                        800: '#1e293b', // Slate 800
                        600: '#475569', // Slate 600
                    }
                }
            }
        }
    }
</script>

<nav class="navbar-top relative flex items-center justify-between h-16 px-4 py-2 bg-white rounded-none shadow-sm border-b border-gray-200">
    <div class="relative group">
        <button type="button" class="btn btn-icon text-gray-600 hover:text-primary hover:bg-gray-100 rounded-md p-2 transition-colors" id="sidebarToggle">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" data-lucide="menu" class="lucide lucide-menu"><path d="M4 5h16"></path><path d="M4 12h16"></path><path d="M4 19h16"></path></svg>
        </button>

        <ul class="absolute start-0 z-50 lg:w-352 md:w-175 w-75 top-full mt-2 hidden flex-col bg-white shadow-xl rounded-lg py-1 border border-gray-100 group-focus-within:flex transition-all duration-300">
            <li>
                <a href="index.php" class="block px-4 py-2.5 text-[15px] font-medium text-gray-700 hover:bg-gray-50 hover:text-primary transition-all">Home</a>
            </li>
            <li>
                <a href="Dashboard.php" class="block px-4 py-2.5 text-[15px] font-medium text-gray-700 hover:bg-gray-50 hover:text-primary transition-all">Dashboard</a>
            </li>
            <li class="border-t border-gray-100 my-1"></li>
            <li>
                <a href="logout.php" class="block px-4 py-2.5 text-[15px] font-medium text-red-600 hover:bg-red-50 transition-all">Logout</a>
            </li>
        </ul>
    </div>

    <a href="index.php" class="flex items-center gap-2">
        <img src="logo.png" alt="FPL Logo" class="h-8 w-8 object-contain">
        <span class="text-xl font-bold text-gray-800 tracking-tight">FPL<span class="text-primary">Master</span></span>
    </a>
    
    <div class="flex items-center gap-3">
        <!-- Add profile or other nav items here if needed -->
        <div class="h-8 w-8 rounded-full bg-gray-200 flex items-center justify-center text-gray-600 font-bold text-xs">
            U
        </div>
    </div>
</nav>
