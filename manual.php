<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BC–Agro Tronics User Manual</title>
    <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700;900&family=Orbitron:wght@500&display=swap" rel="stylesheet" />
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
        .glass {
            background: rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        .glow-btn {
            box-shadow: 0 0 15px rgba(255, 0, 0, 0.75), 0 0 30px rgba(255, 0, 0, 0.4);
        }
        .glow-btn2 {
            box-shadow: 0 0 15px rgba(145, 255, 0, 0.57), 0 0 30px rgba(178, 245, 22, 0.51);
        }
        .neon-text {
            text-shadow: 0 0 5px rgba(253, 89, 89, 0.46), 0 0 10px rgba(253, 71, 71, 0.4);
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
        .sidebar-item {
            transition: all 0.3s ease;
        }
        .sidebar-item:hover, .sidebar-item.active {
            background: rgba(255, 255, 255, 0.1);
            transform: translateX(5px);
        }
        .img-placeholder {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
    </style>
</head>
<body class="min-h-screen font-sans bg-cover bg-no-repeat bg-center" style="background-image: linear-gradient(to left, rgba(24, 63, 45, 0.78),rgba(9, 8, 8, 0.81)), url('img/home.png');">
   
<!-- Back Button with Animation -->
   <div class="fixed top-8 right-8 z-50">
    <button onclick="history.back()" class="bg-white/10 backdrop-blur-md p-3 rounded-full shadow-lg hover:bg-white/20 transition-all duration-300">
      <i class="fas fa-arrow-left text-white text-xl"></i>
    </button>
  </div>
<!-- Loader -->
    <div id="loader">
        <img src="img/loading.gif" alt="Loading...">
    </div>

    <div class="flex flex-col md:flex-row">
        <!-- Sidebar -->
        <div class="w-full md:w-64 glass text-white md:h-screen md:fixed">
            <div class="p-6">
                <h3 class="text-2xl font-bold neon-text mb-6">BC–Agro Tronics</h3>
                <h4 class="text-xl text-red-400 mb-4">User Manual</h4>
                
                <div class="mb-6">
                    <input type="text" placeholder="Search..." class="w-full bg-transparent border border-gray-600 rounded p-2 text-white focus:outline-none focus:border-red-400">
                </div>
                
                <ul class="space-y-1">
                    <li class="sidebar-item active rounded-md p-3" data-section="introduction">Introduction</li>
                    <li class="sidebar-item rounded-md p-3" data-section="getting-started">Getting Started</li>
                    <li class="sidebar-item rounded-md p-3" data-section="dashboard">Dashboard</li>
                    <li class="sidebar-item rounded-md p-3" data-section="features">Key Features</li>
                    <li class="sidebar-item rounded-md p-3" data-section="troubleshooting">Troubleshooting</li>
                    <li class="sidebar-item rounded-md p-3" data-section="faq">FAQ</li>
                </ul>
            </div>
        </div>

        <!-- Main Content -->
        <div class="w-full md:ml-64 p-6 md:p-10">
            <div class="section active" id="introduction">
                <h1 class="text-4xl md:text-5xl font-bold text-white neon-text mb-8">Introduction</h1>
                <div class="glass p-6 rounded-xl">
                    <p class="text-gray-300 mb-4">Welcome to the User Manual for BC–Agro Tronics. This guide will walk you through all the features and functionalities of our website, ensuring you get the most out of your experience.</p>
                    <p class="text-gray-300 mb-6">This manual is designed for all user levels, from beginners to advanced users. It provides step-by-step instructions, helpful tips, and answers to frequently asked questions.</p>
                    
                    
                    
                    <h2 class="text-2xl font-bold text-red-400 mt-8 mb-4">How to Use This Manual</h2>
                    <p class="text-gray-300 mb-4">Navigate through the manual using the sidebar on the left. Click on any topic to view its content. You can also use the search bar to find specific information quickly.</p>
                    <p class="text-gray-300">If you need additional support, please contact our customer service team at support@bcagrotronics.com.</p>
                </div>
            </div>

            <div class="section hidden" id="getting-started">
                <h1 class="text-4xl md:text-5xl font-bold text-white neon-text mb-8">Getting Started</h1>
                <div class="glass p-6 rounded-xl">
                    <h2 class="text-2xl font-bold text-red-400 mb-4">Creating an Account</h2>
                    <p class="text-gray-300 mb-4">To start using our website, you'll need to have a role password:</p>
                    <ol class="list-decimal list-inside text-gray-300 mb-6 ml-4 space-y-2">
                        <li>Passwords and roles are managed by managers and site admins.</li>
                        <li>Provide your details and documents and grant the permissions.</li>
                        <li>After successfull user registration, they will provide you a password.</li>
                        <li>If any error occured, please contact the developer, tharukakalansuriya@gmail.com.</li>
                    </ol>
                    
                    
                    
                    <h2 class="text-2xl font-bold text-red-400 mt-8 mb-4">Logging In</h2>
                    <p class="text-gray-300 mb-4">Once you have an account, you can log in by:</p>
                    <ol class="list-decimal list-inside text-gray-300 mb-4 ml-4 space-y-2">
                        <li>Select your role.</li>
                        <li>Enter the password.</li>
                        <li>Click "Log In" to access your account.</li>
                    </ol>
                    <p class="text-gray-300">For security reasons, we recommend logging out when you're finished using the website, especially on shared devices.</p>
                </div>
            </div>

            <div class="section hidden" id="dashboard">
                <h1 class="text-4xl md:text-5xl font-bold text-white neon-text mb-8">Dashboard</h1>
                <div class="glass p-6 rounded-xl">
                    <p class="text-gray-300 mb-6">The dashboard is your central hub for navigating our website. From here, you can access all the main features and authenticated functions.</p>
                    
                    
                    <h2 class="text-2xl font-bold text-red-400 mt-8 mb-4">Dashboard Layout</h2>
                    <p class="text-gray-300 mb-4">Your dashboard is organized into the following sections:</p>
                    <ul class="list-disc list-inside text-gray-300 mb-6 ml-4 space-y-2">
                        <li><span class="text-red-400 font-semibold">LogOut:</span> End your session and kep your accound safe.</li>
                        <li><span class="text-red-400 font-semibold">View Website:</span> Brows to the website.</li>
                        <li><span class="text-red-400 font-semibold">Add Data:</span> Go the data adding page.</li>
                        <li><span class="text-red-400 font-semibold">User Manual:</span> Go to the user manual.</li>
                    </ul>
                    
                </div>
            </div>

            <div class="section hidden" id="features">
                <h1 class="text-4xl md:text-5xl font-bold text-white neon-text mb-8">Key Features</h1>
                <div class="glass p-6 rounded-xl">
                    <h2 class="text-2xl font-bold text-red-400 mb-4">Feature 1: Color Sorters</h2>
                    <p class="text-gray-300 mb-4">Our advanced color sorting technology allows for precise separation of agricultural products based on color, ensuring the highest quality output.</p>
                    
                    <div class="img-placeholder rounded-lg p-4 text-center mb-6">
                        <img src="/api/placeholder/500/250" alt="Feature 1">
                    </div>
                    
                    <p class="text-gray-300 mb-4">How to use this feature:</p>
                    <ol class="list-decimal list-inside text-gray-300 mb-6 ml-4 space-y-2">
                        <li>Navigate to the Color Sorters section from your dashboard</li>
                        <li>Select the specific sorter you wish to monitor or configure</li>
                        <li>Adjust parameters based on your product requirements</li>
                    </ol>
                    
                    <h2 class="text-2xl font-bold text-red-400 mt-8 mb-4">Feature 2: Compressor Management</h2>
                    <p class="text-gray-300 mb-4">Monitor and control your industrial compressors remotely, with real-time performance metrics and maintenance alerts.</p>
                    
                    <div class="img-placeholder rounded-lg p-4 text-center mb-6">
                        <img src="/api/placeholder/500/250" alt="Feature 2">
                    </div>
                    
                    <p class="text-gray-300 mb-4">How to use this feature:</p>
                    <ol class="list-decimal list-inside text-gray-300 mb-6 ml-4 space-y-2">
                        <li>Access the Compressor dashboard from the main menu</li>
                        <li>View real-time data on pressure, temperature, and efficiency</li>
                        <li>Set up automated alerts for preventative maintenance</li>
                    </ol>
                    
                    <h2 class="text-2xl font-bold text-red-400 mt-8 mb-4">Feature 3: Factory Overview</h2>
                    <p class="text-gray-300 mb-4">Get a comprehensive view of all your connected factories, with performance metrics and production data in one convenient dashboard.</p>
                    
                    <div class="img-placeholder rounded-lg p-4 text-center mb-6">
                        <img src="/api/placeholder/500/250" alt="Feature 3">
                    </div>
                    
                    <p class="text-gray-300 mb-4">How to use this feature:</p>
                    <ol class="list-decimal list-inside text-gray-300 mb-4 ml-4 space-y-2">
                        <li>Select "Factory Overview" from the main navigation</li>
                        <li>Choose a specific factory to see detailed information</li>
                        <li>Export reports or set up scheduled email updates</li>
                    </ol>
                </div>
            </div>

            <div class="section hidden" id="troubleshooting">
                <h1 class="text-4xl md:text-5xl font-bold text-white neon-text mb-8">Troubleshooting</h1>
                <div class="glass p-6 rounded-xl">
                    <h2 class="text-2xl font-bold text-red-400 mb-6">Common Issues and Solutions</h2>
                    
                    <h3 class="text-xl font-semibold text-white mb-3">Issue: Unable to Log In</h3>
                    <p class="text-gray-300 mb-4">If you're having trouble logging in, try these solutions:</p>
                    <ul class="list-disc list-inside text-gray-300 mb-6 ml-4 space-y-2">
                        <li>Ensure you're using the correct email address and password.</li>
                        <li>Check if Caps Lock is turned on.</li>
                        <li>Clear your browser cache and cookies, then try again.</li>
                        <li>Use the "Forgot Password" option to reset your password.</li>
                        <li>If problems persist, contact support at support@bcagrotronics.com.</li>
                    </ul>
                    
                    <h3 class="text-xl font-semibold text-white mt-8 mb-3">Issue: Pages Loading Slowly</h3>
                    <p class="text-gray-300 mb-4">If the website is running slowly:</p>
                    <ul class="list-disc list-inside text-gray-300 mb-6 ml-4 space-y-2">
                        <li>Check your internet connection.</li>
                        <li>Clear your browser cache.</li>
                        <li>Close unnecessary browser tabs or applications.</li>
                        <li>Try using a different browser.</li>
                        <li>Disable browser extensions that might be interfering.</li>
                    </ul>
                    
                    <h3 class="text-xl font-semibold text-white mt-8 mb-3">Issue: Device Connectivity Problems</h3>
                    <p class="text-gray-300 mb-4">If a specific feature isn't working properly:</p>
                    <ul class="list-disc list-inside text-gray-300 mb-4 ml-4 space-y-2">
                        <li>Refresh the page.</li>
                        <li>Check if your device has proper network connectivity.</li>
                        <li>Verify that all hardware is powered on and connected.</li>
                        <li>Check our system status page for any known issues.</li>
                        <li>Contact technical support with details of your setup and any error messages.</li>
                    </ul>
                </div>
            </div>

            <div class="section hidden" id="faq">
                <h1 class="text-4xl md:text-5xl font-bold text-white neon-text mb-8">Frequently Asked Questions</h1>
                <div class="glass p-6 rounded-xl">
                    <h2 class="text-2xl font-bold text-red-400 mb-6">General Questions</h2>
                    
                    <h3 class="text-xl font-semibold text-white mb-3">Q: How much does it cost to use your services?</h3>
                    <p class="text-gray-300 mb-6">A: We offer several pricing tiers, including customized enterprise solutions. Contact our sales team for detailed information on features and costs for each plan.</p>
                    
                    <h3 class="text-xl font-semibold text-white mb-3">Q: Is my data secure?</h3>
                    <p class="text-gray-300 mb-6">A: Yes, we take data security seriously. We use industry-standard encryption and security practices to protect your information. For more details, please review our Privacy Policy.</p>
                    
                    <h3 class="text-xl font-semibold text-white mb-3">Q: Can I access the system on mobile devices?</h3>
                    <p class="text-gray-300 mb-6">A: Yes, our platform is fully responsive and works on smartphones and tablets. We also offer dedicated mobile apps for iOS and Android for field technicians.</p>
                    
                    <h2 class="text-2xl font-bold text-red-400 mt-8 mb-6">Account Questions</h2>
                    
                    <h3 class="text-xl font-semibold text-white mb-3">Q: How do I add users to my account?</h3>
                    <p class="text-gray-300 mb-6">A: To add users, go to Account Settings > Team Management, then click "Add User". You can set appropriate permission levels for each team member you add.</p>
                    
                    <h3 class="text-xl font-semibold text-white mb-3">Q: Can I have multiple factories on one account?</h3>
                    <p class="text-gray-300 mb-6">A: Yes, our Enterprise plans support multiple factories with consolidated reporting. You can manage all locations from a single dashboard.</p>
                    
                    <h2 class="text-2xl font-bold text-red-400 mt-8 mb-6">Technical Questions</h2>
                    
                    <h3 class="text-xl font-semibold text-white mb-3">Q: Which browsers do you support?</h3>
                    <p class="text-gray-300 mb-6">A: We support the latest versions of Chrome, Firefox, Safari, and Edge. For the best experience, we recommend keeping your browser updated.</p>
                    
                    <h3 class="text-xl font-semibold text-white mb-3">Q: How often is system maintenance performed?</h3>
                    <p class="text-gray-300 mb-4">A: We schedule system maintenance during off-peak hours, typically on weekends. All users will receive advance notification of any planned downtime.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Include footer -->
    <?php include 'footer.php'; ?>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Navigation functionality
            const navItems = document.querySelectorAll('.sidebar-item');
            
            navItems.forEach(item => {
                item.addEventListener('click', function() {
                    // Remove active class from all items
                    navItems.forEach(navItem => navItem.classList.remove('active'));
                    
                    // Add active class to clicked item
                    this.classList.add('active');
                    
                    // Hide all sections
                    const sections = document.querySelectorAll('.section');
                    sections.forEach(section => {
                        section.classList.add('hidden');
                        section.classList.remove('active');
                    });
                    
                    // Show selected section
                    const sectionToShow = document.getElementById(this.dataset.section);
                    if (sectionToShow) {
                        sectionToShow.classList.remove('hidden');
                        sectionToShow.classList.add('active');
                    }
                });
            });
            
            // Search functionality
            const searchInput = document.querySelector('input[type="text"]');
            
            searchInput.addEventListener('keyup', function() {
                const searchTerm = this.value.toLowerCase();
                if (searchTerm.length > 2) {
                    alert('Search for: ' + searchTerm + '\n\nIn a real implementation, this would show matching results.');
                }
            });

            // Loader animation
            window.addEventListener('load', function () {
                document.body.classList.remove('loading'); 
                const loader = document.getElementById('loader');

                // Add fade-out class
                loader.classList.add('fade-out');

                setTimeout(function () {
                    loader.style.display = 'none';
                }, 800); 
            });
        });
    </script>
</body>
</html>