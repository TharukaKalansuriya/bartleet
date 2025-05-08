<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include the connection
require_once 'database.php';

// Initialize the Database class
$db = new Database();

// Initialize error message
$errorMessage = '';

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $role = trim($_POST['role']);
    $password = trim($_POST['password']);

    // Load passwords from file
    $passwordFile = 'rolepasswords.php';
    if (file_exists($passwordFile)) {
        $rolePasswords = include($passwordFile);
    } else {
        // Fallback to default passwords if file doesn't exist
        $rolePasswords = [
            'admin' => 'admin',
            'manager' => 'manager',
            'data_entry' => 'data',
        ];
    }

    // Check if selected role exists
    if (array_key_exists($role, $rolePasswords)) {
        // Compare entered password with role-specific password
        if ($password === $rolePasswords[$role]) {
            // Store user details in session (assuming other details are not required)
            $_SESSION['role'] = $role;

            // Redirect based on role
            switch ($role) {
                case 'admin':
                    header("Location: admindashboard.php");
                    break;
                case 'manager':
                    header("Location: home.php");
                    break;
                case 'data_entry':
                    header("Location: dataentrydb.php");
                   
            }
            exit();
        } else {
            $errorMessage = "Incorrect password for selected role.";
        }
    } else {
        $errorMessage = "Invalid role selected.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | BC-Agro Tronics</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.11.4/gsap.min.js"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        
        body {
            font-family: 'Inter', sans-serif;
            background: url('img/background.jpg') no-repeat center center fixed;
            background-size: cover;
        }
        
        .glass-card {
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            background: rgba(255, 255, 255, 0.6);
            border: 1px solid rgba(255, 255, 255, 0.3);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        }
        
        .input-field {
            background: rgba(255, 255, 255, 0.7);
            border: 1px solid rgba(200, 200, 200, 0.5);
            transition: all 0.3s ease;
        }
        
        .input-field:focus {
            background: rgba(255, 255, 255, 0.95);
            box-shadow: 0 0 0 2px rgba(220, 38, 38, 0.2);
        }
        
        .login-btn {
            background: linear-gradient(135deg, #dc2626 0%, #ea580c 100%);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        
        .login-btn:hover {
            background: linear-gradient(135deg, #b91c1c 0%, #c2410c 100%);
            transform: translateY(-1px);
        }
        
        .login-btn:active {
            transform: translateY(1px);
        }
        
        #loader {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: #000;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 9999;
            transition: opacity 0.8s ease, visibility 0.8s ease;
        }

        #loader.fade-out {
            opacity: 0;
            visibility: hidden;
        }

        body.loading {
            overflow: hidden;
        }
        
        .animate-in {
            animation: fadeIn 0.8s ease forwards;
            opacity: 0;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .role-option {
            transition: all 0.2s ease;
        }
        
        .role-option:hover {
            background-color: rgba(220, 38, 38, 0.1);
        }
    </style>
</head>
<body class="flex items-center justify-center min-h-screen loading">
    <!-- Loader -->
    <div id="loader">
        <img src="img/loading.gif" alt="Loading...">
    </div>

    <div id="content" class="w-full max-w-md p-2" style="display: none;">
        <div class="glass-card rounded-2xl overflow-hidden">
            <!-- Top Decorative Bar -->
            <div class="h-2 bg-gradient-to-r from-red-600 to-orange-500"></div>
            
            <div class="p-8">
                <!-- Logo Section -->
                <div class="flex justify-center mb-6">
                    <img src="img/logo.png" alt="BC-Agro Tronics Logo" class="h-16 object-contain animate-in" style="animation-delay: 0.1s;">
                </div>
                
                <!-- Title Section -->
                <div class="text-center animate-in" style="animation-delay: 0.2s;">
                    <h2 class="text-2xl font-bold text-gray-800">Welcome to</h2>
                    <div class="flex justify-center items-baseline space-x-1 mt-1">
                        <span class="text-red-600 text-3xl font-bold">BC</span>
                        <span class="text-yellow-700 text-2xl font-semibold">Agro-Tronics</span>
                    </div>
                    <p class="text-gray-500 text-sm mt-2">Please sign in to continue</p>
                </div>

                <!-- Error Message -->
                <?php if (!empty($errorMessage)): ?>
                    <div class="mt-4 p-3 bg-red-50 border-l-4 border-red-500 text-red-700 text-sm rounded-md animate-in" style="animation-delay: 0.3s;">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                            </svg>
                            <?php echo $errorMessage; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Login Form -->
                <form action="index.php" method="POST" class="space-y-5 mt-6">
                    <div class="animate-in" style="animation-delay: 0.4s;">
                        <label for="role" class="block text-sm font-medium text-gray-700 mb-1 ml-1">Select Role</label>
                        <div class="relative">
                            <select id="role" name="role" required
                                class="input-field w-full px-4 py-3 rounded-xl appearance-none focus:outline-none focus:ring-2 focus:ring-red-500 text-gray-700">
                                <option value="" disabled selected class="text-gray-500">Select your role</option>
                                <option value="admin" class="role-option py-1">Admin</option>
                                <option value="manager" class="role-option py-1">Manager</option>
                                <option value="data_entry" class="role-option py-1">Data Entry</option>
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-gray-500">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                    
                    <div class="animate-in" style="animation-delay: 0.5s;">
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-1 ml-1">Password</label>
                        <div class="relative">
                            <input type="password" id="password" name="password" required
                                class="input-field w-full px-4 py-3 rounded-xl focus:outline-none focus:ring-2 focus:ring-red-500 text-gray-700"
                                placeholder="Enter your password">
                            <div class="absolute inset-y-0 right-0 flex items-center px-3 text-gray-500">
                                <svg id="togglePassword" class="w-5 h-5 cursor-pointer hover:text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                    
                    <div class="pt-2 animate-in" style="animation-delay: 0.6s;">
                        <button type="submit" class="login-btn w-full text-white font-medium py-3 rounded-xl shadow-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2">
                            Sign In
                        </button>
                    </div>
                </form>
                
                <div class="text-center mt-6 text-xs text-gray-500 animate-in" style="animation-delay: 0.7s;">
                    BC Agro-Tronics © 2025 | All Rights Reserved
                </div>
            </div>
        </div>
    </div>

    <script>
        // Loading animation
        window.addEventListener('load', function() {
            document.body.classList.remove('loading'); 
            const loader = document.getElementById('loader');

            // Add fade-out class
            loader.classList.add('fade-out');

            // Remove loader after fade-out
            setTimeout(function() {
                loader.style.display = 'none';
                document.getElementById('content').style.display = 'block';
                
                // Start GSAP animations
                gsap.from(".glass-card", {
                    duration: 0.8,
                    y: 20,
                    opacity: 0,
                    ease: "power3.out"
                });
            }, 800);
        });
        
        // Toggle password visibility
        document.getElementById('togglePassword').addEventListener('click', function() {
            const passwordInput = document.getElementById('password');
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            
            // Change icon based on password visibility
            if (type === 'text') {
                this.innerHTML = `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21">`;
            } else {
                this.innerHTML = `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>`;
            }
        });
        
        // Focus animation for form fields
        const formFields = document.querySelectorAll('.input-field');
        formFields.forEach(field => {
            field.addEventListener('focus', () => {
                gsap.to(field, {
                    duration: 0.3,
                    scale: 1.01,
                    ease: "power1.out"
                });
            });
            
            field.addEventListener('blur', () => {
                gsap.to(field, {
                    duration: 0.3,
                    scale: 1,
                    ease: "power1.in"
                });
            });
        });
    </script>
</body>
</html>