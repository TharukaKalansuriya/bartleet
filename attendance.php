<?php
session_start();

// Define allowed roles
$allowed_roles = ['admin', 'manager'];

// Check if the user's role is not in the allowed roles
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], $allowed_roles)) {
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Attendance Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="min-h-screen font-sans bg-cover bg-no-repeat bg-right" style="background-image: linear-gradient(to left, rgba(0, 0, 0, 0.4), rgba(0, 0, 0, 0.6)), url('img/background.jpg');">
    
    <?php include 'navbar.php'; ?>

      <!-- Back Button - Top Right Corner -->
   <div class="absolute top-10 right-10 z-50 mt-20">
  <img 
    src="img/back.png" 
    onclick="history.back()" 
    alt="Back" 
    class="w-14 h-14 cursor-pointer transition duration-400 ease-in-out transform hover:scale-110 hover:rotate-[-20deg] active:scale-95 active:rotate-[5deg]" 
  />
  </div>

    <div class="px-10 py-6">
        <div class="mb-8">
            <h1 class="text-4xl font-extrabold text-white mb-2">Employee Attendance Dashboard</h1>
            <p class="text-white/80">Track and analyze employee attendance patterns</p>
        </div>

        <!-- Controls Section -->
        <div class="bg-white/10 backdrop-blur-md border border-white/20 rounded-xl p-6 mb-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                <div>
                    <label class="block text-sm font-medium text-white mb-2">Select Employee</label>
                    <select id="employeeSelect" class="w-full p-3 bg-white/10 backdrop-blur-md border border-white/30 rounded-lg text-white placeholder-white/50 focus:ring-2 focus:ring-red-500 focus:border-transparent">
                        <option value="" class="text-gray-800">Choose an employee...</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-white mb-2">Year</label>
                    <select id="yearSelect" class="w-full p-3 bg-white/10 backdrop-blur-md border border-white/30 rounded-lg text-white focus:ring-2 focus:ring-red-500 focus:border-transparent">
                        <option value="2026" class="text-gray-800">2026</option>
                        <option value="2025" class="text-gray-800">2025</option>
                        <option value="2024" class="text-gray-800">2024</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-white mb-2">Month (Optional)</label>
                    <select id="monthSelect" class="w-full p-3 bg-white/10 backdrop-blur-md border border-white/30 rounded-lg text-white focus:ring-2 focus:ring-red-500 focus:border-transparent">
                        <option value="" class="text-gray-800">All Months</option>
                        <option value="1" class="text-gray-800">January</option>
                        <option value="2" class="text-gray-800">February</option>
                        <option value="3" class="text-gray-800">March</option>
                        <option value="4" class="text-gray-800">April</option>
                        <option value="5" class="text-gray-800">May</option>
                        <option value="6" class="text-gray-800">June</option>
                        <option value="7" class="text-gray-800">July</option>
                        <option value="8" class="text-gray-800">August</option>
                        <option value="9" class="text-gray-800">September</option>
                        <option value="10" class="text-gray-800">October</option>
                        <option value="11" class="text-gray-800">November</option>
                        <option value="12" class="text-gray-800">December</option>
                    </select>
                </div>
                
                <div>
                    <button id="loadData" class="w-full bg-red-600/80 backdrop-blur-sm hover:bg-red-700/80 text-white font-medium py-3 px-4 rounded-lg transition duration-200 border border-red-500/30">
                        Load Attendance
                    </button>
                </div>
            </div>
        </div>

        <!-- Dashboard Content -->
        <div id="dashboardContent" class="hidden">
            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="bg-white/10 backdrop-blur-md border border-white/20 rounded-xl p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-10 h-10 bg-green-500/20 backdrop-blur-sm rounded-full flex items-center justify-center">
                                <svg class="w-5 h-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-white/80">Working Days</p>
                            <p id="workingDays" class="text-3xl font-bold text-white">0</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white/10 backdrop-blur-md border border-white/20 rounded-xl p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-10 h-10 bg-red-500/20 backdrop-blur-sm rounded-full flex items-center justify-center">
                                <svg class="w-5 h-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-white/80">Leave Days</p>
                            <p id="leaveDays" class="text-3xl font-bold text-white">0</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white/10 backdrop-blur-md border border-white/20 rounded-xl p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-10 h-10 bg-blue-500/20 backdrop-blur-sm rounded-full flex items-center justify-center">
                                <svg class="w-5 h-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-white/80">Total Days</p>
                            <p id="totalDays" class="text-3xl font-bold text-white">0</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white/10 backdrop-blur-md border border-white/20 rounded-xl p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-10 h-10 bg-yellow-500/20 backdrop-blur-sm rounded-full flex items-center justify-center">
                                <svg class="w-5 h-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-white/80">Attendance Rate</p>
                            <p id="attendanceRate" class="text-3xl font-bold text-white">0%</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts Section -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Current Period Chart -->
                <div class="bg-white/10 backdrop-blur-md border border-white/20 rounded-xl p-6">
                    <h3 class="text-xl font-semibold text-white mb-4" id="currentChartTitle">Current Period</h3>
                    <div class="h-80">
                        <canvas id="currentChart"></canvas>
                    </div>
                </div>

                <!-- Yearly Breakdown Chart -->
                <div class="bg-white/10 backdrop-blur-md border border-white/20 rounded-xl p-6">
                    <h3 class="text-xl font-semibold text-white mb-4" id="yearlyChartTitle">Yearly Breakdown</h3>
                    <div class="h-80">
                        <canvas id="yearlyChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        let currentChart = null;
        let yearlyChart = null;
        let selectedEmployee = null;

        // Initialize
        document.addEventListener('DOMContentLoaded', function() {
            loadEmployees();
            setCurrentYear();
            
            document.getElementById('loadData').addEventListener('click', loadAttendanceData);
        });

        function loadEmployees() {
            fetch('get_employees.php')
                .then(response => response.json())
                .then(data => {
                    const select = document.getElementById('employeeSelect');
                    select.innerHTML = '<option value="" class="text-gray-800">Choose an employee...</option>';
                    
                    data.forEach(emp => {
                        const option = document.createElement('option');
                        option.value = emp.ID;
                        option.textContent = emp.NAME;
                        option.className = 'text-gray-800';
                        select.appendChild(option);
                    });
                })
                .catch(error => {
                    console.error('Error loading employees:', error);
                    showAlert('Failed to load employees', 'error');
                });
        }

        function setCurrentYear() {
            const currentYear = new Date().getFullYear();
            document.getElementById('yearSelect').value = currentYear;
        }

        function loadAttendanceData() {
            const employeeId = document.getElementById('employeeSelect').value;
            const year = document.getElementById('yearSelect').value;
            const month = document.getElementById('monthSelect').value;

            if (!employeeId) {
                showAlert('Please select an employee', 'warning');
                return;
            }

            selectedEmployee = employeeId;

            if (month) {
                // Load specific month data
                loadMonthData(employeeId, year, month);
            } else {
                // Load full year data
                loadYearData(employeeId, year);
            }
        }

        function loadMonthData(employeeId, year, month) {
            const fromDate = `${year}-${month.padStart(2, '0')}-01`;
            const lastDay = new Date(year, month, 0).getDate();
            const toDate = `${year}-${month.padStart(2, '0')}-${lastDay.toString().padStart(2, '0')}`;

            fetch(`get_attendance.php?id=${employeeId}&from=${fromDate}&to=${toDate}`)
                .then(response => response.json())
                .then(data => {
                    if (data.error) {
                        showAlert(data.error, 'error');
                        return;
                    }

                    const total = data.working + data.leaves;
                    const rate = total > 0 ? Math.round((data.working / total) * 100) : 0;

                    updateStats(data.working, data.leaves, total, rate);
                    updateCurrentChart(data, `${getMonthName(month)} ${year}`);
                    
                    // Load yearly data for second chart
                    loadYearlyBreakdown(employeeId, year);
                    
                    document.getElementById('dashboardContent').classList.remove('hidden');
                })
                .catch(error => {
                    console.error('Error:', error);
                    showAlert('Failed to load attendance data', 'error');
                });
        }

        function loadYearData(employeeId, year) {
            const fromDate = `${year}-01-01`;
            const toDate = `${year}-12-31`;

            fetch(`get_attendance.php?id=${employeeId}&from=${fromDate}&to=${toDate}`)
                .then(response => response.json())
                .then(data => {
                    if (data.error) {
                        showAlert(data.error, 'error');
                        return;
                    }

                    const total = data.working + data.leaves;
                    const rate = total > 0 ? Math.round((data.working / total) * 100) : 0;

                    updateStats(data.working, data.leaves, total, rate);
                    updateCurrentChart(data, `Year ${year}`);
                    
                    // Load monthly breakdown for second chart
                    loadYearlyBreakdown(employeeId, year);
                    
                    document.getElementById('dashboardContent').classList.remove('hidden');
                })
                .catch(error => {
                    console.error('Error:', error);
                    showAlert('Failed to load attendance data', 'error');
                });
        }

        function loadYearlyBreakdown(employeeId, year) {
            const monthlyPromises = [];
            const monthlyData = {
                working: new Array(12).fill(0),
                leaves: new Array(12).fill(0)
            };

            // Load data for each month
            for (let month = 1; month <= 12; month++) {
                const fromDate = `${year}-${month.toString().padStart(2, '0')}-01`;
                const lastDay = new Date(year, month, 0).getDate();
                const toDate = `${year}-${month.toString().padStart(2, '0')}-${lastDay.toString().padStart(2, '0')}`;

                monthlyPromises.push(
                    fetch(`get_attendance.php?id=${employeeId}&from=${fromDate}&to=${toDate}`)
                        .then(response => response.json())
                        .then(data => {
                            if (!data.error) {
                                monthlyData.working[month - 1] = data.working;
                                monthlyData.leaves[month - 1] = data.leaves;
                            }
                        })
                );
            }

            Promise.all(monthlyPromises).then(() => {
                updateYearlyChart(monthlyData, year);
            });
        }

        function updateStats(working, leaves, total, rate) {
            document.getElementById('workingDays').textContent = working;
            document.getElementById('leaveDays').textContent = leaves;
            document.getElementById('totalDays').textContent = total;
            document.getElementById('attendanceRate').textContent = rate + '%';
        }

        function updateCurrentChart(data, title) {
            const ctx = document.getElementById('currentChart').getContext('2d');
            
            if (currentChart) {
                currentChart.destroy();
            }

            document.getElementById('currentChartTitle').textContent = title;

            currentChart = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: ['Working Days', 'Leave Days'],
                    datasets: [{
                        data: [data.working, data.leaves],
                        backgroundColor: ['#10B981', '#EF4444'],
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                color: 'white'
                            }
                        }
                    }
                }
            });
        }

        function updateYearlyChart(yearlyData, year) {
            const ctx = document.getElementById('yearlyChart').getContext('2d');
            
            if (yearlyChart) {
                yearlyChart.destroy();
            }

            document.getElementById('yearlyChartTitle').textContent = `Monthly Breakdown ${year}`;

            const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 
                          'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

            yearlyChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: months,
                    datasets: [
                        {
                            label: 'Working Days',
                            data: yearlyData.working,
                            backgroundColor: '#10B981'
                        },
                        {
                            label: 'Leave Days',
                            data: yearlyData.leaves,
                            backgroundColor: '#EF4444'
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                color: 'white'
                            },
                            grid: {
                                color: 'rgba(255, 255, 255, 0.1)'
                            }
                        },
                        x: {
                            ticks: {
                                color: 'white'
                            },
                            grid: {
                                color: 'rgba(255, 255, 255, 0.1)'
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                color: 'white'
                            }
                        }
                    }
                }
            });
        }

        function getMonthName(monthNum) {
            const months = ['January', 'February', 'March', 'April', 'May', 'June',
                          'July', 'August', 'September', 'October', 'November', 'December'];
            return months[parseInt(monthNum) - 1];
        }

        function showAlert(message, type = 'info') {
            const alertDiv = document.createElement('div');
            const bgColor = type === 'error' ? 'bg-red-500/20 border-red-500/50 text-red-200' :
                           type === 'warning' ? 'bg-yellow-500/20 border-yellow-500/50 text-yellow-200' :
                           'bg-blue-500/20 border-blue-500/50 text-blue-200';
            
            alertDiv.className = `fixed top-4 right-4 border backdrop-blur-md p-4 rounded-lg shadow-lg z-50 ${bgColor}`;
            alertDiv.innerHTML = `
                <div class="flex">
                    <div class="ml-3">
                        <p class="text-sm font-medium">${message}</p>
                    </div>
                </div>
            `;
            
            document.body.appendChild(alertDiv);
            
            setTimeout(() => {
                if (document.body.contains(alertDiv)) {
                    document.body.removeChild(alertDiv);
                }
            }, 3000);
        }
    </script>
</body>
</html>