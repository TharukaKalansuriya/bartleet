<?php
//database connection
require 'database.php';
$db = new Database();
$conn = $db->getConnection();

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $FacId = $_POST['FacId'];
    $FSRNo = !empty($_POST['FSRNo']) ? $_POST['FSRNo'] : NULL;
    $ServiceStatus = $_POST['ServiceStatus'];
    $TeamId = $_POST['TeamId'];
    $ServiceDate = $_POST['ServiceDate'];

    $stmt = $conn->prepare("INSERT INTO daily_services (FacId, FSRNo, ServiceStatus, TeamId, ServiceDate) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("issss", $FacId, $FSRNo, $ServiceStatus, $TeamId, $ServiceDate);

    $success = $stmt->execute();
    $stmt->close();
}

// Fetch dropdown data
$factoryList = $conn->query("SELECT FacId, FacName FROM factories");
$teamList = $conn->query("SELECT TeamId, Name FROM teams");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Add Daily Service</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen font-sans bg-cover bg-no-repeat bg-right" style="background-image: linear-gradient(to left, rgba(255, 128, 128, 0.05),rgba(211, 134, 119, 0.44)), url('img/background.jpg');">

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
        <p class="text-xl text-red-400">Schedule Service Rounds</p>
      </div>
    </div>
  </section>

  <!-- Page Container -->
  <div class="max-w-3xl mx-auto p-6 bg-gray-200 shadow-xl mt-10 rounded-xl">

    <h1 class="text-2xl font-bold text-gray-800 mb-6">Add Daily Service</h1>

    <?php if (isset($success) && $success): ?>
      <div class="mb-4 p-3 bg-green-100 text-green-800 rounded">Service round added successfully.</div>
    <?php elseif (isset($success) && !$success): ?>
      <div class="mb-4 p-3 bg-red-100 text-red-800 rounded">Failed to add service round. Try again.</div>
    <?php endif; ?>

    <form method="POST" action="" class="space-y-6">

      <!-- Factory Dropdown -->
      <div>
        <label for="FacId" class="block font-medium text-gray-700">Factory</label>
        <select name="FacId" id="FacId" class="w-full mt-1 p-3 border border-gray-300 rounded-xl" required>
          <option value="">Select a Factory</option>
          <?php
            mysqli_data_seek($factoryList, 0);
            while ($factory = $factoryList->fetch_assoc()):
          ?>
            <option value="<?= $factory['FacId'] ?>">
              <?= $factory['FacId'] ?> - <?= htmlspecialchars($factory['FacName']) ?>
            </option>
          <?php endwhile; ?>
        </select>
      </div>

      <!-- FSR Number -->
      <div>
        <label for="FSRNo" class="block font-medium text-gray-700">FSR Number (optional)</label>
        <input type="text" name="FSRNo" id="FSRNo" placeholder="Enter FSR No"
               class="w-full mt-1 p-3 border border-gray-300 rounded-xl">
      </div>

      <!-- Service Status -->
      <div>
        <label for="ServiceStatus" class="block font-medium text-gray-700">Service Status</label>
        <select name="ServiceStatus" id="ServiceStatus" class="w-full mt-1 p-3 border border-gray-300 rounded-xl" required>
          <option value="Scheduled">Scheduled</option>
          <option value="Completed">Completed</option>
          <option value="Postponed">Postponed</option>
        </select>
      </div>

      <!-- Service Team Dropdown -->
      <div>
        <label for="TeamId" class="block font-medium text-gray-700">Service Team</label>
        <select name="TeamId" id="TeamId" class="w-full mt-1 p-3 border border-gray-300 rounded-xl" required>
          <option value="">Select a Team</option>
          <?php
            mysqli_data_seek($teamList, 0);
            while ($team = $teamList->fetch_assoc()):
          ?>
            <option value="<?= $team['TeamId'] ?>"><?= htmlspecialchars($team['Name']) ?></option>
          <?php endwhile; ?>
        </select>
      </div>

      <!-- Service Date -->
      <div>
        <label for="ServiceDate" class="block font-medium text-gray-700">Service Date</label>
        <input type="date" name="ServiceDate" id="ServiceDate"
               class="w-full mt-1 p-3 border border-gray-300 rounded-xl" required>
      </div>

      <!-- Submit Button -->
      <div>
        <button type="submit"
                class="w-full bg-blue-600 text-white font-semibold py-3 rounded-xl hover:bg-blue-700">
          Add Service Round
        </button>
      </div>

    </form>

  </div>

</body>
</html>
