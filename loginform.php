<?php
    session_start();
    if(isset($_POST['login_btn'])) {
        $email = $_POST['email'];
        $password = $_POST['password'];

        $servername = "localhost";
        $username   = "u913997673_prasanna";
        $password_db   = "Ko%a/2klkcooj]@o";
        $dbname     = "u913997673_prasanna";

        $conn = new mysqli($servername, $username, $password_db, $dbname);

        if($conn->connect_error){
            die("connection failed:".$conn->connect_error);
        }

        // Use prepared statement for security
        $stmt = $conn->prepare("SELECT * FROM signin WHERE email = ? AND password = ?");
        $stmt->bind_param("ss", $email, $password);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0){
            $_SESSION['access'] = true;
            $_SESSION['email'] = $email;
            echo "<script>window.open('Dashboard.php','_self')</script>";
            exit();
        } else{
            // We'll store the error in a variable to display it later in the HTML
            $error_message = "Invalid Email or Password";
        }

        $stmt->close();
        $conn->close();
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
  <link href="style.css?v=<?php echo time(); ?>" rel="stylesheet">
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
                
                <!-- Email Input -->
                <div class="space-y-2">
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider">Email Address</label>
                    <div class="input-group flex items-center border border-gray-200 rounded-xl bg-white/50 transition-all duration-200 overflow-hidden">
                        <div class="pl-4 text-gray-400">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <input required type="email" name="email" class="w-full px-4 py-3 bg-transparent outline-none text-gray-700 placeholder-gray-400" placeholder="name@example.com">
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
            &copy; <?php echo date('Y'); ?> FPL Manager. All rights reserved.
        </p>
    </div>

    <?php if(isset($error_message)): ?>
    <div class='fixed top-4 right-4 bg-red-100 border border-red-200 text-red-700 px-4 py-3 rounded-xl shadow-lg flex items-center animate-bounce-in z-50' role='alert'>
        <svg class='w-5 h-5 mr-2' fill='currentColor' viewBox='0 0 20 20'><path fill-rule='evenodd' d='M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z' clip-rule='evenodd'></path></svg>
        <span class='block sm:inline'><?php echo $error_message; ?></span>
    </div>
    <script>
        setTimeout(() => { document.querySelector('[role="alert"]').remove(); }, 4000);
    </script>
    <?php endif; ?>

</body>
</html>
