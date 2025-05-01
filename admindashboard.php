<?php
session_start();

// Check if the user is logged in as 'admin'

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    
    // Redirect to the login page if not logged in as admin
    header("Location: index.php");
    exit();
}

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'database.php';
$db = new Database();
$conn = $db->getConnection();

$tables = [];
$result = $conn->query("SHOW TABLES");
while ($row = $result->fetch_row()) {
    $tables[] = $row[0];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Craze Kicks</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        body {
            background: url('img/background.jpg') no-repeat center center fixed;
            background-size: cover;
        }
        .glass {
            background: rgba(0, 0, 0, 0.15);
            backdrop-filter: blur(10px);
        }
    </style>
</head>
<body class="min-h-screen flex flex-col items-center justify-start px-4 pt-20 text-white font-sans">

    <?php include "navbar.php" ?>

    <div class="w-full max-w-7xl glass rounded-xl p-8 shadow-lg">
        <h1 class="text-4xl md:text-5xl font-bold text-center text-orange-300 mb-10">Welcome to the Admin Dashboard!</h1>

        <div class="flex flex-col md:flex-row items-center justify-between mb-8">
            <a href="manage.php" class="bg-red-500 hover:bg-red-400 text-white font-semibold py-3 px-6 rounded-lg shadow transition duration-300 mb-4 md:mb-0">Modify Data</a>
            <a href="logout.php" class="bg-red-500 hover:bg-red-400 text-white font-semibold py-3 px-6 rounded-lg shadow transition duration-300 mb-4 md:mb-0">Log Out</a>
            <p class="text-lg text-gray-300">Database: <span class="font-semibold text-white">bartleet</span></p>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full table-auto border-collapse border border-orange-300">
                <thead class="bg-orange-300 text-black">
                    <tr>
                        <th class="py-3 px-4 text-left">#</th>
                        <th class="py-3 px-4 text-left">Table Name</th>
                        <th class="py-3 px-4 text-left">Action</th>
                    </tr>
                </thead>
                <tbody class="bg-white text-black">
                    <?php foreach ($tables as $index => $table): ?>
                        <tr class="hover:bg-orange-100 transition">
                            <td class="py-2 px-4"><?= $index + 1 ?></td>
                            <td class="py-2 px-4"><?= htmlspecialchars($table) ?></td>
                            <td class="py-2 px-4">
                                <a href="viewtable.php?name=<?= urlencode($table) ?>" class="bg-orange-400 hover:bg-orange-500 text-white py-1 px-3 rounded">🔍 View Table</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (count($tables) === 0): ?>
                        <tr>
                            <td colspan="3" class="text-center py-4 text-gray-500">No tables found in the database.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>

<!-- Close database connection at the end -->
<?php
$conn->close();
?>
