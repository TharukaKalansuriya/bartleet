<?php
require_once 'database.php';

$db = new Database();
$conn = $db->getConnection();

// Fetch all machines with factory names
$machines = $conn->query("
    SELECT 
        compressors.SerialNo, 
        factories.FacName,  
       compressors.Model, 
        compressors.InstalledDate, 
        compressors.ServicePersonId
    FROM 
       compressors
    JOIN 
        factories ON compressors.FacId = factories.FacId
    ORDER BY 
        compressors.created_at DESC
");

$db->closeConnection();
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Manage Machines</title>
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
        <p class="text-xl text-red-400">Manage Machines</p>
      </div>
    </div>
  </section>

  <!-- Search Bar -->
  <div class="flex justify-center mb-6">
    <input 
      type="text" 
      id="searchInput" 
      onkeyup="searchTable()" 
      placeholder="Search by Serial No..." 
      class="w-1/2 px-4 py-2 border border-red-300 rounded-full shadow-md focus:outline-none focus:ring-2 focus:ring-red-400"
    />
  </div>

  <!-- Floating Scrollable Table -->
  <div class="flex justify-center">
    <div class="overflow-x-auto overflow-y-auto max-h-[400px] border rounded-xl shadow-inner">
      <table class="min-w-full table-auto text-sm text-left text-gray-700">
      <thead class="bg-red-200 text-red-800 sticky top-0">
  <tr>
    <th class="px-4 py-2">Serial No</th>
    <th class="px-4 py-2">Factory Name</th> <!-- Changed -->
    <th class="px-4 py-2">Model</th>
    <th class="px-4 py-2">Installed Date</th>
    <th class="px-4 py-2">ServicePersonId</th>
  </tr>
</thead>
<tbody>
  <?php while ($row = $machines->fetch_assoc()): ?>
    <tr class="cursor-pointer bg-white hover:bg-red-100 transition" onclick="fillForm('<?= $row['SerialNo'] ?>', '<?= htmlspecialchars($row['FacName'], ENT_QUOTES) ?>', '<?= htmlspecialchars($row['Model'], ENT_QUOTES) ?>', '<?= $row['InstalledDate'] ?>', '<?= $row['ServicePersonId'] ?>')">
      <td class="px-4 py-2"><?= $row['SerialNo'] ?></td>
      <td class="px-4 py-2"><?= $row['FacName'] ?></td> <!-- Changed -->
      <td class="px-4 py-2"><?= $row['Model'] ?></td>
      <td class="px-4 py-2"><?= $row['InstalledDate'] ?></td>
      <td class="px-4 py-2"><?= $row['ServicePersonId'] ?></td>
    </tr>
  <?php endwhile; ?>
</tbody>

         
      </table>
    </div>
  </div>

  <!-- Script for Search -->
  <script>
    function searchTable() {
  const input = document.getElementById("searchInput");
  const filter = input.value.toLowerCase();
  const rows = document.querySelectorAll("tbody tr");

  rows.forEach(row => {
    const serialNoCell = row.cells[0]; // SerialNo column
    if (serialNoCell) {
      const serialNoText = serialNoCell.textContent.toLowerCase();
      row.style.display = serialNoText.includes(filter) ? "" : "none";
    }
  });
}


    function fillForm(serialNo, facId, stage, model, installedDate, servicePersonId) {
      console.log(serialNo, facId, model, installedDate, servicePersonId);
      // You can add your form-filling logic here
    }
  </script>

</body>
</html>
