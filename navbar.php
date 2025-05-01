<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>BC-Agro Tronics Dashboard</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-r from-white to-red-100 min-h-screen font-sans">

  <!-- Navbar -->
  <nav class="bg-transparent px-4 py-6">
    <div class="flex justify-between items-center">
      <div class="flex items-center space-x-2">
      <img src="img/logo.png" alt="Logo" onclick="location.href='index.php'" class="w-48 h-20 md:w-32 md:h-24 lg:w-46 lg:h-32" />

      </div>

      <!-- Hamburger Icon -->
      <div class="md:hidden">
        <button id="menu-button" class="text-red-600 focus:outline-none">
          <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M4 6h16M4 12h16M4 18h16" />
          </svg>
        </button>
      </div>

      <!-- Desktop Links -->
      <div class="hidden md:flex space-x-6 font-bold text-red-600 text-xl items-center">
        <a href="home.php" class="hover:text-red-800">HOME</a>
        <a href="data.php" class="hover:text-red-800">VIEW EVALUVATION</a>
        
        <span id="datetime" class="text-white text-lg font-medium"></span>
      </div>
    </div>

    <!-- Mobile Links -->
    <div id="mobile-menu" class="hidden md:hidden mt-4 flex flex-col space-y-3 font-bold text-red-600 text-lg">
      <a href="index.php" class="hover:text-red-800">HOME</a>
      <a href="data.php" class="hover:text-red-800">DATA</a>
      
      <span id="mobile-datetime" class="text-gray-700 text-base font-medium"></span>
    </div>
  </nav>

  <script>
    // Toggle menu
    const menuButton = document.getElementById('menu-button');
    const mobileMenu = document.getElementById('mobile-menu');

    menuButton.addEventListener('click', () => {
      mobileMenu.classList.toggle('hidden');
    });

    // DateTime update
    function updateDateTime() {
      const now = new Date();
      const options = {
        weekday: 'short',
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit',
        hour12: true
      };
      const formattedTime = now.toLocaleString('en-US', options);
      document.getElementById('datetime').innerText = formattedTime;
      document.getElementById('mobile-datetime').innerText = formattedTime;
    }

    setInterval(updateDateTime, 1000);
    updateDateTime();
  </script>

</body>
</html>
