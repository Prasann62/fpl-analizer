<?php
    if(isset($_POST['but'])) {
        $name  = $_POST['na'];
        $email = $_POST['gm'];
        $psw   = $_POST['ps'];

        $servername = "localhost";
        $username   = "u913997673_prasanna";
        $password   = "Ko%a/2klkcooj]@o";
        $dbname     = "u913997673_prasanna";

        $conn = new mysqli($servername,$username,$password,$dbname);

        if($conn->connect_error){
            die("connection failed:".$conn->connect_error);
        }

        $sql="INSERT INTO signin (name,email,password) values('".$name."','".$email."','".$psw."')";

        if ($conn->query($sql)=== TRUE){
            echo "<script>window.open('loginform.php','_self')</script>";
        } else{
             $error_message = "Registration Failed. Please try again.";
        }

        $conn->close();
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
                
                <!-- Name Input -->
                <div class="space-y-2">
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider">Full Name</label>
                    <div class="input-group flex items-center border border-gray-200 rounded-xl bg-white/50 transition-all duration-200 overflow-hidden">
                        <div class="pl-4 text-gray-400">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                        <input required type="text" name="na" class="w-full px-4 py-3 bg-transparent outline-none text-gray-700 placeholder-gray-400" placeholder="John Doe">
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
                        <input required type="email" name="gm" class="w-full px-4 py-3 bg-transparent outline-none text-gray-700 placeholder-gray-400" placeholder="name@example.com">
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
                        <input required type="password" name="ps" class="w-full px-4 py-3 bg-transparent outline-none text-gray-700 placeholder-gray-400" placeholder="At least 6 characters">
                    </div>
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
                    Sign in here
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                    </svg>
                </a>
            </div>
        </div>
    </div>
    
    <?php if(isset($error_message)): ?>
    <div class='fixed top-4 right-4 bg-red-100 border border-red-200 text-red-700 px-4 py-3 rounded-xl shadow-lg flex items-center animate-bounce-in z-50' role='alert'>
        <span class='block sm:inline'><?php echo $error_message; ?></span>
    </div>
    <script>
        setTimeout(() => { document.querySelector('[role="alert"]').remove(); }, 4000);
    </script>
    <?php endif; ?>

</body>
</html>
