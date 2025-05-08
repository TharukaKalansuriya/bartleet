<?php session_start();  // Define allowed roles $allowed_roles = ['admin', 'manager'];  // Check if the user's role is not in the allowed roles if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], $allowed_roles)) {          // Redirect to the login page if not authorized     header("Location: index.php");     exit(); }  ?> 
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>BC-Agro Tronics - Data View</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700;900&family=Montserrat:wght@300;400;500;600;700&display=swap');
    
    body {
      font-family: 'Montserrat', sans-serif;
    }
    
    .title-font {
      font-family: 'Montserrat', sans-serif;
    }
    
    .menu-card {
      transition: all 0.3s ease;
      background-size: 200% auto;
    }
    
    .menu-card:hover {
      background-position: right center;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
      transform: translateY(-5px);
    }
  </style>
</head>
<body class="min-h-screen bg-cover bg-fixed bg-center" style="background-image: linear-gradient(to right, rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.4)), url('img/background.jpg');">
  
  <!-- Back Button with Animation -->
  <div class="fixed top-8 right-8 z-50">
    <button onclick="history.back()" class="bg-white/10 backdrop-blur-md p-3 rounded-full shadow-lg hover:bg-white/20 transition-all duration-300">
      <i class="fas fa-arrow-left text-white text-xl"></i>
    </button>
  </div>

  <!-- Header with Logo and Title -->
  <header class="pt-12 pb-8 px-4">
    <div class="max-w-6xl mx-auto">
      <div class="backdrop-blur-md bg-black/30 rounded-2xl shadow-2xl p-6 flex flex-col md:flex-row items-center justify-center md:justify-start gap-6">
        <div class="bg-white/10 p-4 rounded-xl">
          <img src="img/logo.png" alt="Logo" class="w-28 h-20 md:w-32 md:h-24 object-contain" />
        </div>
        <div class="text-center md:text-left">
          <h1 class="title-font text-4xl md:text-5xl font-black text-white mb-2">BC–Agro Tronics</h1>
          <div class="flex items-center justify-center md:justify-start">
            <div class="h-0.5 w-12 bg-red-500 mr-3"></div>
            <p class="text-xl text-red-300 font-light tracking-wider">DATA VIEW</p>
          </div>
        </div>
      </div>
    </div>
  </header>

  <!-- Navigation Menu -->
  <section class="py-12 px-6">
    <div class="max-w-6xl mx-auto">
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        
        <a href="attendance.php" class="menu-card group bg-gradient-to-r from-red-800/80 to-red-600/80 backdrop-blur-sm rounded-xl shadow-xl overflow-hidden">
          <div class="p-6 flex items-center gap-4">
            <div class="bg-white/10 p-3 rounded-full">
              <i class="fas fa-calendar-check text-white text-xl"></i>
            </div>
            <div>
              <h3 class="text-white font-semibold text-lg group-hover:text-red-200 transition-colors">ATTENDANCE</h3>
              <div class="w-0 group-hover:w-full h-0.5 bg-red-300 transition-all duration-300"></div>
            </div>
          </div>
        </a>

        <a href="machine.php" class="menu-card group bg-gradient-to-r from-red-800/80 to-red-600/80 backdrop-blur-sm rounded-xl shadow-xl overflow-hidden">
          <div class="p-6 flex items-center gap-4">
            <div class="bg-white/10 p-3 rounded-full">
              <i class="fas fa-cogs text-white text-xl"></i>
            </div>
            <div>
              <h3 class="text-white font-semibold text-lg group-hover:text-red-200 transition-colors">MACHINES</h3>
              <div class="w-0 group-hover:w-full h-0.5 bg-red-300 transition-all duration-300"></div>
            </div>
          </div>
        </a>

        <a href="service.php" class="menu-card group bg-gradient-to-r from-red-800/80 to-red-600/80 backdrop-blur-sm rounded-xl shadow-xl overflow-hidden">
          <div class="p-6 flex items-center gap-4">
            <div class="bg-white/10 p-3 rounded-full">
              <i class="fas fa-tools text-white text-xl"></i>
            </div>
            <div>
              <h3 class="text-white font-semibold text-lg group-hover:text-red-200 transition-colors">DAILY SERVICES</h3>
              <div class="w-0 group-hover:w-full h-0.5 bg-red-300 transition-all duration-300"></div>
            </div>
          </div>
        </a>

        <a href="amc.php" class="menu-card group bg-gradient-to-r from-red-800/80 to-red-600/80 backdrop-blur-sm rounded-xl shadow-xl overflow-hidden">
          <div class="p-6 flex items-center gap-4">
            <div class="bg-white/10 p-3 rounded-full">
              <i class="fas fa-file-contract text-white text-xl"></i>
            </div>
            <div>
              <h3 class="text-white font-semibold text-lg group-hover:text-red-200 transition-colors">AMC</h3>
              <div class="w-0 group-hover:w-full h-0.5 bg-red-300 transition-all duration-300"></div>
            </div>
          </div>
        </a>

        <a href="teamsview.php" class="menu-card group bg-gradient-to-r from-red-800/80 to-red-600/80 backdrop-blur-sm rounded-xl shadow-xl overflow-hidden">
          <div class="p-6 flex items-center gap-4">
            <div class="bg-white/10 p-3 rounded-full">
              <i class="fas fa-users text-white text-xl"></i>
            </div>
            <div>
              <h3 class="text-white font-semibold text-lg group-hover:text-red-200 transition-colors">VIEW TEAM MEMBERS</h3>
              <div class="w-0 group-hover:w-full h-0.5 bg-red-300 transition-all duration-300"></div>
            </div>
          </div>
        </a>

        <a href="viewmachine.php" class="menu-card group bg-gradient-to-r from-red-800/80 to-red-600/80 backdrop-blur-sm rounded-xl shadow-xl overflow-hidden">
          <div class="p-6 flex items-center gap-4">
            <div class="bg-white/10 p-3 rounded-full">
              <i class="fas fa-sort-amount-down text-white text-xl"></i>
            </div>
            <div>
              <h3 class="text-white font-semibold text-lg group-hover:text-red-200 transition-colors">VIEW COLOR SORTERS</h3>
              <div class="w-0 group-hover:w-full h-0.5 bg-red-300 transition-all duration-300"></div>
            </div>
          </div>
        </a>

        <a href="viewcompressors.php" class="menu-card group bg-gradient-to-r from-red-800/80 to-red-600/80 backdrop-blur-sm rounded-xl shadow-xl overflow-hidden">
          <div class="p-6 flex items-center gap-4">
            <div class="bg-white/10 p-3 rounded-full">
              <i class="fas fa-compress-arrows-alt text-white text-xl"></i>
            </div>
            <div>
              <h3 class="text-white font-semibold text-lg group-hover:text-red-200 transition-colors">VIEW OIL COMPRESSORS</h3>
              <div class="w-0 group-hover:w-full h-0.5 bg-red-300 transition-all duration-300"></div>
            </div>
          </div>
        </a>

        <a href="factoryview.php" class="menu-card group bg-gradient-to-r from-red-800/80 to-red-600/80 backdrop-blur-sm rounded-xl shadow-xl overflow-hidden">
          <div class="p-6 flex items-center gap-4">
            <div class="bg-white/10 p-3 rounded-full">
              <i class="fas fa-industry text-white text-xl"></i>
            </div>
            <div>
              <h3 class="text-white font-semibold text-lg group-hover:text-red-200 transition-colors">SEARCH FACTORIES</h3>
              <div class="w-0 group-hover:w-full h-0.5 bg-red-300 transition-all duration-300"></div>
            </div>
          </div>
        </a>

        <a href="maintain.php" class="menu-card group bg-gradient-to-r from-red-800/80 to-red-600/80 backdrop-blur-sm rounded-xl shadow-xl overflow-hidden">
          <div class="p-6 flex items-center gap-4">
            <div class="bg-white/10 p-3 rounded-full">
              <i class="fas fa-clipboard-list text-white text-xl"></i>
            </div>
            <div>
              <h3 class="text-white font-semibold text-lg group-hover:text-red-200 transition-colors">VIEW MAINTENANCE</h3>
              <div class="w-0 group-hover:w-full h-0.5 bg-red-300 transition-all duration-300"></div>
            </div>
          </div>
        </a>
      </div>
    </div>
  </section>

  <?php include "footer.php" ?>
</body>
</html>
