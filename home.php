<?php

session_start();

// Define allowed roles
$allowed_roles = ['repair','admin', 'manager'];

// Check if the user's role is not in the allowed roles
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], $allowed_roles)) {
    // Redirect to the login page if not authorized
    header("Location: index.php");
    session_unset();
    exit();
}

require_once 'database.php';

$db = new Database();
$conn = $db->getConnection();

// Get dashboard counts
$result = $conn->query("SELECT * FROM dashboard_counts LIMIT 1");
$row = $result->fetch_assoc();

$colorSorters = $row['color_sorters'];
$compressors = $row['compressors'];
$factories = $row['factories'];
$teamMembers = $row['team_members'];

// Get count of ALL scheduled service requests
$allServiceQuery = $conn->query("SELECT COUNT(*) as all_service_count FROM daily_services WHERE ServiceStatus = 'Scheduled'");
$allServiceRow = $allServiceQuery->fetch_assoc();
$allServices = $allServiceRow['all_service_count'];

// Get today's date
$today = date('Y-m-d');

// Get count of service requests scheduled for today
$todayServiceQuery = $conn->query("SELECT COUNT(*) as today_count FROM daily_services WHERE ServiceStatus = 'Scheduled' AND ServiceDate = '$today'");
$todayServiceRow = $todayServiceQuery->fetch_assoc();
$todayServices = $todayServiceRow['today_count'];

