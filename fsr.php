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
 ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Field Service Report</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen font-sans bg-cover bg-no-repeat bg-right" style="background-image: linear-gradient(to left, rgba(255, 128, 128, 0.05),rgba(211, 134, 119, 0.44)), url('background.jpg');">

   <!-- Back Button - Top Right Corner -->
   <div class="absolute top-10 right-10 z-50">
  <img 
    src="back.png" 
    onclick="history.back()" 
    alt="Back" 
    class="w-14 h-14 cursor-pointer transition duration-400 ease-in-out transform hover:scale-110 hover:rotate-[-20deg] active:scale-95 active:rotate-[5deg]" 
  />
  </div>

   <!-- Header with Logo and Title in a Blurred Background -->
   <section class="flex items-center justify-center pt-10 px-4">
    <div class="backdrop-blur-md bg-white/20 rounded-2xl shadow-xl p-6 flex items-center gap-6 mb-6">
      <img src="logo.png" alt="Logo" class="w-28 h-20 md:w-32 md:h-24 object-contain" />
      <div>
        <h1 class="text-4xl md:text-5xl font-extrabold text-red-700 ">BC–Agro Tronics</h1>
        <p class="text-xl text-red-400">FSR Update</p>
      </div>
    </div>
  </section>

  <div class="max-w-6xl mx-auto bg-white p-10 shadow-lg rounded-xl space-y-6">
    <div class="flex items-center text-center border-b pb-4">
      <h1 class="text-3xl font-bold uppercase">Field Service Report</h1>
      <p class="text-sm mt-1">Bartleet Agro Tronics (Pvt) Ltd. | Member of Bartleet Group</p>
      <p class="text-sm">Warehouse Complex, 211/10, Veluwana Place, Colombo 09</p>
      <p class="text-sm">Tel: 0117681492 | Email: info@bartleetagro.com</p>
      <div class="flex items-center n w-60 h-15">
      
      </div>
       </div>
       <form action="/submit" method="POST">
      <div class="flex justify-between items-center mt-4">
        <span>No: <input type="text" class="border-b border-black w-16 text-center" /></span>
      </div><br>
   

    <div class="grid grid-cols-2 gap-4">
      <div>
        <label class="font-semibold">Factory/Client Name:</label>
        <input type="text" class="w-full border px-2 py-1 rounded" />
      </div>
      <div>
        <label class="font-semibold">Machine:</label>
        <input type="text" class="w-full border px-2 py-1 rounded" />
      </div>
      <div>
        <label class="font-semibold">Make & Model:</label>
        <input type="text" class="w-full border px-2 py-1 rounded" />
      </div>
      <div>
        <label class="font-semibold">Serial No.:</label>
        <input type="text" class="w-full border px-2 py-1 rounded" />
      </div>
      <div class="col-span-2">
        <label class="font-semibold">Service Personnel Name:</label>
        <input type="text" class="w-full border px-2 py-1 rounded" />
      </div>
    </div>

    <div class="grid grid-cols-2 gap-6">
      <div>
        <h3 class="font-semibold mb-2">Travel Log</h3>
        <table class="table-auto w-full border text-sm">
          <thead>
            <tr class="bg-gray-100">
              <th class="border px-2 py-1">Description</th>
              <th class="border px-2 py-1">Date</th>
              <th class="border px-2 py-1">Time</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td class="border px-2 py-1">Departure from Colombo</td>
              <td class="border px-2 py-1"><input type="date" class="w-full" /></td>
              <td class="border px-2 py-1"><input type="time" class="w-full" /></td>
            </tr>
            <tr>
              <td class="border px-2 py-1">Arrival at Factory</td>
              <td class="border px-2 py-1"><input type="date" class="w-full" /></td>
              <td class="border px-2 py-1"><input type="time" class="w-full" /></td>
            </tr>
            <tr>
              <td class="border px-2 py-1">Departure from Factory</td>
              <td class="border px-2 py-1"><input type="date" class="w-full" /></td>
              <td class="border px-2 py-1"><input type="time" class="w-full" /></td>
            </tr>
          </tbody>
        </table>
      </div>
      <div>
        <h3 class="font-semibold mb-2">Service Type</h3>
        <div class="space-y-2">
          <label><input type="checkbox" class="mr-2" />Site Inspection</label><br/>
          <label><input type="checkbox" class="mr-2" />Installation</label><br/>
          <label><input type="checkbox" class="mr-2" />Repairs</label><br/>
          <label><input type="checkbox" class="mr-2" />Service & Maintenance</label>
        </div>
      </div>
    </div>

    <div>
      <label class="font-semibold">Description of Work:</label>
      <textarea class="w-full border rounded px-2 py-1" rows="3"></textarea>
    </div>

    <div>
      <h3 class="font-semibold mb-2">Replacements</h3>
      <table class="table-auto w-full border text-sm">
        <thead class="bg-gray-100">
          <tr>
            <th class="border px-2 py-1">Description of Replacement</th>
            <th class="border px-2 py-1">Qty</th>
            <th class="border px-2 py-1">Remarks</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td class="border px-2 py-1"><input type="text" class="w-full" /></td>
            <td class="border px-2 py-1"><input type="number" class="w-full" /></td>
            <td class="border px-2 py-1"><input type="text" class="w-full" /></td>
          </tr>
          <!-- More rows as needed -->
        </tbody>
      </table>
    </div>

    <div>
      <label class="font-semibold">Customer Comments:</label>
      <textarea class="w-full border rounded px-2 py-1" rows="2"></textarea>
    </div>

    <div class="flex justify-between">
     
      <div>
        <label class="font-semibold">Date</label>
        <input type="date" class="border px-2 py-1 rounded" />
      </div>
    </div>

    <div>
      <label class="font-semibold">Machine Status</label>
      <textarea class="w-full border rounded px-2 py-1" rows="2"></textarea>
    </div>
    <div class="grid grid-cols-2 gap-4">
      <div>
        <label class="font-semibold">Air Compressor Capacity</label>
        <input type="text" class="w-full border px-2 py-1 rounded" />
      </div>
      <div>
        <label class="font-semibold">Air Compressor Total Run Time</label>
        <input type="text" class="w-full border px-2 py-1 rounded" />
      </div>
    </div>

    <div class="flex justify-between mt-6">
      <div>
        <label class="font-semibold">Name of Client's Representative</label>
        <input type="text" class="border-b w-64 block mt-1" />
      </div>
         <div>
        <label class="font-semibold">Date</label>
        <input type="date" class="border px-2 py-1 rounded" />
      </div>
    </div>
    <button type="submit" class="mt-4 px-7 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
    Submit
  </button>
  </div>
 
  </form>
</body>
</html>
