<?php
require_once "database.php";
$db = new Database();
$conn = $db->getConnection();

// Initialize variables
$message = "";
$messageType = "";
$maintenanceData = null;

// Check if ID is provided
if (!isset($_GET['id'])) {
    header("Location: manage_maintenance.php");
    exit;
}

$contractId = mysqli_real_escape_string($conn, $_GET['id']);

// For Factories
$factorySql = "SELECT FacId, FacName FROM factories";
$factoryResult = $conn->query($factorySql);

// For Machines - will be filtered by JavaScript based on factory
$machinesSql = "SELECT SerialNo, Model, FacId FROM machines";
$machinesResult = $conn->query($machinesSql);

// Store machines data for JavaScript filtering
$machinesData = [];
while ($machine = $machinesResult->fetch_assoc()) {
    $machinesData[] = $machine;
}

// For Service Persons
$personSql = "SELECT ServicePersonId, Name FROM members";
$personResult = $conn->query($personSql);

// Status options
$statusOptions = ["Open", "In Progress", "Completed", "Cancelled"];

// Fetch the maintenance record
$maintenanceSql = "SELECT m.*, f.FacName, ma.Model 
                   FROM maintainance m
                   LEFT JOIN factories f ON m.FacId = f.FacId
                   LEFT JOIN machines ma ON m.SerialNo = ma.SerialNo
                   WHERE m.ContractId = ?";

$stmt = $conn->prepare($maintenanceSql);
$stmt->bind_param("s", $contractId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $maintenanceData = $result->fetch_assoc();
} else {
    $message = "Maintenance record not found!";
    $messageType = "error";
}