// Close database connection AFTER all queries are done
$db->closeConnection();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>BC–Agro Tronics Dashboard</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;900&family=Orbitron:wght@500;600;700&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
  <style>
    body {
      font-family: 'Inter', sans-serif;
      scroll-behavior: smooth;
    }
    
    .glass {
      background: rgba(255, 255, 255, 0.08);
      backdrop-filter: blur(10px);
      border: 1px solid rgba(255, 255, 255, 0.1);
      transition: all 0.5s ease;
    }
    
    .glass:hover {
      background: rgba(255, 255, 255, 0.12);
      transform: translateY(-5px);
      box-shadow: 0 15px 30px rgba(0, 0, 0, 0.3);
    }
    
    .glow-btn {
      box-shadow: 0 0 15px rgba(255, 0, 0, 0.75), 0 0 30px rgba(255, 0, 0, 0.4);
      transition: all 0.4s ease;
    }
    
    .glow-btn:hover {
      box-shadow: 0 0 25px rgba(255, 0, 0, 0.85), 0 0 40px rgba(255, 0, 0, 0.6);
      transform: translateY(-2px);
    }
  
    .glow-btn2:hover {
      box-shadow: 0 0 25px rgba(50, 87, 1, 0.77), 0 0 40px rgba(86, 122, 3, 0.71);
      transform: translateY(-2px);
    }
    
    .neon-text {
      text-shadow: 0 0 5px rgba(253, 89, 89, 0.46), 0 0 10px rgba(253, 71, 71, 0.4);
      transition: text-shadow 0.5s ease;
    }
    
    h1.neon-text:hover {
      text-shadow: 0 0 10px rgba(253, 89, 89, 0.66), 0 0 20px rgba(253, 71, 71, 0.6);
    }
    
    .card-icon {
      font-size: 2.5rem;
      margin-bottom: 1rem;
      background: linear-gradient(45deg, #ff6b6b, #ff8e8e);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      transition: all 0.5s ease;
    }
    
    .glass:hover .card-icon {
      transform: scale(1.2);
    }
    
    .card-value {
      background: linear-gradient(90deg, #ffffff, #f0f0f0);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      transition: all 0.3s ease;
    }
    
    .glass:hover .card-value {
      background: linear-gradient(90deg, #ffffff, #ff9e9e);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
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
    
    .slide-in-left {
      animation: slideInLeft 1s ease forwards;
    }
    
    .slide-in-right {
      animation: slideInRight 1s ease forwards;
    }
    
    @keyframes slideInLeft {
      from {
        transform: translateX(-50px);
        opacity: 0;
      }
      to {
        transform: translateX(0);
        opacity: 1;
      }
    }
    
    @keyframes slideInRight {
      from {
        transform: translateX(50px);
        opacity: 0;
      }
      to {
        transform: translateX(0);
        opacity: 1;
      }
    }
    
    .fade-in {
      animation: fadeIn 1.5s ease forwards;
    }
    
    @keyframes fadeIn {
      from {
        opacity: 0;
      }
      to {
        opacity: 1;
      }
    }
    
    .pulse {
      animation: pulse 2s infinite;
    }
    
    @keyframes pulse {
      0% {
        transform: scale(1);
      }
      50% {
        transform: scale(1.05);
      }
      100% {
        transform: scale(1);
      }
    }
    
    .ripple {
      position: relative;
      overflow: hidden;
    }
    
    .ripple:after {
      content: "";
      display: block;
      position: absolute;
      width: 100%;
      height: 100%;
      top: 0;
      left: 0;
      pointer-events: none;
      background-image: radial-gradient(circle, #fff 10%, transparent 10.01%);
      background-repeat: no-repeat;
      background-position: 50%;
      transform: scale(10, 10);
      opacity: 0;
      transition: transform 0.5s, opacity 1s;
    }
    
    .ripple:active:after {
      transform: scale(0, 0);
      opacity: 0.3;
      transition: 0s;
    }
    
    .dashboard-header {
      border-bottom: 1px solid rgba(255, 255, 255, 0.1);
      padding-bottom: 1.5rem;
    }
    
    .card-grid {
      opacity: 0;
      transform: translateY(20px);
      transition: all 0.8s ease;
    }
    
    .card-grid.visible {
      opacity: 1;
      transform: translateY(0);
    }
  </style>
</head>
<!-- body image-->
<body class="min-h-screen font-sans bg-cover bg-no-repeat bg-center" style="background-image: linear-gradient(to left, rgba(24, 63, 45, 0.78),rgba(9, 8, 8, 0.81)), url('img/home.png');">
  <!-- Loader -->
    <div id="loader" class="fixed top-0 left-0 w-full h-full bg-gradient-to-br from-gray-900 via-gray-800 to-black flex items-center justify-center z-50 transition-all duration-1000 ease-in-out">
        <div class="text-center">
            <img src="img/loading.gif" alt="Loading..." class="max-w-xs max-h-xs mx-auto mb-6">
            <div class="text-white text-lg font-medium animate-pulse">
                Welcome To Management Dashboard!
            </div>
        </div>
    </div>

   

  <!-- Content container -->
  <div id="content" style="display: block;">
    <!--nav bar-->
    <?php include 'navbar.php'; ?>

    <section class="px-6 md:px-16 py-16">
      <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6 dashboard-header">
        <div class="slide-in-left">
          <h1 class="text-5xl md:text-7xl font-extrabold neon-text font-san-serif">BC–Agro Tronics</h1>
          <p class="text-xl md:text-3xl text-red-400 mt-2 animate__animated animate__fadeIn animate__delay-1s">Management Dashboard</p>
        </div>
        <div class="flex flex-col md:flex-row gap-4 slide-in-right">
          <a href="https://www.bartleet.com" target="_blank" class="glow-btn2 bg-green-500 hover:bg-green-300 text-white font-bold py-3 px-6 rounded-lg transition duration-300 ripple">
            <i class="fas fa-globe mr-2"></i> Visit Website
          </a>
          <button onclick="handleLogout()" class="glow-btn bg-red-600 hover:bg-red-500 text-white font-bold py-3 px-6 rounded-lg transition duration-300 ripple">
            <i class="fas fa-sign-out-alt mr-2"></i> Log Out
          </button>
        </div>
      </div>

      <!-- Dashboard Cards -->
      <div class="mt-12 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 card-grid">
        <?php
          $cards = [
            ["title" => "COLOR SORTERS", "id" => "colorSorters", "value" => $colorSorters, "icon" => "fa-microchip"],
            ["title" => "COMPRESSORS", "id" => "compressors", "value" => $compressors, "icon" => "fa-wind"],
            ["title" => "TOTAL FACTORIES", "id" => "factories", "value" => $factories, "icon" => "fa-industry"],
            ["title" => "SERVICE TEAM MEMBERS", "id" => "teamMembers", "value" => $teamMembers, "icon" => "fa-users"],
          ];

          foreach ($cards as $card) {
            echo '
            <div class="glass rounded-2xl p-8 shadow-lg transition transform duration-300">
              <div class="card-icon">
                <i class="fas ' . $card['icon'] . '"></i>
              </div>
              <h2 class="text-sm font-semibold tracking-widest text-gray-300 uppercase mb-4">' . $card['title'] . '</h2>
              <p id="' . $card['id'] . '" class="text-5xl font-bold text-white card-value">0</p>
            </div>
            ';
          }
        ?>
      </div>
      
      <!-- Quick Stats Section -->
      <div class="mt-16 glass rounded-2xl p-8 shadow-lg fade-in" style="opacity: 0; transition: opacity 1s ease;">
        <h2 class="text-2xl font-bold text-white mb-6">Quick Overview</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
          <div class="glass p-6 rounded-xl">
            <h3 class="text-lg font-semibold text-gray-300 mb-4">System Status</h3>
            <div class="flex items-center text-green-400 mb-2">
              <i class="fas fa-check-circle mr-2"></i>
              <span>All systems operational</span>
            </div>
            <div class="h-2 bg-gray-700 rounded overflow-hidden mt-4">
              <div class="bg-green-500 h-full" style="width: 92%;" id="systemProgress"></div>
            </div>
            <div class="text-sm text-gray-400 mt-2">92% uptime this month</div>
          </div>
          
          <div class="glass p-6 rounded-xl">
            <h3 class="text-lg font-semibold text-gray-300 mb-4">Recent Service Updates</h3>
            <div class="space-y-3">
              <div class="flex items-center text-gray-300">
                <i class="fas fa-bell mr-2 text-yellow-400"></i>
                <span><?= $allServices ?> All Scheduled Services</span>
              </div>
              <div class="flex items-center text-gray-300">
                <i class="fas fa-calendar-day mr-2 text-red-400"></i>
                <span><?= $todayServices ?> services scheduled for today</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>

  <script>
    // Make sure the DOM is fully loaded
    document.addEventListener('DOMContentLoaded', function() {
      // Force remove the loader after 3 seconds as a fallback
      setTimeout(function() {
        const loader = document.getElementById('loader');
        if (loader) {
          loader.style.display = 'none';
        }
        // Ensure content is visible
        document.getElementById('content').style.display = 'block';
        // Trigger card animations
        document.querySelector('.card-grid').classList.add('visible');
      }, 3000);
      
      function animateCount(id, target, duration = 2000) {
        const el = document.getElementById(id);
        let start = 0;
        const step = Math.ceil(target / (duration / 16));

        function update() {
          start += step;
          if (start >= target) {
            el.textContent = target.toLocaleString();
          } else {
            el.textContent = start.toLocaleString();
            requestAnimationFrame(update);
          }
        }
        requestAnimationFrame(update);
      }

      const stats = {
        colorSorters: <?= $colorSorters ?>,
        compressors: <?= $compressors ?>,
        factories: <?= $factories ?>,
        teamMembers: <?= $teamMembers ?>
      };
      
      // Service requests counts for use in JavaScript
      const allServices = <?= $allServices ?>;
      const todayServices = <?= $todayServices ?>;
      
      // Add intersection observer for animation on scroll
      const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
          if (entry.isIntersecting) {
            entry.target.classList.add('visible');
            
           
            if (entry.target.classList.contains('card-grid')) {
              for (const id in stats) {
                animateCount(id, stats[id]);
              }
            }
            if (entry.target.style.opacity === "0") {
              entry.target.style.opacity = "1";
            }
          }
        });
      }, {
        threshold: 0.1
      });
      
      // Observe card grid
      observer.observe(document.querySelector('.card-grid'));
      
      // Observe other fade-in elements
      document.querySelectorAll('.fade-in').forEach(el => {
        observer.observe(el);
      });
    });
    
    // Loader handling
    window.addEventListener('load', function() {
      document.body.classList.remove('loading');
      const loader = document.getElementById('loader');
      
      // Add fade-out class to loader
      loader.classList.add('fade-out');
      
      // Display content after loader fades
      setTimeout(function() {
        loader.style.display = 'none';
        
        
        // Trigger animations for visible elements
        document.querySelector('.card-grid').classList.add('visible');
      }, 800);
    });
    
    function handleLogout() {
      fetch('logout.php', {
        method: 'GET',
        credentials: 'include'
      })
      .then(() => {
     
        document.body.style.opacity = 0;
        document.body.style.transition = 'opacity 0.5s ease';
        
        setTimeout(() => {
          window.location.href = 'index.php';
        }, 500);
      })
      .catch(error => {
        console.error('Logout failed:', error);
        alert('Logout failed. Please try again.');
      });
    }
    
    // Animate system progress bar
    setTimeout(() => {
      const progressBar = document.getElementById('systemProgress');
      progressBar.style.transition = 'width 1.5s ease-in-out';
      progressBar.style.width = '92%';
    }, 1500);
    
    // Add ripple effect to buttons
    document.querySelectorAll('.ripple').forEach(button => {
      button.addEventListener('click', function(e) {
        const x = e.clientX - e.target.getBoundingClientRect().left;
        const y = e.clientY - e.target.getBoundingClientRect().top;
        
        const ripple = document.createElement('span');
        ripple.className = 'ripple-effect';
        ripple.style.left = `${x}px`;
        ripple.style.top = `${y}px`;
        
        this.appendChild(ripple);
        
        setTimeout(() => {
          ripple.remove();
        }, 600);
      });
    });
  </script>

  <?php include 'footer.php'; ?>
</body>
</html>