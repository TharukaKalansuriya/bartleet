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

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $colorSorters = (int)$_POST['color_sorters'];
    $compressors = (int)$_POST['compressors'];
    $factories = (int)$_POST['factories'];
    $teamMembers = (int)$_POST['team_members'];

    $stmt = $conn->prepare("UPDATE dashboard_counts SET color_sorters=?, compressors=?, factories=?, team_members=? WHERE id=1");
    $stmt->bind_param("iiii", $colorSorters, $compressors, $factories, $teamMembers);
    $stmt->execute();
    $stmt->close();

    echo "<p class='text-green-600 font-bold mb-4'>Counts updated successfully!</p>";
}

$result = $conn->query("SELECT * FROM dashboard_counts LIMIT 1");
$row = $result->fetch_assoc();

$db->closeConnection();
?>

<!--Form Section -->
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Update Dashboard Counts</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="flex items-center min-h-screen font-sans bg-cover bg-no-repeat bg-right" style="background-image: linear-gradient(to left, rgba(255, 128, 128, 0.05),rgba(211, 134, 119, 0.44)), url('img/background.jpg');">
<div class="absolute top-10 right-10 z-50">
  <img 
    src="img/back.png" 
    onclick="history.back()" 
    alt="Back" 
    class="w-14 h-14 cursor-pointer transition duration-400 ease-in-out transform hover:scale-110 hover:rotate-[-20deg] active:scale-95 active:rotate-[5deg]" 
  />
</div>

<div class="max-w-xl mx-auto bg-white p-8 shadow-lg rounded-xl ">
    <h2 class="text-2xl font-bold mb-6 text-center text-red-500">Update Dashboard Counts</h2>
    <form method="POST" class="space-y-4">
      <div>
        <label class="block font-semibold mb-1">Color Sorters</label>
        <input type="number" name="color_sorters" value="<?php echo $row['color_sorters']; ?>" class="w-full border px-4 py-2 rounded" required />
      </div>
      <div>
        <label class="block font-semibold mb-1">Compressors</label>
        <input type="number" name="compressors" value="<?php echo $row['compressors']; ?>" class="w-full border px-4 py-2 rounded" required />
      </div>
      <div>
        <label class="block font-semibold mb-1">Factories</label>
        <input type="number" name="factories" value="<?php echo $row['factories']; ?>" class="w-full border px-4 py-2 rounded" required />
      </div>
      <div>
        <label class="block font-semibold mb-1">Team Members</label>
        <input type="number" name="team_members" value="<?php echo $row['team_members']; ?>" class="w-full border px-4 py-2 rounded" required />
      </div>
      <button type="submit" class="w-full bg-red-500 text-white py-2 rounded hover:bg-red-600 transition">Update</button>
    </form>
  </div>
</body>
</html>
