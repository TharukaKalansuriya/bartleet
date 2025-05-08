<?php
session_start();

// Check if the user is logged in as 'admin'
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    // Redirect to the login page if not logged in as admin
    header("Location: index.php");
    exit();
}

// Define the roles that can have password changes
$roles = ['admin', 'manager', 'data_entry'];

// Define file path for password storage
$passwordFile = 'rolepasswords.php';

// Initialize success and error messages
$successMessage = '';
$errorMessage = '';

// Check if the password file exists, if not create it with default passwords
if (!file_exists($passwordFile)) {
    $defaultPasswords = [
        'admin' => 'admin',
        'manager' => 'manager',
        'data_entry' => 'data',
    ];
    
    $fileContent = "<?php\n";
    $fileContent .= "// Role passwords - Do not edit directly\n";
    $fileContent .= "return [\n";
    foreach ($defaultPasswords as $role => $password) {
        $fileContent .= "    '$role' => '$password',\n";
    }
    $fileContent .= "];\n";
    
    file_put_contents($passwordFile, $fileContent);
}

// Load current passwords
$rolePasswords = include($passwordFile);

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['change_password'])) {
    $role = trim($_POST['role']);
    $newPassword = trim($_POST['new_password']);
    $confirmPassword = trim($_POST['confirm_password']);
    
    // Validate input
    if (!in_array($role, $roles)) {
        $errorMessage = "Invalid role selected.";
    } else if (empty($newPassword)) {
        $errorMessage = "Password cannot be empty.";
    } else if ($newPassword !== $confirmPassword) {
        $errorMessage = "Passwords do not match.";
    } else {
        // Update password
        $rolePasswords[$role] = $newPassword;
        
        // Save to file
        $fileContent = "<?php\n";
        $fileContent .= "// Role passwords - Do not edit directly\n";
        $fileContent .= "return [\n";
        foreach ($rolePasswords as $r => $p) {
            $fileContent .= "    '$r' => '$p',\n";
        }
        $fileContent .= "];\n";
        
        if (file_put_contents($passwordFile, $fileContent)) {
            $successMessage = "Password for $role has been updated successfully.";
        } else {
            $errorMessage = "Failed to update password. Check file permissions.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Role Passwords - BC Agro-Tronics</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        :root {
            --primary: rgba(243, 60, 60, 0.81);
            --primary-hover: #FF8E63;
            --secondary: #2E2E3A;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            background: url('img/background.jpg') no-repeat center center fixed;
            background-size: cover;
        }
        
        .glass-card {
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(10px);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.18);
        }
        
        .btn-primary {
            background-color: var(--primary);
            transition: all 0.2s;
        }
        
        .btn-primary:hover {
            background-color: var(--primary-hover);
        }
        
        .form-container {
            background: rgba(255, 255, 255, 0.9);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        
        .header-gradient {
            background: linear-gradient(90deg, var(--primary) 0%, #FF8E63 100%);
        }
        
        #loader {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: #000;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 9999;
            transition: opacity 0.8s ease, visibility 0.8s ease;
        }

        #loader.fade-out {
            opacity: 0;
            visibility: hidden;
        }

        body.loading {
            overflow: hidden;
        }
    </style>
</head>
<body class="min-h-screen flex flex-col loading">
    <!-- Loader -->
    <div id="loader">
        <img src="img/loading.gif" alt="Loading...">
    </div>

    <?php include "navbar.php" ?>

    <div id="content" class="container mx-auto px-4 py-8 flex-grow" style="display: none;">
        <div class="glass-card rounded-xl p-8 text-white">
            <div class="flex flex-col md:flex-row items-center justify-between mb-8">
                <div>
                    <h1 class="text-4xl md:text-5xl font-bold">Password Management</h1>
                    <p class="text-lg opacity-80 mt-2">Update role passwords for BC Agro-Tronics system</p>
                </div>
                
                <div class="flex flex-col sm:flex-row gap-4 mt-6 md:mt-0">
                    <a href="admindashboard.php" class="bg-gray-700 hover:bg-gray-600 text-white font-semibold py-3 px-6 rounded-lg shadow text-center">
                        <i class="fas fa-chevron-left mr-2"></i> Back to Dashboard
                    </a>
                </div>
            </div>

            <!-- Success message -->
            <?php if (!empty($successMessage)): ?>
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-md">
                    <div class="flex items-center">
                        <i class="fas fa-check-circle mr-2"></i>
                        <p><?php echo $successMessage; ?></p>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Error message -->
            <?php if (!empty($errorMessage)): ?>
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-md">
                    <div class="flex items-center">
                        <i class="fas fa-exclamation-circle mr-2"></i>
                        <p><?php echo $errorMessage; ?></p>
                    </div>
                </div>
            <?php endif; ?>

            <div class="form-container rounded-xl overflow-hidden">
                <div class="header-gradient text-white px-6 py-4">
                    <h2 class="text-xl font-bold">Change Role Password</h2>
                </div>
                <div class="p-6 bg-white">
                    <form method="POST" class="space-y-6">
                        <div>
                            <label for="role" class="block text-sm font-medium text-gray-700 mb-1">Select Role</label>
                            <select id="role" name="role" required class="w-full bg-red-400 border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500">
                                <option value="" disabled selected>Choose a role</option>
                                <?php foreach ($roles as $role): ?>
                                    <option value="<?php echo $role; ?>"><?php echo ucfirst(str_replace('_', ' ', $role)); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div>
                            <label for="current_password" class="block text-sm font-medium text-gray-700 mb-1">Current Password</label>
                            <input type="text" id="current_password" class="w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 bg-gray-100 text-gray-500" readonly>
                        </div>
                        
                        <div>
                            <label for="new_password" class="block text-sm font-medium text-gray-700 mb-1">New Password</label>
                            <input type="password" id="new_password" name="new_password" required class="w-full border text-gray-900 border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500">
                        </div>
                        
                        <div>
                            <label for="confirm_password" class="block text-sm font-medium text-gray-700 mb-1">Confirm New Password</label>
                            <input type="password" id="confirm_password" name="confirm_password" required class="w-full border text-gray-900 border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500">
                        </div>
                        
                        <div>
                            <button type="submit" name="change_password" class="btn-primary text-white font-semibold py-3 px-6 rounded-lg shadow w-full md:w-auto">
                                <i class="fas fa-key mr-2"></i> Update Password
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            
            <div class="mt-8 bg-white bg-opacity-10 p-4 rounded-lg backdrop-filter backdrop-blur-sm">
                <div class="flex items-center">
                    <i class="fas fa-info-circle text-2xl mr-3 text-amber-300"></i>
                    <p class="text-sm">Passwords are stored securely and can only be changed by administrators. Make sure to use strong passwords for better security.</p>
                </div>
            </div>
        </div>
    </div>

    <footer class="text-center py-4 text-white bg-black bg-opacity-50">
        <p>© <?php echo date('Y'); ?> BC Agro-Tronics Admin Dashboard</p>
    </footer>

    <script>
        // Loading animation
        window.addEventListener('load', function() {
            document.body.classList.remove('loading'); 
            const loader = document.getElementById('loader');

            // Add fade-out class
            loader.classList.add('fade-out');

            // Remove loader after fade-out
            setTimeout(function() {
                loader.style.display = 'none';
                document.getElementById('content').style.display = 'block';
            }, 800);
        });
        
        // Show current password when role is selected
        document.getElementById('role').addEventListener('change', function() {
            const role = this.value;
            const currentPasswordField = document.getElementById('current_password');
            
            // For security, we'll just show masked values instead of actual passwords
            const passwords = <?php echo json_encode($rolePasswords); ?>;
            if (role in passwords) {
                const password = passwords[role];
                const maskedPassword = password.substring(0, 2) + '•'.repeat(password.length - 2);
                currentPasswordField.value = maskedPassword;
            } else {
                currentPasswordField.value = '';
            }
        });
        
        // Password match validation
        document.getElementById('confirm_password').addEventListener('input', function() {
            const newPassword = document.getElementById('new_password').value;
            const confirmPassword = this.value;
            
            if (newPassword === confirmPassword) {
                this.setCustomValidity('');
            } else {
                this.setCustomValidity('Passwords do not match');
            }
        });
        
        document.getElementById('new_password').addEventListener('input', function() {
            const confirmPassword = document.getElementById('confirm_password');
            confirmPassword.dispatchEvent(new Event('input'));
        });
    </script>
</body>
</html>