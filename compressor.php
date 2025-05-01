<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Compressors View - BC Agro Tronics</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-r from-white to-red-100 min-h-screen font-sans text-gray-800">
<?php include "navbar.php" ?>

  <!-- Header -->
  <header class="p-6  text-red-500 shadow-md">
    <h1 class="text-5xl font-bold">Compressors Overview</h1>
    <p class="text-lg">Factory & Team-based Compressor Monitoring</p>
  </header>

  <main class="p-8 flex flex-col lg:flex-row gap-10">

    <!-- Left: Table Section -->
    <section class="w-full lg:w-1/2">
      <h2 class="text-xl font-semibold mb-4">Compressors Table</h2>
      <div class="overflow-auto max-h-[500px] border rounded-lg shadow bg-white">
        <table class="min-w-full text-sm">
          <thead class="bg-red-100 sticky top-0">
            <tr>
              <th class="px-4 py-2 text-left">Serial Number</th>
              <th class="px-4 py-2 text-left">Factory Name</th>
            </tr>
          </thead>
          <tbody>
            <tr class="hover:bg-red-50 cursor-pointer">
              <td class="px-4 py-2">CMP00123</td>
              <td class="px-4 py-2">Factory Alpha</td>
            </tr>
            <tr class="hover:bg-red-50 cursor-pointer">
              <td class="px-4 py-2">CMP00124</td>
              <td class="px-4 py-2">Factory Beta</td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Machine Cards -->
      <div class="mt-8 space-y-6">
        <h2 class="text-xl font-semibold">Machines Details</h2>

        <div class="bg-white p-6 rounded-xl shadow hover:shadow-lg transition">
          <h3 class="text-lg font-bold text-red-600">Compressor CMP00123</h3>
          <p class="text-sm mt-1 text-gray-600">Assigned Team: <span class="font-semibold text-gray-800">Team Alpha</span></p>

          <div class="mt-4">
            <p class="text-sm"><strong>Total Runtime:</strong> 5,400 hours</p>
            <p class="text-sm"><strong>Earliest Reported Date:</strong> 2023-02-01</p>
            <p class="text-sm"><strong>Latest Reported Date:</strong> 2025-03-15</p>
          </div>
        </div>
      </div>
    </section>

    <!-- Right: Team Section -->
    <section class="w-full lg:w-1/2">
      <h2 class="text-xl font-semibold mb-4">Select Team</h2>
      
      <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
        
        <!-- Sample Teams -->
        <button class="bg-red-100 hover:bg-red-200 p-4 rounded-lg shadow text-sm font-semibold">Team Alpha</button>
        <button class="bg-red-100 hover:bg-red-200 p-4 rounded-lg shadow text-sm font-semibold">Team Beta</button>
        <button class="bg-red-100 hover:bg-red-200 p-4 rounded-lg shadow text-sm font-semibold">Team Gamma</button>
        <button class="bg-red-100 hover:bg-red-200 p-4 rounded-lg shadow text-sm font-semibold">Team Delta</button>
      </div>

      <!-- Team Info Display -->
      <div class="mt-8">
        <h3 class="text-lg font-bold text-red-600 mb-2">Team Alpha - Summary</h3>

        <div class="bg-white p-6 rounded-xl shadow space-y-4">
          <div>
            <h4 class="font-semibold mb-1">Jobs Done</h4>
            <ul class="list-disc list-inside text-sm text-gray-700">
              <li>Compressor Maintenance – Factory Alpha</li>
              <li>Filter Replacement – Factory Beta</li>
            </ul>
          </div>

          <div>
            <h4 class="font-semibold mb-1">Factories</h4>
            <ul class="list-disc list-inside text-sm text-gray-700">
              <li>Factory Alpha</li>
              <li>Factory Beta</li>
            </ul>
          </div>

          <div>
            <h4 class="font-semibold mb-1">Machines</h4>
            <ul class="list-disc list-inside text-sm text-gray-700">
              <li>CMP00123</li>
              <li>CMP00126</li>
              <li>CMP00210</li>
            </ul>
          </div>
        </div>
      </div>
    </section>

  </main>
</body>
</html>
