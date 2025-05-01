
<?php
require_once 'database.php';

$db = new Database();
$conn = $db->getConnection();

$TeamId = $Name = $Location = "";
$successMessage = $errorMessage = "";

// Handle INSERT, UPDATE, DELETE
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $TeamId = $_POST['TeamId'];
    $Location = $_POST['Location'];
    $Name = $_POST['Name'];

   

    if (isset($_POST['add'])) {
        try {
            $stmt = $conn->prepare("INSERT INTO teams (TeamId, Location, Name) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $TeamId, $Location, $Name);
            $stmt->execute();
            $successMessage = "Team added!";
            $stmt->close();
        } catch (mysqli_sql_exception $e) {
            if ($e->getCode() == 1062) {
                $errorMessage = "Error: A Team with this ID already exists.";
            } else {
                $errorMessage = "There is an error occurred.";
            }
        }
    
    
    
    } elseif (isset($_POST['update'])) {
        $stmt = $conn->prepare("UPDATE teams SET name = ?, Location = ? WHERE TeamId = ?");
        $stmt->bind_param("sss", $Name, $Location, $ServicePersonId);
        if ($stmt->execute()) $successMessage = "Team updated!";
        else $errorMessage = "Update failed: " . $stmt->error;
        $stmt->close();
    } elseif (isset($_POST['delete'])) {
        $stmt = $conn->prepare("DELETE FROM teams WHERE TeamId = ?");
        $stmt->bind_param("s", $ServicePersonId);
        if ($stmt->execute()) $successMessage = "Team deleted!";
        else $errorMessage = "Delete failed: " . $stmt->error;
        $stmt->close();
    }
}

// Fetch all members
$members = $conn->query("SELECT * FROM Teams ORDER BY NAME");

$db->closeConnection();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Manage Members</title>
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
        <p class="text-xl text-red-400">Add Service Teams</p>
      </div>
    </div>
  </section>

  <div class="max-w-5xl mx-auto bg-white p-8 rounded-xl shadow-xl">
    <h1 class="text-3xl font-bold text-red-600 mb-6 text-center">Manage Teams</h1>

    <?php if ($successMessage): ?>
      <div class="bg-green-100 text-green-700 px-4 py-3 rounded mb-4"><?= $successMessage ?></div>
    <?php endif; ?>

    <?php if ($errorMessage): ?>
      <div class="bg-red-100 text-red-700 px-4 py-3 rounded mb-4"><?= $errorMessage ?></div>
    <?php endif; ?>

    <form method="POST" class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
      <div>
        <label class="block font-semibold text-gray-700">Team ID</label>
        <input type="text" name="TeamId" id="TeamId" required class="w-full mt-1 p-3 border border-gray-300 rounded-xl" />
      </div>
      <div>
        <label class="block font-semibold text-gray-700">Name</label>
        <input type="text" name="Name" id="Name" required class="w-full mt-1 p-3 border border-gray-300 rounded-xl" />
      </div>
      <div>
        <label class="block font-semibold text-gray-700">Location</label>
        <input type="text" name="Location" id="Location" required class="w-full mt-1 p-3 border border-gray-300 rounded-xl" />
      </div>
      <div class="col-span-1 md:col-span-3 flex gap-4 mt-4">
        <button name="add" class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-6 rounded-xl">Add</button>
        <button name="update" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-6 rounded-xl">Update</button>
        <button name="delete" onclick="return confirm('Are you sure you want to delete this member?');" class="bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-6 rounded-xl">Delete</button>
      </div>
    </form>

    <div class="overflow-x-auto overflow-y-auto max-h-[400px] border rounded-xl shadow-inner">
      <table class="min-w-full table-auto text-sm text-left text-gray-700">
        <thead class="bg-red-200 text-red-800 sticky top-0">
          <tr>
            <th class="px-4 py-2">Team ID</th>
            <th class="px-4 py-2">Name</th>
            <th class="px-4 py-2">Location</th>
          </tr>
        </thead>
        <tbody id="TeamTable" class="bg-white divide-y divide-gray-200">
          <?php while ($row = $members->fetch_assoc()): ?>
            <tr class="cursor-pointer hover:bg-red-100 transition" onclick="fillForm('<?= $row['TeamId'] ?>', '<?= htmlspecialchars($row['Name'], ENT_QUOTES) ?>', '<?= htmlspecialchars($row['Location'], ENT_QUOTES) ?>')">
              <td class="px-4 py-2"><?= $row['TeamId'] ?></td>
              <td class="px-4 py-2"><?= $row['Name'] ?></td>
              <td class="px-4 py-2"><?= $row['Location'] ?></td>
            </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>
  </div>

  <script>
    function fillForm(id, name, location) {
      document.getElementById('TeamId').value = id;
      document.getElementById('Name').value = name;
      document.getElementById('Location').value = location;
    }
  </script>
</body>
</html>
