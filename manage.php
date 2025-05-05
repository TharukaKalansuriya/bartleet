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
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Management Dashboard - BC Agro Tronics</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen font-sans bg-cover bg-no-repeat bg-right" style="background-image: linear-gradient(to left, rgba(255, 128, 128, 0.05),rgba(211, 134, 119, 0.44)), url('img/background.jpg');">


<table>
<div class="absolute top-10 right-10 z-50">
  <img 
    src="img/back.png" 
    onclick="history.back()" 
    alt="Back" 
    class="w-14 h-14 cursor-pointer transition duration-400 ease-in-out transform hover:scale-110 hover:rotate-[-20deg] active:scale-95 active:rotate-[5deg]" 
  />
</div>


  <!-- Page Content -->
 
  <section class="flex items-center justify-center pt-10 px-4">
    <div class="backdrop-blur-md bg-white/20 rounded-2xl shadow-xl p-6 flex items-center gap-6">
      <img src="img/logo.png" alt="Logo" class="w-28 h-20 md:w-32 md:h-24 object-contain" />
      <div>
        <h1 class="text-4xl md:text-5xl font-extrabold text-red-700">BC–Agro Tronics</h1>
        <p class="text-xl text-red-100">UPDATE DATA</p>
      </div>
    </div>
    </section>
  
    <section class="mt-16 px-6">
       <!-- Navigation B-->

    <!-- Buttons -->
    <div class="mt-10 grid grid-cols-3 gap-6 max-w-4xl ">
      <a href="addmember.php" class="bg-gradient-to-b from-red-200 to-gray-300 shadow-lg rounded-full py-4 px-8 font-semibold text-gray-800 hover:scale-105 transition duration-200 inline-block text-center">
        Add Team Members
      </a>
      <a href="addteam.php" class="bg-gradient-to-b from-red-200 to-gray-300 shadow-lg rounded-full py-4 px-8 font-semibold text-gray-800 hover:scale-105 transition duration-200 inline-block text-center">
       Add Team
      </a>
      <a href="addmachine.php" class="bg-gradient-to-b from-red-200 to-gray-300 shadow-lg rounded-full py-4 px-8 font-semibold text-gray-800 hover:scale-105 transition duration-200 inline-block text-center">
        Add Machines
      </a>
      <a href="addcompressor.php" class="bg-gradient-to-b from-red-200 to-gray-300 shadow-lg rounded-full py-4 px-8 font-semibold text-gray-800 hover:scale-105 transition duration-200 inline-block text-center">
        Add Oil Compressors
      </a>
      <a href="addfactories.php" class="bg-gradient-to-b from-red-200 to-gray-300 shadow-lg rounded-full py-4 px-8 font-semibold text-gray-800 hover:scale-105 transition duration-200 inline-block text-center">
        Add Factories
      </a>  
      <a href="markattendance.php" class="bg-gradient-to-b from-red-200 to-gray-300 shadow-lg rounded-full py-4 px-8 font-semibold text-gray-800 hover:scale-105 transition duration-200 inline-block text-center">
        Mark Attendance
      </a>  
      <a href="update_counts.php" class="bg-gradient-to-b from-red-200 to-gray-300 shadow-lg rounded-full py-4 px-8 font-semibold text-gray-800 hover:scale-105 transition duration-200 inline-block text-center">
        Update Home Screen Counts
      </a> 
      <a href="repairUpdate.php" class="bg-gradient-to-b from-red-200 to-gray-300 shadow-lg rounded-full py-4 px-8 font-semibold text-gray-800 hover:scale-105 transition duration-200 inline-block text-center">
        FSR Input
      </a>
      <a href="manage_maintenance.php" class="bg-gradient-to-b from-red-200 to-gray-300 shadow-lg rounded-full py-4 px-8 font-semibold text-gray-800 hover:scale-105 transition duration-200 inline-block text-center">
        Insert Maintainance
      </a>
      <a href="adddailyservice.php" class="bg-gradient-to-b from-red-200 to-gray-300 shadow-lg rounded-full py-4 px-8 font-semibold text-gray-800 hover:scale-105 transition duration-200 inline-block text-center">
        Shedule Daily Services
      </a>
    </div>
    </section>
  
</table>

</body>
</html>
