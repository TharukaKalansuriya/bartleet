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
$success = '';
$error = '';

// Fetch all teams for dropdown
$TeamList = $conn->query("SELECT TeamId, Name FROM teams");

// Handle Add / Update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $facId = $_POST['facId'] ?? '';
    $FacName = $_POST['FacName'];
    $Location = $_POST['Location'];
    $TeamID = $_POST['TeamID'];
    $action = $_POST['action'];

    if ($action === 'add') {
        $stmt = $conn->prepare("INSERT INTO factories (FacName, Location, TeamID) VALUES (?, ?, ?)");
        $stmt->bind_param('sss', $FacName, $Location, $TeamID);
        $stmt->execute();
        $success = "Factory added successfully.";
    } elseif ($action === 'update') {
        $stmt = $conn->prepare("UPDATE factories SET FacName=?, Location=?, TeamID=? WHERE facId=?");
        $stmt->bind_param('sssi', $FacName, $Location, $TeamID, $facId);
        $stmt->execute();
        $success = "Factory updated successfully.";
    }
}

// Handle Delete
if (isset($_POST['action']) && $_POST['action'] === 'delete') {
    $facId = $_POST['facId'];

    // First delete associated machines to maintain foreign key integrity
    $conn->query("DELETE FROM machines WHERE FacId = $facId");
    $stmt = $conn->prepare("DELETE FROM factories WHERE facId=?");
    $stmt->bind_param('i', $facId);
    $stmt->execute();
    $success = "Factory and associated machines deleted successfully.";
}

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
            background-image: linear-gradient(to left, rgba(255, 128, 128, 0.05),rgba(211, 134, 119, 0.44)), url('img/background.jpg');
            background-size: cover;
            background-position: right;
            background-repeat: no-repeat;
            background-attachment: fixed;
            min-height: 100vh;
        }
        
        .content-container {
            max-height: calc(100vh - 200px);
            overflow-y: auto;
        }
    </style>
    <script>
        function fillForm(fac) {
            document.getElementById('facId').value = fac.FacId;
            document.getElementById('FacName').value = fac.FacName;
            document.getElementById('Location').value = fac.Location;
            document.getElementById('TeamID').value = fac.TeamID;
            document.getElementById('action').value = 'update';
            document.getElementById('submitBtn').textContent = 'Update Factory';
            document.getElementById('deleteBtn').classList.remove('hidden');
        }

        function resetForm() {
            document.getElementById('factoryForm').reset();
            document.getElementById('facId').value = '';
            document.getElementById('action').value = 'add';
            document.getElementById('submitBtn').textContent = 'Add Factory';
            document.getElementById('deleteBtn').classList.add('hidden');
        }
    </script>
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
                <h1 class="text-4xl md:text-5xl font-extrabold text-red-700">BC–Agro Tronics</h1>
                <p class="text-xl text-red-400">Add New Factories</p>
            </div>
        </div>
    </section>

    <div class="max-w-5xl mx-auto bg-white p-6 rounded shadow mb-10">
        <h2 class="text-2xl font-bold mb-4">Factory Management</h2>

        <?php if ($success): ?>
            <div class="bg-green-100 text-green-700 p-2 rounded mb-4"><?= $success ?></div>
        <?php elseif ($error): ?>
            <div class="bg-red-100 text-red-700 p-2 rounded mb-4"><?= $error ?></div>
        <?php endif; ?>

        <!-- Form -->
        <form id="factoryForm" method="POST" class="space-y-4">
            <input type="hidden" name="facId" id="facId">
            <input type="hidden" name="action" id="action" value="add">

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <input type="text" name="FacName" id="FacName" placeholder="Factory Name" required class="border p-2 rounded w-full">
                <input type="text" name="Location" id="Location" placeholder="Location" required class="border p-2 rounded w-full">
                <div>
                    <label class="block font-semibold text-gray-700">Team ID</label>
                    <select name="TeamID" id="TeamID" class="w-full mt-1 p-3 border border-gray-300 rounded-xl">
                        <option value="">Select a Team</option>
                        <?php
                            mysqli_data_seek($TeamList, 0);
                            while ($Team = $TeamList->fetch_assoc()):
                        ?>
                            <option value="<?= $Team['TeamId'] ?>"><?= $Team['TeamId'] ?> - <?= htmlspecialchars($Team['Name']) ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
            </div>

            <div class="flex space-x-2 mt-2">
                <button type="submit" id="submitBtn" class="bg-blue-500 text-white px-4 py-2 rounded">Add Factory</button>
                <button type="submit" name="action" value="delete" id="deleteBtn" class="bg-red-500 text-white px-4 py-2 rounded hidden" onclick="return confirm('Delete this factory and its machines?')">Delete</button>
                <button type="button" onclick="resetForm()" class="bg-gray-400 text-white px-4 py-2 rounded">Clear</button>
            </div>
        </form>

        <!-- Factory Table -->
        <div class="mt-8">
            <h3 class="text-xl font-semibold mb-2">Existing Factories</h3>

            <!-- Search Bar -->
            <div class="mb-4">
                <input type="text" id="searchInput" placeholder="Search by Factory Name..." class="w-full p-2 border rounded" onkeyup="filterTable()">
            </div>

            <div class="content-container">
                <table class="w-full table-auto border-collapse border border-gray-300">
                    <thead class="bg-gray-200 sticky top-0">
                        <tr>
                            <th class="p-2 border">ID</th>
                            <th class="p-2 border">Name</th>
                            <th class="p-2 border">Serial No</th>
                            <th class="p-2 border">Location</th>
                            <th class="p-2 border">Team ID</th>
                            <th class="p-2 border">Created</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($factories as $fac): ?>
                            <tr class="cursor-pointer hover:bg-gray-100" onclick='fillForm(<?= json_encode($fac) ?>)'>
                                <td class="p-2 border"><?= $fac['FacId'] ?></td>
                                <td class="p-2 border"><?= $fac['FacName'] ?></td>
                                <td class="p-2 border"><?= $fac['SerialNo'] ?? '-' ?></td>
                                <td class="p-2 border"><?= $fac['Location'] ?></td>
                                <td class="p-2 border"><?= $fac['teamId'] ?></td>
                                <td class="p-2 border"><?= $fac['created_at'] ?></td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (empty($factories)): ?>
                            <tr><td colspan="6" class="p-4 text-center text-gray-500">No factories yet.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
    function filterTable() {
        let input = document.getElementById("searchInput").value.toLowerCase();
        let rows = document.querySelectorAll("table tbody tr");
        rows.forEach(row => {
            let nameCell = row.children[1]; // Factory Name column
            if (nameCell && nameCell.textContent.toLowerCase().includes(input)) {
                row.style.display = "";
            } else {
                row.style.display = "none";
            }
        });
    }
    </script>
</body>
</html>