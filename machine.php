<?php
require_once "database.php";
$db = new Database();
$conn = $db->getConnection();

// Fetch all machine data joined with factories
$sql = "SELECT m.SerialNo, m.Model, f.TeamID, f.Location, f.FacName, f.FacId
        FROM machines m
        JOIN factories f ON m.FacId = f.FacId";
$result = $conn->query($sql);

// For Locations
$locationSql = "SELECT DISTINCT Location FROM factories";
$locationResult = $conn->query($locationSql);

// For Factories
$factorySql = "SELECT FacId, FacName FROM factories";
$factoryResult = $conn->query($factorySql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Machine Management</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    body {
      background-image: linear-gradient(to left, rgba(255, 128, 128, 0.05),rgba(211, 134, 119, 0.44)), url('img/background.jpg');
      background-size: cover;
      background-position: right;
      background-repeat: no-repeat;
      background-attachment: fixed;
      min-height: 100vh;
    }
  </style>
</head>
<body class="min-h-screen font-sans">

  <?php include "navbar.php" ?>
  <h1 class="text-4xl font-bold text-red-600 text-center p-6 mb-6">Machine Management Panel</h1>

  <!-- Main Layout -->
  <div class="flex space-x-4">
    
    <!-- Left: Locations -->
    <div class="w-1/5 bg-white shadow-md rounded-lg p-4">
      <h2 class="text-xl font-semibold text-red-500 mb-4">Locations</h2>
      <ul id="locationList" class="space-y-2">
        <?php while ($loc = $locationResult->fetch_assoc()): ?>
          <li class="cursor-pointer p-2 bg-red-100 rounded hover:bg-red-200" onclick="filterByLocation('<?= htmlspecialchars($loc['Location']) ?>')">
            <?= htmlspecialchars($loc['Location']) ?>
          </li>
        <?php endwhile; ?>
      </ul>
    </div>

    <!-- Center: Table -->
    <div class="w-3/5 bg-white shadow-md rounded-lg p-4 flex flex-col">
      <div class="flex justify-between items-center mb-4">
        <h2 class="text-xl font-semibold text-red-500">Machine Details</h2>
        <input id="searchInput" type="text" placeholder="Search Machines..." class="border border-red-300 rounded px-3 py-1 focus:outline-none focus:ring-2 focus:ring-red-300" onkeyup="filterTable()" />
      </div>

      <div class="overflow-y-auto flex-1 border rounded">
        <table class="w-full text-left">
          <thead class="bg-red-200 text-gray-700 sticky top-0">
            <tr>
              <th class="p-3">Serial No</th>
              <th class="p-3">Model</th>
              <th class="p-3">Team</th>
              <th class="p-3">Location</th>
              <th class="p-3">Factory</th>
            </tr>
          </thead>
          <tbody id="machineTableBody" class="text-gray-800">
            <?php while ($row = $result->fetch_assoc()): ?>
              <tr data-location="<?= htmlspecialchars($row['Location']) ?>" data-facid="<?= htmlspecialchars($row['FacId']) ?>">
                <td class="p-3"><?= htmlspecialchars($row['SerialNo']) ?></td>
                <td class="p-3"><?= htmlspecialchars($row['Model']) ?></td>
                <td class="p-3"><?= htmlspecialchars($row['TeamID']) ?></td>
                <td class="p-3"><?= htmlspecialchars($row['Location']) ?></td>
                <td class="p-3"><?= htmlspecialchars($row['FacName']) ?></td>
              </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Right: Factories -->
    <div class="w-1/5 bg-white shadow-md rounded-lg p-4">
      <h2 class="text-xl font-semibold text-red-500 mb-4">Factories</h2>
      <ul id="factoryList" class="space-y-2">
        <?php if ($factoryResult->num_rows > 0): ?>
          <?php while ($factory = $factoryResult->fetch_assoc()): ?>
            <li class="cursor-pointer p-2 bg-red-100 rounded hover:bg-red-200"
                onclick="filterByFactory('<?= $factory['FacId'] ?>')">
              <?= htmlspecialchars($factory['FacName']) ?>
            </li>
          <?php endwhile; ?>
        <?php else: ?>
          <li class="text-gray-500">No factories found.</li>
        <?php endif; ?>
      </ul>
    </div>

  </div>

  <script>
    function filterByLocation(location) {
      const rows = document.querySelectorAll("#machineTableBody tr");
      rows.forEach(row => {
        const rowLoc = row.dataset.location;
        row.style.display = rowLoc === location ? "" : "none";
      });
    }

    function filterByFactory(facId) {
      const rows = document.querySelectorAll("#machineTableBody tr");
      rows.forEach(row => {
        const rowFacId = row.dataset.facid;
        row.style.display = rowFacId === facId ? "" : "none";
      });
    }

    function filterTable() {
      const input = document.getElementById("searchInput").value.toLowerCase();
      const rows = document.querySelectorAll("#machineTableBody tr");

      rows.forEach(row => {
        const match = Array.from(row.children).some(td => td.textContent.toLowerCase().includes(input));
        row.style.display = match ? "" : "none";
      });
    }
  </script>
</body>
</html>
