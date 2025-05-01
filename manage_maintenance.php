<?php
require_once "database.php";
$db = new Database();
$conn = $db->getConnection();

// Initialize variables
$message = "";
$messageType = "";
$records = [];

// Handle delete operation
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $id = mysqli_real_escape_string($conn, $_GET['id']);
    
    // Delete the maintenance record
    $deleteSql = "DELETE FROM maintainance WHERE ContractId = ?";
    $stmt = $conn->prepare($deleteSql);
    $stmt->bind_param("s", $id);
    
    if ($stmt->execute()) {
        $message = "Maintenance record deleted successfully!";
        $messageType = "success";
    } else {
        $message = "Error deleting record: " . $stmt->error;
        $messageType = "error";
    }
}

// Fetch all maintenance records with joined data
$sql = "SELECT m.*, f.FacName, ma.Model, mem.Name as ServicePersonName 
        FROM maintainance m
        LEFT JOIN factories f ON m.FacId = f.FacId
        LEFT JOIN machines ma ON m.SerialNo = ma.SerialNo
        LEFT JOIN members mem ON m.ServicePersonId = mem.ServicePersonId
        ORDER BY m.Date DESC";

$result = $conn->query($sql);

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $records[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Manage Maintenance Records</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
  <style>
    body {
      background-image: linear-gradient(to left, rgba(255, 128, 128, 0.05),rgba(211, 134, 119, 0.44)), url('img/background.jpg');
      background-size: cover;
      background-position: right;
      background-repeat: no-repeat;
      background-attachment: fixed;
      min-height: 100vh;
    }
    
    .table-container {
      overflow-x: auto;
      margin-top: 20px;
    }
    
    table {
      border-collapse: collapse;
      width: 100%;
    }
    
    th {
      position: sticky;
      top: 0;
      background-color: #f8f9fa;
      z-index: 10;
    }
    
    .status-open {
      background-color: #fff3cd;
      color: #856404;
    }
    
    .status-in-progress {
      background-color: #cce5ff;
      color: #004085;
    }
    
    .status-completed {
      background-color: #d4edda;
      color: #155724;
    }
    
    .status-cancelled {
      background-color: #f8d7da;
      color: #721c24;
    }
  </style>
</head>
<body class="min-h-screen font-sans">

  <!-- Back Button - Top Right Corner -->
  <div class="absolute top-10 right-10 z-50">
    <img 
      src="img/back.png" 
      onclick="window.location.href='index.php'" 
      alt="Back" 
      class="w-14 h-14 cursor-pointer transition duration-400 ease-in-out transform hover:scale-110 hover:rotate-[-20deg] active:scale-95 active:rotate-[5deg]" 
    />
  </div>

  <!-- Header with Logo and Title in a Blurred Background -->
  <section class="flex items-center justify-center pt-10 px-4">
    <div class="backdrop-blur-md bg-white/20 rounded-2xl shadow-xl p-6 flex items-center gap-6 mb-6">
      <img src="img/logo.png" alt="Logo" class="w-28 h-20 md:w-32 md:h-24 object-contain" />
      <div>
        <h1 class="text-4xl md:text-5xl font-extrabold text-red-700">BC–Agro Tronics</h1>
        <p class="text-xl text-red-400">Manage Maintenance Records</p>
      </div>
    </div>
  </section>

  <div class="max-w-7xl mx-auto bg-white shadow-md rounded-lg p-6 my-8">
    
    <?php if ($message): ?>
    <div class="mb-6 p-4 rounded <?php echo $messageType === 'success' ? 'bg-green-100 border border-green-300 text-green-700' : 'bg-red-100 border border-red-300 text-red-700'; ?>">
      <?php echo $message; ?>
    </div>
    <?php endif; ?>
    
    <div class="flex justify-between items-center mb-6">
      <h2 class="text-2xl font-bold text-red-600">Maintenance Records</h2>
      <a href="addmaintain.php" class="bg-red-600 hover:bg-red-700 text-white py-2 px-4 rounded-md transition duration-200 flex items-center">
        <i class="fas fa-plus mr-2"></i> Add New Record
      </a>
    </div>
    
    <!-- Search & Filter -->
    <div class="mb-6 grid md:grid-cols-3 gap-4">
      <div class="relative">
        <input type="text" id="searchInput" placeholder="Search records..." class="w-full p-3 border border-gray-300 rounded-md pl-10 focus:outline-none focus:ring-2 focus:ring-red-300">
        <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
      </div>
      
      <div>
        <select id="statusFilter" class="w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-300">
          <option value="">All Statuses</option>
          <option value="Open">Open</option>
          <option value="In Progress">In Progress</option>
          <option value="Completed">Completed</option>
          <option value="Cancelled">Cancelled</option>
        </select>
      </div>
      
      <div>
        <input type="date" id="dateFilter" class="w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-300">
      </div>
    </div>
    
    <?php if (empty($records)): ?>
    <div class="text-center py-8">
      <i class="fas fa-folder-open text-5xl text-gray-300 mb-3"></i>
      <p class="text-gray-500 text-lg">No maintenance records found.</p>
      <a href="addmaintain.php" class="mt-4 inline-block bg-red-600 hover:bg-red-700 text-white py-2 px-4 rounded-md transition duration-200">
        Add Your First Record
      </a>
    </div>
    <?php else: ?>
    <div class="table-container">
      <table class="min-w-full bg-white" id="maintenanceTable">
        <thead>
          <tr class="bg-gray-100 text-gray-600 uppercase text-sm leading-normal">
            <th class="py-3 px-4 text-left">Contract ID</th>
            <th class="py-3 px-4 text-left">Factory</th>
            <th class="py-3 px-4 text-left">Machine</th>
            <th class="py-3 px-4 text-left">Service Date</th>
            <th class="py-3 px-4 text-left">Status</th>
            <th class="py-3 px-4 text-left">Service Person</th>
            <th class="py-3 px-4 text-center">Actions</th>
          </tr>
        </thead>
        <tbody class="text-gray-600 text-sm">
          <?php foreach ($records as $record): ?>
            <?php 
              $statusClass = '';
              switch ($record['Status']) {
                case 'Open':
                  $statusClass = 'status-open';
                  break;
                case 'In Progress':
                  $statusClass = 'status-in-progress';
                  break;
                case 'Completed':
                  $statusClass = 'status-completed';
                  break;
                case 'Cancelled':
                  $statusClass = 'status-cancelled';
                  break;
              }
            ?>
            <tr class="border-b border-gray-200 hover:bg-gray-50">
              <td class="py-3 px-4"><?= htmlspecialchars($record['ContractId']) ?></td>
              <td class="py-3 px-4"><?= htmlspecialchars($record['FacName']) ?></td>
              <td class="py-3 px-4"><?= htmlspecialchars($record['SerialNo']) ?> (<?= htmlspecialchars($record['Model']) ?>)</td>
              <td class="py-3 px-4"><?= date('M d, Y', strtotime($record['Date'])) ?></td>
              <td class="py-3 px-4">
                <span class="py-1 px-3 rounded-full text-xs font-medium <?= $statusClass ?>">
                  <?= htmlspecialchars($record['Status']) ?>
                </span>
              </td>
              <td class="py-3 px-4"><?= htmlspecialchars($record['ServicePersonName']) ?></td>
              <td class="py-3 px-4 text-center">
                <div class="flex item-center justify-center">
                  
                  <a href="edit_maintenance.php?id=<?= urlencode($record['ContractId']) ?>" class="w-8 h-8 mr-2 transform hover:text-yellow-500 hover:scale-110 flex items-center justify-center" title="Edit">
                    <i class="fas fa-edit"></i>
                  </a>
                  <button onclick="confirmDelete('<?= $record['ContractId'] ?>')" class="w-8 h-8 transform hover:text-red-500 hover:scale-110 flex items-center justify-center" title="Delete">
                    <i class="fas fa-trash-alt"></i>
                  </button>
                </div>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    <?php endif; ?>

  </div>

  <!-- Delete Confirmation Modal -->
  <div id="deleteModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-lg shadow-xl p-6 max-w-md w-full">
      <div class="text-center">
        <i class="fas fa-exclamation-triangle text-5xl text-red-500 mb-4"></i>
        <h3 class="text-xl font-bold mb-2">Confirm Deletion</h3>
        <p class="text-gray-600 mb-6">Are you sure you want to delete this maintenance record? This action cannot be undone.</p>
      </div>
      <div class="flex justify-end space-x-3">
        <button onclick="closeDeleteModal()" class="py-2 px-4 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 transition duration-200">
          Cancel
        </button>
        <a id="confirmDeleteBtn" href="#" class="py-2 px-4 bg-red-600 text-white rounded-md hover:bg-red-700 transition duration-200">
          Delete
        </a>
      </div>
    </div>
  </div>

  <script>
    // Table filtering
    document.getElementById('searchInput').addEventListener('keyup', filterTable);
    document.getElementById('statusFilter').addEventListener('change', filterTable);
    document.getElementById('dateFilter').addEventListener('change', filterTable);
    
    function filterTable() {
      const searchValue = document.getElementById('searchInput').value.toLowerCase();
      const statusValue = document.getElementById('statusFilter').value;
      const dateValue = document.getElementById('dateFilter').value;
      
      const table = document.getElementById('maintenanceTable');
      const rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');
      
      for (let i = 0; i < rows.length; i++) {
        const contractId = rows[i].cells[0].textContent.toLowerCase();
        const factory = rows[i].cells[1].textContent.toLowerCase();
        const machine = rows[i].cells[2].textContent.toLowerCase();
        const date = rows[i].cells[3].textContent;
        const status = rows[i].cells[4].textContent.trim();
        const servicePerson = rows[i].cells[5].textContent.toLowerCase();
        
        // Check if row passes all filters
        const matchesSearch = contractId.includes(searchValue) || 
                             factory.includes(searchValue) || 
                             machine.includes(searchValue) || 
                             servicePerson.includes(searchValue);
        
        const matchesStatus = statusValue === '' || status === statusValue;
        
        // Convert display date to format that can be compared with filter date
        const rowDate = new Date(date);
        const filterDate = dateValue ? new Date(dateValue) : null;
        const matchesDate = !filterDate || 
          (rowDate.getFullYear() === filterDate.getFullYear() && 
           rowDate.getMonth() === filterDate.getMonth() && 
           rowDate.getDate() === filterDate.getDate());
        
        if (matchesSearch && matchesStatus && matchesDate) {
          rows[i].style.display = '';
        } else {
          rows[i].style.display = 'none';
        }
      }
    }
    
    // Delete confirmation modal
    function confirmDelete(id) {
      document.getElementById('deleteModal').classList.remove('hidden');
      document.getElementById('confirmDeleteBtn').href = `?action=delete&id=${encodeURIComponent(id)}`;
    }
    
    function closeDeleteModal() {
      document.getElementById('deleteModal').classList.add('hidden');
    }
    
    // Close modal when clicking outside
    window.onclick = function(event) {
      const modal = document.getElementById('deleteModal');
      if (event.target === modal) {
        closeDeleteModal();
      }
    }
  </script>
</body>
</html>