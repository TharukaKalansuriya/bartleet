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

// Fetch all members
$members = $conn->query("SELECT * FROM Members ORDER BY NAME");

$db->closeConnection();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Manage Members</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen font-sans bg-cover bg-no-repeat bg-right" style="background-image: linear-gradient(to left, rgba(0, 0, 0, 0.4), rgba(0, 0, 0, 0.6)), url('img/background.jpg');">

  <!-- Back Button -->
  <div class="absolute top-10 right-10 z-50">
    <img 
      src="img/back.png" 
      onclick="history.back()" 
      alt="Back" 
      class="w-14 h-14 cursor-pointer transition duration-400 ease-in-out transform hover:scale-110 hover:rotate-[-20deg] active:scale-95 active:rotate-[5deg]" 
    />
  </div>

  <!-- Header with Logo and Title -->
  <section class="flex items-center justify-center pt-10 px-4">
    <div class="backdrop-blur-md bg-white/20 rounded-2xl shadow-xl p-6 flex items-center gap-6 mb-6">
      <img src="img/logo.png" alt="Logo" class="w-28 h-20 md:w-32 md:h-24 object-contain" />
      <div>
        <h1 class="text-4xl md:text-5xl font-extrabold text-red-700">BC–Agro Tronics</h1>
        <p class="text-xl text-red-400">Service Team Members</p>
      </div>
    </div>
  </section>

  <!-- Search Bar -->
  <div class="flex justify-center mb-6">
    <input 
      type="text" 
      id="searchInput" 
      onkeyup="searchTable()" 
      placeholder="Search by Name..." 
      class="w-1/2 px-4 py-2 border border-red-300 rounded-full shadow-md focus:outline-none focus:ring-2 focus:ring-red-400"
    />
  </div>

  <!-- Floating Scrollable Table -->
  <div class="flex justify-center">
    <div class="backdrop-blur-md bg-white/30 rounded-2xl shadow-xl p-4 w-11/12 md:w-3/4 max-h-[500px] overflow-y-auto overflow-x-auto">
      <table class="min-w-full table-auto text-sm text-left text-gray-700">
        <thead class="bg-red-200 text-red-800 sticky top-0">
          <tr>
            <th class="px-4 py-2">Photo</th>
            <th class="px-4 py-2">Service Person ID</th>
            <th class="px-4 py-2">Name</th>
            <th class="px-4 py-2">Location</th>
          </tr>
        </thead>
        <tbody id="memberTable" class="bg-white divide-y divide-gray-200">
          <?php while ($row = $members->fetch_assoc()): ?>
            <tr class="cursor-pointer hover:bg-red-100 transition" onclick="showProfile('<?= $row['ServicePersonId'] ?>', '<?= htmlspecialchars($row['NAME'], ENT_QUOTES) ?>', '<?= htmlspecialchars($row['Location'], ENT_QUOTES) ?>', '<?= htmlspecialchars($row['epf_number'] ?? '', ENT_QUOTES) ?>', '<?= htmlspecialchars($row['etf_number'] ?? '', ENT_QUOTES) ?>', '<?= htmlspecialchars($row['photo'] ?? '', ENT_QUOTES) ?>')">
              <td class="px-4 py-2">
                <?php if (!empty($row['photo'])): ?>
                  <img src="uploads/members/<?= $row['photo'] ?>" alt="Photo" class="w-10 h-10 object-cover rounded-full" />
                <?php else: ?>
                  <div class="w-10 h-10 bg-gray-300 rounded-full flex items-center justify-center">
                    <span class="text-gray-600 text-xs font-bold"><?= substr($row['NAME'], 0, 1) ?></span>
                  </div>
                <?php endif; ?>
              </td>
              <td class="px-4 py-2"><?= $row['ServicePersonId'] ?></td>
              <td class="px-4 py-2"><?= $row['NAME'] ?></td>
              <td class="px-4 py-2"><?= $row['Location'] ?></td>
            </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>
  </div>

  <!-- Profile Modal -->
  <div id="profileModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-2xl shadow-2xl p-8 max-w-md w-11/12 m-4 transform transition-all">
      <!-- Close Button -->
      <div class="flex justify-end mb-4">
        <button onclick="closeProfile()" class="text-gray-500 hover:text-gray-700 text-2xl font-bold">&times;</button>
      </div>
      
      <!-- Profile Content -->
      <div class="text-center">
        <!-- Profile Photo -->
        <div id="profilePhoto" class="mx-auto mb-6"></div>
        
        <!-- Profile Info -->
        <div class="space-y-4">
          <div>
            <h2 id="profileName" class="text-2xl font-bold text-red-700 mb-2"></h2>
            <p id="profileId" class="text-gray-600 text-sm"></p>
          </div>
          
          <div class="bg-gray-50 rounded-xl p-4 space-y-3">
            <div class="flex justify-between items-center">
              <span class="font-semibold text-gray-700">Location:</span>
              <span id="profileLocation" class="text-gray-600"></span>
            </div>
            
            <div class="flex justify-between items-center">
              <span class="font-semibold text-gray-700">Role:</span>
              <span id="profileEPF" class="text-gray-600"></span>
            </div>
            
            <div class="flex justify-between items-center">
              <span class="font-semibold text-gray-700">EPF/ETF Numbers:</span>
              <span id="profileETF" class="text-gray-600"></span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Script for Search and Profile Modal -->
  <script>
    function searchTable() {
      const input = document.getElementById("searchInput");
      const filter = input.value.toLowerCase();
      const rows = document.querySelectorAll("#memberTable tr");

      rows.forEach(row => {
        const nameCell = row.cells[2];
        if (nameCell) {
          const nameText = nameCell.textContent.toLowerCase();
          row.style.display = nameText.includes(filter) ? "" : "none";
        }
      });
    }

    function showProfile(id, name, location, epf, etf, photo) {
      // Update profile information
      document.getElementById('profileName').textContent = name;
      document.getElementById('profileId').textContent = 'ID: ' + id;
      document.getElementById('profileLocation').textContent = location || 'Not specified';
      document.getElementById('profileEPF').textContent = epf || 'Not specified';
      document.getElementById('profileETF').textContent = etf || 'Not specified';
      
      // Update profile photo
      const photoContainer = document.getElementById('profilePhoto');
      if (photo) {
        photoContainer.innerHTML = `<img src="uploads/members/${photo}" alt="Profile Photo" class="w-24 h-24 object-cover rounded-full border-4 border-red-200 mx-auto" />`;
      } else {
        photoContainer.innerHTML = `
          <div class="w-24 h-24 bg-red-200 rounded-full flex items-center justify-center mx-auto border-4 border-red-300">
            <span class="text-red-700 text-2xl font-bold">${name.charAt(0)}</span>
          </div>
        `;
      }
      
      // Show modal
      document.getElementById('profileModal').classList.remove('hidden');
    }

    function closeProfile() {
      document.getElementById('profileModal').classList.add('hidden');
    }

    // Close modal when clicking outside
    document.getElementById('profileModal').addEventListener('click', function(e) {
      if (e.target === this) {
        closeProfile();
      }
    });

    // Close modal with Escape key
    document.addEventListener('keydown', function(e) {
      if (e.key === 'Escape') {
        closeProfile();
      }
    });
  </script>

</body>
</html>