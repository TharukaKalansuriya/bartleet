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
require_once 'database.php';

$db = new Database();
$conn = $db->getConnection();

$ContractId = $_POST['ContractId'] ?? '';
$FSRNo = $_POST['FSRNo'] ?? '';
$FacId = $_POST['FacId'] ?? '';
$SerialNo = $_POST['SerialNo'] ?? '';
$Machine = $_POST['Machine'] ?? '';
$MakeModel = $_POST['MakeModel'] ?? '';
$ServicePersonIds = $_POST['ServicePersonIds'] ?? [];
$DepartureFromColombo = $_POST['DepartureFromColombo'] ?? '';
$ArrivalAtFactory = $_POST['ArrivalAtFactory'] ?? '';
$DepartureFromFactory = $_POST['DepartureFromFactory'] ?? '';
$InspectionType = $_POST['InspectionType'] ?? [];
$WorkDescription = $_POST['WorkDescription'] ?? '';
$ReplacementDate = $_POST['ReplacementDate'] ?? '';
$AirCompressorCapacity = $_POST['AirCompressorCapacity'] ?? '';
$TotalRunTime = $_POST['TotalRunTime'] ?? '';
$Date = isset($_POST['Date']) ? date('Y-m-d', strtotime($_POST['Date'])) : '';
$created_at = date('Y-m-d H:i:s');

$successMessage = $errorMessage = "";

// Fetch all members for dropdown
$membersList = $conn->query("SELECT ServicePersonId, Name FROM Members");

