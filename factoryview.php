<?php


session_start();

// Define allowed roles
$allowed_roles = ['admin', 'manager'];

// Check if the user's role is not in the allowed roles
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], $allowed_roles)) {
    
    // Redirect to the login page if not authorized
    header("Location: index.php");
    exit();
}


require_once 'database.php';

$db = new Database();
$conn = $db->getConnection();

// Fetch Data (with SerialNo from machines table)
$factories = [];
$result = $conn->query("
    SELECT f.*, m.SerialNo 
    FROM factories f
    LEFT JOIN machines m ON f.FacId = m.FacId
    ORDER BY f.created_at DESC
");
while ($row = $result->fetch_assoc()) {
    $factories[] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Factories</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            background-image: linear-gradient(to left, rgba(255, 128, 128, 0.05), rgba(211, 134, 119, 0.44)), url('img/background.jpg');
            background-size: cover;
            background-position: right;
            background-repeat: no-repeat;
            background-attachment: fixed;
            min-height: 100vh;
        }
    </style>
</head>
<body class="min-h-screen font-sans">

<!-- Back Button -->
<div class="absolute top-10 right-10 z-50">
    <img 
        src="img/back.png" 
        onclick="history.back()" 
        alt="Back" 
        class="w-14 h-14 cursor-pointer transition duration-400 ease-in-out transform hover:scale-110 hover:rotate-[-20deg] active:scale-95 active:rotate-[5deg]" 
    />
</div>

<!-- Header Section -->
<section class="flex items-center justify-center pt-10 px-4">
    <div class="backdrop-blur-md bg-white/20 rounded-2xl shadow-xl p-6 flex items-center gap-6 mb-6">
        <img src="img/logo.png" alt="Logo" class="w-28 h-20 md:w-32 md:h-24 object-contain" />
        <div>
            <h1 class="text-4xl md:text-5xl font-extrabold text-red-700">BC–Agro Tronics</h1>
            <p class="text-xl text-red-400">Manage Factories</p>
        </div>
    </div>
</section>

<!-- Main Content Section -->
<section class="flex flex-col items-center px-4">

    <!-- Title -->
    <h3 class="text-2xl font-semibold text-red-700 mb-4">Existing Factories</h3>

    <!-- Search Bar -->
    <!-- Search Bars -->
<div class="flex flex-col md:flex-row gap-4 mb-4 w-full max-w-4xl">
    <input 
        type="text" 
        id="searchName" 
        placeholder="Search by Factory Name..." 
        class="flex-1 p-2 border rounded shadow-sm focus:outline-none focus:ring-2 focus:ring-red-300" 
        onkeyup="filterTable()"
    >
    <input 
        type="text" 
        id="searchLocation" 
        placeholder="Search by Location..." 
        class="flex-1 p-2 border rounded shadow-sm focus:outline-none focus:ring-2 focus:ring-red-300" 
        onkeyup="filterTable()"
    >
</div>


    <!-- Factories Table -->
    <div class="w-full max-w-6xl overflow-x-auto bg-white/60 rounded-2xl shadow-lg p-4">
        <table class="w-full table-auto border-collapse">
            <thead class="bg-red-200 text-red-800 sticky top-0">
                <tr>
                    <th class="p-3 border-b">ID</th>
                    <th class="p-3 border-b">Name</th>
                    <th class="p-3 border-b">Serial No</th>
                    <th class="p-3 border-b">Location</th>
                    <th class="p-3 border-b">Team ID</th>
                    <th class="p-3 border-b">Created</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($factories as $fac): ?>
                    <tr class="cursor-pointer hover:bg-red-100 transition" onclick='fillForm(<?= json_encode($fac) ?>)'>
                        <td class="p-3 border-b"><?= $fac['FacId'] ?></td>
                        <td class="p-3 border-b"><?= htmlspecialchars($fac['FacName']) ?></td>
                        <td class="p-3 border-b"><?= $fac['SerialNo'] ?? '-' ?></td>
                        <td class="p-3 border-b"><?= htmlspecialchars($fac['Location']) ?></td>
                        <td class="p-3 border-b"><?= htmlspecialchars($fac['teamId']) ?></td>
                        <td class="p-3 border-b"><?= $fac['created_at'] ?></td>
                    </tr>
                <?php endforeach; ?>

                <?php if (empty($factories)): ?>
                    <tr>
                        <td colspan="6" class="p-4 text-center text-gray-500">No factories found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

</section>

<script>
function filterTable() {
    let nameInput = document.getElementById("searchName").value.toLowerCase();
    let locationInput = document.getElementById("searchLocation").value.toLowerCase();
    let rows = document.querySelectorAll("table tbody tr");

    rows.forEach(row => {
        let nameCell = row.children[1]; // Factory Name column
        let locationCell = row.children[3]; // Location column

        let matchesName = !nameInput || (nameCell && nameCell.textContent.toLowerCase().includes(nameInput));
        let matchesLocation = !locationInput || (locationCell && locationCell.textContent.toLowerCase().includes(locationInput));

        if (matchesName && matchesLocation) {
            row.style.display = "";
        } else {
            row.style.display = "none";
        }
    });
}
</script>


</body>
</html>
