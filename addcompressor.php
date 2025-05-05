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

$SerialNo = $_POST['SerialNo'] ?? '';
$FacId = $_POST['FacId'] ?? '';
$Model = $_POST['Model'] ?? '';
$InstalledDate = isset($_POST['InstalledDate']) ? date('Y-m-d', strtotime($_POST['InstalledDate'])) : '';
$ServicePersonId = $_POST['ServicePersonId'] ?? '';
$created_at = date('Y-m-d H:i:s');

$successMessage = $errorMessage = "";

// Fetch all members for dropdown
$membersList = $conn->query("SELECT ServicePersonId, Name FROM Members");

// Fetch all factories for dropdown
$factoryList = $conn->query("SELECT FacId, FacName FROM factories");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  
    // Validate ServicePersonId exists
    $check = $conn->prepare("SELECT 1 FROM Members WHERE ServicePersonId = ?");
    $check->bind_param("i", $ServicePersonId);
    $check->execute();
    $check->store_result();

    if ($check->num_rows === 0) {
        $errorMessage = "Error: Service person with ID $ServicePersonId does not exist.";
    } else {
        if (isset($_POST['add'])) {
            try {
                $stmt = $conn->prepare("INSERT INTO machines (SerialNo, FacId, Model, InstalledDate, ServicePersonId, created_at) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("ssssss", $SerialNo, $FacId,  $Model, $InstalledDate, $ServicePersonId, $created_at);
                $stmt->execute();
                $successMessage = "Compressor added!";
                $stmt->close();
            } catch (mysqli_sql_exception $e) {
                if ($e->getCode() == 1062) {
                    $errorMessage = "Error: A Compressor with this Serial Number already exists.";
                } else {
                    $errorMessage = "There is an error occurred: " . $e->getMessage();
                }
            }
        } elseif (isset($_POST['update'])) {
            $stmt = $conn->prepare("UPDATE compressors SET FacId = ?, Model = ?, InstalledDate = ?, ServicePersonId = ? WHERE SerialNo = ?");
            $stmt->bind_param("isssis", $FacId, $Model, $InstalledDate, $ServicePersonId, $SerialNo);
            if ($stmt->execute()) $successMessage = "Machine updated!";
            else $errorMessage = "Update failed: " . $stmt->error;
            $stmt->close();
        } elseif (isset($_POST['delete'])) {
            $stmt = $conn->prepare("DELETE FROM compressors WHERE SerialNo = ?");
            $stmt->bind_param("s", $SerialNo);
            if ($stmt->execute()) $successMessage = "Machine deleted!";
            else $errorMessage = "Delete failed: " . $stmt->error;
            $stmt->close();
        }
    }

    $check->close();
}

