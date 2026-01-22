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
<body class="bg-gradient-to-br from-emerald-50 via-white to-emerald-50 min-h-screen flex items-center justify-center p-4">
    <!-- Tailwind CSS -->
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
            <h1 class="text-3xl font-bold text-gray-900 tracking-tight">Create Account</h1>
            <p class="text-gray-500 mt-2 text-sm">Join FPL Manager for free</p>
        </div>

        <!-- Signup Card -->
        <div class="glass-card rounded-2xl p-8 sm:p-10">
            <form method="POST" action="" class="space-y-5">
                <!-- SECURITY: CSRF Token -->
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken); ?>">
                
                <!-- Name Input -->
                <div class="space-y-2">
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider">Full Name</label>
                    <div class="input-group flex items-center border border-gray-200 rounded-xl bg-white/50 transition-all duration-200 overflow-hidden">
                        <div class="pl-4 text-gray-400">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                        <input required type="text" name="na" minlength="2" maxlength="100" class="w-full px-4 py-3 bg-transparent outline-none text-gray-700 placeholder-gray-400" placeholder="John Doe" value="<?php echo isset($_POST['na']) ? htmlspecialchars($_POST['na']) : ''; ?>">
                    </div>
                </div>

                <!-- Email Input -->
                <div class="space-y-2">
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider">Email Address</label>
                    <div class="input-group flex items-center border border-gray-200 rounded-xl bg-white/50 transition-all duration-200 overflow-hidden">
                        <div class="pl-4 text-gray-400">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <input required type="email" name="gm" maxlength="255" class="w-full px-4 py-3 bg-transparent outline-none text-gray-700 placeholder-gray-400" placeholder="name@example.com" value="<?php echo isset($_POST['gm']) ? htmlspecialchars($_POST['gm']) : ''; ?>">
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
                        <input required type="password" name="ps" minlength="8" class="w-full px-4 py-3 bg-transparent outline-none text-gray-700 placeholder-gray-400" placeholder="Min 8 chars, 1 uppercase, 1 number">
                    </div>
                    <p class="text-xs text-gray-500 mt-1">Must contain uppercase, lowercase, and number</p>
                </div>

                <!-- Submit Button -->
                <button type="submit" name="but" class="w-full bg-gradient-to-r from-emerald-600 to-emerald-500 text-white font-semibold rounded-xl py-3.5 shadow-lg shadow-emerald-500/30 hover:shadow-emerald-500/40 hover:-translate-y-0.5 transition-all duration-200 mt-2">
                    Create Account
                </button>
            </form>

            <!-- Divider -->
            <div class="relative my-8">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-gray-100"></div>
                </div>
                <div class="relative flex justify-center text-sm">
                    <span class="px-2 bg-white text-gray-400 bg-opacity-95">Already have an account?</span>
                </div>
            </div>

            <!-- Login Link -->
            <div class="text-center">
                <a href="loginform.php" class="inline-flex items-center text-emerald-600 hover:text-emerald-700 font-semibold transition-colors">
                     Login here
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                    </svg>
                </a>
            </div>
        </div>
    </div>
    
    <?php if(isset($error_message)): ?>
    <div class='fixed top-4 right-4 bg-red-100 border border-red-200 text-red-700 px-4 py-3 rounded-xl shadow-lg flex items-center animate-bounce-in z-50 max-w-md' role='alert'>
        <svg class='w-5 h-5 mr-2 flex-shrink-0' fill='currentColor' viewBox='0 0 20 20'><path fill-rule='evenodd' d='M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z' clip-rule='evenodd'></path></svg>
        <span class='block sm:inline'><?php echo $error_message; ?></span>
    </div>
    <script>
        setTimeout(() => { const alert = document.querySelector('[role="alert"]'); if(alert) alert.remove(); }, 6000);
    </script>
    <?php endif; ?>

</body>
</html>
