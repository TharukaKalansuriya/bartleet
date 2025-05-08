<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>BC-Agro Tronics Dashboard</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.11.4/gsap.min.js"></script>
  <style>
    .glass-effect {
      backdrop-filter: blur(5px);
      -webkit-backdrop-filter: blur(5px);
      background: rgba(255, 255, 255, 0.35);
      border-bottom: 1px solid rgba(255, 255, 255, 0.2);
      box-shadow: 0 2px 4px -1px rgba(0, 0, 0, 0.05);
    }
    
    .nav-link {
      position: relative;
    }
    
    .nav-link::after {
      content: '';
      position: absolute;
      width: 0;
      height: 2px;
      bottom: -4px;
      left: 0;
      background-color: #dc2626;
      transition: width 0.3s ease;
    }
    
    .nav-link:hover::after {
      width: 100%;
    }
    
    .active::after {
      width: 100%;
    }
  </style>
</head>
<body class="bg-gradient-to-r from-white to-red-100 min-h-screen font-sans">
  <!-- Navbar -->
  <nav class="fixed top-0 left-0 right-0 glass-effect px-4 py-3 z-50 transition-all duration-300">
    <div class="max-w-7xl mx-auto flex justify-between items-center">
      <div class="flex items-center">
        <img src="img/logo.png" alt="Logo" onclick="location.href='home.php'" class="h-12 md:h-14 lg:h-16 w-auto object-contain cursor-pointer transition-transform hover:scale-105" />
      </div>
      
      <!-- Hamburger Icon -->
      <div class="md:hidden">
        <button id="menu-button" class="text-red-600 focus:outline-none p-2 rounded-md hover:bg-red-50 transition-colors">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M4 6h16M4 12h16M4 18h16" />
          </svg>
        </button>
      </div>
      
      <!-- Desktop Links -->
      <div class="hidden md:flex items-center space-x-8">
        <div class="flex space-x-8 font-medium text-red-600 text-sm lg:text-base items-center">
          <a href="home.php" class="nav-link active hover:text-red-700 transition-colors">HOME</a>
          <a href="data.php" class="nav-link hover:text-red-700 transition-colors">VIEW EVALUATION</a>
        </div>
        <div class="pl-6 border-l border-red-100">
          <span id="datetime" class="text-gray-600 text-xs lg:text-sm font-medium"></span>
        </div>
      </div>
    </div>
    
    <!-- Mobile Menu -->
    <div id="mobile-menu" class="hidden md:hidden mt-3 pb-2 border-t border-red-50">
      <div class="flex flex-col space-y-2 pt-2 font-medium text-red-600 text-sm">
        <a href="home.php" class="py-2 px-2 hover:bg-red-50/50 rounded-md transition-colors">HOME</a>
        <a href="data.php" class="py-2 px-2 hover:bg-red-50/50 rounded-md transition-colors">VIEW EVALUATION</a>
        <div class="text-gray-600 text-xs font-medium pt-2 px-2">
          <span id="mobile-datetime"></span>
        </div>
      </div>
    </div>
  </nav>
  

  <div class="pt-24 px-4 md:pt-28 lg:pt-32">
    <div class="max-w-7xl mx-auto">
    
    </div>
  </div>
  
  <script>
    // Toggle menu with animation
    const menuButton = document.getElementById('menu-button');
    const mobileMenu = document.getElementById('mobile-menu');
    
    menuButton.addEventListener('click', () => {
      if (mobileMenu.classList.contains('hidden')) {
        mobileMenu.classList.remove('hidden');
        gsap.from(mobileMenu, {height: 0, opacity: 0, duration: 0.3, ease: "power2.out"});
      } else {
        gsap.to(mobileMenu, {
          height: 0, 
          opacity: 0, 
          duration: 0.3, 
          ease: "power2.in",
          onComplete: () => {
            mobileMenu.classList.add('hidden');
            gsap.set(mobileMenu, {height: "auto", opacity: 1});
          }
        });
      }
    });
    
    // Scroll effect for navbar
    window.addEventListener('scroll', () => {
      const navbar = document.querySelector('nav');
      if (window.scrollY > 50) {
        navbar.classList.add('py-2');
        navbar.classList.remove('py-3');
      } else {
        navbar.classList.add('py-3');
        navbar.classList.remove('py-2');
      }
    });
    
    // DateTime 
    function updateDateTime() {
      const now = new Date();
      const options = {
        weekday: 'short',
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit',
        hour12: true
      };
      const formattedTime = now.toLocaleString('en-US', options);
      document.getElementById('datetime').innerText = formattedTime;
      document.getElementById('mobile-datetime').innerText = formattedTime;
    }
    
    setInterval(updateDateTime, 1000);
    updateDateTime();
    
    // Set active link based on current page
    document.addEventListener('DOMContentLoaded', () => {
      const currentPath = window.location.pathname;
      const navLinks = document.querySelectorAll('.nav-link');
      
      navLinks.forEach(link => {
        const href = link.getAttribute('href');
        if (currentPath.endsWith(href)) {
          link.classList.add('active');
        } else {
          link.classList.remove('active');
        }
      });
    });
  </script>
</body>
</html>