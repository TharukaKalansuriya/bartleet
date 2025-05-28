<?php
// Destroy all existing sessions when page loads
session_start();
session_unset();
session_destroy();
session_start(); // Start fresh session

// Clear browser history and prevent back navigation
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

// Prevent referrer information
header("Referrer-Policy: no-referrer");

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
            'repair' => '922470',
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
                case 'repair':
                    header("Location: admindashboard.php");
                    break;
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
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        'inter': ['Inter', 'sans-serif'],
                    },
                    backdropBlur: {
                        xs: '2px',
                    },
                    animation: {
                        'fade-in': 'fadeIn 0.8s ease forwards',
                        'fade-out': 'fadeOut 0.8s ease forwards',
                    },
                    keyframes: {
                        fadeIn: {
                            'from': { opacity: '0', transform: 'translateY(10px)' },
                            'to': { opacity: '1', transform: 'translateY(0)' }
                        },
                        fadeOut: {
                            'from': { opacity: '1' },
                            'to': { opacity: '0' }
                        }
                    }
                }
            }
        }
    </script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            background: url('img/background.jpg') no-repeat center center fixed;
            background-size: cover;
        }
    </style>
</head>
<body class="font-inter flex items-center justify-center min-h-screen overflow-hidden">
    <!-- Loader -->
    <div id="loader" class="fixed top-0 left-0 w-full h-full bg-gradient-to-br from-gray-900 via-gray-800 to-black flex items-center justify-center z-50 transition-all duration-1000 ease-in-out">
        <div class="text-center">
            <img src="img/loading.gif" alt="Loading..." class="max-w-xs max-h-xs mx-auto mb-6">
            <div class="text-white text-lg font-medium animate-pulse">
                Initializing BC Agro-Tronics System...
            </div>
        </div>
    </div>

    <!-- Welcome Screen -->
    <div id="welcome-screen" class="fixed top-0 left-0 w-full h-full bg-gradient-to-br from-orange-700 via-red-700 to-orange-700 flex items-center justify-center z-40 opacity-0 invisible transition-all duration-1000 ease-in-out">
        <div class="text-center text-white px-8">
            <div class="mb-8">
                <img src="img/logo.png" alt="BC-Agro Tronics Logo" class="h-24 mx-auto mb-6 opacity-0" id="welcome-logo">
            </div>
            <h1 class="text-5xl font-bold mb-4 opacity-0" id="welcome-title">Welcome to</h1>
            <div class="flex justify-center items-baseline space-x-2 mb-6 opacity-0" id="welcome-brand">
                <span class="text-6xl font-bold text-white">BC</span>
                <span class="text-6xl font-semibold text-orange-200">Agro-Tronics</span>
            </div>
            <p class="text-2xl font-light mb-8 opacity-0" id="welcome-subtitle">Management Dashboard</p>
            <div class="w-24 h-1 bg-white/50 mx-auto opacity-0" id="welcome-line"></div>
            <p class="text-lg font-light mt-6 text-orange-100 opacity-0" id="welcome-tagline">
                To Connect To Build
            </p>
        </div>
    </div>

    <div id="content" class="w-full max-w-md p-2 hidden">
        <div class="backdrop-blur-2xl bg-white/60 border border-white/30 shadow-2xl rounded-2xl overflow-hidden">
            <!-- Top Decorative Bar -->
            <div class="h-2 bg-gradient-to-r from-red-600 to-orange-500"></div>
            
            <div class="p-8">
                <!-- Logo Section -->
                <div class="flex justify-center mb-6">
                    <img src="img/logo.png" alt="BC-Agro Tronics Logo" class="h-16 object-contain opacity-0 animate-fade-in" style="animation-delay: 0.1s;">
                </div>
                
                <!-- Title Section -->
                <div class="text-center opacity-0 animate-fade-in" style="animation-delay: 0.2s;">
                    <h2 class="text-2xl font-bold text-gray-800">Access Your Dashboard</h2>
                    <div class="flex justify-center items-baseline space-x-1 mt-1">
                        <span class="text-red-600 text-3xl font-bold">BC</span>
                        <span class="text-red-600 text-3xl font-semibold">Agro-Tronics</span>
                    </div>
                    <p class="text-gray-500 text-sm mt-2">Enter your credentials to continue</p>
                </div>

                <!-- Error Message -->
                <?php if (!empty($errorMessage)): ?>
                    <div class="mt-4 p-3 bg-red-50 border-l-4 border-red-500 text-red-700 text-sm rounded-md opacity-0 animate-fade-in" style="animation-delay: 0.3s;">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-2 fill-current" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                            </svg>
                            <?php echo $errorMessage; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Login Form -->
                <form action="index.php" method="POST" class="space-y-5 mt-6">
                    <div class="opacity-0 animate-fade-in" style="animation-delay: 0.4s;">
                        <label for="role" class="block text-sm font-medium text-gray-700 mb-1 ml-1">Select Role</label>
                        <div class="relative">
                            <select id="role" name="role" required
                                class="w-full px-4 py-3 rounded-xl bg-white/70 border border-gray-200/50 focus:bg-white/95 focus:outline-none focus:ring-2 focus:ring-red-500 focus:shadow-lg focus:shadow-red-500/20 text-gray-700 appearance-none transition-all duration-300 ease-in-out">
                                <option value="" disabled selected class="text-gray-500">Select your role</option>
                                <option value="repair" class="py-1 hover:bg-red-600/10">Repair</option>
                                <option value="admin" class="py-1 hover:bg-red-600/10">Admin</option>
                                <option value="manager" class="py-1 hover:bg-red-600/10">Manager</option>
                                <option value="data_entry" class="py-1 hover:bg-red-600/10">Data Entry</option>
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-gray-500">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                    
                    <div class="opacity-0 animate-fade-in" style="animation-delay: 0.5s;">
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-1 ml-1">Password</label>
                        <div class="relative">
                            <input type="password" id="password" name="password" required
                                class="w-full px-4 py-3 rounded-xl bg-white/70 border border-gray-200/50 focus:bg-white/95 focus:outline-none focus:ring-2 focus:ring-red-500 focus:shadow-lg focus:shadow-red-500/20 text-gray-700 placeholder-gray-400 transition-all duration-300 ease-in-out"
                                placeholder="Enter your password">
                            <div class="absolute inset-y-0 right-0 flex items-center px-3 text-gray-500">
                                <svg id="togglePassword" class="w-5 h-5 cursor-pointer hover:text-gray-700 transition-colors duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                    
                    <div class="pt-2 opacity-0 animate-fade-in" style="animation-delay: 0.6s;">
                        <button type="submit" class="w-full bg-gradient-to-br from-red-600 to-orange-500 hover:from-red-700 hover:to-orange-600 text-white font-medium py-3 rounded-xl shadow-lg hover:shadow-xl hover:-translate-y-0.5 active:translate-y-0.5 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-all duration-300 ease-in-out relative overflow-hidden">
                            Sign In
                        </button>
                    </div>
                </form>
                
                <div class="text-center mt-6 text-xs text-gray-500 opacity-0 animate-fade-in" style="animation-delay: 0.7s;">
                    BC Agro-Tronics © 2025 | All Rights Reserved
                </div>
            </div>
        </div>
    </div>

    <script>
        // Prevent back navigation only (lightweight approach)
        (function() {
            // Simple history manipulation to prevent back navigation
            if (typeof history.pushState === "function") {
                history.pushState("preventBack", null, null);
                window.addEventListener('popstate', function() {
                    history.pushState('preventBack', null, null);
                });
            }
        })();

        // Enhanced loading and welcome animation sequence
        window.addEventListener('load', function() {
            document.body.classList.remove('overflow-hidden'); 
            const loader = document.getElementById('loader');
            const welcomeScreen = document.getElementById('welcome-screen');
            const content = document.getElementById('content');

            // Phase 1: Fade out loader after 2 seconds
            setTimeout(() => {
                loader.classList.add('opacity-0', 'invisible');
                
                // Phase 2: Show welcome screen
                setTimeout(() => {
                    loader.style.display = 'none';
                    welcomeScreen.classList.remove('opacity-0', 'invisible');
                    
                    // Animate welcome screen elements
                    animateWelcomeElements();
                    
                    // Phase 3: Show login form after welcome sequence
                    setTimeout(() => {
                        welcomeScreen.classList.add('opacity-0', 'invisible');
                        
                        setTimeout(() => {
                            welcomeScreen.style.display = 'none';
                            content.classList.remove('hidden');
                            
                            // Start login form animations
                            gsap.from(".backdrop-blur-2xl", {
                                duration: 1,
                                y: 30,
                                opacity: 0,
                                ease: "power3.out"
                            });
                        }, 1000);
                    }, 4000); // Show welcome screen for 4 seconds
                }, 500);
            }, 2000); // Show loader for 2 seconds
        });

        // Animate welcome screen elements sequentially
        function animateWelcomeElements() {
            const logo = document.getElementById('welcome-logo');
            const title = document.getElementById('welcome-title');
            const brand = document.getElementById('welcome-brand');
            const subtitle = document.getElementById('welcome-subtitle');
            const line = document.getElementById('welcome-line');
            const tagline = document.getElementById('welcome-tagline');

            // Sequential animations using GSAP
            const tl = gsap.timeline();
            
            tl.to(logo, { duration: 0.8, opacity: 1, y: 0, ease: "power2.out" })
              .to(title, { duration: 0.6, opacity: 1, y: 0, ease: "power2.out" }, "-=0.3")
              .to(brand, { duration: 0.8, opacity: 1, scale: 1, ease: "back.out(1.7)" }, "-=0.2")
              .to(subtitle, { duration: 0.6, opacity: 1, y: 0, ease: "power2.out" }, "-=0.3")
              .to(line, { duration: 0.5, opacity: 1, scaleX: 1, ease: "power2.out" }, "-=0.2")
              .to(tagline, { duration: 0.6, opacity: 1, y: 0, ease: "power2.out" }, "-=0.2");

            // Add subtle floating animation to logo
            gsap.to(logo, {
                duration: 3,
                y: -10,
                repeat: -1,
                yoyo: true,
                ease: "power1.inOut",
                delay: 1
            });
        }
        
        // Toggle password visibility
        document.getElementById('togglePassword').addEventListener('click', function() {
            const passwordInput = document.getElementById('password');
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            
            // Change icon based on password visibility
            if (type === 'text') {
                this.innerHTML = `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path>`;
            } else {
                this.innerHTML = `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>`;
            }
        });
        
        // Focus animation for form fields
        const formFields = document.querySelectorAll('input, select');
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

        // Add ripple effect to button
        document.querySelector('button[type="submit"]').addEventListener('click', function(e) {
            const button = e.currentTarget;
            const rect = button.getBoundingClientRect();
            const size = Math.max(rect.width, rect.height);
            const x = e.clientX - rect.left - size / 2;
            const y = e.clientY - rect.top - size / 2;
            
            const ripple = document.createElement('span');
            ripple.style.cssText = `
                position: absolute;
                width: ${size}px;
                height: ${size}px;
                left: ${x}px;
                top: ${y}px;
                background: rgba(255, 255, 255, 0.3);
                border-radius: 50%;
                transform: scale(0);
                animation: ripple 0.6s linear;
                pointer-events: none;
            `;
            
            button.appendChild(ripple);
            
            setTimeout(() => {
                ripple.remove();
            }, 600);
        });

        // Add ripple animation styles
        const style = document.createElement('style');
        style.textContent = `
            @keyframes ripple {
                to {
                    transform: scale(4);
                    opacity: 0;
                }
            }
        `;
        document.head.appendChild(style);
    </script>
</body>
</html>