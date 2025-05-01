<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Employee Attendance</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="min-h-screen font-sans bg-cover bg-no-repeat bg-right" style="background-image: linear-gradient(to left, rgba(255, 128, 128, 0.05),rgba(211, 134, 119, 0.44)), url('img/background.jpg');">


  <?php include 'navbar.php'; ?>

  <section class="px-10 py-10">
    <h1 class="text-4xl font-extrabold text-red-600 mb-6">Employee Attendance Overview</h1>

    <div class="flex flex-col md:flex-row md:items-center gap-4 mb-6">
      <label class="font-semibold text-gray-700">From:</label>
      <input type="date" id="fromDate" class="p-2 border rounded" />
      <label class="font-semibold text-gray-700">To:</label>
      <input type="date" id="toDate" class="p-2 border rounded" />
    </div>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
      <div class="bg-white rounded-xl p-4 shadow-md h-[500px] overflow-y-auto">
        <h2 class="text-xl font-semibold text-red-600 mb-2">Employees</h2>
        <ul id="employeeList" class="space-y-2"></ul>
      </div>

      <div class="md:col-span-3 space-y-6">
        <div class="bg-white rounded-xl p-6 shadow-md">
          <h2 class="text-lg font-semibold text-red-600 mb-4">Attendance Graph</h2>
          <div class="w-full md:w-[600px] h-[400px] mx-auto">
          <canvas id="attendanceChart"></canvas>
        </div>


        </div>

        <div id="employeeStats" class="hidden gap-6 md:flex">
          <div class="bg-gradient-to-b from-red-200 to-gray-200 p-6 rounded-xl w-full text-center shadow">
            <h3 class="font-bold text-gray-700">Leaves Taken</h3>
            <p id="leavesCount" class="text-3xl font-extrabold mt-2">0</p>
          </div>
          <div class="bg-gradient-to-b from-red-200 to-gray-200 p-6 rounded-xl w-full text-center shadow">
            <h3 class="font-bold text-gray-700">Working Days</h3>
            <p id="workingDaysCount" class="text-3xl font-extrabold mt-2">0</p>
          </div>
        </div>
      </div>
    </div>
  </section>

  <script>
    let attendanceChart;

    window.onload = function () {
      fetch('get_employees.php')
        .then(response => response.json())
        .then(data => {
          const list = document.getElementById('employeeList');
          list.innerHTML = '';
          data.forEach(emp => {
            const li = document.createElement('li');
            li.className = 'cursor-pointer hover:bg-red-100 p-2 rounded';
            li.textContent = emp.NAME;
            li.onclick = () => selectEmployee(emp.ID, emp.NAME);
            list.appendChild(li);
          });
        });
    };

    function selectEmployee(id, name) {
      const from = document.getElementById('fromDate').value;
      const to = document.getElementById('toDate').value;

      if (!from || !to) {
        alert("Please select both 'From' and 'To' dates.");
        return;
      }

      fetch(`get_attendance.php?id=${id}&from=${from}&to=${to}`)
        .then(response => response.json())
        .then(data => {
          if (data.error) {
            alert(data.error);
            return;
          }

          document.getElementById('employeeStats').classList.remove('hidden');
          document.getElementById('workingDaysCount').textContent = data.working;
          document.getElementById('leavesCount').textContent = data.leaves;

          updateChart(data.working, data.leaves);
        })
        .catch(error => {
          console.error('Error:', error);
          alert('Failed to load attendance data');
        });
    }

    function updateChart(working, leaves) {
      const ctx = document.getElementById('attendanceChart').getContext('2d');

      if (attendanceChart) {
        attendanceChart.data.datasets[0].data = [working, leaves];
        attendanceChart.update();
      } else {
        attendanceChart = new Chart(ctx, {
          type: 'doughnut',
          data: {
            labels: ['Work Days', 'Leaves'],
            datasets: [{
              data: [working, leaves],
              backgroundColor: ['#10B981', '#EF4444']
            }]
          },
          options: {
            responsive: true,
            plugins: {
              legend: { position: 'bottom' },
              title: { display: true, text: 'Attendance Summary' }
            }
          }
        });
      }
    }
  </script>

</body>
</html>
