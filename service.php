<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Daily Service Records - BC Agro Tronics</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen font-sans bg-cover bg-no-repeat bg-right" style="background-image: linear-gradient(to left, rgba(255, 128, 128, 0.05),rgba(211, 134, 119, 0.44)), url('img/background.jpg');">

  <!--nav bar-->
  <?php include "navbar.php" ?>
  <!-- Header -->
  <h1 class="text-4xl font-extrabold text-red-600 mb-4">Daily Service Records</h1>

  <!-- Selectors -->
  <div class="flex flex-col md:flex-row items-center gap-4 mb-6">
    <div>
      <label class="block text-red-500 font-semibold mb-1">Select Team:</label>
      <select id="teamSelect" class="w-full md:w-48 p-2 border rounded">
        <option value="Team A">Team A</option>
        <option value="Team B">Team B</option>
        <option value="Team C">Team C</option>
        <option value="Team D">Team D</option>
      </select>
    </div>

    <div>
      <label class="block text-red-500 font-semibold mb-1">Select Date:</label>
      <input type="date" id="datePicker" class="w-full md:w-48 p-2 border rounded" />
    </div>

    <div>
      <label class="block text-red-500 font-semibold mb-1">Service Status:</label>
      <select id="service" class="w-full md:w-48 p-2 border rounded">
        <option value="Pending">Pending</option>
        <option value="Completed">Completed</option>
        <option value="Scheduled">Scheduled</option>
        <option value="Null">Null</option>
      </select>
    </div>
  </div>

  <!-- Tabs -->
  <div class="bg-white p-4 rounded-xl shadow-md">
    <div class="flex border-b mb-4">
      <button id="tabRecords" onclick="switchTab('records')" class="tab-button border-b-2 border-red-500 text-red-600 font-semibold px-4 py-2">
        Service Records
      </button>
      <button id="tabAgenda" onclick="switchTab('agenda')" class="tab-button text-gray-500 hover:text-red-500 px-4 py-2">
        Daily Agenda
      </button>
    </div>

    <!-- Service Records Tab -->
    <div id="recordsTab" class="tab-content">
      <div class="flex justify-between items-center mb-4">
        <h2 class="text-xl font-semibold mb-2 text-red-500">Service Records</h2>
        <input id="searchInput" type="text" placeholder="Input FSR Number to search..." class="border border-red-300 rounded px-3 py-1 focus:outline-none focus:ring-2 focus:ring-red-300" onkeyup="filterTable()" />
      </div>
      <div class="overflow-y-auto max-h-64 border rounded">
        <table class="w-full text-sm text-left text-gray-700">
          <thead class="bg-red-100 text-gray-800">
            <tr>
              <th class="px-4 py-2">Factory Name</th>
              <th class="px-4 py-2">FSR No</th>
              <th class="px-4 py-2">Service Status</th>
            </tr>
          </thead>
          <tbody id="serviceRecords">
            <!-- Sample Rows -->
            <tr><td class="px-4 py-2">Factory One</td><td class="px-4 py-2">FSR-1023</td><td class="px-4 py-2">Completed</td></tr>
            <tr><td class="px-4 py-2">Factory Two</td><td class="px-4 py-2">FSR-1024</td><td class="px-4 py-2">Pending</td></tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Daily Agenda Tab -->
    <div id="agendaTab" class="tab-content hidden">
      <h2 class="text-xl font-semibold mb-2 text-red-500">Daily Agenda</h2>
      <div class="overflow-y-auto max-h-64 border rounded">
        <table class="w-full text-sm text-left text-gray-700">
          <thead class="bg-red-100 text-gray-800">
            <tr>
              <th class="px-4 py-2">Factory Name</th>
              <th class="px-4 py-2">Assigned Team</th>
            </tr>
          </thead>
          <tbody id="agendaRecords">
            <!-- Sample Rows -->
            <tr><td class="px-4 py-2">Factory Alpha</td><td class="px-4 py-2">Team A</td></tr>
            <tr><td class="px-4 py-2">Factory Beta</td><td class="px-4 py-2">Team B</td></tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <script>
    const recordsTab = document.getElementById("recordsTab");
    const agendaTab = document.getElementById("agendaTab");
    const tabRecords = document.getElementById("tabRecords");
    const tabAgenda = document.getElementById("tabAgenda");

    function switchTab(tab) {
      if (tab === "records") {
        recordsTab.classList.remove("hidden");
        agendaTab.classList.add("hidden");
        tabRecords.classList.add("border-b-2", "border-red-500", "text-red-600");
        tabAgenda.classList.remove("border-b-2", "border-red-500", "text-red-600");
      } else {
        agendaTab.classList.remove("hidden");
        recordsTab.classList.add("hidden");
        tabAgenda.classList.add("border-b-2", "border-red-500", "text-red-600");
        tabRecords.classList.remove("border-b-2", "border-red-500", "text-red-600");
      }
    }

    // Switch tab based on selected date
    document.getElementById("datePicker").addEventListener("change", (e) => {
      const selectedDate = new Date(e.target.value);
      const today = new Date();
      today.setHours(0, 0, 0, 0);
      selectedDate.setHours(0, 0, 0, 0);

      if (selectedDate >= today) {
        switchTab("agenda"); // Today or future → Agenda
      } else {
        switchTab("records"); // Past → Records
      }
    });

    // Optional: Filter FSR number in service records
    function filterTable() {
      const input = document.getElementById("searchInput").value.toUpperCase();
      const rows = document.getElementById("serviceRecords").getElementsByTagName("tr");

      for (let i = 0; i < rows.length; i++) {
        const cell = rows[i].getElementsByTagName("td")[1]; // FSR No is the second column
        if (cell) {
          const textValue = cell.textContent || cell.innerText;
          rows[i].style.display = textValue.toUpperCase().indexOf(input) > -1 ? "" : "none";
        }
      }
    }
  </script>
</body>
</html>
