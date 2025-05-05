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
require_once 'Database.php';
$db = new Database();
$conn = $db->getConnection();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $attendance = $_POST['attendance'] ?? [];

    $success = true;
    foreach ($attendance as $id => $status) {
        $attend = ($status === 'Attend') ? 1 : 0;
        $notAttend = ($status === 'NotAttend') ? 1 : 0;

        $stmt = $conn->prepare("INSERT INTO attendance (ServicePersonId, Date, Attend, NotAttend) VALUES (?, CURDATE(), ?, ?)");
        $stmt->bind_param("sii", $id, $attend, $notAttend);
        if (!$stmt->execute()) {
            $success = false;
        }
    }

    if ($success) {
        echo "<div class='bg-green-200 p-3 text-green-800 text-center'>Attendance submitted successfully!</div>";
    } else {
        echo "<div class='bg-red-200 p-3 text-red-800 text-center'>An error occurred while submitting attendance.</div>";
    }
}

// Fetch members for display
$search = $_GET['search'] ?? '';
$location = $_GET['location'] ?? '';

$query = "SELECT * FROM members WHERE 1=1";
$params = [];
$types = '';

if (!empty($search)) {
    $query .= " AND NAME LIKE ?";
    $params[] = "%$search%";
    $types .= 's';
}
if (!empty($location)) {
    $query .= " AND Location = ?";
    $params[] = $location;
    $types .= 's';
}

$stmt = $conn->prepare($query);
if ($params) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();

// Get unique locations
$locations = $conn->query("SELECT DISTINCT Location FROM members")->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Mark Attendance</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen font-sans bg-cover bg-no-repeat bg-right" style="background-image: linear-gradient(to left, rgba(255, 128, 128, 0.05),rgba(211, 134, 119, 0.44)), url('img/background.jpg');">

  <div class="max-w-7xl mx-auto ">
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
        <p class="text-xl text-red-400">Mark Attendance</p>
      </div>
    </div>
  </section>
    <h1 class="text-3xl font-bold mb-6 text-red-600">Mark Attendance</h1>

    <!-- Filter Form -->
    <form method="get" class="mb-6 flex flex-wrap gap-4">
      <input type="text" name="search" placeholder="Search by name" value="<?= htmlspecialchars($search) ?>" class="p-2 border rounded-md w-64"/>
      <select name="location" class="p-2 border rounded-md">
        <option value="">All Locations</option>
        <?php foreach ($locations as $loc): ?>
          <option value="<?= $loc['Location'] ?>" <?= ($location == $loc['Location']) ? 'selected' : '' ?>>
            <?= $loc['Location'] ?>
          </option>
        <?php endforeach; ?>
      </select>
      <button type="submit" class="bg-red-500 text-white px-4 py-2 rounded-md hover:bg-red-600">Filter</button>
    </form>

    <!-- Attendance Form -->
    <form method="post">
      <div class="overflow-auto max-h-[500px] border rounded-lg shadow">
        <table class="w-full text-left">
          <thead class="bg-red-200 sticky top-0">
            <tr>
              <th class="p-3">#</th>
              <th class="p-3">Name</th>
              <th class="p-3">Location</th>
              <th class="p-3 text-center">Attend</th>
              <th class="p-3 text-center">Not Attend</th>
            </tr>
          </thead>
          <tbody class="bg-white">
            <?php $i = 1; while ($row = $result->fetch_assoc()): ?>
              <tr class="border-b">
                <td class="p-3"><?= $i++ ?></td>
                <td class="p-3"><?= htmlspecialchars($row['NAME']) ?></td>
                <td class="p-3"><?= htmlspecialchars($row['Location']) ?></td>
                <td class="p-3 text-center">
                  <input type="radio" name="attendance[<?= $row['ServicePersonId'] ?>]" value="Attend" required />
                </td>
                <td class="p-3 text-center">
                  <input type="radio" name="attendance[<?= $row['ServicePersonId'] ?>]" value="NotAttend" />
                </td>
              </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
      </div>

      <div class="mt-6 text-center">
        <button type="submit" class="bg-green-600 text-white font-semibold px-6 py-3 rounded-md hover:bg-green-700">
          Submit Attendance
        </button>
      </div>
    </form>
  </div>
</body>
</html>
