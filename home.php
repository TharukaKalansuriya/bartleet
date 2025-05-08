<?php

session_start();

// Define allowed roles
$allowed_roles = ['admin', 'manager'];

// Check if the user's role is not in the allowed roles
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], $allowed_roles)) {
    
    // Redirect to the login page if not authorized
    header("Location: index.php");
    exit();
}

require_once 'database.php';

$db = new Database();
$conn = $db->getConnection();

$result = $conn->query("SELECT * FROM dashboard_counts LIMIT 1");
$row = $result->fetch_assoc();

$colorSorters = $row['color_sorters'];
$compressors = $row['compressors'];
$factories = $row['factories'];
$teamMembers = $row['team_members'];

$db->closeConnection();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>BC–Agro Tronics Dashboard</title>
  <script src="https://cdn.tailwindcss.com"></script>
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
      text-shadow: 0 0 5px  rgba(253, 89, 89, 0.46), 0 0 10px rgba(253, 71, 71, 0.4);
    }
    #loader {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: #000; /* dark background or match your theme */
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 9999;
  transition: opacity 0.8s ease, visibility 0.8s ease;
}

/* Hidden state */
#loader.fade-out {
  opacity: 0;
  visibility: hidden;
}

/* Optional: Hide scrollbar during loading */
body.loading {
  overflow: hidden;
}
  </style>
</head>
<!-- body image-->
<body class="min-h-screen font-sans bg-cover bg-no-repeat bg-center" style="background-image: linear-gradient(to left, rgba(72, 33, 33, 0.6),rgba(37, 32, 32, 0.76)), url('img/home.png');">
  <!-- Loader -->
  <div id="loader">
    <img src="img/loading.gif" alt="Loading...">
  </div>

  <!--nav bar-->
  <?php include 'navbar.php'; ?>

 
  <section class="px-6 md:px-16 py-16">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
      <div>
        <h1 class="text-5xl md:text-7xl font-extrabold neon-text font-san-serif">BC–Agro Tronics</h1>
        <p class="text-xl md:text-3xl text-red-400 mt-2">Management Dashboard</p>
      </div>
      <div class="flex flex-col md:flex-row gap-4">
        <a href="https://www.bartleet.com" target="_blank" class="glow-btn2 bg-green-500 hover:bg-green-300 text-white font-bold py-3 px-6 rounded-lg transition duration-300">
          Visit Website
        </a>
        <a href="logout.php" target="_blank" class="glow-btn bg-red-600 hover:bg-red-500 text-white font-bold py-3 px-6 rounded-lg transition duration-300">
          Log Out
        </a>
        
      </div>
    </div>

    <!-- Dashboard Cards -->
    <div class="mt-12 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
      <?php
        $cards = [
          ["title" => "COLOR SORTERS", "id" => "colorSorters", "value" => $colorSorters],
          ["title" => "COMPRESSORS", "id" => "compressors", "value" => $compressors],
          ["title" => "TOTAL FACTORIES", "id" => "factories", "value" => $factories],
          ["title" => "SERVICE TEAM MEMBERS", "id" => "teamMembers", "value" => $teamMembers],
        ];

        foreach ($cards as $card) {
          echo '
          <div class="glass rounded-2xl p-8 shadow-lg hover:scale-105 transition transform duration-300">
            <h2 class="text-sm font-semibold tracking-widest text-gray-300 uppercase mb-4">' . $card['title'] . '</h2>
            <p id="' . $card['id'] . '" class="text-5xl font-bold text-white">0</p>
          </div>
          ';
        }
      ?>
    </div>
  </section>

  <script>
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

    for (const id in stats) {
      animateCount(id, stats[id]);
    }
    
         // Restore scrolling
    window.addEventListener('load', function () {
  document.body.classList.remove('loading'); 
  const loader = document.getElementById('loader');

  // Add fade-out class
  loader.classList.add('fade-out');

  // Optional: Completely remove loader from DOM after fade-out
  setTimeout(function () {
    loader.style.display = 'none';
    document.getElementById('content').style.display = 'block';
  }, 800); // Matches the transition duration
});
   
  </script>

  <?php include 'footer.php'; ?>
</body>
</html>
