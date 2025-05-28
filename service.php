<?php
require 'database.php';
$db = new Database();
$conn = $db->getConnection();

// Optional search filters
$searchFactory = isset($_GET['searchFactory']) ? $_GET['searchFactory'] : '';
$searchDate = isset($_GET['searchDate']) ? $_GET['searchDate'] : '';

// Get current date for comparison
$currentDate = date('Y-m-d');

// Base query
$query = "SELECT d.ServiceId, d.FSRNo, d.ServiceStatus, d.ServiceDate,
                 f.FacName, t.Name AS TeamName, d.FacId, d.TeamId
          FROM daily_services d
          LEFT JOIN factories f ON d.FacId = f.FacId
          LEFT JOIN teams t ON d.TeamId = t.TeamId";

// Add search filters if provided
$whereConditions = array();
$params = array();
$paramTypes = '';

if (!empty($searchFactory)) {
    $whereConditions[] = "f.FacName LIKE ?";
    $searchParam = '%' . $searchFactory . '%';
    $params[] = $searchParam;
    $paramTypes .= 's';
}

if (!empty($searchDate)) {
    $whereConditions[] = "d.ServiceDate = ?";
    $params[] = $searchDate;
    $paramTypes .= 's';
}

if (count($whereConditions) > 0) {
    $query .= " WHERE " . implode(' AND ', $whereConditions);
}

// Add sorting to show current date records first, then future dates, then past dates
$query .= " ORDER BY 
            CASE 
                WHEN d.ServiceDate = ? THEN 1 
                WHEN d.ServiceDate > ? THEN 2 
                ELSE 3 
            END, 
            ABS(DATEDIFF(d.ServiceDate, ?))"; // Sort by closeness to current date

// Add current date params for ORDER BY clause
$params[] = $currentDate;
$paramTypes .= 's';
$params[] = $currentDate;
$paramTypes .= 's';
$params[] = $currentDate;
$paramTypes .= 's';

$stmt = $conn->prepare($query);

// Bind parameters if there are any
if (!empty($params)) {
    // Create reference array for bind_param
    $bindParams = array();
    $bindParams[] = &$paramTypes;
    
    for ($i = 0; $i < count($params); $i++) {
        $bindParams[] = &$params[$i];
    }
    
    call_user_func_array(array($stmt, 'bind_param'), $bindParams);
}

$stmt->execute();
$result = $stmt->get_result();
$records = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Function to determine row class based on service date
function getRowClass($serviceDate, $currentDate) {
    if ($serviceDate < $currentDate) {
        return 'bg-red-100'; // Expired - red
    } elseif ($serviceDate == $currentDate) {
        return 'bg-green-100'; // Current - green
    } else {
        return 'bg-blue-100'; // Future - blue
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>All Service Records</title>
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
        <p class="text-xl text-red-400">Service Records</p>
      </div>
    </div>
  </section>

  <div class="max-w-7xl mx-auto backdrop-blur-md bg-white/30 shadow-xl rounded-xl p-6">
    <h1 class="text-2xl font-bold mb-6 text-center">All Service Records</h1>

    <!-- Search and filter bar -->
    <form method="GET" class="flex flex-col sm:flex-row gap-4 items-center justify-center mb-6">
      <div class="w-full sm:w-1/3">
        <input type="text" name="searchFactory" value="<?php echo htmlspecialchars($searchFactory); ?>" placeholder="Search by factory name..." class="p-3 border border-gray-300 rounded-xl w-full">
      </div>
      <div class="w-full sm:w-1/3 flex items-center">
        <input type="date" name="searchDate" value="<?php echo htmlspecialchars($searchDate); ?>" class="p-3 border border-gray-300 rounded-xl w-full">
      </div>
      <button type="submit" class="bg-blue-600 text-white px-5 py-3 rounded-xl hover:bg-blue-700 transition">Search</button>
      <a href="?" class="bg-gray-500 text-white px-5 py-3 rounded-xl hover:bg-gray-600 transition text-center">Reset</a>
    </form>

    <!-- Legend for color coding -->
    <div class="mb-4 flex flex-wrap gap-4 justify-center">
      <div class="flex items-center">
        <div class="w-4 h-4 bg-green-100 rounded mr-2"></div>
        <span>Today's Services</span>
      </div>
      <div class="flex items-center">
        <div class="w-4 h-4 bg-blue-100 rounded mr-2"></div>
        <span>Future Services</span>
      </div>
      <div class="flex items-center">
        <div class="w-4 h-4 bg-red-100 rounded mr-2"></div>
        <span>Past Services</span>
      </div>
    </div>

    <!-- Records Table -->
    <div class="overflow-x-auto">
      <table class="min-w-full bg-white border border-gray-200 rounded-xl shadow">
        <thead>
          <tr class="bg-gray-200 text-left">
            <th class="py-2 px-4">Service ID</th>
            <th class="py-2 px-4">FSR No</th>
            <th class="py-2 px-4">Factory Name</th>
            <th class="py-2 px-4">Team Name</th>
            <th class="py-2 px-4">Service Status</th>
            <th class="py-2 px-4">Service Date</th>
            <th class="py-2 px-4">Factory ID</th>
            <th class="py-2 px-4">Team ID</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!empty($records)): ?>
            <?php foreach ($records as $row): ?>
              <?php $rowClass = getRowClass($row['ServiceDate'], $currentDate); ?>
              <tr class="border-t <?php echo $rowClass; ?>">
                <td class="py-2 px-4"><?php echo htmlspecialchars($row['ServiceId']); ?></td>
                <td class="py-2 px-4"><?php echo htmlspecialchars($row['FSRNo']); ?></td>
                <td class="py-2 px-4"><?php echo htmlspecialchars($row['FacName']); ?></td>
                <td class="py-2 px-4"><?php echo htmlspecialchars($row['TeamName']); ?></td>
                <td class="py-2 px-4"><?php echo htmlspecialchars($row['ServiceStatus']); ?></td>
                <td class="py-2 px-4"><?php echo htmlspecialchars($row['ServiceDate']); ?></td>
                <td class="py-2 px-4"><?php echo htmlspecialchars($row['FacId']); ?></td>
                <td class="py-2 px-4"><?php echo htmlspecialchars($row['TeamId']); ?></td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr><td colspan="8" class="py-4 text-center text-gray-500">No records found.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</body>
</html>