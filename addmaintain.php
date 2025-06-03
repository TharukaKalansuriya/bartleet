<?php

session_start();

// Define allowed roles
$allowed_roles = ['admin', 'data_entry'];

// Check if the user's role is not in the allowed roles
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], $allowed_roles)) {
    
    // Redirect to the login page if not authorized
    header("Location: index.php");
    exit();
}

require_once "database.php";
$db = new Database();
$conn = $db->getConnection();

// For Factories
$factorySql = "SELECT FacId, FacName FROM factories";
$factoryResult = $conn->query($factorySql);

// For Machines based on selected factory
$machinesSql = "SELECT SerialNo, Model FROM machines";
$machinesResult = $conn->query($machinesSql);

// For Service Persons
$personSql = "SELECT ServicePersonId, Name FROM members";
$personResult = $conn->query($personSql);

// Status options
$statusOptions = ["Open", "In Progress", "Completed", "Cancelled"];

// Check if form was submitted
$message = "";
$messageType = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data and sanitize inputs
    $serialNo = mysqli_real_escape_string($conn, $_POST['serialNo']);
    $facId = mysqli_real_escape_string($conn, $_POST['facId']);
    $fsrNo = mysqli_real_escape_string($conn, $_POST['fsrNo']);
    $serviceNote = mysqli_real_escape_string($conn, $_POST['serviceNote']);
    $status = mysqli_real_escape_string($conn, $_POST['status']);
    $servicePersonId = mysqli_real_escape_string($conn, $_POST['servicePersonId']);
    $date = mysqli_real_escape_string($conn, $_POST['date']);
    
    // Generate a unique contract ID
    $contractId = "MAINT-" . date("Ymd") . "-" . uniqid();
    
    // Insert new maintenance record
    $sql = "INSERT INTO maintainance 
            (ContractId, FacId, SerialNo, FSRno, ServiceNote, Status, ServicePersonId, Date, created_at) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())";
    
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        $message = "SQL Prepare Error: " . $conn->error;
        $messageType = "error";
    } else {
        // Catch fatal errors from execute (e.g., out of range, type mismatch)
        try {
            $stmt->bind_param("sissssss", $contractId, $facId, $serialNo, $fsrNo, $serviceNote, $status, $servicePersonId, $date);
            if ($stmt->execute()) {
                $message = "Maintenance record added successfully! Contract ID: " . $contractId;
                $messageType = "success";
            } else {
                // Specific error for FSRno foreign key violation (error code 1452)
                if ($stmt->errno == 1452 && strpos($stmt->error, 'FSRno') !== false) {
                    $message = "Error: The FSR Number you entered does not exist or is invalid. Please check and try again.";
                } elseif ($stmt->errno == 1264 && strpos($stmt->error, 'FSRno') !== false) {
                    $message = "Error: The FSR Number value is out of range or not valid for this field. Please check and try again.";
                } else {
                    $message = "SQL Error: " . $stmt->error;
                }
                $messageType = "error";
            }
        } catch (mysqli_sql_exception $e) {
            if (strpos($e->getMessage(), 'FSRno') !== false && strpos($e->getMessage(), 'Out of range') !== false) {
                $message = "Error: The FSR Number value is out of range or not valid for this field. Please check and try again.";
            } else {
                $message = "SQL Fatal Error: " . htmlspecialchars($e->getMessage());
            }
            $messageType = "error";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Add Maintenance Record</title>
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
        <h1 class="text-4xl md:text-5xl font-extrabold text-red-700 ">BC–Agro Tronics</h1>
        <p class="text-xl text-red-400">Add maintainance</p>
      </div>
    </div>
  </section>

  <div class="max-w-4xl mx-auto bg-white shadow-md rounded-lg p-6">
    
    <?php if ($message): ?>
    <div class="mb-6 p-4 rounded <?php echo $messageType === 'success' ? 'bg-green-100 border border-green-300 text-green-700' : 'bg-red-100 border border-red-300 text-red-700'; ?>">
      <?php echo $message; ?>
    </div>
    <?php endif; ?>

    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" class="space-y-6">
      
      <!-- Factory Selection Section -->
      <div class="mb-4">
        <h2 class="text-xl font-semibold text-red-500 mb-3">Location & Machine Information</h2>
        <div class="grid md:grid-cols-2 gap-4">
          <!-- Factory Dropdown -->
          <div>
            <label for="facId" class="block text-gray-700 font-medium mb-2">Factory <span class="text-red-500">*</span></label>
            <select id="facId" name="facId" required class="w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-300" onchange="loadMachines(this.value)">
              <option value="">-- Select Factory --</option>
              <?php while ($factory = $factoryResult->fetch_assoc()): ?>
                <option value="<?= $factory['FacId'] ?>"><?= htmlspecialchars($factory['FacName']) ?></option>
              <?php endwhile; ?>
            </select>
          </div>
          
          <!-- Machine Serial Number Dropdown -->
          <div>
            <label for="serialNo" class="block text-gray-700 font-medium mb-2">Machine Serial No <span class="text-red-500">*</span></label>
            <select id="serialNo" name="serialNo" required class="w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-300">
              <option value="">-- Select Machine --</option>
              <?php while ($machine = $machinesResult->fetch_assoc()): ?>
                <option value="<?= htmlspecialchars($machine['SerialNo']) ?>" data-model="<?= htmlspecialchars($machine['Model']) ?>">
                  <?= htmlspecialchars($machine['SerialNo']) ?> (<?= htmlspecialchars($machine['Model']) ?>)
                </option>
              <?php endwhile; ?>
            </select>
          </div>
        </div>
        
        <!-- Display Selected Machine Model -->
        <div id="modelDisplay" class="mt-2 text-gray-600 hidden">
          Selected Model: <span id="selectedModel" class="font-medium"></span>
        </div>
      </div>

      <!-- Service Details Section -->
      <div class="mb-4">
        <h2 class="text-xl font-semibold text-red-500 mb-3">Service Details</h2>
        <div class="grid md:grid-cols-3 gap-4">
          <!-- FSR Number -->
          <div>
            <label for="fsrNo" class="block text-gray-700 font-medium mb-2">FSR Number</label>
            <input type="text" id="fsrNo" name="fsrNo" class="w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-300" placeholder="Enter FSR Number">
          </div>
          
          <!-- Status Dropdown -->
          <div>
            <label for="status" class="block text-gray-700 font-medium mb-2">Status <span class="text-red-500">*</span></label>
            <select id="status" name="status" required class="w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-300">
              <option value="">-- Select Status --</option>
              <?php foreach ($statusOptions as $status): ?>
                <option value="<?= $status ?>"><?= $status ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          
          <!-- Service Date -->
          <div>
            <label for="date" class="block text-gray-700 font-medium mb-2">Service Date <span class="text-red-500">*</span></label>
            <input type="date" id="date" name="date" required class="w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-300" value="<?= date('Y-m-d') ?>">
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
                <option value="<?= htmlspecialchars($person['ServicePersonId']) ?>">
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
          <textarea id="serviceNote" name="serviceNote" rows="4" class="w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-300" placeholder="Enter detailed service notes here..."></textarea>
        </div>
      </div>
      
      <!-- Form Buttons -->
      <div class="flex justify-end space-x-3">
        <button type="button" onclick="window.location.href='manage.php'" class="py-3 px-6 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 transition duration-200">
          Cancel
        </button>
        <button type="reset" class="py-3 px-6 bg-blue-500 text-white rounded-md hover:bg-red-600 transition duration-200">
          Reset Form
        </button>
        <button type="submit" class="py-3 px-6 bg-green-600 text-white rounded-md hover:bg-green-400 transition duration-200">
          Save Record
        </button>
      </div>
    </form>
  </div>

  <script>
    // Show machine model when a serial number is selected
    document.getElementById('serialNo').addEventListener('change', function() {
      const selectedOption = this.options[this.selectedIndex];
      const modelDisplay = document.getElementById('modelDisplay');
      const selectedModel = document.getElementById('selectedModel');
      
      if (this.value) {
        selectedModel.textContent = selectedOption.dataset.model;
        modelDisplay.classList.remove('hidden');
      } else {
        modelDisplay.classList.add('hidden');
      }
    });
    
    // Function to load machines based on selected factory
    function loadMachines(facId) {
      if (!facId) return;
      
      // In a real application, you would make an AJAX call to get machines for the selected factory
      fetch(`get_machines.php?facId=${facId}`)
        .then(response => response.json())
        .then(data => {
          const machineDropdown = document.getElementById('serialNo');
          machineDropdown.innerHTML = '<option value="">-- Select Machine --</option>';
          
          data.forEach(machine => {
            const option = document.createElement('option');
            option.value = machine.SerialNo;
            option.dataset.model = machine.Model;
            option.textContent = `${machine.SerialNo} (${machine.Model})`;
            machineDropdown.appendChild(option);
          });
        })
        .catch(error => {
          console.error('Error loading machines:', error);
        });
    }
  </script>
</body>
</html>