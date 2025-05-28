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

require_once 'database.php';

$db = new Database();
$conn = $db->getConnection();

// Check which tables exist
$machinesTableExists = false;
$compressorsTableExists = false;

// Check if machines table exists
$checkMachines = $conn->query("SHOW TABLES LIKE 'machines'");
if ($checkMachines && $checkMachines->num_rows > 0) {
    $machinesTableExists = true;
}

// Check if compressors table exists
$checkCompressors = $conn->query("SHOW TABLES LIKE 'compressors'");
if ($checkCompressors && $checkCompressors->num_rows > 0) {
    $compressorsTableExists = true;
}

// Build the query to fetch all equipment (machines and compressors)
$machinesQuery = "";
$queries = [];

if ($machinesTableExists) {
    $queries[] = "
        SELECT 
            m.SerialNo, 
            m.Model, 
            m.MachineStage,
            m.InstalledDate, 
            m.ServicePersonId,
            f.FacName,
            f.FacId,
            f.TeamID, 
            f.Location,
            'Machine' as EquipmentType,
            m.created_at
        FROM 
            machines m
        JOIN 
            factories f ON m.FacId = f.FacId
    ";
}

if ($compressorsTableExists) {
    $queries[] = "
        SELECT 
            c.SerialNo, 
            c.Model, 
            'Oil Compressor' as MachineStage,
            c.InstalledDate, 
            c.ServicePersonId,
            f.FacName,
            f.FacId,
            f.TeamID, 
            f.Location,
            'Compressor' as EquipmentType,
            c.created_at
        FROM 
            compressors c
        JOIN 
            factories f ON c.FacId = f.FacId
    ";
}

// Combine queries with UNION if both tables exist
if (!empty($queries)) {
    $machinesQuery = implode(" UNION ALL ", $queries) . " ORDER BY created_at DESC";
} else {
    // Fallback if no tables exist
    $machinesQuery = "SELECT NULL as SerialNo, NULL as Model, NULL as MachineStage, NULL as InstalledDate, NULL as ServicePersonId, NULL as FacName, NULL as FacId, NULL as TeamID, NULL as Location, NULL as EquipmentType, NULL as created_at WHERE 1=0";
}

// Execute the combined query
$machines = $conn->query($machinesQuery);

// Fetch unique teams
$teamsQuery = "SELECT DISTINCT TeamID FROM factories WHERE TeamID IS NOT NULL AND TeamID != '' ORDER BY TeamID";
$teams = $conn->query($teamsQuery);

// Fetch all factories with their teams
$factoriesQuery = "SELECT FacId, FacName, TeamID, Location FROM factories ORDER BY TeamID, FacName";
$factories = $conn->query($factoriesQuery);

// Fetch unique locations
$locationsQuery = "SELECT DISTINCT Location FROM factories WHERE Location IS NOT NULL AND Location != '' ORDER BY Location";
$locations = $conn->query($locationsQuery);

$db->closeConnection();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Machine Management - BC-Agro Tronics</title>
    <script src="https://cdn.tailwindcss.com"></script>
    
