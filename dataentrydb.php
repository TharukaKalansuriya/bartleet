<?php
session_start();

// Define allowed roles
$allowed_roles = ['admin', 'data_entry', 'manager'];

// Check if the user's role is not in the allowed roles
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], $allowed_roles)) {
    // Redirect to the login page if not authorized
    header("Location: index.php");
    exit();
}

require_once 'database.php';

$db = new Database();
$conn = $db->getConnection();

// Get user name if available
$username = isset($_SESSION['username']) ? $_SESSION['username'] : 'User';

$db->closeConnection();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>BC–Agro Tronics Data Entry</title>
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
      text-shadow: 0 0 5px rgba(253, 89, 89, 0.46), 0 0 10px rgba(253, 71, 71, 0.4);
    }
    .data-entry-card {
      transition: all 0.3s ease;
    }
    .data-entry-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 15px 30px rgba(0, 0, 0, 0.2);
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
  </style>
</head>

<body class="min-h-screen font-sans bg-cover bg-no-repeat bg-center" style="background-image: linear-gradient(to left, rgba(24, 63, 45, 0.78),rgba(9, 8, 8, 0.81)), url('img/home.png');">
  <!-- Loader -->
  <div id="loader">
    <img src="img/loading.gif" alt="Loading...">
  </div>

 

  <div id="content" class="container mx-auto px-6 py-16">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6 mb-12">
      <div>
        <h1 class="text-5xl md:text-6xl font-extrabold neon-text font-san-serif">Welcome, <?php echo htmlspecialchars($username); ?></h1>
        <p class="text-xl md:text-2xl text-red-400 mt-2">Data Entry Portal</p>
      </div>
      <div class="flex flex-col md:flex-row gap-4">
        <button onclick="handleLogout()" class="glow-btn bg-red-600 hover:bg-red-500 text-white font-bold py-3 px-6 rounded-lg transition duration-300">
          Log Out
        </button>
      </div>
    </div>

    <!-- Main content -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
      <!-- Data Entry Card -->
      <div class="glass rounded-2xl p-8 shadow-lg data-entry-card">
        <h2 class="text-2xl font-bold text-white mb-4">Data Management</h2>
        <p class="text-gray-300 mb-6">Access the data entry forms to add, edit, or delete records in the system.</p>
        <a href="manage.php" class="glow-btn2 bg-green-500 hover:bg-green-400 text-white font-bold py-3 px-6 rounded-lg inline-block transition duration-300">
          Go to Data Entry
        </a>
      </div>

      <!-- Quick Stats Card -->
      <div class="glass rounded-2xl p-8 shadow-lg data-entry-card">
        <h2 class="text-2xl font-bold text-white mb-4">User Manual</h2>
        <p class="text-gray-300 mb-6">Having any issues? Let's troubleshoot.</p>
        <a href="manual.php" class="glow-btn bg-blue-600 hover:bg-blue-500 text-white font-bold py-3 px-6 rounded-lg inline-block transition duration-300">
          User Manual
        </a>
      </div>
    </div>
  </div>

  <?php include 'footer.php'; ?>

  <script>
    // Loader functionality
    window.addEventListener('load', function() {
      document.body.classList.remove('loading');
      const loader = document.getElementById('loader');
      loader.classList.add('fade-out');
      
      setTimeout(function() {
        loader.style.display = 'none';
        document.getElementById('content').style.display = 'block';
      }, 800);
    });

    function handleLogout() {
      fetch('logout.php', {
        method: 'GET',
        credentials: 'include'
      })
      .then(() => {
        window.location.href = 'index.php';
        window.close();
      })
      .catch(error => {
        console.error('Logout failed:', error);
        alert('Logout failed. Please try again.');
      });
    }
  </script>
</body>
</html>