// Check if form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data and sanitize inputs
    $serialNo = mysqli_real_escape_string($conn, $_POST['serialNo']);
    $facId = mysqli_real_escape_string($conn, $_POST['facId']);
    $fsrNo = mysqli_real_escape_string($conn, $_POST['fsrNo']);
    $serviceNote = mysqli_real_escape_string($conn, $_POST['serviceNote']);
    $status = mysqli_real_escape_string($conn, $_POST['status']);
    $servicePersonId = mysqli_real_escape_string($conn, $_POST['servicePersonId']);
    $date = mysqli_real_escape_string($conn, $_POST['date']);
    
    // Update maintenance record
    $sql = "UPDATE maintainance 
            SET FacId = ?, SerialNo = ?, FSRno = ?, ServiceNote = ?, 
                Status = ?, ServicePersonId = ?, Date = ?, updated_at = NOW()
            WHERE ContractId = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isssssss", $facId, $serialNo, $fsrNo, $serviceNote, $status, $servicePersonId, $date, $contractId);
    
    if ($stmt->execute()) {
        $message = "Maintenance record updated successfully!";
        $messageType = "success";
        
        // Refresh maintenance data
        $stmt = $conn->prepare($maintenanceSql);
        $stmt->bind_param("s", $contractId);
        $stmt->execute();
        $result = $stmt->get_result();
        $maintenanceData = $result->fetch_assoc();
    } else {
        $message = "Error: " . $stmt->error;
        $messageType = "error";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Edit Maintenance Record</title>
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
  </style>
</head>
<body class="min-h-screen font-sans">

  <!-- Back Button - Top Right Corner -->
  <div class="absolute top-10 right-10 z-50">
    <img 
      src="img/back.png" 
      onclick="history.back()" 
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
        <p class="text-xl text-red-400">Edit Maintenance Record</p>
      </div>
    </div>
  </section>

  <div class="max-w-4xl mx-auto bg-white shadow-md rounded-lg p-6 my-8">
    
    <?php if ($message): ?>
    <div class="mb-6 p-4 rounded <?php echo $messageType === 'success' ? 'bg-green-100 border border-green-300 text-green-700' : 'bg-red-100 border border-red-300 text-red-700'; ?>">
      <?php echo $message; ?>
    </div>
    <?php endif; ?>

    <?php if ($maintenanceData): ?>
    <div class="mb-6 bg-gray-50 p-4 rounded-md border border-gray-200">
      <h3 class="text-lg font-semibold text-gray-700 mb-2">Record Details</h3>
      <p class="text-gray-600">Contract ID: <span class="font-medium"><?= htmlspecialchars($maintenanceData['ContractId']) ?></span></p>
      <p class="text-gray-600 text-sm">Created: <span class="font-medium"><?= date('M d, Y H:i', strtotime($maintenanceData['created_at'])) ?></span></p>
      <?php if (isset($maintenanceData['updated_at']) && $maintenanceData['updated_at']): ?>
        <p class="text-gray-600 text-sm">Last Updated: <span class="font-medium"><?= date('M d, Y H:i', strtotime($maintenanceData['updated_at'])) ?></span></p>
      <?php endif; ?>
    </div>
    
    <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF'] . '?id=' . urlencode($contractId)); ?>" method="post" class="space-y-6">
      
      <!-- Factory Selection Section -->
      <div class="mb-4">
        <h2 class="text-xl font-semibold text-red-500 mb-3">Location & Machine Information</h2>
        <div class="grid md:grid-cols-2 gap-4">
          <!-- Factory Dropdown -->
          <div>
            <label for="facId" class="block text-gray-700 font-medium mb-2">Factory <span class="text-red-500">*</span></label>
            <select id="facId" name="facId" required class="w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-300" onchange="filterMachines()">
              <option value="">-- Select Factory --</option>
              <?php while ($factory = $factoryResult->fetch_assoc()): ?>
                <option value="<?= $factory['FacId'] ?>" <?= ($maintenanceData['FacId'] == $factory['FacId']) ? 'selected' : '' ?>>
                  <?= htmlspecialchars($factory['FacName']) ?>
                </option>
              <?php endwhile; ?>
            </select>
          </div>
          
          <!-- Machine Serial Number Dropdown -->
          <div>
            <label for="serialNo" class="block text-gray-700 font-medium mb-2">Machine Serial No <span class="text-red-500">*</span></label>
            <select id="serialNo" name="serialNo" required class="w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-300" onchange="updateModelDisplay()">
              <option value="">-- Select Machine --</option>
              <!-- Options will be populated by JavaScript -->
            </select>
          </div>
        </div>
        
        <!-- Display Selected Machine Model -->
        <div id="modelDisplay" class="mt-2 text-gray-600">
          Selected Model: <span id="selectedModel" class="font-medium"><?= htmlspecialchars($maintenanceData['Model'] ?? '') ?></span>
        </div>
      </div>

      <!-- Service Details Section -->
      <div class="mb-4">
        <h2 class="text-xl font-semibold text-red-500 mb-3">Service Details</h2>
        <div class="grid md:grid-cols-3 gap-4">
          <!-- FSR Number -->
          <div>
            <label for="fsrNo" class="block text-gray-700 font-medium mb-2">FSR Number</label>
            <input type="text" id="fsrNo" name="fsrNo" class="w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-300" placeholder="Enter FSR Number" value="<?= htmlspecialchars($maintenanceData['FSRno'] ?? '') ?>">
          </div>
          
          <!-- Status Dropdown -->
          <div>
            <label for="status" class="block text-gray-700 font-medium mb-2">Status <span class="text-red-500">*</span></label>
            <select id="status" name="status" required class="w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-300">
              <option value="">-- Select Status --</option>
              <?php foreach ($statusOptions as $status): ?>
                <option value="<?= $status ?>" <?= ($maintenanceData['Status'] == $status) ? 'selected' : '' ?>>
                  <?= $status ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>
          
          <!-- Service Date -->
          <div>
            <label for="date" class="block text-gray-700 font-medium mb-2">Service Date <span class="text-red-500">*</span></label>
            <input type="date" id="date" name="date" required class="w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-300" value="<?= htmlspecialchars($maintenanceData['Date'] ?? '') ?>">
          </div>
        </div>
      </div>
      
      <!-- Service Person Section -->
      <div class="mb-4">
        <h2 class="text-xl font-semibold text-red-500 mb-3">Service Personnel</h2>
        <div>
          <label for="servicePersonId" class="block text-gray-700 font-medium mb-2">Service Person <span class="text-red-500">*</span></label>
          <select id="servicePersonId" name="servicePersonId" required class="w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-300">
            <option value="">-- Select Service Person --</option>
            <?php if ($personResult && $personResult->num_rows > 0): ?>
              <?php while ($person = $personResult->fetch_assoc()): ?>
                <option value="<?= htmlspecialchars($person['ServicePersonId']) ?>" <?= ($maintenanceData['ServicePersonId'] == $person['ServicePersonId']) ? 'selected' : '' ?>>
                  <?= htmlspecialchars($person['Name']) ?> (ID: <?= htmlspecialchars($person['ServicePersonId']) ?>)
                </option>
              <?php endwhile; ?>
            <?php else: ?>
              <option value="" disabled>No service personnel available</option>
            <?php endif; ?>
          </select>
        </div>
      </div>
      
      <!-- Service Notes Section -->
      <div class="mb-6">
        <h2 class="text-xl font-semibold text-red-500 mb-3">Service Notes</h2>
        <div>
          <label for="serviceNote" class="block text-gray-700 font-medium mb-2">Maintenance Notes</label>
          <textarea id="serviceNote" name="serviceNote" rows="4" class="w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-300" placeholder="Enter detailed service notes here..."><?= htmlspecialchars($maintenanceData['ServiceNote'] ?? '') ?></textarea>
        </div>
      </div>
      
      <!-- Form Buttons -->
      <div class="flex justify-end space-x-3">
        <button type="button" onclick="window.location.href='manage_maintenance.php'" class="py-3 px-6 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 transition duration-200">
          Cancel
        </button>
        <button type="submit" class="py-3 px-6 bg-yellow-500 text-white rounded-md hover:bg-yellow-600 transition duration-200">
          Update Record
        </button>
      </div>
    </form>
    <?php else: ?>
    <div class="text-center py-8">
      <i class="fas fa-exclamation-circle text-5xl text-red-300 mb-3"></i>
      <p class="text-gray-500 text-lg">Maintenance record not found. Please return to the maintenance management page.</p>
      <button onclick="window.location.href='manage_maintenance.php'" class="mt-4 py-2 px-6 bg-red-500 text-white rounded-md hover:bg-red-600 transition duration-200">
        Back to Maintenance Records
      </button>
    </div>
    <?php endif; ?>
  </div>

  <script>
    // Store machine data for filtering
    const machinesData = <?php echo json_encode($machinesData); ?>;
    const currentSerialNo = '<?php echo $maintenanceData ? htmlspecialchars($maintenanceData['SerialNo']) : ''; ?>';
    
    // Function to filter machines by factory
    function filterMachines() {
      const facIdSelect = document.getElementById('facId');
      const serialNoSelect = document.getElementById('serialNo');
      const selectedFacId = facIdSelect.value;
      
      // Clear the machine dropdown
      serialNoSelect.innerHTML = '<option value="">-- Select Machine --</option>';
      
      // Add filtered machines
      if (selectedFacId) {
        const filteredMachines = machinesData.filter(machine => machine.FacId == selectedFacId);
        
        filteredMachines.forEach(machine => {
          const option = document.createElement('option');
          option.value = machine.SerialNo;
          option.textContent = `${machine.SerialNo} (${machine.Model})`;
          option.selected = (machine.SerialNo === currentSerialNo);
          option.dataset.model = machine.Model;
          serialNoSelect.appendChild(option);
        });
      }
      
      // Update the model display
      updateModelDisplay();
    }
    
    // Function to update the model display when a machine is selected
    function updateModelDisplay() {
      const serialNoSelect = document.getElementById('serialNo');
      const modelDisplay = document.getElementById('selectedModel');
      
      if (serialNoSelect.selectedIndex > 0) {
        const selectedOption = serialNoSelect.options[serialNoSelect.selectedIndex];
        const model = selectedOption.dataset.model || '';
        modelDisplay.textContent = model;
      } else {
        modelDisplay.textContent = '';
      }
    }
    
    // Initialize the machine dropdown on page load
    document.addEventListener('DOMContentLoaded', function() {
      filterMachines();
    });
  </script>

</body>
</html>