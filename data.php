<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>BC-Agro Tronics - Data View</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen font-sans bg-cover bg-no-repeat bg-center" style="background-image: linear-gradient(to left, rgba(255, 128, 128, 0.1),rgba(211, 134, 119, 0.4)), url('img/background.jpg');">

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
    <div class="backdrop-blur-md bg-white/20 rounded-2xl shadow-xl p-6 flex items-center gap-6">
      <img src="img/logo.png" alt="Logo" class="w-28 h-20 md:w-32 md:h-24 object-contain" />
      <div>
        <h1 class="text-4xl md:text-5xl font-extrabold text-red-700">BC–Agro Tronics</h1>
        <p class="text-xl text-red-100">DATA VIEW</p>
      </div>
    </div>
  </section>

  <!-- Navigation Buttons Grid -->
  <section class="mt-16 px-6">
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 max-w-6xl mx-auto">
      <a href="attendance.php" class="bg-gradient-to-b from-red-200 to-gray-300 shadow-lg rounded-full py-4 px-8 font-semibold text-gray-800 text-center hover:scale-105 transition duration-200">
        ATTENDANCE
      </a>
      <a href="machine.php" class="bg-gradient-to-b from-red-200 to-gray-300 shadow-lg rounded-full py-4 px-8 font-semibold text-gray-800 text-center hover:scale-105 transition duration-200">
        MACHINES
      </a>
      <a href="service.php" class="bg-gradient-to-b from-red-200 to-gray-300 shadow-lg rounded-full py-4 px-8 font-semibold text-gray-800 text-center hover:scale-105 transition duration-200">
        DAILY SERVICES
      </a>
      <a href="amc.php" class="bg-gradient-to-b from-red-200 to-gray-300 shadow-lg rounded-full py-4 px-8 font-semibold text-gray-800 text-center hover:scale-105 transition duration-200">
        AMC
      </a>
      <a href="compressor.php" class="bg-gradient-to-b from-red-200 to-gray-300 shadow-lg rounded-full py-4 px-8 font-semibold text-gray-800 text-center hover:scale-105 transition duration-200">
        COMPRESSOR
      </a>
      <a href="teamsview.php" class="bg-gradient-to-b from-red-200 to-gray-300 shadow-lg rounded-full py-4 px-8 font-semibold text-gray-800 text-center hover:scale-105 transition duration-200">
        View Team Members
      </a>
      <a href="viewmachine.php" class="bg-gradient-to-b from-red-200 to-gray-300 shadow-lg rounded-full py-4 px-8 font-semibold text-gray-800 text-center hover:scale-105 transition duration-200">
        View Color Sorters
      </a>
      <a href="viewcompressors.php" class="bg-gradient-to-b from-red-200 to-gray-300 shadow-lg rounded-full py-4 px-8 font-semibold text-gray-800 text-center hover:scale-105 transition duration-200">
        View Oil Compressors
      </a>
      <a href="factoryview.php" class="bg-gradient-to-b from-red-200 to-gray-300 shadow-lg rounded-full py-4 px-8 font-semibold text-gray-800 text-center hover:scale-105 transition duration-200">
        Seach Factories
      </a>
      <a href="maintain.php" class="bg-gradient-to-b from-red-200 to-gray-300 shadow-lg rounded-full py-4 px-8 font-semibold text-gray-800 text-center hover:scale-105 transition duration-200">
        View Maintainance
      </a>
    </div>
  </section>
</body>
</html>