// Fetch all factories for dropdown
$factoryList = $conn->query("SELECT FacId, FacName FROM factories");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  
    // Validate at least one service person is selected
    if (empty($ServicePersonIds)) {
        $errorMessage = "Error: Please select at least one service person.";
    } else {
        if (isset($_POST['add'])) {
            try {
                // First, insert the main FSR record
                $stmt = $conn->prepare("INSERT INTO fsr (ContractId, FSRNo, FacId, SerialNo, Machine, MakeModel, 
                DepartureFromColombo, ArrivalAtFactory, DepartureFromFactory, InspectionType, WorkDescription, 
                ReplacementDate, AirCompressorCapacity, TotalRunTime, Date, created_at) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                
                $inspectionTypeString = implode(',', $InspectionType);
                
                $stmt->bind_param("ssisssssssssssss", 
                    $ContractId, $FSRNo, $FacId, $SerialNo, $Machine, $MakeModel,
                    $DepartureFromColombo, $ArrivalAtFactory, $DepartureFromFactory, 
                    $inspectionTypeString, $WorkDescription, $ReplacementDate, 
                    $AirCompressorCapacity, $TotalRunTime, $Date, $created_at);
                
                $stmt->execute();
                $fsrId = $conn->insert_id; // Get the ID of the new FSR
                
                // Then insert service person associations
                foreach ($ServicePersonIds as $personId) {
                    $personStmt = $conn->prepare("INSERT INTO fsr_service_persons (FSRId, ServicePersonId) VALUES (?, ?)");
                    $personStmt->bind_param("ii", $fsrId, $personId);
                    $personStmt->execute();
                    $personStmt->close();
                }
                
                // Insert replacements if any
                if (!empty($_POST['replacements'])) {
                    foreach ($_POST['replacements'] as $replacement) {
                        if (!empty($replacement['description'])) {
                            $repStmt = $conn->prepare("INSERT INTO fsr_replacements (FSRId, Description, Date, Remarks) VALUES (?, ?, ?, ?)");
                            // Use the synchronized date from the frontend
                            $repDate = !empty($replacement['date']) ? $replacement['date'] : $ReplacementDate;
                            $repStmt->bind_param("isss", $fsrId, $replacement['description'], $repDate, $replacement['remarks']);
                            $repStmt->execute();
                            $repStmt->close();
                        }
                    }
                }
                
                // Process photo upload if exists
                if (isset($_FILES['photo']) && $_FILES['photo']['error'] == 0) {
                    $targetDir = "uploads/fsr/";
                    // Ensure directory exists
                    if (!is_dir($targetDir)) {
                        mkdir($targetDir, 0755, true);
                    }
                    $fileName = $FSRNo . "_" . basename($_FILES['photo']['name']);
                    $targetFile = $targetDir . $fileName;
                    
                    if (move_uploaded_file($_FILES['photo']['tmp_name'], $targetFile)) {
                        $photoStmt = $conn->prepare("UPDATE fsr SET PhotoPath = ? WHERE FSRId = ?");
                        $photoStmt->bind_param("si", $targetFile, $fsrId);
                        $photoStmt->execute();
                        $photoStmt->close();
                    }
                }
                
                $successMessage = "FSR record added successfully!";
                $stmt->close();
            } catch (mysqli_sql_exception $e) {
                if ($e->getCode() == 1062) {
                    $errorMessage = "Error: An FSR with this number already exists.";
                } else {
                    $errorMessage = "An error occurred: " . $e->getMessage();
                }
            }
        } elseif (isset($_POST['update'])) {
            try {
                // Update main FSR record
                $stmt = $conn->prepare("UPDATE fsr SET ContractId = ?, FacId = ?, SerialNo = ?, 
                Machine = ?, MakeModel = ?, DepartureFromColombo = ?, ArrivalAtFactory = ?, 
                DepartureFromFactory = ?, InspectionType = ?, WorkDescription = ?, ReplacementDate = ?, 
                AirCompressorCapacity = ?, TotalRunTime = ?, Date = ? WHERE FSRNo = ?");
                
                $inspectionTypeString = implode(',', $InspectionType);
                
                $stmt->bind_param("sisssssssssssss", 
                    $ContractId, $FacId, $SerialNo, $Machine, $MakeModel,
                    $DepartureFromColombo, $ArrivalAtFactory, $DepartureFromFactory, 
                    $inspectionTypeString, $WorkDescription, $ReplacementDate, 
                    $AirCompressorCapacity, $TotalRunTime, $Date, $FSRNo);
                
                $stmt->execute();
                
                // Get FSRId for this FSRNo
                $idQuery = $conn->prepare("SELECT FSRId FROM fsr WHERE FSRNo = ?");
                $idQuery->bind_param("s", $FSRNo);
                $idQuery->execute();
                $idResult = $idQuery->get_result();
                $row = $idResult->fetch_assoc();
                $fsrId = $row['FSRId'];
                $idQuery->close();
                
                // Update service persons - delete old ones and insert new ones
                $deleteStmt = $conn->prepare("DELETE FROM fsr_service_persons WHERE FSRId = ?");
                $deleteStmt->bind_param("i", $fsrId);
                $deleteStmt->execute();
                $deleteStmt->close();
                
                foreach ($ServicePersonIds as $personId) {
                    $personStmt = $conn->prepare("INSERT INTO fsr_service_persons (FSRId, ServicePersonId) VALUES (?, ?)");
                    $personStmt->bind_param("ii", $fsrId, $personId);
                    $personStmt->execute();
                    $personStmt->close();
                }
                
                // Update replacements - delete old ones and insert new ones
                $deleteRepStmt = $conn->prepare("DELETE FROM fsr_replacements WHERE FSRId = ?");
                $deleteRepStmt->bind_param("i", $fsrId);
                $deleteRepStmt->execute();
                $deleteRepStmt->close();
                
                if (!empty($_POST['replacements'])) {
                    foreach ($_POST['replacements'] as $replacement) {
                        if (!empty($replacement['description'])) {
                            $repStmt = $conn->prepare("INSERT INTO fsr_replacements (FSRId, Description, Date, Remarks) VALUES (?, ?, ?, ?)");
                            // Use the synchronized date from the frontend
                            $repDate = !empty($replacement['date']) ? $replacement['date'] : $ReplacementDate;
                            $repStmt->bind_param("isss", $fsrId, $replacement['description'], $repDate, $replacement['remarks']);
                            $repStmt->execute();
                            $repStmt->close();
                        }
                    }
                }
                
                // Process photo upload if exists
                if (isset($_FILES['photo']) && $_FILES['photo']['error'] == 0) {
                    $targetDir = "uploads/fsr/";
                    // Ensure directory exists
                    if (!is_dir($targetDir)) {
                        mkdir($targetDir, 0755, true);
                    }
                    $fileName = $FSRNo . "_" . basename($_FILES['photo']['name']);
                    $targetFile = $targetDir . $fileName;
                    
                    if (move_uploaded_file($_FILES['photo']['tmp_name'], $targetFile)) {
                        $photoStmt = $conn->prepare("UPDATE fsr SET PhotoPath = ? WHERE FSRId = ?");
                        $photoStmt->bind_param("si", $targetFile, $fsrId);
                        $photoStmt->execute();
                        $photoStmt->close();
                    }
                }
                
                $successMessage = "FSR record updated successfully!";
                $stmt->close();
            } catch (mysqli_sql_exception $e) {
                $errorMessage = "Update failed: " . $e->getMessage();
            }
        } elseif (isset($_POST['delete'])) {
            try {
                // Get FSRId for this FSRNo
                $idQuery = $conn->prepare("SELECT FSRId FROM fsr WHERE FSRNo = ?");
                $idQuery->bind_param("s", $FSRNo);
                $idQuery->execute();
                $idResult = $idQuery->get_result();
                $row = $idResult->fetch_assoc();
                $fsrId = $row['FSRId'];
                $idQuery->close();
                
                // Delete related records first (service persons and replacements)
                $conn->query("DELETE FROM fsr_service_persons WHERE FSRId = $fsrId");
                $conn->query("DELETE FROM fsr_replacements WHERE FSRId = $fsrId");
                
                // Then delete the main FSR record
                $stmt = $conn->prepare("DELETE FROM fsr WHERE FSRNo = ?");
                $stmt->bind_param("s", $FSRNo);
                
                if ($stmt->execute()) {
                    $successMessage = "FSR record deleted successfully!";
                } else {
                    $errorMessage = "Delete failed: " . $stmt->error;
                }
                $stmt->close();
            } catch (mysqli_sql_exception $e) {
                $errorMessage = "Delete failed: " . $e->getMessage();
            }
        }
    }
}

