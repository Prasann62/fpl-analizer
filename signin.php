<?php
/**
 * User Registration Page
 * SECURITY: Rate limited, input validated, passwords hashed, SQL injection protected
 */

// Load security libraries
require_once __DIR__ . '/includes/security.php';
require_once __DIR__ . '/includes/ratelimit.php';
require_once __DIR__ . '/includes/database.php';
require_once __DIR__ . '/includes/session.php';

// Start secure session
SecureSession::start();

// Initialize error/success messages
$error_message = null;
$success_message = null;

// Get CSRF token (don't force generate a new one every load)
$csrfToken = Security::getCSRFToken();

if(isset($_POST['but'])) {
    // SECURITY: Enforce rate limiting (3 registrations per hour per IP)
    RateLimit::enforce('register');

    // SECURITY: Verify CSRF token
    $submittedToken = $_POST['csrf_token'] ?? '';
    if (!Security::verifyCSRFToken($submittedToken)) {
        Security::logSecurityEvent('CSRF token validation failed on registration');
        $error_message = "Security validation failed. Please try again.";
        // Refresh token on failure
        $csrfToken = Security::generateCSRFToken();
    } else {
        // SECURITY: Sanitize and validate inputs
        $name  = Security::sanitizeInput($_POST['na'] ?? '', 'string', 100);
        $email = Security::sanitizeInput($_POST['gm'] ?? '', 'email', 255);
        $password   = $_POST['ps'] ?? ''; // Don't sanitize password (preserve special chars)

        $errors = [];

        // Validate name
        if (!Security::validateLength($name, 2, 100)) {
            $errors[] = "Name must be between 2 and 100 characters";
        }

        // Validate email
        if (!Security::validateEmail($email)) {
            $errors[] = "Invalid email address";
        }

        // Validate password strength
        $passwordValidation = Security::validatePassword($password);
        if (!$passwordValidation['valid']) {
            $errors = array_merge($errors, $passwordValidation['errors']);
        }

        if (empty($errors)) {
            try {
                $db = Database::getInstance();

                // SECURITY: Check if email already exists
                $existingUser = $db->selectOne(
                    "SELECT id FROM signin WHERE email = ?",
                    's',
                    [$email]
                );

                if ($existingUser) {
                    $error_message = "Email already registered. Please login instead.";
                } else {
                    // SECURITY: Hash password before storage
                    $hashedPassword = Security::hashPassword($password);

                    // SECURITY: Use prepared statement to prevent SQL injection
                    $db->execute(
                        "INSERT INTO signin (name, email, password, role) VALUES (?, ?, ?, 'user')",
                        'sss',
                        [$name, $email, $hashedPassword]
                    );

                    Security::logSecurityEvent('User registered successfully', [
                        'email' => $email
                    ]);

                    // Redirect to login page
                    header('Location: loginform.php?registered=1');
                    exit;
                }

            } catch (Exception $e) {
                Security::logSecurityEvent('Registration error', [
                    'error' => $e->getMessage()
                ]);
                $error_message = "Registration failed. Please try again later.";
            }
        } else {
            $error_message = implode('<br>', $errors);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <?php include 'favicon-meta.php'; ?>
  <title>Sign Up | FPL Manager</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="style.css?v=<?php echo time(); ?>" rel="stylesheet">
</head>
<body class="bg-[#0f172a] text-slate-200 min-h-screen flex items-center justify-center p-4 font-['Outfit',_sans-serif]">
    <!-- Tailwind CSS (Loaded via CDN) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#10b981', // Emerald 500
                        accent: '#3b82f6',  // Blue 500
                    },
                }
            }
        }
    </script>
    
    <!-- Animated Mesh Background -->
    <div class="fixed inset-0 -z-10 overflow-hidden">
        <div class="absolute -top-[40%] -left-[20%] w-[80%] h-[80%] rounded-full bg-emerald-500/10 blur-[120px] animate-pulse"></div>
        <div class="absolute -bottom-[40%] -right-[20%] w-[80%] h-[80%] rounded-full bg-blue-500/10 blur-[120px] animate-pulse" style="animation-delay: 2s;"></div>
    </div>

    <style>
        .glass-container {
            background: rgba(30, 41, 59, 0.7);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
        }
        .input-glow:focus-within {
            border-color: #10b981;
            box-shadow: 0 0 20px rgba(16, 185, 129, 0.15);
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-in {
            animation: fadeIn 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }
    </style>

    <div class="w-full max-w-md animate-fade-in">
        <!-- Logo/Brand Section -->
        <div class="text-center mb-10">
            <h1 class="text-4xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-white to-slate-400 tracking-tight">Create Account</h1>
            <p class="text-slate-400 mt-3 font-light">Join the elite FPL scouting network</p>
        </div>

        <!-- Signup Card -->
        <div class="glass-container rounded-[2.5rem] p-8 sm:p-12">
            <form method="POST" action="" class="space-y-6">
                <!-- SECURITY: CSRF Token -->
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken); ?>">
                
                <div class="space-y-5">
                    <!-- Name Input -->
                    <div class="space-y-2">
                        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-[0.2em] ml-1">Full Name</label>
                        <div class="input-glow flex items-center border border-slate-700/50 rounded-2xl bg-slate-800/30 transition-all duration-300 overflow-hidden group">
                            <div class="pl-5 text-slate-500 group-focus-within:text-emerald-500 transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </div>
                            <input required type="text" name="na" minlength="2" maxlength="100" class="w-full px-4 py-4 bg-transparent outline-none text-white placeholder-slate-600 font-light" placeholder="John Doe" value="<?php echo isset($_POST['na']) ? htmlspecialchars($_POST['na']) : ''; ?>">
                        </div>
                    </div>

                    <!-- Email Input -->
                    <div class="space-y-2">
                        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-[0.2em] ml-1">Email Address</label>
                        <div class="input-glow flex items-center border border-slate-700/50 rounded-2xl bg-slate-800/30 transition-all duration-300 overflow-hidden group">
                            <div class="pl-5 text-slate-500 group-focus-within:text-emerald-500 transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <input required type="email" name="gm" maxlength="255" class="w-full px-4 py-4 bg-transparent outline-none text-white placeholder-slate-600 font-light" placeholder="name@example.com" value="<?php echo isset($_POST['gm']) ? htmlspecialchars($_POST['gm']) : ''; ?>">
                        </div>
                    </div>

                    <!-- Password Input -->
                    <div class="space-y-2">
                        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-[0.2em] ml-1">Password</label>
                        <div class="input-glow flex items-center border border-slate-700/50 rounded-2xl bg-slate-800/30 transition-all duration-300 overflow-hidden group">
                            <div class="pl-5 text-slate-500 group-focus-within:text-emerald-500 transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                            </div>
                            <input required type="password" name="ps" minlength="8" class="w-full px-4 py-4 bg-transparent outline-none text-white placeholder-slate-600 font-light" placeholder="••••••••">
                        </div>
                        <p class="text-[10px] text-slate-500 uppercase tracking-wider ml-1">8+ chars, 1 uppercase, 1 number</p>
                    </div>
                </div>
                
                <!-- Submit Button -->
                <button type="submit" name="but" class="w-full relative group overflow-hidden bg-emerald-500 text-white font-bold rounded-2xl py-4 transition-all duration-300 hover:shadow-[0_0_30px_rgba(16,185,129,0.4)] active:scale-[0.98] mt-4">
                    <div class="absolute inset-0 w-full h-full bg-gradient-to-r from-emerald-400 to-blue-500 opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                    <span class="relative z-10">Create Account</span>
                </button>
            </form>

            <div class="mt-10 text-center">
                <p class="text-slate-500 font-light">Already have an account? 
                    <a href="loginform.php" class="text-emerald-400 hover:text-emerald-300 font-semibold transition-colors ml-1">Login here</a>
                </p>
            </div>
        </div>

        <p class="text-center text-slate-600 text-xs mt-12 tracking-widest uppercase">
            &copy; <?= date('Y'); ?> FPL Manager &bull; Advanced Scouting
        </p>
    </div>
    
    <?php if(isset($error_message)): ?>
    <div id="notification" class='fixed bottom-8 left-1/2 -translate-x-1/2 bg-slate-900/90 border border-slate-700/50 backdrop-blur-xl text-slate-200 px-6 py-4 rounded-2xl shadow-2xl flex items-center space-x-3 z-50 min-w-[300px]' role='alert'>
        <div class="bg-red-500/10 p-2 rounded-lg">
            <svg class='w-5 h-5 text-red-500' fill='currentColor' viewBox='0 0 20 20'><path fill-rule='evenodd' d='M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z' clip-rule='evenodd'></path></svg>
        </div>
        <span class='font-medium text-sm text-center w-full'><?php echo $error_message; ?></span>
    </div>
    <script>
        setTimeout(() => { 
            const node = document.getElementById('notification');
            if(node) {
                node.style.opacity = '0';
                node.style.transform = 'translate(-50%, 20px)';
                node.style.transition = 'all 0.5s ease';
                setTimeout(() => node.remove(), 500);
            }
        }, 6000);
    </script>
    <?php endif; ?>

</body>
</html>
