<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include the connection
require_once 'database.php';

// Initialize the Database class
$db = new Database();

// Initialize error message
$errorMessage = '';

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $role = trim($_POST['role']);
    $password = trim($_POST['password']);

    // Define passwords for each role
    $rolePasswords = [
        'admin' => 'admin',
        'manager' => 'manager',
        'data_entry' => 'data',
    ];

    // Check if selected role exists
    if (array_key_exists($role, $rolePasswords)) {
        // Compare entered password with role-specific password
        if ($password === $rolePasswords[$role]) {
            // Store user details in session (assuming other details are not required)
            $_SESSION['role'] = $role;

            // Redirect based on role
            switch ($role) {
                case 'admin':
                    header("Location: admindashboard.php");
                    break;
                case 'manager':
                    header("Location: home.php");
                    break;
                case 'data_entry':
                    header("Location: manage.php");
                   
            }
            exit();
        } else {
            $errorMessage = "Incorrect password for selected role.";
        }
    } else {
        $errorMessage = "Invalid role selected.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        body {
            background: url('img/background.jpg') no-repeat center center fixed;
            background-size: cover;
        }
    </style>
</head>
<body class="flex items-center justify-center min-h-screen bg-gray-100 bg-opacity-90">
    <div class="w-full max-w-md bg-white bg-opacity-90 shadow-xl rounded-lg p-6">
        <img include="href='logo2.png'" />
        <h2 class="text-3xl font-bold text-center text-gray-800 mb-4">
            Welcome to <span class="text-red-600">BC</span><br><span class="text-yellow-700">Agro-Tronics</span>
        </h2>

        <!-- Display error message -->
        <?php if (!empty($errorMessage)): ?>
            <div class="mb-4 text-red-600 text-center font-semibold">
                <?php echo $errorMessage; ?>
            </div>
        <?php endif; ?>

        <!-- Login Form -->
        <form action="index.php" method="POST" class="space-y-4">
            <div>
                <label for="role" class="block text-sm font-medium text-gray-700">Select Role</label>
                <select id="role" name="role" required
                        class="w-full px-3 py-2 mt-1 border border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500">
                    <option value="" disabled selected>Select your role</option>
                    <option value="admin">Admin</option>
                    <option value="manager">Manager</option>
                    <option value="data_entry">Data Entry</option>
                </select>
            </div>
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                <input type="password" id="password" name="password" required
                       class="w-full px-3 py-2 mt-1 border border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500"
                       placeholder="Enter your password">
            </div>
            <button type="submit" class="w-full bg-gradient-to-r from-red-300 to-yellow-300 text-gray-700 font-bold py-2 rounded-xl shadow-lg hover:from-red-300 hover:to-green-200 focus:outline-none focus:ring-2 focus:ring-green-950 focus:ring-offset-2">
                Log In
            </button>
        </form>
    </div>
</body>
</html>