</head>
<body class="min-h-screen font-sans bg-cover bg-no-repeat bg-right" style="background-image: linear-gradient(to left, rgba(0, 0, 0, 0.4), rgba(0, 0, 0, 0.6)), url('img/background.jpg');">

    <!-- Navigation -->
    <?php include "navbar.php" ?>

    <!-- Back Button -->
    <div class="absolute top-10 right-10 z-50 mt-20">
        <img 
            src="img/back.png" 
            onclick="history.back()" 
            alt="Back" 
            class="w-14 h-14 cursor-pointer transition duration-400 ease-in-out transform hover:scale-110 hover:rotate-[-20deg] active:scale-95 active:rotate-[5deg]" 
        />
    </div>

    <!-- Header with Logo and Title -->
    <section class="flex items-center justify-center pt-10 px-4">
        <div class="backdrop-blur-md bg-white/20 rounded-2xl shadow-xl p-6 flex items-center gap-6 mb-6">
            <img src="img/logo.png" alt="Logo" class="w-28 h-20 md:w-32 md:h-24 object-contain" />
            <div>
                <h1 class="text-4xl md:text-5xl font-extrabold text-red-700">BC–Agro Tronics</h1>
                <p class="text-xl text-red-400">Equipment Management</p>
            </div>
        </div>
    </section>

    <!-- Filter Controls -->
    <div class="flex justify-center mb-6">
        <div class="backdrop-blur-md bg-white/30 rounded-xl shadow-lg p-4 flex flex-wrap gap-4 items-center">
            <!-- Search Input -->
            <input 
                type="text" 
                id="searchInput" 
                placeholder="Search equipment..." 
                class="px-4 py-2 border border-red-300 rounded-full shadow-md focus:outline-none focus:ring-2 focus:ring-red-400 min-w-64"
                onkeyup="filterMachines()"
            />
            
            <!-- Equipment Type Filter -->
            <select id="equipmentTypeFilter" class="px-4 py-2 border border-red-300 rounded-full shadow-md focus:outline-none focus:ring-2 focus:ring-red-400" onchange="filterMachines()">
                <option value="">All Equipment</option>
                <option value="Machine">Machines Only</option>
                <option value="Compressor">Compressors Only</option>
            </select>
            
            <!-- Team Filter -->
            <select id="teamFilter" class="px-4 py-2 border border-red-300 rounded-full shadow-md focus:outline-none focus:ring-2 focus:ring-red-400" onchange="filterMachines()">
                <option value="">All Teams</option>
                <?php if ($teams && $teams->num_rows > 0): ?>
                    <?php while ($team = $teams->fetch_assoc()): ?>
                        <option value="<?= htmlspecialchars($team['TeamID']) ?>">
                            Team <?= htmlspecialchars($team['TeamID']) ?>
                        </option>
                    <?php endwhile; ?>
                <?php endif; ?>
            </select>

            <!-- Factory Filter -->
            <select id="factoryFilter" class="px-4 py-2 border border-red-300 rounded-full shadow-md focus:outline-none focus:ring-2 focus:ring-red-400" onchange="filterMachines()">
                <option value="">All Factories</option>
                <?php if ($factories && $factories->num_rows > 0): ?>
                    <?php 
                    $factories->data_seek(0); // Reset pointer
                    while ($factory = $factories->fetch_assoc()): 
                    ?>
                        <option value="<?= $factory['FacId'] ?>" data-team="<?= htmlspecialchars($factory['TeamID']) ?>">
                            <?= htmlspecialchars($factory['FacName']) ?> (Team <?= htmlspecialchars($factory['TeamID']) ?>)
                        </option>
                    <?php endwhile; ?>
                <?php endif; ?>
            </select>

            <!-- Location Filter -->
            <select id="locationFilter" class="px-4 py-2 border border-red-300 rounded-full shadow-md focus:outline-none focus:ring-2 focus:ring-red-400" onchange="filterMachines()">
                <option value="">All Locations</option>
                <?php if ($locations && $locations->num_rows > 0): ?>
                    <?php while ($location = $locations->fetch_assoc()): ?>
                        <option value="<?= htmlspecialchars($location['Location']) ?>">
                            <?= htmlspecialchars($location['Location']) ?>
                        </option>
                    <?php endwhile; ?>
                <?php endif; ?>
            </select>

            <!-- Clear All Filters -->
            <button 
                onclick="clearAllFilters()" 
                class="px-4 py-2 bg-red-500 text-white rounded-full shadow-md hover:bg-red-600 transition"
            >
                Clear Filters
            </button>
        </div>
    </div>

    <!-- Results Summary -->
    <div class="flex justify-center mb-4">
        <div class="backdrop-blur-md bg-white/20 rounded-lg shadow-md p-3">
            <span id="resultsCount" class="text-red-700 font-semibold">Loading...</span>
        </div>
    </div>

    <!-- Equipment Table -->
    <div class="flex justify-center px-4">
        <div class="w-full max-w-7xl backdrop-blur-md bg-white/30 rounded-xl shadow-xl overflow-hidden">
            <div class="overflow-x-auto overflow-y-auto max-h-96">
                <table class="min-w-full table-auto text-sm text-left">
                    <thead class="bg-red-200 text-red-800 sticky top-0">
                        <tr>
                            <th class="px-4 py-3 font-semibold">Serial No</th>
                            <th class="px-4 py-3 font-semibold">Model</th>
                            <th class="px-4 py-3 font-semibold">Type</th>
                            <th class="px-4 py-3 font-semibold">Stage</th>
                            <th class="px-4 py-3 font-semibold">Factory</th>
                            <th class="px-4 py-3 font-semibold">Team</th>
                            <th class="px-4 py-3 font-semibold">Location</th>
                            <th class="px-4 py-3 font-semibold">Installed Date</th>
                            <th class="px-4 py-3 font-semibold">Service Person ID</th>
                            <th class="px-4 py-3 font-semibold">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="machineTableBody">
                        <?php if ($machines && $machines->num_rows > 0): ?>
                            <?php while ($row = $machines->fetch_assoc()): ?>
                                <tr class="machine-row bg-white/80 hover:bg-red-50 transition cursor-pointer border-b border-red-100"
                                    data-serial="<?= htmlspecialchars($row['SerialNo']) ?>"
                                    data-model="<?= htmlspecialchars($row['Model']) ?>"
                                    data-stage="<?= htmlspecialchars($row['MachineStage']) ?>"
                                    data-factory="<?= htmlspecialchars($row['FacName']) ?>"
                                    data-factory-id="<?= $row['FacId'] ?>"
                                    data-team="<?= htmlspecialchars($row['TeamID']) ?>"
                                    data-location="<?= htmlspecialchars($row['Location']) ?>"
                                    data-installed="<?= $row['InstalledDate'] ?>"
                                    data-service-person="<?= $row['ServicePersonId'] ?>"
                                    data-equipment-type="<?= htmlspecialchars($row['EquipmentType']) ?>"
                                >
                                    <td class="px-4 py-3 font-medium text-red-700"><?= htmlspecialchars($row['SerialNo']) ?></td>
                                    <td class="px-4 py-3"><?= htmlspecialchars($row['Model']) ?></td>
                                    <td class="px-4 py-3">
                                        <span class="<?= $row['EquipmentType'] == 'Compressor' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800' ?> px-2 py-1 rounded-full text-xs font-medium">
                                            <?= htmlspecialchars($row['EquipmentType']) ?>
                                        </span>
                                    </td>
                                    <td class="px-4 py-3"><?= htmlspecialchars($row['MachineStage']) ?></td>
                                    <td class="px-4 py-3"><?= htmlspecialchars($row['FacName']) ?></td>
                                    <td class="px-4 py-3">
                                        <span class="bg-red-100 text-red-800 px-2 py-1 rounded-full text-xs font-medium">
                                            Team <?= htmlspecialchars($row['TeamID']) ?>
                                        </span>
                                    </td>
                                    <td class="px-4 py-3"><?= htmlspecialchars($row['Location']) ?></td>
                                    <td class="px-4 py-3"><?= $row['InstalledDate'] ? date('Y-m-d', strtotime($row['InstalledDate'])) : 'N/A' ?></td>
                                    <td class="px-4 py-3"><?= $row['ServicePersonId'] ?: 'N/A' ?></td>
                                    <td class="px-4 py-3">
                                        <button 
                                            onclick="viewMachineDetails('<?= htmlspecialchars($row['SerialNo']) ?>')"
                                            class="bg-red-500 text-white px-3 py-1 rounded text-xs hover:bg-red-600 transition"
                                        >
                                            View
                                        </button>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="10" class="px-4 py-8 text-center text-gray-600 bg-white/80">
                                    No equipment found in the database.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Equipment Details Modal -->
    <div id="machineModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 max-w-2xl w-full m-4 max-h-96 overflow-y-auto">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-bold text-red-700">Equipment Details</h3>
                <button onclick="closeMachineModal()" class="text-gray-500 hover:text-gray-700">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <div id="machineDetails" class="space-y-3">
                <!-- Details will be populated by JavaScript -->
            </div>
        </div>
    </div>

    <!-- JavaScript -->
    <script>
        // Initialize page
        document.addEventListener('DOMContentLoaded', function() {
            updateResultsCount();
        });

        function filterMachines() {
            const searchTerm = document.getElementById('searchInput').value.toLowerCase();
            const selectedTeam = document.getElementById('teamFilter').value;
            const selectedFactory = document.getElementById('factoryFilter').value;
            const selectedLocation = document.getElementById('locationFilter').value;
            const selectedEquipmentType = document.getElementById('equipmentTypeFilter').value;
            
            const rows = document.querySelectorAll('.machine-row');
            let visibleCount = 0;

            rows.forEach(row => {
                const serial = row.dataset.serial.toLowerCase();
                const model = row.dataset.model.toLowerCase();
                const factory = row.dataset.factory.toLowerCase();
                const team = row.dataset.team;
                const factoryId = row.dataset.factoryId;
                const location = row.dataset.location;
                const equipmentType = row.dataset.equipmentType;

                // Text search
                const matchesSearch = !searchTerm || 
                    serial.includes(searchTerm) || 
                    model.includes(searchTerm) || 
                    factory.includes(searchTerm);

                // Team filter
                const matchesTeam = !selectedTeam || team === selectedTeam;

                // Factory filter
                const matchesFactory = !selectedFactory || factoryId === selectedFactory;

                // Location filter
                const matchesLocation = !selectedLocation || location === selectedLocation;

                // Equipment type filter
                const matchesEquipmentType = !selectedEquipmentType || equipmentType === selectedEquipmentType;

                const shouldShow = matchesSearch && matchesTeam && matchesFactory && matchesLocation && matchesEquipmentType;
                
                row.style.display = shouldShow ? '' : 'none';
                if (shouldShow) visibleCount++;
            });

            updateResultsCount(visibleCount);
        }

        function updateResultsCount(count = null) {
            if (count === null) {
                const allRows = document.querySelectorAll('.machine-row');
                count = allRows.length;
            }
            
            const totalRows = document.querySelectorAll('.machine-row').length;
            document.getElementById('resultsCount').textContent = 
                `Showing ${count} of ${totalRows} equipment`;
        }

        function clearAllFilters() {
            document.getElementById('searchInput').value = '';
            document.getElementById('teamFilter').value = '';
            document.getElementById('factoryFilter').value = '';
            document.getElementById('locationFilter').value = '';
            document.getElementById('equipmentTypeFilter').value = '';
            filterMachines();
        }

        function viewMachineDetails(serialNo) {
            const row = document.querySelector(`[data-serial="${serialNo}"]`);
            if (!row) return;

            const details = {
                'Serial Number': row.dataset.serial,
                'Model': row.dataset.model,
                'Equipment Type': row.dataset.equipmentType,
                'Stage': row.dataset.stage || 'N/A',
                'Factory': row.dataset.factory,
                'Team': `Team ${row.dataset.team}`,
                'Location': row.dataset.location,
                'Installed Date': row.dataset.installed || 'N/A',
                'Service Person ID': row.dataset.servicePerson || 'N/A'
            };

            let detailsHtml = '';
            for (const [key, value] of Object.entries(details)) {
                detailsHtml += `
                    <div class="flex justify-between py-2 border-b border-gray-200">
                        <span class="font-medium text-gray-700">${key}:</span>
                        <span class="text-gray-900">${value}</span>
                    </div>
                `;
            }

            document.getElementById('machineDetails').innerHTML = detailsHtml;
            document.getElementById('machineModal').classList.remove('hidden');
        }

        function closeMachineModal() {
            document.getElementById('machineModal').classList.add('hidden');
        }

        // Close modal when clicking outside
        document.getElementById('machineModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeMachineModal();
            }
        });

        // Update factory filter based on team selection
        document.getElementById('teamFilter').addEventListener('change', function() {
            const selectedTeam = this.value;
            const factorySelect = document.getElementById('factoryFilter');
            const factoryOptions = factorySelect.querySelectorAll('option');

            factoryOptions.forEach(option => {
                if (option.value === '') {
                    option.style.display = 'block'; // Always show "All Factories"
                } else {
                    const optionTeam = option.dataset.team;
                    option.style.display = (!selectedTeam || optionTeam === selectedTeam) ? 'block' : 'none';
                }
            });

            // Reset factory selection if current selection is not compatible with team
            if (selectedTeam && factorySelect.value) {
                const currentFactoryOption = factorySelect.querySelector(`option[value="${factorySelect.value}"]`);
                if (currentFactoryOption && currentFactoryOption.dataset.team !== selectedTeam) {
                    factorySelect.value = '';
                }
            }

            filterMachines();
        });
    </script>

</body>
</html>