<?php
session_start();

// Define allowed roles
$allowed_roles = ['admin', 'repair'];

// Check if the user's role is not in the allowed roles
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], $allowed_roles)) {
    
    // Redirect to the login page if not authorized
    header("Location: index.php");
    exit();
}

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'database.php';
$db = new Database();
$conn = $db->getConnection();

$tables = [];
$result = $conn->query("SHOW TABLES");
while ($row = $result->fetch_row()) {
    $tables[] = $row[0];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - BC Agro-Tronics</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        :root {
            --primary:rgba(243, 60, 60, 0.81);
            --primary-hover: #FF8E63;
            --secondary: #2E2E3A;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            background: url('img/background.jpg') no-repeat center center fixed;
            background-size: cover;
        }
        
        .glass-card {
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(10px);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.18);
        }
        
        .btn-primary {
            background-color: var(--primary);
            transition: all 0.2s;
        }
        
        .btn-primary:hover {
            background-color: var(--primary-hover);
        }
        
        .table-container {
            background: rgba(255, 255, 255, 0.9);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        
        .header-gradient {
            background: linear-gradient(90deg, var(--primary) 0%, #FF8E63 100%);
        }
        /* Loader full-screen style */
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
<body class="min-h-screen flex flex-col">


  <!-- Loader -->
  <div id="loader">
    <img src="img/loading.gif" alt="Loading...">
  </div>


    <?php include "navbar.php" ?>

    <div class="container mx-auto px-4 py-8 flex-grow">
        <div class="glass-card rounded-xl p-8 text-white">
            <div class="flex flex-col md:flex-row items-center justify-between mb-8">
                <div>
                    <h1 class="text-4xl md:text-5xl font-bold">Admin Dashboard</h1>
                    <p class="text-lg opacity-80 mt-2">Welcome to the BC Agro-Tronics admin area</p>
                </div>
                
                <div class="flex flex-col sm:flex-row gap-4 mt-6 md:mt-0">
                    <a href="change_passwords.php" class="btn-primary text-white font-semibold py-3 px-6 rounded-lg shadow text-center">
                        <i class="fas fa-key mr-2"></i> Manage Passwords
                    </a>
                    <a href="manage.php" class="btn-primary text-white font-semibold py-3 px-6 rounded-lg shadow text-center">
                        <i class="fas fa-database mr-2"></i> Modify Data
                    </a>
                    <button onclick="handleLogout()" class="glow-btn bg-orange-600 hover:bg-red-500 text-white font-bold py-3 px-6 rounded-lg transition duration-300">
  Log Out
</button>
                </div>
            </div>

            <div class="bg-white bg-opacity-10 p-4 rounded-lg mb-8 backdrop-filter backdrop-blur-sm">
                <div class="flex items-center">
                    <i class="fas fa-info-circle text-2xl mr-3 text-amber-300"></i>
                    <p>Database: <span class="font-semibold"><?= htmlspecialchars('bartleet') ?></span></p>
                </div>
            </div>

            <div class="table-container rounded-xl overflow-hidden">
                <div class="header-gradient text-white px-6 py-4 flex justify-between items-center">
                    <h2 class="text-xl font-bold">Database Tables</h2>
                    <div class="relative">
                        <input type="text" id="tableSearch" placeholder="Search tables..." 
                            class="bg-white bg-opacity-20 text-white placeholder-gray-300 border-0 rounded-md py-2 px-4 pl-9 focus:outline-none focus:ring-2 focus:ring-white focus:ring-opacity-40">
                        <i class="fas fa-search absolute left-3 top-2.5 text-gray-300"></i>
                    </div>
                </div>
                <table class="w-full table-auto text-gray-800">
                    <thead class="bg-gray-100 text-gray-700">
                        <tr>
                            <th class="py-3 px-6 text-left">#</th>
                            <th class="py-3 px-6 text-left">Table Name</th>
                            <th class="py-3 px-6 text-left">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <?php foreach ($tables as $index => $table): ?>
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="py-3 px-6"><?= $index + 1 ?></td>
                                <td class="py-3 px-6 font-medium"><?= htmlspecialchars($table) ?></td>
                                <td class="py-3 px-6">
                                    <a href="viewtable.php?name=<?= urlencode($table) ?>" 
                                       class="bg-blue-500 hover:bg-blue-600 text-white py-1 px-4 rounded inline-flex items-center">
                                        <i class="fas fa-eye mr-2"></i> View Table
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (count($tables) === 0): ?>
                            <tr>
                                <td colspan="3" class="text-center py-8 text-gray-500">
                                    <i class="fas fa-database text-4xl mb-3 block opacity-30"></i>
                                    <p>No tables found in the database.</p>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <footer class="text-center py-4 text-white bg-black bg-opacity-50">
        <p>© <?= date('Y') ?> BC Agro-Tronics Admin Dashboard</p>
    </footer>

    <script>
        // Table search functionality
        document.getElementById('tableSearch').addEventListener('keyup', function() {
            const searchValue = this.value.toLowerCase();
            const tableRows = document.querySelectorAll('tbody tr');
            
            tableRows.forEach(row => {
                const tableName = row.querySelector('td:nth-child(2)').textContent.toLowerCase();
                if (tableName.includes(searchValue)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
        // Restore scrolling
    window.addEventListener('load', function () {
  document.body.classList.remove('loading'); 
  const loader = document.getElementById('loader');

  // Add fade-out class
  loader.classList.add('fade-out');

  
  setTimeout(function () {
    loader.style.display = 'none';
    document.getElementById('content').style.display = 'block';
  }, 2000); // Matches the transition duration
});

function handleLogout() {
 
  fetch('logout.php', {
    method: 'GET',
    credentials: 'include' 
  })
  .then(() => {
    // After successful logout, redirect to index.php
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

<?php
$conn->close();
?>