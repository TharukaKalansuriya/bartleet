<?php
require_once "database.php";
$db = new Database();
$conn = $db->getConnection();

// Fetch all maintenance data joined with factories and machines
$sql = "SELECT m.ContractId, m.FacId, m.SerialNo, m.FSRno, m.ServiceNote, m.Status, 
        m.ServicePersonId, m.Date, f.FacName, f.Location, ma.Model
        FROM maintainance m
        JOIN factories f ON m.FacId = f.FacId
        JOIN machines ma ON m.SerialNo = ma.SerialNo";
$result = $conn->query($sql);

// For Locations
$locationSql = "SELECT DISTINCT Location FROM factories";
$locationResult = $conn->query($locationSql);

// For Factories
$factorySql = "SELECT FacId, FacName FROM factories";
$factoryResult = $conn->query($factorySql);

// For Status options
$statusOptions = ["Open", "In Progress", "Completed", "Cancelled"];

// For Service Persons
$personSql = "SELECT ServicePersonId, Name FROM members";
$personResult = $conn->query($personSql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Maintenance Management</title>
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
    .modal {
      display: none;
      position: fixed;
      z-index: 50;
      left: 0;
      top: 0;
      width: 100%;
      height: 100%;
      overflow: auto;
      background-color: rgba(0,0,0,0.5);
    }
    .modal-content {
      background-color: white;
      margin: 10% auto;
      padding: 20px;
      border-radius: 8px;
      width: 60%;
      max-width: 600px;
    }
  </style>
</head>
<body class="min-h-screen font-sans">

  <?php include "navbar.php" ?>
  <h1 class="text-4xl font-bold text-red-600 text-center p-6 mb-6">Maintenance Management Panel</h1>

  <!-- Main Layout -->
  <div class="flex space-x-4 px-4">
    
    <!-- Left: Filters -->
    <div class="w-1/5 bg-white shadow-md rounded-lg p-4">
      <h2 class="text-xl font-semibold text-red-500 mb-4">Filters</h2>
      
      <!-- Locations Filter -->
      <div class="mb-4">
        <h3 class="font-medium text-gray-700 mb-2">Locations</h3>
        <select id="locationFilter" class="w-full p-2 border border-red-200 rounded focus:outline-none focus:ring-2 focus:ring-red-300">
          <option value="">All Locations</option>
          <?php while ($loc = $locationResult->fetch_assoc()): ?>
            <option value="<?= htmlspecialchars($loc['Location']) ?>"><?= htmlspecialchars($loc['Location']) ?></option>
          <?php endwhile; ?>
        </select>
      </div>
      
      <!-- Factories Filter -->
      <div class="mb-4">
        <h3 class="font-medium text-gray-700 mb-2">Factories</h3>
        <select id="factoryFilter" class="w-full p-2 border border-red-200 rounded focus:outline-none focus:ring-2 focus:ring-red-300">
          <option value="">All Factories</option>
          <?php while ($factory = $factoryResult->fetch_assoc()): ?>
            <option value="<?= $factory['FacId'] ?>"><?= htmlspecialchars($factory['FacName']) ?></option>
          <?php endwhile; ?>
        </select>
      </div>
      
      <!-- Status Filter -->
      <div class="mb-4">
        <h3 class="font-medium text-gray-700 mb-2">Status</h3>
        <select id="statusFilter" class="w-full p-2 border border-red-200 rounded focus:outline-none focus:ring-2 focus:ring-red-300">
          <option value="">All Statuses</option>
          <?php foreach ($statusOptions as $status): ?>
            <option value="<?= $status ?>"><?= $status ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      
      <!-- Date Range Filter -->
      <div class="mb-4">
        <h3 class="font-medium text-gray-700 mb-2">Date Range</h3>
        <input type="date" id="startDate" class="w-full p-2 border border-red-200 rounded mb-2 focus:outline-none focus:ring-2 focus:ring-red-300" placeholder="Start Date">
        <input type="date" id="endDate" class="w-full p-2 border border-red-200 rounded focus:outline-none focus:ring-2 focus:ring-red-300" placeholder="End Date">
      </div>
      
      <button onclick="applyFilters()" class="w-full bg-red-500 text-white py-2 px-4 rounded hover:bg-red-600 transition duration-200">Apply Filters</button>
      <button onclick="resetFilters()" class="w-full mt-2 bg-gray-200 text-gray-700 py-2 px-4 rounded hover:bg-gray-300 transition duration-200">Reset</button>
    </div>

    <!-- Center: Table -->
    <div class="w-4/5 bg-white shadow-md rounded-lg p-4 flex flex-col">
      <div class="flex justify-between items-center mb-4">
        <h2 class="text-xl font-semibold text-red-500">Maintenance Records</h2>
        <div class="flex space-x-2">
          <input id="searchInput" type="text" placeholder="Search records..." class="border border-red-300 rounded px-3 py-1 focus:outline-none focus:ring-2 focus:ring-red-300" onkeyup="filterTable()">
        </div>
      </div>

      <div class="overflow-y-auto flex-1 border rounded">
        <table class="w-full text-left">
          <thead class="bg-red-200 text-gray-700 sticky top-0">
            <tr>
              <th class="p-3">Contract ID</th>
              <th class="p-3">Serial No</th>
              <th class="p-3">Model</th>
              <th class="p-3">Factory</th>
              <th class="p-3">Service Note</th>
              <th class="p-3">Status</th>
              <th class="p-3">Service Person</th>
              <th class="p-3">Date</th>
              <th class="p-3">Actions</th>
            </tr>
          </thead>
          <tbody id="maintenanceTableBody" class="text-gray-800">
            <?php if ($result && $result->num_rows > 0): ?>
              <?php while ($row = $result->fetch_assoc()): ?>
                <tr data-location="<?= htmlspecialchars($row['Location']) ?>" 
                    data-facid="<?= htmlspecialchars($row['FacId']) ?>"
                    data-status="<?= htmlspecialchars($row['Status']) ?>"
                    data-date="<?= htmlspecialchars($row['Date']) ?>">
                  <td class="p-3"><?= htmlspecialchars($row['ContractId']) ?></td>
                  <td class="p-3"><?= htmlspecialchars($row['SerialNo']) ?></td>
                  <td class="p-3"><?= htmlspecialchars($row['Model']) ?></td>
                  <td class="p-3"><?= htmlspecialchars($row['FacName']) ?></td>
                  <td class="p-3"><?= htmlspecialchars($row['ServiceNote']) ?></td>
                  <td class="p-3">
                    <span class="px-2 py-1 rounded text-white
                      <?php
                        switch($row['Status']) {
                          case 'Open': echo 'bg-blue-500'; break;
                          case 'In Progress': echo 'bg-yellow-500'; break;
                          case 'Completed': echo 'bg-green-500'; break;
                          case 'Cancelled': echo 'bg-red-500'; break;
                          default: echo 'bg-gray-500';
                        }
                      ?>">
                      <?= htmlspecialchars($row['Status']) ?>
                    </span>
                  </td>
                  <td class="p-3"><?= htmlspecialchars($row['ServicePersonId']) ?></td>
                  <td class="p-3"><?= htmlspecialchars($row['Date']) ?></td>
                </tr>
              <?php endwhile; ?>
            <?php else: ?>
              <tr>
                <td colspan="9" class="p-3 text-center text-gray-500">No maintenance records found.</td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>

  </div>

  <!-- Add Maintenance Modal -->
  <div id="addModal" class="modal">
    <div class="modal-content">
      <div class="flex justify-between items-center mb-4">
        <h2 class="text-xl font-semibold text-red-500">Add New Maintenance Record</h2>
        <span onclick="closeModals()" class="text-gray-500 text-2xl cursor-pointer">&times;</span>
      </div>
      <form id="addForm" action="process_maintenance.php" method="post">
        <div class="grid grid-cols-2 gap-4 mb-4">
          <div>
            <label class="block text-gray-700 mb-1">Serial No:</label>
            <select name="serialNo" required class="w-full p-2 border border-gray-300 rounded">
              <?php
                // Fetch machines for dropdown
                $machinesSql = "SELECT SerialNo, Model FROM machines";
                $machinesResult = $conn->query($machinesSql);
                while ($machine = $machinesResult->fetch_assoc()):
              ?>
                <option value="<?= htmlspecialchars($machine['SerialNo']) ?>">
                  <?= htmlspecialchars($machine['SerialNo']) ?> (<?= htmlspecialchars($machine['Model']) ?>)
                </option>
              <?php endwhile; ?>
            </select>
          </div>
          <div>
            <label class="block text-gray-700 mb-1">Factory:</label>
            <select name="facId" required class="w-full p-2 border border-gray-300 rounded">
              <?php 
                // Reset the factory result pointer
                $conn->query($factorySql);
                $factoryResult = $conn->query($factorySql);
                while ($factory = $factoryResult->fetch_assoc()):
              ?>
                <option value="<?= $factory['FacId'] ?>"><?= htmlspecialchars($factory['FacName']) ?></option>
              <?php endwhile; ?>
            </select>
          </div>
          <div>
            <label class="block text-gray-700 mb-1">FSR Number:</label>
            <input type="text" name="fsrNo" class="w-full p-2 border border-gray-300 rounded">
          </div>
          <div>
            <label class="block text-gray-700 mb-1">Status:</label>
            <select name="status" required class="w-full p-2 border border-gray-300 rounded">
              <?php foreach ($statusOptions as $status): ?>
                <option value="<?= $status ?>"><?= $status ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div>
            <label class="block text-gray-700 mb-1">Service Person:</label>
            <select name="servicePersonId" required class="w-full p-2 border border-gray-300 rounded">
              <?php 
                // Reset service person result
                if ($personResult) {
                  $personResult->data_seek(0);
                  while ($person = $personResult->fetch_assoc()):
              ?>
                <option value="<?= htmlspecialchars($person['ServicePersonId']) ?>">
                  <?= htmlspecialchars($person['Name']) ?>
                </option>
              <?php 
                  endwhile;
                }
              ?>
            </select>
          </div>
          <div>
            <label class="block text-gray-700 mb-1">Date:</label>
            <input type="date" name="date" required class="w-full p-2 border border-gray-300 rounded">
          </div>
        </div>
        <div class="mb-4">
          <label class="block text-gray-700 mb-1">Service Note:</label>
          <textarea name="serviceNote" rows="3" class="w-full p-2 border border-gray-300 rounded"></textarea>
        </div>
        <div class="flex justify-end space-x-2">
          <button type="button" onclick="closeModals()" class="bg-gray-300 text-gray-700 py-2 px-4 rounded hover:bg-gray-400 transition duration-200">Cancel</button>
          <button type="submit" class="bg-green-500 text-white py-2 px-4 rounded hover:bg-green-600 transition duration-200">Add Record</button>
        </div>
      </form>
    </div>
  </div>

  <!-- Edit Maintenance Modal -->
  <div id="editModal" class="modal">
    <div class="modal-content">
      <div class="flex justify-between items-center mb-4">
        <h2 class="text-xl font-semibold text-red-500">Edit Maintenance Record</h2>
        <span onclick="closeModals()" class="text-gray-500 text-2xl cursor-pointer">&times;</span>
      </div>
      <form id="editForm" action="update_maintenance.php" method="post">
        <input type="hidden" id="editContractId" name="contractId">
        <div class="grid grid-cols-2 gap-4 mb-4">
          <div>
            <label class="block text-gray-700 mb-1">Serial No:</label>
            <select id="editSerialNo" name="serialNo" required class="w-full p-2 border border-gray-300 rounded">
              <?php
                // Reset machines result
                $machinesResult->data_seek(0);
                while ($machine = $machinesResult->fetch_assoc()):
              ?>
                <option value="<?= htmlspecialchars($machine['SerialNo']) ?>">
                  <?= htmlspecialchars($machine['SerialNo']) ?> (<?= htmlspecialchars($machine['Model']) ?>)
                </option>
              <?php endwhile; ?>
            </select>
          </div>
          <div>
            <label class="block text-gray-700 mb-1">Factory:</label>
            <select id="editFacId" name="facId" required class="w-full p-2 border border-gray-300 rounded">
              <?php 
                // Reset factory result
                $conn->query($factorySql);
                $factoryResult = $conn->query($factorySql);
                while ($factory = $factoryResult->fetch_assoc()):
              ?>
                <option value="<?= $factory['FacId'] ?>"><?= htmlspecialchars($factory['FacName']) ?></option>
              <?php endwhile; ?>
            </select>
          </div>
          <div>
            <label class="block text-gray-700 mb-1">FSR Number:</label>
            <input type="text" id="editFsrNo" name="fsrNo" class="w-full p-2 border border-gray-300 rounded">
          </div>
          <div>
            <label class="block text-gray-700 mb-1">Status:</label>
            <select id="editStatus" name="status" required class="w-full p-2 border border-gray-300 rounded">
              <?php foreach ($statusOptions as $status): ?>
                <option value="<?= $status ?>"><?= $status ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div>
            <label class="block text-gray-700 mb-1">Service Person:</label>
            <select id="editServicePersonId" name="servicePersonId" required class="w-full p-2 border border-gray-300 rounded">
              <?php 
                // Reset service person result
                if ($personResult) {
                  $personResult->data_seek(0);
                  while ($person = $personResult->fetch_assoc()):
              ?>
                <option value="<?= htmlspecialchars($person['ServicePersonId']) ?>">
                  <?= htmlspecialchars($person['Name']) ?>
                </option>
              <?php 
                  endwhile;
                }
              ?>
            </select>
          </div>
          <div>
            <label class="block text-gray-700 mb-1">Date:</label>
            <input type="date" id="editDate" name="date" required class="w-full p-2 border border-gray-300 rounded">
          </div>
        </div>
        <div class="mb-4">
          <label class="block text-gray-700 mb-1">Service Note:</label>
          <textarea id="editServiceNote" name="serviceNote" rows="3" class="w-full p-2 border border-gray-300 rounded"></textarea>
        </div>
        <div class="flex justify-end space-x-2">
          <button type="button" onclick="closeModals()" class="bg-gray-300 text-gray-700 py-2 px-4 rounded hover:bg-gray-400 transition duration-200">Cancel</button>
          <button type="submit" class="bg-blue-500 text-white py-2 px-4 rounded hover:bg-blue-600 transition duration-200">Update Record</button>
        </div>
      </form>
    </div>
  </div>

  <script>
    // Filter functions
    function applyFilters() {
      const location = document.getElementById("locationFilter").value;
      const factory = document.getElementById("factoryFilter").value;
      const status = document.getElementById("statusFilter").value;
      const startDate = document.getElementById("startDate").value;
      const endDate = document.getElementById("endDate").value;
      
      const rows = document.querySelectorAll("#maintenanceTableBody tr");
      
      rows.forEach(row => {
        let locationMatch = true;
        let factoryMatch = true;
        let statusMatch = true;
        let dateMatch = true;
        
        if (location && row.dataset.location !== location) {
          locationMatch = false;
        }
        
        if (factory && row.dataset.facid !== factory) {
          factoryMatch = false;
        }
        
        if (status && row.dataset.status !== status) {
          statusMatch = false;
        }
        
        if (startDate || endDate) {
          const rowDate = new Date(row.dataset.date);
          
          if (startDate && new Date(startDate) > rowDate) {
            dateMatch = false;
          }
          
          if (endDate && new Date(endDate) < rowDate) {
            dateMatch = false;
          }
        }
        
        row.style.display = (locationMatch && factoryMatch && statusMatch && dateMatch) ? "" : "none";
      });
    }
    
    function resetFilters() {
      document.getElementById("locationFilter").value = "";
      document.getElementById("factoryFilter").value = "";
      document.getElementById("statusFilter").value = "";
      document.getElementById("startDate").value = "";
      document.getElementById("endDate").value = "";
      
      const rows = document.querySelectorAll("#maintenanceTableBody tr");
      rows.forEach(row => {
        row.style.display = "";
      });
    }
    
    function filterTable() {
      const input = document.getElementById("searchInput").value.toLowerCase();
      const rows = document.querySelectorAll("#maintenanceTableBody tr");

      rows.forEach(row => {
        const match = Array.from(row.children).some(td => td.textContent.toLowerCase().includes(input));
        row.style.display = match ? "" : "none";
      });
    }

    // Modal functions
    function openAddModal() {
      document.getElementById("addModal").style.display = "block";
    }
    
    function openEditModal(contractId) {
      // Fetch record details with AJAX
      fetch(`get_maintenance.php?contractId=${contractId}`)
        .then(response => response.json())
        .then(data => {
          document.getElementById("editContractId").value = data.ContractId;
          document.getElementById("editSerialNo").value = data.SerialNo;
          document.getElementById("editFacId").value = data.FacId;
          document.getElementById("editFsrNo").value = data.FSRno;
          document.getElementById("editStatus").value = data.Status;
          document.getElementById("editServicePersonId").value = data.ServicePersonId;
          document.getElementById("editDate").value = data.Date;
          document.getElementById("editServiceNote").value = data.intServiceNote;
          
          document.getElementById("editModal").style.display = "block";
        })
        .catch(error => {
          console.error("Error fetching maintenance record:", error);
          alert("Error loading record details. Please try again.");
        });
    }
    
    function closeModals() {
      document.getElementById("addModal").style.display = "none";
      document.getElementById("editModal").style.display = "none";
    }
    
    // Close modals when clicking outside of them
    window.onclick = function(event) {
      if (event.target.classList.contains("modal")) {
        closeModals();
      }
    };
  </script>
</body>
</html>