$machines = $conn->query("SELECT * FROM compressors ORDER BY created_at DESC");
$db->closeConnection();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Manage Oil Compressors</title>
  <script src="https://cdn.tailwindcss.com"></script>
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
        <h1 class="text-4xl md:text-5xl font-extrabold text-red-700 ">BC–Agro Tronics</h1>
        <p class="text-xl text-red-400">Add New Oil Compressor</p>
      </div>
    </div>
  </section>

  <div class="max-w-6xl mx-auto bg-white p-8 rounded-xl shadow-xl">
    <h1 class="text-3xl font-bold text-red-600 mb-6 text-center">Manage Oil Compressors</h1>

    <?php if ($successMessage): ?>
      <div class="bg-green-100 text-green-700 px-4 py-3 rounded mb-4"><?= $successMessage ?></div>
    <?php endif; ?>

    <?php if ($errorMessage): ?>
      <div class="bg-red-100 text-red-700 px-4 py-3 rounded mb-4"><?= $errorMessage ?></div>
    <?php endif; ?>

    <form method="POST" class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
      <div>
        <label class="block font-semibold text-gray-700">Serial No</label>
        <input type="text" name="SerialNo" id="SerialNo" required class="w-full mt-1 p-3 border border-gray-300 rounded-xl" />
      </div>
      <div>
        <label class="block font-semibold text-gray-700">Model</label>
        <input type="text" name="Model" id="Model" class="w-full mt-1 p-3 border border-gray-300 rounded-xl" />
      </div>
      <div>
        <label class="block font-semibold text-gray-700">Installed Date</label>
        <input type="date" name="InstalledDate" id="InstalledDate" class="w-full mt-1 p-3 border border-gray-300 rounded-xl" />
      </div>
      <div>
        <label class="block font-semibold text-gray-700">Service Person ID</label>
        <select name="ServicePersonId" id="ServicePersonId" class="w-full mt-1 p-3 border border-gray-300 rounded-xl">
          <option value="">Select a Service Person</option>
          <?php
            mysqli_data_seek($membersList, 0);
            while ($member = $membersList->fetch_assoc()):
          ?>
            <option value="<?= $member['ServicePersonId'] ?>"><?= $member['ServicePersonId'] ?> - <?= htmlspecialchars($member['Name']) ?></option>
          <?php endwhile; ?>
        </select>
      </div>

      <!--neeeeew-->
      <div>
        <label class="block font-semibold text-gray-700">Factory ID (FacId)</label>
        <select name="FacId" id="FacId" class="w-full mt-1 p-3 border border-gray-300 rounded-xl">
          <option value="">Select a Factory</option>
          <?php
            mysqli_data_seek($factoryList, 0);
            while ($factory = $factoryList->fetch_assoc()):
          ?>
            <option value="<?= $factory['FacId'] ?>"><?= $factory['FacId'] ?> - <?= htmlspecialchars($factory['FacName']) ?></option>
          <?php endwhile; ?>
        </select>
      </div>


      <div class="col-span-1 md:col-span-3 flex gap-4 mt-4">
        <button name="add" class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-6 rounded-xl">Add</button>
        <button name="update" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-6 rounded-xl">Update</button>
        <button name="delete" onclick="return confirm('Are you sure you want to delete this machine?');" class="bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-6 rounded-xl">Delete</button>
      </div>
    </form>

    <div class="overflow-x-auto overflow-y-auto max-h-[400px] border rounded-xl shadow-inner">
      <table class="min-w-full table-auto text-sm text-left text-gray-700">
        <thead class="bg-red-200 text-red-800 sticky top-0">
          <tr>
            <th class="px-4 py-2">Serial No</th>
            <th class="px-4 py-2">FacId</th>
            <th class="px-4 py-2">Model</th>
            <th class="px-4 py-2">Installed Date</th>
            <th class="px-4 py-2">ServicePersonId</th>
          </tr>
        </thead>
        <tbody>
          <?php while ($row = $machines->fetch_assoc()): ?>
            <tr class="cursor-pointer hover:bg-red-100 transition" onclick="fillForm('<?= $row['SerialNo'] ?>', '<?= $row['FacId'] ?>', '<?= htmlspecialchars($row['Model'], ENT_QUOTES) ?>', '<?= $row['InstalledDate'] ?>', '<?= $row['ServicePersonId'] ?>')">
              <td class="px-4 py-2"><?= $row['SerialNo'] ?></td>
              <td class="px-4 py-2"><?= $row['FacId'] ?></td>
              
              <td class="px-4 py-2"><?= $row['Model'] ?></td>
              <td class="px-4 py-2"><?= $row['InstalledDate'] ?></td>
              <td class="px-4 py-2"><?= $row['ServicePersonId'] ?></td>
            </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>
  </div>

  <script>
    function fillForm(serial, facId, model, date, personId) {
      document.getElementById('SerialNo').value = serial;
      document.getElementById('FacId').value = facId;
      document.getElementById('Model').value = model;
      document.getElementById('InstalledDate').value = date;
      document.getElementById('ServicePersonId').value = personId;
    }
  </script>
</body>
</html>
