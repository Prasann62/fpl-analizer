<?php
// Ensure session is started if not already
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// SECURITY: Load database wrapper
require_once __DIR__ . '/includes/database.php';
require_once __DIR__ . '/includes/security.php';

$userName = "User";
$userInitial = "U";

if (isset($_SESSION['email'])) {
    $email = $_SESSION['email'];
    
    try {
        // SECURITY: Use secure database wrapper
        $db = Database::getInstance();
        
        // SECURITY: Use prepared statement
        $user = $db->selectOne(
            "SELECT name FROM signin WHERE email = ?",
            's',
            [$email]
        );
        
        if ($user) {
            // SECURITY: Escape output to prevent XSS
            $userName = Security::escapeOutput($user['name']);
            $userInitial = strtoupper(substr($userName, 0, 1));
        }
    } catch (Exception $e) {
        // Silently fail - user will see default values
        Security::logSecurityEvent('Navbar user fetch failed', [
            'error' => $e->getMessage()
        ]);
    }
}
?>
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
    <div class="relative">
        <button type="button" class="btn btn-icon text-gray-600 hover:text-primary hover:bg-gray-100 rounded-md p-2 transition-colors" id="sidebarToggle">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" data-lucide="menu" class="lucide lucide-menu"><path d="M4 5h16"></path><path d="M4 12h16"></path><path d="M4 19h16"></path></svg>
        </button>
    </div>

    <a href="index.php" class="flex items-center gap-2">
        <img src="logo.png" alt="FPL Logo" class="h-8 w-8 object-contain">
        <span class="text-xl font-bold text-gray-800 tracking-tight">FPL<span class="text-primary">Master</span></span>
    </a>
    
    <div class="flex items-center gap-3">
        <div class="relative" id="profileDropdownContainer">
            <button class="flex items-center gap-2 focus:outline-none" id="profileDropdownBtn">
                <div class="hidden md:block text-right">
                    <div class="text-sm font-semibold text-gray-700"><?php echo $userName; ?></div>
                    <div class="text-xs text-gray-500">Manager</div>
                </div>
                <div class="h-9 w-9 rounded-full bg-emerald-100 text-emerald-700 border border-emerald-200 flex items-center justify-center font-bold text-sm shadow-sm">
                    <?php echo $userInitial; ?>
                </div>
            </button>

            <!-- Dropdown Menu -->
            <div id="profileDropdownMenu" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg py-1 border border-gray-100 z-50">
                <div class="px-4 py-3 border-b border-gray-100">
                    <p class="text-xs text-gray-500 uppercase font-bold">Signed in as</p>
                    <p class="text-sm font-medium text-gray-900 truncate"><?php echo isset($email) ? htmlspecialchars($email) : ''; ?></p>
                </div>
                <ul class="py-1">
                     <li>
                        <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 hover:text-primary transition-colors">
                           My Profile
                        </a>
                    </li> 
                    <li>
                        <a href="logout.php" class="block px-4 py-2 text-sm text-red-600 hover:bg-red-50 hover:text-red-700 transition-colors">
                           log out
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</nav>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const profileBtn = document.getElementById('profileDropdownBtn');
        const profileMenu = document.getElementById('profileDropdownMenu');

        if (profileBtn && profileMenu) {
            // Toggle menu
            profileBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                profileMenu.classList.toggle('hidden');
            });

            // Close when clicking outside
            document.addEventListener('click', (e) => {
                if (!profileBtn.contains(e.target) && !profileMenu.contains(e.target)) {
                    profileMenu.classList.add('hidden');
                }
            });
        }
    });
</script>