// Fetch existing FSR records for the table
$fsrRecords = $conn->query("
    SELECT f.FSRNo, f.FacId, GROUP_CONCAT(m.Name SEPARATOR ', ') as ServicePersons, f.Date 
    FROM fsr f
    LEFT JOIN fsr_service_persons fsp ON f.FSRId = fsp.FSRId
    LEFT JOIN Members m ON fsp.ServicePersonId = m.ServicePersonId
    GROUP BY f.FSRId
    ORDER BY f.created_at DESC
");

$db->closeConnection();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Repair Update</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
</head>
<body class="min-h-screen font-sans bg-cover bg-no-repeat bg-right" style="background-image: linear-gradient(to left, rgba(255, 128, 128, 0.05),rgba(211, 134, 119, 0.44)), url('img/background.jpg');">

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
        <p class="text-xl text-red-400">Repair Update</p>
      </div>
    </div>
  </section>

  <div class="max-w-6xl mx-auto bg-white p-8 rounded-xl shadow-xl mb-10">
    <h1 class="text-3xl font-bold text-red-600 mb-6 text-center">Field Service Report (FSR)</h1>

    <?php if ($successMessage): ?>
      <div class="bg-green-100 text-green-700 px-4 py-3 rounded mb-4"><?= $successMessage ?></div>
    <?php endif; ?>

    <?php if ($errorMessage): ?>
      <div class="bg-red-100 text-red-700 px-4 py-3 rounded mb-4"><?= $errorMessage ?></div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data" class="space-y-6">
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <!-- Basic Information Section -->
        <div>
          <label class="block font-semibold text-gray-700">Contract Id</label>
          <input type="text" name="ContractId" id="ContractId" required class="w-full mt-1 p-3 border border-gray-300 rounded-xl" />
        </div>
        <div>
          <label class="block font-semibold text-gray-700">FSR No</label>
          <input type="text" name="FSRNo" id="FSRNo" required class="w-full mt-1 p-3 border border-gray-300 rounded-xl" />
        </div>
        <div>
          <label class="block font-semibold text-gray-700">Factory</label>
          <select name="FacId" id="FacId" required class="w-full mt-1 p-3 border border-gray-300 rounded-xl">
            <option value="">Select a Factory</option>
            <?php
              mysqli_data_seek($factoryList, 0);
              while ($factory = $factoryList->fetch_assoc()):
            ?>
              <option value="<?= $factory['FacId'] ?>"><?= $factory['FacId'] ?> - <?= htmlspecialchars($factory['FacName']) ?></option>
            <?php endwhile; ?>
          </select>
        </div>

        <div>
          <label class="block font-semibold text-gray-700">Serial No</label>
          <input type="text" name="SerialNo" id="SerialNo" class="w-full mt-1 p-3 border border-gray-300 rounded-xl" />
        </div>
        <div>
          <label class="block font-semibold text-gray-700">Machine</label>
          <input type="text" name="Machine" id="Machine" class="w-full mt-1 p-3 border border-gray-300 rounded-xl" />
        </div>
        <div>
          <label class="block font-semibold text-gray-700">Make & Model</label>
          <input type="text" name="MakeModel" id="MakeModel" class="w-full mt-1 p-3 border border-gray-300 rounded-xl" />
        </div>

        <!-- Service Person Selection - Multiple -->
        <div class="col-span-1 md:col-span-3">
          <label class="block font-semibold text-gray-700">Service Person(s)</label>
          <select name="ServicePersonIds[]" id="ServicePersonIds" multiple class="w-full mt-1 p-3 border border-gray-300 rounded-xl select2">
            <?php
              mysqli_data_seek($membersList, 0);
              while ($member = $membersList->fetch_assoc()):
            ?>
              <option value="<?= $member['ServicePersonId'] ?>"><?= $member['ServicePersonId'] ?> - <?= htmlspecialchars($member['Name']) ?></option>
            <?php endwhile; ?>
          </select>
        </div>
      </div>

      <!-- Time Section -->
      <div class="bg-gray-50 p-4 rounded-xl">
        <h2 class="text-xl font-bold text-red-600 mb-3">Time</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
          <div>
            <label class="block font-semibold text-gray-700">Departure from Colombo</label>
            <input type="time" name="DepartureFromColombo" id="DepartureFromColombo" class="w-full mt-1 p-3 border border-gray-300 rounded-xl" />
          </div>
          <div>
            <label class="block font-semibold text-gray-700">Arrival at Factory</label>
            <input type="time" name="ArrivalAtFactory" id="ArrivalAtFactory" class="w-full mt-1 p-3 border border-gray-300 rounded-xl" />
          </div>
          <div>
            <label class="block font-semibold text-gray-700">Departure from Factory</label>
            <input type="time" name="DepartureFromFactory" id="DepartureFromFactory" class="w-full mt-1 p-3 border border-gray-300 rounded-xl" />
          </div>
        </div>
      </div>

      <!-- Inspection Section -->
      <div class="bg-gray-50 p-4 rounded-xl">
        <h2 class="text-xl font-bold text-red-600 mb-3">Inspection</h2>
        <div class="flex flex-wrap gap-6">
          <div class="flex items-center">
            <input type="checkbox" name="InspectionType[]" value="Site Inspection" id="SiteInspection" class="w-5 h-5 text-red-600" />
            <label class="ml-2 text-gray-700" for="SiteInspection">Site Inspection</label>
          </div>
          <div class="flex items-center">
            <input type="checkbox" name="InspectionType[]" value="Repairs" id="Repairs" class="w-5 h-5 text-red-600" />
            <label class="ml-2 text-gray-700" for="Repairs">Repairs</label>
          </div>
          <div class="flex items-center">
            <input type="checkbox" name="InspectionType[]" value="Service & Maintenance" id="ServiceMaintenance" class="w-5 h-5 text-red-600" />
            <label class="ml-2 text-gray-700" for="ServiceMaintenance">Service & Maintenance</label>
          </div>
        </div>
      </div>

      <!-- Description Section -->
      <div>
        <label class="block font-semibold text-gray-700">Description of work</label>
        <textarea name="WorkDescription" id="WorkDescription" rows="4" class="w-full mt-1 p-3 border border-gray-300 rounded-xl"></textarea>
      </div>

     <!-- Replacements Section -->
<div class="bg-gray-50 p-4 rounded-xl">
  <h2 class="text-xl font-bold text-red-600 mb-3">Replacements</h2>
  <div class="flex items-center mb-4">
    <label class="block font-semibold text-gray-700 mr-4">Replacement Date:</label>
    <input type="date" name="ReplacementDate" id="ReplacementDate" class="w-full max-w-xs p-3 border border-gray-300 rounded-xl" />
  </div>
  <div id="replacements-container">
    <div class="replacement-row grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
      <div>
        <label class="block font-semibold text-gray-700">Description</label>
        <input type="text" name="replacements[0][description]" class="w-full mt-1 p-3 border border-gray-300 rounded-xl" />
      </div>
      <div>
        <label class="block font-semibold text-gray-700">Date</label>
        <input type="date" name="replacements[0][date]" class="replacement-date w-full mt-1 p-3 border border-gray-300 rounded-xl" />
      </div>
      <div>
        <label class="block font-semibold text-gray-700">Remarks</label>
        <input type="text" name="replacements[0][remarks]" class="w-full mt-1 p-3 border border-gray-300 rounded-xl" />
      </div>
    </div>
  </div>
  <button type="button" id="add-replacement" class="mt-2 px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
    Add Another Replacement
  </button>
</div>

      <!-- Additional Information Section -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
          <label class="block font-semibold text-gray-700">Date</label>
          <input type="date" name="Date" id="Date" required class="w-full mt-1 p-3 border border-gray-300 rounded-xl" />
        </div>
        
        <div class="col-span-1 md:col-span-2">
          <h2 class="text-xl font-bold text-red-600 mb-3">If applicable</h2>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="block font-semibold text-gray-700">Air Compressor Capacity</label>
              <input type="text" name="AirCompressorCapacity" id="AirCompressorCapacity" class="w-full mt-1 p-3 border border-gray-300 rounded-xl" />
            </div>
            <div>
              <label class="block font-semibold text-gray-700">Total Run Time</label>
              <input type="text" name="TotalRunTime" id="TotalRunTime" class="w-full mt-1 p-3 border border-gray-300 rounded-xl" />
            </div>
          </div>
        </div>
      </div>

      <!-- Photo Upload Section -->
      <div>
        <label class="block font-semibold text-gray-700">Upload Photo</label>
        <input type="file" name="photo" accept="image/*" class="w-full mt-1 p-3 border border-gray-300 rounded-xl" />
      </div>

      <!-- Action Buttons -->
      <div class="flex gap-4 mt-8">
        <button type="submit" name="add" class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-6 rounded-xl">Add</button>
        <button type="submit" name="update" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-6 rounded-xl">Update</button>
        <button type="submit" name="delete" onclick="return confirm('Are you sure you want to delete this FSR record?');" class="bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-6 rounded-xl">Delete</button>
      </div>
    </form>

    <!-- FSR Records Table -->
    <div class="mt-10">
      <h2 class="text-2xl font-bold text-red-600 mb-4">FSR Records</h2>
      <div class="overflow-x-auto overflow-y-auto max-h-[400px] border rounded-xl shadow-inner">
        <table class="min-w-full table-auto text-sm text-left text-gray-700">
          <thead class="bg-red-200 text-red-800 sticky top-0">
            <tr>
              <th class="px-4 py-2">FSR No</th>
              <th class="px-4 py-2">Service Person(s)</th>
              <th class="px-4 py-2">Factory</th>
              <th class="px-4 py-2">Date</th>
            </tr>
          </thead>
          <tbody>
            <?php if ($fsrRecords && $fsrRecords->num_rows > 0): ?>
              <?php while ($row = $fsrRecords->fetch_assoc()): ?>
                <tr class="cursor-pointer hover:bg-red-100 transition" onclick="loadFSRDetails('<?= $row['FSRNo'] ?>')">
                  <td class="px-4 py-2"><?= htmlspecialchars($row['FSRNo']) ?></td>
                  <td class="px-4 py-2"><?= htmlspecialchars($row['ServicePersons']) ?></td>
                  <td class="px-4 py-2"><?= htmlspecialchars($row['FacId']) ?></td>
                  <td class="px-4 py-2"><?= htmlspecialchars($row['Date']) ?></td>
                </tr>
              <?php endwhile; ?>
            <?php else: ?>
              <tr>
                <td colspan="4" class="px-4 py-2 text-center">No FSR records found</td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <script>
$(document).ready(function() {
  // Initialize Select2 for multiple selection
  $('.select2').select2({
    placeholder: "Select service person(s)",
    allowClear: true
  });
  
  // Function to synchronize all replacement dates
  function syncReplacementDates(newDate) {
    $('.replacement-date').val(newDate);
  }
  
  // Set up event handler for the main replacement date
  $('#ReplacementDate').on('change', function() {
    syncReplacementDates($(this).val());
  });
  
  // Add replacement row with synchronized date
  let replacementCount = 1;
  $('#add-replacement').click(function() {
    // Get current replacement date
    const currentDate = $('#ReplacementDate').val();
    
    const newRow = `
      <div class="replacement-row grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
        <div>
          <label class="block font-semibold text-gray-700">Description</label>
          <input type="text" name="replacements[${replacementCount}][description]" class="w-full mt-1 p-3 border border-gray-300 rounded-xl" />
        </div>
        <div>
          <label class="block font-semibold text-gray-700">Date</label>
          <input type="date" name="replacements[${replacementCount}][date]" value="${currentDate}" class="replacement-date w-full mt-1 p-3 border border-gray-300 rounded-xl" />
        </div>
        <div>
          <label class="block font-semibold text-gray-700">Remarks</label>
          <input type="text" name="replacements[${replacementCount}][remarks]" class="w-full mt-1 p-3 border border-gray-300 rounded-xl" />
        </div>
      </div>
    `;
    $('#replacements-container').append(newRow);
    replacementCount++;
  });
  
  // Function to load existing FSR record
  function loadFSRDetails(fsrNo) {
    // AJAX request to get FSR details
    $.ajax({
      url: 'get_fsr_details.php',
      type: 'GET',
      data: { fsrNo: fsrNo },
      dataType: 'json',
      success: function(data) {
        // Fill form with FSR details
        $('#FSRNo').val(data.FSRNo);
        $('#ContractId').val(data.ContractId);
        $('#FacId').val(data.FacId);
        $('#SerialNo').val(data.SerialNo);
        $('#Machine').val(data.Machine);
        $('#MakeModel').val(data.MakeModel);
        
        // Clear and set service persons
        $('#ServicePersonIds').val(null).trigger('change');
        if (data.ServicePersonIds) {
          $('#ServicePersonIds').val(data.ServicePersonIds.split(',')).trigger('change');
        }
        
        $('#DepartureFromColombo').val(data.DepartureFromColombo);
        $('#ArrivalAtFactory').val(data.ArrivalAtFactory);
        $('#DepartureFromFactory').val(data.DepartureFromFactory);
        
        // Set checkboxes
        $('input[name="InspectionType[]"]').prop('checked', false);
        if (data.InspectionType) {
          const types = data.InspectionType.split(',');
          types.forEach(type => {
            $(`input[value="${type}"]`).prop('checked', true);
          });
        }
        
        $('#WorkDescription').val(data.WorkDescription);
        $('#Date').val(data.Date);
        $('#ReplacementDate').val(data.ReplacementDate);
        $('#AirCompressorCapacity').val(data.AirCompressorCapacity);
        $('#TotalRunTime').val(data.TotalRunTime);
        
        // Handle replacements
        $('#replacements-container').empty();
        if (data.replacements && data.replacements.length > 0) {
          // Get the common date (from first item or from ReplacementDate)
          const commonDate = data.replacements[0].Date || data.ReplacementDate;
          $('#ReplacementDate').val(commonDate);
          
          data.replacements.forEach((rep, index) => {
            const row = `
              <div class="replacement-row grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                <div>
                  <label class="block font-semibold text-gray-700">Description</label>
                  <input type="text" name="replacements[${index}][description]" value="${rep.Description}" class="w-full mt-1 p-3 border border-gray-300 rounded-xl" />
                </div>
                <div>
                  <label class="block font-semibold text-gray-700">Date</label>
                  <input type="date" name="replacements[${index}][date]" value="${commonDate}" class="replacement-date w-full mt-1 p-3 border border-gray-300 rounded-xl" />
                </div>
                <div>
                  <label class="block font-semibold text-gray-700">Remarks</label>
                  <input type="text" name="replacements[${index}][remarks]" value="${rep.Remarks}" class="w-full mt-1 p-3 border border-gray-300 rounded-xl" />
                </div>
              </div>
            `;
            $('#replacements-container').append(row);
          });
          replacementCount = data.replacements.length;
        } else {
          // Add empty replacement row if none exists
          const emptyRow = `
            <div class="replacement-row grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
              <div>
                <label class="block font-semibold text-gray-700">Description</label>
                <input type="text" name="replacements[0][description]" class="w-full mt-1 p-3 border border-gray-300 rounded-xl" />
              </div>
              <div>
                <label class="block font-semibold text-gray-700">Date</label>
                <input type="date" name="replacements[0][date]" value="${data.ReplacementDate}" class="replacement-date w-full mt-1 p-3 border border-gray-300 rounded-xl" />
              </div>
              <div>
                <label class="block font-semibold text-gray-700">Remarks</label>
                <input type="text" name="replacements[0][remarks]" class="w-full mt-1 p-3 border border-gray-300 rounded-xl" />
              </div>
            </div>
          `;
          $('#replacements-container').append(emptyRow);
          replacementCount = 1;
        }
      },
      error: function(xhr, status, error) {
        console.error('Error loading FSR details:', error);
        alert('Error loading FSR details. Please try again.');
      }
    });
  }
  
  // Make the loadFSRDetails function globally available
  window.loadFSRDetails = loadFSRDetails;
  
  // Ensure first date row is linked to main replacement date
  $('#ReplacementDate').on('change', function() {
    syncReplacementDates($(this).val());
  });
  
  // Initialize replacement dates with the main date field if it has a value
  const initialDate = $('#ReplacementDate').val();
  if (initialDate) {
    syncReplacementDates(initialDate);
  }
});
</script>
</body>
</html>