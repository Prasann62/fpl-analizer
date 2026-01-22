<?php
/**
 * User Login Page
 * SECURITY: Rate limited, CSRF protected, password verification with on-the-fly migration
 */

// Load security libraries
require_once __DIR__ . '/includes/security.php';
require_once __DIR__ . '/includes/ratelimit.php';
require_once __DIR__ . '/includes/database.php';
require_once __DIR__ . '/includes/session.php';

// Start secure session
SecureSession::start();

// Initialize error message
$error_message = null;

// Check for registration success
if (isset($_GET['registered']) && $_GET['registered'] == '1') {
    $error_message = "<span class='text-green-600'>Registration successful! Please login.</span>";
}

// Generate CSRF token
$csrfToken = Security::generateCSRFToken();

if(isset($_POST['login_btn'])) {
    // SECURITY: Enforce rate limiting (5 attempts per 15 minutes per IP)
    RateLimit::enforce('login');

    // SECURITY: Verify CSRF token
    $submittedToken = $_POST['csrf_token'] ?? '';
    if (!Security::verifyCSRFToken($submittedToken)) {
        Security::logSecurityEvent('CSRF token validation failed on login');
        $error_message = "Security validation failed. Please try again.";
    } else {
        // SECURITY: Sanitize inputs
        $email = Security::sanitizeInput($_POST['email'] ?? '', 'email', 255);
        $password = $_POST['password'] ?? '';

        // Validate inputs
        if (empty($email) || !Security::validateEmail($email)) {
            $error_message = "Invalid email address";
        } elseif (empty($password)) {
            $error_message = "Password is required";
        } else {
            try {
                $db = Database::getInstance();

                // SECURITY: Use prepared statement
                $user = $db->selectOne(
                    "SELECT * FROM signin WHERE email = ?",
                    's',
                    [$email]
                );

                if ($user) {
                    $passwordValid = false;
                    $needsMigration = false;

                    // SECURITY: Check if password is hashed or plain text
                    if (password_get_info($user['password'])['algo'] !== null) {
                        // Password is hashed - verify using password_verify
                        $passwordValid = Security::verifyPassword($password, $user['password']);
                        
                        // Check if rehashing needed (algorithm updated)
                        if ($passwordValid && Security::needsRehash($user['password'])) {
                            $needsMigration = true;
                        }
                    } else {
                        // Password is plain text - compare directly then migrate
                        if ($password === $user['password']) {
                            $passwordValid = true;
                            $needsMigration = true;
                            
                            Security::logSecurityEvent('Plain text password detected and migrated', [
                                'email' => $email
                            ]);
                        }
                    }

                    if ($passwordValid) {
                        // SECURITY: Migrate password if needed
                        if ($needsMigration) {
                            $newHash = Security::hashPassword($password);
                            $db->execute(
                                "UPDATE signin SET password = ? WHERE id = ?",
                                'si',
                                [$newHash, $user['id']]
                            );
                        }

                        // Login successful
                        $_SESSION['access'] = true;
                        $_SESSION['email'] = $email;
                        $_SESSION['role'] = $user['role'] ?? 'user';

                        // SECURITY: Regenerate session ID after login
                        SecureSession::regenerate();

                        Security::logSecurityEvent('User logged in successfully', [
                            'email' => $email
                        ]);

                        // Reset rate limit on successful login
                        RateLimit::reset('login');

                        // Redirect based on role
                        if ($user['role'] === 'admin') {
                            header("Location: admin_dashboard.php");
                        } else {
                            header("Location: Dashboard.php");
                        }
                        exit();
                    } else {
                        // Invalid password
                        Security::logSecurityEvent('Failed login attempt - invalid password', [
                            'email' => $email
                        ]);
                        $error_message = "Invalid Email or Password";
                    }
                } else {
                    // User not found
                    Security::logSecurityEvent('Failed login attempt - user not found', [
                        'email' => $email
                    ]);
                    $error_message = "Invalid Email or Password";
                }

            } catch (Exception $e) {
                Security::logSecurityEvent('Login error', [
                    'error' => $e->getMessage()
                ]);
                $error_message = "An error occurred. Please try again later.";
            }
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
  <title>Login | FPL Manager</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="style.css?v=<?= time(); ?>" rel="stylesheet">
</head>
<body class="bg-gradient-to-br from-emerald-50 via-white to-emerald-50 min-h-screen flex items-center justify-center p-4">
    <!-- Tailwind CSS (Loaded via CDN for simplicity, matches navbar) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#059669', // Emerald 600
                        secondary: '#10b981', // Emerald 500
                    },
                    fontFamily: {
                        sans: ['Outfit', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    
    <style>
        .glass-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.5);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.05);
        }
        .input-group:focus-within {
            border-color: #059669; /* Emerald 600 */
            box-shadow: 0 0 0 4px rgba(5, 150, 105, 0.1);
        }
    </style>

    <div class="w-full max-w-md">
        <!-- Logo/Brand Section -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-gradient-to-tr from-emerald-600 to-emerald-400 text-white shadow-lg mb-4 transform hover:scale-105 transition-transform duration-300">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                </svg>
            </div>
            <h1 class="text-3xl font-bold text-gray-900 tracking-tight">Welcome Back</h1>
            <p class="text-gray-500 mt-2 text-sm">Sign in to your FPL Manager account</p>
        </div>

        <!-- Login Card -->
        <div class="glass-card rounded-2xl p-8 sm:p-10">
            <form method="POST" action="" class="space-y-6">
                <!-- SECURITY: CSRF Token -->
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken); ?>">
                
                <!-- Email Input -->
                <div class="space-y-2">
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider">Email Address</label>
                    <div class="input-group flex items-center border border-gray-200 rounded-xl bg-white/50 transition-all duration-200 overflow-hidden">
                        <div class="pl-4 text-gray-400">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <input required type="email" name="email" maxlength="255" class="w-full px-4 py-3 bg-transparent outline-none text-gray-700 placeholder-gray-400" placeholder="name@example.com" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                    </div>
                </div>

                <!-- Password Input -->
                <div class="space-y-2">
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider">Password</label>
                    <div class="input-group flex items-center border border-gray-200 rounded-xl bg-white/50 transition-all duration-200 overflow-hidden">
                        <div class="pl-4 text-gray-400">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                        </div>
                        <input required type="password" name="password" class="w-full px-4 py-3 bg-transparent outline-none text-gray-700 placeholder-gray-400" placeholder="••••••••">
                    </div>
                </div>
                
                <!-- Submit Button -->
                <button type="submit" name="login_btn" class="w-full bg-gradient-to-r from-emerald-600 to-emerald-500 text-white font-semibold rounded-xl py-3.5 shadow-lg shadow-emerald-500/30 hover:shadow-emerald-500/40 hover:-translate-y-0.5 transition-all duration-200">
                    Login
                </button>
            </form>

            <!-- Divider -->
            <div class="relative my-8">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-gray-100"></div>
                </div>
                <div class="relative flex justify-center text-sm">
                    <span class="px-2 bg-white text-gray-400 bg-opacity-95">New here?</span>
                </div>
            </div>

            <!-- Sign Up Link -->
            <div class="text-center">
                <a href="signin.php" class="inline-flex items-center text-emerald-600 hover:text-emerald-700 font-semibold transition-colors">
                    Create an account
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                    </svg>
                </a>
            </div>
        </div>
        
        <p class="text-center text-gray-400 text-xs mt-8">
            &copy; <?= date('Y'); ?> FPL Manager. All rights reserved.
        </p>
    </div>

    <?php if(isset($error_message)): ?>
    <div class='fixed top-4 right-4 bg-red-100 border border-red-200 text-red-700 px-4 py-3 rounded-xl shadow-lg flex items-center animate-bounce-in z-50' role='alert'>
        <svg class='w-5 h-5 mr-2' fill='currentColor' viewBox='0 0 20 20'><path fill-rule='evenodd' d='M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z' clip-rule='evenodd'></path></svg>
        <span class='block sm:inline'><?= $error_message; ?></span>
    </div>
    <script>
        setTimeout(() => { const alert = document.querySelector('[role="alert"]'); if(alert) alert.remove(); }, 4000);
    </script>
    <?php endif; ?>

</body>
</html>
